<x-app-layout>
    <x-slot name="header">
    </x-slot>

    {{-- INCLUSIÓN DE SWEETALERT2 PARA NOTIFICACIONES --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- CLASES DE ESPACIADO AJUSTADAS PARA UN FORMULARIO COMPACTO Y ELEGANTE --}}
    <div class="py-6 flex justify-center">
        <div class="w-full max-w-3xl mx-auto"> {{-- Aumentado el ancho a 3xl para acomodar más campos --}}
            <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 
                        rounded-2xl shadow-3xl p-10 lg:p-12 
                        border-t-4 border-b-4 border-blue-500 dark:border-blue-600 
                        transform hover:shadow-4xl transition-all duration-300 ease-in-out">
                
                {{-- Bloque de Encabezado Elegante --}}
                <div class="text-center mb-10">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full 
                                bg-gradient-to-br from-blue-500 to-blue-700 text-white 
                                mb-5 shadow-lg transform hover:scale-110 transition-all duration-300 ease-in-out 
                                dark:from-blue-600 dark:to-blue-800">
                         <i class="fas fa-edit text-4xl"></i>
                    </div>
                    <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-2 leading-tight">
                        Modificar: <span class="text-blue-600 dark:text-blue-400">{{ $empresa->nombre_negocio }}</span>
                    </h3>
                    <p class="text-md text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                        Actualiza la información principal y de clasificación de la empresa.
                    </p>
                    
                    <a href="{{ route('empresas.index') }}" 
                       class="mt-6 inline-flex items-center text-sm font-semibold 
                              text-blue-600 dark:text-blue-400 
                              hover:text-blue-800 dark:hover:text-blue-200 
                              transition duration-300 ease-in-out 
                              transform hover:-translate-x-1 hover:underline">
                        <i class="fas fa-arrow-left mr-2"></i> Volver a la Lista de Empresas
                    </a>
                </div>

                {{-- Manejo de Errores de Validación Estilizado --}}
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

                <form method="POST" action="{{ route('empresas.update', $empresa) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Campo Nombre del Negocio --}}
                        <div class="md:col-span-2 mb-4">
                            <label for="nombre_negocio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nombre del Negocio <span class="text-red-500">*</span></label>
                            <input type="text" id="nombre_negocio" name="nombre_negocio" value="{{ old('nombre_negocio', $empresa->nombre_negocio) }}" required 
                                   placeholder="Nombre comercial de la empresa"
                                   class="w-full px-5 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-blue-500 focus:border-blue-500 transition @error('nombre_negocio') border-red-500 ring-red-500 @enderror">
                            @error('nombre_negocio')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center"><i class="fas fa-info-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Campo Dirección --}}
                        <div class="md:col-span-2 mb-4">
                            <label for="direccion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dirección <span class="text-red-500">*</span></label>
                            <input type="text" id="direccion" name="direccion" value="{{ old('direccion', $empresa->direccion) }}" required 
                                   placeholder="Dirección principal o fiscal"
                                   class="w-full px-5 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-blue-500 focus:border-blue-500 transition @error('direccion') border-red-500 ring-red-500 @enderror">
                            @error('direccion')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center"><i class="fas fa-info-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Campo Rubro (Select) --}}
                        <div class="mb-4">
                            <label for="rubro_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rubro <span class="text-red-500">*</span></label>
                            <select id="rubro_id" name="rubro_id" required 
                                    class="w-full px-5 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 transition @error('rubro_id') border-red-500 ring-red-500 @enderror">
                                <option value="">Seleccione un rubro</option>
                                @foreach($rubros as $rubro)
                                    <option value="{{ $rubro->id }}" {{ (old('rubro_id', $empresa->rubro_id) == $rubro->id) ? 'selected' : '' }}>
                                        {{ $rubro->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('rubro_id')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center"><i class="fas fa-info-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Campo Tipo de Organización (Select) --}}
                        <div class="mb-4">
                            <label for="tipo_organizacion_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo de Organización <span class="text-red-500">*</span></label>
                            <select id="tipo_organizacion_id" name="tipo_organizacion_id" required 
                                    class="w-full px-5 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 transition @error('tipo_organizacion_id') border-red-500 ring-red-500 @enderror">
                                <option value="">Seleccione un tipo</option>
                                @foreach($tiposOrganizacion as $tipo)
                                    <option value="{{ $tipo->id }}" {{ (old('tipo_organizacion_id', $empresa->tipo_organizacion_id) == $tipo->id) ? 'selected' : '' }}>
                                        {{ $tipo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo_organizacion_id')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center"><i class="fas fa-info-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- Campo País de Exportación (Select) --}}
                        <div class="mb-4">
                            <label for="pais_exportacion_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">País de Exportación</label>
                            <select id="pais_exportacion_id" name="pais_exportacion_id" 
                                    class="w-full px-5 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 transition @error('pais_exportacion_id') border-red-500 ring-red-500 @enderror">
                                <option value="">Ninguno</option>
                                @foreach($paises as $pais)
                                    <option value="{{ $pais->id }}" {{ (old('pais_exportacion_id', $empresa->pais_exportacion_id) == $pais->id) ? 'selected' : '' }}>
                                        {{ $pais->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pais_exportacion_id')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center"><i class="fas fa-info-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Campo Estado (Select) --}}
                        <div class="mb-4">
                            <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Estado <span class="text-red-500">*</span></label>
                            <select id="estado" name="estado" required 
                                    class="w-full px-5 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 transition @error('estado') border-red-500 ring-red-500 @enderror">
                                @foreach(['pendiente', 'aprobado', 'rechazado'] as $estado)
                                    <option value="{{ $estado }}" {{ (old('estado', $empresa->estado) == $estado) ? 'selected' : '' }}>
                                        {{ ucfirst($estado) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('estado')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center"><i class="fas fa-info-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- CAMPO CHECKBOX FACTURACIÓN --}}
                        <div class="md:col-span-2 mt-2">
                            <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 shadow-inner">
                                <input type="checkbox" id="facturacion" name="facturacion" value="1"
                                    class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-600 dark:border-gray-500"
                                    @checked(old('facturacion', $empresa->facturacion))>
                                <label for="facturacion" class="ml-3 block text-sm font-bold text-gray-700 dark:text-gray-200">
                                    Habilitar Facturación
                                </label>
                                <i class="fas fa-info-circle ml-auto text-gray-400 dark:text-gray-500 cursor-pointer" 
                                title="Marca esta casilla para habilitar la capacidad de generar facturas fiscales para esta empresa."></i>
                                @error('facturacion')
                                    <p class="text-red-500 text-xs italic mt-2 flex items-center"><i class="fas fa-info-circle mr-1"></i> {{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        {{-- FIN CAMPO CHECKBOX FACTURACIÓN --}}

                    </div>

                    <hr class="my-10 border-gray-200 dark:border-gray-700 opacity-60 md:col-span-2">

                    <button type="submit" 
                            class="w-full py-4 bg-gradient-to-r from-green-500 to-teal-600 text-white 
                                   font-extrabold rounded-xl shadow-lg hover:shadow-xl 
                                   hover:from-green-600 hover:to-teal-700 
                                   transition duration-300 ease-in-out transform hover:-translate-y-0.5 
                                   text-xl uppercase tracking-widest focus:outline-none focus:ring-4 
                                   focus:ring-green-300 dark:focus:ring-green-800">
                        <i class="fas fa-sync-alt mr-2"></i> Actualizar Empresa
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- SCRIPT PARA NOTIFICACIÓN DE SESIÓN (Redirigido desde el update) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: '¡Actualización Exitosa!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    toast: true,
                    position: 'top-end'
                });
            @endif
        });
    </script>
</x-app-layout>