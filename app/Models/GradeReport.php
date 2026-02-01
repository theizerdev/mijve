<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Multitenantable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class GradeReport extends Model
{
    use HasFactory, SoftDeletes, Multitenantable, LogsActivity;

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'school_period_id',
        'section_id',
        'subject_id',
        'evaluation_period_id',
        'report_number',
        'report_type',
        'title',
        'description',
        'grades_data',
        'statistics',
        'total_students',
        'approved_count',
        'failed_count',
        'average_grade',
        'highest_grade',
        'lowest_grade',
        'status',
        'generated_at',
        'approved_at',
        'published_at',
        'generated_by',
        'approved_by',
        'observations',
        'file_path',
    ];

    protected $casts = [
        'grades_data' => 'array',
        'statistics' => 'array',
        'average_grade' => 'decimal:2',
        'highest_grade' => 'decimal:2',
        'lowest_grade' => 'decimal:2',
        'generated_at' => 'datetime',
        'approved_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    const TYPE_PERIOD = 'period';
    const TYPE_FINAL = 'final';
    const TYPE_RECOVERY = 'recovery';

    const STATUS_DRAFT = 'draft';
    const STATUS_GENERATED = 'generated';
    const STATUS_APPROVED = 'approved';
    const STATUS_PUBLISHED = 'published';

    public static function getTypes(): array
    {
        return [
            self::TYPE_PERIOD => 'Acta de Lapso',
            self::TYPE_FINAL => 'Acta Final',
            self::TYPE_RECOVERY => 'Acta de Recuperación',
        ];
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Borrador',
            self::STATUS_GENERATED => 'Generada',
            self::STATUS_APPROVED => 'Aprobada',
            self::STATUS_PUBLISHED => 'Publicada',
        ];
    }

    public static function getStatusColors(): array
    {
        return [
            self::STATUS_DRAFT => 'secondary',
            self::STATUS_GENERATED => 'info',
            self::STATUS_APPROVED => 'success',
            self::STATUS_PUBLISHED => 'primary',
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        return self::getTypes()[$this->report_type] ?? $this->report_type;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::getStatusColors()[$this->status] ?? 'secondary';
    }

    public function getApprovalRateAttribute(): float
    {
        if ($this->total_students === 0) return 0;
        return round(($this->approved_count / $this->total_students) * 100, 1);
    }

    public function schoolPeriod(): BelongsTo
    {
        return $this->belongsTo(SchoolPeriod::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function evaluationPeriod(): BelongsTo
    {
        return $this->belongsTo(EvaluationPeriod::class);
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function generatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeByPeriod($query, $periodId)
    {
        return $query->where('school_period_id', $periodId);
    }

    public function scopeBySection($query, $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('report_type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public static function generateReportNumber(): string
    {
        $year = date('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;
        return sprintf('ACTA-%s-%06d', $year, $count);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'title', 'observations', 'approved_at', 'published_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
