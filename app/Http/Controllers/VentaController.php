<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Tienda;
use App\Models\Inventario;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VentaController extends Controller
{
    // ===========================================
    // VISTAS Y DATOS BASE
    // ===========================================

    public function create()
    {
        $tiendas = Tienda::all();
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
     * Función centralizada para ejecutar la transacción.
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

            // 4. Procesamiento de Detalles y Stock
            foreach ($request->detalles as $detalle) {
                $documento->detalles()->create([
                    'venta_id' => $documento->id,
                    'inventario_id' => $detalle['inventario_id'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'subtotal' => ($detalle['cantidad'] * $detalle['precio_unitario']),
                ]);

                if ($afectaStock) {
                    $inventario = Inventario::lockForUpdate()->find($detalle['inventario_id']);
                    $inventario->decrement('stock', $detalle['cantidad']);
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
        
        // Esta comprobación se eliminó, confiamos en el tipo pasado por la URL para la vista
        /*
        if (strtolower($documento->tipo_documento) !== strtolower($tipo)) {
             abort(403, 'El tipo de documento solicitado no coincide con el registro.');
        }
        */

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
     * Listar ventas (historial).
     */
    public function index()
    {
        // Se asegura de cargar el tipo_documento para que la vista pueda generar los enlaces de impresión
        $ventas = Venta::with(['tienda', 'usuario', 'cliente'])
            ->orderBy('fecha_venta', 'desc')
            ->select('*') 
            ->paginate(15);
            
        return view('ventas.index', compact('ventas'));
    }

    public function show(Venta $venta)
    {
        $venta->load([
            'tienda',
            'usuario',
            'cliente',
            'detalles.inventario.marca.producto',
        ]);
        return view('ventas.show', compact('venta'));
    }

    public function destroy(Venta $venta)
    {
        DB::beginTransaction();

        try {
            $venta->load('detalles');
            foreach ($venta->detalles as $detalle) {
                $inventario = Inventario::lockForUpdate()->find($detalle->inventario_id);
                if ($inventario) {
                    $inventario->increment('stock', $detalle->cantidad);
                }
            }

            $venta->delete();
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