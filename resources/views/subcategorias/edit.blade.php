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
                         <i class="fas fa-edit text-4xl"></i>
                    </div>
                    <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-2 leading-tight">
                        Modificar: <span class="text-blue-600 dark:text-blue-400">{{ $subcategoria->nombre }}</span>
                    </h3>
                    <p class="text-md text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                        Actualiza la categoría principal o el nombre de la subcategoría.
                    </p>
                    
                    <a href="{{ route('subcategorias.index') }}" 
                       class="mt-6 inline-flex items-center text-sm font-semibold 
                              text-blue-600 dark:text-blue-400 
                              hover:text-blue-800 dark:hover:text-blue-200 
                              transition duration-300 ease-in-out 
                              transform hover:-translate-x-1 hover:underline">
                        <i class="fas fa-arrow-left mr-2"></i> Volver a la lista
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

                <form method="POST" action="{{ route('subcategorias.update', $subcategoria->id) }}">
                    @csrf
                    @method('PUT')
                    
                    {{-- Campo Categoría (Select) --}}
                    <div class="mb-6">
                        <label for="categoria_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Categoría Principal <span class="text-red-500">*</span>
                        </label>
                        <select name="categoria_id" id="categoria_id" required 
                                class="w-full px-5 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 ease-in-out 
                                       @error('categoria_id') border-red-500 ring-red-500 @enderror">
                            <option value="">Seleccione una categoría</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}" {{ old('categoria_id', $subcategoria->categoria_id) == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('categoria_id')
                            <p class="text-red-500 text-xs italic mt-2 flex items-center font-medium">
                                <i class="fas fa-info-circle mr-1"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Campo Nombre de la Subcategoría --}}
                    <div class="mb-8">
                        <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Nombre de la Subcategoría <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $subcategoria->nombre) }}" required 
                               placeholder="Ej: Mesas de Centro, Vestidos de Noche..."
                               class="w-full px-5 py-3.5 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm 
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400
                                      focus:ring-2 focus:ring-blue-500 focus:border-blue-500 
                                      transition duration-200 ease-in-out 
                                      @error('nombre') border-red-500 ring-red-500 @enderror">
                        @error('nombre')
                            <p class="text-red-500 text-xs italic mt-2 flex items-center font-medium">
                                <i class="fas fa-info-circle mr-1"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    <hr class="my-10 border-gray-200 dark:border-gray-700 opacity-60">

                    <button type="submit" 
                            class="w-full py-4 bg-gradient-to-r from-green-500 to-teal-600 text-white 
                                   font-extrabold rounded-xl shadow-lg hover:shadow-xl 
                                   hover:from-green-600 hover:to-teal-700 
                                   transition duration-300 ease-in-out transform hover:-translate-y-0.5 
                                   text-xl uppercase tracking-widest focus:outline-none focus:ring-4 
                                   focus:ring-green-300 dark:focus:ring-green-800">
                        <i class="fas fa-sync-alt mr-2"></i> Actualizar Subcategoría
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- SCRIPT PARA NOTIFICACIÓN DE SESIÓN (Redirigido desde el update) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: '¡Actualización Exitosa!',
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