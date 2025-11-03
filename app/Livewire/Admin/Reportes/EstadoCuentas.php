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
        $this->pagos = Pago::with(['detalles.conceptoPago'])
            ->where('matricula_id', $this->matricula_id)
            ->get();

        // Calcular totales
        $this->totalPagado = $this->pagos->where('estado', 'aprobado')->sum('total');
        
        // Calcular saldo pendiente
        $costoTotal = $this->matriculaSeleccionada->costo ?? 0;
        $this->saldoPendiente = max(0, $costoTotal - $this->totalPagado);
    }

    public function exportarExcel()
    {
        if (!$this->matriculaSeleccionada) {
            session()->flash('error', 'Debe seleccionar una matrícula para exportar.');
            return;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Configurar página
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        
        // Encabezado principal con logo/empresa
        $sheet->setCellValue('A1', 'ESTADO DE CUENTA ESTUDIANTIL');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(18)->getColor()->setRGB('2E3B4E');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1')->getFill()->setFillType('solid')->getStartColor()->setRGB('E8F4FD');
        
        // Fecha de generación
        $sheet->setCellValue('A2', 'Generado el: ' . now()->format('d/m/Y H:i'));
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10);
        
        // Información del estudiante con formato mejorado
        $sheet->setCellValue('A4', 'INFORMACIÓN DEL ESTUDIANTE');
        $sheet->mergeCells('A4:F4');
        $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(12)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A4')->getFill()->setFillType('solid')->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('A4')->getAlignment()->setHorizontal('center');
        
        $sheet->setCellValue('A5', 'Nombre Completo:');
        $sheet->setCellValue('B5', ($this->estudianteSeleccionado->nombres ?? '') . ' ' . ($this->estudianteSeleccionado->apellidos ?? ''));
        $sheet->setCellValue('D5', 'Documento:');
        $sheet->setCellValue('E5', $this->estudianteSeleccionado->documento_identidad ?? '');
        
        $sheet->setCellValue('A6', 'Programa:');
        $sheet->setCellValue('B6', $this->matriculaSeleccionada->programa->nombre ?? '');
        $sheet->setCellValue('D6', 'Fecha Matrícula:');
        $sheet->setCellValue('E6', $this->matriculaSeleccionada->fecha_matricula?->format('d/m/Y') ?? 'N/A');
        
        // Estilo para etiquetas
        $sheet->getStyle('A5:A6')->getFont()->setBold(true);
        $sheet->getStyle('D5:D6')->getFont()->setBold(true);
        
        // Resumen financiero
        $sheet->setCellValue('A8', 'RESUMEN FINANCIERO');
        $sheet->mergeCells('A8:F8');
        $sheet->getStyle('A8')->getFont()->setBold(true)->setSize(12)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A8')->getFill()->setFillType('solid')->getStartColor()->setRGB('70AD47');
        $sheet->getStyle('A8')->getAlignment()->setHorizontal('center');
        
        $costoTotal = $this->matriculaSeleccionada->costo ?? 0;
        $sheet->setCellValue('A9', 'Costo Total:');
        $sheet->setCellValue('B9', $costoTotal);
        $sheet->setCellValue('C9', 'Total Pagado:');
        $sheet->setCellValue('D9', $this->totalPagado);
        $sheet->setCellValue('E9', 'Saldo Pendiente:');
        $sheet->setCellValue('F9', $this->saldoPendiente);
        
        // Formato de moneda para resumen
        $sheet->getStyle('B9:F9')->getNumberFormat()->setFormatCode('$#,##0.00');
        $sheet->getStyle('A9:F9')->getFont()->setBold(true);
        
        // Color condicional para saldo
        if ($this->saldoPendiente > 0) {
            $sheet->getStyle('F9')->getFill()->setFillType('solid')->getStartColor()->setRGB('FFE699');
        } else {
            $sheet->getStyle('F9')->getFill()->setFillType('solid')->getStartColor()->setRGB('C6EFCE');
        }
        
        // Detalle de pagos
        $sheet->setCellValue('A11', 'DETALLE DE PAGOS');
        $sheet->mergeCells('A11:F11');
        $sheet->getStyle('A11')->getFont()->setBold(true)->setSize(12)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A11')->getFill()->setFillType('solid')->getStartColor()->setRGB('C55A5A');
        $sheet->getStyle('A11')->getAlignment()->setHorizontal('center');
        
        // Encabezados de tabla
        $headers = ['Fecha', 'Documento', 'Concepto', 'Método Pago', 'Monto', 'Estado'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '12', $header);
            $col++;
        }
        
        // Estilo de encabezados
        $sheet->getStyle('A12:F12')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A12:F12')->getFill()->setFillType('solid')->getStartColor()->setRGB('5B9BD5');
        $sheet->getStyle('A12:F12')->getAlignment()->setHorizontal('center');
        
        // Datos de pagos
        $row = 13;
        foreach ($this->pagos as $pago) {
            $conceptos = $pago->detalles->pluck('conceptoPago.nombre')->filter()->implode(', ');
            
            $sheet->setCellValue('A' . $row, $pago->fecha->format('d/m/Y'));
            $sheet->setCellValue('B' . $row, $pago->numero_completo ?? 'N/A');
            $sheet->setCellValue('C' . $row, $conceptos ?: 'N/A');
            $sheet->setCellValue('D' . $row, ucfirst($pago->metodo_pago ?? 'N/A'));
            $sheet->setCellValue('E' . $row, $pago->total);
            $sheet->setCellValue('F' . $row, ucfirst($pago->estado));
            
            // Color condicional por estado
            if ($pago->estado === 'aprobado') {
                $sheet->getStyle('F' . $row)->getFill()->setFillType('solid')->getStartColor()->setRGB('C6EFCE');
            } elseif ($pago->estado === 'pendiente') {
                $sheet->getStyle('F' . $row)->getFill()->setFillType('solid')->getStartColor()->setRGB('FFE699');
            } else {
                $sheet->getStyle('F' . $row)->getFill()->setFillType('solid')->getStartColor()->setRGB('FFC7CE');
            }
            
            $row++;
        }
        
        // Formato de moneda para columna de montos
        if ($row > 13) {
            $sheet->getStyle('E13:E' . ($row - 1))->getNumberFormat()->setFormatCode('$#,##0.00');
        }
        
        // Totales finales
        $row += 1;
        $sheet->setCellValue('D' . $row, 'TOTAL PAGADO:');
        $sheet->setCellValue('E' . $row, $this->totalPagado);
        $sheet->getStyle('D' . $row . ':E' . $row)->getFont()->setBold(true);
        $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');
        $sheet->getStyle('D' . $row . ':E' . $row)->getFill()->setFillType('solid')->getStartColor()->setRGB('D9E2F3');
        
        // Bordes para toda la tabla
        $sheet->getStyle('A12:F' . $row)->getBorders()->getAllBorders()->setBorderStyle('thin');
        
        // Ajustar ancho de columnas
        $sheet->getColumnDimension('A')->setWidth(12);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(12);
        
        // Pie de página
        $footerRow = $row + 3;
        $sheet->setCellValue('A' . $footerRow, 'Este documento es un estado de cuenta oficial generado automáticamente.');
        $sheet->mergeCells('A' . $footerRow . ':F' . $footerRow);
        $sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A' . $footerRow)->getFont()->setItalic(true)->setSize(9)->getColor()->setRGB('666666');
        
        // Crear respuesta de descarga
        $nombreEstudiante = str_replace(' ', '_', ($this->estudianteSeleccionado->nombres ?? '') . '_' . ($this->estudianteSeleccionado->apellidos ?? ''));
        $filename = 'Estado_Cuenta_' . $nombreEstudiante . '_' . date('Y-m-d') . '.xlsx';
        
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