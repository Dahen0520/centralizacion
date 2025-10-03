<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Detalles del Afiliado') }}
        </h2>
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
                         <i class="fas fa-user-check text-4xl"></i>
                    </div>
                    <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-2 leading-tight">
                        Ficha de Afiliado: <span class="text-blue-600 dark:text-blue-400">{{ $afiliado->nombre }}</span>
                    </h3>
                    <p class="text-md text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                        Identificación completa y datos de contacto del socio.
                    </p>
                    
                    <a href="{{ route('afiliados.list') }}" 
                       class="mt-6 inline-flex items-center text-sm font-semibold 
                              text-blue-600 dark:text-blue-400 
                              hover:text-blue-800 dark:hover:text-blue-200 
                              transition duration-300 ease-in-out 
                              transform hover:-translate-x-1 hover:underline">
                        <i class="fas fa-arrow-left mr-2"></i> Volver a la Lista de Afiliados
                    </a>
                </div>

                {{-- TARJETA DE DETALLES (Agrupada por secciones) --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl p-8 shadow-xl border border-gray-200 dark:border-gray-700">
                    
                    {{-- Sección de Identificación y Metadatos --}}
                    <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-6 border-b pb-3 border-gray-200 dark:border-gray-700 flex items-center">
                        <i class="fas fa-id-card mr-2 text-blue-500"></i> Identificación y Personales
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-6 mb-8">
                        
                        {{-- DNI (Principal) --}}
                        <div class="md:col-span-2 flex items-start p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                            <i class="fas fa-fingerprint text-xl mr-3 mt-1 text-indigo-500 dark:text-indigo-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Documento de Identidad (DNI)</p>
                                <p class="text-lg font-extrabold text-gray-900 dark:text-white">{{ $afiliado->dni }}</p>
                            </div>
                        </div>

                        {{-- Género --}}
                        <div class="flex items-start">
                            <i class="fas fa-venus-mars text-lg mr-3 mt-1 text-pink-500 dark:text-pink-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Género</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $afiliado->genero ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Fecha de Nacimiento --}}
                        <div class="flex items-start">
                            <i class="fas fa-birthday-cake text-lg mr-3 mt-1 text-teal-500 dark:text-teal-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Fecha de Nacimiento</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $afiliado->fecha_nacimiento ? \Carbon\Carbon::parse($afiliado->fecha_nacimiento)->format('d/m/Y') : 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Sección de Contacto y Ubicación --}}
                    <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-6 border-b pb-3 border-gray-200 dark:border-gray-700 flex items-center">
                        <i class="fas fa-mobile-alt mr-2 text-green-500"></i> Contacto y Domicilio
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-6 mb-8">
                         {{-- Email --}}
                        <div class="flex items-start">
                            <i class="fas fa-envelope text-lg mr-3 mt-1 text-yellow-500 dark:text-yellow-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Email</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white break-words">{{ $afiliado->email ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Teléfono --}}
                        <div class="flex items-start">
                            <i class="fas fa-phone-alt text-lg mr-3 mt-1 text-green-500 dark:text-green-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Teléfono</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $afiliado->telefono ?? 'N/A' }}</p>
                            </div>
                        </div>
                        
                        {{-- Municipio --}}
                        <div class="flex items-start">
                            <i class="fas fa-city text-lg mr-3 mt-1 text-blue-500 dark:text-blue-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Municipio</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $afiliado->municipio->nombre ?? 'N/A' }}</p>
                            </div>
                        </div>
                        
                        {{-- Barrio --}}
                        <div class="flex items-start">
                            <i class="fas fa-home text-lg mr-3 mt-1 text-orange-500 dark:text-orange-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Barrio</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $afiliado->barrio ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Sección Financiera --}}
                    <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-6 border-b pb-3 border-gray-200 dark:border-gray-700 flex items-center">
                        <i class="fas fa-file-invoice-dollar mr-2 text-red-500"></i> Datos Financieros
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-6">
                        {{-- RTN --}}
                        <div class="flex items-start">
                            <i class="fas fa-receipt text-lg mr-3 mt-1 text-red-500 dark:text-red-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">RTN</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $afiliado->rtn ?? 'N/A' }}</p>
                            </div>
                        </div>
                        
                        {{-- Número de Cuenta --}}
                        <div class="flex items-start">
                            <i class="fas fa-university text-lg mr-3 mt-1 text-gray-500 dark:text-gray-400"></i>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">Número de Cuenta</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $afiliado->numero_cuenta ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-10 border-gray-200 dark:border-gray-700 opacity-60">
                
                {{-- Botones de Acción --}}
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('afiliados.edit', $afiliado->id) }}" 
                       class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-green-500 to-teal-600 border border-transparent 
                              rounded-xl font-bold text-lg text-white uppercase tracking-widest shadow-lg 
                              hover:shadow-xl hover:from-green-600 hover:to-teal-700 focus:outline-none focus:ring-4 focus:ring-green-300 dark:focus:ring-green-800 
                              transition duration-300 transform hover:scale-[1.02]">
                        <i class="fas fa-edit mr-2"></i> Editar Afiliado
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>