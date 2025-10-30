<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comprobante extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'numero',
        'fecha_emision',
        'serie',
        'tipo',
        'contenido',
        'comprobanteable_id',
        'comprobanteable_type'
    ];

    protected $casts = [
        'fecha_emision' => 'datetime',
        'contenido' => 'array'
    ];

    public function comprobanteable()
    {
        return $this->morphTo();
    }
}
