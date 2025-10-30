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
        return NivelEducativo::query()
            ->when($this->search, function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%');
            })
            ->when($this->status !== '', function ($query) {
                $query->where('status', $this->status);
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    protected function getExportHeaders()
    {
        return [
            'ID',
            'Nombre',
            'Descripción',
            'Estado',
            'Fecha de Creación',
            'Fecha de Actualización'
        ];
    }

    protected function formatExportRow($row)
    {
        return [
            $row->id,
            $row->nombre,
            $row->descripcion ?? '-',
            $row->status ? 'Activo' : 'Inactivo',
            $row->created_at->format('Y-m-d H:i:s'),
            $row->updated_at->format('Y-m-d H:i:s')
        ];
    }

    public function delete(NivelEducativo $nivel)
    {
        if (!auth()->user()->can('delete', $nivel)) {
            session()->flash('error', 'No tienes permiso para eliminar este nivel educativo.');
            return;
        }

        // Verificar si hay programas asociados
        if ($nivel->programas()->count() > 0) {
            session()->flash('error', 'No se puede eliminar este nivel educativo porque tiene programas asociados.');
            return;
        }

        $nivel->delete();
        session()->flash('message', 'Nivel Educativo eliminado exitosamente.');
    }

    public function render()
    {
        $niveles = NivelEducativo::query()
            ->when($this->search, function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%');
            })
            ->when($this->status !== '', function ($query) {
                $query->where('status', $this->status);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.niveles-educativos.index', compact('niveles'))
            ->layout('components.layouts.admin', [
                'title' => 'Niveles Educativos',
                'breadcrumb' => [
                    'admin.dashboard' => 'Dashboard',
                    'admin.niveles-educativos.index' => 'Niveles Educativos'
                ]
            ]);
    }
}