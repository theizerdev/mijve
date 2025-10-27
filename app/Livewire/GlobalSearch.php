<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\Student;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Sucursal;

class GlobalSearch extends Component
{
    public $search = '';
    public $results = [];
    public $showResults = false;

    public function updatedSearch()
    {
        if (strlen($this->search) < 3) {
            $this->results = [];
            $this->showResults = false;
            return;
        }

        $this->results = [
            'students' => Student::query()
                ->where('nombres', 'like', "%{$this->search}%")
                ->orWhere('apellidos', 'like', "%{$this->search}%")
                ->orWhere('codigo', 'like', "%{$this->search}%")
                ->limit(5)->get(),
            'users' => User::forUser()
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")
                ->limit(5)->get(),
            'empresas' => Empresa::forUser()
                ->where('razon_social', 'like', "%{$this->search}%")
                ->limit(5)->get(),
            'sucursales' => Sucursal::forUser()
                ->where('nombre', 'like', "%{$this->search}%")
                ->limit(5)->get(),
        ];

        $this->showResults = true;
    }

    public function closeResults()
    {
        $this->showResults = false;
    }

    public function render()
    {
        return view('livewire.global-search');
    }
}
