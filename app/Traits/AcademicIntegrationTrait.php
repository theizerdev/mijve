<?php

namespace App\Traits;

use App\Models\AcademicRecord;
use App\Models\AcademicStatusTracking;
use App\Models\Certificate;
use App\Models\Matricula;
use Illuminate\Support\Facades\DB;

trait AcademicIntegrationTrait
{
    /**
     * Sincronizar el estado académico de una matrícula
     */
    public function syncAcademicStatus(Matricula $matricula): AcademicStatusTracking
    {
        return DB::transaction(function () use ($matricula) {
            // Obtener registros académicos de la matrícula
            $academicRecords = AcademicRecord::where('matricula_id', $matricula->id)->get();
            
            if ($academicRecords->isEmpty()) {
                throw new \Exception("No hay registros académicos para la matrícula {$matricula->id}");
            }

            // Calcular estadísticas
            $totalSubjects = $academicRecords->count();
            $approvedSubjects = $academicRecords->where('status', AcademicRecord::STATUS_COMPLETED)
                                               ->where('promoted', true)
                                               ->count();
            $failedSubjects = $academicRecords->where('status', AcademicRecord::STATUS_FAILED)->count();
            $inRecoverySubjects = $academicRecords->where('in_recovery', true)->count();
            
            // Calcular promedio
            $average = $academicRecords->avg('final_grade');
            
            // Determinar estado académico
            $academicStatus = $this->determineAcademicStatus($approvedSubjects, $totalSubjects, $failedSubjects);
            
            // Determinar nivel de rendimiento
            $performanceLevel = $this->determinePerformanceLevel($average);

            // Calcular asistencia promedio
            $attendancePercentage = $academicRecords->avg('attendance_percentage');

            // Calcular conducta promedio
            $conductGrade = $this->calculateConductGrade($academicRecords);

            // Actualizar o crear registro de seguimiento
            $tracking = AcademicStatusTracking::updateOrCreate(
                [
                    'student_id' => $matricula->estudiante_id,
                    'matricula_id' => $matricula->id,
                    'school_period_id' => $matricula->periodo_id,
                ],
                [
                    'program_id' => $matricula->programa_id,
                    'educational_level_id' => $matricula->nivel_educativo_id,
                    'empresa_id' => $matricula->empresa_id,
                    'sucursal_id' => $matricula->sucursal_id,
                    'academic_status' => $academicStatus,
                    'period_average' => $average,
                    'total_subjects' => $totalSubjects,
                    'approved_subjects' => $approvedSubjects,
                    'failed_subjects' => $failedSubjects,
                    'in_recovery_subjects' => $inRecoverySubjects,
                    'performance_level' => $performanceLevel,
                    'attendance_percentage' => $attendancePercentage,
                    'conduct_grade' => $conductGrade,
                    'promoted' => $approvedSubjects >= ($totalSubjects * 0.7), // 70% de aprobación
                    'repeated' => $failedSubjects >= ($totalSubjects * 0.5), // 50% de reprobación
                    'graduated' => $this->isGraduated($matricula, $approvedSubjects, $totalSubjects),
                    'withdrawn' => $matricula->estado === 'inactivo',
                    'enrollment_date' => $matricula->fecha_matricula,
                    'completion_date' => $this->getCompletionDate($academicStatus, $matricula),
                    'withdrawal_date' => $matricula->estado === 'inactivo' ? now() : null,
                    'status' => AcademicStatusTracking::TRACKING_ACTIVE,
                ]
            );

            return $tracking;
        });
    }

