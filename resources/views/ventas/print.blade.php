<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $type }} #{{ $venta->id }}</title>
    <style>
        /* Aquí va tu CSS para impresión. DOMPDF/Snappy tienen limitaciones con CSS moderno. */
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 10px; }
        .container { width: 90%; margin: auto; }
        .header, .footer { text-align: center; }
        .line-item th, .line-item td { border-bottom: 1px dashed #ccc; padding: 4px 0; }
        .totals table { width: 100%; }
        .totals td { padding: 2px 0; }
    </style>
</head>
<body>
    <div class="container">
        {{-- ENCABEZADO DE LA EMPRESA (Replicando tu imagen) --}}
        <div class="header">
            <h3>{{ $venta->tienda->nombre ?? 'Nombre de la Empresa' }}</h3>
            <p style="margin: 0; line-height: 1.2;">{{ $venta->tienda->direccion ?? 'Dirección de la Tienda' }}</p>
            <p style="margin: 0; line-height: 1.2;">Tel: {{ $venta->tienda->telefono ?? 'N/A' }}</p>
            <p style="margin: 0; line-height: 1.2;">R.T.N: {{ $venta->tienda->rtn ?? 'N/A' }}</p>
            <p style="margin: 0 0 10px 0;">{{ $venta->fecha_venta->format('d/m/y H:i A') }}</p>
            
            @if ($type === 'INVOICE')
                <h4 style="margin: 5px 0;">FACTURA</h4>
                <p style="margin: 0; font-size: 9px;">CAI: {{ $venta->cai }}</p>
                <p style="margin: 0; font-size: 12px; font-weight: bold;">FACTURA: {{ $venta->numero_documento }}</p>
            @else 
                <h4 style="margin: 5px 0;">TICKET DE VENTA</h4>
            @endif
        </div>

        {{-- DATOS DEL CLIENTE --}}
        <div style="margin: 10px 0; border-top: 1px dashed #ccc;">
            <p style="margin: 4px 0;">Cliente: {{ $venta->cliente->nombre ?? 'CONSUMIDOR FINAL' }}</p>
            <p style="margin: 4px 0;">RTN: {{ $venta->cliente->identificacion ?? '0' }}</p>
        </div>

        {{-- DETALLE DE PRODUCTOS --}}
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;" class="line-item">
            <thead>
                <tr>
                    <th style="width: 10%; text-align: left;">Cant</th>
                    <th style="width: 60%; text-align: left;">DESCRIPCION</th>
                    <th style="width: 30%; text-align: right;">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $subtotalNeto = 0;
                    $totalIsv = 0;
                @endphp
                @foreach($venta->detalles as $detalle)
                @php
                    $subtotalNeto += $detalle->subtotal_base;
                    $totalIsv += $detalle->isv_monto;
                @endphp
                <tr>
                    <td style="text-align: left;">{{ $detalle->cantidad }}</td>
                    <td style="text-align: left;">{{ $detalle->inventario->marca->producto->nombre ?? 'Producto Desconocido' }}</td>
                    <td style="text-align: right;">{{ number_format($detalle->subtotal_final, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- TOTALES FISCALES --}}
        <div class="totals" style="border-top: 1px dashed #ccc; padding-top: 5px;">
            <table>
                <tr>
                    <td>SubTotal Base (sin ISV)</td>
                    <td style="text-align: right;">L {{ number_format($venta->detalles->sum('subtotal_base'), 2) }}</td>
                </tr>
                <tr>
                    <td>Descto. y Rebaja</td>
                    <td style="text-align: right;">L {{ number_format($venta->descuento, 2) }}</td>
                </tr>
                <tr>
                    <td>Subt. Exento (0.00%)</td>
                    <td style="text-align: right;">L {{ number_format($venta->detalles->where('isv_tasa', 0)->sum('subtotal_base'), 2) }}</td>
                </tr>
                <tr>
                    <td>Subt. Gravado (15.00%)</td>
                    <td style="text-align: right;">L {{ number_format($venta->detalles->where('isv_tasa', 0.15)->sum('subtotal_base'), 2) }}</td>
                </tr>
                <tr>
                    <td>ISV (15.00%)</td>
                    <td style="text-align: right;">L {{ number_format($venta->detalles->where('isv_tasa', 0.15)->sum('isv_monto'), 2) }}</td>
                </tr>
                {{-- Aquí puedes añadir más filas para otros porcentajes como el 18% --}}
                
                <tr style="font-weight: bold;">
                    <td>Total ISV</td>
                    <td style="text-align: right;">L {{ number_format($venta->total_isv, 2) }}</td>
                </tr>
                <tr style="font-size: 16px; font-weight: bold;">
                    <td>TOTAL NETO</td>
                    <td style="text-align: right;">L {{ number_format($venta->total_final, 2) }}</td>
                </tr>
            </table>
        </div>

        {{-- PIE DE PÁGINA (CAI y Fechas) --}}
        @if ($type === 'INVOICE')
        <div class="footer" style="margin-top: 15px; border-top: 1px dashed #ccc; padding-top: 10px;">
            <p style="margin: 0; font-size: 9px;">Fecha límite de Emisión: {{ \Carbon\Carbon::parse($venta->tienda->rangoCaiActivo()->fecha_limite_emision)->format('d/m/y') }}</p>
            <p style="margin: 0; font-size: 9px;">Rango autorizado del: {{ $venta->tienda->rangoCaiActivo()->rango_inicial }} al: {{ $venta->tienda->rangoCaiActivo()->rango_final }}</p>
            {{-- Necesitarás una relación o un método en Tienda para obtener el RangoCai activo --}}
        </div>
        @endif
        
    </div>
</body>
</html>