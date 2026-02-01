<?php

namespace App\Livewire\Representative;

use Livewire\Component;
use App\Models\Student;
use App\Models\Matricula;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\ConductRecord;
use App\Models\PaymentSchedule;
use App\Models\SchoolPeriod;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public $students = [];
    public $selectedStudent = null;
    public $activePeriod;

    public function mount()
    {
        $user = Auth::user();
        $this->activePeriod = SchoolPeriod::where('is_active', true)->first();
        
        // Obtener estudiantes vinculados al representante
        $this->students = Student::where('representante_email', $user->email)
            ->orWhere('correo_electronico', $user->email)
            ->get();
        
        if ($this->students->count() > 0) {
            $this->selectedStudent = $this->students->first();
        }
    }

    public function selectStudent($studentId)
    {
        $this->selectedStudent = $this->students->firstWhere('id', $studentId);
    }

    public function getStatsProperty()
    {
        if (!$this->selectedStudent) {
            return [
                'average' => 0,
                'attendance_rate' => 0,
                'pending_payments' => 0,
                'conduct_alerts' => 0,
            ];
        }

        // Promedio de notas
        $grades = Grade::where('student_id', $this->selectedStudent->id)
            ->where('status', 'graded')
            ->whereHas('evaluation', function($q) {
                if ($this->activePeriod) {
                    $q->where('school_period_id', $this->activePeriod->id);
                }
            })
            ->get();
        
        $average = $grades->avg('score') ?? 0;

        // Asistencia
        $attendances = Attendance::where('student_id', $this->selectedStudent->id)
            ->when($this->activePeriod, fn($q) => $q->where('school_period_id', $this->activePeriod->id))
            ->get();
        
        $totalDays = $attendances->count();
        $presentDays = $attendances->whereIn('status', ['present', 'late'])->count();
        $attendanceRate = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 0;

        // Pagos pendientes
        $pendingPayments = PaymentSchedule::whereHas('matricula', function($q) {
            $q->where('student_id', $this->selectedStudent->id);
        })->where('status', 'pendiente')->count();

        // Alertas de conducta
        $conductAlerts = ConductRecord::where('student_id', $this->selectedStudent->id)
            ->where('resolved', false)
            ->whereIn('type', ['negative', 'warning', 'sanction'])
            ->count();

        return [
            'average' => round($average, 2),
            'attendance_rate' => $attendanceRate,
            'pending_payments' => $pendingPayments,
            'conduct_alerts' => $conductAlerts,
        ];
    }

    public function getRecentGradesProperty()
    {
        if (!$this->selectedStudent) return collect();

        return Grade::with(['evaluation.subject', 'evaluation.evaluationPeriod'])
            ->where('student_id', $this->selectedStudent->id)
            ->where('status', 'graded')
            ->orderBy('graded_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.representative.dashboard')
            ->layout('layouts.representative');
    }
}
