<?php

namespace App\Livewire\Admin\Reportes;

use Livewire\Component;
use App\Models\Student;
use App\Models\Matricula;
use App\Models\Pago;
use App\Models\EducationalLevel;
use App\Models\Programa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\DebtNotification;

class Morosidad extends Component
{
    public $nivelesEducativos;
    public $programas;
    public $nivel_educativo_id;
    public $programa_id;
    public $morosos = [];
    public $totales = [];
    public $detalleDeuda = [];
    public $mostrarModal = false;
    public $estudianteSeleccionado = null;

    public function mount()
    {
        $this->nivelesEducativos = EducationalLevel::all();
        $this->programas = collect(); // Inicialmente vacío
        // Inicializar totales con valores por defecto
        $this->totales = [
            'total_estudiantes' => 0,
            'total_morosos' => 0,
            'porcentaje_morosidad' => 0
        ];
    }

    public function updatedNivelEducativoId()
    {
        if ($this->nivel_educativo_id) {
            $this->programas = Programa::where('nivel_educativo_id', $this->nivel_educativo_id)->get();
        } else {
            $this->programas = collect();
        }
        
        $this->programa_id = '';
        $this->morosos = [];
        // Reinicializar totales cuando cambian los filtros
        $this->totales = [
            'total_estudiantes' => 0,
            'total_morosos' => 0,
            'porcentaje_morosidad' => 0
        ];
    }

    public function cargarReporte()
    {
        $query = Matricula::with(['student', 'programa.nivelEducativo'])
            ->where('matriculas.estado', 'activo')
            ->leftJoin('pagos', 'matriculas.id', '=', 'pagos.matricula_id')
            ->select(
                'matriculas.*',
                DB::raw('COALESCE(SUM(pagos.monto_pagado), 0) as total_pagado')
            )
            ->groupBy('matriculas.id');

        if ($this->programa_id) {
            $query->where('matriculas.programa_id', $this->programa_id);
        } elseif ($this->nivel_educativo_id) {
            $query->join('programas', 'matriculas.programa_id', '=', 'programas.id')
                  ->where('programas.nivel_educativo_id', $this->nivel_educativo_id);
        }

        $matriculas = $query->get();

        // Calcular morosidad para cada matrícula
        $this->morosos = [];
        foreach ($matriculas as $matricula) {
            // Obtener el total pagado de forma más precisa
            $pagos = Pago::where("matricula_id", $matricula->id)->get();
            $totalPagado = $pagos->sum("monto_pagado");
            $costoTotal = $matricula->costo ?? 0;
            $saldoPendiente = $costoTotal - $totalPagado;

            // Verificar si el estudiante tiene pagos completos
            $tienePagosCompletos = $pagos->where("estado", Pago::ESTADO_COMPLETADO)->count() > 0;
            $esPagoCompleto = $totalPagado >= ($costoTotal * 0.9); // Considerar completo si pagó al menos el 90%

            // Considerar moroso si:
            // 1. Tiene saldo pendiente mayor a 10% del costo total
            // 2. No tiene pagos completos registrados
            // 3. No ha pagado al menos el 90% del costo total
            if ($saldoPendiente > ($costoTotal * 0.1) && !$tienePagosCompletos && !$esPagoCompleto) {
                $this->morosos[] = [
                    'matricula' => $matricula,
                    'total_pagado' => $totalPagado,
                    'saldo_pendiente' => $saldoPendiente,
                    'porcentaje_pagado' => $costoTotal > 0 ? ($totalPagado / $costoTotal) * 100 : 0
                ];
            }
        }

        // Calcular totales
        $totalEstudiantes = $matriculas->count();
        $totalMorosos = count($this->morosos);
        $porcentajeMorosidad = $totalEstudiantes > 0 ? ($totalMorosos / $totalEstudiantes) * 100 : 0;

        $this->totales = [
            'total_estudiantes' => $totalEstudiantes,
            'total_morosos' => $totalMorosos,
            'porcentaje_morosidad' => $porcentajeMorosidad
        ];
    }

