<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Crear Nueva Marca') }}
        </h2>
    </x-slot>

    <div class="w-full max-w-6xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border-t-8 border-blue-600 mx-auto mt-12">
        <div class="text-center mb-8">
            <p class="text-gray-600 dark:text-gray-400 text-base mb-4">Ingresa los datos de la nueva marca</p>
            <a href="{{ route('marcas.index') }}" class="text-base text-blue-600 dark:text-blue-400 hover:underline font-medium block">
                <i class="fas fa-arrow-left mr-2"></i> Volver a la Lista de Marcas
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

        <form method="POST" action="{{ route('marcas.store') }}">
            @csrf
            
            <div class="mb-4">
                <label for="producto_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Producto</label>
                <select name="producto_id" id="producto_id" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('producto_id') border-red-500 @enderror">
                    <option value="">Selecciona un producto</option>
                    @foreach($productos as $producto)
                        <option value="{{ $producto->id }}" {{ old('producto_id') == $producto->id ? 'selected' : '' }}>
                            {{ $producto->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('producto_id')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="empresa_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Empresa</label>
                <select name="empresa_id" id="empresa_id" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('empresa_id') border-red-500 @enderror">
                    <option value="">Selecciona una empresa</option>
                    @foreach($empresas as $empresa)
                        <option value="{{ $empresa->id }}" {{ old('empresa_id') == $empresa->id ? 'selected' : '' }}>
                            {{ $empresa->nombre_negocio }}
                        </option>
                    @endforeach
                </select>
                @error('empresa_id')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="estado" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Estado</label>
                <select name="estado" id="estado" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('estado') border-red-500 @enderror">
                    <option value="pendiente" {{ old('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="aprobado" {{ old('estado') == 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                    <option value="rechazado" {{ old('estado') == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                </select>
                @error('estado')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>
            
            <hr class="my-8 border-gray-200 dark:border-gray-700">

            <button type="submit" class="w-full py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition duration-200 btn-hover-scale text-lg">
                Guardar Marca
            </button>
        </form>
    </div>
</x-app-layout>
