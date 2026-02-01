<?php

namespace App\Livewire\Representative;

use Livewire\Component;
use App\Models\Student;
use App\Models\Matricula;
use App\Models\PaymentSchedule;
use App\Models\Pago;
use Illuminate\Support\Facades\Auth;

class Payments extends Component
{
    public $students = [];
    public $selectedStudentId = '';
    public $selectedStudent = null;
    public $matriculas = [];
    public $selectedMatriculaId = '';
    public $payments = [];
    public $schedule = [];

    public function mount()
    {
        $user = Auth::user();
        
        $this->students = Student::where('representante_email', $user->email)
            ->orWhere('correo_electronico', $user->email)
            ->get();
        
        if ($this->students->count() > 0) {
            $this->selectedStudentId = $this->students->first()->id;
            $this->selectedStudent = $this->students->first();
            $this->loadMatriculas();
        }
    }

    public function updatedSelectedStudentId()
    {
        $this->selectedStudent = $this->students->firstWhere('id', $this->selectedStudentId);
        $this->loadMatriculas();
    }

    public function updatedSelectedMatriculaId()
    {
        $this->loadPayments();
    }

    public function loadMatriculas()
    {
        if (!$this->selectedStudent) {
            $this->matriculas = [];
            return;
        }

        $this->matriculas = Matricula::with(['programa', 'schoolPeriod'])
            ->where('student_id', $this->selectedStudent->id)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($this->matriculas->count() > 0) {
            $this->selectedMatriculaId = $this->matriculas->first()->id;
            $this->loadPayments();
        }
    }

    public function loadPayments()
    {
        if (!$this->selectedMatriculaId) {
            $this->payments = [];
            $this->schedule = [];
            return;
        }

        // Cronograma de pagos
        $this->schedule = PaymentSchedule::where('matricula_id', $this->selectedMatriculaId)
            ->orderBy('fecha_vencimiento')
            ->get();

        // Pagos realizados
        $this->payments = Pago::with('comprobante')
            ->where('matricula_id', $this->selectedMatriculaId)
            ->orderBy('fecha_pago', 'desc')
            ->get();
    }

    public function getBalanceProperty()
    {
        $totalDebt = $this->schedule->sum('monto');
        $totalPaid = $this->schedule->sum('monto_pagado');
        
        return [
            'total' => $totalDebt,
            'paid' => $totalPaid,
            'pending' => $totalDebt - $totalPaid,
            'pending_count' => $this->schedule->where('status', 'pendiente')->count(),
        ];
    }

    public function render()
    {
        return view('livewire.representative.payments')
            ->layout('layouts.representative');
    }
}
