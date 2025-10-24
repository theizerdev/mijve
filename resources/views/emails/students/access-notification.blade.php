<!DOCTYPE html>
<html>
<head>
    <title>Notificación de Acceso de Estudiante</title>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 20px;
            border: 1px solid #dee2e6;
        }
        .footer {
            background-color: #e9ecef;
            padding: 15px;
            text-align: center;
            border-radius: 0 0 5px 5px;
            font-size: 0.9em;
        }
        .info-group {
            margin-bottom: 15px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }
        .badge {
            display: inline-block;
            padding: 0.25em 0.4em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }
        .badge-success {
            color: #fff;
            background-color: #28a745;
        }
        .badge-danger {
            color: #fff;
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Notificación de {{ $log->type === 'entry' ? 'Entrada' : 'Salida' }} de Estudiante</h1>
        </div>
        
        <div class="content">
            <p>Estimado(a) {{ $log->student->esMenorDeEdad ? $log->student->representante_nombres . ' ' . $log->student->representante_apellidos : $log->student->nombres . ' ' . $log->student->apellidos }},</p>
            
            <p>Le informamos que {{ $log->student->esMenorDeEdad ? 'su representado(a)' : 'usted' }} ha sido registrado(a) {{ $log->type === 'entry' ? 'ingresando al' : 'saliendo del' }} plantel educativo.</p>
            
            <div class="info-group">
                <span class="info-label">Estudiante:</span>
                {{ $log->student->nombres }} {{ $log->student->apellidos }}
            </div>
            
            <div class="info-group">
                <span class="info-label">Documento:</span>
                {{ $log->student->documento_identidad }}
            </div>
            
            <div class="info-group">
                <span class="info-label">Código:</span>
                {{ $log->student->codigo }}
            </div>
            
            <div class="info-group">
                <span class="info-label">Grado/Sección:</span>
                {{ $log->student->grado }} - {{ $log->student->seccion }}
            </div>
            
            <div class="info-group">
                <span class="info-label">Tipo de Acceso:</span>
                @if($log->type === 'entry')
                    <span class="badge badge-success">Entrada</span>
                @else
                    <span class="badge badge-danger">Salida</span>
                @endif
            </div>
            
            <div class="info-group">
                <span class="info-label">Fecha y Hora:</span>
                {{ $log->access_time->format('d/m/Y H:i:s') }}
            </div>
            
            @if($log->notes)
            <div class="info-group">
                <span class="info-label">Notas:</span>
                {{ $log->notes }}
            </div>
            @endif
            
            <p>Este es un mensaje automático del sistema de control de acceso del plantel educativo.</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Plante Educativo. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>