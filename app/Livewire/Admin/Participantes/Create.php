<?php

namespace App\Livewire\Admin\Participantes;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Participante;
use App\Models\Empresa;
use App\Models\Sucursal;
use App\Models\Extension;
use App\Models\Actividad;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Create extends Component
{
    use HasDynamicLayout;

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
            'cedula' => 'nullable|string|max:20|unique:participantes,cedula',
            'telefono_principal' => 'nullable|string|max:20',
            'telefono_alternativo' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'zona' => 'nullable|string',
            'distrito' => 'nullable|string',
            'fecha_nacimiento' => 'required|date|before:today',
            'edad' => 'required|integer|min:0',
            'genero' => 'required|in:Masculino,Femenino',
        ];
    }

    public function mount()
    {
        if (!Auth::user()->can('create participantes')) {
            abort(403, 'No tienes permiso para crear participantes.');
        }

        $this->empresas = Empresa::where('status', true)->get();
        $this->actividades = Actividad::where('status', 'Activo')->get();

        if (Auth::user()->empresa_id) {
            $this->empresa_id = Auth::user()->empresa_id;
            $this->updatedEmpresaId($this->empresa_id);
        }
        
        if (Auth::user()->sucursal_id) {
            $this->sucursal_id = Auth::user()->sucursal_id;
        }

        if (empty($this->sucursal_id)) {
            $this->sucursal_id = 1;
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
        $this->sucursal_id = $this->sucursal_id ?: 1;
        $this->extension_id = '';
        $this->zona = '';
        $this->distrito = '';
    }

    public function updatedCedula($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            $this->resetErrorBag('cedula');
            return;
        }

        $exists = Participante::where('cedula', $value)->exists();
        if ($exists) {
            $this->addError('cedula', 'Ya existe un participante registrado con esta cédula.');
        } else {
            $this->resetErrorBag('cedula');
        }
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
        $this->validateCapacidadActividad();
    }

    public function validateCapacidadActividad()
    {
        if ($this->actividad_id) {
            $actividad = Actividad::find($this->actividad_id);
            if ($actividad) {
                $cuposOcupados = Participante::where('actividad_id', $this->actividad_id)->count();
                if ($cuposOcupados >= $actividad->capacidad) {
                    $this->addError('actividad_id', "Esta actividad ha alcanzado su capacidad máxima ({$actividad->capacidad} participantes). No se pueden registrar más participantes.");
                } else {
                    $this->resetErrorBag('actividad_id');
                }
            }
        }
    }

    public function validateEdadConActividad()
    {
        if ($this->actividad_id && $this->edad !== '') {
            $actividad = Actividad::find($this->actividad_id);
            if ($actividad) {
                if ($this->edad > 40) {
                    $this->resetErrorBag('edad');
                } elseif ($this->edad < $actividad->edad_desde || $this->edad > $actividad->edad_hasta) {
                    $this->addError('edad', "La edad debe estar entre {$actividad->edad_desde} y {$actividad->edad_hasta} años para esta actividad.");
                } else {
                    $this->resetErrorBag('edad');
                }
            }
        }
    }

    public function save()
    {
        $this->validate();
        $this->validateEdadConActividad();
        $this->validateCapacidadActividad();

        if ($this->getErrorBag()->has('edad') || $this->getErrorBag()->has('actividad_id')) {
            return;
        }

        // Validación final de capacidad antes de guardar
        $actividad = Actividad::find($this->actividad_id);
        $cuposOcupados = Participante::where('actividad_id', $this->actividad_id)->count();
        
        if ($cuposOcupados >= $actividad->capacidad) {
            session()->flash('error', 'La actividad ha alcanzado su capacidad máxima. No se pueden registrar más participantes.');
            return;
        }

        $participante = Participante::create([
            'empresa_id' => $this->empresa_id ?: null,
            'sucursal_id' => $this->sucursal_id ?: 1,
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

        // Notificar al Líder de la Extensión
        $this->notifyExtensionLeader($participante);

        session()->flash('message', 'Participante creado correctamente.');

        return redirect()->route('admin.participantes.index');
    }

    private function notifyExtensionLeader($participante)
    {
        try {
            if (!$participante->extension_id) return;

            $extension = Extension::with('lider.empresa.pais')->find($participante->extension_id);
            
            // Verificar si la extensión tiene un líder asignado con teléfono
            if (!$extension || !$extension->lider || empty($extension->lider->telefono)) {
                return;
            }

            $lider = $extension->lider;
            $nombreCompleto = $participante->nombres . ' ' . $participante->apellidos;
            $actividadNombre = $participante->actividad->nombre ?? 'N/A';
            $fechaRegistro = now()->format('d/m/Y h:i A');

            // Formatear teléfono del líder
            $telefono = preg_replace('/[^0-9]/', '', $lider->telefono);
            $codigoPais = '58'; // Default
            
            if ($lider->empresa && $lider->empresa->pais) {
                $codigoPais = preg_replace('/[^0-9]/', '', $lider->empresa->pais->codigo_telefono ?? '58');
            }

            if (substr($telefono, 0, 1) === '0') {
                $telefono = $codigoPais . substr($telefono, 1);
            } elseif (substr($telefono, 0, strlen($codigoPais)) !== $codigoPais) {
                $telefono = $codigoPais . $telefono;
            }

            // Construir mensaje
            $mensaje = "🆕 *Nuevo Participante Registrado*\n\n";
            $mensaje .= "Hola *{$lider->name}*, se ha registrado un nuevo participante en tu extensión *{$extension->nombre}*.\n\n";
            $mensaje .= "👤 *Nombre:* {$nombreCompleto}\n";
            $mensaje .= "📅 *Edad:* {$participante->edad} años\n";
            $mensaje .= "🎭 *Actividad:* {$actividadNombre}\n";
            $mensaje .= "🕒 *Fecha:* {$fechaRegistro}\n\n";
            $mensaje .= "Por favor, verifica la información en el sistema.";

            // Enviar WhatsApp
            $whatsapp = new WhatsAppService($lider->empresa_id);
            $whatsapp->sendMessage($telefono, $mensaje);

        } catch (\Exception $e) {
            \Log::error('Error notificando líder de extensión: ' . $e->getMessage());
            // No interrumpimos el flujo principal
        }
    }

    public function render()
    {
        return view('livewire.admin.participantes.create')->layout($this->getLayout());
    }
}
