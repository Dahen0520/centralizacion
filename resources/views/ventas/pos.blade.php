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

    {{-- INCLUSIÓN DE SWEETALERT2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6" x-data="posModule()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- COLUMNA IZQUIERDA: PRODUCTOS --}}
                <div class="lg:col-span-2 space-y-4">
                    
                    {{-- Card: Selección de Tienda --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <i class="fas fa-store text-emerald-600 dark:text-emerald-400"></i>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Selecciona la Tienda</h3>
                        </div>
                        <select x-model="tiendaId" @change="fetchProductos" 
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-gray-900 dark:text-white">
                            <option value="">-- Seleccione una tienda --</option>
                            @foreach($tiendas as $tienda)
                                <option value="{{ $tienda->id }}">{{ $tienda->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Card: Búsqueda y Productos --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden" x-show="tiendaId" x-cloak>
                        
                        {{-- Barra de búsqueda --}}
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="relative">
                                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <input type="text" x-model="searchQuery" @input.debounce.300ms="filterProductos" 
                                       placeholder="Buscar por nombre o código..."
                                       class="w-full pl-12 pr-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-gray-900 dark:text-white">
                            </div>
                        </div>

                        {{-- Lista de productos --}}
                        <div class="p-4 max-h-[500px] overflow-y-auto">
                            <template x-for="producto in filteredProductos" :key="producto.inventario_id">
                                <div class="group bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 mb-3 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors border border-transparent hover:border-emerald-200 dark:hover:border-emerald-800">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1 min-w-0 mr-4">
                                            <h4 class="font-semibold text-gray-900 dark:text-white mb-1 truncate" x-text="producto.producto_nombre"></h4>
                                            <div class="flex flex-wrap items-center gap-3 text-xs text-gray-600 dark:text-gray-400">
                                                <span class="flex items-center gap-1">
                                                    <i class="fas fa-barcode"></i>
                                                    <span x-text="producto.codigo_marca"></span>
                                                </span>
                                                <span class="flex items-center gap-1">
                                                    <i class="fas fa-box"></i>
                                                    <span x-text="producto.stock_actual"></span> disponibles
                                                </span>
                                                <span class="font-bold text-emerald-600 dark:text-emerald-400">
                                                    L <span x-text="producto.precio.toFixed(2)"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <button @click="addToCart(producto)" 
                                                :disabled="producto.stock_actual === 0"
                                                class="flex-shrink-0 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white rounded-lg font-medium transition-colors shadow-sm hover:shadow-md">
                                            <i class="fas fa-plus mr-1"></i>
                                            Añadir
                                        </button>
                                    </div>
                                </div>
                            </template>
                            
                            {{-- Estados vacíos --}}
                            <div x-show="!filteredProductos.length && searchQuery && tiendaId" class="text-center py-12">
                                <i class="fas fa-search text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                                <p class="text-gray-500 dark:text-gray-400">No se encontraron productos</p>
                            </div>
                            <div x-show="tiendaId && !searchQuery && !allProductos.length" class="text-center py-12">
                                <i class="fas fa-box-open text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                                <p class="text-gray-500 dark:text-gray-400">No hay productos con stock en esta tienda</p>
                            </div>
                        </div>
                    </div>

                    {{-- Mensaje inicial --}}
                    <div x-show="!tiendaId" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                        <i class="fas fa-arrow-up text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                        <p class="text-gray-600 dark:text-gray-400">Selecciona una tienda para comenzar</p>
                    </div>
                </div>

                {{-- COLUMNA DERECHA: CARRITO --}}
                <div class="space-y-4">
                    
                    {{-- Card: Carrito --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden sticky top-6">
                        
                        {{-- Header del carrito --}}
                        <div class="p-6 bg-gradient-to-r from-emerald-500 to-teal-600">
                            <div class="flex items-center justify-between text-white">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-shopping-cart text-xl"></i>
                                    <h3 class="text-lg font-semibold">Carrito</h3>
                                </div>
                                <span class="px-3 py-1 bg-white/20 rounded-full text-sm font-bold" x-text="cart.length + ' items'"></span>
                            </div>
                        </div>

                        {{-- Items del carrito --}}
                        <div class="p-4 max-h-[400px] overflow-y-auto">
                            <template x-for="(item, index) in cart" :key="item.inventario_id">
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 mb-3 border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1 min-w-0 mr-2">
                                            <h4 class="font-semibold text-sm text-gray-900 dark:text-white mb-1 truncate" x-text="item.nombre"></h4>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                                L <span x-text="item.precio.toFixed(2)"></span> c/u
                                                <span class="ml-2 text-gray-500">· Max: <span x-text="item.stockMax"></span></span>
                                            </p>
                                        </div>
                                        <button @click="removeFromCart(index)" 
                                                class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors p-1">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <label class="text-xs text-gray-600 dark:text-gray-400">Cantidad:</label>
                                            <input type="number" x-model.number="item.cantidad" min="1" :max="item.stockMax"
                                                   @input="updateCart"
                                                   class="w-20 px-3 py-1.5 text-center text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 text-gray-900 dark:text-white">
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Subtotal</p>
                                            <p class="font-bold text-emerald-600 dark:text-emerald-400" x-text="'L ' + (item.precio * item.cantidad).toFixed(2)"></p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            
                            {{-- Carrito vacío --}}
                            <div x-show="!cart.length" class="text-center py-12">
                                <i class="fas fa-shopping-cart text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                                <p class="text-gray-500 dark:text-gray-400 text-sm">Tu carrito está vacío</p>
                            </div>
                        </div>

                        {{-- Footer: Total y botón --}}
                        <div class="p-6 border-t border-gray-200 dark:border-gray-700 space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400 font-medium">Total:</span>
                                <span class="text-3xl font-bold text-gray-900 dark:text-white">
                                    L <span x-text="total.toFixed(2)"></span>
                                </span>
                            </div>
                            
                            <button @click="processSale" 
                                    :disabled="!cart.length || isProcessing"
                                    class="w-full bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 disabled:from-gray-400 disabled:to-gray-400 disabled:cursor-not-allowed text-white font-bold py-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02] disabled:transform-none">
                                <span x-show="!isProcessing" class="flex items-center justify-center gap-2">
                                    <i class="fas fa-cash-register"></i>
                                    Procesar Venta
                                </span>
                                <span x-show="isProcessing" class="flex items-center justify-center gap-2">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    Procesando...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- SCRIPT JAVASCRIPT/ALPINE.JS --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('posModule', () => ({
                tiendaId: @json(old('tienda_id', '')),
                searchQuery: '',
                allProductos: [],
                filteredProductos: [],
                cart: [],
                total: 0.00,
                isProcessing: false,

                init() {
                    if (this.tiendaId) {
                        this.fetchProductos();
                    }
                },

                // -------------------------
                // 1. LÓGICA DE DATOS Y FETCH
                // -------------------------
                fetchProductos() {
                    this.searchQuery = '';
                    this.allProductos = [];
                    this.filteredProductos = [];
                    this.cart = []; 
                    this.updateCart();

                    if (!this.tiendaId) return;

                    fetch(`{{ url('ventas/productos-por-tienda') }}/${this.tiendaId}`)
                        .then(response => {
                            if (!response.ok) throw new Error('Network response was not ok');
                            return response.json();
                        })
                        .then(data => {
                            this.allProductos = data;
                            this.filteredProductos = data;
                        })
                        .catch(error => {
                            console.error('Error fetching products:', error);
                            Swal.fire('Error', 'No se pudieron cargar los productos de inventario para esta tienda.', 'error');
                        });
                },

                filterProductos() {
                    if (!this.searchQuery) {
                        this.filteredProductos = this.allProductos;
                        return;
                    }
                    const query = this.searchQuery.toLowerCase();
                    this.filteredProductos = this.allProductos.filter(p =>
                        p.producto_nombre.toLowerCase().includes(query) ||
                        p.codigo_marca.toLowerCase().includes(query)
                    );
                },
                
                // -------------------------
                // 2. LÓGICA DEL CARRITO
                // -------------------------
                addToCart(producto) {
                    const existingItem = this.cart.find(item => item.inventario_id === producto.inventario_id);

                    if (existingItem) {
                        if (existingItem.cantidad < existingItem.stockMax) {
                            existingItem.cantidad++;
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Stock máximo alcanzado',
                                text: `Solo puedes añadir ${existingItem.stockMax} unidades de ${producto.producto_nombre}.`,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    } else {
                        this.cart.push({
                            inventario_id: producto.inventario_id,
                            nombre: producto.producto_nombre,
                            precio: producto.precio,
                            stockMax: producto.stock_actual,
                            cantidad: 1,
                        });
                    }
                    this.updateCart();
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                    this.updateCart();
                },

                updateCart() {
                    // 1. Asegurar que la cantidad sea un número válido y no exceda el stock
                    this.cart.forEach(item => {
                        if (typeof item.cantidad !== 'number' || item.cantidad < 1) {
                            item.cantidad = 1;
                        }
                        if (item.cantidad > item.stockMax) {
                            item.cantidad = item.stockMax;
                        }
                    });
                    // 2. Calcular el total
                    this.total = this.cart.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
                },

                // -------------------------
                // 3. PROCESAMIENTO DE VENTA (AJAX POST)
                // -------------------------
                async processSale() {
                    if (!this.cart.length || this.isProcessing) return;
                    if (!this.tiendaId) {
                        Swal.fire('Advertencia', 'Debe seleccionar una tienda para procesar la venta.', 'warning');
                        return;
                    }

                    this.isProcessing = true;

                    const detalles = this.cart.map(item => ({
                        inventario_id: item.inventario_id,
                        cantidad: item.cantidad,
                        precio_unitario: item.precio,
                    }));

                    const payload = {
                        _token: '{{ csrf_token() }}',
                        tienda_id: this.tiendaId,
                        total_venta: this.total,
                        detalles: detalles
                    };

                    try {
                        const response = await fetch('{{ route('ventas.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Venta Exitosa!',
                                text: data.message,
                                confirmButtonColor: '#10b981'
                            });
                            // Limpiar y recargar el inventario
                            this.cart = [];
                            this.updateCart();
                            this.fetchProductos();
                        } else {
                            // Captura errores de validación y stock (422)
                            if (response.status === 422) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Stock Insuficiente',
                                    text: data.message || 'Algunos productos tienen stock insuficiente.',
                                    confirmButtonColor: '#f59e0b'
                                });
                                this.fetchProductos(); // Recargar para mostrar el stock real
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message || 'Error desconocido al registrar la venta.',
                                    confirmButtonColor: '#ef4444'
                                });
                            }
                        }
                    } catch (error) {
                        console.error('Error durante la venta:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de Conexión',
                            text: 'Ocurrió un error al procesar la venta. Intenta nuevamente.',
                            confirmButtonColor: '#ef4444'
                        });
                    } finally {
                        this.isProcessing = false;
                    }
                }
            }))
        });
    </script>
</x-app-layout>