<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Gestión de Productos') }}
        </h2>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-12">
        <div class="w-full max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 
                        rounded-2xl shadow-3xl p-8 lg:p-10 
                        border-t-4 border-blue-600 dark:border-blue-500">
                
                {{-- HEADER DE ACCIONES, BÚSQUEDA Y FILTROS ELEGANTES --}}
                <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                    
                    {{-- Botón Crear --}}
                    <a href="{{ route('productos.create') }}" 
                       class="w-full md:w-auto inline-flex items-center justify-center px-8 py-3 
                              bg-gradient-to-r from-blue-600 to-indigo-700 text-white 
                              font-bold text-sm uppercase tracking-wider rounded-xl shadow-lg 
                              hover:shadow-xl hover:from-blue-700 hover:to-indigo-800 
                              focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 
                              transition duration-300 transform hover:scale-[1.02]">
                        <i class="fas fa-plus-circle mr-2 text-lg"></i> Crear Producto
                    </a>

                    {{-- Barra de búsqueda y filtros (Horizontal y estilizados) --}}
                    <div class="flex flex-col md:flex-row gap-3 w-full md:w-3/4 lg:w-2/3">
                        
                        {{-- Input de Búsqueda --}}
                        <div class="relative flex-1 min-w-[200px]">
                            <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400"></i>
                            <input type="text" id="search-input" placeholder="Buscar por nombre o descripción..."
                                   class="pl-12 py-3 border border-gray-300 dark:border-gray-600 rounded-xl w-full text-gray-900 dark:text-gray-100 dark:bg-gray-700 placeholder-gray-500 focus:ring-blue-500 focus:border-blue-500 transition duration-150 shadow-md">
                        </div>

                        {{-- Filtro de Categoría (Select estilizado) --}}
                        <select id="categoria-filter"
                                class="w-full md:w-auto px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-md text-gray-700 dark:text-gray-200 dark:bg-gray-700 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                            <option value="">-- Categoría --</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                            @endforeach
                        </select>

                        {{-- Filtro de Estado (Select estilizado) --}}
                        <select id="estado-filter"
                                class="w-full md:w-auto px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-md text-gray-700 dark:text-gray-200 dark:bg-gray-700 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                            <option value="">-- Estado --</option>
                            @foreach ($estados as $estado)
                                <option value="{{ $estado }}">{{ ucfirst($estado) }}</option>
                            @endforeach
                        </select>
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
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-24">
                                    <i class="fas fa-check-circle mr-1"></i> Estado
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
                    
                    {{-- Mensaje de No Resultados --}}
                    <div id="no-results-message" class="hidden p-10 text-center bg-white dark:bg-gray-800">
                        <i class="fas fa-box-open text-5xl text-blue-400 dark:text-blue-600 mb-3"></i>
                        <p class="font-extrabold text-xl text-gray-900 dark:text-white">¡Vaya! No se encontraron productos.</p>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">Ajusta los filtros o los términos de búsqueda.</p>
                    </div>
                </div>

                {{-- Paginación --}}
                <div id="pagination-links" class="mt-8">
                    {{ $productos->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- 1. CONFIGURACIÓN Y SWEETALERT2 PARA NOTIFICACIONES ---

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

            const searchInput = document.getElementById('search-input');
            const categoriaFilter = document.getElementById('categoria-filter');
            const estadoFilter = document.getElementById('estado-filter');
            const productosTableBody = document.getElementById('productos-table-body');
            const paginationLinksContainer = document.getElementById('pagination-links');

            let searchTimeout;
            const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';


            // --- 2. MANEJO DE ELIMINACIÓN (SWEETALERT2) ---
            function handleDeleteClick(e) {
                e.preventDefault();
                const form = this.closest('form');
                const productName = this.getAttribute('data-name') || 'este producto';

                Swal.fire({
                    title: '¿Eliminar ' + productName + '?',
                    text: '¡Esta acción es irreversible!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444', 
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Sí, ¡Eliminar!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Nota: El formulario original usa method='POST', por eso se mantiene POST aquí,
                        // pero se recomienda usar DELETE si el router lo permite.
                        fetch(form.action, {
                            method: 'POST',
                            body: new FormData(form),
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => { throw new Error(err.message || 'Error al eliminar el producto'); });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                Swal.fire('¡Eliminado!', data.message, 'success');
                                fetchProductos(getCurrentPage());
                            } else {
                                Swal.fire('Error', data.message || 'No se pudo eliminar el producto.', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error en fetch handleDeleteClick:', error);
                            Swal.fire('Error', 'Ocurrió un error inesperado al eliminar: ' + error.message, 'error');
                        });
                    }
                });
            }

            // --- 3. LÓGICA AJAX PARA BÚSQUEDA Y FILTRADO ---

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
                fetchProductos(page);
            }

            function attachPaginationListeners() {
                const links = paginationLinksContainer.querySelectorAll('a');
                links.forEach(link => {
                    const newLink = link.cloneNode(true);
                    link.parentNode.replaceChild(newLink, link);
                    newLink.addEventListener('click', handlePaginationClick);
                });
            }

            function fetchProductos(page = 1) {
                const query = searchInput.value;
                const categoria = categoriaFilter.value;
                const estado = estadoFilter.value;

                // Construcción de la URL de búsqueda/filtrado
                const url = `{{ route('productos.index') }}?page=${page}&search=${encodeURIComponent(query)}&categoria=${categoria}&estado=${estado}`;

                // Mostrar estado de carga
                productosTableBody.innerHTML = '<tr><td colspan="7" class="p-6 text-center text-blue-500 dark:text-blue-400"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando productos...</td></tr>';
                document.getElementById('no-results-message')?.classList.add('hidden'); // Ocultar mensaje si existe

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
                    // Actualizar URL del navegador para persistencia de filtros/búsqueda
                    window.history.pushState({}, '', url);

                    productosTableBody.innerHTML = data.table_rows;
                    paginationLinksContainer.innerHTML = data.pagination_links;
                    
                    // Manejar el mensaje de no resultados (si el controlador devuelve el conteo)
                    if (data.productos_count === 0) {
                        document.getElementById('no-results-message')?.classList.remove('hidden');
                    } else {
                        document.getElementById('no-results-message')?.classList.add('hidden');
                    }
                    
                    attachDeleteListeners();
                    attachPaginationListeners();
                })
                .catch(error => {
                    console.error('Error al buscar productos:', error);
                    productosTableBody.innerHTML = '<tr><td colspan="7" class="p-6 text-center text-red-500 dark:text-red-400"><i class="fas fa-exclamation-triangle mr-2"></i> Error al cargar.</td></tr>';
                    Swal.fire('Error de Carga', 'No se pudieron cargar los productos. Detalles: ' + error.message, 'error');
                });
            }

            function getCurrentPage() {
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get('page') || 1;
            }

            // --- 4. ASIGNACIÓN DE EVENTOS ---
            
            // Eventos que disparan la búsqueda a la página 1
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    fetchProductos(1); 
                }, 300);
            });

            categoriaFilter.addEventListener('change', function() {
                fetchProductos(1);
            });

            estadoFilter.addEventListener('change', function() {
                fetchProductos(1);
            });

            // Adjuntar listeners iniciales cuando la página se carga
            attachDeleteListeners();
            attachPaginationListeners();
        });
    </script>
</x-app-layout>