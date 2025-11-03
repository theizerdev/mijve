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
                        @can('create school periods')
                        <div>
                            <a href="{{ route('admin.school-periods.create') }}" class="btn btn-primary">
                                <i class="ri ri-add-line"></i> Nuevo Período Escolar
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
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end gap-2">
                            <button type="button" class="btn btn-label-secondary" wire:click="resetFilters">
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
                                <th wire:click="sortBy('name')" style="cursor: pointer;">
                                    Nombre 
                                    @if($sortField == 'name') 
                                        <i class="ri ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> 
                                    @endif
                                </th>
                                <th wire:click="sortBy('start_date')" style="cursor: pointer;">
                                    Fecha Inicio 
                                    @if($sortField == 'start_date') 
                                        <i class="ri ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> 
                                    @endif
                                </th>
                                <th wire:click="sortBy('end_date')" style="cursor: pointer;">
                                    Fecha Fin 
                                    @if($sortField == 'end_date') 
                                        <i class="ri ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> 
                                    @endif
                                </th>
                                <th wire:click="sortBy('is_active')" style="cursor: pointer;">
                                    Estado 
                                    @if($sortField == 'is_active') 
                                        <i class="ri ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> 
                                    @endif
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
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox"
                                               id="statusSwitch{{ $schoolPeriod->id }}"
                                               {{ $schoolPeriod->is_active ? 'checked' : '' }}
                                               @can('edit school periods') wire:click="toggleStatus({{ $schoolPeriod->id }})" @endcan>
                                        <label class="form-check-label" for="statusSwitch{{ $schoolPeriod->id }}">
                                            {{ $schoolPeriod->is_active ? 'Activo' : 'Inactivo' }}
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="ri ri-more-2-line"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            @can('view school periods')
                                            <a class="dropdown-item" href="{{ route('admin.school-periods.show', $schoolPeriod) }}">
                                                <i class="ri ri-eye-line me-1"></i> Ver
                                            </a>
                                            @endcan
                                            @can('edit school periods')
                                            <a class="dropdown-item" href="{{ route('admin.school-periods.edit', $schoolPeriod) }}">
                                                <i class="ri ri-pencil-line me-1"></i> Editar
                                            </a>
                                            @endcan
                                            @if(!$schoolPeriod->is_current)
                                                @can('edit school periods')
                                                <button class="dropdown-item" wire:click="setCurrent({{ $schoolPeriod->id }})" wire:confirm="¿Estás seguro de establecer este período como el actual?">
                                                    <i class="ri ri-check-line me-1"></i> Establecer como actual
                                                </button>
                                                @endcan
                                            @endif
                                            @if(!$schoolPeriod->is_current)
                                                @can('delete school periods')
                                                <button type="button" class="dropdown-item text-danger"
                                                        wire:click="delete({{ $schoolPeriod->id }})"
                                                        wire:confirm="¿Estás seguro de eliminar este período escolar?">
                                                    <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                                </button>
                                                @endcan
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

                <!-- Paginación -->
                <div class="card-footer">
                   {{ $schoolPeriods->links('vendor.pagination.materialize') }}
                </div>
            </div>
        </div>
    </div>
</div>