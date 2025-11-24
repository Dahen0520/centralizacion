document.addEventListener('DOMContentLoaded', function () {
    
    const config = window.AppConfig || {};
    const tiendasIndexRoute = config.tiendasIndexRoute;
    const csrfToken = config.csrfToken;
    const COLUMN_COUNT = config.columnCount || 6;
    const sessionSuccess = config.sessionSuccess;

    const searchInput = document.getElementById('search-input');
    const tiendasTableBody = document.getElementById('tiendas-table-body');
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
        const form = this.closest('form');
        const tiendaName = this.getAttribute('data-name') || 'esta tienda';

        Swal.fire({
            title: '¿Eliminar ' + tiendaName + '?',
            text: '¡No podrás revertir esto!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444', 
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Sí, eliminarla',
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
                        return response.json().then(err => { throw new Error(err.message || 'Error al eliminar la tienda'); });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire(
                            '¡Eliminada!',
                            data.message,
                            'success'
                        );
                        fetchTiendas(getCurrentPage());
                    } else {
                        Swal.fire(
                            'Error',
                            data.message || 'No se pudo eliminar la tienda.',
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

    function attachDeleteListeners() {
        const deleteButtons = document.querySelectorAll('.delete-btn');
        deleteButtons.forEach(button => {
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);
            newButton.addEventListener('click', handleDeleteClick);
        });
    }

    function handlePaginationClick(e) {
        e.preventDefault();
        const url = new URL(this.href);
        const page = url.searchParams.get('page');
        
        const newUrl = `${tiendasIndexRoute}?page=${page}&search=${encodeURIComponent(searchInput.value)}`;
        window.history.pushState({}, '', newUrl);
        fetchTiendas(page);
    }

    function attachPaginationListeners() {
        const links = paginationLinksContainer.querySelectorAll('a');
        links.forEach(link => {
            const newLink = link.cloneNode(true);
            link.parentNode.replaceChild(newLink, link);
            newLink.addEventListener('click', handlePaginationClick);
        });
    }

    function fetchTiendas(page = 1) {
        const query = searchInput.value;
        const url = `${tiendasIndexRoute}?page=${page}&search=${encodeURIComponent(query)}`;

        tiendasTableBody.innerHTML = `<tr><td colspan="${COLUMN_COUNT}" class="p-6 text-center text-blue-500 dark:text-blue-400"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando tiendas...</td></tr>`;
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

            tiendasTableBody.innerHTML = data.table_rows;
            paginationLinksContainer.innerHTML = data.pagination_links;

            const hasRows = tiendasTableBody.querySelector('tr') && !tiendasTableBody.querySelector('tr td[colspan="6"]');
            
            if (!hasRows) {
                noResultsMessage.classList.remove('hidden');
            } else {
                noResultsMessage.classList.add('hidden');
            }

            attachDeleteListeners();
            attachPaginationListeners();
        })
        .catch(error => {
            console.error('Error al buscar tiendas:', error);
            tiendasTableBody.innerHTML = `<tr><td colspan="${COLUMN_COUNT}" class="p-6 text-center text-red-500 dark:text-red-400"><i class="fas fa-exclamation-triangle mr-2"></i> Error al cargar.</td></tr>`;
            Swal.fire('Error de Carga', 'No se pudieron cargar las tiendas. Detalles: ' + error.message, 'error');
        });
    }

    function getCurrentPage() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('page') || 1;
    }

    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetchTiendas(1);
        }, 300);
    });

    attachDeleteListeners();
    attachPaginationListeners();
});