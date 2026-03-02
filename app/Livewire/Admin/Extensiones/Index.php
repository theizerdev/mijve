<?php

namespace App\Livewire\Admin\Extensiones;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Extension;
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
        // Verificar permiso para ver extensiones
        // Asumiendo que el permiso es 'access extensiones' o similar, ajustar según necesidad
        // if (!Auth::user()->can('access extensiones')) {
        //     abort(403, 'No tienes permiso para acceder a esta sección.');
        // }
    }

    public function updatedSearch() { $this->resetPage(); }
    public function updatedStatus() { $this->resetPage(); }
    public function updatedEmpresaId() { $this->resetPage(); }

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
        return ['ID', 'Empresa', 'Nombre', 'Zona', 'Distrito', 'Status'];
    }

    protected function formatExportRow($extension): array
    {
        return [
            $extension->id,
            $extension->empresa->razon_social ?? 'N/A',
            $extension->nombre,
            $extension->zona,
            $extension->distrito,
            $extension->status
        ];
    }

    private function getBaseQuery()
    {
        return Extension::forUser()->with('empresa')
            ->when($this->search, function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%')
                      ->orWhere('zona', 'like', '%' . $this->search . '%')
                      ->orWhere('distrito', 'like', '%' . $this->search . '%');
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->empresa_id, function ($query) {
                $query->where('empresa_id', $this->empresa_id);
            });
    }

    public function delete(Extension $extension)
    {
        // if (!Auth::user()->can('delete extensiones')) {
        //     abort(403, 'No tienes permiso para eliminar extensiones.');
        // }

        $extension->delete();
        session()->flash('message', 'Extensión eliminada correctamente.');
    }

    public function render()
    {
        $extensiones = $this->getBaseQuery()
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        // Calcular estadísticas
        $totalExtensiones = Extension::forUser()->count();
        $extensionesActivas = Extension::forUser()->where('status', 'Activo')->count();
        $extensionesInactivas = Extension::forUser()->where('status', 'Inactivo')->count();

        return view('livewire.admin.extensiones.index', [
            'extensiones' => $extensiones,
            'empresas' => Empresa::where('status', true)->get(),
            'totalExtensiones' => $totalExtensiones,
            'extensionesActivas' => $extensionesActivas,
            'extensionesInactivas' => $extensionesInactivas
        ])->layout($this->getLayout());
    }
}
