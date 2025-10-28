<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Multitenantable;

class ConceptoPago extends Model
{
    use HasFactory, Multitenantable;
    
    protected $table = 'conceptos_pago';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
        'empresa_id',
        'sucursal_id'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

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