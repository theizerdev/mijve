<?php

namespace App\Livewire\Admin\Cajas;

use App\Models\Caja;
use App\Traits\Exportable;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, Exportable;

    public $search = '';
    public $status = '';
    public $perPage = 10;
    public $sortBy = 'fecha';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'perPage' => ['except' => 10],
        'sortBy' => ['except' => 'fecha'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'status', 'perPage']);
        $this->resetPage();
    }

    public function cerrarCaja($cajaId)
    {
        $caja = Caja::findOrFail($cajaId);
        
        if ($caja->cerrar()) {
            session()->flash('message', 'Caja cerrada exitosamente.');
        } else {
            session()->flash('error', 'No se pudo cerrar la caja.');
        }
    }

    public function getStatsProperty()
    {
        $baseQuery = Caja::where('empresa_id', auth()->user()->empresa_id)
            ->where('sucursal_id', auth()->user()->sucursal_id);

        return [
            'total' => (clone $baseQuery)->count() ?: 0,
            'abiertas' => (clone $baseQuery)->where('estado', 'abierta')->count() ?: 0,
            'cerradas' => (clone $baseQuery)->where('estado', 'cerrada')->count() ?: 0,
            'ingresos_hoy' => (clone $baseQuery)->whereDate('fecha', today())->sum('total_ingresos') ?: 0,
        ];
    }

    public function getExportQuery()
    {
        $query = Caja::with(['usuario', 'sucursal'])
            ->where('empresa_id', auth()->user()->empresa_id)
            ->where('sucursal_id', auth()->user()->sucursal_id);

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereDate('fecha', 'like', '%' . $this->search . '%')
                  ->orWhereHas('usuario', function ($q) {
                      $q->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->status) {
            $query->where('estado', $this->status);
        }

        return $query->orderBy($this->sortBy, $this->sortDirection);
    }

    public function getExportHeaders()
    {
        return [
            'Fecha',
            'Usuario',
            'Monto Inicial',
            'Total Efectivo',
            'Total Transferencias',
            'Total Tarjetas',
            'Total Ingresos',
            'Monto Final',
            'Estado',
            'Fecha Apertura',
            'Fecha Cierre'
        ];
    }

    public function formatExportRow($caja)
    {
        return [
            $caja->fecha->format('d/m/Y'),
            $caja->usuario->name ?? '',
            number_format($caja->monto_inicial, 2),
            number_format($caja->total_efectivo, 2),
            number_format($caja->total_transferencias, 2),
            number_format($caja->total_tarjetas, 2),
            number_format($caja->total_ingresos, 2),
            number_format($caja->monto_final, 2),
            ucfirst($caja->estado),
            $caja->fecha_apertura->format('d/m/Y H:i'),
            $caja->fecha_cierre ? $caja->fecha_cierre->format('d/m/Y H:i') : ''
        ];
    }

    public function render()
    {
        $cajas = $this->getExportQuery()->paginate($this->perPage);
        return view('livewire.admin.cajas.index', compact('cajas'))->layout('components.layouts.admin', [
            'title' => 'Cajas'
        ]);
    }
}