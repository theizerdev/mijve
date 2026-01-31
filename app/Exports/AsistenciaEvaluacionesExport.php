<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AsistenciaEvaluacionesExport implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $rows = [];

        // Información del reporte
        $rows[] = ['REPORTE DE ASISTENCIA Y EVALUACIONES'];
        $rows[] = [''];
        $rows[] = ['Materia:', $this->data['statistics']['subject_name']];
        $rows[] = ['Docente:', $this->data['statistics']['teacher_name']];
        $rows[] = ['Período:', $this->data['statistics']['period_name']];
        $rows[] = ['Generado el:', now()->format('d/m/Y H:i')];
        $rows[] = [''];

        // Estadísticas generales
        $rows[] = ['ESTADÍSTICAS DE ASISTENCIA'];
        $rows[] = ['Total Estudiantes:', $this->data['statistics']['total_students']];
        $rows[] = ['Total Presentes:', $this->data['statistics']['total_present']];
        $rows[] = ['Total Ausentes:', $this->data['statistics']['total_absent']];
        $rows[] = ['Total Exentos:', $this->data['statistics']['total_exempt']];
        $rows[] = ['Tasa de Asistencia:', $this->data['statistics']['overall_attendance_rate'] . '%'];
        $rows[] = [''];

        // Encabezados del detalle
        $rows[] = ['ESTUDIANTE', 'CÓDIGO', 'PRESENTES', 'AUSENTES', 'EXENTOS', 'TASA ASISTENCIA', 'TOTAL EVALUACIONES'];

        // Detalle por estudiante
        foreach ($this->data['attendanceData'] as $studentData) {
            $rows[] = [
                $studentData['student']->nombres . ' ' . $studentData['student']->apellidos,
                $studentData['student']->codigo,
                $studentData['attendance_count'],
                $studentData['absence_count'],
                $studentData['exemption_count'],
                $studentData['attendance_rate'] . '%',
                $studentData['attendance_count'] + $studentData['absence_count'] + $studentData['exemption_count']
            ];
        }

        return $rows;
    }

    public function title(): string
    {
        return 'Asistencia y Evaluaciones';
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        // Estilos para el título
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A8')->getFont()->setBold(true);
        $sheet->getStyle('A15:G15')->getFont()->setBold(true);

        // Colores para los encabezados
        $sheet->getStyle('A15:G15')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('A15:G15')->getFont()->getColor()->setRGB('FFFFFF');

        // Bordes para la tabla
        $lastRow = 15 + count($this->data['attendanceData']);
        $sheet->getStyle('A15:G' . $lastRow)->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        return [];
    }
}