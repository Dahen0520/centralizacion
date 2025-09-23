<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Editar Subcategoría') }}
        </h2>
    </x-slot>

    <div class="w-full max-w-6xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border-t-8 border-blue-600 mx-auto mt-12">
        <div class="text-center mb-8">
            <p class="text-gray-600 dark:text-gray-400 text-base mb-4">Actualiza los datos de la subcategoría.</p>
            <a href="{{ route('subcategorias.index') }}" class="text-base text-blue-600 dark:text-blue-400 hover:underline font-medium block">
                <i class="fas fa-arrow-left mr-2"></i> Volver a la lista
            </a>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong class="font-bold">¡Ups!</strong>
                <span class="block sm:inline">Hay algunos problemas con tu entrada.</span>
                <ul class="mt-3 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                    </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('subcategorias.update', $subcategoria->id) }}">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="categoria_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Categoría</label>
                <select name="categoria_id" id="categoria_id" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('categoria_id') border-red-500 @enderror">
                    <option value="">Seleccione una categoría</option>
                    @foreach ($categorias as $categoria)
                        <option value="{{ $categoria->id }}" {{ old('categoria_id', $subcategoria->categoria_id) == $categoria->id ? 'selected' : '' }}>
                            {{ $categoria->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('categoria_id')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="nombre" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Nombre</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $subcategoria->nombre) }}" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('nombre') border-red-500 @enderror">
                @error('nombre')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>
            
            <hr class="my-8 border-gray-200 dark:border-gray-700">

            <button type="submit" class="w-full py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition duration-200 btn-hover-scale text-lg">
                Actualizar Subcategoría
            </button>
        </form>
    </div>
</x-app-layout>