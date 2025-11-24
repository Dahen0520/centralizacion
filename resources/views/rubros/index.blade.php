<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Gestión de Rubros') }}
        </h2>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-12">
        <div class="w-full max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 
                        rounded-2xl shadow-3xl p-8 lg:p-10 
                        border-t-4 border-blue-600 dark:border-blue-500">
                
                <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                    
                    <a href="{{ route('rubros.create') }}" 
                       class="w-full md:w-auto inline-flex items-center justify-center px-8 py-3 
                              bg-gradient-to-r from-blue-600 to-indigo-700 text-white 
                              font-bold text-sm uppercase tracking-wider rounded-xl shadow-lg 
                              hover:shadow-xl hover:from-blue-700 hover:to-indigo-800 
                              focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 
                              transition duration-300 transform hover:scale-[1.02]">
                        <i class="fas fa-plus-circle mr-2 text-lg"></i> Crear Nuevo Rubro
                    </a>

                    <div class="relative w-full md:w-1/2">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400"></i>
                        <input type="text" id="search-input" placeholder="Buscar rubros por ID o Nombre..."
                               class="pl-12 py-3.5 shadow-md border border-gray-300 dark:border-gray-600 rounded-xl w-full 
                                      text-gray-900 dark:text-gray-100 dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-xl">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-1/4">
                                    <i class="fas fa-hashtag mr-1"></i> ID
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-1/2">
                                    <i class="fas fa-industry mr-1"></i> Nombre
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-1/4">
                                    <i class="fas fa-cogs mr-1"></i> Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody id="rubros-table-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                            @include('rubros.partials.rubros_table_rows')
                        </tbody>
                    </table>
                    
                    <div id="no-results-message" class="hidden p-10 text-center bg-white dark:bg-gray-800">
                        <i class="fas fa-box-open text-5xl text-blue-400 dark:text-blue-600 mb-3"></i>
                        <p class="font-extrabold text-xl text-gray-900 dark:text-white">¡Vaya! No hay coincidencias.</p>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">Intenta cambiar los términos de búsqueda o revisa la ortografía.</p>
                    </div>
                </div>

                <div id="pagination-links" class="mt-8">
                    {{ $rubros->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        window.AppConfig = {
            rubrosIndexRoute: '{{ route('rubros.index') }}',
            csrfToken: document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '',
            sessionSuccess: '{{ session('success') }}',
            columnCount: 3
        };
    </script>
    
    <script src="{{ asset('js/rubros/index.js') }}"></script>
</x-app-layout>