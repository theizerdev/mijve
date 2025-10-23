<?php

namespace App\Livewire\Admin\Students;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Student;
use App\Models\EducationalLevel;
use App\Models\Turno;
use App\Models\SchoolPeriod;
use App\Mail\StudentWelcomeMail;
use App\Mail\RepresentativeWelcomeMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $nivelEducativoId = '';
    public $turnoId = '';
    public $schoolPeriodId = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $showQrModal = false;
    public $selectedStudent = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'nivelEducativoId' => ['except' => ''],
        'turnoId' => ['except' => ''],
        'schoolPeriodId' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10]
    ];

    public function mount()
    {
        // Verificar permiso para ver estudiantes
        if (!Auth::user()->can('view students')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingNivelEducativoId()
    {
        $this->resetPage();
    }

    public function updatingTurnoId()
    {
        $this->resetPage();
    }

    public function updatingSchoolPeriodId()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortBy = $field;
    }

    public function render()
    {
        $students = Student::with(['nivelEducativo', 'turno', 'schoolPeriod'])
            ->when($this->search, function ($query) {
                $query->where('nombres', 'like', '%' . $this->search . '%')
                    ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                    ->orWhere('codigo', 'like', '%' . $this->search . '%')
                    ->orWhere('documento_identidad', 'like', '%' . $this->search . '%');
            })
            ->when($this->status !== '', function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->nivelEducativoId, function ($query) {
                $query->where('nivel_educativo_id', $this->nivelEducativoId);
            })
            ->when($this->turnoId, function ($query) {
                $query->where('turno_id', $this->turnoId);
            })
            ->when($this->schoolPeriodId, function ($query) {
                $query->where('school_periods_id', $this->schoolPeriodId);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $nivelesEducativos = EducationalLevel::all();
        $turnos = Turno::all();
        $schoolPeriods = SchoolPeriod::all();

        // Calcular estadísticas
        $totalStudents = Student::count();
        $activeStudents = Student::where('status', 1)->count();
        $inactiveStudents = Student::where('status', 0)->count();

        return view('livewire.admin.students.index', compact(
            'students',
            'nivelesEducativos',
            'turnos',
            'schoolPeriods',
            'totalStudents',
            'activeStudents',
            'inactiveStudents'
        ))
        ->layout('components.layouts.admin', [
            'title' => 'Lista de Estudiantes'
        ]);
    }

    public function delete(Student $student)
    {
        // Verificar permiso para eliminar estudiantes
        if (!Auth::user()->can('delete students')) {
            session()->flash('error', 'No tienes permiso para eliminar estudiantes.');
            return;
        }

        $student->delete();
        session()->flash('message', 'Estudiante eliminado correctamente.');
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->status = '';
        $this->nivelEducativoId = '';
        $this->turnoId = '';
        $this->schoolPeriodId = '';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->perPage = 10;
        $this->resetPage();
    }

    public function sendWelcomeEmail(Student $student)
    {
        // Verificar permiso para enviar correos
        if (!Auth::user()->can('edit students')) {
            session()->flash('error', 'No tienes permiso para enviar correos de bienvenida.');
            return;
        }

        try {
            // Para estudiantes mayores de edad con correo
            if (!$student->esMenorDeEdad && $student->correo_electronico) {
                Mail::to($student->correo_electronico)->send(new StudentWelcomeMail($student));
                session()->flash('message', 'Correo de bienvenida enviado al estudiante.');
            }
            // Para estudiantes menores de edad con correo de representante
            elseif ($student->esMenorDeEdad && $student->representante_correo) {
                Mail::to($student->representante_correo)->send(new RepresentativeWelcomeMail($student));
                session()->flash('message', 'Correo de bienvenida enviado al representante.');
            }
            // Si no hay correo al que enviar
            else {
                session()->flash('error', 'No hay correo registrado para enviar el mensaje de bienvenida.');
                return;
            }
        } catch (\Exception $e) {
            \Log::error('Error al enviar correo de bienvenida: ' . $e->getMessage());
            session()->flash('error', 'Error al enviar el correo de bienvenida: ' . $e->getMessage() . '. Por favor, inténtelo más tarde.');
        }

        $this->resetPage();
    }

    public function showQrCode(Student $student)
    {
        $this->selectedStudent = $student;
        $this->showQrModal = true;
    }

    public function closeQrModal()
    {
        $this->showQrModal = false;
        $this->selectedStudent = null;
    }

    public function downloadQrCode(Student $student)
    {
        // Generar código QR en formato PNG
        $qrCode = $student->generateQrCode(300);
        $imageData = base64_decode(substr($qrCode, strpos($qrCode, ",") + 1));

        // Nombre del archivo
        $filename = 'qr_' . $student->codigo . '_' . str_replace(' ', '_', $student->nombres) . '_' . str_replace(' ', '_', $student->apellidos) . '.svg';

        // Enviar respuesta con la imagen PNG
        return response()->streamDownload(function () use ($imageData) {
            echo $imageData;
        }, $filename, [
            'Content-Type' => 'image/svg',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }
}
