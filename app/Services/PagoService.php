<?php

namespace App\Services;

use App\Models\Pago;
use App\Models\PagoDetalle;
use App\Models\PaymentSchedule;
use App\Models\ConceptoPago;
use App\Models\Caja;
use Illuminate\Support\Facades\DB;

class PagoService
{
    public function crearPago(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Obtener caja abierta del día
            $caja = Caja::obtenerCajaAbierta(
                $data['empresa_id'],
                $data['sucursal_id'],
                $data['fecha'] ?? now()->toDateString()
            );

            // Crear pago (cabecera)
            $pago = Pago::create([
                'tipo_pago' => $data['tipo_pago'],
                'fecha' => $data['fecha'],
                'matricula_id' => $data['matricula_id'],
                'serie_id' => $data['serie_id'] ?? null,
                'caja_id' => $caja?->id,
                'user_id' => auth()->id(),
                'metodo_pago' => $data['metodo_pago'] ?? null,
                'referencia' => $data['referencia'] ?? null,
                'descuento' => $data['descuento'] ?? 0,
                'total' => 0,
                'observaciones' => $data['observaciones'] ?? null,
                'empresa_id' => $data['empresa_id'],
                'sucursal_id' => $data['sucursal_id'],
                'estado' => $data['estado'] ?? Pago::ESTADO_PENDIENTE
            ]);

            // Crear detalles
            foreach ($data['detalles'] as $detalle) {
                $this->agregarDetalle($pago, $detalle);
            }

            $pago->calcularTotales();
            
            return $pago->fresh(['detalles']);
        });
    }

    public function agregarDetalle(Pago $pago, array $detalle)
    {
        $pagoDetalle = PagoDetalle::create([
            'pago_id' => $pago->id,
            'concepto_pago_id' => $detalle['concepto_pago_id'],
            'payment_schedule_id' => $detalle['payment_schedule_id'] ?? null,
            'descripcion' => $detalle['descripcion'],
            'cantidad' => $detalle['cantidad'] ?? 1,
            'precio_unitario' => $detalle['precio_unitario']
        ]);

        // Si el detalle está vinculado a una cuota, actualizar el cronograma
        if (isset($detalle['payment_schedule_id'])) {
            $schedule = PaymentSchedule::find($detalle['payment_schedule_id']);
            if ($schedule) {
                $schedule->registrarPago($pagoDetalle->subtotal);
            }
        }

        return $pagoDetalle;
    }

    public function pagarCuota($scheduleId, array $data)
    {
        $schedule = PaymentSchedule::findOrFail($scheduleId);
        $conceptoMensualidad = ConceptoPago::where('nombre', 'Mensualidad')->first();

        return $this->crearPago([
            'tipo_pago' => $data['tipo_pago'] ?? Pago::TIPO_RECIBO,
            'fecha' => $data['fecha'] ?? now(),
            'matricula_id' => $schedule->matricula_id,
            'metodo_pago' => $data['metodo_pago'] ?? null,
            'referencia' => $data['referencia'] ?? null,
            'descuento' => $data['descuento'] ?? 0,
            'observaciones' => $data['observaciones'] ?? null,
            'empresa_id' => $schedule->empresa_id,
            'sucursal_id' => $schedule->sucursal_id,
            'estado' => Pago::ESTADO_APROBADO,
            'detalles' => [
                [
                    'concepto_pago_id' => $conceptoMensualidad?->id,
                    'payment_schedule_id' => $schedule->id,
                    'descripcion' => "Cuota #{$schedule->numero_cuota} - Vencimiento: {$schedule->fecha_vencimiento->format('d/m/Y')}",
                    'cantidad' => 1,
                    'precio_unitario' => $data['monto'] ?? $schedule->saldo_pendiente
                ]
            ]
        ]);
    }

    public function aprobarPago(Pago $pago)
    {
        $pago->update(['estado' => Pago::ESTADO_APROBADO]);
        
        // Actualizar totales de la caja si está asociada
        if ($pago->caja) {
            $pago->caja->calcularTotales();
        }
        
        return $pago;
    }

    public function cancelarPago(Pago $pago, $motivo = null)
    {
        DB::transaction(function () use ($pago, $motivo) {
            // Revertir pagos en cronogramas
            foreach ($pago->detalles as $detalle) {
                if ($detalle->payment_schedule_id) {
                    $schedule = $detalle->paymentSchedule;
                    $schedule->monto_pagado -= $detalle->subtotal;
                    
                    if ($schedule->monto_pagado < $schedule->monto) {
                        $schedule->estado = 'pendiente';
                        $schedule->fecha_pago = null;
                    }
                    
                    $schedule->save();
                }
            }

            $pago->update([
                'estado' => Pago::ESTADO_CANCELADO,
                'observaciones' => ($pago->observaciones ? $pago->observaciones . "\n" : '') . "Cancelado: $motivo"
            ]);
        });

        return $pago;
    }
}
