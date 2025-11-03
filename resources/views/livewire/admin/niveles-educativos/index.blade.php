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
        <div class="col-md-3">
            <div class="card border-start border-primary border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Niveles</h6>
                            <h2 class="mb-0">{{ \App\Models\NivelEducativo::count() }}</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="ri-graduation-cap-line text-primary" style="font-size: 1.5rem;"></i>
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
                            <h6 class="text-muted mb-2">Activos</h6>
                            <h2 class="mb-0">{{ \App\Models\NivelEducativo::where('status', 1)->count() }}</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="ri-check-double-line text-success" style="font-size: 1.5rem;"></i>
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
                            <h6 class="text-muted mb-2">Programas</h6>
                            <h2 class="mb-0">{{ \App\Models\Programa::count() }}</h2>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="ri-book-line text-info" style="font-size: 1.5rem;"></i>
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
                            <h6 class="text-muted mb-2">Estudiantes</h6>
                            <h2 class="mb-0">{{ \App\Models\Student::count() }}</h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="ri-user-3-line text-warning" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Principal -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">Niveles Educativos</h5>
                <small class="text-muted">Gestión de niveles académicos</small>
            </div>
            @can('create niveles educativos')
            <a href="{{ route('admin.niveles-educativos.create') }}" class="btn btn-primary">
                <i class="ri-add-line me-1"></i> Nuevo Nivel
            </a>
            @endcan
        </div>

        <div class="card-body">
            <!-- Filtros -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="ri-search-line"></i></span>
                        <input type="text" wire:model.live="search" class="form-control" placeholder="Buscar niveles...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="perPage" class="form-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
                <div class="col-md-3 text-end">
                    <div class="d-flex gap-2 justify-content-end">
                        <span class="badge bg-label-primary">Total: {{ $niveles->total() }}</span>
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
                                <i class="ri-graduation-cap-line me-1"></i>Nombre
                                @if($sortField == 'nombre') <i class="ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> @endif
                            </th>
                            <th><i class="ri-file-text-line me-1"></i>Descripción</th>
                            <th wire:click="sortBy('status')" style="cursor: pointer;">
                                <i class="ri-toggle-line me-1"></i>Estado
                                @if($sortField == 'status') <i class="ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> @endif
                            </th>
                            <th width="120">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($niveles as $nivel)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded bg-label-primary">{{ substr($nivel->nombre, 0, 1) }}</span>
                                    </div>
                                    <span class="fw-medium">{{ $nivel->nombre }}</span>
                                </div>
                            </td>
                            <td>{{ $nivel->descripcion ?? '-' }}</td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           wire:click="toggleStatus({{ $nivel->id }})" 
                                           {{ $nivel->status ? 'checked' : '' }}
                                           id="switch{{ $nivel->id }}">
                                    <label class="form-check-label" for="switch{{ $nivel->id }}">
                                        <span class="badge bg-label-{{ $nivel->status ? 'success' : 'secondary' }}">
                                            {{ $nivel->status ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    @can('edit niveles educativos')
                                    <a href="{{ route('admin.niveles-educativos.edit', $nivel) }}" 
                                       class="btn btn-sm btn-icon btn-text-secondary rounded-pill" 
                                       title="Editar">
                                        <i class="ri-edit-line ri-20px"></i>
                                    </a>
                                    @endcan
                                    @can('delete niveles educativos')
                                    <button wire:click="delete({{ $nivel->id }})" 
                                            wire:confirm="¿Eliminar el nivel {{ $nivel->nombre }}?" 
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
                            <td colspan="4" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="ri-graduation-cap-line ri-48px text-muted mb-2"></i>
                                    <h6 class="text-muted">No hay niveles educativos</h6>
                                    <p class="text-muted mb-0">Crea el primer nivel educativo</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($niveles->hasPages())
            <div class="mt-4 d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Mostrando {{ $niveles->firstItem() }} a {{ $niveles->lastItem() }} de {{ $niveles->total() }} resultados
                </div>
                <div>
                    {{ $niveles->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
