<?php

namespace App\Livewire\Admin\Reportes;

use App\Traits\HasDynamicLayout;
use App\Traits\HasRegionalFormatting;
use Livewire\Component;
use App\Models\Subject;
use App\Models\Evaluation;
use App\Models\Grade;
use App\Models\EvaluationPeriod;
use App\Models\Teacher;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EstadisticasCalificacionesMateria extends Component
{
    use HasDynamicLayout, HasRegionalFormatting;

    public $subjects = [];
    public $subject_id;
    public $evaluation_period_id;
    public $teacher_id;
    public $evaluationPeriods = [];
    public $teachers = [];
    public $statistics = [];
    public $gradeDistribution = [];
    public $topStudents = [];
    public $bottomStudents = [];

    public function mount()
    {
        $this->evaluationPeriods = EvaluationPeriod::where('is_active', true)
            ->orderBy('start_date', 'desc')
            ->get();

        $this->subjects = Subject::with(['programa.nivelEducativo', 'teachers'])
            
            ->orderBy('name')
            ->get();

        $this->teachers = Teacher::with('user')
            
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function updatedSubjectId()
    {
        $this->generateStatistics();
    }

    public function updatedEvaluationPeriodId()
    {
        $this->generateStatistics();
    }

    public function updatedTeacherId()
    {
        $this->generateStatistics();
    }

    public function generateStatistics()
    {
        if (!$this->subject_id || !$this->evaluation_period_id) {
            $this->statistics = [];
            $this->gradeDistribution = [];
            $this->topStudents = [];
            $this->bottomStudents = [];
            return;
        }

        // Obtener evaluaciones de la materia y período
        $evaluationsQuery = Evaluation::with(['grades.student', 'subject', 'teacher.user', 'evaluationType'])
            ->where('subject_id', $this->subject_id)
            ->where('evaluation_period_id', $this->evaluation_period_id)
            ->where('is_published', true);

        if ($this->teacher_id) {
            $evaluationsQuery->where('teacher_id', $this->teacher_id);
        }

        $evaluations = $evaluationsQuery->get();

        if ($evaluations->isEmpty()) {
            $this->statistics = [];
            $this->gradeDistribution = [];
            $this->topStudents = [];
            $this->bottomStudents = [];
            return;
        }

        // Calcular estadísticas generales
        $totalGrades = 0;
        $sumGrades = 0;
        $approvedCount = 0;
        $failedCount = 0;
        $absentCount = 0;
        $exemptCount = 0;
        
        // Distribución de calificaciones
        $gradeRanges = [
            '0-5' => 0,
            '6-9' => 0,
            '10-13' => 0,
            '14-16' => 0,
            '17-20' => 0
        ];

        // Recopilar datos por estudiante
        $studentData = [];

        foreach ($evaluations as $evaluation) {
            foreach ($evaluation->grades as $grade) {
                if ($grade->status === 'graded' && $grade->score !== null) {
                    $totalGrades++;
                    $sumGrades += $grade->score;

                    // Clasificar por rangos
                    if ($grade->score >= 0 && $grade->score <= 5) {
                        $gradeRanges['0-5']++;
                    } elseif ($grade->score >= 6 && $grade->score <= 9) {
                        $gradeRanges['6-9']++;
                    } elseif ($grade->score >= 10 && $grade->score <= 13) {
                        $gradeRanges['10-13']++;
                        $approvedCount++;
                    } elseif ($grade->score >= 14 && $grade->score <= 16) {
                        $gradeRanges['14-16']++;
                        $approvedCount++;
                    } elseif ($grade->score >= 17 && $grade->score <= 20) {
                        $gradeRanges['17-20']++;
                        $approvedCount++;
                    }

                    // Datos por estudiante
                    $studentId = $grade->student_id;
                    if (!isset($studentData[$studentId])) {
                        $studentData[$studentId] = [
                            'student' => $grade->student,
                            'total_grades' => 0,
                            'sum_grades' => 0,
                            'grades' => []
                        ];
                    }
                    
                    $studentData[$studentId]['total_grades']++;
                    $studentData[$studentId]['sum_grades'] += $grade->score;
                    $studentData[$studentId]['grades'][] = $grade;

                } elseif ($grade->status === 'absent') {
                    $absentCount++;
                } elseif ($grade->status === 'exempt') {
                    $exemptCount++;
                }
            }
        }

        // Calcular promedios por estudiante y ordenar
        foreach ($studentData as &$data) {
            $data['average'] = $data['total_grades'] > 0 ? round($data['sum_grades'] / $data['total_grades'], 2) : 0;
        }
        unset($data);

        // Ordenar estudiantes por promedio
        usort($studentData, function($a, $b) {
            return $b['average'] <=> $a['average'];
        });

        // Estudiantes destacados y con bajo rendimiento
        $this->topStudents = array_slice($studentData, 0, 5);
        $this->bottomStudents = array_slice($studentData, -5, 5);
        $this->bottomStudents = array_reverse($this->bottomStudents); // Para tener el más bajo primero

        // Calcular estadísticas finales
        $this->statistics = [
            'total_evaluations' => $evaluations->count(),
            'total_grades' => $totalGrades,
            'average_grade' => $totalGrades > 0 ? round($sumGrades / $totalGrades, 2) : 0,
            'approved_count' => $approvedCount,
            'failed_count' => $failedCount,
            'absent_count' => $absentCount,
            'exempt_count' => $exemptCount,
            'approval_rate' => $totalGrades > 0 ? round(($approvedCount / $totalGrades) * 100, 2) : 0,
            'subject_name' => $evaluations->first()->subject->nombre ?? 'N/A',
            'teacher_name' => $evaluations->first()->teacher->name ?? 'N/A',
            'period_name' => $evaluations->first()->evaluationPeriod->name ?? 'N/A'
        ];

        $this->gradeDistribution = $gradeRanges;
    }

    public function exportarExcel()
    {
        if (empty($this->statistics)) {
            session()->flash('error', 'No hay datos para exportar.');
            return;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Configurar página
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        // Información del reporte
        $sheet->setCellValue('A1', 'ESTADÍSTICAS DE CALIFICACIONES POR MATERIA');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->getColor()->setRGB('2E3B4E');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        $sheet->setCellValue('A3', 'Materia: ' . $this->statistics['subject_name']);
        $sheet->setCellValue('A4', 'Docente: ' . $this->statistics['teacher_name']);
        $sheet->setCellValue('A5', 'Período: ' . $this->statistics['period_name']);
        $sheet->setCellValue('A6', 'Generado el: ' . now()->format('d/m/Y H:i'));

        // Estadísticas generales
        $sheet->setCellValue('A8', 'ESTADÍSTICAS GENERALES');
        $sheet->mergeCells('A8:F8');
        $sheet->getStyle('A8')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A8')->getFill()->setFillType('solid')->getStartColor()->setRGB('4472C4');

        $sheet->setCellValue('A9', 'Total Evaluaciones: ' . $this->statistics['total_evaluations']);
        $sheet->setCellValue('B9', 'Total Calificaciones: ' . $this->statistics['total_grades']);
        $sheet->setCellValue('C9', 'Promedio General: ' . $this->statistics['average_grade']);
        $sheet->setCellValue('D9', 'Tasa Aprobación: ' . $this->statistics['approval_rate'] . '%');

        $sheet->setCellValue('A10', 'Aprobados: ' . $this->statistics['approved_count']);
        $sheet->setCellValue('B10', 'Reprobados: ' . $this->statistics['failed_count']);
        $sheet->setCellValue('C10', 'Ausentes: ' . $this->statistics['absent_count']);
        $sheet->setCellValue('D10', 'Exentos: ' . $this->statistics['exempt_count']);

        // Distribución de calificaciones
        $sheet->setCellValue('A12', 'DISTRIBUCIÓN DE CALIFICACIONES');
        $sheet->mergeCells('A12:B12');
        $sheet->getStyle('A12')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A12')->getFill()->setFillType('solid')->getStartColor()->setRGB('70AD47');

        $sheet->setCellValue('A13', 'Rango');
        $sheet->setCellValue('B13', 'Cantidad');
        $sheet->getStyle('A13:B13')->getFont()->setBold(true);

        $row = 14;
        foreach ($this->gradeDistribution as $range => $count) {
            $sheet->setCellValue('A' . $row, $range);
            $sheet->setCellValue('B' . $row, $count);
            $row++;
        }

        // Estudiantes destacados
        $row += 1;
        $sheet->setCellValue('A' . $row, 'ESTUDIANTES DESTACADOS (Top 5)');
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A' . $row)->getFill()->setFillType('solid')->getStartColor()->setRGB('C6EFCE');

        $row++;
        $sheet->setCellValue('A' . $row, 'Nombre');
        $sheet->setCellValue('B' . $row, 'Promedio');
        $sheet->setCellValue('C' . $row, 'Calificaciones');
        $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);

        $row++;
        foreach ($this->topStudents as $studentData) {
            $sheet->setCellValue('A' . $row, $studentData['student']->nombres . ' ' . $studentData['student']->apellidos);
            $sheet->setCellValue('B' . $row, $studentData['average']);
            $sheet->setCellValue('C' . $row, $studentData['total_grades']);
            $row++;
        }

        // Estudiantes con bajo rendimiento
        $row += 1;
        $sheet->setCellValue('A' . $row, 'ESTUDIANTES CON BAJO RENDIMIENTO (Bottom 5)');
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A' . $row)->getFill()->setFillType('solid')->getStartColor()->setRGB('FFC7CE');

        $row++;
        $sheet->setCellValue('A' . $row, 'Nombre');
        $sheet->setCellValue('B' . $row, 'Promedio');
        $sheet->setCellValue('C' . $row, 'Calificaciones');
        $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);

        $row++;
        foreach ($this->bottomStudents as $studentData) {
            $sheet->setCellValue('A' . $row, $studentData['student']->nombres . ' ' . $studentData['student']->apellidos);
            $sheet->setCellValue('B' . $row, $studentData['average']);
            $sheet->setCellValue('C' . $row, $studentData['total_grades']);
            $row++;
        }

        // Autoajustar columnas
        foreach (range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Crear el archivo Excel
        $filename = 'estadisticas_calificaciones_materia_' . date('Y-m-d_H-i-s') . '.xlsx';

        return new StreamedResponse(function() use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function render()
    {
        return view('livewire.admin.reportes.estadisticas-calificaciones-materia')
            ->layout($this->getLayout());
    }

    public function getPageTitle()
    {
        return 'Estadísticas de Calificaciones por Materia';
    }

    public function getBreadcrumb()
    {
        return [
            ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
            ['title' => 'Reportes', 'route' => 'admin.reportes'],
            ['title' => 'Estadísticas por Materia', 'route' => 'admin.reportes.estadisticas-materia'],
        ];
    }
}