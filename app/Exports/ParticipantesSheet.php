<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ParticipantesSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    private $participantes;

    public function __construct($participantes)
    {
        $this->participantes = $participantes;
    }

    public function title(): string
    {
        return 'Participantes';
    }

    public function headings(): array
    {
        return [
            'ID', 'Empresa', 'Extensión', 'Actividad', 'Nombres', 'Apellidos',
            'Cédula', 'Género', 'Edad', 'Fecha de Nacimiento', 'Estado Civil',
            'Tipo de Miembro', 'Teléfono Principal', 'Teléfono Alternativo',
            'Dirección', 'Zona', 'Distrito', 'Estado', 'Fecha de Registro'
        ];
    }

    public function collection()
    {
        return $this->participantes->map(function ($p) {
            return [
                $p->id,
                $p->empresa->razon_social ?? 'N/A',
                $p->extension->nombre ?? 'N/A',
                $p->actividad->nombre ?? 'N/A',
                $p->nombres,
                $p->apellidos,
                $p->cedula,
                $p->genero ?? 'N/A',
                $p->edad,
                $p->fecha_nacimiento?->format('d/m/Y') ?? 'N/A',
                $p->estado_civil ?? 'N/A',
                $p->tipo_miembro ?? 'N/A',
                $p->telefono_principal ?? 'N/A',
                $p->telefono_alternativo ?? 'N/A',
                $p->direccion ?? 'N/A',
                $p->zona ?? 'N/A',
                $p->distrito ?? 'N/A',
                $p->status ? 'Activo' : 'Inactivo',
                $p->created_at?->format('d/m/Y H:i') ?? 'N/A',
            ];
        });
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = $sheet->getHighestColumn();
        $lastRow = $sheet->getHighestRow();

        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
        ]);

        if ($lastRow > 1) {
            $sheet->getStyle('A2:' . $lastColumn . $lastRow)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'EEEEEE']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            for ($i = 2; $i <= $lastRow; $i += 2) {
                $sheet->getStyle('A' . $i . ':' . $lastColumn . $i)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F9FAFB']],
                ]);
            }
        }

        $sheet->getRowDimension(1)->setRowHeight(25);
        return [];
    }
}
