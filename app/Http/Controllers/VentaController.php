<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Tienda;
use App\Models\Inventario;
use App\Models\Cliente;
use App\Models\MovimientoInventario; // ⭐ IMPORTANTE: Nuevo Modelo
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class VentaController extends Controller
{
    // ===========================================
    // VISTAS Y DATOS BASE
    // ===========================================

    public function create()
    {
        $tiendas = Tienda::all();
        // Asegúrate de que la vista exista: resources/views/ventas/pos.blade.php
        return view('ventas.pos', compact('tiendas'));
    }

    public function getProductosParaVenta($tienda_id)
    {
        $inventarios = Inventario::with(['marca.producto'])
            ->where('tienda_id', $tienda_id)
            ->where('stock', '>', 0)
            ->get();

        $productos = $inventarios->map(function ($item) {
            return [
                'inventario_id' => $item->id,
                'producto_nombre' => $item->marca->producto->nombre ?? 'N/A',
                'codigo_marca' => $item->marca->codigo_marca,
                'stock_actual' => (int) $item->stock,
                'precio' => (float) $item->precio,
            ];
        });

        return response()->json($productos);
    }

    // ===========================================
    // LÓGICA DE TRANSACCIONES (TICKET, QUOTE, INVOICE)
    // ===========================================

    /**
     * Maneja el proceso de Venta Rápida (TICKET).
     */
    public function storeTicket(Request $request)
    {
        $request->merge(['tipo_documento' => 'TICKET', 'afecta_stock' => true]);
        return $this->handleTransaction($request);
    }

    /**
     * Maneja el proceso de Facturación (INVOICE).
     */
    public function storeInvoice(Request $request)
    {
        $request->merge(['tipo_documento' => 'INVOICE', 'afecta_stock' => true]);
        if (!$request->cliente_id) {
             return response()->json([
                'success' => false, 
                'message' => "El cliente es obligatorio para generar una Factura."
            ], 422);
        }
        return $this->handleTransaction($request);
    }

    /**
     * Maneja el proceso de Cotización (QUOTE).
     */
    public function storeQuote(Request $request)
    {
        $request->merge(['tipo_documento' => 'QUOTE', 'afecta_stock' => false]);
         if (!$request->cliente_id) {
             return response()->json([
                'success' => false, 
                'message' => "El cliente es obligatorio para generar una Cotización."
            ], 422);
        }
        return $this->handleTransaction($request);
    }
    
    /**
     * Función centralizada para ejecutar la transacción, ahora registrando movimientos de inventario.
     */
    protected function handleTransaction(Request $request)
    {
        // 1. Validación unificada
        $request->validate([
            'tienda_id' => 'required|exists:tiendas,id',
            'cliente_id' => 'nullable|exists:clientes,id',
            'total_monto' => 'required|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'tipo_documento' => 'required|in:TICKET,QUOTE,INVOICE',
            'afecta_stock' => 'required|boolean',
            'detalles' => 'required|array|min:1',
            'detalles.*.inventario_id' => 'required|exists:inventarios,id',
            'detalles.*.cantidad' => 'required|integer|min:1',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        $tipo = $request->tipo_documento;
        $afectaStock = $request->afecta_stock;
        $clienteId = $request->cliente_id > 0 ? $request->cliente_id : null;
        

        DB::beginTransaction();

        try {
            // 2. Verificación de Stock (solo si afecta stock)
            if ($afectaStock) {
                foreach ($request->detalles as $detalle) {
                    $inventario = Inventario::find($detalle['inventario_id']);
                    if (!$inventario || $inventario->stock < $detalle['cantidad']) {
                        DB::rollBack();
                        $nombre = $inventario ? ($inventario->marca->producto->nombre ?? 'Producto sin nombre') : 'Desconocido';
                        throw ValidationException::withMessages([
                            'stock' => "Stock insuficiente para '{$nombre}'. Disponible: {$inventario->stock}, solicitado: {$detalle['cantidad']}."
                        ]);
                    }
                }
            }

            // 3. Creación del Documento
            $documento = Venta::create([
                'tienda_id' => $request->tienda_id,
                'cliente_id' => $clienteId,
                'fecha_venta' => now(),
                'total_venta' => $request->total_monto,
                'descuento' => $request->descuento ?? 0,
                'usuario_id' => auth()->id(),
                'tipo_documento' => $tipo,
                'estado' => ($tipo === 'QUOTE' ? 'PENDIENTE' : 'COMPLETADA')
            ]);
            
            $documentoId = $documento->id;

            // 4. Procesamiento de Detalles, Stock y REGISTRO DE MOVIMIENTOS
            foreach ($request->detalles as $detalleData) {
                // 4a. Crear el DetalleVenta
                $detalleVenta = $documento->detalles()->create([
                    'venta_id' => $documento->id,
                    'inventario_id' => $detalleData['inventario_id'],
                    'cantidad' => $detalleData['cantidad'],
                    'precio_unitario' => $detalleData['precio_unitario'],
                    'subtotal' => ($detalleData['cantidad'] * $detalleData['precio_unitario']),
                ]);

                if ($afectaStock) {
                    $inventario = Inventario::lockForUpdate()->find($detalleData['inventario_id']);
                    
                    // 4b. ⭐ Registrar el Movimiento de Inventario (SALIDA)
                    MovimientoInventario::create([
                        'inventario_id' => $inventario->id,
                        'tipo_movimiento' => 'SALIDA',
                        'razon' => 'Venta (' . $tipo . ')', 
                        'cantidad' => $detalleData['cantidad'],
                        'movible_id' => $detalleVenta->id,
                        'movible_type' => DetalleVenta::class, // Vinculado al DetalleVenta
                        'usuario_id' => auth()->id(),
                    ]);
                    
                    // 4c. Actualizar el Stock
                    $inventario->decrement('stock', $detalleData['cantidad']);
                }
            }
            
            // 5. Generación de URL de impresión/descarga
            $documentoUrl = route('ventas.print-document', ['id' => $documentoId, 'type' => strtolower($tipo)]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Transacción tipo {$tipo} registrada exitosamente.",
                'documento_id' => $documentoId,
                'documento_url' => $documentoUrl
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al procesar la transacción: ' . $e->getMessage()], 500);
        }
    }


    // ===========================================
    // FUNCIÓN DE IMPRESIÓN (MUESTRA LA VISTA)
    // ===========================================

    /**
     * Muestra la vista para imprimir/descargar el documento.
     */
    public function printDocument($id, $type)
    {
        $tipo = strtoupper($type); 
        
        $documento = Venta::with([
            'tienda', 
            'usuario', 
            'cliente', 
            'detalles.inventario.marca.producto'
        ])->find($id);

        if (!$documento) {
            abort(404, 'Documento no encontrado.');
        }
        
        // Asegúrate de que la vista exista: resources/views/documentos/imprimir.blade.php
        return view('documentos.imprimir', compact('documento', 'tipo'));
    }

    // ===========================================
    // FUNCIONES AUXILIARES (Cliente, Historial)
    // ===========================================

    public function storeCliente(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'identificacion' => 'nullable|string|max:50|unique:clientes,identificacion',
            'email' => 'nullable|email|max:255|unique:clientes,email',
            'telefono' => 'nullable|string|max:20',
        ]);

        try {
            $validated['identificacion'] = str_replace('-', '', $validated['identificacion']);
            $cliente = Cliente::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Cliente guardado correctamente.',
                'cliente' => [
                    'id' => $cliente->id,
                    'nombre' => $cliente->nombre,
                    'identificacion' => $cliente->identificacion, 
                ],
            ], 201);
        } catch (\Illuminate\Database\QueryException $e) {
            $msg = $e->getCode() == 23000
                ? 'La Identificación o el Email ya están registrados.'
                : 'Error al guardar el cliente.';
            return response()->json(['success' => false, 'message' => $msg], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error inesperado: ' . $e->getMessage()], 500);
        }
    }

    public function buscarClientes(Request $request)
    {
        $query = $request->get('query');
        if (empty($query)) {
            return response()->json([]);
        }

        $cleanQuery = str_replace('-', '', $query);

        $clientes = Cliente::where('nombre', 'like', "%$query%")
            ->orWhere('identificacion', 'like', "%$cleanQuery%")
            ->limit(10)
            ->get(['id', 'nombre', 'identificacion']);

        if ($clientes->isEmpty()) {
            return response()->json([[
                'id' => 0,
                'nombre' => 'Cliente Genérico / Sin Registro',
                'identificacion' => 'N/A',
            ]]);
        }

        return response()->json($clientes);
    }
    
    /**
     * Listar ventas (historial) y aplicar filtros por tienda y rango de fecha.
     */
    public function index(Request $request)
    {
        $tiendas = Tienda::orderBy('nombre')->get();
        
        $query = Venta::with(['tienda', 'usuario', 'cliente'])
            ->orderBy('fecha_venta', 'desc')
            ->select('*');
            
        if ($request->filled('tienda_id') && $request->tienda_id != '') {
            $query->where('tienda_id', $request->tienda_id);
        }

        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        if ($fechaInicio && $fechaFin) {
            $query->whereDate('fecha_venta', '>=', $fechaInicio);
            $query->where('fecha_venta', '<=', Carbon::parse($fechaFin)->endOfDay());
            
        } elseif ($fechaInicio) {
            $query->whereDate('fecha_venta', $fechaInicio);
            
        } elseif ($fechaFin) {
            $query->where('fecha_venta', '<=', Carbon::parse($fechaFin)->endOfDay());
        }

        $ventas = $query->paginate(15)->withQueryString();
        
        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            // Asegúrate de que la vista exista: resources/views/ventas/partials/_ventas_table.blade.php
            return view('ventas.partials._ventas_table', compact('ventas'));
        }
        
        // Asegúrate de que la vista exista: resources/views/ventas/index.blade.php
        return view('ventas.index', compact('ventas', 'tiendas'));
    }

    public function show(Venta $venta)
    {
        $venta->load([
            'tienda',
            'usuario',
            'cliente',
            'detalles.inventario.marca.producto',
        ]);
        // Asegúrate de que la vista exista: resources/views/ventas/show.blade.php
        return view('ventas.show', compact('venta'));
    }

    /**
     * Anula una venta, revierte el stock Y registra el movimiento de anulación (ENTRADA).
     */
    public function destroy(Venta $venta)
    {
        // Si la venta no afectó stock (ej. era una QUOTE), solo la eliminamos/anulamos el registro.
        if ($venta->tipo_documento === 'QUOTE' || $venta->estado === 'ANULADA') {
            $venta->update(['estado' => 'ANULADA']); 
             return redirect()->route('ventas.index')
                ->with('success', "Documento #{$venta->id} marcado como ANULADO.");
        }
        
        DB::beginTransaction();

        try {
            $venta->load('detalles');
            
            // 1. Recorrer los detalles para revertir stock y registrar movimientos
            foreach ($venta->detalles as $detalle) {
                $inventario = Inventario::lockForUpdate()->find($detalle->inventario_id);
                
                if ($inventario) {
                    // 1a. ⭐ REGISTRAR EL MOVIMIENTO DE ENTRADA (Anulación)
                    MovimientoInventario::create([
                        'inventario_id' => $inventario->id,
                        'tipo_movimiento' => 'ENTRADA',
                        'razon' => 'Anulación Venta (' . $venta->tipo_documento . ')',
                        'cantidad' => $detalle->cantidad,
                        'movible_id' => $venta->id, // Vinculado a la VENTA (registro de anulación)
                        'movible_type' => Venta::class, 
                        'usuario_id' => auth()->id(),
                    ]);
                    
                    // 1b. Devolver el stock
                    $inventario->increment('stock', $detalle->cantidad);
                }
            }
            
            // 2. Marcar la venta como anulada
            $venta->update(['estado' => 'ANULADA']); 

            DB::commit();

            return redirect()->route('ventas.index')
                ->with('success', "Venta #{$venta->id} anulada y stock devuelto.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('ventas.index')
                ->with('error', 'Error al anular la venta: ' . $e->getMessage());
        }
    }
}