<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Gestión de Vinculaciones') }}
        </h2>
    </x-slot>

    {{-- Script de SweetAlert2 (CDN) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Enlace al CSS modular --}}
    <link rel="stylesheet" href="{{ asset('css/empresa-tienda/index.css') }}">

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-xl overflow-hidden">
                
                <div class="bg-gray-50 dark:bg-gray-700 px-8 py-6 border-b border-gray-200 dark:border-gray-600">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex items-center">
                            <div class="bg-blue-600 p-3 rounded-lg mr-4 shadow-lg">
                                <i class="fas fa-handshake text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Vinculaciones Registradas</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Total: <span class="font-medium">{{ $asociaciones->total() }}</span> registros</p>
                            </div>
                        </div>
                        <div class="mt-4 lg:mt-0">
                            <a href="{{ route('asociaciones.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium text-sm rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                                <i class="fas fa-plus mr-2"></i>
                                Nueva Vinculación
                            </a>
                        </div>
                    </div>
                </div>

                <div class="px-8 py-6 bg-white dark:bg-gray-800">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end mb-6">
                        <div class="md:col-span-5">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-search text-gray-400 mr-1"></i>Buscar
                            </label>
                            <div class="relative">
                                <input type="text" id="search-input" placeholder="Buscar empresa o tienda..."
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-store text-gray-400 mr-1"></i>Tienda
                            </label>
                            <select id="filter-tienda" class="w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                <option value="">Todas</option>
                                @foreach($tiendas as $tienda)
                                    <option value="{{ $tienda->id }}">{{ $tienda->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-flag text-purple-500 mr-1"></i>Estado
                            </label>
                            <select id="filter-estado" class="w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                <option value="">Todos</option>
                                <option value="pendiente"> Pendiente</option>
                                <option value="aprobado"> Aprobado</option>
                                <option value="rechazado"> Rechazado</option>
                            </select>
                        </div>
                        <div class="md:col-span-1">
                            <button id="clear-filters" class="w-full px-4 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 rounded-lg transition-colors duration-200" title="Limpiar filtros">
                                <i class="fas fa-eraser"></i>
                            </button>
                        </div>
                    </div>

                    @if (session('success'))
                        <div id="success-alert" class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/50 dark:to-emerald-900/50 border-l-4 border-green-400 p-4 mb-6 rounded-lg shadow-sm" role="alert">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-green-400 rounded-full p-1">
                                        <i class="fas fa-check text-white text-xs"></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="font-semibold text-green-800 dark:text-green-200">¡Operación exitosa!</p>
                                    <p class="text-green-700 dark:text-green-300 text-sm">{{ session('success') }}</p>
                                </div>
                                <button class="ml-auto text-green-400 hover:text-green-600 transition-colors" onclick="this.closest('#success-alert').remove()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                            <div class="flex items-center">
                                                <i class="fas fa-building text-blue-500 mr-2"></i>
                                                Empresa
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                            <div class="flex items-center">
                                                <i class="fas fa-store text-green-500 mr-2"></i>
                                                Tienda
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                            <div class="flex items-center">
                                                <i class="fas fa-flag text-purple-500 mr-2"></i>
                                                Estado
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                            <div class="flex items-center justify-end">
                                                <i class="fas fa-cog text-gray-500 mr-2"></i>
                                                Acciones
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="associations-table-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                                    @include('empresa-tienda.partials.association_table_rows')
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div id="pagination-links" class="mt-8 flex justify-center">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-1">
                            {{ $asociaciones->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

{{-- Bloque para pasar datos de Blade a JavaScript --}}
<script>
    window.AppConfig = {
        asociacionesIndexRoute: '{{ route('asociaciones.index') }}',
        csrfToken: document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : ''
    };
</script>

{{-- Enlace al archivo JavaScript separado --}}
<script src="{{ asset('js/empresa-tienda/index.js') }}"></script>
</x-app-layout>