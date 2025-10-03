<x-app-layout>
    <x-slot name="header">
    </x-slot>

    <div class="py-12 flex justify-center">
        <div class="w-full max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 
                        rounded-2xl shadow-3xl p-10 lg:p-12 
                        border-t-4 border-b-4 border-blue-500 dark:border-blue-600 
                        transform hover:shadow-4xl transition-all duration-300 ease-in-out">
                
                {{-- Bloque de Encabezado Elegante --}}
                <div class="text-center mb-10">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full 
                                bg-gradient-to-br from-indigo-500 to-purple-600 text-white 
                                mb-5 shadow-lg transform hover:scale-110 transition-all duration-300 ease-in-out 
                                dark:from-indigo-600 dark:to-purple-800">
                         <i class="fas fa-search-dollar text-4xl"></i>
                    </div>
                    <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-2 leading-tight">
                        Ficha de: <span class="text-blue-600 dark:text-blue-400">{{ $empresa->nombre_negocio }}</span>
                    </h3>
                    <p class="text-md text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                        Visualiza la información principal y de clasificación de la entidad.
                    </p>
                    
                    <a href="{{ route('empresas.index') }}" 
                       class="mt-6 inline-flex items-center text-sm font-semibold 
                              text-blue-600 dark:text-blue-400 
                              hover:text-blue-800 dark:hover:text-blue-200 
                              transition duration-300 ease-in-out 
                              transform hover:-translate-x-1 hover:underline">
                        <i class="fas fa-arrow-left mr-2"></i> Volver a la Lista de Empresas
                    </a>
                </div>

                {{-- Tarjeta de Estado (Destacada) --}}
                @php
                    $color = [
                        'aprobado' => 'green',
                        'pendiente' => 'yellow',
                        'rechazado' => 'red',
                    ][$empresa->estado] ?? 'gray';
                @endphp
                <div class="bg-{{ $color }}-50 dark:bg-gray-700/50 border border-{{ $color }}-300 dark:border-{{ $color }}-600 
                            rounded-xl p-5 mb-8 shadow-inner flex justify-between items-center">
                    <div class="flex items-center">
                        <i class="fas fa-tag text-2xl mr-4 text-{{ $color }}-600 dark:text-{{ $color }}-400"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Estado Actual</p>
                            <span class="text-2xl font-extrabold text-{{ $color }}-700 dark:text-{{ $color }}-400 uppercase">
                                {{ $empresa->estado }}
                            </span>
                        </div>
                    </div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Desde: {{ $empresa->created_at->format('d/m/Y') }}</span>
                </div>

                {{-- Grid de Detalles (Mejorado) --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl p-8 shadow-xl border border-gray-200 dark:border-gray-700">
                    <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-6 border-b pb-3 border-gray-200 dark:border-gray-700 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-blue-500"></i> Datos Clave
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-6">
                        
                        {{-- Dirección --}}
                        <div class="flex items-start">
                            <i class="fas fa-map-marker-alt text-lg mr-3 mt-1 text-red-500 dark:text-red-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Dirección</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $empresa->direccion }}</p>
                            </div>
                        </div>

                        {{-- Afiliado --}}
                        <div class="flex items-start">
                            <i class="fas fa-user-tie text-lg mr-3 mt-1 text-purple-500 dark:text-purple-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Afiliado Registrante</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $empresa->afiliado->nombre }}</p>
                            </div>
                        </div>

                        {{-- Rubro --}}
                        <div class="flex items-start">
                            <i class="fas fa-industry text-lg mr-3 mt-1 text-green-500 dark:text-green-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Rubro</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $empresa->rubro->nombre }}</p>
                            </div>
                        </div>
                        
                        {{-- Tipo de Organización --}}
                        <div class="flex items-start">
                            <i class="fas fa-sitemap text-lg mr-3 mt-1 text-orange-500 dark:text-orange-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Tipo de Organización</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $empresa->tipoOrganizacion->nombre }}</p>
                            </div>
                        </div>
                        
                        {{-- País de Exportación --}}
                        <div class="flex items-start">
                            <i class="fas fa-globe-americas text-lg mr-3 mt-1 text-cyan-500 dark:text-cyan-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">País de Exportación</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $empresa->paisExportacion ? $empresa->paisExportacion->nombre : 'N/A' }}</p>
                            </div>
                        </div>

                    </div>
                </div>

                <hr class="my-10 border-gray-200 dark:border-gray-700 opacity-60">
                
                {{-- Botón de Acción --}}
                <div class="flex justify-center">
                    <a href="{{ route('empresas.edit', $empresa->id) }}" 
                       class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-green-500 to-teal-600 border border-transparent 
                              rounded-xl font-bold text-lg text-white uppercase tracking-widest shadow-lg 
                              hover:shadow-xl hover:from-green-600 hover:to-teal-700 focus:outline-none focus:ring-4 focus:ring-green-300 dark:focus:ring-green-800 
                              transition duration-300 transform hover:scale-[1.02]">
                        <i class="fas fa-edit mr-2"></i> Editar Datos
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>