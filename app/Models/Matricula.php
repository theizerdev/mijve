<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Multitenantable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Matricula extends Model
{
    use HasFactory, Multitenantable, LogsActivity;

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'estudiante_id',
        'programa_id',
        'nivel_educativo_id',
        'turno_id',
        'school_periods_id',
        'fecha_matricula',
        'costo_matricula',
        'costo_matricula_pagado',
        'porcentaje_descuento',
        'numero_cuotas',
        'monto_inicial',
        'monto_inicial_pagado',
        'monto_mensual',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_matricula' => 'date',
        'costo_matricula' => 'decimal:2',
        'costo_matricula_pagado' => 'decimal:2',
        'porcentaje_descuento' => 'decimal:2',
        'monto_inicial' => 'decimal:2',
        'monto_inicial_pagado' => 'decimal:2',
        'monto_mensual' => 'decimal:2',
    ];

    public function estudiante()
    {
        return $this->belongsTo(Student::class, 'estudiante_id');
    }

    // Alias para la relación estudiante
    public function student()
    {
        return $this->estudiante();
    }

    public function programa()
    {
        return $this->belongsTo(Programa::class, 'programa_id');
    }

    public function nivelEducativo()
    {
        return $this->belongsTo(EducationalLevel::class, 'nivel_educativo_id');
    }

    public function turno()
    {
        return $this->belongsTo(Turno::class, 'turno_id');
    }

    public function schoolPeriod()
    {
        return $this->belongsTo(SchoolPeriod::class, 'school_periods_id');
    }

    // Alias para la relación schoolPeriod
    public function periodo()
    {
        //dd($this->belongsTo(SchoolPeriod::class, 'periodo_id'));

        return $this->belongsTo(SchoolPeriod::class, 'periodo_id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    // Corregir el nombre de la relación para que coincida con el modelo PaymentSchedule
    public function paymentSchedules()
    {
        return $this->hasMany(PaymentSchedule::class, 'matricula_id');
    }

    // Alias para la relación paymentSchedules
    public function cronogramaPagos()
    {
        return $this->paymentSchedules();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'estudiante_id',
                'programa_id',
                'nivel_educativo_id',
                'turno_id',
                'school_periods_id',
                'fecha_matricula',
                'costo_matricula',
                'estado'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
