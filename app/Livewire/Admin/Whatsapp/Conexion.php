<?php

namespace App\Livewire\Admin\Whatsapp;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use App\Services\WhatsAppService;

class Conexion extends Component
{
    public $status = 'disconnected';
    public $qrCode = null;
    public $isConnecting = false;
    public $isDisconnecting = false;
    public $error = null;
    public $success = null;
    public $whatsappApiKey = null;
    public $companyId = null;
    public $user = null;
    public $lastSeen = null;
    public $pollingActive = false;
    public $empresaNombre = null;
    public $whatsappPhone = null;

    protected $listeners = ['checkConnectionStatus' => 'checkStatus'];

    public function mount()
    {
        $this->initializeWhatsApp();
        $this->checkStatus();
    }

    /**
     * Inicializa la configuración de WhatsApp para la empresa del usuario
     */
    public function initializeWhatsApp()
    {
        $empresa = auth()->user()->empresa ?? null;
        
        if ($empresa) {
            $this->companyId = $empresa->id;
            $this->whatsappApiKey = $empresa->whatsapp_api_key;
            $this->empresaNombre = $empresa->razon_social;
            $this->whatsappPhone = $empresa->whatsapp_phone;
            
            // Si no tiene API key, mostrar mensaje
            if (empty($this->whatsappApiKey)) {
                $this->error = 'Esta empresa no tiene configurada la API Key de WhatsApp. Contacte al administrador.';
            }
        } else {
            $this->error = 'Usuario sin empresa asignada.';
        }
    }

    /**
     * Obtiene los headers necesarios para la API de WhatsApp
     */
    private function getApiHeaders(): array
    {
        return [
            'X-API-Key' => $this->whatsappApiKey,
            'X-Company-Id' => (string) $this->companyId,
            'Content-Type' => 'application/json'
        ];
    }

    public function connect()
    {
        if (!$this->whatsappApiKey) {
            $this->error = 'No se ha configurado la API Key de WhatsApp para esta empresa.';
            return;
        }

        $this->isConnecting = true;
        $this->error = null;
        $this->success = null;
        $this->qrCode = null;

        try {
            $response = Http::timeout(15)
                ->withHeaders($this->getApiHeaders())
                ->post(config('whatsapp.api_url') . '/api/whatsapp/connect');

            if ($response->successful()) {
                $this->status = 'connecting';
                $this->pollingActive = true;
                $this->success = 'Iniciando conexión para ' . $this->empresaNombre . '. Espere el código QR...';
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
        if (!$this->whatsappApiKey) {
            $this->status = 'error';
            $this->error = 'No se ha configurado la API Key de WhatsApp para esta empresa.';
            return;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders($this->getApiHeaders())
                ->get(config('whatsapp.api_url') . '/api/whatsapp/status');

            if ($response->successful()) {
                $data = $response->json();
                $this->status = $data['connectionState'] ?? 'disconnected';
                $this->user = $data['user'] ?? null;
                $this->lastSeen = $data['lastSeen'] ?? null;
                $this->whatsappPhone = $data['user']['id'] ?? $this->whatsappPhone;

                if ($this->status === 'connected') {
                    $this->qrCode = null;
                    $this->pollingActive = false;
                    $this->error = null;
                    $this->dispatch('connectionUpdated', newStatus: 'connected');
                    
                    // Actualizar estado en la empresa
                    $this->updateEmpresaWhatsAppStatus('connected');
                } elseif ($this->status === 'qr_ready') {
                    $this->checkQR();
                }
            } else {
                // Si la respuesta no es exitosa, mostrar el error detallado
                $errorData = $response->json();
                $this->error = 'Error del servidor: ' . ($errorData['error'] ?? 'Error desconocido') . ' (Código: ' . $response->status() . ')';
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $this->status = 'service_unavailable';
            $this->error = 'Servicio de WhatsApp no disponible. No se puede conectar al servidor.';
        } catch (\Exception $e) {
            $this->error = 'Error al verificar estado: ' . $e->getMessage();
        }
    }

    public function checkQR()
    {
        if (!$this->whatsappApiKey) return;

        try {
            $response = Http::timeout(10)
                ->withHeaders($this->getApiHeaders())
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
        if (!$this->whatsappApiKey) return;

        $this->isDisconnecting = true;
        $this->error = null;

        try {
            $response = Http::timeout(10)
                ->withHeaders($this->getApiHeaders())
                ->delete(config('whatsapp.api_url') . '/api/whatsapp/disconnect');

            if ($response->successful()) {
                $this->status = 'disconnected';
                $this->qrCode = null;
                $this->user = null;
                $this->pollingActive = false;
                $this->success = 'WhatsApp desconectado correctamente para ' . $this->empresaNombre;
                $this->dispatch('connectionUpdated', newStatus: 'disconnected');
                
                // Actualizar estado en la empresa
                $this->updateEmpresaWhatsAppStatus('disconnected');
            } else {
                $this->error = $response->json()['error'] ?? 'Error al desconectar.';
            }
        } catch (\Exception $e) {
            $this->error = 'Error: ' . $e->getMessage();
        }

        $this->isDisconnecting = false;
    }

    /**
     * Actualiza el estado de WhatsApp en la empresa
     */
    private function updateEmpresaWhatsAppStatus(string $status): void
    {
        $empresa = auth()->user()->empresa;
        if ($empresa) {
            $empresa->updateWhatsAppStatus($status, $this->whatsappPhone);
        }
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