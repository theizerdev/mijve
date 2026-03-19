<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Participante;
use App\Models\Empresa;
use App\Models\Sucursal;
use App\Models\Extension;
use App\Models\Actividad;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\WhatsAppMessage;

class RegistroParticipante extends Component
{
    // Wizard step
    public int $currentStep = 1;
    public int $totalSteps = 5;

    // Step 1: Términos y Condiciones
    public bool $acepta_terminos = false;

    // Step 2: Ubicación y Actividad
    public $empresa_id = '';
    public $sucursal_id = '';
    public $extension_id = '';
    public $actividad_id = '';
    public $zona = '';
    public $distrito = '';

    // Step 3: Información Personal
    public $nombres = '';
    public $apellidos = '';
    public $cedula = '';
    public $fecha_nacimiento = '';
    public $edad = '';
    public $genero = '';
    public $estado_civil = '';
    public $tipo_miembro = '';

    // Step 4: Contacto
    public $telefono_principal = '';
    public $telefono_alternativo = '';
    public $direccion = '';

    // Data collections
    public $empresas = [];
    public $sucursales = [];
    public $extensiones = [];
    public $actividades = [];

    // State
    public bool $registroExitoso = false;
    public $participanteCreado = null;

    public function mount()
    {
        $this->empresas = Empresa::where('status', true)->get();
        $this->actividades = Actividad::where('status', 'Activo')->get();

        // Si solo hay una empresa, preseleccionarla
        if ($this->empresas->count() === 1) {
            $this->empresa_id = $this->empresas->first()->id;
            $this->loadDependencies($this->empresa_id);
        }

        if (empty($this->sucursal_id)) {
            $this->sucursal_id = 1;
        }
    }

    private function loadDependencies($empresaId)
    {
        if ($empresaId) {
            $this->sucursales = Sucursal::where('empresa_id', $empresaId)->where('status', true)->get();
            $this->extensiones = Extension::where('empresa_id', $empresaId)->where('status', 'Activo')->get();
        } else {
            $this->sucursales = collect();
            $this->extensiones = collect();
        }
    }

    public function updatedEmpresaId($value)
    {
        $this->loadDependencies($value);
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

    public function updatedFechaNacimiento($value)
    {
        if ($value) {
            try {
                $this->edad = Carbon::parse($value)->age;
            } catch (\Exception $e) {
                $this->edad = '';
            }
            $this->validateEdadConActividad();
        }
    }

    public function updatedActividadId($value)
    {
        $this->validateEdadConActividad();
        $this->validateCapacidadActividad();
    }

    private function validateCapacidadActividad()
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

    private function validateEdadConActividad()
    {
        if ($this->actividad_id && $this->edad !== '') {
            $actividad = Actividad::find($this->actividad_id);
            if ($actividad) {
                if ($this->edad > 35) {
                    $this->resetErrorBag('edad');
                } elseif ($this->edad < $actividad->edad_desde || $this->edad > $actividad->edad_hasta) {
                    $this->addError('edad', "La edad debe estar entre {$actividad->edad_desde} y {$actividad->edad_hasta} años para esta actividad.");
                } else {
                    $this->resetErrorBag('edad');
                }
            }
        }
    }

    // Step validation rules
    private function rulesForStep(int $step): array
    {
        return match ($step) {
            1 => [
                'acepta_terminos' => 'accepted',
            ],
            2 => [
                'empresa_id'   => 'required|exists:empresas,id',
                'extension_id' => 'required|exists:extensiones,id',
                'actividad_id' => 'required|exists:actividads,id',
            ],
            3 => [
                'nombres'          => 'required|string|min:3|max:100',
                'apellidos'        => 'required|string|min:3|max:100',
                'cedula'           => 'nullable|string|max:20|unique:participantes,cedula',
                'fecha_nacimiento' => 'required|date|before:today',
                'edad'             => 'required|integer|min:0',
                'genero'           => 'required|in:Masculino,Femenino',
                'tipo_miembro'     => 'required|in:Miembro Activo,Probante',
            ],
            4 => [
                'telefono_principal'  => 'nullable|string|max:20',
                'telefono_alternativo' => 'nullable|string|max:20',
                'direccion'           => 'nullable|string|max:500',
            ],
            default => [],
        };
    }

    private function messagesForStep(): array
    {
        return [
            'acepta_terminos.accepted' => 'Debes aceptar los términos y condiciones para continuar.',
            'empresa_id.required'   => 'Debes seleccionar una empresa.',
            'extension_id.required' => 'Debes seleccionar una extensión.',
            'actividad_id.required' => 'Debes seleccionar una actividad.',
            'nombres.required'      => 'Los nombres son obligatorios.',
            'nombres.min'           => 'Los nombres deben tener al menos 3 caracteres.',
            'apellidos.required'    => 'Los apellidos son obligatorios.',
            'apellidos.min'         => 'Los apellidos deben tener al menos 3 caracteres.',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.before'   => 'La fecha de nacimiento debe ser anterior a hoy.',
            'edad.required'         => 'La edad es obligatoria. Selecciona una fecha de nacimiento.',
            'genero.required'       => 'El género es obligatorio.',
            'tipo_miembro.required' => 'Debes indicar si eres miembro activo o probante.',
        ];
    }

    public function nextStep()
    {
        $this->validate(
            $this->rulesForStep($this->currentStep),
            $this->messagesForStep()
        );

        // Validación extra de edad vs actividad en step 3
        if ($this->currentStep === 3) {
            $this->validateEdadConActividad();
            if ($this->getErrorBag()->has('edad')) {
                return;
            }
        }

        // Validación de capacidad en step 2
        if ($this->currentStep === 2) {
            $this->validateCapacidadActividad();
            if ($this->getErrorBag()->has('actividad_id')) {
                return;
            }
        }

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
            $this->dispatch('stepChanged', step: $this->currentStep);
        }
    }

