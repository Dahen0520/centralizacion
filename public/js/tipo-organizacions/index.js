document.addEventListener('DOMContentLoaded', function () {
    const config = window.AppConfig || {};
    const tiposOrganizacionIndexRoute = config.tiposOrganizacionIndexRoute;
    const csrfToken = config.csrfToken;
    const COLUMN_COUNT = config.columnCount || 3;
    const sessionSuccess = config.sessionSuccess;

    const searchInput = document.getElementById('search-input');
    const tiposOrganizacionTableBody = document.getElementById('tipos-organizacion-table-body');
    const paginationLinksContainer = document.getElementById('pagination-links');
    const noResultsMessage = document.getElementById('no-results-message');
    
    let searchTimeout;

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

    function handleDeleteClick(e) {
        e.preventDefault();
        const deleteButton = this;
        const form = deleteButton.closest('form');
        const tipoOrganizacionName = deleteButton.getAttribute('data-name');

        Swal.fire({
            title: '¿Eliminar ' + tipoOrganizacionName + '?',
            text: '¡Esta acción es irreversible! ¿Deseas continuar?',
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
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw new Error(err.message || 'Error al eliminar el tipo de organización'); });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire('¡Eliminado!', data.message, 'success');
                        fetchTiposOrganizacion(getCurrentPage()); 
                    } else {
                        Swal.fire('Error', data.message || 'No se pudo eliminar el tipo de organización.', 'error');
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
                window.history.pushState({}, '', `${tiposOrganizacionIndexRoute}?page=${page}&search=${encodeURIComponent(searchInput.value)}`);
                fetchTiposOrganizacion(page);
            });
        });
    }

    function fetchTiposOrganizacion(page = 1) {
        const query = searchInput.value;
        const url = `${tiposOrganizacionIndexRoute}?page=${page}&search=${encodeURIComponent(query)}`;

        tiposOrganizacionTableBody.innerHTML = `<tr><td colspan="${COLUMN_COUNT}" class="p-6 text-center text-blue-500 dark:text-blue-400"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando tipos de organización...</td></tr>`;
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
            tiposOrganizacionTableBody.innerHTML = data.table_rows;
            paginationLinksContainer.innerHTML = data.pagination_links;

            if (data.tipos_organizacion_count === 0) {
                noResultsMessage.classList.remove('hidden');
                tiposOrganizacionTableBody.innerHTML = '';
            } else {
                noResultsMessage.classList.add('hidden');
            }

            attachDeleteListeners();
            attachPaginationListeners();
        })
        .catch(error => {
            console.error('Error al buscar tipos de organización:', error);
            tiposOrganizacionTableBody.innerHTML = `<tr><td colspan="${COLUMN_COUNT}" class="p-6 text-center text-red-500 dark:text-red-400"><i class="fas fa-exclamation-triangle mr-2"></i> Error al cargar.</td></tr>`;
            Swal.fire('Error de Carga', 'No se pudieron cargar los tipos de organización. Inténtalo de nuevo. Detalles: ' + error.message, 'error');
        });
    }

    function getCurrentPage() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('page') || 1;
    }

    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetchTiposOrganizacion(1);
        }, 300);
    });

    attachDeleteListeners();
    attachPaginationListeners();
});