<?php

namespace App\Livewire\Admin\Reportes;

use Livewire\Component;
use App\Models\Pago;
use App\Models\ConceptoPago;
use Illuminate\Support\Facades\DB;

class IngresosTotales extends Component
{
    public $fecha_inicio;
    public $fecha_fin;
    public $ingresos = [];
    public $totales = [];

    public function mount()
    {
        $this->fecha_inicio = now()->startOfMonth()->format('Y-m-d');
        $this->fecha_fin = now()->endOfMonth()->format('Y-m-d');
        $this->cargarReporte();
    }

    public function cargarReporte()
    {
        // Validar fechas
        if (!$this->fecha_inicio || !$this->fecha_fin) {
            return;
        }

        // Obtener ingresos por concepto
        $this->ingresos = DB::table('pagos')
            ->join('conceptos_pago', 'pagos.concepto_pago_id', '=', 'conceptos_pago.id')
            ->select(
                'conceptos_pago.nombre as concepto',
                DB::raw('SUM(pagos.monto_pagado) as total'),
                DB::raw('COUNT(pagos.id) as cantidad')
            )
            ->whereBetween('pagos.fecha_pago', [$this->fecha_inicio, $this->fecha_fin])
            ->where('pagos.estado', 'pagado')
            ->groupBy('conceptos_pago.nombre')
            ->orderBy('total', 'desc')
            ->get();

        // Calcular totales
        $this->totales = [
            'total_ingresos' => $this->ingresos->sum('total'),
            'total_transacciones' => $this->ingresos->sum('cantidad')
        ];
    }

    public function exportarExcel()
    {
        // Lógica para exportar a Excel
        session()->flash('message', 'Funcionalidad de exportación en desarrollo.');
    }

    public function exportarPDF()
    {
        // Lógica para exportar a PDF
        session()->flash('message', 'Funcionalidad de exportación en desarrollo.');
    }

    public function render()
    {
        return view('livewire.admin.reportes.ingresos-totales')
            ->layout('components.layouts.admin', [
                'title' => 'Ingresos Totales',
                'description' => 'Ingresos totales por concepto'
            ]);
    }
}