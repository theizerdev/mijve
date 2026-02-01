<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Multitenantable;

class StudyPlanSubject extends Model
{
    use HasFactory, Multitenantable;

    protected $table = 'study_plan_subjects';

    protected $fillable = [
        'study_plan_id',
        'subject_id',
        'semester',
        'year',
        'subject_type',
        'order',
        'is_active',
    ];

    protected $casts = [
        'semester' => 'integer',
        'year' => 'integer',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relación con el plan de estudio
     */
    public function studyPlan(): BelongsTo
    {
        return $this->belongsTo(StudyPlan::class, 'study_plan_id');
    }

    /**
     * Relación con la materia
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Scope para obtener asignaciones activas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para obtener materias por semestre
     */
    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    /**
     * Scope para obtener materias por año
     */
    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope para obtener materias obligatorias
     */
    public function scopeMandatory($query)
    {
        return $query->where('subject_type', 'mandatory');
    }

    /**
     * Scope para obtener materias electivas
     */
    public function scopeElective($query)
    {
        return $query->where('subject_type', 'elective');
    }
}