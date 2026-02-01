<?php

namespace App\Livewire\Admin\Attendance;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Attendance;
use App\Models\Section;
use App\Models\Student;
use App\Models\SchoolPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Register extends Component
{
    use HasDynamicLayout;

    public $section_id = '';
    public $school_period_id = '';
    public $date;
    public $students = [];
    public $attendanceData = [];
    public $bulkStatus = 'present';

    protected $rules = [
        'section_id' => 'required|exists:sections,id',
        'school_period_id' => 'required|exists:school_periods,id',
        'date' => 'required|date',
    ];

    public function mount()
    {
        $this->date = now()->format('Y-m-d');
        
        // Obtener período activo
        $activePeriod = SchoolPeriod::where('is_active', true)->first();
        if ($activePeriod) {
            $this->school_period_id = $activePeriod->id;
        }
    }

    public function updatedSectionId()
    {
        $this->loadStudents();
    }

    public function updatedDate()
    {
        $this->loadStudents();
    }

    public function loadStudents()
    {
        if (!$this->section_id) {
            $this->students = [];
            $this->attendanceData = [];
            return;
        }

        $section = Section::with(['students' => function($q) {
            $q->wherePivot('estado', 'activo')->orderBy('apellidos')->orderBy('nombres');
        }])->find($this->section_id);

        if (!$section) return;

        $this->students = $section->students;
        
        // Cargar asistencias existentes para la fecha
        $existingAttendances = Attendance::where('section_id', $this->section_id)
            ->whereDate('date', $this->date)
            ->get()
            ->keyBy('student_id');

        $this->attendanceData = [];
        foreach ($this->students as $student) {
            $existing = $existingAttendances->get($student->id);
            $this->attendanceData[$student->id] = [
                'status' => $existing ? $existing->status : 'present',
                'observations' => $existing ? $existing->observations : '',
                'arrival_time' => $existing ? ($existing->arrival_time ? $existing->arrival_time->format('H:i') : '') : '',
            ];
        }
    }

    public function applyBulkStatus()
    {
        foreach ($this->attendanceData as $studentId => $data) {
            $this->attendanceData[$studentId]['status'] = $this->bulkStatus;
        }
    }

    public function save()
    {
        $this->validate();

        if (empty($this->attendanceData)) {
            session()->flash('error', 'No hay estudiantes para registrar asistencia.');
            return;
        }

        try {
            DB::beginTransaction();

            $user = Auth::user();

            foreach ($this->attendanceData as $studentId => $data) {
                Attendance::updateOrCreate(
                    [
                        'section_id' => $this->section_id,
                        'student_id' => $studentId,
                        'date' => $this->date,
                    ],
                    [
                        'empresa_id' => $user->empresa_id,
                        'sucursal_id' => $user->sucursal_id,
                        'school_period_id' => $this->school_period_id,
                        'status' => $data['status'],
                        'observations' => $data['observations'] ?? null,
                        'arrival_time' => !empty($data['arrival_time']) ? $data['arrival_time'] : null,
                        'registered_by' => $user->id,
                    ]
                );
            }

            DB::commit();

            session()->flash('message', 'Asistencia registrada correctamente para ' . count($this->attendanceData) . ' estudiantes.');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al registrar asistencia: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $sections = Section::active()->orderBy('nombre')->get();
        $schoolPeriods = SchoolPeriod::where('is_active', true)->orderBy('year', 'desc')->get();
        $statuses = Attendance::getStatuses();
        $statusColors = Attendance::getStatusColors();

        return view('livewire.admin.attendance.register', compact('sections', 'schoolPeriods', 'statuses', 'statusColors'))
            ->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Registrar Asistencia';
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.attendance.index' => 'Asistencia',
            'admin.attendance.register' => 'Registrar'
        ];
    }
}
