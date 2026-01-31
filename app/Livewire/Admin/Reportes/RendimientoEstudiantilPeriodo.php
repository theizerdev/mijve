<?php

namespace App\Livewire\Admin\Reportes;

use App\Traits\HasDynamicLayout;
use App\Traits\HasRegionalFormatting;
use Livewire\Component;
use App\Models\Student;
use App\Models\Matricula;
use App\Models\Grade;
use App\Models\EvaluationPeriod;
use App\Models\Subject;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RendimientoEstudiantilPeriodo extends Component
{
    use HasDynamicLayout, HasRegionalFormatting;

    public $students = [];
    public $evaluation_period_id;
    public $subject_id;
    public $program_id;
    public $evaluationPeriods = [];
    public $subjects = [];
    public $programs = [];
    public $reportData = [];
    public $statistics = [];

    public function mount()
    {
        $this->evaluationPeriods = EvaluationPeriod::where('empresa_id', session('empresa_id'))
            ->where('is_active', true)
            ->orderBy('start_date', 'desc')
            ->get();

        $this->programs = \App\Models\Programa::with('nivelEducativo')
            ->orderBy('nombre')
            ->get();

        $this->subjects = collect();
        $this->reportData = collect();
    }

    public function updatedProgramId()
    {
        if ($this->program_id) {
            $this->subjects = Subject::where('program_id', $this->program_id)
                ->orderBy('name')
                ->get();
        } else {
            $this->subjects = collect();
        }
        
        $this->subject_id = null;
        $this->reportData = collect();
        $this->statistics = [];
    }

    public function updatedEvaluationPeriodId()
    {
        $this->generateReport();
    }

    public function updatedSubjectId()
    {
        $this->generateReport();
    }

    public function generateReport()
    {
        if (!$this->evaluation_period_id || !$this->program_id) {
            return;
        }

        $query = Matricula::with(['estudiante', 'programa'])
            ->where('sucursal_id', session('sucursal_id'))
            ->where('programa_id', $this->program_id)
            ->where('estado', 'activo')
            ->whereHas('estudiante', function($q) {
                $q->where('status', 1);
            });

        if ($this->subject_id) {
            $query->whereHas('estudiante.grades.evaluation', function($q) {
                $q->where('subject_id', $this->subject_id)
                  ->where('evaluation_period_id', $this->evaluation_period_id);
            });
        }

        $matriculas = $query->get();

        $this->reportData = collect();
        $totalGrades = 0;
        $approvedCount = 0;
        $failedCount = 0;
        $sumGrades = 0;

        foreach ($matriculas as $matricula) {
            $student = $matricula->estudiante;
            
            if ($this->subject_id) {
                // Reporte por materia específica
                $grades = Grade::with(['evaluation'])
                    ->where('student_id', $student->id)
                    ->whereHas('evaluation', function($q) {
                        $q->where('evaluation_period_id', $this->evaluation_period_id)
                          ->where('subject_id', $this->subject_id);
                    })
                    ->get();
            } else {
                // Reporte por todas las materias del período
                $grades = Grade::with(['evaluation.subject'])
                    ->where('student_id', $student->id)
                    ->whereHas('evaluation', function($q) {
                        $q->where('evaluation_period_id', $this->evaluation_period_id);
                    })
                    ->get();
            }

            if ($grades->isEmpty()) {
                continue;
            }

            $average = $this->calculateAverage($grades);
            $status = $this->determineStatus($average);
            
            if ($status === 'Aprobado') {
                $approvedCount++;
            } else {
                $failedCount++;
            }

            $totalGrades++;
            $sumGrades += $average;

            $this->reportData->push([
                'student' => $student,
                'matricula' => $matricula,
                'grades' => $grades,
                'average' => $average,
                'status' => $status,
                'grade_count' => $grades->count()
            ]);
        }

        // Calcular estadísticas
        $this->statistics = [
            'total_students' => $totalGrades,
            'approved_count' => $approvedCount,
            'failed_count' => $failedCount,
            'approval_rate' => $totalGrades > 0 ? round(($approvedCount / $totalGrades) * 100, 2) : 0,
            'average_grade' => $totalGrades > 0 ? round($sumGrades / $totalGrades, 2) : 0,
            'highest_average' => $this->reportData->max('average') ?? 0,
            'lowest_average' => $this->reportData->min('average') ?? 0
        ];
    }

    private function calculateAverage($grades)
    {
        if ($grades->isEmpty()) {
            return 0;
        }

        $validGrades = $grades->where('status', 'graded')->whereNotNull('score');
        
        if ($validGrades->isEmpty()) {
            return 0;
        }

        return round($validGrades->avg('score'), 2);
    }

    private function determineStatus($average)
    {
        return $average >= 10 ? 'Aprobado' : 'Reprobado';
    }

    public function exportarExcel()
    {
        if ($this->reportData->isEmpty()) {
            session()->flash('error', 'No hay datos para exportar.');
            return;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Configurar página
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        // Información del reporte
        $periodName = EvaluationPeriod::find($this->evaluation_period_id)?->name ?? 'Todos los períodos';
        $programName = \App\Models\Program::find($this->program_id)?->nombre ?? 'Todos los programas';
        $subjectName = $this->subject_id ? Subject::find($this->subject_id)?->nombre : 'Todas las materias';

        // Encabezado principal
        $sheet->setCellValue('A1', 'REPORTE DE RENDIMIENTO ESTUDIANTIL');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->getColor()->setRGB('2E3B4E');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // Información del período
        $sheet->setCellValue('A3', 'Período: ' . $periodName);
        $sheet->setCellValue('A4', 'Programa: ' . $programName);
        $sheet->setCellValue('A5', 'Materia: ' . $subjectName);
        $sheet->setCellValue('A6', 'Generado el: ' . now()->format('d/m/Y H:i'));

        // Estadísticas
        $sheet->setCellValue('A8', 'ESTADÍSTICAS GENERALES');
        $sheet->mergeCells('A8:D8');
        $sheet->getStyle('A8')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A8')->getFill()->setFillType('solid')->getStartColor()->setRGB('4472C4');

        $sheet->setCellValue('A9', 'Total Estudiantes: ' . $this->statistics['total_students']);
        $sheet->setCellValue('B9', 'Aprobados: ' . $this->statistics['approved_count']);
        $sheet->setCellValue('C9', 'Reprobados: ' . $this->statistics['failed_count']);
        $sheet->setCellValue('D9', 'Tasa de Aprobación: ' . $this->statistics['approval_rate'] . '%');

        $sheet->setCellValue('A10', 'Promedio General: ' . $this->statistics['average_grade']);
        $sheet->setCellValue('B10', 'Promedio Más Alto: ' . $this->statistics['highest_average']);
        $sheet->setCellValue('C10', 'Promedio Más Bajo: ' . $this->statistics['lowest_average']);

        // Encabezados de la tabla
        $headers = ['Código', 'Nombre Completo', 'Programa', 'Materias Evaluadas', 'Promedio', 'Estado', 'Detalle'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '12', $header);
            $col++;
        }

        // Estilo de encabezados
        $sheet->getStyle('A12:G12')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A12:G12')->getFill()->setFillType('solid')->getStartColor()->setRGB('5B9BD5');
        $sheet->getStyle('A12:G12')->getAlignment()->setHorizontal('center');

        // Datos de los estudiantes
        $row = 13;
        foreach ($this->reportData as $data) {
            $student = $data['student'];
            $program = $data['matricula']->programa;

            $sheet->setCellValue('A' . $row, $student->codigo);
            $sheet->setCellValue('B' . $row, $student->nombres . ' ' . $student->apellidos);
            $sheet->setCellValue('C' . $row, $program->nombre);
            $sheet->setCellValue('D' . $row, $data['grade_count']);
            $sheet->setCellValue('E' . $row, $data['average']);
            $sheet->setCellValue('F' . $row, $data['status']);

            // Color condicional según estado
            if ($data['status'] === 'Aprobado') {
                $sheet->getStyle('F' . $row)->getFill()->setFillType('solid')->getStartColor()->setRGB('C6EFCE');
            } else {
                $sheet->getStyle('F' . $row)->getFill()->setFillType('solid')->getStartColor()->setRGB('FFC7CE');
            }

            $row++;
        }

        // Autoajustar columnas
        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Crear el archivo Excel
        $filename = 'rendimiento_estudiantil_' . date('Y-m-d_H-i-s') . '.xlsx';

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
        return view('livewire.admin.reportes.rendimiento-estudiantil-periodo')
            ->layout($this->getLayout());
    }

    public function getPageTitle()
    {
        return 'Reporte de Rendimiento Estudiantil por Período';
    }

    public function getBreadcrumb()
    {
        return [
            ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
            ['title' => 'Reportes', 'route' => 'admin.reportes'],
            ['title' => 'Rendimiento Estudiantil', 'route' => 'admin.reportes.rendimiento-periodo'],
        ];
    }
}