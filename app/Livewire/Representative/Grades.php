<?php

namespace App\Livewire\Representative;

use Livewire\Component;
use App\Models\Student;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\EvaluationPeriod;
use App\Models\SchoolPeriod;
use Illuminate\Support\Facades\Auth;

class Grades extends Component
{
    public $students = [];
    public $selectedStudentId = '';
    public $selectedStudent = null;
    public $subject_id = '';
    public $evaluation_period_id = '';
    public $activePeriod;
    public $gradesData = [];

    public function mount()
    {
        $user = Auth::user();
        $this->activePeriod = SchoolPeriod::where('is_active', true)->first();
        
        $this->students = Student::where('representante_email', $user->email)
            ->orWhere('correo_electronico', $user->email)
            ->get();
        
        if ($this->students->count() > 0) {
            $this->selectedStudentId = $this->students->first()->id;
            $this->selectedStudent = $this->students->first();
            $this->loadGrades();
        }
    }

    public function updatedSelectedStudentId()
    {
        $this->selectedStudent = $this->students->firstWhere('id', $this->selectedStudentId);
        $this->loadGrades();
    }

    public function updatedSubjectId()
    {
        $this->loadGrades();
    }

    public function updatedEvaluationPeriodId()
    {
        $this->loadGrades();
    }

    public function loadGrades()
    {
        if (!$this->selectedStudent) {
            $this->gradesData = [];
            return;
        }

        $query = Grade::with(['evaluation.subject', 'evaluation.evaluationPeriod', 'evaluation.evaluationType'])
            ->where('student_id', $this->selectedStudent->id)
            ->where('status', 'graded');

        if ($this->activePeriod) {
            $query->whereHas('evaluation', fn($q) => $q->where('school_period_id', $this->activePeriod->id));
        }

        if ($this->subject_id) {
            $query->whereHas('evaluation', fn($q) => $q->where('subject_id', $this->subject_id));
        }

        if ($this->evaluation_period_id) {
            $query->whereHas('evaluation', fn($q) => $q->where('evaluation_period_id', $this->evaluation_period_id));
        }

        $grades = $query->orderBy('graded_at', 'desc')->get();

        // Agrupar por materia
        $this->gradesData = $grades->groupBy(fn($g) => $g->evaluation->subject->name ?? 'Sin Materia')
            ->map(function($subjectGrades, $subjectName) {
                return [
                    'subject' => $subjectName,
                    'grades' => $subjectGrades,
                    'average' => round($subjectGrades->avg('score'), 2),
                    'count' => $subjectGrades->count(),
                ];
            })->values()->toArray();
    }

    public function render()
    {
        $subjects = Subject::active()->orderBy('name')->get();
        $evaluationPeriods = EvaluationPeriod::active()->orderBy('number')->get();

        return view('livewire.representative.grades', compact('subjects', 'evaluationPeriods'))
            ->layout('layouts.representative');
    }
}
