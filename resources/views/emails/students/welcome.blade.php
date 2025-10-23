<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bienvenido - Información de Matrícula</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
        }
        .content {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .details {
            background-color: white;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            margin: 15px 0;
        }
        .details-header {
            background-color: #e9ecef;
            padding: 10px 15px;
            font-weight: bold;
            border-bottom: 1px solid #dee2e6;
        }
        .details-body {
            padding: 15px;
        }
        .detail-row {
            display: flex;
            margin-bottom: 10px;
        }
        .detail-label {
            font-weight: bold;
            width: 200px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6c757d;
            font-size: 0.9em;
        }
        .cost-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .cost-table th, .cost-table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }
        .cost-table th {
            background-color: #e9ecef;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Bienvenido {{ $student->nombres }} {{ $student->apellidos }}</h1>
        <p>Información de tu matrícula</p>
    </div>

    <div class="content">
        <p>Estimado/a {{ $student->nombres }} {{ $student->apellidos }},</p>

        <p>Te damos la más cordial bienvenida. A continuación te presentamos la información detallada de tu matrícula:</p>

        <div class="details">
            <div class="details-header">Datos del Estudiante</div>
            <div class="details-body">
                <div class="detail-row">
                    <div class="detail-label">Nombres:</div>
                    <div>{{ $student->nombres }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Apellidos:</div>
                    <div>{{ $student->apellidos }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Documento de Identidad:</div>
                    <div>{{ $student->documento_identidad }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Fecha de Nacimiento:</div>
                    <div>{{ $student->fecha_nacimiento->format('d/m/Y') }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Edad:</div>
                    <div>{{ $student->edad }} años</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Código Estudiantil:</div>
                    <div>{{ $student->codigo }}</div>
                </div>
            </div>
        </div>

        <div class="details">
            <div class="details-header">Detalles de la Matrícula</div>
            <div class="details-body">
                <div class="detail-row">
                    <div class="detail-label">Nivel Educativo:</div>
                    <div>{{ $student->nivelEducativo->nombre ?? 'N/A' }}</div>
                </div>

                @if($student->nivelEducativo)
                <div class="detail-row">
                    <div class="detail-label">Costo Total:</div>
                    <div>{{ number_format($student->nivelEducativo->costo ?? 0, 2) }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Cuota Inicial:</div>
                    <div>{{ number_format($student->nivelEducativo->cuota_inicial ?? 0, 2) }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Número de Cuotas:</div>
                    <div>{{ $student->nivelEducativo->numero_cuotas ?? 0 }}</div>
                </div>

                @if(($student->nivelEducativo->costo ?? 0) > 0 && ($student->nivelEducativo->numero_cuotas ?? 0) > 0)
                <table class="cost-table">
                    <thead>
                        <tr>
                            <th>Concepto</th>
                            <th>Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Costo Total</td>
                            <td>{{ number_format($student->nivelEducativo->costo, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Cuota Inicial</td>
                            <td>{{ number_format($student->nivelEducativo->cuota_inicial, 2) }}</td>
                        </tr>
                        @php
                            $montoCuota = (($student->nivelEducativo->costo - $student->nivelEducativo->cuota_inicial) / $student->nivelEducativo->numero_cuotas);
                        @endphp
                        <tr>
                            <td>Cuotas ({{ $student->nivelEducativo->numero_cuotas }} cuotas de)</td>
                            <td>{{ number_format($montoCuota, 2) }} cada una</td>
                        </tr>
                    </tbody>
                </table>
                @endif
                @endif

                <div class="detail-row">
                    <div class="detail-label">Grado:</div>
                    <div>{{ $student->grado }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Sección:</div>
                    <div>{{ $student->seccion }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Turno:</div>
                    <div>{{ $student->turno->nombre ?? 'N/A' }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Período Escolar:</div>
                    <div>{{ $student->schoolPeriod->name ?? 'N/A' }}</div>
                </div>
            </div>
        </div>

        <p>Te recordamos que esta información también puedes consultarla en nuestra plataforma educativa.</p>

        <p>Si tienes alguna duda o necesitas más información, no dudes en contactarnos.</p>
    </div>

    <div class="footer">
        <p>Este es un mensaje automático, por favor no respondas a este correo.</p>
        <p>&copy; {{ date('Y') }} Sistema Educativo. Todos los derechos reservados.</p>
    </div>
</body>
</html>
