<!DOCTYPE html>
<html>
<head>
    <title>Recordatorio de Pago</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2d3748; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
            Recordatorio de Pago
        </h2>
        
        <p>Hola,</p>
        
        <p>Le escribimos para recordarle que la 
            <strong>
                @if($schedule->numero_cuota == 0)
                    cuota inicial
                @else
                    cuota #{{ $schedule->numero_cuota }}
                @endif
            </strong> 
            del estudiante <strong>{{ $student->nombres }} {{ $student->apellidos }}</strong> 
            vence en 3 días ({{ \Carbon\Carbon::parse($schedule->fecha_vencimiento)->format('d/m/Y') }}).
        </p>
        
        <div style="background-color: #f7fafc; border: 1px solid #e2e8f0; border-radius: 5px; padding: 15px; margin: 20px 0;">
            <h3 style="margin-top: 0; color: #2d3748;">Detalles del pago:</h3>
            <ul style="padding-left: 20px;">
                <li><strong>Monto:</strong> ${{ number_format($schedule->monto, 2) }}</li>
                <li><strong>Fecha de vencimiento:</strong> {{ \Carbon\Carbon::parse($schedule->fecha_vencimiento)->format('d/m/Y') }}</li>
                <li><strong>Grado:</strong> {{ $student->grado }} - {{ $student->seccion }}</li>
            </ul>
        </div>
        
        <p>Por favor, asegúrese de realizar el pago antes de la fecha de vencimiento para evitar recargos.</p>
        
        <p>Si ya ha realizado el pago, por favor ignore este mensaje.</p>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
            <p style="font-size: 0.9em; color: #718096;">
                Atentamente,<br>
                <strong>Equipo de Administración</strong>
            </p>
        </div>
    </div>
</body>
</html>