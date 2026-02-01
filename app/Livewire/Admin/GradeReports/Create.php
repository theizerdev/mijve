<?php

namespace App\Livewire\Admin\GradeReports;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\GradeReport;
use App\Models\Grade;
use App\Models\Section;
use App\Models\Subject;
use App\Models\SchoolPeriod;
use App\Models\EvaluationPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Create extends Component
{
    use HasDynamicLayout;

    public $school_period_id = '';
    public $section_id = '';
    public $subject_id = '';
    public $evaluation_period_id = '';
    public $report_type = 'period';
    public $title = '';
    public $description = '';
    public $observations = '';

    public $previewData = null;

    protected $rules = [
        'school_period_id' => 'required|exists:school_periods,id',
        'section_id' => 'required|exists:sections,id',
        'report_type' => 'required|in:period,final,recovery',
        'title' => 'required|min:5',
    ];

    public function mount()
    {
        $activePeriod = SchoolPeriod::where('is_active', true)->first();
        if ($activePeriod) {
            $this->school_period_id = $activePeriod->id;
        }
    }

    public function generatePreview()
    {
        $this->validate([
            'school_period_id' => 'required',
            'section_id' => 'required',
        ]);

        // Obtener calificaciones según los filtros
        $query = Grade::with(['student', 'evaluation.subject'])
            ->whereHas('evaluation', function($q) {
                $q->where('school_period_id', $this->school_period_id);
                if ($this->subject_id) {
                    $q->where('subject_id', $this->subject_id);
                }
                if ($this->evaluation_period_id) {
                    $q->where('evaluation_period_id', $this->evaluation_period_id);
                }
            })
            ->whereHas('student.sections', function($q) {
                $q->where('sections.id', $this->section_id);
            })
            ->where('status', 'graded');

        $grades = $query->get();

        // Agrupar por estudiante
        $studentGrades = $grades->groupBy('student_id')->map(function($studentGrades) {
            $student = $studentGrades->first()->student;
            $avgScore = $studentGrades->avg('score');
            $maxScore = $studentGrades->first()->evaluation->max_score ?? 20;
            
            return [
                'student_id' => $student->id,
                'codigo' => $student->codigo,
                'nombres' => $student->nombres,
                'apellidos' => $student->apellidos,
                'grades' => $studentGrades->pluck('score')->toArray(),
                'average' => round($avgScore, 2),
                'status' => $avgScore >= ($maxScore / 2) ? 'approved' : 'failed',
            ];
        })->values()->toArray();

        // Calcular estadísticas
        $scores = collect($studentGrades)->pluck('average');
        $approved = collect($studentGrades)->where('status', 'approved')->count();
        $failed = collect($studentGrades)->where('status', 'failed')->count();

        $this->previewData = [
            'students' => $studentGrades,
            'statistics' => [
                'total_students' => count($studentGrades),
                'approved_count' => $approved,
                'failed_count' => $failed,
                'average_grade' => $scores->count() > 0 ? round($scores->avg(), 2) : 0,
                'highest_grade' => $scores->max() ?? 0,
                'lowest_grade' => $scores->min() ?? 0,
                'approval_rate' => count($studentGrades) > 0 ? round(($approved / count($studentGrades)) * 100, 1) : 0,
            ],
        ];

        // Generar título automático si está vacío
        if (empty($this->title)) {
            $section = Section::find($this->section_id);
            $period = SchoolPeriod::find($this->school_period_id);
            $evalPeriod = $this->evaluation_period_id ? EvaluationPeriod::find($this->evaluation_period_id) : null;
            
            $this->title = 'Acta de Notas - ' . ($section->nombre ?? '') . ' - ' . ($period->name ?? '');
            if ($evalPeriod) {
                $this->title .= ' - ' . $evalPeriod->name;
            }
        }
    }

    public function save()
    {
        $this->validate();

        if (!$this->previewData) {
            $this->generatePreview();
        }

        try {
            DB::beginTransaction();

            $user = Auth::user();

            $report = GradeReport::create([
                'empresa_id' => $user->empresa_id,
                'sucursal_id' => $user->sucursal_id,
                'school_period_id' => $this->school_period_id,
                'section_id' => $this->section_id,
                'subject_id' => $this->subject_id ?: null,
                'evaluation_period_id' => $this->evaluation_period_id ?: null,
                'report_number' => GradeReport::generateReportNumber(),
                'report_type' => $this->report_type,
                'title' => $this->title,
                'description' => $this->description,
                'grades_data' => $this->previewData['students'],
                'statistics' => $this->previewData['statistics'],
                'total_students' => $this->previewData['statistics']['total_students'],
                'approved_count' => $this->previewData['statistics']['approved_count'],
                'failed_count' => $this->previewData['statistics']['failed_count'],
                'average_grade' => $this->previewData['statistics']['average_grade'],
                'highest_grade' => $this->previewData['statistics']['highest_grade'],
                'lowest_grade' => $this->previewData['statistics']['lowest_grade'],
                'status' => 'generated',
                'generated_at' => now(),
                'generated_by' => $user->id,
                'observations' => $this->observations,
            ]);

            DB::commit();

            session()->flash('message', 'Acta generada correctamente. Número: ' . $report->report_number);
            return redirect()->route('admin.grade-reports.show', $report->id);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al generar acta: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $sections = Section::active()->with('nivelEducativo')->orderBy('nombre')->get();
        $subjects = Subject::active()->orderBy('name')->get();
        $schoolPeriods = SchoolPeriod::orderBy('year', 'desc')->get();
        $evaluationPeriods = EvaluationPeriod::active()->orderBy('number')->get();
        $types = GradeReport::getTypes();

        return view('livewire.admin.grade-reports.create', compact(
            'sections', 'subjects', 'schoolPeriods', 'evaluationPeriods', 'types'
        ))->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Generar Acta de Notas';
    }
}
