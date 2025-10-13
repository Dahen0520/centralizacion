<x-app-layout>
    <x-slot name="header">

    </x-slot>

    {{-- INCLUSIÓN DE SWEETALERT2 PARA NOTIFICACIONES --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- CLASES DE ESPACIADO AJUSTADAS PARA UN FORMULARIO COMPACTO Y ELEGANTE --}}
    <div class="py-6 flex justify-center">
        <div class="w-full max-w-xl mx-auto">
            <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 
                        rounded-2xl shadow-3xl p-10 lg:p-12 
                        border-t-4 border-b-4 border-indigo-600 dark:border-indigo-500 
                        transform hover:shadow-4xl transition-all duration-300 ease-in-out"
                 x-data="{ 
                     identificacion: '{{ old('identificacion') }}', 
                     telefono: '{{ old('telefono') }}' 
                 }">
                
                {{-- Bloque de Encabezado Elegante --}}
                <div class="text-center mb-10">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full 
                                bg-gradient-to-br from-indigo-600 to-purple-700 text-white 
                                mb-5 shadow-lg transform hover:scale-110 transition-all duration-300 ease-in-out 
                                dark:from-indigo-700 dark:to-purple-800">
                         <i class="fas fa-user-plus text-4xl"></i>
                    </div>
                    <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-2 leading-tight">
                        Registrar Nuevo Cliente
                    </h3>
                    <p class="text-md text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                        Completa la información para registros de ventas formales o cotizaciones.
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

                {{-- Manejo de Errores de Validación Estilizado --}}
                @if ($errors->any())
                    <div class="bg-red-50 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-700 dark:text-red-200 
                                px-6 py-4 rounded-lg relative mb-8 shadow-inner" role="alert">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-exclamation-triangle text-xl mr-3 text-red-500 dark:text-red-300"></i>
                            <strong class="font-bold text-lg">¡Atención! Datos Inválidos</strong>
                        </div>
                        <ul class="list-disc list-inside text-sm space-y-1 ml-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('clientes.store') }}" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Nombre Completo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required 
                               placeholder="Ej: Juan Pérez o Empresa S.A."
                               class="w-full px-5 py-3.5 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm 
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400
                                      focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 
                                      transition duration-200 ease-in-out 
                                      @error('nombre') border-red-500 ring-red-500 @enderror">
                        @error('nombre')
                            <p class="text-red-500 text-xs italic mt-2 flex items-center font-medium">
                                <i class="fas fa-info-circle mr-1"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <div>
                            <label for="identificacion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                Identificación / RTN
                            </label>
                            <input type="text" name="identificacion" id="identificacion" 
                                   :value="identificacion" 
                                   @input="formatIdentificacion($event)"
                                   placeholder="0000-0000-000000"
                                   maxlength="16"
                                   autocomplete="off"
                                   class="w-full px-5 py-3.5 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm 
                                          bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400
                                          focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 
                                          transition duration-200 ease-in-out 
                                          @error('identificacion') border-red-500 ring-red-500 @enderror">
                            @error('identificacion')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center font-medium">
                                    <i class="fas fa-info-circle mr-1"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="telefono" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                Teléfono
                            </label>
                            <input type="tel" name="telefono" id="telefono" 
                                   :value="telefono" 
                                   @input="formatTelefono($event)"
                                   placeholder="9999-9999"
                                   maxlength="9"
                                   autocomplete="tel"
                                   class="w-full px-5 py-3.5 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm 
                                          bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400
                                          focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 
                                          transition duration-200 ease-in-out 
                                          @error('telefono') border-red-500 ring-red-500 @enderror">
                            @error('telefono')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center font-medium">
                                    <i class="fas fa-info-circle mr-1"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Email
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                               placeholder="correo@ejemplo.com"
                               class="w-full px-5 py-3.5 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm 
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400
                                      focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 
                                      transition duration-200 ease-in-out 
                                      @error('email') border-red-500 ring-red-500 @enderror">
                        @error('email')
                            <p class="text-red-500 text-xs italic mt-2 flex items-center font-medium">
                                <i class="fas fa-info-circle mr-1"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <hr class="my-10 border-gray-200 dark:border-gray-700 opacity-60">

                    <button type="submit" 
                            class="w-full py-4 bg-gradient-to-r from-indigo-600 to-purple-700 text-white 
                                   font-extrabold rounded-xl shadow-lg hover:shadow-xl 
                                   hover:from-indigo-700 hover:to-purple-800 
                                   transition duration-300 ease-in-out transform hover:-translate-y-0.5 
                                   text-xl uppercase tracking-widest focus:outline-none focus:ring-4 
                                   focus:ring-indigo-300 dark:focus:ring-purple-800">
                        <i class="fas fa-save mr-2"></i> Guardar Cliente
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- SCRIPT PARA FORMATEO Y NOTIFICACIONES --}}
    <script>
        // Funciones de formateo de Alpine
        window.formatIdentificacion = (event) => {
            let value = event.target.value.replace(/\D/g, '');
            if (value.length > 4 && value.length <= 8) {
                value = value.replace(/^(\d{4})(\d+)/, '$1-$2');
            } else if (value.length > 8) {
                value = value.replace(/^(\d{4})(\d{4})(\d{0,6}).*/, '$1-$2-$3');
            }
            event.target.value = value;
        };

        window.formatTelefono = (event) => {
            let value = event.target.value.replace(/\D/g, '');
            if (value.length > 4) {
                value = value.replace(/^(\d{4})(\d{0,4}).*/, '$1-$2');
            }
            event.target.value = value;
        };

        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: '¡Creación Exitosa!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    toast: true,
                    position: 'top-end'
                });
            @endif
        });
    </script>
</x-app-layout>