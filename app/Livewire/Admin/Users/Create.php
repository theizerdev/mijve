<?php

namespace App\Livewire\Admin\Users;

use Livewire\Component;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Sucursal;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserWelcomeMail;
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

    public function save()
    {
        $this->validate();

        $plainPassword = $this->password;

        $user = new User();
        $user->name = $this->name;
        $user->email = $this->email;
        $user->password = Hash::make($plainPassword);
        $user->empresa_id = $this->empresa_id;
        $user->sucursal_id = $this->sucursal_id;
        $user->status = $this->status;
        $user->save();
        
        $user->assignRole($this->role);

        // Enviar correo de bienvenida
        try {
            Mail::to($user->email)->send(new UserWelcomeMail($user, $plainPassword));
        } catch (\Exception $e) {
            \Log::error('Error enviando correo de bienvenida: ' . $e->getMessage());
        }

        session()->flash('message', 'Usuario creado correctamente. Se ha enviado un correo con las credenciales.');

        return redirect()->route('admin.users.index');
    }

    public function render()
    {
        $empresas = Empresa::forUser()->where('status', true)->get();
        $roles = Role::all();

        return view('livewire.admin.users.create', [
            'empresas' => $empresas,
            'roles' => $roles
        ])->layout('components.layouts.admin', [
            'title' => 'Crear Usuario'
        ]);
    }
}