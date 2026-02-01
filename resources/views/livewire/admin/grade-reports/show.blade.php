<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">{{ $report->title }}</h5>
                        <small class="text-muted">Número: <code>{{ $report->report_number }}</code></small>
                    </div>
                    <span class="badge bg-{{ $report->status_color }} fs-6">{{ $report->status_label }}</span>
                </div>

                <!-- Estadísticas -->
                <div class="card-body border-bottom">
                    <div class="row text-center">
                        <div class="col">
                            <h4 class="mb-0 text-primary">{{ $report->total_students }}</h4>
                            <small class="text-muted">Estudiantes</small>
                        </div>
                        <div class="col">
                            <h4 class="mb-0 text-success">{{ $report->approved_count }}</h4>
                            <small class="text-muted">Aprobados</small>
                        </div>
                        <div class="col">
                            <h4 class="mb-0 text-danger">{{ $report->failed_count }}</h4>
                            <small class="text-muted">Reprobados</small>
                        </div>
                        <div class="col">
                            <h4 class="mb-0 text-info">{{ number_format($report->average_grade, 2) }}</h4>
                            <small class="text-muted">Promedio</small>
                        </div>
                        <div class="col">
                            <h4 class="mb-0 text-warning">{{ $report->approval_rate }}%</h4>
                            <small class="text-muted">Aprobación</small>
                        </div>
                    </div>
                </div>

                <!-- Tabla de calificaciones -->
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Código</th>
                                <th>Estudiante</th>
                                <th class="text-center">Promedio</th>
                                <th class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($report->grades_data ?? [] as $index => $studentData)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><code>{{ $studentData['codigo'] ?? '-' }}</code></td>
                                    <td>{{ $studentData['apellidos'] ?? '' }}, {{ $studentData['nombres'] ?? '' }}</td>
                                    <td class="text-center">
                                        <strong class="fs-5">{{ number_format($studentData['average'] ?? 0, 2) }}</strong>
                                    </td>
                                    <td class="text-center">
                                        @if(($studentData['status'] ?? '') === 'approved')
                                            <span class="badge bg-success">Aprobado</span>
                                        @else
                                            <span class="badge bg-danger">Reprobado</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($report->observations)
                    <div class="card-body border-top">
                        <h6 class="text-muted mb-2">Observaciones</h6>
                        <p class="mb-0">{{ $report->observations }}</p>
                    </div>
                @endif

                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('admin.grade-reports.index') }}" class="btn btn-secondary">
                        <i class="ri ri-arrow-left-line me-1"></i> Volver
                    </a>
                    <div>
                        @if($report->status === 'generated')
                            <button type="button" class="btn btn-success me-2" wire:click="approve">
                                <i class="ri ri-checkbox-circle-line me-1"></i> Aprobar
                            </button>
                        @endif
                        @if($report->status === 'approved')
                            <button type="button" class="btn btn-primary me-2" wire:click="publish">
                                <i class="ri ri-send-plane-line me-1"></i> Publicar
                            </button>
                        @endif
                        <button type="button" class="btn btn-outline-primary" wire:click="downloadPdf">
                            <i class="ri ri-download-line me-1"></i> Descargar PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Información del Acta</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted">Tipo:</td>
                            <td>{{ $report->type_label }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Sección:</td>
                            <td>{{ $report->section->nombre ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Período:</td>
                            <td>{{ $report->schoolPeriod->name ?? '-' }}</td>
                        </tr>
                        @if($report->subject)
                            <tr>
                                <td class="text-muted">Materia:</td>
                                <td>{{ $report->subject->name }}</td>
                            </tr>
                        @endif
                        @if($report->evaluationPeriod)
                            <tr>
                                <td class="text-muted">Lapso:</td>
                                <td>{{ $report->evaluationPeriod->name }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="text-muted">Nota más alta:</td>
                            <td><strong class="text-success">{{ $report->highest_grade }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nota más baja:</td>
                            <td><strong class="text-danger">{{ $report->lowest_grade }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Historial</h6>
                </div>
                <div class="card-body">
                    <ul class="timeline-simple">
                        @if($report->generated_at)
                            <li class="mb-3">
                                <span class="badge bg-info me-2">Generada</span>
                                <small class="text-muted">{{ $report->generated_at->format('d/m/Y H:i') }}</small>
                                <br><small>Por: {{ $report->generatedByUser->name ?? '-' }}</small>
                            </li>
                        @endif
                        @if($report->approved_at)
                            <li class="mb-3">
                                <span class="badge bg-success me-2">Aprobada</span>
                                <small class="text-muted">{{ $report->approved_at->format('d/m/Y H:i') }}</small>
                                <br><small>Por: {{ $report->approvedByUser->name ?? '-' }}</small>
                            </li>
                        @endif
                        @if($report->published_at)
                            <li class="mb-3">
                                <span class="badge bg-primary me-2">Publicada</span>
                                <small class="text-muted">{{ $report->published_at->format('d/m/Y H:i') }}</small>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
