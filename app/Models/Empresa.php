<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Empresa extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'razon_social',
        'documento',
        'direccion',
        'latitud',
        'longitud',
        'representante_legal',
        'status',
        'telefono',
        'email'
    ];

    protected $casts = [
        'status' => 'boolean',
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
    ];

    public function sucursales()
    {
        return $this->hasMany(Sucursal::class);
    }

    public function scopeForUser($query)
    {
        if (auth()->check() && !auth()->user()->hasRole('Super Administrador')) {
            if (auth()->user()->empresa_id) {
                $query->where('id', auth()->user()->empresa_id);
            }
        }
        return $query;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['razon_social', 'documento', 'direccion', 'representante_legal', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}