    public function mostrarDetalleDeuda($matriculaId)
    {
        // Obtener la matrícula con sus pagos
        $matricula = Matricula::with(['student', 'programa.nivelEducativo', 'pagos.conceptoPago'])
            ->find($matriculaId);
            
        if (!$matricula) {
            return;
        }
        
        $this->estudianteSeleccionado = $matricula;
        $this->detalleDeuda = $matricula->pagos;
        $this->mostrarModal = true;
        
        // Emitir evento para mostrar la modal
        $this->dispatch('mostrarModal');
    }

    public function enviarNotificacionDeuda()
    {
        if (!$this->estudianteSeleccionado) {
            session()->flash('error', 'No se ha seleccionado un estudiante.');
            return;
        }

        $estudiante = $this->estudianteSeleccionado->student;
        
        // Verificar si el estudiante es mayor de edad
        $esMayorDeEdad = $estudiante->fecha_nacimiento && 
                         $estudiante->fecha_nacimiento->age >= 18;
        
        $correoDestino = null;
        $nombreDestino = null;
        
        if ($esMayorDeEdad && $estudiante->correo_electronico) {
            // Enviar al correo del estudiante si es mayor de edad
            $correoDestino = $estudiante->correo_electronico;
            $nombreDestino = $estudiante->nombres . ' ' . $estudiante->apellidos;
        } elseif (!$esMayorDeEdad && $estudiante->representante_correo) {
            // Enviar al correo del representante si es menor de edad
            $correoDestino = $estudiante->representante_correo;
            $nombreDestino = $estudiante->representante_nombres . ' ' . $estudiante->representante_apellidos;
        }
        
        // Agregar información de depuración
        Log::info('Verificación de correo para notificación', [
            'es_mayor_de_edad' => $esMayorDeEdad,
            'estudiante_email' => $estudiante->correo_electronico,
            'representante_email' => $estudiante->representante_correo,
            'correo_destino' => $correoDestino
        ]);
        
        if (!$correoDestino) {
            // Mensaje más detallado para diagnosticar el problema
            if (!$esMayorDeEdad && !$estudiante->representante_correo) {
                session()->flash('error', 'No se encontró un correo de representante para enviar la notificación. Verifique que el estudiante tenga un correo de representante registrado.');
            } else {
                session()->flash('error', 'No se encontró un correo válido para enviar la notificación.');
            }
            return;
        }
        
        try {
            // Preparar datos para el correo
            $pendingAmount = ($this->estudianteSeleccionado->costo ?? 0) - $this->estudianteSeleccionado->pagos->sum('monto_pagado');
            
            // Enviar correo real
            Mail::to($correoDestino)->send(new DebtNotification($estudiante, $this->detalleDeuda, $pendingAmount));
            
            Log::info('Notificación de deuda enviada', [
                'destinatario' => $correoDestino,
                'estudiante' => $estudiante->nombres . ' ' . $estudiante->apellidos,
                'saldo_pendiente' => $pendingAmount
            ]);
            
            session()->flash('message', 'Notificación enviada correctamente a ' . $nombreDestino . ' (' . $correoDestino . ')');
        } catch (\Exception $e) {
            Log::error('Error al enviar notificación de deuda', [
                'error' => $e->getMessage(),
                'estudiante_id' => $estudiante->id
            ]);
            
            session()->flash('error', 'Error al enviar la notificación. Por favor, inténtelo de nuevo.');
        }
    }

    public function cerrarModal()
    {
        $this->mostrarModal = false;
        $this->detalleDeuda = [];
        $this->estudianteSeleccionado = null;
    }

    public function exportarExcel()
    {
        // Lógica para exportar a Excel
        session()->flash('message', 'Funcionalidad de exportación en desarrollo.');
    }

    public function exportarPDF()
    {
        // Lógica para exportar a PDF
        session()->flash('message', 'Funcionalidad de exportación en desarrollo.');
    }

    public function enviarNotificaciones()
    {
        // Lógica para enviar notificaciones a morosos
        session()->flash('message', 'Funcionalidad de notificaciones en desarrollo. Se enviarían notificaciones a ' . count($this->morosos) . ' estudiantes.');
    }

    public function render()
    {
        return view('livewire.admin.reportes.morosidad')
            ->layout('components.layouts.admin', [
                'title' => 'Reporte de Morosidad',
                'description' => 'Morosidad por nivel/programa'
            ]);
    }
}