<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\StudentAccessLog;

class StudentAccessNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $log;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(StudentAccessLog $log)
    {
        $this->log = $log;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Notificación de ' . ($this->log->type === 'entry' ? 'Entrada' : 'Salida') . ' de Estudiante')
                    ->view('emails.students.access-notification');
    }
}