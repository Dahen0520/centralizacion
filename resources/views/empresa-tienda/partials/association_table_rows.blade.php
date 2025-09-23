@foreach($asociaciones as $asociacion)
    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
            {{ $asociacion->empresa->nombre_negocio }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
            {{ $asociacion->tienda->nombre }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                @if($asociacion->estado == 'aprobado') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                @elseif($asociacion->estado == 'rechazado') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @endif">
                {{ ucfirst($asociacion->estado) }}
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <div class="flex items-center space-x-2 justify-end">
                <a href="{{ route('asociaciones.show', ['empresa' => $asociacion->empresa_id, 'tienda' => $asociacion->tienda_id]) }}" 
                   class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200 font-medium">
                    Ver
                </a>
                <a href="{{ route('asociaciones.edit', ['empresa' => $asociacion->empresa_id, 'tienda' => $asociacion->tienda_id]) }}" 
                   class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 font-medium">
                    Editar
                </a>
                <form action="{{ route('asociaciones.destroy', ['empresa' => $asociacion->empresa_id, 'tienda' => $asociacion->tienda_id]) }}" 
                      method="POST" class="inline delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="button" 
                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200 delete-btn font-medium" 
                            data-id="{{ $asociacion->empresa_id }}">
                        Eliminar
                    </button>
                    <input type="hidden" name="empresa_id" value="{{ $asociacion->empresa_id }}">
                    <input type="hidden" name="tienda_id" value="{{ $asociacion->tienda_id }}">
                </form>
            </div>
        </td>
    </tr>
@endforeach

@if($asociaciones->isEmpty())
    <tr>
        <td colspan="4" class="px-6 py-12 whitespace-nowrap text-center">
            <div class="flex flex-col items-center">
                <i class="fas fa-search text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No hay vinculaciones</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">No se encontraron asociaciones que coincidan con los criterios de búsqueda.</p>
            </div>
        </td>
    </tr>
@endif