{{-- resources/views/subcategorias/partials/subcategorias_table_rows.blade.php --}}
@foreach ($subcategorias as $subcategoria)
    <tr id="subcategoria-row-{{ $subcategoria->id }}">
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
            {{ $subcategoria->id }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
            {{ $subcategoria->nombre }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
            {{ $subcategoria->categoria->nombre }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            <a href="{{ route('subcategorias.show', $subcategoria) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200 mr-2">Ver</a>
            <a href="{{ route('subcategorias.edit', $subcategoria) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 mr-2">Editar</a>
            <form action="{{ route('subcategorias.destroy', $subcategoria) }}" method="POST" class="inline delete-form">
                @csrf
                @method('DELETE')
                <button type="button" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200 delete-btn" data-id="{{ $subcategoria->id }}">
                    Eliminar
                </button>
            </form>
        </td>
    </tr>
@endforeach