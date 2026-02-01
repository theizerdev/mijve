<?php

namespace App\Livewire\Teacher;

use Livewire\Component;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Section;
use App\Models\Evaluation;
use App\Models\Grade;
use App\Models\SchoolPeriod;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public $teacher;
    public $activePeriod;

    public function mount()
    {
        $user = Auth::user();
        $this->teacher = Teacher::where('user_id', $user->id)->first();
        $this->activePeriod = SchoolPeriod::where('is_active', true)->first();
    }

    public function getStatsProperty()
    {
        if (!$this->teacher) {
            return [
                'subjects' => 0,
                'sections' => 0,
                'students' => 0,
                'pending_grades' => 0,
                'evaluations' => 0,
            ];
        }

        $subjectIds = $this->teacher->subjects()->pluck('subjects.id');
        
        return [
            'subjects' => $subjectIds->count(),
            'sections' => Section::whereHas('schedules', function($q) use ($subjectIds) {
                $q->whereIn('subject_id', $subjectIds);
            })->count(),
            'students' => \App\Models\SectionStudent::whereHas('section.schedules', function($q) use ($subjectIds) {
                $q->whereIn('subject_id', $subjectIds);
            })->where('estado', 'activo')->count(),
            'pending_grades' => Grade::whereHas('evaluation', function($q) use ($subjectIds) {
                $q->whereIn('subject_id', $subjectIds);
            })->where('status', 'pending')->count(),
            'evaluations' => Evaluation::whereIn('subject_id', $subjectIds)
                ->when($this->activePeriod, fn($q) => $q->where('school_period_id', $this->activePeriod->id))
                ->count(),
        ];
    }

    public function getRecentEvaluationsProperty()
    {
        if (!$this->teacher) return collect();

        $subjectIds = $this->teacher->subjects()->pluck('subjects.id');
        
        return Evaluation::with(['subject', 'evaluationPeriod'])
            ->whereIn('subject_id', $subjectIds)
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();
    }

    public function getMySubjectsProperty()
    {
        if (!$this->teacher) return collect();
        
        return $this->teacher->subjects()->with(['nivelEducativo'])->get();
    }

    public function render()
    {
        return view('livewire.teacher.dashboard')
            ->layout('layouts.teacher');
    }
}
