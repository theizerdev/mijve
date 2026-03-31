<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ParticipantesMultiSheetExport implements WithMultipleSheets
{
    private $participantes;

    public function __construct($participantes)
    {
        $this->participantes = $participantes;
    }

    public function sheets(): array
    {
        return [
            'Participantes' => new ParticipantesSheet($this->participantes),
            'Cantidad por Género' => new GeneroSheet($this->participantes),
            'Por Edades' => new EdadesSheet($this->participantes),
        ];
    }
}
