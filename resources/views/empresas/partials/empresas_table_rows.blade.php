@php
    // Asegurar que $start_index esté definida, si no, usar 1 (para cuando se carga la vista inicial)
    $startIndex = $start_index ?? 1;
@endphp

@forelse ($empresas as $index => $empresa)
    <tr>
        {{-- COLUMNA DE NÚMERO DE REGISTRO --}}
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100 text-center">
            {{ $startIndex + $index }}
        </td>
        
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $empresa->nombre_negocio }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $empresa->afiliado->nombre }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $empresa->rubro->nombre }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize
                @if($empresa->estado == 'aprobado') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200
                @elseif($empresa->estado == 'rechazado') bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200
                @else bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200
                @endif">
                {{ $empresa->estado }}
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
            <a href="{{ route('empresas.show', $empresa->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600 transition-colors mr-2" title="Ver"><i class="fas fa-eye"></i></a>
            <a href="{{ route('empresas.edit', $empresa->id) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-600 transition-colors mr-2" title="Editar"><i class="fas fa-edit"></i></a>
            <form action="{{ route('empresas.destroy', $empresa->id) }}" method="POST" class="inline" onsubmit="return false;">
                @csrf
                @method('DELETE')
                <button type="button" data-name="{{ $empresa->nombre_negocio }}" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600 transition-colors delete-btn" title="Eliminar">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
            No se encontraron empresas.
        </td>
    </tr>
@endforelse