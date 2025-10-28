<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Multitenantable;

class Pago extends Model
{
    use HasFactory, Multitenantable;

    protected $fillable = [
        'matricula_id',
        'concepto_pago_id',
        'monto',
        'monto_pagado',
        'fecha_pago',
        'metodo_pago',
        'referencia',
        'estado',
        'empresa_id',
        'sucursal_id'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'monto_pagado' => 'decimal:2',
        'fecha_pago' => 'date'
    ];

    public function matricula()
    {
        return $this->belongsTo(Matricula::class);
    }

    public function conceptoPago()
    {
        return $this->belongsTo(ConceptoPago::class, 'concepto_pago_id');
    }

    // Alias para la relación conceptoPago
    public function concepto()
    {
        return $this->conceptoPago();
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