<?php

namespace App\Livewire\Teacher;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Teacher;
use App\Models\Evaluation;
use App\Models\Subject;
use App\Models\EvaluationPeriod;
use App\Models\EvaluationType;
use App\Models\SchoolPeriod;
use Illuminate\Support\Facades\Auth;

class MyEvaluations extends Component
{
    use WithPagination;

    public $teacher;
    public $subject_id = '';
    public $evaluation_period_id = '';
    
    // Para crear evaluación
    public $showCreateModal = false;
    public $newEvaluation = [
        'subject_id' => '',
        'evaluation_period_id' => '',
        'evaluation_type_id' => '',
        'name' => '',
        'description' => '',
        'date' => '',
        'max_score' => 20,
        'weight' => 100,
    ];

    public function mount()
    {
        $user = Auth::user();
        $this->teacher = Teacher::where('user_id', $user->id)->first();
        $this->newEvaluation['date'] = now()->format('Y-m-d');
    }

    public function openCreateModal()
    {
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetNewEvaluation();
    }

    public function resetNewEvaluation()
    {
        $this->newEvaluation = [
            'subject_id' => '',
            'evaluation_period_id' => '',
            'evaluation_type_id' => '',
            'name' => '',
            'description' => '',
            'date' => now()->format('Y-m-d'),
            'max_score' => 20,
            'weight' => 100,
        ];
    }

    public function createEvaluation()
    {
        $this->validate([
            'newEvaluation.subject_id' => 'required|exists:subjects,id',
            'newEvaluation.evaluation_period_id' => 'required|exists:evaluation_periods,id',
            'newEvaluation.evaluation_type_id' => 'required|exists:evaluation_types,id',
            'newEvaluation.name' => 'required|min:3',
            'newEvaluation.date' => 'required|date',
            'newEvaluation.max_score' => 'required|numeric|min:1',
        ]);

        $user = Auth::user();
        $activePeriod = SchoolPeriod::where('is_active', true)->first();

        Evaluation::create([
            'empresa_id' => $user->empresa_id,
            'sucursal_id' => $user->sucursal_id,
            'school_period_id' => $activePeriod?->id,
            'subject_id' => $this->newEvaluation['subject_id'],
            'evaluation_period_id' => $this->newEvaluation['evaluation_period_id'],
            'evaluation_type_id' => $this->newEvaluation['evaluation_type_id'],
            'name' => $this->newEvaluation['name'],
            'description' => $this->newEvaluation['description'],
            'date' => $this->newEvaluation['date'],
            'max_score' => $this->newEvaluation['max_score'],
            'weight' => $this->newEvaluation['weight'],
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        session()->flash('message', 'Evaluación creada correctamente.');
        $this->closeCreateModal();
    }

    public function render()
    {
        $subjectIds = $this->teacher ? $this->teacher->subjects()->pluck('subjects.id') : collect();

        $evaluations = Evaluation::with(['subject', 'evaluationPeriod', 'evaluationType'])
            ->whereIn('subject_id', $subjectIds)
            ->when($this->subject_id, fn($q) => $q->where('subject_id', $this->subject_id))
            ->when($this->evaluation_period_id, fn($q) => $q->where('evaluation_period_id', $this->evaluation_period_id))
            ->orderBy('date', 'desc')
            ->paginate(15);

        $subjects = $this->teacher ? $this->teacher->subjects()->orderBy('name')->get() : collect();
        $evaluationPeriods = EvaluationPeriod::active()->orderBy('number')->get();
        $evaluationTypes = EvaluationType::where('is_active', true)->orderBy('name')->get();

        return view('livewire.teacher.my-evaluations', compact('evaluations', 'subjects', 'evaluationPeriods', 'evaluationTypes'))
            ->layout('layouts.teacher');
    }
}
