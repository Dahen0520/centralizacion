document.addEventListener('DOMContentLoaded', function() {
    
    const config = window.AppConfig || {};
    const apiEndpoints = config.apiEndpoints;
    
    const tiendaSelect = document.getElementById('tienda_id');
    const empresaSelect = document.getElementById('empresa_id');
    const marcaSelect = document.getElementById('marca_id');
    
    const oldTiendaId = config.oldTiendaId;
    const oldEmpresaId = config.oldEmpresaId;
    const oldMarcaId = config.oldMarcaId;
    
    function resetSelect(selectElement, defaultMessage) {
        selectElement.innerHTML = `<option value="">${defaultMessage}</option>`;
        selectElement.disabled = true;
        selectElement.classList.remove('bg-white', 'text-gray-900', 'dark:bg-gray-700', 'dark:text-gray-100');
        selectElement.classList.add('bg-gray-100', 'text-gray-500', 'dark:bg-gray-700', 'dark:text-gray-400');
    }

    function enableSelect(selectElement, message) {
         selectElement.disabled = false;
         selectElement.classList.remove('bg-gray-100', 'text-gray-500', 'dark:bg-gray-700', 'dark:text-gray-400');
         selectElement.classList.add('bg-white', 'text-gray-900', 'dark:bg-gray-700', 'dark:text-gray-100');
         if (message) {
            selectElement.innerHTML = `<option value="">${message}</option>` + selectElement.innerHTML;
         }
    }

    async function fetchEmpresas(tiendaId) {
        resetSelect(empresaSelect, 'Cargando Empresas...');
        resetSelect(marcaSelect, 'Seleccione la Tienda y Empresa');

        if (!tiendaId) {
            resetSelect(empresaSelect, '2. Seleccione la Tienda primero');
            return;
        }

        try {
            const url = apiEndpoints.fetchEmpresas(tiendaId);
            const response = await fetch(url);
            
            if (!response.ok) {
                throw new Error('Error al cargar las empresas. Código: ' + response.status);
            }
            
            const empresas = await response.json();
            let options = '';
            
            if (empresas.length === 0) {
                resetSelect(empresaSelect, 'No hay Empresas asociadas a esta Tienda');
            } else {
                empresas.forEach(empresa => {
                    const isSelected = oldEmpresaId == empresa.id ? 'selected' : '';
                    options += `<option value="${empresa.id}" ${isSelected}>${empresa.nombre_negocio}</option>`;
                });
                empresaSelect.innerHTML = options;
                enableSelect(empresaSelect, '2. Seleccione una Empresa');
                
                if (oldEmpresaId && empresaSelect.value == oldEmpresaId) {
                   fetchMarcas(oldEmpresaId, tiendaId);
                }
            }
            
        } catch (error) {
            console.error('Fetch Error Empresas:', error);
            resetSelect(empresaSelect, 'Error al cargar: Intente recargar.');
        }
    }

    async function fetchMarcas(empresaId, tiendaId) {
        resetSelect(marcaSelect, 'Cargando Productos...');

        if (!empresaId || !tiendaId) {
            resetSelect(marcaSelect, '3. Seleccione la Empresa primero');
            return;
        }
        
        try {
            const url = apiEndpoints.fetchMarcas(empresaId, tiendaId);
            const response = await fetch(url);
            
            if (!response.ok) {
                throw new Error('Error al cargar los productos. Código: ' + response.status);
            }
            
            const marcas = await response.json();
            let options = '';
            
            if (marcas.length === 0) {
                resetSelect(marcaSelect, 'No hay productos nuevos disponibles de esta Empresa para esta Tienda');
            } else {
                marcas.forEach(marca => {
                    const productoNombre = marca.producto ? marca.producto.nombre : 'Producto sin nombre';
                    const isSelected = oldMarcaId == marca.id ? 'selected' : '';
                    options += `<option value="${marca.id}" ${isSelected}>${productoNombre} (Código: ${marca.codigo_marca})</option>`;
                });
                marcaSelect.innerHTML = options;
                enableSelect(marcaSelect, '3. Seleccione un Producto (Marca)');
            }
            
        } catch (error) {
            console.error('Fetch Error Marcas:', error);
            resetSelect(marcaSelect, 'Error al cargar. Intente recargar.');
        }
    }

    tiendaSelect.addEventListener('change', (e) => {
        resetSelect(marcaSelect, 'Seleccione la Empresa primero'); 
        fetchEmpresas(e.target.value);
    });

    empresaSelect.addEventListener('change', (e) => {
        const tiendaId = tiendaSelect.value;
        const empresaId = e.target.value;
        fetchMarcas(empresaId, tiendaId);
    });

    if (oldTiendaId) {
        fetchEmpresas(oldTiendaId);
    }

    const sessionSuccess = config.sessionSuccess;
    if (sessionSuccess) {
        Swal.fire({
            icon: 'success',
            title: '¡Creación Exitosa!',
            text: sessionSuccess,
            showConfirmButton: false,
            timer: 3000,
            toast: true,
            position: 'top-end'
        });
    }
});