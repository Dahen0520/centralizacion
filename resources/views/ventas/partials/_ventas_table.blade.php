{{-- CONTENEDOR DE LA TABLA --}}
<div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-xl">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-100 dark:bg-gray-700">
            <tr>
                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-16">
                    ID
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                    <i class="fas fa-calendar-alt mr-1"></i> Fecha/Hora
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                    <i class="fas fa-user mr-1"></i> Cliente </th>
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
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
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
                    L {{ number_format($venta->total_venta, 2) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                    
                    {{-- Botón para ver el DOCUMENTO (Factura/Cotización/Ticket) --}}
                    @if ($venta->tipo_documento)
                        <a href="{{ route('ventas.print-document', ['id' => $venta->id, 'type' => strtolower($venta->tipo_documento)]) }}" 
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
                    
                    {{-- Botón para anular la venta --}}
                    <form action="{{ route('ventas.destroy', $venta) }}" method="POST" class="inline delete-form" onsubmit="return false;">
                        @csrf
                        @method('DELETE')
                        <button type="button" 
                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200 delete-btn" 
                                data-name="Venta #{{ $venta->id }}"
                                title="Anular Venta y Devolver Stock">
                            <i class="fas fa-ban"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="p-10 text-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-clipboard-list text-5xl text-gray-300 dark:text-gray-600 mb-3"></i>
                    <p class="font-extrabold text-xl">No se encontraron registros de ventas.</p>
                    <p>Ajuste sus filtros o use el Punto de Venta.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Paginación --}}
<div class="mt-8 pagination">
    {{ $ventas->appends(request()->input())->links() }}
</div>