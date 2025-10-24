<?php

namespace App\Livewire\Admin\Permissions;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class Edit extends Component
{
    public Permission $permission;
    public $name;
    public $module;
    public $guard_name;

    public function mount(Permission $permission)
    {
        // Verificar permiso para editar permisos (temporalmente deshabilitado)
        // if (!Auth::user()->can('edit permissions')) {
        //     abort(403, 'No tienes permiso para acceder a esta sección.');
        // }

        $this->permission = $permission;
        $this->name = $permission->name;
        $this->module = $permission->module;
        $this->guard_name = $permission->guard_name;
    }

    protected function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'name')->ignore($this->permission->id)
            ],
            'module' => 'required|string|max:255',
            'guard_name' => 'required|string|max:255'
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => 'El nombre del permiso es obligatorio.',
            'name.unique' => 'Este permiso ya existe en el sistema.',
            'module.required' => 'El módulo es obligatorio.',
            'guard_name.required' => 'El guard name es obligatorio.'
        ];
    }

    public function save()
    {
        $this->validate();

        try {
            $this->permission->update([
                'name' => $this->name,
                'module' => $this->module,
                'guard_name' => $this->guard_name
            ]);

            session()->flash('message', 'Permiso actualizado exitosamente.');

            return redirect()->route('admin.permissions.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el permiso: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.permissions.edit')
            ->layout('components.layouts.admin', [
                'title' => 'Editar Permiso'
            ]);
    }
}
