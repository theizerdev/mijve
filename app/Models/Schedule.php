<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Multitenantable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Schedule extends Model
{
    use HasFactory, Multitenantable, LogsActivity;

    protected $table = 'schedules';

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'section_id',
        'subject_id',
        'teacher_id',
        'classroom_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'fecha_inicio',
        'fecha_fin',
        'observaciones',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function scopeBySection($query, $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeByClassroom($query, $classroomId)
    {
        return $query->where('classroom_id', $classroomId);
    }

    public function scopeByDay($query, $day)
    {
        return $query->where('dia_semana', $day);
    }

    public function scopeActive($query)
    {
        return $query->whereDate('fecha_inicio', '<=', now())
                    ->whereDate('fecha_fin', '>=', now());
    }

    public function getDurationAttribute(): string
    {
        $start = \Carbon\Carbon::parse($this->hora_inicio);
        $end = \Carbon\Carbon::parse($this->hora_fin);
        return $start->diff($end)->format('%H:%I');
    }

    public function conflictsWith($day, $startTime, $endTime, $excludeId = null): bool
    {
        $query = Schedule::where('dia_semana', $day)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where(function ($q) use ($startTime, $endTime) {
                    $q->where('hora_inicio', '<', $endTime)
                      ->where('hora_fin', '>', $startTime);
                });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        // Verificar conflictos por aula
        $classroomConflict = (clone $query)->where('classroom_id', $this->classroom_id)->exists();
        
        // Verificar conflictos por profesor
        $teacherConflict = (clone $query)->where('teacher_id', $this->teacher_id)->exists();

        return $classroomConflict || $teacherConflict;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'section_id',
                'subject_id',
                'teacher_id',
                'classroom_id',
                'dia_semana',
                'hora_inicio',
                'hora_fin',
                'fecha_inicio',
                'fecha_fin',
                'observaciones'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}