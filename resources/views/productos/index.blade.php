<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Gestión de Productos') }}
        </h2>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>


    <div class="py-12">
        <div class="w-full max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 
                        rounded-2xl shadow-3xl p-8 lg:p-10 
                        border-t-4 border-blue-600 dark:border-blue-500">
                
                <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                    
                    <a href="{{ route('productos.create') }}" 
                       class="w-full md:w-auto inline-flex items-center justify-center px-8 py-3 
                              bg-gradient-to-r from-blue-600 to-indigo-700 text-white 
                              font-bold text-sm uppercase tracking-wider rounded-xl shadow-lg 
                              hover:shadow-xl hover:from-blue-700 hover:to-indigo-800 
                              focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 
                              transition duration-300 transform hover:scale-[1.02]">
                        <i class="fas fa-plus-circle mr-2 text-lg"></i> Crear Producto
                    </a>

                    <div class="flex flex-col md:flex-row gap-3 w-full md:w-3/4 lg:w-2/3">
                        
                        <div class="relative flex-1 min-w-[200px]">
                            <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400"></i>
                            <input type="text" id="search-input" placeholder="Buscar por nombre o descripción..."
                                   class="pl-12 py-3 border border-gray-300 dark:border-gray-600 rounded-xl w-full text-gray-900 dark:text-gray-100 dark:bg-gray-700 placeholder-gray-500 focus:ring-blue-500 focus:border-blue-500 transition duration-150 shadow-md">
                        </div>

                        <select id="categoria-filter"
                                class="w-full md:w-auto px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-md text-gray-700 dark:text-gray-200 dark:bg-gray-700 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                            <option value="">-- Categoría --</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                            @endforeach
                        </select>

                        <select id="estado-filter"
                                class="w-full md:w-auto px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-md text-gray-700 dark:text-gray-200 dark:bg-gray-700 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                            <option value="">-- Estado --</option>
                            @foreach ($estados as $estado)
                                <option value="{{ $estado }}">{{ ucfirst($estado) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-xl">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-16">
                                    #
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-fingerprint mr-1"></i> ID
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-tag mr-1"></i> Nombre del Producto
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-sitemap mr-1"></i> Subcategoría
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-boxes mr-1"></i> Categoría
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-32">
                                    <i class="fas fa-balance-scale mr-1"></i> Impuesto
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-24">
                                    <i class="fas fa-percent mr-1"></i> Tasa
                                </th>

                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-32">
                                    <i class="fas fa-cogs mr-1"></i> Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody id="productos-table-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                            @include('productos.partials.productos_table_rows')
                        </tbody>
                    </table>
                    
                    <div id="no-results-message" class="hidden p-10 text-center bg-white dark:bg-gray-800">
                        <i class="fas fa-box-open text-5xl text-blue-400 dark:text-blue-600 mb-3"></i>
                        <p class="font-extrabold text-xl text-gray-900 dark:text-white">¡Vaya! No se encontraron productos.</p>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">Ajusta los filtros o los términos de búsqueda.</p>
                    </div>
                </div>

                <div id="pagination-links" class="mt-8">
                    {{ $productos->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        window.AppConfig = {
            productosIndexRoute: '{{ route('productos.index') }}',
            csrfToken: document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '',
            sessionSuccess: '{{ session('success') }}'
        };
    </script>
    
    <script src="{{ asset('js/productos/index.js') }}"></script>
</x-app-layout>