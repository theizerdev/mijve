<?php

namespace App\Livewire\Teacher;

use Livewire\Component;
use App\Models\Teacher;
use App\Models\Evaluation;
use App\Models\Grade;
use App\Models\Section;
use App\Models\Subject;
use App\Models\SchoolPeriod;
use App\Models\EvaluationPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GradeEntry extends Component
{
    public $teacher;
    public $subject_id = '';
    public $section_id = '';
    public $evaluation_id = '';
    public $students = [];
    public $grades = [];

    public function mount()
    {
        $user = Auth::user();
        $this->teacher = Teacher::where('user_id', $user->id)->first();
    }

    public function updatedSubjectId()
    {
        $this->section_id = '';
        $this->evaluation_id = '';
        $this->students = [];
        $this->grades = [];
    }

    public function updatedSectionId()
    {
        $this->evaluation_id = '';
        $this->loadStudents();
    }

    public function updatedEvaluationId()
    {
        $this->loadGrades();
    }

    public function loadStudents()
    {
        if (!$this->section_id) {
            $this->students = [];
            return;
        }

        $section = Section::with(['students' => function($q) {
            $q->wherePivot('estado', 'activo')->orderBy('apellidos')->orderBy('nombres');
        }])->find($this->section_id);

        $this->students = $section ? $section->students : collect();
    }

    public function loadGrades()
    {
        if (!$this->evaluation_id || $this->students->isEmpty()) {
            $this->grades = [];
            return;
        }

        $existingGrades = Grade::where('evaluation_id', $this->evaluation_id)
            ->whereIn('student_id', $this->students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        $this->grades = [];
        foreach ($this->students as $student) {
            $grade = $existingGrades->get($student->id);
            $this->grades[$student->id] = [
                'score' => $grade ? $grade->score : '',
                'status' => $grade ? $grade->status : 'pending',
                'observations' => $grade ? $grade->observations : '',
            ];
        }
    }

    public function save()
    {
        if (!$this->evaluation_id) {
            session()->flash('error', 'Seleccione una evaluación.');
            return;
        }

        try {
            DB::beginTransaction();

            $evaluation = Evaluation::findOrFail($this->evaluation_id);
            $user = Auth::user();

            foreach ($this->grades as $studentId => $gradeData) {
                $score = $gradeData['score'];
                $status = 'pending';

                if ($score !== '' && $score !== null) {
                    $status = 'graded';
                } elseif ($gradeData['status'] === 'absent') {
                    $status = 'absent';
                } elseif ($gradeData['status'] === 'exempt') {
                    $status = 'exempt';
                }

                Grade::updateOrCreate(
                    [
                        'evaluation_id' => $this->evaluation_id,
                        'student_id' => $studentId,
                    ],
                    [
                        'empresa_id' => $user->empresa_id,
                        'sucursal_id' => $user->sucursal_id,
                        'score' => $score !== '' ? $score : null,
                        'status' => $status,
                        'observations' => $gradeData['observations'] ?? null,
                        'graded_by' => $user->id,
                        'graded_at' => now(),
                    ]
                );
            }

            DB::commit();
            session()->flash('message', 'Calificaciones guardadas correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $subjects = $this->teacher ? $this->teacher->subjects()->orderBy('name')->get() : collect();
        
        $sections = collect();
        if ($this->subject_id && $this->teacher) {
            $activePeriod = SchoolPeriod::where('is_active', true)->first();
            $sections = Section::whereHas('schedules', function($q) {
                $q->where('subject_id', $this->subject_id);
            })->when($activePeriod, fn($q) => $q->where('periodo_escolar_id', $activePeriod->id))
            ->orderBy('nombre')
            ->get();
        }

        $evaluations = collect();
        if ($this->subject_id && $this->section_id) {
            $evaluations = Evaluation::where('subject_id', $this->subject_id)
                ->with('evaluationPeriod')
                ->orderBy('date', 'desc')
                ->get();
        }

        return view('livewire.teacher.grade-entry', compact('subjects', 'sections', 'evaluations'))
            ->layout('layouts.teacher');
    }
}
