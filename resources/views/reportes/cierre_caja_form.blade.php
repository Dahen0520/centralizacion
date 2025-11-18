{{-- resources/views/reportes/cierre_caja_form.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Generar Reporte de Cierre de Caja') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg p-6">
                
                @if (session('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">{{ session('error') }}</div>
                @endif
                
                <form method="GET" action="{{ route('reportes.cierre_caja.generar') }}" class="space-y-6">
                    
                    {{-- Seleccionar Tienda --}}
                    <div>
                        <label for="tienda_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Tienda/Sucursal
                        </label>
                        <select id="tienda_id" name="tienda_id" required
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Seleccione una tienda</option>
                            @foreach ($tiendas as $tienda)
                                <option value="{{ $tienda->id }}" {{ old('tienda_id') == $tienda->id ? 'selected' : '' }}>
                                    {{ $tienda->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('tienda_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Seleccionar Fecha --}}
                    <div>
                        <label for="fecha" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Fecha del Cierre
                        </label>
                        <input type="date" id="fecha" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('fecha') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Botón Generar --}}
                    <button type="submit"
                        class="w-full justify-center flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-file-invoice-dollar mr-2"></i> Generar Reporte
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>