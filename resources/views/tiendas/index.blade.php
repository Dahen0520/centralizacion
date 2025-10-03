<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Gestión de Tiendas') }}
        </h2>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-12">
        <div class="w-full max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 
                        rounded-2xl shadow-3xl p-8 lg:p-10 
                        border-t-4 border-blue-600 dark:border-blue-500">

                {{-- HEADER DE ACCIONES Y BÚSQUEDA ESTILIZADA --}}
                <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                    
                    {{-- Botón Crear --}}
                    <a href="{{ route('tiendas.create') }}" 
                       class="w-full md:w-auto inline-flex items-center justify-center px-8 py-3 
                              bg-gradient-to-r from-blue-600 to-indigo-700 text-white 
                              font-bold text-sm uppercase tracking-wider rounded-xl shadow-lg 
                              hover:shadow-xl hover:from-blue-700 hover:to-indigo-800 
                              focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 
                              transition duration-300 transform hover:scale-[1.02]">
                        <i class="fas fa-plus-circle mr-2 text-lg"></i> Crear Tienda
                    </a>

                    {{-- Barra de búsqueda (Estilo compacto y elegante) --}}
                    <div class="relative w-full md:w-1/2">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400"></i>
                        <input type="text" id="search-input" placeholder="Buscar tiendas por nombre..."
                               class="pl-12 py-3.5 shadow-md border border-gray-300 dark:border-gray-600 rounded-xl w-full 
                                      text-gray-900 dark:text-gray-100 dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
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
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-1/4">
                                    <i class="fas fa-fingerprint mr-1"></i> ID
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-1/4">
                                    <i class="fas fa-store mr-1"></i> Nombre de la Tienda
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-1/4">
                                    <i class="fas fa-city mr-1"></i> Municipio
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-1/4">
                                    <i class="fas fa-cogs mr-1"></i> Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody id="tiendas-table-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                            {{-- Las tiendas se cargarán aquí --}}
                            @include('tiendas.partials.tiendas_table_rows')
                        </tbody>
                    </table>
                    
                    {{-- Mensaje de No Resultados --}}
                    <div id="no-results-message" class="hidden p-10 text-center bg-white dark:bg-gray-800">
                        <i class="fas fa-store-slash text-5xl text-blue-400 dark:text-blue-600 mb-3"></i>
                        <p class="font-extrabold text-xl text-gray-900 dark:text-white">¡Vaya! No se encontraron tiendas.</p>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">Intenta ajustar tu búsqueda o crea una nueva.</p>
                    </div>
                </div>

                {{-- Paginación --}}
                <div id="pagination-links" class="mt-8">
                    {{ $tiendas->links() }}
                </div>
            </div>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        
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
        const tiendasTableBody = document.getElementById('tiendas-table-body');
        const paginationLinksContainer = document.getElementById('pagination-links');
        const noResultsMessage = document.getElementById('no-results-message');

        let searchTimeout;
        const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';


        // Función para manejar el clic en el botón de eliminar
        function handleDeleteClick(e) {
            e.preventDefault();
            const form = this.closest('form');
            const tiendaName = this.getAttribute('data-name') || 'esta tienda';

            Swal.fire({
                title: '¿Eliminar ' + tiendaName + '?',
                text: '¡No podrás revertir esto!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444', 
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Sí, eliminarla',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(form.action, {
                        method: 'DELETE',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json'
                        },
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw new Error(err.message || 'Error al eliminar la tienda'); });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire(
                                '¡Eliminada!',
                                data.message,
                                'success'
                            );
                            fetchTiendas(getCurrentPage());
                        } else {
                            Swal.fire(
                                'Error',
                                data.message || 'No se pudo eliminar la tienda.',
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
                // Se clona el nodo para asegurar la eliminación de listeners anteriores si la tabla se actualiza
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
            
            // Actualizar la URL y cargar datos con AJAX
            const newUrl = `{{ route('tiendas.index') }}?page=${page}&search=${encodeURIComponent(searchInput.value)}`;
            window.history.pushState({}, '', newUrl);
            fetchTiendas(page);
        }

        // Función para adjuntar listeners de paginación a los enlaces
        function attachPaginationListeners() {
            const links = paginationLinksContainer.querySelectorAll('a');
            links.forEach(link => {
                const newLink = link.cloneNode(true);
                link.parentNode.replaceChild(newLink, link);
                newLink.addEventListener('click', handlePaginationClick);
            });
        }

        // Función principal para obtener y mostrar las tiendas
        function fetchTiendas(page = 1) {
            const query = searchInput.value;
            const url = `{{ route('tiendas.index') }}?page=${page}&search=${encodeURIComponent(query)}`;

            // Muestra estado de carga
            tiendasTableBody.innerHTML = '<tr><td colspan="5" class="p-6 text-center text-blue-500 dark:text-blue-400"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando tiendas...</td></tr>';
            noResultsMessage.classList.add('hidden');

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
                tiendasTableBody.innerHTML = data.table_rows;
                paginationLinksContainer.innerHTML = data.pagination_links;

                // Manejar el mensaje de no resultados (asumiendo que el controlador devuelve el conteo)
                if (data.tiendas_count === 0) {
                    noResultsMessage.classList.remove('hidden');
                } else {
                    noResultsMessage.classList.add('hidden');
                }

                attachDeleteListeners();
                attachPaginationListeners();
            })
            .catch(error => {
                console.error('Error al buscar tiendas:', error);
                tiendasTableBody.innerHTML = '<tr><td colspan="5" class="p-6 text-center text-red-500 dark:text-red-400"><i class="fas fa-exclamation-triangle mr-2"></i> Error al cargar.</td></tr>';
                Swal.fire('Error de Carga', 'No se pudieron cargar las tiendas. Detalles: ' + error.message, 'error');
            });
        }

        // Función para obtener la página actual de la URL
        function getCurrentPage() {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get('page') || 1;
        }

        // Event listener para la barra de búsqueda (con debounce)
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                fetchTiendas(1);
            }, 300);
        });

        // Adjuntar listeners iniciales cuando la página se carga
        attachDeleteListeners();
        attachPaginationListeners();
    });
</script>
</x-app-layout>