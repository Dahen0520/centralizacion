<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Registrar Nuevo Rango CAI') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg dark:bg-red-900 dark:border-red-600 dark:text-red-300">
                        <p class="font-bold">Por favor, corrija los siguientes errores:</p>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('rangos-cai.store') }}">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                        {{-- Campo Tienda --}}
                        <div class="md:col-span-2">
                            <label for="tienda_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tienda Asociada</label>
                            <select id="tienda_id" name="tienda_id" required 
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Seleccione una tienda</option>
                                @foreach($tiendas as $tienda)
                                    <option value="{{ $tienda->id }}" @selected(old('tienda_id') == $tienda->id)>
                                        {{ $tienda->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tienda_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Campo CAI (Código de Autorización de Impresión) --}}
                        <div class="md:col-span-2">
                            <label for="cai" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CAI (Código de Autorización de Impresión) <i class="fas fa-barcode text-gray-400 ml-1"></i></label>
                            <input type="text" id="cai" name="cai" value="{{ old('cai') }}" required maxlength="100"
                                   placeholder="Ej: XXXXXXXXXX-XXXX-XXXX-XXXX-XXXXXX"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                            @error('cai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Rango Inicial --}}
                        <div>
                            <label for="rango_inicial" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rango Inicial (SAR)</label>
                            <input type="text" id="rango_inicial" name="rango_inicial" value="{{ old('rango_inicial') }}" required maxlength="50"
                                   placeholder="Ej: 000-001-01-0000001"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                            @error('rango_inicial') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Rango Final --}}
                        <div>
                            <label for="rango_final" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rango Final (SAR)</label>
                            <input type="text" id="rango_final" name="rango_final" value="{{ old('rango_final') }}" required maxlength="50"
                                   placeholder="Ej: 000-001-01-0000500"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                            @error('rango_final') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        {{-- Fecha Límite de Emisión --}}
                        <div>
                            <label for="fecha_limite_emision" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha Límite de Emisión</label>
                            <input type="date" id="fecha_limite_emision" name="fecha_limite_emision" value="{{ old('fecha_limite_emision') }}" required
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                            @error('fecha_limite_emision') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Estado (Activo por defecto en Store, pero útil si se edita) --}}
                        <div>
                             <label for="initial_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Estado Inicial</label>
                             <div class="mt-2 flex items-center">
                                <span class="text-sm font-semibold text-green-600 dark:text-green-400">
                                    <i class="fas fa-lightbulb mr-2"></i> Activo (Se activará automáticamente al guardar, desactivando los anteriores de esta tienda).
                                </span>
                             </div>
                        </div>

                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6 border-t pt-4">
                        <a href="{{ route('rangos-cai.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg font-medium transition duration-150">
                            Cancelar
                        </a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition duration-150 shadow-md">
                            <i class="fas fa-save mr-2"></i> Registrar Rango CAI
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>