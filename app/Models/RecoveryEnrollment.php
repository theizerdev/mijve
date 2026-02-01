<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Multitenantable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecoveryEnrollment extends Model
{
    use HasFactory, Multitenantable, LogsActivity, SoftDeletes;

    protected $fillable = [
        'recovery_period_id',
        'student_id',
        'academic_record_id',
        'empresa_id',
        'sucursal_id',
        'enrollment_status',
        'enrollment_date',
        'approval_date',
        'rejection_reason',
        'original_subject_id',
        'recovery_subject_id',
        'teacher_id',
        'grade',
        'section',
        'original_grade',
        'recovery_grade',
        'final_grade',
        'result',
        'attended_recovery',
        'completed_recovery',
        'recovery_completion_date',
        'recovery_cost',
        'payment_status',
        'payment_date',
        'payment_reference',
        'documentation_complete',
        'documentation_notes',
        'schedule',
        'classroom',
        'total_recovery_classes',
        'attended_recovery_classes',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'approval_date' => 'date',
        'recovery_completion_date' => 'date',
        'payment_date' => 'date',
        'original_grade' => 'decimal:2',
        'recovery_grade' => 'decimal:2',
        'final_grade' => 'decimal:2',
        'recovery_cost' => 'decimal:2',
        'attended_recovery' => 'boolean',
        'completed_recovery' => 'boolean',
        'documentation_complete' => 'boolean',
        'total_recovery_classes' => 'integer',
        'attended_recovery_classes' => 'integer',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_COMPLETED = 'completed';
    const STATUS_WITHDRAWN = 'withdrawn';

    const RESULT_APPROVED = 'approved';
    const RESULT_FAILED = 'failed';
    const RESULT_WITHDRAWN = 'withdrawn';

    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_WAIVED = 'waived';

    public static function getEnrollmentStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pendiente',
            self::STATUS_APPROVED => 'Aprobado',
            self::STATUS_REJECTED => 'Rechazado',
            self::STATUS_COMPLETED => 'Completado',
            self::STATUS_WITHDRAWN => 'Retirado',
        ];
    }

    public static function getResults(): array
    {
        return [
            self::RESULT_APPROVED => 'Aprobado',
            self::RESULT_FAILED => 'Reprobado',
            self::RESULT_WITHDRAWN => 'Retirado',
        ];
    }

    public static function getPaymentStatuses(): array
    {
        return [
            self::PAYMENT_PENDING => 'Pendiente',
            self::PAYMENT_PAID => 'Pagado',
            self::PAYMENT_WAIVED => 'Exonerado',
        ];
    }

    public function recoveryPeriod(): BelongsTo
    {
        return $this->belongsTo(RecoveryPeriod::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicRecord(): BelongsTo
    {
        return $this->belongsTo(AcademicRecord::class);
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function originalSubject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'original_subject_id');
    }

    public function recoverySubject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'recovery_subject_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function scopeByRecoveryPeriod($query, $periodId)
    {
        return $query->where('recovery_period_id', $periodId);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('enrollment_status', $status);
    }

    public function scopeApproved($query)
    {
        return $query->where('enrollment_status', self::STATUS_APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('enrollment_status', self::STATUS_PENDING);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', self::PAYMENT_PAID);
    }

    public function getEnrollmentStatusLabelAttribute(): string
    {
        return self::getEnrollmentStatuses()[$this->enrollment_status] ?? $this->enrollment_status;
    }

    public function getResultLabelAttribute(): ?string
    {
        return $this->result ? (self::getResults()[$this->result] ?? $this->result) : null;
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return self::getPaymentStatuses()[$this->payment_status] ?? $this->payment_status;
    }

    public function getAttendancePercentageAttribute(): ?float
    {
        if ($this->total_recovery_classes > 0) {
            return round(($this->attended_recovery_classes / $this->total_recovery_classes) * 100, 2);
        }
        return null;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'enrollment_status',
                'approval_date',
                'rejection_reason',
                'recovery_grade',
                'final_grade',
                'result',
                'attended_recovery',
                'completed_recovery',
                'recovery_completion_date',
                'payment_status',
                'payment_date',
                'documentation_complete',
                'attended_recovery_classes',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}