<?php

namespace App\Livewire\Admin\Pagos;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Pago;
use Illuminate\Support\Facades\Auth;

class Show extends Component
{
    use HasDynamicLayout;

    public $pago;

    public function mount(Pago $pago)
    {
        if (!Auth::user()->can('access pagos')) {
            abort(403, 'No tienes permiso para ver pagos.');
        }
        
        $this->pago = $pago->load(['participante', 'actividad', 'metodoPago', 'empresa', 'sucursal']);
    }

    public function render()
    {
        return view('livewire.admin.pagos.show')->layout($this->getLayout());
    }
}
