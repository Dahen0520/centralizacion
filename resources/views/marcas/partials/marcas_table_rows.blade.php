@foreach ($marcas as $marca)
    <tr id="marca-row-{{ $marca->id }}">
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
            {{ $marca->id }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
            {{ $marca->codigo_marca }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
            {{ $marca->producto->nombre }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
            {{ $marca->empresa->nombre_negocio }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            @php
                $colorClass = '';
                switch ($marca->estado) {
                    case 'pendiente':
                        $colorClass = 'bg-yellow-100 text-yellow-800';
                        break;
                    case 'aprobado':
                        $colorClass = 'bg-green-100 text-green-800';
                        break;
                    case 'rechazado':
                        $colorClass = 'bg-red-100 text-red-800';
                        break;
                }
            @endphp
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colorClass }} dark:bg-gray-700 dark:text-white">
                {{ ucfirst($marca->estado) }}
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            <a href="{{ route('marcas.show', $marca) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200 mr-2">Ver</a>
            <a href="{{ route('marcas.edit', $marca) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 mr-2">Editar</a>
            <form action="{{ route('marcas.destroy', $marca) }}" method="POST" class="inline delete-form">
                @csrf
                @method('DELETE')
                <button type="button" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200 delete-btn" data-id="{{ $marca->id }}">
                    Eliminar
                </button>
            </form>
        </td>
    </tr>
@endforeach