<?php

namespace App\Livewire\Teacher;

use Livewire\Component;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Section;
use App\Models\SchoolPeriod;
use Illuminate\Support\Facades\Auth;

class MySubjects extends Component
{
    public $teacher;
    public $activePeriod;

    public function mount()
    {
        $user = Auth::user();
        $this->teacher = Teacher::where('user_id', $user->id)->first();
        $this->activePeriod = SchoolPeriod::where('is_active', true)->first();
    }

    public function render()
    {
        $subjects = collect();
        
        if ($this->teacher) {
            $subjects = $this->teacher->subjects()
                ->with(['nivelEducativo', 'schedules.section'])
                ->get()
                ->map(function($subject) {
                    $sections = $subject->schedules
                        ->where('school_period_id', $this->activePeriod?->id)
                        ->pluck('section')
                        ->filter()
                        ->unique('id');
                    
                    $subject->assigned_sections = $sections;
                    $subject->total_students = $sections->sum(fn($s) => $s->students()->wherePivot('estado', 'activo')->count());
                    
                    return $subject;
                });
        }

        return view('livewire.teacher.my-subjects', compact('subjects'))
            ->layout('layouts.teacher');
    }
}
