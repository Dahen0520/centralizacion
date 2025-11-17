<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-center gap-3">
            <div class="p-2 bg-indigo-100 dark:bg-indigo-900 rounded-lg">
                <i class="fas fa-file-invoice-dollar text-2xl text-indigo-600 dark:text-indigo-400"></i>
            </div>
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Administración de Rangos CAI (SAR)') }}
            </h2>
        </div>
    </x-slot>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-200 dark:border-gray-700">
                
                {{-- HEADER CON FILTRO Y BOTÓN --}}
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-gray-900 dark:to-indigo-950 p-6 border-b-2 border-indigo-200 dark:border-indigo-800">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                        
                        {{-- FILTRO MEJORADO --}}
                        <div class="relative w-full md:w-2/5">
                            <label for="status-filter" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                                <i class="fas fa-filter text-indigo-500"></i>
                                Filtrar por Estado Fiscal
                            </label>
                            <div class="relative">
                                <select id="status-filter"
                                        class="filter-select appearance-none w-full px-4 py-3 pr-10 bg-white dark:bg-gray-700 
                                               border-2 border-gray-300 dark:border-gray-600 rounded-xl
                                               text-gray-900 dark:text-gray-100 font-medium
                                               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 
                                               transition-all duration-200 cursor-pointer
                                               hover:border-indigo-400 dark:hover:border-indigo-500
                                               shadow-sm hover:shadow-md">
                                    <option value="activo" @if(!request()->get('status') || request()->get('status') == 'activo') selected @endif>
                                        Rangos Activos (Vigentes)
                                    </option>
                                    <option value="todos" @if(request()->get('status') == 'todos') selected @endif>
                                        Mostrar Todos los Rangos
                                    </option>
                                    <option value="expirado" @if(request()->get('status') == 'expirado') selected @endif>
                                        ⚠️ Rangos Expirados/Vencidos
                                    </option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-indigo-500">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                        </div>

                        {{-- BOTÓN DE CREACIÓN MEJORADO --}}
                        <a href="{{ route('rangos-cai.create') }}" 
                           class="w-full md:w-auto group relative px-8 py-3.5 bg-gradient-to-r from-green-500 to-emerald-600 
                                  hover:from-green-600 hover:to-emerald-700 text-white rounded-xl font-semibold 
                                  transition-all duration-300 shadow-lg hover:shadow-xl 
                                  flex items-center justify-center gap-2 transform hover:scale-105 hover:-translate-y-0.5">
                            <i class="fas fa-plus-circle text-lg group-hover:rotate-90 transition-transform duration-300"></i>
                            <span>Nuevo Rango CAI</span>
                            <div class="absolute inset-0 rounded-xl bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                        </a>
                    </div>
                </div>

                {{-- MENSAJES DE SESIÓN MEJORADOS --}}
                <div class="px-6 pt-6">
                    @if (session('success'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                             x-transition:enter="transform ease-out duration-300"
                             x-transition:enter-start="translate-y-2 opacity-0"
                             x-transition:enter-end="translate-y-0 opacity-100"
                             class="mb-4 p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20
                                    border-l-4 border-green-500 text-green-700 dark:text-green-400 rounded-r-xl shadow-md flex items-center gap-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white"></i>
                            </div>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                    @endif
                    
                    @if (session('error'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                             x-transition:enter="transform ease-out duration-300"
                             x-transition:enter-start="translate-y-2 opacity-0"
                             x-transition:enter-end="translate-y-0 opacity-100"
                             class="mb-4 p-4 bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20
                                    border-l-4 border-red-500 text-red-700 dark:text-red-400 rounded-r-xl shadow-md flex items-center gap-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-red-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-exclamation text-white"></i>
                            </div>
                            <span class="font-medium">{{ session('error') }}</span>
                        </div>
                    @endif
                </div>

                {{-- TABLA DE RANGOS CAI MEJORADA --}}
                <div class="p-6">
                    <div class="overflow-hidden rounded-xl border-2 border-gray-200 dark:border-gray-700 shadow-lg">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-store text-indigo-500"></i>
                                                <span>Tienda</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-file-invoice text-indigo-500"></i>
                                                <span>CAI / Rango Autorizado</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                            <div class="flex items-center justify-center gap-2">
                                                <i class="fas fa-sort-numeric-up-alt text-indigo-500"></i>
                                                <span>Núm. Actual</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                            <div class="flex items-center justify-center gap-2">
                                                <i class="fas fa-calendar-alt text-indigo-500"></i>
                                                <span>Fecha Límite</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                            <div class="flex items-center justify-center gap-2">
                                                <i class="fas fa-check-circle text-indigo-500"></i>
                                                <span>Estado</span>
                                            </div>
                                        </th>

                                    </tr>
                                </thead>
                                <tbody id="rangos-cai-table-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @include('rangos-cai.partials.rangos_table_rows')
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- PAGINACIÓN MEJORADA --}}
                    <div id="pagination-links" class="mt-6">
                        {{ $rangos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- JAVASCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // Asumiendo que tienes un tag <meta name="csrf-token" content="..."> en tu layout principal
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            const filterSelect = document.getElementById('status-filter');
            const tableBody = document.getElementById('rangos-cai-table-body');
            const paginationContainer = document.getElementById('pagination-links');
            let fetchController = null;
            
            function fetchRangos(urlOrPage = 1) {
                const status = filterSelect.value;
                let fetchUrl;
                
                if (typeof urlOrPage === 'string') {
                    fetchUrl = new URL(urlOrPage);
                    fetchUrl.searchParams.set('status', status); 
                } else {
                    fetchUrl = `{{ route('rangos-cai.index') }}?page=${urlOrPage}&status=${status}`;
                }

                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <div class="relative">
                                    <div class="w-16 h-16 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
                                    <i class="fas fa-file-invoice-dollar absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-indigo-600"></i>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400 font-medium">Cargando rangos...</p>
                            </div>
                        </td>
                    </tr>
                `;

                if (fetchController) {
                    fetchController.abort();
                }
                fetchController = new AbortController();

                fetch(fetchUrl, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    signal: fetchController.signal
                })
                .then(response => response.text())
                .then(html => {
                    
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;
                    
                    const tableContent = tempDiv.querySelector('#rangos-cai-table-body').innerHTML;
                    tableBody.innerHTML = tableContent;

                    const newPaginationContainer = tempDiv.querySelector('#pagination-links');

                    if (newPaginationContainer && newPaginationContainer.innerHTML.trim()) {
                        paginationContainer.innerHTML = newPaginationContainer.innerHTML;
                        paginationContainer.style.display = 'block';
                    } else {
                        paginationContainer.innerHTML = '';
                        paginationContainer.style.display = 'none';
                    }
                    
                    bindDeleteListeners();
                    bindPaginationListeners();
                    fetchController = null;
                })
                .catch(error => {
                    if (error.name !== 'AbortError') {
                        console.error('Error al cargar rangos:', error);
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center gap-3">
                                        <div class="w-16 h-16 bg-red-100 dark:bg-red-900/20 rounded-full flex items-center justify-center">
                                            <i class="fas fa-exclamation-triangle text-2xl text-red-500"></i>
                                        </div>
                                        <p class="text-red-600 dark:text-red-400 font-medium">Error al cargar los datos</p>
                                    </div>
                                </td>
                            </tr>
                        `;
                    }
                });
            }
            
            function bindPaginationListeners() {
                paginationContainer.querySelectorAll('.pagination a').forEach(link => {
                    link.removeEventListener('click', handlePagination);
                    link.addEventListener('click', handlePagination);
                });
            }
            
            function handlePagination(e) {
                e.preventDefault();
                fetchRangos(this.href); 
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }

            filterSelect.addEventListener('change', function () {
                const newUrl = new URL(window.location.href);
                newUrl.searchParams.set('status', this.value);
                newUrl.searchParams.set('page', 1);
                window.history.pushState({ path: newUrl.href }, '', newUrl.href);

                fetchRangos(1); 
            });
            
            function bindDeleteListeners() {
                document.querySelectorAll('.delete-btn').forEach(button => {
                    button.removeEventListener('click', handleDelete);
                    button.addEventListener('click', handleDelete);
                });
            }

            // ** FUNCIÓN MODIFICADA PARA RECARGA COMPLETA (OPCIÓN B) **
            function handleDelete(e) {
                e.preventDefault();
                const form = this.closest('form');
                const cai = this.getAttribute('data-name');
                
                // Obtener la URL de la página actual para la recarga, manteniendo filtros.
                const currentUrl = window.location.href; 

                Swal.fire({
                    title: '¿Eliminar Rango CAI?',
                    text: `Está a punto de eliminar el rango con CAI: ${cai}. ¡Esto es irreversible si se elimina!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444', 
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Sí, ¡Eliminar!',
                    cancelButtonText: 'Cancelar',
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'rounded-lg',
                        cancelButton: 'rounded-lg'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(form.action, {
                            method: 'POST', 
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: new URLSearchParams({
                                '_method': 'DELETE',
                                '_token': csrfToken
                            })
                        })
                        .then(response => {
                            // PRIMERO, verificar si la respuesta es OK (200) o Error (409, 500, etc.)
                            if (!response.ok) {
                                // Si hay un error HTTP, extraemos el mensaje de la respuesta JSON
                                return response.json().then(data => {
                                    throw new Error(data.message || 'Error en la eliminación.');
                                });
                            }
                            return response.json(); // Si es 200 OK
                        })
                        .then(data => {
                            // Si el Controller devuelve éxito (código 200)
                            Swal.fire({
                                title: '¡Eliminado!',
                                text: data.message,
                                icon: 'success',
                                customClass: {
                                    popup: 'rounded-2xl',
                                    confirmButton: 'rounded-lg'
                                }
                            });
                            
                            // ** RECARGA COMPLETA Y SEGURA **
                            window.location.href = currentUrl; 

                        })
                        .catch(error => {
                            // Captura el error lanzado (incluyendo el mensaje de clave foránea)
                            console.error('Error al intentar eliminar:', error);
                            
                            Swal.fire({
                                title: 'Error',
                                text: error.message || 'Ocurrió un error inesperado al eliminar el rango.',
                                icon: 'error',
                                customClass: {
                                    popup: 'rounded-2xl',
                                    confirmButton: 'rounded-lg'
                                }
                            });
                        });
                    }
                });
            }
            
            bindDeleteListeners();
            bindPaginationListeners();

            if (new URLSearchParams(window.location.search).get('status')) {
                 fetchRangos(1);
            }
        });
    </script>
</x-app-layout>