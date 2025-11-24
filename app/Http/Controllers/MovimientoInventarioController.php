<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Models\Tienda; 
use App\Models\Empresa; 
use App\Models\Marca; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException; 
use Log;

class MovimientoInventarioController extends Controller
{

    public function index(Request $request)
    {
        $tipo = $request->get('tipo');
        $fechaDesde = $request->get('fecha_desde');
        $fechaHasta = $request->get('fecha_hasta');
        $tiendaId = $request->get('tienda_id'); 
        $query = MovimientoInventario::with(['inventario.marca.producto', 'inventario.tienda', 'usuario']);

        if ($tipo) {
            $query->where('tipo_movimiento', $tipo);
        }

        if ($fechaDesde) {
            $query->whereDate('created_at', '>=', $fechaDesde);
        }

        if ($fechaHasta) {
            $query->whereDate('created_at', '<=', $fechaHasta);
        }

        if ($tiendaId) {
            $query->whereHas('inventario', function ($q) use ($tiendaId) {
                $q->where('tienda_id', $tiendaId);
            });
        }

        $resumenQuery = clone $query; 
        $movimientos = $query->latest()->paginate(20);
        $movimientos->appends($request->all());

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

        $tiendas = Tienda::all(['id', 'nombre']);

        return view('movimientos.index', [
            'movimientos' => $movimientos,
            'resumen' => $resumenData,
            'tiendas' => $tiendas, 
        ]);
    }

    public function create()
    {
        $tiendas = Tienda::all(['id', 'nombre']);
        $empresas = Empresa::all(['id', 'nombre_negocio']); 
        
        $inventarios = Inventario::with(['marca.producto', 'tienda', 'marca.empresa'])
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tienda_id' => $item->tienda_id,
                    'empresa_id' => $item->marca->empresa_id ?? null, 
                    'nombre_completo' => ($item->marca->producto->nombre ?? 'N/A') . 
                                         ' (' . ($item->marca->codigo_marca ?? 'N/A') . ')' .
                                         ' (Stock: ' . $item->stock . ' u.)',
                    'stock_actual' => $item->stock,
                ];
            })->toJson();
        
        $razones = [
            'ENTRADA' => ['Ingreso por Compra', 'Devolución de Cliente', 'Ajuste Positivo', 'Transferencia Recibida'],
            'SALIDA'  => ['Descarte por Daño', 'Ajuste Negativo', 'Muestra', 'Transferencia Enviada']
        ];
        
        return view('movimientos.create', compact('tiendas', 'inventarios', 'razones', 'empresas')); 
    }

    public function store(Request $request)
    {
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
        $userId = auth()->id() ?: 1; 
        $movibleType = 'Ajuste Manual'; 
        $movibleId = 0; 

        DB::beginTransaction();

        try {
            if ($tipoMovimiento === 'SALIDA' && $inventario->stock < $cantidad) {
                DB::rollBack();
                throw ValidationException::withMessages(['cantidad' => 'Stock insuficiente para esta salida. Stock actual: ' . $inventario->stock]);
            }
            
            MovimientoInventario::create([
                'inventario_id' => $validated['inventario_id'],
                'tipo_movimiento' => $tipoMovimiento,
                'razon' => $validated['razon'],
                'cantidad' => $cantidad,
                'usuario_id' => $userId, 
                'movible_id' => $movibleId, 
                'movible_type' => $movibleType, 
            ]);

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