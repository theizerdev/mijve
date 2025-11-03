<?php

namespace App\Livewire\Admin\Reportes;

use Livewire\Component;
use App\Models\Matricula;
use App\Models\SchoolPeriod;
use App\Models\EducationalLevel;
use App\Models\Programa;
use Illuminate\Support\Facades\DB;

class HistoricoMatriculas extends Component
{
    public $matriculas = [];
    public $periodos;
    public $nivelesEducativos;
    public $programas;
    
    public $periodo_id;
    public $nivel_educativo_id;
    public $programa_id;
    public $fecha_inicio;
    public $fecha_fin;
    
    public $estadisticas = [];

    public function mount()
    {
        $this->periodos = SchoolPeriod::all();
        $this->nivelesEducativos = EducationalLevel::all();
        $this->programas = collect();
        $this->fecha_inicio = now()->startOfMonth()->format('Y-m-d');
        $this->fecha_fin = now()->endOfMonth()->format('Y-m-d');
        $this->cargarReporte();
    }

    public function updatedNivelEducativoId()
    {
        if ($this->nivel_educativo_id) {
            $this->programas = Programa::where('nivel_educativo_id', $this->nivel_educativo_id)->get();
        } else {
            $this->programas = collect();
        }
        
        $this->programa_id = '';
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
        $query = Matricula::with(['student', 'programa.nivelEducativo', 'periodo'])
            ->whereBetween('fecha_matricula', [$this->fecha_inicio, $this->fecha_fin]);

        if ($this->programa_id) {
            $query->where('programa_id', $this->programa_id);
        } elseif ($this->nivel_educativo_id) {
            $query->join('programas', 'matriculas.programa_id', '=', 'programas.id')
                  ->where('programas.nivel_educativo_id', $this->nivel_educativo_id);
        }

        $this->matriculas = $query->get();

        // Calcular estadísticas
        $this->calcularEstadisticas();
    }

    private function calcularEstadisticas()
    {
        $total = $this->matriculas->count();
        
        // Agrupar por estado
        $porEstado = $this->matriculas->groupBy('estado')->map->count();
        
        // Agrupar por nivel educativo
        $porNivel = $this->matriculas->groupBy(function($matricula) {
            return $matricula->programa->nivelEducativo->nombre ?? 'Sin nivel';
        })->map->count();
        
        // Agrupar por programa
        $porPrograma = $this->matriculas->groupBy(function($matricula) {
            return $matricula->programa->nombre ?? 'Sin programa';
        })->map->count();
        
        // Agrupar por período
        $porPeriodo = $this->matriculas->groupBy(function($matricula) {
            return $matricula->periodo->nombre ?? 'Sin período';
        })->map->count();

        $this->estadisticas = [
            'total' => $total,
            'por_estado' => $porEstado,
            'por_nivel' => $porNivel,
            'por_programa' => $porPrograma,
            'por_periodo' => $porPeriodo
        ];
    }

