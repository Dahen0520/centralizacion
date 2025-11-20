{{-- resources/views/movimientos/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Registrar Movimiento de Inventario') }}
        </h2>
    </x-slot>

    <script>
        const allInventariosData = @json($inventarios); 

        function movimientoModule() {
            return {
                tiendaSeleccionada: null,
                empresaSeleccionada: null, // 🔑 NUEVO ESTADO
                inventariosDisponibles: [],
                todosLosInventarios: JSON.parse(allInventariosData),
                stockSeleccionado: 0,
                tipoMovimiento: '{{ old("tipo_movimiento", "") }}',
                cantidad: {{ old('cantidad', 0) }},
                inventarioSeleccionado: null,
                razonSeleccionada: '{{ old("razon", "") }}',
                
                init() {
                    this.inventariosDisponibles = this.todosLosInventarios;
                    this.tiendaSeleccionada = @json(old('tienda_id', ''));
                    this.empresaSeleccionada = @json(old('empresa_id', '')); // Inicializar
                    this.filterInventario();
                },
                
                // 🔑 FUNCIÓN DE FILTRO EN CASCADA
                filterInventario() {
                    this.stockSeleccionado = 0;
                    this.inventarioSeleccionado = null;
                    
                    let filtered = this.todosLosInventarios;
                    
                    // 1. FILTRAR POR TIENDA
                    if (this.tiendaSeleccionada) {
                        filtered = filtered.filter(item => 
                            item.tienda_id == this.tiendaSeleccionada
                        );
                    }
                    
                    // 2. FILTRAR POR EMPRESA
                    if (this.empresaSeleccionada) {
                        filtered = filtered.filter(item => 
                            item.empresa_id == this.empresaSeleccionada
                        );
                    }
                    
                    this.inventariosDisponibles = filtered;
                },
                
                updateStockDisplay(event) {
                    const inventarioId = event.target.value;
                    const selectedItem = this.todosLosInventarios.find(item => item.id == inventarioId);
                    this.inventarioSeleccionado = selectedItem;
                    this.stockSeleccionado = selectedItem ? selectedItem.stock_actual : 0;
                },
                
                get stockInsuficiente() {
                    return this.tipoMovimiento === 'SALIDA' && 
                           this.cantidad > 0 && 
                           this.cantidad > this.stockSeleccionado;
                },
                
                get stockResultante() {
                    if (!this.cantidad || this.cantidad <= 0) return this.stockSeleccionado;
                    
                    const currentStock = parseInt(this.stockSeleccionado);
                    const amount = parseInt(this.cantidad);
                    
                    if (this.tipoMovimiento === 'ENTRADA') {
                        return currentStock + amount;
                    } else if (this.tipoMovimiento === 'SALIDA') {
                        return Math.max(0, currentStock - amount);
                    }
                    return currentStock;
                },
                
                get puedeEnviar() {
                    return this.tiendaSeleccionada && 
                           this.empresaSeleccionada && // 🔑 Empresa es obligatorio ahora
                           this.inventarioSeleccionado && 
                           this.tipoMovimiento && 
                           this.razonSeleccionada && 
                           this.cantidad > 0 && 
                           !this.stockInsuficiente;
                }
            }
        }
    </script>

    <div class="py-12" x-data="movimientoModule()">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Mensajes de Notificación (sin cambios) --}}
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded relative mb-4 shadow-md" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-3 text-xl"></i>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded relative mb-4 shadow-md" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-3 text-xl"></i>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded relative mb-4 shadow-md" role="alert">
                    <div class="flex items-start">
                        <i class="fas fa-times-circle mr-3 text-xl mt-1"></i>
                        <ul class="list-disc ml-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
            
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg overflow-hidden">
                {{-- Encabezado del formulario --}}
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-exchange-alt mr-3"></i>
                        Nuevo Movimiento de Inventario
                    </h3>
                </div>
                
                <form method="POST" action="{{ route('movimientos.store') }}" class="p-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- 1. Selección de Tienda --}}
                        <div class="md:col-span-1">
                            <label for="tienda_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-store mr-2 text-indigo-500"></i>
                                Tienda
                            </label>
                            <select id="tienda_id" name="tienda_id" x-model="tiendaSeleccionada" @change="filterInventario" required
                                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4 transition duration-150">
                                <option value="">-- Seleccione una tienda --</option>
                                @foreach($tiendas as $tienda)
                                    <option value="{{ $tienda->id }}">{{ $tienda->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- 🔑 2. Selección de Empresa --}}
                        <div class="md:col-span-1" :class="{'opacity-50 pointer-events-none': !tiendaSeleccionada}">
                             <label for="empresa_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-building mr-2 text-indigo-500"></i>
                                Empresa (Proveedor)
                            </label>
                            <select id="empresa_id" x-model="empresaSeleccionada" @change="filterInventario" required
                                    :disabled="!tiendaSeleccionada"
                                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4 transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                                <option value="">-- Seleccione el proveedor --</option>
                                @foreach($empresas as $empresa)
                                    <option value="{{ $empresa->id }}">{{ $empresa->nombre_negocio }}</option>
                                @endforeach
                            </select>
                        </div>


                        {{-- 3. Producto/Stock --}}
                        <div class="md:col-span-2" :class="{'opacity-50 pointer-events-none': !tiendaSeleccionada || !empresaSeleccionada}">
                            <label for="inventario_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-box mr-2 text-indigo-500"></i>
                                Producto en Stock
                            </label>
                            <select id="inventario_id" name="inventario_id" required :disabled="inventariosDisponibles.length === 0" @change="updateStockDisplay"
                                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4 transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                                <option value="">
                                    <template x-if="inventariosDisponibles.length > 0">
                                        <span>-- Seleccione el producto afectado --</span>
                                    </template>
                                    <template x-else>
                                        <span>-- No hay productos de esta empresa en la tienda seleccionada --</span>
                                    </template>
                                </option>
                                <template x-for="item in inventariosDisponibles" :key="item.id">
                                    <option :value="item.id" x-text="item.nombre_completo"></option>
                                </template>
                            </select>
                            @error('inventario_id') <p class="text-sm text-red-500 mt-1 flex items-center"><i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}</p> @enderror
                            
                            {{-- Panel de información del stock --}}
                            <div x-show="stockSeleccionado >= 0 && inventarioSeleccionado" 
                                 x-transition
                                 class="mt-3 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-600 rounded-lg border border-indigo-200 dark:border-gray-500">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="fas fa-layer-group text-indigo-600 dark:text-indigo-400 text-2xl mr-3"></i>
                                        <div>
                                            <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wide">Stock Actual</p>
                                            <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400" x-text="stockSeleccionado"></p>
                                        </div>
                                    </div>
                                    <div class="text-right" x-show="cantidad > 0 && tipoMovimiento">
                                        <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wide">Stock Resultante</p>
                                        <p class="text-2xl font-bold" 
                                           :class="stockInsuficiente ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'"
                                           x-text="stockResultante"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- 4. Tipo de Movimiento --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                <i class="fas fa-arrows-alt-v mr-2 text-indigo-500"></i>
                                Tipo de Movimiento
                            </label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="relative flex cursor-pointer">
                                    <input type="radio" name="tipo_movimiento" value="ENTRADA" required 
                                           x-model="tipoMovimiento"
                                           class="peer sr-only" 
                                           {{ old('tipo_movimiento') == 'ENTRADA' ? 'checked' : '' }}>
                                    <div class="w-full p-4 border-2 rounded-lg transition-all duration-200
                                                peer-checked:border-green-500 peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20
                                                border-gray-300 dark:border-gray-600 hover:border-green-400">
                                        <div class="flex items-center justify-center">
                                            <i class="fas fa-arrow-down text-green-600 text-2xl mr-3"></i>
                                            <span class="font-semibold text-gray-700 dark:text-gray-300">ENTRADA</span>
                                        </div>
                                    </div>
                                </label>
                                <label class="relative flex cursor-pointer">
                                    <input type="radio" name="tipo_movimiento" value="SALIDA" required 
                                           x-model="tipoMovimiento"
                                           class="peer sr-only" 
                                           {{ old('tipo_movimiento') == 'SALIDA' ? 'checked' : '' }}>
                                    <div class="w-full p-4 border-2 rounded-lg transition-all duration-200
                                                peer-checked:border-red-500 peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20
                                                border-gray-300 dark:border-gray-600 hover:border-red-400">
                                        <div class="flex items-center justify-center">
                                            <i class="fas fa-arrow-up text-red-600 text-2xl mr-3"></i>
                                            <span class="font-semibold text-gray-700 dark:text-gray-300">SALIDA</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @error('tipo_movimiento') <p class="text-sm text-red-500 mt-2 flex items-center"><i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}</p> @enderror
                        </div>

                        {{-- 5. Razón del Movimiento --}}
                        <div class="md:col-span-2">
                            <label for="razon" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-clipboard-list mr-2 text-indigo-500"></i>
                                Razón Específica
                            </label>
                            <select id="razon" name="razon" required x-model="razonSeleccionada"
                                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4 transition duration-150">
                                <option value="">-- Seleccione una razón --</option>
                                @foreach($razones as $tipo => $lista_razones)
                                    <optgroup label="{{ $tipo }}">
                                        @foreach($lista_razones as $razon)
                                            <option value="{{ $razon }}" {{ old('razon') == $razon ? 'selected' : '' }}>
                                                {{ $razon }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('razon') <p class="text-sm text-red-500 mt-1 flex items-center"><i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}</p> @enderror
                        </div>

                        {{-- 6. Cantidad --}}
                        <div class="md:col-span-2">
                            <label for="cantidad" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-calculator mr-2 text-indigo-500"></i>
                                Cantidad a Mover
                            </label>
                            <input type="number" id="cantidad" name="cantidad" min="1" required
                                   x-model="cantidad"
                                   value="{{ old('cantidad') }}"
                                   class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4 transition duration-150">
                            @error('cantidad') <p class="text-sm text-red-500 mt-1 flex items-center"><i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}</p> @enderror
                            
                            {{-- Alerta de stock insuficiente --}}
                            <div x-show="stockInsuficiente" 
                                 x-transition
                                 class="mt-3 p-3 bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 rounded">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 mr-3"></i>
                                    <p class="text-sm text-red-700 dark:text-red-300 font-medium">
                                        Stock insuficiente. Solo hay <span x-text="stockSeleccionado"></span> unidades disponibles.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Botones de acción --}}
                    <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('movimientos.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-lg font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 transition ease-in-out duration-150">
                            <i class="fas fa-arrow-left mr-2"></i> Cancelar
                        </a>
                        
                        <button type="submit"
                                :disabled="!puedeEnviar"
                                :class="puedeEnviar ? 'bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-900' : 'bg-gray-400 cursor-not-allowed'"
                                class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 transition ease-in-out duration-150 shadow-lg">
                            <i class="fas fa-save mr-2"></i> Registrar Movimiento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>