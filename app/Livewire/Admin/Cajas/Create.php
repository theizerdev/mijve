<?php

namespace App\Livewire\Admin\Cajas;

use App\Models\Caja;
use Livewire\Component;

class Create extends Component
{
    public $monto_inicial = 0;
    public $observaciones_apertura = '';

    protected $rules = [
        'monto_inicial' => 'required|numeric|min:0',
        'observaciones_apertura' => 'nullable|string|max:500',
    ];

    public function mount()
    {
        // Verificar si ya existe una caja abierta para hoy
        $cajaExistente = Caja::obtenerCajaAbierta(
            auth()->user()->empresa_id,
            auth()->user()->sucursal_id
        );

        if ($cajaExistente) {
            session()->flash('error', 'Ya existe una caja abierta para el día de hoy.');
            return redirect()->route('admin.cajas.index');
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $caja = Caja::crearCajaDiaria(
                auth()->user()->empresa_id,
                auth()->user()->sucursal_id,
                $this->monto_inicial,
                $this->observaciones_apertura
            );

            session()->flash('message', 'Caja abierta exitosamente.');
            return redirect()->route('admin.cajas.show', $caja);
        } catch (\Exception $e) {
            session()->flash('error', 'Error al abrir la caja: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.cajas.create')->layout('components.layouts.admin', [
            'title' => 'Abrir Caja'
        ]);
    }
}