    /**
     * Generar certificado para una matrícula
     */
    public function generateCertificate(Matricula $matricula, string $type = 'completion', bool $force = false): Certificate
    {
        return DB::transaction(function () use ($matricula, $type, $force) {
            // Verificar si ya existe un certificado activo del mismo tipo
            if (!$force) {
                $existingCertificate = Certificate::where('matricula_id', $matricula->id)
                                                ->where('certificate_type', $type)
                                                ->where('status', Certificate::STATUS_ACTIVE)
                                                ->first();

                if ($existingCertificate) {
                    return $existingCertificate;
                }
            }

            // Obtener el estado académico actual
            $academicStatus = AcademicStatusTracking::where('matricula_id', $matricula->id)
                                                   ->where('status', AcademicStatusTracking::TRACKING_ACTIVE)
                                                   ->first();

            if (!$academicStatus) {
                // Sincronizar primero si no existe
                $academicStatus = $this->syncAcademicStatus($matricula);
            }

            // Verificar requisitos para generar certificado
            if (!$this->canGenerateCertificate($matricula, $academicStatus, $type)) {
                throw new \Exception("No se cumplen los requisitos para generar el certificado de tipo {$type}");
            }

            // Obtener registros académicos
            $academicRecords = AcademicRecord::where('matricula_id', $matricula->id)->get();

            // Preparar datos académicos
            $academicData = $this->prepareAcademicData($academicStatus, $academicRecords);

            // Generar contenido del certificado
            $content = $this->generateCertificateContent($matricula, $academicStatus, $type);

            // Generar número de certificado y código de verificación
            $certificateNumber = $this->generateCertificateNumber($matricula, $type);
            $verificationCode = $this->generateVerificationCode();

            // Crear el certificado
            $certificate = Certificate::create([
                'student_id' => $matricula->estudiante_id,
                'matricula_id' => $matricula->id,
                'school_period_id' => $matricula->periodo_id,
                'empresa_id' => $matricula->empresa_id,
                'sucursal_id' => $matricula->sucursal_id,
                'certificate_type' => $type,
                'certificate_number' => $certificateNumber,
                'issue_date' => now(),
                'status' => Certificate::STATUS_ACTIVE,
                'content' => $content,
                'academic_data' => $academicData,
                'overall_average' => $academicStatus->period_average,
                'total_subjects' => $academicStatus->total_subjects,
                'approved_subjects' => $academicStatus->approved_subjects,
                'conduct_grade' => $academicStatus->conduct_grade,
                'attendance_percentage' => $academicStatus->attendance_percentage,
                'completed' => true,
                'is_digital' => true,
                'verification_code' => $verificationCode,
                'issued_by_user_id' => auth()->id() ?? 1,
            ]);

            return $certificate;
        });
    }

    /**
     * Determinar el estado académico basado en el rendimiento
     */
    private function determineAcademicStatus($approved, $total, $failed): string
    {
        if ($total == 0) return AcademicStatusTracking::STATUS_ACTIVE;
        
        $approvalRate = ($approved / $total) * 100;
        
        if ($approvalRate >= 70) {
            return AcademicStatusTracking::STATUS_COMPLETED;
        } elseif ($approvalRate >= 50) {
            return AcademicStatusTracking::STATUS_ACTIVE;
        } elseif ($failed > ($total * 0.5)) {
            return AcademicStatusTracking::STATUS_PROBATION;
        } else {
            return AcademicStatusTracking::STATUS_SUSPENDED;
        }
    }

    /**
     * Determinar el nivel de rendimiento
     */
    private function determinePerformanceLevel($average): string
    {
        if ($average >= 90) return AcademicStatusTracking::PERFORMANCE_EXCELLENT;
        if ($average >= 80) return AcademicStatusTracking::PERFORMANCE_GOOD;
        if ($average >= 70) return AcademicStatusTracking::PERFORMANCE_AVERAGE;
        return AcademicStatusTracking::PERFORMANCE_POOR;
    }

    /**
     * Calcular la calificación de conducta
     */
    private function calculateConductGrade($academicRecords): string
    {
        // Lógica para calcular la conducta basada en observaciones y registros
        // Por ahora, retornamos una calificación predeterminada
        return 'BUENO';
    }

    /**
     * Verificar si el estudiante se ha graduado
     */
    private function isGraduated(Matricula $matricula, $approvedSubjects, $totalSubjects): bool
    {
        // Lógica para determinar si el estudiante se ha graduado
        // Por ejemplo, si ha completado todas las materias del programa
        return $approvedSubjects >= $totalSubjects && $totalSubjects > 0;
    }

