<?php

namespace App\Livewire\Admin\Roles;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10]
    ];

    public function mount()
    {
        // Verificar permiso para ver roles
        if (!Auth::user()->can('view roles')) {
            // Si no tiene permiso para ver roles, verificamos si tiene permiso para ver permisos
            if (!Auth::user()->can('view permissions')) {
                abort(403, 'No tienes permiso para acceder a esta sección.');
            }
        }
    }

    public function updatingSearch()
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

    public function clearFilters()
    {
        $this->search = '';
        $this->sortBy = 'name';
        $this->sortDirection = 'asc';
        $this->perPage = 10;
        $this->resetPage();
    }

    public function delete(Role $role)
    {
        // Verificar permiso para eliminar roles
        if (!Auth::user()->can('delete roles')) {
            session()->flash('error', 'No tienes permiso para eliminar roles.');
            return;
        }

        // No permitir eliminar roles predeterminados
        if (in_array($role->name, ['super-admin', 'admin', 'empresa-admin', 'user'])) {
            session()->flash('error', 'No puedes eliminar roles del sistema.');
            return;
        }

        $role->delete();
        session()->flash('message', 'Rol eliminado correctamente.');
    }

    public function render()
    {
        $query = Role::query();

        // Aplicar búsqueda
        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        // Aplicar ordenamiento
        $query->orderBy($this->sortBy, $this->sortDirection);

        // Obtener resultados con paginación
        $roles = $query->paginate($this->perPage);

        // Estadísticas
        $totalRoles = Role::count();
        $totalPermissions = Permission::count();

        return view('livewire.admin.roles.index', [
            'roles' => $roles,
            'totalRoles' => $totalRoles,
            'totalPermissions' => $totalPermissions,
        ])
            ->layout('components.layouts.admin', [
                'title' => 'Lista de Roles'
            ]);
    }
}