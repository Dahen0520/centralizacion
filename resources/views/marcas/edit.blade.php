<x-app-layout>
    <x-slot name="header">
    </x-slot>

    {{-- INCLUSIÓN DE SWEETALERT2 PARA NOTIFICACIONES --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-6 flex justify-center">
        <div class="w-full max-w-xl mx-auto"> {{-- Ancho ajustado --}}
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
                        Modificar Marca: <span class="text-blue-600 dark:text-blue-400">{{ $marca->codigo_marca }}</span>
                    </h3>
                    <p class="text-md text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                        Actualiza el producto y empresa asociados, y el estado de aprobación.
                    </p>
                    
                    <a href="{{ route('marcas.index') }}" 
                       class="mt-6 inline-flex items-center text-sm font-semibold 
                              text-blue-600 dark:text-blue-400 
                              hover:text-blue-800 dark:hover:text-blue-200 
                              transition duration-300 ease-in-out 
                              transform hover:-translate-x-1 hover:underline">
                        <i class="fas fa-arrow-left mr-2"></i> Volver a la Lista de Marcas
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

                <form method="POST" action="{{ route('marcas.update', $marca) }}">
                    @csrf
                    @method('PUT')
                    
                    {{-- Campo Producto --}}
                    <div class="mb-6">
                        <label for="producto_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Producto <span class="text-red-500">*</span></label>
                        <select name="producto_id" id="producto_id" required 
                                class="w-full px-5 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 transition @error('producto_id') border-red-500 ring-red-500 @enderror">
                            <option value="">Selecciona un producto</option>
                            @foreach($productos as $producto)
                                <option value="{{ $producto->id }}" @selected(old('producto_id', $marca->producto_id) == $producto->id)>
                                    {{ $producto->nombre }} ({{ $producto->subcategoria->nombre ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                        @error('producto_id')
                            <p class="text-red-500 text-xs italic mt-2 flex items-center"><i class="fas fa-info-circle mr-1"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Campo Empresa --}}
                    <div class="mb-6">
                        <label for="empresa_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Empresa <span class="text-red-500">*</span></label>
                        <select name="empresa_id" id="empresa_id" required 
                                class="w-full px-5 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 transition @error('empresa_id') border-red-500 ring-red-500 @enderror">
                            <option value="">Selecciona una empresa</option>
                            @foreach($empresas as $empresa)
                                <option value="{{ $empresa->id }}" @selected(old('empresa_id', $marca->empresa_id) == $empresa->id)>
                                    {{ $empresa->nombre_negocio }}
                                </option>
                            @endforeach
                        </select>
                        @error('empresa_id')
                            <p class="text-red-500 text-xs italic mt-2 flex items-center"><i class="fas fa-info-circle mr-1"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Campo Estado --}}
                    <div class="mb-8">
                        <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Estado <span class="text-red-500">*</span></label>
                        <select name="estado" id="estado" required 
                                class="w-full px-5 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 transition @error('estado') border-red-500 ring-red-500 @enderror">
                            <option value="pendiente" @selected(old('estado', $marca->estado) == 'pendiente')>Pendiente</option>
                            <option value="aprobado" @selected(old('estado', $marca->estado) == 'aprobado')>Aprobado</option>
                            <option value="rechazado" @selected(old('estado', $marca->estado) == 'rechazado')>Rechazado</option>
                        </select>
                        @error('estado')
                            <p class="text-red-500 text-xs italic mt-2 flex items-center"><i class="fas fa-info-circle mr-1"></i> {{ $message }}</p>
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
                        <i class="fas fa-sync-alt mr-2"></i> Actualizar Marca
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