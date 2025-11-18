<x-app-layout>
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-lg p-8 dark:bg-gray-800">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-6 border-b pb-2">
                💰 Cierre de Caja Detallado (CMV)
            </h1>
            
            <div class="mb-8 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border dark:border-gray-600 flex justify-between items-center">
                <div>
                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">Tienda: <span class="text-indigo-600 dark:text-indigo-400">{{ $cierreData['tienda'] }}</span></p>
                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">Fecha: <span class="text-indigo-600 dark:text-indigo-400">{{ $cierreData['fecha'] }}</span></p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Descuentos Aplicados:</p>
                    <p class="text-xl font-bold text-red-600 dark:text-red-400">L. {{ number_format($cierreData['documentos_general']['total_descuentos'], 2) }}</p>
                </div>
            </div>

            ---

            {{-- RESUMEN GENERAL (TOTALES) --}}
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4 mt-6">⭐ Totales Generales de la Tienda</h2>
            <div class="space-y-2 border p-4 rounded-lg bg-blue-50/50 dark:bg-blue-900/50 border-blue-200 dark:border-blue-700">
                <p class="flex justify-between font-medium text-gray-700 dark:text-gray-300">
                    <span>Total de Documentos Emitidos:</span>
                    <span class="text-lg font-bold text-indigo-700 dark:text-indigo-400">{{ $cierreData['documentos_general']['total_documentos'] }}</span>
                </p>
                <hr class="border-t-2 border-dashed my-2 border-blue-300 dark:border-blue-700">
                <p class="flex justify-between text-2xl font-extrabold text-blue-800 dark:text-blue-200">
                    <span>TOTAL RECAUDADO (Monto Neto):</span>
                    <span>L. {{ number_format($cierreData['documentos_general']['total_neto'], 2) }}</span>
                </p>
            </div>

            ---

            {{-- DETALLE POR EMPRESA / PROVEEDOR --}}
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4 mt-8">🏭 Ventas Detalladas por Empresa (Costo y Utilidad)</h2>
            <div class="overflow-x-auto rounded-lg border dark:border-gray-700 shadow-lg">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Empresa</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Subtotal Base</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Costo (CMV)</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Utilidad Bruta</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">% Utilidad</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Total (Base + ISV)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @php
                            $granTotalVentas = 0;
                            $granTotalCosto = 0;
                            $granTotalUtilidad = 0;
                        @endphp
                        @forelse ($cierreData['ventas_por_empresa'] as $venta)
                            @php
                                $granTotalVentas += $venta->total_bruto_empresa;
                                $granTotalCosto += $venta->costo_total_venta;
                                $granTotalUtilidad += $venta->utilidad_bruta;
                                
                                $porcentajeUtilidad = $venta->subtotal_base_total > 0 
                                                      ? ($venta->utilidad_bruta / $venta->subtotal_base_total) * 100 
                                                      : 0;
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $venta->nombre_negocio }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-600 dark:text-gray-300">
                                    L. {{ number_format($venta->subtotal_base_total, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-600 dark:text-red-400">
                                    L. {{ number_format($venta->costo_total_venta, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-green-700 dark:text-green-400">
                                    L. {{ number_format($venta->utilidad_bruta, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold {{ $porcentajeUtilidad > 0 ? 'text-green-600' : 'text-gray-500' }}">
                                    {{ number_format($porcentajeUtilidad, 2) }}%
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-blue-700 dark:text-blue-400">
                                    L. {{ number_format($venta->total_bruto_empresa, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 italic">No hay ventas registradas para ninguna empresa en esta fecha.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-gray-700 border-t-4 border-double border-indigo-200 dark:border-indigo-600">
                        <tr>
                            <td class="px-6 py-4 text-left font-extrabold text-gray-900 dark:text-gray-100">GRAN TOTAL</td>
                            <td class="px-6 py-4 text-right font-bold text-gray-700 dark:text-gray-300">L. {{ number_format($cierreData['ventas_por_empresa']->sum('subtotal_base_total'), 2) }}</td>
                            <td class="px-6 py-4 text-right font-bold text-red-700 dark:text-red-400">L. {{ number_format($granTotalCosto, 2) }}</td>
                            <td class="px-6 py-4 text-right font-extrabold text-green-700 dark:text-green-400">L. {{ number_format($granTotalUtilidad, 2) }}</td>
                            <td class="px-6 py-4 text-right font-extrabold text-blue-800 dark:text-blue-200">
                                {{ $cierreData['ventas_por_empresa']->sum('subtotal_base_total') > 0 
                                ? number_format(($granTotalUtilidad / $cierreData['ventas_por_empresa']->sum('subtotal_base_total')) * 100, 2)
                                : 0 }}%
                            </td>
                            <td class="px-6 py-4 text-right font-extrabold text-blue-800 dark:text-blue-200">L. {{ number_format($granTotalVentas, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            ---

            {{-- DETALLE POR MÉTODO DE PAGO --}}
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4 mt-8">💳 Detalle por Método de Pago</h2>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 rounded-lg border dark:border-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Método</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Monto Recibido</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    @forelse ($cierreData['metodos_pago'] as $pago)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $pago->metodo_pago }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-gray-800 dark:text-gray-200">L. {{ number_format($pago->total_pagado_por_metodo, 2) }}</td>
                        </tr>
                    @empty
                         <tr>
                            <td colspan="2" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 italic">No se registraron pagos detallados.</td>
                        </tr>
                    @endforelse
                    <tr class="bg-blue-50 dark:bg-blue-900/50">
                        <td class="px-6 py-4 whitespace-nowrap text-lg font-bold text-blue-800 dark:text-blue-200">Total Pagos</td>
                        @php
                            $totalPagos = $cierreData['metodos_pago']->sum('total_pagado_por_metodo');
                        @endphp
                        <td class="px-6 py-4 whitespace-nowrap text-lg text-right font-bold text-blue-800 dark:text-blue-200">L. {{ number_format($totalPagos, 2) }}</td>
                    </tr>
                </tbody>
            </table>
            
            <div class="mt-8 text-center">
                 <a href="{{ route('reportes.cierre_caja.form') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Volver a Generar Otro Reporte
                </a>
            </div>
        </div>
    </div>
</x-app-layout>