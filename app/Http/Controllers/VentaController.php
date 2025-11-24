<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Tienda;
use App\Models\Inventario;
use App\Models\Cliente;
use App\Models\MovimientoInventario;
use App\Models\RangoCai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf; 
 
class VentaController extends Controller
{

    public function create()
    {
        $tiendas = Tienda::all();
        $tiposPago = Venta::TIPOS_PAGO; 
        return view('ventas.pos', compact('tiendas', 'tiposPago'));
    }

    public function getProductosParaVenta($tienda_id)
    {
        try {
            $inventarios = Inventario::with(['marca.producto.impuesto'])
                ->where('tienda_id', $tienda_id)
                ->where('stock', '>', 0)
                ->get();

            $productos = $inventarios->map(function ($item) {
                $impuesto = $item->marca->producto->impuesto;
                $tasa = $impuesto ? ((float) $impuesto->porcentaje / 100) : 0.00;

                return [
                    'inventario_id' => $item->id,
                    'producto_nombre' => $item->marca->producto->nombre ?? 'N/A',
                    'codigo_marca' => $item->marca->codigo_marca ?? '',
                    'stock_actual' => (int) $item->stock,
                    'precio' => (float) $item->precio,
                    'isv_tasa' => $tasa,
                ];
            });

            return response()->json($productos);
        } catch (\Exception $e) {
            Log::error('Error al obtener productos para venta: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error al cargar productos.'
            ], 500);
        }
    }

    public function storeCliente(Request $request)
    {
        $identificacion = trim($request->input('identificacion', ''));
        $email = trim($request->input('email', ''));
        $telefono = trim($request->input('telefono', ''));
        $nombre = $request->input('nombre');
        
        if (!empty($identificacion)) {
            $identificacion = str_replace('-', '', $identificacion);
        }

        $request->merge([
            'identificacion' => $identificacion,
            'email' => $email,
            'telefono' => $telefono,
        ]);

        try {
            $rules = [
                'nombre' => 'required|string|max:255',
                'telefono' => 'nullable|string|max:20',
                
                'identificacion' => [
                    'nullable', 
                    'string', 
                    'max:50',
                    Rule::unique('clientes', 'identificacion')->where(fn ($query) => $query->whereNotNull('identificacion')),
                ],
                'email' => [
                    'nullable', 
                    'email', 
                    'max:255',
                    Rule::unique('clientes', 'email')->where(fn ($query) => $query->whereNotNull('email')),
                ],
            ];

            $validated = $request->validate($rules);
            
            $validated = array_map(function ($value) {
                return (is_string($value) && $value === '') ? null : $value;
            }, $validated);

            $cliente = Cliente::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Cliente guardado y seleccionado correctamente.',
                'cliente' => [
                    'id' => (int)$cliente->id, 
                    'nombre' => $cliente->nombre,
                    'identificacion' => $cliente->identificacion ?? '', 
                    'telefono' => $cliente->telefono ?? '',
                    'email' => $cliente->email ?? '',
                ],
            ], 201);
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error("Error CRÍTICO de DB/Server al guardar cliente (POS): " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error inesperado al guardar el cliente.',
            ], 500);
        }
    }

    public function buscarClientes(Request $request)
    {
        try {
            $query = trim($request->get('query', ''));
            
            if (strlen($query) < 2) {
                return response()->json([]);
            }

            $cleanQuery = str_replace(['-', ' '], '', $query);

            $clientes = Cliente::where(function ($q) use ($query, $cleanQuery) {
                    $q->where('nombre', 'like', "%{$query}%")
                      ->orWhere('identificacion', 'like', "%{$cleanQuery}%");
                })
                ->orderBy('nombre')
                ->limit(10)
                ->get(['id', 'nombre', 'identificacion', 'telefono', 'email']);

            return response()->json($clientes);
            
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Error de base de datos al buscar clientes: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error al consultar la base de datos.'
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('Error inesperado al buscar clientes: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error en el servidor al buscar clientes.'
            ], 500);
        }
    }

    public function storeTicket(Request $request)
    {
        $request->merge(['tipo_documento' => 'TICKET', 'afecta_stock' => true]);
        return $this->handleTransaction($request);
    }

    public function storeInvoice(Request $request)
    {
        if (!$request->cliente_id) { 
            return response()->json([
                'success' => false, 
                'message' => 'Un cliente registrado es obligatorio para generar una Factura.'
            ], 422);
        }
        
        $request->merge(['tipo_documento' => 'INVOICE', 'afecta_stock' => true]);
        return $this->handleTransaction($request);
    }

    public function storeQuote(Request $request)
    {
        if (!$request->cliente_id) {
            return response()->json([
                'success' => false, 
                'message' => 'El cliente es obligatorio para generar una Cotización.'
            ], 422);
        }
        
        $request->merge(['tipo_documento' => 'QUOTE', 'afecta_stock' => false]);
        return $this->handleTransaction($request);
    }
    
    protected function handleTransaction(Request $request)
    {
        try {
            $validated = $request->validate([
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
                'detalles.*.isv_tasa' => 'required|numeric|min:0|max:1', 
                'tipo_pago' => ['required', 'string', Rule::in(array_keys(Venta::TIPOS_PAGO))],
                'monto_recibido' => 'nullable|numeric|min:0',
                'vuelto' => 'nullable|numeric',
            ]);

            $tipo = $request->tipo_documento;
            $tiendaId = $request->tienda_id;
            $afectaStock = $request->afecta_stock;
            $clienteId = $request->cliente_id;
            
            $totalMonto = floatval($request->total_monto);
            $descuento = floatval($request->descuento);
            $tipoPago = $request->tipo_pago;
            
            $montoRecibido = floatval($request->monto_recibido) ?: 0;
            $vuelto = floatval($request->vuelto) ?: 0;

            DB::beginTransaction();
            
            $caiData = [];
            $rangoCai = null; 

            if ($tipo === 'INVOICE' || $tipo === 'TICKET') {
                RangoCai::where('tienda_id', $tiendaId)
                    ->where('esta_activo', true)
                    ->whereDate('fecha_limite_emision', '<', Carbon::today()) 
                    ->update(['esta_activo' => false]);
                
                $rangoCai = RangoCai::where('tienda_id', $tiendaId)
                    ->where('esta_activo', true)
                    ->lockForUpdate() 
                    ->first();

                if (!$rangoCai) {
                    throw new \Exception('No se encontró un rango CAI activo para esta tienda. No se puede emitir el documento fiscal.');
                }
                
                $numeroActualSecuencia = $rangoCai->numero_actual;
                $rangoFinalSecuencia = $rangoCai->rango_final;
                $prefijoSar = $rangoCai->prefijo_sar;

                $nuevoNumeroSecuencia = $numeroActualSecuencia + 1;
                
                if ($nuevoNumeroSecuencia > $rangoFinalSecuencia) {
                    throw new \Exception('El rango de facturación CAI ha sido excedido. Por favor, solicite un nuevo rango.');
                }
                
                $ceroPad = 8; 
                $numeroSecuencialFormateado = str_pad($nuevoNumeroSecuencia, $ceroPad, '0', STR_PAD_LEFT);
                $nuevoNumeroFormateado = $prefijoSar . $numeroSecuencialFormateado;

                $caiData['cai'] = $rangoCai->cai;
                $caiData['numero_documento'] = $nuevoNumeroFormateado;
                
                $rangoCai->numero_actual = $nuevoNumeroSecuencia;

                if ($nuevoNumeroSecuencia == $rangoFinalSecuencia) {
                    $rangoCai->esta_activo = false;
                }
            }

            // Verificación de Stock
            if ($afectaStock) {
                foreach ($request->detalles as $detalle) {
                    $inventario = Inventario::lockForUpdate()->find($detalle['inventario_id']);
                    
                    if (!$inventario) {
                        throw new \Exception('Inventario no encontrado.');
                    }
                    
                    if ($inventario->stock < $detalle['cantidad']) {
                        $nombre = $inventario->marca->producto->nombre ?? 'Producto';
                        throw ValidationException::withMessages([
                            'stock' => "Stock insuficiente para '{$nombre}'. Disponible: {$inventario->stock}, solicitado: {$detalle['cantidad']}."
                        ]);
                    }
                }
            }
            
            // Crear documento (Venta)
            $documento = Venta::create([
                'tienda_id' => $tiendaId,
                'cliente_id' => $clienteId,
                'fecha_venta' => now(),
                'total_venta' => $totalMonto,
                'descuento' => $descuento,
                'usuario_id' => auth()->id(),
                'tipo_documento' => $tipo,
                'tipo_pago' => $tipoPago,
                'estado' => ($tipo === 'QUOTE' ? 'PENDIENTE' : 'COMPLETADA'),
                
                'cai' => $caiData['cai'] ?? null,
                'numero_documento' => $caiData['numero_documento'] ?? null,

                'monto_recibido' => ($tipoPago === 'EFECTIVO' ? $montoRecibido : null),
                'vuelto' => ($tipoPago === 'EFECTIVO' ? $vuelto : null),
            ]);

            // Procesar detalles, calcular ISV y totales fiscales
            $totalIsv = 0;
            $subtotalNetoVenta = 0;
            $subtotalGravadoVenta = 0;
            $subtotalExoneradoVenta = 0;
            
            foreach ($request->detalles as $detalleData) {
                $cantidad = (int) $detalleData['cantidad'];
                $precioUnitario = (float) $detalleData['precio_unitario'];
                $isvTasa = (float) $detalleData['isv_tasa'];
                
                $subtotalBase = $cantidad * $precioUnitario;
                $isvMonto = round($subtotalBase * $isvTasa, 2); 
                $subtotalFinal = $subtotalBase + $isvMonto; 
                
                $totalIsv += $isvMonto;
                $subtotalNetoVenta += $subtotalBase;

                if ($isvTasa > 0) {
                    $subtotalGravadoVenta += $subtotalBase;
                } else {
                    $subtotalExoneradoVenta += $subtotalBase;
                }

                $detalleVenta = $documento->detalles()->create([
                    'venta_id' => $documento->id,
                    'inventario_id' => $detalleData['inventario_id'],
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'subtotal_base' => $subtotalBase, 
                    'isv_tasa' => $isvTasa, 
                    'isv_monto' => $isvMonto, 
                    'subtotal_final' => $subtotalFinal, 
                ]);

                // Afectar Stock y Registrar Movimiento
                if ($afectaStock) {
                    $inventario = Inventario::lockForUpdate()->find($detalleData['inventario_id']);
                    
                    MovimientoInventario::create([
                        'inventario_id' => $inventario->id,
                        'tipo_movimiento' => 'SALIDA',
                        'razon' => "Venta ({$tipo})",
                        'cantidad' => $cantidad,
                        'movible_id' => $detalleVenta->id,
                        'movible_type' => DetalleVenta::class,
                        'usuario_id' => auth()->id(),
                    ]);
                    
                    $inventario->decrement('stock', $cantidad);
                }
            }

            // Actualizar totales fiscales en el documento principal
            $documento->subtotal_neto = $subtotalNetoVenta;
            $documento->subtotal_gravado = $subtotalGravadoVenta;
            $documento->subtotal_exonerado = $subtotalExoneradoVenta;
            $documento->total_isv = $totalIsv;
            $documento->total_final = ($subtotalNetoVenta + $totalIsv) - $descuento;
            $documento->save();

            // Guardar la actualización del Rango CAI
            if (($tipo === 'INVOICE' || $tipo === 'TICKET') && $rangoCai) {
                $rangoCai->save();
            }

            DB::commit();

            $documentoUrl = route('ventas.print', ['id' => $documento->id, 'type' => $tipo]);

            return response()->json([
                'success' => true,
                'message' => "Transacción tipo {$tipo} registrada exitosamente.",
                'documento_id' => $documento->id,
                'documento_url' => $documentoUrl
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => 'Error de validación: ' . $e->getMessage(),
                'errors' => $e->errors() 
            ], 422);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al procesar la transacción ({$tipo}): " . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error al procesar la transacción. Detalle: ' . $e->getMessage() 
            ], 500);
        }
    }

    public function index(Request $request)
    {
        $tiendas = Tienda::orderBy('nombre')->get();
        
        $query = Venta::with(['tienda', 'usuario', 'cliente', 'detalles.inventario.marca.producto'])
            ->orderBy('fecha_venta', 'desc');
            
        if ($request->filled('tienda_id') && $request->tienda_id != '') {
            $query->where('tienda_id', $request->tienda_id);
        }

        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        if ($fechaInicio && $fechaFin) {
            $query->whereDate('fecha_venta', '>=', $fechaInicio)
                  ->whereDate('fecha_venta', '<=', $fechaFin);
        } elseif ($fechaInicio) {
            $query->whereDate('fecha_venta', $fechaInicio);
        } elseif ($fechaFin) {
            $query->whereDate('fecha_venta', '<=', $fechaFin);
        }

        $queryForSum = clone $query;
        $totalVentasSum = $queryForSum->sum('total_final');

        $ventas = $query->paginate(15)->withQueryString();
        
        $data = compact('ventas', 'tiendas', 'totalVentasSum');

        if ($request->ajax()) {
            return view('ventas.partials._ventas_table', $data);
        }
        
        return view('ventas.index', $data);
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
        if ($venta->estado === 'ANULADA') {
            return redirect()->route('ventas.index')
                ->with('info', "La venta #{$venta->id} ya está anulada.");
        }
        
        if ($venta->tipo_documento === 'QUOTE') {
            $venta->update(['estado' => 'ANULADA']);
            return redirect()->route('ventas.index')
                ->with('success', "Cotización #{$venta->id} anulada.");
        }
        
        DB::beginTransaction();

        try {
            $venta->load('detalles');
            
            foreach ($venta->detalles as $detalle) {
                $inventario = Inventario::lockForUpdate()->find($detalle->inventario_id);
                
                if ($inventario) {
                    MovimientoInventario::create([
                        'inventario_id' => $inventario->id,
                        'tipo_movimiento' => 'ENTRADA',
                        'razon' => "Anulación Venta ({$venta->tipo_documento})",
                        'cantidad' => $detalle->cantidad,
                        'movible_id' => $venta->id,
                        'movible_type' => Venta::class,
                        'usuario_id' => auth()->id(),
                    ]);
                    
                    $inventario->increment('stock', $detalle->cantidad);
                }
            }
            
            $venta->update(['estado' => 'ANULADA']);

            DB::commit();

            return redirect()->route('ventas.index')
                ->with('success', "Venta #{$venta->id} anulada y stock devuelto.");
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al anular venta: " . $e->getMessage());
            return redirect()->route('ventas.index')
                ->with('error', 'Error al anular la venta: ' . $e->getMessage());
        }
    }

    public function printDocument($id, $type)
    {
        $venta = Venta::with([
            'tienda',
            'cliente',
            'usuario',
            'detalles.inventario.marca.producto',
            'tienda.rangosCai' 
        ])->findOrFail($id);
        
        $rangoCaiActivo = $venta->tienda->rangosCai->where('esta_activo', true)
            ->filter(function ($rango) {
                return Carbon::parse($rango->fecha_limite_emision)->gte(Carbon::today());
            })
            ->first();
        
        $data = compact('venta', 'type', 'rangoCaiActivo');
        
        $html = view('ventas.print', $data)->render();

        $pdf = Pdf::loadHtml($html);
        
        $pdf->setPaper([0, 0, 226.77, 841.89], 'portrait'); 
        $pdf->setOption('margin-top', 0);
        $pdf->setOption('margin-right', 0);
        $pdf->setOption('margin-bottom', 0);
        $pdf->setOption('margin-left', 0);
        
        $filename = "{$type}_" . ($venta->numero_documento ?? $venta->id) . ".pdf";
        
        return $pdf->stream($filename); 
    }

    public function showDevolucionForm(Request $request)
    {
        $venta = null;
        $detalles = collect();

        $numeroDocumento = $request->input('numero_documento');
        
        if ($numeroDocumento) {
            $venta = Venta::where('numero_documento', $numeroDocumento)
                           ->orWhere('id', $numeroDocumento)
                           ->with(['detalles', 'detalles.inventario.marca.producto', 'cliente']) 
                           ->first();
            
            if ($venta && $venta->estado === 'COMPLETADA') {
                $detalles = $venta->detalles->where('cantidad', '>', 0);
            } elseif ($venta && $venta->estado === 'ANULADA') {
                $venta = null;
                return back()->with('error', 'La venta ya ha sido ANULADA completamente.');
            }
        }

        return view('ventas.devolucion', compact('venta', 'detalles', 'numeroDocumento'));
    }

    public function processDevolucion(Request $request, Venta $venta)
    {

        $validated = $request->validate([
            'devoluciones' => 'required|array|min:1',
            'devoluciones.*.detalle_id' => 'required|integer|exists:detalle_ventas,id',
            'devoluciones.*.cantidad' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $totalMontoAfectado = 0;
            $isvDevueltoGlobal = 0; 
            $baseDevueltaGlobal = 0;
            $baseGravadaDevuelta = 0;
            $baseExoneradaDevuelta = 0;

            foreach ($validated['devoluciones'] as $devolucion) {
                $detalleId = $devolucion['detalle_id'];
                $cantidadDevuelta = $devolucion['cantidad'];

                $detalle = DetalleVenta::lockForUpdate()->findOrFail($detalleId);
                $detalle->load('inventario.marca.producto');
                $inventario = Inventario::lockForUpdate()->findOrFail($detalle->inventario_id);

                if ($cantidadDevuelta > $detalle->cantidad) {
                    throw ValidationException::withMessages([
                        'cantidad' => 'La cantidad devuelta excede la cantidad vendida para el producto ' . ($detalle->inventario->marca->producto->nombre ?? 'N/A')
                    ]);
                }

                // Calcular valores afectados
                $montoUnitarioBase = (float) $detalle->precio_unitario;
                $isvTasa = (float) $detalle->isv_tasa;

                $montoBaseDevuelto = $cantidadDevuelta * $montoUnitarioBase;
                $isvDevuelto = round($montoBaseDevuelto * $isvTasa, 2);
                $totalLineaDevuelta = $montoBaseDevuelto + $isvDevuelto;
                
                $totalMontoAfectado += $totalLineaDevuelta;
                $isvDevueltoGlobal += $isvDevuelto;
                $baseDevueltaGlobal += $montoBaseDevuelto;
                
                // Separar base gravada vs exonerada
                if ($isvTasa > 0) {
                    $baseGravadaDevuelta += $montoBaseDevuelto;
                } else {
                    $baseExoneradaDevuelta += $montoBaseDevuelto;
                }

                // Registrar Movimiento de ENTRADA (Devolución)
                MovimientoInventario::create([
                    'inventario_id' => $inventario->id,
                    'tipo_movimiento' => 'ENTRADA',
                    'razon' => "Devolución Parcial/Anulación de Detalle (Venta #{$venta->id})",
                    'cantidad' => $cantidadDevuelta,
                    'movible_id' => $detalle->id,
                    'movible_type' => DetalleVenta::class,
                    'usuario_id' => auth()->id(),
                ]);

                // Ajustar Stock
                $inventario->increment('stock', $cantidadDevuelta);

                // Actualizar el Detalle de Venta
                $detalle->cantidad -= $cantidadDevuelta;
                $detalle->subtotal_base -= $montoBaseDevuelto;
                $detalle->isv_monto -= $isvDevuelto;
                $detalle->subtotal_final -= $totalLineaDevuelta;
                    
                if ($detalle->cantidad <= 0) {
                     $detalle->delete();
                } else {
                     $detalle->save();
                }
            }

            // Actualizar Totales del Documento (Venta)
            $venta->subtotal_neto -= $baseDevueltaGlobal; 
            $venta->subtotal_gravado -= $baseGravadaDevuelta;
            $venta->subtotal_exonerado -= $baseExoneradaDevuelta;
            $venta->total_isv -= $isvDevueltoGlobal;
            $venta->total_final -= $totalMontoAfectado;
            
            if ($venta->detalles()->count() === 0) {
                 $venta->estado = 'ANULADA'; 
            }
            
            $venta->save();
            
            DB::commit();

            return redirect()->route('ventas.index')
                ->with('success', "Devolución de productos registrada exitosamente. Stock ajustado y Venta #{$venta->id} actualizada.");

        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al procesar devolución de productos: " . $e->getMessage());
            return back()->with('error', 'Error crítico al procesar la devolución: ' . $e->getMessage());
        }
    }
}