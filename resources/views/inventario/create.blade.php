<x-app-layout>
    <x-slot name="header">
    </x-slot>

    {{-- INCLUSIÓN DE SWEETALERT2 PARA NOTIFICACIONES --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <div class="py-6 flex justify-center">
        <div class="w-full max-w-4xl mx-auto"> {{-- Ancho un poco más grande --}}
            <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 
                        rounded-2xl shadow-3xl p-10 lg:p-12 
                        border-t-4 border-b-4 border-emerald-500 dark:border-emerald-600 
                        transform hover:shadow-4xl transition-all duration-300 ease-in-out">
                
                {{-- Bloque de Encabezado Elegante --}}
                <div class="text-center mb-10">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full 
                                bg-gradient-to-br from-emerald-500 to-emerald-700 text-white 
                                mb-5 shadow-lg transform hover:scale-110 transition-all duration-300 ease-in-out 
                                dark:from-emerald-600 dark:to-emerald-800">
                         <i class="fas fa-boxes text-4xl"></i>
                    </div>
                    <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-2 leading-tight">
                        Registrar Inventario Único
                    </h3>
                    <p class="text-md text-gray-600 dark:text-gray-400 max-w-lg mx-auto">
                        Sigue los pasos: Selecciona Tienda, Empresa asociada y el Producto disponible.
                    </p>
                    
                    <a href="{{ route('inventarios.index') }}" 
                       class="mt-6 inline-flex items-center text-sm font-semibold 
                              text-emerald-600 dark:text-emerald-400 
                              hover:text-emerald-800 dark:hover:text-emerald-200 
                              transition duration-300 ease-in-out 
                              transform hover:-translate-x-1 hover:underline">
                        <i class="fas fa-arrow-left mr-2"></i> Volver a la Lista de Inventario
                    </a>
                </div>

                {{-- Manejo de Errores de Validación Estilizado --}}
                @if ($errors->any())
                    <div class="bg-red-50 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-700 dark:text-red-200 
                                px-6 py-4 rounded-lg relative mb-8 shadow-inner" role="alert">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-exclamation-triangle text-xl mr-3 text-red-500 dark:text-red-300"></i>
                            <strong class="font-bold text-lg">¡Atención! Datos Inválidos</strong>
                        </div>
                        <ul class="list-disc list-inside text-sm space-y-1 ml-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('inventarios.store') }}">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-8">
                        
                        {{-- CAMPO TIENDA (Select) - PASO 1 --}}
                        <div>
                            <label for="tienda_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">1. Tienda / Punto de Venta <span class="text-red-500">*</span></label>
                            <select name="tienda_id" id="tienda_id" required 
                                    class="w-full px-5 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-emerald-500 focus:border-emerald-500 transition @error('tienda_id') border-red-500 ring-red-500 @enderror">
                                <option value="">Seleccione una Tienda</option>
                                @foreach($tiendas as $tienda)
                                    <option value="{{ $tienda->id }}" @selected(old('tienda_id') == $tienda->id)>
                                        {{ $tienda->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tienda_id')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center"><i class="fas fa-info-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        {{-- CAMPO EMPRESA (Select DINÁMICO) - PASO 2 --}}
                        <div>
                            <label for="empresa_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">2. Empresa Asociada <span class="text-red-500">*</span></label>
                            <select name="empresa_id" id="empresa_id" required disabled
                                    class="w-full px-5 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 focus:ring-emerald-500 focus:border-emerald-500 transition @error('empresa_id') border-red-500 ring-red-500 @enderror">
                                <option value="">Seleccione la Tienda primero</option>
                            </select>
                            @error('empresa_id')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center"><i class="fas fa-info-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        {{-- CAMPO PRODUCTO/MARCA (Select DINÁMICO) - PASO 3 --}}
                        <div class="md:col-span-2">
                            <label for="marca_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">3. Producto (Marca) <span class="text-red-500">*</span></label>
                            <select name="marca_id" id="marca_id" required disabled
                                    class="w-full px-5 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 focus:ring-emerald-500 focus:border-emerald-500 transition @error('marca_id') border-red-500 ring-red-500 @enderror">
                                <option value="">Seleccione la Empresa primero</option>
                            </select>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Solo se mostrarán productos **NO** registrados aún en la Tienda seleccionada.</p>
                            @error('marca_id')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center"><i class="fas fa-info-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Campo Precio --}}
                        <div>
                            <label for="precio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Precio (L) <span class="text-red-500">*</span></label>
                            <input type="number" id="precio" name="precio" value="{{ old('precio') }}" required 
                                   step="0.01" min="0" placeholder="Ej: 19.99"
                                   class="w-full px-5 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-emerald-500 focus:border-emerald-500 transition @error('precio') border-red-500 ring-red-500 @enderror">
                            @error('precio')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center"><i class="fas fa-info-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Campo Stock --}}
                        <div>
                            <label for="stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Stock Inicial <span class="text-red-500">*</span></label>
                            <input type="number" id="stock" name="stock" value="{{ old('stock') }}" required 
                                   min="0" placeholder="Ej: 150"
                                   class="w-full px-5 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-emerald-500 focus:border-emerald-500 transition @error('stock') border-red-500 ring-red-500 @enderror">
                            @error('stock')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center"><i class="fas fa-info-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>
                        
                    </div>
                    
                    <hr class="my-10 border-gray-200 dark:border-gray-700 opacity-60 md:col-span-2">

                    <button type="submit" 
                            class="w-full py-4 bg-gradient-to-r from-emerald-600 to-green-700 text-white 
                                   font-extrabold rounded-xl shadow-lg hover:shadow-xl 
                                   hover:from-emerald-700 hover:to-green-800 
                                   transition duration-300 ease-in-out transform hover:-translate-y-0.5 
                                   text-xl uppercase tracking-widest focus:outline-none focus:ring-4 
                                   focus:ring-emerald-300 dark:focus:ring-emerald-800">
                        <i class="fas fa-save mr-2"></i> Crear Registro de Inventario
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- SCRIPT PARA LÓGICA DINÁMICA DE SELECCIÓN EN CASCADA --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tiendaSelect = document.getElementById('tienda_id');
            const empresaSelect = document.getElementById('empresa_id');
            const marcaSelect = document.getElementById('marca_id');

            // --- Funciones de Utilidad ---
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

            // --- Lógica del Paso 1: Obtener Empresas ---
            async function fetchEmpresas(tiendaId) {
                resetSelect(empresaSelect, 'Cargando Empresas...');
                resetSelect(marcaSelect, 'Seleccione la Tienda y Empresa');

                if (!tiendaId) {
                    resetSelect(empresaSelect, '2. Seleccione la Tienda primero');
                    return;
                }

                try {
                    // Endpoint para obtener empresas asociadas a la tienda
                    const url = `/api/tiendas/${tiendaId}/empresas`;
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
                            // Usamos .nombre_negocio que es la columna correcta en tu modelo Empresa
                            const isSelected = "{{ old('empresa_id') }}" == empresa.id ? 'selected' : '';
                            options += `<option value="${empresa.id}" ${isSelected}>${empresa.nombre_negocio}</option>`;
                        });
                        empresaSelect.innerHTML = options;
                        enableSelect(empresaSelect, '2. Seleccione una Empresa');
                        
                        // Si old('empresa_id') tiene un valor, forzar la carga del paso 3
                        if ("{{ old('empresa_id') }}" && "{{ old('empresa_id') }}" == empresaSelect.value) {
                           fetchMarcas(empresaSelect.value, tiendaId);
                        }
                    }
                    
                } catch (error) {
                    console.error('Fetch Error Empresas:', error);
                    resetSelect(empresaSelect, 'Error al cargar: Intente recargar.');
                }
            }

            // --- Lógica del Paso 2: Obtener Marcas ---
            async function fetchMarcas(empresaId, tiendaId) {
                resetSelect(marcaSelect, 'Cargando Productos...');

                if (!empresaId || !tiendaId) {
                    resetSelect(marcaSelect, '3. Seleccione la Empresa primero');
                    return;
                }
                
                try {
                    // Endpoint para obtener marcas de la empresa y filtrar por tienda
                    const url = `/api/empresas/${empresaId}/marcas-disponibles/${tiendaId}`;
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
                            const isSelected = "{{ old('marca_id') }}" == marca.id ? 'selected' : '';
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

            // --- Event Listeners ---
            tiendaSelect.addEventListener('change', (e) => {
                // Reiniciar el select de Marcas siempre que se cambie la Tienda
                resetSelect(marcaSelect, 'Seleccione la Empresa primero'); 
                fetchEmpresas(e.target.value);
            });

            empresaSelect.addEventListener('change', (e) => {
                const tiendaId = tiendaSelect.value;
                const empresaId = e.target.value;
                fetchMarcas(empresaId, tiendaId);
            });

            // --- Manejo de old() para errores de validación ---
            // Esto garantiza que la cascada se cargue de nuevo si la validación falla
            if (tiendaSelect.value) {
                fetchEmpresas(tiendaSelect.value);
            }

            {{-- SCRIPT PARA NOTIFICACIÓN DE SESIÓN --}}
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: '¡Creación Exitosa!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    toast: true,
                    position: 'top-end'
                });
            @endif
        });
    </script>
</x-app-layout>