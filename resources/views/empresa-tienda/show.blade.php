<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Detalle de Vinculación') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Navegación superior -->
            <div class="mb-6">
                <nav class="flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-400">
                    <a href="{{ route('asociaciones.index') }}" class="flex items-center hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Volver a Vinculaciones
                    </a>
                    <span>•</span>
                    <span class="text-gray-800 dark:text-gray-200">Detalle</span>
                </nav>
            </div>

            <!-- Card principal -->
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl overflow-hidden">
                <!-- Header del card -->
                <div class="bg-gray-50 dark:bg-gray-700 px-8 py-6 border-b border-gray-200 dark:border-gray-600">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="bg-blue-600 p-3 rounded-lg mr-4">
                                <i class="fas fa-handshake text-white text-lg"></i>
                            </div>
                            <div>
                                <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                    Información de la Vinculación
                                </h1>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Relación entre empresa y tienda
                                </p>
                            </div>
                        </div>
                        
                        <!-- Acciones -->
                        <div class="flex space-x-3">
                            <a href="{{ route('asociaciones.edit', ['empresa' => $empresa->id, 'tienda' => $tienda->id]) }}" 
                               class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                                <i class="fas fa-edit mr-2"></i>
                                Editar
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Contenido -->
                <div class="p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Información de la Empresa -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center mb-4">
                                <div class="bg-blue-600 p-2 rounded-lg mr-3">
                                    <i class="fas fa-building text-white"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100">
                                    Empresa
                                </h3>
                            </div>
                            <div class="space-y-3">
                                <div class="flex justify-between items-start">
                                    <span class="text-sm font-medium text-blue-800 dark:text-blue-200">Nombre:</span>
                                    <span class="text-sm text-blue-700 dark:text-blue-300 text-right max-w-xs">
                                        {{ $empresa->nombre_negocio }}
                                    </span>
                                </div>
                                @if($empresa->descripcion)
                                    <div class="flex justify-between items-start">
                                        <span class="text-sm font-medium text-blue-800 dark:text-blue-200">Descripción:</span>
                                        <span class="text-sm text-blue-700 dark:text-blue-300 text-right max-w-xs">
                                            {{ $empresa->descripcion }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Información de la Tienda -->
                        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-6 border border-green-200 dark:border-green-800">
                            <div class="flex items-center mb-4">
                                <div class="bg-green-600 p-2 rounded-lg mr-3">
                                    <i class="fas fa-store text-white"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-green-900 dark:text-green-100">
                                    Tienda
                                </h3>
                            </div>
                            <div class="space-y-3">
                                <div class="flex justify-between items-start">
                                    <span class="text-sm font-medium text-green-800 dark:text-green-200">Nombre:</span>
                                    <span class="text-sm text-green-700 dark:text-green-300 text-right max-w-xs">
                                        {{ $tienda->nombre }}
                                    </span>
                                </div>
                                @if($tienda->descripcion)
                                    <div class="flex justify-between items-start">
                                        <span class="text-sm font-medium text-green-800 dark:text-green-200">Descripción:</span>
                                        <span class="text-sm text-green-700 dark:text-green-300 text-right max-w-xs">
                                            {{ $tienda->descripcion }}
                                        </span>
                                    </div>
                                @endif
                                @if($tienda->ubicacion)
                                    <div class="flex justify-between items-start">
                                        <span class="text-sm font-medium text-green-800 dark:text-green-200">Ubicación:</span>
                                        <span class="text-sm text-green-700 dark:text-green-300 text-right max-w-xs">
                                            {{ $tienda->ubicacion }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Estado de la vinculación -->
                    <div class="mt-8">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="bg-purple-600 p-2 rounded-lg mr-3">
                                        <i class="fas fa-flag text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                            Estado de la Vinculación
                                        </h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            Estado actual de la relación
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-full
                                        @if($asociacion->estado == 'aprobado') 
                                            bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($asociacion->estado == 'rechazado') 
                                            bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @else 
                                            bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @endif">
                                        @if($asociacion->estado == 'aprobado')
                                            <i class="fas fa-check-circle mr-2"></i>
                                        @elseif($asociacion->estado == 'rechazado')
                                            <i class="fas fa-times-circle mr-2"></i>
                                        @else
                                            <i class="fas fa-clock mr-2"></i>
                                        @endif
                                        {{ ucfirst($asociacion->estado) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    @if($asociacion->created_at)
                        <div class="mt-8">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                    <i class="fas fa-info-circle text-gray-500 mr-2"></i>
                                    Información Adicional
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="font-medium text-gray-700 dark:text-gray-300">Fecha de creación:</span>
                                        <span class="text-gray-600 dark:text-gray-400 ml-2">
                                            {{ $asociacion->created_at->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                    @if($asociacion->updated_at && $asociacion->updated_at != $asociacion->created_at)
                                        <div>
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Última actualización:</span>
                                            <span class="text-gray-600 dark:text-gray-400 ml-2">
                                                {{ $asociacion->updated_at->format('d/m/Y H:i') }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>