document.addEventListener('DOMContentLoaded', function () {
    
    // --- 1. CONFIGURACIÓN INICIAL (USANDO AppConfig) ---
    const config = window.AppConfig || {};
    const impuestosIndexRoute = config.impuestosIndexRoute;
    const csrfToken = config.csrfToken;
    const COLUMN_COUNT = config.columnCount || 4; 

    // --- Elementos DOM ---
    const searchInput = document.getElementById('search-input');
    const impuestosTableBody = document.getElementById('impuestos-table-body');
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
        const impuestoName = this.getAttribute('data-name') || 'este impuesto';

        Swal.fire({
            title: '¿Eliminar ' + impuestoName + '?',
            text: '¡Esta acción es irreversible! Asegúrate de que no esté asignado a ningún producto.',
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
                    method: 'POST', 
                    body: new FormData(form),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        // Intenta capturar el mensaje de error del servidor (ej. 409 Conflict)
                        return response.json().then(err => { 
                            throw new Error(err.message || `Error ${response.status}: No se pudo eliminar el impuesto.`); 
                        });
                    }
                    // Si es éxito, asumimos que devuelve JSON con data.success
                    return response.json(); 
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire('¡Eliminado!', data.message, 'success');
                        fetchImpuestos(getCurrentPage());
                    } else {
                        Swal.fire('Error', data.message || 'No se pudo eliminar el impuesto.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error en fetch handleDeleteClick:', error);
                    Swal.fire('Error', error.message, 'error'); 
                });
            }
        });
    }

    // --- 3. LÓGICA AJAX PARA BÚSQUEDA Y PAGINACIÓN ---

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
        const newUrl = `${impuestosIndexRoute}?page=${page}&search=${encodeURIComponent(searchInput.value)}`;
        window.history.pushState({}, '', newUrl);

        fetchImpuestos(page);
    }

    function attachPaginationListeners() {
        const links = paginationLinksContainer.querySelectorAll('a');
        links.forEach(link => {
            const newLink = link.cloneNode(true);
            link.parentNode.replaceChild(newLink, link);
            newLink.addEventListener('click', handlePaginationClick);
        });
    }

    function fetchImpuestos(page = 1) {
        const query = searchInput.value;

        // Construcción de la URL de búsqueda/filtrado (usando variable JS de ruta)
        const url = `${impuestosIndexRoute}?page=${page}&search=${encodeURIComponent(query)}`;

        // Mostrar estado de carga
        impuestosTableBody.innerHTML = `<tr><td colspan="${COLUMN_COUNT}" class="p-6 text-center text-blue-500 dark:text-blue-400"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando impuestos...</td></tr>`;
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

            impuestosTableBody.innerHTML = data.table_rows;
            paginationLinksContainer.innerHTML = data.pagination_links;
            
            // Manejar el mensaje de no resultados
            if (data.impuestos_count === 0) {
                noResultsMessage?.classList.remove('hidden');
                impuestosTableBody.innerHTML = '';
            } else {
                noResultsMessage?.classList.add('hidden');
            }
            
            attachDeleteListeners();
            attachPaginationListeners();
        })
        .catch(error => {
            console.error('Error al buscar impuestos:', error);
            impuestosTableBody.innerHTML = `<tr><td colspan="${COLUMN_COUNT}" class="p-6 text-center text-red-500 dark:text-red-400"><i class="fas fa-exclamation-triangle mr-2"></i> Error al cargar los impuestos.</td></tr>`;
            Swal.fire('Error de Carga', 'No se pudieron cargar los impuestos. Detalles: ' + error.message, 'error');
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
            fetchImpuestos(1); 
        }, 300);
    });

    // Adjuntar listeners iniciales cuando la página se carga
    attachDeleteListeners();
    attachPaginationListeners();
});