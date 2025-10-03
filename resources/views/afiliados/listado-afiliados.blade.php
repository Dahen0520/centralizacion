<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Gestión de Afiliados') }}
        </h2>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-12">
        <div class="w-full max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 
                        rounded-2xl shadow-3xl p-8 lg:p-10 
                        border-t-4 border-blue-600 dark:border-blue-500">

                {{-- HEADER DE ACCIONES Y BÚSQUEDA ESTILIZADA --}}
                <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                    
                    {{-- Botón Crear (Ejemplo, si lo necesitas) --}}
                    {{-- Si hay una ruta de creación, descomenta y usa el estilo coherente: --}}
                    {{-- Barra de búsqueda (Estilo compacto y elegante) --}}
                    <div class="relative w-full max-w-lg md:max-w-md">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400"></i>
                        <input type="text" id="search-input" placeholder="Buscar DNI o nombre..."
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
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-id-card mr-1"></i> DNI
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-user mr-1"></i> Nombre
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-phone-alt mr-1"></i> Teléfono
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-envelope mr-1"></i> Email
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-city mr-1"></i> Municipio
                                </th>

                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    RTN
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-wallet mr-1"></i> Nº Cuenta
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-24">
                                    <i class="fas fa-cogs mr-1"></i> Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody id="afiliados-table-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                            @include('afiliados.partials.table_rows')
                        </tbody>
                    </table>
                    
                    <div id="no-results-message" class="hidden p-10 text-center bg-white dark:bg-gray-800">
                        <i class="fas fa-box-open text-5xl text-blue-400 dark:text-blue-600 mb-3"></i>
                        <p class="font-extrabold text-xl text-gray-900 dark:text-white">¡Vaya! No se encontraron afiliados.</p>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">Intenta ajustar tu búsqueda.</p>
                    </div>
                </div>

                <div id="pagination-links" class="mt-8">
                    {{ $afiliados->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

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
        const afiliadosTableBody = document.getElementById('afiliados-table-body');
        const paginationLinksContainer = document.getElementById('pagination-links');
        const noResultsMessage = document.getElementById('no-results-message');
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
        let searchTimeout;

        function handleDeleteClick(e) {
            e.preventDefault();
            const form = this.closest('form');
            const afiliadoName = this.getAttribute('data-name') || 'este afiliado'; 

            Swal.fire({
                title: '¿Eliminar ' + afiliadoName + '?',
                text: '¡Esta acción es irreversible! Se eliminarán todos los registros asociados.',
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
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json'
                        },
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw new Error(err.message || 'Error al eliminar el afiliado'); });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire('¡Eliminado!', data.message, 'success');
                            fetchAfiliados(getCurrentPage()); 
                        } else {
                            Swal.fire('Error', data.message || 'No se pudo eliminar el afiliado.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error en fetch handleDeleteClick:', error);
                        Swal.fire('Error', 'Ocurrió un error inesperado al eliminar: ' + error.message, 'error');
                    });
                }
            });
        }

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
                    
                    // Actualizar la URL sin recargar la página
                    const newUrl = `{{ route('afiliados.list') }}?page=${page}&search=${encodeURIComponent(searchInput.value)}`;
                    window.history.pushState({}, '', newUrl);
                    fetchAfiliados(page);
                });
            });
        }

        function fetchAfiliados(page = 1) {
            const query = searchInput.value;
            const url = `{{ route('afiliados.list') }}?page=${page}&search=${encodeURIComponent(query)}`;

            // Muestra el estado de carga
            afiliadosTableBody.innerHTML = '<tr><td colspan="10" class="p-6 text-center text-blue-500 dark:text-blue-400"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando afiliados...</td></tr>';
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
                afiliadosTableBody.innerHTML = data.table_rows;
                paginationLinksContainer.innerHTML = data.pagination_links;

                // Muestra/oculta el mensaje de "no resultados"
                if (data.afiliados_count === 0) {
                    noResultsMessage.classList.remove('hidden');
                    afiliadosTableBody.innerHTML = '';
                } else {
                    noResultsMessage.classList.add('hidden');
                }

                attachDeleteListeners();
                attachPaginationListeners();
            })
            .catch(error => {
                console.error('Error al buscar afiliados:', error);
                afiliadosTableBody.innerHTML = '<tr><td colspan="10" class="p-6 text-center text-red-500 dark:text-red-400"><i class="fas fa-exclamation-triangle mr-2"></i> Error al cargar.</td></tr>';
                Swal.fire('Error de Carga', 'No se pudieron cargar los afiliados. Inténtalo de nuevo. Detalles: ' + error.message, 'error');
            });
        }

        function getCurrentPage() {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get('page') || 1;
        }

        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                fetchAfiliados(1);
            }, 300);
        });

        // Inicialización: Usar el valor actual de la búsqueda en la URL
        const initialSearch = searchInput.value;
        window.history.replaceState({}, '', `{{ route('afiliados.list') }}?search=${encodeURIComponent(initialSearch)}`);
        
        attachDeleteListeners();
        attachPaginationListeners();
    });
</script>