<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectPrerequisite extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'prerequisite_subject_id',
        'type',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relación con la materia principal
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Relación con la materia prerrequisito
     */
    public function prerequisiteSubject()
    {
        return $this->belongsTo(Subject::class, 'prerequisite_subject_id');
    }

    /**
     * Scope para obtener solo prerrequisitos obligatorios
     */
    public function scopeMandatory($query)
    {
        return $query->where('type', 'mandatory');
    }

    /**
     * Scope para obtener solo prerrequisitos recomendados
     */
    public function scopeRecommended($query)
    {
        return $query->where('type', 'recommended');
    }

    /**
     * Scope para obtener prerrequisitos activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}