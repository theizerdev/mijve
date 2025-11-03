<!DOCTYPE html>
<html>
<head>
    <title>Notificación de Deuda Pendiente</title>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .content {
            background-color: #fff;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 0.9em;
            color: #6c757d;
        }
        .amount {
            font-size: 1.2em;
            font-weight: bold;
            color: #dc3545;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        th {
            background-color: #f8f9fa;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Notificación de Deuda Pendiente</h2>
    </div>
    
    <div class="content">
        <p>Estimado(a) {{ $student->representante_nombres ?? $student->nombres }} {{ $student->representante_apellidos ?? $student->apellidos }},</p>
        
        <p>Le escribimos para informarle sobre el estado de cuenta de {{ $student->nombres }} {{ $student->apellidos }} en nuestra institución.</p>
        
        <p>Actualmente, existe un saldo pendiente por pagar:</p>
        
        <p class="amount">Monto pendiente: ${{ number_format($pendingAmount, 2) }}</p>
        
        <h3>Detalle de la deuda:</h3>
        
        @if(count($debtDetails) > 0)
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Concepto</th>
                        <th class="text-right">Monto</th>
                        <th class="text-right">Pagado</th>
                        <th class="text-right">Pendiente</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($debtDetails as $detail)
                        <tr>
                            <td>{{ $detail->fecha_vencimiento?->format('d/m/Y') ?? 'N/A' }}</td>
                            <td>Cuota {{ $detail->numero_cuota ?? 'N/A' }}</td>
                            <td class="text-right">${{ number_format($detail->monto, 2) }}</td>
                            <td class="text-right">${{ number_format($detail->monto_pagado ?? 0, 2) }}</td>
                            <td class="text-right">${{ number_format($detail->monto - ($detail->monto_pagado ?? 0), 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No se encontraron detalles de pagos registrados.</p>
        @endif
        
        <p>Le solicitamos gestionar el pago de esta deuda a la mayor brevedad posible para evitar inconvenientes en el servicio.</p>
        
        <p>Si ya ha realizado el pago, por favor ignore este mensaje o comuníquese con nosotros para actualizar nuestros registros.</p>
    </div>
    
    <div class="footer">
        <p>Este es un mensaje automático, por favor no responda a este correo.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.</p>
    </div>
</body>
</html>