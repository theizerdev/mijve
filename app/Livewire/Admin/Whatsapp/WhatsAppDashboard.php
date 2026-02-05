<?php

namespace App\Livewire\Admin\Whatsapp;

use Livewire\Component;
use App\Traits\HasDynamicLayout;
use Illuminate\Support\Facades\Http;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Auth;
use App\Models\WhatsAppMessage;
use App\Models\User;
use Carbon\Carbon;

class WhatsAppDashboard extends Component
{
    use HasDynamicLayout;

    public $status = 'disconnected';
    public $user = null;
    public $lastSeen = null;
    public $whatsappApiKey = null;
    public $companyId = null;
    public $stats = [
        'sent' => 0,
        'delivered' => 0,
        'failed' => 0,
        'pending' => 0,
        'total' => 0
    ];
    public $recentMessages = [];
    public $dailyStats = [];
    public $weeklyStats = [];
    public $monthlyStats = [];
    public $topRecipients = [];
    public $recentActivity = [];
    public $isLoading = false;
    public $empresaNombre = null;
    public $whatsappPhone = null;
    public $error = null;

    public function mount()
    {
        if (!Auth::user()->can('access whatsapp')) {
            abort(403, 'No tienes permiso para acceder a WhatsApp.');
        }

        $this->initializeWhatsApp();
        $this->loadDashboardData();
    }

