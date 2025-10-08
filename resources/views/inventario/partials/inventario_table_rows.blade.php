@php
    // Asegurar que $startIndex esté definida
    $startIndex = $startIndex ?? ($inventarios->currentPage() - 1) * $inventarios->perPage() + 1;
@endphp

@forelse ($inventarios as $index => $inventario)
<tr id="inventario-row-{{ $inventario->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 ease-in-out">
    
    {{-- COLUMNA 1: NUMERACIÓN (#) --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300 text-center">
        {{ $startIndex + $index }}
    </td>

    {{-- COLUMNA 2: MARCA / PRODUCTO --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm font-normal text-gray-900 dark:text-gray-100">
        <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $inventario->marca->producto->nombre ?? 'N/A' }}</span>
        <span class="text-xs text-gray-500 dark:text-gray-400 block">{{ $inventario->marca->codigo_marca }}</span>
    </td>

    {{-- COLUMNA 3: EMPRESA ASOCIADA --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-700 dark:text-gray-300">
        {{ $inventario->marca->empresa->nombre_negocio ?? 'Sin Empresa' }}
    </td>

    {{-- COLUMNA 4: TIENDA --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm font-normal text-gray-700 dark:text-gray-300">
        <span class="font-semibold">{{ $inventario->tienda->nombre ?? 'N/A' }}</span>
    </td>

    {{-- COLUMNA 5: PRECIO (Lempiras) --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-center">
        <span class="text-green-600 dark:text-green-400">L {{ number_format($inventario->precio, 2) }}</span>
    </td>

    {{-- COLUMNA 6: STOCK --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-center">
        @php
            $stockClass = $inventario->stock > 5 ? 'text-green-600 dark:text-green-400' : ($inventario->stock > 0 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400');
        @endphp
        <span class="{{ $stockClass }}">{{ number_format($inventario->stock, 0) }}</span>
    </td>

    {{-- COLUMNA 7: ACCIONES (Iconos elegantes) --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
        <div class="flex items-center justify-center space-x-2">
            <a href="{{ route('inventarios.show', $inventario) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200 transition-colors" title="Ver Detalles">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('inventarios.edit', $inventario) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 transition-colors" title="Editar Precio/Stock">
                <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('inventarios.destroy', $inventario) }}" method="POST" class="inline delete-form" onsubmit="return false;">
                @csrf
                <button type="button" 
                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200 delete-btn" 
                        data-id="{{ $inventario->id }}"
                        data-name="{{ $inventario->marca->producto->nombre ?? 'Registro' }}">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr>
    {{-- COLSPAN CORREGIDO: Debe ser 7 --}}
    <td colspan="7" class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300 text-center">
        No se encontraron registros de inventario.
    </td>
</tr>
@endforelse