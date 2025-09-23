<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Lista de Productos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-8 border-t-8 border-blue-600">
                <div class="flex justify-between items-center mb-6">
                    <a href="{{ route('productos.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-plus mr-2"></i> Crear Producto
                    </a>
                </div>

                {{-- Barra de búsqueda y filtros --}}
                <div class="flex flex-col sm:flex-row gap-4 mb-4">
                    <input type="text" id="search-input" placeholder="Buscar productos por nombre o descripción..." class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline">

                    <select id="categoria-filter" class="shadow appearance-none border rounded w-full sm:w-1/3 py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">Filtrar por Categoría</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>

                    <select id="estado-filter" class="shadow appearance-none border rounded w-full sm:w-1/3 py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">Filtrar por Estado</option>
                        @foreach ($estados as $estado)
                            <option value="{{ $estado }}">{{ ucfirst($estado) }}</option>
                        @endforeach
                    </select>
                </div>

                @if (session('success'))
                    <div id="success-alert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                        <strong class="font-bold">¡Éxito!</strong>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nombre</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subcategoría</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Categoría</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="productos-table-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @include('productos.partials.productos_table_rows')
                        </tbody>
                    </table>
                </div>

                {{-- Enlaces de paginación --}}
                <div id="pagination-links" class="mt-4">
                    {{ $productos->links() }}
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Oculta la alerta de éxito después de 3 segundos
            const successAlert = document.getElementById('success-alert');
            if (successAlert) {
                setTimeout(function() {
                    successAlert.style.display = 'none';
                }, 3000);
            }

            const searchInput = document.getElementById('search-input');
            const categoriaFilter = document.getElementById('categoria-filter');
            const estadoFilter = document.getElementById('estado-filter');
            const productosTableBody = document.getElementById('productos-table-body');
            const paginationLinksContainer = document.getElementById('pagination-links');

            let searchTimeout;

            // Función para manejar el clic en el botón de eliminar
            function handleDeleteClick(e) {
                e.preventDefault();
                const form = this.closest('form');

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: '¡No podrás revertir esto!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminarlo',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(form.action, {
                            method: 'POST',
                            body: new FormData(form),
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
                                Swal.fire(
                                    '¡Eliminado!',
                                    data.message,
                                    'success'
                                );
                                fetchProductos(getCurrentPage());
                            } else {
                                Swal.fire(
                                    'Error',
                                    data.message || 'No se pudo eliminar el producto.',
                                    'error'
                                );
                            }
                        })
                        .catch(error => {
                            console.error('Error en fetch handleDeleteClick:', error);
                            Swal.fire(
                                'Error',
                                'Ocurrió un error inesperado al eliminar: ' + error.message,
                                'error'
                            );
                        });
                    }
                });
            }

            // Función para adjuntar listeners de eliminación a los botones
            function attachDeleteListeners() {
                const deleteButtons = document.querySelectorAll('.delete-btn');
                deleteButtons.forEach(button => {
                    // Clona el nodo para eliminar todos los listeners existentes y luego reemplázalo
                    const newButton = button.cloneNode(true);
                    button.parentNode.replaceChild(newButton, button);
                    newButton.addEventListener('click', handleDeleteClick);
                });
            }

            // Función para manejar el clic en los enlaces de paginación
            function handlePaginationClick(e) {
                e.preventDefault();
                const url = new URL(this.href);
                const page = url.searchParams.get('page');
                fetchProductos(page);
            }

            // Función para adjuntar listeners de paginación a los enlaces
            function attachPaginationListeners() {
                const links = paginationLinksContainer.querySelectorAll('a');
                links.forEach(link => {
                    // Clona el nodo para eliminar todos los listeners existentes y luego reemplázalo
                    const newLink = link.cloneNode(true);
                    link.parentNode.replaceChild(newLink, link);
                    newLink.addEventListener('click', handlePaginationClick);
                });
            }

            // Función principal para obtener y mostrar los productos
            function fetchProductos(page = 1) {
                const query = searchInput.value;
                const categoria = categoriaFilter.value;
                const estado = estadoFilter.value;

                const url = `{{ route('productos.index') }}?page=${page}&search=${encodeURIComponent(query)}&categoria=${categoria}&estado=${estado}`;

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
                    productosTableBody.innerHTML = data.table_rows;
                    paginationLinksContainer.innerHTML = data.pagination_links;
                    attachDeleteListeners();
                    attachPaginationListeners();
                })
                .catch(error => {
                    console.error('Error al buscar productos:', error);
                    Swal.fire(
                        'Error de Carga',
                        'No se pudieron cargar los municipios. Inténtalo de nuevo. Detalles: ' + error.message,
                        'error'
                    );
                });
            }

            // Función para obtener la página actual de la URL
            function getCurrentPage() {
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get('page') || 1;
            }

            // Event listeners para los filtros y búsqueda
            searchInput.addEventListener('keyup', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    fetchProductos(1); // Siempre ir a la primera página al buscar
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
