<?php

namespace App\Livewire\Admin\AcademicTracking;

use App\Models\AcademicRecord;
use App\Models\Student;
use App\Models\SchoolPeriod;
use App\Models\Subject;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Historial Académico')]
class AcademicHistory extends Component
{
    use WithPagination;

    public $studentId;
    public $student;
    public $selectedPeriodId;
    public $selectedSubjectId;
    public $selectedStatus = '';
    public $search = '';
    public $showGrades = true;
    public $showObservations = false;
    public $showRecovery = true;

    protected $queryString = [
        'selectedPeriodId' => ['except' => ''],
        'selectedSubjectId' => ['except' => ''],
        'selectedStatus' => ['except' => ''],
        'search' => ['except' => ''],
    ];

    public function mount($studentId = null)
    {
        if ($studentId) {
            $this->studentId = $studentId;
            $this->student = Student::find($studentId);
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedPeriodId()
    {
        $this->resetPage();
    }

    public function updatingSelectedSubjectId()
    {
        $this->resetPage();
    }

    public function updatingSelectedStatus()
    {
        $this->resetPage();
    }

    public function getBaseQuery()
    {
        $query = AcademicRecord::with([
            'student',
            'subject',
            'schoolPeriod',
            'program',
            'educationalLevel',
            'teacher',
            'recoveryPeriod'
        ]);

        if ($this->studentId) {
            $query->where('student_id', $this->studentId);
        }

        if ($this->selectedPeriodId) {
            $query->where('school_period_id', $this->selectedPeriodId);
        }

        if ($this->selectedSubjectId) {
            $query->where('subject_id', $this->selectedSubjectId);
        }

        if ($this->selectedStatus) {
            $query->where('status', $this->selectedStatus);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('student', function ($studentQuery) {
                    $studentQuery->where('nombres', 'like', '%' . $this->search . '%')
                                ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                                ->orWhere('codigo', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('subject', function ($subjectQuery) {
                    $subjectQuery->where('nombre', 'like', '%' . $this->search . '%')
                                ->orWhere('codigo', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('schoolPeriod', function ($periodQuery) {
                    $periodQuery->where('nombre', 'like', '%' . $this->search . '%');
                });
            });
        }

        return $query;
    }

    public function getAcademicRecordsProperty()
    {
        return $this->getBaseQuery()
            ->orderBy('school_period_id', 'desc')
            ->orderBy('subject_id')
            ->paginate(15);
    }

    public function getSchoolPeriodsProperty()
    {
        return SchoolPeriod::orderBy('nombre', 'desc')->get();
    }

    public function getSubjectsProperty()
    {
        return Subject::orderBy('nombre')->get();
    }

    public function getStatsProperty()
    {
        $baseQuery = $this->getBaseQuery();
        
        return [
            'total' => $baseQuery->count(),
            'approved' => (clone $baseQuery)->where('approved', true)->count(),
            'failed' => (clone $baseQuery)->where('approved', false)->count(),
            'in_recovery' => (clone $baseQuery)->where('status', AcademicRecord::STATUS_IN_RECOVERY)->count(),
            'recovery_approved' => (clone $baseQuery)->where('recovery_status', AcademicRecord::RECOVERY_STATUS_APPROVED)->count(),
            'recovery_failed' => (clone $baseQuery)->where('recovery_status', AcademicRecord::RECOVERY_STATUS_FAILED)->count(),
            'withdrawn' => (clone $baseQuery)->where('withdrawn', true)->count(),
            'promoted' => (clone $baseQuery)->where('promoted', true)->count(),
            'repeated' => (clone $baseQuery)->where('repeated', true)->count(),
        ];
    }

    public function getStudentStatsProperty()
    {
        if (!$this->studentId) {
            return null;
        }

        $studentRecords = AcademicRecord::where('student_id', $this->studentId);
        
        return [
            'total_subjects' => $studentRecords->count(),
            'approved_subjects' => (clone $studentRecords)->where('approved', true)->count(),
            'failed_subjects' => (clone $studentRecords)->where('approved', false)->count(),
            'average_grade' => $studentRecords->avg('final_grade'),
            'recovery_subjects' => (clone $studentRecords)->where('status', AcademicRecord::STATUS_IN_RECOVERY)->count(),
            'recovery_approved' => (clone $studentRecords)->where('recovery_status', AcademicRecord::RECOVERY_STATUS_APPROVED)->count(),
            'promoted_count' => (clone $studentRecords)->where('promoted', true)->count(),
            'repeated_count' => (clone $studentRecords)->where('repeated', true)->count(),
        ];
    }

    public function export()
    {
        // Implementar exportación de historial académico
        $this->dispatch('success', 'Exportación de historial académico en desarrollo');
    }

    public function generateReport()
    {
        // Implementar generación de reporte académico
        $this->dispatch('success', 'Generación de reporte académico en desarrollo');
    }

    public function render()
    {
        return view('livewire.admin.academic-tracking.academic-history', [
            'academicRecords' => $this->academicRecords,
            'schoolPeriods' => $this->schoolPeriods,
            'subjects' => $this->subjects,
            'stats' => $this->stats,
            'studentStats' => $this->studentStats,
        ]);
    }
}