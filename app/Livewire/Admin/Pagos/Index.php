<?php

namespace App\Livewire\Admin\Pagos;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Pago;
use App\Models\Participante;
use App\Models\Empresa;
use App\Models\Actividad;
use App\Models\Caja;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Auth;
use App\Traits\Exportable;

class Index extends Component
{
    use WithPagination, Exportable, HasDynamicLayout;

    public $search = '';
    public $status = '';
    public $actividad_id = '';
    public $empresa_id = '';
    public $fecha_inicio = '';
    public $fecha_fin = '';
    
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'actividad_id' => ['except' => ''],
        'empresa_id' => ['except' => ''],
        'fecha_inicio' => ['except' => ''],
        'fecha_fin' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10]
    ];

    public function mount()
    {
        if (!Auth::user()->can('access pagos')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
    }

    public function updatedSearch() { $this->resetPage(); }
    public function updatedStatus() { $this->resetPage(); }
    public function updatedActividadId() { $this->resetPage(); }
    public function updatedEmpresaId() { $this->resetPage(); }
    public function updatedFechaInicio() { $this->resetPage(); }
    public function updatedFechaFin() { $this->resetPage(); }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortBy = $field;
    }

    protected function getExportQuery()
    {
        return $this->getBaseQuery();
    }

    protected function getExportHeaders(): array
    {
        return ['ID', 'Fecha', 'Participante', 'Actividad', 'Método Pago', 'Monto EUR', 'Tasa', 'Monto Bs', 'Ref', 'Estado'];
    }

    protected function formatExportRow($pago): array
    {
        return [
            $pago->id,
            $pago->fecha_pago->format('d/m/Y'),
            $pago->participante->nombres . ' ' . $pago->participante->apellidos,
            $pago->actividad->nombre,
            $pago->metodoPago->tipo_pago . ($pago->metodoPago->banco ? ' - ' . $pago->metodoPago->banco : ''),
            $pago->monto_euro,
            $pago->tasa_cambio,
            $pago->monto_bolivares,
            $pago->referencia_bancaria ?? 'N/A',
            $pago->status
        ];
    }

    private function getBaseQuery()
    {
        $query = Pago::forUser()
            ->with(['participante', 'actividad', 'metodoPago', 'empresa']);

        // Líder de Jóvenes: solo ve pagos de participantes de su extensión
        if (Auth::user()->hasRole('Líder de Jóvenes')) {
            $extensionIds = \App\Models\Extension::where('user_id', Auth::id())->pluck('id');
            $participanteIds = Participante::whereIn('extension_id', $extensionIds)->pluck('id');
            $query->whereIn('participante_id', $participanteIds);
        }

        return $query
            ->when($this->search, function ($query) {
                $query->where(function($q) {
                    $q->where('referencia_bancaria', 'like', '%' . $this->search . '%')
                      ->orWhereHas('participante', function($qp) {
                          $qp->where('nombres', 'like', '%' . $this->search . '%')
                             ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                             ->orWhere('cedula', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->actividad_id, function ($query) {
                $query->where('actividad_id', $this->actividad_id);
            })
            ->when($this->empresa_id, function ($query) {
                $query->where('empresa_id', $this->empresa_id);
            })
            ->when($this->fecha_inicio, function ($query) {
                $query->whereDate('fecha_pago', '>=', $this->fecha_inicio);
            })
            ->when($this->fecha_fin, function ($query) {
                $query->whereDate('fecha_pago', '<=', $this->fecha_fin);
            });
    }

    public function confirmPayment(Pago $pago)
    {

       

        // Buscar caja abierta para el usuario actual
        $caja = Caja::where('estado', 'abierta')
                    ->latest()
                    ->first();

        if (!$caja) {
            session()->flash('error', 'No puedes aprobar pagos porque no tienes una caja abierta.');
            return;
        }

        $pago->status = 'Aprobado';
        $pago->caja_id = $caja->id; // Relacionar con la caja abierta
        $pago->save();

        // Enviar notificación de aprobación por WhatsApp
        $this->sendApprovalNotification($pago);

        session()->flash('message', 'Pago aprobado y registrado en caja correctamente.');
    }

    private function sendApprovalNotification(Pago $pago)
    {
        try {
            $participante = $pago->participante;
            if (!$participante || empty($participante->telefono_principal)) {
                return;
            }

            $telefono = $this->formatPhoneNumber($participante->telefono_principal);
            $nombreParticipante = $participante->nombres . ' ' . $participante->apellidos;
            $actividad = $pago->actividad;
            $montoEur = number_format($pago->monto_euro, 2);
            $fecha = now()->format('d/m/Y');

            $mensaje = "✅ *Pago Aprobado Satisfactoriamente*\n\n";
            $mensaje .= "Hola *$nombreParticipante*, tu pago ha sido verificado con éxito.\n\n";
            $mensaje .= "📚 *Actividad:* {$actividad->nombre}\n";
            $mensaje .= "💰 *Monto:* €$montoEur\n";
            $mensaje .= "📅 *Fecha de Verificación:* $fecha\n\n";
            
            // Agregar información de ubicación si existe
            if ($actividad->direccion || ($actividad->latitud && $actividad->longitud)) {
                $mensaje .= "📍 *Ubicación de la Actividad:*\n";
                
                if ($actividad->direccion) {
                    $mensaje .= "$actividad->direccion\n\n";
                }
                
                if ($actividad->latitud && $actividad->longitud) {
                    $googleMapsUrl = "https://www.google.com/maps?q={$actividad->latitud},{$actividad->longitud}";
                    $mensaje .= "Ver en Google Maps: $googleMapsUrl\n\n";
                }
            }
            
            $mensaje .= "Gracias por tu confianza. ¡Te esperamos en nuestro campamento: *Valiente*!";

            $whatsapp = new WhatsAppService(auth()->user()->empresa_id);
            $whatsapp->sendMessage($telefono, $mensaje);
            \Log::info("Notificación de aprobación de pago enviada a {$telefono}");

        } catch (\Exception $e) {
            \Log::error('Error enviando notificación de aprobación de pago: ' . $e->getMessage());
        }
    }

    private function formatPhoneNumber($phone)
    {
        $phone = preg_replace('/\D/', '', $phone);
        $empresa = auth()->user()->empresa;
        $countryCode = isset($empresa->codigo_telefono) ? str_replace('+', '', $empresa->codigo_telefono) : '58';
        
        if (str_starts_with($phone, $countryCode)) {
            $numberPart = substr($phone, strlen($countryCode));
            if (str_starts_with($numberPart, '0')) {
                $phone = $countryCode . substr($numberPart, 1);
            }
            return $phone;
        }

        if (str_starts_with($phone, '0')) {
            $phone = substr($phone, 1);
        }
        
        return $countryCode . $phone;
    }

    public function render()
    {
        $pagos = $this->getBaseQuery()
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        // Estadísticas
        $totalPagos = Pago::forUser()->count();
        $pagosAprobados = Pago::forUser()->where('status', 'Aprobado')->count();
        $pagosPendientes = Pago::forUser()->where('status', 'Pendiente')->count();
        $totalBs = Pago::forUser()->where('status', 'Aprobado')->sum('monto_bolivares');
        $totalEur = Pago::forUser()->where('status', 'Aprobado')->sum('monto_euro');

        return view('livewire.admin.pagos.index', [
            'pagos' => $pagos,
            'actividades' => Actividad::where('status', 'Activo')->get(),
            'empresas' => Empresa::where('status', true)->get(),
            'totalPagos' => $totalPagos,
            'pagosAprobados' => $pagosAprobados,
            'pagosPendientes' => $pagosPendientes,
            'totalBs' => $totalBs,
            'totalEur' => $totalEur
        ])->layout($this->getLayout());
    }
}
