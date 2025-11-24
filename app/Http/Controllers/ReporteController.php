<?php

namespace App\Http\Controllers;

use App\Models\Tienda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteController extends Controller
{

    public function index()
    {
        return view('reportes.index');
    }

    public function showCierreCajaForm()
    {
        $tiendas = Tienda::all(['id', 'nombre']);
        return view('reportes.cierre_caja_form', compact('tiendas'));
    }

    public function generarCierreCajaReporte(Request $request)
    {

        try {
            $validated = $request->validate([
                'fecha' => 'required|date',
                'tienda_id' => 'required|exists:tiendas,id',
            ]);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        $fechaCierre = $validated['fecha'];
        $tiendaId = $validated['tienda_id'];

        DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

        $ventasPorEmpresa = DB::table('ventas')
            ->join('detalle_ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
            ->join('inventarios', 'detalle_ventas.inventario_id', '=', 'inventarios.id')
            ->join('marcas', 'inventarios.marca_id', '=', 'marcas.id') 
            ->join('empresas', 'marcas.empresa_id', '=', 'empresas.id') 
            ->whereDate('ventas.fecha_venta', $fechaCierre)
            ->where('ventas.tienda_id', $tiendaId)
            ->where('ventas.estado', 'COMPLETADA')
            ->select(
                'empresas.nombre_negocio',
                'empresas.id as empresa_id',
                DB::raw('SUM(detalle_ventas.subtotal_base) as subtotal_base_total'),
                DB::raw('SUM(detalle_ventas.isv_monto) as isv_monto_total'),
                DB::raw('SUM(detalle_ventas.subtotal_final) as total_bruto_empresa'),
                // CMV SIMULADO
                DB::raw('0.00 as costo_total_venta'),
                // Utilidad = Ingreso Base - Costo (0.00)
                DB::raw('SUM(detalle_ventas.subtotal_base - 0.00) as utilidad_bruta')
            )
            ->groupBy('empresas.id', 'empresas.nombre_negocio')
            ->get();
            
        // Cálculo del TOTAL GENERAL
        $totalGeneral = DB::table('ventas')
            ->whereDate('fecha_venta', $fechaCierre)
            ->where('tienda_id', $tiendaId)
            ->where('estado', 'COMPLETADA')
            ->select(
                DB::raw('COUNT(id) as total_documentos'),
                DB::raw('SUM(subtotal_neto) as total_bruto'),
                DB::raw('SUM(descuento) as total_descuentos'),
                DB::raw('SUM(total_isv) as total_impuestos'),
                DB::raw('SUM(total_final) as total_neto')
            )
            ->first();

        if (($totalGeneral->total_documentos ?? 0) === 0) {
             return back()->with('error', 'No se encontraron ventas completadas para la fecha y tienda seleccionadas.')->withInput();
        }

        $pagos = DB::table('ventas')
            ->whereDate('fecha_venta', $fechaCierre)
            ->where('tienda_id', $tiendaId)
            ->where('estado', 'COMPLETADA') 
            ->select(
                'tipo_pago',
                DB::raw('SUM(total_final) as total_pagado_por_metodo')
            )
            ->whereNotNull('tipo_pago')
            ->groupBy('tipo_pago')
            ->get()->map(function ($pago) {
                $pago->metodo_pago = $pago->tipo_pago;
                return $pago;
            });
        
        $tienda = Tienda::find($tiendaId);

        $cierreData = [
            'tienda' => $tienda->nombre,
            'fecha' => Carbon::parse($fechaCierre)->format('d/m/Y'),
            'documentos_general' => [
                'total_documentos' => $totalGeneral->total_documentos,
                'total_descuentos' => $totalGeneral->total_descuentos ?? 0,
                'total_neto' => $totalGeneral->total_neto ?? 0,
            ],
            'ventas_por_empresa' => $ventasPorEmpresa, 
            'metodos_pago' => $pagos,
        ];

        return view('reportes.cierre_caja', compact('cierreData')); 
    }

    public function reporte(Request $request) 
    {
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $tiendaId = $request->input('tienda_id');
        $export = $request->has('export'); 

        DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

        $posRate = 0.0125;          // 1.25%
        $tiendaTarjetaRate = 0.094642; // 9.4642%
        $tiendaEfectivoRate = 0.107142; // 10.7142%
        $retencionRate = 0.15;      // 15%

        // SQL para la retención condicional
        $retencionSql = "CASE WHEN empresas.facturacion = 0 THEN {$retencionRate} ELSE 0 END";

        // SQL para el total de la venta por tipo de pago
        $ventaTarjetaSql = "CASE WHEN ventas.tipo_pago = 'TARJETA' THEN detalle_ventas.subtotal_final ELSE 0 END";
        $ventaEfectivoSql = "CASE WHEN ventas.tipo_pago = 'EFECTIVO' THEN detalle_ventas.subtotal_final ELSE 0 END";
        
        // CÁLCULO DE DEDUCCIONES Y TOTAL EMPRESARIO
        $deduccionTotalTarjSql = "({$posRate} + {$tiendaTarjetaRate} + {$retencionSql})";
        $totalEmpresarioTarjSql = "SUM(CASE WHEN ventas.tipo_pago = 'TARJETA' THEN detalle_ventas.subtotal_final * (1 - {$deduccionTotalTarjSql}) ELSE 0 END)";
        
        $deduccionTotalEfectivoSql = "({$tiendaEfectivoRate} + {$retencionSql})";
        $totalEmpresarioEfectivoSql = "SUM(CASE WHEN ventas.tipo_pago = 'EFECTIVO' THEN detalle_ventas.subtotal_final * (1 - {$deduccionTotalEfectivoSql}) ELSE 0 END)";
        
        // CÁLCULO DE AMBAS COMISIONES DE TIENDA SUMADAS
        $totalComisionTiendaSql = "SUM({$ventaTarjetaSql} * {$tiendaTarjetaRate}) + SUM({$ventaEfectivoSql} * {$tiendaEfectivoRate})";


        // Consulta de Ingresos Agrupados por Afiliado/Empresa
        $query = DB::table('ventas')
            ->join('detalle_ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
            ->join('inventarios', 'detalle_ventas.inventario_id', '=', 'inventarios.id')
            ->join('marcas', 'inventarios.marca_id', '=', 'marcas.id') 
            ->join('empresas', 'marcas.empresa_id', '=', 'empresas.id') 
            ->join('afiliados', 'empresas.afiliado_id', '=', 'afiliados.id') 
            
            // Condiciones de estado y fechas
            ->where('ventas.estado', 'COMPLETADA') 
            ->when($fechaInicio, function ($query, $fechaInicio) {
                return $query->whereDate('ventas.fecha_venta', '>=', $fechaInicio);
            })
            ->when($fechaFin, function ($query, $fechaFin) {
                return $query->whereDate('ventas.fecha_venta', '<=', $fechaFin);
            })
            ->when($tiendaId, function ($query, $tiendaId) {
                return $query->where('ventas.tienda_id', $tiendaId);
            });

        $selectFields = [
            // 1. NÚMERO DE CUENTA
            'afiliados.numero_cuenta',
            // 2. CUENTAHABIENTE
            'afiliados.nombre as nombre_afiliado',
            // 3. EMPRESA
            'empresas.nombre_negocio',
            'empresas.facturacion', 
            
            // 4. VENTA CON TARJETA
            DB::raw("SUM({$ventaTarjetaSql}) as ingresos_tarjeta"),
            
            // 5. RETENCIÓN TARJETA
            DB::raw("SUM(CASE WHEN empresas.facturacion = 0 AND ventas.tipo_pago = 'TARJETA' THEN detalle_ventas.subtotal_final * {$retencionRate} ELSE 0 END) as retencion_tarjeta"),
            
            // 6. COMISIÓN POS
            DB::raw("SUM({$ventaTarjetaSql} * {$posRate}) as comision_pos"),
            
            // 7. COMISIÓN TIENDA
            DB::raw("SUM({$ventaTarjetaSql} * {$tiendaTarjetaRate}) as comision_tienda_tarjeta"),
            
            // 8. TOTAL EMPRESARIO / VTA TARJETAS
            DB::raw("{$totalEmpresarioTarjSql} as total_empresario_tarjeta"),
            
            // 9. VENTA CON EFECTIVO
            DB::raw("SUM({$ventaEfectivoSql}) as ingresos_efectivo"),
            
            // 10. RETENCIÓN EFECTIVO
            DB::raw("SUM(CASE WHEN empresas.facturacion = 0 AND ventas.tipo_pago = 'EFECTIVO' THEN detalle_ventas.subtotal_final * {$retencionRate} ELSE 0 END) as retencion_efectivo"),
            
            // 11. COMISIÓN TIENDA EFECTIVO
            DB::raw("SUM({$ventaEfectivoSql} * {$tiendaEfectivoRate}) as comision_tienda_efectivo"),
            
            // 12. TOTAL EMPRESARIO EFECTIVO
            DB::raw("{$totalEmpresarioEfectivoSql} as total_empresario_efectivo"),
            
            // 13. TOTAL A DEPOSITAR A EMPRENDEDOR
            DB::raw("({$totalEmpresarioTarjSql} + {$totalEmpresarioEfectivoSql}) as total_a_depositar"),
            
            // 14. CONTRIBUCIÓN DEL EMPRENDEDOR (CERO)
            DB::raw('0.00 as contribucion_emprendedor'),
            
            // 15. TOTAL COMISIÓN TIENDA (Suma de ambas comisiones)
            DB::raw("{$totalComisionTiendaSql} as total_comision_tienda")
        ];

        $reporte = $query->select($selectFields)
            ->groupBy(
                'empresas.facturacion', 
                'afiliados.numero_cuenta', 
                'afiliados.nombre', 
                'empresas.nombre_negocio'
            )
            ->orderByDesc(DB::raw('SUM(detalle_ventas.subtotal_final)'))
            ->get();
            
        // LÓGICA DE EXPORTACIÓN (CSV)
        if ($export) {
            return $this->exportToCsv($reporte);
        }

        // Devolver la vista con los datos
        $tiendas = Tienda::all(['id', 'nombre']); 
        
        return view('reportes.resumen_afiliados', [
            'reporte' => $reporte,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'tiendas' => $tiendas,      
            'tiendaIdSeleccionada' => $tiendaId, 
        ]);
    }

    private function exportToCsv($reporte)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="resumen_ingresos_afiliados_' . Carbon::now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function() use ($reporte) {
            $file = fopen('php://output', 'w');

            $headerGroup1 = [
                'INFORMACIÓN GENERAL', '', '', 
                'TRANSACCIONES CON TARJETA', '', '', '', '', '', 
                'TRANSACCIONES EN EFECTIVO', '', '', '', '', '', 
                'RESUMEN FINAL', '', '', 
            ];
            fputcsv($file, $headerGroup1, ';'); 

            $headerRow2 = [
                'Núm. Cuenta', 
                'Cuentahabiente', 
                'Empresa', 
                
                // Tarjeta
                'Venta Tarjeta', 
                'Fact. (Tarj)', 
                'Retención (Tarj)', 
                'Comisión POS (1.25%)', 
                'Comisión Tienda (9.46%)', 
                'Total Empresario/Tarjeta', 

                // Efectivo
                'Venta Efectivo', 
                'Fact. (Efec)', 
                'Retención (Efec)', 
                'Com. Tienda (10.71%)', 
                'Total Empresario/Efectivo', 
                
                // Final
                'TOTAL A DEPOSITAR', 
                'Contribución Emprendedor', 
                'Total Com. Tienda',
            ];
            fputcsv($file, $headerRow2, ';'); 

            foreach ($reporte as $row) {
                fputcsv($file, [
                    $row->numero_cuenta,
                    $row->nombre_afiliado,
                    $row->nombre_negocio,
                    number_format($row->ingresos_tarjeta, 2, '.', ''),
                    $row->facturacion ? 'Sí' : 'No',
                    number_format($row->retencion_tarjeta, 2, '.', ''),
                    number_format($row->comision_pos, 2, '.', ''),
                    number_format($row->comision_tienda_tarjeta, 2, '.', ''),
                    number_format($row->total_empresario_tarjeta, 2, '.', ''),
                    number_format($row->ingresos_efectivo, 2, '.', ''),
                    $row->facturacion ? 'Sí' : 'No',
                    number_format($row->retencion_efectivo, 2, '.', ''),
                    number_format($row->comision_tienda_efectivo, 2, '.', ''),
                    number_format($row->total_empresario_efectivo, 2, '.', ''),
                    number_format($row->total_a_depositar, 2, '.', ''),
                    number_format($row->contribucion_emprendedor, 2, '.', ''),
                    number_format($row->total_comision_tienda, 2, '.', ''),
                ], ';');
            }
            
            // FILA DE TOTALES GENERALES (Formato a 2 decimales para Excel)
            $totalRow = [
                'TOTALES GENERALES', '', '', 
                number_format($reporte->sum('ingresos_tarjeta'), 2, '.', ''), '',
                number_format($reporte->sum('retencion_tarjeta'), 2, '.', ''),
                number_format($reporte->sum('comision_pos'), 2, '.', ''),
                number_format($reporte->sum('comision_tienda_tarjeta'), 2, '.', ''),
                number_format($reporte->sum('total_empresario_tarjeta'), 2, '.', ''),
                number_format($reporte->sum('ingresos_efectivo'), 2, '.', ''), '',
                number_format($reporte->sum('retencion_efectivo'), 2, '.', ''),
                number_format($reporte->sum('comision_tienda_efectivo'), 2, '.', ''),
                number_format($reporte->sum('total_empresario_efectivo'), 2, '.', ''),
                number_format($reporte->sum('total_a_depositar'), 2, '.', ''),
                number_format($reporte->sum('contribucion_emprendedor'), 2, '.', ''),
                number_format($reporte->sum('total_comision_tienda'), 2, '.', ''),
            ];
            fputcsv($file, $totalRow, ';');

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}