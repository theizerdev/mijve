<?php

namespace App\Livewire\Representative;

use Livewire\Component;
use App\Models\Student;
use App\Models\Attendance as AttendanceModel;
use App\Models\SchoolPeriod;
use Illuminate\Support\Facades\Auth;

class Attendance extends Component
{
    public $students = [];
    public $selectedStudentId = '';
    public $selectedStudent = null;
    public $month;
    public $year;
    public $activePeriod;
    public $attendanceData = [];
    public $stats = [];

    public function mount()
    {
        $user = Auth::user();
        $this->activePeriod = SchoolPeriod::where('is_active', true)->first();
        $this->month = now()->month;
        $this->year = now()->year;
        
        $this->students = Student::where('representante_email', $user->email)
            ->orWhere('correo_electronico', $user->email)
            ->get();
        
        if ($this->students->count() > 0) {
            $this->selectedStudentId = $this->students->first()->id;
            $this->selectedStudent = $this->students->first();
            $this->loadAttendance();
        }
    }

    public function updatedSelectedStudentId()
    {
        $this->selectedStudent = $this->students->firstWhere('id', $this->selectedStudentId);
        $this->loadAttendance();
    }

    public function updatedMonth()
    {
        $this->loadAttendance();
    }

    public function updatedYear()
    {
        $this->loadAttendance();
    }

    public function loadAttendance()
    {
        if (!$this->selectedStudent) {
            $this->attendanceData = [];
            $this->stats = [];
            return;
        }

        $startDate = \Carbon\Carbon::createFromDate($this->year, $this->month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $this->attendanceData = AttendanceModel::with('section')
            ->where('student_id', $this->selectedStudent->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        // Estadísticas del mes
        $total = $this->attendanceData->count();
        $present = $this->attendanceData->where('status', 'present')->count();
        $late = $this->attendanceData->where('status', 'late')->count();
        $absent = $this->attendanceData->where('status', 'absent')->count();
        $excused = $this->attendanceData->where('status', 'excused')->count();

        $this->stats = [
            'total' => $total,
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
            'excused' => $excused,
            'rate' => $total > 0 ? round((($present + $late) / $total) * 100, 1) : 0,
        ];
    }

    public function render()
    {
        $months = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        $years = range(now()->year - 2, now()->year);

        return view('livewire.representative.attendance', compact('months', 'years'))
            ->layout('layouts.representative');
    }
}
