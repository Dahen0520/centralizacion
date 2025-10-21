<x-app-layout>
    <x-slot name="header">
    </x-slot>

    <div class="py-12 flex justify-center">
        <div class="w-full max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 
                        rounded-2xl shadow-3xl p-10 lg:p-12 
                        border-t-4 border-b-4 border-purple-500 dark:border-purple-600 
                        transform hover:shadow-4xl transition-all duration-300 ease-in-out">
                
                {{-- Bloque de Encabezado Elegante --}}
                <div class="text-center mb-10">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full 
                                bg-gradient-to-br from-purple-500 to-indigo-700 text-white 
                                mb-5 shadow-lg transform hover:scale-110 transition-all duration-300 ease-in-out 
                                dark:from-purple-600 dark:to-indigo-800">
                         <i class="fas fa-percent text-4xl"></i>
                    </div>
                    <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-2 leading-tight">
                        Detalles del Impuesto: <span class="text-purple-600 dark:text-purple-400">{{ $impuesto->nombre }}</span>
                    </h3>
                    <p class="text-md text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                        Visualizando la tasa y el historial de este registro impositivo.
                    </p>
                    
                    <a href="{{ route('impuestos.index') }}" 
                       class="mt-6 inline-flex items-center text-sm font-semibold 
                              text-purple-600 dark:text-purple-400 
                              hover:text-purple-800 dark:hover:text-purple-200 
                              transition duration-300 ease-in-out 
                              transform hover:-translate-x-1 hover:underline">
                        <i class="fas fa-arrow-left mr-2"></i> Volver a la Lista de Impuestos
                    </a>
                </div>

                {{-- Tarjeta de Porcentaje (Destacada) --}}
                <div class="bg-indigo-50 dark:bg-gray-700/50 border border-indigo-300 dark:border-indigo-600 
                            rounded-xl p-5 mb-8 shadow-inner flex justify-between items-center">
                    <div class="flex items-center">
                        <i class="fas fa-calculator text-2xl mr-4 text-indigo-600 dark:text-indigo-400"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tasa Aplicable</p>
                            <span class="text-3xl font-extrabold text-indigo-700 dark:text-indigo-400">
                                {{ number_format($impuesto->porcentaje, 2) }}%
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Grid de Detalles (Mejorado) --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl p-8 shadow-xl border border-gray-200 dark:border-gray-700">
                    <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-6 border-b pb-3 border-gray-200 dark:border-gray-700 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-purple-500"></i> Información General
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-6">
                        
                        {{-- ID --}}
                        <div class="flex items-start">
                            <i class="fas fa-fingerprint mr-4 text-xl mt-1 text-gray-500 dark:text-gray-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Identificador (ID)</p>
                                <p class="text-base font-medium text-gray-700 dark:text-gray-300">{{ $impuesto->id }}</p>
                            </div>
                        </div>
                        
                        {{-- Nombre del Impuesto --}}
                        <div class="flex items-start">
                            <i class="fas fa-tag text-lg mr-3 mt-1 text-purple-500 dark:text-purple-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Nombre</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $impuesto->nombre }}</p>
                            </div>
                        </div>
                        
                        {{-- Creado en --}}
                        <div class="flex items-start">
                            <i class="fas fa-clock mr-4 text-xl mt-1 text-green-500 dark:text-green-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Fecha de Creación</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $impuesto->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        {{-- Actualizado en --}}
                        <div class="flex items-start">
                            <i class="fas fa-calendar-alt mr-4 text-xl mt-1 text-yellow-500 dark:text-yellow-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Última Modificación</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $impuesto->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                    </div>
                </div>

                <hr class="my-10 border-gray-200 dark:border-gray-700 opacity-60">
                
                {{-- Botón de Acción --}}
                <div class="flex justify-center">
                    <a href="{{ route('impuestos.edit', $impuesto) }}" 
                       class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-green-500 to-teal-600 border border-transparent 
                              rounded-xl font-bold text-lg text-white uppercase tracking-widest shadow-lg 
                              hover:shadow-xl hover:from-green-600 hover:to-teal-700 focus:outline-none focus:ring-4 focus:ring-green-300 dark:focus:ring-green-800 
                              transition duration-300 transform hover:scale-[1.02]">
                        <i class="fas fa-edit mr-2"></i> Editar Impuesto
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>