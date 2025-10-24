<?php

namespace App\Livewire\Admin\Permissions;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class Create extends Component
{
    public $name = '';
    public $module = '';
    public $guard_name = 'web';
    public $existingModules = [];

    protected $rules = [
        'name' => 'required|string|max:255|unique:permissions,name',
        'module' => 'required|string|max:255',
        'guard_name' => 'required|string|max:255'
    ];

    public function mount()
    {
        $this->loadExistingModules();
    }

    public function loadExistingModules()
    {
        $this->existingModules = Permission::select('module')
            ->distinct()
            ->whereNotNull('module')
            ->orderBy('module')
            ->pluck('module')
            ->toArray();
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            Permission::create([
                'name' => $this->name,
                'module' => $this->module,
                'guard_name' => $this->guard_name
            ]);
        });

        session()->flash('message', 'Permiso creado exitosamente.');
        return redirect()->route('admin.permissions.index');
    }

    public function render()
    {
        return view('livewire.admin.permissions.create', [
            'existingModules' => $this->existingModules
        ])
        ->layout('components.layouts.admin', ['title' => 'Crear Permiso']);
    }
}
