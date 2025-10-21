<?php

namespace App\Livewire\Admin\SchoolYears;

use App\Models\SchoolYear;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $listeners = ['schoolYearDeleted' => 'render'];

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
        $schoolYears = SchoolYear::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.school-years.index', compact('schoolYears'))

        ->layout('components.layouts.admin', [
                'title' => 'Listado del año escolar'
        ]);
    }

    public function delete(SchoolYear $schoolYear)
    {
        // Verificar si es el año escolar actual
        if ($schoolYear->is_current) {
            session()->flash('error', 'No se puede eliminar el año escolar actual.');
            return;
        }

        $schoolYear->delete();
        session()->flash('message', 'Año escolar eliminado exitosamente.');
        $this->dispatch('schoolYearDeleted');
    }

    public function setCurrent(SchoolYear $schoolYear)
    {
        // Desactivar el año escolar actual
        SchoolYear::where('is_current', true)->update(['is_current' => false]);

        // Establecer el nuevo año escolar como actual
        $schoolYear->update(['is_current' => true]);

        session()->flash('message', 'Año escolar actualizado exitosamente.');
        $this->dispatch('schoolYearDeleted');
    }

    public function toggleActive(SchoolYear $schoolYear)
    {
        // Si es el año escolar actual, no permitir desactivar
        if ($schoolYear->is_current && $schoolYear->is_active) {
            session()->flash('error', 'No se puede desactivar el año escolar actual.');
            return;
        }

        $schoolYear->update(['is_active' => !$schoolYear->is_active]);
        session()->flash('message', 'Estado actualizado exitosamente.');
    }
}
