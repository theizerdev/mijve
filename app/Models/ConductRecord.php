<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Multitenantable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ConductRecord extends Model
{
    use HasFactory, SoftDeletes, Multitenantable, LogsActivity;

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'student_id',
        'school_period_id',
        'section_id',
        'date',
        'type',
        'severity',
        'category',
        'description',
        'actions_taken',
        'parent_notified',
        'parent_notification_date',
        'follow_up_notes',
        'follow_up_date',
        'resolved',
        'resolution_date',
        'resolution_notes',
        'registered_by',
        'resolved_by',
    ];

    protected $casts = [
        'date' => 'date',
        'parent_notification_date' => 'date',
        'follow_up_date' => 'date',
        'resolution_date' => 'date',
        'resolved' => 'boolean',
    ];

    const TYPE_POSITIVE = 'positive';
    const TYPE_NEGATIVE = 'negative';
    const TYPE_NEUTRAL = 'neutral';
    const TYPE_WARNING = 'warning';
    const TYPE_SANCTION = 'sanction';

    const SEVERITY_LOW = 'low';
    const SEVERITY_MEDIUM = 'medium';
    const SEVERITY_HIGH = 'high';
    const SEVERITY_CRITICAL = 'critical';

    public static function getTypes(): array
    {
        return [
            self::TYPE_POSITIVE => 'Positivo',
            self::TYPE_NEGATIVE => 'Negativo',
            self::TYPE_NEUTRAL => 'Neutral',
            self::TYPE_WARNING => 'Amonestación',
            self::TYPE_SANCTION => 'Sanción',
        ];
    }

    public static function getTypeColors(): array
    {
        return [
            self::TYPE_POSITIVE => 'success',
            self::TYPE_NEGATIVE => 'danger',
            self::TYPE_NEUTRAL => 'secondary',
            self::TYPE_WARNING => 'warning',
            self::TYPE_SANCTION => 'dark',
        ];
    }

    public static function getSeverities(): array
    {
        return [
            self::SEVERITY_LOW => 'Leve',
            self::SEVERITY_MEDIUM => 'Moderado',
            self::SEVERITY_HIGH => 'Grave',
            self::SEVERITY_CRITICAL => 'Muy Grave',
        ];
    }

    public static function getSeverityColors(): array
    {
        return [
            self::SEVERITY_LOW => 'info',
            self::SEVERITY_MEDIUM => 'warning',
            self::SEVERITY_HIGH => 'danger',
            self::SEVERITY_CRITICAL => 'dark',
        ];
    }

    public static function getCategories(): array
    {
        return [
            'puntualidad' => 'Puntualidad',
            'respeto' => 'Respeto',
            'uniforme' => 'Uniforme',
            'disciplina' => 'Disciplina',
            'rendimiento' => 'Rendimiento Académico',
            'participacion' => 'Participación',
            'colaboracion' => 'Colaboración',
            'convivencia' => 'Convivencia',
            'responsabilidad' => 'Responsabilidad',
            'otro' => 'Otro',
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        return self::getTypes()[$this->type] ?? $this->type;
    }

    public function getTypeColorAttribute(): string
    {
        return self::getTypeColors()[$this->type] ?? 'secondary';
    }

    public function getSeverityLabelAttribute(): string
    {
        return self::getSeverities()[$this->severity] ?? $this->severity;
    }

    public function getSeverityColorAttribute(): string
    {
        return self::getSeverityColors()[$this->severity] ?? 'secondary';
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::getCategories()[$this->category] ?? $this->category;
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolPeriod(): BelongsTo
    {
        return $this->belongsTo(SchoolPeriod::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function registeredByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function resolvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByPeriod($query, $periodId)
    {
        return $query->where('school_period_id', $periodId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopePositive($query)
    {
        return $query->where('type', self::TYPE_POSITIVE);
    }

    public function scopeNegative($query)
    {
        return $query->whereIn('type', [self::TYPE_NEGATIVE, self::TYPE_WARNING, self::TYPE_SANCTION]);
    }

    public function scopeUnresolved($query)
    {
        return $query->where('resolved', false);
    }

    public function scopeResolved($query)
    {
        return $query->where('resolved', true);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['type', 'severity', 'description', 'resolved', 'resolution_notes'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
