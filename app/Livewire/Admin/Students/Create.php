<?php

namespace App\Livewire\Admin\Students;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Student;
use App\Models\EducationalLevel;
use App\Models\Turno;
use App\Models\SchoolPeriod;
use App\Mail\StudentWelcomeMail;
use App\Mail\RepresentativeWelcomeMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class Create extends Component
{
    use WithFileUploads;

    public $nombres = '';
    public $apellidos = '';
    public $fecha_nacimiento = '';
    public $codigo = '';
    public $documento_identidad = '';
    public $grado = '';
    public $seccion = '';
    public $nivel_educativo_id = '';
    public $turno_id = '';
    public $school_periods_id = '';
    public $foto;
    public $correo_electronico = ''; // Nuevo campo para estudiantes mayores de edad
    public $status = true;
    public $useCamera = false;
    public $cameraImage = null;

    // Campos para representante (solo para menores de edad)
    public $representante_nombres = '';
    public $representante_apellidos = '';
    public $representante_documento_identidad = '';
    public $representante_telefonos = ''; // Se manejará como string separado por comas
    public $representante_correo = '';

    protected $rules = [
        'nombres' => 'required|string|max:255',
        'apellidos' => 'required|string|max:255',
        'fecha_nacimiento' => 'required|date|before:today',
        'codigo' => 'required|string|unique:students,codigo|size:8',
        'documento_identidad' => 'required|string|unique:students,documento_identidad',
        'grado' => 'required|string|max:50',
        'seccion' => 'required|string|max:10',
        'nivel_educativo_id' => 'required|exists:niveles_educativos,id',
        'turno_id' => 'required|exists:turnos,id',
        'school_periods_id' => 'required|exists:school_periods,id',
        'foto' => 'nullable|image|max:2048',
        'correo_electronico' => 'nullable|email|max:255', // Validación para estudiantes mayores de edad
        'status' => 'boolean',
        // Validaciones para representante (solo para menores de edad)
        'representante_nombres' => 'nullable|string|max:255',
        'representante_apellidos' => 'nullable|string|max:255',
        'representante_documento_identidad' => 'nullable|string',
        'representante_telefonos' => 'nullable|string|max:255',
        'representante_correo' => 'nullable|email|max:255',
    ];

    protected $messages = [
        'fecha_nacimiento.before' => 'La fecha de nacimiento no puede ser una fecha futura.',
        'codigo.unique' => 'Este código ya está en uso.',
        'documento_identidad.unique' => 'Este documento de identidad ya está registrado.',
    ];

    public function mount()
    {
        // Verificar permiso para crear estudiantes
        if (!Auth::user()->can('create students')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        // Generar código aleatorio de 8 dígitos
        $this->generateCode();
    }

    public function generateCode()
    {
        do {
            $this->codigo = str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
        } while (Student::where('codigo', $this->codigo)->exists());
    }

    public function toggleCamera()
    {
        $this->useCamera = !$this->useCamera;
    }

    // Función para verificar si el estudiante es menor de edad
    public function getEsMenorDeEdadProperty()
    {
        if ($this->fecha_nacimiento) {
            try {
                $fechaNacimiento = Carbon::createFromFormat('Y-m-d', $this->fecha_nacimiento);
                return $fechaNacimiento->age < 18;
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }

    // Función para obtener la edad con años y meses
    public function getEdadConMesesProperty()
    {
        if ($this->fecha_nacimiento) {
            try {
                $fechaNacimiento = Carbon::createFromFormat('Y-m-d', $this->fecha_nacimiento);
                $now = Carbon::now();

                // Asegurarse de que la fecha de nacimiento no sea futura
                if ($fechaNacimiento->isFuture()) {
                    return 'Fecha inválida';
                }

                // Calcular diferencia usando Carbon de forma precisa
                $years = $now->diff($fechaNacimiento)->y;
                $months = $now->diff($fechaNacimiento)->m;

                if ($years < 10) {
                    if ($months == 0) {
                        return "$years año" . ($years != 1 ? 's' : '');
                    }
                    return "$years año" . ($years != 1 ? 's' : '') . " y $months mes" . ($months != 1 ? 'es' : '');
                }

                return "$years años";
            } catch (\Exception $e) {
                return 'Edad no disponible';
            }
        }
        return 'Fecha no especificada';
    }

    public function save()
    {
        $this->validate();

        $fotoPath = null;
        if ($this->foto) {
            $fotoPath = $this->foto->store('students', 'public');
        }

        $student = Student::create([
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'fecha_nacimiento' => $this->fecha_nacimiento,
            'codigo' => $this->codigo,
            'documento_identidad' => $this->documento_identidad,
            'grado' => $this->grado,
            'seccion' => $this->seccion,
            'nivel_educativo_id' => $this->nivel_educativo_id,
            'turno_id' => $this->turno_id,
            'school_periods_id' => $this->school_periods_id,
            'foto' => $fotoPath,
            'correo_electronico' => $this->correo_electronico, // Guardar correo para estudiantes mayores
            'status' => $this->status,
            // Datos del representante (solo para menores de edad)
            'representante_nombres' => $this->representante_nombres,
            'representante_apellidos' => $this->representante_apellidos,
            'representante_documento_identidad' => $this->representante_documento_identidad,
            'representante_telefonos' => $this->representante_telefonos,
            'representante_correo' => $this->representante_correo,
        ]);

        // Enviar correo de bienvenida si el estudiante es mayor de edad y tiene correo
        if (!$this->esMenorDeEdad && $this->correo_electronico) {
            try {
                Mail::to($this->correo_electronico)->send(new StudentWelcomeMail($student));
            } catch (\Exception $e) {
                // Registrar el error pero no detener el proceso de registro
                \Log::error('Error al enviar correo de bienvenida al estudiante: ' . $e->getMessage());
                session()->flash('warning', 'Estudiante creado correctamente, pero hubo un error al enviar el correo de bienvenida. Puedes reenviarlo más tarde desde la lista de estudiantes.');
            }
        }

        // Enviar correo de bienvenida al representante si el estudiante es menor de edad y tiene correo
        if ($this->esMenorDeEdad && $this->representante_correo) {
            try {
                Mail::to($this->representante_correo)->send(new RepresentativeWelcomeMail($student));
            } catch (\Exception $e) {
                // Registrar el error pero no detener el proceso de registro
                \Log::error('Error al enviar correo de bienvenida al representante: ' . $e->getMessage());
                session()->flash('warning', 'Estudiante creado correctamente, pero hubo un error al enviar el correo de bienvenida al representante. Puedes reenviarlo más tarde desde la lista de estudiantes.');
            }
        }

        session()->flash('message', 'Estudiante creado correctamente.');
        return redirect()->route('admin.students.index');
    }

    public function render()
    {
        $nivelesEducativos = EducationalLevel::where('status', 1)->get();
        $turnos = Turno::where('status', 1)->get();
        $schoolPeriods = SchoolPeriod::where('is_active', 1)->get();

        return view('livewire.admin.students.create', compact('nivelesEducativos', 'turnos', 'schoolPeriods'))
            ->layout('components.layouts.admin');
    }
}
