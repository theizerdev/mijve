<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Multitenantable;

class PaymentSchedule extends Model
{
    use HasFactory, Multitenantable;

    protected $fillable = [
        'matricula_id',
        'numero_cuota',
        'monto',
        'monto_pagado',
        'fecha_vencimiento',
        'estado',
        'empresa_id',
        'sucursal_id'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'monto_pagado' => 'decimal:2',
        'fecha_vencimiento' => 'date'
    ];

    protected $attributes = [
        'estado' => 'pendiente',
        'monto_pagado' => 0
    ];

    public function matricula()
    {
        return $this->belongsTo(Matricula::class);
    }

    public function pagoDetalles()
    {
        return $this->hasMany(PagoDetalle::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopePagados($query)
    {
        return $query->where('estado', 'pagado');
    }

    public function scopeVencidos($query)
    {
        return $query->where('estado', 'pendiente')
            ->where('fecha_vencimiento', '<', now());
    }

    public function getSaldoPendienteAttribute()
    {
        return $this->monto - $this->monto_pagado;
    }

    public function getEstaPagadoAttribute()
    {
        return $this->monto_pagado >= $this->monto;
    }

    public function registrarPago($monto)
    {
        $this->monto_pagado += $monto;
        
        if ($this->monto_pagado >= $this->monto) {
            $this->estado = 'pagado';
            $this->fecha_pago = now();
        }
        
        $this->save();
    }
}
