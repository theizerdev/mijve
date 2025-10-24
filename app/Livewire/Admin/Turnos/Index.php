<?php

namespace App\Livewire\Admin\Turnos;

use App\Models\Turno;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Gate;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $sortField = 'nombre';
    public $sortDirection = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage',
        'sortField',
        'sortDirection'
    ];

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function render()
    {
        Gate::authorize('access turnos');

        return view('livewire.admin.turnos.index', [
            'turnos' => Turno::query()
                ->when($this->search, fn($query) =>
                    $query->where('nombre', 'like', '%'.$this->search.'%')
                        ->orWhere('hora_inicio', 'like', '%'.$this->search.'%')
                        ->orWhere('hora_fin', 'like', '%'.$this->search.'%')
                )
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage)
        ])->layout('components.layouts.admin');
    }
}
