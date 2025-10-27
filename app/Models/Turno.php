<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Multitenantable;

class Turno extends Model
{
    use HasFactory, Multitenantable;

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'nombre',
        'hora_inicio', 
        'hora_fin',
        'descripcion',
        'status'
    ];

    protected $casts = [
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
        'status' => 'boolean'
    ];
    
    protected $table = 'turnos';

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
}