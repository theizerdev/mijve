<div>
    <div class="card shadow-sm">
        <div class="card-header border-bottom">
            <h5 class="mb-0">
                <i class="ri ri-bar-chart-box-line me-2"></i>Estadísticas de Calificaciones por Materia
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
                    <label for="subject_id" class="form-label">Materia</label>
                    <select id="subject_id" class="form-select" wire:model.live="subject_id">
                        <option value="">Seleccione una materia</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->nombre }} - {{ $subject->programa->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="teacher_id" class="form-label">Docente (Opcional)</label>
                    <select id="teacher_id" class="form-select" wire:model.live="teacher_id">
                        <option value="">Todos los docentes</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button wire:click="exportarExcel" class="btn btn-success" {{ empty($statistics) ? 'disabled' : '' }}>
                        <i class="ri ri-file-excel-2-line me-1"></i>Exportar Excel
                    </button>
                </div>
            </div>

            @if($subject_id && $evaluation_period_id)
                <!-- Estadísticas Generales -->
                @if(!empty($statistics))
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">
                                        <i class="ri ri-bar-chart-2-line me-2"></i>Estadísticas Generales - {{ $statistics['subject_name'] }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-2">
                                            <div class="border-end">
                                                <h4 class="text-info">{{ $statistics['total_evaluations'] }}</h4>
                                                <p class="text-muted mb-0">Total Evaluaciones</p>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="border-end">
                                                <h4 class="text-primary">{{ $statistics['total_grades'] }}</h4>
                                                <p class="text-muted mb-0">Total Calificaciones</p>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="border-end">
                                                <h4 class="text-warning">{{ $statistics['average_grade'] }}</h4>
                                                <p class="text-muted mb-0">Promedio General</p>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="border-end">
                                                <h4 class="text-success">{{ $statistics['approval_rate'] }}%</h4>
                                                <p class="text-muted mb-0">Tasa Aprobación</p>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="border-end">
                                                <h4 class="text-success">{{ $statistics['approved_count'] }}</h4>
                                                <p class="text-muted mb-0">Aprobados</p>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <h4 class="text-danger">{{ $statistics['failed_count'] }}</h4>
                                            <p class="text-muted mb-0">Reprobados</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Distribución de Calificaciones -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">
                                        <i class="ri ri-pie-chart-2-line me-2"></i>Distribución de Calificaciones
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Rango</th>
                                                    <th class="text-center">Cantidad</th>
                                                    <th class="text-center">Porcentaje</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $total = array_sum($gradeDistribution);
                                                @endphp
                                                @foreach($gradeDistribution as $range => $count)
                                                    @php
                                                        $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                                                        $progressClass = $count > 0 ? 'bg-primary' : 'bg-secondary';
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $range }}</td>
                                                        <td class="text-center">
                                                            <span class="badge bg-info">{{ $count }}</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="progress" style="height: 20px;">
                                                                <div class="progress-bar {{ $progressClass }}" 
                                                                     role="progressbar" 
                                                                     style="width: {{ $percentage }}%"
                                                                     aria-valuenow="{{ $percentage }}" 
                                                                     aria-valuemin="0" 
                                                                     aria-valuemax="100">
                                                                    {{ $percentage }}%
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Estudiantes Destacados -->
                        <div class="col-md-6">
                            <div class="row">
                                <!-- Top 5 Estudiantes -->
                                <div class="col-md-12 mb-3">
                                    <div class="card border-success">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0">
                                                <i class="ri ri-trophy-line me-2"></i>Estudiantes Destacados (Top 5)
                                            </h6>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-sm mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Nombre</th>
                                                            <th class="text-center">Promedio</th>
                                                            <th class="text-center">Calificaciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($topStudents as $index => $studentData)
                                                            <tr>
                                                                <td>
                                                                    <span class="badge bg-warning text-dark">{{ $index + 1 }}</span>
                                                                </td>
                                                                <td>
                                                                    <strong>{{ $studentData['student']->nombres }} {{ $studentData['student']->apellidos }}</strong>
                                                                    <br>
                                                                    <small class="text-muted">{{ $studentData['student']->codigo }}</small>
                                                                </td>
                                                                <td class="text-center">
                                                                    <span class="badge bg-success">{{ number_format($studentData['average'], 2) }}</span>
                                                                </td>
                                                                <td class="text-center">
                                                                    <span class="badge bg-info">{{ $studentData['total_grades'] }}</span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bottom 5 Estudiantes -->
                                <div class="col-md-12">
                                    <div class="card border-danger">
                                        <div class="card-header bg-danger text-white">
                                            <h6 class="mb-0">
                                                <i class="ri ri-alert-line me-2"></i>Estudiantes con Bajo Rendimiento (Bottom 5)
                                            </h6>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-sm mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Nombre</th>
                                                            <th class="text-center">Promedio</th>
                                                            <th class="text-center">Calificaciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($bottomStudents as $index => $studentData)
                                                            <tr>
                                                                <td>
                                                                    <span class="badge bg-secondary">{{ $index + 1 }}</span>
                                                                </td>
                                                                <td>
                                                                    <strong>{{ $studentData['student']->nombres }} {{ $studentData['student']->apellidos }}</strong>
                                                                    <br>
                                                                    <small class="text-muted">{{ $studentData['student']->codigo }}</small>
                                                                </td>
                                                                <td class="text-center">
                                                                    <span class="badge bg-danger">{{ number_format($studentData['average'], 2) }}</span>
                                                                </td>
                                                                <td class="text-center">
                                                                    <span class="badge bg-info">{{ $studentData['total_grades'] }}</span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-warning text-center">
                            <i class="ri ri-filter-3-line me-2"></i>
                            Por favor seleccione un período de evaluación y una materia para generar el reporte.
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>