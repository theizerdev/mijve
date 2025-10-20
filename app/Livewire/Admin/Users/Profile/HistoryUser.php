<?php

namespace App\Livewire\Admin\Users\Profile;

use Livewire\Component;
use App\Models\ActiveSession;
use Livewire\WithPagination;
use App\Models\User;

class HistoryUser extends Component
{
    use WithPagination;

    public $user_id;
    public $user;

    public function mount()
    {
        $this->user_id = auth()->user()->id;
        $this->user = User::findOrFail(auth()->user()->id);
    }

    public function render()
    {
        $sessions = ActiveSession::where('user_id', $this->user_id)
            ->where('login_at', '>=', now()->subDays(30))
            ->orderBy('login_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.users.profile.history-user', [
            'sessions' => $sessions
        ])->layout('components.layouts.admin', [
            'title' => 'Historial de Sesiones'
        ]);
    }
}
