<?php

namespace App\Livewire\Admin\Whatsapp;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class Conexion extends Component
{
    public $status = 'disconnected';
    public $qrCode = null;
    public $isConnecting = false;
    public $isDisconnecting = false;
    public $error = null;
    public $success = null;
    public $jwtToken = null;
    public $user = null;
    public $lastSeen = null;
    public $pollingActive = false;

    protected $listeners = ['checkConnectionStatus' => 'checkStatus'];

    public function mount()
    {
        $this->generateToken();
        $this->checkStatus();
    }

    public function generateToken()
    {
        $empresa = auth()->user()->empresa ?? null;
        $this->jwtToken = $empresa->api_key ?? null;
    }

    public function connect()
    {
        if (!$this->jwtToken) {
            $this->error = 'No se ha configurado la API Key de WhatsApp.';
            return;
        }

        $this->isConnecting = true;
        $this->error = null;
        $this->success = null;
        $this->qrCode = null;

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'X-API-Key' => $this->jwtToken,
                    'Content-Type' => 'application/json'
                ])
                ->post(config('whatsapp.api_url') . '/api/whatsapp/connect');

            if ($response->successful()) {
                $this->status = 'connecting';
                $this->pollingActive = true;
                $this->success = 'Iniciando conexión. Espere el código QR...';
                $this->checkQR();
            } else {
                $this->error = $response->json()['error'] ?? 'Error al iniciar conexión.';
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $this->error = 'No se puede conectar al servidor de WhatsApp. Verifique que el servicio esté activo.';
        } catch (\Exception $e) {
            $this->error = 'Error: ' . $e->getMessage();
        }

        $this->isConnecting = false;
    }

    public function checkStatus()
    {
        if (!$this->jwtToken) {
            $this->status = 'error';
            $this->error = 'API Key no configurada.';
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

                if ($this->status === 'connected') {
                    $this->qrCode = null;
                    $this->pollingActive = false;
                    $this->error = null;
                    $this->dispatch('connectionUpdated', newStatus: 'connected');
                } elseif ($this->status === 'qr_ready') {
                    $this->checkQR();
                }
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $this->status = 'service_unavailable';
            $this->error = 'Servicio de WhatsApp no disponible.';
        } catch (\Exception $e) {
            $this->error = 'Error al verificar estado.';
        }
    }

    public function checkQR()
    {
        if (!$this->jwtToken) return;

        try {
            $response = Http::timeout(10)
                ->withHeaders(['X-API-Key' => $this->jwtToken])
                ->get(config('whatsapp.api_url') . '/api/whatsapp/qr');

            if ($response->successful()) {
                $data = $response->json();
                if (($data['success'] ?? false) && isset($data['qr'])) {
                    $this->qrCode = $data['qr'];
                    $this->status = 'qr_ready';
                    $this->pollingActive = true;
                }
            }
        } catch (\Exception $e) {
            // Silencioso
        }
    }

    public function disconnect()
    {
        if (!$this->jwtToken) return;

        $this->isDisconnecting = true;
        $this->error = null;

        try {
            $response = Http::timeout(10)
                ->withHeaders(['X-API-Key' => $this->jwtToken])
                ->delete(config('whatsapp.api_url') . '/api/whatsapp/disconnect');

            if ($response->successful()) {
                $this->status = 'disconnected';
                $this->qrCode = null;
                $this->user = null;
                $this->pollingActive = false;
                $this->success = 'WhatsApp desconectado correctamente.';
                $this->dispatch('connectionUpdated', newStatus: 'disconnected');
            } else {
                $this->error = $response->json()['error'] ?? 'Error al desconectar.';
            }
        } catch (\Exception $e) {
            $this->error = 'Error: ' . $e->getMessage();
        }

        $this->isDisconnecting = false;
    }

    public function clearMessages()
    {
        $this->error = null;
        $this->success = null;
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
            default => 'ri ri-close-circle-fill'
        };
    }

    public function getStatusTextProperty()
    {
        return match ($this->status) {
            'connected' => 'Conectado',
            'connecting' => 'Conectando...',
            'qr_ready' => 'Escanear QR',
            'service_unavailable' => 'Servicio No Disponible',
            default => 'Desconectado'
        };
    }

    public function render()
    {
        return view('livewire.admin.whatsapp.conexion', [
            'statusColor' => $this->statusColor,
            'statusIcon' => $this->statusIcon,
            'statusText' => $this->statusText
        ]);
    }
}
