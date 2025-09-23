<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 leading-tight text-center">
            {{ __('Editar Vinculación') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <nav class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                    <a href="{{ route('asociaciones.index') }}" class="flex items-center hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        <span>Volver a Vinculaciones</span>
                    </a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span class="text-gray-800 dark:text-gray-200">Editar Vinculación</span>
                </nav>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 dark:from-blue-700 dark:to-blue-800 px-8 py-6">
                    <div class="flex items-center">
                        <div class="bg-white/20 p-3 rounded-full mr-4">
                            <i class="fas fa-pen-alt text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-white">
                                Editar Vinculación
                            </h1>
                            <p class="text-sm text-blue-100 dark:text-blue-200 mt-1">
                                Modifica los detalles de la vinculación entre la empresa y la tienda.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    @if (session('success'))
                        <div id="success-alert" class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/50 dark:to-emerald-900/50 border-l-4 border-green-400 p-4 mb-6 rounded-lg shadow-sm" role="alert">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-green-400 rounded-full p-1">
                                        <i class="fas fa-check text-white text-xs"></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="font-semibold text-green-800 dark:text-green-200">¡Actualización exitosa!</p>
                                    <p class="text-green-700 dark:text-green-300 text-sm">{{ session('success') }}</p>
                                </div>
                                <button class="ml-auto text-green-400 hover:text-green-600 transition-colors" onclick="this.closest('#success-alert').remove()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-100 dark:bg-red-950 border-l-4 border-red-500 text-red-700 dark:text-red-300 p-4 mb-6 rounded-lg" role="alert">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mt-1">
                                    <i class="fas fa-exclamation-circle text-red-500"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="font-bold text-red-800 dark:text-red-200">
                                        Se encontraron errores en el formulario
                                    </h3>
                                    <ul class="mt-2 list-disc list-inside space-y-1 text-sm">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('asociaciones.update', ['empresa' => $empresa->id, 'tienda' => $tienda->id]) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center mb-4 text-blue-600 dark:text-blue-400">
                                    <i class="fas fa-building text-lg mr-3"></i>
                                    <h3 class="text-lg font-semibold">
                                        Seleccionar Empresa
                                    </h3>
                                </div>
                                <div class="space-y-2">
                                    <label for="empresa_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Empresa
                                    </label>
                                    <div class="relative">
                                        <select id="empresa_id" name="empresa_id" required 
                                            class="form-select block w-full px-4 py-3 appearance-none border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('empresa_id') border-red-500 @enderror">
                                            @foreach($empresas as $opcionEmpresa)
                                                <option value="{{ $opcionEmpresa->id }}" {{ $opcionEmpresa->id == $empresa->id ? 'selected' : '' }}>
                                                    {{ $opcionEmpresa->nombre_negocio }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                                        </div>
                                    </div>
                                    @error('empresa_id')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center mb-4 text-green-600 dark:text-green-400">
                                    <i class="fas fa-store text-lg mr-3"></i>
                                    <h3 class="text-lg font-semibold">
                                        Seleccionar Tienda
                                    </h3>
                                </div>
                                <div class="space-y-2">
                                    <label for="tienda_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Tienda
                                    </label>
                                    <div class="relative">
                                        <select id="tienda_id" name="tienda_id" required 
                                            class="form-select block w-full px-4 py-3 appearance-none border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 @error('tienda_id') border-red-500 @enderror">
                                            @foreach($tiendas as $opcionTienda)
                                                <option value="{{ $opcionTienda->id }}" {{ $opcionTienda->id == $tienda->id ? 'selected' : '' }}>
                                                    {{ $opcionTienda->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                                        </div>
                                    </div>
                                    @error('tienda_id')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 bg-gray-50 dark:bg-gray-900 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center mb-4 text-purple-600 dark:text-purple-400">
                                <i class="fas fa-flag text-lg mr-3"></i>
                                <h3 class="text-lg font-semibold">
                                    Seleccionar Estado
                                </h3>
                            </div>
                            <div class="space-y-2">
                                <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Estado
                                </label>
                                <div class="relative">
                                    <select id="estado" name="estado" required class="form-select block w-full px-4 py-3 appearance-none border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 leading-tight focus:outline-none focus:shadow-outline transition-all duration-200 @error('estado') border-red-500 @enderror">
                                        <option value="pendiente" {{ old('estado', $asociacion->estado) == 'pendiente' ? 'selected' : '' }}>
                                            Pendiente
                                        </option>
                                        <option value="aprobado" {{ old('estado', $asociacion->estado) == 'aprobado' ? 'selected' : '' }}>
                                            Aprobado
                                        </option>
                                        <option value="rechazado" {{ old('estado', $asociacion->estado) == 'rechazado' ? 'selected' : '' }}>
                                            Rechazado
                                        </option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                                    </div>
                                </div>
                                @error('estado')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mt-8 flex flex-col-reverse sm:flex-row justify-end gap-4">
                            <a href="{{ route('asociaciones.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200 font-semibold">
                                <i class="fas fa-times mr-2"></i>
                                Cancelar
                            </a>
                            <button type="submit" class="order-first sm:order-last w-full sm:w-auto py-3 px-6 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center">
                                <i class="fas fa-save mr-2"></i>
                                Actualizar Vinculación
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Oculta la alerta de éxito después de 5 segundos con animación suave
        const successAlert = document.getElementById('success-alert');
        if (successAlert) {
            setTimeout(function() {
                successAlert.style.opacity = '0';
                successAlert.style.transform = 'translateY(-10px)';
                setTimeout(() => successAlert.remove(), 300);
            }, 5000);
        }
    });
</script>
