@forelse ($afiliados as $afiliado)
<tr>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $afiliado->id }}</td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $afiliado->dni }}</td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $afiliado->nombre }}</td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $afiliado->genero ?? '—' }}</td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $afiliado->fecha_nacimiento ? \Carbon\Carbon::parse($afiliado->fecha_nacimiento)->format('d/m/Y') : '—' }}</td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $afiliado->telefono ?? '—' }}</td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $afiliado->email ?? '—' }}</td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $afiliado->municipio->nombre ?? '—' }}</td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $afiliado->barrio ?? '—' }}</td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $afiliado->rtn ?? '—' }}</td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $afiliado->numero_cuenta ?? '—' }}</td>
    <td class="px-6 py-4 whitespace-nowrap text-sm">
        <div class="flex items-center space-x-2">
            <a href="{{ route('afiliados.show', $afiliado->id) }}" class="text-blue-600 hover:text-blue-900 transition duration-150 ease-in-out" title="Ver">
                <i class="fas fa-eye"></i>
            </a>
            
            <a href="{{ route('afiliados.edit', $afiliado->id) }}" class="text-indigo-600 hover:text-indigo-900 transition duration-150 ease-in-out" title="Editar">
                <i class="fas fa-edit"></i>
            </a>

            <form action="{{ route('afiliados.destroy', $afiliado->id) }}" method="POST" class="inline-block">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-900 transition duration-150 ease-in-out delete-btn" title="Eliminar">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="12" class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300 text-center">
        No hay afiliados para mostrar.
    </td>
</tr>
@endforelse