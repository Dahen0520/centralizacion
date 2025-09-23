@foreach ($productos as $producto)
    <tr id="producto-row-{{ $producto->id }}">
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
            {{ $producto->id }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
            {{ $producto->nombre }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
            {{ $producto->subcategoria->nombre }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
            {{ $producto->subcategoria->categoria->nombre }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm">
            @php
                $color = '';
                if ($producto->estado == 'pendiente') {
                    $color = 'bg-yellow-100 text-yellow-800';
                } elseif ($producto->estado == 'aprobado') {
                    $color = 'bg-green-100 text-green-800';
                } elseif ($producto->estado == 'rechazado') {
                    $color = 'bg-red-100 text-red-800';
                }
            @endphp
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                {{ $producto->estado }}
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            <a href="{{ route('productos.show', $producto) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200 mr-2">Ver</a>
            <a href="{{ route('productos.edit', $producto) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 mr-2">Editar</a>
            <form action="{{ route('productos.destroy', $producto) }}" method="POST" class="inline delete-form">
                @csrf
                @method('DELETE')
                <button type="button" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200 delete-btn" data-id="{{ $producto->id }}">
                    Eliminar
                </button>
            </form>
        </td>
    </tr>
@endforeach
