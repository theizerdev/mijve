<?php

namespace App\Livewire\Admin\Pagos;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Pago;
use App\Models\Participante;
use App\Models\Actividad;
use App\Models\MetodoPago;
use App\Models\ExchangeRate;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Auth;

class Create extends Component
{
    use HasDynamicLayout, WithFileUploads;

    // Pasos
    public $step = 1;

    // Datos del Participante
    public $search_participante = '';
    public $participante_id = null;
    public $participante_selected = null;

    // Datos de la Actividad
    public $actividad_id = null;
    public $actividad_selected = null;

    // Datos del Pago
    public $metodo_pago_id = null;
    public $metodo_pago_selected = null;
    public $monto_euro = 0;
    public $referencia_bancaria = ''; // Opcional para divisas
    public $fecha_pago = '';
    public $observaciones = '';
    public $evidencia_pago;

    public $participantes = [];

    protected function rules()
    {
        return [
            'participante_id' => 'required|exists:participantes,id',
            'actividad_id' => 'required|exists:actividads,id',
            'metodo_pago_id' => 'required|exists:metodo_pagos,id',
            'monto_euro' => 'required|numeric|min:0.01',
            'fecha_pago' => 'required|date',
            'referencia_bancaria' => 'nullable|string|max:255',
            'evidencia_pago' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', 
        ];
    }

    public function mount()
    {
        if (!Auth::user()->can('create pagos')) {
            abort(403, 'No tienes permiso para crear pagos.');
        }

        $this->fecha_pago = date('Y-m-d');
    }

    /*
    public function loadExchangeRate()
    {
        $rate = ExchangeRate::getLatestRate('EUR');
        $this->tasa_cambio = $rate ? floatval($rate) : 0;
    }
    */

    // Buscador de Participantes
    public function updatedSearchParticipante()
    {
        if (strlen($this->search_participante) > 2) {
            $this->participantes = Participante::forUser()
                ->where(function($q) {
                    $q->where('nombres', 'like', '%' . $this->search_participante . '%')
                      ->orWhere('apellidos', 'like', '%' . $this->search_participante . '%')
                      ->orWhere('cedula', 'like', '%' . $this->search_participante . '%');
                })
                ->limit(5)
                ->get();
        } else {
            $this->participantes = [];
        }
    }

    public function selectParticipante($id)
    {
        $this->participante_id = $id;
        $this->participante_selected = Participante::find($id);
        $this->participantes = [];
        $this->search_participante = '';
    }

    public function updatedActividadId($value)
    {
        if ($value) {
            $this->actividad_selected = Actividad::find($value);
            
            // Validar capacidad
            if (!$this->actividad_selected->tieneCuposDisponibles()) {
                session()->flash('error', "La actividad '{$this->actividad_selected->nombre}' no tiene cupos disponibles (Capacidad: {$this->actividad_selected->capacidad}).");
                $this->actividad_id = null;
                $this->actividad_selected = null;
                return;
            }

            $this->monto_euro = $this->actividad_selected->costo;
        } else {
            $this->actividad_selected = null;
            $this->monto_euro = 0;
        }
    }

    public function updatedMetodoPagoId($value)
    {
        if ($value) {
            $this->metodo_pago_selected = MetodoPago::find($value);
        } else {
            $this->metodo_pago_selected = null;
        }
    }

    public function save()
    {
        $this->validate();

        // Revalidar capacidad antes de guardar (por concurrencia)
        $actividad = Actividad::find($this->actividad_id);
        if (!$actividad->tieneCuposDisponibles()) {
            session()->flash('error', 'Error: Los cupos para esta actividad se han agotado.');
            return;
        }

        $path = null;
        if ($this->evidencia_pago) {
            $path = $this->evidencia_pago->store('pagos', 'public');
        }

        // Incrementar cupos ocupados
        $actividad->increment('cupos_ocupados');

        $pago = Pago::create([
            'empresa_id' => auth()->user()->empresa_id,
            'sucursal_id' => auth()->user()->sucursal_id,
            'participante_id' => $this->participante_id,
            'actividad_id' => $this->actividad_id,
            'metodo_pago_id' => $this->metodo_pago_id,
            'monto_euro' => $this->monto_euro,
            'tasa_cambio' => 0, // No aplica
            'monto_bolivares' => 0, // No aplica
            'fecha_pago' => $this->fecha_pago,
            'referencia_bancaria' => $this->referencia_bancaria,
            'evidencia_pago' => $path,
            'status' => 'Pendiente', // Por defecto pendiente de revisión
            'observaciones' => $this->observaciones
        ]);

        // Enviar notificación por WhatsApp si es Pago Móvil (Ya no aplica, pero mantenemos por si acaso)
        // if ($this->metodo_pago_selected && $this->metodo_pago_selected->tipo_pago === 'Pago Móvil') {
        //     $this->sendWhatsAppNotification($pago);
        // }

        session()->flash('message', 'Pago registrado correctamente.');
        return redirect()->route('admin.pagos.index');
    }

    private function formatPhoneNumber($phone)
    {
        // Limpiar todo lo que no sea número
        $phone = preg_replace('/\D/', '', $phone);
        
        $empresa = auth()->user()->empresa;
        // Obtener código de país de la empresa, limpiar el "+" si lo tiene
        $countryCode = isset($empresa->codigo_telefono) ? str_replace('+', '', $empresa->codigo_telefono) : '58';
        
        // Si el número empieza con el código de país (ej: 58424...), verificar si tiene un 0 redundante (580424...)
        if (str_starts_with($phone, $countryCode)) {
            $numberPart = substr($phone, strlen($countryCode));
            if (str_starts_with($numberPart, '0')) {
                // Caso raro: 580424... -> 58424...
                $phone = $countryCode . substr($numberPart, 1);
            }
            return $phone;
        }

        // Si el número empieza con 0 (ej: 0424...), quitar el 0 y agregar código país
        if (str_starts_with($phone, '0')) {
            $phone = substr($phone, 1);
        }
        
        return $countryCode . $phone;
    }

    public function render()
    {
        return view('livewire.admin.pagos.create', [
            'actividades' => Actividad::where('status', 'Activo')->get(),
            'metodos_pago' => MetodoPago::forUser()->where('status', true)->get()
        ])->layout($this->getLayout());
    }
}
