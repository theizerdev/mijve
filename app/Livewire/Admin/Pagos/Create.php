<?php

namespace App\Livewire\Admin\Pagos;

use Livewire\Component;
use App\Models\Pago;
use App\Models\Matricula;
use App\Models\ConceptoPago;
use App\Models\PaymentSchedule;
use App\Models\Serie;
use App\Models\Caja;
use App\Services\PagoService;
use DB;
class Create extends Component
{
    public $matricula_id;
    public $tipo_pago = 'recibo';
    public $fecha;
    public $metodo_pago = 'efectivo';
    public $referencia;
    public $descuento = 0;
    public $observaciones;
    public $detalles = [];

    // Propiedades adicionales para la vista
    public $fecha_pago;
    public $concepto_id;
    public $monto;

    public $matriculas = [];
    public $conceptos = [];
    public $cuotasPendientes = [];
    public $serie_actual;
    public $numero_documento;
    public $caja_abierta;

    protected $rules = [
        'matricula_id' => 'required|exists:matriculas,id',
        'tipo_pago' => 'required|in:factura,boleta,nota_credito,recibo',

        'fecha' => 'required|date',
        'metodo_pago' => 'nullable|string',
        'referencia' => 'nullable|string',
        'descuento' => 'nullable|numeric|min:0',
        'detalles.*.concepto_pago_id' => 'required|exists:conceptos_pago,id',
        'detalles.*.descripcion' => 'required|string',
        'detalles.*.cantidad' => 'required|numeric|min:0.01',
        'detalles.*.precio_unitario' => 'required|numeric|min:0'
    ];

    public function mount()
    {
        $this->fecha = now()->format('Y-m-d');
        $this->fecha_pago = now()->format('Y-m-d');
        $this->cargarDatos();
        $this->verificarCajaAbierta();
        $this->agregarDetalle();
    }

    public function verificarCajaAbierta()
    {
        $this->caja_abierta = Caja::obtenerCajaAbierta(
            auth()->user()->empresa_id,
            auth()->user()->sucursal_id
        );
    }

    public function cargarDatos()
    {
        $query = Matricula::with(['estudiante', 'programa']);

        if (!auth()->user()->hasRole('Super Administrador')) {
            $query->where('empresa_id', auth()->user()->empresa_id)
                  ->where('sucursal_id', auth()->user()->sucursal_id);
        }

        $this->matriculas = $query->get();
        $this->conceptos = ConceptoPago::where('activo', true)->get();
        $this->cargarSerieActual();
    }

    public function updatedMatriculaId($value)
    {
        if ($value) {
            if (class_exists('\App\Models\PaymentSchedule')) {
                $this->cuotasPendientes = PaymentSchedule::where('matricula_id', $value)
                    ->where('estado', 'pendiente')
                    ->orderBy('numero_cuota')
                    ->get();
            }
        }
    }

    public function updatedTipoPago($value)
    {
        $this->cargarSerieActual();
    }

    public function cargarSerieActual()
    {
        $query = Serie::where('tipo_documento', $this->tipo_pago)
                     ->where('activo', true);

        if (!auth()->user()->hasRole('Super Administrador')) {
            $query->where('empresa_id', auth()->user()->empresa_id)
                  ->where('sucursal_id', auth()->user()->sucursal_id);
        }

        $this->serie_actual = $query->first();

        if ($this->serie_actual) {
            $siguienteNumero = $this->serie_actual->correlativo_actual + 1;
            $this->numero_documento = $this->serie_actual->serie . '-' .
                str_pad($siguienteNumero, $this->serie_actual->longitud_correlativo, '0', STR_PAD_LEFT);
        } else {
            $this->numero_documento = null;
        }
    }

    public function agregarDetalle()
    {
        $this->detalles[] = [
            'concepto_pago_id' => '',
            'payment_schedule_id' => null,
            'descripcion' => '',
            'cantidad' => 1,
            'precio_unitario' => 0
        ];
    }

    public function eliminarDetalle($index)
    {
        unset($this->detalles[$index]);
        $this->detalles = array_values($this->detalles);
    }

    public function seleccionarCuota($scheduleId)
    {
        $schedule = PaymentSchedule::find($scheduleId);
        if ($schedule) {
            $conceptoMensualidad = ConceptoPago::where('nombre', 'Mensualidad')->first();

            // Agregar nuevo detalle en lugar de editar existente
            $this->detalles[] = [
                'concepto_pago_id' => $conceptoMensualidad?->id,
                'payment_schedule_id' => $schedule->id,
                'descripcion' => "Cuota #{$schedule->numero_cuota} - {$schedule->fecha_vencimiento->format('M Y')}",
                'cantidad' => 1,
                'precio_unitario' => $schedule->saldo_pendiente
            ];
        }
    }

    public function agregarAbono($scheduleId, $monto = null)
    {
        $schedule = PaymentSchedule::find($scheduleId);
        if ($schedule) {
            $conceptoMensualidad = ConceptoPago::where('nombre', 'Mensualidad')->first();
            $montoAbono = $monto ?? $schedule->saldo_pendiente;

            $this->detalles[] = [
                'concepto_pago_id' => $conceptoMensualidad?->id,
                'payment_schedule_id' => $schedule->id,
                'descripcion' => "Abono Cuota #{$schedule->numero_cuota}",
                'cantidad' => 1,
                'precio_unitario' => $montoAbono
            ];
        }
    }

    public function calcularSubtotal($index)
    {
        $detalle = $this->detalles[$index];
        return ($detalle['cantidad'] ?? 0) * ($detalle['precio_unitario'] ?? 0);
    }

    public function getSubtotalProperty()
    {
        return collect($this->detalles)->sum(fn($d) => ($d['cantidad'] ?? 0) * ($d['precio_unitario'] ?? 0));
    }

    public function getTotalProperty()
    {
        return $this->subtotal - $this->descuento;
    }

    public function guardar()
    {
        $this->validate();

       try {
        DB::transaction(function () {
            $matricula = Matricula::find($this->matricula_id);

            $pagoService = new PagoService();
            $pago = $pagoService->crearPago([
                'tipo_pago' => $this->tipo_pago,
                'fecha' => $this->fecha,
                'matricula_id' => $this->matricula_id,
                'serie_id' => $this->serie_actual?->id,
                'metodo_pago' => $this->metodo_pago,
                'referencia' => $this->referencia,
                'descuento' => $this->descuento,
                'observaciones' => $this->observaciones,
                'empresa_id' => auth()->user()->empresa_id,
                'sucursal_id' => auth()->user()->sucursal_id,
                'estado' => Pago::ESTADO_APROBADO,
                'detalles' => $this->detalles
            ]);

            session()->flash('message', 'Pago registrado exitosamente: ' . $pago->numero_completo);
            return redirect()->route('admin.pagos.index');
        });
        } catch (\Throwable $th) {
            dd($th);
            session()->flash('error', 'Algo salió mal al crear el pago');
        }
    }

    public function render()
    {
        return view('livewire.admin.pagos.create', [
            'tipos' => Pago::getTipos(),
            'estados' => Pago::getEstados()
        ])->layout('components.layouts.admin', [
            'title' => 'Registrar Pago'
        ]);
    }
}
