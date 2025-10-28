<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DebtNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $debtDetails;
    public $pendingAmount;

    /**
     * Create a new message instance.
     */
    public function __construct($student, $debtDetails, $pendingAmount)
    {
        $this->student = $student;
        $this->debtDetails = $debtDetails;
        $this->pendingAmount = $pendingAmount;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notificación de Deuda Pendiente - ' . ($this->student->nombres ?? '') . ' ' . ($this->student->apellidos ?? ''),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.debt-notification',
            with: [
                'student' => $this->student,
                'debtDetails' => $this->debtDetails,
                'pendingAmount' => $this->pendingAmount,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
