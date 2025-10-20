<?php

namespace App\Livewire\Admin\Users\Profile;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class AvatarUpload extends Component
{
    use WithFileUploads;

    public $user;
    public $avatar;
    public $tempAvatar;

    public function mount(User $user)
    {
        $this->user = $user;
    }

    public function updatedAvatar()
    {
        $this->validate([
            'avatar' => 'image|max:2048', // 2MB Max
        ]);

        $this->tempAvatar = $this->avatar->temporaryUrl();
    }

    public function save()
    {
        $this->validate([
            'avatar' => 'required|image|max:2048',
        ]);

        // Eliminar avatar anterior si existe
        if ($this->user->avatar) {
            Storage::delete($this->user->avatar);
        }

        // Guardar nuevo avatar
        $path = $this->avatar->store('avatars', 'public');

        // Actualizar usuario
        $this->user->update(['avatar' => $path]);

        // Resetear propiedades
        $this->reset(['avatar', 'tempAvatar']);

        // Emitir evento para actualizar la vista
        $this->dispatch('avatarUpdated');
    }

    public function render()
    {
        return view('livewire.admin.users.profile.avatar-upload');
    }
}
