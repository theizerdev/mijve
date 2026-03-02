<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Actividad extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'actividads';

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'descripcion',
        'direccion',
        'latitud',
        'longitud',
        'costo',
        'capacidad',
        'cupos_ocupados',
        'status',
        'edad_desde',
        'edad_hasta',
    ];

    protected static function booted()
    {
        // Actualizar cupos ocupados al crear un participante
        static::created(function ($actividad) {
            $actividad->actualizarCuposOcupados();
        });
    }

    public function actualizarCuposOcupados()
    {
        $this->cupos_ocupados = Participante::where('actividad_id', $this->id)->count();
        $this->saveQuietly();
    }

    public function participantes()
    {
        return $this->hasMany(Participante::class);
    }

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'edad_desde' => 'integer',
        'edad_hasta' => 'integer',
        'capacidad' => 'integer',
        'cupos_ocupados' => 'integer',
        'costo' => 'decimal:2',
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    /**
     * Verifica si hay cupos disponibles
     */
    public function tieneCuposDisponibles(): bool
    {
        return $this->cupos_ocupados < $this->capacidad;
    }

    /**
     * Obtiene el porcentaje de ocupación
     */
    public function getPorcentajeOcupacionAttribute()
    {
        if ($this->capacidad <= 0) return 0;
        return round(($this->cupos_ocupados / $this->capacidad) * 100, 1);
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
            ->logOnly(['nombre', 'fecha_inicio', 'fecha_fin', 'status', 'edad_desde', 'edad_hasta', 'costo', 'direccion', 'latitud', 'longitud'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
