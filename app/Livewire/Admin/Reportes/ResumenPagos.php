<?php

namespace App\Livewire\Admin\Reportes;

use Livewire\Component;
use App\Models\Pago;
use App\Models\SchoolPeriod;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResumenPagos extends Component
{
    public $periodos;
    public $periodo_id;
    public $fecha_inicio;
    public $fecha_fin;
    public $pagos = [];
    public $totales = [];

    public function mount()
    {
        $this->periodos = SchoolPeriod::all();
        $this->fecha_inicio = now()->startOfMonth()->format('Y-m-d');
        $this->fecha_fin = now()->endOfMonth()->format('Y-m-d');
        $this->cargarReporte();
    }

    public function updatedPeriodoId()
    {
        if ($this->periodo_id) {
            $periodo = SchoolPeriod::find($this->periodo_id);
            if ($periodo) {
                $this->fecha_inicio = $periodo->fecha_inicio ? $periodo->fecha_inicio->format('Y-m-d') : now()->startOfMonth()->format('Y-m-d');
                $this->fecha_fin = $periodo->fecha_fin ? $periodo->fecha_fin->format('Y-m-d') : now()->endOfMonth()->format('Y-m-d');
                $this->cargarReporte();
            }
        }
    }

    public function cargarReporte()
    {
        // Validar fechas
        if (!$this->fecha_inicio || !$this->fecha_fin) {
            return;
        }

        // Obtener pagos en el rango de fechas
        $this->pagos = Pago::with(['matricula.student', 'conceptoPago'])
            ->whereBetween('fecha_pago', [$this->fecha_inicio, $this->fecha_fin])
            ->where('estado', 'pagado')
            ->get();

        // Calcular totales por concepto
        $this->totales = DB::table('pagos')
            ->join('conceptos_pago', 'pagos.concepto_pago_id', '=', 'conceptos_pago.id')
            ->select(
                'conceptos_pago.nombre as concepto',
                DB::raw('SUM(pagos.monto_pagado) as total'),
                DB::raw('COUNT(pagos.id) as cantidad')
            )
            ->whereBetween('pagos.fecha_pago', [$this->fecha_inicio, $this->fecha_fin])
            ->where('pagos.estado', 'pagado')
            ->groupBy('conceptos_pago.nombre')
            ->get();
    }

    public function exportarExcel()
    {
        // Validar que haya datos para exportar
        if (count($this->pagos) == 0) {
            session()->flash('error', 'No hay datos para exportar.');
            return;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Título
        $sheet->setCellValue('A1', 'Resumen de Pagos');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        
        // Fechas del reporte
        $sheet->setCellValue('A3', 'Período:');
        $sheet->setCellValue('B3', ($this->fecha_inicio ? \Carbon\Carbon::createFromFormat('Y-m-d', $this->fecha_inicio)->format('d/m/Y') : 'N/A') . ' - ' . 
                              ($this->fecha_fin ? \Carbon\Carbon::createFromFormat('Y-m-d', $this->fecha_fin)->format('d/m/Y') : 'N/A'));
        
        // Encabezados de la tabla de pagos
        $sheet->setCellValue('A5', 'Fecha');
        $sheet->setCellValue('B5', 'Estudiante');
        $sheet->setCellValue('C5', 'Concepto');
        $sheet->setCellValue('D5', 'Monto');
        $sheet->setCellValue('E5', 'Pagado');
        $sheet->setCellValue('F5', 'Método');
        
        // Formato de encabezados
        $sheet->getStyle('A5:F5')->getFont()->setBold(true);
        
        // Datos de pagos
        $row = 6;
        foreach ($this->pagos as $pago) {
            $sheet->setCellValue('A' . $row, $pago->fecha_pago?->format('d/m/Y') ?? 'N/A');
            $sheet->setCellValue('B' . $row, ($pago->matricula?->student?->nombres ?? '') . ' ' . ($pago->matricula?->student?->apellidos ?? ''));
            $sheet->setCellValue('C' . $row, $pago->conceptoPago?->nombre ?? 'N/A');
            $sheet->setCellValue('D' . $row, $pago->monto);
            $sheet->setCellValue('E' . $row, $pago->monto_pagado);
            $sheet->setCellValue('F' . $row, ucfirst($pago->metodo_pago ?? 'N/A'));
            $row++;
        }
        
        // Formato de moneda
        $sheet->getStyle('D6:E' . ($row - 1))->getNumberFormat()->setFormatCode('$#,##0.00');
        
        // Ancho de columnas
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        
        // Segunda hoja con totales por concepto
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Totales por Concepto');
        
        // Título de la segunda hoja
        $sheet2->setCellValue('A1', 'Totales por Concepto');
        $sheet2->mergeCells('A1:D1');
        $sheet2->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        
        // Encabezados de la tabla de totales
        $sheet2->setCellValue('A3', 'Concepto');
        $sheet2->setCellValue('B3', 'Cantidad');
        $sheet2->setCellValue('C3', 'Total');
        $sheet2->setCellValue('D3', 'Porcentaje');
        
        // Formato de encabezados
        $sheet2->getStyle('A3:D3')->getFont()->setBold(true);
        
        // Calcular total general para porcentajes
        $totalGeneral = $this->totales->sum('total');
        
        // Datos de totales
        $row2 = 4;
        foreach ($this->totales as $total) {
            $sheet2->setCellValue('A' . $row2, $total->concepto);
            $sheet2->setCellValue('B' . $row2, $total->cantidad);
            $sheet2->setCellValue('C' . $row2, $total->total);
            $sheet2->setCellValue('D' . $row2, $totalGeneral > 0 ? ($total->total / $totalGeneral) * 100 : 0);
            $row2++;
        }
        
        // Formato de moneda y porcentaje
        $sheet2->getStyle('C4:C' . ($row2 - 1))->getNumberFormat()->setFormatCode('$#,##0.00');
        $sheet2->getStyle('D4:D' . ($row2 - 1))->getNumberFormat()->setFormatCode('0.00%');
        
        // Ancho de columnas en la segunda hoja
        $sheet2->getColumnDimension('A')->setWidth(30);
        $sheet2->getColumnDimension('B')->setWidth(15);
        $sheet2->getColumnDimension('C')->setWidth(15);
        $sheet2->getColumnDimension('D')->setWidth(15);
        
        // Crear respuesta de descarga
        $filename = 'resumen_pagos_' . date('Y-m-d') . '.xlsx';
        
        return new StreamedResponse(
            function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . urlencode($filename) . '"',
            ]
        );
    }

    public function exportarPDF()
    {
        // Lógica para exportar a PDF
        session()->flash('message', 'Funcionalidad de exportación en desarrollo.');
    }

    public function render()
    {
        return view('livewire.admin.reportes.resumen-pagos')
            ->layout('components.layouts.admin', [
                'title' => 'Resumen de Pagos',
                'description' => 'Resumen de pagos por período'
            ]);
    }
}