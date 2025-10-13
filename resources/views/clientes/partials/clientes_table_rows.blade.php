@forelse ($clientes as $index => $cliente)
<tr id="cliente-row-{{ $cliente->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 ease-in-out">
    
    {{-- COLUMNA DE NUMERACIÓN (#) --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300 text-center">
        {{ $clientes->firstItem() + $index }}
    </td>

    {{-- NOMBRE --}}
    <td class="px-6 py-4 whitespace-nowrap text-base font-normal text-gray-900 dark:text-gray-100">
        {{ $cliente->nombre }}
    </td>
    
    {{-- IDENTIFICACIÓN / RTN --}}
    @php
        $rtn_display = $cliente->identificacion 
            ? preg_replace('/^(\d{4})(\d{4})(\d{6})$/', '$1-$2-$3', $cliente->identificacion) 
            : 'N/A';
    @endphp
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
        {{ $rtn_display }}
    </td>

    {{-- EMAIL --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
        {{ $cliente->email ?? 'N/A' }}
    </td>
    
    {{-- TELÉFONO --}}
    @php
        $phone_display = $cliente->telefono 
            ? preg_replace('/^(\d{4})(\d{4})$/', '$1-$2', $cliente->telefono) 
            : 'N/A';
    @endphp
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
        {{ $phone_display }}
    </td>

    {{-- ACCIONES --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
        <div class="flex items-center justify-center space-x-2">
            <a href="{{ route('clientes.show', $cliente) }}" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-200 transition-colors" title="Ver Detalles">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('clientes.edit', $cliente) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 transition-colors" title="Editar">
                <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" class="inline delete-form" onsubmit="return false;">
                @csrf
                @method('DELETE')
                <button type="button" 
                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200 delete-btn" 
                        data-id="{{ $cliente->id }}"
                        data-name="{{ $cliente->nombre }}" >
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="p-10 text-center text-gray-500 dark:text-gray-400">
        <i class="fas fa-user-slash text-5xl text-indigo-400 dark:text-indigo-600 mb-3"></i>
        <p class="font-extrabold text-xl text-gray-900 dark:text-white">No se encontraron clientes.</p>
        <p class="text-gray-500 dark:text-gray-400 mt-2">Intenta ajustar tu búsqueda o crea un nuevo cliente.</p>
    </td>
</tr>
@endforelse