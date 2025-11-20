<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Inventario en ') }}<span class="text-emerald-600 dark:text-emerald-400">{{ $tienda->nombre }}</span>
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                <i class="fas fa-map-marker-alt mr-1"></i> Punto de venta seleccionado
            </p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Navegación --}}
            <div class="mb-6">
                <a href="{{ route('inventarios.explorar.tiendas') }}" 
                   class="inline-flex items-center text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition font-medium group">
                    <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i> 
                    Volver a Tiendas
                </a>
            </div>

            {{-- Breadcrumb Visual --}}
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-850 rounded-xl p-4 mb-8 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-center space-x-3 text-sm">
                    <div class="flex items-center">
                        <span class="bg-emerald-500 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold shadow-lg">
                            <i class="fas fa-check text-xs"></i>
                        </span>
                        <span class="ml-2 font-medium text-gray-700 dark:text-gray-300">Tienda Seleccionada</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                    <div class="flex items-center">
                        <span class="bg-indigo-500 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold shadow-lg animate-pulse">
                            2
                        </span>
                        <span class="ml-2 font-medium text-indigo-600 dark:text-indigo-400">Seleccionar Empresa</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                    <div class="flex items-center">
                        <span class="bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400 rounded-full w-8 h-8 flex items-center justify-center font-bold">
                            3
                        </span>
                        <span class="ml-2 font-medium text-gray-500 dark:text-gray-500">Ver Inventario</span>
                    </div>
                </div>
            </div>

            {{-- Header con estadísticas --}}
            <div class="mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200 flex items-center">
                            <span class="bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-full w-10 h-10 flex items-center justify-center mr-3 shadow-lg">
                                2
                            </span>
                            Selecciona una Empresa
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 ml-13 mt-1">
                            Elige la empresa para explorar sus productos en <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ $tienda->nombre }}</span>
                        </p>
                    </div>
                    <div class="hidden md:block">
                        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20 rounded-xl px-6 py-3 border-2 border-indigo-200 dark:border-indigo-700">
                            <p class="text-sm text-indigo-700 dark:text-indigo-300 font-medium">
                                <i class="fas fa-building mr-2"></i>Empresas Disponibles
                            </p>
                            <p class="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400">
                                {{ $empresas->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Barra de búsqueda --}}
            @if($empresas->count() > 6)
                <div class="mb-6">
                    <div class="relative">
                        <input type="text" 
                               id="searchEmpresas"
                               placeholder="Buscar empresa por nombre..."
                               class="w-full pl-12 pr-4 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 shadow-md">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-lg"></i>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Grid de Empresas --}}
            <div id="empresasGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($empresas as $empresa)
                    <a href="{{ route('inventarios.explorar.inventario', ['empresa' => $empresa->id, 'tienda' => $tienda->id]) }}"
                       data-empresa-name="{{ strtolower($empresa->nombre_negocio) }}"
                       class="empresa-card group relative block bg-white dark:bg-gray-800 rounded-2xl shadow-lg border-2 border-gray-200 dark:border-gray-700 hover:border-indigo-500 dark:hover:border-indigo-500 transition-all duration-300 ease-in-out transform hover:-translate-y-2 hover:shadow-2xl overflow-hidden">
                        
                        {{-- Decoración superior --}}
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 via-purple-600 to-pink-500"></div>
                        
                        {{-- Efecto de brillo en hover --}}
                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/0 to-indigo-500/0 group-hover:from-indigo-500/5 group-hover:to-purple-500/5 transition-all duration-300"></div>
                        
                        <div class="relative p-6">
                            {{-- Icono con animación --}}
                            <div class="flex items-center justify-between mb-4">
                                <div class="bg-gradient-to-br from-indigo-100 to-indigo-200 dark:from-indigo-900/40 dark:to-indigo-800/40 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-building text-3xl text-indigo-600 dark:text-indigo-400"></i>
                                </div>
                                <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <i class="fas fa-arrow-right text-2xl text-indigo-600 dark:text-indigo-400"></i>
                                </div>
                            </div>

                            {{-- Contenido --}}
                            <div>
                                <h4 class="text-xl font-extrabold text-gray-900 dark:text-white mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-300">
                                    {{ $empresa->nombre_negocio }}
                                </h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed mb-4">
                                    Consulta el inventario completo de productos de esta empresa en {{ $tienda->nombre }}
                                </p>

                                {{-- Footer de la tarjeta --}}
                                <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wide">
                                        Ver Productos
                                    </span>
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300">
                                            <i class="fas fa-boxes text-xs mr-1"></i>
                                            Inventario
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Indicador de hover en la esquina --}}
                        <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="bg-indigo-500 rounded-full p-1.5 shadow-lg">
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
                                    <i class="fas fa-building-slash text-4xl text-gray-400 dark:text-gray-500"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-700 dark:text-gray-300 mb-2">
                                    No hay empresas asociadas
                                </h3>
                                <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-md mx-auto">
                                    Esta tienda no tiene empresas asociadas aún. Asocia empresas para comenzar a gestionar su inventario.
                                </p>
                                <a href="{{ route('empresas.index') }}" 
                                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200">
                                    <i class="fas fa-link mr-2"></i>
                                    Asociar Empresas
                                </a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Estado de no resultados en búsqueda --}}
            <div id="noResults" class="hidden">
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-850 rounded-2xl border border-gray-300 dark:border-gray-600 p-12">
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-200 dark:bg-gray-700 rounded-full mb-6">
                            <i class="fas fa-search-minus text-4xl text-gray-400 dark:text-gray-500"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-700 dark:text-gray-300 mb-2">
                            No se encontraron empresas
                        </h3>
                        <p class="text-gray-500 dark:text-gray-400">
                            Intenta con otro término de búsqueda
                        </p>
                    </div>
                </div>
            </div>

            {{-- Sección de ayuda --}}
            @if($empresas->count() > 0)
                <div class="mt-10 bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-2xl border border-amber-200 dark:border-amber-800 p-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="bg-amber-100 dark:bg-amber-900/50 rounded-xl p-3">
                                <i class="fas fa-lightbulb text-2xl text-amber-600 dark:text-amber-400"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
                                💡 Consejo
                            </h4>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Al seleccionar una empresa podrás ver todos sus productos disponibles en <span class="font-semibold text-amber-700 dark:text-amber-400">{{ $tienda->nombre }}</span>, 
                                ajustar precios y stock, y consultar el historial de ventas detallado.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- JavaScript para búsqueda --}}
    @if($empresas->count() > 6)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('searchEmpresas');
                const empresasGrid = document.getElementById('empresasGrid');
                const noResults = document.getElementById('noResults');
                const empresaCards = document.querySelectorAll('.empresa-card');

                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    let visibleCount = 0;

                    empresaCards.forEach(card => {
                        const empresaName = card.dataset.empresaName;
                        
                        if (empresaName.includes(searchTerm)) {
                            card.classList.remove('hidden');
                            visibleCount++;
                        } else {
                            card.classList.add('hidden');
                        }
                    });

                    if (visibleCount === 0) {
                        empresasGrid.classList.add('hidden');
                        noResults.classList.remove('hidden');
                    } else {
                        empresasGrid.classList.remove('hidden');
                        noResults.classList.add('hidden');
                    }
                });
            });
        </script>
    @endif
</x-app-layout>