<?php

namespace App\Livewire\Admin\Actividades;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Actividad;
use App\Models\Empresa;
use Illuminate\Support\Facades\Auth;
use App\Traits\Exportable;

class Index extends Component
{
    use WithPagination, Exportable, HasDynamicLayout;

    public $search = '';
    public $status = '';
    public $empresa_id = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'empresa_id' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10]
    ];

    public function mount()
    {
        // Verificar permiso para ver actividades
        if (!Auth::user()->can('access actividades')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingEmpresaId()
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
    }

    protected function getExportQuery()
    {
        return $this->getBaseQuery();
    }

    protected function getExportHeaders(): array
    {
        return ['ID', 'Empresa', 'Nombre', 'Fecha Inicio', 'Fecha Fin', 'Status', 'Edad Desde', 'Edad Hasta'];
    }

    protected function formatExportRow($actividad): array
    {
        return [
            $actividad->id,
            $actividad->empresa->razon_social ?? 'N/A',
            $actividad->nombre,
            $actividad->fecha_inicio ? $actividad->fecha_inicio->format('d/m/Y') : '',
            $actividad->fecha_fin ? $actividad->fecha_fin->format('d/m/Y') : '',
            $actividad->status,
            $actividad->edad_desde,
            $actividad->edad_hasta
        ];
    }

    private function getBaseQuery()
    {
        return Actividad::forUser()->with('empresa')
            ->when($this->search, function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%')
                      ->orWhere('descripcion', 'like', '%' . $this->search . '%');
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->empresa_id, function ($query) {
                $query->where('empresa_id', $this->empresa_id);
            });
    }

    public function render()
    {
        $actividades = $this->getBaseQuery()
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        // Calcular estadísticas
        $totalActividades = Actividad::forUser()->count();
        $actividadesActivas = Actividad::forUser()->where('status', 'Activo')->count();
        $actividadesInactivas = Actividad::forUser()->where('status', 'Inactivo')->count();

        return view('livewire.admin.actividades.index', [
            'actividades' => $actividades,
            'empresas' => Empresa::where('status', true)->get(),
            'totalActividades' => $totalActividades,
            'actividadesActivas' => $actividadesActivas,
            'actividadesInactivas' => $actividadesInactivas
        ])->layout($this->getLayout());
    }

    public function toggleStatus(Actividad $actividad)
    {
        if (!Auth::user()->can('edit actividades')) {
            session()->flash('error', 'No tienes permiso para editar actividades.');
            return;
        }

        $actividad->status = $actividad->status === 'Activo' ? 'Inactivo' : 'Activo';
        $actividad->save();

        session()->flash('message', 'Estado de actividad actualizado correctamente.');
    }
}
