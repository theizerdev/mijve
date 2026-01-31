<div>
    <div class="container-fluid">
        <!-- Encabezado -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Boletines de Calificaciones</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            @foreach($this->getBreadcrumb() as $item)
                                <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}">
                                    @if(!$loop->last)
                                        <a href="{{ $item['route'] }}">{{ $item['name'] }}</a>
                                    @else
                                        {{ $item['name'] }}
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Filtros del Boletín</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="student_id" class="form-label">Estudiante</label>
                                <select wire:model="student_id" class="form-select" id="student_id">
                                    <option value="">Seleccione un estudiante</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}">{{ $student->nombres }} {{ $student->apellidos }} - {{ $student->codigo }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="evaluation_period_id" class="form-label">Período de Evaluación</label>
                                <select wire:model="evaluation_period_id" class="form-select" id="evaluation_period_id">
                                    <option value="">Seleccione un período</option>
                                    @foreach($evaluationPeriods as $period)
                                        <option value="{{ $period->id }}">{{ $period->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="program_id" class="form-label">Programa</label>
                                <select wire:model="program_id" class="form-select" id="program_id">
                                    <option value="">Todos los programas</option>
                                    @foreach($programs as $program)
                                        <option value="{{ $program->id }}">{{ $program->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button wire:click="generateBoletin" class="btn btn-primary w-100" @if(!$student_id || !$evaluation_period_id) disabled @endif>
                                    <i class="fas fa-search"></i> Generar Boletín
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Boletín del Estudiante -->
        @if(!empty($boletinData))
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title mb-1">Boletín de Calificaciones</h5>
                                <small class="text-muted">{{ $boletinData['student_name'] }} - {{ $boletinData['period_name'] }}</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button wire:click="imprimirBoletin" class="btn btn-outline-secondary">
                                    <i class="fas fa-print"></i> Imprimir
                                </button>
                                <button wire:click="exportarExcel" class="btn btn-success">
                                    <i class="fas fa-file-excel"></i> Exportar Excel
                                </button>
                            </div>
                        </div>

                        <!-- Información del Estudiante -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card border border-primary">
                                    <div class="card-body text-center">
                                        <h5 class="text-primary">{{ $boletinData['student_name'] }}</h5>
                                        <p class="text-muted mb-0">{{ $boletinData['student_code'] }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border border-info">
                                    <div class="card-body text-center">
                                        <h5 class="text-info">{{ $boletinData['program_name'] }}</h5>
                                        <p class="text-muted mb-0">Programa</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border border-success">
                                    <div class="card-body text-center">
                                        <h3 class="text-success">{{ $boletinData['overall_average'] }}</h3>
                                        <p class="text-muted mb-0">Promedio General</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla de Calificaciones por Materia -->
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Materia</th>
                                        <th class="text-center">Docente</th>
                                        <th class="text-center">Tipo Evaluación</th>
                                        <th class="text-center">Calificación</th>
                                        <th class="text-center">Estado</th>
                                        <th class="text-center">Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($boletinData['grades'] as $grade)
                                    <tr>
                                        <td>
                                            <strong>{{ $grade['subject_name'] }}</strong>
                                            @if($grade['subject_description'])
                                                <br><small class="text-muted">{{ $grade['subject_description'] }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            {{ $grade['teacher_name'] }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $grade['evaluation_type'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if($grade['score'] !== null)
                                                <span class="badge {{ $grade['score'] >= 10 ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $grade['score'] }}
                                                </span>
                                            @else
                                                <span class="badge bg-warning">Pendiente</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($grade['status'] == 'graded')
                                                @if($grade['score'] >= 10)
                                                    <span class="badge bg-success">Aprobado</span>
                                                @else
                                                    <span class="badge bg-danger">Reprobado</span>
                                                @endif
                                            @elseif($grade['status'] == 'absent')
                                                <span class="badge bg-warning">Ausente</span>
                                            @elseif($grade['status'] == 'exempt')
                                                <span class="badge bg-info">Exento</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($grade['status']) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            {{ $grade['evaluation_date'] }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="3" class="text-end">Resumen del Período:</th>
                                        <th class="text-center">{{ $boletinData['total_evaluations'] }} Evaluaciones</th>
                                        <th class="text-center">
                                            <span class="badge bg-success">{{ $boletinData['approved_count'] }} Aprobadas</span>
                                            <br>
                                            <span class="badge bg-danger">{{ $boletinData['failed_count'] }} Reprobadas</span>
                                        </th>
                                        <th class="text-center">
                                            <strong>Promedio: {{ $boletinData['overall_average'] }}</strong>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Observaciones -->
                        @if(!empty($boletinData['observations']))
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border border-warning">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="mb-0">
                                            <i class="fas fa-exclamation-triangle"></i> Observaciones
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <ul class="mb-0">
                                            @foreach($boletinData['observations'] as $observation)
                                                <li>{{ $observation }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Boletín de Calificaciones</h5>
                        <p class="text-muted">Seleccione un estudiante y período de evaluación para generar el boletín.</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>