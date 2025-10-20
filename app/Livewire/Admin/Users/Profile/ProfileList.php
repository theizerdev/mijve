<?php

namespace App\Livewire\Admin\Users\Profile;

use Livewire\Component;
use App\Models\User;
use App\Models\ActiveSession;
use Illuminate\Support\Facades\Auth;

class ProfileList extends Component
{
    public $user;
    public $sessions;
    public $stats;

    public function mount()
    {
        $this->user = Auth::user();
        $this->loadData();
    }

    public function loadData()
    {
        // Cargar sesiones activas
        $this->sessions = ActiveSession::where('user_id', $this->user->id)
            ->orderBy('login_at', 'desc')
            ->limit(10)
            ->get();

        // Calcular estadísticas
        $this->stats = [
            'total_sessions' => ActiveSession::where('user_id', $this->user->id)->count(),
            'active_sessions' => ActiveSession::where('user_id', $this->user->id)
                ->where('is_active', true)
                ->count(),
            'last_login' => $this->sessions->first()->login_at ?? null
        ];
    }

    public function render()
    {
        return view('livewire.admin.users.profile.index')
            ->layout('components.layouts.admin', [
                'title' => 'Perfil de Usuario'
            ]);
    }
}