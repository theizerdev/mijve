<?php

namespace App\Livewire\Admin\Certificates;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Certificate;
use App\Models\Student;
use App\Models\Matricula;
use App\Models\SchoolPeriod;
use App\Models\AcademicRecord;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Create extends Component
{
    use HasDynamicLayout;

    public $student_id = '';
    public $matricula_id = '';
    public $school_period_id = '';
    public $certificate_type = 'enrollment';
    public $observations = '';
    
    public $student = null;
    public $matriculas = [];
    public $academicData = null;

    protected $rules = [
        'student_id' => 'required|exists:students,id',
        'certificate_type' => 'required|in:academic,attendance,conduct,completion,enrollment',
        'school_period_id' => 'required|exists:school_periods,id',
    ];

    public function updatedStudentId()
    {
        if ($this->student_id) {
            $this->student = Student::with(['nivelEducativo'])->find($this->student_id);
            $this->matriculas = Matricula::where('student_id', $this->student_id)
                ->with(['programa', 'schoolPeriod'])
                ->orderBy('created_at', 'desc')
                ->get();
            $this->loadAcademicData();
        } else {
            $this->student = null;
            $this->matriculas = [];
            $this->academicData = null;
        }
    }

    public function updatedMatriculaId()
    {
        $this->loadAcademicData();
    }

    public function loadAcademicData()
    {
        if (!$this->student_id || !$this->school_period_id) {
            $this->academicData = null;
            return;
        }

        // Cargar registros académicos
        $records = AcademicRecord::where('student_id', $this->student_id)
            ->where('school_period_id', $this->school_period_id)
            ->get();

        // Calcular asistencia
        $attendanceQuery = Attendance::where('student_id', $this->student_id);
        if ($this->school_period_id) {
            $attendanceQuery->where('school_period_id', $this->school_period_id);
        }
        
        $totalDays = $attendanceQuery->count();
        $presentDays = $attendanceQuery->clone()->whereIn('status', ['present', 'late'])->count();
        $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0;

        $this->academicData = [
            'overall_average' => $records->avg('final_grade') ?? 0,
            'total_subjects' => $records->count(),
            'approved_subjects' => $records->where('status', 'approved')->count(),
            'attendance_percentage' => $attendancePercentage,
        ];
    }

    public function generate()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $user = Auth::user();
            
            // Crear el certificado
            $certificate = Certificate::create([
                'student_id' => $this->student_id,
                'matricula_id' => $this->matricula_id ?: null,
                'school_period_id' => $this->school_period_id,
                'empresa_id' => $user->empresa_id,
                'sucursal_id' => $user->sucursal_id,
                'certificate_type' => $this->certificate_type,
                'issue_date' => now(),
                'status' => Certificate::STATUS_ACTIVE,
                'overall_average' => $this->academicData['overall_average'] ?? null,
                'total_subjects' => $this->academicData['total_subjects'] ?? null,
                'approved_subjects' => $this->academicData['approved_subjects'] ?? null,
                'attendance_percentage' => $this->academicData['attendance_percentage'] ?? null,
                'academic_data' => $this->academicData,
                'observations' => $this->observations,
                'issued_by' => $user->name,
                'issued_by_user_id' => $user->id,
                'is_digital' => true,
            ]);

            // Generar número y código de verificación
            $certificate->update([
                'certificate_number' => $certificate->generateCertificateNumber(),
                'verification_code' => $certificate->generateVerificationCode(),
            ]);

            DB::commit();

            session()->flash('message', 'Certificado generado correctamente. Número: ' . $certificate->certificate_number);
            return redirect()->route('admin.certificates.show', $certificate->id);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al generar certificado: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $students = Student::orderBy('apellidos')->orderBy('nombres')->get();
        $schoolPeriods = SchoolPeriod::orderBy('year', 'desc')->get();
        $types = Certificate::getTypes();

        return view('livewire.admin.certificates.create', compact('students', 'schoolPeriods', 'types'))
            ->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Generar Certificado';
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.certificates.index' => 'Certificados',
            'admin.certificates.create' => 'Generar'
        ];
    }
}
