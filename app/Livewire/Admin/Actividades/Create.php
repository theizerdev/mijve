<?php

namespace App\Livewire\Admin\Actividades;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Actividad;
use App\Models\Empresa;
use App\Models\Sucursal;
use Illuminate\Support\Facades\Auth;

class Create extends Component
{
    use HasDynamicLayout;

    public $empresa_id = '';
    public $sucursal_id = '';
    public $nombre = '';
    public $fecha_inicio = '';
    public $fecha_fin = '';
    public $descripcion = '';
    public $direccion = '';
    public $latitud = null;
    public $longitud = null;
    public $status = 'Pendiente';
    public $edad_desde = 0;
    public $edad_hasta = 0;
    public $costo = 0;
    public $capacidad = 400; // Valor por defecto solicitado

    public $empresas;
    public $sucursales = [];

    protected $rules = [
        'empresa_id' => 'nullable|exists:empresas,id',
        'sucursal_id' => 'nullable|exists:sucursales,id',
        'nombre' => 'required|string|min:3|max:100',
        'fecha_inicio' => 'required|date|after_or_equal:today',
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

    public function mount()
    {
        if (!Auth::user()->can('create actividades')) {
            abort(403, 'No tienes permiso para crear actividades.');
        }

        $this->empresas = Empresa::where('status', true)->get();
        
        if (Auth::user()->empresa_id) {
            $this->empresa_id = Auth::user()->empresa_id;
            $this->updatedEmpresaId($this->empresa_id);
        }
        
        if (Auth::user()->sucursal_id) {
            $this->sucursal_id = Auth::user()->sucursal_id;
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

        Actividad::create([
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
            'cupos_ocupados' => 0
        ]);

        session()->flash('message', 'Actividad creada correctamente.');

        return redirect()->route('admin.actividades.index');
    }

    public function render()
    {
        return view('livewire.admin.actividades.create')->layout($this->getLayout());
    }
}
