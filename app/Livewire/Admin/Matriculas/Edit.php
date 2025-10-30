<?php

namespace App\Livewire\Admin\Matriculas;

use Livewire\Component;
use App\Models\Matricula;
use App\Models\Student;
use App\Models\Programa;
use App\Models\SchoolPeriod;
use App\Models\PaymentSchedule;

class Edit extends Component
{
    public $matricula;
    public $student_id;
    public $programa_id;
    public $periodo_id;
    public $fecha_matricula;
    public $estado;
    
    // Campos de costos
    public $costo;
    public $cuota_inicial;
    public $numero_cuotas;

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

    public function mount(Matricula $matricula)
    {
        $this->matricula = $matricula;
        $this->student_id = $matricula->estudiante_id;
        $this->programa_id = $matricula->programa_id;
        $this->periodo_id = $matricula->periodo_id;
        $this->fecha_matricula = $matricula->fecha_matricula->format('Y-m-d');
        $this->estado = $matricula->estado;
        $this->costo = $matricula->costo;
        $this->cuota_inicial = $matricula->cuota_inicial;
        $this->numero_cuotas = $matricula->numero_cuotas;
        
        $this->loadData();
        $this->loadPaymentSchedule();
    }

    public function loadData()
    {
        // Cargar solo estudiantes que no tienen matrícula, excluyendo el estudiante actual
        $this->students = Student::where(function($query) {
                $query->whereDoesntHave('matriculas')
                      ->orWhere('id', $this->student_id);
            })
            ->orderBy('nombres')
            ->orderBy('apellidos')
            ->get();
            
        $this->programas = Programa::where('activo', true)->orderBy('nombre')->get();
        $this->periodos = SchoolPeriod::orderBy('name')->get();
    }
    
    public function loadPaymentSchedule()
    {
        $this->paymentSchedule = $this->matricula->cronogramaPagos()
            ->where('estado', '!=', 'pagado') // Mostrar solo cuotas no pagadas (pendientes o parcialmente pagadas)
            ->orderBy('numero_cuota')
            ->get()
            ->map(function ($schedule) {
                return [
                    'numero_cuota' => $schedule->numero_cuota,
                    'descripcion' => $schedule->numero_cuota == 0 ? 'Cuota inicial' : 'Cuota ' . $schedule->numero_cuota,
                    'monto' => $schedule->monto,
                    'fecha_vencimiento' => $schedule->fecha_vencimiento
                ];
            })
            ->toArray();
            
        $this->showSchedule = count($this->paymentSchedule) > 0;
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

    public function update()
    {
        // Verificar permiso para editar matrículas
        if (!auth()->user()->can('edit matriculas')) {
            session()->flash('error', 'No tienes permiso para editar matrículas.');
            return;
        }

        $this->validate();

        try {
            $this->matricula->update([
                'estudiante_id' => $this->student_id,
                'programa_id' => $this->programa_id,
                'periodo_id' => $this->periodo_id,
                'fecha_matricula' => $this->fecha_matricula,
                'estado' => $this->estado,
                'costo' => $this->costo,
                'cuota_inicial' => $this->cuota_inicial,
                'numero_cuotas' => $this->numero_cuotas
            ]);

            // Actualizar cronograma de pagos
            $this->updatePaymentSchedule();

            session()->flash('message', 'Matrícula actualizada correctamente.');
            return redirect()->route('admin.matriculas.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar la matrícula: ' . $e->getMessage());
        }
    }
    
    private function updatePaymentSchedule()
    {
        // Eliminar cronograma existente
        $this->matricula->cronogramaPagos()->delete();
        
        // Crear nuevo cronograma
        foreach ($this->paymentSchedule as $schedule) {
            PaymentSchedule::create([
                'matricula_id' => $this->matricula->id,
                'numero_cuota' => $schedule['numero_cuota'],
                'monto' => $schedule['monto'],
                'fecha_vencimiento' => $schedule['fecha_vencimiento'],
                'estado' => 'pendiente',
                'empresa_id' => auth()->user()->empresa_id,
                'sucursal_id' => auth()->user()->sucursal_id,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.matriculas.edit')
            ->layout('components.layouts.admin', [
                'title' => 'Editar Matrícula',
                'description' => 'Actualizar datos de matrícula de estudiante'
            ]);
    }
}