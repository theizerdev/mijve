<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $report->title }} - {{ $report->report_number }}</title>
    <style>
        @page {
            margin: 1.5cm;
            size: letter landscape;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #1a56db;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 14pt;
            color: #1a56db;
        }
        .header p {
            margin: 3px 0;
            color: #666;
            font-size: 9pt;
        }
        .title {
            text-align: center;
            margin: 15px 0;
        }
        .title h2 {
            font-size: 14pt;
            margin: 0 0 5px 0;
            color: #333;
        }
        .title .report-number {
            font-size: 10pt;
            color: #666;
        }
        .info-box {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .info-box table {
            width: 100%;
        }
        .info-box td {
            padding: 3px 10px;
            font-size: 9pt;
        }
        .info-box td:first-child {
            color: #666;
            width: 15%;
        }
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .grades-table th {
            background: #1a56db;
            color: white;
            padding: 8px 5px;
            font-size: 9pt;
            text-align: center;
            border: 1px solid #1a56db;
        }
        .grades-table td {
            padding: 6px 5px;
            border: 1px solid #ddd;
            font-size: 9pt;
        }
        .grades-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        .grades-table .student-name {
            text-align: left;
        }
        .grades-table .grade {
            text-align: center;
            font-weight: bold;
        }
        .grades-table .approved {
            color: #198754;
        }
        .grades-table .failed {
            color: #dc3545;
        }
        .stats-box {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .stats-item {
            display: table-cell;
            text-align: center;
            padding: 10px;
            background: #f8f9fa;
            border: 1px solid #ddd;
        }
        .stats-item h3 {
            margin: 0;
            font-size: 16pt;
            color: #1a56db;
        }
        .stats-item small {
            color: #666;
            font-size: 8pt;
        }
        .observations {
            margin: 15px 0;
            padding: 10px;
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            font-size: 9pt;
        }
        .footer {
            margin-top: 30px;
        }
        .signatures {
            display: table;
            width: 100%;
            margin-top: 40px;
        }
        .signature-block {
            display: table-cell;
            width: 33%;
            text-align: center;
            padding: 0 20px;
        }
        .signature-line {
            border-top: 1px solid #333;
            padding-top: 5px;
            margin-top: 50px;
        }
        .signature-title {
            font-size: 8pt;
            color: #666;
        }
        .watermark {
            position: fixed;
            top: 45%;
            left: 30%;
            font-size: 60pt;
            color: rgba(0,0,0,0.03);
            transform: rotate(-30deg);
            z-index: -1;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
        }
        .status-published {
            background: #d1e7dd;
            color: #0f5132;
        }
        .status-approved {
            background: #cfe2ff;
            color: #084298;
        }
        .status-draft {
            background: #e2e3e5;
            color: #41464b;
        }
        .page-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="watermark">OFICIAL</div>

    <div class="header">
        <h1>{{ $report->empresa->razon_social ?? 'Institución Educativa' }}</h1>
        <p>{{ $report->sucursal->direccion ?? '' }}</p>
        @if($report->empresa->telefono ?? false)
            <p>Teléfono: {{ $report->empresa->telefono }}</p>
        @endif
    </div>

    <div class="title">
        <h2>{{ $report->title }}</h2>
        <div class="report-number">
            Acta N° <strong>{{ $report->report_number }}</strong>
            <span class="status-badge status-{{ $report->status }}">
                {{ strtoupper($report->status_label) }}
            </span>
        </div>
    </div>

    <div class="info-box">
        <table>
            <tr>
                <td>Tipo de Acta:</td>
                <td><strong>{{ $report->type_label }}</strong></td>
                <td>Período Escolar:</td>
                <td><strong>{{ $report->schoolPeriod->name ?? '-' }}</strong></td>
                <td>Sección:</td>
                <td><strong>{{ $report->section->nombre ?? '-' }}</strong></td>
            </tr>
            <tr>
                @if($report->subject)
                    <td>Materia:</td>
                    <td><strong>{{ $report->subject->name }}</strong></td>
                @endif
                @if($report->evaluationPeriod)
                    <td>Lapso:</td>
                    <td><strong>{{ $report->evaluationPeriod->name }}</strong></td>
                @endif
                <td>Fecha Generación:</td>
                <td><strong>{{ $report->generated_at ? $report->generated_at->format('d/m/Y') : '-' }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="stats-box">
        <div class="stats-item">
            <h3>{{ $report->total_students }}</h3>
            <small>Total Estudiantes</small>
        </div>
        <div class="stats-item">
            <h3 style="color: #198754;">{{ $report->approved_count }}</h3>
            <small>Aprobados</small>
        </div>
        <div class="stats-item">
            <h3 style="color: #dc3545;">{{ $report->failed_count }}</h3>
            <small>Reprobados</small>
        </div>
        <div class="stats-item">
            <h3>{{ number_format($report->average_grade, 2) }}</h3>
            <small>Promedio General</small>
        </div>
        <div class="stats-item">
            <h3>{{ $report->approval_rate }}%</h3>
            <small>Tasa de Aprobación</small>
        </div>
    </div>

    <table class="grades-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 12%;">Código</th>
                <th style="width: 35%;">Apellidos y Nombres</th>
                <th style="width: 15%;">Promedio</th>
                <th style="width: 15%;">Literal</th>
                <th style="width: 18%;">Condición</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report->grades_data ?? [] as $index => $studentData)
                @php
                    $average = $studentData['average'] ?? 0;
                    $isApproved = ($studentData['status'] ?? '') === 'approved';
                    
                    // Escala literal
                    if ($average >= 18) $literal = 'A';
                    elseif ($average >= 15) $literal = 'B';
                    elseif ($average >= 12) $literal = 'C';
                    elseif ($average >= 10) $literal = 'D';
                    else $literal = 'E';
                @endphp
                <tr>
                    <td class="grade">{{ $index + 1 }}</td>
                    <td class="grade">{{ $studentData['codigo'] ?? '-' }}</td>
                    <td class="student-name">{{ $studentData['apellidos'] ?? '' }}, {{ $studentData['nombres'] ?? '' }}</td>
                    <td class="grade {{ $isApproved ? 'approved' : 'failed' }}">
                        {{ number_format($average, 2) }}
                    </td>
                    <td class="grade">{{ $literal }}</td>
                    <td class="grade {{ $isApproved ? 'approved' : 'failed' }}">
                        {{ $isApproved ? 'APROBADO' : 'REPROBADO' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background: #e9ecef; font-weight: bold;">
                <td colspan="3" style="text-align: right;">RESUMEN:</td>
                <td class="grade">{{ number_format($report->average_grade, 2) }}</td>
                <td class="grade">-</td>
                <td class="grade">
                    <span class="approved">{{ $report->approved_count }} APR</span> / 
                    <span class="failed">{{ $report->failed_count }} REP</span>
                </td>
            </tr>
        </tfoot>
    </table>

    @if($report->observations)
        <div class="observations">
            <strong>Observaciones:</strong> {{ $report->observations }}
        </div>
    @endif

    <div class="footer">
        <div class="signatures">
            <div class="signature-block">
                <div class="signature-line">
                    <p><strong>{{ $report->generatedByUser->name ?? 'Docente' }}</strong></p>
                    <p class="signature-title">Elaborado por</p>
                </div>
            </div>
            <div class="signature-block">
                <div class="signature-line">
                    <p><strong>{{ $report->approvedByUser->name ?? 'Coordinador(a)' }}</strong></p>
                    <p class="signature-title">Revisado por</p>
                </div>
            </div>
            <div class="signature-block">
                <div class="signature-line">
                    <p><strong>Director(a)</strong></p>
                    <p class="signature-title">Aprobado por</p>
                </div>
            </div>
        </div>
    </div>

    <div class="page-footer">
        <p>
            Documento generado el {{ now()->format('d/m/Y H:i') }} | 
            Acta N° {{ $report->report_number }} | 
            Este documento es válido sin firma cuando es emitido electrónicamente
        </p>
    </div>
</body>
</html>
