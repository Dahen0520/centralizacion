@php
    // Asegurar que $start_index esté definida (pasada desde el controlador)
    $startIndex = $start_index ?? 1;
@endphp

@forelse ($marcas as $index => $marca)
<tr id="marca-row-{{ $marca->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 ease-in-out">
    
    {{-- COLUMNA DE NUMERACIÓN (#) --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300 text-center">
        {{ $startIndex + $index }}
    </td>

    {{-- ID --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
        <span class="font-normal text-gray-600 dark:text-gray-400">{{ $marca->id }}</span>
    </td>
    
    {{-- Código de Marca --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
        {{ $marca->codigo_marca }}
    </td>
    
    {{-- Producto --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm font-normal text-gray-700 dark:text-gray-300">
        {{ $marca->producto->nombre }}
    </td>

    {{-- Empresa --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm font-normal text-gray-700 dark:text-gray-300">
        {{ $marca->empresa->nombre_negocio }}
    </td>
    
    {{-- ESTADO (Cápsula Estilizada) --}}
    <td class="px-6 py-4 whitespace-nowrap text-center">
        @php
            $colorClass = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'; // Default
            switch ($marca->estado) {
                case 'pendiente':
                    $colorClass = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-300';
                    break;
                case 'aprobado':
                    $colorClass = 'bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-300';
                    break;
                case 'rechazado':
                    $colorClass = 'bg-red-100 text-red-800 dark:bg-red-800/30 dark:text-red-300';
                    break;
            }
        @endphp
        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full capitalize {{ $colorClass }}">
            {{ ucfirst($marca->estado) }}
        </span>
    </td>
    
    {{-- ACCIONES (Iconos elegantes) --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
        <div class="flex items-center justify-center space-x-2">
            <a href="{{ route('marcas.show', $marca) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200 transition-colors" title="Ver Detalles">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('marcas.edit', $marca) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 transition-colors" title="Editar">
                <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('marcas.destroy', $marca) }}" method="POST" class="inline delete-form" onsubmit="return false;">
                @csrf
                @method('DELETE')
                <button type="button" 
                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200 delete-btn" 
                        data-id="{{ $marca->id }}"
                        data-name="{{ $marca->codigo_marca }}"> 
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="7" class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300 text-center">
        No se encontraron marcas.
    </td>
</tr>
@endforelse