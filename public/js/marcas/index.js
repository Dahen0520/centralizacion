document.addEventListener('DOMContentLoaded', function () {
    
    const config = window.AppConfig || {};
    const marcasIndexRoute = config.marcasIndexRoute;
    const csrfToken = config.csrfToken;
    const COLUMN_COUNT = 7;

    const searchInput = document.getElementById('search-input');
    const marcasTableBody = document.getElementById('marcas-table-body');
    const paginationLinksContainer = document.getElementById('pagination-links');
    const noResultsMessage = document.getElementById('no-results-message');
    const estadoFilter = document.getElementById('estado-filter');

    let searchTimeout;

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

    function getCurrentStatus() {
        return estadoFilter.value;
    }

    function fetchMarcas(page = 1) {
        const query = searchInput.value;
        const status = getCurrentStatus();
        const url = `${marcasIndexRoute}?page=${page}&search=${encodeURIComponent(query)}&estado=${status}`;

        marcasTableBody.innerHTML = `<tr><td colspan="${COLUMN_COUNT}" class="p-6 text-center text-blue-500 dark:text-blue-400"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando marcas...</td></tr>`;
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
            window.history.pushState({}, '', url);

            marcasTableBody.innerHTML = data.table_rows;
            paginationLinksContainer.innerHTML = data.pagination_links;
            
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
            marcasTableBody.innerHTML = `<tr><td colspan="${COLUMN_COUNT}" class="p-6 text-center text-red-500 dark:text-red-400"><i class="fas fa-exclamation-triangle mr-2"></i> Error al cargar.</td></tr>`;
            Swal.fire('Error de Carga', 'No se pudieron cargar las marcas. Detalles: ' + error.message, 'error');
        });
    }

    function getCurrentPage() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('page') || 1;
    }

    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetchMarcas(1); 
        }, 300);
    });

    estadoFilter.addEventListener('change', function() {
        fetchMarcas(1);
    });

    function attachDeleteListeners() {
        const deleteButtons = document.querySelectorAll('.delete-btn');
        deleteButtons.forEach(button => {
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);
            newButton.addEventListener('click', handleDeleteClick);
        });
    }

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

    function attachPaginationListeners() {
        const links = paginationLinksContainer.querySelectorAll('a');
        links.forEach(link => {
            const newLink = link.cloneNode(true);
            link.parentNode.replaceChild(newLink, link);
            newLink.addEventListener('click', function(e) {
                e.preventDefault();
                const url = new URL(this.href);
                const page = url.searchParams.get('page');
                
                const newUrl = `${marcasIndexRoute}?page=${page}&search=${encodeURIComponent(searchInput.value)}&estado=${getCurrentStatus()}`;
                window.history.pushState({}, '', newUrl);
                fetchMarcas(page);
            });
        });
    }

    attachDeleteListeners();
    attachPaginationListeners();
});