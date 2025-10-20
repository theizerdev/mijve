<?php

namespace App\Livewire\Admin\Users\Profile;

use Livewire\Component;
use App\Models\User;

class Index extends Component
{
    public $user;

    public function mount()
    {
        $this->user = User::findOrFail(auth()->id());
    }

    public function render()
    {
        return view('livewire.admin.users.profile.index')
            ->layout('components.layouts.admin', [
                'title' => 'Perfil de Usuario'
            ]);
    }
}
