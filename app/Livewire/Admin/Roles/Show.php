<?php

namespace App\Livewire\Admin\Roles;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;

class Show extends Component
{
    public $role;
    public $groupedPermissions = [];
    
    // Propiedades para estadísticas
    public $totalUsers = 0;
    public $totalPermissions = 0;

    public function mount(Role $role)
    {
        // Verificar permiso para ver roles
        if (!Auth::user()->can('view roles')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $this->role = $role;
        
        // Cargar estadísticas
        $this->totalUsers = $role->users()->count();
        $this->totalPermissions = $role->permissions()->count();
        
        // Agrupar permisos del rol
        $this->groupPermissions();
    }

    public function groupPermissions()
    {
        $rolePermissions = $this->role->permissions;
        
        // Agrupar permisos por módulo (basado en el nombre del permiso)
        foreach ($rolePermissions as $permission) {
            // Extraer el módulo del permiso (ej: "view users" -> "users")
            $parts = explode(' ', $permission->name);
            $module = end($parts);
            
            // Convertir plural a singular para agrupación
            $module = rtrim($module, 's');
            
            if (!isset($this->groupedPermissions[$module])) {
                $this->groupedPermissions[$module] = [];
            }
            
            $this->groupedPermissions[$module][] = $permission;
        }
    }

    public function render()
    {
        return view('livewire.admin.roles.show')
            ->layout('components.layouts.admin', [
                'title' => 'Detalles del Rol'
            ]);
    }
}