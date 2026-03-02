<?php

namespace App\Livewire\Admin\Participantes;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Participante;
use App\Models\Empresa;
use App\Models\Sucursal;
use App\Models\Extension;
use App\Models\Actividad;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Edit extends Component
{
    use HasDynamicLayout;

    public $participante;
    public $empresa_id = '';
    public $sucursal_id = '';
    public $extension_id = '';
    public $actividad_id = '';
    public $nombres = '';
    public $apellidos = '';
    public $cedula = '';
    public $telefono_principal = '';
    public $telefono_alternativo = '';
    public $direccion = '';
    public $zona = '';
    public $distrito = '';
    public $fecha_nacimiento = '';
    public $edad = '';
    public $genero = '';
    public $estado_civil = '';

    public $empresas;
    public $sucursales = [];
    public $extensiones = [];
    public $actividades = [];

    protected function rules()
    {
        return [
            'empresa_id' => 'nullable|exists:empresas,id',
            'sucursal_id' => 'nullable|exists:sucursales,id',
            'extension_id' => 'required|exists:extensiones,id',
            'actividad_id' => 'required|exists:actividads,id',
            'nombres' => 'required|string|min:3|max:100',
            'apellidos' => 'required|string|min:3|max:100',
            'cedula' => 'nullable|string|max:20',
            'telefono_principal' => 'nullable|string|max:20',
            'telefono_alternativo' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'zona' => 'nullable|string',
            'distrito' => 'nullable|string',
            'fecha_nacimiento' => 'required|date|before:today',
            'edad' => 'required|integer|min:0',
            'genero' => 'required|in:Masculino,Femenino',
            'estado_civil' => 'nullable|in:Soltero(a),Casado(a),Divorciado(a),Viudo(a),Unión Libre',
        ];
    }

    public function mount(Participante $participante)
    {
        if (!Auth::user()->can('edit participantes')) {
            abort(403, 'No tienes permiso para editar participantes.');
        }

        $this->participante = $participante;
        $this->empresa_id = $participante->empresa_id;
        $this->sucursal_id = $participante->sucursal_id;
        $this->extension_id = $participante->extension_id;
        $this->actividad_id = $participante->actividad_id;
        $this->nombres = $participante->nombres;
        $this->apellidos = $participante->apellidos;
        $this->cedula = $participante->cedula;
        $this->telefono_principal = $participante->telefono_principal;
        $this->telefono_alternativo = $participante->telefono_alternativo;
        $this->direccion = $participante->direccion;
        $this->zona = $participante->zona;
        $this->distrito = $participante->distrito;
        $this->fecha_nacimiento = $participante->fecha_nacimiento->format('Y-m-d');
        $this->edad = $participante->edad;
        $this->genero = $participante->genero;
        $this->estado_civil = $participante->estado_civil;

        $this->empresas = Empresa::where('status', true)->get();
        $this->actividades = Actividad::where('status', 'Activo')->get();
        
        if ($this->empresa_id) {
            $this->sucursales = Sucursal::where('empresa_id', $this->empresa_id)->where('status', true)->get();
            $this->extensiones = Extension::where('empresa_id', $this->empresa_id)->where('status', 'Activo')->get();
        }

        // Si hay una extensión asignada, asegurarse que zona y distrito coincidan con ella (opcional, o dejarlos como están guardados)
        if ($this->extension_id) {
            $extension = Extension::find($this->extension_id);
            if ($extension) {
                $this->zona = $extension->zona;
                $this->distrito = $extension->distrito;
            }
        }
    }

    public function updatedEmpresaId($value)
    {
        if ($value) {
            $this->sucursales = Sucursal::where('empresa_id', $value)->where('status', true)->get();
            $this->extensiones = Extension::where('empresa_id', $value)->where('status', 'Activo')->get();
        } else {
            $this->sucursales = [];
            $this->extensiones = [];
        }
        $this->sucursal_id = '';
        $this->extension_id = '';
        $this->zona = '';
        $this->distrito = '';
    }

    public function updatedExtensionId($value)
    {
        if ($value) {
            $extension = Extension::find($value);
            if ($extension) {
                $this->zona = $extension->zona;
                $this->distrito = $extension->distrito;
            }
        } else {
            $this->zona = '';
            $this->distrito = '';
        }
    }

    public function save()
    {
        $this->validate();

        $this->participante->update([
            'empresa_id' => $this->empresa_id ?: null,
            'sucursal_id' => $this->sucursal_id ?: null,
            'extension_id' => $this->extension_id,
            'actividad_id' => $this->actividad_id,
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'cedula' => $this->cedula,
            'telefono_principal' => $this->telefono_principal,
            'telefono_alternativo' => $this->telefono_alternativo,
            'direccion' => $this->direccion,
            'zona' => $this->zona,
            'distrito' => $this->distrito,
            'fecha_nacimiento' => $this->fecha_nacimiento,
            'edad' => $this->edad,
            'genero' => $this->genero,
            'estado_civil' => $this->estado_civil,
        ]);

        session()->flash('message', 'Participante actualizado correctamente.');

        return redirect()->route('admin.participantes.index');
    }

    public function updatedFechaNacimiento($value)
    {
        if ($value) {
            $this->edad = Carbon::parse($value)->age;
            $this->validateEdadConActividad();
        }
    }

    public function updatedActividadId($value)
    {
        $this->validateEdadConActividad();
    }

    public function validateEdadConActividad()
    {
        if ($this->actividad_id && $this->edad !== '') {
            $actividad = Actividad::find($this->actividad_id);
            if ($actividad) {
                if ($this->edad < $actividad->edad_desde || $this->edad > $actividad->edad_hasta) {
                    $this->addError('edad', "La edad debe estar entre {$actividad->edad_desde} y {$actividad->edad_hasta} años para esta actividad.");
                } else {
                    $this->resetErrorBag('edad');
                }
            }
        }
    }

    

    public function render()
    {
        return view('livewire.admin.participantes.edit')->layout($this->getLayout());
    }
}
