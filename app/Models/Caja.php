<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Caja extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'user_id',
        'fecha',
        'monto_inicial',
        'total_efectivo',
        'total_transferencias',
        'total_tarjetas',
        'total_ingresos',
        'monto_final',
        'estado',
        'fecha_apertura',
        'fecha_cierre',
        'observaciones_apertura',
        'observaciones_cierre',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
        'monto_inicial' => 'decimal:2',
        'total_efectivo' => 'decimal:2',
        'total_transferencias' => 'decimal:2',
        'total_tarjetas' => 'decimal:2',
        'total_ingresos' => 'decimal:2',
        'monto_final' => 'decimal:2',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    public function calcularTotales(): void
    {
        $pagos = $this->pagos()->where('estado', 'aprobado');
        
        $this->total_efectivo = $pagos->where('metodo_pago', 'efectivo')->sum('total');
        $this->total_transferencias = $pagos->where('metodo_pago', 'transferencia')->sum('total');
        $this->total_tarjetas = $pagos->where('metodo_pago', 'tarjeta')->sum('total');
        $this->total_ingresos = $this->total_efectivo + $this->total_transferencias + $this->total_tarjetas;
        $this->monto_final = $this->monto_inicial + $this->total_efectivo;
        
        $this->save();
    }

    public function cerrar(string $observaciones = null): bool
    {
        if ($this->estado === 'cerrada') {
            return false;
        }

        $this->calcularTotales();
        $this->estado = 'cerrada';
        $this->fecha_cierre = now();
        $this->observaciones_cierre = $observaciones;
        
        return $this->save();
    }

    public static function obtenerCajaAbierta($empresaId, $sucursalId, $fecha = null): ?self
    {
        $fecha = $fecha ?? now()->toDateString();
        
        return self::where('empresa_id', $empresaId)
            ->where('sucursal_id', $sucursalId)
            ->where('fecha', $fecha)
            ->where('estado', 'abierta')
            ->first();
    }

    public static function crearCajaDiaria($empresaId, $sucursalId, $montoInicial = 0, $observaciones = null): self
    {
        return self::create([
            'empresa_id' => $empresaId,
            'sucursal_id' => $sucursalId,
            'user_id' => auth()->id(),
            'fecha' => now()->toDateString(),
            'monto_inicial' => $montoInicial,
            'fecha_apertura' => now(),
            'observaciones_apertura' => $observaciones,
        ]);
    }
}