<?php

namespace App\Jobs;

use App\Models\Student;
use App\Models\StudentAccessLog;
use App\Mail\StudentAccessNotificationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendAccessNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $student;
    public $accessLog;

    public function __construct(Student $student, StudentAccessLog $accessLog)
    {
        $this->student = $student;
        $this->accessLog = $accessLog;
    }

    public function handle(): void
    {
        if (!$this->student->representante_correo) {
            return;
        }

        $timeInSchool = null;
        
        if ($this->accessLog->type === 'salida') {
            $entryLog = StudentAccessLog::where('student_id', $this->student->id)
                ->whereDate('access_time', Carbon::today())
                ->where('type', 'entrada')
                ->orderBy('access_time', 'desc')
                ->first();
                
            if ($entryLog) {
                $entryTime = Carbon::parse($entryLog->access_time);
                $exitTime = Carbon::parse($this->accessLog->access_time);
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

        Mail::to($this->student->representante_correo)
            ->send(new StudentAccessNotificationMail($this->student, $this->accessLog, $timeInSchool));
    }
}
