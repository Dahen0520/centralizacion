<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Devolución y Ajuste de Stock') }}
        </h2>
    </x-slot>

    <script>
        window.DevolucionData = {
            detallesJson: '{{ $detalles->keyBy('id')->toJson() }}',
            numeroDocumento: '{{ $numeroDocumento ?? '' }}',
            ventaId: {{ $venta->id ?? 'null' }},
            totalFinal: {{ $venta->total_final ?? '0' }}
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/ventas/devolucion.js') }}"></script>

    <div class="py-12 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 min-h-screen" 
         x-data="devolucionModule()">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-800 p-5 rounded-xl mb-6 shadow-lg animate-fade-in">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-semibold">{{ session('success') }}</span>
                    </div>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 text-red-800 p-5 rounded-xl mb-6 shadow-lg animate-fade-in">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-semibold">{{ session('error') }}</span>
                    </div>
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 text-red-800 p-5 rounded-xl mb-6 shadow-lg">
                    <p class="font-bold mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Errores de validación:
                    </p>
                    <ul class="list-disc ml-7 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl p-8 mb-8 border border-gray-200 dark:border-gray-700 hover:shadow-2xl transition-shadow duration-300">
                <div class="flex items-center mb-6">
                    <div class="bg-indigo-100 dark:bg-indigo-900 p-3 rounded-xl mr-4">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                        Buscar Documento de Venta
                    </h3>
                </div>
                <form method="GET" action="{{ route('ventas.devolucion.form') }}">
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Número de Factura o ID de Venta
                            </label>
                            <input type="text" 
                                   name="numero_documento" 
                                   required
                                   value="{{ $numeroDocumento ?? '' }}"
                                   placeholder="Ej: 001-001-00000123 o ID"
                                   class="w-full rounded-xl border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200 dark:focus:ring-indigo-900 transition-all duration-200 px-4 py-3">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" 
                                    class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl font-semibold hover:from-indigo-700 hover:to-indigo-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Buscar
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            @if (isset($venta))
                <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-2xl overflow-hidden border border-red-200 dark:border-red-900">
                    
                    <div class="bg-gradient-to-r from-red-500 to-rose-600 p-6">
                        <div class="flex items-center text-white">
                            <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl mr-4">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold">
                                    Devolución de Productos
                                </h3>
                                <p class="text-red-100 text-sm mt-1">
                                    Venta #{{ $venta->numero_documento ?? $venta->id }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <div class="mb-8 p-6 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-xl border border-gray-200 dark:border-gray-600">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="flex items-center">
                                    <div class="bg-blue-100 dark:bg-blue-900 p-2 rounded-lg mr-3">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Cliente</p>
                                        <p class="font-bold text-gray-800 dark:text-gray-100">{{ $venta->cliente->nombre ?? 'Venta Genérica' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <div class="bg-purple-100 dark:bg-purple-900 p-2 rounded-lg mr-3">
                                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Fecha de Venta</p>
                                        <p class="font-bold text-gray-800 dark:text-gray-100">{{ $venta->fecha_venta->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <div class="bg-green-100 dark:bg-green-900 p-2 rounded-lg mr-3">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Total Original</p>
                                        <p class="font-bold text-gray-800 dark:text-gray-100">L {{ number_format($venta->total_final, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form method="POST" 
                              action="{{ route('ventas.devolucion.process', $venta) }}" 
                              @submit.prevent="handleSubmit($event)">
                            @csrf
                            
                            <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 mb-8 shadow-md">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800">
                                        <tr>
                                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                Producto
                                            </th>
                                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                Cant. Vendida
                                            </th>
                                            <th class="px-6 py-4 text-center text-xs font-bold text-red-600 dark:text-red-400 uppercase tracking-wider">
                                                Cant. a Devolver
                                            </th>
                                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                Total Línea
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @forelse ($detalles as $detalle)
                                            @if ($detalle->cantidad > 0)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                                    <td class="px-6 py-4">
                                                        <div class="flex items-center">
                                                            <div class="bg-indigo-100 dark:bg-indigo-900 p-2 rounded-lg mr-3">
                                                                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                                                    {{ $detalle->inventario->marca->producto->nombre ?? 'N/A' }}
                                                                </p>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                                    Código: {{ $detalle->inventario->marca->codigo_marca ?? 'N/A' }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 text-center">
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                                            {{ $detalle->cantidad }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 text-center">
                                                        <input type="number" 
                                                               min="0" 
                                                               max="{{ $detalle->cantidad }}"
                                                               value="0"
                                                               @input="
                                                                    cantidad = parseInt($event.target.value) || 0;
                                                                    if (cantidad > 0) {
                                                                        productosDevolver[{{ $detalle->id }}] = cantidad;
                                                                    } else {
                                                                        delete productosDevolver[{{ $detalle->id }}];
                                                                    }
                                                                    updateDevolucionTotal();
                                                               "
                                                               class="w-24 text-center rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-red-500 focus:ring-4 focus:ring-red-200 dark:focus:ring-red-900 font-semibold transition-all duration-200">
                                                    </td>
                                                    <td class="px-6 py-4 text-right">
                                                        <span class="text-base font-bold text-teal-600 dark:text-teal-400">
                                                            L {{ number_format($detalle->subtotal_final, 2) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endif
                                        @empty
                                            <tr>
                                                <td colspan="4" class="py-12 text-center">
                                                    <div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                                        <svg class="w-16 h-16 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                                        </svg>
                                                        <p class="font-medium">No hay productos disponibles para devolución</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 p-6 rounded-xl border-2 border-red-300 dark:border-red-700 mb-6 shadow-lg">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="bg-red-100 dark:bg-red-900 p-3 rounded-xl mr-4">
                                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Total a Reembolsar</p>
                                            <h4 class="text-3xl font-bold text-red-600 dark:text-red-400" x-text="'L ' + totalDevolucion.toFixed(2)">
                                                L 0.00
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Productos seleccionados</p>
                                        <p class="text-2xl font-bold text-gray-700 dark:text-gray-300" x-text="Object.keys(productosDevolver).length">0</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-4">
                                <a href="{{ route('ventas.index') }}" 
                                   class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-semibold hover:bg-gray-300 dark:hover:bg-gray-600 transition-all duration-200 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                    </svg>
                                    Cancelar
                                </a>
                                <button type="submit" 
                                        class="px-8 py-3 bg-gradient-to-r from-red-600 to-rose-600 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center"
                                        :disabled="Object.keys(productosDevolver).length === 0">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                    </svg>
                                    Procesar Devolución
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @elseif (isset($numeroDocumento))
                <div class="bg-gradient-to-r from-yellow-50 to-amber-50 dark:from-yellow-900/20 dark:to-amber-900/20 border-l-4 border-yellow-500 p-6 rounded-xl shadow-lg">
                    <div class="flex items-center">
                        <div class="bg-yellow-100 dark:bg-yellow-900 p-3 rounded-xl mr-4">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-yellow-800 dark:text-yellow-300 text-lg">Documento no encontrado</p>
                            <p class="text-yellow-700 dark:text-yellow-400 text-sm mt-1">
                                No se encontró la Venta <strong>#{{ $numeroDocumento }}</strong> o no está en estado COMPLETADA para permitir devoluciones.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>