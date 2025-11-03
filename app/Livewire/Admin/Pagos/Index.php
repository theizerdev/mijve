<?php

namespace App\Livewire\Admin\Pagos;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Pago;
use App\Traits\Exportable;

class Index extends Component
{
    use WithPagination, Exportable;

    public $search = '';
    public $status = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10]
    ];

    public function getStatsProperty()
    {
        $baseQuery = Pago::query();
        
        if (!auth()->user()->hasRole('Super Administrador')) {
            $baseQuery->where('empresa_id', auth()->user()->empresa_id)
                      ->where('sucursal_id', auth()->user()->sucursal_id);
        }

        return [
            'total' => (clone $baseQuery)->count(),
            'aprobados' => (clone $baseQuery)->where('estado', 'aprobado')->count(),
            'pendientes' => (clone $baseQuery)->where('estado', 'pendiente')->count(),
            'ingresos_totales' => (clone $baseQuery)->where('estado', 'aprobado')->sum('total') ?: 0
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortBy = $field;
        $this->resetPage();
    }

    public function delete(Pago $pago)
    {
        // Verificar permiso para eliminar pagos
        if (!auth()->user()->can('delete pagos')) {
            session()->flash('error', 'No tienes permiso para eliminar pagos.');
            return;
        }

        try {
            $pago->delete();
            session()->flash('message', 'Pago eliminado correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el pago: ' . $e->getMessage());
        }

        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->status = '';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->perPage = 10;
        $this->resetPage();
    }

    public function toggleStatus($pagoId)
    {
        if (!auth()->user()->can('edit pagos')) {
            session()->flash('error', 'No tienes permiso para editar pagos.');
            return;
        }

        $pago = Pago::find($pagoId);
        if ($pago) {
            $pago->estado = $pago->estado === 'aprobado' ? 'pendiente' : 'aprobado';
            $pago->save();
        }
    }

    public function getExportQuery()
    {
        return $this->getQuery();
    }

    public function getExportHeaders()
    {
        return [
            'Documento', 'Estudiante', 'DNI', 'Total', 'Fecha', 'Estado', 'Método Pago'
        ];
    }

    public function formatExportRow($pago)
    {
        return [
            $pago->numero_completo,
            ($pago->matricula->student->nombres ?? '') . ' ' . ($pago->matricula->student->apellidos ?? ''),
            $pago->matricula->student->documento_identidad ?? '',
            $pago->total,
            $pago->fecha->format('d/m/Y'),
            ucfirst($pago->estado),
            $pago->metodo_pago ?? ''
        ];
    }

    private function getQuery()
    {
        $query = Pago::with(['matricula.student', 'detalles.conceptoPago', 'user', 'serieModel']);
        
        if (!auth()->user()->hasRole('Super Administrador')) {
            $query->where('empresa_id', auth()->user()->empresa_id)
                  ->where('sucursal_id', auth()->user()->sucursal_id);
        }

        return $query->when($this->search, function ($query) {
                $query->whereHas('matricula.student', function ($subQuery) {
                    $subQuery->where('nombres', 'like', '%' . $this->search . '%')
                        ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                        ->orWhere('documento_identidad', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('detalles.conceptoPago', function($subQuery) {
                    $subQuery->where('nombre', 'like', '%' . $this->search . '%');
                })
                ->orWhere('referencia', 'like', '%' . $this->search . '%')
                ->orWhere('serie', 'like', '%' . $this->search . '%')
                ->orWhere('numero', 'like', '%' . $this->search . '%');
            })
            ->when($this->status !== '', function ($query) {
                $query->where('estado', $this->status);
            })
            ->orderBy($this->sortBy, $this->sortDirection);
    }

    public function render()
    {
        $pagos = $this->getQuery()->paginate($this->perPage);

        return view('livewire.admin.pagos.index', compact('pagos'))
            ->layout('components.layouts.admin', [
                'title' => 'Pagos',
                'description' => 'Gestión de pagos de estudiantes'
            ]);
    }
}