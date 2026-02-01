<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Multitenantable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Attendance extends Model
{
    use HasFactory, SoftDeletes, Multitenantable, LogsActivity;

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'section_id',
        'student_id',
        'school_period_id',
        'date',
        'status',
        'arrival_time',
        'departure_time',
        'observations',
        'excuse_document',
        'registered_by',
    ];

    protected $casts = [
        'date' => 'date',
        'arrival_time' => 'datetime:H:i',
        'departure_time' => 'datetime:H:i',
    ];

    const STATUS_PRESENT = 'present';
    const STATUS_ABSENT = 'absent';
    const STATUS_LATE = 'late';
    const STATUS_EXCUSED = 'excused';
    const STATUS_EARLY_LEAVE = 'early_leave';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PRESENT => 'Presente',
            self::STATUS_ABSENT => 'Ausente',
            self::STATUS_LATE => 'Tardanza',
            self::STATUS_EXCUSED => 'Justificado',
            self::STATUS_EARLY_LEAVE => 'Salida Temprana',
        ];
    }

    public static function getStatusColors(): array
    {
        return [
            self::STATUS_PRESENT => 'success',
            self::STATUS_ABSENT => 'danger',
            self::STATUS_LATE => 'warning',
            self::STATUS_EXCUSED => 'info',
            self::STATUS_EARLY_LEAVE => 'secondary',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::getStatusColors()[$this->status] ?? 'secondary';
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolPeriod(): BelongsTo
    {
        return $this->belongsTo(SchoolPeriod::class);
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function scopeBySection($query, $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeByPeriod($query, $periodId)
    {
        return $query->where('school_period_id', $periodId);
    }

    public function scopePresent($query)
    {
        return $query->where('status', self::STATUS_PRESENT);
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', self::STATUS_ABSENT);
    }

    public function scopeLate($query)
    {
        return $query->where('status', self::STATUS_LATE);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'observations', 'excuse_document'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Método para calcular porcentaje de asistencia de un estudiante
    public static function calculateAttendancePercentage($studentId, $sectionId, $startDate = null, $endDate = null): float
    {
        $query = self::where('student_id', $studentId)
            ->where('section_id', $sectionId);

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        $total = $query->count();
        if ($total === 0) return 0;

        $present = $query->clone()->whereIn('status', [self::STATUS_PRESENT, self::STATUS_LATE])->count();
        
        return round(($present / $total) * 100, 2);
    }
}
