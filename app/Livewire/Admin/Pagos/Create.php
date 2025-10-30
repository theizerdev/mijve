<?php

namespace App\Livewire\Admin\Pagos;

use Livewire\Component;
use App\Models\Pago;
use App\Models\Matricula;
use App\Models\Comprobante;
use App\Models\ConceptoPago;
use App\Models\EducationalLevel;
use App\Models\PaymentSchedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Create extends Component
{
    public $matricula_id;
    public $concepto_id;
    public $monto;
    public $fecha_pago;
    public $estado = 'pendiente';
    public $metodo_pago = 'efectivo';
    public $referencia = '';

    public $matriculas = [];
    public $conceptos = [];
    public $carrito = [];
    public $total = 0;
    
    // Para la tabla de amortización
    public $paymentSchedule = [];
    public $selectedCuotas = [];
    public $selectAll = false;

    protected $rules = [
        'matricula_id' => 'required|exists:matriculas,id',
        'referencia' => 'nullable|string|max:255',
        'fecha_pago' => 'nullable|date',
        'metodo_pago' => 'required|in:efectivo,transferencia,tarjeta',
        'carrito' => 'required|array|min:1'
    ];

    public function mount()
    {
        $this->fecha_pago = now()->format('Y-m-d');
        $this->loadData();
    }

    public function loadData()
    {
        $this->matriculas = Matricula::with('student', 'programa')->where('estado', 'activo')->get();
        $this->conceptos = ConceptoPago::where('activo', true)->get();
    }

    public function updatedMatriculaId()
    {
        // Limpiar el carrito cuando se cambia la matrícula
        $this->carrito = [];
        $this->calcularTotal();
        $this->selectedCuotas = [];
        $this->selectAll = false;
        
        // Cargar la tabla de amortización
        if ($this->matricula_id) {
            $this->loadPaymentSchedule();
        }
    }
    
    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedCuotas = $this->paymentSchedule->pluck('id')->toArray();
        } else {
            $this->selectedCuotas = [];
        }
    }
    
    public function loadPaymentSchedule()
    {
        $this->paymentSchedule = PaymentSchedule::where('matricula_id', $this->matricula_id)
            ->where('estado', '!=', 'pagado') // Mostrar también las parcialmente pagadas
            ->orderBy('numero_cuota')
            ->get();
    }

    public function updatedConceptoId()
    {
        if ($this->concepto_id && $this->matricula_id) {
            $concepto = ConceptoPago::find($this->concepto_id);
            if ($concepto) {
                // Si el concepto tiene un monto definido, usar ese monto
                if ($concepto->monto > 0) {
                    $this->monto = $concepto->monto;
                } else {
                    // Si no tiene monto definido, calcular según el nivel educativo y tipo de concepto
                    $matricula = Matricula::with('programa.nivelEducativo')->find($this->matricula_id);
                    if ($matricula && $matricula->programa && $matricula->programa->nivelEducativo) {
                        $nivel = $matricula->programa->nivelEducativo;

                        // Asignar montos según el nombre del concepto
                        if (stripos($concepto->nombre, 'matrícula') !== false && $nivel->costo_matricula > 0) {
                            $this->monto = $nivel->costo_matricula;
                        } elseif ((stripos($concepto->nombre, 'mensualidad') !== false || stripos($concepto->nombre, 'mensual') !== false) && $nivel->costo_mensualidad > 0) {
                            $this->monto = $nivel->costo_mensualidad;
                        } elseif (stripos($concepto->nombre, 'inicial') !== false && $matricula->cuota_inicial > 0) {
                            // Usar la cuota inicial de la matrícula
                            $this->monto = $matricula->cuota_inicial;
                        }
                    }
                }
            }
        }
    }

    public function agregarConcepto()
    {
        if (!$this->concepto_id) {
            session()->flash('error', 'Debe seleccionar un concepto de pago.');
            return;
        }

        $concepto = ConceptoPago::find($this->concepto_id);
        if (!$concepto) {
            session()->flash('error', 'Concepto de pago no encontrado.');
            return;
        }

        // Verificar si el concepto ya está en el carrito
        foreach ($this->carrito as $item) {
            if ($item['concepto_id'] == $this->concepto_id && !isset($item['cuota_id'])) {
                session()->flash('error', 'Este concepto ya ha sido agregado al carrito.');
                return;
            }
        }

        // Agregar al carrito
        $this->carrito[] = [
            'concepto_id' => $this->concepto_id,
            'concepto_nombre' => $concepto->nombre,
            'monto' => $this->monto ?? $concepto->monto ?? 0,
            'monto_pagado' => 0, // Inicialmente no se ha pagado nada
            'es_parcial' => false // Por defecto no es pago parcial
        ];

        $this->calcularTotal();

        // Limpiar selección
        $this->concepto_id = null;
        $this->monto = null;
    }
    
    public function agregarCuotasSeleccionadas()
    {
        if (empty($this->selectedCuotas)) {
            session()->flash('error', 'Debe seleccionar al menos una cuota.');
            return;
        }
        
        foreach ($this->selectedCuotas as $cuotaId) {
            $cuota = $this->paymentSchedule->firstWhere('id', $cuotaId);
            if ($cuota) {
                // Verificar si la cuota ya está en el carrito
                $existeEnCarrito = false;
                foreach ($this->carrito as $item) {
                    if (isset($item['cuota_id']) && $item['cuota_id'] == $cuotaId) {
                        $existeEnCarrito = true;
                        break;
                    }
                }
                
                if (!$existeEnCarrito) {
                    $descripcion = $cuota->numero_cuota == 0 ? 'Cuota inicial' : 'Cuota ' . $cuota->numero_cuota;
                    
                    $this->carrito[] = [
                        'concepto_id' => null, // No hay concepto específico para cuotas
                        'concepto_nombre' => $descripcion,
                        'monto' => $cuota->monto,
                        'monto_pagado' => $cuota->monto_pagado ?? 0, // Usar el monto ya pagado
                        'es_parcial' => false,
                        'cuota_id' => $cuota->id, // Identificador de la cuota
                        'numero_cuota' => $cuota->numero_cuota
                    ];
                }
            }
        }
        
        $this->calcularTotal();
        $this->selectedCuotas = []; // Limpiar selección
        $this->selectAll = false; // Desmarcar selección completa
    }

    public function togglePagoParcial($index)
    {
        $this->carrito[$index]['es_parcial'] = !$this->carrito[$index]['es_parcial'];

        // Si se marca como completo, establecer monto_pagado igual al monto restante
        if (!$this->carrito[$index]['es_parcial']) {
            $montoRestante = $this->carrito[$index]['monto'] - ($this->carrito[$index]['monto_pagado'] ?? 0);
            $this->carrito[$index]['monto_pagado'] += $montoRestante;
        } else {
            // Si se marca como parcial, permitir que el usuario ingrese el monto
            // No hacemos nada aquí, dejamos que el usuario ingrese el monto
        }
    }

    public function calcularTotal()
    {
        $this->total = 0;
        $totalPagado = 0;
        
        foreach ($this->carrito as $item) {
            $this->total += $item['monto'];
            $totalPagado += $item['monto_pagado'] ?? 0;
        }

        $this->dispatch('update-payment-totals', 
            total: $this->total,
            totalPagado: $totalPagado,
            saldoPendiente: $this->total - $totalPagado
        );
    }

    public function removerItem($index)
    {
        unset($this->carrito[$index]);
        $this->carrito = array_values($this->carrito); // Reindexar array
        $this->calcularTotal();
    }

    public function agregarCostosAutomaticos()
    {
        if (!$this->matricula_id) {
            session()->flash('error', 'Debe seleccionar una matrícula primero.');
            return;
        }

        $matricula = Matricula::with('programa.nivelEducativo')->find($this->matricula_id);
        if (!$matricula) {
            session()->flash('error', 'No se pudo obtener la información de la matrícula.');
            return;
        }

        if (!$matricula->programa) {
            session()->flash('error', 'La matrícula no tiene un programa asignado.');
            return;
        }

        if (!$matricula->programa->nivelEducativo) {
            session()->flash('error', 'El programa no tiene un nivel educativo asignado.');
            return;
        }

        $nivel = $matricula->programa->nivelEducativo;
        $costosAgregados = 0;

        // Agregar costo de matrícula si existe
        if ($nivel->costo_matricula > 0) {
            // Verificar si ya está en el carrito
            $existeEnCarrito = false;
            foreach ($this->carrito as $item) {
                if (stripos($item['concepto_nombre'], 'matrícula') !== false || stripos($item['concepto_nombre'], 'matricula') !== false) {
                    $existeEnCarrito = true;
                    break;
                }
            }

            // Verificar si ya se pagó en la base de datos
            $existeEnBase = Pago::where('matricula_id', $this->matricula_id)
                ->whereHas('conceptoPago', function ($query) {
                    $query->where('nombre', 'Matrícula');
                })
                ->where('estado', 'completado')
                ->exists();

            if (!$existeEnCarrito && !$existeEnBase) {
                $this->carrito[] = [
                    'concepto_id' => null,
                    'concepto_nombre' => 'Matrícula',
                    'monto' => $nivel->costo_matricula,
                    'monto_pagado' => 0,
                    'es_parcial' => false
                ];
                $costosAgregados++;
            }
        }

        // Agregar costo de mensualidad si existe
        if ($nivel->costo_mensualidad > 0) {
            // Verificar si ya está en el carrito
            $existeEnCarrito = false;
            foreach ($this->carrito as $item) {
                if (stripos($item['concepto_nombre'], 'mensualidad') !== false || stripos($item['concepto_nombre'], 'mensual') !== false) {
                    $existeEnCarrito = true;
                    break;
                }
            }

            // Verificar si ya se pagó en la base de datos
            $pagosMensuales = Pago::where('matricula_id', $this->matricula_id)
                ->whereHas('conceptoPago', function ($query) {
                    $query->where('nombre', 'like', '%Mensualidad%');
                })
                ->where('estado', 'completado')
                ->count();

            if (!$existeEnCarrito && $pagosMensuales < 10) { // Máximo 10 mensualidades
                $this->carrito[] = [
                    'concepto_id' => null,
                    'concepto_nombre' => 'Mensualidad',
                    'monto' => $nivel->costo_mensualidad,
                    'monto_pagado' => 0,
                    'es_parcial' => false
                ];
                $costosAgregados++;
            }
        }

        if ($costosAgregados > 0) {
            $this->calcularTotal();
            session()->flash('message', "Se agregaron $costosAgregados conceptos automáticamente.");
        } else {
            session()->flash('message', 'No se encontraron costos pendientes para agregar.');
        }
    }

    public function store()
    {
        $this->validate();

        if (empty($this->carrito)) {
            session()->flash('error', 'El carrito está vacío. Agregue al menos un concepto de pago.');
            return;
        }

        try {
            DB::beginTransaction();

            $pagosCreados = [];
            $comprobantes = [];

            foreach ($this->carrito as $item) {
                $pago = new Pago();
                $pago->matricula_id = $this->matricula_id;
                $pago->monto = $item['monto'];
                $pago->monto_pagado = $item['monto_pagado'] ?? 0;
                $pago->fecha_pago = $this->fecha_pago ?? now();
                $pago->metodo_pago = $this->metodo_pago;
                $pago->referencia = $this->referencia;
                
                // Determinar el estado del pago
                if ($item['es_parcial'] || ($item['monto_pagado'] ?? 0) < $item['monto']) {
                    $pago->estado = 'parcial';
                } else {
                    $pago->estado = 'completado';
                }
                $pago->empresa_id = auth()->user()->empresa_id;
                $pago->sucursal_id = auth()->user()->sucursal_id;
                $pago->user_id = auth()->user()->id;
                
                // Solo establecer concepto_pago_id si no es una cuota (cuota_id no está definido)
                if (!isset($item['cuota_id'])) {
                    $pago->concepto_pago_id = $item['concepto_id'];
                }
                
                $pago->save();

                // Si es una cuota, actualizar su estado
                if (isset($item['cuota_id'])) {
                    $cuota = PaymentSchedule::find($item['cuota_id']);
                    if ($cuota) {
                        // Actualizar el monto pagado en la cuota
                        $cuota->monto_pagado = ($cuota->monto_pagado ?? 0) + $pago->monto_pagado;
                        
                        // Determinar el estado de la cuota
                        if ($cuota->monto_pagado >= $cuota->monto) {
                            $cuota->estado = 'pagado';
                        } else {
                            $cuota->estado = 'parcial';
                        }
                        
                        $cuota->save();
                    }
                }

                $pagosCreados[] = $pago;

                // Generar comprobante para cada pago usando la relación polimórfica
                $comprobante = new Comprobante();
                $comprobante->numero = 'CMP-' . now()->format('Ymd') . '-' . str_pad($pago->id, 6, '0', STR_PAD_LEFT);
                $comprobante->fecha_emision = now();
                $comprobante->serie = 'CMP';
                $comprobante->tipo = 'comprobante_pago';
                $comprobante->contenido = [
                    'matricula_id' => $this->matricula_id,
                    'concepto_nombre' => $item['concepto_nombre'],
                    'monto' => $item['monto'],
                    'monto_pagado' => $item['monto_pagado'] ?? $item['monto'],
                    'fecha_pago' => $this->fecha_pago ?? now(),
                    'metodo_pago' => $this->metodo_pago,
                    'referencia' => $this->referencia,
                ];
                $pago->comprobante()->save($comprobante);

                $comprobantes[] = $comprobante;
            }

            DB::commit();

            // Limpiar formulario
            $this->reset(['concepto_id', 'monto', 'carrito', 'total', 'selectedCuotas', 'selectAll']);
            $this->paymentSchedule = collect(); // Limpiar tabla de amortización
            
            session()->flash('message', 'Pagos registrados exitosamente. Se generaron ' . count($comprobantes) . ' comprobantes.');
            
            // Redirigir a la lista de pagos
            return redirect()->route('admin.pagos.index');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar pagos: ' . $e->getMessage());
            session()->flash('error', 'Ocurrió un error al registrar los pagos: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.pagos.create')
            ->layout('components.layouts.admin', [
                'title' => 'Crear Pago',
                'description' => 'Registrar nuevos pagos en estilo de carrito de compras'
            ]);
    }
}