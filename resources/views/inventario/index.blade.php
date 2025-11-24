<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Gestión de Inventario (Precio y Stock)') }}
        </h2>
    </x-slot>

    {{-- Script de SweetAlert2 (CDN) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-12">
        <div class="w-full max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 
                        rounded-2xl shadow-3xl p-8 lg:p-10 
                        border-t-4 border-emerald-600 dark:border-emerald-500">
                
                {{-- HEADER DE ACCIONES, BÚSQUEDA Y FILTROS --}}
                <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                    
                    {{-- Botón Crear --}}
                    <a href="{{ route('inventarios.create') }}" 
                       class="w-full md:w-auto inline-flex items-center justify-center px-8 py-3 
                              bg-gradient-to-r from-emerald-600 to-green-700 text-white 
                              font-bold text-sm uppercase tracking-wider rounded-xl shadow-lg 
                              hover:shadow-xl hover:from-emerald-700 hover:to-green-800 
                              focus:outline-none focus:ring-4 focus:ring-emerald-300 dark:focus:ring-emerald-800 
                              transition duration-300 transform hover:scale-[1.02]">
                        <i class="fas fa-plus-circle mr-2 text-lg"></i> Crear Registro
                    </a>

                    {{-- Barra de búsqueda y Filtro por Tienda --}}
                    <div class="flex flex-col md:flex-row gap-3 w-full md:w-3/4 lg:w-2/3">
                        
                        {{-- Filtro por Tienda --}}
                        <select id="tienda-filter" 
                                class="w-full md:w-1/3 px-5 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-md dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-emerald-500 focus:border-emerald-500 transition">
                            <option value="">Filtrar por Tienda</option>
                            @foreach($tiendas as $tienda)
                                <option value="{{ $tienda->id }}">{{ $tienda->nombre }}</option>
                            @endforeach
                        </select>

                        {{-- Barra de búsqueda --}}
                        <div class="relative flex-1 min-w-[200px]">
                            <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400"></i>
                            <input type="text" id="search-input" placeholder="Buscar por Producto o Tienda..."
                                   class="pl-12 py-3 border border-gray-300 dark:border-gray-600 rounded-xl w-full text-gray-900 dark:text-gray-100 dark:bg-gray-700 placeholder-gray-500 focus:ring-emerald-500 focus:border-emerald-500 transition duration-150 shadow-md">
                        </div>
                    </div>
                </div>

                {{-- CONTENEDOR DE LA TABLA --}}
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-xl">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-16">
                                    #
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-tag mr-1"></i> Marca / Producto
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-building mr-1"></i> Empresa Asociada
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-store mr-1"></i> Tienda
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-money-bill-wave mr-1"></i> Precio (L)
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-cubes mr-1"></i> Stock
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-32">
                                    <i class="fas fa-cogs mr-1"></i> Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody id="inventario-table-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                            @include('inventario.partials.inventario_table_rows') 
                        </tbody>
                    </table>
                    
                    {{-- Mensaje de No Resultados --}}
                    <div id="no-results-message" class="hidden p-10 text-center bg-white dark:bg-gray-800">
                        <i class="fas fa-box-open text-5xl text-emerald-400 dark:text-emerald-600 mb-3"></i>
                        <p class="font-extrabold text-xl text-gray-900 dark:text-white">¡Vaya! No se encontraron registros de inventario.</p>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">Ajusta la búsqueda o el filtro de tienda.</p>
                    </div>
                </div>

                {{-- Paginación --}}
                <div id="pagination-links" class="mt-8">
                    {{ $inventarios->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Bloque para pasar datos de Blade a JavaScript --}}
    <script>
        window.AppConfig = {
            // Variables de entorno y rutas
            inventariosIndexRoute: '{{ route('inventarios.index') }}',
            csrfToken: '{{ csrf_token() }}',
            
            // Variables dinámicas de la vista
            sessionSuccess: '{{ session('success') }}',
            columnCount: 7 
        };
    </script>
    
    {{-- Enlace al archivo JavaScript separado --}}
    <script src="{{ asset('js/inventario/index.js') }}"></script>
</x-app-layout>