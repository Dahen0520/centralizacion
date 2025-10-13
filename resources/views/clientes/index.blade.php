<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Administración de Clientes') }}
        </h2>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-12">
        <div class="w-full max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 
                        rounded-2xl shadow-3xl p-8 lg:p-10 
                        border-t-4 border-indigo-600 dark:border-indigo-500">

                {{-- HEADER DE ACCIONES Y BÚSQUEDA ESTILIZADA --}}
                <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                    
                    {{-- Botón Crear --}}
                    <a href="{{ route('clientes.create') }}" 
                       class="w-full md:w-auto inline-flex items-center justify-center px-8 py-3 
                              bg-gradient-to-r from-indigo-600 to-purple-700 text-white 
                              font-bold text-sm uppercase tracking-wider rounded-xl shadow-lg 
                              hover:shadow-xl hover:from-indigo-700 hover:to-purple-800 
                              focus:outline-none focus:ring-4 focus:ring-indigo-300 dark:focus:ring-purple-800 
                              transition duration-300 transform hover:scale-[1.02]">
                        <i class="fas fa-plus-circle mr-2 text-lg"></i> Crear Cliente
                    </a>

                    {{-- Barra de búsqueda --}}
                    <div class="relative w-full md:w-1/2">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400"></i>
                        <input type="text" id="search-input" value="{{ request('search') }}" placeholder="Buscar por nombre, RTN o ID..."
                               class="pl-12 py-3.5 shadow-md border border-gray-300 dark:border-gray-600 rounded-xl w-full 
                                      text-gray-900 dark:text-gray-100 dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150">
                    </div>
                </div>
                
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 dark:bg-green-900/30 dark:border-green-800 dark:text-green-300">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 dark:bg-red-900/30 dark:border-red-800 dark:text-red-300">
                        {{ session('error') }}
                    </div>
                @endif


                {{-- CONTENEDOR DE LA TABLA --}}
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-xl">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-16">
                                    #
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-user mr-1"></i> Nombre del Cliente
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-id-card mr-1"></i> Identificación / RTN
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-envelope mr-1"></i> Email
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-phone mr-1"></i> Teléfono
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-1/4">
                                    <i class="fas fa-cogs mr-1"></i> Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody id="clientes-table-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                            {{-- Incluye las filas de clientes usando el parcial --}}
                            @include('clientes.partials.clientes_table_rows', ['clientes' => $clientes, 'start_index' => $clientes->firstItem()])
                        </tbody>
                    </table>
                    
                    {{-- Mensaje de No Resultados (Usado por AJAX) --}}
                    <div id="no-results-message" class="hidden p-10 text-center bg-white dark:bg-gray-800">
                        <i class="fas fa-user-slash text-5xl text-indigo-400 dark:text-indigo-600 mb-3"></i>
                        <p class="font-extrabold text-xl text-gray-900 dark:text-white">¡Vaya! No se encontraron clientes.</p>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">Intenta ajustar tu búsqueda o crea uno nuevo.</p>
                    </div>
                </div>

                {{-- Paginación --}}
                <div id="pagination-links" class="mt-8">
                    {{ $clientes->links() }}
                </div>
            </div>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        const searchInput = document.getElementById('search-input');
        const clientesTableBody = document.getElementById('clientes-table-body');
        const paginationLinksContainer = document.getElementById('pagination-links');
        const noResultsMessage = document.getElementById('no-results-message');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        let searchTimeout;

        // --- MANEJO DE EVENTOS ---
        
        function handleDeleteClick(e) {
            e.preventDefault();
            const form = this.closest('form');
            const deleteButton = this;
            const clienteName = deleteButton.getAttribute('data-name') || 'este cliente'; 

            Swal.fire({
                title: '¿Eliminar ' + clienteName + '?',
                text: '¡Esta acción es irreversible! Se verificará que no tenga transacciones asociadas.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444', 
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Sí, ¡Eliminar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    
                    // 1. Mostrar estado de cargando
                    Swal.fire({
                        title: 'Eliminando...',
                        text: 'Procesando la solicitud, por favor espere.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // 2. Enviar solicitud DELETE
                    fetch(form.action, {
                        method: 'POST', // Usamos POST para rutas DELETE en formularios Blade con _method
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json'
                        },
                        // El cuerpo DEBE incluir _method: DELETE
                        body: JSON.stringify({
                            _method: 'DELETE'
                        })
                    })
                    .then(response => {
                        // Intentamos leer el JSON, incluso si hay un error HTTP.
                        if (response.headers.get('content-type')?.includes('application/json')) {
                            return response.json().then(data => ({ status: response.status, data }));
                        }
                        // Si es 204 No Content, asumimos éxito y devolvemos un objeto de datos.
                        if (response.status === 204) {
                             return { status: 200, data: { success: true, message: `Cliente ${clienteName} eliminado.` } };
                        }
                        // Si la respuesta es OK pero no JSON (no debería pasar), devolvemos éxito.
                        if (response.ok) {
                             return { status: 200, data: { success: true, message: `Cliente ${clienteName} eliminado.` } };
                        }
                        
                        // Forzamos error si el estado es malo y no pudimos leer el JSON.
                        throw new Error(`Error ${response.status}: Respuesta inesperada.`);
                    })
                    .then(({ status, data }) => {
                        // 3. Evaluar el resultado
                        if (data.success) {
                            // Éxito: Recargar datos de la tabla y mostrar SweetAlert de éxito
                            Swal.fire({
                                icon: 'success',
                                title: '¡Eliminado!',
                                text: data.message,
                                timer: 3000,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                            fetchClientes(getCurrentPage());
                        } else {
                            // Fallo (Ej: Error 422 del controlador por restricciones)
                            Swal.fire({
                                icon: 'error',
                                title: 'Error al Eliminar',
                                text: data.message || 'No se pudo eliminar el cliente.',
                                confirmButtonColor: '#EF4444'
                            });
                             // Refrescar los datos por si acaso (si hubo un error de restricción)
                            fetchClientes(getCurrentPage());
                        }
                    })
                    .catch(error => {
                        // 4. Fallo de conexión o error no manejado
                        console.error('Error al eliminar:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de Conexión',
                            text: error.message || 'Ocurrió un error de red o servidor.',
                            confirmButtonColor: '#EF4444'
                        });
                        fetchClientes(getCurrentPage());
                    });
                }
            });
        }

        function attachDeleteListeners() {
            // Adjunta listeners a todos los botones de eliminar
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.removeEventListener('click', handleDeleteClick);
                button.addEventListener('click', handleDeleteClick);
            });
        }

        // --- FUNCIONES DE AJAX Y PAGINACIÓN ---

        function attachPaginationListeners() {
            // Adjunta listeners a los nuevos enlaces de paginación
            paginationLinksContainer.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = new URL(this.href);
                    const page = url.searchParams.get('page');
                    
                    const newUrl = `{{ route('clientes.index') }}?page=${page}&search=${encodeURIComponent(searchInput.value)}`;
                    window.history.pushState({}, '', newUrl);
                    fetchClientes(page);
                });
            });
        }

        function fetchClientes(page = 1) {
            const query = searchInput.value;
            const url = `{{ route('clientes.index') }}?page=${page}&search=${encodeURIComponent(query)}`;

            clientesTableBody.innerHTML = '<tr><td colspan="6" class="p-6 text-center text-indigo-500 dark:text-indigo-400"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando clientes...</td></tr>';
            noResultsMessage.classList.add('hidden');

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                clientesTableBody.innerHTML = data.table_rows;
                paginationLinksContainer.innerHTML = data.pagination_links;

                if (data.clientes_count === 0 && query) {
                    noResultsMessage.classList.remove('hidden');
                } else {
                    noResultsMessage.classList.add('hidden');
                }

                attachDeleteListeners();
                attachPaginationListeners();
            })
            .catch(error => {
                console.error('Error al buscar clientes:', error);
                clientesTableBody.innerHTML = '<tr><td colspan="6" class="p-6 text-center text-red-500 dark:text-red-400"><i class="fas fa-exclamation-triangle mr-2"></i> Error al cargar los clientes.</td></tr>';
            });
        }

        function getCurrentPage() {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get('page') || 1;
        }

        // --- LÓGICA DE BÚSQUEDA ---
        
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                fetchClientes(1); 
            }, 300);
        });

        // Inicialización
        attachDeleteListeners();
        attachPaginationListeners();
        
        if (searchInput.value || getCurrentPage() > 1) {
             fetchClientes(getCurrentPage());
        }

        // Manejo de mensajes de sesión iniciales (para creación/edición, que sí recargan)
        const successMessage = '{{ session('success') }}';
        const errorMessage = '{{ session('error') }}';
        
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
        
        if (errorMessage) {
            Swal.fire({
                icon: 'error',
                title: 'Error de Operación',
                text: errorMessage,
                confirmButtonColor: '#ef4444',
            });
        }
    });
</script>
</x-app-layout>