@php
    // Asegurar que $impuestos esté definida para evitar errores al inicializar la tabla
    $impuestos = $impuestos ?? collect();
@endphp

@forelse ($impuestos as $impuesto)
<tr id="impuesto-row-{{ $impuesto->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 ease-in-out">
    
    {{-- COLUMNA DE ID --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300 text-center">
        {{ $impuesto->id }}
    </td>

    {{-- NOMBRE --}}
    <td class="px-6 py-4 whitespace-nowrap text-base font-semibold text-gray-900 dark:text-gray-100">
        {{ $impuesto->nombre }}
    </td>
    
    {{-- PORCENTAJE --}}
    <td class="px-6 py-4 whitespace-nowrap text-base font-extrabold text-center">
        <span class="inline-flex items-center px-3 py-1 text-sm font-bold leading-none text-blue-800 bg-blue-100 rounded-full dark:bg-blue-800 dark:text-blue-100">
            {{ number_format($impuesto->porcentaje, 2) }}%
        </span>
    </td>

    {{-- ACCIONES --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
        <div class="flex items-center justify-center space-x-2">
            <a href="{{ route('impuestos.edit', $impuesto) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 transition-colors" title="Editar">
                <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('impuestos.destroy', $impuesto) }}" method="POST" class="inline delete-form">
                @csrf
                @method('DELETE')
                <button type="button" 
                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200 delete-btn" 
                        data-id="{{ $impuesto->id }}"
                        data-name="{{ $impuesto->nombre }}" > 
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="4" class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300 text-center">
        No se encontraron impuestos.
    </td>
</tr>
@endforelse
