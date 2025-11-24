<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ ($type === 'INVOICE' || $type === 'TICKET') ? 'FACTURA' : strtoupper($type) }} #{{ $venta->numero_documento ?? $venta->id }}</title>
    <style>
        body { 
            font-family: 'Helvetica', Arial, sans-serif; 
            font-size: 10px; 
            margin: 0;
            padding: 0;
        }
        .container { 
            width: 220px; 
            margin: 0 auto; 
            padding: 5px; 
        }
        .header, .footer { text-align: center; }
        .line-item th, .line-item td { 
            border-bottom: 1px dashed #ccc; 
            padding: 4px 0; 
            word-break: break-word; 
        }
        .totals table { width: 100%; }
        .totals td { padding: 2px 0; }
    </style>
</head>
<body>
    <div class="container">
        @php
            $isFiscalDocument = ($venta->tipo_documento === 'INVOICE' || $venta->tipo_documento === 'TICKET');
            
            if ($isFiscalDocument) {
                $documentTitle = 'FACTURA';
            } elseif ($venta->tipo_documento === 'QUOTE') {
                $documentTitle = 'COTIZACIÓN'; 
            } else {
                $documentTitle = strtoupper($venta->tipo_documento);
            }
        @endphp

        <div class="header">
            <h3>{{ $venta->tienda->nombre ?? 'Nombre de la Empresa' }}</h3>
            <p style="margin: 0; line-height: 1.2;">{{ $venta->tienda->direccion ?? 'Dirección de la Tienda' }}</p>
            <p style="margin: 0; line-height: 1.2;">Tel: {{ $venta->tienda->telefono ?? 'N/A' }}</p>
            <p style="margin: 0; line-height: 1.2;">R.T.N: {{ $venta->tienda->rtn ?? 'N/A' }}</p>
            <p style="margin: 0 0 10px 0;">{{ $venta->fecha_venta->format('d/m/y H:i A') }}</p>
            
            <h4 class="document-title">{{ $documentTitle }}</h4>
            <p style="margin: 0; font-size: 12px; font-weight: bold;">
                Nº: {{ $venta->numero_documento ?? $venta->id }}
            </p>
        </div>

        <div style="margin: 10px 0; border-top: 1px dashed #ccc;">
            <p style="margin: 4px 0;">Cliente: {{ $venta->cliente->nombre ?? 'CONSUMIDOR FINAL' }}</p>
            <p style="margin: 4px 0;">RTN: {{ $venta->cliente->identificacion ?? 'N/A' }}</p>
        </div>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;" class="line-item">
            <thead>
                <tr>
                    <th style="width: 15%; text-align: left; padding-left: 2px;">Cant</th>
                    <th style="width: 55%; text-align: left;">DESCRIPCION</th>
                    <th style="width: 30%; text-align: right;">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venta->detalles as $detalle)
                <tr>
                    <td style="text-align: left; padding-left: 2px;">{{ $detalle->cantidad }}</td>
                    <td style="text-align: left;">{{ $detalle->inventario->marca->producto->nombre ?? 'Producto Desconocido' }}</td>
                    <td style="text-align: right;">{{ number_format($detalle->subtotal_final, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals" style="border-top: 1px dashed #ccc; padding-top: 5px;">
            <table>
                <tr>
                    <td>SubTotal Base (sin ISV)</td>
                    <td style="text-align: right;">L {{ number_format($venta->subtotal_neto, 2) }}</td>
                </tr>
                <tr>
                    <td>Descto. y Rebaja</td>
                    <td style="text-align: right;">L {{ number_format($venta->descuento, 2) }}</td>
                </tr>
                <tr>
                    <td>Subt. Exento (0.00%)</td>
                    <td style="text-align: right;">L {{ number_format($venta->subtotal_exonerado, 2) }}</td>
                </tr>
                <tr>
                    <td>Subt. Gravado (15.00%)</td>
                    <td style="text-align: right;">L {{ number_format($venta->subtotal_gravado, 2) }}</td>
                </tr>
                <tr>
                    <td>ISV (15.00%)</td>
                    <td style="text-align: right;">L {{ number_format($venta->total_isv, 2) }}</td>
                </tr>
                
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

        @if ($isFiscalDocument && $rangoCaiActivo)
        <div class="footer" style="margin-top: 15px; border-top: 1px dashed #ccc; padding-top: 10px;">
            <p style="margin: 0; font-size: 9px;">
                <strong>CAI:</strong> {{ $venta->cai }}
            </p>
            <p style="margin: 0; font-size: 9px;">
                Rango autorizado del: <strong style="font-size: 10px;">{{ $rangoCaiActivo->rango_inicial }}</strong> al: <strong style="font-size: 10px;">{{ $rangoCaiActivo->rango_final }}</strong>
            </p>
            <p style="margin: 0; font-size: 9px;">
                Fecha límite de Emisión: {{ \Carbon\Carbon::parse($rangoCaiActivo->fecha_limite_emision)->format('d/m/Y') }}
            </p>
        </div>
        @endif
        
    </div>
</body>
</html>