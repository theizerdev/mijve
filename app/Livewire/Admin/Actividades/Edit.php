<?php

namespace App\Livewire\Admin\Actividades;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Actividad;
use App\Models\Empresa;
use App\Models\Sucursal;
use Illuminate\Support\Facades\Auth;

class Edit extends Component
{
    use HasDynamicLayout;

    public $actividad;
    public $empresa_id = '';
    public $sucursal_id = '';
    public $nombre = '';
    public $fecha_inicio = '';
    public $fecha_fin = '';
    public $descripcion = '';
    public $direccion = '';
    public $latitud = null;
    public $longitud = null;
    public $status = '';
    public $edad_desde = 0;
    public $edad_hasta = 0;
    public $costo = 0;
    public $capacidad = 400;

    public $empresas;
    public $sucursales = [];

    protected function rules()
    {
        return [
            'empresa_id' => 'nullable|exists:empresas,id',
            'sucursal_id' => 'nullable|exists:sucursales,id',
            'nombre' => 'required|string|min:3|max:100',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'descripcion' => 'nullable|string|max:500',
            'direccion' => 'nullable|string|max:255',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'status' => 'required|in:Activo,Inactivo,Pendiente',
            'edad_desde' => 'required|integer|min:0|max:100',
            'edad_hasta' => 'required|integer|gte:edad_desde|max:100',
            'costo' => 'required|numeric|min:0',
            'capacidad' => 'required|integer|min:1',
        ];
    }

    public function mount(Actividad $actividad)
    {
        if (!Auth::user()->can('edit actividades')) {
            abort(403, 'No tienes permiso para editar actividades.');
        }

        $this->actividad = $actividad;
        $this->empresa_id = $actividad->empresa_id;
        $this->sucursal_id = $actividad->sucursal_id;
        $this->nombre = $actividad->nombre;
        $this->fecha_inicio = $actividad->fecha_inicio->format('Y-m-d');
        $this->fecha_fin = $actividad->fecha_fin->format('Y-m-d');
        $this->descripcion = $actividad->descripcion;
        $this->direccion = $actividad->direccion;
        $this->latitud = $actividad->latitud;
        $this->longitud = $actividad->longitud;
        $this->status = $actividad->status;
        $this->edad_desde = $actividad->edad_desde;
        $this->edad_hasta = $actividad->edad_hasta;
        $this->costo = $actividad->costo;
        $this->capacidad = $actividad->capacidad;

        $this->empresas = Empresa::where('status', true)->get();
        
        if ($this->empresa_id) {
            $this->sucursales = Sucursal::where('empresa_id', $this->empresa_id)->where('status', true)->get();
        }
    }

    public function updatedEmpresaId($value)
    {
        if ($value) {
            $this->sucursales = Sucursal::where('empresa_id', $value)->where('status', true)->get();
        } else {
            $this->sucursales = [];
        }
        $this->sucursal_id = '';
    }

    public function save()
    {
        $this->validate();

        $this->actividad->update([
            'empresa_id' => $this->empresa_id ?: null,
            'sucursal_id' => $this->sucursal_id ?: null,
            'nombre' => $this->nombre,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'descripcion' => $this->descripcion,
            'direccion' => $this->direccion,
            'latitud' => $this->latitud,
            'longitud' => $this->longitud,
            'status' => $this->status,
            'edad_desde' => $this->edad_desde,
            'edad_hasta' => $this->edad_hasta,
            'costo' => $this->costo,
            'capacidad' => $this->capacidad,
        ]);

        session()->flash('message', 'Actividad actualizada correctamente.');

        return redirect()->route('admin.actividades.index');
    }

    public function render()
    {
        return view('livewire.admin.actividades.edit')->layout($this->getLayout());
    }
}
