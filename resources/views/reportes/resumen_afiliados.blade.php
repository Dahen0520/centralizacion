{{-- resources/views/reportes/resumen_afiliados.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                <i class="fas fa-chart-line mr-2"></i>Reporte de Ventas
            </h2>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
    </x-slot>

    <div class="max-w-full mx-auto py-6 px-4 sm:px-6 lg:px-8">
        
        {{-- Tarjetas de Resumen Rápido --}}
        @if ($reporte->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                {{-- Total Ventas --}}
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Total Ventas (Neto)</p>
                            <p class="text-2xl font-bold mt-1">
                                L. {{ number_format($reporte->sum('ingresos_tarjeta') + $reporte->sum('ingresos_efectivo'), 2) }}
                            </p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-lg">
                            <i class="fas fa-shopping-cart text-2xl"></i>
                        </div>
                    </div>
                </div>

                {{-- Total a Depositar --}}
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Total a Depositar</p>
                            <p class="text-2xl font-bold mt-1">
                                L. {{ number_format($reporte->sum('total_a_depositar'), 2) }}
                            </p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-lg">
                            <i class="fas fa-money-bill-wave text-2xl"></i>
                        </div>
                    </div>
                </div>

                {{-- Total Comisiones --}}
                <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-red-100 text-sm font-medium">Total Comisiones</p>
                            <p class="text-2xl font-bold mt-1">
                                L. {{ number_format($reporte->sum('total_comision_tienda') + $reporte->sum('comision_pos'), 2) }}
                            </p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-lg">
                            <i class="fas fa-percentage text-2xl"></i>
                        </div>
                    </div>
                </div>

                {{-- Número de Afiliados --}}
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">Afiliados Únicos</p>
                            <p class="text-2xl font-bold mt-1">{{ $reporte->count() }}</p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-lg">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Formulario de Filtros Mejorado --}}
        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl mb-6 border border-gray-100 dark:border-gray-700 overflow-hidden">
            {{-- Header del Card --}}
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white/20 backdrop-blur-sm p-2 rounded-lg">
                            <i class="fas fa-filter text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">Filtros de Búsqueda</h3>
                            <p class="text-blue-100 text-xs mt-0.5">Personaliza tu reporte según tus necesidades</p>
                        </div>
                    </div>
                    <button type="button" 
                            onclick="document.getElementById('formFiltros').reset(); window.location.href='{{ route('reportes.resumen.afiliados') }}'"
                            class="text-white/80 hover:text-white transition-colors duration-200"
                            title="Restablecer todos los filtros">
                        <i class="fas fa-sync-alt text-sm"></i>
                    </button>
                </div>
            </div>
            
            {{-- Body del Card --}}
            <div class="p-6 bg-gradient-to-br from-gray-50 to-white dark:from-gray-800 dark:to-gray-900">
                <form method="GET" action="{{ route('reportes.resumen.afiliados') }}" id="formFiltros">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-5">
                        
                        {{-- Filtro de Tienda --}}
                        <div class="lg:col-span-4">
                            <label for="tienda_id" class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                <div class="bg-blue-100 dark:bg-blue-900/30 p-1.5 rounded-md mr-2">
                                    <i class="fas fa-store text-blue-600 dark:text-blue-400 text-xs"></i>
                                </div>
                                Tienda
                            </label>
                            <div class="relative">
                                <select name="tienda_id" id="tienda_id" 
                                       class="w-full pl-4 pr-10 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 appearance-none cursor-pointer hover:border-blue-400 dark:hover:border-blue-500">
                                    <option value="">🏪 Todas las Tiendas</option>
                                    @foreach ($tiendas as $tienda)
                                        <option value="{{ $tienda->id }}" {{ (string)$tienda->id === request('tienda_id') ? 'selected' : '' }}>
                                            {{ $tienda->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Fecha Inicio --}}
                        <div class="lg:col-span-3">
                            <label for="fecha_inicio" class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                <div class="bg-green-100 dark:bg-green-900/30 p-1.5 rounded-md mr-2">
                                    <i class="fas fa-calendar-alt text-green-600 dark:text-green-400 text-xs"></i>
                                </div>
                                Fecha Inicio
                            </label>
                            <div class="relative">
                                <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ $fechaInicio }}"
                                       class="w-full pl-4 pr-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 hover:border-green-400 dark:hover:border-green-500">
                            </div>
                        </div>
                        
                        {{-- Fecha Fin --}}
                        <div class="lg:col-span-3">
                            <label for="fecha_fin" class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                <div class="bg-red-100 dark:bg-red-900/30 p-1.5 rounded-md mr-2">
                                    <i class="fas fa-calendar-check text-red-600 dark:text-red-400 text-xs"></i>
                                </div>
                                Fecha Fin
                            </label>
                            <div class="relative">
                                <input type="date" name="fecha_fin" id="fecha_fin" value="{{ $fechaFin }}"
                                       class="w-full pl-4 pr-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 hover:border-red-400 dark:hover:border-red-500">
                            </div>
                        </div>
                        
                        {{-- Botones de Acción --}}
                        <div class="lg:col-span-2 flex flex-col justify-end gap-2">
                            {{-- Botón Filtrar --}}
                            <button type="submit" name="filter" value="1" 
                                    class="group relative w-full inline-flex justify-center items-center px-5 py-3 bg-gradient-to-r from-blue-600 to-blue-700 border border-transparent rounded-xl font-bold text-sm text-white uppercase tracking-wide hover:from-blue-700 hover:to-blue-800 active:scale-95 transition-all duration-200 shadow-lg hover:shadow-xl overflow-hidden">
                                <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity duration-200"></div>
                                <i class="fas fa-search mr-2 relative z-10"></i>
                                <span class="relative z-10">Filtrar</span>
                            </button>
                        </div>
                    </div>
                    
                    {{-- Fila adicional para botones secundarios --}}
                    <div class="mt-5 pt-5 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex flex-wrap gap-3">
                            {{-- Botón Exportar --}}
                            <button type="submit" name="export" value="excel" 
                                    class="group inline-flex items-center justify-center px-6 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-wide hover:from-green-700 hover:to-emerald-700 active:scale-95 transition-all duration-200 shadow-md hover:shadow-lg" 
                                    title="Exportar a Excel">
                                <i class="fas fa-file-excel mr-2 group-hover:scale-110 transition-transform duration-200"></i>
                                Exportar Excel
                            </button>
                            
                            {{-- Botón Limpiar Filtros --}}
                            <a href="{{ route('reportes.resumen.afiliados') }}" 
                               class="group inline-flex items-center justify-center px-6 py-2.5 bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 border-2 border-gray-300 dark:border-gray-500 rounded-xl font-semibold text-sm text-gray-700 dark:text-gray-200 uppercase tracking-wide hover:from-gray-200 hover:to-gray-300 dark:hover:from-gray-600 dark:hover:to-gray-500 active:scale-95 transition-all duration-200 shadow-md hover:shadow-lg" 
                               title="Limpiar Filtros">
                                <i class="fas fa-redo mr-2 group-hover:rotate-180 transition-transform duration-500"></i>
                                Limpiar
                            </a>
                            
                            {{-- Badge informativo (opcional) --}}
                            @if(request()->hasAny(['tienda_id', 'fecha_inicio', 'fecha_fin']))
                                <div class="inline-flex items-center px-4 py-2.5 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl">
                                    <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mr-2"></i>
                                    <span class="text-sm font-medium text-blue-700 dark:text-blue-300">
                                        Filtros activos
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabla de Resultados --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800">
                        <tr>
                            {{-- Información General (Total 3 columnas) --}}
                            <th colspan="3" class="px-4 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase bg-gray-200 dark:bg-gray-600 sticky left-0">
                                Información General
                            </th>
                            
                            {{-- Transacciones con Tarjeta (Total 6 columnas) --}}
                            <th colspan="6" class="px-4 py-3 text-center text-xs font-bold text-yellow-700 dark:text-yellow-400 uppercase bg-yellow-50 dark:bg-yellow-900/30">
                                Transacciones con Tarjeta
                            </th>
                            
                            {{-- Transacciones en Efectivo (Total 5 columnas) --}}
                            <th colspan="5" class="px-4 py-3 text-center text-xs font-bold text-green-700 dark:text-green-400 uppercase bg-green-50 dark:bg-green-900/30">
                                Transacciones en Efectivo
                            </th>
                            
                            {{-- Totales Finales (Total 3 columnas) --}}
                            <th colspan="3" class="px-4 py-3 text-center text-xs font-bold text-indigo-700 dark:text-indigo-400 uppercase bg-indigo-50 dark:bg-indigo-900/30">
                                Resumen Final
                            </th>
                        </tr>
                        <tr>
                            {{-- 1-3. Información General --}}
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider sticky left-0 bg-gray-100 dark:bg-gray-700">Núm. Cuenta</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Cuentahabiente</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Empresa</th>
                            
                            {{-- 4-9. Tarjeta --}}
                            <th class="px-4 py-3 text-right text-xs font-bold text-yellow-600 dark:text-yellow-400 uppercase tracking-wider whitespace-nowrap">Venta (L.)</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Fact.</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-red-600 dark:text-red-400 uppercase tracking-wider whitespace-nowrap">Retención</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-red-600 dark:text-red-400 uppercase tracking-wider whitespace-nowrap">Comisión POS (1.25%)</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-red-600 dark:text-red-400 uppercase tracking-wider whitespace-nowrap">Comisión Tienda (9.46%)</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider whitespace-nowrap">Total Empresario</th>

                            {{-- 10-14. Efectivo --}}
                            <th class="px-4 py-3 text-right text-xs font-bold text-green-700 dark:text-green-300 uppercase tracking-wider whitespace-nowrap">Venta (L.)</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Fact.</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-red-600 dark:text-red-400 uppercase tracking-wider whitespace-nowrap">Retención</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-red-600 dark:text-red-400 uppercase tracking-wider whitespace-nowrap">Com. Tienda (10.71%)</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider whitespace-nowrap">Total Empresario</th>

                            {{-- 15-17. Totales --}}
                            <th class="px-4 py-3 text-right text-xs font-bold text-indigo-700 dark:text-indigo-400 uppercase tracking-wider whitespace-nowrap">A Depositar</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">Contrib. Emprendedor</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-red-700 dark:text-red-400 uppercase tracking-wider whitespace-nowrap">Total Com. Tienda</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @forelse ($reporte as $index => $fila)
                            @php
                                // Helper para determinar la precisión dinámica
                                $format = function ($value) {
                                    $value = (float)$value;
                                    // Comprueba si el valor tiene más de 2 decimales significativos
                                    if (round($value, 2) !== round($value, 4)) {
                                        return number_format($value, 4);
                                    }
                                    // De lo contrario, usar 2.
                                    return number_format($value, 2);
                                };
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150 {{ $index % 2 == 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50/50 dark:bg-gray-800/50' }}">
                                {{-- 1-3. Información General --}}
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400 font-semibold sticky left-0 bg-inherit">
                                    {{ $fila->numero_cuenta ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $fila->nombre_afiliado }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ $fila->nombre_negocio }}
                                </td>
                                
                                {{-- 4-9. Tarjeta --}}
                                <td class="px-4 py-3 whitespace-nowrap text-right font-medium text-yellow-600 dark:text-yellow-400">
                                    L. {{ $format($fila->ingresos_tarjeta) }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    @if ($fila->facturacion)
                                        <span class="inline-flex items-center justify-center w-6 h-6 bg-green-100 dark:bg-green-900/30 rounded-full" title="Facturación Habilitada">
                                            <i class="fas fa-check text-green-600 dark:text-green-400 text-xs"></i>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center w-6 h-6 bg-red-100 dark:bg-red-900/30 rounded-full" title="Facturación Deshabilitada">
                                            <i class="fas fa-times text-red-600 dark:text-red-400 text-xs"></i>
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right font-bold text-red-600 dark:text-red-400">
                                    L. {{ $format($fila->retencion_tarjeta) }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right font-medium text-red-600 dark:text-red-400">
                                    L. {{ $format($fila->comision_pos) }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right font-medium text-red-600 dark:text-red-400">
                                    L. {{ $format($fila->comision_tienda_tarjeta) }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right font-bold text-teal-600 dark:text-teal-400">
                                    L. {{ $format($fila->total_empresario_tarjeta) }}
                                </td>
                                
                                {{-- 10-14. Efectivo --}}
                                <td class="px-4 py-3 whitespace-nowrap text-right font-medium text-green-600 dark:text-green-400">
                                    L. {{ $format($fila->ingresos_efectivo) }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    @if ($fila->facturacion)
                                        <span class="inline-flex items-center justify-center w-6 h-6 bg-green-100 dark:bg-green-900/30 rounded-full" title="Facturación Habilitada">
                                            <i class="fas fa-check text-green-600 dark:text-green-400 text-xs"></i>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center w-6 h-6 bg-red-100 dark:bg-red-900/30 rounded-full" title="Facturación Deshabilitada">
                                            <i class="fas fa-times text-red-600 dark:text-red-400 text-xs"></i>
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right font-medium text-red-600 dark:text-red-400">
                                    L. {{ $format($fila->retencion_efectivo) }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right font-medium text-red-600 dark:text-red-400">
                                    L. {{ $format($fila->comision_tienda_efectivo) }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right font-bold text-teal-600 dark:text-teal-400">
                                    L. {{ $format($fila->total_empresario_efectivo) }}
                                </td>

                                {{-- 15-17. Totales --}}
                                <td class="px-4 py-3 whitespace-nowrap text-right font-extrabold text-indigo-700 dark:text-indigo-400 text-base">
                                    L. {{ number_format($fila->total_a_depositar, 2) }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right font-medium text-gray-500 dark:text-gray-400">
                                    L. {{ number_format($fila->contribucion_emprendedor, 2) }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right font-extrabold text-red-700 dark:text-red-400 text-base">
                                    L. {{ number_format($fila->total_comision_tienda, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="17" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-inbox text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                                        <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">No se encontraron registros</p>
                                        <p class="text-gray-400 dark:text-gray-500 text-sm mt-2">Intenta ajustar los filtros de búsqueda</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($reporte->isNotEmpty())
                        <tfoot class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/30 dark:to-indigo-900/30 border-t-2 border-blue-200 dark:border-blue-700">
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-left font-extrabold text-blue-900 dark:text-blue-200 text-base">TOTALES GENERALES</td>
                                
                                {{-- 4-9. Tarjeta (SUMAS) --}}
                                <td class="px-4 py-4 text-right font-extrabold text-yellow-700 dark:text-yellow-400 text-sm">L. {{ number_format($reporte->sum('ingresos_tarjeta'), 2) }}</td>
                                <td class="px-4 py-4"></td> {{-- Facturación (Vacía) --}}
                                <td class="px-4 py-4 text-right font-extrabold text-red-700 dark:text-red-400 text-sm">L. {{ number_format($reporte->sum('retencion_tarjeta'), 2) }}</td>
                                <td class="px-4 py-4 text-right font-extrabold text-red-700 dark:text-red-400 text-sm">L. {{ number_format($reporte->sum('comision_pos'), 2) }}</td>
                                <td class="px-4 py-4 text-right font-extrabold text-red-700 dark:text-red-400 text-sm">L. {{ number_format($reporte->sum('comision_tienda_tarjeta'), 2) }}</td>
                                <td class="px-4 py-4 text-right font-extrabold text-teal-600 dark:text-teal-400 text-sm">L. {{ number_format($reporte->sum('total_empresario_tarjeta'), 2) }}</td>
                                
                                {{-- 10-14. Efectivo (SUMAS) --}}
                                <td class="px-4 py-4 text-right font-extrabold text-green-700 dark:text-green-300 text-sm">L. {{ number_format($reporte->sum('ingresos_efectivo'), 2) }}</td>
                                <td class="px-4 py-4"></td> {{-- Facturación (Vacía) --}}
                                <td class="px-4 py-4 text-right font-extrabold text-red-700 dark:text-red-400 text-sm">L. {{ number_format($reporte->sum('retencion_efectivo'), 2) }}</td>
                                <td class="px-4 py-4 text-right font-extrabold text-red-700 dark:text-red-400 text-sm">L. {{ number_format($reporte->sum('comision_tienda_efectivo'), 2) }}</td>
                                <td class="px-4 py-4 text-right font-extrabold text-teal-600 dark:text-teal-400 text-sm">L. {{ number_format($reporte->sum('total_empresario_efectivo'), 2) }}</td>

                                {{-- 15-17. Totales Finales (SUMAS) --}}
                                <td class="px-4 py-4 text-right font-extrabold text-indigo-700 dark:text-indigo-400 text-sm">L. {{ number_format($reporte->sum('total_a_depositar'), 2) }}</td>
                                <td class="px-4 py-4 text-right font-extrabold text-gray-600 dark:text-gray-400 text-sm">L. {{ number_format($reporte->sum('contribucion_emprendedor'), 2) }}</td>
                                <td class="px-4 py-4 text-right font-extrabold text-red-700 dark:text-red-400 text-sm">L. {{ number_format($reporte->sum('total_comision_tienda'), 2) }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Función para enviar el formulario con el parámetro 'export'
        function exportarExcel() {
            const form = document.getElementById('formFiltros');
            const url = new URL(form.action);
            const params = new URLSearchParams(new FormData(form));
            
            // Añadir el parámetro de exportación
            params.append('export', 'excel');
            
            // Redirigir usando el método GET para descargar el CSV
            window.location.href = `${url.pathname}?${params.toString()}`;
        }
    </script>
    @endpush
</x-app-layout>