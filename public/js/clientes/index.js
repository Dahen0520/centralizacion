document.addEventListener('DOMContentLoaded', function () {
    // Referencia al objeto de configuración pasado desde Blade
    const config = window.AppConfig || {};
    
    const searchInput = document.getElementById('search-input');
    const clientesTableBody = document.getElementById('clientes-table-body');
    const paginationLinksContainer = document.getElementById('pagination-links');
    const noResultsMessage = document.getElementById('no-results-message');
    
    // Variables dinámicas pasadas desde Blade
    const csrfToken = config.csrfToken;
    const clientesIndexRoute = config.clientesIndexRoute;
    
    let searchTimeout;

    // --- MANEJO DE EVENTOS ---
    
    function handleDeleteClick(e) {
        e.preventDefault();
        const form = this.closest('form');
        const deleteButton = this;
        const clienteName = deleteButton.getAttribute('data-name') || 'este cliente'; 

        Swal.fire({
            title: '¿Eliminar ' + clienteName + '?',
            text: '¡Esta acción es irreversible! Se verificará que no tenga transacciones asociadas.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444', 
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Sí, ¡Eliminar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                
                // 1. Mostrar estado de cargando
                Swal.fire({
                    title: 'Eliminando...',
                    text: 'Procesando la solicitud, por favor espere.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // 2. Enviar solicitud DELETE
                fetch(form.action, {
                    method: 'POST', // Usamos POST para rutas DELETE en formularios Blade con _method
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json'
                    },
                    // El cuerpo DEBE incluir _method: DELETE
                    body: JSON.stringify({
                        _method: 'DELETE'
                    })
                })
                .then(response => {
                    // Intentamos leer el JSON, incluso si hay un error HTTP.
                    if (response.headers.get('content-type')?.includes('application/json')) {
                        return response.json().then(data => ({ status: response.status, data }));
                    }
                    // Si es 204 No Content, asumimos éxito y devolvemos un objeto de datos.
                    if (response.status === 204) {
                         return { status: 200, data: { success: true, message: `Cliente ${clienteName} eliminado.` } };
                    }
                    // Si la respuesta es OK pero no JSON (no debería pasar), devolvemos éxito.
                    if (response.ok) {
                         return { status: 200, data: { success: true, message: `Cliente ${clienteName} eliminado.` } };
                    }
                    
                    // Forzamos error si el estado es malo y no pudimos leer el JSON.
                    throw new Error(`Error ${response.status}: Respuesta inesperada.`);
                })
                .then(({ status, data }) => {
                    // 3. Evaluar el resultado
                    if (data.success) {
                        // Éxito: Recargar datos de la tabla y mostrar SweetAlert de éxito
                        Swal.fire({
                            icon: 'success',
                            title: '¡Eliminado!',
                            text: data.message,
                            timer: 3000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                        fetchClientes(getCurrentPage());
                    } else {
                        // Fallo (Ej: Error 422 del controlador por restricciones)
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al Eliminar',
                            text: data.message || 'No se pudo eliminar el cliente.',
                            confirmButtonColor: '#EF4444'
                        });
                        // Refrescar los datos por si acaso (si hubo un error de restricción)
                        fetchClientes(getCurrentPage());
                    }
                })
                .catch(error => {
                    // 4. Fallo de conexión o error no manejado
                    console.error('Error al eliminar:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Conexión',
                        text: error.message || 'Ocurrió un error de red o servidor.',
                        confirmButtonColor: '#EF4444'
                    });
                    fetchClientes(getCurrentPage());
                });
            }
        });
    }

    function attachDeleteListeners() {
        // Adjunta listeners a todos los botones de eliminar
        document.querySelectorAll('.delete-btn').forEach(button => {
            // Clonar y reemplazar para evitar listeners duplicados al recargar AJAX
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);
            newButton.addEventListener('click', handleDeleteClick);
        });
    }

    // --- FUNCIONES DE AJAX Y PAGINACIÓN ---

    function attachPaginationListeners() {
        // Adjunta listeners a los nuevos enlaces de paginación
        paginationLinksContainer.querySelectorAll('a').forEach(link => {
            link.removeEventListener('click', handlePaginationClick);
            link.addEventListener('click', handlePaginationClick);
        });
    }
    
    function handlePaginationClick(e) {
        e.preventDefault();
        const url = new URL(this.href);
        const page = url.searchParams.get('page');
        
        const newUrl = `${clientesIndexRoute}?page=${page}&search=${encodeURIComponent(searchInput.value)}`;
        window.history.pushState({}, '', newUrl);
        fetchClientes(page);
    }

    function fetchClientes(page = 1) {
        const query = searchInput.value;
        const url = `${clientesIndexRoute}?page=${page}&search=${encodeURIComponent(query)}`;

        clientesTableBody.innerHTML = '<tr><td colspan="6" class="p-6 text-center text-indigo-500 dark:text-indigo-400"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando clientes...</td></tr>';
        noResultsMessage.classList.add('hidden');

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            clientesTableBody.innerHTML = data.table_rows;
            paginationLinksContainer.innerHTML = data.pagination_links;

            // Mostrar el mensaje de no resultados solo si no hay datos Y hay una búsqueda activa.
            if (data.clientes_count === 0 && query) {
                noResultsMessage.classList.remove('hidden');
            } else {
                noResultsMessage.classList.add('hidden');
            }

            attachDeleteListeners();
            attachPaginationListeners();
        })
        .catch(error => {
            console.error('Error al buscar clientes:', error);
            clientesTableBody.innerHTML = '<tr><td colspan="6" class="p-6 text-center text-red-500 dark:text-red-400"><i class="fas fa-exclamation-triangle mr-2"></i> Error al cargar los clientes.</td></tr>';
        });
    }

    function getCurrentPage() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('page') || 1;
    }
    
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetchClientes(1); 
        }, 300);
    });

    if (searchInput.value || getCurrentPage() > 1) {
         // La función fetchClientes() se encarga de re-adjuntar listeners
         fetchClientes(getCurrentPage());
    } else {
         // Si es la carga inicial sin AJAX (primer acceso), solo adjuntamos los listeners
         attachDeleteListeners();
         attachPaginationListeners();
    }
});