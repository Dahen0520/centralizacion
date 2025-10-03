<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Gestión de Marcas Registradas') }}
        </h2>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-12">
        <div class="w-full max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 
                        rounded-2xl shadow-3xl p-8 lg:p-10 
                        border-t-4 border-blue-600 dark:border-blue-500">
                
                @php
                    $currentStatus = $currentStatus ?? request('estado', 'aprobado');
                    $statusCounts = $statusCounts ?? ['todos' => 0, 'aprobado' => 0, 'pendiente' => 0, 'rechazado' => 0];

                    $statuses = [
                        'todos' => ['label' => 'Todas', 'icon' => 'fas fa-list'],
                        'aprobado' => ['label' => 'Aprobadas', 'icon' => 'fas fa-check-circle'],
                        'pendiente' => ['label' => 'Pendientes', 'icon' => 'fas fa-clock'],
                        'rechazado' => ['label' => 'Rechazadas', 'icon' => 'fas fa-times-circle'],
                    ];
                @endphp

                {{-- HEADER DE ACCIONES, FILTROS Y BÚSQUEDA --}}
                <div class="flex flex-col lg:flex-row justify-between items-center mb-8 gap-4">
                    
                    {{-- Botón Crear --}}
                    <a href="{{ route('marcas.create') }}" 
                       class="w-full md:w-auto inline-flex items-center justify-center px-8 py-3 
                              bg-gradient-to-r from-blue-600 to-indigo-700 text-white 
                              font-bold text-sm uppercase tracking-wider rounded-xl shadow-lg 
                              hover:shadow-xl hover:from-blue-700 hover:to-indigo-800 
                              focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 
                              transition duration-300 transform hover:scale-[1.02]">
                        <i class="fas fa-plus-circle mr-2 text-lg"></i> Crear Marca
                    </a>

                    {{-- GRUPO DE FILTRO DESPLEGABLE Y BÚSQUEDA --}}
                    <div class="flex flex-col sm:flex-row justify-end items-center gap-3 w-full lg:w-3/5">
                        
                        {{-- Filtro Desplegable de Estado --}}
                        <select id="estado-filter" class="filter-select select-styled px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-md text-gray-700 dark:text-gray-200 dark:bg-gray-700 focus:ring-blue-500 focus:border-blue-500 transition duration-150 w-full sm:w-auto">
                            @foreach ($statuses as $statusKey => $statusData)
                                @php
                                    $count = $statusCounts[$statusKey] ?? 0;
                                    $label = "{$statusData['label']} ({$count})";
                                @endphp
                                <option value="{{ $statusKey }}" @selected($currentStatus === $statusKey)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        
                        {{-- Barra de búsqueda --}}
                        <div class="relative w-full sm:w-2/3">
                            <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400"></i>
                            <input type="text" id="search-input" placeholder="Buscar por producto..."
                                   value="{{ request('search') }}"
                                   class="pl-12 py-3.5 shadow-md border border-gray-300 dark:border-gray-600 rounded-xl w-full 
                                          text-gray-900 dark:text-gray-100 dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400
                                          focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
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
                                    <i class="fas fa-fingerprint mr-1"></i> ID
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    Código de Marca
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-box mr-1"></i> Producto
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-building mr-1"></i> Empresa
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-24">
                                    Estado
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-24">
                                    <i class="fas fa-cogs mr-1"></i> Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody id="marcas-table-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                            @include('marcas.partials.marcas_table_rows')
                        </tbody>
                    </table>
                    
                    {{-- Mensaje de No Resultados --}}
                    <div id="no-results-message" class="hidden p-10 text-center bg-white dark:bg-gray-800">
                        <i class="fas fa-tag text-5xl text-blue-400 dark:text-blue-600 mb-3"></i>
                        <p class="font-extrabold text-xl text-gray-900 dark:text-white">¡Vaya! No se encontraron marcas.</p>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">Ajusta los filtros o los términos de búsqueda.</p>
                    </div>
                </div>

                {{-- Paginación --}}
                <div id="pagination-links" class="mt-8">
                    {{ $marcas->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Estilos personalizados para el select --}}
    <style>
        .select-styled {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%236B7280'%3E%3Cpath fill-rule='evenodd' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' clip-rule='evenodd' /%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1.5em;
            padding-right: 3rem !important;
            cursor: pointer;
        }
        .dark .select-styled {
             background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%239CA3AF'%3E%3Cpath fill-rule='evenodd' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' clip-rule='evenodd' /%3E%3C/svg%3E");
        }
    </style>

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
            const marcasTableBody = document.getElementById('marcas-table-body');
            const paginationLinksContainer = document.getElementById('pagination-links');
            const noResultsMessage = document.getElementById('no-results-message');
            const estadoFilter = document.getElementById('estado-filter'); // Nuevo selector

            let searchTimeout;
            const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

            // Obtener el estado actual del selector
            function getCurrentStatus() {
                return estadoFilter.value;
            }

            // Función principal para obtener y mostrar las marcas
            function fetchMarcas(page = 1) {
                const query = searchInput.value;
                const status = getCurrentStatus();
                const url = `{{ route('marcas.index') }}?page=${page}&search=${encodeURIComponent(query)}&estado=${status}`;

                // Muestra estado de carga
                marcasTableBody.innerHTML = '<tr><td colspan="7" class="p-6 text-center text-blue-500 dark:text-blue-400"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando marcas...</td></tr>';
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
                    // Actualizar URL del navegador para persistencia
                    window.history.pushState({}, '', url);

                    marcasTableBody.innerHTML = data.table_rows;
                    paginationLinksContainer.innerHTML = data.pagination_links;
                    
                    // Actualizar conteos (Aunque el selector no los muestra, es bueno tener la lógica si se necesita)
                    // Si el selector se actualiza, la vista principal ya tiene los datos seleccionados
                    
                    // Manejar el mensaje de no resultados
                    if (data.marcas_count === 0) {
                        noResultsMessage.classList.remove('hidden');
                    } else {
                        noResultsMessage.classList.add('hidden');
                    }

                    attachDeleteListeners();
                    attachPaginationListeners();
                })
                .catch(error => {
                    console.error('Error al buscar marcas:', error);
                    marcasTableBody.innerHTML = '<tr><td colspan="7" class="p-6 text-center text-red-500 dark:text-red-400"><i class="fas fa-exclamation-triangle mr-2"></i> Error al cargar.</td></tr>';
                    Swal.fire('Error de Carga', 'No se pudieron cargar las marcas. Detalles: ' + error.message, 'error');
                });
            }

            function getCurrentPage() {
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get('page') || 1;
            }

            // --- LISTENERS DE EVENTOS ---
            
            // Listener para el input de búsqueda (con debounce)
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    fetchMarcas(1); 
                }, 300);
            });

            // Listener para el filtro desplegable de estado (inmediato)
            estadoFilter.addEventListener('change', function() {
                fetchMarcas(1);
            });

            // Función para adjuntar listeners de eliminación a los botones
            function attachDeleteListeners() {
                const deleteButtons = document.querySelectorAll('.delete-btn');
                deleteButtons.forEach(button => {
                    const newButton = button.cloneNode(true);
                    button.parentNode.replaceChild(newButton, button);
                    newButton.addEventListener('click', handleDeleteClick);
                });
            }

            // Función para manejar el clic en el botón de eliminar
            function handleDeleteClick(e) {
                e.preventDefault();
                const form = this.closest('form');
                const marcaName = this.getAttribute('data-name') || 'esta marca';

                Swal.fire({
                    title: '¿Eliminar ' + marcaName + '?',
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
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => { throw new Error(err.message || 'Error al eliminar la marca'); });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                Swal.fire('¡Eliminado!', data.message, 'success');
                                fetchMarcas(getCurrentPage());
                            } else {
                                Swal.fire('Error', data.message || 'No se pudo eliminar la marca.', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error en fetch handleDeleteClick:', error);
                            Swal.fire('Error', 'Ocurrió un error inesperado al eliminar: ' + error.message, 'error');
                        });
                    }
                });
            }

            // Función para adjuntar listeners de paginación a los enlaces
            function attachPaginationListeners() {
                const links = paginationLinksContainer.querySelectorAll('a');
                links.forEach(link => {
                    const newLink = link.cloneNode(true);
                    link.parentNode.replaceChild(newLink, link);
                    newLink.addEventListener('click', function(e) {
                        e.preventDefault();
                        const url = new URL(this.href);
                        const page = url.searchParams.get('page');
                        
                        const newUrl = `{{ route('marcas.index') }}?page=${page}&search=${encodeURIComponent(searchInput.value)}&estado=${getCurrentStatus()}`;
                        window.history.pushState({}, '', newUrl);
                        fetchMarcas(page);
                    });
                });
            }

            // Inicialización:
            attachDeleteListeners();
            attachPaginationListeners();
        });
    </script>
</x-app-layout>