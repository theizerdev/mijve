<div>
    <div class="mb-4">
        <div class="row">
            <div class="col-md-6">
                <h4 class="card-title">Detalles del Aula</h4>
                <p class="card-title-desc">Información completa del aula: {{ $classroom->nombre }}</p>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.classrooms.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <a href="{{ route('admin.classrooms.edit', $classroom->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Editar
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nombre del Aula</label>
                                <p class="text-muted">{{ $classroom->nombre }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Código</label>
                                <p class="text-muted">{{ $classroom->codigo }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tipo de Aula</label>
                                <p class="text-muted">
                                    @switch($classroom->tipo_aula)
                                        @case('regular')
                                            <i class="fas fa-chalkboard-teacher"></i> Regular
                                            @break
                                        @case('laboratorio')
                                            <i class="fas fa-flask"></i> Laboratorio
                                            @break
                                        @case('taller')
                                            <i class="fas fa-tools"></i> Taller
                                            @break
                                        @case('auditorio')
                                            <i class="fas fa-theater-masks"></i> Auditorio
                                            @break
                                        @case('biblioteca')
                                            <i class="fas fa-book"></i> Biblioteca
                                            @break
                                        @default
                                            <i class="fas fa-question"></i> Otro
                                    @endswitch
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Capacidad</label>
                                <p class="text-muted">{{ $classroom->capacidad }} estudiantes</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Ubicación</label>
                                <p class="text-muted">{{ $classroom->ubicacion ?? 'No especificada' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Estado</label>
                                <p>
                                    @if($classroom->is_active)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Inactivo</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($classroom->recursos && count($classroom->recursos) > 0)
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Recursos Disponibles</label>
                                <div>
                                    @foreach($classroom->recursos as $recurso)
                                        <span class="badge bg-info me-1 mb-1">
                                            @switch($recurso)
                                                @case('proyector')
                                                    <i class="fas fa-projector"></i> Proyector
                                                    @break
                                                @case('pizarra')
                                                    <i class="fas fa-chalkboard"></i> Pizarra
                                                    @break
                                                @case('aire_acondicionado')
                                                    <i class="fas fa-snowflake"></i> Aire Acondicionado
                                                    @break
                                                @case('computadoras')
                                                    <i class="fas fa-desktop"></i> Computadoras
                                                    @break
                                                @default
                                                    <i class="fas fa-check"></i> {{ ucfirst($recurso) }}
                                            @endswitch
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($classroom->empresa || $classroom->sucursal)
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Empresa</label>
                                <p class="text-muted">{{ $classroom->empresa->nombre ?? 'No asignada' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Sucursal</label>
                                <p class="text-muted">{{ $classroom->sucursal->nombre ?? 'No asignada' }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Creado por</label>
                                <p class="text-muted">{{ optional($classroom->created_by_user)->name ?? 'Sistema' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Actualizado por</label>
                                <p class="text-muted">{{ optional($classroom->updated_by_user)->name ?? 'Sistema' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Estadísticas del Aula</h5>
                    
                    <div class="mb-3">
                        <label class="form-label">Secciones Asignadas</label>
                        <p class="text-muted">{{ $classroom->sections->count() }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Horarios Activos</label>
                        <p class="text-muted">{{ $classroom->schedules->count() }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Fecha de Creación</label>
                        <p class="text-muted">{{ $classroom->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    @if($classroom->updated_at != $classroom->created_at)
                    <div class="mb-3">
                        <label class="form-label">Última Actualización</label>
                        <p class="text-muted">{{ $classroom->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($schedulesByDay->isNotEmpty())
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Horarios del Aula</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Día</th>
                                    <th>Hora</th>
                                    <th>Sección</th>
                                    <th>Materia</th>
                                    <th>Profesor</th>
                                    <th>Período</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($schedulesByDay as $day => $schedules)
                                    @foreach($schedules as $schedule)
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">
                                                {{ ucfirst($day) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($schedule->hora_inicio)->format('H:i') }} - 
                                            {{ \Carbon\Carbon::parse($schedule->hora_fin)->format('H:i') }}
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $schedule->section->nombre }}
                                            </span>
                                        </td>
                                        <td>{{ $schedule->subject->name }}</td>
                                        <td>{{ $schedule->teacher->user->name ?? 'Sin asignar' }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($schedule->fecha_inicio)->format('d/m') }} - 
                                            {{ \Carbon\Carbon::parse($schedule->fecha_fin)->format('d/m') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Este aula no tiene horarios asignados actualmente.
            </div>
        </div>
    </div>
    @endif
</div>