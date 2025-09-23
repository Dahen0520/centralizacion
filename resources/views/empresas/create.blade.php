<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Registro de Empresa</title>
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
      /* Estilo custom para select */
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

      /* Checkbox container custom */
      .checkbox-container {
        border: 1px solid #d1d5db; /* border-gray-300 */
        background-color: #f9fafb; /* bg-gray-50 */
        border-radius: 0.375rem; /* rounded-md */
        padding: 1rem;
        max-height: 220px;
        overflow-y: auto;
        -webkit-transition: all 0.2s ease-in-out;
        transition: all 0.2s ease-in-out;
        box-shadow: inset 0 1px 2px rgb(0 0 0 / 0.05);
      }

      .checkbox-container:focus-within {
        border-color: #3b82f6; /* focus:border-blue-500 */
        box-shadow: 0 0 0 2px #3b82f6; /* foco más visible */
        outline: none;
      }

      /* Checkboxes */
      .checkbox-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.6rem;
        cursor: pointer;
        padding: 0.5rem 0.6rem;
        border-radius: 0.25rem;
        transition: background-color 0.2s ease-in-out;
        font-weight: 500;
      }

      .checkbox-item:hover {
        background-color: #eff6ff; /* bg-blue-50 */
      }

      .checkbox-item input[type="checkbox"] {
        margin-right: 0.8rem;
        width: 1.1rem;
        height: 1.1rem;
        accent-color: #3b82f6; /* azul enfocado */
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
          Registro de Empresa
        </h2>
        <p class="text-lg text-gray-600 dark:text-gray-400">
          Datos de la empresa para el afiliado:
          <span class="font-semibold text-blue-600">{{ $afiliado->nombre }}</span>
        </p>
      </header>
      
      {{-- **NUEVO: BLOQUE DE ERRORES** --}}
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

      <form action="{{ route('empresas.store') }}" method="POST" class="space-y-8">
        @csrf
        <input type="hidden" name="afiliado_id" value="{{ $afiliado->id }}" />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div>
            <label
              for="nombre_negocio"
              class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 cursor-pointer"
              >Nombre del Negocio</label
            >
            <input
              type="text"
              name="nombre_negocio"
              id="nombre_negocio"
              value="{{ old('nombre_negocio') }}" {{-- **CORREGIDO** --}}
              required
              class="block w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-3 px-4 text-base shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
              placeholder="Ejemplo: Mi Empresa S.A."
            />
            @error('nombre_negocio') {{-- **NUEVO** --}}
              <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label
              for="direccion"
              class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 cursor-pointer"
              >Dirección</label
            >
            <input
              type="text"
              name="direccion"
              id="direccion"
              value="{{ old('direccion') }}" {{-- **CORREGIDO** --}}
              required
              class="block w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-3 px-4 text-base shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
              placeholder="Ejemplo: Calle Falsa 123, Ciudad"
            />
            @error('direccion') {{-- **NUEVO** --}}
              <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label
              for="rubro_id"
              class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 cursor-pointer"
              >Rubro</label
            >
            <select
              name="rubro_id"
              id="rubro_id"
              required
              class="block w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-3 px-4 text-base shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
            >
              <option value="" disabled selected>Seleccione un rubro</option>
              @foreach($rubros as $rubro)
              <option value="{{ $rubro->id }}" {{ old('rubro_id') == $rubro->id ? 'selected' : '' }}>{{ $rubro->nombre }}</option>
              @endforeach
            </select>
            @error('rubro_id') {{-- **NUEVO** --}}
              <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label
              for="tipo_organizacion_id"
              class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 cursor-pointer"
              >Tipo de Organización</label
            >
            <select
              name="tipo_organizacion_id"
              id="tipo_organizacion_id"
              required
              class="block w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-3 px-4 text-base shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
            >
              <option value="" disabled selected>Seleccione una organización</option>
              @foreach($tiposOrganizacion as $tipo)
              <option value="{{ $tipo->id }}" {{ old('tipo_organizacion_id') == $tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
              @endforeach
            </select>
            @error('tipo_organizacion_id') {{-- **NUEVO** --}}
              <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label
              for="pais_exportacion_id"
              class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 cursor-pointer"
              >País de Exportación (Opcional)</label
            >
            <select
              name="pais_exportacion_id"
              id="pais_exportacion_id"
              class="block w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-3 px-4 text-base shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
            >
              <option value="" disabled selected
                >Seleccione un país (Opcional)</option
              >
              @foreach($paises as $pais)
              <option value="{{ $pais->id }}" {{ old('pais_exportacion_id') == $pais->id ? 'selected' : '' }}>{{ $pais->nombre }}</option>
              @endforeach
            </select>
            @error('pais_exportacion_id') {{-- **NUEVO** --}}
              <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
            @enderror
          </div>

          <div class="md:col-span-2">
            <label
              class="block mb-3 text-gray-700 dark:text-gray-300 font-semibold"
              >Tiendas en las que desea participar</label
            >
            <div
              tabindex="0"
              role="group"
              aria-labelledby="checkbox-group"
              class="checkbox-container"
            >
              @foreach($tiendas as $tienda)
              <label class="checkbox-item cursor-pointer">
                <input
                  type="checkbox"
                  name="tiendas[]"
                  value="{{ $tienda->id }}"
                  {{ in_array($tienda->id, old('tiendas', [])) ? 'checked' : '' }} {{-- **CORREGIDO** --}}
                  class="rounded accent-blue-600 focus:ring-blue-500 focus:outline-none"
                />
                <span>{{ $tienda->nombre }}</span>
              </label>
              @endforeach
            </div>
            @error('tiendas') {{-- **NUEVO** --}}
              <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
            @enderror
          </div>
        </div>

        <div class="mt-10 flex justify-end">
          <button
            type="submit"
            class="px-8 py-4 bg-blue-600 text-white rounded-md font-bold shadow-md hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-opacity-50 transition transform hover:scale-105"
          >
            Guardar Empresa
          </button>
        </div>
      </form>
    </div>
  </body>
</html>