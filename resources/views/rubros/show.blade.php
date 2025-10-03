<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Detalles del Rubro') }}
        </h2>
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
                        Información del Rubro
                    </h3>
                    <p class="text-md text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                        Visualizando los datos principales y metadatos del registro.
                    </p>
                    
                    <a href="{{ route('rubros.index') }}" 
                       class="mt-6 inline-flex items-center text-sm font-semibold 
                              text-blue-600 dark:text-blue-400 
                              hover:text-blue-800 dark:hover:text-blue-200 
                              transition duration-300 ease-in-out 
                              transform hover:-translate-x-1 hover:underline">
                        <i class="fas fa-arrow-left mr-2"></i> Volver a la Lista de Rubros
                    </a>
                </div>

                {{-- TARJETA DE DETALLES PRINCIPAL --}}
                <div class="bg-white dark:bg-gray-700 rounded-xl p-8 shadow-xl border border-gray-100 dark:border-gray-600">
                    
                    {{-- Nombre del Rubro (Destacado) --}}
                    <h3 class="text-3xl font-extrabold text-blue-600 dark:text-blue-400 mb-5 border-b pb-3 border-gray-200 dark:border-gray-600 flex items-center">
                        <i class="fas fa-tag mr-3 text-2xl"></i> {{ $rubro->nombre }}
                    </h3>
                    
                    {{-- Grid de Metadatos --}}
                    <div class="grid grid-cols-1 gap-6 text-gray-700 dark:text-gray-300">
                        
                        <div class="flex items-center">
                            <i class="fas fa-fingerprint mr-4 text-xl text-indigo-500 dark:text-indigo-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Identificador (ID)</p>
                                <p class="text-lg font-bold">{{ $rubro->id }}</p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <i class="fas fa-clock mr-4 text-xl text-green-500 dark:text-green-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Fecha de Creación</p>
                                <p class="text-lg font-medium">{{ $rubro->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <i class="fas fa-calendar-alt mr-4 text-xl text-yellow-500 dark:text-yellow-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Última Actualización</p>
                                <p class="text-lg font-medium">{{ $rubro->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Botones de Acción --}}
                <div class="mt-8 flex justify-center space-x-4">
                    <a href="{{ route('rubros.edit', $rubro) }}" 
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-md text-white 
                              bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 
                              transition duration-150 transform hover:scale-105">
                        <i class="fas fa-edit mr-2"></i> Editar Rubro
                    </a>

                    {{-- Formulario de Eliminación (Requiere SweetAlert2, asumo que ya lo tienes implementado en rubros.index) --}}
                    <form method="POST" action="{{ route('rubros.destroy', $rubro) }}" onsubmit="return false;">
                        @csrf
                        @method('DELETE')
                        <button type="button" 
                                class="delete-btn inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-md text-white 
                                       bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 
                                       transition duration-150 transform hover:scale-105"
                                data-name="{{ $rubro->nombre }}">
                            <i class="fas fa-trash-alt mr-2"></i> Eliminar Rubro
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>