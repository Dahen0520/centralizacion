<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Explorador de Inventario') }}
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                <i class="fas fa-map-marked-alt mr-1"></i> Gestiona el inventario de tus puntos de venta
            </p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Header con estadísticas --}}
            <div class="mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200 flex items-center">
                            <span class="bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-full w-10 h-10 flex items-center justify-center mr-3 shadow-lg">
                                1
                            </span>
                            Selecciona una Tienda
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 ml-13 mt-1">
                            Elige el punto de venta para explorar su inventario y empresas asociadas
                        </p>
                    </div>
                    <div class="hidden md:block">
                        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20 rounded-xl px-6 py-3 border-2 border-emerald-200 dark:border-emerald-700">
                            <p class="text-sm text-emerald-700 dark:text-emerald-300 font-medium">
                                <i class="fas fa-store-alt mr-2"></i>Total de Tiendas
                            </p>
                            <p class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400">
                                {{ $tiendas->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Grid de Tiendas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($tiendas as $tienda)
                    <a href="{{ route('inventarios.explorar.empresas', $tienda) }}"
                       class="group relative block bg-white dark:bg-gray-800 rounded-2xl shadow-lg border-2 border-gray-200 dark:border-gray-700 hover:border-emerald-500 dark:hover:border-emerald-500 transition-all duration-300 ease-in-out transform hover:-translate-y-2 hover:shadow-2xl overflow-hidden">
                        
                        {{-- Decoración superior --}}
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-500"></div>
                        
                        {{-- Efecto de brillo en hover --}}
                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/0 to-emerald-500/0 group-hover:from-emerald-500/5 group-hover:to-teal-500/5 transition-all duration-300"></div>
                        
                        <div class="relative p-6">
                            {{-- Icono con animación --}}
                            <div class="flex items-center justify-between mb-4">
                                <div class="bg-gradient-to-br from-emerald-100 to-emerald-200 dark:from-emerald-900/40 dark:to-emerald-800/40 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-store-alt text-3xl text-emerald-600 dark:text-emerald-400"></i>
                                </div>
                                <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <i class="fas fa-arrow-right text-2xl text-emerald-600 dark:text-emerald-400"></i>
                                </div>
                            </div>

                            {{-- Contenido --}}
                            <div>
                                <h4 class="text-xl font-extrabold text-gray-900 dark:text-white mb-2 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors duration-300">
                                    {{ $tienda->nombre }}
                                </h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed mb-4">
                                    Explora empresas asociadas y gestiona el inventario de este punto de venta
                                </p>

                                {{-- Badge de acción --}}
                                <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wide">
                                        Ver Inventario
                                    </span>
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">
                                            <i class="fas fa-boxes text-xs mr-1"></i>
                                            Activo
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Indicador de hover en la esquina --}}
                        <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="bg-emerald-500 rounded-full p-1.5 shadow-lg">
                                <i class="fas fa-check text-white text-xs"></i>
                            </div>
                        </div>
                    </a>
                @empty
                    {{-- Estado vacío mejorado --}}
                    <div class="col-span-full">
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-850 rounded-2xl border-2 border-dashed border-gray-300 dark:border-gray-600 p-12">
                            <div class="text-center">
                                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-200 dark:bg-gray-700 rounded-full mb-6">
                                    <i class="fas fa-store-alt-slash text-4xl text-gray-400 dark:text-gray-500"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-700 dark:text-gray-300 mb-2">
                                    No hay tiendas registradas
                                </h3>
                                <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-md mx-auto">
                                    Aún no tienes puntos de venta configurados. Crea tu primera tienda para comenzar a gestionar inventario.
                                </p>
                                <a href="{{ route('tiendas.create') }}" 
                                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200">
                                    <i class="fas fa-plus-circle mr-2"></i>
                                    Crear Primera Tienda
                                </a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Sección de ayuda --}}
            @if($tiendas->count() > 0)
                <div class="mt-10 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-2xl border border-blue-200 dark:border-blue-800 p-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="bg-blue-100 dark:bg-blue-900/50 rounded-xl p-3">
                                <i class="fas fa-info-circle text-2xl text-blue-600 dark:text-blue-400"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
                                ¿Cómo funciona el explorador?
                            </h4>
                            <div class="text-sm text-gray-700 dark:text-gray-300 space-y-2">
                                <p class="flex items-start">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-600 text-white rounded-full text-xs font-bold mr-3 flex-shrink-0 mt-0.5">1</span>
                                    <span>Selecciona una tienda para ver todas las empresas que distribuyen productos en ese punto de venta</span>
                                </p>
                                <p class="flex items-start">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-600 text-white rounded-full text-xs font-bold mr-3 flex-shrink-0 mt-0.5">2</span>
                                    <span>Elige una empresa para consultar su inventario específico en esa tienda</span>
                                </p>
                                <p class="flex items-start">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-600 text-white rounded-full text-xs font-bold mr-3 flex-shrink-0 mt-0.5">3</span>
                                    <span>Gestiona precios, stock y visualiza el historial de ventas por producto</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>