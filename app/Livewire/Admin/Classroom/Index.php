<?php

namespace App\Livewire\Admin\Classroom;

use App\Models\Classroom;
use App\Models\Empresa;
use App\Models\Sucursal;
use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\HasDynamicLayout;

class Index extends Component
{
    use WithPagination, HasDynamicLayout;

    public $search = '', $perPage = 10;
    public $sortField = 'nombre', $sortDirection = 'asc';
    public $empresa_id = '', $sucursal_id = '', $tipo_aula = '', $estado = '';
    
    public $confirmingDeletion = false;
    public $classroomToDelete;

    protected $queryString = ['search', 'perPage', 'sortField', 'sortDirection', 'empresa_id', 'sucursal_id', 'tipo_aula', 'estado'];

    protected $rules = [
        'classroomToDelete' => 'required|exists:classrooms,id',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedEmpresaId()
    {
        $this->sucursal_id = '';
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

    public function render()
    {
        $classrooms = Classroom::with(['empresa', 'sucursal'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->search . '%')
                      ->orWhere('codigo', 'like', '%' . $this->search . '%')
                      ->orWhere('ubicacion', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->empresa_id, function ($query) {
                $query->where('empresa_id', $this->empresa_id);
            })
            ->when($this->sucursal_id, function ($query) {
                $query->where('sucursal_id', $this->sucursal_id);
            })
            ->when($this->tipo_aula, function ($query) {
                $query->where('tipo_aula', $this->tipo_aula);
            })
            ->when($this->estado !== '', function ($query) {
                $query->where('is_active', $this->estado);
            })
            ->when(auth()->user()->empresa_id, function ($query) {
                $query->where('empresa_id', auth()->user()->empresa_id);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $empresas = Empresa::all();
        $sucursales = Sucursal::when($this->empresa_id, function ($query) {
            $query->where('empresa_id', $this->empresa_id);
        }, function ($query) {
            if (auth()->user()->empresa_id) {
                $query->where('empresa_id', auth()->user()->empresa_id);
            }
        })->get();

        return $this->renderWithLayout('livewire.admin.classroom.index', [
            'classrooms' => $classrooms,
            'empresas' => $empresas,
            'sucursales' => $sucursales,
        ], [
            'title' => 'Gestión de Aulas',
            'breadcrumb' => [
                'admin.dashboard' => 'Dashboard',
                'admin.classrooms.index' => 'Aulas'
            ]
        ]);
    }

    public function confirmDelete($id)
    {
        $this->classroomToDelete = $id;
        $this->confirmingDeletion = true;
    }

    public function delete()
    {
        $this->validate();
        
        $classroom = Classroom::findOrFail($this->classroomToDelete);
        
        // Verificar si el aula está siendo usada en algún horario
        if ($classroom->schedules()->exists()) {
            session()->flash('error', 'No se puede eliminar el aula porque tiene horarios asignados.');
            $this->confirmingDeletion = false;
            return;
        }

        $classroom->delete();
        session()->flash('message', 'Aula eliminada exitosamente.');
        $this->confirmingDeletion = false;
    }

    public function toggleStatus($id)
    {
        $classroom = Classroom::findOrFail($id);
        $classroom->update([
            'is_active' => !$classroom->is_active,
            'updated_by' => auth()->id()
        ]);
        session()->flash('message', 'Estado del aula actualizado.');
    }

    public function resetFilters()
    {
        $this->reset(['search', 'empresa_id', 'sucursal_id', 'tipo_aula', 'estado']);
        $this->resetPage();
    }
}