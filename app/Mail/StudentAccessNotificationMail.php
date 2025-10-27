<?php

namespace App\Mail;

use App\Models\Student;
use App\Models\StudentAccessLog;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class StudentAccessNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $accessLog;
    public $timeInSchool;

    public function __construct(Student $student, StudentAccessLog $accessLog, $timeInSchool = null)
    {
        $this->student = $student;
        $this->accessLog = $accessLog;
        $this->timeInSchool = $timeInSchool;
    }

    public function envelope(): Envelope
    {
        $subject = $this->accessLog->type === 'entrada' 
            ? "Entrada registrada - {$this->student->nombres} {$this->student->apellidos}"
            : "Salida registrada - {$this->student->nombres} {$this->student->apellidos}";

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.student-access-notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
