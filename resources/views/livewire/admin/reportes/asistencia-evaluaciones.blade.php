<div>
    <div class="container-fluid">
        <!-- Encabezado -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Reporte de Asistencia y Evaluaciones</h4>
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
                        <h5 class="card-title mb-3">Filtros</h5>
                        <div class="row">
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
                                <label for="subject_id" class="form-label">Materia</label>
                                <select wire:model="subject_id" class="form-select" id="subject_id">
                                    <option value="">Seleccione una materia</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->nombre }} - {{ $subject->programa->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="teacher_id" class="form-label">Docente</label>
                                <select wire:model="teacher_id" class="form-select" id="teacher_id">
                                    <option value="">Todos los docentes</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="evaluation_type_id" class="form-label">Tipo de Evaluación</label>
                                <select wire:model="evaluation_type_id" class="form-select" id="evaluation_type_id">
                                    <option value="">Todos los tipos</option>
                                    @foreach($evaluationTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas Generales -->
        @if(!empty($statistics))
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Estadísticas de Asistencia</h5>
                            <button wire:click="exportarExcel" class="btn btn-success btn-sm">
                                <i class="fas fa-file-excel"></i> Exportar Excel
                            </button>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card border border-primary">
                                    <div class="card-body text-center">
                                        <h4 class="text-primary">{{ $statistics['total_students'] }}</h4>
                                        <p class="text-muted mb-0">Total Estudiantes</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border border-success">
                                    <div class="card-body text-center">
                                        <h4 class="text-success">{{ $statistics['total_present'] }}</h4>
                                        <p class="text-muted mb-0">Presentes</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border border-warning">
                                    <div class="card-body text-center">
                                        <h4 class="text-warning">{{ $statistics['total_absent'] }}</h4>
                                        <p class="text-muted mb-0">Ausentes</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border border-info">
                                    <div class="card-body text-center">
                                        <h4 class="text-info">{{ $statistics['total_exempt'] }}</h4>
                                        <p class="text-muted mb-0">Exentos</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h3 class="text-dark">{{ $statistics['overall_attendance_rate'] }}%</h3>
                                        <p class="text-muted mb-0">Tasa de Asistencia General</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h5 class="text-dark">{{ $statistics['subject_name'] }}</h5>
                                        <p class="text-muted mb-0">Materia</p>
                                        <small class="text-muted">{{ $statistics['period_name'] }} - {{ $statistics['teacher_name'] }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Tabla de Asistencia por Estudiante -->
        @if(!empty($attendanceData))
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Detalle de Asistencia por Estudiante</h5>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Estudiante</th>
                                        <th>Presentes</th>
                                        <th>Ausentes</th>
                                        <th>Exentos</th>
                                        <th>Tasa Asistencia</th>
                                        <th>Detalle Evaluaciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attendanceData as $data)
                                    <tr>
                                        <td>
                                            <strong>{{ $data['student']->nombres }} {{ $data['student']->apellidos }}</strong>
                                            <br>
                                            <small class="text-muted">Código: {{ $data['student']->codigo }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success">{{ $data['attendance_count'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-warning">{{ $data['absence_count'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ $data['exemption_count'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $data['attendance_rate'] >= 80 ? 'bg-success' : ($data['attendance_rate'] >= 60 ? 'bg-warning' : 'bg-danger') }}">
                                                {{ $data['attendance_rate'] }}%
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($data['evaluations'] as $eval)
                                                    @if($eval['attendance'] == 'Presente')
                                                        <span class="badge bg-success" style="font-size: 0.7rem;" 
                                                              title="{{ $eval['evaluation']->name }}: {{ $eval['score'] ?? 'Sin nota' }}">
                                                            P
                                                        </span>
                                                    @elseif($eval['attendance'] == 'Ausente')
                                                        <span class="badge bg-warning" style="font-size: 0.7rem;"
                                                              title="{{ $eval['evaluation']->name }}: Ausente">
                                                            A
                                                        </span>
                                                    @elseif($eval['attendance'] == 'Exento')
                                                        <span class="badge bg-info" style="font-size: 0.7rem;"
                                                              title="{{ $eval['evaluation']->name }}: Exento">
                                                            E
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary" style="font-size: 0.7rem;"
                                                              title="{{ $eval['evaluation']->name }}: No registrado">
                                                            -
                                                        </span>
                                                    @endif
                                                @endforeach
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
        </div>
        @else
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay datos para mostrar</h5>
                        <p class="text-muted">Seleccione un período de evaluación y una materia para ver el reporte de asistencia.</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>