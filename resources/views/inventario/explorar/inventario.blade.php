<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Inventario de ') }} {{ $empresa->nombre_negocio }} {{ __(' en ') }} {{ $tienda->nombre }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('inventarios.explorar.empresas', $tienda) }}" class="mb-6 inline-block text-emerald-600 dark:text-emerald-400 hover:underline">
                <i class="fas fa-arrow-left mr-2"></i> Volver a la Lista de Empresas
            </a>
            
            <h3 class="text-2xl font-bold text-gray-700 dark:text-gray-300 mb-6">3. Inventario Registrado</h3>

            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-xl">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                Producto
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                Precio (L)
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                Stock
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                Acción
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($inventarios as $inventario)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $inventario->marca->producto->nombre ?? 'N/A' }}
                                    <span class="text-xs text-gray-500 dark:text-gray-400 block">Cód: {{ $inventario->marca->codigo_marca }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-green-600 dark:text-green-400">
                                    L {{ number_format($inventario->precio, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold @if($inventario->stock <= 10) text-red-500 @else text-emerald-600 dark:text-emerald-400 @endif">
                                    {{ number_format($inventario->stock, 0) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    {{-- ENLACE CORREGIDO: Añade el parámetro para redirigir correctamente --}}
                                    <a href="{{ route('inventarios.edit', ['inventario' => $inventario->id, 'redirect_to' => 'explorar', 'empresa_id' => $empresa->id, 'tienda_id' => $tienda->id]) }}" 
                                       class="text-emerald-600 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-200 transition-colors" 
                                       title="Ajustar Precio/Stock">
                                        <i class="fas fa-hand-holding-usd mr-1"></i> Ajustar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    No hay registros de inventario para esta empresa en esta tienda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>