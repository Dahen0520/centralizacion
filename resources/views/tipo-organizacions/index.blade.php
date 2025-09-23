<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Lista de Tipos de Organización') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-8 border-t-8 border-blue-600">
                <div class="flex justify-between items-center mb-6">
                    <a href="{{ route('tipo-organizacions.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-plus mr-2"></i> Crear Tipo de Organización
                    </a>
                </div>

                {{-- Barra de búsqueda --}}
                <div class="mb-4">
                    <input type="text" id="search-input" placeholder="Buscar tipos de organización por nombre..."
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline">
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
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/3">
                                    ID
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/3">
                                    Nombre
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/3">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody id="tipos-organizacion-table-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            {{-- Los tipos de organización se cargarán aquí --}}
                            @include('tipo-organizacions.partials.tipo_organizacion_table_rows')
                        </tbody>
                    </table>
                </div>

                {{-- Enlaces de paginación --}}
                <div id="pagination-links" class="mt-4">
                    {{ $tiposOrganizacion->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

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
        const tiposOrganizacionTableBody = document.getElementById('tipos-organizacion-table-body');
        const paginationLinksContainer = document.getElementById('pagination-links');

        let searchTimeout;

        // Función para manejar el clic en el botón de eliminar
        function handleDeleteClick(e) {
            e.preventDefault();
            const form = this.closest('form');
            const tipoOrganizacionId = this.getAttribute('data-id');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

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
                        method: 'DELETE',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json'
                        },
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw new Error(err.message || 'Error al eliminar el tipo de organización'); });
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
                            fetchTiposOrganizacion(getCurrentPage());
                        } else {
                            Swal.fire(
                                'Error',
                                data.message || 'No se pudo eliminar el tipo de organización.',
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
            fetchTiposOrganizacion(page);
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

        // Función principal para obtener y mostrar los tipos de organización
        function fetchTiposOrganizacion(page = 1) {
            const query = searchInput.value;
            const url = `{{ route('tipo-organizacions.index') }}?page=${page}&search=${encodeURIComponent(query)}`;

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
                tiposOrganizacionTableBody.innerHTML = data.table_rows;
                paginationLinksContainer.innerHTML = data.pagination_links;

                attachDeleteListeners();
                attachPaginationListeners();
            })
            .catch(error => {
                console.error('Error al buscar tipos de organización:', error);
                Swal.fire(
                    'Error de Carga',
                    'No se pudieron cargar los tipos de organización. Inténtalo de nuevo. Detalles: ' + error.message,
                    'error'
                );
            });
        }

        // Función para obtener la página actual de la URL
        function getCurrentPage() {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get('page') || 1;
        }

        // Event listener para la barra de búsqueda
        searchInput.addEventListener('keyup', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                fetchTiposOrganizacion(1);
            }, 300);
        });

        // Adjuntar listeners iniciales cuando la página se carga
        attachDeleteListeners();
        attachPaginationListeners();
    });
</script>