<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $certificate->type_label }} - {{ $certificate->certificate_number }}</title>
    <style>
        @page {
            margin: 2cm;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #1a56db;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #1a56db;
            font-size: 18pt;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 10pt;
        }
        .title {
            text-align: center;
            margin: 40px 0;
        }
        .title h2 {
            font-size: 24pt;
            color: #1a56db;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 3px;
        }
        .content {
            text-align: justify;
            margin: 30px 0;
            font-size: 12pt;
        }
        .content p {
            margin-bottom: 15px;
        }
        .student-name {
            font-weight: bold;
            text-transform: uppercase;
        }
        .highlight {
            font-weight: bold;
            color: #1a56db;
        }
        .academic-data {
            margin: 30px 0;
            padding: 15px;
            background: #f8f9fa;
            border-left: 4px solid #1a56db;
        }
        .academic-data table {
            width: 100%;
            border-collapse: collapse;
        }
        .academic-data td {
            padding: 5px 10px;
        }
        .academic-data td:first-child {
            color: #666;
            width: 40%;
        }
        .academic-data td:last-child {
            font-weight: bold;
        }
        .observations {
            margin: 20px 0;
            padding: 10px;
            background: #fff3cd;
            border-radius: 5px;
            font-size: 10pt;
        }
        .footer {
            margin-top: 60px;
        }
        .footer-info {
            display: table;
            width: 100%;
        }
        .footer-left, .footer-right {
            display: table-cell;
            width: 50%;
        }
        .footer-right {
            text-align: right;
        }
        .signature {
            text-align: center;
            margin-top: 80px;
        }
        .signature-line {
            border-top: 1px solid #333;
            width: 250px;
            margin: 0 auto;
            padding-top: 10px;
        }
        .signature-name {
            font-weight: bold;
        }
        .signature-title {
            font-size: 10pt;
            color: #666;
        }
        .verification {
            margin-top: 40px;
            text-align: center;
            font-size: 9pt;
            color: #666;
            border-top: 1px dashed #ccc;
            padding-top: 15px;
        }
        .verification code {
            background: #e9ecef;
            padding: 3px 8px;
            font-family: monospace;
            font-size: 10pt;
        }
        .watermark {
            position: fixed;
            top: 45%;
            left: 25%;
            font-size: 80pt;
            color: rgba(0,0,0,0.03);
            transform: rotate(-30deg);
            z-index: -1;
        }
        @if($certificate->status === 'revoked')
        .revoked-stamp {
            position: fixed;
            top: 40%;
            left: 20%;
            font-size: 48pt;
            color: rgba(255,0,0,0.3);
            transform: rotate(-30deg);
            border: 5px solid rgba(255,0,0,0.3);
            padding: 10px 30px;
        }
        @endif
    </style>
