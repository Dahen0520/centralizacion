@forelse ($rangos as $rango)
    @php
        $ceroPad = 8;
        $rangoInicialFull = $rango->prefijo_sar . str_pad($rango->rango_inicial, $ceroPad, '0', STR_PAD_LEFT);
        $rangoFinalFull = $rango->prefijo_sar . str_pad($rango->rango_final, $ceroPad, '0', STR_PAD_LEFT);
        $numeroActualFull = $rango->prefijo_sar . str_pad($rango->numero_actual, $ceroPad, '0', STR_PAD_LEFT);

        $fechaLimite = \Carbon\Carbon::parse($rango->fecha_limite_emision);
        $isExpired = $fechaLimite->isPast();
        // CUIDADO: La verificación 'isNear' debe ser con la fecha de expiración, no con la fecha actual
        $isNear = $fechaLimite->diffInDays(now()) < 30 && !$isExpired; 
        $dateClass = $isExpired ? 'text-red-600 font-bold' : ($isNear ? 'text-yellow-600 font-semibold' : 'text-gray-700 dark:text-gray-300');
        
        // ** NUEVA LÓGICA: Determinar si el rango ya fue utilizado **
        $isUsed = $rango->numero_actual >= $rango->rango_inicial; 
    @endphp
    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
            {{ $rango->tienda->nombre ?? 'N/A' }}
        </td>
        <td class="px-6 py-4 text-xs text-gray-700 dark:text-gray-300">
            <p class="font-semibold text-blue-600 dark:text-blue-400 mb-1">CAI: {{ $rango->cai }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400" title="Prefijo: {{ $rango->prefijo_sar }}">
                Del: <strong>{{ $rangoInicialFull }}</strong> al: <strong>{{ $rangoFinalFull }}</strong>
            </p>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-teal-600 dark:text-teal-400">
            <span title="Secuencia: {{ $rango->numero_actual }}">
                {{ $numeroActualFull }}
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
            <span class="{{ $dateClass }}">
                {{ $fechaLimite->format('d/M/Y') }}
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-center">
            @if ($isExpired)
                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800/30 dark:text-red-300">
                    EXPIRADO
                </span>
            @elseif ($rango->esta_activo)
                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-300">
                    ACTIVO
                </span>
            @else
                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                    INACTIVO
                </span>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400 italic">
            No hay rangos CAI registrados que coincidan con el filtro seleccionado.
        </td>
    </tr>
@endforelse

{{-- 🔑 ESTRUCTURA DE PAGINACIÓN SEPARADA (IMPORTANTE PARA AJAX) --}}
<div id="pagination-links" style="display:none;">
    {{ $rangos->links() }}
</div>