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

    <div class="d-flex justify-content-between align-items-center mb-4">
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

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0">Filtros</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" id="search" placeholder="Nombre, apellido o DNI del estudiante...">
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">Estado</label>
                    <select wire:model.live="status" class="form-select" id="status">
                        <option value="">Todos</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                        <option value="graduado">Graduado</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="perPage" class="form-label">Mostrar</label>
                    <select class="form-select" id="perPage" wire:model.live="perPage">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <button wire:click="clearFilters" class="btn btn-outline-secondary">
                    <i class="ri ri-delete-bin-line"></i> Limpiar Filtros
                </button>
            </div>
        </div>
    </div>

    <!-- Tabla de matrículas -->
    <div class="card">
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
                                @if($matricula->estado === 'activo')
                                    <span class="badge bg-success">Activo</span>
                                @elseif($matricula->estado === 'inactivo')
                                    <span class="badge bg-secondary">Inactivo</span>
                                @elseif($matricula->estado === 'graduado')
                                    <span class="badge bg-primary">Graduado</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-text-secondary rounded-pill text-body-secondary border-0 p-1" type="button" id="actionsDropdown{{ $matricula->id }}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="ri ri-more-2-fill ri-24px"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="actionsDropdown{{ $matricula->id }}">
                                        @can('view matriculas')
                                        <a class="dropdown-item" href="{{ route('admin.matriculas.show', $matricula) }}">
                                            <i class="ri ri-eye-line me-1"></i> Ver
                                        </a>
                                        @endcan
                                        @can('edit matriculas')
                                        <a class="dropdown-item" href="{{ route('admin.matriculas.edit', $matricula) }}">
                                            <i class="ri ri-edit-line me-1"></i> Editar
                                        </a>
                                        @endcan
                                        @can('delete matriculas')
                                        <button 
                                            class="dropdown-item text-danger" 
                                            wire:click="delete({{ $matricula }})"
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
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    Mostrando {{ $matriculas->firstItem() }} a {{ $matriculas->lastItem() }} de {{ $matriculas->total() }} matrículas
                </div>
                <div>
                    {{ $matriculas->links('vendor.pagination.materialize') }}
                </div>
            </div>
        </div>
    </div>
</div>