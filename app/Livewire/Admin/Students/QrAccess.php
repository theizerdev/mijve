<?php

namespace App\Livewire\Admin\Students;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Student;
use App\Models\StudentAccessLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Models\Notification;

class QrAccess extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedStudent = null;
    public $accessType = 'entrada'; // entrada o salida
    public $notes = '';
    public $soundEnabled = true;
    public $scanMode = 'camera'; // camera o manual
    public $manualCode = '';
    public $showStudentInfo = false;
    public $todayLogs = [];
    public $stats = [
        'entries' => 0,
        'exits' => 0,
        'total' => 0,
        'activeStudents' => 0
    ];

    protected $listeners = ['qr-scanned' => 'processQrScan'];

    public function mount()
    {
        // Verificar permiso para acceder al control de acceso
        if (!Auth::user()->can('access students')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
        
        // Cargar estadísticas iniciales
        $this->loadStats();
        $this->loadTodayLogs();
    }

    public function loadStats()
    {
        $today = Carbon::today();
        
        $this->stats['entries'] = StudentAccessLog::whereDate('access_time', $today)
            ->where('type', 'entrada')
            ->count();
            
        $this->stats['exits'] = StudentAccessLog::whereDate('access_time', $today)
            ->where('type', 'salida')
            ->count();
            
        $this->stats['total'] = $this->stats['entries'] + $this->stats['exits'];
        
        $this->stats['activeStudents'] = Student::where('status', 1)->count();
    }

    public function loadTodayLogs()
    {
        $this->todayLogs = StudentAccessLog::with(['student', 'registeredBy'])
            ->whereDate('access_time', Carbon::today())
            ->orderBy('access_time', 'desc')
            ->limit(20)
            ->get()
            ->map(function($log) {
                return [
                    'id' => $log->id,
                    'access_time' => $log->access_time,
                    'type' => $log->type,
                    'notes' => $log->notes,
                    'student' => $log->student ? [
                        'nombres' => $log->student->nombres,
                        'apellidos' => $log->student->apellidos,
                        'codigo' => $log->student->codigo,
                    ] : null,
                    'registered_by_user' => $log->registeredBy ? [
                        'name' => $log->registeredBy->name,
                    ] : null,
                ];
            })
            ->toArray();
    }

    public function processQrScan($qrData)
    {
        // Extraer el código del estudiante del QR
        $code = $this->extractStudentCode($qrData);
        
        if ($code) {
            $this->findStudentByCode($code);
        } else {
            $this->dispatch('show-error', 'Código QR no válido');
            $this->playSound('error');
        }
    }

    public function extractStudentCode($qrData)
    {
        // El formato del QR es: "Código: XXXXX\nNombre: ..."
        if (preg_match('/Código:\s*([\w\d]+)/', $qrData, $matches)) {
            return $matches[1];
        }
        return null;
    }

    public function searchByManualCode()
    {
        if (empty($this->manualCode)) {
            $this->dispatch('show-error', 'Por favor ingrese un código');
            return;
        }
        
        $this->findStudentByCode($this->manualCode);
    }

    public function findStudentByCode($code)
    {
        $student = Student::with(['nivelEducativo', 'turno'])
            ->where('codigo', $code)
            ->where('status', 1)
            ->first();
            
        if (!$student) {
            $this->dispatch('show-error', 'Estudiante no encontrado o inactivo');
            $this->playSound('error');
            return;
        }
        
        $this->selectedStudent = $student;
        $this->determineAccessType($student);
        $this->showStudentInfo = true;
        $this->playSound('success');
    }

    public function determineAccessType($student)
    {
        $today = Carbon::today();
        
        // Obtener el último acceso del día
        $lastAccess = StudentAccessLog::where('student_id', $student->id)
            ->whereDate('access_time', $today)
            ->orderBy('access_time', 'desc')
            ->first();
        
        // Si no hay accesos hoy o el último fue salida, sugerir entrada
        // Si el último fue entrada, sugerir salida
        if (!$lastAccess || $lastAccess->type === 'salida') {
            $this->accessType = 'entrada';
        } else {
            $this->accessType = 'salida';
        }
    }

    public function registerAccess()
    {
        if (!$this->selectedStudent) {
            $this->dispatch('show-error', 'No hay estudiante seleccionado');
            return;
        }
        
        $accessLog = StudentAccessLog::create([
            'student_id' => $this->selectedStudent->id,
            'type' => $this->accessType,
            'access_time' => now(),
            'registered_by' => Auth::id(),
            'notes' => $this->notes
        ]);
        
        $this->sendAccessNotification($accessLog);
        
        // Crear notificación
        Notification::create([
            'user_id' => auth()->id(),
            'type' => $this->accessType === 'entrada' ? 'info' : 'warning',
            'title' => ucfirst($this->accessType) . ' registrada',
            'message' => "{$this->selectedStudent->nombres} {$this->selectedStudent->apellidos} - " . ucfirst($this->accessType) . " registrada a las " . now()->format('H:i'),
            'data' => ['student_id' => $this->selectedStudent->id, 'access_log_id' => $accessLog->id]
        ]);
        
        $this->dispatch('notification-created');
        $this->dispatch('show-success', $this->accessType . ' registrada correctamente');
        $this->playSound('notification');
        $this->resetForm();
        $this->loadStats();
        $this->loadTodayLogs();
    }

    private function sendAccessNotification($accessLog)
    {
        if (!$this->selectedStudent->representante_correo) {
            return;
        }

        try {
            $student = Student::with(['nivelEducativo', 'turno'])->find($this->selectedStudent->id);
            $timeInSchool = null;
            
            if ($accessLog->type === 'salida') {
                $entryLog = StudentAccessLog::where('student_id', $student->id)
                    ->whereDate('access_time', Carbon::today())
                    ->where('type', 'entrada')
                    ->orderBy('access_time', 'desc')
                    ->first();
                    
                if ($entryLog) {
                    $entryTime = Carbon::parse($entryLog->access_time);
                    $exitTime = Carbon::parse($accessLog->access_time);
                    $diff = $entryTime->diff($exitTime);
                    
                    $hours = $diff->h;
                    $minutes = $diff->i;
                    
                    if ($hours > 0) {
                        $timeInSchool = "{$hours} hora" . ($hours != 1 ? 's' : '') . " y {$minutes} minuto" . ($minutes != 1 ? 's' : '');
                    } else {
                        $timeInSchool = "{$minutes} minuto" . ($minutes != 1 ? 's' : '');
                    }
                }
            }

            \Mail::to($student->representante_correo)
                ->send(new \App\Mail\StudentAccessNotificationMail($student, $accessLog, $timeInSchool));
                
        } catch (\Exception $e) {
            \Log::error('Error enviando notificación: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->selectedStudent = null;
        $this->accessType = 'entrada';
        $this->notes = '';
        $this->manualCode = '';
        $this->showStudentInfo = false;
    }

    public function toggleSound()
    {
        $this->soundEnabled = !$this->soundEnabled;
    }

    public function playSound($type)
    {
        if (!$this->soundEnabled) return;
        
        $this->dispatch('play-sound', $type);
    }

    public function deleteLog($logId)
    {
        // Solo administradores pueden eliminar registros
        if (!Auth::user()->hasRole('Admin')) {
            $this->dispatch('show-error', 'No tienes permiso para eliminar registros');
            return;
        }
        
        $log = StudentAccessLog::find($logId);
        if ($log) {
            $log->delete();
            $this->dispatch('show-success', 'Registro eliminado correctamente');
            $this->loadStats();
            $this->loadTodayLogs();
        }
    }

    public function render()
    {
        return view('livewire.admin.students.qr-access')
            ->layout('components.layouts.admin', [
                'title' => 'Control de Acceso con QR'
            ]);
    }
}
