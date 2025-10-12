<div class="p-6 bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 mt-6"
     x-data="{
        carrito: [],
        total: 0,

        agregarProducto(producto) {
            let item = this.carrito.find(p => p.id === producto.id);
            if (item) {
                item.cantidad++;
            } else {
                this.carrito.push({...producto, cantidad: 1});
            }
            this.calcularTotal();
        },

        eliminarProducto(id) {
            this.carrito = this.carrito.filter(p => p.id !== id);
            this.calcularTotal();
        },

        cambiarCantidad(id, nuevaCantidad) {
            let item = this.carrito.find(p => p.id === id);
            if (item) {
                item.cantidad = parseInt(nuevaCantidad);
            }
            this.calcularTotal();
        },

        calcularTotal() {
            this.total = this.carrito.reduce((sum, p) => sum + (p.precio * p.cantidad), 0);
        },

        limpiarCarrito() {
            this.carrito = [];
            this.total = 0;
        }
     }"
     @producto-agregado.window="agregarProducto($event.detail)">
    
    <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Carrito de Compras</h2>

    <template x-if="carrito.length === 0">
        <p class="text-gray-500 dark:text-gray-400 text-sm">Tu carrito está vacío 🛒</p>
    </template>

    <template x-for="producto in carrito" :key="producto.id">
        <div class="flex items-center justify-between border-b py-2 dark:border-gray-700">
            <div>
                <p class="font-medium text-gray-800 dark:text-gray-200" x-text="producto.nombre"></p>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    L <span x-text="producto.precio.toFixed(2)"></span>
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <input type="number"
                       min="1"
                       class="w-16 border rounded-lg text-center dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                       x-model.number="producto.cantidad"
                       @change="cambiarCantidad(producto.id, producto.cantidad)">
                <button @click="eliminarProducto(producto.id)" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </template>

    <div x-show="carrito.length > 0" class="mt-4 pt-4 border-t dark:border-gray-700">
        <div class="flex justify-between text-lg font-semibold text-gray-800 dark:text-gray-200">
            <span>Total:</span>
            <span>L <span x-text="total.toFixed(2)"></span></span>
        </div>

        <div class="flex justify-end space-x-2 mt-4">
            <button @click="limpiarCarrito"
                    class="px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white">
                Limpiar
            </button>
            <button class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">
                Procesar Venta
            </button>
        </div>
    </div>
</div>
