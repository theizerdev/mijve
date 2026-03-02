<?php

namespace App\Livewire\Admin\Pagos;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Pago;
use App\Models\Participante;
use App\Models\Actividad;
use App\Models\MetodoPago;
use Illuminate\Support\Facades\Auth;

class Create extends Component
{
    use HasDynamicLayout;

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
    public $fecha_pago = '';
    public $observaciones = '';

    public $participantes = [];

    protected function rules()
    {
        return [
            'participante_id' => 'required|exists:participantes,id',
            'actividad_id' => 'required|exists:actividads,id',
            'metodo_pago_id' => 'required|exists:metodo_pagos,id',
            'monto_euro' => 'required|numeric|min:0.01',
            'fecha_pago' => 'required|date',
        ];
    }

    public function mount()
    {
        if (!Auth::user()->can('create pagos')) {
            abort(403, 'No tienes permiso para crear pagos.');
        }

        $this->fecha_pago = date('Y-m-d');
    }

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
            
            if (!$this->actividad_selected->tieneCuposDisponibles()) {
                session()->flash('error', "La actividad '{$this->actividad_selected->nombre}' no tiene cupos disponibles (Capacidad: {$this->actividad_selected->capacidad}).");
                $this->actividad_id = null;
                $this->actividad_selected = null;
                return;
            }

            if ($this->participante_id) {
                $pagoExistente = Pago::where('participante_id', $this->participante_id)
                    ->where('actividad_id', $value)
                    ->whereIn('status', ['Pendiente', 'Aprobado'])
                    ->first();
                
                if ($pagoExistente) {
                    session()->flash('error', 'Este participante ya tiene un pago registrado para esta actividad.');
                    $this->actividad_id = null;
                    $this->actividad_selected = null;
                    return;
                }
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

        $actividad->increment('cupos_ocupados');

        $pago = Pago::create([
            'empresa_id' => auth()->user()->empresa_id,
            'sucursal_id' => auth()->user()->sucursal_id,
            'participante_id' => $this->participante_id,
            'actividad_id' => $this->actividad_id,
            'metodo_pago_id' => $this->metodo_pago_id,
            'monto_euro' => $this->monto_euro,
            'tasa_cambio' => 0,
            'monto_bolivares' => 0,
            'fecha_pago' => $this->fecha_pago,
            'referencia_bancaria' => null,
            'evidencia_pago' => null,
            'status' => 'Pendiente',
            'observaciones' => $this->observaciones
        ]);

        session()->flash('message', 'Pago registrado correctamente.');
        return redirect()->route('admin.pagos.index');
    }

    public function render()
    {
        return view('livewire.admin.pagos.create', [
            'actividades' => Actividad::where('status', 'Activo')->get(),
            'metodos_pago' => MetodoPago::forUser()->where('status', true)->get()
        ])->layout($this->getLayout());
    }
}
