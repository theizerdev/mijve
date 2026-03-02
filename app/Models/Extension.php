<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Extension extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'extensiones';

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'user_id',
        'nombre',
        'zona',
        'distrito',
        'status',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function lider()
    {
        return $this->belongsTo(User::class, 'user_id');
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
            ->logOnly(['nombre', 'zona', 'distrito', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
