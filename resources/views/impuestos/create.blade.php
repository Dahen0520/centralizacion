<x-app-layout>
    <x-slot name="header">
    </x-slot>

    {{-- INCLUSIÓN DE SWEETALERT2 PARA NOTIFICACIONES --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-6 flex justify-center">
        <div class="w-full max-w-3xl mx-auto"> {{-- Ancho ajustado para formulario --}}
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
                        Registrar Nuevo Impuesto
                    </h3>
                    <p class="text-md text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                        Define el nombre y el porcentaje de la nueva tasa impositiva.
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

                <form method="POST" action="{{ route('impuestos.store') }}">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-8">
                        
                        {{-- Campo Nombre del Impuesto --}}
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nombre <span class="text-red-500">*</span></label>
                            <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required 
                                   placeholder="Ej: IVA General, IEPS"
                                   class="w-full px-5 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-purple-500 focus:border-purple-500 transition @error('nombre') border-red-500 ring-red-500 @enderror">
                            @error('nombre')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center"><i class="fas fa-info-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Campo Porcentaje --}}
                        <div>
                            <label for="porcentaje" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Porcentaje (%) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="number" id="porcentaje" name="porcentaje" value="{{ old('porcentaje') }}" required step="0.01" min="0" max="100"
                                       placeholder="Ej: 16.00"
                                       class="w-full px-5 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-purple-500 focus:border-purple-500 transition @error('porcentaje') border-red-500 ring-red-500 @enderror">
                                <span class="absolute right-0 top-0 mt-3 mr-4 text-gray-500 dark:text-gray-400 font-bold">%</span>
                            </div>
                            @error('porcentaje')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center"><i class="fas fa-info-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>
                        
                    </div>
                    
                    <hr class="my-10 border-gray-200 dark:border-gray-700 opacity-60 md:col-span-2">

                    <button type="submit" 
                            class="w-full py-4 bg-gradient-to-r from-purple-600 to-indigo-700 text-white 
                                   font-extrabold rounded-xl shadow-lg hover:shadow-xl 
                                   hover:from-purple-700 hover:to-indigo-800 
                                   transition duration-300 ease-in-out transform hover:-translate-y-0.5 
                                   text-xl uppercase tracking-widest focus:outline-none focus:ring-4 
                                   focus:ring-purple-300 dark:focus:ring-purple-800">
                        <i class="fas fa-save mr-2"></i> Guardar Impuesto
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- SCRIPT PARA NOTIFICACIÓN DE SESIÓN --}}
    <script>
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