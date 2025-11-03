<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Multitenantable;

class Serie extends Model
{
    use Multitenantable;

    protected $fillable = [
        'tipo_documento',
        'serie',
        'correlativo_actual',
        'longitud_correlativo',
        'activo',
        'empresa_id',
        'sucursal_id'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'correlativo_actual' => 'integer',
        'longitud_correlativo' => 'integer'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function obtenerSiguienteNumero()
    {
        $this->increment('correlativo_actual');
        $this->refresh();
        
        return str_pad($this->correlativo_actual, $this->longitud_correlativo, '0', STR_PAD_LEFT);
    }

    public function getNumeroCompletoAttribute()
    {
        return $this->serie . '-' . str_pad($this->correlativo_actual, $this->longitud_correlativo, '0', STR_PAD_LEFT);
    }

    public static function getTiposDocumento()
    {
        return [
            'factura' => 'Factura',
            'boleta' => 'Boleta',
            'nota_credito' => 'Nota de Crédito',
            'recibo' => 'Recibo'
        ];
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_documento', $tipo);
    }
}
