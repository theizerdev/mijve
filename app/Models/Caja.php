<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Multitenantable;

class Caja extends Model
{
    use HasFactory,Multitenantable;

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'user_id',
        'fecha',
        'numero_corte',
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
        // Cargar pagos aprobados con su método de pago
        $pagos = $this->pagos()->with('metodoPago')->where('status', 'Aprobado')->get();

        $totalEfectivo = 0;
        $totalTransferencias = 0;
        $totalTarjetas = 0;

        foreach ($pagos as $pago) {
            $metodo = $pago->metodoPago ? $pago->metodoPago->tipo_pago : 'Desconocido';
            
            // Determinar el monto a sumar (priorizar bolívares si existe, sino euro)
            // Nota: Esto asume que la caja maneja una moneda base mixta o que se mostrará separado en la vista
            // Dado que Create.php pone monto_bolivares = 0 para Divisa, usamos monto_euro en ese caso.
            
            switch ($metodo) {
                case 'Divisa':
                case 'Efectivo':
                    $totalEfectivo += ($pago->monto_bolivares > 0 ? $pago->monto_bolivares : $pago->monto_euro);
                    break;
                case 'Pago Móvil':
                case 'Transferencia Bancaria':
                case 'Transferencia':
                    $totalTransferencias += $pago->monto_bolivares;
                    break;
                case 'Tarjeta':
                case 'Punto de Venta':
                    $totalTarjetas += $pago->monto_bolivares;
                    break;
                default:
                    $totalTransferencias += $pago->monto_bolivares;
            }
        }

        $this->total_efectivo = $totalEfectivo;
        $this->total_transferencias = $totalTransferencias;
        $this->total_tarjetas = $totalTarjetas;
        $this->total_ingresos = $totalEfectivo + $totalTransferencias + $totalTarjetas;
        $this->monto_final = $this->monto_inicial + $this->total_ingresos;

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

    public static function crearCajaDiaria($empresaId, $sucursalId, $montoInicial = 0, $observaciones = null, $userId = null): self
    {
        return self::create([
            'empresa_id' => $empresaId,
            'sucursal_id' => $sucursalId,
            'user_id' => $userId ?? auth()->id() ?? 1,
            'fecha' => now()->toDateString(),
            'numero_corte' => 1,
            'monto_inicial' => $montoInicial,
            'estado' => 'abierta',
            'fecha_apertura' => now(),
            'observaciones_apertura' => $observaciones,
        ]);
    }

    public static function crearCorte($empresaId, $sucursalId, $montoInicial = 0, $observaciones = null, $userId = null): self
    {
        $numeroCorte = self::where('empresa_id', $empresaId)
            ->where('sucursal_id', $sucursalId)
            ->whereDate('fecha', today())
            ->count() + 1;

        return self::create([
            'empresa_id' => $empresaId,
            'sucursal_id' => $sucursalId,
            'user_id' => $userId ?? auth()->id() ?? 1,
            'fecha' => now()->toDateString(),
            'numero_corte' => $numeroCorte,
            'monto_inicial' => $montoInicial,
            'estado' => 'abierta',
            'fecha_apertura' => now(),
            'observaciones_apertura' => ($observaciones ?? '') . " (Corte #{$numeroCorte})",
        ]);
    }
}
