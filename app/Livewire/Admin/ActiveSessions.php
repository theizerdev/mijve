<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ActiveSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ActiveSessions extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $status = '';
    public $sortBy = 'last_activity';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'sortBy' => ['except' => 'last_activity'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10]
    ];

    public function mount()
    {
        // Verificar permiso para ver sesiones activas
        if (!Auth::user()->can('view active sessions')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
    }

    public function loadSessions()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortBy = $field;
    }

    public function render()
    {
        $sessions = ActiveSession::with('user')
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status, function ($query) {
                $query->where('is_current', $this->status === 'current' ? true : false);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.active-sessions', compact('sessions'))
            ->layout('components.layouts.admin', [
                'title' => 'Sesiones Activas'
            ]);
    }

    public function terminateSession($sessionId)
    {
        // Verificar permiso para terminar sesiones activas
        if (!Auth::user()->can('terminate active sessions')) {
            session()->flash('error', 'No tienes permiso para terminar sesiones activas.');
            return;
        }

        $session = ActiveSession::find($sessionId);

        if (!$session) {
            session()->flash('error', 'Sesión no encontrada.');
            return;
        }

        // No permitir terminar la sesión actual del usuario
        if ($session->is_current && $session->user_id === Auth::id()) {
            session()->flash('error', 'No puedes terminar tu sesión actual.');
            return;
        }

        // Terminar la sesión
        $session->update([
            'is_current' => false,
            'logout_at' => now(),
            'is_active' => false
        ]);

        session()->flash('status', 'Sesión terminada correctamente.');
    }
    
    public function clearFilters()
    {
        $this->search = '';
        $this->status = '';
        $this->sortBy = 'last_activity';
        $this->sortDirection = 'desc';
        $this->perPage = 10;
        $this->resetPage();
    }
}