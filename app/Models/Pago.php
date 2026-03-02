<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Pago extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'participante_id',
        'actividad_id',
        'metodo_pago_id',
        'monto_euro',
        'tasa_cambio',
        'monto_bolivares',
        'fecha_pago',
        'referencia_bancaria',
        'evidencia_pago',
        'status',
        'observaciones',
        'caja_id'
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'monto_euro' => 'decimal:2',
        'tasa_cambio' => 'decimal:4',
        'monto_bolivares' => 'decimal:2',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function participante()
    {
        return $this->belongsTo(Participante::class);
    }

    public function actividad()
    {
        return $this->belongsTo(Actividad::class);
    }

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class);
    }

    public function caja()
    {
        return $this->belongsTo(Caja::class);
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
            ->logOnly(['monto_euro', 'monto_bolivares', 'fecha_pago', 'status', 'referencia_bancaria'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
