<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Multitenantable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Programa extends Model
{
    use HasFactory, Multitenantable, LogsActivity;

    protected $fillable = [
        'nombre',
        'descripcion',
        'nivel_educativo_id',
        'activo',
        'empresa_id',
        'sucursal_id',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function nivelEducativo()
    {
        return $this->belongsTo(EducationalLevel::class, 'nivel_educativo_id');
    }

    public function matriculas()
    {
        return $this->hasMany(Matricula::class, 'programa_id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'nombre',
                'descripcion',
                'nivel_educativo_id',
                'activo'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}