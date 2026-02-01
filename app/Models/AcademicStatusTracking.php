<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Multitenantable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicStatusTracking extends Model
{
    use HasFactory, Multitenantable, LogsActivity, SoftDeletes;

    protected $table = 'academic_status_tracking';

    protected $fillable = [
        'student_id',
        'matricula_id',
        'school_period_id',
        'program_id',
        'educational_level_id',
        'empresa_id',
        'sucursal_id',
        'academic_status',
        'period_average',
        'total_subjects',
        'approved_subjects',
        'failed_subjects',
        'in_recovery_subjects',
        'performance_level',
        'attendance_percentage',
        'conduct_grade',
        'promoted',
        'repeated',
        'graduated',
        'withdrawn',
        'enrollment_date',
        'completion_date',
        'withdrawal_date',
        'academic_observations',
        'disciplinary_observations',
        'recommendations',
        'status',
        'reviewed_by',
        'review_date',
    ];

    protected $casts = [
        'period_average' => 'decimal:2',
        'attendance_percentage' => 'decimal:2',
        'promoted' => 'boolean',
        'repeated' => 'boolean',
        'graduated' => 'boolean',
        'withdrawn' => 'boolean',
        'enrollment_date' => 'date',
        'completion_date' => 'date',
        'withdrawal_date' => 'date',
        'review_date' => 'date',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_PROBATION = 'probation';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_COMPLETED = 'completed';
    const STATUS_WITHDRAWN = 'withdrawn';

    const PERFORMANCE_EXCELLENT = 'excellent';
    const PERFORMANCE_GOOD = 'good';
    const PERFORMANCE_AVERAGE = 'average';
    const PERFORMANCE_POOR = 'poor';

    const TRACKING_ACTIVE = 'active';
    const TRACKING_CLOSED = 'closed';
    const TRACKING_CANCELLED = 'cancelled';

    public static function getAcademicStatuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Activo',
            self::STATUS_PROBATION => 'En Prueba',
            self::STATUS_SUSPENDED => 'Suspendido',
            self::STATUS_COMPLETED => 'Completado',
            self::STATUS_WITHDRAWN => 'Retirado',
        ];
    }

    public static function getPerformanceLevels(): array
    {
        return [
            self::PERFORMANCE_EXCELLENT => 'Excelente',
            self::PERFORMANCE_GOOD => 'Bueno',
            self::PERFORMANCE_AVERAGE => 'Promedio',
            self::PERFORMANCE_POOR => 'Deficiente',
        ];
    }

    public static function getTrackingStatuses(): array
    {
        return [
            self::TRACKING_ACTIVE => 'Activo',
            self::TRACKING_CLOSED => 'Cerrado',
            self::TRACKING_CANCELLED => 'Cancelado',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function matricula(): BelongsTo
    {
        return $this->belongsTo(Matricula::class);
    }

    public function schoolPeriod(): BelongsTo
    {
        return $this->belongsTo(SchoolPeriod::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Programa::class, 'program_id');
    }

    public function educationalLevel(): BelongsTo
    {
        return $this->belongsTo(EducationalLevel::class);
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByMatricula($query, $matriculaId)
    {
        return $query->where('matricula_id', $matriculaId);
    }

    public function scopeByPeriod($query, $periodId)
    {
        return $query->where('school_period_id', $periodId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('academic_status', $status);
    }

    public function scopeActive($query)
    {
        return $query->where('academic_status', self::STATUS_ACTIVE);
    }

    public function scopeCompleted($query)
    {
        return $query->where('academic_status', self::STATUS_COMPLETED);
    }

    public function scopePromoted($query)
    {
        return $query->where('promoted', true);
    }

    public function scopeRepeated($query)
    {
        return $query->where('repeated', true);
    }

    public function scopeGraduated($query)
    {
        return $query->where('graduated', true);
    }

    public function scopeByPerformanceLevel($query, $level)
    {
        return $query->where('performance_level', $level);
    }

    public function scopeTrackingActive($query)
    {
        return $query->where('status', self::TRACKING_ACTIVE);
    }

    public function getAcademicStatusLabelAttribute(): string
    {
        return self::getAcademicStatuses()[$this->academic_status] ?? $this->academic_status;
    }

    public function getPerformanceLevelLabelAttribute(): ?string
    {
        return $this->performance_level ? (self::getPerformanceLevels()[$this->performance_level] ?? $this->performance_level) : null;
    }

    public function getTrackingStatusLabelAttribute(): string
    {
        return self::getTrackingStatuses()[$this->status] ?? $this->status;
    }

    public function getApprovalRateAttribute(): ?float
    {
        if ($this->total_subjects > 0) {
            return round(($this->approved_subjects / $this->total_subjects) * 100, 2);
        }
        return null;
    }

    public function getRecoveryRateAttribute(): ?float
    {
        if ($this->total_subjects > 0) {
            return round(($this->in_recovery_subjects / $this->total_subjects) * 100, 2);
        }
        return null;
    }

    public function getAcademicSummaryAttribute(): array
    {
        return [
            'academic_status' => $this->academic_status,
            'period_average' => $this->period_average,
            'total_subjects' => $this->total_subjects,
            'approved_subjects' => $this->approved_subjects,
            'failed_subjects' => $this->failed_subjects,
            'in_recovery_subjects' => $this->in_recovery_subjects,
            'approval_rate' => $this->approval_rate,
            'recovery_rate' => $this->recovery_rate,
            'performance_level' => $this->performance_level,
            'attendance_percentage' => $this->attendance_percentage,
            'conduct_grade' => $this->conduct_grade,
            'promoted' => $this->promoted,
            'repeated' => $this->repeated,
            'graduated' => $this->graduated,
            'withdrawn' => $this->withdrawn,
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'academic_status',
                'period_average',
                'total_subjects',
                'approved_subjects',
                'failed_subjects',
                'in_recovery_subjects',
                'performance_level',
                'attendance_percentage',
                'conduct_grade',
                'promoted',
                'repeated',
                'graduated',
                'withdrawn',
                'academic_observations',
                'disciplinary_observations',
                'recommendations',
                'status',
                'review_date'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}