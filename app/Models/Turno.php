<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    use HasFactory;

    protected $fillable = [
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
}