<?php

namespace App\Livewire\Admin\MetodosPago;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MetodoPago;
use App\Models\Empresa;
use Illuminate\Support\Facades\Auth;
use App\Traits\Exportable;

class Index extends Component
{
    use WithPagination, Exportable, HasDynamicLayout;

    public $search = '';
    public $tipo_pago = '';
    public $empresa_id = '';
    public $status = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'tipo_pago' => ['except' => ''],
        'empresa_id' => ['except' => ''],
        'status' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10]
    ];

    public function mount()
    {
        if (!Auth::user()->can('access metodos_pago')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTipoPago()
    {
        $this->resetPage();
    }

    public function updatingEmpresaId()
    {
        $this->resetPage();
    }

    public function updatingStatus()
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
        return ['ID', 'Empresa', 'Tipo de Pago', 'Banco', 'Cédula', 'Teléfono', 'Cuenta', 'Status'];
    }

    protected function formatExportRow($metodo): array
    {
        return [
            $metodo->id,
            $metodo->empresa->razon_social ?? 'N/A',
            $metodo->tipo_pago,
            $metodo->banco ?? 'N/A',
            $metodo->cedula ?? 'N/A',
            $metodo->telefono ?? 'N/A',
            $metodo->numero_cuenta ?? 'N/A',
            $metodo->status ? 'Activo' : 'Inactivo'
        ];
    }

    private function getBaseQuery()
    {
        return MetodoPago::forUser()->with('empresa')
            ->when($this->search, function ($query) {
                $query->where(function($q) {
                    $q->where('banco', 'like', '%' . $this->search . '%')
                      ->orWhere('cedula', 'like', '%' . $this->search . '%')
                      ->orWhere('telefono', 'like', '%' . $this->search . '%')
                      ->orWhere('nombre', 'like', '%' . $this->search . '%')
                      ->orWhere('apellido', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->tipo_pago, function ($query) {
                $query->where('tipo_pago', $this->tipo_pago);
            })
            ->when($this->empresa_id, function ($query) {
                $query->where('empresa_id', $this->empresa_id);
            })
            ->when($this->status !== '', function ($query) {
                $query->where('status', $this->status === 'active');
            });
    }

    public function render()
    {
        $metodos = $this->getBaseQuery()
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        // Calcular estadísticas
        $totalMetodos = MetodoPago::forUser()->count();
        $metodosActivos = MetodoPago::forUser()->where('status', true)->count();
        $metodosInactivos = MetodoPago::forUser()->where('status', false)->count();

        return view('livewire.admin.metodos-pago.index', [
            'metodos' => $metodos,
            'empresas' => Empresa::where('status', true)->get(),
            'totalMetodos' => $totalMetodos,
            'metodosActivos' => $metodosActivos,
            'metodosInactivos' => $metodosInactivos
        ])->layout($this->getLayout());
    }

    public function toggleStatus(MetodoPago $metodo)
    {
        if (!Auth::user()->can('edit metodos_pago')) {
            session()->flash('error', 'No tienes permiso para editar métodos de pago.');
            return;
        }

        $metodo->status = !$metodo->status;
        $metodo->save();

        session()->flash('message', 'Estado actualizado correctamente.');
    }
}
