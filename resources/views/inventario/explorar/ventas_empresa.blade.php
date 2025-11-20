{{-- resources/views/inventario/explorar/ventas_empresa.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Trazabilidad de Ventas - ') }} {{ $empresa->nombre_negocio }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Breadcrumb/Navegación --}}
            <div class="mb-6">
                <a href="{{ route('inventarios.explorar.inventario', ['empresa' => $empresa->id, 'tienda' => $tienda->id]) }}" 
                   class="inline-flex items-center text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition">
                    <i class="fas fa-arrow-left mr-2"></i> 
                    <span class="font-medium">Volver a Inventario</span>
                </a>
            </div>

            {{-- Tarjetas de Resumen Rápido --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Unidades Vendidas</p>
                            <p class="text-3xl font-bold mt-1">{{ number_format($totalCantidadVendida, 0) }}</p>
                        </div>
                        <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                            <i class="fas fa-box text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">Subtotal Base</p>
                            <p class="text-3xl font-bold mt-1">L {{ number_format($totalSubtotalBase, 2) }}</p>
                        </div>
                        <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                            <i class="fas fa-calculator text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-emerald-100 text-sm font-medium">Ingreso Total</p>
                            <p class="text-3xl font-bold mt-1">L {{ number_format($totalVentasSum, 2) }}</p>
                        </div>
                        <div class="bg-emerald-400 bg-opacity-30 rounded-full p-3">
                            <i class="fas fa-chart-line text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Panel de Filtros --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl mb-6 border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-store text-indigo-600 dark:text-indigo-400"></i>
                            <span class="font-semibold text-gray-700 dark:text-gray-300">Tienda:</span>
                            <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $tienda->nombre }}</span>
                        </div>
                        <button type="button" onclick="document.getElementById('filterForm').classList.toggle('hidden')" 
                                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            <i class="fas fa-filter mr-1"></i> Filtros
                        </button>
                    </div>
                </div>

                <form id="filterForm" method="GET" action="{{ route('inventarios.explorar.ventas', ['empresa' => $empresa->id, 'tienda' => $tienda->id]) }}" 
                      class="p-6 {{ request()->has('fecha_inicio') || request()->has('fecha_fin') ? '' : 'hidden' }}">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-calendar-alt mr-1"></i> Fecha Inicio
                            </label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ $fechaInicio }}"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="fecha_fin" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-calendar-alt mr-1"></i> Fecha Fin
                            </label>
                            <input type="date" name="fecha_fin" id="fecha_fin" value="{{ $fechaFin }}"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div class="flex items-end">
                            <button type="submit" class="w-full px-4 py-2.5 bg-blue-600 text-white rounded-lg font-semibold shadow-md hover:bg-blue-700 transition flex items-center justify-center">
                                <i class="fas fa-search mr-2"></i> Aplicar
                            </button>
                        </div>

                        <div class="flex items-end">
                            <a href="{{ route('inventarios.explorar.ventas', ['empresa' => $empresa->id, 'tienda' => $tienda->id]) }}" 
                               class="w-full px-4 py-2.5 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg font-semibold shadow-md hover:bg-gray-300 dark:hover:bg-gray-500 transition flex items-center justify-center">
                               <i class="fas fa-redo mr-2"></i> Restablecer
                            </a>
                        </div>
                    </div>
                </form>
            </div>
            
            {{-- Tabla de Detalles --}}
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                        <i class="fas fa-list-ul mr-2 text-indigo-600 dark:text-indigo-400"></i>
                        Líneas de Producto Vendidas
                        <span class="ml-3 px-3 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 text-sm font-bold rounded-full">
                            {{ $detalles->total() }}
                        </span>
                    </h4>

                    {{-- Botón de Exportar (opcional) --}}
                    <button type="button" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition">
                        <i class="fas fa-file-excel mr-2"></i> Exportar
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-box mr-1"></i> Producto
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-user mr-1"></i> Cliente
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-calendar mr-1"></i> Fecha
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-hashtag mr-1"></i> Cantidad
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-tag mr-1"></i> Precio Unit.
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">
                                    <i class="fas fa-dollar-sign mr-1"></i> Total
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($detalles as $detalle)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <td class="px-6 py-4 text-sm">
                                    <div class="font-semibold text-gray-900 dark:text-white">
                                        {{ $detalle->inventario->marca->producto->nombre ?? 'N/A' }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700">
                                            <i class="fas fa-barcode mr-1 text-xs"></i>
                                            {{ $detalle->inventario->marca->codigo_marca ?? 'N/A' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="font-medium text-gray-900 dark:text-white">
                                        {{ $detalle->venta->cliente->nombre ?? 'Venta Genérica' }}
                                    </div>
                                    <div class="text-xs text-indigo-600 dark:text-indigo-400 mt-1">
                                        <i class="fas fa-file-invoice text-xs mr-1"></i>
                                        {{ $detalle->venta->numero_documento ?? '#' . $detalle->venta_id }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600 dark:text-gray-300">
                                    <div class="font-medium">{{ $detalle->venta->fecha_venta->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $detalle->venta->fecha_venta->format('h:i A') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-base font-bold rounded-full">
                                        {{ $detalle->cantidad }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-700 dark:text-gray-300">
                                    L {{ number_format($detalle->precio_unitario, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-base font-bold text-emerald-600 dark:text-emerald-400">
                                    L {{ number_format($detalle->subtotal_final, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
                                        <i class="fas fa-search-minus text-6xl mb-4"></i>
                                        <p class="text-lg font-semibold mb-1">No se encontraron ventas</p>
                                        <p class="text-sm">No hay registros de ventas para el período seleccionado</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        
                        {{-- Footer con Totales --}}
                        @if($detalles->count() > 0)
                        <tfoot class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 border-t-4 border-emerald-500">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right text-base font-bold text-gray-900 dark:text-white">
                                    <i class="fas fa-calculator mr-2"></i> TOTALES:
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-4 py-2 bg-blue-600 text-white text-lg font-extrabold rounded-lg shadow">
                                        {{ number_format($totalCantidadVendida, 0) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-lg font-extrabold text-gray-900 dark:text-white">
                                    L {{ number_format($totalSubtotalBase, 2) }}
                                </td>
                                <td class="px-6 py-4 text-right text-xl font-extrabold text-emerald-600 dark:text-emerald-400">
                                    L {{ number_format($totalVentasSum, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                    {{ $detalles->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>