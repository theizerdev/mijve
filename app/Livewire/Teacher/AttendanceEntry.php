<?php

namespace App\Livewire\Teacher;

use Livewire\Component;
use App\Models\Teacher;
use App\Models\Attendance;
use App\Models\Section;
use App\Models\Subject;
use App\Models\SchoolPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceEntry extends Component
{
    public $teacher;
    public $subject_id = '';
    public $section_id = '';
    public $date;
    public $students = [];
    public $attendanceData = [];

    public function mount()
    {
        $user = Auth::user();
        $this->teacher = Teacher::where('user_id', $user->id)->first();
        $this->date = now()->format('Y-m-d');
    }

    public function updatedSectionId()
    {
        $this->loadStudents();
    }

    public function updatedDate()
    {
        $this->loadStudents();
    }

    public function loadStudents()
    {
        if (!$this->section_id) {
            $this->students = [];
            $this->attendanceData = [];
            return;
        }

        $section = Section::with(['students' => function($q) {
            $q->wherePivot('estado', 'activo')->orderBy('apellidos')->orderBy('nombres');
        }])->find($this->section_id);

        $this->students = $section ? $section->students : collect();

        // Cargar asistencia existente
        $existing = Attendance::where('section_id', $this->section_id)
            ->whereDate('date', $this->date)
            ->get()
            ->keyBy('student_id');

        $this->attendanceData = [];
        foreach ($this->students as $student) {
            $att = $existing->get($student->id);
            $this->attendanceData[$student->id] = [
                'status' => $att ? $att->status : 'present',
                'observations' => $att ? $att->observations : '',
            ];
        }
    }

    public function applyAll($status)
    {
        foreach ($this->attendanceData as $studentId => $data) {
            $this->attendanceData[$studentId]['status'] = $status;
        }
    }

    public function save()
    {
        if (!$this->section_id || !$this->date) {
            session()->flash('error', 'Seleccione sección y fecha.');
            return;
        }

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $activePeriod = SchoolPeriod::where('is_active', true)->first();

            foreach ($this->attendanceData as $studentId => $data) {
                Attendance::updateOrCreate(
                    [
                        'section_id' => $this->section_id,
                        'student_id' => $studentId,
                        'date' => $this->date,
                    ],
                    [
                        'empresa_id' => $user->empresa_id,
                        'sucursal_id' => $user->sucursal_id,
                        'school_period_id' => $activePeriod?->id,
                        'status' => $data['status'],
                        'observations' => $data['observations'] ?? null,
                        'registered_by' => $user->id,
                    ]
                );
            }

            DB::commit();
            session()->flash('message', 'Asistencia registrada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error: ' . $e->getMessage());
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

        $statuses = Attendance::getStatuses();

        return view('livewire.teacher.attendance-entry', compact('subjects', 'sections', 'statuses'))
            ->layout('layouts.teacher');
    }
}
