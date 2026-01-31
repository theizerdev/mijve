<div>
    <div class="card shadow-sm">
        <div class="card-header border-bottom">
            <h5 class="mb-0">
                <i class="ri ri-file-list-3-line me-2"></i>Reporte de Rendimiento Estudiantil por Período
            </h5>
        </div>
        <div class="card-body">
            <!-- Filtros -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label for="evaluation_period_id" class="form-label">Período de Evaluación</label>
                    <select id="evaluation_period_id" class="form-select" wire:model.live="evaluation_period_id">
                        <option value="">Seleccione un período</option>
                        @foreach($evaluationPeriods as $period)
                            <option value="{{ $period->id }}">{{ $period->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="program_id" class="form-label">Programa</label>
                    <select id="program_id" class="form-select" wire:model.live="program_id">
                        <option value="">Seleccione un programa</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}">{{ $program->nombre }} - {{ $program->nivelEducativo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="subject_id" class="form-label">Materia (Opcional)</label>
                    <select id="subject_id" class="form-select" wire:model.live="subject_id" {{ !$program_id ? 'disabled' : '' }}>
                        <option value="">Todas las materias</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button wire:click="exportarExcel" class="btn btn-success" {{ $reportData->isEmpty() ? 'disabled' : '' }}>
                        <i class="ri ri-file-excel-2-line me-1"></i>Exportar Excel
                    </button>
                </div>
            </div>

            @if($evaluation_period_id && $program_id)
                <!-- Estadísticas Generales -->
                @if(!empty($statistics))
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">
                                        <i class="ri ri-bar-chart-2-line me-2"></i>Estadísticas Generales
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-2">
                                            <div class="border-end">
                                                <h4 class="text-primary">{{ $statistics['total_students'] }}</h4>
                                                <p class="text-muted mb-0">Total Estudiantes</p>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="border-end">
                                                <h4 class="text-success">{{ $statistics['approved_count'] }}</h4>
                                                <p class="text-muted mb-0">Aprobados</p>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="border-end">
                                                <h4 class="text-danger">{{ $statistics['failed_count'] }}</h4>
                                                <p class="text-muted mb-0">Reprobados</p>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="border-end">
                                                <h4 class="text-info">{{ $statistics['approval_rate'] }}%</h4>
                                                <p class="text-muted mb-0">Tasa Aprobación</p>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="border-end">
                                                <h4 class="text-warning">{{ $statistics['average_grade'] }}</h4>
                                                <p class="text-muted mb-0">Promedio General</p>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <h4 class="text-secondary">{{ $statistics['highest_average'] }}</h4>
                                            <p class="text-muted mb-0">Máximo</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Tabla de Resultados -->
                @if($reportData->isNotEmpty())
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Código</th>
                                            <th>Nombre Completo</th>
                                            <th>Programa</th>
                                            <th class="text-center">Materias Evaluadas</th>
                                            <th class="text-center">Promedio</th>
                                            <th class="text-center">Estado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reportData as $data)
                                            <tr>
                                                <td>
                                                    <strong>{{ $data['student']->codigo }}</strong>
                                                </td>
                                                <td>
                                                    {{ $data['student']->nombres }} {{ $data['student']->apellidos }}
                                                </td>
                                                <td>
                                                    {{ $data['matricula']->programa->nombre }}
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-info">{{ $data['grade_count'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <strong>{{ number_format($data['average'], 2) }}</strong>
                                                </td>
                                                <td class="text-center">
                                                    @if($data['status'] === 'Aprobado')
                                                        <span class="badge bg-success">
                                                            <i class="ri ri-check-line me-1"></i>Aprobado
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger">
                                                            <i class="ri ri-close-line me-1"></i>Reprobado
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('admin.grades.student', $data['student']->id) }}" 
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="Ver detalles">
                                                        <i class="ri ri-eye-line"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info text-center">
                                <i class="ri ri-information-line me-2"></i>
                                No se encontraron datos para los filtros seleccionados.
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-warning text-center">
                            <i class="ri ri-filter-3-line me-2"></i>
                            Por favor seleccione un período de evaluación y un programa para generar el reporte.
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>