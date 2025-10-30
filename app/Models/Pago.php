<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Multitenantable;

class Pago extends Model
{
    use HasFactory, Multitenantable, SoftDeletes;

    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_COMPLETADO = 'completado';
    const ESTADO_CANCELADO = 'cancelado';
    const ESTADO_REEMBOLSADO = 'reembolsado';

    protected $fillable = [
        'matricula_id',
        'concepto_pago_id',
        'user_id',
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
        'fecha_pago' => 'date',
        'estado' => 'string'
    ];

    protected $attributes = [
        'estado' => self::ESTADO_PENDIENTE
    ];

    public function matricula()
    {
        return $this->belongsTo(Matricula::class);
    }

    public function conceptoPago()
    {
        return $this->belongsTo(ConceptoPago::class, 'concepto_pago_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comprobante()
    {
        return $this->morphOne(Comprobante::class, 'comprobanteable');
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

    public static function getEstados()
    {
        return [
            self::ESTADO_PENDIENTE => 'Pendiente',
            self::ESTADO_COMPLETADO => 'Completado',
            self::ESTADO_CANCELADO => 'Cancelado',
            self::ESTADO_REEMBOLSADO => 'Reembolsado'
        ];
    }
}