    /**
     * Inicializa la configuración de WhatsApp para la empresa del usuario
     */
    public function initializeWhatsApp()
    {
        $empresa = auth()->user()->empresa ?? null;
        dd($this->whatsappApiKey);   
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

    public function loadDashboardData()
    {
        $this->isLoading = true;
        $this->checkStatus();
        $this->loadRecentMessages();
        $this->isLoading = false;
    }

    public function checkStatus()
    {
        

        if (!$this->whatsappApiKey) {
            $this->status = 'error';
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
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $this->status = 'service_unavailable';
            $this->error = 'Servicio de WhatsApp no disponible.';
        } catch (\Exception $e) {
            $this->status = 'error';
            $this->error = 'Error al verificar estado.';
        }
    }

    public function loadRecentMessages()
    {
        // Cargar mensajes recientes de la base de datos
        $this->recentMessages = WhatsAppMessage::with(['creator', 'template'])
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'message_id' => $message->message_id,
                    'recipient_phone' => $message->recipient_phone,
                    'recipient_name' => $message->recipient_name,
                    'message_content' => $message->message_content,
                    'status' => $message->status,
                    'direction' => $message->direction,
                    'created_at' => $message->created_at,
                    'sent_at' => $message->sent_at,
                    'creator' => $message->creator ? $message->creator->name : 'Sistema',
                    'error_message' => $message->error_message,
                    'retry_count' => $message->retry_count ?? 0
                ];
            })->toArray();

        // Calcular estadísticas generales
        $this->stats = [
            'sent' => WhatsAppMessage::where('status', 'sent')->count(),
            'delivered' => WhatsAppMessage::where('status', 'delivered')->count(),
            'failed' => WhatsAppMessage::where('status', 'failed')->count(),
            'pending' => WhatsAppMessage::where('status', 'pending')->count(),
            'total' => WhatsAppMessage::count(),
            'received' => WhatsAppMessage::where('direction', 'inbound')->count()
        ];

        // Estadísticas diarias (últimos 7 días)
        $this->dailyStats = collect(range(6, 0))->map(function ($daysAgo) {
            $date = Carbon::now()->subDays($daysAgo);
            return [
                'date' => $date->format('d/m'),
                'sent' => WhatsAppMessage::whereDate('created_at', $date)->where('status', 'sent')->count(),
                'failed' => WhatsAppMessage::whereDate('created_at', $date)->where('status', 'failed')->count()
            ];
        })->toArray();

        // Estadísticas semanales (últimas 4 semanas)
        $this->weeklyStats = collect(range(3, 0))->map(function ($weeksAgo) {
            $startOfWeek = Carbon::now()->subWeeks($weeksAgo)->startOfWeek();
            $endOfWeek = Carbon::now()->subWeeks($weeksAgo)->endOfWeek();
            return [
                'week' => 'Sem ' . ($weeksAgo + 1),
                'sent' => WhatsAppMessage::whereBetween('created_at', [$startOfWeek, $endOfWeek])->where('status', 'sent')->count(),
                'failed' => WhatsAppMessage::whereBetween('created_at', [$startOfWeek, $endOfWeek])->where('status', 'failed')->count()
            ];
        })->toArray();

        // Estadísticas mensuales (últimos 6 meses)
        $this->monthlyStats = collect(range(5, 0))->map(function ($monthsAgo) {
            $date = Carbon::now()->subMonths($monthsAgo);
            return [
                'month' => $date->format('M'),
                'sent' => WhatsAppMessage::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->where('status', 'sent')->count(),
                'failed' => WhatsAppMessage::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->where('status', 'failed')->count()
            ];
        })->toArray();

        // Top destinatarios (más mensajes enviados)
        $this->topRecipients = WhatsAppMessage::selectRaw('recipient_phone, recipient_name, COUNT(*) as total_messages,
                MAX(created_at) as last_message')
            ->groupBy('recipient_phone', 'recipient_name')
            ->orderBy('total_messages', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($recipient) {
                return [
                    'phone' => $recipient->recipient_phone,
                    'name' => $recipient->recipient_name ?: 'Sin nombre',
                    'total_messages' => $recipient->total_messages,
                    'last_message' => Carbon::parse($recipient->last_message)->diffForHumans()
                ];
            })->toArray();

        // Actividad reciente (últimas 24 horas)
        $this->recentActivity = WhatsAppMessage::with('creator')
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($message) {
                return [
                    'time' => $message->created_at->diffForHumans(),
                    'action' => $this->getActionText($message),
                    'status' => $message->status,
                    'user' => $message->creator ? $message->creator->name : 'Sistema'
                ];
            })->toArray();
    }

    private function getActionText($message)
    {
        $phone = substr($message->recipient_phone, -4);
        $name = $message->recipient_name ? $message->recipient_name : "***{$phone}";

        switch ($message->status) {
            case 'sent':
                return "Mensaje enviado a {$name}";
            case 'delivered':
                return "Mensaje entregado a {$name}";
            case 'failed':
                return "Falló envío a {$name}";
            case 'pending':
                return "Mensaje pendiente para {$name}";
            default:
                return "Actividad con {$name}";
        }
    }

    public function refresh()
    {
        $this->loadDashboardData();
        session()->flash('message', 'Dashboard actualizado correctamente.');
    }

    public function getStatusColorProperty()
    {
        return match($this->status) {
            'connected' => 'success',
            'connecting' => 'warning',
            'qr_ready' => 'info',
            'disconnected' => 'danger',
            default => 'secondary'
        };
    }

    public function getStatusIconProperty()
    {
        return match($this->status) {
            'connected' => 'ri-checkbox-circle-line',
            'connecting' => 'ri-loader-4-line',
            'qr_ready' => 'ri-qr-code-line',
            'disconnected' => 'ri-close-circle-line',
            default => 'ri-help-circle-line'
        };
    }

    protected function getPageTitle(): string
    {
        return 'WhatsApp Dashboard';
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.whatsapp.dashboard' => 'WhatsApp',
            'admin.whatsapp.dashboard' => 'Dashboard'
        ];
    }

    public function render()
    {
        return $this->renderWithLayout('livewire.admin.whatsapp.whatsapp-dashboard', [
            'status' => $this->status,
            'user' => $this->user,
            'lastSeen' => $this->lastSeen,
            'stats' => $this->stats,
            'recentMessages' => $this->recentMessages,
            'dailyStats' => $this->dailyStats,
            'weeklyStats' => $this->weeklyStats,
            'monthlyStats' => $this->monthlyStats,
            'topRecipients' => $this->topRecipients,
            'recentActivity' => $this->recentActivity,
            'isLoading' => $this->isLoading
        ], [
            'title' => 'WhatsApp Dashboard',
            'description' => 'Panel de control de WhatsApp Business API',
            'breadcrumb' => $this->getBreadcrumb()
        ]);
    }
}