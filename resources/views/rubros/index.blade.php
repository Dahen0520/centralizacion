<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Gestión de Rubros') }}
        </h2>
    </x-slot>

    {{-- INCLUSIÓN DE SWEETALERT2 PARA NOTIFICACIONES --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-12">
        <div class="w-full max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 
                        rounded-2xl shadow-3xl p-8 lg:p-10 
                        border-t-4 border-blue-600 dark:border-blue-500">
                
                {{-- HEADER DE ACCIONES Y BÚSQUEDA --}}
                <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                    
                    {{-- Botón Crear (Estilo Elegante) --}}
                    <a href="{{ route('rubros.create') }}" 
                       class="w-full md:w-auto inline-flex items-center justify-center px-8 py-3 
                              bg-gradient-to-r from-blue-600 to-indigo-700 text-white 
                              font-bold text-sm uppercase tracking-wider rounded-xl shadow-lg 
                              hover:shadow-xl hover:from-blue-700 hover:to-indigo-800 
                              focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 
                              transition duration-300 transform hover:scale-[1.02]">
                        <i class="fas fa-plus-circle mr-2 text-lg"></i> Crear Nuevo Rubro
                    </a>

                    {{-- Barra de búsqueda (con icono y estilo refinado) --}}
                    <div class="relative w-full md:w-1/2">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400"></i>
                        <input type="text" id="search-input" placeholder="Buscar rubros por ID o Nombre..."
                               class="pl-12 py-3.5 shadow-md border border-gray-300 dark:border-gray-600 rounded-xl w-full 
                                      text-gray-900 dark:text-gray-100 dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                    </div>
                </div>

                {{-- Contenedor de la tabla y estado vacío --}}
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-xl">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-1/4">
                                    <i class="fas fa-hashtag mr-1"></i> ID
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-1/2">
                                    <i class="fas fa-industry mr-1"></i> Nombre
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-1/4">
                                    <i class="fas fa-cogs mr-1"></i> Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody id="rubros-table-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                            {{-- Contenido de rubros.partials.rubros_table_rows --}}
                            @include('rubros.partials.rubros_table_rows')
                        </tbody>
                    </table>
                    
                    {{-- Placeholder para cuando no hay resultados (Estilo mejorado) --}}
                    <div id="no-results-message" class="hidden p-10 text-center bg-white dark:bg-gray-800">
                        <i class="fas fa-box-open text-5xl text-blue-400 dark:text-blue-600 mb-3"></i>
                        <p class="font-extrabold text-xl text-gray-900 dark:text-white">¡Vaya! No hay coincidencias.</p>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">Intenta cambiar los términos de búsqueda o revisa la ortografía.</p>
                    </div>
                </div>

                {{-- Enlaces de paginación (Estilo refinado) --}}
                <div id="pagination-links" class="mt-8">
                    {{ $rubros->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPTS DE SWEETALERT2 Y LÓGICA AJAX --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('search-input');
            const rubrosTableBody = document.getElementById('rubros-table-body');
            const paginationLinksContainer = document.getElementById('pagination-links');
            const noResultsMessage = document.getElementById('no-results-message');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            let searchTimeout;

            // --- 1. MANEJO DE NOTIFICACIONES DE SESIÓN (SWEETALERT2) ---
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: '¡Operación Exitosa!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    toast: true,
                    position: 'top-end'
                });
            @endif


            // --- 2. MANEJO DE ELIMINACIÓN (SWEETALERT2) ---
            function handleDeleteClick(e) {
                e.preventDefault();
                const deleteButton = this;
                const form = deleteButton.closest('form');
                const rubroName = deleteButton.getAttribute('data-name');

                Swal.fire({
                    title: '¿Eliminar ' + rubroName + '?',
                    text: '¡Esta acción es irreversible! Se eliminarán todos los datos asociados.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444', // Red-500
                    cancelButtonColor: '#6B7280', // Gray
                    confirmButtonText: 'Sí, ¡Eliminar!',
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
                                return response.json().then(err => { throw new Error(err.message || 'Error al eliminar el rubro'); });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                Swal.fire('¡Eliminado!', data.message, 'success');
                                fetchRubros(getCurrentPage()); 
                            } else {
                                Swal.fire('Error', data.message || 'No se pudo eliminar el rubro.', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error en fetch handleDeleteClick:', error);
                            Swal.fire('Error', 'Ocurrió un error inesperado al eliminar: ' + error.message, 'error');
                        });
                    }
                });
            }

            // --- 3. LÓGICA DE BÚSQUEDA Y PAGINACIÓN AJAX ---

            function attachDeleteListeners() {
                document.querySelectorAll('.delete-btn').forEach(button => {
                    const newButton = button.cloneNode(true);
                    button.parentNode.replaceChild(newButton, button);
                    newButton.addEventListener('click', handleDeleteClick);
                });
            }

            function attachPaginationListeners() {
                paginationLinksContainer.querySelectorAll('a').forEach(link => {
                    const newLink = link.cloneNode(true);
                    link.parentNode.replaceChild(newLink, link);
                    newLink.addEventListener('click', function(e) {
                        e.preventDefault();
                        const url = new URL(this.href);
                        const page = url.searchParams.get('page');
                        window.history.pushState({}, '', `{{ route('rubros.index') }}?page=${page}&search=${encodeURIComponent(searchInput.value)}`);
                        fetchRubros(page);
                    });
                });
            }

            function fetchRubros(page = 1) {
                const query = searchInput.value;
                const url = `{{ route('rubros.index') }}?page=${page}&search=${encodeURIComponent(query)}`;

                // Muestra el estado de carga
                rubrosTableBody.innerHTML = '<tr><td colspan="3" class="p-6 text-center text-blue-500 dark:text-blue-400"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando rubros...</td></tr>';
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
                    rubrosTableBody.innerHTML = data.table_rows;
                    paginationLinksContainer.innerHTML = data.pagination_links;

                    // Muestra/oculta el mensaje de "no resultados"
                    if (data.rubros_count === 0) {
                        noResultsMessage.classList.remove('hidden');
                        rubrosTableBody.innerHTML = '';
                    } else {
                        noResultsMessage.classList.add('hidden');
                    }

                    attachDeleteListeners();
                    attachPaginationListeners();
                })
                .catch(error => {
                    console.error('Error al buscar rubros:', error);
                    rubrosTableBody.innerHTML = '<tr><td colspan="3" class="p-6 text-center text-red-500 dark:text-red-400"><i class="fas fa-exclamation-triangle mr-2"></i> Error al cargar.</td></tr>';
                    Swal.fire('Error de Carga', 'No se pudieron cargar los rubros. Inténtalo de nuevo. Detalles: ' + error.message, 'error');
                });
            }

            function getCurrentPage() {
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get('page') || 1;
            }

            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    fetchRubros(1);
                }, 300);
            });

            // Adjuntar listeners iniciales
            attachDeleteListeners();
            attachPaginationListeners();
            
            // Chequeo inicial de si la tabla está vacía (en caso de que la búsqueda inicial no devuelva resultados)
            if (rubrosTableBody.children.length === 0 && !searchInput.value) {
                noResultsMessage.classList.remove('hidden');
            }
        });
    </script>
</x-app-layout>