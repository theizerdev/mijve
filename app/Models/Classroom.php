<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Multitenantable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Classroom extends Model
{
    use HasFactory, SoftDeletes, Multitenantable, LogsActivity;

    protected $table = 'classrooms';

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'nombre',
        'codigo',
        'ubicacion',
        'capacidad',
        'tipo_aula',
        'recursos',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacidad' => 'integer',
        'recursos' => 'array',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'aula_asignada');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('tipo_aula', $type);
    }

    public function scopeByCapacity($query, $minCapacity)
    {
        return $query->where('capacidad', '>=', $minCapacity);
    }

    public function isAvailable($day, $startTime, $endTime, $excludeScheduleId = null): bool
    {
        $query = $this->schedules()
            ->where('dia_semana', $day)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where(function ($q) use ($startTime, $endTime) {
                    $q->where('hora_inicio', '<', $endTime)
                      ->where('hora_fin', '>', $startTime);
                });
            });

        if ($excludeScheduleId) {
            $query->where('id', '!=', $excludeScheduleId);
        }

        return $query->count() === 0;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'nombre',
                'codigo',
                'ubicacion',
                'capacidad',
                'tipo_aula',
                'recursos',
                'is_active'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}