</head>
<body>
    <div class="watermark">OFICIAL</div>

    @if($certificate->status === 'revoked')
        <div class="revoked-stamp">REVOCADO</div>
    @endif

    <div class="header">
        <h1>{{ $certificate->empresa->razon_social ?? 'Institución Educativa' }}</h1>
        <p>{{ $certificate->sucursal->direccion ?? '' }}</p>
        @if($certificate->empresa->telefono ?? false)
            <p>Teléfono: {{ $certificate->empresa->telefono }}</p>
        @endif
    </div>

    <div class="title">
        <h2>{{ $certificate->type_label }}</h2>
    </div>

    <div class="content">
        <p>
            Quien suscribe, en uso de las atribuciones que le confiere el cargo, hace constar que:
        </p>

        <p>
            El/La estudiante <span class="student-name">{{ $certificate->student->nombres }} {{ $certificate->student->apellidos }}</span>, 
            identificado(a) con código estudiantil <span class="highlight">{{ $certificate->student->codigo }}</span>,
            @if($certificate->student->cedula ?? false)
                portador(a) de la cédula de identidad N° {{ $certificate->student->cedula }},
            @endif

            @if($certificate->certificate_type === 'enrollment')
                se encuentra debidamente inscrito(a) y cursando estudios en esta institución educativa 
                para el período escolar <span class="highlight">{{ $certificate->schoolPeriod->name ?? '' }}</span>.
            @elseif($certificate->certificate_type === 'attendance')
                ha cumplido con la asistencia regular a clases, registrando un porcentaje de asistencia del 
                <span class="highlight">{{ number_format($certificate->attendance_percentage, 1) }}%</span> 
                durante el período escolar <span class="highlight">{{ $certificate->schoolPeriod->name ?? '' }}</span>.
            @elseif($certificate->certificate_type === 'academic')
                ha culminado satisfactoriamente el período escolar 
                <span class="highlight">{{ $certificate->schoolPeriod->name ?? '' }}</span>, 
                obteniendo los siguientes resultados académicos:
            @elseif($certificate->certificate_type === 'completion')
                ha culminado satisfactoriamente sus estudios correspondientes al período escolar 
                <span class="highlight">{{ $certificate->schoolPeriod->name ?? '' }}</span>,
                cumpliendo con todos los requisitos académicos establecidos.
            @elseif($certificate->certificate_type === 'conduct')
                ha demostrado una conducta <span class="highlight">{{ $certificate->conduct_grade ?? 'satisfactoria' }}</span> 
                durante el período escolar <span class="highlight">{{ $certificate->schoolPeriod->name ?? '' }}</span>,
                cumpliendo con las normas de convivencia institucional.
            @endif
        </p>

        @if(in_array($certificate->certificate_type, ['academic', 'completion']) && ($certificate->overall_average || $certificate->total_subjects))
            <div class="academic-data">
                <table>
                    @if($certificate->overall_average)
                        <tr>
                            <td>Promedio General:</td>
                            <td>{{ number_format($certificate->overall_average, 2) }} puntos</td>
                        </tr>
                    @endif
                    @if($certificate->total_subjects)
                        <tr>
                            <td>Materias Cursadas:</td>
                            <td>{{ $certificate->total_subjects }}</td>
                        </tr>
                    @endif
                    @if($certificate->approved_subjects)
                        <tr>
                            <td>Materias Aprobadas:</td>
                            <td>{{ $certificate->approved_subjects }}</td>
                        </tr>
                    @endif
                    @if($certificate->attendance_percentage)
                        <tr>
                            <td>Porcentaje de Asistencia:</td>
                            <td>{{ number_format($certificate->attendance_percentage, 1) }}%</td>
                        </tr>
                    @endif
                </table>
            </div>
        @endif

        @if($certificate->observations)
            <div class="observations">
                <strong>Observaciones:</strong> {{ $certificate->observations }}
            </div>
        @endif

        <p>
            Se expide la presente constancia a solicitud del interesado, para los fines que estime conveniente.
        </p>
    </div>

    <div class="footer">
        <div class="footer-info">
            <div class="footer-left">
                <p><strong>Fecha de emisión:</strong><br>
                {{ $certificate->issue_date ? $certificate->issue_date->format('d') }} de 
                {{ $certificate->issue_date ? $certificate->issue_date->translatedFormat('F') : now()->translatedFormat('F') }} de 
                {{ $certificate->issue_date ? $certificate->issue_date->format('Y') : now()->format('Y') }}</p>
            </div>
            <div class="footer-right">
                <p><strong>Número de documento:</strong><br>
                {{ $certificate->certificate_number }}</p>
            </div>
        </div>
    </div>

    <div class="signature">
        <div class="signature-line">
            <p class="signature-name">{{ $certificate->issued_by ?? 'Autoridad Competente' }}</p>
            <p class="signature-title">Firma y Sello Institucional</p>
        </div>
    </div>

    <div class="verification">
        <p>Este documento puede ser verificado con el código: <code>{{ $certificate->verification_code }}</code></p>
        <p>Documento generado electrónicamente el {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