    public function exportarExcel()
    {
        if (count($this->matriculas) == 0) {
            session()->flash('error', 'No hay datos para exportar.');
            return;
        }

        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Histórico Matrículas');

            // Encabezado principal
            $sheet->setCellValue('A1', 'HISTÓRICO DE MATRÍCULAS');
            $sheet->mergeCells('A1:I1');
            $sheet->getStyle('A1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => '0D6EFD']],
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
            $sheet->setCellValue('A5', 'Total matrículas:');
            $sheet->setCellValue('B5', $this->estadisticas['total']);

            $sheet->getStyle('A3:A5')->getFont()->setBold(true);

            // Estadísticas por estado
            $sheet->setCellValue('D3', 'Activas:');
            $sheet->setCellValue('E3', $this->estadisticas['por_estado']['activo'] ?? 0);
            $sheet->setCellValue('D4', 'Inactivas:');
            $sheet->setCellValue('E4', $this->estadisticas['por_estado']['inactivo'] ?? 0);
            $sheet->setCellValue('D5', 'Suspendidas:');
            $sheet->setCellValue('E5', $this->estadisticas['por_estado']['suspendido'] ?? 0);

            $sheet->getStyle('D3:D5')->getFont()->setBold(true);
            $sheet->getStyle('E3')->getFont()->setBold(true)->getColor()->setRGB('28A745');
            $sheet->getStyle('E4')->getFont()->setBold(true)->getColor()->setRGB('DC3545');
            $sheet->getStyle('E5')->getFont()->setBold(true)->getColor()->setRGB('FFC107');

            // Encabezados de la tabla
            $headers = ['Fecha', 'Estudiante', 'Documento', 'Programa', 'Nivel', 'Período', 'Costo', 'Cuotas', 'Estado'];
            foreach ($headers as $index => $header) {
                $column = chr(65 + $index);
                $sheet->setCellValue($column . '7', $header);
            }
            
            $sheet->getStyle('A7:I7')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => '0D6EFD']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
            ]);

            // Datos de matrículas
            $row = 8;
            foreach ($this->matriculas as $matricula) {
                $sheet->setCellValue('A' . $row, $matricula->fecha_matricula?->format('d/m/Y') ?? 'N/A');
                $sheet->setCellValue('B' . $row, ($matricula->student->nombres ?? '') . ' ' . ($matricula->student->apellidos ?? ''));
                $sheet->setCellValue('C' . $row, $matricula->student->documento_identidad ?? 'N/A');
                $sheet->setCellValue('D' . $row, $matricula->programa->nombre ?? 'N/A');
                $sheet->setCellValue('E' . $row, $matricula->programa->nivelEducativo->nombre ?? 'N/A');
                $sheet->setCellValue('F' . $row, $matricula->periodo->nombre ?? 'N/A');
                $sheet->setCellValue('G' . $row, $matricula->costo ?? 0);
                $sheet->setCellValue('H' . $row, $matricula->numero_cuotas ?? 0);
                $sheet->setCellValue('I' . $row, ucfirst($matricula->estado ?? 'N/A'));
                $row++;
            }

            // Formato de la tabla
            $rangeData = 'A8:I' . ($row - 1);
            $sheet->getStyle($rangeData)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('G8:G' . ($row - 1))->getNumberFormat()->setFormatCode('$#,##0.00');

            // Configuración de columnas
            $sheet->getColumnDimension('A')->setWidth(12);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(15);
            $sheet->getColumnDimension('D')->setWidth(25);
            $sheet->getColumnDimension('E')->setWidth(20);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(12);
            $sheet->getColumnDimension('H')->setWidth(10);
            $sheet->getColumnDimension('I')->setWidth(12);

            // Segunda hoja con estadísticas
            $sheet2 = $spreadsheet->createSheet();
            $sheet2->setTitle('Estadísticas');

            // Título de estadísticas
            $sheet2->setCellValue('A1', 'ESTADÍSTICAS DE MATRÍCULAS');
            $sheet2->mergeCells('A1:C1');
            $sheet2->getStyle('A1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 16],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'E9ECEF']]
            ]);

            // Estadísticas por nivel
            $currentRow = 3;
            $sheet2->setCellValue('A' . $currentRow, 'POR NIVEL EDUCATIVO');
            $sheet2->mergeCells('A' . $currentRow . ':C' . $currentRow);
            $sheet2->getStyle('A' . $currentRow)->getFont()->setBold(true);
            $currentRow += 2;

            foreach ($this->estadisticas['por_nivel'] as $nivel => $cantidad) {
                $sheet2->setCellValue('A' . $currentRow, $nivel);
                $sheet2->setCellValue('B' . $currentRow, $cantidad);
                $sheet2->setCellValue('C' . $currentRow, $this->estadisticas['total'] > 0 ? ($cantidad / $this->estadisticas['total']) : 0);
                $currentRow++;
            }

            // Estadísticas por programa
            $currentRow += 2;
            $sheet2->setCellValue('A' . $currentRow, 'POR PROGRAMA');
            $sheet2->mergeCells('A' . $currentRow . ':C' . $currentRow);
            $sheet2->getStyle('A' . $currentRow)->getFont()->setBold(true);
            $currentRow += 2;

            foreach ($this->estadisticas['por_programa'] as $programa => $cantidad) {
                $sheet2->setCellValue('A' . $currentRow, $programa);
                $sheet2->setCellValue('B' . $currentRow, $cantidad);
                $sheet2->setCellValue('C' . $currentRow, $this->estadisticas['total'] > 0 ? ($cantidad / $this->estadisticas['total']) : 0);
                $currentRow++;
            }

            // Formato de porcentajes
            $sheet2->getStyle('C1:C' . $currentRow)->getNumberFormat()->setFormatCode('0.00%');
            $sheet2->getColumnDimension('A')->setWidth(30);
            $sheet2->getColumnDimension('B')->setWidth(15);
            $sheet2->getColumnDimension('C')->setWidth(15);

            $filename = 'historico_matriculas_' . 
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
            \Log::error('Error exportando Excel histórico: ' . $e->getMessage());
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
        return view('livewire.admin.reportes.historico-matriculas')
            ->layout('components.layouts.admin', [
                'title' => 'Histórico de Matrículas',
                'description' => 'Historial de matrículas por estudiante'
            ]);
    }
}