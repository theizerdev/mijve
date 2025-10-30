<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Student;
use App\Models\PaymentSchedule;

class EnrollmentDueMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $schedule;

    /**
     * Create a new message instance.
     */
    public function __construct(Student $student, PaymentSchedule $schedule)
    {
        $this->student = $student;
        $this->schedule = $schedule;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $cuotaDescription = $this->schedule->numero_cuota == 0 ? 'cuota inicial' : "cuota #{$this->schedule->numero_cuota}";
        
        return new Envelope(
            subject: "Recordatorio de pago - {$cuotaDescription} - {$this->student->nombres} {$this->student->apellidos}"
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.enrollment-due',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}