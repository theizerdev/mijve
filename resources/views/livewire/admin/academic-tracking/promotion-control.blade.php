<div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Control de Promoción</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Control de Promoción</li>
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
                            <p class="text-muted fw-medium">Total Estudiantes</p>
                            <h4 class="mb-0">{{ $stats['total_students'] }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                <span class="avatar-title">
                                    <i class="bx bx-user font-size-24"></i>
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
                            <p class="text-muted fw-medium">Promovidos</p>
                            <h4 class="mb-0">{{ $stats['promoted_students'] }}</h4>
                            <small class="text-success">{{ $stats['approved_percentage'] }}%</small>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                <span class="avatar-title">
                                    <i class="bx bx-up-arrow-circle font-size-24"></i>
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
                            <p class="text-muted fw-medium">Retenidos</p>
                            <h4 class="mb-0">{{ $stats['repeated_students'] }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                                <span class="avatar-title">
                                    <i class="bx bx-refresh font-size-24"></i>
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
                            <h4 class="mb-0">{{ $stats['in_recovery_students'] }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                                <span class="avatar-title">
                                    <i class="bx bx-time font-size-24"></i>
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
                            <label class="form-label">Período Escolar</label>
                            <select class="form-select" wire:model="selectedPeriodId">
                                <option value="">Seleccione período</option>
                                @foreach($schoolPeriods as $period)
                                    <option value="{{ $period->id }}">{{ $period->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Programa</label>
                            <select class="form-select" wire:model="selectedProgramId">
                                <option value="">Seleccione programa</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}">{{ $program->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Nivel Educativo</label>
                            <select class="form-select" wire:model="selectedLevelId">
                                <option value="">Seleccione nivel</option>
                                @foreach($educationalLevels as $level)
                                    <option value="{{ $level->id }}">{{ $level->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Grado</label>
                            <select class="form-select" wire:model="selectedGrade">
                                <option value="">Seleccione grado</option>
                                @foreach($grades as $grade)
                                    <option value="{{ $grade }}">{{ $grade }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label class="form-label">Sección</label>
                            <select class="form-select" wire:model="selectedSection">
                                <option value="">Seleccione sección</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section }}">{{ $section }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Estado de Promoción</label>
                            <select class="form-select" wire:model="promotionStatus">
                                <option value="">Todos</option>
                                <option value="promoted">Promovidos</option>
                                <option value="repeated">Retenidos</option>
                                <option value="pending">Pendientes</option>
                                <option value="in_recovery">En Recuperación</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Búsqueda</label>
                            <input type="text" class="form-control" wire:model.debounce.300ms="search" placeholder="Buscar estudiante...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Opciones</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="showOnlyPending" wire:model="showOnlyPending">
                                <label class="form-check-label" for="showOnlyPending">Solo pendientes</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Estudiantes -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Estudiantes</h4>
                    <div>
                        <button class="btn btn-success btn-sm" wire:click="bulkPromote">
                            <i class="bx bx-check-circle me-1"></i> Promoción Masiva
                        </button>
                        <button class="btn btn-info btn-sm" wire:click="generatePromotionReport">
                            <i class="bx bx-file me-1"></i> Generar Reporte
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Estudiante</th>
                                    <th>Código</th>
                                    <th>Programa</th>
                                    <th>Nivel</th>
                                    <th>Grado/Sección</th>
                                    <th>Materias Aprobadas</th>
                                    <th>Materias Reprobadas</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                @php
                                    $studentRecords = $student->academicRecords;
                                    $totalSubjects = $studentRecords->count();
                                    $approvedSubjects = $studentRecords->where('approved', true)->count();
                                    $failedSubjects = $studentRecords->where('approved', false)->count();
                                    $inRecoverySubjects = $studentRecords->where('status', 'in_recovery')->count();
                                    $promoted = $studentRecords->where('promoted', true)->isNotEmpty();
                                    $repeated = $studentRecords->where('repeated', true)->isNotEmpty();
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $student->nombres }} {{ $student->apellidos }}</strong>
                                    </td>
                                    <td>{{ $student->codigo }}</td>
                                    <td>{{ $studentRecords->first()->program->nombre ?? '-' }}</td>
                                    <td>{{ $studentRecords->first()->educationalLevel->nombre ?? '-' }}</td>
                                    <td>{{ $studentRecords->first()->grade ?? '-' }} "{{ $studentRecords->first()->section ?? '-' }}"</td>
                                    <td>
                                        <span class="badge bg-success">{{ $approvedSubjects }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger">{{ $failedSubjects }}</span>
                                        @if($inRecoverySubjects > 0)
                                            <br><small class="text-warning">({{ $inRecoverySubjects }} en recuperación)</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($promoted)
                                            <span class="badge bg-success">Promovido</span>
                                        @elseif($repeated)
                                            <span class="badge bg-warning">Retenido</span>
                                        @elseif($inRecoverySubjects > 0)
                                            <span class="badge bg-info">En Recuperación</span>
                                        @else
                                            <span class="badge bg-secondary">Pendiente</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                @if(!$promoted && !$repeated)
                                                    <a class="dropdown-item" href="#" wire:click.prevent="promoteStudent({{ $student->id }})">
                                                        <i class="bx bx-check-circle me-1"></i> Promover
                                                    </a>
                                                    <a class="dropdown-item" href="#" wire:click.prevent="repeatStudent({{ $student->id }})">
                                                        <i class="bx bx-refresh me-1"></i> Retener
                                                    </a>
                                                @endif
                                                <a class="dropdown-item" href="{{ route('admin.students.historico', $student->id) }}">
                                                    <i class="bx bx-show me-1"></i> Ver Historial
                                                </a>
                                                <a class="dropdown-item" href="#" wire:click.prevent="">
                                                    <i class="bx bx-printer me-1"></i> Imprimir Reporte
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">
                                        No se encontraron estudiantes con los filtros aplicados
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $students->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>