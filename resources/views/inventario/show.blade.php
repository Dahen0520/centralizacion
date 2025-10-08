<x-app-layout>
    <x-slot name="header">
    </x-slot>

    <div class="py-12 flex justify-center">
        <div class="w-full max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 
                        rounded-2xl shadow-3xl p-10 lg:p-12 
                        border-t-4 border-b-4 border-emerald-500 dark:border-emerald-600 
                        transform hover:shadow-4xl transition-all duration-300 ease-in-out">
                
                {{-- Bloque de Encabezado Elegante --}}
                <div class="text-center mb-10">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full 
                                bg-gradient-to-br from-emerald-500 to-emerald-700 text-white 
                                mb-5 shadow-lg transform hover:scale-110 transition-all duration-300 ease-in-out 
                                dark:from-emerald-600 dark:to-emerald-800">
                         <i class="fas fa-cubes text-4xl"></i>
                    </div>
                    <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-2 leading-tight">
                        Detalle del Inventario: <span class="text-emerald-600 dark:text-emerald-400">{{ $inventario->marca->producto->nombre ?? 'N/A' }}</span>
                    </h3>
                    <p class="text-md text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                        Revisando precios y existencias en {{ $inventario->tienda->nombre ?? 'Tienda Desconocida' }}.
                    </p>
                    
                    <a href="{{ route('inventarios.index') }}" 
                       class="mt-6 inline-flex items-center text-sm font-semibold 
                              text-emerald-600 dark:text-emerald-400 
                              hover:text-emerald-800 dark:hover:text-emerald-200 
                              transition duration-300 ease-in-out 
                              transform hover:-translate-x-1 hover:underline">
                        <i class="fas fa-arrow-left mr-2"></i> Volver a la Lista de Inventario
                    </a>
                </div>

                {{-- Tarjeta de Stock y Precio (Destacada) --}}
                @php
                    $stockColor = $inventario->stock > 5 ? 'green' : ($inventario->stock > 0 ? 'yellow' : 'red');
                @endphp
                <div class="bg-gray-100 dark:bg-gray-700/50 rounded-xl p-6 mb-8 shadow-inner grid grid-cols-1 md:grid-cols-3 gap-6 items-center border border-gray-200 dark:border-gray-700">
                    
                    {{-- Artículo --}}
                    <div class="border-r border-gray-300 dark:border-gray-600 pr-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Código de Marca</p>
                        <span class="text-xl font-extrabold text-indigo-600 dark:text-indigo-400">
                            {{ $inventario->marca->codigo_marca }}
                        </span>
                    </div>

                    {{-- Stock --}}
                    <div class="border-r border-gray-300 dark:border-gray-600 pr-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Stock Disponible</p>
                        <span class="text-2xl font-extrabold text-{{ $stockColor }}-700 dark:text-{{ $stockColor }}-400">
                            {{ number_format($inventario->stock) }}
                        </span>
                    </div>

                    {{-- Precio --}}
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Precio Unitario</p>
                        <span class="text-2xl font-extrabold text-green-700 dark:text-green-400">
                            L. {{ number_format($inventario->precio, 2) }}
                        </span>
                    </div>
                </div>

                {{-- Grid de Detalles Generales --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl p-8 shadow-xl border border-gray-200 dark:border-gray-700">
                    <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-6 border-b pb-3 border-gray-200 dark:border-gray-700 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-blue-500"></i> Ubicación y Clasificación
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-6">
                        
                        {{-- ID de Inventario --}}
                        <div class="flex items-start">
                            <i class="fas fa-fingerprint mr-4 text-xl mt-1 text-gray-500 dark:text-gray-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Registro ID</p>
                                <p class="text-base font-medium text-gray-700 dark:text-gray-300">{{ $inventario->id }}</p>
                            </div>
                        </div>
                        
                        {{-- Tienda --}}
                        <div class="flex items-start">
                            <i class="fas fa-store text-lg mr-3 mt-1 text-blue-500 dark:text-blue-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Tienda Asignada</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $inventario->tienda->nombre ?? 'N/A' }}</p>
                            </div>
                        </div>
                        
                        {{-- Producto (Detalle) --}}
                        <div class="flex items-start md:col-span-2 border-t pt-4 mt-2 border-gray-100 dark:border-gray-700">
                            <i class="fas fa-tag text-lg mr-3 mt-1 text-purple-500 dark:text-purple-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Producto Base</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $inventario->marca->producto->nombre ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $inventario->marca->producto->subcategoria->nombre ?? '' }} | Categoría: {{ $inventario->marca->producto->subcategoria->categoria->nombre ?? '' }}
                                </p>
                            </div>
                        </div>

                        {{-- Creado en --}}
                        <div class="flex items-start">
                            <i class="fas fa-clock mr-4 text-xl mt-1 text-green-500 dark:text-green-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Fecha de Creación</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $inventario->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        {{-- Actualizado en --}}
                        <div class="flex items-start">
                            <i class="fas fa-calendar-alt mr-4 text-xl mt-1 text-yellow-500 dark:text-yellow-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Última Modificación</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $inventario->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                    </div>
                </div>

                <hr class="my-10 border-gray-200 dark:border-gray-700 opacity-60">
                
                {{-- Botón de Acción --}}
                <div class="flex justify-center">
                    <a href="{{ route('inventarios.edit', $inventario) }}" 
                       class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-green-500 to-teal-600 border border-transparent 
                              rounded-xl font-bold text-lg text-white uppercase tracking-widest shadow-lg 
                              hover:shadow-xl hover:from-green-600 hover:to-teal-700 focus:outline-none focus:ring-4 focus:ring-green-300 dark:focus:ring-green-800 
                              transition duration-300 transform hover:scale-[1.02]">
                        <i class="fas fa-edit mr-2"></i> Editar Precio y Stock
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
