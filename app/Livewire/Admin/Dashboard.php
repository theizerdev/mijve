<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Traits\HasDynamicLayout;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{


    use HasDynamicLayout;

   


    public function mount()
    {
        // Verificar si hay usuario autenticado
        if (!auth()->check()) {
            // Si no hay usuario autenticado, redirigir al login
            return redirect()->route('login');
        }

     }


     
    public function render()
    {
        // Verificar autenticación antes de procesar
        if (!auth()->check()) {
            return redirect()->route('login');
        }

    
        return view('livewire.admin.dashboard')->layout($this->getLayout());
    }

}