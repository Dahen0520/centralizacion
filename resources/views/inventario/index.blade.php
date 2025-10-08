<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Gestión de Inventario (Precio y Stock)') }}
        </h2>
    </x-slot>

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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- 1. CONFIGURACIÓN ---
            const searchInput = document.getElementById('search-input');
            const tiendaFilter = document.getElementById('tienda-filter');
            const inventarioTableBody = document.getElementById('inventario-table-body');
            const paginationLinksContainer = document.getElementById('pagination-links');

            let searchTimeout;
            
            const successMessage = '{{ session('success') }}';
            if (successMessage) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Operación Exitosa!',
                    text: successMessage,
                    showConfirmButton: false,
                    timer: 3000,
                    toast: true,
                    position: 'top-end'
                });
            }

            // --- 2. MANEJO DE ELIMINACIÓN (SWEETALERT2) ---
            function handleDeleteClick(e) {
                e.preventDefault();
                const form = this.closest('form');
                const itemName = this.getAttribute('data-name') || 'este registro de inventario';

                Swal.fire({
                    title: '¿Eliminar ' + itemName + '?',
                    text: '¡Esta acción es irreversible!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444', 
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Sí, ¡Eliminar!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(form.action, {
                            method: 'DELETE', 
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => { throw new Error(err.message || 'Error al eliminar'); });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                Swal.fire('¡Eliminado!', data.message, 'success');
                                fetchInventarios(getCurrentPage());
                            } else {
                                Swal.fire('Error', data.message || 'No se pudo eliminar el registro.', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error en fetch handleDeleteClick:', error);
                            Swal.fire('Error', 'Ocurrió un error inesperado al eliminar: ' + error.message, 'error');
                        });
                    }
                });
            }

            // --- 3. LÓGICA AJAX PARA BÚSQUEDA, FILTRADO Y PAGINACIÓN ---

            function attachDeleteListeners() {
                const deleteButtons = document.querySelectorAll('.delete-btn');
                deleteButtons.forEach(button => {
                    const newButton = button.cloneNode(true);
                    button.parentNode.replaceChild(newButton, button);
                    newButton.addEventListener('click', handleDeleteClick);
                });
            }

            function handlePaginationClick(e) {
                e.preventDefault();
                const url = new URL(this.href);
                const page = url.searchParams.get('page');
                fetchInventarios(page);
            }

            function attachPaginationListeners() {
                const links = paginationLinksContainer.querySelectorAll('a');
                links.forEach(link => {
                    const newLink = link.cloneNode(true);
                    link.parentNode.replaceChild(newLink, link);
                    newLink.addEventListener('click', handlePaginationClick);
                });
            }

            function fetchInventarios(page = 1) {
                const query = searchInput.value;
                const tiendaId = tiendaFilter.value;

                // Construcción de la URL de búsqueda/filtrado
                const url = `{{ route('inventarios.index') }}?page=${page}&search=${encodeURIComponent(query)}&tienda_id=${tiendaId}`;

                // Mostrar estado de carga (COLSPAN CORREGIDO A 7)
                inventarioTableBody.innerHTML = '<tr><td colspan="7" class="p-6 text-center text-emerald-500 dark:text-emerald-400"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando inventario...</td></tr>';
                document.getElementById('no-results-message')?.classList.add('hidden');

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('La respuesta de la red no fue correcta: ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    window.history.pushState({}, '', url);

                    inventarioTableBody.innerHTML = data.table_rows;
                    paginationLinksContainer.innerHTML = data.pagination_links;
                    
                    const totalItems = parseInt(data.inventarios_count);
                    const noResultsMessage = document.getElementById('no-results-message');

                    if (totalItems === 0) {
                        noResultsMessage?.classList.remove('hidden');
                    } else {
                        noResultsMessage?.classList.add('hidden');
                    }
                    
                    attachDeleteListeners();
                    attachPaginationListeners();
                })
                .catch(error => {
                    console.error('Error al buscar inventario:', error);
                    // Mensaje de error (COLSPAN CORREGIDO A 7)
                    inventarioTableBody.innerHTML = '<tr><td colspan="7" class="p-6 text-center text-red-500 dark:text-red-400"><i class="fas fa-exclamation-triangle mr-2"></i> Error al cargar.</td></tr>';
                    Swal.fire('Error de Carga', 'No se pudieron cargar los registros de inventario. Detalles: ' + error.message, 'error');
                });
            }

            function getCurrentPage() {
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get('page') || 1;
            }

            // --- 4. ASIGNACIÓN DE EVENTOS ---
            
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    fetchInventarios(1); 
                }, 300);
            });

            tiendaFilter.addEventListener('change', function () {
                fetchInventarios(1);
            });

            // Adjuntar listeners iniciales cuando la página se carga
            attachDeleteListeners();
            attachPaginationListeners();
        });
    </script>
</x-app-layout>