<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Student;
use App\Models\PaymentSchedule;

class EnrollmentDueNotification extends Notification
{
    use Queueable;

    protected $student;
    protected $schedule;

    /**
     * Create a new notification instance.
     */
    public function __construct(Student $student, PaymentSchedule $schedule)
    {
        $this->student = $student;
        $this->schedule = $schedule;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $cuotaDescription = $this->schedule->numero_cuota == 0 ? 'cuota inicial' : "cuota #{$this->schedule->numero_cuota}";
        
        return [
            'message' => "La {$cuotaDescription} del estudiante {$this->student->nombres} {$this->student->apellidos} vence en 3 días",
            'student_id' => $this->student->id,
            'student_name' => $this->student->nombres . ' ' . $this->student->apellidos,
            'schedule_id' => $this->schedule->id,
            'due_date' => $this->schedule->fecha_vencimiento,
            'amount' => $this->schedule->monto,
            'type' => 'enrollment_due',
        ];
    }
}