<?php

namespace App\Livewire\Admin\Turnos;

use App\Models\Turno;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Gate;
use App\Traits\Exportable;

class Index extends Component
{
    use WithPagination, Exportable;

    public $search = '';
    public $status = '';
    public $perPage = 10;
    public $sortField = 'nombre';
    public $sortDirection = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'perPage',
        'sortField',
        'sortDirection'
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    protected function getExportQuery()
    {
        return $this->getBaseQuery();
    }

    protected function getExportHeaders(): array
    {
        return ['ID', 'Nombre', 'Hora Inicio', 'Hora Fin', 'Status'];
    }

    protected function formatExportRow($turno): array
    {
        return [
            $turno->id,
            $turno->nombre,
            $turno->hora_inicio,
            $turno->hora_fin,
            $turno->status ? 'Activo' : 'Inactivo'
        ];
    }

    private function getBaseQuery()
    {
        return Turno::query()
            ->when($this->search, fn($query) =>
                $query->where('nombre', 'like', '%'.$this->search.'%')
                    ->orWhere('hora_inicio', 'like', '%'.$this->search.'%')
                    ->orWhere('hora_fin', 'like', '%'.$this->search.'%')
            )
            ->when($this->status !== '', fn($query) =>
                $query->where('status', $this->status)
            );
    }

    public function render()
    {
        Gate::authorize('access turnos');

        return view('livewire.admin.turnos.index', [
            'turnos' => $this->getBaseQuery()
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage)
        ])->layout('components.layouts.admin');
    }
}
