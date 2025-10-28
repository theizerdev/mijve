<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Multitenantable;

class Matricula extends Model
{
    use HasFactory, Multitenantable;

    protected $fillable = [
        'estudiante_id',
        'programa_id',
        'periodo_id',
        'fecha_matricula',
        'estado',
        'costo',
        'cuota_inicial',
        'numero_cuotas',
        'empresa_id',
        'sucursal_id'
    ];

    protected $casts = [
        'fecha_matricula' => 'date',
        'costo' => 'decimal:2',
        'cuota_inicial' => 'decimal:2',
        'numero_cuotas' => 'integer'
    ];

    public function estudiante()
    {
        return $this->belongsTo(Student::class);
    }

    // Alias para la relación estudiante
    public function student()
    {
        return $this->estudiante();
    }

    public function programa()
    {
        return $this->belongsTo(Programa::class);
    }

    public function periodo()
    {
        return $this->belongsTo(SchoolPeriod::class, 'periodo_id');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
}