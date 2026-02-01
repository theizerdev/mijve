<div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Historial Académico</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Historial Académico</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    @if($this->showStatistics)
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium">Total Registros</p>
                            <h4 class="mb-0">{{ $stats['total'] }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                <span class="avatar-title">
                                    <i class="bx bx-book-bookmark font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium">Aprobados</p>
                            <h4 class="mb-0">{{ $stats['approved'] }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                <span class="avatar-title">
                                    <i class="bx bx-check-circle font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium">En Recuperación</p>
                            <h4 class="mb-0">{{ $stats['in_recovery'] }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                                <span class="avatar-title">
                                    <i class="bx bx-time font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium">Retirados</p>
                            <h4 class="mb-0">{{ $stats['withdrawn'] }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-danger">
                                <span class="avatar-title">
                                    <i class="bx bx-user-x font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Búsqueda</label>
                            <input type="text" class="form-control" wire:model.debounce.300ms="search" placeholder="Buscar estudiante, materia, período...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Período Escolar</label>
                            <select class="form-select" wire:model="selectedPeriodId">
                                <option value="">Todos los períodos</option>
                                @foreach($schoolPeriods as $period)
                                    <option value="{{ $period->id }}">{{ $period->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Materia</label>
                            <select class="form-select" wire:model="selectedSubjectId">
                                <option value="">Todas las materias</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" wire:model="selectedStatus">
                                <option value="">Todos los estados</option>
                                <option value="enrolled">Matriculado</option>
                                <option value="in_recovery">En Recuperación</option>
                                <option value="approved">Aprobado</option>
                                <option value="failed">Reprobado</option>
                                <option value="withdrawn">Retirado</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="showGrades" wire:model="showGrades">
                                <label class="form-check-label" for="showGrades">Mostrar Calificaciones</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="showObservations" wire:model="showObservations">
                                <label class="form-check-label" for="showObservations">Mostrar Observaciones</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="showRecovery" wire:model="showRecovery">
                                <label class="form-check-label" for="showRecovery">Mostrar Recuperación</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Historial -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Registros Académicos</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Estudiante</th>
                                    <th>Período</th>
                                    <th>Programa</th>
                                    <th>Nivel</th>
                                    <th>Materia</th>
                                    <th>Grado/Sección</th>
                                    @if($showGrades)
                                    <th>Calificación</th>
                                    @endif
                                    <th>Estado</th>
                                    @if($showRecovery)
                                    <th>Recuperación</th>
                                    @endif
                                    @if($showObservations)
                                    <th>Observaciones</th>
                                    @endif
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($academicRecords as $record)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $record->student->nombres }} {{ $record->student->apellidos }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $record->student->codigo }}</small>
                                        </div>
                                    </td>
                                    <td>{{ $record->schoolPeriod->nombre }}</td>
                                    <td>{{ $record->program->nombre }}</td>
                                    <td>{{ $record->educationalLevel->nombre }}</td>
                                    <td>{{ $record->subject->nombre }}</td>
                                    <td>{{ $record->grade }} "{{ $record->section }}"</td>
                                    @if($showGrades)
                                    <td>
                                        @if($record->final_grade !== null)
                                            <span class="badge bg-{{ $record->final_grade >= 10 ? 'success' : 'danger' }}">
                                                {{ number_format($record->final_grade, 2) }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Sin calificar</span>
                                        @endif
                                    </td>
                                    @endif
                                    <td>
                                        <span class="badge bg-{{ 
                                            $record->status == 'approved' ? 'success' : 
                                            ($record->status == 'failed' ? 'danger' : 
                                            ($record->status == 'in_recovery' ? 'warning' : 
                                            ($record->status == 'withdrawn' ? 'dark' : 'info')))
                                        }}">
                                            {{ ucfirst($record->status) }}
                                        </span>
                                        @if($record->approved)
                                            <i class="bx bx-check-circle text-success ms-1" title="Aprobado"></i>
                                        @endif
                                        @if($record->withdrawn)
                                            <i class="bx bx-user-x text-danger ms-1" title="Retirado"></i>
                                        @endif
                                    </td>
                                    @if($showRecovery)
                                    <td>
                                        @if($record->status == 'in_recovery')
                                            <div>
                                                <span class="badge bg-warning">En Recuperación</span>
                                                @if($record->recovery_period)
                                                    <br><small>{{ $record->recovery_period->name }}</small>
                                                @endif
                                            </div>
                                        @elseif($record->recovery_status)
                                            <span class="badge bg-{{ $record->recovery_status == 'approved' ? 'success' : 'danger' }}">
                                                {{ ucfirst($record->recovery_status) }}
                                                @if($record->recovery_grade !== null)
                                                    ({{ number_format($record->recovery_grade, 2) }})
                                                @endif
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    @endif
                                    @if($showObservations)
                                    <td>
                                        @if($record->observations)
                                            <small>{{ Str::limit($record->observations, 50) }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    @endif
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="#" wire:click.prevent="">
                                                    <i class="bx bx-show me-1"></i> Ver Detalles
                                                </a>
                                                <a class="dropdown-item" href="#" wire:click.prevent="">
                                                    <i class="bx bx-edit me-1"></i> Editar
                                                </a>
                                                <a class="dropdown-item" href="#" wire:click.prevent="">
                                                    <i class="bx bx-printer me-1"></i> Imprimir
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ 7 + ($showGrades ? 1 : 0) + ($showRecovery ? 1 : 0) + ($showObservations ? 1 : 0) }}" class="text-center">
                                        No se encontraron registros académicos
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $academicRecords->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-end gap-2">
                <button class="btn btn-primary" wire:click="export">
                    <i class="bx bx-export me-1"></i> Exportar
                </button>
                <button class="btn btn-info" wire:click="generateReport">
                    <i class="bx bx-file me-1"></i> Generar Reporte
                </button>
            </div>
        </div>
    </div>
</div>