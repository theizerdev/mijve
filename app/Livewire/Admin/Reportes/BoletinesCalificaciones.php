<?php

namespace App\Livewire\Admin\Reportes;

use App\Models\Student;
use App\Models\EvaluationPeriod;
use App\Models\Programa;
use App\Models\Grade;
use App\Models\Evaluation;
use App\Models\Subject;
use App\Models\Teacher;
use App\Traits\HasDynamicLayout;
use App\Traits\HasRegionalFormatting;
use Livewire\Component;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BoletinesCalificaciones extends Component
{
    use HasDynamicLayout, HasRegionalFormatting;

    public $student_id = '';
    public $evaluation_period_id = '';
    public $program_id = '';

    public $students = [];
    public $evaluationPeriods = [];
    public $programs = [];
    public $boletinData = [];

    public function mount()
    {
        $this->evaluationPeriods = EvaluationPeriod::where('is_active', true)
            ->orderBy('start_date', 'desc')
            ->get();

        $this->programs = Programa::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $this->loadStudents();
    }

    public function loadStudents()
    {
        $query = Student::where('status', true);

        if ($this->program_id) {
            $query->where('program_id', $this->program_id);
        }

        $this->students = $query->orderBy('nombres')
            ->orderBy('apellidos')
            ->get();
    }

    public function updatedStudentId()
    {
        $this->generateBoletin();
    }

    public function updatedEvaluationPeriodId()
    {
        $this->generateBoletin();
    }

    public function updatedProgramId()
    {
        $this->loadStudents();
        $this->generateBoletin();
    }

    public function generateBoletin()
    {
        if (!$this->student_id || !$this->evaluation_period_id) {
            $this->boletinData = [];
            return;
        }

        // Obtener información del estudiante
        $student = Student::with(['program'])->find($this->student_id);
        $evaluationPeriod = EvaluationPeriod::find($this->evaluation_period_id);

        if (!$student || !$evaluationPeriod) {
            $this->boletinData = [];
            return;
        }

        // Obtener calificaciones del estudiante en el período
        $grades = Grade::with(['evaluation.subject', 'evaluation.teacher', 'evaluation.evaluationType'])
            ->where('student_id', $this->student_id)
            ->whereHas('evaluation', function($query) {
                $query->where('evaluation_period_id', $this->evaluation_period_id)
                      ->where('is_published', true);
            })
            ->get();

        if ($grades->isEmpty()) {
            $this->boletinData = [
                'student_name' => $student->nombres . ' ' . $student->apellidos,
                'student_code' => $student->codigo,
                'program_name' => $student->program->nombre,
                'period_name' => $evaluationPeriod->name,
                'grades' => [],
                'overall_average' => '0.00',
                'total_evaluations' => 0,
                'approved_count' => 0,
                'failed_count' => 0,
                'observations' => ['No hay calificaciones registradas para este período.']
            ];
            return;
        }

        // Procesar calificaciones por materia
        $processedGrades = [];
        $totalScore = 0;
        $gradedCount = 0;
        $approvedCount = 0;
        $failedCount = 0;
        $observations = [];

        foreach ($grades as $grade) {
            $evaluation = $grade->evaluation;
            $subject = $evaluation->subject;
            $teacher = $evaluation->teacher;
            $evaluationType = $evaluation->evaluationType;

            $processedGrade = [
                'subject_name' => $subject->nombre,
                'subject_description' => $subject->descripcion,
                'teacher_name' => $teacher->name,
                'evaluation_type' => $evaluationType->name,
                'evaluation_date' => $evaluation->date->format('d/m/Y'),
                'score' => $grade->score,
                'status' => $grade->status,
                'evaluation_name' => $evaluation->name
            ];

            $processedGrades[] = $processedGrade;

            // Calcular promedio solo para calificaciones válidas
            if ($grade->status === 'graded' && $grade->score !== null) {
                $totalScore += $grade->score;
                $gradedCount++;

                if ($grade->score >= 10) {
                    $approvedCount++;
                } else {
                    $failedCount++;
                }
            } elseif ($grade->status === 'absent') {
                $observations[] = "Ausente en evaluación: {$evaluation->name} ({$subject->nombre})";
            } elseif ($grade->status === 'exempt') {
                $observations[] = "Exento de evaluación: {$evaluation->name} ({$subject->nombre})";
            }
        }

        // Calcular promedio general
        $overallAverage = $gradedCount > 0 ? round($totalScore / $gradedCount, 2) : 0;

        // Generar observaciones adicionales
        if ($overallAverage < 10) {
            $observations[] = "Promedio general bajo ({$overallAverage}). Se recomienda seguimiento académico.";
        }

        if ($failedCount > 0) {
            $observations[] = "Tiene {$failedCount} evaluaciones reprobadas. Se sugiere reforzar en estas áreas.";
        }

        if ($approvedCount === count($processedGrades)) {
            $observations[] = "Excelente desempeño: todas las evaluaciones aprobadas.";
        }

        $this->boletinData = [
            'student_name' => $student->nombres . ' ' . $student->apellidos,
            'student_code' => $student->codigo,
            'program_name' => $student->program->nombre,
            'period_name' => $evaluationPeriod->name,
            'grades' => $processedGrades,
            'overall_average' => number_format($overallAverage, 2),
            'total_evaluations' => count($processedGrades),
            'approved_count' => $approvedCount,
            'failed_count' => $failedCount,
            'observations' => $observations
        ];
    }

    public function imprimirBoletin()
    {
        if (empty($this->boletinData)) {
            session()->flash('error', 'No hay datos para imprimir.');
            return;
        }

        // Abrir en una nueva ventana para impresión
        $this->dispatch('openPrintWindow', [
            'student_id' => $this->student_id,
            'evaluation_period_id' => $this->evaluation_period_id
        ]);
    }

    public function exportarExcel()
    {
        if (empty($this->boletinData)) {
            session()->flash('error', 'No hay datos para exportar.');
            return;
        }

        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Configurar página
            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
            $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

            // Información del boletín
            $sheet->setCellValue('A1', 'BOLETÍN DE CALIFICACIONES');
            $sheet->mergeCells('A1:F1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(18)->getColor()->setRGB('2E3B4E');
            $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

            $sheet->setCellValue('A3', 'Estudiante: ' . $this->boletinData['student_name']);
            $sheet->setCellValue('A4', 'Código: ' . $this->boletinData['student_code']);
            $sheet->setCellValue('A5', 'Programa: ' . $this->boletinData['program_name']);
            $sheet->setCellValue('A6', 'Período: ' . $this->boletinData['period_name']);
            $sheet->setCellValue('A7', 'Generado el: ' . now()->format('d/m/Y H:i'));

            // Resumen del período
            $sheet->setCellValue('A9', 'RESUMEN DEL PERÍODO');
            $sheet->mergeCells('A9:F9');
            $sheet->getStyle('A9')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $sheet->getStyle('A9')->getFill()->setFillType('solid')->getStartColor()->setRGB('4472C4');

            $sheet->setCellValue('A10', 'Total Evaluaciones: ' . $this->boletinData['total_evaluations']);
            $sheet->setCellValue('B10', 'Aprobadas: ' . $this->boletinData['approved_count']);
            $sheet->setCellValue('C10', 'Reprobadas: ' . $this->boletinData['failed_count']);
            $sheet->setCellValue('D10', 'Promedio General: ' . $this->boletinData['overall_average']);

            // Tabla de calificaciones
            $row = 12;
            $sheet->setCellValue('A' . $row, 'DETALLE DE CALIFICACIONES');
            $sheet->mergeCells('A' . $row . ':F' . $row);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $sheet->getStyle('A' . $row)->getFill()->setFillType('solid')->getStartColor()->setRGB('70AD47');

            $row++;
            $sheet->setCellValue('A' . $row, 'Materia');
            $sheet->setCellValue('B' . $row, 'Docente');
            $sheet->setCellValue('C' . $row, 'Tipo Evaluación');
            $sheet->setCellValue('D' . $row, 'Calificación');
            $sheet->setCellValue('E' . $row, 'Estado');
            $sheet->setCellValue('F' . $row, 'Fecha');
            $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setBold(true);

            $row++;
            foreach ($this->boletinData['grades'] as $grade) {
                $sheet->setCellValue('A' . $row, $grade['subject_name']);
                $sheet->setCellValue('B' . $row, $grade['teacher_name']);
                $sheet->setCellValue('C' . $row, $grade['evaluation_type']);
                $sheet->setCellValue('D' . $row, $grade['score'] ?? 'N/A');
                
                $statusText = ucfirst($grade['status']);
                if ($grade['status'] == 'graded') {
                    $statusText = $grade['score'] >= 10 ? 'Aprobado' : 'Reprobado';
                }
                $sheet->setCellValue('E' . $row, $statusText);
                $sheet->setCellValue('F' . $row, $grade['evaluation_date']);
                $row++;
            }

            // Observaciones
            if (!empty($this->boletinData['observations'])) {
                $row += 2;
                $sheet->setCellValue('A' . $row, 'OBSERVACIONES');
                $sheet->mergeCells('A' . $row . ':F' . $row);
                $sheet->getStyle('A' . $row)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
                $sheet->getStyle('A' . $row)->getFill()->setFillType('solid')->getStartColor()->setRGB('FFC000');

                $row++;
                foreach ($this->boletinData['observations'] as $observation) {
                    $sheet->setCellValue('A' . $row, '• ' . $observation);
                    $sheet->mergeCells('A' . $row . ':F' . $row);
                    $row++;
                }
            }

            // Autoajustar columnas
            foreach (range('A', 'F') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            // Crear el archivo Excel
            $filename = 'boletin_' . str_replace(' ', '_', $this->boletinData['student_name']) . '_' . 
                       str_replace(' ', '_', $this->boletinData['period_name']) . '_' . 
                       date('Y-m-d') . '.xlsx';

            return new StreamedResponse(function() use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            }, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'max-age=0',
            ]);

        } catch (\Exception $e) {
            session()->flash('error', 'Error al exportar el boletín: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.reportes.boletines-calificaciones')
            ->layout($this->getLayout());
    }

    public function getPageTitle(): string
    {
        return 'Boletines de Calificaciones';
    }

    public function getBreadcrumb(): array
    {
        return [
            ['name' => 'Dashboard', 'route' => route('admin.dashboard')],
            ['name' => 'Reportes', 'route' => '#'],
            ['name' => 'Boletines de Calificaciones', 'route' => ''],
        ];
    }
}