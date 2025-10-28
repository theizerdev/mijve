<?php

namespace App\Livewire\Admin\ConceptosPago;

use Livewire\Component;
use App\Models\ConceptoPago;

class Create extends Component
{
    public $nombre;
    public $descripcion;
    public $activo = true;

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'activo' => 'boolean'
        ];
    }

    public function store()
    {
        // Verificar permiso para crear conceptos de pago
        if (!auth()->user()->can('create conceptos_pago')) {
            session()->flash('error', 'No tienes permiso para crear conceptos de pago.');
            return;
        }

        $this->validate();

        try {
            ConceptoPago::create([
                'nombre' => $this->nombre,
                'descripcion' => $this->descripcion,
                'activo' => $this->activo
            ]);

            session()->flash('message', 'Concepto de pago creado correctamente.');
            return redirect()->route('admin.conceptos-pago.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear el concepto de pago: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.conceptos-pago.create')
            ->layout('components.layouts.admin', [
                'title' => 'Crear Concepto de Pago',
                'description' => 'Registrar un nuevo concepto de pago'
            ]);
    }
}