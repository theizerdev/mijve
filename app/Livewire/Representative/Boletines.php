<?php

namespace App\Livewire\Representative;

use Livewire\Component;
use App\Models\Student;
use App\Models\Grade;
use App\Models\GradeSummary;
use App\Models\EvaluationPeriod;
use App\Models\SchoolPeriod;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class Boletines extends Component
{
    public $students = [];
    public $selectedStudentId = '';
    public $selectedStudent = null;
    public $school_period_id = '';
    public $evaluation_period_id = '';
    public $activePeriod;
    public $boletinData = [];

    public function mount()
    {
        $user = Auth::user();
        $this->activePeriod = SchoolPeriod::where('is_active', true)->first();
        $this->school_period_id = $this->activePeriod?->id ?? '';
        
        $this->students = Student::where('representante_email', $user->email)
            ->orWhere('correo_electronico', $user->email)
            ->get();
        
        if ($this->students->count() > 0) {
            $this->selectedStudentId = $this->students->first()->id;
            $this->selectedStudent = $this->students->first();
        }
    }

    public function updatedSelectedStudentId()
    {
        $this->selectedStudent = $this->students->firstWhere('id', $this->selectedStudentId);
        $this->loadBoletin();
    }

    public function updatedSchoolPeriodId()
    {
        $this->loadBoletin();
    }

    public function updatedEvaluationPeriodId()
    {
        $this->loadBoletin();
    }

    public function loadBoletin()
    {
        if (!$this->selectedStudent || !$this->school_period_id) {
            $this->boletinData = [];
            return;
        }

        // Obtener calificaciones agrupadas por materia
        $query = Grade::with(['evaluation.subject', 'evaluation.evaluationPeriod', 'evaluation.evaluationType'])
            ->where('student_id', $this->selectedStudent->id)
            ->where('status', 'graded')
            ->whereHas('evaluation', fn($q) => $q->where('school_period_id', $this->school_period_id));

        if ($this->evaluation_period_id) {
            $query->whereHas('evaluation', fn($q) => $q->where('evaluation_period_id', $this->evaluation_period_id));
        }

        $grades = $query->get();

        // Agrupar por materia y calcular promedios
        $this->boletinData = $grades->groupBy(fn($g) => $g->evaluation->subject_id)
            ->map(function($subjectGrades) {
                $subject = $subjectGrades->first()->evaluation->subject;
                
                // Agrupar por período de evaluación
                $byPeriod = $subjectGrades->groupBy(fn($g) => $g->evaluation->evaluation_period_id);
                
                $periodAverages = $byPeriod->map(function($periodGrades, $periodId) {
                    $period = $periodGrades->first()->evaluation->evaluationPeriod;
                    return [
                        'period_id' => $periodId,
                        'period_name' => $period?->name ?? 'Sin Lapso',
                        'average' => round($periodGrades->avg('score'), 2),
                        'grades' => $periodGrades,
                    ];
                });

                return [
                    'subject_id' => $subject->id,
                    'subject_name' => $subject->name,
                    'periods' => $periodAverages->values()->toArray(),
                    'final_average' => round($subjectGrades->avg('score'), 2),
                ];
            })->values()->toArray();
    }

    public function downloadPdf()
    {
        if (empty($this->boletinData)) {
            session()->flash('error', 'No hay datos para generar el boletín.');
            return;
        }

        $schoolPeriod = SchoolPeriod::find($this->school_period_id);
        $evaluationPeriod = $this->evaluation_period_id ? EvaluationPeriod::find($this->evaluation_period_id) : null;

        $pdf = Pdf::loadView('pdf.boletin-representante', [
            'student' => $this->selectedStudent,
            'boletinData' => $this->boletinData,
            'schoolPeriod' => $schoolPeriod,
            'evaluationPeriod' => $evaluationPeriod,
        ]);

        $filename = 'boletin-' . $this->selectedStudent->codigo . '-' . now()->format('Y-m-d') . '.pdf';
        
        return response()->streamDownload(fn() => print($pdf->output()), $filename);
    }

    public function render()
    {
        $schoolPeriods = SchoolPeriod::orderBy('year', 'desc')->get();
        $evaluationPeriods = EvaluationPeriod::active()->orderBy('number')->get();

        return view('livewire.representative.boletines', compact('schoolPeriods', 'evaluationPeriods'))
            ->layout('layouts.representative');
    }
}
