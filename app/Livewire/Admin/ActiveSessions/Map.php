<?php

namespace App\Livewire\Admin\ActiveSessions;

use Livewire\Component;
use App\Models\ActiveSession;
use Illuminate\Support\Facades\DB;

class Map extends Component
{
    public $sessions = [];

    public function mount()
    {
        $this->loadSessions();
    }

    public function loadSessions()
    {
        $this->sessions = ActiveSession::with('user')
            ->select([
                'id',
                'user_id',
                'ip_address',
                'user_agent',
                'last_activity',
                'is_active',
                DB::raw('(SELECT country FROM geo_ip WHERE geo_ip.ip_address = active_sessions.ip_address LIMIT 1) as country'),
                DB::raw('(SELECT city FROM geo_ip WHERE geo_ip.ip_address = active_sessions.ip_address LIMIT 1) as city'),
                DB::raw('(SELECT latitude FROM geo_ip WHERE geo_ip.ip_address = active_sessions.ip_address LIMIT 1) as latitude'),
                DB::raw('(SELECT longitude FROM geo_ip WHERE geo_ip.ip_address = active_sessions.ip_address LIMIT 1) as longitude')
            ])
            ->orderBy('last_activity', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->id,
                    'user' => $session->user?->name ?? 'Desconocido',
                    'ip' => $session->ip_address,
                    'device' => $session->user_agent,
                    'last_activity' => $session->last_activity->diffForHumans(),
                    'status' => $session->is_active ? 'Activa' : 'Inactiva',
                    'country' => $session->country,
                    'city' => $session->city,
                    'latitude' => $session->latitude,
                    'longitude' => $session->longitude,
                ];
            })
            ->toArray();
    }

    public function render()
    {
        return view('livewire.admin.active-sessions.map');
    }
}
