<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación de Acceso</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { background: {{ $accessLog->type === 'entrada' ? '#28a745' : '#dc3545' }}; color: #fff; padding: 30px 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px 20px; }
        .info-box { background: #f8f9fa; border-left: 4px solid {{ $accessLog->type === 'entrada' ? '#28a745' : '#dc3545' }}; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e9ecef; }
        .info-row:last-child { border-bottom: none; }
        .label { font-weight: bold; color: #666; }
        .value { color: #333; }
        .time-badge { display: inline-block; background: #007bff; color: #fff; padding: 8px 16px; border-radius: 20px; font-size: 14px; margin: 10px 0; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $accessLog->type === 'entrada' ? '✓ Entrada Registrada' : '✓ Salida Registrada' }}</h1>
        </div>
        
        <div class="content">
            <p>Estimado(a) <strong>{{ $student->representante_nombres }} {{ $student->representante_apellidos }}</strong>,</p>
            
            <p>Le informamos que su representado(a) ha {{ $accessLog->type === 'entrada' ? 'ingresado al' : 'salido del' }} plantel educativo.</p>
            
            <div class="info-box">
                <div class="info-row">
                    <span class="label">Estudiante:</span>
                    <span class="value">{{ $student->nombres }} {{ $student->apellidos }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Código:</span>
                    <span class="value">{{ $student->codigo }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Grado:</span>
                    <span class="value">{{ $student->grado }} - {{ $student->seccion }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Tipo de Acceso:</span>
                    <span class="value">{{ ucfirst($accessLog->type) }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Fecha y Hora:</span>
                    <span class="value">{{ $accessLog->access_time->format('d/m/Y H:i:s') }}</span>
                </div>
                @if($accessLog->notes)
                <div class="info-row">
                    <span class="label">Observaciones:</span>
                    <span class="value">{{ $accessLog->notes }}</span>
                </div>
                @endif
            </div>

            @if($accessLog->type === 'salida' && $timeInSchool)
            <div style="text-align: center; margin: 20px 0;">
                <p style="margin: 10px 0; color: #666;">Tiempo en el plantel:</p>
                <div class="time-badge">{{ $timeInSchool }}</div>
            </div>
            @endif

            <p style="margin-top: 20px; color: #666; font-size: 14px;">
                Esta es una notificación automática del sistema de control de acceso. 
                Si tiene alguna consulta, por favor contacte con la institución.
            </p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Sistema de Control de Acceso</p>
            <p>Este correo fue enviado automáticamente, por favor no responder.</p>
        </div>
    </div>
</body>
</html>
