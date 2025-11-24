<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Gestión de Empresas Registradas') }}
        </h2>
    </x-slot>

    {{-- Script de SweetAlert2 (CDN) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-12">
        <div class="w-full max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 
                        rounded-2xl shadow-3xl p-8 lg:p-10 
                        border-t-4 border-blue-600 dark:border-blue-500">
                
                @php
                    $currentStatus = $currentStatus ?? request('estado', 'aprobado');
                    // Asegúrate de que $statusCounts se inicialice si no viene del controlador para evitar errores
                    $statusCounts = $statusCounts ?? ['todos' => 0, 'aprobado' => 0, 'pendiente' => 0, 'rechazado' => 0];

                    $statuses = [
                        'todos' => ['label' => 'Todas', 'icon' => 'fas fa-list', 'color' => 'gray'],
                        'aprobado' => ['label' => 'Aprobadas', 'icon' => 'fas fa-check-circle', 'color' => 'green'],
                        'pendiente' => ['label' => 'Pendientes', 'icon' => 'fas fa-clock', 'color' => 'yellow'],
                        'rechazado' => ['label' => 'Rechazadas', 'icon' => 'fas fa-times-circle', 'color' => 'red'],
                    ];
                @endphp

                {{-- FILTROS COMPACTOS Y BÚSQUEDA --}}
                <div class="flex flex-col lg:flex-row justify-between items-center mb-8 gap-4">
                    
                    {{-- GRUPO DE FILTROS DE ESTADO --}}
                    <div class="flex flex-wrap justify-center lg:justify-start items-center gap-1 
                                p-1 bg-gray-100 dark:bg-gray-700 rounded-lg shadow-inner border border-gray-200 dark:border-gray-600">
                        <span class="text-xs font-bold text-gray-600 dark:text-gray-300 py-1 px-2 mr-1 hidden md:block">Filtrar por:</span>
                        @foreach ($statuses as $statusKey => $statusData)
                            @php
                                $isActive = $currentStatus === $statusKey;
                                $activeClasses = $isActive 
                                    ? "bg-blue-600 text-white shadow-md" 
                                    : "bg-transparent text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600";
                                $count = $statusCounts[$statusKey] ?? 0;
                            @endphp
                            <button type="button" 
                                    data-status="{{ $statusKey }}" 
                                    class="status-filter inline-flex items-center px-3 py-1.5 rounded-md 
                                           text-xs font-semibold uppercase transition duration-150 ease-in-out 
                                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 
                                           {{ $activeClasses }}">
                                <i class="{{ $statusData['icon'] }} mr-1"></i>
                                <span>{{ $statusData['label'] }}</span>
                                <span class="ml-2 px-1.5 py-0.5 text-xs font-bold rounded-full {{ $isActive ? 'bg-white/30' : 'bg-blue-100 dark:bg-gray-900 text-blue-600 dark:text-blue-400' }}" id="count-{{ $statusKey }}">
                                    {{ $count }}
                                </span>
                            </button>
                        @endforeach
                    </div>

                    {{-- Barra de búsqueda --}}
                    <div class="relative w-full lg:w-1/3">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400"></i>
                        <input type="text" id="search-input" placeholder="Buscar por Nombre, Afiliado o Rubro..."
                               value="{{ request('search') }}"
                               class="pl-12 py-3.5 shadow-md border border-gray-300 dark:border-gray-600 rounded-xl w-full 
                                      text-gray-900 dark:text-gray-100 dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                    </div>
                </div>

                {{-- TABLA --}}
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-xl">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-16">
                                    #
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-building mr-1"></i> Negocio
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-user-tie mr-1"></i> Afiliado
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-industry mr-1"></i> Rubro
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-24">
                                    <i class="fas fa-file-invoice mr-1"></i> Facturación
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-24">
                                    <i class="fas fa-check-circle mr-1"></i> Estado
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-32">
                                    <i class="fas fa-cogs mr-1"></i> Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody id="empresas-table-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                            @include('empresas.partials.empresas_table_rows')
                        </tbody>
                    </table>
                    
                    <div id="no-results-message" class="hidden p-10 text-center bg-white dark:bg-gray-800">
                        <i class="fas fa-box-open text-5xl text-blue-400 dark:text-blue-600 mb-3"></i>
                        <p class="font-extrabold text-xl text-gray-900 dark:text-white">¡Vaya! No se encontraron empresas.</p>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">Intenta ajustar tu búsqueda.</p>
                    </div>
                </div>

                <div id="pagination-links" class="mt-8">
                    {{ $empresas->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Bloque para pasar datos de Blade a JavaScript --}}
    <script>
        window.AppConfig = {
            empresasIndexRoute: '{{ route('empresas.index') }}',
            csrfToken: document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '',
            // Pasamos el conteo inicial de estados
            statusCounts: @json($statusCounts),
            currentStatus: '{{ $currentStatus }}',
            columnCount: 7 
        };
    </script>
    
    {{-- Enlace al archivo JavaScript separado --}}
    <script src="{{ asset('js/empresas/index.js') }}"></script>
</x-app-layout>