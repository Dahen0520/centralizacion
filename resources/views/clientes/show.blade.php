<x-app-layout>
    <x-slot name="header">
    </x-slot>

    <div class="py-12 flex justify-center">
        <div class="w-full max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 
                        rounded-2xl shadow-3xl p-10 lg:p-12 
                        border-t-4 border-b-4 border-indigo-600 dark:border-indigo-500 
                        transform hover:shadow-4xl transition-all duration-300 ease-in-out">
                
                {{-- Bloque de Encabezado Elegante --}}
                <div class="text-center mb-10">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full 
                                bg-gradient-to-br from-indigo-600 to-purple-700 text-white 
                                mb-5 shadow-lg transform hover:scale-110 transition-all duration-300 ease-in-out 
                                dark:from-indigo-700 dark:to-purple-800">
                         <i class="fas fa-user-tag text-4xl"></i>
                    </div>
                    <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-2 leading-tight">
                        Ficha de Cliente: <span class="text-indigo-600 dark:text-indigo-400">{{ $cliente->nombre }}</span>
                    </h3>
                    <p class="text-md text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                        Visualizando la información de contacto y metadatos del cliente.
                    </p>
                    
                    <a href="{{ route('clientes.index') }}" 
                       class="mt-6 inline-flex items-center text-sm font-semibold 
                              text-indigo-600 dark:text-indigo-400 
                              hover:text-indigo-800 dark:hover:text-indigo-200 
                              transition duration-300 ease-in-out 
                              transform hover:-translate-x-1 hover:underline">
                        <i class="fas fa-arrow-left mr-2"></i> Volver a la Lista de Clientes
                    </a>
                </div>

                {{-- TARJETA DE DETALLES PRINCIPAL --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl p-8 shadow-xl border border-gray-200 dark:border-gray-700">
                    
                    {{-- Nombre del Cliente (Destacado) --}}
                    <h4 class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400 mb-6 border-b pb-3 border-gray-200 dark:border-gray-700 flex items-center">
                        <i class="fas fa-address-book mr-3 text-2xl"></i> {{ $cliente->nombre }}
                    </h4>
                    
                    {{-- Grid de Metadatos --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-700 dark:text-gray-300">
                        
                        {{-- ID --}}
                        <div class="flex items-start">
                            <i class="fas fa-fingerprint mr-4 text-xl mt-1 text-purple-500 dark:text-purple-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Identificador (ID)</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $cliente->id }}</p>
                            </div>
                        </div>

                        {{-- Identificación / RTN --}}
                        @php
                            $rtn_display = $cliente->identificacion 
                                ? preg_replace('/^(\d{4})(\d{4})(\d{6})$/', '$1-$2-$3', $cliente->identificacion) 
                                : 'N/A';
                        @endphp
                        <div class="flex items-start">
                            <i class="fas fa-id-card mr-4 text-xl mt-1 text-gray-500 dark:text-gray-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">RTN / Identidad</p>
                                <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $rtn_display }}</p>
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="flex items-start">
                            <i class="fas fa-envelope mr-4 text-xl mt-1 text-blue-500 dark:text-blue-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Email</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $cliente->email ?? 'No registrado' }}</p>
                            </div>
                        </div>

                        {{-- Teléfono --}}
                        @php
                            $phone_display = $cliente->telefono 
                                ? preg_replace('/^(\d{4})(\d{4})$/', '$1-$2', $cliente->telefono) 
                                : 'N/A';
                        @endphp
                        <div class="flex items-start">
                            <i class="fas fa-phone mr-4 text-xl mt-1 text-green-500 dark:text-green-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Teléfono</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $phone_display }}</p>
                            </div>
                        </div>
                        
                        {{-- Creado en --}}
                        <div class="flex items-start">
                            <i class="fas fa-clock mr-4 text-xl mt-1 text-teal-500 dark:text-teal-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Fecha de Registro</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $cliente->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        {{-- Actualizado en --}}
                        <div class="flex items-start">
                            <i class="fas fa-calendar-alt mr-4 text-xl mt-1 text-amber-500 dark:text-amber-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Última Modificación</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $cliente->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                    </div>
                </div>

                <hr class="my-10 border-gray-200 dark:border-gray-700 opacity-60">
                
                {{-- Botón de Acción --}}
                <div class="flex justify-center">
                    <a href="{{ route('clientes.edit', $cliente) }}" 
                       class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-green-500 to-teal-600 border border-transparent 
                              rounded-xl font-bold text-lg text-white uppercase tracking-widest shadow-lg 
                              hover:shadow-xl hover:from-green-600 hover:to-teal-700 focus:outline-none focus:ring-4 focus:ring-green-300 dark:focus:ring-green-800 
                              transition duration-300 transform hover:scale-[1.02]">
                        <i class="fas fa-edit mr-2"></i> Editar Cliente
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>