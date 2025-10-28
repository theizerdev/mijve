<?php

namespace App\Livewire\Admin\Pagos;

use Livewire\Component;
use App\Models\Pago;

class Show extends Component
{
    public $pago;

    public function mount(Pago $pago)
    {
        $this->pago = $pago->load(['matricula.student', 'matricula.programa', 'concepto']);
    }

    public function render()
    {
        return view('livewire.admin.pagos.show')
            ->layout('components.layouts.admin', [
                'title' => 'Ver Pago',
                'description' => 'Detalles del pago realizado'
            ]);
    }
}