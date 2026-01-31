<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Multitenantable;

class SectionStudent extends Model
{
    use HasFactory, Multitenantable;

    protected $table = 'section_student';

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'section_id',
        'student_id',
        'fecha_inscripcion',
        'estado',
        'observaciones',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'fecha_inscripcion' => 'date',
    ];

    const ESTADO_ACTIVO = 'activo';
    const ESTADO_INACTIVO = 'inactivo';
    const ESTADO_RETIRADO = 'retirado';
    const ESTADO_SUSPENDIDO = 'suspendido';

    public static function getEstados(): array
    {
        return [
            self::ESTADO_ACTIVO => 'Activo',
            self::ESTADO_INACTIVO => 'Inactivo',
            self::ESTADO_RETIRADO => 'Retirado',
            self::ESTADO_SUSPENDIDO => 'Suspendido',
        ];
    }

    public function getEstadoLabelAttribute(): string
    {
        return self::getEstados()[$this->estado] ?? 'Desconocido';
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
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
        return $query->where('estado', self::ESTADO_ACTIVO);
    }

    public function scopeBySection($query, $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }
}