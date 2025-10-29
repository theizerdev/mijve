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
                            <h4 class="mb-1">{{ \App\Models\Turno::count() }}</h4>
                            <p class="mb-0">Total Turnos</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ri ri-time-line ri-24px"></i>
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
                            <h4 class="mb-1">{{ \App\Models\Turno::whereTime('hora_inicio', '<', '12:00:00')->count() }}</h4>
                            <p class="mb-0">Turnos Mañana</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="ri ri-sun-line ri-24px"></i>
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
                            <h4 class="mb-1">{{ \App\Models\Turno::whereTime('hora_inicio', '>=', '12:00:00')->count() }}</h4>
                            <p class="mb-0">Turnos Tarde</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="ri ri-moon-line ri-24px"></i>
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
                            <h5 class="card-title mb-1">Lista de Turnos</h5>
                            <p class="mb-0">Administra los turnos registrados en el sistema</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.turnos.create') }}" class="btn btn-primary">
                                <i class="ri ri-add-line"></i> Nuevo Turno
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card-header border-bottom">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Buscar</label>
                            <input type="text" class="form-control" placeholder="Nombre, hora inicio, hora fin..."
                                   wire:model.live.debounce.300ms="search">
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
                            <button type="button" class="btn btn-label-secondary" wire:click="$refresh">
                                <i class="ri ri-refresh-line"></i> Actualizar
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-datatable table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th wire:click="sortBy('nombre')" style="cursor: pointer;">
                                    Nombre @if($sortField == 'nombre') <i class="ri ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th wire:click="sortBy('descripcion')" style="cursor: pointer;">
                                    Descripción @if($sortField == 'descripcion') <i class="ri ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th wire:click="sortBy('hora_inicio')" style="cursor: pointer;">
                                    Hora Inicio @if($sortField == 'hora_inicio') <i class="ri ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th wire:click="sortBy('hora_fin')" style="cursor: pointer;">
                                    Hora Fin @if($sortField == 'hora_fin') <i class="ri ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th wire:click="sortBy('status')" style="cursor: pointer;">
                                    Estado @if($sortField == 'status') <i class="ri ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($turnos as $turno)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded bg-label-primary">{{ substr($turno->nombre, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $turno->nombre }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $turno->descripcion }}</td>
                                    <td>{{ $turno->hora_inicio->format('H:i') }}</td>
                                    <td>{{ $turno->hora_fin->format('H:i') }}</td>
                                    <td>
                                        @if($turno->status)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-text-secondary rounded-pill text-body-secondary border-0 p-1" type="button" id="actionsDropdown{{ $turno->id }}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="ri ri-more-2-fill ri-24px"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="actionsDropdown{{ $turno->id }}">

                                                <a class="dropdown-item" href="{{ route('admin.turnos.edit', $turno) }}">
                                                    <i class="ri ri-pencil-line me-1"></i> Editar
                                                </a>
                                                <button class="dropdown-item text-danger" wire:click="delete({{ $turno->id }})" wire:confirm="¿Estás seguro de eliminar este turno?">
                                                    <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No se encontraron turnos</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            Mostrando {{ $turnos->firstItem() }} a {{ $turnos->lastItem() }} de {{ $turnos->total() }} resultados
                        </div>
                        <div>
                            {{ $turnos->links('vendor.pagination.materialize') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
