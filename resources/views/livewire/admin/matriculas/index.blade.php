<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-start border-primary border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Matrículas</h6>
                            <h2 class="mb-0">{{ $totalMatriculas }}</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="ri ri-graduation-cap-line text-primary" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-success border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Activas</h6>
                            <h2 class="mb-0">{{ $matriculasActivas }}</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="ri ri-check-circle-line text-success" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-warning border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Inactivas</h6>
                            <h2 class="mb-0">{{ $matriculasInactivas }}</h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="ri ri-pause-circle-line text-warning" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-info border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Ingresos Totales</h6>
                            <h2 class="mb-0">${{ number_format($ingresosTotales, 0) }}</h2>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="ri ri-money-dollar-circle-line text-info" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Lista de Matrículas</h5>
                            <p class="mb-0">Administra las matrículas de los estudiantes</p>
                        </div>
                        @can('create matriculas')
                        <div>
                            <a href="{{ route('admin.matriculas.create') }}" class="btn btn-primary">
                                <i class="ri ri-add-line"></i> Nueva Matrícula
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card-header border-bottom">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Buscar</label>
                            <input type="text" class="form-control" placeholder="Nombre, apellido o DNI del estudiante..."
                                   wire:model.live.debounce.300ms="search">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" wire:model.live="status">
                                <option value="">Todos</option>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                                <option value="graduado">Graduado</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Mostrar</label>
                            <select class="form-select" wire:model.live="perPage">
                                <option value="10">10 por página</option>
                                <option value="25">25 por página</option>
                                <option value="50">50 por página</option>
                                <option value="100">100 por página</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end gap-2">
                            <button type="button" class="btn btn-label-secondary" wire:click="clearFilters">
                                <i class="ri ri-eraser-line"></i> Limpiar
                            </button>
                            <button type="button" class="btn btn-label-success" wire:click="export">
                                <i class="mdi mdi-file-excel"></i> Exportar
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-datatable table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th wire:click="sortBy('students.nombres')" style="cursor: pointer;">
                            Estudiante
                            @if($sortBy === 'students.nombres')
                                <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('programas.nombre')" style="cursor: pointer;">
                            Programa
                            @if($sortBy === 'programas.nombre')
                                <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('school_periods.name')" style="cursor: pointer;">
                            Período
                            @if($sortBy === 'school_periods.name')
                                <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('fecha_matricula')" style="cursor: pointer;">
                            Fecha
                            @if($sortBy === 'fecha_matricula')
                                <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                            @endif
                        </th>
                        <th>Costo Total</th>
                        <th wire:click="sortBy('estado')" style="cursor: pointer;">
                            Estado
                            @if($sortBy === 'estado')
                                <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                            @endif
                        </th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($matriculas as $matricula)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded bg-label-primary">{{ substr($matricula->estudiante->nombres ?? '', 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $matricula->estudiante->nombres ?? '' }} {{ $matricula->estudiante->apellidos ?? '' }}</h6>
                                        <small class="text-muted">{{ $matricula->estudiante->documento_identidad ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>{{ $matricula->programa->nombre ?? '' }}</div>
                                @if($matricula->programa && !$matricula->programa->activo)
                                    <small class="text-muted">Programa inactivo</small>
                                @endif
                            </td>
                            <td>{{ $matricula->periodo->name ?? '' }}</td>
                            <td>{{ $matricula->fecha_matricula->format('d/m/Y') }}</td>
                            <td>${{ number_format($matricula->costo, 2) }}</td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"
                                           id="statusSwitch{{ $matricula->id }}"
                                           {{ $matricula->estado === 'activo' ? 'checked' : '' }}
                                           @can('edit matriculas') wire:click="toggleStatus({{ $matricula->id }})" @endcan>
                                    <label class="form-check-label" for="statusSwitch{{ $matricula->id }}">
                                        {{ ucfirst($matricula->estado) }}
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="ri ri-more-2-line"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @can('view matriculas')
                                        <a class="dropdown-item" href="{{ route('admin.matriculas.show', $matricula) }}">
                                            <i class="ri ri-eye-line me-1"></i> Ver
                                        </a>
                                        @endcan
                                        @can('edit matriculas')
                                        <a class="dropdown-item" href="{{ route('admin.matriculas.edit', $matricula) }}">
                                            <i class="ri ri-pencil-line me-1"></i> Editar
                                        </a>
                                        @endcan
                                        @can('delete matriculas')
                                        <button type="button" class="dropdown-item text-danger"
                                                wire:click="delete({{ $matricula->id }})"
                                                wire:confirm="¿Estás seguro de eliminar esta matrícula?">
                                            <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                        </button>
                                        @endcan
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No se encontraron matrículas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

                <div class="card-footer">
                   {{ $matriculas->links('vendor.pagination.materialize') }}
                </div>
            </div>
        </div>
    </div>
</div>