    /**
     * Obtener la fecha de finalización
     */
    private function getCompletionDate($academicStatus, Matricula $matricula)
    {
        if ($academicStatus === AcademicStatusTracking::STATUS_COMPLETED) {
            return now();
        }
        return null;
    }

    /**
     * Verificar si se puede generar un certificado
     */
    private function canGenerateCertificate(Matricula $matricula, AcademicStatusTracking $academicStatus, string $type): bool
    {
        // Verificar que la matrícula esté activa o completada
        if (!in_array($matricula->estado, ['activo', 'completado'])) {
            return false;
        }

        // Verificar requisitos según el tipo de certificado
        switch ($type) {
            case Certificate::TYPE_COMPLETION:
                return $academicStatus->academic_status === AcademicStatusTracking::STATUS_COMPLETED;
                
            case Certificate::TYPE_CONDUCT:
                return true; // Siempre se puede generar certificado de conducta
                
            case Certificate::TYPE_ACADEMIC:
                return $academicStatus->total_subjects > 0;
                
            default:
                return false;
        }
    }

    /**
     * Preparar datos académicos para el certificado
     */
    private function prepareAcademicData(AcademicStatusTracking $academicStatus, $academicRecords): array
    {
        return [
            'subjects' => $academicRecords->map(function ($record) {
                return [
                    'subject' => $record->subject->name ?? 'N/A',
                    'grade' => $record->final_grade,
                    'status' => $record->status,
                    'promoted' => $record->promoted,
                ];
            })->toArray(),
            'performance_summary' => [
                'average' => $academicStatus->period_average,
                'performance_level' => $academicStatus->performance_level,
                'approval_rate' => $academicStatus->approval_rate ?? 0,
            ],
            'attendance_summary' => [
                'percentage' => $academicStatus->attendance_percentage,
            ],
        ];
    }

    /**
     * Generar contenido del certificado
     */
    private function generateCertificateContent(Matricula $matricula, AcademicStatusTracking $academicStatus, string $type): string
    {
        $student = $matricula->estudiante;
        $program = $matricula->programa;
        $period = $matricula->periodo;

        $content = "CERTIFICADO DE ";
        
        switch ($type) {
            case Certificate::TYPE_COMPLETION:
                $content .= "COMPLETACIÓN DE ESTUDIOS";
                $body = "La institución certifica que el/la estudiante {$student->full_name} ha completado exitosamente el período académico {$period->name} en el programa {$program->name}.";
                break;
                
            case Certificate::TYPE_CONDUCT:
                $content .= "CONDUCTA";
                $body = "La institución certifica que el/la estudiante {$student->full_name} ha mantenido una conducta {$academicStatus->conduct_grade} durante el período académico {$period->name}.";
                break;
                
            case Certificate::TYPE_ACADEMIC:
                $content .= "RENDIMIENTO ACADÉMICO";
                $body = "La institución certifica que el/la estudiante {$student->full_name} ha obtenido un promedio de {$academicStatus->period_average} en el período académico {$period->name}.";
                break;
        }

        $content .= "\n\n{$body}\n\n";
        $content .= "Promedio: {$academicStatus->period_average}\n";
        $content .= "Materias aprobadas: {$academicStatus->approved_subjects} de {$academicStatus->total_subjects}\n";
        $content .= "Asistencia: {$academicStatus->attendance_percentage}%\n";
        $content .= "Nivel de rendimiento: {$academicStatus->performance_level}\n";

        return $content;
    }

    /**
     * Generar número de certificado
     */
    private function generateCertificateNumber(Matricula $matricula, string $type): string
    {
        $prefix = match($type) {
            Certificate::TYPE_COMPLETION => 'COM',
            Certificate::TYPE_CONDUCT => 'CON',
            Certificate::TYPE_ACADEMIC => 'ACA',
            default => 'CRT',
        };

        $year = date('Y');
        $sequence = str_pad(Certificate::whereYear('created_at', $year)->count() + 1, 6, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$year}-{$sequence}";
    }

    /**
     * Generar código de verificación
     */
    private function generateVerificationCode(): string
    {
        return strtoupper(\Illuminate\Support\Str::random(8));
    }
}