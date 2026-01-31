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

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-primary"><i class="ri ri-group-line ri-24px"></i></span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <h6 class="mb-0">Total Profesores</h6>
                            </div>
                            <div class="user-progress">
                                <h4 class="mb-0">{{ $this->stats['total'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-success"><i class="ri ri-checkbox-circle-line ri-24px"></i></span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <h6 class="mb-0">Activos</h6>
                            </div>
                            <div class="user-progress">
                                <h4 class="mb-0">{{ $this->stats['activos'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-danger"><i class="ri ri-close-circle-line ri-24px"></i></span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <h6 class="mb-0">Inactivos</h6>
                            </div>
                            <div class="user-progress">
                                <h4 class="mb-0">{{ $this->stats['inactivos'] }}</h4>
                            </div>
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
                            <h5 class="card-title mb-1">Lista de Profesores</h5>
                            <p class="mb-0">Administra los profesores del sistema</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary">
                                <i class="ri ri-add-line"></i> Nuevo Profesor
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card-header border-bottom">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Buscar</label>
                            <input type="text" class="form-control" placeholder="Nombre, código, email..." wire:model.live.debounce.300ms="search">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Especialización</label>
                            <select class="form-select" wire:model.live="specialization">
                                <option value="">Todas las especializaciones</option>
                                <option value="Matemáticas">Matemáticas</option>
                                <option value="Ciencias">Ciencias</option>
                                <option value="Lenguaje">Lenguaje</option>
                                <option value="Historia">Historia</option>
                                <option value="Educación Física">Educación Física</option>
                                <option value="Arte">Arte</option>
                                <option value="Música">Música</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" wire:model.live="is_active">
                                <option value="">Todos</option>
                                <option value="1">Activos</option>
                                <option value="0">Inactivos</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-label-secondary w-100" wire:click="clearFilters">
                                <i class="ri ri-eraser-line"></i> Limpiar
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-datatable table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th wire:click="sortBy('employee_code')" style="cursor: pointer;">
                                    Código @if($sortBy === 'employee_code') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th wire:click="sortBy('name')" style="cursor: pointer;">
                                    Profesor @if($sortBy === 'name') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th>Especialización</th>
                                <th>Título</th>
                                <th>Años Exp.</th>
                                <th wire:click="sortBy('hire_date')" style="cursor: pointer;">
                                    Fecha Contratación @if($sortBy === 'hire_date') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teachers as $teacher)
                                <tr>
                                    <td>
                                        <span class="fw-medium">{{ $teacher->employee_code }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded bg-label-primary">
                                                    {{ strtoupper(substr($teacher->name ?? 'SN', 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $teacher->name ?? 'Sin nombre' }}</h6>
                                                <small class="text-muted">{{ $teacher->email ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-info">{{ $teacher->specialization }}</span>
                                    </td>
                                    <td>{{ $teacher->degree }}</td>
                                    <td>
                                        <span class="badge bg-label-secondary">{{ $teacher->years_experience }} años</span>
                                    </td>
                                    <td>{{ $teacher->hire_date?->format('d/m/Y') ?? 'N/A' }}</td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                   id="statusSwitch{{ $teacher->id }}"
                                                   {{ $teacher->is_active ? 'checked' : '' }}
                                                   wire:click="toggleStatus({{ $teacher->id }})"
                                                   wire:confirm="¿Está seguro de cambiar el estado?">
                                            <label class="form-check-label" for="statusSwitch{{ $teacher->id }}">
                                                {{ $teacher->is_active ? 'Activo' : 'Inactivo' }}
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="ri ri-more-2-line"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('admin.teachers.show', $teacher) }}">
                                                    <i class="ri ri-eye-line me-1"></i> Ver Detalles
                                                </a>
                                                <a class="dropdown-item" href="{{ route('admin.teachers.edit', $teacher) }}">
                                                    <i class="ri ri-pencil-line me-1"></i> Editar
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <button type="button" class="dropdown-item text-danger"
                                                        wire:click="delete({{ $teacher->id }})"
                                                        wire:confirm="¿Está seguro de eliminar este profesor?">
                                                    <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="ri ri-user-line ri-3x mb-2"></i>
                                            <p class="mb-0">No se encontraron profesores</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    {{ $teachers->links('livewire.pagination') }}
                </div>
            </div>
        </div>
    </div>
</div>