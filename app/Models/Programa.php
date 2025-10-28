<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Multitenantable;

class Programa extends Model
{
    use HasFactory, Multitenantable;

    protected $fillable = [
        'nombre',
        'descripcion',
        'nivel_educativo_id',
        'costo_matricula',
        'costo_mensualidad',
        'activo',
        'empresa_id',
        'sucursal_id'
    ];

    protected $casts = [
        'costo_matricula' => 'decimal:2',
        'costo_mensualidad' => 'decimal:2',
        'activo' => 'boolean'
    ];

    public function nivelEducativo()
    {
        return $this->belongsTo(EducationalLevel::class, 'nivel_educativo_id');
    }

    public function matriculas()
    {
        return $this->hasMany(Matricula::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
}