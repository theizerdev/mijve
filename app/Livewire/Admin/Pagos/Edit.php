<?php

namespace App\Livewire\Admin\Pagos;

use Livewire\Component;
use App\Models\Pago;
use App\Models\Matricula;
use App\Models\ConceptoPago;

class Edit extends Component
{
    public Pago $pago;
    public $matricula_id;
    public $concepto_pago_id;
    public $monto;
    public $monto_pagado;
    public $fecha_pago;
    public $metodo_pago;
    public $referencia;
    public $estado;

    public $matriculas = [];
    public $conceptos = [];

    protected $rules = [
        'matricula_id' => 'required|exists:matriculas,id',
        'concepto_pago_id' => 'required|exists:conceptos_pago,id',
        'monto' => 'required|numeric|min:0',
        'monto_pagado' => 'required|numeric|min:0',
        'fecha_pago' => 'nullable|date',
        'metodo_pago' => 'required|in:efectivo,transferencia,tarjeta',
        'referencia' => 'nullable|string|max:255',
        'estado' => 'required|in:pendiente,parcial,pagado'
    ];

    public function mount(Pago $pago)
    {
        $this->pago = $pago;
        $this->matricula_id = $pago->matricula_id;
        $this->concepto_pago_id = $pago->concepto_pago_id;
        $this->monto = $pago->monto;
        $this->monto_pagado = $pago->monto_pagado;
        $this->fecha_pago = $pago->fecha_pago ? $pago->fecha_pago->format('Y-m-d') : null;
        $this->metodo_pago = $pago->metodo_pago;
        $this->referencia = $pago->referencia;
        $this->estado = $pago->estado;

        $this->loadData();
    }

    public function loadData()
    {
        $this->matriculas = Matricula::with('student', 'programa')->get();
        $this->conceptos = ConceptoPago::all();
    }

    public function update()
    {
        // Verificar permiso para editar pagos
        if (!auth()->user()->can('edit pagos')) {
            session()->flash('error', 'No tienes permiso para editar pagos.');
            return;
        }

        $this->validate();

        try {
            $this->pago->update([
                'matricula_id' => $this->matricula_id,
                'concepto_pago_id' => $this->concepto_pago_id,
                'monto' => $this->monto,
                'monto_pagado' => $this->monto_pagado,
                'fecha_pago' => $this->fecha_pago,
                'metodo_pago' => $this->metodo_pago,
                'referencia' => $this->referencia,
                'estado' => $this->estado,
            ]);

            session()->flash('message', 'Pago actualizado correctamente.');
            return redirect()->route('admin.pagos.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el pago: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.pagos.edit')
            ->layout('components.layouts.admin', [
                'title' => 'Editar Pago',
                'description' => 'Modificar información del pago'
            ]);
    }
}