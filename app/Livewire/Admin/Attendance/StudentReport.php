<?php

namespace App\Livewire\Admin\Attendance;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\SchoolPeriod;

class StudentReport extends Component
{
    use HasDynamicLayout;

    public $student;
    public $school_period_id = '';
    public $date_from = '';
    public $date_to = '';
    public $attendances = [];
    public $stats = [];

    public function mount(Student $student)
    {
        $this->student = $student;
        $this->date_from = now()->startOfYear()->format('Y-m-d');
        $this->date_to = now()->format('Y-m-d');
        
        $activePeriod = SchoolPeriod::where('is_active', true)->first();
        if ($activePeriod) {
            $this->school_period_id = $activePeriod->id;
        }
        
        $this->loadData();
    }

    public function updatedSchoolPeriodId()
    {
        $this->loadData();
    }

    public function updatedDateFrom()
    {
        $this->loadData();
    }

    public function updatedDateTo()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $query = Attendance::where('student_id', $this->student->id)
            ->with(['section', 'registeredBy']);

        if ($this->school_period_id) {
            $query->where('school_period_id', $this->school_period_id);
        }

        if ($this->date_from) {
            $query->whereDate('date', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->whereDate('date', '<=', $this->date_to);
        }

        $this->attendances = $query->orderBy('date', 'desc')->get();

        // Calcular estadísticas
        $total = $this->attendances->count();
        $present = $this->attendances->where('status', 'present')->count();
        $absent = $this->attendances->where('status', 'absent')->count();
        $late = $this->attendances->where('status', 'late')->count();
        $excused = $this->attendances->where('status', 'excused')->count();

        $this->stats = [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'excused' => $excused,
            'attendance_rate' => $total > 0 ? round((($present + $late) / $total) * 100, 1) : 0,
        ];
    }

    public function render()
    {
        $schoolPeriods = SchoolPeriod::orderBy('year', 'desc')->get();

        return view('livewire.admin.attendance.student-report', compact('schoolPeriods'))
            ->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Reporte de Asistencia - ' . $this->student->nombres . ' ' . $this->student->apellidos;
    }
}
