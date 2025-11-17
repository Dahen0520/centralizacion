<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-cash-register text-white"></i>
            </div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Punto de Venta') }}
            </h2>
        </div>
    </x-slot>

    {{-- SCRIPTS CRÍTICOS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- CRÍTICO: Asegurar que Alpine.js esté cargado para que x-data funcione --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Contenedor principal con estado Alpine.js --}}
    {{-- Se inyecta la lista de tipos de pago desde el backend (necesario para la validación interna en JS) --}}
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6"
        x-data="posModule({{ json_encode($tiposPago ?? []) }})"
        @keydown.window="handleKeyboard($event)"
        @new-client-saved.window="setClientAfterModal($event.detail.client)">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- COLUMNA IZQUIERDA --}}
                <div class="lg:col-span-2 space-y-4">

                    {{-- Sección: Selección de tienda --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all duration-300 hover:shadow-md">
                        <div class="flex items-center gap-2 mb-4">
                            <i class="fas fa-store text-emerald-600 dark:text-emerald-400"></i>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Selecciona la Tienda</h3>
                            <span x-show="tiendaId" class="ml-auto text-xs px-2 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 rounded-full">
                                <i class="fas fa-check-circle"></i> Seleccionada
                            </span>
                        </div>
                        <select x-model="tiendaId" @change="fetchProductos"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-gray-900 dark:text-white">
                            <option value="">-- Seleccione una tienda --</option>
                            @foreach($tiendas as $tienda)
                                <option value="{{ $tienda->id }}">{{ $tienda->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Sección: Cliente y Búsqueda --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 relative transition-all duration-300 hover:shadow-md">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-user-tag text-indigo-600 dark:text-indigo-400"></i>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Cliente</h3>
                                <span class="text-xs text-gray-500 dark:text-gray-400">(Opcional)</span>
                            </div>
                            <button @click="openNewClientModal"
                                    class="text-xs px-3 py-1.5 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg transition-all duration-200 hover:shadow-md transform hover:scale-105">
                                <i class="fas fa-plus mr-1"></i> Nuevo Cliente
                            </button>
                        </div>

                        <div class="relative">
                            <input type="text"
                                id="client_search_input"
                                x-model="clientSearchQuery"
                                @input.debounce.300ms="onClientSearchInput"
                                @input="formatIdentificacion($event, 'clientSearch')"
                                @keydown.escape="clearClientSearch"
                                placeholder="Buscar por RTN (Ej: 0615-2003-001441)"
                                class="w-full pl-4 pr-10 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-gray-900 dark:text-white">
                            
                            <button x-show="clientSearchQuery.length > 0" @click="clearClientSearch"
                                x-transition
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                <i class="fas fa-times-circle"></i>
                            </button>

                            {{-- Cliente Seleccionado --}}
                            <div x-show="selectedClientName !== 'Cliente Genérico / Sin Registro'" 
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform -translate-y-2"
                                x-transition:enter-end="opacity-100 transform translate-y-0"
                                class="mt-3">
                                <div class="flex items-center justify-between p-3 rounded-lg bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 border border-indigo-200 dark:border-indigo-800">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-user-check text-indigo-600 dark:text-indigo-400"></i>
                                        <p class="text-sm text-indigo-700 dark:text-indigo-300 font-medium" x-text="selectedClientName"></p>
                                    </div>
                                    <button @click="clearClient" class="text-indigo-500 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 text-xs font-semibold transition-colors px-2 py-1 rounded hover:bg-indigo-100 dark:hover:bg-indigo-900/40">
                                        <i class="fas fa-times mr-1"></i> Cambiar
                                    </button>
                                </div>
                            </div>

                            {{-- Cliente Genérico --}}
                            <div x-show="selectedClientName === 'Cliente Genérico / Sin Registro'" 
                                x-transition
                                class="mt-3">
                                <div class="flex items-center gap-2 p-2 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                                    <i class="fas fa-user-circle text-gray-400"></i>
                                    <p class="text-sm text-gray-600 dark:text-gray-400" x-text="selectedClientName"></p>
                                </div>
                            </div>

                            {{-- Dropdown de resultados --}}
                            <div x-show="clientSearchResults.length && !isClientLoading" 
                                @click.away="clientSearchResults = []"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform -translate-y-2"
                                x-transition:enter-end="opacity-100 transform translate-y-0"
                                class="absolute z-20 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-xl max-h-60 overflow-y-auto">
                                <template x-for="(client, idx) in clientSearchResults" :key="client.id">
                                    <div @click="selectClient(client)"
                                        :class="{'bg-gray-50 dark:bg-gray-600': idx === selectedClientIndex}"
                                        class="p-3 cursor-pointer hover:bg-indigo-50 dark:hover:bg-indigo-900/30 text-gray-900 dark:text-white text-sm border-b border-gray-100 dark:border-gray-600 last:border-0 transition-colors">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="font-semibold" x-text="client.nombre"></p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    RTN: <span x-text="client.identificacion || 'N/A'"></span>
                                                    <span x-show="client.telefono" class="ml-2">· Tel: <span x-text="client.telefono"></span></span>
                                                </p>
                                            </div>
                                            <i class="fas fa-arrow-right text-indigo-400"></i>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            {{-- Loading state --}}
                            <div x-show="isClientLoading" 
                                x-transition
                                class="absolute z-20 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-xl p-4 text-center">
                                <i class="fas fa-spinner fa-spin text-indigo-500"></i>
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Buscando...</span>
                            </div>
                        </div>
                    </div>

                    {{-- Sección: Búsqueda de Productos con Barcode --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300 hover:shadow-md" x-show="tiendaId" x-cloak>
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex gap-3">
                                <div class="flex-1 relative">
                                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 transition-colors" :class="{'text-emerald-500': searchQuery.length > 0}"></i>
                                    <input type="text" 
                                            x-ref="productSearch"
                                            x-model="searchQuery" 
                                            @input.debounce.300ms="filterProductos"
                                            @keydown.escape="clearSearch"
                                            placeholder="Buscar producto (nombre, código o escanea código de barras)..."
                                            class="w-full pl-12 pr-10 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:shadow-lg transition text-gray-900 dark:text-white">
                                    <button x-show="searchQuery.length > 0" @click="clearSearch"
                                            x-transition
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                </div>
                            </div>
                            
                            {{-- Stats rápidas --}}
                            <div class="flex gap-4 mt-4 text-xs text-gray-600 dark:text-gray-400">
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-box-open text-emerald-500"></i>
                                    <span><span x-text="filteredProductos.length"></span> productos</span>
                                </div>
                                <div class="flex items-center gap-1" x-show="searchQuery">
                                    <i class="fas fa-filter text-blue-500"></i>
                                    <span>Filtrado</span>
                                </div>
                            </div>
                        </div>

                        {{-- Lista de productos con animación --}}
                        <div class="p-4 max-h-[450px] overflow-y-auto">
                            <div x-show="isProductsLoading" class="text-center py-12">
                                <i class="fas fa-spinner fa-spin text-3xl text-emerald-500 mb-3"></i>
                                <p class="text-gray-500 dark:text-gray-400">Cargando productos...</p>
                            </div>

                            <template x-for="(producto, idx) in filteredProductos" :key="producto.inventario_id">
                                <div x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform translate-y-2"
                                    x-transition:enter-end="opacity-100 transform translate-y-0"
                                    :style="`transition-delay: ${idx * 20}ms`"
                                    class="group bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 mb-3 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-all duration-200 border border-transparent hover:border-emerald-200 dark:hover:border-emerald-800 hover:shadow-md">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1 min-w-0 mr-4">
                                            <h4 class="font-semibold text-gray-900 dark:text-white mb-1 truncate" x-text="producto.producto_nombre"></h4>
                                            <div class="flex flex-wrap items-center gap-3 text-xs text-gray-600 dark:text-gray-400">
                                                <span class="flex items-center gap-1">
                                                    <i class="fas fa-barcode"></i>
                                                    <span x-text="producto.codigo_marca || 'Sin código'"></span>
                                                </span>
                                                <span class="flex items-center gap-1" :class="{'text-red-500': producto.stock_actual < 5, 'text-yellow-500': producto.stock_actual >= 5 && producto.stock_actual < 10}">
                                                    <i class="fas fa-box"></i>
                                                    <span x-text="producto.stock_actual"></span> disponibles
                                                </span>
                                                <span class="font-bold text-emerald-600 dark:text-emerald-400">
                                                    L <span x-text="producto.precio.toFixed(2)"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <button @click.stop.prevent="addToCart(producto)"
                                            :disabled="producto.stock_actual === 0"
                                            class="flex-shrink-0 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white rounded-lg font-medium transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105 disabled:transform-none">
                                            <i class="fas fa-plus mr-1"></i>
                                            Añadir
                                        </button>
                                    </div>
                                </div>
                            </template>

                            {{-- Estados vacíos --}}
                            <div x-show="!isProductsLoading && !filteredProductos.length && searchQuery && tiendaId" 
                                x-transition
                                class="text-center py-12">
                                <i class="fas fa-search text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                                <p class="text-gray-500 dark:text-gray-400 font-medium">No se encontraron productos</p>
                                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Intenta con otro término de búsqueda</p>
                            </div>
                            
                            <div x-show="!isProductsLoading && tiendaId && !searchQuery && !allProductos.length" 
                                x-transition
                                class="text-center py-12">
                                <i class="fas fa-box-open text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                                <p class="text-gray-500 dark:text-gray-400 font-medium">No hay productos con stock</p>
                                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Selecciona otra tienda o añade inventario</p>
                            </div>
                        </div>
                    </div>

                    {{-- Sección: Mensaje inicial mejorado --}}
                    <div x-show="!tiendaId" 
                        x-transition
                        class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-xl shadow-sm border-2 border-dashed border-gray-300 dark:border-gray-600 p-12 text-center">
                        <div class="animate-bounce mb-4">
                            <i class="fas fa-arrow-up text-5xl text-emerald-500"></i>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 text-lg font-medium">Selecciona una tienda para comenzar</p>
                        <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">Podrás ver los productos disponibles y realizar ventas</p>
                    </div>
                </div>

                {{-- COLUMNA DERECHA: CARRITO MEJORADO --}}
                <div class="space-y-4">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden sticky lg:top-6">
                        <div class="p-6 bg-gradient-to-r from-emerald-500 to-teal-600">
                            <div class="flex items-center justify-between text-white">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-shopping-cart text-xl"></i>
                                    <h3 class="text-lg font-semibold">Carrito de Compras</h3>
                                </div>
                                <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm font-bold flex items-center gap-1">
                                    <i class="fas fa-shopping-bag text-xs"></i>
                                    <span x-text="cart.length"></span>
                                </span>
                            </div>
                        </div>

                        {{-- Items del carrito --}}
                        <div class="p-4 max-h-[450px] overflow-y-auto">
                            <template x-for="(item, index) in cart" :key="item.inventario_id">
                                <div x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform translate-x-4"
                                    x-transition:enter-end="opacity-100 transform translate-x-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 transform translate-x-0"
                                    x-transition:leave-end="opacity-0 transform -translate-x-4"
                                    class="bg-gradient-to-r from-gray-50 to-white dark:from-gray-700/50 dark:to-gray-700/30 rounded-lg p-4 mb-3 border border-gray-200 dark:border-gray-600 hover:shadow-md transition-shadow">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1 min-w-0 mr-2">
                                            <h4 class="font-semibold text-sm text-gray-900 dark:text-white mb-1 truncate" x-text="item.nombre"></h4>
                                            <div class="flex items-center gap-3 text-xs text-gray-600 dark:text-gray-400">
                                                <span>L <span x-text="item.precio.toFixed(2)"></span> c/u</span>
                                                <span class="flex items-center gap-1">
                                                    <i class="fas fa-box-open"></i>
                                                    Max: <span x-text="item.stockMax"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <button @click="removeFromCart(index)"
                                            class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-all p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <label class="text-xs text-gray-600 dark:text-gray-400 font-medium">Cant:</label>
                                            <div class="flex items-center gap-1">
                                                <button @click.stop.prevent="decrementQuantity(index)"
                                                    class="w-8 h-8 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 rounded-lg transition-colors flex items-center justify-center text-gray-700 dark:text-gray-200">
                                                    <i class="fas fa-minus text-xs"></i>
                                                </button>
                                                <input type="number" 
                                                    x-model.number="item.cantidad" 
                                                    min="1" 
                                                    :max="item.stockMax"
                                                    @input="updateCart"
                                                    class="w-16 px-2 py-1.5 text-center text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 text-gray-900 dark:text-white font-semibold">
                                                <button @click.stop.prevent="incrementQuantity(index, item.stockMax)"
                                                    :disabled="item.cantidad >= item.stockMax"
                                                    class="w-8 h-8 bg-emerald-500 hover:bg-emerald-600 disabled:bg-gray-400 rounded-lg transition-colors flex items-center justify-center text-white disabled:cursor-not-allowed">
                                                    <i class="fas fa-plus text-xs"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Subtotal</p>
                                            <p class="font-bold text-emerald-600 dark:text-emerald-400 text-lg" x-text="'L ' + (item.precio * item.cantidad).toFixed(2)"></p>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- Carrito vacío --}}
                            <div x-show="!cart.length" 
                                x-transition
                                class="text-center py-12">
                                <i class="fas fa-shopping-cart text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
                                <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Tu carrito está vacío</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Añade productos para comenzar</p>
                            </div>
                        </div>

                        {{-- Resumen y total --}}
                        <div class="p-6 border-t border-gray-200 dark:border-gray-700 space-y-3 bg-gray-50 dark:bg-gray-900/50">
                            
                            {{-- SELECTOR DE TIPO DE PAGO (Corregido a Blade simple) --}}
                            <div class="space-y-2" x-show="cart.length">
                                <label for="payment_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-money-check-alt mr-1 text-teal-600"></i> Tipo de Pago: <span class="text-red-500">*</span>
                                </label>
                                <select id="payment_type" x-model="selectedPaymentType" @change="updateCart"
                                        class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition text-gray-900 dark:text-white text-sm font-semibold">
                                    <option value="">-- Seleccione Tipo de Pago --</option>
                                    @foreach($tiposPago as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- CAMPO MONTO RECIBIDO Y VUELTO (NUEVO) --}}
                            <div x-show="cart.length && selectedPaymentType === 'EFECTIVO'">
                                <label class="flex items-center justify-between text-sm pt-3">
                                    <span class="text-gray-600 dark:text-gray-400 font-medium">
                                        <i class="fas fa-money-bill-wave mr-1 text-green-500"></i>
                                        Monto Recibido (L):
                                    </span>
                                    <input type="number" 
                                        x-model.number="amountReceived" 
                                        @input="calculateChange"
                                        min="0" 
                                        :placeholder="finalTotal.toFixed(2)"
                                        step="0.01"
                                        class="w-24 px-2 py-1 text-sm text-right bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 text-gray-900 dark:text-white">
                                </label>

                                {{-- VUELTO (CAMBIO) --}}
                                <div class="flex justify-between items-center mt-3 p-3 rounded-lg border border-dashed"
                                    :class="{'border-green-400 bg-green-50 dark:bg-green-900/20': change > 0, 'border-gray-300': change <= 0}">
                                    <span class="text-gray-700 dark:text-gray-300 font-semibold text-lg">Cambio (Vuelto):</span>
                                    <span class="text-2xl font-bold" 
                                        :class="{'text-green-600 dark:text-green-400': change > 0, 'text-gray-500 dark:text-gray-400': change <= 0}"
                                        x-text="'L ' + change.toFixed(2)">
                                    </span>
                                </div>
                            </div>
                            
                            {{-- Cliente asignado --}}
                            <div class="border-b pt-2 pb-3 mb-3 text-xs" x-cloak>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Venta para:</span>
                                    <span class="font-semibold text-indigo-600 dark:text-indigo-400 flex items-center gap-1">
                                        <i class="fas fa-user text-xs"></i>
                                        <span x-text="selectedClientName.length > 30 ? selectedClientName.substring(0, 30) + '...' : selectedClientName"></span>
                                    </span>
                                </div>
                            </div>

                            {{-- Descuento opcional --}}
                            <div class="space-y-2" x-show="cart.length">
                                <label class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400 font-medium">
                                        <i class="fas fa-tag text-amber-500 mr-1"></i>
                                        Descuento:
                                    </span>
                                    <div class="flex items-center gap-2">
                                        <input type="number" 
                                            x-model.number="discount" 
                                            @input="updateCart"
                                            min="0" 
                                            :max="total"
                                            step="0.01"
                                            placeholder="0.00"
                                            class="w-24 px-2 py-1 text-sm text-right bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 text-gray-900 dark:text-white">
                                        <span class="text-gray-500">L</span>
                                    </div>
                                </label>
                            </div>

                            {{-- DESGLOSE FISCAL --}}
                            <div class="pt-2 border-t border-gray-200 dark:border-gray-700 space-y-1 text-sm text-gray-700 dark:text-gray-300">
                                {{-- Subtotal Neto (Base de la factura, sin ISV) --}}
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-600 dark:text-gray-400">SubTotal Base (sin ISV):</span>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">L <span x-text="subtotalNeto.toFixed(2)"></span></span>
                                </div>
                                
                                {{-- Total Exonerado/Exento (Según tu factura: Subt.Exento) --}}
                                <div class="flex justify-between items-center text-xs text-gray-500 dark:text-gray-400" x-show="totalExento > 0">
                                    <span>Importe Exonerado/Exento:</span>
                                    <span>L <span x-text="totalExento.toFixed(2)"></span></span>
                                </div>

                                {{-- Total Gravado (Suma de las bases que sí llevan impuesto) --}}
                                <div class="flex justify-between items-center text-xs text-gray-500 dark:text-gray-400" x-show="totalGravado > 0">
                                    <span>Subt. Gravado:</span>
                                    <span>L <span x-text="totalGravado.toFixed(2)"></span></span>
                                </div>

                                {{-- Descuento Global --}}
                                <div class="flex justify-between items-center text-amber-600 dark:text-amber-400" x-show="discount > 0">
                                    <span>Descuento Aplicado:</span>
                                    <span>- L <span x-text="discount.toFixed(2)"></span></span>
                                </div>
                            </div>
                            
                            {{-- TOTAL IMPUESTOS --}}
                            <div class="flex justify-between items-center pt-2 border-t border-gray-200 dark:border-gray-700">
                                <span class="text-gray-700 dark:text-gray-300 font-semibold text-lg">Total ISV:</span>
                                <span class="font-bold text-red-600 dark:text-red-400 text-xl">
                                    L <span x-text="totalIsv.toFixed(2)"></span>
                                </span>
                            </div>

                            {{-- TOTAL A PAGAR --}}
                            <div class="flex justify-between items-center pt-2 border-t border-gray-200 dark:border-gray-700">
                                <span class="text-gray-700 dark:text-gray-300 font-semibold text-xl">Total a Pagar:</span>
                                <span class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">
                                    L <span x-text="finalTotal.toFixed(2)"></span>
                                </span>
                            </div>

                            {{-- Botón procesar venta (TICKET DE VENTA) --}}
                            <button @click="processSale('TICKET')"
                                :disabled="!isSaleReady('TICKET') || isProcessing"
                                class="w-full bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 disabled:from-gray-400 disabled:to-gray-400 disabled:cursor-not-allowed text-white font-bold py-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98] disabled:transform-none">
                                <span x-show="!isProcessing" class="flex items-center justify-center gap-2">
                                    <i class="fas fa-receipt text-xl"></i>
                                    <span>Generar Ticket de Venta (Factura)</span>
                                    <span class="text-xs opacity-75">(F9)</span>
                                </span>
                                <span x-show="isProcessing" class="flex items-center justify-center gap-2">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    <span>Procesando...</span>
                                </span>
                            </button>

                            {{-- Acciones rápidas (Limpiar, Cotizar, Facturar) --}}
                            <div class="flex gap-2 pt-2">
                                <button @click="clearCart" 
                                    :disabled="!cart.length"
                                    class="flex-1 px-3 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed text-gray-700 dark:text-gray-300 text-sm rounded-lg transition-colors">
                                    <i class="fas fa-trash-alt mr-1"></i> Limpiar
                                </button>
                                {{-- Cotización --}}
                                <button @click="processSale('QUOTE')" 
                                    :disabled="!isSaleReady('QUOTE') || isProcessing"
                                    class="flex-1 px-3 py-2 bg-blue-500 hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm rounded-lg transition-colors">
                                    <i class="fas fa-file-alt mr-1"></i> Cotizar
                                </button>
                                {{-- Facturar --}}
                                <button @click="processSale('INVOICE')" 
                                    :disabled="!isSaleReady('INVOICE') || isProcessing"
                                    class="flex-1 px-3 py-2 bg-indigo-500 hover:bg-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm rounded-lg transition-colors">
                                    <i class="fas fa-file-invoice-dollar mr-1"></i> Facturar
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Atajos de teclado --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                        <button @click="showKeyboardShortcuts = !showKeyboardShortcuts"
                            class="w-full flex items-center justify-between text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-keyboard"></i>
                                Atajos de Teclado
                            </span>
                            <i class="fas" :class="showKeyboardShortcuts ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                        </button>
                        <div x-show="showKeyboardShortcuts" 
                            x-transition
                            class="mt-3 space-y-2 text-xs">
                            <div class="flex justify-between items-center py-1">
                                <span class="text-gray-600 dark:text-gray-400">Buscar productos</span>
                                <kbd class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded border border-gray-300 dark:border-gray-600">F1</kbd>
                            </div>
                            <div class="flex justify-between items-center py-1">
                                <span class="text-gray-600 dark:text-gray-400">Buscar cliente</span>
                                <kbd class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded border border-gray-300 dark:border-gray-600">F2</kbd>
                            </div>
                            <div class="flex justify-between items-center py-1">
                                <span class="text-gray-600 dark:text-gray-400">Procesar venta (Factura)</span>
                                <kbd class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded border border-gray-300 dark:border-gray-600">F9</kbd>
                            </div>
                            <div class="flex justify-between items-center py-1">
                                <span class="text-gray-600 dark:text-gray-400">Limpiar carrito</span>
                                <kbd class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded border border-gray-300 dark:border-gray-600">Ctrl+X</kbd>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL NUEVO CLIENTE MEJORADO (FUNCIONAL) --}}
        <div x-show="showNewClientModal" x-cloak
            @click.self="closeNewClientModal"
            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 backdrop-blur-sm flex items-center justify-center transition-opacity duration-300 p-4">
            <div x-show="showNewClientModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                @click.stop
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl transform transition-all sm:max-w-lg w-full mx-4 my-8 overflow-hidden">

                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6">
                    <div class="flex justify-between items-center text-white">
                        <h3 class="text-xl font-bold flex items-center gap-2">
                            <i class="fas fa-user-plus"></i> Nuevo Cliente
                        </h3>
                        <button @click="closeNewClientModal" 
                            class="text-white/80 hover:text-white transition-colors p-1 rounded-lg hover:bg-white/10">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <p class="text-indigo-100 text-sm mt-2">Complete la información del cliente</p>
                </div>

                <form @submit.prevent="saveNewClient" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nombre Completo <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" 
                                x-model="newClientForm.nombre" 
                                required
                                placeholder="Ej: Juan Pérez"
                                class="w-full pl-10 pr-3 py-2.5 border rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            RTN
                        </label>
                        <div class="relative">
                            <i class="fas fa-id-card absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" 
                                id="new_client_identificacion" 
                                x-model="newClientForm.identificacion"
                                @input="formatIdentificacion($event, 'newClient')"
                                maxlength="16"
                                placeholder="0615-2003-001441"
                                class="w-full pl-10 pr-3 py-2.5 border rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Formato: 0000-0000-000000</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Email
                            </label>
                            <div class="relative">
                                <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <input type="email" 
                                        x-model="newClientForm.email"
                                        placeholder="correo@ejemplo.com"
                                        class="w-full pl-10 pr-3 py-2.5 border rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Teléfono
                            </label>
                            <div class="relative">
                                <i class="fas fa-phone absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <input type="tel" 
                                        id="new_client_telefono" 
                                        x-model="newClientForm.telefono"
                                        placeholder="9999-9999"
                                        maxlength="9"
                                        @input="formatTelefono($event)"
                                        class="w-full pl-10 pr-3 py-2.5 border rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 gap-3 border-t dark:border-gray-700">
                        <button type="button" 
                                @click="closeNewClientModal"
                                class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg text-gray-800 dark:text-gray-200 font-medium transition-colors">
                            <i class="fas fa-times mr-1"></i> Cancelar
                        </button>
                        <button type="submit"
                                :disabled="newClientSaving"
                                class="px-5 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 disabled:from-gray-500 disabled:to-gray-500 rounded-lg text-white font-medium transition-all shadow-lg hover:shadow-xl transform hover:scale-105 disabled:transform-none disabled:cursor-not-allowed">
                            <span x-show="!newClientSaving" class="flex items-center gap-2">
                                <i class="fas fa-save"></i> Guardar Cliente
                            </span>
                            <span x-show="newClientSaving" class="flex items-center gap-2">
                                <i class="fas fa-spinner fa-spin"></i> Guardando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- SCRIPT JS/ALPINE CON LA LÓGICA COMPLETA Y CORREGIDA --}}
    <script>
        function posModule(tiposPago) {
            return {
                // Estado general
                tiendaId: @json(old('tienda_id', '')),
                searchQuery: '',
                allProductos: [],
                filteredProductos: [],
                cart: [],
                
                // Variables de control para prevenir doble clic/escaneo rápido
                lastAddedProductId: null, 
                lastAddedTimestamp: 0, 

                // **NUEVAS VARIABLES DE CONTROL PARA EL CARRITO**
                lastCartUpdateId: null,
                lastCartUpdateTimestamp: 0,

                // Totales
                total: 0.00, 
                discount: 0,
                finalTotal: 0.00,

                // ESTADOS FISCALES
                subtotalNeto: 0.00,     
                totalExento: 0.00,      
                totalGravado: 0.00,     
                totalIsv: 0.00,         
                totalImpuestos: 0.00,   

                isProcessing: false,
                isProductsLoading: false,
                showKeyboardShortcuts: false,

                // Cliente
                clientId: null,
                clientSearchQuery: '',
                clientSearchResults: [],
                selectedClientName: 'Cliente Genérico / Sin Registro',
                selectedClientIndex: 0,
                isClientLoading: false,

                // Modal cliente
                showNewClientModal: false,
                newClientSaving: false,
                newClientError: '', 
                newClientForm: { nombre: '', identificacion: '', email: '', telefono: '' },
                
                // ESTADO DE PAGO
                tiposPago: tiposPago, 
                selectedPaymentType: 'EFECTIVO', // Valor por defecto

                init() {
                    if (this.tiendaId) this.fetchProductos();
                    this.updateCart();
                    this.showWelcomeNotification();
                },

                // ==========================================
                // LÓGICA DE PAGO Y HABILITACIÓN DE BOTONES
                // ==========================================
                isSaleReady(type) {
                    if (!this.cart.length || this.tiendaId === '') return false;
                    if (!this.selectedPaymentType) return false;
                    
                    // Factura y Cotización requieren cliente registrado
                    if ((type === 'INVOICE' || type === 'QUOTE') && !(this.clientId > 0)) return false;

                    // Crédito requiere cliente registrado
                    if (this.selectedPaymentType === 'CREDITO' && !(this.clientId > 0)) return false;

                    return true;
                },
                
                // ==========================================
                // NOTIFICACIONES Y MENSAJES
                // ==========================================
                showWelcomeNotification() {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });

                    Toast.fire({
                        icon: 'info',
                        title: '¡Bienvenido al POS!',
                        text: 'Presiona F1 para buscar productos'
                    });
                },

                showNotification(type, title, text) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });

                    Toast.fire({ icon: type, title: title, text: text });
                },

                // ==========================================
                // MANEJO DE TECLADO
                // ==========================================
                handleKeyboard(event) {
                    if (event.key === 'F1') {
                        event.preventDefault();
                        if (this.tiendaId) {
                            this.$refs.productSearch?.focus();
                            this.showNotification('info', 'Búsqueda de productos', 'Escribe para buscar');
                        }
                    }
                    
                    if (event.key === 'F2') {
                        event.preventDefault();
                        document.getElementById('client_search_input')?.focus();
                        this.showNotification('info', 'Búsqueda de clientes', 'Busca por nombre o RTN');
                    }
                    
                    if (event.key === 'F9') {
                        event.preventDefault();
                        if (this.isSaleReady('TICKET') && !this.isProcessing) {
                            this.processSale('TICKET');
                        }
                    }
                    
                    if (event.ctrlKey && event.key === 'x') {
                        event.preventDefault();
                        if (this.cart.length) {
                            this.clearCart();
                        }
                    }

                    if (event.key === 'Escape') {
                        if (this.showNewClientModal) {
                            this.closeNewClientModal();
                        }
                    }

                    // Navegación en resultados de clientes (solo si el dropdown está visible)
                    if (document.activeElement.id === 'client_search_input' && this.clientSearchResults.length > 0) {
                        if (event.key === 'ArrowDown') {
                            event.preventDefault();
                            this.selectedClientIndex = Math.min(
                                this.selectedClientIndex + 1, 
                                this.clientSearchResults.length - 1
                            );
                        } else if (event.key === 'ArrowUp') {
                            event.preventDefault();
                            this.selectedClientIndex = Math.max(this.selectedClientIndex - 1, 0);
                        } else if (event.key === 'Enter') {
                            event.preventDefault();
                            this.selectClient(this.clientSearchResults[this.selectedClientIndex]);
                        }
                    }
                },

                // ==========================================
                // PRODUCTOS
                // ==========================================
                async fetchProductos() {
                    this.searchQuery = '';
                    this.allProductos = [];
                    this.filteredProductos = [];
                    this.cart = [];
                    this.updateCart();
                    this.isProductsLoading = true;

                    if (!this.tiendaId) {
                        this.isProductsLoading = false;
                        return;
                    }

                    try {
                        const res = await fetch(`{{ url('ventas/productos-por-tienda') }}/${this.tiendaId}`);
                        if (!res.ok) throw new Error('Error de red o de servidor al obtener productos');
                        const data = await res.json();
                        
                        // Capturamos la tasa ISV real del backend
                        const productos = data.map(p => ({
                            ...p,
                            // Convertir la tasa a float, debe venir del backend.
                            isv_tasa: p.isv_tasa !== undefined ? parseFloat(p.isv_tasa) : 0.00,
                        }));
                        
                        this.allProductos = productos;
                        this.filteredProductos = productos;
                        
                        this.showNotification('success', 'Productos cargados', `${data.length} productos disponibles`);
                    } catch (err) {
                        console.error('Error al cargar productos:', err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: err.message || 'No se pudieron cargar los productos. Intente nuevamente.',
                            confirmButtonColor: '#ef4444'
                        });
                    } finally {
                        this.isProductsLoading = false;
                    }
                },

                filterProductos() {
                    const q = (this.searchQuery || '').toLowerCase().trim();
                    
                    if (!q) {
                        this.filteredProductos = this.allProductos;
                        return;
                    }

                    this.filteredProductos = this.allProductos.filter(p => {
                        const nombre = (p.producto_nombre || '').toLowerCase();
                        const codigo = (p.codigo_marca || '').toLowerCase();
                        
                        return nombre.includes(q) || codigo.includes(q);
                    });

                    // Si hay un solo resultado y el query coincide exactamente con el código de barras, agrégalo al carrito
                    if (this.filteredProductos.length === 1) {
                        const producto = this.filteredProductos[0];
                        if (producto.codigo_marca && producto.codigo_marca.toLowerCase() === q) {
                            this.addToCart(producto);
                            this.clearSearch(); // Limpiar búsqueda después de escanear/añadir
                        }
                    }
                },

                clearSearch() {
                    this.searchQuery = '';
                    this.filteredProductos = this.allProductos;
                },

                // ==========================================
                // CLIENTES
                // ==========================================
                async onClientSearchInput() {
                    this.clientSearchQuery = this.clientSearchQuery || '';
                    this.selectedClientIndex = 0;
                    
                    if (this.clientSearchQuery.length < 2) {
                        this.clientSearchResults = [];
                        return;
                    }

                    this.isClientLoading = true;

                    try {
                        const res = await fetch(
                            `{{ route('ventas.buscar-clientes') }}?query=${encodeURIComponent(this.clientSearchQuery)}`
                        );
                        
                        if (!res.ok) {
                            const errorData = await res.json().catch(() => ({ message: `Error ${res.status}: El servidor no devolvió JSON esperado.` }));
                            throw new Error(errorData.message || 'Error de red o de servidor');
                        }
                        
                        const data = await res.json();
                        this.clientSearchResults = data;
                    } catch (err) {
                        console.error('Error buscando clientes:', err);
                        this.clientSearchResults = [];
                        this.showNotification('error', 'Error', err.message || 'No se pudieron buscar clientes');
                    } finally {
                        this.isClientLoading = false;
                    }
                },

                selectClient(client) {
                    this.clientId = client.id > 0 ? parseInt(client.id) : null; 
                    
                    this.selectedClientName = `${client.nombre}${client.identificacion ? ' (' + client.identificacion + ')' : ''}`;
                    this.clientSearchQuery = ''; 
                    this.clientSearchResults = [];
                    this.selectedClientIndex = 0;
                    
                    this.showNotification('success', 'Cliente seleccionado', client.nombre);
                    this.updateCart();
                },
                
                setClientAfterModal(client) {
                    if (!client || !client.id || isNaN(parseInt(client.id))) {
                        console.error('Error: Cliente creado no devolvió un ID válido.');
                        this.clearClient(); 
                        return;
                    }
                    
                    this.clientId = parseInt(client.id); 
                    this.selectedClientName = `${client.nombre}${client.identificacion ? ' (' + client.identificacion + ')' : ''}`;
                    this.clientSearchQuery = ''; 
                    this.clientSearchResults = [];
                    this.updateCart(); 
                    
                    this.$nextTick(() => {
                        this.$refs.productSearch?.focus();
                    });
                },

                clearClientSearch() {
                    this.clientSearchQuery = '';
                    this.clientSearchResults = [];
                    this.selectedClientIndex = 0;
                },

                clearClient() {
                    this.clientId = null;
                    this.selectedClientName = 'Cliente Genérico / Sin Registro';
                    this.clientSearchQuery = '';
                    this.clientSearchResults = [];
                    this.updateCart();
                },

                // ==========================================
                // FORMATO DE CAMPOS
                // ==========================================
                formatIdentificacion(event, fieldType) {
                    let value = event.target.value.replace(/\D/g, '');
                    
                    if (value.length > 4 && value.length <= 8) {
                        value = value.replace(/^(\d{4})(\d+)/, '$1-$2');
                    } else if (value.length > 8) {
                        value = value.replace(/^(\d{4})(\d{4})(\d{0,6}).*/, '$1-$2-$3');
                    }
                    
                    if (fieldType === 'clientSearch') {
                        this.clientSearchQuery = value;
                        this.$nextTick(() => {
                            const el = document.getElementById('client_search_input');
                            if (el) el.value = value;
                        });
                    } else if (fieldType === 'newClient') {
                        this.newClientForm.identificacion = value;
                        this.$nextTick(() => {
                            const el = document.getElementById('new_client_identificacion');
                            if (el) el.value = value;
                        });
                    }
                },

                formatTelefono(event) {
                    let value = event.target.value.replace(/\D/g, '');
                    
                    if (value.length > 4) {
                        value = value.replace(/^(\d{4})(\d{0,4}).*/, '$1-$2');
                    }
                    
                    this.newClientForm.telefono = value;
                    this.$nextTick(() => {
                        const el = document.getElementById('new_client_telefono');
                        if (el) el.value = value;
                    });
                },

                // ==========================================
                // MODAL NUEVO CLIENTE (CORREGIDO)
                // ==========================================
                openNewClientModal() {
                    this.resetNewClientForm(); // <-- Resetear antes de abrir
                    this.showNewClientModal = true;
                    
                    // Enfoca el primer campo del modal al abrir (Mejora UX)
                    this.$nextTick(() => {
                        document.querySelector('input[x-model="newClientForm.nombre"]').focus();
                    });
                },

                closeNewClientModal() {
                    this.showNewClientModal = false;
                    this.resetNewClientForm(); // <-- Resetear después de cerrar
                },

                resetNewClientForm() {
                    // Asegura que todos los campos del formulario modal estén en blanco
                    this.newClientForm = { nombre: '', identificacion: '', email: '', telefono: '' };
                    this.newClientError = '';
                },

                async saveNewClient() {
                    this.newClientSaving = true;
                    this.newClientError = ''; 

                    const payload = {
                        _token: '{{ csrf_token() }}',
                        ...this.newClientForm
                    };

                    try {
                        const response = await fetch('{{ route('ventas.store-cliente') }}', {
                            method: 'POST',
                            headers: {  
                                'Content-Type': 'application/json',  
                                'X-Requested-With': 'XMLHttpRequest'  
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await response.json();

                        // 1. EVALUACIÓN DE ÉXITO (Código 200-299 Y success: true)
                        if (response.ok && data.success) {
                            
                            window.dispatchEvent(new CustomEvent('new-client-saved', {
                                detail: { client: data.cliente }
                            }));
                            
                            // Muestra la alerta de éxito
                            Swal.fire({
                                icon: 'success',
                                title: '¡Cliente Registrado!',
                                text: `${data.cliente.nombre} ha sido guardado y seleccionado.`,
                                timer: 2000,
                                showConfirmButton: false,
                                position: 'top-end',
                                toast: true
                            });
                            
                            this.$nextTick(() => {
                                setTimeout(() => {
                                    this.closeNewClientModal(); // Cierra el modal solo en caso de éxito
                                }, 100); 
                            });

                        } else if (response.status === 422) {
                            // 2. LÓGICA DE ERROR DE VALIDACIÓN (422)
                            console.error('Error de validación 422 (Cliente):', data.errors);
                            
                            // Si falla por validación (ej. duplicado), cerramos el modal para seguir el flujo
                            this.$nextTick(() => {
                                setTimeout(() => {
                                    this.closeNewClientModal(); 
                                }, 100); 
                            });
                            
                        } else {
                            // 3. OTROS ERRORES DE SERVIDOR (500, 400, etc.)
                            console.error('Error de servidor (Cliente):', data);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Ocurrió un error al guardar el cliente.',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    } catch (err) {
                        // 4. ERROR CRÍTICO
                        console.error('Error de conexión o JSON inválido:', err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error Crítico',
                            text: 'Error de conexión. Intente nuevamente o revise la consola (F12).',
                            confirmButtonColor: '#ef4444'
                        });
                    } finally {
                        this.newClientSaving = false;
                    }
                },

                // ==========================================
                // CARRITO Y CÁLCULOS
                // ==========================================
                addToCart(producto) {
                    if (producto.stock_actual === 0) {
                        this.showNotification('warning', 'Sin stock', 'Este producto no tiene stock disponible');
                        return;
                    }

                    // Lógica para prevenir doble clic/escaneo rápido (CORRECCIÓN LISTADO PRODUCTOS)
                    const now = Date.now();
                    if (this.lastAddedProductId === producto.inventario_id && (now - this.lastAddedTimestamp) < 200) {
                        return; // Ignora la segunda llamada rápida
                    }
                    
                    // Actualiza el estado para la siguiente llamada
                    this.lastAddedProductId = producto.inventario_id;
                    this.lastAddedTimestamp = now;

                    const existing = this.cart.find(i => i.inventario_id === producto.inventario_id);
                    
                    if (existing) {
                        if (existing.cantidad < existing.stockMax) {
                            existing.cantidad = existing.cantidad + 1; 
                            this.showNotification('success', 'Cantidad actualizada', `${producto.producto_nombre} (${existing.cantidad})`);
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Stock máximo',
                                text: `Solo hay ${existing.stockMax} unidades disponibles.`,
                                timer: 2000,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                        }
                    } else {
                        this.cart.push({
                            inventario_id: producto.inventario_id,
                            nombre: producto.producto_nombre,
                            precio: producto.precio,
                            stockMax: producto.stock_actual,
                            cantidad: 1, // Cantidad inicial es 1
                            isv_tasa: producto.isv_tasa 
                        });
                        this.showNotification('success', 'Producto añadido', producto.producto_nombre);
                    }
                    
                    this.updateCart();
                },

                removeFromCart(index) {
                    const item = this.cart[index];
                    
                    Swal.fire({
                        title: '¿Eliminar producto?',
                        text: `Se eliminará "${item.nombre}" del carrito`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.cart.splice(index, 1);
                            this.updateCart();
                            this.showNotification('info', 'Producto eliminado', item.nombre);
                        }
                    });
                },

                // FUNCIÓN CORREGIDA PARA PREVENIR DOBLE INCREMENTO
                incrementQuantity(index, maxStock) {
                    const item = this.cart[index];
                    const now = Date.now();

                    // Control de tiempo para prevenir doble clic en el mismo item
                    if (this.lastCartUpdateId === item.inventario_id && (now - this.lastCartUpdateTimestamp) < 200) {
                        return; 
                    }
                    
                    this.lastCartUpdateId = item.inventario_id;
                    this.lastCartUpdateTimestamp = now;
                    // Fin de control

                    if (item.cantidad < maxStock) {
                        item.cantidad++; 
                        this.updateCart();
                    } else {
                        this.showNotification('warning', 'Stock máximo', 'No hay más unidades disponibles');
                    }
                },

                // FUNCIÓN CORREGIDA PARA PREVENIR DOBLE DECREMENTO
                decrementQuantity(index) {
                    const item = this.cart[index];
                    const now = Date.now();

                    // Control de tiempo para prevenir doble clic en el mismo item
                    if (this.lastCartUpdateId === item.inventario_id && (now - this.lastCartUpdateTimestamp) < 200) {
                        return;
                    }
                    
                    this.lastCartUpdateId = item.inventario_id;
                    this.lastCartUpdateTimestamp = now;
                    // Fin de control

                    if (item.cantidad > 1) {
                        item.cantidad--; 
                        this.updateCart();
                    }
                },

                // 🔑 FUNCIÓN UPDATE CART CORREGIDA PARA CÁLCULOS FISCALES Y DE PRECIO
                updateCart() {
                    this.cart.forEach(item => {
                        if (typeof item.cantidad !== 'number' || item.cantidad < 1 || isNaN(item.cantidad)) {
                            item.cantidad = 1;
                        }
                        if (item.cantidad > item.stockMax) {
                            item.cantidad = item.stockMax;
                        }
                        item.cantidad = parseInt(item.cantidad);
                    });

                    let subtotalNeto = 0;
                    let totalExento = 0;
                    let totalGravado = 0;
                    let totalIsv = 0;
                    
                    this.cart.forEach(item => {
                        // 1. Asegurar que precio y cantidad sean números válidos
                        const precioUnitario = parseFloat(item.precio) || 0;
                        const cantidad = parseInt(item.cantidad) || 0;
                        
                        const base = precioUnitario * cantidad; // Base neta de la línea (100 * 1 = 100)
                        const tasa = parseFloat(item.isv_tasa) || 0.00;
                        const isvMonto = base * tasa;
                        
                        // 2. Acumulación de totales
                        subtotalNeto += base;  
                        totalIsv += isvMonto;

                        // 3. Acumulación de subtotales por tipo de impuesto (Gravado/Exento)
                        if (tasa > 0) {
                            totalGravado += base;
                        } else {
                            totalExento += base;
                        }
                        
                        // 4. Actualizar propiedades del ítem (Para mostrar Subtotal L 100.00)
                        item.subtotalBase = base;
                        item.isvMonto = isvMonto;
                    });

                    // 5. Redondeo final y asignación de variables
                    this.subtotalNeto = parseFloat(subtotalNeto.toFixed(2));
                    this.totalExento = parseFloat(totalExento.toFixed(2));
                    this.totalGravado = parseFloat(totalGravado.toFixed(2));
                    this.totalIsv = parseFloat(totalIsv.toFixed(2));
                    this.totalImpuestos = this.totalIsv;  

                    const totalConIsv = this.subtotalNeto + this.totalIsv;
                    this.total = parseFloat(totalConIsv.toFixed(2));

                    let currentDiscount = parseFloat(this.discount) || 0;  

                    if (currentDiscount < 0) currentDiscount = 0;
                    if (currentDiscount > totalConIsv) currentDiscount = totalConIsv;
                    this.discount = parseFloat(currentDiscount.toFixed(2));  
                    
                    this.finalTotal = parseFloat((totalConIsv - this.discount).toFixed(2));
                },

                clearCart() {
                    if (!this.cart.length) return;

                    Swal.fire({
                        title: '¿Vaciar el carrito?',
                        text: 'Se eliminarán todos los productos del carrito',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Sí, vaciar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.cart = [];
                            this.discount = 0;
                            this.selectedPaymentType = 'EFECTIVO';
                            this.updateCart();
                            this.showNotification('info', 'Carrito vacío', 'Se han eliminado todos los productos');
                        }
                    });
                },

                // ==========================================
                // PROCESAR VENTA / COTIZACIÓN / FACTURA
                // ==========================================
                async processSale(type) {
                    if (!this.isSaleReady(type) || this.isProcessing) {
                        if (!this.cart.length || this.tiendaId === '') {
                             Swal.fire({
                                icon: 'warning',
                                title: 'Faltan datos',
                                text: 'Seleccione una tienda y añada productos al carrito para continuar.',
                                confirmButtonColor: '#f59e0b'
                            });
                        } else if (!this.selectedPaymentType) {
                             Swal.fire({
                                icon: 'warning',
                                title: 'Tipo de Pago Requerido',
                                text: 'Debe seleccionar un tipo de pago antes de procesar la venta.',
                                confirmButtonColor: '#f59e0b'
                            });
                        } else if (this.selectedPaymentType === 'CREDITO' && !(this.clientId > 0)) {
                             Swal.fire({
                                icon: 'info',
                                title: 'Cliente Requerido',
                                text: 'Para ventas a Crédito, debe seleccionar un cliente registrado.',
                                confirmButtonColor: '#3b82f6'
                            });
                        } else if ((type === 'INVOICE' || type === 'QUOTE') && !(this.clientId > 0)) {
                             Swal.fire({
                                icon: 'info',
                                title: `Cliente Requerido`,
                                text: `Seleccione un cliente para generar la ${type === 'INVOICE' ? 'Factura' : 'Cotización'}.`,
                                confirmButtonColor: '#3b82f6'
                            });
                        }
                        return;
                    }

                    // 🔑 CLAVE 1: Usar "Factura" como nombre visible para TICKET en la confirmación de SweetAlert
                    const titleMap = {
                        'TICKET': '¿Confirmar Factura (Consumidor Final)?', // Etiqueta fiscal visible
                        'QUOTE': '¿Guardar como Cotización?',
                        'INVOICE': '¿Generar Factura?'
                    };
                    
                    const endpointMap = {
                        'TICKET': '{{ route('ventas.store-ticket') }}',  
                        'QUOTE': '{{ route('ventas.store-quote') }}',  
                        'INVOICE': '{{ route('ventas.store-invoice') }}'
                    };
                    
                    const successColorMap = {
                        'TICKET': '#10b981',  
                        'QUOTE': '#3b82f6',  
                        'INVOICE': '#4f46e5'  
                    };
                    const confirmButtonTextMap = {
                        'TICKET': 'Sí, procesar',
                        'QUOTE': 'Sí, guardar',
                        'INVOICE': 'Sí, facturar'
                    };

                    const result = await Swal.fire({
                        title: titleMap[type],
                        html: `
                            <div class="text-left space-y-2">
                                <p class="text-sm text-gray-600">
                                    <strong>Tipo de Pago:</strong> ${this.tiposPago[this.selectedPaymentType] || 'N/A'}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <strong>Cliente:</strong> ${this.selectedClientName}
                                </p>
                                <p class="text-lg font-bold text-emerald-600 mt-3 pt-3 border-t">
                                    <strong>Total:</strong> L ${this.finalTotal.toFixed(2)}
                                </p>
                            </div>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: successColorMap[type],
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: `<i class="fas fa-check mr-2"></i> ${confirmButtonTextMap[type]}`,
                        cancelButtonText: '<i class="fas fa-times mr-2"></i> Cancelar',
                        customClass: {
                            popup: 'swal-wide'
                        }
                    });

                    if (!result.isConfirmed) return;

                    this.isProcessing = true;
                    const url = endpointMap[type];

                    const detalles = this.cart.map(item => ({
                        inventario_id: item.inventario_id,
                        cantidad: item.cantidad,
                        precio_unitario: item.precio,
                        isv_tasa: item.isv_tasa  
                    }));

                    const payload = {
                        _token: '{{ csrf_token() }}',
                        tienda_id: this.tiendaId,
                        cliente_id: this.clientId,
                        tipo_documento: type,  
                        total_monto: this.total, 
                        descuento: this.discount,
                        detalles: detalles,
                        tipo_pago: this.selectedPaymentType  
                    };

                    try {
                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {  
                                'Content-Type': 'application/json',  
                                'X-Requested-With': 'XMLHttpRequest'  
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await res.json().catch(err => {
                            console.error("Error de JSON.parse en processSale", err);
                            throw new Error(`El servidor devolvió un error de formato (${res.status}). Revise los logs del servidor.`);
                        });

                        if (res.ok && data.success) {
                            // Usamos el tipo real para los colores y mensajes
                            const actualType = type; 

                            await Swal.fire({
                                icon: 'success',
                                title: data.documento_id ? `Transacción #${data.documento_id}` : '¡Transacción Procesada!',
                                html: `<p class="text-gray-700">${data.message}</p>` + 
                                            (data.documento_url ? `<a href="${data.documento_url}" target="_blank" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"><i class="fas fa-print mr-2"></i> Imprimir / Descargar</a>` : ''),
                                confirmButtonColor: successColorMap[actualType],
                                confirmButtonText: 'Hecho'
                            });

                            // Resetear estado después de la venta
                            this.cart = [];
                            this.discount = 0;
                            this.selectedPaymentType = 'EFECTIVO';
                            this.updateCart();
                            this.fetchProductos();
                            this.clearClient();

                        } else if (res.status === 422) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Error de Validación',
                                text: data.message || `No se pudo procesar la solicitud de ${type}.`,
                                confirmButtonColor: '#f59e0b'
                            });
                            this.fetchProductos();
                            
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || `Ocurrió un error al procesar la solicitud de ${type}.`,
                                confirmButtonColor: '#ef4444'
                            });
                        }

                    } catch (err) {
                        console.error(`Error en processSale:`, err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error Crítico',
                            text: err.message || 'Error de conexión con el servidor o JSON inválido en la respuesta. Por favor, revise la Consola del navegador (F12).',
                            confirmButtonColor: '#ef4444'
                        });
                    } finally {
                        this.isProcessing = false;
                    }
                }
            };
        }
    </script>

    {{-- Estilos CSS --}}
    <style>
        /* Animaciones y Estilos CSS (Incluidos) */
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* Scrollbar personalizado */
        .overflow-y-auto::-webkit-scrollbar {
            width: 8px;
        }

        .overflow-y-auto::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 10px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.3);
        }

        .dark .overflow-y-auto::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        .dark .overflow-y-auto::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
        }

        .dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Efecto de pulso para elementos importantes */
        @keyframes pulse-slow {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.8;
            }
        }

        .animate-pulse-slow {
            animation: pulse-slow 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Estilos para el modal de SweetAlert */
        .swal-wide {
            width: 600px !important;
        }

        /* Mejora de contraste en modo oscuro */
        .dark input:focus,
        .dark select:focus,
        .dark textarea:focus {
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.3);
        }

        /* Transiciones suaves */
        * {
            transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }

        /* Ocultar flechas de número en inputs */
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
        }

        /* Indicador de carga */
        .fa-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        /* Efectos hover mejorados */
        button:not(:disabled):hover {
            transform: translateY(-1px);
        }

        button:not(:disabled):active {
            transform: translateY(0);
        }

        /* Badge de stock bajo */
        .stock-warning {
            position: relative;
        }

        .stock-warning::after {
            content: '!';
            position: absolute;
            top: -5px;
            right: -5px;
            width: 16px;
            height: 16px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: bold;
        }
    </style>
</x-app-layout>