<?php

namespace App\Livewire\Admin\Pagos;

use Livewire\Component;
use App\Models\Comprobante;

class Comprobantes extends Component
{
    public Comprobante $comprobante;

    public function mount($comprobante)
    {


        $comprobanteId = $comprobante->id;

        $this->comprobante = Comprobante::with([
            'comprobanteable',
            'comprobanteable.user',
            'comprobanteable.matricula.student',
            'comprobanteable.matricula.programa',
            'comprobanteable.conceptoPago'
        ])->findOrFail($comprobanteId);
    }

    public function render()
    {
        return view('livewire.admin.pagos.comprobante')
            ->layout('components.layouts.admin', [
                'title' => 'Comprobante de Pago',
                'description' => 'Visualización de comprobante de pago'
            ]);
    }
}
