document.addEventListener('DOMContentLoaded', function () {
    // Referencia al objeto de configuración pasado desde Blade
    const config = window.AppConfig || {};
    
    // Muestra mensaje de éxito si existe
    const successMessage = config.successMessage;
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

    const searchInput = document.getElementById('search-input');
    const categoriasTableBody = document.getElementById('categorias-table-body');
    const paginationLinksContainer = document.getElementById('pagination-links');
    const noResultsMessage = document.getElementById('no-results-message');

    const csrfToken = config.csrfToken;
    const categoriasIndexRoute = config.categoriasIndexRoute;
    let searchTimeout;

    function handleDeleteClick(e) {
        e.preventDefault();
        const form = this.closest('form');
        const categoriaName = this.getAttribute('data-name') || 'esta categoría'; 

        Swal.fire({
            title: '¿Eliminar ' + categoriaName + '?',
            text: '¡Esta acción es irreversible! Se eliminarán las subcategorías asociadas.',
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
                        return response.json().then(err => { throw new Error(err.message || 'Error al eliminar la categoría'); });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire('¡Eliminada!', data.message, 'success');
                        fetchCategorias(getCurrentPage()); 
                    } else {
                        Swal.fire('Error', data.message || 'No se pudo eliminar la categoría.', 'error');
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
                
                const newUrl = `${categoriasIndexRoute}?page=${page}&search=${encodeURIComponent(searchInput.value)}`;
                window.history.pushState({}, '', newUrl);
                fetchCategorias(page);
            });
        });
    }

    function fetchCategorias(page = 1) {
        const query = searchInput.value;
        const url = `${categoriasIndexRoute}?page=${page}&search=${encodeURIComponent(query)}`;

        categoriasTableBody.innerHTML = '<tr><td colspan="4" class="p-6 text-center text-blue-500 dark:text-blue-400"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando categorías...</td></tr>';
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
            categoriasTableBody.innerHTML = data.table_rows;
            paginationLinksContainer.innerHTML = data.pagination_links;

            if (data.categorias_count === 0) {
                noResultsMessage.classList.remove('hidden');
                categoriasTableBody.innerHTML = '';
            } else {
                noResultsMessage.classList.add('hidden');
            }

            attachDeleteListeners();
            attachPaginationListeners();
        })
        .catch(error => {
            console.error('Error al buscar categorías:', error);
            categoriasTableBody.innerHTML = '<tr><td colspan="4" class="p-6 text-center text-red-500 dark:text-red-400"><i class="fas fa-exclamation-triangle mr-2"></i> Error al cargar.</td></tr>';
            Swal.fire('Error de Carga', 'No se pudieron cargar las categorías. Inténtalo de nuevo. Detalles: ' + error.message, 'error');
        });
    }

    function getCurrentPage() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('page') || 1;
    }

    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetchCategorias(1);
        }, 300);
    });

    const initialSearch = searchInput.value;
    if (categoriasIndexRoute) {
        window.history.replaceState({}, '', `${categoriasIndexRoute}?search=${encodeURIComponent(initialSearch)}`);
    }
    
    attachDeleteListeners();
    attachPaginationListeners();
});