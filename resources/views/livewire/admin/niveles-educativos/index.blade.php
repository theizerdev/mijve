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
        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ \App\Models\NivelEducativo::count() }}</h4>
                            <p class="mb-0">Total Niveles</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ri ri-graduation-cap-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ \App\Models\NivelEducativo::where('status', 1)->count() }}</h4>
                            <p class="mb-0">Niveles Activos</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ri ri-check-double-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ \App\Models\Programa::count() }}</h4>
                            <p class="mb-0">Total Programas</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="ri ri-book-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ \App\Models\Student::count() }}</h4>
                            <p class="mb-0">Total Estudiantes</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="ri ri-user-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-1">Lista de Niveles Educativos</h5>
                <div>
                    @can('create', \App\Models\NivelEducativo::class)
                    <a href="{{ route('admin.niveles-educativos.create') }}" class="btn btn-primary">
                        <i class="ri ri-add-line ri-16px me-1"></i>Crear Nivel Educativo
                    </a>
                    @endcan
                </div>
            </div>
        </div>

        <div class="card-body pt-3">
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Buscar</label>
                    <input wire:model.live.debounce.300ms="search" type="text" class="form-control" placeholder="Buscar por nombre...">
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Estado</label>
                    <select wire:model.live="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
                
                <div class="col-md-2 mb-3">
                    <label class="form-label">Mostrar</label>
                    <select wire:model.live="perPage" class="form-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th wire:click="sortBy('nombre')" style="cursor: pointer;">
                                Nombre @if($sortField == 'nombre') <i class="ri ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> @endif
                            </th>
                            <th>Descripción</th>
                            <th wire:click="sortBy('status')" style="cursor: pointer;">
                                Estado @if($sortField == 'status') <i class="ri ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> @endif
                            </th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($niveles as $nivel)
                        <tr>
                            <td>{{ $nivel->nombre }}</td>
                            <td>{{ $nivel->descripcion ?? '-' }}</td>
                            <td>
                                @if($nivel->status)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    @can('view', $nivel)
                                    <a href="{{ route('admin.niveles-educativos.show', $nivel) }}" class="btn btn-sm btn-icon btn-text-secondary waves-effect">
                                        <i class="ri ri-eye-line ri-20px"></i>
                                    </a>
                                    @endcan
                                    
                                    @can('update', $nivel)
                                    <a href="{{ route('admin.niveles-educativos.edit', $nivel) }}" class="btn btn-sm btn-icon btn-text-secondary waves-effect">
                                        <i class="ri ri-edit-line ri-20px"></i>
                                    </a>
                                    @endcan
                                    
                                    @can('delete', $nivel)
                                    <button type="button" class="btn btn-sm btn-icon btn-text-secondary waves-effect" 
                                            wire:click="delete({{ $nivel->id }})"
                                            wire:confirm="¿Estás seguro de eliminar este nivel educativo?">
                                        <i class="ri ri-delete-bin-line ri-20px text-danger"></i>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">No se encontraron niveles educativos</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $niveles->links() }}
            </div>
        </div>
    </div>
</div>