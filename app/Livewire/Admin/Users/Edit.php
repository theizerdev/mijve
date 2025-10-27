<?php

namespace App\Livewire\Admin\Users;

use Livewire\Component;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Sucursal;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

class Edit extends Component
{
    public User $user;
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $empresa_id;
    public $sucursal_id;
    public $status;
    public $role;
    public $sucursales = [];

    public function mount(User $user)
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->empresa_id = $user->empresa_id;
        $this->sucursal_id = $user->sucursal_id;
        $this->status = $user->status;
        $this->role = $user->getRoleNames()->first();
        $this->sucursales = Sucursal::forUser()
            ->where('empresa_id', $user->empresa_id)
            ->where('status', true)
            ->get();
    }

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $this->user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'empresa_id' => ['required', 'exists:empresas,id'],
            'sucursal_id' => ['required', 'exists:sucursales,id'],
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
            $this->sucursales = Sucursal::forUser()
                ->where('empresa_id', $this->empresa_id)
                ->where('status', true)
                ->get();
        } else {
            $this->sucursales = [];
        }
        $this->sucursal_id = null;
    }

    public function update()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'empresa_id' => $this->empresa_id,
            'sucursal_id' => $this->sucursal_id,
            'status' => $this->status
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        $user = User::find($this->user->id);
        $user->name = $this->name;
        $user->email = $this->email;
        $user->password = $data['password'] ?? $user->password;
        $user->empresa_id = $this->empresa_id;
        $user->sucursal_id = $this->sucursal_id;
        $user->status = $this->status;
        $user->save();
        
        // Sincronizar rol del usuario
        $user->syncRoles([$this->role]);

        session()->flash('message', 'Usuario actualizado correctamente.');

        return redirect()->route('admin.users.index');
    }

    public function render()
    {
        $empresas = Empresa::forUser()->where('status', true)->get();
        $roles = Role::all();

        return view('livewire.admin.users.edit', [
            'empresas' => $empresas,
            'roles' => $roles
        ])->layout('components.layouts.admin', [
            'title' => 'Editar Usuario'
        ]);
    }
}