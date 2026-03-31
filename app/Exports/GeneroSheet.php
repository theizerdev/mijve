<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCharts;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Chart\Layout;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class GeneroSheet implements FromArray, WithHeadings, WithTitle, WithStyles, ShouldAutoSize, WithCharts
{
    private $participantes;
    private $stats = [];

    public function __construct($participantes)
    {
        $this->participantes = $participantes;
        $this->buildStats();
    }

    private function buildStats()
    {
        $grouped = $this->participantes->groupBy(function ($p) {
            return $p->genero ?: 'Sin especificar';
        });

        $total = $this->participantes->count();

        foreach ($grouped as $genero => $items) {
            $cantidad = $items->count();
            $this->stats[] = [
                'genero' => $genero,
                'cantidad' => $cantidad,
                'porcentaje' => $total > 0 ? round(($cantidad / $total) * 100, 1) . '%' : '0%',
            ];
        }

        $this->stats[] = [
            'genero' => 'TOTAL',
            'cantidad' => $total,
            'porcentaje' => '100%',
        ];
    }

    public function title(): string
    {
        return 'Cantidad por Género';
    }

    public function headings(): array
    {
        return ['Género', 'Cantidad', 'Porcentaje'];
    }

    public function array(): array
    {
        return array_map(function ($row) {
            return [$row['genero'], $row['cantidad'], $row['porcentaje']];
        }, $this->stats);
    }

    public function charts()
    {
        $dataCount = count($this->stats) - 1; // Excluir fila TOTAL
        if ($dataCount <= 0) {
            return [];
        }

        $lastDataRow = $dataCount + 1; // +1 por el heading

        $labels = [new DataSeriesValues('String', "'Cantidad por Género'!\$A\$2:\$A\$" . $lastDataRow, null, $dataCount)];
        $values = [new DataSeriesValues('Number', "'Cantidad por Género'!\$B\$2:\$B\$" . $lastDataRow, null, $dataCount)];

        $series = new DataSeries(
            DataSeries::TYPE_PIECHART,
            DataSeries::GROUPING_STANDARD,
            range(0, 0),
            [],
            $labels,
            $values
        );

        $layout = new Layout();
        $layout->setShowPercent(true);

        $plotArea = new PlotArea($layout, [$series]);
        $legend = new Legend(Legend::POSITION_BOTTOM, null, false);
        $title = new Title('Distribución por Género');

        $chart = new Chart(
            'generoChart',
            $title,
            $legend,
            $plotArea
        );

        $chart->setTopLeftPosition('E2');
        $chart->setBottomRightPosition('L18');

        return $chart;
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = count($this->stats) + 1;

        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
        ]);

        $sheet->getStyle('A2:C' . $lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'EEEEEE']]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Estilo para fila TOTAL
        $sheet->getStyle('A' . $lastRow . ':C' . $lastRow)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8E8E8']],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(25);
        return [];
    }
}
