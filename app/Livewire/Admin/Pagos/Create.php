<?php

namespace App\Livewire\Admin\Pagos;

use Livewire\Component;
use App\Models\Pago;
use App\Models\Matricula;
use App\Models\ConceptoPago;
use App\Models\EducationalLevel;

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
            if ($item['concepto_id'] == $this->concepto_id) {
                session()->flash('error', 'Este concepto ya ha sido agregado al carrito.');
                return;
            }
        }

        // Agregar al carrito
        $this->carrito[] = [
            'concepto_id' => $this->concepto_id,
            'concepto_nombre' => $concepto->nombre,
            'monto' => $this->monto ?? $concepto->monto ?? 0,
        ];

        $this->calcularTotal();
        
        // Limpiar selección
        $this->concepto_id = null;
        $this->monto = null;
    }

    public function calcularTotal()
    {
        $this->total = 0;
        foreach ($this->carrito as $item) {
            $this->total += $item['monto'];
        }
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
                })->exists();

            if (!$existeEnCarrito && !$existeEnBase) {
                // Crear un concepto de pago temporal para matrícula
                $conceptoMatricula = ConceptoPago::firstOrCreate(
                    ['nombre' => 'Matrícula'],
                    [
                        'descripcion' => 'Pago de matrícula',
                        'monto' => $nivel->costo_matricula,
                        'activo' => true
                    ]
                );

                $this->carrito[] = [
                    'concepto_id' => $conceptoMatricula->id,
                    'concepto_nombre' => 'Matrícula',
                    'monto' => $nivel->costo_matricula,
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
            $existeEnBase = Pago::where('matricula_id', $this->matricula_id)
                ->whereHas('conceptoPago', function ($query) {
                    $query->where('nombre', 'Mensualidad');
                })->exists();

            if (!$existeEnCarrito && !$existeEnBase) {
                // Crear un concepto de pago temporal para mensualidad
                $conceptoMensualidad = ConceptoPago::firstOrCreate(
                    ['nombre' => 'Mensualidad'],
                    [
                        'descripcion' => 'Pago de mensualidad',
                        'monto' => $nivel->costo_mensualidad,
                        'activo' => true
                    ]
                );

                $this->carrito[] = [
                    'concepto_id' => $conceptoMensualidad->id,
                    'concepto_nombre' => 'Mensualidad',
                    'monto' => $nivel->costo_mensualidad,
                ];
                
                $costosAgregados++;
            }
        }

        // Agregar cuota inicial si existe
        if ($matricula->cuota_inicial > 0) {
            // Verificar si ya está en el carrito
            $existeEnCarrito = false;
            foreach ($this->carrito as $item) {
                if (stripos($item['concepto_nombre'], 'inicial') !== false) {
                    $existeEnCarrito = true;
                    break;
                }
            }

            // Verificar si ya se pagó en la base de datos
            $existeEnBase = Pago::where('matricula_id', $this->matricula_id)
                ->whereHas('conceptoPago', function ($query) {
                    $query->where('nombre', 'Cuota Inicial');
                })->exists();

            if (!$existeEnCarrito && !$existeEnBase) {
                // Crear un concepto de pago temporal para cuota inicial
                $conceptoInicial = ConceptoPago::firstOrCreate(
                    ['nombre' => 'Cuota Inicial'],
                    [
                        'descripcion' => 'Pago de cuota inicial',
                        'monto' => $matricula->cuota_inicial,
                        'activo' => true
                    ]
                );

                $this->carrito[] = [
                    'concepto_id' => $conceptoInicial->id,
                    'concepto_nombre' => 'Cuota Inicial',
                    'monto' => $matricula->cuota_inicial,
                ];
                
                $costosAgregados++;
            }
        }

        $this->calcularTotal();
        
        // Mostrar mensaje de éxito
        if ($costosAgregados > 0) {
            session()->flash('message', 'Se agregaron ' . $costosAgregados . ' costos automáticos al carrito.');
        } else {
            session()->flash('message', 'No se agregaron costos automáticos. Pueden estar ya en el carrito o ya haber sido pagados.');
        }
    }

    public function store()
    {
        // Verificar permiso para crear pagos
        if (!auth()->user()->can('create pagos')) {
            session()->flash('error', 'No tienes permiso para registrar pagos.');
            return;
        }

        $this->validate();

        try {
            // Registrar todos los pagos del carrito
            foreach ($this->carrito as $item) {
                Pago::create([
                    'matricula_id' => $this->matricula_id,
                    'concepto_pago_id' => $item['concepto_id'],
                    'monto' => $item['monto'],
                    'monto_pagado' => $item['monto'], // En este caso se paga el total
                    'fecha_pago' => $this->fecha_pago,
                    'metodo_pago' => $this->metodo_pago,
                    'referencia' => $this->referencia,
                    'estado' => 'pagado', // Marcar como pagado ya que se está registrando el pago
                ]);
            }

            session()->flash('message', 'Pagos registrados correctamente (' . count($this->carrito) . ' conceptos).');
            return redirect()->route('admin.pagos.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al registrar los pagos: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.pagos.create')
            ->layout('components.layouts.admin', [
                'title' => 'Registrar Pago',
                'description' => 'Registrar nuevos pagos en estilo de carrito de compras'
            ]);
    }
}