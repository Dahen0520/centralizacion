<x-app-layout>
    <x-slot name="header">
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <div class="py-12">
        <div class="w-full max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                
                {{-- HEADER MODERNO --}}
                <div class="bg-gradient-to-r from-emerald-600 to-teal-600 dark:from-emerald-700 dark:to-teal-700 px-8 py-6">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                        <div class="flex items-center gap-4">
                            <div class="bg-white/20 backdrop-blur-sm p-4 rounded-xl">
                                <i class="fas fa-chart-line text-3xl text-white"></i>
                            </div>
                            <div class="text-white">
                                <h3 class="text-2xl font-bold tracking-tight">Registro de Ventas</h3>
                                <p class="text-emerald-100 text-sm mt-1">Gestiona y consulta el historial completo de transacciones</p>
                            </div>
                        </div>
                        
                        <a href="{{ route('ventas.pos') }}" 
                           class="group inline-flex items-center gap-3 px-6 py-3.5 
                                  bg-white text-emerald-700 font-semibold rounded-xl 
                                  shadow-lg hover:shadow-xl hover:bg-emerald-50
                                  transform hover:scale-105 transition-all duration-300">
                            <i class="fas fa-cash-register text-xl group-hover:rotate-12 transition-transform duration-300"></i>
                            <span>Punto de Venta</span>
                        </a>
                    </div>
                </div>

                {{-- PANEL DE FILTROS AVANZADO --}}
                <div class="px-8 py-8 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="mb-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-1 h-8 bg-gradient-to-b from-indigo-600 to-purple-600 rounded-full"></div>
                                <h4 class="text-lg font-bold text-gray-800 dark:text-gray-100">Filtros de Búsqueda</h4>
                            </div>
                            <button type="button" id="toggle-filtros" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 font-medium flex items-center gap-2">
                                <i class="fas fa-chevron-up transition-transform duration-300" id="icon-toggle"></i>
                                <span>Ocultar filtros</span>
                            </button>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 ml-7">Refina tu búsqueda utilizando los siguientes criterios</p>
                    </div>
                    
                    <form id="filtro-ventas-form" method="GET" action="{{ route('ventas.index') }}">
                        <div id="filtros-container" class="space-y-6">
                            
                            {{-- FILTRO TIENDA - DESTACADO --}}
                            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-300">
                                <div class="flex items-start gap-4">
                                    <div class="mt-1 bg-gradient-to-br from-blue-500 to-indigo-600 p-3 rounded-lg shadow-md">
                                        <i class="fas fa-store text-white text-xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <label for="tienda_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                            Seleccionar Tienda
                                        </label>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Filtra las ventas por ubicación específica</p>
                                        <select name="tienda_id" id="tienda_id" 
                                                class="filter-input block w-full rounded-lg border-2 border-gray-300 dark:border-gray-600 
                                                       dark:bg-gray-700 dark:text-white shadow-sm 
                                                       focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200 dark:focus:ring-indigo-800
                                                       text-base py-3 px-4 transition-all duration-200 cursor-pointer">
                                            <option value="">Todas las Tiendas</option>
                                            @if (isset($tiendas)) 
                                                @foreach ($tiendas as $tienda)
                                                    <option value="{{ $tienda->id }}"
                                                            {{ request('tienda_id') == $tienda->id ? 'selected' : '' }}>
                                                        {{ $tienda->nombre }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- FILTROS DE FECHA - GRID MEJORADO --}}
                            <div class="grid md:grid-cols-2 gap-6">
                                
                                {{-- FECHA INICIO --}}
                                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-300">
                                    <div class="flex items-start gap-4">
                                        <div class="mt-1 bg-gradient-to-br from-green-500 to-emerald-600 p-3 rounded-lg shadow-md">
                                            <i class="fas fa-calendar-alt text-white text-xl"></i>
                                        </div>
                                        <div class="flex-1">
                                            <label for="fecha_inicio" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                                Fecha de Inicio
                                            </label>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Desde cuando deseas consultar</p>
                                            <input type="date" name="fecha_inicio" id="fecha_inicio"
                                                   value="{{ request('fecha_inicio') }}"
                                                   class="filter-input block w-full rounded-lg border-2 border-gray-300 dark:border-gray-600 
                                                          dark:bg-gray-700 dark:text-white shadow-sm 
                                                          focus:border-green-500 focus:ring-4 focus:ring-green-200 dark:focus:ring-green-800
                                                          text-base py-3 px-4 transition-all duration-200">
                                        </div>
                                    </div>
                                </div>

                                {{-- FECHA FIN --}}
                                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-300">
                                    <div class="flex items-start gap-4">
                                        <div class="mt-1 bg-gradient-to-br from-orange-500 to-red-600 p-3 rounded-lg shadow-md">
                                            <i class="fas fa-calendar-check text-white text-xl"></i>
                                        </div>
                                        <div class="flex-1">
                                            <label for="fecha_fin" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                                Fecha de Fin
                                            </label>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Hasta cuando deseas consultar</p>
                                            <input type="date" name="fecha_fin" id="fecha_fin"
                                                   value="{{ request('fecha_fin') }}"
                                                   class="filter-input block w-full rounded-lg border-2 border-gray-300 dark:border-gray-600 
                                                          dark:bg-gray-700 dark:text-white shadow-sm 
                                                          focus:border-orange-500 focus:ring-4 focus:ring-orange-200 dark:focus:ring-orange-800
                                                          text-base py-3 px-4 transition-all duration-200">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ACCIONES DE FILTROS --}}
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-4">
                                <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                    <i class="fas fa-info-circle"></i>
                                    <span>Los filtros se aplican automáticamente al cambiar los valores</span>
                                </div>
                                <button type="button" id="limpiar-filtros"
                                        class="inline-flex items-center gap-2 px-6 py-3 
                                               bg-gradient-to-r from-gray-600 to-gray-700 text-white 
                                               font-semibold rounded-lg shadow-md
                                               hover:from-gray-700 hover:to-gray-800 hover:shadow-lg
                                               focus:outline-none focus:ring-4 focus:ring-gray-400
                                               transform hover:scale-105 transition-all duration-200">
                                    <i class="fas fa-redo-alt"></i>
                                    <span>Limpiar Filtros</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- INDICADOR DE FILTROS ACTIVOS --}}
                <div id="filtros-activos" class="hidden px-8 py-4 bg-indigo-50 dark:bg-indigo-900/20 border-b border-indigo-200 dark:border-indigo-800">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex items-center gap-2 text-indigo-700 dark:text-indigo-300">
                            <i class="fas fa-filter"></i>
                            <span class="font-semibold text-sm">Filtros Activos:</span>
                        </div>
                        <div id="badges-filtros" class="flex flex-wrap gap-2"></div>
                        <button type="button" onclick="document.getElementById('limpiar-filtros').click()"
                                class="ml-auto text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-200 font-medium underline">
                            Limpiar todo
                        </button>
                    </div>
                </div>

                {{-- CONTENEDOR DE RESULTADOS --}}
                <div class="px-8 py-8">
                    <div id="resultados-ventas" class="transition-all duration-300">
                        {{-- La tabla parcial se carga aquí --}}
                        @include('ventas.partials._ventas_table', ['ventas' => $ventas, 'totalVentasSum' => $totalVentasSum])
                    </div>
                </div>

            </div>
        </div>
    </div>
    
    {{-- SCRIPTS --}}
    <script>
        // 🔑 FUNCIÓN GLOBAL PARA EXPANDIR DETALLES (Requerida por el partial)
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
            // =========================================================
            // SWEETALERT - MENSAJES
            // =========================================================
            const successMessage = '{{ session('success') }}';
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

            // =========================================================
            // TOGGLE FILTROS
            // =========================================================
            const toggleBtn = document.getElementById('toggle-filtros');
            const filtrosContainer = document.getElementById('filtros-container');
            const iconToggle = document.getElementById('icon-toggle');
            
            toggleBtn.addEventListener('click', function() {
                if (filtrosContainer.style.maxHeight && filtrosContainer.style.maxHeight !== '0px') {
                    filtrosContainer.style.maxHeight = '0px';
                    filtrosContainer.style.opacity = '0';
                    iconToggle.classList.add('rotate-180');
                    toggleBtn.querySelector('span').textContent = 'Mostrar filtros';
                } else {
                    // Si no está visible, calculamos la altura para la transición fluida
                    filtrosContainer.style.maxHeight = filtrosContainer.scrollHeight + 100 + 'px'; 
                    filtrosContainer.style.opacity = '1';
                    iconToggle.classList.remove('rotate-180');
                    toggleBtn.querySelector('span').textContent = 'Ocultar filtros';
                }
            });

            // =========================================================
            // ELIMINAR/ANULAR VENTA (CORREGIDO)
            // =========================================================
            function bindDeleteButtons() {
                document.querySelectorAll('.delete-btn').forEach(button => {
                    // Prevenir múltiples listeners
                    button.removeEventListener('click', handleDelete);
                    button.addEventListener('click', handleDelete);
                });
            }

            function handleDelete() {
                // 🔑 Obtener una referencia al formulario
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
                        // 🔑 Enviamos el formulario al confirmar
                        form.submit();
                    }
                });
            }

            bindDeleteButtons();

            // =========================================================
            // SISTEMA DE FILTROS AJAX
            // =========================================================
            const form = document.getElementById('filtro-ventas-form');
            const resultadosDiv = document.getElementById('resultados-ventas');
            const filtrosActivosDiv = document.getElementById('filtros-activos');
            const badgesFiltrosDiv = document.getElementById('badges-filtros');
            let fetchController = null;
            let debounceTimer = null;

            function aplicarFiltros(url, immediate = false) {
                clearTimeout(debounceTimer);
                
                const applyFilter = () => {
                    if (fetchController) {
                        fetchController.abort();
                    }
                    fetchController = new AbortController();

                    const formData = new FormData(form);
                    // Eliminar el parámetro 'export' si se envió previamente
                    formData.delete('export'); 
                    
                    const params = new URLSearchParams(formData).toString();
                    const fetchUrl = url ? url : `${form.action}?${params}`;

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
                        
                        // Si el contenedor de filtros estaba abierto, ajustamos la altura después de cargar
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
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Por favor, recarga la página e intenta nuevamente</p>
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
                    // Solo aplica debounce si no es una paginación (immediate=false)
                    debounceTimer = setTimeout(applyFilter, 500);
                }
            }

            function actualizarFiltrosActivos() {
                const formData = new FormData(form);
                const badges = [];
                
                formData.forEach((value, key) => {
                    // Evitar 'filter=1' y claves vacías
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
            
            // Inicialmente, ajustamos la altura si los filtros están abiertos por defecto
            if (filtrosContainer.clientHeight > 0) {
                filtrosContainer.style.maxHeight = filtrosContainer.scrollHeight + 100 + 'px';
            }
        });
    </script>

    <style>
        /* Estilos de transición y animación */
        #filtros-container {
            max-height: 2000px;
            opacity: 1;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #icon-toggle {
            transition: transform 0.3s ease;
        }

        .filter-input {
            transition: all 0.2s ease;
        }

        .filter-input:focus {
            transform: translateY(-1px);
        }

        #resultados-ventas {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        
        /* Estilo para el botón de anular en la tabla */
        .delete-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            color: inherit;
        }
    </style>
</x-app-layout>