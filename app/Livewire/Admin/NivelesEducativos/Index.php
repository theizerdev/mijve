<?php

namespace App\Livewire\Admin\NivelesEducativos;
use App\Traits\HasDynamicLayout;

use App\Models\EducationalLevel;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Gate;
use App\Traits\Exportable;

class Index extends Component
{
    use WithPagination, Exportable, HasDynamicLayout;

    public $search = '';
    public $status = '';
    public $perPage = 10;
    public $sortField = 'nombre';
    public $sortDirection = 'asc';
    
    // Controles de UI
    public $selectedNiveles = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'perPage',
        'sortField',
        'sortDirection'
    ];

    protected $listeners = [
        'refreshNiveles' => '$refresh'
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

    public function toggleAdvancedFilters()
    {
        $this->showAdvancedFilters = !$this->showAdvancedFilters;
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->status = '';
        $this->perPage = 10;
        $this->sortField = 'nombre';
        $this->sortDirection = 'asc';
        $this->resetPage();
    }

    public function updatedSelectedNiveles()
    {
        // Esta función se ejecuta cuando se seleccionan/deseleccionan niveles
    }

    protected function getExportQuery()
    {
        return EducationalLevel::query()
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

    public function delete(EducationalLevel $nivel)
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
        
        // Refrescar la lista
        $this->dispatch('refreshNiveles');
    }
	
	public function toggleStatus($value)
	{
		$pais = EducationalLevel::findOrFail($value);
        $pais->update(['status' => !$pais->status]);
        session()->flash('message', $pais->status ? 'Nivel Educativo activado exitosamente.' : 'Nivel Educativo desactivado exitosamente.');
	}
    public function render()
    {
        $niveles = EducationalLevel::query()
            ->when($this->search, function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%');
            })
            ->when($this->status !== '', function ($query) {
                $query->where('status', $this->status);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        // Estadísticas
        $totalNiveles = EducationalLevel::count();
        $nivelesActivos = EducationalLevel::where('status', 1)->count();
        $totalProgramas = \App\Models\Programa::count();
        $totalEstudiantes = \App\Models\Student::count();

        return view('livewire.admin.niveles-educativos.index', compact('niveles', 'totalNiveles', 'nivelesActivos', 'totalProgramas', 'totalEstudiantes'))
            ->layout($this->getLayout(), [
                'title' => 'Niveles Educativos',
                'breadcrumb' => [
                    'admin.dashboard' => 'Dashboard',
                    'admin.niveles-educativos.index' => 'Niveles Educativos'
                ]
            ]);
    }
}