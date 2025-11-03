<?php

namespace App\Livewire\Admin\Matriculas;

use Livewire\Component;
use App\Models\Matricula;
use App\Models\Student;
use App\Models\Programa;
use App\Models\SchoolPeriod;
use App\Models\PaymentSchedule;

class Create extends Component
{
    public $student_id;
    public $programa_id;
    public $periodo_id;
    public $fecha_matricula;
    public $estado = 'activo';

    // Campos de costos
    public $costo = 0;
    public $cuota_inicial = 0;
    public $numero_cuotas = 0;

    public $students = [];
    public $programas = [];
    public $periodos = [];
    
    // Tabla de amortización
    public $paymentSchedule = [];
    public $showSchedule = false;

    protected $rules = [
        'student_id' => 'required|exists:students,id',
        'programa_id' => 'required|exists:programas,id',
        'periodo_id' => 'required|exists:school_periods,id',
        'fecha_matricula' => 'required|date',
        'estado' => 'required|in:activo,inactivo,graduado',
        'costo' => 'required|numeric|min:0',
        'cuota_inicial' => 'required|numeric|min:0',
        'numero_cuotas' => 'required|integer|min:0'
    ];

    public function mount()
    {
        $this->fecha_matricula = now()->format('Y-m-d');
        $this->loadData();
    }

    public function loadData()
    {
        // Cargar solo estudiantes que no tienen matrícula
        $this->students = Student::whereDoesntHave('matriculas')
            ->with('nivelEducativo')
            ->orderBy('nombres')
            ->orderBy('apellidos')
            ->get();
            
        $this->programas = Programa::where('activo', true)->orderBy('nombre')->get();
        $this->periodos = SchoolPeriod::orderBy('name')->get();
    }

    public function updatedStudentId($value)
    {
        if ($value) {
            $student = Student::with('nivelEducativo')->find($value);

            if ($student && $student->nivelEducativo) {
                $this->costo = $student->nivelEducativo->costo;
                $this->cuota_inicial = $student->nivelEducativo->cuota_inicial;
                $this->numero_cuotas = $student->nivelEducativo->numero_cuotas;
            }
            
            // Generar tabla de amortización cuando se selecciona un estudiante
            $this->generatePaymentSchedule();
        }
    }
    
    public function updatedCosto()
    {
        $this->generatePaymentSchedule();
    }
    
    public function updatedCuotaInicial()
    {
        $this->generatePaymentSchedule();
    }
    
    public function updatedNumeroCuotas()
    {
        $this->generatePaymentSchedule();
    }
    
    public function updatedPeriodoId()
    {
        $this->generatePaymentSchedule();
    }

    public function generatePaymentSchedule()
    {
        // Solo generar si tenemos todos los datos necesarios
        if (!$this->costo || !$this->periodo_id) {
            $this->paymentSchedule = [];
            $this->showSchedule = false;
            return;
        }

        $periodo = SchoolPeriod::find($this->periodo_id);
        if (!$periodo) {
            $this->paymentSchedule = [];
            $this->showSchedule = false;
            return;
        }

        // Calcular monto restante después de la cuota inicial
        $montoRestante = $this->costo - $this->cuota_inicial;
        
        // Si no hay cuotas, todo se cobra en la cuota inicial
        if ($this->numero_cuotas <= 0) {
            $this->paymentSchedule = [
                [
                    'numero_cuota' => 1,
                    'descripcion' => 'Pago único',
                    'monto' => $this->costo,
                    'fecha_vencimiento' => $periodo->start_date
                ]
            ];
            $this->showSchedule = true;
            return;
        }

        // Calcular monto por cuota
        $montoCuota = $montoRestante / $this->numero_cuotas;
        
        // Generar cuotas mensuales
        $this->paymentSchedule = [];
        
        // Agregar cuota inicial
        if ($this->cuota_inicial > 0) {
            $this->paymentSchedule[] = [
                'numero_cuota' => 0,
                'descripcion' => 'Cuota inicial',
                'monto' => $this->cuota_inicial,
                'fecha_vencimiento' => $periodo->start_date
            ];
        }
        
        // Agregar cuotas distribuidas uniformemente a lo largo del período escolar
        $startDate = new \DateTime($periodo->start_date);
        $endDate = new \DateTime($periodo->end_date);
        
        // Calcular intervalo total en días
        $totalDays = $startDate->diff($endDate)->days;
        
        // Para cada cuota, calcular la fecha de vencimiento distribuida uniformemente
        for ($i = 1; $i <= $this->numero_cuotas; $i++) {
            $dueDate = clone $startDate;
            
            // Calcular días entre cuotas (distribución uniforme)
            if ($this->numero_cuotas > 1) {
                $daysBetweenPayments = floor($totalDays / ($this->numero_cuotas - 1));
                $dueDate->modify('+' . ($daysBetweenPayments * ($i - 1)) . ' days');
            } else {
                // Si solo hay una cuota, colocarla a la mitad del período
                $daysBetweenPayments = floor($totalDays / 2);
                $dueDate->modify('+' . $daysBetweenPayments . ' days');
            }
            
            // Asegurarse de que la fecha no exceda la fecha final
            if ($dueDate > $endDate) {
                $dueDate = clone $endDate;
            }
            
            $this->paymentSchedule[] = [
                'numero_cuota' => $i,
                'descripcion' => 'Cuota ' . $i,
                'monto' => round($montoCuota, 2),
                'fecha_vencimiento' => $dueDate->format('Y-m-d')
            ];
        }
        
        $this->showSchedule = true;
    }

    public function store()
    {
        // Verificar permiso para crear matrículas
        if (!auth()->user()->can('create matriculas')) {
            session()->flash('error', 'No tienes permiso para crear matrículas.');
            return;
        }

        $this->validate();

        try {
            $matricula = Matricula::create([
                'empresa_id' => auth()->user()->empresa_id,
                'sucursal_id' => auth()->user()->sucursal_id,
                'estudiante_id' => $this->student_id,
                'programa_id' => $this->programa_id,
                'periodo_id' => $this->periodo_id,
                'fecha_matricula' => $this->fecha_matricula,
                'estado' => $this->estado,
                'costo' => $this->costo,
                'cuota_inicial' => $this->cuota_inicial,
                'numero_cuotas' => $this->numero_cuotas
            ]);

            // Generar cronograma de pagos si existe la clase
            if (class_exists('\App\Models\PaymentSchedule')) {
                $this->createPaymentSchedule($matricula);
            }

            session()->flash('message', 'Matrícula creada correctamente.');
            return redirect()->route('admin.matriculas.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear la matrícula: ' . $e->getMessage());
            \Log::error('Error creating matricula: ' . $e->getMessage());
        }
    }
    
    private function createPaymentSchedule($matricula)
    {
        try {
            foreach ($this->paymentSchedule as $schedule) {
                PaymentSchedule::create([
                    'matricula_id' => $matricula->id,
                    'numero_cuota' => $schedule['numero_cuota'],
                    'monto' => $schedule['monto'],
                    'fecha_vencimiento' => $schedule['fecha_vencimiento'],
                    'estado' => 'pendiente',
                    'empresa_id' => auth()->user()->empresa_id,
                    'sucursal_id' => auth()->user()->sucursal_id,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error creating payment schedule: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.matriculas.create')
            ->layout('components.layouts.admin', [
                'title' => 'Crear Matrícula',
                'description' => 'Registrar una nueva matrícula de estudiante'
            ]);
    }
}