@foreach ($rubros as $rubro)
    <tr id="rubro-row-{{ $rubro->id }}">
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
            {{ $rubro->id }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
            {{ $rubro->nombre }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            <a href="{{ route('rubros.show', $rubro) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200 mr-2">Ver</a>
            <a href="{{ route('rubros.edit', $rubro) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 mr-2">Editar</a>
            <form action="{{ route('rubros.destroy', $rubro) }}" method="POST" class="inline delete-form">
                @csrf
                @method('DELETE')
                <button type="button" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200 delete-btn" data-id="{{ $rubro->id }}">
                    Eliminar
                </button>
            </form>
        </td>
    </tr>
@endforeach