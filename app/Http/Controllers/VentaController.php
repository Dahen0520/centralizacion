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
use Barryvdh\DomPDF\Facade\Pdf; // Importación correcta para DomPDF
 
class VentaController extends Controller
{
    /**
     * Mostrar interfaz del POS
     */
    public function create()
    {
        $tiendas = Tienda::all();
        // CRÍTICO: Cargar la lista de tipos de pago desde el modelo.
        $tiposPago = Venta::TIPOS_PAGO; 
        return view('ventas.pos', compact('tiendas', 'tiposPago'));
    }

    /**
     * Obtener productos con stock de una tienda (CON TASA DE IMPUESTO DINÁMICA)
     */
    public function getProductosParaVenta($tienda_id)
    {
        try {
            // Carga anidada: Inventario -> Marca -> Producto -> Impuesto
            $inventarios = Inventario::with(['marca.producto.impuesto'])
                ->where('tienda_id', $tienda_id)
                ->where('stock', '>', 0)
                ->get();

            $productos = $inventarios->map(function ($item) {
                // Obtener el porcentaje de impuesto (0.15, 0.00, etc.)
                $impuesto = $item->marca->producto->impuesto;
                // Si no hay impuesto asignado o es nulo, usamos 0.00. Dividimos entre 100 para obtener la tasa (0.15).
                $tasa = $impuesto ? ((float) $impuesto->porcentaje / 100) : 0.00;

                return [
                    'inventario_id' => $item->id,
                    'producto_nombre' => $item->marca->producto->nombre ?? 'N/A',
                    'codigo_marca' => $item->marca->codigo_marca ?? '',
                    'stock_actual' => (int) $item->stock,
                    'precio' => (float) $item->precio,
                    'isv_tasa' => $tasa, // Tasa de impuesto real para el cálculo en el frontend
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

    /**
     * Guardar nuevo cliente desde el POS
     */
    public function storeCliente(Request $request)
    {
        // 1. Saneamiento: Limpiamos y aseguramos que los valores vacíos sean tratados como tales.
        $identificacion = trim($request->input('identificacion', ''));
        $email = trim($request->input('email', ''));
        $telefono = trim($request->input('telefono', ''));
        $nombre = $request->input('nombre');
        
        // Limpiar RTN antes de la validación
        if (!empty($identificacion)) {
            $identificacion = str_replace('-', '', $identificacion);
        }

        // Fusionar los datos limpios al request para que la validación los use.
        $request->merge([
            'identificacion' => $identificacion,
            'email' => $email,
            'telefono' => $telefono,
        ]);


        try {
            // 2. Validación
            $rules = [
                'nombre' => 'required|string|max:255',
                'telefono' => 'nullable|string|max:20',
                
                // Regla para Identificación: Solo comprueba unicidad si el valor es NOT NULL.
                'identificacion' => [
                    'nullable', 
                    'string', 
                    'max:50',
                    Rule::unique('clientes', 'identificacion')->where(fn ($query) => $query->whereNotNull('identificacion')),
                ],
                // Regla para Email: Solo comprueba unicidad si el valor es NOT NULL.
                'email' => [
                    'nullable', 
                    'email', 
                    'max:255',
                    Rule::unique('clientes', 'email')->where(fn ($query) => $query->whereNotNull('email')),
                ],
            ];

            $validated = $request->validate($rules);
            
            // 3. Convertir cadenas vacías ('') a NULL.
            $validated = array_map(function ($value) {
                return (is_string($value) && $value === '') ? null : $value;
            }, $validated);

            $cliente = Cliente::create($validated);

            // 4. RESPUESTA DE ÉXITO ESTÁNDAR
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
            // Maneja errores de validación (422)
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            // Manejo de errores no validados, asume fallo y registra.
            Log::error("Error CRÍTICO de DB/Server al guardar cliente (POS): " . $e->getMessage());
            
            // Devolver error 500 para evitar que el frontend asuma éxito y cierre el modal.
            return response()->json([
                'success' => false,
                'message' => 'Error inesperado al guardar el cliente.',
            ], 500);
        }
    }


    /**
     * Búsqueda de clientes en tiempo real
     */
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

    /**
     * Procesar Ticket de Venta (Cliente genérico/NULL permitido)
     * Debe consumir CAI.
     */
    public function storeTicket(Request $request)
    {
        // Si no se envía cliente_id, se guardará como NULL, lo cual se asume como Cliente Genérico.
        $request->merge(['tipo_documento' => 'TICKET', 'afecta_stock' => true]);
        return $this->handleTransaction($request);
    }

    /**
     * Procesar Factura (Cliente registrado obligatorio)
     * Debe consumir CAI.
     */
    public function storeInvoice(Request $request)
    {
        // Factura requiere un cliente_id no nulo
        if (!$request->cliente_id) { 
            return response()->json([
                'success' => false, 
                'message' => 'Un cliente registrado es obligatorio para generar una Factura.'
            ], 422);
        }
        
        $request->merge(['tipo_documento' => 'INVOICE', 'afecta_stock' => true]);
        return $this->handleTransaction($request);
    }

    /**
     * Procesar Cotización (Cliente registrado obligatorio)
     * NO consume CAI, NO afecta stock.
     */
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
    
    /**
     * Lógica central para procesar transacciones
     * Asigna CAI para TICKET e INVOICE.
     */
    protected function handleTransaction(Request $request)
    {
        try {
            $validated = $request->validate([
                'tienda_id' => 'required|exists:tiendas,id',
                'cliente_id' => 'nullable|exists:clientes,id', // Cliente puede ser NULL para TICKET
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
            ]);

            $tipo = $request->tipo_documento;
            $tiendaId = $request->tienda_id;
            $afectaStock = $request->afecta_stock;
            $clienteId = $request->cliente_id;
            
            $totalMonto = floatval($request->total_monto);
            $descuento = floatval($request->descuento);
            $tipoPago = $request->tipo_pago;
            
            DB::beginTransaction();
            
            // =========================================
            // LÓGICA DE CAI/FACTURACIÓN (TICKET e INVOICE) 🔑
            // =========================================
            $caiData = [];
            
            // La asignación de CAI es obligatoria para TICKET e INVOICE
            if ($tipo === 'INVOICE' || $tipo === 'TICKET') {
                
                // 1. Bloquear y buscar un rango CAI activo y válido para la tienda
                $rangoCai = RangoCai::where('tienda_id', $tiendaId)
                    ->where('esta_activo', true)
                    ->whereDate('fecha_limite_emision', '>=', Carbon::today())
                    ->lockForUpdate() 
                    ->first();

                if (!$rangoCai) {
                    throw new \Exception('No se encontró un rango CAI activo y válido para esta tienda. No se puede emitir el documento fiscal.');
                }
                
                // 2. Obtener la secuencia numérica
                $numeroActualSecuencia = $rangoCai->numero_actual;
                $rangoFinalSecuencia = $rangoCai->rango_final;
                $prefijoSar = $rangoCai->prefijo_sar;

                $nuevoNumeroSecuencia = $numeroActualSecuencia + 1;
                
                // 3. Comprobar si se excede el rango
                if ($nuevoNumeroSecuencia > $rangoFinalSecuencia) {
                    throw new \Exception('El rango de facturación CAI ha sido excedido. Por favor, solicite un nuevo rango.');
                }
                
                // 4. Reconstruir el número de factura completo
                $ceroPad = 8; 
                $numeroSecuencialFormateado = str_pad($nuevoNumeroSecuencia, $ceroPad, '0', STR_PAD_LEFT);
                $nuevoNumeroFormateado = $prefijoSar . $numeroSecuencialFormateado;

                $caiData['cai'] = $rangoCai->cai;
                $caiData['numero_documento'] = $nuevoNumeroFormateado;
                
                // 5. Preparar la actualización del CAI
                $rangoCai->numero_actual = $nuevoNumeroSecuencia;
            }

            // 1. Verificación de Stock (Si afecta stock)
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
            
            // 2. Crear documento (Venta)
            $documento = Venta::create([
                'tienda_id' => $tiendaId,
                'cliente_id' => $clienteId, // Permite NULL
                'fecha_venta' => now(),
                'total_venta' => $totalMonto,
                'descuento' => $descuento,
                'usuario_id' => auth()->id(),
                'tipo_documento' => $tipo,
                'tipo_pago' => $tipoPago,
                'estado' => ($tipo === 'QUOTE' ? 'PENDIENTE' : 'COMPLETADA'),
                
                // Asignación de CAI y Número de Documento
                'cai' => $caiData['cai'] ?? null,
                'numero_documento' => $caiData['numero_documento'] ?? null,
            ]);

            // 3. Procesar detalles, calcular ISV y totales fiscales
            $totalIsv = 0;
            $subtotalNetoVenta = 0;
            $subtotalGravadoVenta = 0;
            $subtotalExoneradoVenta = 0;
            
            foreach ($request->detalles as $detalleData) {
                
                $cantidad = (int) $detalleData['cantidad'];
                $precioUnitario = (float) $detalleData['precio_unitario'];
                $isvTasa = (float) $detalleData['isv_tasa'];
                
                // Cálculos
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

                // 4. Afectar Stock y Registrar Movimiento (solo si afecta stock)
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

            // 5. Actualizar totales fiscales en el documento principal
            $documento->subtotal_neto = $subtotalNetoVenta;
            $documento->subtotal_gravado = $subtotalGravadoVenta;
            $documento->subtotal_exonerado = $subtotalExoneradoVenta;
            $documento->total_isv = $totalIsv;
            $documento->total_final = ($subtotalNetoVenta + $totalIsv) - $descuento;
            $documento->save();

            // 6. Actualizar el Rango CAI (SOLO si es TICKET o INVOICE)
            if ($tipo === 'INVOICE' || $tipo === 'TICKET') {
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
    
    /**
     * Listado de ventas con filtros
     */
    public function index(Request $request)
    {
        $tiendas = Tienda::orderBy('nombre')->get();
        
        $query = Venta::with(['tienda', 'usuario', 'cliente'])
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

        $ventas = $query->paginate(15)->withQueryString();
        
        if ($request->ajax()) {
            return view('ventas.partials._ventas_table', compact('ventas'));
        }
        
        return view('ventas.index', compact('ventas', 'tiendas'));
    }

    /**
     * Ver detalle de una venta
     */
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

    /**
     * Anular una venta
     */
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
            
            // Si era una Factura/Ticket, no se revierte el número CAI, solo se marca como anulada.
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

    /**
     * Imprimir documento (Genera PDF)
     */
    public function printDocument($id, $type)
    {
        $venta = Venta::with([
            'tienda',
            'cliente',
            'usuario',
            'detalles.inventario.marca.producto',
            'tienda.rangosCai' 
        ])->findOrFail($id);
        
        // Obtener el RangoCai activo (usamos Collection filter para evitar el error anterior)
        $rangoCaiActivo = $venta->tienda->rangosCai->where('esta_activo', true)
            ->filter(function ($rango) {
                // Carbon::today() asegura que la comparación incluya el día actual
                return Carbon::parse($rango->fecha_limite_emision)->gte(Carbon::today());
            })
            ->first();
        
        // NOTA: La variable $type aquí es el tipo de documento DE LA RUTA. 
        // Usamos $venta->tipo_documento para la lógica de visualización en la vista de impresión.
        $data = compact('venta', 'type', 'rangoCaiActivo');
        
        // Carga la vista Blade (print.blade.php) como HTML
        $html = view('ventas.print', $data)->render();

        // Genera el PDF a partir del HTML
        $pdf = Pdf::loadHtml($html);
        
        $filename = "{$type}_" . ($venta->numero_documento ?? $venta->id) . ".pdf";
        
        // Devuelve el PDF para que se muestre en el navegador
        return $pdf->stream($filename); 
    }
}