@php
    // Asumiendo que el controlador pasa $start_index para la paginación
    $startIndex = $start_index ?? 1;
@endphp

@forelse ($subcategorias as $index => $subcategoria)
<tr id="subcategoria-row-{{ $subcategoria->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 ease-in-out">
    
    {{-- COLUMNA DE NUMERACIÓN (#) --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300 text-center">
        {{ $startIndex + $index }}
    </td>

    {{-- ID (font-normal y color sutil) --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
        <span class="font-normal text-gray-600 dark:text-gray-400">{{ $subcategoria->id }}</span>
    </td>
    
    {{-- NOMBRE (font-normal) --}}
    <td class="px-6 py-4 whitespace-nowrap text-base font-normal text-gray-900 dark:text-gray-100">
        {{ $subcategoria->nombre }}
    </td>
    
    {{-- CATEGORÍA PRINCIPAL (font-normal) --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm font-normal text-gray-700 dark:text-gray-300">
        {{ $subcategoria->categoria->nombre }}
    </td>
    
    {{-- ACCIONES --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
        <div class="flex items-center justify-center space-x-2">
            <a href="{{ route('subcategorias.show', $subcategoria) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200 transition-colors" title="Ver Detalles">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('subcategorias.edit', $subcategoria) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 transition-colors" title="Editar">
                <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('subcategorias.destroy', $subcategoria) }}" method="POST" class="inline delete-form" onsubmit="return false;">
                @csrf
                @method('DELETE')
                <button type="button" 
                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200 delete-btn" 
                        data-id="{{ $subcategoria->id }}"
                        data-name="{{ $subcategoria->nombre }}" > {{-- Añadido data-name para SweetAlert2 --}}
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr>
    {{-- Colspan ajustado para las 5 columnas: #, ID, Nombre, Categoría, Acciones --}}
    <td colspan="5" class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300 text-center">
        No hay subcategorías para mostrar.
    </td>
</tr>
@endforelse