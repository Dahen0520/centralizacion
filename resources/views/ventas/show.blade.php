<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Detalle de Venta') }} #{{ $venta->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg p-8">

                {{-- Botón Volver --}}
                <a href="{{ route('ventas.index') }}" 
                   class="mb-6 inline-flex items-center text-sm font-semibold 
                          text-emerald-600 dark:text-emerald-400 
                          hover:text-emerald-800 dark:hover:text-emerald-200 
                          transition duration-300 transform hover:-translate-x-1 hover:underline">
                    <i class="fas fa-arrow-left mr-2"></i> Volver al Historial
                </a>

                {{-- RESUMEN DE LA TRANSACCIÓN --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-gray-50 dark:bg-gray-700 p-6 rounded-xl mb-8 shadow-inner border border-gray-200 dark:border-gray-600">
                    
                    {{-- ID y Fecha --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Venta ID / Fecha</p>
                        <p class="text-xl font-extrabold text-gray-900 dark:text-white">#{{ $venta->id }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $venta->fecha_venta->format('d/M/Y H:i A') }}</p>
                    </div>

                    {{-- Tienda y Usuario --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Tienda / Usuario</p>
                        <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400">{{ $venta->tienda->nombre ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $venta->usuario->name ?? 'Sistema' }}</p>
                    </div>

                    {{-- Total --}}
                    <div class="text-right md:text-left">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Total de la Venta</p>
                        <p class="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400">L {{ number_format($venta->total_venta, 2) }}</p>
                    </div>
                </div>

                {{-- DETALLE DE PRODUCTOS VENDIDOS --}}
                <h4 class="text-xl font-bold text-gray-700 dark:text-gray-300 mb-4 border-b pb-2">Productos Incluidos</h4>

                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-lg">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    Producto / Código
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    Precio Unitario (L)
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    Cantidad
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    Subtotal (L)
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($venta->detalles as $detalle)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $detalle->inventario->marca->producto->nombre ?? 'Producto Eliminado' }}
                                    <span class="text-xs text-gray-500 dark:text-gray-400 block">Cód: {{ $detalle->inventario->marca->codigo_marca ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-green-600 dark:text-green-400">
                                    L {{ number_format($detalle->precio_unitario, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-gray-800 dark:text-white">
                                    {{ $detalle->cantidad }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-indigo-600 dark:text-indigo-400">
                                    L {{ number_format($detalle->subtotal, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    Esta venta no tiene detalles de productos.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Resumen final al pie --}}
                <div class="flex justify-end mt-4">
                    <div class="text-right p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
                         <p class="text-xl font-extrabold text-gray-900 dark:text-white">TOTAL: L {{ number_format($venta->total_venta, 2) }}</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>