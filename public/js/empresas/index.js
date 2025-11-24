document.addEventListener('DOMContentLoaded', function () {
    
    // --- Configuración Inicial (Sin directivas de Blade) ---
    const config = window.AppConfig || {};
    const empresasIndexRoute = config.empresasIndexRoute;
    const csrfToken = config.csrfToken;
    const COLUMN_COUNT = config.columnCount || 7; 
    const initialStatus = config.currentStatus;

    // --- Elementos DOM ---
    const searchInput = document.getElementById('search-input');
    const empresasTableBody = document.getElementById('empresas-table-body');
    const paginationLinksContainer = document.getElementById('pagination-links');
    const noResultsMessage = document.getElementById('no-results-message');
    const filterButtons = document.querySelectorAll('.status-filter');
    
    let searchTimeout;
    
    // --- Inicialización y Helpers ---

    // Mostrar mensaje de sesión si existe (usando la variable pasada)
    const sessionSuccess = config.sessionSuccess;
    if (sessionSuccess) {
        Swal.fire({
            icon: 'success',
            title: '¡Operación Exitosa!',
            text: sessionSuccess,
            showConfirmButton: false,
            timer: 3000,
            toast: true,
            position: 'top-end'
        });
    }

    function updateStatusCounts(counts, currentStatus) {
        const statuses = ['todos', 'aprobado', 'pendiente', 'rechazado'];
        
        statuses.forEach(status => {
            const countElement = document.getElementById(`count-${status}`);
            
            if (countElement) {
                const count = counts[status] ?? 0;
                countElement.textContent = count;
                
                // Actualizar clases del contador
                if (status === currentStatus) {
                     countElement.classList.remove('bg-blue-100', 'dark:bg-gray-900', 'text-blue-600', 'dark:text-blue-400');
                     countElement.classList.add('bg-white/30');
                } else {
                     countElement.classList.add('bg-blue-100', 'dark:bg-gray-900', 'text-blue-600', 'dark:text-blue-400');
                     countElement.classList.remove('bg-white/30');
                }
            }
        });
    }

    // Inicializar los contadores con el valor de Blade
    if (config.statusCounts) {
        updateStatusCounts(config.statusCounts, initialStatus);
    }
    
    function getCurrentStatus() {
        const activeFilter = document.querySelector('.status-filter.bg-blue-600');
        if (activeFilter) {
            return activeFilter.getAttribute('data-status');
        }
        // Fallback al estado inicial si no se encuentra el activo
        return initialStatus;
    }
    
    function getCurrentPage() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('page') || 1;
    }

    // --- Funciones de Acciones (Eliminar) ---

    function handleDeleteClick(e) {
        e.preventDefault();
        const deleteButton = this;
        const form = deleteButton.closest('form');
        const empresaName = deleteButton.getAttribute('data-name') || 'esta empresa'; 

        Swal.fire({
            title: '¿Eliminar ' + empresaName + '?',
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
                        return response.json().then(err => { throw new Error(err.message || 'Error al eliminar la empresa'); });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire('¡Eliminado!', data.message, 'success');
                        fetchEmpresas(getCurrentPage()); 
                    } else {
                        Swal.fire('Error', data.message || 'No se pudo eliminar la empresa.', 'error');
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

    // --- Lógica Principal de Carga (AJAX) ---

    function fetchEmpresas(page = 1) {
        const query = searchInput.value;
        const status = getCurrentStatus();
        
        // Usar la variable JS de la ruta
        const url = `${empresasIndexRoute}?page=${page}&search=${encodeURIComponent(query)}&estado=${status}`;

        // Muestra estado de carga
        empresasTableBody.innerHTML = `<tr><td colspan="${COLUMN_COUNT}" class="p-6 text-center text-blue-500 dark:text-blue-400"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando empresas...</td></tr>`;
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
            empresasTableBody.innerHTML = data.table_rows;
            paginationLinksContainer.innerHTML = data.pagination_links;
            
            // Actualizar contadores si se reciben
            if (data.status_counts) {
                updateStatusCounts(data.status_counts, status);
            }
            
            // Mostrar/ocultar mensaje de no resultados
            if (data.empresas_count === 0) {
                noResultsMessage.classList.remove('hidden');
                empresasTableBody.innerHTML = '';
            } else {
                noResultsMessage.classList.add('hidden');
            }

            attachDeleteListeners();
            attachPaginationListeners();
        })
        .catch(error => {
            console.error('Error al buscar empresas:', error);
            empresasTableBody.innerHTML = `<tr><td colspan="${COLUMN_COUNT}" class="p-6 text-center text-red-500 dark:text-red-400"><i class="fas fa-exclamation-triangle mr-2"></i> Error al cargar.</td></tr>`;
            Swal.fire('Error de Carga', 'No se pudieron cargar las empresas. Inténtalo de nuevo. Detalles: ' + error.message, 'error');
        });
    }

    // --- Listeners de Interacción ---

    function attachPaginationListeners() {
        paginationLinksContainer.querySelectorAll('a').forEach(link => {
            const newLink = link.cloneNode(true);
            link.parentNode.replaceChild(newLink, link);
            
            newLink.addEventListener('click', function(e) {
                e.preventDefault();
                const url = new URL(this.href);
                const page = url.searchParams.get('page');
                const status = getCurrentStatus();
                
                // Usar la variable JS de la ruta
                const newUrl = `${empresasIndexRoute}?page=${page}&search=${encodeURIComponent(searchInput.value)}&estado=${status}`;
                window.history.pushState({}, '', newUrl);
                fetchEmpresas(page);
            });
        });
    }

    // Listener para los botones de estado
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Lógica para cambiar las clases del botón activo
            filterButtons.forEach(btn => {
                btn.classList.remove('bg-blue-600', 'text-white', 'shadow-md');
                btn.classList.add('bg-transparent', 'text-gray-700', 'dark:text-gray-300', 'hover:bg-gray-200', 'dark:hover:bg-gray-600');
            });
            
            this.classList.remove('bg-transparent', 'text-gray-700', 'dark:text-gray-300', 'hover:bg-gray-200', 'dark:hover:bg-gray-600');
            this.classList.add('bg-blue-600', 'text-white', 'shadow-md');
            
            fetchEmpresas(1); 
        });
    });

    // Listener para el input de búsqueda
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetchEmpresas(1); 
        }, 300);
    });

    // --- Inicialización Final ---
    
    // Asegurar que la URL y el estado inicial sean correctos
    const currentStatusInUrl = new URLSearchParams(window.location.search).get('estado');
    const statusToApply = currentStatusInUrl || initialStatus;

    // Sincronizar los botones con el estado de la URL si es necesario
    if (statusToApply !== initialStatus) {
         filterButtons.forEach(btn => {
            btn.classList.remove('bg-blue-600', 'text-white', 'shadow-md');
            btn.classList.add('bg-transparent', 'text-gray-700', 'dark:text-gray-300', 'hover:bg-gray-200', 'dark:hover:bg-gray-600');
            if (btn.getAttribute('data-status') === statusToApply) {
                 btn.classList.remove('bg-transparent', 'text-gray-700', 'dark:text-gray-300', 'hover:bg-gray-200', 'dark:hover:bg-gray-600');
                 btn.classList.add('bg-blue-600', 'text-white', 'shadow-md');
            }
         });
    }
    
    // Si hay parámetros de búsqueda o paginación en la URL, se debe recargar la tabla (porque la primera carga es estática)
    if (searchInput.value || getCurrentPage() > 1 || statusToApply !== initialStatus) {
        fetchEmpresas(getCurrentPage());
    }

    attachDeleteListeners();
    attachPaginationListeners();
});