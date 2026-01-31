<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Multitenantable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Section extends Model
{
    use HasFactory, SoftDeletes, Multitenantable, LogsActivity;

    protected $table = 'sections';

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'nivel_educativo_id',
        'programa_id',
        'nombre',
        'codigo',
        'descripcion',
        'capacidad_maxima',
        'aula_asignada',
        'turno_id',
        'periodo_escolar_id',
        'profesor_guia_id',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacidad_maxima' => 'integer',
    ];

    public function nivelEducativo(): BelongsTo
    {
        return $this->belongsTo(EducationalLevel::class, 'nivel_educativo_id');
    }

    public function programa(): BelongsTo
    {
        return $this->belongsTo(Programa::class, 'programa_id');
    }

    public function turno(): BelongsTo
    {
        return $this->belongsTo(Turno::class, 'turno_id');
    }

    public function periodoEscolar(): BelongsTo
    {
        return $this->belongsTo(SchoolPeriod::class, 'periodo_escolar_id');
    }

    public function profesorGuia(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'profesor_guia_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'section_student', 'section_id', 'student_id')
                    ->withPivot('fecha_inscripcion', 'estado', 'observaciones')
                    ->withTimestamps();
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByPeriod($query, $periodId)
    {
        return $query->where('periodo_escolar_id', $periodId);
    }

    public function scopeByLevel($query, $levelId)
    {
        return $query->where('nivel_educativo_id', $levelId);
    }

    public function scopeByProgram($query, $programId)
    {
        return $query->where('programa_id', $programId);
    }

    public function getCurrentStudentCountAttribute(): int
    {
        return $this->students()->wherePivot('estado', 'activo')->count();
    }

    public function getAvailableSlotsAttribute(): int
    {
        return $this->capacidad_maxima - $this->current_student_count;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'nombre',
                'codigo',
                'descripcion',
                'capacidad_maxima',
                'aula_asignada',
                'is_active'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}