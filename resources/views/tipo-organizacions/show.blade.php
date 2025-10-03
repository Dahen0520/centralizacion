<x-app-layout>
    <x-slot name="header">
    </x-slot>

    <div class="py-12 flex justify-center"> 
        <div class="w-full max-w-xl mx-auto">
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
                         <i class="fas fa-info-circle text-4xl"></i>
                    </div>
                    <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-2 leading-tight">
                        Información de la Organización
                    </h3>
                    <p class="text-md text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                        Visualizando los datos principales y la línea de tiempo del registro.
                    </p>
                    
                    <a href="{{ route('tipo-organizacions.index') }}" 
                       class="mt-6 inline-flex items-center text-sm font-semibold 
                              text-blue-600 dark:text-blue-400 
                              hover:text-blue-800 dark:hover:text-blue-200 
                              transition duration-300 ease-in-out 
                              transform hover:-translate-x-1 hover:underline">
                        <i class="fas fa-arrow-left mr-2"></i> Volver a la Lista
                    </a>
                </div>

                {{-- TARJETA DE DETALLES PRINCIPAL --}}
                <div class="bg-white dark:bg-gray-700 rounded-xl p-8 shadow-xl border border-gray-100 dark:border-gray-600">
                    
                    {{-- Nombre del Tipo de Organización (Destacado) --}}
                    <h3 class="text-3xl font-extrabold text-purple-600 dark:text-purple-400 mb-5 border-b pb-3 border-gray-200 dark:border-gray-600 flex items-center">
                        <i class="fas fa-sitemap mr-3 text-2xl"></i> {{ $tipoOrganizacion->nombre }}
                    </h3>
                    
                    {{-- Grid de Metadatos --}}
                    <div class="grid grid-cols-1 gap-6 text-gray-700 dark:text-gray-300">
                        
                        {{-- ID --}}
                        <div class="flex items-center">
                            <i class="fas fa-fingerprint mr-4 text-xl text-indigo-500 dark:text-indigo-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Identificador (ID)</p>
                                <p class="text-lg font-bold">{{ $tipoOrganizacion->id }}</p>
                            </div>
                        </div>

                        {{-- Nombre (Repetido, pero útil para resaltar) --}}
                         <div class="flex items-center">
                            <i class="fas fa-tag mr-4 text-xl text-blue-500 dark:text-blue-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Nombre Completo</p>
                                <p class="text-lg font-medium">{{ $tipoOrganizacion->nombre }}</p>
                            </div>
                        </div>

                        {{-- Creado en --}}
                        <div class="flex items-center">
                            <i class="fas fa-clock mr-4 text-xl text-green-500 dark:text-green-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Fecha de Creación</p>
                                <p class="text-lg font-medium">{{ $tipoOrganizacion->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        {{-- Actualizado en --}}
                        <div class="flex items-center">
                            <i class="fas fa-calendar-alt mr-4 text-xl text-yellow-500 dark:text-yellow-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Última Actualización</p>
                                <p class="text-lg font-medium">{{ $tipoOrganizacion->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Botones de Acción (Opcional, pero útil) --}}
                <div class="mt-8 flex justify-center space-x-4">
                    <a href="{{ route('tipo-organizacions.edit', $tipoOrganizacion) }}" 
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-md text-white 
                              bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 
                              transition duration-150 transform hover:scale-105">
                        <i class="fas fa-edit mr-2"></i> Editar Organización
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>