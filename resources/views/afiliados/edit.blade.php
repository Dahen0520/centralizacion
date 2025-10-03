<x-app-layout>
    <x-slot name="header">
    </x-slot>

    {{-- INCLUSIÓN DE SWEETALERT2 PARA NOTIFICACIONES --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-6 flex justify-center">
        <div class="w-full max-w-4xl mx-auto sm:px-6 lg:px-8">
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
                         <i class="fas fa-user-edit text-4xl"></i>
                    </div>
                    <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-2 leading-tight">
                        Modificar Afiliado: <span class="text-blue-600 dark:text-blue-400">{{ $afiliado->nombre }}</span>
                    </h3>
                    <p class="text-md text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                        Actualiza la información personal, de contacto y financiera del afiliado.
                    </p>
                    
                    <a href="{{ route('afiliados.list') }}" 
                       class="mt-6 inline-flex items-center text-sm font-semibold 
                              text-blue-600 dark:text-blue-400 
                              hover:text-blue-800 dark:hover:text-blue-200 
                              transition duration-300 ease-in-out 
                              transform hover:-translate-x-1 hover:underline">
                        <i class="fas fa-arrow-left mr-2"></i> Volver a la Lista
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

                <form action="{{ route('afiliados.update', $afiliado) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-8">
                        
                        {{-- Nombre --}}
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nombre <span class="text-red-500">*</span></label>
                            <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $afiliado->nombre) }}" required 
                                   class="w-full px-5 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 transition @error('nombre') border-red-500 ring-red-500 @enderror">
                            @error('nombre')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center"><i class="fas fa-info-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $afiliado->email) }}" 
                                   class="w-full px-5 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 transition @error('email') border-red-500 ring-red-500 @enderror">
                            @error('email')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center"><i class="fas fa-info-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- Teléfono --}}
                        <div>
                            <label for="telefono" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Teléfono</label>
                            <input type="text" name="telefono" id="telefono" value="{{ old('telefono', $afiliado->telefono) }}" 
                                   class="w-full px-5 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 transition @error('telefono') border-red-500 ring-red-500 @enderror">
                            @error('telefono')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center"><i class="fas fa-info-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- Municipio --}}
                        <div>
                            <label for="municipio_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Municipio</label>
                            <select name="municipio_id" id="municipio_id" 
                                    class="w-full px-5 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 transition @error('municipio_id') border-red-500 ring-red-500 @enderror">
                                <option value="">Seleccione un municipio</option>
                                @foreach($municipios as $municipio)
                                    <option value="{{ $municipio->id }}" @selected(old('municipio_id', $afiliado->municipio_id) == $municipio->id)>{{ $municipio->nombre }}</option>
                                @endforeach
                            </select>
                            @error('municipio_id')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center"><i class="fas fa-info-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- Barrio --}}
                        <div>
                            <label for="barrio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Barrio</label>
                            <input type="text" name="barrio" id="barrio" value="{{ old('barrio', $afiliado->barrio) }}" 
                                   class="w-full px-5 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 transition @error('barrio') border-red-500 ring-red-500 @enderror">
                            @error('barrio')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center"><i class="fas fa-info-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- RTN --}}
                        <div>
                            <label for="rtn" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">RTN</label>
                            <input type="text" name="rtn" id="rtn" value="{{ old('rtn', $afiliado->rtn) }}" 
                                   class="w-full px-5 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 transition @error('rtn') border-red-500 ring-red-500 @enderror">
                            @error('rtn')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center"><i class="fas fa-info-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- Número de Cuenta --}}
                        <div>
                            <label for="numero_cuenta" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Número de Cuenta</label>
                            <input type="text" name="numero_cuenta" id="numero_cuenta" value="{{ old('numero_cuenta', $afiliado->numero_cuenta) }}" 
                                   class="w-full px-5 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 transition @error('numero_cuenta') border-red-500 ring-red-500 @enderror">
                            @error('numero_cuenta')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center"><i class="fas fa-info-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- Estado --}}
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Estado <span class="text-red-500">*</span></label>
                            <select name="status" id="status" required
                                    class="w-full px-5 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 transition @error('status') border-red-500 ring-red-500 @enderror">
                                <option value="0" @selected(old('status', $afiliado->status) == 0)>Pendiente</option>
                                <option value="1" @selected(old('status', $afiliado->status) == 1)>Activo</option>
                                <option value="2" @selected(old('status', $afiliado->status) == 2)>Rechazado</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-xs italic mt-2 flex items-center"><i class="fas fa-info-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-10 border-gray-200 dark:border-gray-700 opacity-60">

                    <div class="flex justify-end items-center space-x-4">
                        <a href="{{ route('afiliados.list') }}" 
                           class="px-6 py-3 text-gray-700 dark:text-gray-400 bg-gray-200 dark:bg-gray-700 rounded-lg font-semibold shadow-md hover:bg-gray-300 dark:hover:bg-gray-600 transition duration-150 ease-in-out">
                            <i class="fas fa-times-circle mr-2"></i> Cancelar
                        </a>
                        <button type="submit" 
                                class="px-8 py-3 bg-gradient-to-r from-green-500 to-teal-600 text-white 
                                       font-extrabold rounded-lg shadow-lg hover:shadow-xl 
                                       hover:from-green-600 hover:to-teal-700 
                                       transition duration-300 ease-in-out transform hover:-translate-y-0.5 
                                       text-base uppercase tracking-widest focus:outline-none focus:ring-4 
                                       focus:ring-green-300 dark:focus:ring-green-800">
                            <i class="fas fa-sync-alt mr-2"></i> Actualizar Afiliado
                        </button>
                    </div>
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