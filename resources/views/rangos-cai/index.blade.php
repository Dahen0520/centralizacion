<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-center gap-3">
            <div class="p-2 bg-indigo-100 dark:bg-indigo-900 rounded-lg">
                <i class="fas fa-file-invoice-dollar text-2xl text-indigo-600 dark:text-indigo-400"></i>
            </div>
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Administración de Rangos CAI (SAR)') }}
            </h2>
        </div>
    </x-slot>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-200 dark:border-gray-700">
                
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-gray-900 dark:to-indigo-950 p-6 border-b-2 border-indigo-200 dark:border-indigo-800">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                        <a href="{{ route('rangos-cai.create') }}" 
                           class="w-full md:w-auto group relative px-8 py-3.5 bg-gradient-to-r from-green-500 to-emerald-600 
                                  hover:from-green-600 hover:to-emerald-700 text-white rounded-xl font-semibold 
                                  transition-all duration-300 shadow-lg hover:shadow-xl 
                                  flex items-center justify-center gap-2 transform hover:scale-105 hover:-translate-y-0.5">
                            <i class="fas fa-plus-circle text-lg group-hover:rotate-90 transition-transform duration-300"></i>
                            <span>Nuevo Rango CAI</span>
                            <div class="absolute inset-0 rounded-xl bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                        </a>
                    </div>
                </div>

                <div class="px-6 pt-6">
                    @if (session('success'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                             x-transition:enter="transform ease-out duration-300"
                             x-transition:enter-start="translate-y-2 opacity-0"
                             x-transition:enter-end="translate-y-0 opacity-100"
                             class="mb-4 p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20
                                    border-l-4 border-green-500 text-green-700 dark:text-green-400 rounded-r-xl shadow-md flex items-center gap-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white"></i>
                            </div>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                    @endif
                    
                    @if (session('error'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                             x-transition:enter="transform ease-out duration-300"
                             x-transition:enter-start="translate-y-2 opacity-0"
                             x-transition:enter-end="translate-y-0 opacity-100"
                             class="mb-4 p-4 bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20
                                    border-l-4 border-red-500 text-red-700 dark:text-red-400 rounded-r-xl shadow-md flex items-center gap-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-red-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-exclamation text-white"></i>
                            </div>
                            <span class="font-medium">{{ session('error') }}</span>
                        </div>
                    @endif
                </div>

                <div class="p-6">
                    <div class="overflow-hidden rounded-xl border-2 border-gray-200 dark:border-gray-700 shadow-lg">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-store text-indigo-500"></i>
                                                <span>Tienda</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-file-invoice text-indigo-500"></i>
                                                <span>CAI / Rango Autorizado</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                            <div class="flex items-center justify-center gap-2">
                                                <i class="fas fa-sort-numeric-up-alt text-indigo-500"></i>
                                                <span>Núm. Actual</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                            <div class="flex items-center justify-center gap-2">
                                                <i class="fas fa-calendar-alt text-indigo-500"></i>
                                                <span>Fecha Límite</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                            <div class="flex items-center justify-center gap-2">
                                                <i class="fas fa-check-circle text-indigo-500"></i>
                                                <span>Estado</span>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="rangos-cai-table-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @include('rangos-cai.partials.rangos_table_rows')
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div id="pagination-links" class="mt-6">
                        {{ $rangos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        window.AppConfig = {
            rangosIndexRoute: '{{ route('rangos-cai.index') }}',
            csrfToken: document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : ''
        };
    </script>
    <script src="{{ asset('js/rangos-cai/index.js') }}"></script>
</x-app-layout>