<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\Multitenantable;

class StudyPlan extends Model
{
    use HasFactory, Multitenantable;

    protected $fillable = [
        'name',
        'code',
        'description',
        'program_id',
        'educational_level_id',
        'total_credits',
        'total_hours',
        'duration_years',
        'duration_semesters',
        'status',
        'effective_date',
        'expiration_date',
        'is_default',
        'created_by',
        'updated_by',
        'empresa_id',
        'sucursal_id',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'expiration_date' => 'date',
        'is_default' => 'boolean',
        'total_credits' => 'integer',
        'total_hours' => 'integer',
        'duration_years' => 'integer',
        'duration_semesters' => 'integer',
    ];

    /**
     * Relación con el programa
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Programa::class, 'program_id');
    }

    /**
     * Relación con el nivel educativo
     */
    public function nivelEducativo(): BelongsTo
    {
        return $this->belongsTo(NivelEducativo::class, 'educational_level_id');
    }

    /**
     * Alias para la relación educationalLevel
     */
    public function educationalLevel(): BelongsTo
    {
        return $this->nivelEducativo();
    }

    /**
     * Relación con las materias del plan de estudio
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'study_plan_subjects', 'study_plan_id', 'subject_id')
                    ->withPivot('semester', 'year', 'subject_type', 'order', 'is_active')
                    ->withTimestamps();
    }

    /**
     * Relación con el usuario creador
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relación con el usuario actualizador
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relación con la empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    /**
     * Relación con la sucursal
     */
    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    /**
     * Scope para obtener planes activos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope para obtener planes por programa
     */
    public function scopeByProgram($query, $programId)
    {
        return $query->where('program_id', $programId);
    }

    /**
     * Scope para obtener el plan por defecto
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Obtener materias por semestre
     */
    public function getSubjectsBySemester($semester)
    {
        return $this->subjects()
                    ->wherePivot('semester', $semester)
                    ->wherePivot('is_active', true)
                    ->orderBy('pivot_order')
                    ->get();
    }

    /**
     * Obtener todos los semestres con materias
     */
    public function getSemestersWithSubjects()
    {
        return $this->subjects()
                    ->select('pivot_semester', 'pivot_year')
                    ->wherePivot('is_active', true)
                    ->distinct()
                    ->orderBy('pivot_semester')
                    ->get()
                    ->groupBy(['pivot_year', 'pivot_semester']);
    }
}