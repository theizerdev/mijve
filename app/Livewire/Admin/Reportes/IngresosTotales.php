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

        // Obtener ingresos por concepto usando la nueva estructura
        $this->ingresos = DB::table('pagos')
            ->join('pago_detalles', 'pagos.id', '=', 'pago_detalles.pago_id')
            ->join('conceptos_pago', 'pago_detalles.concepto_pago_id', '=', 'conceptos_pago.id')
            ->select(
                'conceptos_pago.nombre as concepto',
                DB::raw('SUM(pago_detalles.subtotal) as total'),
                DB::raw('COUNT(DISTINCT pagos.id) as cantidad')
            )
            ->whereBetween('pagos.fecha', [$this->fecha_inicio, $this->fecha_fin])
            ->where('pagos.estado', 'aprobado')
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
        if (count($this->ingresos) == 0) {
            session()->flash('error', 'No hay datos para exportar.');
            return;
        }

        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Ingresos Totales');

            // Encabezado principal
            $sheet->setCellValue('A1', 'INGRESOS TOTALES POR CONCEPTO');
            $sheet->mergeCells('A1:E1');
            $sheet->getStyle('A1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => '28A745']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'F8F9FA']]
            ]);

            // Información del reporte
            $sheet->setCellValue('A3', 'Período:');
            $sheet->setCellValue('B3', 
                (\Carbon\Carbon::createFromFormat('Y-m-d', $this->fecha_inicio)->format('d/m/Y')) . 
                ' al ' . 
                (\Carbon\Carbon::createFromFormat('Y-m-d', $this->fecha_fin)->format('d/m/Y'))
            );
            $sheet->setCellValue('A4', 'Fecha de generación:');
            $sheet->setCellValue('B4', now()->format('d/m/Y H:i:s'));
            $sheet->setCellValue('D3', 'Total ingresos:');
            $sheet->setCellValue('E3', '$' . number_format($this->totales['total_ingresos'], 2));
            $sheet->setCellValue('D4', 'Total transacciones:');
            $sheet->setCellValue('E4', $this->totales['total_transacciones']);

            $sheet->getStyle('A3:A4')->getFont()->setBold(true);
            $sheet->getStyle('D3:D4')->getFont()->setBold(true);
            $sheet->getStyle('E3')->getFont()->setBold(true)->getColor()->setRGB('28A745');

            // Encabezados de la tabla
            $headers = ['Concepto', 'Cantidad', 'Total', 'Porcentaje', 'Promedio'];
            foreach ($headers as $index => $header) {
                $column = chr(65 + $index);
                $sheet->setCellValue($column . '6', $header);
            }
            
            $sheet->getStyle('A6:E6')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => '28A745']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
            ]);

            // Datos de ingresos
            $row = 7;
            $totalGeneral = $this->totales['total_ingresos'];
            foreach ($this->ingresos as $ingreso) {
                $sheet->setCellValue('A' . $row, $ingreso->concepto);
                $sheet->setCellValue('B' . $row, $ingreso->cantidad);
                $sheet->setCellValue('C' . $row, $ingreso->total);
                $sheet->setCellValue('D' . $row, $totalGeneral > 0 ? ($ingreso->total / $totalGeneral) : 0);
                $sheet->setCellValue('E' . $row, $ingreso->cantidad > 0 ? ($ingreso->total / $ingreso->cantidad) : 0);
                $row++;
            }

            // Total general
            $sheet->setCellValue('A' . $row, 'TOTAL GENERAL');
            $sheet->setCellValue('B' . $row, $this->totales['total_transacciones']);
            $sheet->setCellValue('C' . $row, $totalGeneral);
            $sheet->setCellValue('D' . $row, 1); // 100%
            $sheet->setCellValue('E' . $row, $this->totales['total_transacciones'] > 0 ? ($totalGeneral / $this->totales['total_transacciones']) : 0);
            
            $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'F8F9FA']],
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
            ]);

            // Formato de la tabla
            $rangeData = 'A7:E' . $row;
            $sheet->getStyle($rangeData)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('C7:C' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');
            $sheet->getStyle('D7:D' . $row)->getNumberFormat()->setFormatCode('0.00%');
            $sheet->getStyle('E7:E' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');

            // Configuración de columnas
            $sheet->getColumnDimension('A')->setWidth(30);
            $sheet->getColumnDimension('B')->setWidth(12);
            $sheet->getColumnDimension('C')->setWidth(15);
            $sheet->getColumnDimension('D')->setWidth(12);
            $sheet->getColumnDimension('E')->setWidth(15);

            // Pie de página
            $footerRow = $row + 2;
            $sheet->setCellValue('A' . $footerRow, 'Reporte generado por Sistema de Gestión Académica');
            $sheet->mergeCells('A' . $footerRow . ':E' . $footerRow);
            $sheet->getStyle('A' . $footerRow)->applyFromArray([
                'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '6C757D']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
            ]);

            $filename = 'ingresos_totales_' . 
                       \Carbon\Carbon::createFromFormat('Y-m-d', $this->fecha_inicio)->format('Y-m-d') . 
                       '_al_' . 
                       \Carbon\Carbon::createFromFormat('Y-m-d', $this->fecha_fin)->format('Y-m-d') . 
                       '.xlsx';

            session()->flash('success', 'Archivo Excel generado correctamente.');

            return new \Symfony\Component\HttpFoundation\StreamedResponse(
                function () use ($spreadsheet) {
                    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                    $writer->save('php://output');
                },
                200,
                [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => 'attachment; filename="' . urlencode($filename) . '"',
                    'Cache-Control' => 'max-age=0',
                ]
            );
        } catch (\Exception $e) {
            \Log::error('Error exportando Excel ingresos: ' . $e->getMessage());
            session()->flash('error', 'Error al generar el archivo Excel: ' . $e->getMessage());
            return;
        }
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