<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-center gap-3">
            <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                <i class="fas fa-plus-circle text-2xl text-green-600 dark:text-green-400"></i>
            </div>
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Registrar Nuevo Rango CAI') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-200 dark:border-gray-700">
                
                {{-- ERRORES DE VALIDACIÓN MEJORADOS --}}
                @if ($errors->any())
                    <div x-data="{ show: true }" x-show="show"
                         x-transition:enter="transform ease-out duration-300"
                         x-transition:enter-start="translate-y-2 opacity-0"
                         x-transition:enter-end="translate-y-0 opacity-100"
                         class="m-6 p-5 bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 
                                border-l-4 border-red-500 rounded-r-xl shadow-lg">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-red-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-red-700 dark:text-red-400 text-lg mb-2">
                                    Por favor, corrija los siguientes errores:
                                </p>
                                <ul class="space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li class="flex items-start gap-2 text-red-600 dark:text-red-400">
                                            <i class="fas fa-circle text-xs mt-1.5"></i>
                                            <span>{{ str_replace('_full', '', $error) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <button @click="show = false" class="text-red-400 hover:text-red-600 transition-colors">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endif

                {{-- FORMULARIO --}}
                <form method="POST" action="{{ route('rangos-cai.store') }}" class="p-8">
                    @csrf
                    
                    {{-- SECCIÓN: INFORMACIÓN BÁSICA --}}
                    <div class="mb-8">
                        <div class="flex items-center gap-3 mb-6 pb-3 border-b-2 border-indigo-500">
                            <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center">
                                <i class="fas fa-info-circle text-indigo-600 dark:text-indigo-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200">
                                Información Básica
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 gap-6">
                            {{-- Campo Tienda --}}
                            <div>
                                <label for="tienda_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                                    <i class="fas fa-store text-indigo-500"></i>
                                    Tienda Asociada
                                </label>
                                <div class="relative">
                                    <select id="tienda_id" name="tienda_id" required 
                                            class="appearance-none w-full px-4 py-3 pr-10 border-2 border-gray-300 dark:border-gray-600 
                                                   rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 
                                                   focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 
                                                   transition-all duration-200 hover:border-indigo-400">
                                        <option value="">Seleccione una tienda</option>
                                        @foreach($tiendas as $tienda)
                                            <option value="{{ $tienda->id }}" @selected(old('tienda_id') == $tienda->id)>
                                                {{ $tienda->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-indigo-500">
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>
                                @error('tienda_id') 
                                    <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                    </p> 
                                @enderror
                            </div>

                            {{-- Campo CAI --}}
                            <div>
                                <label for="cai" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                                    <i class="fas fa-barcode text-indigo-500"></i>
                                    CAI (Código de Autorización de Impresión)
                                </label>
                                <input type="text" id="cai" name="cai" value="{{ old('cai') }}" required maxlength="100"
                                       placeholder="Ej: XXXXXXXXXX-XXXX-XXXX-XXXX-XXXXXX"
                                       class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl 
                                              bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 
                                              focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 
                                              transition-all duration-200 hover:border-indigo-400
                                              placeholder:text-gray-400 dark:placeholder:text-gray-500">
                                @error('cai') 
                                    <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                    </p> 
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- SECCIÓN: RANGOS DE FACTURACIÓN --}}
                    <div class="mb-8">
                        <div class="flex items-center gap-3 mb-6 pb-3 border-b-2 border-purple-500">
                            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-invoice text-purple-600 dark:text-purple-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200">
                                Rangos de Facturación
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Rango Inicial --}}
                            <div>
                                <label for="rango_inicial_full" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                                    <i class="fas fa-arrow-right text-purple-500"></i>
                                    Rango Inicial Completo (SAR)
                                </label>
                                <input type="text" id="rango_inicial_full" name="rango_inicial_full" value="{{ old('rango_inicial_full') }}" required maxlength="50"
                                       placeholder="Ej: 000-001-01-00000001"
                                       class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl 
                                              bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 
                                              focus:ring-2 focus:ring-purple-500 focus:border-purple-500 
                                              transition-all duration-200 hover:border-purple-400
                                              placeholder:text-gray-400 dark:placeholder:text-gray-500 font-mono">
                                @error('rango_inicial_full') 
                                    <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                    </p> 
                                @enderror
                            </div>

                            {{-- Rango Final --}}
                            <div>
                                <label for="rango_final_full" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                                    <i class="fas fa-arrow-left text-purple-500"></i>
                                    Rango Final Completo (SAR)
                                </label>
                                <input type="text" id="rango_final_full" name="rango_final_full" value="{{ old('rango_final_full') }}" required maxlength="50"
                                       placeholder="Ej: 000-001-01-00000500"
                                       class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl 
                                              bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 
                                              focus:ring-2 focus:ring-purple-500 focus:border-purple-500 
                                              transition-all duration-200 hover:border-purple-400
                                              placeholder:text-gray-400 dark:placeholder:text-gray-500 font-mono">
                                @error('rango_final_full') 
                                    <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                    </p> 
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- SECCIÓN: CONFIGURACIÓN ADICIONAL --}}
                    <div class="mb-8">
                        <div class="flex items-center gap-3 mb-6 pb-3 border-b-2 border-blue-500">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                <i class="fas fa-cog text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200">
                                Configuración Adicional
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Fecha Límite de Emisión --}}
                            <div>
                                <label for="fecha_limite_emision" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                                    <i class="fas fa-calendar-alt text-blue-500"></i>
                                    Fecha Límite de Emisión
                                </label>
                                <input type="date" id="fecha_limite_emision" name="fecha_limite_emision" value="{{ old('fecha_limite_emision') }}" required
                                       class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl 
                                              bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 
                                              focus:ring-2 focus:ring-blue-500 focus:border-blue-500 
                                              transition-all duration-200 hover:border-blue-400">
                                @error('fecha_limite_emision') 
                                    <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                    </p> 
                                @enderror
                            </div>

                            {{-- Estado Inicial --}}
                            <div class="flex items-center">
                                <div class="p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 
                                            border-2 border-green-300 dark:border-green-700 rounded-xl w-full">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0 w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                            <i class="fas fa-check text-white"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-green-700 dark:text-green-400 mb-1">
                                                Estado Inicial: Activo
                                            </p>
                                            <p class="text-xs text-green-600 dark:text-green-500">
                                                Se activará automáticamente al guardar, desactivando los rangos anteriores de esta tienda.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- BOTONES DE ACCIÓN --}}
                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t-2 border-gray-200 dark:border-gray-700">
                        <a href="{{ route('rangos-cai.index') }}" 
                           class="group px-6 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 
                                  text-gray-800 dark:text-gray-200 rounded-xl font-semibold 
                                  transition-all duration-200 shadow-md hover:shadow-lg
                                  flex items-center justify-center gap-2">
                            <i class="fas fa-times group-hover:rotate-90 transition-transform duration-300"></i>
                            <span>Cancelar</span>
                        </a>
                        
                        <button type="submit" 
                                class="group px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 
                                       hover:from-green-600 hover:to-emerald-700 text-white rounded-xl font-semibold 
                                       transition-all duration-300 shadow-lg hover:shadow-xl 
                                       flex items-center justify-center gap-2 transform hover:scale-105">
                            <i class="fas fa-save group-hover:scale-110 transition-transform duration-300"></i>
                            <span>Registrar Rango CAI</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>