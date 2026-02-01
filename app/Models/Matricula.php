<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'periodo_id',
        'nivel_educativo_id',
        'turno_id',
        'fecha_matricula',
        'costo',
        'cuota_inicial',
        'numero_cuotas',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_matricula' => 'date',
        'costo' => 'decimal:2',
        'cuota_inicial' => 'decimal:2',
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
        return $this->belongsTo(SchoolPeriod::class, 'periodo_id');
    }

    // Alias para la relación schoolPeriod
    public function periodo()
    {
        return $this->schoolPeriod();
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    // Relaciones con AcademicTracking
    public function academicRecords(): HasMany
    {
        return $this->hasMany(AcademicRecord::class, 'matricula_id');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class, 'matricula_id');
    }

    public function academicStatusTracking(): HasMany
    {
        return $this->hasMany(AcademicStatusTracking::class, 'matricula_id');
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

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'matricula_id');
    }

    // Métodos auxiliares para el estado académico
    public function getCurrentAcademicStatusAttribute()
    {
        return $this->academicStatusTracking()
                   ->where('status', AcademicStatusTracking::TRACKING_ACTIVE)
                   ->first();
    }

    public function getLatestCertificateAttribute()
    {
        return $this->certificates()
                   ->where('status', Certificate::STATUS_ACTIVE)
                   ->latest()
                   ->first();
    }

    public function getAcademicPerformanceAttribute()
    {
        $academicStatus = $this->current_academic_status;
        return $academicStatus ? $academicStatus->academic_summary : null;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'estudiante_id',
                'programa_id',
                'nivel_educativo_id',
                'turno_id',
                'periodo_id',
                'fecha_matricula',
                'costo',
                'cuota_inicial',
                'numero_cuotas',
                'estado'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}