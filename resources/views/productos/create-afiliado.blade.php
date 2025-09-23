<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sugerir Producto</title>
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link
      href="https://fonts.bunny.net/css?family=inter:400,600,700&display=swap"
      rel="stylesheet"
    />
    <script src="https://cdn.tailwindcss.com"></script>
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}" />
    <style>
      select:not([multiple]) {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: url(
          "data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='none'%3e%3cpath d='M7 7l3-3 3 3m0 6l-3 3-3-3' stroke='%23d1d5db' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3e%3c/svg%3e"
        );
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 1.5em 1.5em;
        padding-right: 2.5rem;
      }
    </style>
  </head>
  <body
  
    class="body-animated-gradient dark:dark-body-animated-gradient text-[#1b1b18] dark:text-[#EDEDEC] flex items-center justify-center min-h-screen font-sans p-6"
  >
    
    <div
      class="w-full max-w-4xl bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-10 relative z-10 border border-gray-200 dark:border-gray-800 transition-transform duration-300 ease-in-out transform hover:scale-[1.01]"
    >
      <header class="text-center mb-10">
        <img
          src="{{ asset('assets/imgs/vertical.png') }}"
          alt="Logo"
          class="mx-auto mb-5 w-56 h-auto"
        />
        <h2
          class="text-4xl font-extrabold text-gray-900 dark:text-gray-100 mb-3 tracking-tight"
        >
          Sugerir Nuevo Producto
        </h2>
        <p class="text-lg text-gray-600 dark:text-gray-400">
          Describe tu producto para que sea revisado y aprobado.
        </p>
      </header>
      
      @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
          <strong class="font-bold">¡Éxito!</strong>
          <span class="block sm:inline">{{ session('success') }}</span>
        </div>
      @endif

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
      
      <p class="text-center mb-6 text-gray-500 dark:text-gray-400 font-semibold text-lg">
          Productos restantes: <span class="text-blue-600 dark:text-blue-400">{{ $productosDisponibles }} de 5</span>
      </p>

      <form method="POST" action="{{ route('afiliados.productos.store') }}" class="space-y-8">
        @csrf
        
        {{-- Campo oculto para pasar el ID de la empresa --}}
        <input type="hidden" name="empresa_id" value="{{ request('empresa_id') }}">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div>
            <label for="nombre" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 cursor-pointer">Nombre del Producto</label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required class="block w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-3 px-4 text-base shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="Ej: Jabón Artesanal de Lavanda"/>
            @error('nombre')
              <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label for="subcategoria_id" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 cursor-pointer">Subcategoría</label>
            <select name="subcategoria_id" id="subcategoria_id" required class="block w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-3 px-4 text-base shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                <option value="" disabled selected>Selecciona una subcategoría</option>
                @foreach($subcategorias as $subcategoria)
                    <option value="{{ $subcategoria->id }}" {{ old('subcategoria_id') == $subcategoria->id ? 'selected' : '' }}>{{ $subcategoria->nombre }}</option>
                @endforeach
            </select>
            @error('subcategoria_id')
              <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
            @enderror
          </div>
          
          <div class="md:col-span-2">
            <label for="descripcion" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 cursor-pointer">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="4" class="block w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-3 px-4 text-base shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="Describe tu producto con detalle">{{ old('descripcion') }}</textarea>
            @error('descripcion')
              <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
            @enderror
          </div>
        </div>

        <div class="mt-10 flex flex-col sm:flex-row justify-end gap-4">
            @if ($productosDisponibles > 0)
            <button
              type="submit"
              name="action"
              value="agregar-otro"
              class="px-8 py-4 bg-green-600 text-white rounded-md font-bold shadow-md hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-500 focus:ring-opacity-50 transition transform hover:scale-105"
            >
              <i class="fas fa-plus mr-2"></i> Agregar Otro Producto
            </button>
            @endif
            <button
              type="submit"
              name="action"
              value="finalizar"
              class="px-8 py-4 bg-blue-600 text-white rounded-md font-bold shadow-md hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-opacity-50 transition transform hover:scale-105"
            >
              <i class="fas fa-check-circle mr-2"></i> Finalizar
            </button>
        </div>
      </form>
    </div>
  </body>
</html>