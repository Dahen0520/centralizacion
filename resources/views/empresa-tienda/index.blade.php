<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Gestión de Vinculaciones') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-xl overflow-hidden">
                
                <!-- Header con estadísticas -->
                <div class="bg-gray-50 dark:bg-gray-700 px-8 py-6 border-b border-gray-200 dark:border-gray-600">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex items-center">
                            <div class="bg-blue-600 p-3 rounded-lg mr-4 shadow-lg">
                                <i class="fas fa-handshake text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Vinculaciones Registradas</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Total: <span class="font-medium">{{ $asociaciones->total() }}</span> registros</p>
                            </div>
                        </div>
                        <div class="mt-4 lg:mt-0">
                            <a href="{{ route('asociaciones.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium text-sm rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                                <i class="fas fa-plus mr-2"></i>
                                Nueva Vinculación
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Filtros mejorados -->
                <div class="px-8 py-6 bg-white dark:bg-gray-800">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end mb-6">
                        <div class="md:col-span-5">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-search text-gray-400 mr-1"></i>Buscar
                            </label>
                            <div class="relative">
                                <input type="text" id="search-input" placeholder="Buscar empresa o tienda..."
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-store text-gray-400 mr-1"></i>Tienda
                            </label>
                            <select id="filter-tienda" class="w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                <option value="">Todas</option>
                                @foreach($tiendas as $tienda)
                                    <option value="{{ $tienda->id }}">{{ $tienda->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-flag text-purple-500 mr-1"></i>Estado
                            </label>
                            <select id="filter-estado" class="w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                <option value="">Todos</option>
                                <option value="pendiente"> Pendiente</option>
                                <option value="aprobado"> Aprobado</option>
                                <option value="rechazado"> Rechazado</option>
                            </select>
                        </div>
                        <div class="md:col-span-1">
                            <button id="clear-filters" class="w-full px-4 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 rounded-lg transition-colors duration-200" title="Limpiar filtros">
                                <i class="fas fa-eraser"></i>
                            </button>
                        </div>
                    </div>

                    @if (session('success'))
                        <div id="success-alert" class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/50 dark:to-emerald-900/50 border-l-4 border-green-400 p-4 mb-6 rounded-lg shadow-sm" role="alert">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-green-400 rounded-full p-1">
                                        <i class="fas fa-check text-white text-xs"></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="font-semibold text-green-800 dark:text-green-200">¡Operación exitosa!</p>
                                    <p class="text-green-700 dark:text-green-300 text-sm">{{ session('success') }}</p>
                                </div>
                                <button class="ml-auto text-green-400 hover:text-green-600 transition-colors" onclick="this.closest('#success-alert').remove()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Tabla con diseño mejorado -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                            <div class="flex items-center">
                                                <i class="fas fa-building text-blue-500 mr-2"></i>
                                                Empresa
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                            <div class="flex items-center">
                                                <i class="fas fa-store text-green-500 mr-2"></i>
                                                Tienda
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                            <div class="flex items-center">
                                                <i class="fas fa-flag text-purple-500 mr-2"></i>
                                                Estado
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                            <div class="flex items-center justify-end">
                                                <i class="fas fa-cog text-gray-500 mr-2"></i>
                                                Acciones
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="associations-table-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                                    @include('empresa-tienda.partials.association_table_rows')
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Paginación mejorada -->
                    <div id="pagination-links" class="mt-8 flex justify-center">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-1">
                            {{ $asociaciones->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Oculta la alerta de éxito después de 5 segundos con animación suave
        const successAlert = document.getElementById('success-alert');
        if (successAlert) {
            setTimeout(function() {
                successAlert.style.opacity = '0';
                successAlert.style.transform = 'translateY(-10px)';
                setTimeout(() => successAlert.remove(), 300);
            }, 5000);
        }

        const searchInput = document.getElementById('search-input');
        const filterTienda = document.getElementById('filter-tienda');
        const filterEstado = document.getElementById('filter-estado');
        const clearFiltersBtn = document.getElementById('clear-filters');
        const associationsTableBody = document.getElementById('associations-table-body');
        const paginationLinksContainer = document.getElementById('pagination-links');

        let searchTimeout;

        // Función para manejar eliminación con SweetAlert y AJAX
        function handleDeleteClick(e) {
            e.preventDefault();
            const form = e.target.closest('form');
            const url = form.action;
            const csrfToken = form.querySelector('input[name="_token"]').value;
            const method = form.querySelector('input[name="_method"]').value;
            
            Swal.fire({
                title: '¿Confirmar eliminación?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(url, {
                        method: 'POST', // Usamos POST para la simulación de DELETE
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-HTTP-Method-Override': method // Laravel interpreta el método real aquí
                        },
                        body: JSON.stringify({}) // Cuerpo vacío para la petición DELETE
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Encontrar la fila y eliminarla con una animación
                            const row = form.closest('tr');
                            if (row) {
                                row.style.opacity = '0';
                                row.style.transform = 'translateY(-20px)';
                                setTimeout(() => row.remove(), 300);
                            }

                            // Mostrar mensaje de éxito
                            Swal.fire({
                                title: '¡Eliminado!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonColor: '#3B82F6'
                            });

                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.message,
                                icon: 'error',
                                confirmButtonColor: '#3B82F6'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error de conexión',
                            text: 'No se pudo completar la operación.',
                            icon: 'error',
                            confirmButtonColor: '#3B82F6'
                        });
                    });
                }
            });
        }

        function attachDeleteListeners() {
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.removeEventListener('click', handleDeleteClick);
                button.addEventListener('click', handleDeleteClick);
            });
        }

        function handlePaginationClick(e) {
            e.preventDefault();
            const url = e.target.closest('a')?.href;
            if (!url) return;
            
            const urlParams = new URLSearchParams(new URL(url).search);
            const page = urlParams.get('page');
            fetchAssociations(page);
        }

        function attachPaginationListeners() {
            document.querySelectorAll('#pagination-links a').forEach(link => {
                link.removeEventListener('click', handlePaginationClick);
                link.addEventListener('click', handlePaginationClick);
            });
        }

        function fetchAssociations(page = 1) {
            const query = searchInput.value;
            const tiendaId = filterTienda.value;
            const estado = filterEstado.value;

            // Efecto de carga sutil
            associationsTableBody.style.opacity = '0.6';

            const url = `{{ route('asociaciones.index') }}?page=${page}&search=${encodeURIComponent(query)}&tienda_id=${tiendaId}&estado=${estado}`;

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta: ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                associationsTableBody.innerHTML = data.table_rows;
                paginationLinksContainer.innerHTML = data.pagination_links;
                
                attachDeleteListeners();
                attachPaginationListeners();
                
                // Restaurar opacidad
                associationsTableBody.style.opacity = '1';
            })
            .catch(error => {
                console.error('Error:', error);
                associationsTableBody.style.opacity = '1';
                
                Swal.fire({
                    title: 'Error de conexión',
                    text: 'No se pudieron cargar los datos. Verifica tu conexión.',
                    icon: 'error',
                    confirmButtonColor: '#3B82F6'
                });
            });
        }

        // Función para limpiar filtros
        function clearFilters() {
            searchInput.value = '';
            filterTienda.value = '';
            filterEstado.value = '';
            fetchAssociations(1);
            
            // Feedback visual
            clearFiltersBtn.innerHTML = '<i class="fas fa-check text-emerald-600"></i>';
            setTimeout(() => {
                clearFiltersBtn.innerHTML = '<i class="fas fa-eraser"></i>';
            }, 1000);
        }

        // Event listeners
        searchInput.addEventListener('keyup', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => fetchAssociations(1), 300);
        });

        filterTienda.addEventListener('change', () => fetchAssociations(1));
        filterEstado.addEventListener('change', () => fetchAssociations(1));
        clearFiltersBtn.addEventListener('click', clearFilters);

        // Inicializar
        attachDeleteListeners();
        attachPaginationListeners();
    });
