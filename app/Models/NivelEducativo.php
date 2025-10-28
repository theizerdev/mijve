<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Multitenantable;
class NivelEducativo extends Model
{
    use SoftDeletes, Multitenantable;

    protected $table = 'niveles_educativos';

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'nombre',
        'descripcion',
        'costo',
        'costo_matricula',
        'costo_mensualidad',
        'numero_cuotas',
        'cuota_inicial',
        'status'
    ];

    protected $casts = [
        'costo' => 'decimal:2',
        'costo_matricula' => 'decimal:2',
        'costo_mensualidad' => 'decimal:2',
        'cuota_inicial' => 'decimal:2',
        'status' => 'boolean',
    ];
}