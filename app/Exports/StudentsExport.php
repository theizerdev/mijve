<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class StudentsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'Código',
            'Nombres',
            'Apellidos',
            'Documento',
            'Fecha Nacimiento',
            'Edad',
            'Grado',
            'Sección',
            'Nivel Educativo',
            'Turno',
            'Período Escolar',
            'Empresa',
            'Sucursal',
            'Estado',
            'Correo Electrónico',
            'Representante',
            'Teléfonos Representante',
            'Correo Representante',
            'Monto Total Matrícula',
            'Monto Pagado',
            'Monto Pendiente',
            'Próxima Fecha de Vencimiento',
            'Días de Retraso'
        ];
    }

    public function map($student): array
    {
        // Obtener información de morosidad
        $debtInfo = $this->getStudentDebtInfo($student);
        
        // Formatear teléfonos del representante
        $telefonos = '';
        if ($student->representante_telefonos) {
            if (is_array($student->representante_telefonos)) {
                $telefonos = implode(', ', $student->representante_telefonos);
            } else {
                $telefonos = $student->representante_telefonos;
            }
        }
        
        return [
            $student->codigo,
            $student->nombres,
            $student->apellidos,
            $student->documento_identidad,
            $student->fecha_nacimiento ? $student->fecha_nacimiento->format('d/m/Y') : '',
            $student->edad ?? '',
            $student->grado,
            $student->seccion,
            $student->nivelEducativo->nombre ?? '',
            $student->turno->nombre ?? '',
            $student->schoolPeriod->nombre ?? '',
            $student->empresa->razon_social ?? '',
            $student->sucursal->nombre ?? '',
            $student->status ? 'Activo' : 'Inactivo',
            $student->correo_electronico ?? '',
            $student->representante_nombres ? $student->representante_nombres . ' ' . $student->representante_apellidos : '',
            $telefonos,
            $student->representante_correo ?? '',
            $debtInfo['total_amount'],
            $debtInfo['paid_amount'],
            $debtInfo['pending_amount'],
            $debtInfo['next_due_date'],
            $debtInfo['days_overdue']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para la fila de encabezados
            1 => ['font' => ['bold' => true]],
        ];
    }

    // Función para obtener información de morosidad del estudiante
    private function getStudentDebtInfo($student)
    {
        // Obtener la matrícula activa del estudiante
        $matricula = $student->matriculas()
            ->where('estado', 'activo')
            ->with('paymentSchedules') // Esta relación ya ha sido corregida en el modelo
            ->first();

        if (!$matricula) {
            return [
                'total_amount' => 0,
                'paid_amount' => 0,
                'pending_amount' => 0,
                'next_due_date' => '',
                'days_overdue' => 0
            ];
        }

        // Calcular totales
        $totalAmount = $matricula->paymentSchedules->sum('monto');
        $paidAmount = $matricula->paymentSchedules->sum('monto_pagado');
        $pendingAmount = $totalAmount - ($paidAmount ?? 0);

        // Obtener próxima fecha de vencimiento
        $nextDueDate = $matricula->paymentSchedules
            ->where('estado', '!=', 'pagado')
            ->where('fecha_vencimiento', '!=', null)
            ->min('fecha_vencimiento');

        // Calcular días de retraso
        $daysOverdue = 0;
        if ($nextDueDate) {
            $nextDueDate = Carbon::parse($nextDueDate);
            if ($nextDueDate->isPast()) {
                $daysOverdue = $nextDueDate->diffInDays(Carbon::now());
            }
        }

        return [
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount ?? 0,
            'pending_amount' => $pendingAmount,
            'next_due_date' => $nextDueDate ? $nextDueDate->format('d/m/Y') : '',
            'days_overdue' => $daysOverdue
        ];
    }
}