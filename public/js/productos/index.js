document.addEventListener('DOMContentLoaded', function () {
    
    const config = window.AppConfig || {};
    const productosIndexRoute = config.productosIndexRoute;
    const csrfToken = config.csrfToken;
    const COLUMN_COUNT = 9;

    const searchInput = document.getElementById('search-input');
    const categoriaFilter = document.getElementById('categoria-filter');
    const estadoFilter = document.getElementById('estado-filter');
    const productosTableBody = document.getElementById('productos-table-body');
    const paginationLinksContainer = document.getElementById('pagination-links');

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

    function fetchProductos(page = 1) {
        const query = searchInput.value;
        const categoria = categoriaFilter.value;
        const estado = estadoFilter.value;

        const url = `${productosIndexRoute}?page=${page}&search=${encodeURIComponent(query)}&categoria=${categoria}&estado=${estado}`;

        productosTableBody.innerHTML = `<tr><td colspan="${COLUMN_COUNT}" class="p-6 text-center text-blue-500 dark:text-blue-400"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando productos...</td></tr>`;
        document.getElementById('no-results-message')?.classList.add('hidden');

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

            productosTableBody.innerHTML = data.table_rows;
            paginationLinksContainer.innerHTML = data.pagination_links;
            
            if (productosTableBody.children.length === 0 || productosTableBody.querySelector('.no-results-row')) {
                document.getElementById('no-results-message')?.classList.remove('hidden');
            } else {
                document.getElementById('no-results-message')?.classList.add('hidden');
            }
            
            attachDeleteListeners();
            attachPaginationListeners();
        })
        .catch(error => {
            console.error('Error al buscar productos:', error);
            productosTableBody.innerHTML = `<tr><td colspan="${COLUMN_COUNT}" class="p-6 text-center text-red-500 dark:text-red-400"><i class="fas fa-exclamation-triangle mr-2"></i> Error al cargar.</td></tr>`;
            Swal.fire('Error de Carga', 'No se pudieron cargar los productos. Detalles: ' + error.message, 'error');
        });
    }

    function getCurrentPage() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('page') || 1;
    }

    function handleDeleteClick(e) {
        e.preventDefault();
        const form = this.closest('form');
        const productName = this.getAttribute('data-name') || 'este producto';

        Swal.fire({
            title: '¿Eliminar ' + productName + '?',
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
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw new Error(err.message || 'Error al eliminar el producto'); });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire('¡Eliminado!', data.message, 'success');
                        fetchProductos(getCurrentPage());
                    } else {
                        Swal.fire('Error', data.message || 'No se pudo eliminar el producto.', 'error');
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
        fetchProductos(page);
    }

    function attachPaginationListeners() {
        const links = paginationLinksContainer.querySelectorAll('a');
        links.forEach(link => {
            const newLink = link.cloneNode(true);
            link.parentNode.replaceChild(newLink, link);
            newLink.addEventListener('click', handlePaginationClick);
        });
    }
    
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetchProductos(1); 
        }, 300);
    });

    categoriaFilter.addEventListener('change', function() {
        fetchProductos(1);
    });

    estadoFilter.addEventListener('change', function() {
        fetchProductos(1);
    });

    attachDeleteListeners();
    attachPaginationListeners();
});