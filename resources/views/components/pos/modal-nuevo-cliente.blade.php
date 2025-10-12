<div 
    x-data="{ open: false, nombre: '', correo: '', telefono: '', direccion: '' }" 
    @abrir-modal-nuevo-cliente.window="open = true">

    <!-- Botón para abrir modal -->
    <button 
        @click="open = true"
        class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow">
        <i class="fas fa-user-plus mr-1"></i> Nuevo Cliente
    </button>

    <!-- Modal -->
    <div 
        x-show="open" 
        x-transition 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        style="display: none">

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md p-6 relative">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-100">
                Registrar Nuevo Cliente
            </h2>

            <!-- Formulario -->
            <form @submit.prevent="
                if(nombre.trim() === '') { alert('El nombre es obligatorio'); return; }
                $dispatch('nuevo-cliente-registrado', { nombre, correo, telefono, direccion });
                open = false;
                nombre = correo = telefono = direccion = '';
            ">

                <div class="space-y-3">
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm mb-1">Nombre</label>
                        <input type="text" x-model="nombre" class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-white dark:border-gray-600">
                    </div>

                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm mb-1">Correo</label>
                        <input type="email" x-model="correo" class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-white dark:border-gray-600">
                    </div>

                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm mb-1">Teléfono</label>
                        <input type="text" x-model="telefono" class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-white dark:border-gray-600">
                    </div>

                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm mb-1">Dirección</label>
                        <textarea x-model="direccion" class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-white dark:border-gray-600"></textarea>
                    </div>
                </div>

                <div class="flex justify-end space-x-2 mt-5">
                    <button type="button" 
                        @click="open = false" 
                        class="px-3 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white">
                        Cancelar
                    </button>
                    <button type="submit" 
                        class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">
                        Guardar
                    </button>
                </div>
            </form>

            <!-- Botón cerrar (X) -->
            <button 
                @click="open = false"
                class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</div>
