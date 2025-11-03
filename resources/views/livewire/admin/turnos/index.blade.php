<div>
    <!-- Alertas -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-check-line me-2"></i>{{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Estadísticas -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-start border-primary border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Turnos</h6>
                            <h2 class="mb-0">{{ \App\Models\Turno::count() }}</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="ri-time-line text-primary" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-start border-warning border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Turnos Mañana</h6>
                            <h2 class="mb-0">{{ \App\Models\Turno::whereTime('hora_inicio', '<', '12:00:00')->count() }}</h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="ri-sun-line text-warning" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-start border-info border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Turnos Tarde</h6>
                            <h2 class="mb-0">{{ \App\Models\Turno::whereTime('hora_inicio', '>=', '12:00:00')->count() }}</h2>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="ri-moon-line text-info" style="font-size: 1.5rem;"></i>
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
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="ri-search-line"></i></span>
                        <input type="text" wire:model.live="search" class="form-control" placeholder="Buscar turnos...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="perPage" class="form-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
                <div class="col-md-6 text-end">
                    <div class="d-flex gap-2 justify-content-end">
                        <span class="badge bg-label-primary">Total: {{ $turnos->total() }}</span>
                        <button wire:click="clearFilters" class="btn btn-outline-secondary">
                            <i class="ri-eraser-line me-1"></i> Limpiar
                        </button>
                        <button wire:click="export" class="btn btn-outline-success">
                            <i class="ri-file-excel-line me-1"></i> Exportar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabla -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th wire:click="sortBy('nombre')" style="cursor: pointer;">
                                <i class="ri-time-line me-1"></i>Nombre
                                @if($sortField == 'nombre') <i class="ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> @endif
                            </th>
                            <th wire:click="sortBy('descripcion')" style="cursor: pointer;">
                                <i class="ri-file-text-line me-1"></i>Descripción
                                @if($sortField == 'descripcion') <i class="ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> @endif
                            </th>
                            <th wire:click="sortBy('hora_inicio')" style="cursor: pointer;">
                                <i class="ri-sun-line me-1"></i>Hora Inicio
                                @if($sortField == 'hora_inicio') <i class="ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> @endif
                            </th>
                            <th wire:click="sortBy('hora_fin')" style="cursor: pointer;">
                                <i class="ri-moon-line me-1"></i>Hora Fin
                                @if($sortField == 'hora_fin') <i class="ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> @endif
                            </th>
                            <th wire:click="sortBy('status')" style="cursor: pointer;">
                                <i class="ri-toggle-line me-1"></i>Estado
                                @if($sortField == 'status') <i class="ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> @endif
                            </th>
                            <th width="120">Acciones</th>
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
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   wire:click="toggleStatus({{ $turno->id }})" 
                                                   {{ $turno->status ? 'checked' : '' }}
                                                   id="switch{{ $turno->id }}">
                                            <label class="form-check-label" for="switch{{ $turno->id }}">
                                                <span class="badge bg-label-{{ $turno->status ? 'success' : 'secondary' }}">
                                                    {{ $turno->status ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            @can('edit turnos')
                                            <a href="{{ route('admin.turnos.edit', $turno) }}" 
                                               class="btn btn-sm btn-icon btn-text-secondary rounded-pill" 
                                               title="Editar">
                                                <i class="ri-edit-line ri-20px"></i>
                                            </a>
                                            @endcan
                                            @can('delete turnos')
                                            <button wire:click="delete({{ $turno->id }})" 
                                                    wire:confirm="¿Eliminar el turno {{ $turno->nombre }}?" 
                                                    class="btn btn-sm btn-icon btn-text-danger rounded-pill" 
                                                    title="Eliminar">
                                                <i class="ri-delete-bin-7-line ri-20px"></i>
                                            </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="ri-time-line ri-48px text-muted mb-2"></i>
                                        <h6 class="text-muted">No hay turnos registrados</h6>
                                        <p class="text-muted mb-0">Crea el primer turno para comenzar</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            <!-- Paginación -->
            @if($turnos->hasPages())
            <div class="mt-4 d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Mostrando {{ $turnos->firstItem() }} a {{ $turnos->lastItem() }} de {{ $turnos->total() }} resultados
                </div>
                <div>
                    {{ $turnos->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
