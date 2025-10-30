<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Student;

class BirthdayNotification extends Notification
{
    use Queueable;

    protected $student;

    /**
     * Create a new notification instance.
     */
    public function __construct(Student $student)
    {
        $this->student = $student;
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
        return [
            'message' => "Hoy es el cumpleaños de {$this->student->nombres} {$this->student->apellidos}",
            'student_id' => $this->student->id,
            'student_name' => $this->student->nombres . ' ' . $this->student->apellidos,
            'type' => 'birthday',
        ];
    }
}