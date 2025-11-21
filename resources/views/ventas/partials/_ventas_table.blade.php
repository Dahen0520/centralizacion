{{-- resources/views/ventas/partials/_ventas_table.blade.php --}}
@php
    // Asegurarse de que totalVentasSum siempre sea un número
    $totalVentasSum = $totalVentasSum ?? 0;
@endphp

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    {{-- Columna para el botón de expansión --}}
                    <th class="px-3 py-3 text-center w-8"></th> 
                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-16">
                        ID
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                        <i class="fas fa-calendar-alt mr-1"></i> Fecha/Hora
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                        <i class="fas fa-user mr-1"></i> Cliente 
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                        <i class="fas fa-store mr-1"></i> Tienda
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                        <i class="fas fa-user-tag mr-1"></i> Registrado Por
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                        <i class="fas fa-money-bill-wave mr-1"></i> Total (L)
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-32">
                        <i class="fas fa-cogs mr-1"></i> Acciones
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($ventas as $venta)
                @php
                    $statusColor = [
                        'COMPLETADA' => 'bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-300',
                        'PENDIENTE' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-300',
                        'ANULADA' => 'bg-red-100 text-red-800 dark:bg-red-800/30 dark:text-red-300',
                    ][$venta->estado] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-800/30 dark:text-gray-300';
                @endphp
                
                {{-- Fila Principal de Venta --}}
                {{-- 🔑 Cambiado el evento onclick para llamar a toggleDetalle --}}
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 cursor-pointer"
                    onclick="toggleDetalle('{{ $venta->id }}')">
                    
                    {{-- 🔑 Botón Expandir (Comienza como UP/Down) --}}
                    <td class="px-3 py-4 text-center">
                        <i class="fas fa-chevron-down text-gray-500" id="toggle-icon-{{ $venta->id }}"></i>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-gray-900 dark:text-white">
                        #{{ $venta->id }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                        {{ $venta->fecha_venta->format('d/M/Y H:i A') }}
                    </td>
                    {{-- DATOS DEL CLIENTE --}}
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-gray-100">
                        @if ($venta->cliente)
                            {{ $venta->cliente->nombre }}
                            <span class="text-xs text-gray-500 dark:text-gray-400 block">{{ $venta->cliente->identificacion ?? 'N/A' }}</span>
                        @else
                            <span class="text-xs text-gray-500 dark:text-gray-400 italic">Cliente Genérico</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                        {{ $venta->tienda->nombre ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        {{ $venta->usuario->name ?? 'Sistema' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-lg font-extrabold text-indigo-600 dark:text-indigo-400">
                        L {{ number_format($venta->total_final, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                        
                        {{-- Botón para ver el DOCUMENTO (Factura/Cotización/Ticket) --}}
                        @if ($venta->tipo_documento)
                            <a href="{{ route('ventas.print', ['id' => $venta->id, 'type' => strtolower($venta->tipo_documento)]) }}" 
                                target="_blank"
                                class="
                                @if ($venta->tipo_documento == 'INVOICE') text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-200
                                @elseif ($venta->tipo_documento == 'QUOTE') text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200
                                @else text-emerald-600 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-200 @endif
                                transition-colors" 
                                title="Ver Documento {{ $venta->tipo_documento }}">
                                <i class="fas fa-file-invoice"></i>
                            </a>
                        @endif
                        
                        {{-- Botón para ver detalles de la venta (vista show original) --}}
                        <a href="{{ route('ventas.show', $venta) }}" 
                            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors" 
                            title="Ver Detalles Internos">
                            <i class="fas fa-info-circle"></i>
                        </a>
                    </td>
                </tr>
                
                {{-- Fila Detalle de Productos (Visible por defecto, con ID dinámico) --}}
                <tr id="detalle-row-{{ $venta->id }}" style="display: table-row;">
                    <td colspan="8" class="p-0 border-t border-gray-100 dark:border-gray-700">
                        <div class="bg-gray-50 dark:bg-gray-900 p-4">
                            <h5 class="font-bold text-sm text-gray-700 dark:text-gray-200 mb-2">
                                Productos en Documento #{{ $venta->numero_documento ?? $venta->id }}:
                            </h5>
                            <table class="w-full text-left text-sm">
                                <thead class="text-xs text-gray-600 dark:text-gray-400 border-b dark:border-gray-600">
                                    <tr>
                                        <th class="py-2 pl-4 w-1/2">Producto (Marca)</th>
                                        <th class="py-2 text-right">Cant.</th>
                                        <th class="py-2 text-right">Precio Unitario</th>
                                        <th class="py-2 text-right">ISV</th>
                                        <th class="py-2 pr-4 text-right">Total Línea</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-800 dark:text-gray-100">
                                    @forelse ($venta->detalles as $detalle)
                                    <tr>
                                        <td class="py-2 pl-4 font-semibold">
                                            {{ $detalle->inventario->marca->producto->nombre ?? 'N/A' }}
                                            <span class="text-xs text-gray-500 dark:text-gray-400 block">
                                                Cód: {{ $detalle->inventario->marca->codigo_marca ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="py-2 text-right text-base font-bold">{{ $detalle->cantidad }}</td>
                                        <td class="py-2 text-right">L {{ number_format($detalle->precio_unitario, 2) }}</td>
                                        <td class="py-2 text-right text-sm text-blue-600 dark:text-blue-400">L {{ number_format($detalle->isv_monto, 2) }}</td>
                                        <td class="py-2 pr-4 text-right font-bold text-teal-600 dark:text-teal-400">
                                            L {{ number_format($detalle->subtotal_final, 2) }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="py-2 pl-4 text-center text-gray-500 italic">
                                            No hay detalles de productos cargados para esta venta.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="8" class="p-10 text-center text-gray-500 dark:text-gray-400">
                        <i class="fas fa-clipboard-list text-5xl text-gray-300 dark:text-gray-600 mb-3"></i>
                        <p class="font-extrabold text-xl">No se encontraron registros de ventas.</p>
                        <p>Ajuste sus filtros o use el Punto de Venta.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
            
            {{-- SUMATORIA TOTAL DE VENTAS 🔑 FOOTER CON TOTAL --}}
            <tfoot class="bg-gray-100 dark:bg-gray-700 border-t-2 border-emerald-500 dark:border-emerald-600">
                <tr>
                    <td colspan="6" class="px-6 py-3 text-right text-lg font-extrabold text-gray-900 dark:text-white">
                        TOTAL DE VENTAS (FILTRADO):
                    </td>
                    <td class="px-6 py-3 text-center text-lg font-extrabold text-indigo-600 dark:text-indigo-400">
                        L {{ number_format($totalVentasSum, 2) }}
                    </td>
                    <td class="px-6 py-3 text-right"></td>
                </tr>
            </tfoot>
        </table>
    </div>
    
    {{-- Paginación --}}
    <div class="mt-8 pagination">
        {{ $ventas->appends(request()->input())->links() }}
    </div>
</div>

<script>
    // 🔑 FUNCIÓN GLOBAL PARA ALTERNAR DETALLES (Se define en el archivo principal)
    function toggleDetalle(ventaId) {
        const detalleRow = document.getElementById('detalle-row-' + ventaId);
        const icon = document.getElementById('toggle-icon-' + ventaId);

        if (detalleRow.style.display === 'table-row') {
            // Ocultar
            detalleRow.style.display = 'none';
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-right');
        } else {
            // Mostrar
            detalleRow.style.display = 'table-row';
            icon.classList.remove('fa-chevron-right');
            icon.classList.add('fa-chevron-down');
        }
    }
    
    // Al cargar la tabla (via AJAX o load inicial), inicializar los iconos
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.venta-row').forEach(row => {
            const ventaId = row.getAttribute('onclick').match(/'(.*?)'/)[1];
            const icon = document.getElementById('toggle-icon-' + ventaId);
            const detalleRow = document.getElementById('detalle-row-' + ventaId);
            
            // Si la fila de detalle está visible (que lo está por defecto), establecer el icono de cierre
            if (detalleRow && detalleRow.style.display === 'table-row') {
                icon.classList.remove('fa-chevron-right');
                icon.classList.add('fa-chevron-down');
            }
        });
    });
</script>