{{-- resources/views/movimientos/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Historial de Movimientos de Inventario') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Mensajes de Notificación --}}
            @if (session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 text-green-800 px-4 py-3 rounded-lg relative mb-6 shadow-sm" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-3 text-green-500 text-lg"></i>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border-l-4 border-red-400 text-red-800 px-4 py-3 rounded-lg relative mb-6 shadow-sm" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-3 text-red-500 text-lg"></i>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif
            
            {{-- Filtros --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl mb-6 border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-filter text-blue-600 text-lg mr-2"></i>
                        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Filtros de Búsqueda</h3>
                    </div>
                    <form method="GET" action="{{ route('movimientos.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="tipo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-exchange-alt text-blue-500 mr-1"></i> Tipo de Movimiento
                            </label>
                            <select name="tipo" id="tipo" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition">
                                <option value="">Todos los tipos</option>
                                <option value="ENTRADA" {{ request('tipo') === 'ENTRADA' ? 'selected' : '' }}>✓ Entradas</option>
                                <option value="SALIDA" {{ request('tipo') === 'SALIDA' ? 'selected' : '' }}>✗ Salidas</option>
                            </select>
                        </div>

                        <div>
                            <label for="fecha_desde" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-calendar-alt text-blue-500 mr-1"></i> Fecha Inicial
                            </label>
                            <input type="date" name="fecha_desde" id="fecha_desde" value="{{ request('fecha_desde') }}" 
                                   class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition">
                        </div>

                        <div>
                            <label for="fecha_hasta" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-calendar-check text-blue-500 mr-1"></i> Fecha Final
                            </label>
                            <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ request('fecha_hasta') }}" 
                                   class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition">
                        </div>

                        <div class="flex items-end gap-2">
                            <button type="submit" class="flex-1 inline-flex justify-center items-center px-4 py-2.5 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                                <i class="fas fa-search mr-2"></i> Buscar
                            </button>
                            @if(request()->hasAny(['tipo', 'fecha_desde', 'fecha_hasta']))
                                <a href="{{ route('movimientos.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-200 dark:bg-gray-600 border border-transparent rounded-lg font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-500 transition ease-in-out duration-150 shadow-sm" title="Limpiar filtros">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tarjetas de Resumen --}}
            @if(isset($resumen))
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 overflow-hidden shadow-md rounded-xl border border-green-200 dark:border-green-700">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-green-700 dark:text-green-300 uppercase tracking-wide">Total Entradas</p>
                                    <p class="text-3xl font-bold text-green-800 dark:text-green-100 mt-2">{{ $resumen['entradas'] ?? 0 }}</p>
                                </div>
                                <div class="bg-green-200 dark:bg-green-700 rounded-full p-4">
                                    <i class="fas fa-arrow-circle-up text-green-700 dark:text-green-200 text-3xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900 dark:to-red-800 overflow-hidden shadow-md rounded-xl border border-red-200 dark:border-red-700">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-red-700 dark:text-red-300 uppercase tracking-wide">Total Salidas</p>
                                    <p class="text-3xl font-bold text-red-800 dark:text-red-100 mt-2">{{ $resumen['salidas'] ?? 0 }}</p>
                                </div>
                                <div class="bg-red-200 dark:bg-red-700 rounded-full p-4">
                                    <i class="fas fa-arrow-circle-down text-red-700 dark:text-red-200 text-3xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 overflow-hidden shadow-md rounded-xl border border-blue-200 dark:border-blue-700">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-blue-700 dark:text-blue-300 uppercase tracking-wide">Total Movimientos</p>
                                    <p class="text-3xl font-bold text-blue-800 dark:text-blue-100 mt-2">{{ $resumen['total'] ?? 0 }}</p>
                                </div>
                                <div class="bg-blue-200 dark:bg-blue-700 rounded-full p-4">
                                    <i class="fas fa-list-alt text-blue-700 dark:text-blue-200 text-3xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Tabla de Movimientos --}}
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">
                            <i class="fas fa-clipboard-list text-blue-600 mr-2"></i>Registro de Movimientos
                        </h3>
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-sm font-semibold rounded-full">
                                {{ $movimientos->total() }} registros
                            </span>
                            <a href="{{ route('movimientos.create') }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                                <i class="fas fa-plus mr-2"></i> Nuevo Movimiento
                            </a>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-600">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        <i class="fas fa-calendar mr-1"></i> Fecha
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        <i class="fas fa-box mr-1"></i> Producto
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        <i class="fas fa-store mr-1"></i> Tienda
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        <i class="fas fa-exchange-alt mr-1"></i> Tipo
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        <i class="fas fa-comment mr-1"></i> Razón
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        <i class="fas fa-sort-numeric-up mr-1"></i> Cantidad
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        <i class="fas fa-user mr-1"></i> Usuario
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($movimientos as $movimiento)
                                    <tr class="hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                            <div class="flex flex-col">
                                                <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $movimiento->created_at->format('d/m/Y') }}</span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $movimiento->created_at->format('H:i:s') }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-200">
                                            <div>
                                                <div class="font-semibold text-gray-800 dark:text-gray-100">{{ $movimiento->inventario->marca->producto->nombre ?? 'N/A' }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    <i class="fas fa-tag mr-1"></i>{{ $movimiento->inventario->marca->nombre ?? 'Sin marca' }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100">
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                {{ $movimiento->inventario->tienda->nombre ?? 'Sin Tienda' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="px-3 py-1.5 inline-flex items-center text-xs leading-5 font-bold rounded-lg shadow-sm
                                                {{ $movimiento->tipo_movimiento === 'ENTRADA' 
                                                    ? 'bg-green-100 text-green-800 border border-green-300 dark:bg-green-800 dark:text-green-100 dark:border-green-600' 
                                                    : 'bg-red-100 text-red-800 border border-red-300 dark:bg-red-800 dark:text-red-100 dark:border-red-600' }}">
                                                <i class="fas fa-{{ $movimiento->tipo_movimiento === 'ENTRADA' ? 'arrow-up' : 'arrow-down' }} mr-1.5"></i>
                                                {{ $movimiento->tipo_movimiento }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                            <span class="line-clamp-2">{{ $movimiento->razon }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg text-base font-bold shadow-sm
                                                {{ $movimiento->tipo_movimiento === 'ENTRADA' 
                                                    ? 'bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-200' 
                                                    : 'bg-red-100 text-red-700 dark:bg-red-800 dark:text-red-200' }}">
                                                {{ $movimiento->tipo_movimiento === 'ENTRADA' ? '+' : '-' }}{{ $movimiento->cantidad }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8 bg-blue-100 dark:bg-blue-800 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-user text-blue-600 dark:text-blue-200 text-xs"></i>
                                                </div>
                                                <span class="ml-2 font-medium">{{ $movimiento->usuario->name ?? 'Sistema' }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-16 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="bg-blue-100 dark:bg-blue-900 rounded-full p-6 mb-4">
                                                    <i class="fas fa-inbox text-5xl text-blue-400 dark:text-blue-300"></i>
                                                </div>
                                                <p class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">No hay movimientos registrados</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Los movimientos de inventario aparecerán aquí</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginación --}}
                    @if($movimientos->hasPages())
                        <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                            {{ $movimientos->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>