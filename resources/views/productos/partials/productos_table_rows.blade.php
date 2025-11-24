@php
    // Asegurar que $start_index esté definida (pasada desde el controlador)
    $startIndex = $start_index ?? 1;
@endphp

@forelse ($productos as $index => $producto)
<tr id="producto-row-{{ $producto->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 ease-in-out">
    
    {{-- COLUMNA DE NUMERACIÓN (#) --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300 text-center">
        {{ $startIndex + $index }}
    </td>

    {{-- ID (font-normal y color sutil) --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
        <span class="font-normal text-gray-600 dark:text-gray-400">{{ $producto->id }}</span>
    </td>

    {{-- NOMBRE (font-normal) --}}
    <td class="px-6 py-4 whitespace-nowrap text-base font-normal text-gray-900 dark:text-gray-100">
        {{ $producto->nombre }}
    </td>

    {{-- SUBCATEGORÍA (font-normal) --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm font-normal text-gray-700 dark:text-gray-300">
        {{ $producto->subcategoria->nombre ?? 'N/A' }}
    </td>

    {{-- CATEGORÍA PRINCIPAL (font-normal) --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm font-normal text-gray-700 dark:text-gray-300">
        {{ $producto->subcategoria->categoria->nombre ?? 'N/A' }}
    </td>
    
    {{-- COLUMNA: NOMBRE DEL IMPUESTO --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm font-normal text-gray-700 dark:text-gray-300">
        {{ $producto->impuesto->nombre ?? 'N/A' }}
    </td>

    {{-- COLUMNA: TASA DEL IMPUESTO --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-center text-gray-800 dark:text-gray-200">
        {{ number_format($producto->impuesto->porcentaje ?? 0, 2) }}%
    </td>
    
    {{-- ACCIONES (Iconos elegantes) --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
        <div class="flex items-center justify-center space-x-2">
            <a href="{{ route('productos.show', $producto) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200 transition-colors" title="Ver Detalles">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('productos.edit', $producto) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 transition-colors" title="Editar">
                <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('productos.destroy', $producto) }}" method="POST" class="inline delete-form" onsubmit="return false;">
                @csrf
                @method('DELETE')
                <button type="button" 
                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200 delete-btn" 
                        data-id="{{ $producto->id }}"
                        data-name="{{ $producto->nombre }}" >
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr>
    {{-- Colspan ajustado a 9 (antes 10) --}}
    <td colspan="9" class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300 text-center">
        No se encontraron productos.
    </td>
</tr>
@endforelse