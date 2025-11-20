<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-center">
            <div class="text-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Inventario de ') }}<span class="text-indigo-600 dark:text-indigo-400">{{ $empresa->nombre_negocio }}</span>
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    <i class="fas fa-store-alt mr-1"></i> {{ $tienda->nombre }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Navegación --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <a href="{{ route('inventarios.explorar.empresas', $tienda) }}" 
                   class="inline-flex items-center text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition font-medium group">
                    <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i> 
                    Volver a Lista de Empresas
                </a>
                
                <a href="{{ route('inventarios.explorar.ventas', ['empresa' => $empresa->id, 'tienda' => $tienda->id]) }}" 
                   class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-200 group">
                    <i class="fas fa-chart-line mr-2 group-hover:scale-110 transition-transform"></i> 
                    Ver Historial de Ventas
                </a>
            </div>

            {{-- Tarjetas de Estadísticas Rápidas --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Total Productos</p>
                            <p class="text-3xl font-bold mt-1">{{ $inventarios->count() }}</p>
                        </div>
                        <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                            <i class="fas fa-boxes text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-emerald-100 text-sm font-medium">Stock Total</p>
                            <p class="text-3xl font-bold mt-1">{{ number_format($inventarios->sum('stock'), 0) }}</p>
                        </div>
                        <div class="bg-emerald-400 bg-opacity-30 rounded-full p-3">
                            <i class="fas fa-warehouse text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-amber-100 text-sm font-medium">Stock Bajo</p>
                            <p class="text-3xl font-bold mt-1">{{ $inventarios->where('stock', '<=', 10)->count() }}</p>
                        </div>
                        <div class="bg-amber-400 bg-opacity-30 rounded-full p-3">
                            <i class="fas fa-exclamation-triangle text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">Valor Inventario</p>
                            <p class="text-3xl font-bold mt-1">L {{ number_format($inventarios->sum(function($inv) { return $inv->precio * $inv->stock; }), 0) }}</p>
                        </div>
                        <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                            <i class="fas fa-dollar-sign text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filtros de Búsqueda --}}
            <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-850 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 mb-6 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-3">
                    <h3 class="text-white font-semibold flex items-center">
                        <i class="fas fa-sliders-h mr-2"></i>
                        Filtros y Búsqueda
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="relative">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-search mr-1 text-indigo-600 dark:text-indigo-400"></i> Buscar Producto
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       id="searchInput"
                                       placeholder="Nombre o código..."
                                       class="w-full pl-10 pr-4 py-2.5 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                            </div>
                        </div>

                        <div class="relative">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-filter mr-1 text-purple-600 dark:text-purple-400"></i> Filtrar por Stock
                            </label>
                            <div class="relative">
                                <select id="stockFilter" 
                                        class="w-full appearance-none pl-10 pr-10 py-2.5 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 shadow-sm cursor-pointer">
                                    <option value="all">Todos los productos</option>
                                    <option value="low">Stock Bajo (≤10)</option>
                                    <option value="normal">Stock Normal (>10)</option>
                                    <option value="out">Sin Stock (0)</option>
                                </select>
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-boxes text-gray-400"></i>
                                </div>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                        </div>

                        <div class="relative">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-sort mr-1 text-emerald-600 dark:text-emerald-400"></i> Ordenar por
                            </label>
                            <div class="relative">
                                <select id="sortFilter"
                                        class="w-full appearance-none pl-10 pr-10 py-2.5 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 shadow-sm cursor-pointer">
                                    <option value="name">Nombre (A-Z)</option>
                                    <option value="stock-asc">Stock (Menor primero)</option>
                                    <option value="stock-desc">Stock (Mayor primero)</option>
                                    <option value="price-asc">Precio (Menor primero)</option>
                                    <option value="price-desc">Precio (Mayor primero)</option>
                                </select>
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-sort-amount-down text-gray-400"></i>
                                </div>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Botón para limpiar filtros --}}
                    <div class="mt-4 flex justify-end">
                        <button id="clearFilters" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-all duration-200 shadow-sm">
                            <i class="fas fa-redo mr-2"></i>
                            Limpiar Filtros
                        </button>
                    </div>
                </div>
            </div>
            
            {{-- Tabla de Inventario --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-600">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 flex items-center">
                            <i class="fas fa-clipboard-list mr-2 text-indigo-600 dark:text-indigo-400"></i>
                            Productos en Inventario
                        </h3>
                        <span class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 text-sm font-bold rounded-full">
                            <span id="resultCount">{{ $inventarios->count() }}</span> productos
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-box mr-1"></i> Producto
                                </th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-tag mr-1"></i> Precio
                                </th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-cubes mr-1"></i> Stock
                                </th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-calculator mr-1"></i> Valor Total
                                </th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-cog mr-1"></i> Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody id="inventoryTable" class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($inventarios as $inventario)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150" 
                                    data-name="{{ strtolower($inventario->marca->producto->nombre ?? '') }}"
                                    data-code="{{ strtolower($inventario->marca->codigo_marca ?? '') }}"
                                    data-stock="{{ $inventario->stock }}"
                                    data-price="{{ $inventario->precio }}">
                                    <td class="px-6 py-4 text-sm">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center mr-3">
                                                <i class="fas fa-cube text-indigo-600 dark:text-indigo-400"></i>
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-900 dark:text-white">
                                                    {{ $inventario->marca->producto->nombre ?? 'N/A' }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700">
                                                        <i class="fas fa-barcode mr-1 text-xs"></i>
                                                        {{ $inventario->marca->codigo_marca }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-lg font-bold text-emerald-600 dark:text-emerald-400">
                                            L {{ number_format($inventario->precio, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($inventario->stock == 0)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                <i class="fas fa-times-circle mr-1"></i> 0
                                            </span>
                                        @elseif($inventario->stock <= 10)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200">
                                                <i class="fas fa-exclamation-triangle mr-1"></i> {{ number_format($inventario->stock, 0) }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                                                <i class="fas fa-check-circle mr-1"></i> {{ number_format($inventario->stock, 0) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-base font-bold text-gray-900 dark:text-white">
                                            L {{ number_format($inventario->precio * $inventario->stock, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <a href="{{ route('inventarios.edit', ['inventario' => $inventario->id, 'redirect_to' => 'explorar', 'empresa_id' => $empresa->id, 'tienda_id' => $tienda->id]) }}" 
                                           class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-all duration-200 shadow-md hover:shadow-lg group" 
                                           title="Ajustar Precio/Stock">
                                            <i class="fas fa-edit mr-2 group-hover:rotate-12 transition-transform"></i> 
                                            Ajustar
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr id="emptyState">
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
                                            <i class="fas fa-inbox text-6xl mb-4"></i>
                                            <p class="text-lg font-semibold mb-1">No hay productos en inventario</p>
                                            <p class="text-sm">Esta empresa no tiene registros de inventario en esta tienda</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Estado de No Resultados (oculto por defecto) --}}
                <div id="noResults" class="hidden px-6 py-16 text-center">
                    <div class="flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
                        <i class="fas fa-search-minus text-6xl mb-4"></i>
                        <p class="text-lg font-semibold mb-1">No se encontraron productos</p>
                        <p class="text-sm">Intenta con otros criterios de búsqueda</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript para Filtros en Tiempo Real --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const stockFilter = document.getElementById('stockFilter');
            const sortFilter = document.getElementById('sortFilter');
            const table = document.getElementById('inventoryTable');
            const resultCount = document.getElementById('resultCount');
            const noResults = document.getElementById('noResults');
            
            function filterAndSort() {
                const searchTerm = searchInput.value.toLowerCase();
                const stockValue = stockFilter.value;
                const sortValue = sortFilter.value;
                
                let rows = Array.from(table.querySelectorAll('tr[data-name]'));
                let visibleCount = 0;
                
                // Filtrar
                rows.forEach(row => {
                    const name = row.dataset.name;
                    const code = row.dataset.code;
                    const stock = parseInt(row.dataset.stock);
                    
                    let matchesSearch = name.includes(searchTerm) || code.includes(searchTerm);
                    let matchesStock = true;
                    
                    if (stockValue === 'low') matchesStock = stock <= 10 && stock > 0;
                    if (stockValue === 'normal') matchesStock = stock > 10;
                    if (stockValue === 'out') matchesStock = stock === 0;
                    
                    if (matchesSearch && matchesStock) {
                        row.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        row.classList.add('hidden');
                    }
                });
                
                // Ordenar
                rows.sort((a, b) => {
                    if (sortValue === 'name') {
                        return a.dataset.name.localeCompare(b.dataset.name);
                    } else if (sortValue === 'stock-asc') {
                        return parseInt(a.dataset.stock) - parseInt(b.dataset.stock);
                    } else if (sortValue === 'stock-desc') {
                        return parseInt(b.dataset.stock) - parseInt(a.dataset.stock);
                    } else if (sortValue === 'price-asc') {
                        return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                    } else if (sortValue === 'price-desc') {
                        return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                    }
                });
                
                rows.forEach(row => table.appendChild(row));
                
                // Actualizar contador y mostrar/ocultar mensaje
                resultCount.textContent = visibleCount;
                
                if (visibleCount === 0) {
                    table.classList.add('hidden');
                    noResults.classList.remove('hidden');
                } else {
                    table.classList.remove('hidden');
                    noResults.classList.add('hidden');
                }
            }
            
            searchInput.addEventListener('input', filterAndSort);
            stockFilter.addEventListener('change', filterAndSort);
            sortFilter.addEventListener('change', filterAndSort);
        });
    </script>
</x-app-layout>