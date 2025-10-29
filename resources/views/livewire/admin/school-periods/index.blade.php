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

    <div class="row">
        <!-- Estadísticas -->
        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $schoolPeriods->total() }}</h4>
                            <p class="mb-0">Total Períodos</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ri ri-calendar-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ \App\Models\SchoolPeriod::where('is_active', true)->count() }}</h4>
                            <p class="mb-0">Períodos Activos</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ri ri-checkbox-circle-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ \App\Models\SchoolPeriod::where('is_current', true)->count() }}</h4>
                            <p class="mb-0">Período Actual</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="ri ri-star-line ri-24px"></i>
                            </span>
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
                            <h5 class="card-title mb-1">Lista de Períodos Escolares</h5>
                            <p class="mb-0">Administra los períodos escolares registrados en el sistema</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.school-periods.create') }}" class="btn btn-primary">
                                <i class="ri ri-add-line"></i> Nuevo Período Escolar
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card-header border-bottom">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Buscar</label>
                            <input type="text" class="form-control" placeholder="Nombre, descripción..." 
                                   wire:model.live.debounce.300ms="search">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" wire:model.live="filters.status">
                                <option value="">Todos</option>
                                <option value="active">Activos</option>
                                <option value="inactive">Inactivos</option>
                                <option value="current">Actual</option>
                                <option value="past">Pasados</option>
                                <option value="future">Futuros</option>
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

                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-label-secondary me-2" wire:click="resetFilters">
                                <i class="ri ri-eraser-line"></i> Limpiar filtros
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-datatable table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th wire:click="sortBy('name')" style="cursor: pointer;">
                                    Nombre @if($sortField == 'name') <i class="ri ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th wire:click="sortBy('start_date')" style="cursor: pointer;">
                                    Fecha Inicio @if($sortField == 'start_date') <i class="ri ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th wire:click="sortBy('end_date')" style="cursor: pointer;">
                                    Fecha Fin @if($sortField == 'end_date') <i class="ri ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th wire:click="sortBy('is_active')" style="cursor: pointer;">
                                    Estado @if($sortField == 'is_active') <i class="ri ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($schoolPeriods as $schoolPeriod)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded bg-label-primary">{{ substr($schoolPeriod->name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $schoolPeriod->name }}</h6>
                                                @if($schoolPeriod->is_current)
                                                    <span class="badge bg-label-info">Actual</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $schoolPeriod->start_date->format('d/m/Y') }}</td>
                                    <td>{{ $schoolPeriod->end_date->format('d/m/Y') }}</td>
                                    <td>
                                        @if($schoolPeriod->is_active)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-text-secondary rounded-pill text-body-secondary border-0 p-1" type="button" id="actionsDropdown{{ $schoolPeriod->id }}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="ri ri-more-2-fill ri-24px"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="actionsDropdown{{ $schoolPeriod->id }}">
                                                <a class="dropdown-item" href="{{ route('admin.school-periods.show', $schoolPeriod) }}">
                                                    <i class="ri ri-eye-line me-1"></i> Ver
                                                </a>
                                                <a class="dropdown-item" href="{{ route('admin.school-periods.edit', $schoolPeriod) }}">
                                                    <i class="ri ri-pencil-line me-1"></i> Editar
                                                </a>
                                                @if(!$schoolPeriod->is_current)
                                                    <button class="dropdown-item" wire:click="setCurrent({{ $schoolPeriod->id }})" wire:confirm="¿Estás seguro de establecer este período como el actual?">
                                                        <i class="ri ri-check-line me-1"></i> Establecer como actual
                                                    </button>
                                                @endif
                                                @if(!$schoolPeriod->is_current)
                                                    <button class="dropdown-item text-danger" wire:click="delete({{ $schoolPeriod->id }})" wire:confirm="¿Estás seguro de eliminar este período escolar?">
                                                        <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No se encontraron períodos escolares</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            Mostrando {{ $schoolPeriods->firstItem() }} a {{ $schoolPeriods->lastItem() }} de {{ $schoolPeriods->total() }} resultados
                        </div>
                        <div>
                            {{ $schoolPeriods->links('vendor.pagination.materialize') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
