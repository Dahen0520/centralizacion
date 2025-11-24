document.addEventListener('DOMContentLoaded', function () {
    // Referencia al objeto de configuración pasado desde Blade
    const config = window.AppConfig || {};

    // Obtener variables dinámicas
    const asociacionesIndexRoute = config.asociacionesIndexRoute;
    const csrfToken = config.csrfToken;

    // Oculta la alerta de éxito después de 5 segundos con animación suave
    const successAlert = document.getElementById('success-alert');
    if (successAlert) {
        setTimeout(function() {
            successAlert.style.opacity = '0';
            successAlert.style.transform = 'translateY(-10px)';
            setTimeout(() => successAlert.remove(), 300);
        }, 5000);
    }

    const searchInput = document.getElementById('search-input');
    const filterTienda = document.getElementById('filter-tienda');
    const filterEstado = document.getElementById('filter-estado');
    const clearFiltersBtn = document.getElementById('clear-filters');
    const associationsTableBody = document.getElementById('associations-table-body');
    const paginationLinksContainer = document.getElementById('pagination-links');

    let searchTimeout;

    // Función para manejar eliminación con SweetAlert y AJAX
    function handleDeleteClick(e) {
        e.preventDefault();
        const form = e.target.closest('form');
        const url = form.action;
        const method = form.querySelector('input[name="_method"]').value;
        
        Swal.fire({
            title: '¿Confirmar eliminación?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(url, {
                    method: 'POST', // Usamos POST para la simulación de DELETE
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-HTTP-Method-Override': method // Laravel interpreta el método real aquí
                    },
                    body: JSON.stringify({ _method: method })
                })
                .then(response => {
                    // Intenta parsear JSON incluso si el estado no es 200
                    if (response.headers.get('content-type')?.includes('application/json')) {
                        return response.json().then(data => ({ status: response.status, data }));
                    }
                    // Manejar 204 No Content u otras respuestas sin JSON
                    if (response.status === 204) {
                         return { status: 200, data: { success: true, message: 'Registro eliminado exitosamente.' } };
                    }
                    throw new Error('Respuesta de red inesperada.');
                })
                .then(({ status, data }) => {
                    if (data.success) {
                        // Encontrar la fila y eliminarla con una animación
                        const row = form.closest('tr');
                        if (row) {
                            row.style.opacity = '0';
                            row.style.transform = 'translateY(-20px)';
                            setTimeout(() => row.remove(), 300);
                        }

                        // Mostrar mensaje de éxito
                        Swal.fire({
                            title: '¡Eliminado!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#3B82F6'
                        });

                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.message || 'No se pudo eliminar el registro.',
                            icon: 'error',
                            confirmButtonColor: '#3B82F6'
                        });
                    }
                    fetchAssociations(getCurrentPage()); // Recargar datos para actualizar la tabla
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error de conexión',
                        text: 'No se pudo completar la operación. ' + (error.message || ''),
                        icon: 'error',
                        confirmButtonColor: '#3B82F6'
                    });
                    fetchAssociations(getCurrentPage());
                });
            }
        });
    }

    function attachDeleteListeners() {
        document.querySelectorAll('.delete-btn').forEach(button => {
            // Clonar y reemplazar para evitar listeners duplicados tras AJAX
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);
            newButton.addEventListener('click', handleDeleteClick);
        });
    }

    function handlePaginationClick(e) {
        e.preventDefault();
        const url = e.target.closest('a')?.href;
        if (!url) return;
        
        const urlParams = new URLSearchParams(new URL(url).search);
        const page = urlParams.get('page');
        
        // Actualizar la URL de la ventana
        const newUrl = `${asociacionesIndexRoute}?page=${page}&search=${encodeURIComponent(searchInput.value)}&tienda_id=${filterTienda.value}&estado=${filterEstado.value}`;
        window.history.pushState({}, '', newUrl);
        
        fetchAssociations(page);
    }

    function attachPaginationListeners() {
        document.querySelectorAll('#pagination-links a').forEach(link => {
            link.removeEventListener('click', handlePaginationClick);
            link.addEventListener('click', handlePaginationClick);
        });
    }

    function fetchAssociations(page = 1) {
        const query = searchInput.value;
        const tiendaId = filterTienda.value;
        const estado = filterEstado.value;

        // Efecto de carga sutil
        associationsTableBody.style.opacity = '0.6';

        const url = `${asociacionesIndexRoute}?page=${page}&search=${encodeURIComponent(query)}&tienda_id=${tiendaId}&estado=${estado}`;

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            associationsTableBody.innerHTML = data.table_rows;
            paginationLinksContainer.innerHTML = data.pagination_links;
            
            attachDeleteListeners();
            attachPaginationListeners();
            
            // Restaurar opacidad
            associationsTableBody.style.opacity = '1';
        })
        .catch(error => {
            console.error('Error:', error);
            associationsTableBody.style.opacity = '1';
            
            Swal.fire({
                title: 'Error de conexión',
                text: 'No se pudieron cargar los datos. Verifica tu conexión.',
                icon: 'error',
                confirmButtonColor: '#3B82F6'
            });
        });
    }

    function getCurrentPage() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('page') || 1;
    }

    // Función para limpiar filtros
    function clearFilters() {
        searchInput.value = '';
        filterTienda.value = '';
        filterEstado.value = '';
        
        // Feedback visual
        clearFiltersBtn.innerHTML = '<i class="fas fa-check text-emerald-600"></i>';
        setTimeout(() => {
            clearFiltersBtn.innerHTML = '<i class="fas fa-eraser"></i>';
        }, 1000);

        // Recargar con filtros vacíos y página 1
        const newUrl = `${asociacionesIndexRoute}?page=1&search=&tienda_id=&estado=`;
        window.history.pushState({}, '', newUrl);
        fetchAssociations(1);
    }

    // Event listeners
    searchInput.addEventListener('keyup', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => fetchAssociations(1), 300);
    });

    filterTienda.addEventListener('change', () => fetchAssociations(1));
    filterEstado.addEventListener('change', () => fetchAssociations(1));
    clearFiltersBtn.addEventListener('click', clearFilters);

    // Inicializar (Adjuntar listeners y manejar cualquier carga inicial por URL)
    attachDeleteListeners();
    attachPaginationListeners();
    
    const initialPage = getCurrentPage();
    const initialQuery = searchInput.value || filterTienda.value || filterEstado.value;
    
    if (initialQuery || initialPage > 1) {
        // Si hay filtros o paginación activa, recargamos vía AJAX para asegurar el estado correcto
        fetchAssociations(initialPage);
    }
});