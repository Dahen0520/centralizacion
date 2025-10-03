@php
    $startIndex = $start_index ?? 1;
@endphp

@forelse ($categorias as $index => $categoria)
<tr id="categoria-row-{{ $categoria->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 ease-in-out">
    
    {{-- COLUMNA DE NUMERACIÓN (#) --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300 text-center">
        {{ $startIndex + $index }}
    </td>

    {{-- ID (Estilo neutro y font-normal) --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
        <span class="font-normal text-gray-600 dark:text-gray-400">{{ $categoria->id }}</span>
    </td>
    
    {{-- NOMBRE (font-normal) --}}
    <td class="px-6 py-4 whitespace-nowrap text-base font-normal text-gray-900 dark:text-gray-100">
        {{ $categoria->nombre }}
    </td>
    
    {{-- ACCIONES (font-medium mantenido solo en el contenedor) --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
        <div class="flex items-center justify-center space-x-2">
            <a href="{{ route('categorias.show', $categoria) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200 transition-colors" title="Ver Detalles">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('categorias.edit', $categoria) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 transition-colors" title="Editar">
                <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('categorias.destroy', $categoria) }}" method="POST" class="inline delete-form" onsubmit="return false;">
                @csrf
                @method('DELETE')
                <button type="button" 
                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200 delete-btn" 
                        data-id="{{ $categoria->id }}"
                        data-name="{{ $categoria->nombre }}" >
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="4" class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300 text-center">
        No hay categorías para mostrar.
    </td>
</tr>
@endforelse