</script>

<style>
/* Estilos optimizados con colores */
tbody tr {
    @apply transition-all duration-300;
}

tbody tr:hover {
    @apply bg-gradient-to-r from-blue-25 via-indigo-25 to-purple-25 dark:from-blue-900/20 dark:via-indigo-900/20 dark:to-purple-900/20 transform translate-x-1;
    box-shadow: 0 4px 20px rgba(59, 130, 246, 0.15);
    border-left: 4px solid #3B82F6;
}

/* Mejoras en los enlaces de acción con colores */
tbody a {
    @apply font-medium transition-all duration-200 px-2 py-1 rounded-md;
}

tbody a[href*="show"] {
    @apply text-blue-600 hover:text-white hover:bg-blue-600;
}

tbody a[href*="edit"] {
    @apply text-indigo-600 hover:text-white hover:bg-indigo-600;
}

/* Estilo para botones de eliminación */
.delete-btn {
    @apply font-medium transition-all duration-200 px-2 py-1 rounded-md text-red-600 hover:text-white hover:bg-red-600;
}

/* Animaciones para filtros con colores */
input:focus {
    @apply transform scale-[1.02] ring-2 ring-blue-400 ring-opacity-50 shadow-lg shadow-blue-100;
}

select:focus {
    @apply transform scale-[1.02] ring-2 ring-opacity-50 shadow-lg;
}

#filter-tienda:focus {
    @apply ring-emerald-400 shadow-emerald-100;
}

#filter-estado:focus {
    @apply ring-purple-400 shadow-purple-100;
}

/* Paginación con colores */
.pagination {
    @apply flex items-center space-x-2;
}

.pagination a, .pagination span {
    @apply px-3 py-2 text-sm rounded-md transition-all duration-200;
}

.pagination a {
    @apply text-slate-600 hover:text-blue-600 hover:bg-blue-50 hover:shadow-md dark:text-slate-400 dark:hover:text-blue-400 dark:hover:bg-blue-900/20;
}

.pagination .active span {
    @apply bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg;
}

/* Responsive con colores */
@media (max-width: 768px) {
    tbody tr:hover {
        @apply transform translate-x-0 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/10 dark:to-indigo-900/10;
        border-left: 2px solid #3B82F6;
    }
    
    th, td {
        @apply px-3 py-3 text-xs;
    }
}

/* Animaciones adicionales */
@keyframes pulse-soft {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.animate-pulse {
    animation: pulse-soft 2s infinite;
}

/* Estados de carga con color */
.loading-shimmer {
    background: linear-gradient(90deg, #f0f4f8 25%, #e2e8f0 50%, #f0f4f8 75%);
    background-size: 200px 100%;
    animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
    0% { background-position: -200px 0; }
    100% { background-position: calc(200px + 100%) 0; }
}
</style>
