<?php

namespace App\Livewire\Admin\NivelesEducativos;

use App\Models\NivelEducativo;
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
        return ['ID', 'Nombre', 'Descripción', 'Status'];
    }

    protected function formatExportRow($nivel): array
    {
        return [
            $nivel->id,
            $nivel->nombre,
            $nivel->descripcion ?? 'N/A',
            $nivel->status ? 'Activo' : 'Inactivo'
        ];
    }

    private function getBaseQuery()
    {
        return NivelEducativo::query()
            ->when($this->search, fn($query) =>
                $query->where('nombre', 'like', '%'.$this->search.'%')
                    ->orWhere('descripcion', 'like', '%'.$this->search.'%')
            )
            ->when($this->status !== '', fn($query) =>
                $query->where('status', $this->status)
            );
    }

    public function render()
    {
        Gate::authorize('access niveles educativos', NivelEducativo::class);

        return view('livewire.admin.niveles-educativos.index', [
            'niveles' => $this->getBaseQuery()
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage)
        ])->layout('components.layouts.admin');
    }
}
