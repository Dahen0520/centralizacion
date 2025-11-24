document.addEventListener('DOMContentLoaded', function () {
    
    // --- 1. CONFIGURACIÓN INICIAL (USANDO AppConfig) ---
    const config = window.AppConfig || {};
    const inventariosIndexRoute = config.inventariosIndexRoute;
    const csrfToken = config.csrfToken;
    const COLUMN_COUNT = config.columnCount || 7; 

    // --- Elementos DOM ---
    const searchInput = document.getElementById('search-input');
    const tiendaFilter = document.getElementById('tienda-filter');
    const inventarioTableBody = document.getElementById('inventario-table-body');
    const paginationLinksContainer = document.getElementById('pagination-links');
    const noResultsMessage = document.getElementById('no-results-message');

    let searchTimeout;

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

    // --- 2. MANEJO DE ELIMINACIÓN (SWEETALERT2) ---
    function handleDeleteClick(e) {
        e.preventDefault();
        const form = this.closest('form');
        const itemName = this.getAttribute('data-name') || 'este registro de inventario';

        Swal.fire({
            title: '¿Eliminar ' + itemName + '?',
            text: '¡Esta acción es irreversible!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444', 
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Sí, ¡Eliminar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Usamos fetch con el método DELETE simulado para formularios Blade
                fetch(form.action, {
                    method: 'DELETE', 
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken 
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { 
                            throw new Error(err.message || `Error ${response.status}: No se pudo eliminar el registro.`); 
                        });
                    }
                    return response.json(); 
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire('¡Eliminado!', data.message, 'success');
                        fetchInventarios(getCurrentPage());
                    } else {
                        Swal.fire('Error', data.message || 'No se pudo eliminar el registro.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error en fetch handleDeleteClick:', error);
                    Swal.fire('Error', 'Ocurrió un error inesperado al eliminar: ' + error.message, 'error');
                });
            }
        });
    }

    // --- 3. LÓGICA AJAX PARA BÚSQUEDA, FILTRADO Y PAGINACIÓN ---

    function attachDeleteListeners() {
        const deleteButtons = document.querySelectorAll('.delete-btn');
        deleteButtons.forEach(button => {
            // Clonar y reemplazar para evitar listeners duplicados tras AJAX
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);
            newButton.addEventListener('click', handleDeleteClick);
        });
    }

    function handlePaginationClick(e) {
        e.preventDefault();
        const url = new URL(this.href);
        const page = url.searchParams.get('page');
        
        // Actualizar la URL antes de la llamada
        const newUrl = `${inventariosIndexRoute}?page=${page}&search=${encodeURIComponent(searchInput.value)}&tienda_id=${tiendaFilter.value}`;
        window.history.pushState({}, '', newUrl);

        fetchInventarios(page);
    }

    function attachPaginationListeners() {
        const links = paginationLinksContainer.querySelectorAll('a');
        links.forEach(link => {
            const newLink = link.cloneNode(true);
            link.parentNode.replaceChild(newLink, link);
            newLink.addEventListener('click', handlePaginationClick);
        });
    }

    function fetchInventarios(page = 1) {
        const query = searchInput.value;
        const tiendaId = tiendaFilter.value;

        // Construcción de la URL de búsqueda/filtrado (usando variable JS de ruta)
        const url = `${inventariosIndexRoute}?page=${page}&search=${encodeURIComponent(query)}&tienda_id=${tiendaId}`;

        // Mostrar estado de carga
        inventarioTableBody.innerHTML = `<tr><td colspan="${COLUMN_COUNT}" class="p-6 text-center text-emerald-500 dark:text-emerald-400"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando inventario...</td></tr>`;
        noResultsMessage?.classList.add('hidden'); 

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

            inventarioTableBody.innerHTML = data.table_rows;
            paginationLinksContainer.innerHTML = data.pagination_links;
            
            // Manejar el mensaje de no resultados
            const totalItems = parseInt(data.inventarios_count);

            if (totalItems === 0) {
                noResultsMessage?.classList.remove('hidden');
            } else {
                noResultsMessage?.classList.add('hidden');
            }
            
            attachDeleteListeners();
            attachPaginationListeners();
        })
        .catch(error => {
            console.error('Error al buscar inventario:', error);
            inventarioTableBody.innerHTML = `<tr><td colspan="${COLUMN_COUNT}" class="p-6 text-center text-red-500 dark:text-red-400"><i class="fas fa-exclamation-triangle mr-2"></i> Error al cargar.</td></tr>`;
            Swal.fire('Error de Carga', 'No se pudieron cargar los registros de inventario. Detalles: ' + error.message, 'error');
        });
    }

    function getCurrentPage() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('page') || 1;
    }

    // --- 4. ASIGNACIÓN DE EVENTOS ---
    
    // Evento que dispara la búsqueda a la página 1
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetchInventarios(1); 
        }, 300);
    });

    // Evento que dispara el filtro por tienda a la página 1
    tiendaFilter.addEventListener('change', function () {
        fetchInventarios(1);
    });

    // Adjuntar listeners iniciales cuando la página se carga
    attachDeleteListeners();
    attachPaginationListeners();
});