    public function prevStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
            $this->dispatch('stepChanged', step: $this->currentStep);
        }
    }

    public function goToStep(int $step)
    {
        // Solo permitir ir a steps ya visitados (hacia atrás)
        if ($step < $this->currentStep && $step >= 1) {
            $this->currentStep = $step;
            $this->dispatch('stepChanged', step: $this->currentStep);
        }
    }

    public function save()
    {
        // Validar todos los steps
        $allRules = array_merge(
            $this->rulesForStep(1),
            $this->rulesForStep(2),
            $this->rulesForStep(3),
            $this->rulesForStep(4)
        );

        $this->validate($allRules, $this->messagesForStep());
        $this->validateEdadConActividad();
        $this->validateCapacidadActividad();

        if ($this->getErrorBag()->has('edad') || $this->getErrorBag()->has('actividad_id')) {
            session()->flash('error', 'Por favor corrige los errores antes de continuar.');
            return;
        }

        // Validación final de capacidad antes de guardar
        $actividad = Actividad::find($this->actividad_id);
        $cuposOcupados = Participante::where('actividad_id', $this->actividad_id)->count();
        
        if ($cuposOcupados >= $actividad->capacidad) {
            session()->flash('error', 'Lo sentimos, la actividad ha alcanzado su capacidad máxima mientras completabas el registro.');
            $this->currentStep = 2;
            return;
        }

        $participante = Participante::create([
            'empresa_id'          => $this->empresa_id ?: null,
            'sucursal_id'         => $this->sucursal_id ?: 1,
            'extension_id'        => $this->extension_id,
            'actividad_id'        => $this->actividad_id,
            'nombres'             => $this->nombres,
            'apellidos'           => $this->apellidos,
            'cedula'              => $this->cedula,
            'telefono_principal'  => $this->telefono_principal,
            'telefono_alternativo' => $this->telefono_alternativo,
            'direccion'           => $this->direccion,
            'zona'                => $this->zona,
            'distrito'            => $this->distrito,
            'fecha_nacimiento'    => $this->fecha_nacimiento,
            'edad'                => $this->edad,
            'genero'              => $this->genero,
            'estado_civil'        => $this->estado_civil,
            'tipo_miembro'        => $this->tipo_miembro,
        ]);

        $this->notifyExtensionLeader($participante);
        $this->notifyParticipant($participante);

        $this->participanteCreado = $participante;
        $this->registroExitoso = true;
    }

    public function nuevoRegistro()
    {
        $this->reset([
            'currentStep', 'acepta_terminos', 'extension_id', 'sucursal_id', 'actividad_id',
            'zona', 'distrito', 'nombres', 'apellidos', 'cedula',
            'fecha_nacimiento', 'edad', 'genero', 'estado_civil', 'tipo_miembro',
            'telefono_principal', 'telefono_alternativo', 'direccion',
            'registroExitoso', 'participanteCreado',
        ]);
        $this->currentStep = 1;
        $this->resetErrorBag();
    }

    private function notifyExtensionLeader($participante)
    {
        
        try {

            $extension = Extension::with('lider.empresa.pais')->find($participante->extension_id);

            if (!$extension || !$extension->lider || empty($extension->lider->telefono)) {
                return;
            }

            $lider = $extension->lider;
            $nombreCompleto = $participante->nombres . ' ' . $participante->apellidos;
            $actividadNombre = $participante->actividad->nombre ?? 'N/A';
            $fechaRegistro = now()->format('d/m/Y h:i A');

            $telefono = preg_replace('/[^0-9]/', '', $lider->telefono);
            $codigoPais = '58';

            if ($lider->empresa && $lider->empresa->pais) {
                $codigoPais = preg_replace('/[^0-9]/', '', $lider->empresa->pais->codigo_telefonico ?? '58');
            }

            if (str_starts_with($telefono, '0')) {
                $telefono = $codigoPais . substr($telefono, 1);
            } elseif (!str_starts_with($telefono, $codigoPais)) {
                $telefono = $codigoPais . $telefono;
            }

            $mensaje = "🆕 *Nuevo Participante Registrado*\n\n";
            $mensaje .= "Hola *{$lider->name}*, se ha registrado un nuevo participante en tu extensión *{$extension->nombre}*.\n\n";
            $mensaje .= "👤 *Nombre:* {$nombreCompleto}\n";
            $mensaje .= "📅 *Edad:* {$participante->edad} años\n";
            $mensaje .= "🎭 *Actividad:* {$actividadNombre}\n";
            $mensaje .= "⛪ *Tipo:* {$participante->tipo_miembro}\n";
            $mensaje .= "🕒 *Fecha:* {$fechaRegistro}\n\n";
            $mensaje .= "Por favor, verifica la información en el sistema.";

            $whatsapp = new WhatsAppService($lider->empresa_id);
            $whatsapp->sendMessage($telefono, $mensaje);

        } catch (\Exception $e) {
            Log::error('Error notificando líder desde registro público: ' . $e->getMessage());
        }
     }


    private function notifyParticipant($participante): void
    {
        try {
            $extension = Extension::with('lider', 'empresa.pais')->find($participante->extension_id);
            if (!$extension) return;

            $telefonoRaw = $participante->telefono_principal ?: $participante->telefono_alternativo;
            if (empty($telefonoRaw)) {
                Log::warning('Participante sin teléfono, no se envía WhatsApp', ['participante_id' => $participante->id]);
                return;
            }

            $telefono = preg_replace('/[^0-9]/', '', $telefonoRaw);
            $codigoPais = '58';
            if ($extension->empresa && $extension->empresa->pais) {
                $codigoPais = preg_replace('/[^0-9]/', '', $extension->empresa->pais->codigo_telefonico ?? '58');
            }
            if (str_starts_with($telefono, '0')) {
                $telefono = $codigoPais . substr($telefono, 1);
            } elseif (!str_starts_with($telefono, $codigoPais)) {
                $telefono = $codigoPais . $telefono;
            }

           

            $nombreParticipante = trim($participante->nombres . ' ' . $participante->apellidos);
            $nombreLider = $extension->lider?->name ?: 'tu líder';
            $actividadNombre = $participante->actividad->nombre ?? 'la actividad';

            $mensaje = "✅ Confirmación de Registro\n\n";
            $mensaje .= "Hola {$nombreParticipante}, hemos recibido tu registro para la actividad {$actividadNombre}.\n\n";
            $mensaje .= "Para completar tu inscripción, por favor comunícate con tu Líder {$nombreLider}.\n";
            $mensaje .= "El aporte de la actividad es de 20 USD en efectivo.\n";
            $mensaje .= "Podemos coordinar la forma de pago si lo necesitas. 🙌";

            $empresaId = $participante->empresa_id ?: ($extension->empresa?->id ?? null);
            $whatsapp = new WhatsAppService($empresaId);
            $whatsapp->sendMessage($telefono, $mensaje);
            
        } catch (\Throwable $e) {
            Log::error('Error notificando participante desde registro público: ' . $e->getMessage(), [
                'participante_id' => $participante->id ?? null
            ]);
        }
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

    // Helpers para la vista de confirmación
    public function getExtensionNombre(): string
    {
        return Extension::find($this->extension_id)?->nombre ?? 'N/A';
    }

    public function getActividadNombre(): string
    {
        $actividad = Actividad::find($this->actividad_id);
        return $actividad ? $actividad->nombre : 'N/A';
    }

    public function getEmpresaNombre(): string
    {
        return Empresa::find($this->empresa_id)?->razon_social ?? 'N/A';
    }

    public function render()
    {
        return view('livewire.registro-participante')
            ->layout('components.layouts.guest-wizard', ['title' => 'Registro de Participante']);
    }
}
