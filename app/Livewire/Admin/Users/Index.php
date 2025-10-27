<?php

namespace App\Livewire\Admin\Users;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Sucursal;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use App\Traits\Exportable;

class Index extends Component
{
    use WithPagination, Exportable;

    public $search = '';
    public $status = '';
    public $empresa_id = '';
    public $sucursal_id = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'empresa_id' => ['except' => ''],
        'sucursal_id' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10]
    ];

    public function mount()
    {
        // Verificar permiso para ver usuarios
        if (!Auth::user()->can('access users')) {
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

    public function updatingSucursalId()
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
        return ['ID', 'Nombre', 'Email', 'Empresa', 'Sucursal', 'Rol', 'Status'];
    }

    protected function formatExportRow($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->empresa->razon_social ?? 'N/A',
            $user->sucursal->nombre ?? 'N/A',
            $user->roles->pluck('name')->join(', '),
            $user->status ? 'Activo' : 'Inactivo'
        ];
    }

    private function getBaseQuery()
    {
        return User::forUser()->with(['empresa', 'sucursal', 'roles'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->when($this->status !== '', function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->empresa_id, function ($query) {
                $query->where('empresa_id', $this->empresa_id);
            })
            ->when($this->sucursal_id, function ($query) {
                $query->where('sucursal_id', $this->sucursal_id);
            });
    }

    public function render()
    {
        $users = $this->getBaseQuery()
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $empresas = Empresa::forUser()->where('status', 'active')->get();
        $sucursales = Sucursal::forUser()->where('status', 'active')
            ->when($this->empresa_id, function ($query) {
                $query->where('empresa_id', $this->empresa_id);
            })
            ->get();

        $roles = Role::all();

        // Calcular estadísticas
        $totalUsers = User::forUser()->count();
        $activeUsers = User::forUser()->where('status', 1)->count();
        $pendingUsers = 0;
        $inactiveUsers = User::forUser()->where('status', 0)->count();

        return view('livewire.admin.users.index', compact('users', 'empresas', 'sucursales', 'roles', 'totalUsers', 'activeUsers', 'pendingUsers', 'inactiveUsers'))
            ->layout('components.layouts.admin', [
                'title' => 'Lista de Usuarios'
            ]);
    }

    public function toggleStatus(User $user)
    {
        if (!Auth::user()->can('edit users')) {
            session()->flash('error', 'No tienes permiso para editar usuarios.');
            return;
        }

        if ($user->id === Auth::id()) {
            session()->flash('error', 'No puedes desactivar tu propia cuenta.');
            return;
        }

        $user->status = !$user->status;
        $user->save();

        session()->flash('message', 'Estado de usuario actualizado correctamente.');
    }

    public function delete(User $user)
    {
        // Verificar permiso para eliminar usuarios
        if (!Auth::user()->can('delete users')) {
            session()->flash('error', 'No tienes permiso para eliminar usuarios.');
            return;
        }

        // No permitir eliminar al usuario actual
        if ($user->id === Auth::id()) {
            session()->flash('error', 'No puedes eliminar tu propia cuenta.');
            return;
        }

        $user->delete();
        session()->flash('message', 'Usuario eliminado correctamente.');
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->status = '';
        $this->empresa_id = '';
        $this->sucursal_id = '';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->perPage = 10;
        $this->resetPage();
    }

    public function loadSucursales()
    {
        // Este método se llama cuando se cambia la empresa en los filtros
        $this->sucursal_id = '';
    }
}
