<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Rango CAI') }}
        </h2>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-12 flex justify-center">
        <div class="w-full max-w-xl mx-auto">
            <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 
                        rounded-2xl shadow-3xl p-10 lg:p-12 
                        border-t-4 border-b-4 border-indigo-500 dark:border-indigo-600 
                        transform hover:shadow-4xl transition-all duration-300 ease-in-out">
                
                {{-- Bloque de Encabezado --}}
                <div class="text-center mb-10">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full 
                                bg-gradient-to-br from-indigo-500 to-purple-600 text-white 
                                mb-5 shadow-lg transform hover:scale-110 transition-all duration-300 ease-in-out">
                         <i class="fas fa-file-invoice-dollar text-4xl"></i>
                    </div>
                    <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-2 leading-tight">
                        Modificar Rango CAI: <span class="text-indigo-600 dark:text-indigo-400">{{ $rangoCai->cai }}</span>
                    </h3>
                    <p class="text-md text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                        Actualiza el estado de vigencia y la fecha límite de este rango.
                    </p>
                    
                    <a href="{{ route('rangos-cai.index') }}" 
                       class="mt-6 inline-flex items-center text-sm font-semibold 
                              text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-200 
                              transition duration-300 ease-in-out transform hover:-translate-x-1 hover:underline">
                        <i class="fas fa-arrow-left mr-2"></i> Volver a la Gestión de Rangos
                    </a>
                </div>

                {{-- Manejo de Errores de Validación --}}
                @if ($errors->any())
                    <div class="bg-red-50 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-700 dark:text-red-200 
                                px-6 py-4 rounded-lg relative mb-8 shadow-inner" role="alert">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-exclamation-triangle text-xl mr-3 text-red-500 dark:text-red-300"></i>
                            <strong class="font-bold text-lg">¡Atención! Datos Inválidos</strong>
                        </div>
                        <ul class="list-disc list-inside text-sm space-y-1 ml-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- 🔑 CORRECCIÓN: Pasar el ID numérico explícito para resolver el error de parámetro --}}
                <form method="POST" action="{{ route('rangos-cai.update', $rangoCai->id) }}">
                    @csrf
                    @method('PUT')

                    {{-- INFORMACIÓN NO EDITABLE --}}
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Información del Rango:</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">CAI: {{ $rangoCai->cai }}</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">Tienda: {{ $rangoCai->tienda->nombre ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">Rango: {{ $rangoCai->rango_inicial }} - {{ $rangoCai->rango_final }}</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">Último Nº Emitido: {{ $rangoCai->numero_actual }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Campo ESTADO ACTIVO --}}
                        <div class="mb-4">
                            <label for="esta_activo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Estado del Rango <span class="text-red-500">*</span></label>
                            <select name="esta_activo" id="esta_activo" required 
                                    class="w-full px-5 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition @error('esta_activo') border-red-500 ring-red-500 @enderror">
                                
                                {{-- Si está activo, lo muestra --}}
                                <option value="1" @selected(old('esta_activo', $rangoCai->esta_activo) == 1)>
                                    Activo (Será usado para facturar)
                                </option>
                                {{-- Si está inactivo --}}
                                <option value="0" @selected(old('esta_activo', $rangoCai->esta_activo) == 0)>
                                    Inactivo (Solo Historial)
                                </option>
                            </select>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Al activar, otros rangos de esta tienda se desactivarán.</p>
                            @error('esta_activo')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center font-medium">
                                    <i class="fas fa-info-circle mr-1"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Campo FECHA LÍMITE --}}
                        <div class="mb-4">
                            <label for="fecha_limite_emision" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fecha Límite de Emisión <span class="text-red-500">*</span></label>
                            @php
                                // Formatear la fecha para que el input type="date" la acepte (YYYY-MM-DD)
                                $currentDate = \Carbon\Carbon::parse($rangoCai->fecha_limite_emision)->format('Y-m-d');
                            @endphp
                            <input type="date" id="fecha_limite_emision" name="fecha_limite_emision" 
                                   value="{{ old('fecha_limite_emision', $currentDate) }}" required 
                                   class="w-full px-5 py-3.5 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition @error('fecha_limite_emision') border-red-500 ring-red-500 @enderror">
                            @error('fecha_limite_emision')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center font-medium">
                                    <i class="fas fa-info-circle mr-1"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>
                    
                    <hr class="my-10 border-gray-200 dark:border-gray-700 opacity-60">

                    <button type="submit" 
                            class="w-full py-4 bg-gradient-to-r from-green-600 to-teal-700 text-white 
                                   font-extrabold rounded-xl shadow-lg hover:shadow-xl 
                                   hover:from-green-700 hover:to-teal-800 
                                   transition duration-300 ease-in-out transform hover:-translate-y-0.5 
                                   text-xl uppercase tracking-widest focus:outline-none focus:ring-4 
                                   focus:ring-green-300 dark:focus:ring-green-800">
                        <i class="fas fa-sync-alt mr-2"></i> Actualizar Rango
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>