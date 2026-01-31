<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BoletinesCalificacionesExport implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $rows = [];

        // Información del estudiante
        $rows[] = ['ESTUDIANTE:', $this->data['student_name']];
        $rows[] = ['CÓDIGO:', $this->data['student_code']];
        $rows[] = ['PROGRAMA:', $this->data['program_name']];
        $rows[] = ['PERÍODO:', $this->data['period_name']];
        $rows[] = ['GENERADO EL:', now()->format('d/m/Y H:i')];
        $rows[] = [''];

        // Resumen
        $rows[] = ['RESUMEN DEL PERÍODO'];
        $rows[] = ['Total Evaluaciones:', $this->data['total_evaluations']];
        $rows[] = ['Aprobadas:', $this->data['approved_count']];
        $rows[] = ['Reprobadas:', $this->data['failed_count']];
        $rows[] = ['Promedio General:', $this->data['overall_average']];
        $rows[] = [''];

        // Encabezados de calificaciones
        $rows[] = ['MATERIA', 'DOCENTE', 'TIPO EVALUACIÓN', 'CALIFICACIÓN', 'ESTADO', 'FECHA'];

        // Calificaciones
        foreach ($this->data['grades'] as $grade) {
            $statusText = ucfirst($grade['status']);
            if ($grade['status'] == 'graded') {
                $statusText = $grade['score'] >= 10 ? 'Aprobado' : 'Reprobado';
            }

            $rows[] = [
                $grade['subject_name'],
                $grade['teacher_name'],
                $grade['evaluation_type'],
                $grade['score'] ?? 'N/A',
                $statusText,
                $grade['evaluation_date']
            ];
        }

        // Observaciones
        if (!empty($this->data['observations'])) {
            $rows[] = [''];
            $rows[] = ['OBSERVACIONES'];
            foreach ($this->data['observations'] as $observation) {
                $rows[] = ['• ' . $observation];
            }
        }

        return $rows;
    }

    public function title(): string
    {
        return 'Boletín de Calificaciones';
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        // Estilos para el encabezado del reporte
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A7:F7')->getFont()->setBold(true);
        $sheet->getStyle('A13:F13')->getFont()->setBold(true);

        // Colores para el encabezado de calificaciones
        $sheet->getStyle('A13:F13')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('A13:F13')->getFont()->getColor()->setRGB('FFFFFF');

        // Bordes para la tabla de calificaciones
        $lastRow = 13 + count($this->data['grades']);
        $sheet->getStyle('A13:F' . $lastRow)->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        return [];
    }
}