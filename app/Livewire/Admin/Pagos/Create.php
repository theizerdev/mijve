<?php

namespace App\Livewire\Admin\Pagos;

use App\Traits\HasDynamicLayout;
use App\Traits\HasRegionalFormatting;
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
    use HasDynamicLayout, HasRegionalFormatting;


    public $matricula_id;
    public $tipo_pago = 'recibo';
    public $fecha;
    public $metodo_pago = 'efectivo';
    public $referencia;
    public $descuento = 0;
    public $observaciones;
    public $detalles = [];
    public $tasa_cambio;
    public $mostrar_bolivares = false;

    // Propiedades para pago mixto
    public $es_pago_mixto = false;
    public $metodos_pago_mixto = [];

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

    // Propiedades para búsqueda
    public $busqueda_estudiante = '';
    public $matriculas_filtradas = [];

    // Propiedades para mejoras
    public $monto_recibido = 0;
    public $pagos_anteriores = [];
    public $plantillas_pago = [];

    protected $rules = [
        'matricula_id' => 'required|exists:matriculas,id',
        'tipo_pago' => 'required|in:factura,boleta,nota_credito,recibo',
        'fecha' => 'required|date',
        'metodo_pago' => 'nullable|string',
        'referencia' => 'nullable|string|unique:pagos,referencia',
        'descuento' => 'nullable|numeric|min:0',
        'detalles.*.concepto_pago_id' => 'nullable|integer|exists:conceptos_pago,id',
        'detalles.*.descripcion' => 'nullable|string',
        'detalles.*.cantidad' => 'nullable|numeric|min:0.01',
        'detalles.*.precio_unitario' => 'nullable|numeric|min:0',
    ];

    public function mount()
    {
        $this->fecha = now()->format('Y-m-d');
        $this->fecha_pago = now()->format('Y-m-d');
        $this->cargarTasaCambio();
        $this->cargarDatos();
        $this->verificarCajaAbierta();
        $this->agregarDetalle();
        $this->inicializarPagoMixto();
        $this->cargarPlantillasPago();
    }

    public function inicializarPagoMixto()
    {
        $this->metodos_pago_mixto = [
            ['metodo' => 'efectivo_dolares', 'monto' => 0, 'referencia' => ''],
            ['metodo' => 'transferencia', 'monto' => 0, 'referencia' => '']
        ];
    }

    public function cargarTasaCambio()
    {
        $tasaHoy = \App\Models\ExchangeRate::getTodayRate();
        if ($tasaHoy) {
            $this->tasa_cambio = $tasaHoy->usd_rate;
            $this->mostrar_bolivares = true;
        }
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
        $query = Matricula::with(['student', 'programa']);

        if (!auth()->user()->hasRole('Super Administrador')) {
            $query->where('empresa_id', auth()->user()->empresa_id)
                  ->where('sucursal_id', auth()->user()->sucursal_id);
        }

        $this->matriculas = $query->get();
        $this->conceptos = ConceptoPago::where('activo', true)->get();
        $this->cargarSerieActual();
    }

    public function updatedBusquedaEstudiante($value)
    {
        if (strlen($value) >= 2) {
            $query = Matricula::with(['student', 'programa'])
                ->whereHas('student', function($q) use ($value) {
                    $q->where('nombres', 'like', '%' . $value . '%')
                      ->orWhere('apellidos', 'like', '%' . $value . '%')
                      ->orWhere('documento_identidad', 'like', '%' . $value . '%')
                      ->orWhere('codigo', 'like', '%' . $value . '%');
                })
                ->orWhereHas('programa', function($q) use ($value) {
                    $q->where('nombre', 'like', '%' . $value . '%');
                });

            if (!auth()->user()->hasRole('Super Administrador')) {
                $query->where('empresa_id', auth()->user()->empresa_id)
                      ->where('sucursal_id', auth()->user()->sucursal_id);
            }

            $this->matriculas_filtradas = $query->limit(10)->get();
        } else {
            $this->matriculas_filtradas = [];
        }
    }

    public function seleccionarMatricula($matriculaId)
    {
        $this->matricula_id = $matriculaId;
        $this->busqueda_estudiante = '';
        $this->matriculas_filtradas = [];
        $this->updatedMatriculaId($matriculaId);
        $this->cargarPagosAnteriores($matriculaId);
    }

    public function cargarPagosAnteriores($matriculaId)
    {
        $this->pagos_anteriores = Pago::where('matricula_id', $matriculaId)
            ->where('estado', 'aprobado')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function cargarPlantillasPago()
    {
        $this->plantillas_pago = [
            'mensualidad' => ['nombre' => 'Mensualidad', 'conceptos' => ['Mensualidad']],
            'inscripcion' => ['nombre' => 'Inscripción', 'conceptos' => ['Inscripción', 'Materiales']],
            'materiales' => ['nombre' => 'Materiales', 'conceptos' => ['Materiales Escolares']]
        ];
    }

    public function aplicarPlantilla($tipo)
    {
        $this->detalles = [];
        $plantilla = $this->plantillas_pago[$tipo] ?? null;

        if ($plantilla) {
            foreach ($plantilla['conceptos'] as $nombreConcepto) {
                $concepto = ConceptoPago::where('nombre', 'like', '%' . $nombreConcepto . '%')
                    ->where('activo', true)
                    ->first();

                if ($concepto) {
                    $this->detalles[] = [
                        'concepto_pago_id' => $concepto->id,
                        'payment_schedule_id' => null,
                        'descripcion' => $concepto->nombre,
                        'cantidad' => 1,
                        'precio_unitario' => $concepto->precio_sugerido ?? 0
                    ];
                }
            }
        }

        if (empty($this->detalles)) {
            $this->agregarDetalle();
        }
    }

    public function updatedDetalles($value, $key)
    {
        if (str_contains($key, 'precio_unitario') || str_contains($key, 'cantidad')) {
            $this->validateOnly("detalles.{$key}");
        }
    }

    public function getCambioProperty()
    {
        return max(0, $this->monto_recibido - $this->total);
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
        $conceptoDefault = ConceptoPago::where('activo', true)->first();

        $this->detalles[] = [
            'concepto_pago_id' => $conceptoDefault?->id ?? '',
            'payment_schedule_id' => null,
            'descripcion' => '',
            'cantidad' => 1,
            'precio_unitario' => 0.01
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
            $conceptoRecargo = ConceptoPago::where('nombre', 'Recargo por Mora')->first();

            // Agregar cuota principal
            $this->detalles[] = [
                'concepto_pago_id' => $conceptoMensualidad?->id,
                'payment_schedule_id' => $schedule->id,
                'descripcion' => "Cuota #{$schedule->numero_cuota} - {$schedule->fecha_vencimiento->format('M Y')}",
                'cantidad' => 1,
                'precio_unitario' => $schedule->saldo_pendiente
            ];

            // Agregar recargo si existe
            if ($schedule->recargo_morosidad > 0) {
                if (!$conceptoRecargo) {
                    $conceptoRecargo = ConceptoPago::create([
                        'nombre' => 'Recargo por Mora',
                        'descripcion' => 'Recargo aplicado por pagos vencidos',
                        'activo' => true
                    ]);
                }

                $this->detalles[] = [
                    'concepto_pago_id' => $conceptoRecargo->id,
                    'payment_schedule_id' => null,
                    'descripcion' => "Recargo por mora - Cuota #{$schedule->numero_cuota}",
                    'cantidad' => 1,
                    'precio_unitario' => $schedule->recargo_morosidad
                ];
            }
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

    public function getTotalBolivaresProperty()
    {
        if ($this->tasa_cambio) {
            return $this->total * $this->tasa_cambio;
        }
        return 0;
    }

    public function updatedMetodoPago($value)
    {
        $this->es_pago_mixto = ($value === 'pago mixto');
        if (!$this->es_pago_mixto) {
            $this->inicializarPagoMixto();
        }
    }

    public function agregarMetodoPago()
    {
        $this->metodos_pago_mixto[] = ['metodo' => 'efectivo_dolares', 'monto' => 0, 'referencia' => ''];
    }

    public function eliminarMetodoPago($index)
    {
        if (count($this->metodos_pago_mixto) > 1) {
            unset($this->metodos_pago_mixto[$index]);
            $this->metodos_pago_mixto = array_values($this->metodos_pago_mixto);
        }
    }

    public function getTotalPagoMixtoProperty()
    {
        return collect($this->metodos_pago_mixto)->sum('monto');
    }

    public function guardar()
    {
        // Validación especial para pago mixto
        if ($this->es_pago_mixto && $this->totalPagoMixto != $this->total) {
            session()->flash('error', 'El total configurado en el pago mixto debe coincidir con el total a pagar.');
            return;
        }



       try {
        DB::transaction(function () {
            //$this->validate();

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
                'tasa_cambio' => $this->tasa_cambio,
                'es_pago_mixto' => $this->es_pago_mixto,
                'detalles_pago_mixto' => $this->es_pago_mixto ? $this->metodos_pago_mixto : null,
                'empresa_id' => auth()->user()->empresa_id,
                'sucursal_id' => auth()->user()->sucursal_id,
                'estado' => Pago::ESTADO_APROBADO,
                'detalles' => $this->detalles
            ]);

            $this->dispatch('pago-registrado', ['mensaje' => 'Pago registrado exitosamente: ' . $pago->numero_completo]);

            session()->flash('message', 'Pago registrado exitosamente: ' . $pago->numero_completo);
            return redirect()->route('admin.pagos.index');
        });
        } catch (\Throwable $th) {
            dd($th);
            session()->flash('error', 'Error al crear el pago: ' . $th->getMessage());
        }
    }

    public function render()
    {
        $tipos = [
            'factura' => 'Factura',
            'boleta' => 'Boleta',
            'nota_credito' => 'Nota de Crédito',
            'recibo' => 'Recibo',
            'comunidad educativa' => 'Comunidad Educativa',
        ];

        return view('livewire.admin.pagos.create', compact('tipos'))->layout($this->getLayout());
    }
}
