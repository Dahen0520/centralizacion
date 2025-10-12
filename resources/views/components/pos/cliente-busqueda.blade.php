<div class="p-6 bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 mt-6" x-data="{
    searchQuery: '',
    clientes: [],
    showResults: false,

    async buscarClientes() {
        if (this.searchQuery.trim() === '') {
            this.clientes = [];
            this.showResults = false;
            return;
        }
        try {
            const response = await fetch(`/ventas/buscar-clientes?query=${encodeURIComponent(this.searchQuery)}`);
            this.clientes = await response.json();
            this.showResults = true;
        } catch (error) {
            console.error('Error al buscar clientes:', error);
        }
    },
    limpiarBusqueda() {
        this.searchQuery = '';
        this.clientes = [];
        this.showResults = false;
    },
    formatIdentificacion(event) {
        let value = event.target.value.replace(/\D/g, '');
        if (value.length > 4 && value.length <= 8) {
            value = value.replace(/^(\d{4})(\d+)/, '$1-$2');
        } else if (value.length > 8) {
            value = value.replace(/^(\d{4})(\d{4})(\d{0,5}).*/, '$1-$2-$3');
        }
        this.searchQuery = value;
    }
}">
    <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Cliente y Búsqueda</h2>

    <!-- Campo de búsqueda -->
    <div class="relative">
        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
        <input type="text"
               x-model="searchQuery"
               @input.debounce.500ms="buscarClientes"
               @input="formatIdentificacion"
               placeholder="Buscar cliente por nombre o RTN..."
               class="w-full pl-10 pr-10 py-2 border rounded-lg focus:ring-indigo-500 focus:border-indigo-500
                      dark:bg-gray-700 dark:border-gray-600 dark:text-white"
        >
        <!-- Botón limpiar -->
        <button @click="limpiarBusqueda"
                type="button"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Resultados de búsqueda -->
    <div x-show="showResults && clientes.length > 0" x-cloak class="mt-4 border rounded-lg dark:border-gray-600">
        <template x-for="cliente in clientes" :key="cliente.id">
            <div class="p-2 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer"
                 @click="$dispatch('cliente-seleccionado', cliente)">
                <p class="text-gray-800 dark:text-gray-200 font-medium" x-text="cliente.nombre"></p>
                <p class="text-sm text-gray-500 dark:text-gray-400" x-text="cliente.identificacion"></p>
            </div>
        </template>
    </div>

    <div x-show="showResults && clientes.length === 0" x-cloak class="mt-4 text-gray-500 dark:text-gray-400 text-sm">
        No se encontraron clientes.
    </div>
</div>
