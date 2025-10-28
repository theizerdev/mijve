<?php

namespace App\Livewire\Admin\ConceptosPago;

use Livewire\Component;
use App\Models\ConceptoPago;

class Edit extends Component
{
    public $concepto;
    public $nombre;
    public $descripcion;
    public $activo;

    public function mount(ConceptoPago $concepto)
    {
        // Verificar permiso para editar conceptos de pago
        if (!auth()->user()->can('edit conceptos_pago')) {
            session()->flash('error', 'No tienes permiso para editar conceptos de pago.');
            return redirect()->route('admin.conceptos-pago.index');
        }

        $this->concepto = $concepto;
        $this->nombre = $concepto->nombre;
        $this->descripcion = $concepto->descripcion;
        $this->activo = $concepto->activo;
    }

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'activo' => 'boolean'
        ];
    }

    public function update()
    {
        // Verificar permiso para editar conceptos de pago
        if (!auth()->user()->can('edit conceptos_pago')) {
            session()->flash('error', 'No tienes permiso para editar conceptos de pago.');
            return;
        }

        $this->validate();

        try {
            $this->concepto->update([
                'nombre' => $this->nombre,
                'descripcion' => $this->descripcion,
                'activo' => $this->activo
            ]);

            session()->flash('message', 'Concepto de pago actualizado correctamente.');
            return redirect()->route('admin.conceptos-pago.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el concepto de pago: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.conceptos-pago.edit')
            ->layout('components.layouts.admin', [
                'title' => 'Editar Concepto de Pago',
                'description' => 'Actualizar información del concepto de pago'
            ]);
    }
}