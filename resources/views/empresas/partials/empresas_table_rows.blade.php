@foreach ($empresas as $empresa)
    <tr>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $empresa->nombre_negocio }}</td>
        <td class="px-6 py-4 whitespace-now-1wrap text-sm text-gray-900 dark:text-gray-100">{{ $empresa->afiliado->nombre }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $empresa->rubro->nombre }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm">
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                @if($empresa->estado == 'aprobado') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200
                @elseif($empresa->estado == 'rechazado') bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200
                @else bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200
                @endif">
                {{ $empresa->estado }}
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            <a href="{{ route('empresas.show', $empresa->id) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-600 transition-colors">Ver</a>
            <a href="{{ route('empresas.edit', $empresa->id) }}" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-600 ml-4 transition-colors">Editar</a>
            <form action="{{ route('empresas.destroy', $empresa->id) }}" method="POST" class="inline ml-4">
                @csrf
                @method('DELETE')
                <button type="submit" data-id="{{ $empresa->id }}" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600 transition-colors delete-btn">Eliminar</button>
            </form>
        </td>
    </tr>
@endforeach