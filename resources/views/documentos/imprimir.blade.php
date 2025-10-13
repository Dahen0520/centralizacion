<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ strtoupper($tipo) }} #{{ $documento->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Estilos específicos para impresión */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            /* Asegura que el contenido se imprima sin fondo ni sombras innecesarias */
            .document-container {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
            }
        }
        /* Color de borde según tipo de documento */
        .border-invoice { border-color: #4f46e5; } /* Indigo */
        .border-quote { border-color: #3b82f6; }   /* Blue */
        .border-ticket { border-color: #10b981; }  /* Emerald */
    </style>
</head>
<body class="bg-gray-100 p-8">

    <div class="max-w-3xl mx-auto bg-white shadow-xl document-container p-8 rounded-lg border-t-8
        @if($tipo === 'INVOICE') border-invoice
        @elseif($tipo === 'QUOTE') border-quote
        @else border-ticket
        @endif">
        
        <div class="text-center border-b pb-4 mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-1">{{ $documento->tienda->nombre ?? 'Tienda Central' }}</h1>
            <p class="text-sm text-gray-500">
                @if($tipo === 'INVOICE')
                    Documento de Factura (Venta)
                @elseif($tipo === 'QUOTE')
                    Documento de Cotización
                @else
                    Ticket de Venta Rápida
                @endif
            </p>
        </div>

        <div class="flex justify-between mb-6 text-sm border-b pb-4">
            <div>
                <p class="font-semibold text-gray-700">Cliente:</p>
                <p class="font-medium text-gray-900">{{ $documento->cliente->nombre ?? 'Cliente Genérico / Público General' }}</p>
                @if($documento->cliente && $documento->cliente->identificacion)
                    <p class="text-xs text-gray-500">RTN: {{ $documento->cliente->identificacion }}</p>
                @endif
                @if($documento->cliente && $documento->cliente->telefono)
                    <p class="text-xs text-gray-500">Tel: {{ $documento->cliente->telefono }}</p>
                @endif
            </div>
            <div class="text-right">
                <p class="font-semibold text-lg text-gray-800">{{ strtoupper($tipo) }} #{{ $documento->id }}</p>
                <p class="text-gray-600">Fecha: {{ $documento->fecha_venta->format('d/m/Y h:i A') }}</p>
                <p class="text-gray-600">Vendedor: {{ $documento->usuario->name ?? 'Sistema' }}</p>
                @if($tipo === 'QUOTE')
                     <p class="mt-2 font-bold text-blue-600">Estado: PENDIENTE</p>
                @endif
            </div>
        </div>

        <table class="min-w-full divide-y divide-gray-200 mb-6">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto (Código)</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Cant.</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Precio U.</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($documento->detalles as $detalle)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $detalle->inventario->marca->producto->nombre ?? 'N/A' }}
                        <span class="text-xs text-gray-400">({{ $detalle->inventario->marca->codigo_marca ?? 'N/A' }})</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ $detalle->cantidad }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">L {{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">L {{ number_format($detalle->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="flex justify-end">
            <div class="w-full max-w-xs space-y-2">
                <div class="flex justify-between font-medium text-sm text-gray-700">
                    <span>Subtotal Neto:</span>
                    @php
                        $subtotalNeto = $documento->total_venta + ($documento->descuento ?? 0);
                    @endphp
                    <span>L {{ number_format($subtotalNeto, 2) }}</span>
                </div>

                @if(($documento->descuento ?? 0) > 0)
                <div class="flex justify-between font-medium text-sm text-red-600 border-t pt-2">
                    <span>Descuento Aplicado:</span>
                    <span>- L {{ number_format($documento->descuento, 2) }}</span>
                </div>
                @endif

                <div class="flex justify-between font-bold text-xl border-t pt-3 
                    @if($tipo === 'QUOTE') text-blue-600 @else text-emerald-600 @endif">
                    <span>TOTAL A PAGAR:</span>
                    <span>L {{ number_format($documento->total_venta, 2) }}</span>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-8 pt-4 border-t border-dashed">
            @if($tipo === 'QUOTE')
                <p class="text-xs text-gray-500 italic">Esta cotización es válida por 30 días y no afecta el stock de inventario.</p>
            @else
                <p class="text-xs text-gray-500 italic">¡Gracias por su compra!</p>
            @endif
        </div>

        <div class="text-center mt-8 no-print">
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center shadow-md">
                <i class="fas fa-print mr-2"></i> Imprimir
            </button>
            <a href="{{ route('ventas.pos') }}" class="ml-4 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg inline-flex items-center shadow-md">
                <i class="fas fa-arrow-left mr-2"></i> Volver al POS
            </a>
        </div>
    </div>

</body>
</html>
