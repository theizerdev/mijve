<?php

namespace App\Exports;

use App\Models\SchoolPeriod;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SchoolPeriodsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return SchoolPeriod::select([
            'name',
            'description',
            'start_date',
            'end_date',
            'is_active',
            'is_current',
            'created_at'
        ])->get();
    }

    public function headings(): array
    {
        return [
            'Nombre',
            'Descripción',
            'Fecha Inicio',
            'Fecha Fin',
            'Activo',
            'Actual',
            'Creado el'
        ];
    }

    public function map($schoolPeriod): array
    {
        return [
            $schoolPeriod->name,
            $schoolPeriod->description,
            $schoolPeriod->start_date->format('d/m/Y'),
            $schoolPeriod->end_date->format('d/m/Y'),
            $schoolPeriod->is_active ? 'Sí' : 'No',
            $schoolPeriod->is_current ? 'Sí' : 'No',
            $schoolPeriod->created_at->format('d/m/Y H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para los encabezados
            1 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'D9D9D9']]
            ],
            // Estilo para fechas
            'C:D' => ['numberFormat' => ['formatCode' => 'dd/mm/yyyy']],
            'G' => ['numberFormat' => ['formatCode' => 'dd/mm/yyyy hh:mm']]
        ];
    }
}
