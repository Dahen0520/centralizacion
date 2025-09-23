<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Editar Empresa') }}
        </h2>
    </x-slot>

    <div class="w-full max-w-6xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border-t-8 border-blue-600 mx-auto mt-12">
        <div class="text-center mb-8">
            <p class="text-gray-600 dark:text-gray-400 text-base mb-4">Actualiza los datos de la empresa</p>
            <a href="{{ route('empresas.index') }}" class="text-base text-blue-600 dark:text-blue-400 hover:underline font-medium block">
                <i class="fas fa-arrow-left mr-2"></i> Volver a la Lista de Empresas
            </a>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong class="font-bold">¡Oops!</strong>
                <span class="block sm:inline">Hay algunos problemas con los datos que ingresaste.</span>
                <ul class="mt-3 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('empresas.update', $empresa) }}">
            @csrf
            @method('PUT')

            {{-- Campo Nombre del Negocio --}}
            <div class="mb-4">
                <label for="nombre_negocio" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Nombre del Negocio</label>
                <input type="text" id="nombre_negocio" name="nombre_negocio" value="{{ old('nombre_negocio', $empresa->nombre_negocio) }}" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('nombre_negocio') border-red-500 @enderror">
                @error('nombre_negocio')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- Campo Dirección --}}
            <div class="mb-4">
                <label for="direccion" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Dirección</label>
                <input type="text" id="direccion" name="direccion" value="{{ old('direccion', $empresa->direccion) }}" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('direccion') border-red-500 @enderror">
                @error('direccion')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- Campo Rubro (Select) --}}
            <div class="mb-4">
                <label for="rubro_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Rubro</label>
                <select id="rubro_id" name="rubro_id" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('rubro_id') border-red-500 @enderror">
                    <option value="">Seleccione un rubro</option>
                    @foreach($rubros as $rubro)
                        <option value="{{ $rubro->id }}" {{ (old('rubro_id', $empresa->rubro_id) == $rubro->id) ? 'selected' : '' }}>
                            {{ $rubro->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('rubro_id')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- Campo Tipo de Organización (Select) --}}
            <div class="mb-4">
                <label for="tipo_organizacion_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Tipo de Organización</label>
                <select id="tipo_organizacion_id" name="tipo_organizacion_id" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('tipo_organizacion_id') border-red-500 @enderror">
                    <option value="">Seleccione un tipo</option>
                    @foreach($tiposOrganizacion as $tipo)
                        <option value="{{ $tipo->id }}" {{ (old('tipo_organizacion_id', $empresa->tipo_organizacion_id) == $tipo->id) ? 'selected' : '' }}>
                            {{ $tipo->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('tipo_organizacion_id')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- Campo País de Exportación (Select) --}}
            <div class="mb-4">
                <label for="pais_exportacion_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">País de Exportación</label>
                <select id="pais_exportacion_id" name="pais_exportacion_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('pais_exportacion_id') border-red-500 @enderror">
                    <option value="">Ninguno</option>
                    @foreach($paises as $pais)
                        <option value="{{ $pais->id }}" {{ (old('pais_exportacion_id', $empresa->pais_exportacion_id) == $pais->id) ? 'selected' : '' }}>
                            {{ $pais->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('pais_exportacion_id')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- Campo Estado (Select) --}}
            <div class="mb-4">
                <label for="estado" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Estado</label>
                <select id="estado" name="estado" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('estado') border-red-500 @enderror">
                    @foreach(['pendiente', 'aprobado', 'rechazado'] as $estado)
                        <option value="{{ $estado }}" {{ (old('estado', $empresa->estado) == $estado) ? 'selected' : '' }}>
                            {{ ucfirst($estado) }}
                        </option>
                    @endforeach
                </select>
                @error('estado')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <hr class="my-8 border-gray-200 dark:border-gray-700">

            <button type="submit" class="w-full py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition duration-200 btn-hover-scale text-lg">
                Actualizar Empresa
            </button>
        </form>
    </div>
</x-app-layout>