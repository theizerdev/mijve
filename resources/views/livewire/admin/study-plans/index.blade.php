<div>
    <!-- Encabezado con estadísticas -->
    <div class="row mb-4">
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ri ri-file-list-3-line ri-22px"></i>
                            </span>
                        </div>
                        <h4 class="ms-1 mb-0">{{ $this->getStatsProperty()['total'] }}</h4>
                    </div>
                    <p class="mb-0">Total Planes</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ri ri-check-line ri-22px"></i>
                            </span>
                        </div>
                        <h4 class="ms-1 mb-0">{{ $this->getStatsProperty()['activos'] }}</h4>
                    </div>
                    <p class="mb-0">Planes Activos</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="ri ri-close-line ri-22px"></i>
                            </span>
                        </div>
                        <h4 class="ms-1 mb-0">{{ $this->getStatsProperty()['inactivos'] }}</h4>
                    </div>
                    <p class="mb-0">Planes Inactivos</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="ri ri-star-line ri-22px"></i>
                            </span>
                        </div>
                        <h4 class="ms-1 mb-0">{{ $this->getStatsProperty()['por_defecto'] }}</h4>
                    </div>
                    <p class="mb-0">Planes por Defecto</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Filtros de Búsqueda</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Búsqueda</label>
                    <input type="text" class="form-control" id="search" wire:model.debounce.300ms="search" placeholder="Buscar por nombre o código...">
                </div>
                <div class="col-md-3">
                    <label for="program_id" class="form-label">Programa</label>
                    <select class="form-select" id="program_id" wire:model="program_id">
                        <option value="">Todos los programas</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}">{{ $program->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="educational_level_id" class="form-label">Nivel Educativo</label>
                    <select class="form-select" id="educational_level_id" wire:model="educational_level_id">
                        <option value="">Todos los niveles</option>
                        @foreach($educationalLevels as $level)
                            <option value="{{ $level->id }}">{{ $level->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Estado</label>
                    <select class="form-select" id="status" wire:model="status">
                        <option value="">Todos</option>
                        <option value="active">Activo</option>
                        <option value="inactive">Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <button class="btn btn-outline-secondary" wire:click="clearFilters">
                        <i class="ri ri-refresh-line me-1"></i>Limpiar Filtros
                    </button>
                    @can('export study_plans')
                    <button class="btn btn-outline-primary ms-2" wire:click="export">
                        <i class="ri ri-download-line me-1"></i>Exportar
                    </button>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de planes de estudio -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Planes de Estudio</h5>
            @can('create study_plans')
            <a href="{{ route('admin.study-plans.create') }}" class="btn btn-primary">
                <i class="ri ri-add-line me-1"></i>Crear Plan de Estudio
            </a>
            @endcan
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th wire:click="sortBy('code')" style="cursor: pointer;">
                                Código
                                @if($sortBy === 'code')
                                    <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('name')" style="cursor: pointer;">
                                Nombre
                                @if($sortBy === 'name')
                                    <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                @endif
                            </th>
                            <th>Programa</th>
                            <th>Nivel Educativo</th>
                            <th wire:click="sortBy('total_credits')" style="cursor: pointer;">
                                Créditos
                                @if($sortBy === 'total_credits')
                                    <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                @endif
                            </th>
                            <th>Estado</th>
                            <th>Por Defecto</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($studyPlans as $plan)
                        <tr>
                            <td>{{ $plan->code }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div class="fw-semibold">{{ $plan->name }}</div>
                                        @if($plan->description)
                                            <small class="text-muted">{{ Str::limit($plan->description, 50) }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $plan->program->nombre }}</td>
                            <td>{{ $plan->educationalLevel->nombre }}</td>
                            <td>{{ $plan->total_credits ?? 0 }}</td>
                            <td>
                                <span class="badge bg-{{ $plan->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ $plan->status === 'active' ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                @if($plan->is_default)
                                    <span class="badge bg-info">
                                        <i class="ri ri-star-line"></i> Por Defecto
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="ri ri-more-2-line"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @can('view study_plans')
                                        <a class="dropdown-item" href="{{ route('admin.study-plans.show', $plan) }}">
                                            <i class="ri ri-eye-line me-1"></i>Ver Detalles
                                        </a>
                                        @endcan
                                        @can('edit study_plans')
                                        <a class="dropdown-item" href="{{ route('admin.study-plans.edit', $plan) }}">
                                            <i class="ri ri-pencil-line me-1"></i>Editar
                                        </a>
                                        @endcan
                                        @can('edit study_plans')
                                        <button class="dropdown-item" wire:click="toggleStatus({{ $plan->id }})">
                                            <i class="ri ri-toggle-line me-1"></i>
                                            {{ $plan->status === 'active' ? 'Desactivar' : 'Activar' }}
                                        </button>
                                        @endcan
                                        @can('delete study_plans')
                                        <button class="dropdown-item" wire:click="delete({{ $plan->id }})" 
                                                onclick="confirm('¿Está seguro de eliminar este plan de estudio?') || event.stopImmediatePropagation()">
                                            <i class="ri ri-delete-bin-line me-1"></i>Eliminar
                                        </button>
                                        @endcan
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="ri ri-file-list-3-line ri-2x mb-2"></i>
                                <p>No se encontraron planes de estudio</p>
                                @can('create study_plans')
                                <a href="{{ route('admin.study-plans.create') }}" class="btn btn-primary">
                                    <i class="ri ri-add-line me-1"></i>Crear Primer Plan
                                </a>
                                @endcan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    <select class="form-select form-select-sm" wire:model="perPage" style="width: auto;">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="ms-2 text-muted">registros por página</span>
                </div>
                <div>
                    {{ $studyPlans->links() }}
                </div>
            </div>
        </div>
    </div>
</div>