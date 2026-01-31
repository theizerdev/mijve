<?php

namespace App\Livewire\Admin\Reportes;

use App\Models\EvaluationPeriod;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Evaluation;
use App\Models\Grade;
use App\Models\Matricula;
use App\Models\Student;
use App\Traits\HasDynamicLayout;
use App\Traits\HasRegionalFormatting;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AsistenciaEvaluacionesExport;

class AsistenciaEvaluaciones extends Component
{
    use HasDynamicLayout, HasRegionalFormatting;

    public $evaluation_period_id = '';
    public $subject_id = '';
    public $teacher_id = '';
    public $evaluation_type_id = '';

    public $evaluationPeriods = [];
    public $subjects = [];
    public $teachers = [];
    public $evaluationTypes = [];
    public $attendanceData = [];
    public $statistics = [];

    public function mount()
    {
        $this->evaluationPeriods = EvaluationPeriod::where('is_active', true)
            ->orderBy('name')
            ->get();

        $this->loadSubjects();
        $this->loadTeachers();
        $this->loadEvaluationTypes();
    }

    public function loadSubjects()
    {
        $this->subjects = Subject::with('programa')
            
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function loadTeachers()
    {
        $this->teachers = Teacher::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function loadEvaluationTypes()
    {
        $this->evaluationTypes = \App\Models\EvaluationType::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function updatedSubjectId()
    {
        $this->calculateAttendance();
    }

    public function updatedEvaluationPeriodId()
    {
        $this->calculateAttendance();
    }

    public function updatedTeacherId()
    {
        $this->calculateAttendance();
    }

    public function updatedEvaluationTypeId()
    {
        $this->calculateAttendance();
    }

    public function calculateAttendance()
    {
        if (!$this->subject_id || !$this->evaluation_period_id) {
            $this->attendanceData = [];
            $this->statistics = [];
            return;
        }

        // Obtener evaluaciones con filtros
        $query = Evaluation::with(['grades.student', 'subject', 'teacher', 'evaluationType'])
            ->where('subject_id', $this->subject_id)
            ->where('evaluation_period_id', $this->evaluation_period_id)
            ;

        if ($this->teacher_id) {
            $query->where('teacher_id', $this->teacher_id);
        }

        if ($this->evaluation_type_id) {
            $query->where('evaluation_type_id', $this->evaluation_type_id);
        }

        $evaluations = $query->get();

        if ($evaluations->isEmpty()) {
            $this->attendanceData = [];
            $this->statistics = [];
            return;
        }

        $evaluationIds = $evaluations->pluck('id');

        // Obtener calificaciones con información de asistencia
        $grades = Grade::with(['student', 'evaluation'])
            ->whereIn('evaluation_id', $evaluationIds)
            ->get();

        if ($grades->isEmpty()) {
            $this->attendanceData = [];
            $this->statistics = [];
            return;
        }

        // Procesar datos de asistencia
        $attendanceData = [];
        $presentCount = 0;
        $absentCount = 0;
        $exemptCount = 0;
        $totalEvaluations = $evaluations->count();

        foreach ($grades->groupBy('student_id') as $studentId => $studentGrades) {
            $student = $studentGrades->first()->student;
            $studentEvaluations = [];
            $attendanceCount = 0;
            $absenceCount = 0;
            $exemptionCount = 0;

            foreach ($evaluations as $evaluation) {
                $grade = $studentGrades->where('evaluation_id', $evaluation->id)->first();
                
                if ($grade) {
                    $status = $grade->status;
                    $score = $grade->score;
                    
                    switch ($status) {
                        case 'graded':
                            $attendanceCount++;
                            $presentCount++;
                            break;
                        case 'absent':
                            $absenceCount++;
                            $absentCount++;
                            break;
                        case 'exempt':
                            $exemptionCount++;
                            $exemptCount++;
                            break;
                    }

                    $studentEvaluations[] = [
                        'evaluation' => $evaluation,
                        'status' => $status,
                        'score' => $score,
                        'attendance' => $this->getAttendanceStatus($status)
                    ];
                } else {
                    $studentEvaluations[] = [
                        'evaluation' => $evaluation,
                        'status' => 'not_registered',
                        'score' => null,
                        'attendance' => 'No registrado'
                    ];
                }
            }

            $attendanceRate = $totalEvaluations > 0 ? round(($attendanceCount / $totalEvaluations) * 100, 1) : 0;

            $attendanceData[] = [
                'student' => $student,
                'evaluations' => $studentEvaluations,
                'attendance_count' => $attendanceCount,
                'absence_count' => $absenceCount,
                'exemption_count' => $exemptionCount,
                'attendance_rate' => $attendanceRate,
                'total_evaluations' => $totalEvaluations
            ];
        }

        // Calcular estadísticas generales
        $totalStudents = count($attendanceData);
        $totalPossibleEvaluations = $totalStudents * $totalEvaluations;
        $overallAttendanceRate = $totalPossibleEvaluations > 0 ? round(($presentCount / $totalPossibleEvaluations) * 100, 1) : 0;

        $subject = Subject::with('programa')->find($this->subject_id);
        $evaluationPeriod = EvaluationPeriod::find($this->evaluation_period_id);

        $this->statistics = [
            'subject_name' => $subject->nombre . ' - ' . $subject->programa->nombre,
            'period_name' => $evaluationPeriod->name,
            'total_students' => $totalStudents,
            'total_evaluations' => $totalEvaluations,
            'total_present' => $presentCount,
            'total_absent' => $absentCount,
            'total_exempt' => $exemptCount,
            'overall_attendance_rate' => $overallAttendanceRate,
            'teacher_name' => $this->teacher_id ? Teacher::find($this->teacher_id)->name : 'Todos los docentes'
        ];

        $this->attendanceData = $attendanceData;
    }

    private function getAttendanceStatus($status)
    {
        switch ($status) {
            case 'graded':
                return 'Presente';
            case 'absent':
                return 'Ausente';
            case 'exempt':
                return 'Exento';
            default:
                return 'No registrado';
        }
    }

    public function exportarExcel()
    {
        if (empty($this->statistics)) {
            session()->flash('error', 'No hay datos para exportar.');
            return;
        }

        try {
            $data = [
                'statistics' => $this->statistics,
                'attendanceData' => $this->attendanceData,
                'evaluation_period_id' => $this->evaluation_period_id,
                'subject_id' => $this->subject_id,
                'teacher_id' => $this->teacher_id,
                'evaluation_type_id' => $this->evaluation_type_id,
            ];

            $fileName = 'asistencia_evaluaciones_' . 
                       str_replace(' ', '_', $this->statistics['subject_name']) . '_' . 
                       date('Y-m-d_H-i-s') . '.xlsx';

            return Excel::download(new AsistenciaEvaluacionesExport($data), $fileName);

        } catch (\Exception $e) {
            session()->flash('error', 'Error al exportar el reporte: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.reportes.asistencia-evaluaciones')
            ->layout($this->getLayout());
    }

    public function getPageTitle(): string
    {
        return 'Reporte de Asistencia y Evaluaciones';
    }

    public function getBreadcrumb(): array
    {
        return [
            ['name' => 'Dashboard', 'route' => route('admin.dashboard')],
            ['name' => 'Reportes', 'route' => '#'],
            ['name' => 'Asistencia y Evaluaciones', 'route' => ''],
        ];
    }
}