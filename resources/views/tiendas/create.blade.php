<x-app-layout>
    <x-slot name="header">
    </x-slot>

    {{-- INCLUSIÓN DE SWEETALERT2 PARA NOTIFICACIONES --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-6 flex justify-center">
        <div class="w-full max-w-xl mx-auto">
            <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 
                        rounded-2xl shadow-3xl p-10 lg:p-12 
                        border-t-4 border-b-4 border-blue-500 dark:border-blue-600 
                        transform hover:shadow-4xl transition-all duration-300 ease-in-out">
                
                {{-- Bloque de Encabezado Elegante --}}
                <div class="text-center mb-10">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full 
                                bg-gradient-to-br from-blue-500 to-blue-700 text-white 
                                mb-5 shadow-lg transform hover:scale-110 transition-all duration-300 ease-in-out 
                                dark:from-blue-600 dark:to-blue-800">
                         <i class="fas fa-store text-4xl"></i>
                    </div>
                    <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-2 leading-tight">
                        Registrar nueva Tienda
                    </h3>
                    <p class="text-md text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                        Define el nombre y la información fiscal de la nueva tienda.
                    </p>
                    
                    <a href="{{ route('tiendas.index') }}" 
                       class="mt-6 inline-flex items-center text-sm font-semibold 
                              text-blue-600 dark:text-blue-400 
                              hover:text-blue-800 dark:hover:text-blue-200 
                              transition duration-300 ease-in-out 
                              transform hover:-translate-x-1 hover:underline">
                        <i class="fas fa-arrow-left mr-2"></i> Volver a la Lista de Tiendas
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

                <form method="POST" action="{{ route('tiendas.store') }}">
                    @csrf

                    {{-- 1. INFORMACIÓN BÁSICA Y FISCAL --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Campo Nombre de la Tienda --}}
                        <div class="mb-4 md:mb-0 md:col-span-2">
                            <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nombre de la Tienda <span class="text-red-500">*</span></label>
                            <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required 
                                placeholder="Ej: Mall Multiplaza, Sucursal Centro..."
                                class="w-full px-5 py-3.5 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('nombre') border-red-500 ring-red-500 @enderror">
                            @error('nombre')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center font-medium">
                                    <i class="fas fa-info-circle mr-1"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Campo RTN (NUEVO) --}}
                        <div class="mb-4">
                            <label for="rtn" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">RTN</label>
                            <input type="text" id="rtn" name="rtn" value="{{ old('rtn') }}" 
                                placeholder="Ej: 08011980123456"
                                maxlength="50"
                                class="w-full px-5 py-3.5 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('rtn') border-red-500 ring-red-500 @enderror">
                            @error('rtn')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center font-medium">
                                    <i class="fas fa-info-circle mr-1"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Campo Teléfono (NUEVO) --}}
                        <div class="mb-4">
                            <label for="telefono" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Teléfono</label>
                            <input type="text" id="telefono" name="telefono" value="{{ old('telefono') }}" 
                                placeholder="Ej: 9999-9999"
                                maxlength="20"
                                class="w-full px-5 py-3.5 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('telefono') border-red-500 ring-red-500 @enderror">
                            @error('telefono')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center font-medium">
                                    <i class="fas fa-info-circle mr-1"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-8 border-gray-200 dark:border-gray-700 opacity-60">
                    
                    {{-- 2. INFORMACIÓN DE UBICACIÓN --}}

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Campo Municipio --}}
                        <div class="mb-6">
                            <label for="municipio_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Municipio <span class="text-red-500">*</span></label>
                            <select name="municipio_id" id="municipio_id" required 
                                    class="w-full px-5 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('municipio_id') border-red-500 ring-red-500 @enderror">
                                <option value="">Seleccione un municipio</option>
                                @foreach($municipios as $municipio)
                                    <option value="{{ $municipio->id }}" @selected(old('municipio_id') == $municipio->id)>
                                        {{ $municipio->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('municipio_id')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center font-medium">
                                    <i class="fas fa-info-circle mr-1"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>
                        
                        {{-- Campo Dirección (NUEVO) --}}
                        <div class="mb-6">
                            <label for="direccion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dirección Completa</label>
                            <input type="text" id="direccion" name="direccion" value="{{ old('direccion') }}" 
                                placeholder="Colonia, calle, referencia..."
                                maxlength="255"
                                class="w-full px-5 py-3.5 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('direccion') border-red-500 ring-red-500 @enderror">
                            @error('direccion')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center font-medium">
                                    <i class="fas fa-info-circle mr-1"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>
                    
                    <hr class="my-10 border-gray-200 dark:border-gray-700 opacity-60">

                    <button type="submit" 
                            class="w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-700 text-white 
                                   font-extrabold rounded-xl shadow-lg hover:shadow-xl 
                                   hover:from-blue-700 hover:to-indigo-800 
                                   transition duration-300 ease-in-out transform hover:-translate-y-0.5 
                                   text-xl uppercase tracking-widest focus:outline-none focus:ring-4 
                                   focus:ring-blue-300 dark:focus:ring-blue-800">
                        <i class="fas fa-save mr-2"></i> Guardar Tienda
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- SCRIPT PARA NOTIFICACIÓN DE SESIÓN (Redirigido desde el store) --}}
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