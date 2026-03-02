<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Participante extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'extension_id',
        'actividad_id',
        'nombres',
        'apellidos',
        'cedula',
        'telefono_principal',
        'telefono_alternativo',
        'direccion',
        'zona',
        'distrito',
        'fecha_nacimiento',
        'edad',
        'genero',
        'estado_civil',
        'tipo_miembro'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'edad' => 'integer',
    ];

    protected static function booted()
    {
        static::created(function ($participante) {
            if ($participante->actividad_id) {
                $participante->actividad->actualizarCuposOcupados();
            }
        });

        static::deleted(function ($participante) {
            if ($participante->actividad_id) {
                $participante->actividad->actualizarCuposOcupados();
            }
        });
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function extension()
    {
        return $this->belongsTo(Extension::class);
    }

    public function actividad()
    {
        return $this->belongsTo(Actividad::class);
    }

    public function scopeForUser($query)
    {
        if (auth()->check() && !auth()->user()->hasRole('Super Administrador')) {
            if (auth()->user()->empresa_id) {
                $query->where('empresa_id', auth()->user()->empresa_id);
            }
            if (auth()->user()->sucursal_id) {
                $query->where('sucursal_id', auth()->user()->sucursal_id);
            }
        }
        return $query;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nombres', 'apellidos', 'cedula', 'actividad_id', 'genero', 'estado_civil', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
