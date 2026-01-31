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
        'email',
        'pais_id',
        'api_key',
        'whatsapp_api_key',
        'whatsapp_rate_limit',
        'whatsapp_active'
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

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function pais()
    {
        return $this->belongsTo(Pais::class);
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

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($empresa) {
            if (!$empresa->api_key) {
                $empresa->api_key = self::generateApiKey();
            }
        });
    }

    public static function generateApiKey(): string
    {
        return 'vg_' . bin2hex(random_bytes(24));
    }

    public function regenerateApiKey(): void
    {
        $this->update(['api_key' => self::generateApiKey()]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['razon_social', 'documento', 'direccion', 'representante_legal', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}