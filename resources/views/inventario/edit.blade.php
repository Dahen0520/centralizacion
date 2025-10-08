<x-app-layout>
    <x-slot name="header">
    </x-slot>

    {{-- INCLUSIÓN DE SWEETALERT2 PARA NOTIFICACIONES --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-6 flex justify-center">
        <div class="w-full max-w-3xl mx-auto"> {{-- Ancho ajustado para formulario --}}
            <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 
                        rounded-2xl shadow-3xl p-10 lg:p-12 
                        border-t-4 border-b-4 border-emerald-500 dark:border-emerald-600 
                        transform hover:shadow-4xl transition-all duration-300 ease-in-out">
                
                {{-- Bloque de Encabezado Elegante --}}
                <div class="text-center mb-10">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full 
                                bg-gradient-to-br from-emerald-500 to-emerald-700 text-white 
                                mb-5 shadow-lg transform hover:scale-110 transition-all duration-300 ease-in-out 
                                dark:from-emerald-600 dark:to-emerald-800">
                         <i class="fas fa-sync-alt text-4xl"></i>
                    </div>
                    <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-2 leading-tight">
                        Actualizar Inventario
                    </h3>
                    <p class="text-md text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                        Modificando precio y stock para la ubicación actual.
                    </p>
                    
                    {{-- Lógica de Redirección para el botón Volver --}}
                    @php
                        $backRoute = route('inventarios.index');
                        if (request('redirect_to') === 'explorar' && request('empresa_id') && request('tienda_id')) {
                            $backRoute = route('inventarios.explorar.inventario', ['empresa' => request('empresa_id'), 'tienda' => request('tienda_id')]);
                        }
                    @endphp
                    
                    <a href="{{ $backRoute }}" 
                       class="mt-6 inline-flex items-center text-sm font-semibold 
                              text-emerald-600 dark:text-emerald-400 
                              hover:text-emerald-800 dark:hover:text-emerald-200 
                              transition duration-300 ease-in-out 
                              transform hover:-translate-x-1 hover:underline">
                        <i class="fas fa-arrow-left mr-2"></i> Volver a la Vista Anterior
                    </a>
                </div>

                {{-- INFORMACIÓN ESTÁTICA DEL REGISTRO --}}
                <div class="bg-gray-100 dark:bg-gray-700/50 p-5 rounded-lg mb-8 border border-gray-200 dark:border-gray-600">
                    <p class="text-lg font-bold text-gray-800 dark:text-white mb-2">
                        Producto: <span class="text-indigo-600 dark:text-indigo-400">{{ $inventario->marca->producto->nombre ?? 'N/A' }}</span>
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-code mr-1"></i> Código de Marca: {{ $inventario->marca->codigo_marca }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-store mr-1"></i> Tienda: {{ $inventario->tienda->nombre ?? 'N/A' }}
                    </p>
                    {{-- Información de Empresa, si está cargada (útil para el usuario) --}}
                    @if(isset($inventario->marca->empresa->nombre_negocio))
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-building mr-1"></i> Empresa: {{ $inventario->marca->empresa->nombre_negocio }}
                    </p>
                    @endif
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

                <form method="POST" action="{{ route('inventarios.update', $inventario) }}">
                    @csrf
                    @method('PUT')
                    
                    {{-- 1. CAMPOS OCULTOS PARA CONTROLAR LA REDIRECCIÓN (CLAVE PARA LA SOLUCIÓN) --}}
                    <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">
                    <input type="hidden" name="empresa_id" value="{{ request('empresa_id') }}">
                    <input type="hidden" name="tienda_id" value="{{ request('tienda_id') }}">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-8">
                        
                        {{-- Campo Precio --}}
                        <div>
                            <label for="precio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Precio (L) <span class="text-red-500">*</span></label>
                            <input type="number" id="precio" name="precio" value="{{ old('precio', $inventario->precio) }}" required 
                                   step="0.01" min="0" placeholder="Ej: 19.99"
                                   class="w-full px-5 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-emerald-500 focus:border-emerald-500 transition @error('precio') border-red-500 ring-red-500 @enderror">
                            @error('precio')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center"><i class="fas fa-info-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Campo Stock --}}
                        <div>
                            <label for="stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Stock Disponible <span class="text-red-500">*</span></label>
                            <input type="number" id="stock" name="stock" value="{{ old('stock', $inventario->stock) }}" required 
                                   min="0" placeholder="Ej: 150"
                                   class="w-full px-5 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-emerald-500 focus:border-emerald-500 transition @error('stock') border-red-500 ring-red-500 @enderror">
                            @error('stock')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center"><i class="fas fa-info-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>
                        
                    </div>
                    
                    <hr class="my-10 border-gray-200 dark:border-gray-700 opacity-60 md:col-span-2">

                    <button type="submit" 
                            class="w-full py-4 bg-gradient-to-r from-green-500 to-teal-600 text-white 
                                   font-extrabold rounded-xl shadow-lg hover:shadow-xl 
                                   hover:from-green-600 hover:to-teal-700 
                                   transition duration-300 ease-in-out transform hover:-translate-y-0.5 
                                   text-xl uppercase tracking-widest focus:outline-none focus:ring-4 
                                   focus:ring-green-300 dark:focus:ring-green-800">
                        <i class="fas fa-sync-alt mr-2"></i> Actualizar Inventario
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