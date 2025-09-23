<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 leading-tight text-center">
            {{ __('Crear Nueva Vinculación') }}
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
                    <span class="text-gray-800 dark:text-gray-200">Nueva Vinculación</span>
                </nav>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 dark:from-blue-700 dark:to-blue-800 px-8 py-6">
                    <div class="flex items-center">
                        <div class="bg-white/20 p-3 rounded-full mr-4">
                            <i class="fas fa-link text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-white">
                                Nueva Vinculación
                            </h1>
                            <p class="text-sm text-blue-100 dark:text-blue-200 mt-1">
                                Selecciona una empresa y una tienda para crear una nueva relación.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-8">
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

                    <form method="POST" action="{{ route('asociaciones.store') }}">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="bg-white dark:bg-gray-900 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center mb-4 text-blue-600 dark:text-blue-400">
                                    <i class="fas fa-building text-lg mr-3"></i>
                                    <h3 class="text-lg font-semibold">
                                        Seleccionar Empresa
                                    </h3>
                                </div>

                                @if($empresas->isEmpty())
                                    <div class="bg-yellow-100 dark:bg-yellow-950 border border-yellow-400 text-yellow-800 dark:text-yellow-200 px-4 py-3 rounded-lg">
                                        <div class="flex items-center">
                                            <i class="fas fa-exclamation-triangle mr-3"></i>
                                            <span>No hay empresas disponibles. <a href="#" class="font-semibold underline hover:text-yellow-600">Crea una empresa primero.</a></span>
                                        </div>
                                    </div>
                                @else
                                    <div class="space-y-2">
                                        <label for="empresa_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Empresa
                                        </label>
                                        <div class="relative">
                                            <select id="empresa_id" name="empresa_id" required 
                                                class="form-select block w-full px-4 py-3 appearance-none border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('empresa_id') border-red-500 @enderror">
                                                <option value="" disabled selected>Selecciona una empresa</option>
                                                @foreach($empresas as $empresa)
                                                    <option value="{{ $empresa->id }}" {{ old('empresa_id') == $empresa->id ? 'selected' : '' }}>
                                                        {{ $empresa->nombre_negocio }}
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
                                @endif
                            </div>

                            <div class="bg-white dark:bg-gray-900 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center mb-4 text-green-600 dark:text-green-400">
                                    <i class="fas fa-store text-lg mr-3"></i>
                                    <h3 class="text-lg font-semibold">
                                        Seleccionar Tienda
                                    </h3>
                                </div>

                                @if($tiendas->isEmpty())
                                    <div class="bg-yellow-100 dark:bg-yellow-950 border border-yellow-400 text-yellow-800 dark:text-yellow-200 px-4 py-3 rounded-lg">
                                        <div class="flex items-center">
                                            <i class="fas fa-exclamation-triangle mr-3"></i>
                                            <span>No hay tiendas disponibles. <a href="#" class="font-semibold underline hover:text-yellow-600">Crea una tienda primero.</a></span>
                                        </div>
                                    </div>
                                @else
                                    <div class="space-y-2">
                                        <label for="tienda_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Tienda
                                        </label>
                                        <div class="relative">
                                            <select id="tienda_id" name="tienda_id" required 
                                                class="form-select block w-full px-4 py-3 appearance-none border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 @error('tienda_id') border-red-500 @enderror">
                                                <option value="" disabled selected>Selecciona una tienda</option>
                                                @foreach($tiendas as $tienda)
                                                    <option value="{{ $tienda->id }}" {{ old('tienda_id') == $tienda->id ? 'selected' : '' }}>
                                                        {{ $tienda->nombre }}
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
                                @endif
                            </div>
                        </div>

                        <div class="mt-8 bg-gray-50 dark:bg-gray-900 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mt-1 mr-3 text-blue-500 dark:text-blue-400">
                                    <i class="fas fa-info-circle text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                        Consideraciones importantes
                                    </h4>
                                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                        <li>• La vinculación se creará en estado **"Pendiente"** por defecto.</li>
                                        <li>• Podrás modificar el estado de la vinculación una vez creada.</li>
                                        <li>• Cada empresa puede vincularse con múltiples tiendas.</li>
                                        <li>• **No se permiten** vinculaciones duplicadas entre la misma empresa y tienda.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex flex-col-reverse sm:flex-row justify-end gap-4">
                            <a href="{{ route('asociaciones.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200 font-semibold">
                                <i class="fas fa-times mr-2"></i>
                                Cancelar
                            </a>
                            @if($empresas->isNotEmpty() && $tiendas->isNotEmpty())
                                <button type="submit" class="order-first sm:order-last w-full sm:w-auto py-3 px-6 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center">
                                    <i class="fas fa-plus mr-2"></i>
                                    Crear Vinculación
                                </button>
                            @else
                                <button type="button" disabled class="order-first sm:order-last w-full sm:w-auto py-3 px-6 bg-gray-400 dark:bg-gray-600 text-white font-semibold rounded-lg cursor-not-allowed flex items-center justify-center opacity-75">
                                    <i class="fas fa-ban mr-2"></i>
                                    Crear Vinculación
                                </button>
                            @endif
                        </div>
                        @if($empresas->isEmpty() || $tiendas->isEmpty())
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 text-center sm:text-right">
                                Necesitas al menos una empresa y una tienda para crear una vinculación.
                            </p>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>