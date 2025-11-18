<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Models\Tienda; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException; 
use Log;

class MovimientoInventarioController extends Controller
{
    /**
     * Muestra una lista del historial de movimientos de inventario y aplica filtros.
     */
    public function index(Request $request)
    {
        // 1. Obtener los parámetros de filtro de la URL
        $tipo = $request->get('tipo');
        $fechaDesde = $request->get('fecha_desde');
        $fechaHasta = $request->get('fecha_hasta');
        $tiendaId = $request->get('tienda_id'); // 🆕 NUEVO FILTRO

        // 2. Construir la consulta base con relaciones
        $query = MovimientoInventario::with(['inventario.marca.producto', 'inventario.tienda', 'usuario']);

        // 3. Aplicar Filtros
        if ($tipo) {
            $query->where('tipo_movimiento', $tipo);
        }

        if ($fechaDesde) {
            $query->whereDate('created_at', '>=', $fechaDesde);
        }

        if ($fechaHasta) {
            $query->whereDate('created_at', '<=', $fechaHasta);
        }

        // 🆕 APLICAR FILTRO POR TIENDA
        if ($tiendaId) {
            // Un movimiento se relaciona con una tienda a través del inventario
            $query->whereHas('inventario', function ($q) use ($tiendaId) {
                $q->where('tienda_id', $tiendaId);
            });
        }

        // 4. Clonar la consulta para el resumen ANTES de añadir el 'latest()'
        $resumenQuery = clone $query; 

        // 5. Ejecutar consulta de paginación
        $movimientos = $query->latest()->paginate(20);

        // Aseguramos que los links de paginación mantengan los filtros
        $movimientos->appends($request->all());

        // 6. Calcular Resumen:
        $resumen = $resumenQuery
            ->select('tipo_movimiento', DB::raw('SUM(cantidad) as total_cantidad'))
            ->groupBy('tipo_movimiento')
            ->get()
            ->pluck('total_cantidad', 'tipo_movimiento');

        $resumenData = [
            'entradas' => $resumen['ENTRADA'] ?? 0,
            'salidas' => $resumen['SALIDA'] ?? 0,
            'total' => ($resumen['ENTRADA'] ?? 0) + ($resumen['SALIDA'] ?? 0),
        ];

        // 🆕 Cargar todas las tiendas para el dropdown de la vista
        $tiendas = Tienda::all(['id', 'nombre']);


        // 7. Devolver la vista con los datos, el resumen y las tiendas
        return view('movimientos.index', [
            'movimientos' => $movimientos,
            'resumen' => $resumenData,
            'tiendas' => $tiendas, // 🆕 PASAMOS LAS TIENDAS A LA VISTA
        ]);
    }

    // ... (El resto de los métodos create y store permanecen iguales) ...

    /**
     * Muestra el formulario para registrar un nuevo movimiento (Ajuste/Ingreso/Descarte).
     */
    public function create()
    {
        $tiendas = Tienda::all(['id', 'nombre']);

        $inventarios = Inventario::with(['marca.producto', 'tienda'])
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tienda_id' => $item->tienda_id,
                    'nombre_completo' => ($item->marca->producto->nombre ?? 'N/A') . 
                                         ' (Stock: ' . $item->stock . ' u. en ' . ($item->tienda->nombre ?? 'N/A') . ')',
                    'stock_actual' => $item->stock,
                ];
            })->toJson();
        
        $razones = [
            'ENTRADA' => ['Ingreso por Compra', 'Devolución de Cliente', 'Ajuste Positivo', 'Transferencia Recibida'],
            'SALIDA'  => ['Descarte por Daño', 'Ajuste Negativo', 'Muestra', 'Transferencia Enviada']
        ];
        
        return view('movimientos.create', compact('tiendas', 'inventarios', 'razones'));
    }

    /**
     * Almacena un nuevo movimiento de inventario en la base de datos y actualiza el stock.
     */
    public function store(Request $request)
    {
        // 1. Validación de los datos
        $validated = $request->validate([
            'inventario_id' => ['required', 'exists:inventarios,id'],
            'tipo_movimiento' => ['required', Rule::in(['ENTRADA', 'SALIDA'])],
            'razon' => ['required', 'string', 'max:100'],
            'cantidad' => ['required', 'integer', 'min:1'],
        ]);

        $inventario = Inventario::find($validated['inventario_id']);

        if (!$inventario) {
            return back()->with('error', 'El registro de inventario seleccionado no es válido.')->withInput();
        }

        $cantidad = $validated['cantidad'];
        $tipoMovimiento = $validated['tipo_movimiento'];

        // ⭐ Aseguramos ID de usuario y valores por defecto
        $userId = auth()->id() ?: 1; 
        $movibleType = 'Ajuste Manual'; 
        $movibleId = 0; 

        DB::beginTransaction();

        try {
            // 2. Validación de Stock
            if ($tipoMovimiento === 'SALIDA' && $inventario->stock < $cantidad) {
                DB::rollBack();
                throw ValidationException::withMessages(['cantidad' => 'Stock insuficiente para esta salida. Stock actual: ' . $inventario->stock]);
            }
            
            // 3. Registrar el Movimiento en la tabla de historial
            MovimientoInventario::create([
                'inventario_id' => $validated['inventario_id'],
                'tipo_movimiento' => $tipoMovimiento,
                'razon' => $validated['razon'],
                'cantidad' => $cantidad,
                'usuario_id' => $userId, 
                'movible_id' => $movibleId, 
                'movible_type' => $movibleType, 
            ]);

            // 4. Actualizar el Stock
            if ($tipoMovimiento === 'ENTRADA') {
                $inventario->increment('stock', $cantidad); 
            } else { 
                $inventario->decrement('stock', $cantidad); 
            }

            DB::commit();

            return redirect()->route('movimientos.index')->with('success', 'Movimiento de inventario registrado y stock actualizado con éxito.');

        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            DB::rollBack(); 
            
            Log::error("Fallo CRÍTICO de BD en Store: " . $e->getMessage(), ['exception' => $e]);
            
            return back()->with('error', '¡ERROR CRÍTICO! Falló la transacción. Revise los logs. Causa: ' . substr($e->getMessage(), 0, 150))->withInput();
        }
    }
}