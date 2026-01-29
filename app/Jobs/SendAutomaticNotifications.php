<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Student;
use App\Models\Matricula;
use App\Models\PaymentSchedule;
use App\Models\User;
use App\Notifications\BirthdayNotification;
use App\Notifications\EnrollmentDueNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

class SendAutomaticNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Enviar notificaciones de cumpleaños
        $this->sendBirthdayNotifications();
        
        // Enviar notificaciones de vencimiento de matrículas
        $this->sendEnrollmentDueNotifications();
    }
    
    /**
     * Enviar notificaciones de cumpleaños
     */
    private function sendBirthdayNotifications()
    {
        // Obtener estudiantes cuyo cumpleaños es hoy
        $students = Student::whereRaw('DATE_FORMAT(fecha_nacimiento, "%m-%d") = ?', [Carbon::now()->format('m-d')])
            ->where('status', 1)
            ->get();
            
        foreach ($students as $student) {
            // Obtener usuarios administradores de la misma empresa
            $admins = User::where('empresa_id', $student->empresa_id)
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'Super Admin')
                        ->orWhere('name', 'Admin');
                })
                ->get();
                
            if ($admins->isNotEmpty()) {
                // Enviar notificación a los administradores
                Notification::send($admins, new BirthdayNotification($student));
            }
        }
    }
    
    /**
     * Enviar notificaciones de vencimiento de matrículas
     */
    private function sendEnrollmentDueNotifications()
    {
        // Obtener pagos que vencen en 3 días
        $dueDate = Carbon::now()->addDays(3);
        
        $paymentSchedules = PaymentSchedule::whereDate('fecha_vencimiento', $dueDate)
            ->where('estado', '!=', 'pagado')
            ->with('matricula.student')
            ->get();
            
        foreach ($paymentSchedules as $schedule) {
            $student = $schedule->matricula->student;
            
            if ($student) {
                // Notificar al estudiante o representante
                if (!$student->esMenorDeEdad && $student->correo_electronico) {
                    // Enviar notificación al estudiante (mayor de edad)
                    \Mail::to($student->correo_electronico)
                        ->send(new \App\Mail\EnrollmentDueMail($student, $schedule));
                    
                    // Enviar notificación WhatsApp si el estudiante tiene teléfono
                    if (!empty($student->telefono)) {
                        // Aquí puedes agregar la lógica para enviar WhatsApp al estudiante
                        // Por ejemplo: $this->sendWhatsAppNotification($student->telefono, $student, $schedule);
                    }
                } elseif ($student->esMenorDeEdad && $student->representante_correo) {
                    // Enviar notificación al representante
                    \Mail::to($student->representante_correo)
                        ->send(new \App\Mail\EnrollmentDueMail($student, $schedule));
                    
                    // Enviar notificación WhatsApp al representante si tiene teléfono
                    if (!empty($student->representante_telefonos) && is_array($student->representante_telefonos)) {
                        foreach ($student->representante_telefonos as $telefono) {
                            if (!empty($telefono)) {
                                // Aquí puedes agregar la lógica para enviar WhatsApp al representante
                                // Por ejemplo: $this->sendWhatsAppNotification($telefono, $student, $schedule);
                            }
                        }
                    }
                }
                
                // Notificar a los administradores
                $admins = User::where('empresa_id', $student->empresa_id)
                    ->whereHas('roles', function ($query) {
                        $query->where('name', 'Super Admin')
                            ->orWhere('name', 'Admin');
                    })
                    ->get();
                    
                if ($admins->isNotEmpty()) {
                    Notification::send($admins, new EnrollmentDueNotification($student, $schedule));
                }
            }
        }
    }
}