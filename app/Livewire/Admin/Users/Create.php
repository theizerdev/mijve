<?php

namespace App\Livewire\Admin\Users;

use Livewire\Component;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Sucursal;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class Create extends Component
{
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $empresa_id;
    public $sucursal_id;
    public $status = true;
    public $role;
    public $sucursales = [];

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'empresa_id' => ['required', 'exists:empresas,id'],
            'sucursal_id' => ['required', 'exists:sucursals,id'],
            'status' => ['boolean'],
            'role' => ['required', 'exists:roles,name']
        ];
    }

    public function updatedEmpresaId($value)
    {
        $this->loadSucursales();
    }

    public function loadSucursales()
    {
        if ($this->empresa_id) {
            $this->sucursales = Sucursal::where('empresa_id', $this->empresa_id)
                ->where('status', true)
                ->get();
        } else {
            $this->sucursales = [];
        }
        $this->sucursal_id = null;
    }

    public function save()
    {
        $this->validate();

        $user = new User();
        $user->name = $this->name;
        $user->email = $this->email;
        $user->password = Hash::make($this->password);
        $user->empresa_id = $this->empresa_id;
        $user->sucursal_id = $this->sucursal_id;
        $user->status = $this->status;
        $user->save();
        
        // Asignar rol al usuario
        $user->assignRole($this->role);

        session()->flash('message', 'Usuario creado correctamente.');

        return redirect()->route('admin.users.index');
    }

    public function render()
    {
        $empresas = Empresa::where('status', true)->get();
        $roles = Role::all();

        return view('livewire.admin.users.create', [
            'empresas' => $empresas,
            'roles' => $roles
        ])->layout('components.layouts.admin', [
            'title' => 'Crear Usuario'
        ]);
    }
}