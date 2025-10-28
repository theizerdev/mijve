<?php

namespace App\Livewire\Admin\Reportes;

use Livewire\Component;
use App\Models\Student;
use App\Models\Matricula;
use App\Models\Pago;
use App\Models\ConceptoPago;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EstadoCuentas extends Component
{
    public $estudiantes;
    public $estudiante_id;
    public $matricula_id;
    public $estudianteSeleccionado;
    public $matriculaSeleccionada;
    public $pagos = [];
    public $totalPagado = 0;
    public $saldoPendiente = 0;

    public function mount()
    {
        $this->estudiantes = Student::with('matriculas.programa')->get();
    }

    public function updatedEstudianteId()
    {
        $this->estudianteSeleccionado = Student::with('matriculas.programa')
            ->find($this->estudiante_id);
            
        $this->matricula_id = '';
        $this->matriculaSeleccionada = null;
        $this->pagos = [];
        $this->totalPagado = 0;
        $this->saldoPendiente = 0;
    }

    public function updatedMatriculaId()
    {
        $this->matriculaSeleccionada = Matricula::with('programa.nivelEducativo')
            ->find($this->matricula_id);
            
        $this->cargarEstadoCuenta();
    }

    public function cargarEstadoCuenta()
    {
        if (!$this->matricula_id) {
            return;
        }

        // Obtener todos los pagos de esta matrícula
        $this->pagos = Pago::with('conceptoPago')
            ->where('matricula_id', $this->matricula_id)
            ->get();

        // Calcular totales
        $this->totalPagado = $this->pagos->sum('monto_pagado');
        
        // Calcular saldo pendiente (esto es un ejemplo, se podría hacer más complejo)
        $costoTotal = $this->matriculaSeleccionada->costo ?? 0;
        $this->saldoPendiente = $costoTotal - $this->totalPagado;
    }

    public function exportarExcel()
    {
        if (!$this->matriculaSeleccionada) {
            session()->flash('error', 'Debe seleccionar una matrícula para exportar.');
            return;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Título
        $sheet->setCellValue('A1', 'Estado de Cuenta');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        
        // Información del estudiante
        $sheet->setCellValue('A3', 'Estudiante:');
        $sheet->setCellValue('B3', ($this->estudianteSeleccionado->nombres ?? '') . ' ' . ($this->estudianteSeleccionado->apellidos ?? ''));
        $sheet->setCellValue('A4', 'Documento:');
        $sheet->setCellValue('B4', $this->estudianteSeleccionado->documento_identidad ?? '');
        $sheet->setCellValue('A5', 'Programa:');
        $sheet->setCellValue('B5', $this->matriculaSeleccionada->programa->nombre ?? '');
        $sheet->setCellValue('A6', 'Fecha de Matrícula:');
        $sheet->setCellValue('B6', $this->matriculaSeleccionada->fecha_matricula?->format('d/m/Y') ?? 'N/A');
        
        // Encabezados de la tabla de pagos
        $sheet->setCellValue('A8', 'Fecha');
        $sheet->setCellValue('B8', 'Concepto');
        $sheet->setCellValue('C8', 'Monto');
        $sheet->setCellValue('D8', 'Pagado');
        $sheet->setCellValue('E8', 'Estado');
        
        // Formato de encabezados
        $sheet->getStyle('A8:E8')->getFont()->setBold(true);
        
        // Datos de pagos
        $row = 9;
        foreach ($this->pagos as $pago) {
            $sheet->setCellValue('A' . $row, $pago->fecha_pago?->format('d/m/Y') ?? 'N/A');
            $sheet->setCellValue('B' . $row, $pago->conceptoPago->nombre ?? 'N/A');
            $sheet->setCellValue('C' . $row, $pago->monto);
            $sheet->setCellValue('D' . $row, $pago->monto_pagado);
            $sheet->setCellValue('E' . $row, ucfirst($pago->estado));
            $row++;
        }
        
        // Totales
        $sheet->setCellValue('A' . ($row + 1), 'Total Pagado:');
        $sheet->setCellValue('B' . ($row + 1), $this->totalPagado);
        $sheet->setCellValue('A' . ($row + 2), 'Saldo Pendiente:');
        $sheet->setCellValue('B' . ($row + 2), $this->saldoPendiente);
        
        // Formato de moneda
        $sheet->getStyle('C9:D' . ($row + 2))->getNumberFormat()->setFormatCode('$#,##0.00');
        
        // Ancho de columnas
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        
        // Crear respuesta de descarga
        $filename = 'estado_cuenta_' . ($this->estudianteSeleccionado->nombres ?? 'estudiante') . '_' . date('Y-m-d') . '.xlsx';
        
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
        return view('livewire.admin.reportes.estado-cuentas')
            ->layout('components.layouts.admin', [
                'title' => 'Estado de Cuentas',
                'description' => 'Consulta el estado de cuenta de cada estudiante'
            ]);
    }
}