<?php

namespace App\Livewire\Admin\Whatsapp;

use Livewire\Component;
use App\Traits\HasDynamicLayout;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class Index extends Component
{
    use HasDynamicLayout;

    public $status = 'disconnected';
    public $user = null;
    public $lastSeen = null;
    public $jwtToken = null;
    public $messages = [];
    public $isLoading = false;
    public $connectionError = null;
    public $activeTab = 'dashboard';

    public $stats = [
        'sent' => 0,
        'delivered' => 0,
        'read' => 0,
        'failed' => 0,
        'pending' => 0,
        'total' => 0
    ];

    protected $listeners = [
        'refreshWhatsapp' => 'loadDashboard',
        'connectionUpdated' => 'handleConnectionUpdate'
    ];

    public function mount()
    {
        if (!Auth::user()->can('access whatsapp')) {
            abort(403, 'No tienes permiso para acceder a WhatsApp.');
        }

        $this->generateToken();
        $this->loadDashboard();
    }

    public function generateToken()
    {
        $this->jwtToken = auth()->user()->empresa->api_key ?? null;
    }

    public function loadDashboard()
    {
        $this->isLoading = true;
        $this->connectionError = null;

        $this->checkStatus();
        $this->loadMessages();

        $this->isLoading = false;
    }

    public function checkStatus()
    {
        if (!$this->jwtToken) {
            $this->connectionError = 'No se ha configurado la API Key de WhatsApp.';
            return;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders(['X-API-Key' => $this->jwtToken])
                ->get(config('whatsapp.api_url') . '/api/whatsapp/status');

            if ($response->successful()) {
                $data = $response->json();
                $this->status = $data['connectionState'] ?? 'disconnected';
                $this->user = $data['user'] ?? null;
                $this->lastSeen = $data['lastSeen'] ?? null;
                $this->connectionError = null;
            } else {
                $this->handleApiError($response);
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $this->status = 'service_unavailable';
            $this->connectionError = 'No se puede conectar al servidor de WhatsApp. Verifique que el servicio esté activo.';
        } catch (\Exception $e) {
            $this->status = 'error';
            $this->connectionError = 'Error al verificar estado: ' . $e->getMessage();
        }
    }

    public function loadMessages()
    {
        if (!$this->jwtToken) return;

        try {
            $response = Http::timeout(10)
                ->withHeaders(['X-API-Key' => $this->jwtToken])
                ->get(config('whatsapp.api_url') . '/api/whatsapp/messages?limit=50');

            if ($response->successful()) {
                $data = $response->json();
                $allMessages = collect($data['messages'] ?? []);
                $this->messages = $allMessages->take(10)->toArray();

                $this->stats = [
                    'sent' => $allMessages->where('status', 'sent')->count(),
                    'delivered' => $allMessages->where('status', 'delivered')->count(),
                    'read' => $allMessages->where('status', 'read')->count(),
                    'failed' => $allMessages->where('status', 'failed')->count(),
                    'pending' => $allMessages->where('status', 'pending')->count(),
                    'total' => $data['total'] ?? $allMessages->count()
                ];
            }
        } catch (\Exception $e) {
            // Silencioso para mensajes
        }
    }

    public function refresh()
    {
        $this->loadDashboard();
        $this->dispatch('notify', type: 'success', message: 'Dashboard actualizado correctamente.');
    }

    public function handleConnectionUpdate($newStatus)
    {
        $this->status = $newStatus;
        if ($newStatus === 'connected') {
            $this->loadDashboard();
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    protected function handleApiError($response)
    {
        $statusCode = $response->status();
        $error = $response->json()['error'] ?? 'Error desconocido';

        switch ($statusCode) {
            case 401:
                $this->connectionError = 'API Key inválida o expirada.';
                break;
            case 403:
                $this->connectionError = 'No tiene permisos para acceder a este recurso.';
                break;
            case 500:
                $this->connectionError = 'Error interno del servidor de WhatsApp.';
                break;
            default:
                $this->connectionError = $error;
        }

        $this->status = 'error';
    }

    public function getStatusColorProperty()
    {
        return match ($this->status) {
            'connected' => 'success',
            'connecting', 'qr_ready' => 'warning',
            'service_unavailable' => 'secondary',
            'error' => 'danger',
            default => 'danger'
        };
    }

    public function getStatusIconProperty()
    {
        return match ($this->status) {
            'connected' => 'ri ri-checkbox-circle-fill',
            'connecting' => 'ri ri-loader-4-line',
            'qr_ready' => 'ri ri-qr-code-line',
            'service_unavailable' => 'ri ri-wifi-off-line',
            'error' => 'ri ri-error-warning-fill',
            default => 'ri ri-close-circle-fill'
        };
    }

    public function getStatusTextProperty()
    {
        return match ($this->status) {
            'connected' => 'Conectado',
            'connecting' => 'Conectando...',
            'qr_ready' => 'Esperando QR',
            'service_unavailable' => 'Servicio No Disponible',
            'error' => 'Error',
            default => 'Desconectado'
        };
    }

    protected function getPageTitle(): string
    {
        return 'WhatsApp - Panel de Control';
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.whatsapp.index' => 'WhatsApp'
        ];
    }

    public function render()
    {
        return $this->renderWithLayout('livewire.admin.whatsapp.index', [
            'status' => $this->status,
            'statusColor' => $this->statusColor,
            'statusIcon' => $this->statusIcon,
            'statusText' => $this->statusText,
            'user' => $this->user,
            'lastSeen' => $this->lastSeen,
            'messages' => $this->messages,
            'stats' => $this->stats,
            'isLoading' => $this->isLoading,
            'connectionError' => $this->connectionError,
            'activeTab' => $this->activeTab
        ], [
            'title' => 'WhatsApp - Panel de Control',
            'description' => 'Panel de control para gestión de WhatsApp',
            'breadcrumb' => $this->getBreadcrumb()
        ]);
    }
}
