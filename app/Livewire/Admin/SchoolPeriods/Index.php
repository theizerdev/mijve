<?php

namespace App\Livewire\Admin\SchoolPeriods;

use App\Models\SchoolPeriod;
use App\Exports\SchoolPeriodsExport;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\Exportable;

class Index extends Component
{
    use WithPagination, Exportable;

    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $filters = [
        'status' => '',
        'date_range' => ''
    ];

    protected $listeners = ['schoolPeriodDeleted' => 'render'];

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
        return ['ID', 'Nombre', 'Descripción', 'Fecha Inicio', 'Fecha Fin', 'Actual', 'Activo'];
    }

    protected function formatExportRow($period): array
    {
        return [
            $period->id,
            $period->name,
            $period->description ?? 'N/A',
            $period->start_date->format('d/m/Y'),
            $period->end_date->format('d/m/Y'),
            $period->is_current ? 'Sí' : 'No',
            $period->is_active ? 'Activo' : 'Inactivo'
        ];
    }

    private function getBaseQuery()
    {
        return SchoolPeriod::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->filters['status'], function ($query) {
                switch ($this->filters['status']) {
                    case 'active':
                        return $query->where('is_active', true);
                    case 'inactive':
                        return $query->where('is_active', false);
                    case 'current':
                        return $query->where('is_current', true);
                    case 'past':
                        return $query->where('end_date', '<', now());
                    case 'future':
                        return $query->where('start_date', '>', now());
                }
            })
            ->when($this->filters['date_range'], function ($query) {
                $dates = explode(' - ', $this->filters['date_range']);
                if (count($dates) === 2) {
                    $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                    return $query->whereBetween('start_date', [$startDate, $endDate])
                                ->orWhereBetween('end_date', [$startDate, $endDate]);
                }
            });
    }

    public function render()
    {
        $query = $this->getBaseQuery()
            ->orderBy($this->sortField, $this->sortDirection);

        $schoolPeriods = $query->paginate($this->perPage);

        return view('livewire.admin.school-periods.index', compact('schoolPeriods'))
            ->layout('components.layouts.admin');
    }

    public function resetFilters()
    {
        $this->reset('filters');
    }

    public function exportExcel()
    {
        return Excel::download(new SchoolPeriodsExport, 'periodos-escolares.xlsx');
    }

    public function exportPDF()
    {
        return Excel::download(new SchoolPeriodsExport, 'periodos-escolares.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
    }

    public function delete(SchoolPeriod $schoolPeriod)
    {
        // Verificar si es el periodo escolar actual
        if ($schoolPeriod->is_current) {
            session()->flash('error', 'No se puede eliminar el periodo escolar actual.');
            return;
        }

        $schoolPeriod->delete();
        session()->flash('message', 'Periodo escolar eliminado exitosamente.');
        $this->dispatch('schoolPeriodDeleted');
    }

    public function setCurrent(SchoolPeriod $schoolPeriod)
    {
        // Desactivar el periodo escolar actual
        SchoolPeriod::where('is_current', true)->update(['is_current' => false]);
        
        // Establecer el nuevo periodo escolar como actual
        $schoolPeriod->update(['is_current' => true]);
        
        session()->flash('message', 'Periodo escolar actualizado exitosamente.');
        $this->dispatch('schoolPeriodDeleted');
    }

    public function toggleActive(SchoolPeriod $schoolPeriod)
    {
        // Si es el periodo escolar actual, no permitir desactivar
        if ($schoolPeriod->is_current && $schoolPeriod->is_active) {
            session()->flash('error', 'No se puede desactivar el periodo escolar actual.');
            return;
        }

        $schoolPeriod->update(['is_active' => !$schoolPeriod->is_active]);
        session()->flash('message', 'Estado actualizado exitosamente.');
    }
}