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
    
    {{-- NUEVA COLUMNA: PERMITE FACTURACIÓN --}}
    <td class="px-4 py-4 whitespace-nowrap text-center">
        @if ($producto->permite_facturacion)
            <i class="fas fa-check-circle text-lg text-green-500" title="Permite Facturación"></i>
        @else
            <i class="fas fa-times-circle text-lg text-red-500" title="No Permite Facturación"></i>
        @endif
    </td>

    {{-- ESTADO (Cápsula Estilizada) --}}
    <td class="px-6 py-4 whitespace-nowrap text-center">
        @php
            $colorClass = 'bg-gray-100 text-gray-800';
            if ($producto->estado == 'pendiente') {
                $colorClass = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-300';
            } elseif ($producto->estado == 'aprobado') {
                $colorClass = 'bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-300';
            } elseif ($producto->estado == 'rechazado') {
                $colorClass = 'bg-red-100 text-red-800 dark:bg-red-800/30 dark:text-red-300';
            }
        @endphp
        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full capitalize {{ $colorClass }}">
            {{ $producto->estado }}
        </span>
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
                        data-name="{{ $producto->nombre }}" > {{-- Añadido data-name para SweetAlert2 --}}
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr>
    {{-- El colspan es 10 (6 columnas originales + 2 Impuesto + 1 Facturación + 1 Estado + 1 Acciones) --}}
    <td colspan="10" class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300 text-center">
        No se encontraron productos.
    </td>
</tr>
@endforelse
