document.addEventListener('DOMContentLoaded', function () {
    
    const config = window.AppConfig || {};
    const rangosIndexRoute = config.rangosIndexRoute;
    const csrfToken = config.csrfToken;
    const COLUMN_COUNT = 6; 

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
            fetchUrl = `${rangosIndexRoute}?page=${urlOrPage}&status=${status}`;
        }

        tableBody.innerHTML = `
            <tr>
                <td colspan="${COLUMN_COUNT}" class="px-6 py-12 text-center">
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
                        <td colspan="${COLUMN_COUNT}" class="px-6 py-12 text-center">
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

    function handleDelete(e) {
        e.preventDefault();
        const form = this.closest('form');
        const cai = this.getAttribute('data-name');
        
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
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Error en la eliminación.');
                        });
                    }
                    return response.json(); 
                })
                .then(data => {
                    Swal.fire({
                        title: '¡Eliminado!',
                        text: data.message,
                        icon: 'success',
                        customClass: {
                            popup: 'rounded-2xl',
                            confirmButton: 'rounded-lg'
                        }
                    });
                    
                    window.location.href = currentUrl; 
                })
                .catch(error => {
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