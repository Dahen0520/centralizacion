<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Administración de Clientes') }}
        </h2>
    </x-slot>

    {{-- Script de SweetAlert2 (CDN) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-12">
        <div class="w-full max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 
                        rounded-2xl shadow-3xl p-8 lg:p-10 
                        border-t-4 border-indigo-600 dark:border-indigo-500">

                {{-- HEADER DE ACCIONES Y BÚSQUEDA ESTILIZADA --}}
                <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                    
                    {{-- Botón Crear --}}
                    <a href="{{ route('clientes.create') }}" 
                       class="w-full md:w-auto inline-flex items-center justify-center px-8 py-3 
                              bg-gradient-to-r from-indigo-600 to-purple-700 text-white 
                              font-bold text-sm uppercase tracking-wider rounded-xl shadow-lg 
                              hover:shadow-xl hover:from-indigo-700 hover:to-purple-800 
                              focus:outline-none focus:ring-4 focus:ring-indigo-300 dark:focus:ring-purple-800 
                              transition duration-300 transform hover:scale-[1.02]">
                        <i class="fas fa-plus-circle mr-2 text-lg"></i> Crear Cliente
                    </a>

                    {{-- Barra de búsqueda --}}
                    <div class="relative w-full md:w-1/2">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400"></i>
                        <input type="text" id="search-input" value="{{ request('search') }}" placeholder="Buscar por nombre, RTN o ID..."
                               class="pl-12 py-3.5 shadow-md border border-gray-300 dark:border-gray-600 rounded-xl w-full 
                                      text-gray-900 dark:text-gray-100 dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150">
                    </div>
                </div>
                
                {{-- Mensajes de sesión se mantuvieron en el Blade para que se muestren sin JavaScript, pero se limpia el JS del cuerpo. --}}
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 dark:bg-green-900/30 dark:border-green-800 dark:text-green-300">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 dark:bg-red-900/30 dark:border-red-800 dark:text-red-300">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- CONTENEDOR DE LA TABLA --}}
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-xl">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-16">
                                    #
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-user mr-1"></i> Nombre del Cliente
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-id-card mr-1"></i> Identificación / RTN
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-envelope mr-1"></i> Email
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-phone mr-1"></i> Teléfono
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-1/4">
                                    <i class="fas fa-cogs mr-1"></i> Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody id="clientes-table-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                            {{-- Incluye las filas de clientes usando el parcial --}}
                            @include('clientes.partials.clientes_table_rows', ['clientes' => $clientes, 'start_index' => $clientes->firstItem()])
                        </tbody>
                    </table>
                    
                    {{-- Mensaje de No Resultados (Usado por AJAX) --}}
                    <div id="no-results-message" class="hidden p-10 text-center bg-white dark:bg-gray-800">
                        <i class="fas fa-user-slash text-5xl text-indigo-400 dark:text-indigo-600 mb-3"></i>
                        <p class="font-extrabold text-xl text-gray-900 dark:text-white">¡Vaya! No se encontraron clientes.</p>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">Intenta ajustar tu búsqueda o crea uno nuevo.</p>
                    </div>
                </div>

                {{-- Paginación --}}
                <div id="pagination-links" class="mt-8">
                    {{ $clientes->links() }}
                </div>
            </div>
        </div>
    </div>

{{-- Bloque para pasar datos de Blade a JavaScript --}}
<script>
    window.AppConfig = {
        clientesIndexRoute: '{{ route('clientes.index') }}',
        csrfToken: document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : ''
    };
</script>

{{-- Enlace al archivo JavaScript separado --}}
<script src="{{ asset('js/clientes/index.js') }}"></script>
</x-app-layout>