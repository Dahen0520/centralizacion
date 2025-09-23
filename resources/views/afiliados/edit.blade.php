<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Afiliado') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('afiliados.update', $afiliado) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="nombre" class="block font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre</label>
                                <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $afiliado->nombre) }}" class="block w-full py-2 text-base rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white" required>
                                @error('nombre')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>
                            <div>
                                <label for="email" class="block font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $afiliado->email) }}" class="block w-full py-2 text-base rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                @error('email')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>
                            <div>
                                <label for="telefono" class="block font-medium text-gray-700 dark:text-gray-300 mb-1">Teléfono</label>
                                <input type="text" name="telefono" id="telefono" value="{{ old('telefono', $afiliado->telefono) }}" class="block w-full py-2 text-base rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                @error('telefono')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>
                            <div>
                                <label for="municipio_id" class="block font-medium text-gray-700 dark:text-gray-300 mb-1">Municipio</label>
                                <select name="municipio_id" id="municipio_id" class="block w-full py-2 text-base rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                    <option value="">Seleccione un municipio</option>
                                    @foreach($municipios as $municipio)
                                        <option value="{{ $municipio->id }}" @selected(old('municipio_id', $afiliado->municipio_id) == $municipio->id)>{{ $municipio->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('municipio_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>
                            <div>
                                <label for="barrio" class="block font-medium text-gray-700 dark:text-gray-300 mb-1">Barrio</label>
                                <input type="text" name="barrio" id="barrio" value="{{ old('barrio', $afiliado->barrio) }}" class="block w-full py-2 text-base rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                @error('barrio')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>
                            <div>
                                <label for="rtn" class="block font-medium text-gray-700 dark:text-gray-300 mb-1">RTN</label>
                                <input type="text" name="rtn" id="rtn" value="{{ old('rtn', $afiliado->rtn) }}" class="block w-full py-2 text-base rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                @error('rtn')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>
                            <div>
                                <label for="numero_cuenta" class="block font-medium text-gray-700 dark:text-gray-300 mb-1">Número de Cuenta</label>
                                <input type="text" name="numero_cuenta" id="numero_cuenta" value="{{ old('numero_cuenta', $afiliado->numero_cuenta) }}" class="block w-full py-2 text-base rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                @error('numero_cuenta')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>
                            <div>
                                <label for="status" class="block font-medium text-gray-700 dark:text-gray-300 mb-1">Estado</label>
                                <select name="status" id="status" class="block w-full py-2 text-base rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white" required>
                                    <option value="0" @selected(old('status', $afiliado->status) == 0)>Pendiente</option>
                                    <option value="1" @selected(old('status', $afiliado->status) == 1)>Activo</option>
                                    <option value="2" @selected(old('status', $afiliado->status) == 2)>Rechazado</option>
                                </select>
                                @error('status')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end items-center space-x-4">
                            <a href="{{ route('afiliados.list') }}" class="px-4 py-2 text-gray-600 dark:text-gray-400 bg-gray-200 dark:bg-gray-700 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">Cancelar</a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>