<?php

namespace App\Livewire\Admin\Users\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ChangePassword extends Component
{
    public $current_password;
    public $password;
    public $password_confirmation;
    public $user;

    function mount()
    {
        $this->user = Auth::user();
    }

    protected function rules()
    {
        return [
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ],
        ];
    }

    public function updatePassword()
    {
        $this->validate();

        Auth::user()->update([
            'password' => Hash::make($this->password)
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);

        session()->flash('message', 'Contraseña actualizada correctamente');
    }

    public function render()
    {
        return view('livewire.admin.users.profile.password')
            ->layout('components.layouts.admin', [
                'title' => 'Cambio de contraseña'
            ]);
    }
}
