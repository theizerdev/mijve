<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Multitenantable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certificate extends Model
{
    use HasFactory, Multitenantable, LogsActivity, SoftDeletes;

    protected $fillable = [
        'student_id',
        'matricula_id',
        'school_period_id',
        'empresa_id',
        'sucursal_id',
        'certificate_type',
        'certificate_number',
        'issue_date',
        'expiration_date',
        'status',
        'content',
        'academic_data',
        'overall_average',
        'total_subjects',
        'approved_subjects',
        'conduct_grade',
        'attendance_percentage',
        'completed',
        'verification_code',
        'is_digital',
        'digital_signature',
        'issued_by',
        'issued_by_user_id',
        'observations',
        'revocation_reason',
        'revocation_date',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiration_date' => 'date',
        'academic_data' => 'array',
        'overall_average' => 'decimal:2',
        'attendance_percentage' => 'decimal:2',
        'completed' => 'boolean',
        'is_digital' => 'boolean',
        'revocation_date' => 'date',
    ];

    const TYPE_ACADEMIC = 'academic';
    const TYPE_ATTENDANCE = 'attendance';
    const TYPE_CONDUCT = 'conduct';
    const TYPE_COMPLETION = 'completion';
    const TYPE_ENROLLMENT = 'enrollment';

    const STATUS_ACTIVE = 'active';
    const STATUS_REVOKED = 'revoked';
    const STATUS_EXPIRED = 'expired';

    public static function getTypes(): array
    {
        return [
            self::TYPE_ACADEMIC => 'Certificado Académico',
            self::TYPE_ATTENDANCE => 'Constancia de Asistencia',
            self::TYPE_CONDUCT => 'Certificado de Conducta',
            self::TYPE_COMPLETION => 'Certificado de Finalización',
            self::TYPE_ENROLLMENT => 'Constancia de Matrícula',
        ];
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Activo',
            self::STATUS_REVOKED => 'Revocado',
            self::STATUS_EXPIRED => 'Expirado',
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

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by_user_id');
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

    public function scopeByType($query, $type)
    {
        return $query->where('certificate_type', $type);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeByCertificateNumber($query, $number)
    {
        return $query->where('certificate_number', $number);
    }

    public function scopeByVerificationCode($query, $code)
    {
        return $query->where('verification_code', $code);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::getTypes()[$this->certificate_type] ?? $this->certificate_type;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    public function getIsValidAttribute(): bool
    {
        return $this->status === self::STATUS_ACTIVE && 
               ($this->expiration_date === null || $this->expiration_date >= now());
    }

    public function getAcademicSummaryAttribute(): array
    {
        return [
            'overall_average' => $this->overall_average,
            'total_subjects' => $this->total_subjects,
            'approved_subjects' => $this->approved_subjects,
            'attendance_percentage' => $this->attendance_percentage,
            'conduct_grade' => $this->conduct_grade,
        ];
    }

    public function generateCertificateNumber(): string
    {
        $prefix = strtoupper(substr($this->certificate_type, 0, 3));
        $year = date('Y');
        $sequence = str_pad($this->id ?? 0, 6, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$year}-{$sequence}";
    }

    public function generateVerificationCode(): string
    {
        return strtoupper(bin2hex(random_bytes(16)));
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'certificate_type',
                'certificate_number',
                'status',
                'overall_average',
                'total_subjects',
                'approved_subjects',
                'conduct_grade',
                'attendance_percentage',
                'completed',
                'observations',
                'revocation_reason',
                'revocation_date'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}