function toggleDetalle(ventaId) {
    const detalleRow = document.getElementById('detalle-row-' + ventaId);
    const icon = document.getElementById('toggle-icon-' + ventaId);

    if (detalleRow.style.display === 'none' || !detalleRow.style.display) {
        detalleRow.style.display = 'table-row';
        icon.classList.remove('fa-chevron-right');
        icon.classList.add('fa-chevron-down');
    } else {
        detalleRow.style.display = 'none';
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-right');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    
    const config = window.AppConfig || {};
    const ventasIndexRoute = config.ventasIndexRoute;
    
    const toggleBtn = document.getElementById('toggle-filtros');
    const filtrosContainer = document.getElementById('filtros-container');
    const iconToggle = document.getElementById('icon-toggle');
    const form = document.getElementById('filtro-ventas-form');
    const resultadosDiv = document.getElementById('resultados-ventas');
    const filtrosActivosDiv = document.getElementById('filtros-activos');
    const badgesFiltrosDiv = document.getElementById('badges-filtros');

    let fetchController = null;
    let debounceTimer = null;

    const successMessage = config.sessionSuccess;
    if (successMessage) {
        Swal.fire({
            icon: 'success',
            title: 'Operación Exitosa',
            text: successMessage,
            showConfirmButton: false,
            timer: 3000,
            toast: true,
            position: 'top-end',
            customClass: {
                popup: 'rounded-xl shadow-2xl'
            }
        });
    }

    toggleBtn.addEventListener('click', function() {
        const isHidden = filtrosContainer.style.maxHeight === '0px' || !filtrosContainer.style.maxHeight;

        if (!isHidden) {
            filtrosContainer.style.maxHeight = '0px';
            filtrosContainer.style.opacity = '0';
            iconToggle.classList.add('rotate-180');
            toggleBtn.querySelector('span').textContent = 'Mostrar filtros';
        } else {
            filtrosContainer.style.maxHeight = filtrosContainer.scrollHeight + 100 + 'px'; 
            filtrosContainer.style.opacity = '1';
            iconToggle.classList.remove('rotate-180');
            toggleBtn.querySelector('span').textContent = 'Ocultar filtros';
        }
    });

    function bindDeleteButtons() {
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.removeEventListener('click', handleDelete);
            button.addEventListener('click', handleDelete);
        });
    }

    function handleDelete() {
        const form = this.closest('form');
        const itemName = this.getAttribute('data-name') || 'esta venta';

        Swal.fire({
            title: '¿Anular ' + itemName + '?',
            html: '<p class="text-sm text-gray-600 mt-2">Esta acción es <strong>irreversible</strong> y requiere ajuste manual de inventario.</p>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Sí, Anular Venta',
            cancelButtonText: 'Cancelar',
            customClass: {
                popup: 'rounded-xl',
                confirmButton: 'rounded-lg font-semibold',
                cancelButton: 'rounded-lg font-semibold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }

    bindDeleteButtons();

    function aplicarFiltros(url, immediate = false) {
        clearTimeout(debounceTimer);
        
        const applyFilter = () => {
            if (fetchController) {
                fetchController.abort();
            }
            fetchController = new AbortController();

            const formData = new FormData(form);
            formData.delete('export'); 
            
            const params = new URLSearchParams(formData).toString();
            const fetchUrl = url ? url : `${ventasIndexRoute}?${params}`;

            resultadosDiv.style.opacity = '0.4';
            resultadosDiv.style.transform = 'scale(0.98)';

            fetch(fetchUrl, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                signal: fetchController.signal
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            })
            .then(html => {
                resultadosDiv.innerHTML = html;
                resultadosDiv.style.opacity = '1';
                resultadosDiv.style.transform = 'scale(1)';
                bindPaginationListeners();
                bindDeleteButtons();
                actualizarFiltrosActivos();
                fetchController = null;
                
                if (filtrosContainer.style.maxHeight && filtrosContainer.style.maxHeight !== '0px') {
                    filtrosContainer.style.maxHeight = filtrosContainer.scrollHeight + 100 + 'px';
                }
            })
            .catch(error => {
                if (error.name !== 'AbortError') {
                    console.error('Error al cargar filtros:', error);
                    resultadosDiv.innerHTML = `
                        <div class="text-center p-16 bg-red-50 dark:bg-red-900/20 rounded-xl border-2 border-red-200 dark:border-red-800">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 dark:bg-red-900 rounded-full mb-4">
                                <i class="fas fa-exclamation-triangle text-3xl text-red-600 dark:text-red-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-red-700 dark:text-red-300 mb-2">Error al cargar el historial</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Por favor, recarga la p\u00e1gina e intenta nuevamente</p>
                        </div>
                    `;
                    resultadosDiv.style.opacity = '1';
                    resultadosDiv.style.transform = 'scale(1)';
                }
            });
        };

        if (immediate) {
            applyFilter();
        } else {
            debounceTimer = setTimeout(applyFilter, 500);
        }
    }

    function actualizarFiltrosActivos() {
        const formData = new FormData(form);
        const badges = [];
        
        formData.forEach((value, key) => {
            if (value && key !== '_token' && key !== 'filter') {
                let label = '';
                let displayValue = value;
                let icon = '';
                
                switch(key) {
                    case 'tienda_id':
                        const tiendaSelect = document.getElementById('tienda_id');
                        displayValue = tiendaSelect.options[tiendaSelect.selectedIndex].text;
                        label = 'Tienda';
                        icon = 'fa-store';
                        break;
                    case 'fecha_inicio':
                        label = 'Desde';
                        displayValue = new Date(value).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });
                        icon = 'fa-calendar-alt';
                        break;
                    case 'fecha_fin':
                        label = 'Hasta';
                        displayValue = new Date(value).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });
                        icon = 'fa-calendar-check';
                        break;
                }
                
                if (displayValue !== 'Todas las Tiendas' && displayValue !== '') {
                    badges.push(`
                        <div class="inline-flex items-center gap-2 px-4 py-2 
                                    bg-white dark:bg-gray-800 border-2 border-indigo-200 dark:border-indigo-700
                                    text-gray-700 dark:text-gray-200 rounded-lg shadow-sm">
                            <i class="fas ${icon} text-indigo-600 dark:text-indigo-400 text-sm"></i>
                            <span class="text-sm"><span class="font-semibold">${label}:</span> ${displayValue}</span>
                        </div>
                    `);
                }
            }
        });

        if (badges.length > 0) {
            badgesFiltrosDiv.innerHTML = badges.join('');
            filtrosActivosDiv.classList.remove('hidden');
        } else {
            filtrosActivosDiv.classList.add('hidden');
        }
    }

    document.querySelectorAll('.filter-input').forEach(input => {
        input.addEventListener('change', () => aplicarFiltros());
    });

    function bindPaginationListeners() {
        resultadosDiv.querySelectorAll('.pagination a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                aplicarFiltros(this.href, true);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });
    }

    document.getElementById('limpiar-filtros').addEventListener('click', function() {
        form.reset();
        aplicarFiltros();
    });

    bindPaginationListeners();
    actualizarFiltrosActivos();
    
    if (filtrosContainer.clientHeight > 0) {
        filtrosContainer.style.maxHeight = filtrosContainer.scrollHeight + 100 + 'px';
    }
});