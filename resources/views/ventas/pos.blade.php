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
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- CONFIGURACIÓN DE RUTAS PARA EL JS --}}
    <script>
        window.posRoutes = {
            productsByStore: '{{ url('ventas/productos-por-tienda') }}',
            searchClients: '{{ route('ventas.buscar-clientes') }}',
            storeClient: '{{ route('ventas.store-cliente') }}',
            storeTicket: '{{ route('ventas.store-ticket') }}',
            storeQuote: '{{ route('ventas.store-quote') }}',
            storeInvoice: '{{ route('ventas.store-invoice') }}',
            csrfToken: '{{ csrf_token() }}'
        };
    </script>

    {{-- ARCHIVO JS EXTERNO --}}
    <script src="{{ asset('js/ventas/pos.js') }}"></script>

    {{-- Contenedor principal con estado Alpine.js --}}
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

                    {{-- Sección: Búsqueda de Productos --}}
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

                        {{-- Lista de productos --}}
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

                    {{-- Mensaje inicial --}}
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

                {{-- COLUMNA DERECHA: CARRITO --}}
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
                            
                            {{-- Selector de tipo de pago --}}
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

                            {{-- Monto recibido y vuelto --}}
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

                                {{-- Vuelto --}}
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

                            {{-- Descuento --}}
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
                                {{-- Subtotal Neto --}}
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-600 dark:text-gray-400">SubTotal Base (sin ISV):</span>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">L <span x-text="subtotalNeto.toFixed(2)"></span></span>
                                </div>
                                
                                {{-- Total Exonerado --}}
                                <div class="flex justify-between items-center text-xs text-gray-500 dark:text-gray-400" x-show="totalExento > 0">
                                    <span>Importe Exonerado/Exento:</span>
                                    <span>L <span x-text="totalExento.toFixed(2)"></span></span>
                                </div>

                                {{-- Total Gravado --}}
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

                            {{-- Botón procesar venta --}}
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

                            {{-- Acciones rápidas --}}
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

        {{-- MODAL NUEVO CLIENTE --}}
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

    {{-- ESTILOS CSS --}}
    <style>
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