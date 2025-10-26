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
     * Muestra una lista del historial de movimientos de inventario.
     */
    public function index()
    {
        $movimientos = MovimientoInventario::with(['inventario.marca.producto', 'inventario.tienda', 'usuario'])
            ->latest()
            ->paginate(20);

        return view('movimientos.index', compact('movimientos'));
    }

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

        // ⭐ SOLUCIÓN DE FORZADO DE VALORES
        // 1. Usamos el ID de usuario logeado o forzamos el ID 1 (el más común). Si tu usuario ID 1 no existe, debes cambiarlo.
        // 2. Usamos valores no NULL para movible_id/type para evitar errores de restricción de BD mal aplicadas.
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
            // Diagnóstico Final: Esto es lo que se ejecuta cuando la BD falla
            DB::rollBack(); 
            
            // Registra el error completo en el log
            Log::error("Fallo CRÍTICO de BD en Store: " . $e->getMessage(), ['exception' => $e]);
            
            // Muestra un mensaje de error más suave para el usuario
            return back()->with('error', '¡ERROR CRÍTICO! Falló la transacción. Revise los logs. Causa: ' . substr($e->getMessage(), 0, 150))->withInput();
        }
    }
}