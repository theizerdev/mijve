<div>
    @if (session()->has('message') || session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') ?? session('success') }}
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
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Lista de Programas</h5>
                            <p class="mb-0">Administra los programas académicos del sistema</p>
                        </div>
                        @can('create programas')
                        <div>
                            <a href="{{ route('admin.programas.create') }}" class="btn btn-primary">
                                <i class="ri ri-add-line"></i> Nuevo Programa
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
                            <label class="form-label">Nivel Educativo</label>
                            <select class="form-select" wire:model.live="nivel_educativo_id">
                                <option value="">Todos los niveles</option>
                                @foreach($nivelesEducativos as $nivel)
                                    <option value="{{ $nivel->id }}">{{ $nivel->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" wire:model.live="status">
                                <option value="">Todos</option>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
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
                                <th wire:click="sortBy('nombre')" style="cursor: pointer;">
                                    Programa
                                    @if($sortBy === 'nombre')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('nivel_educativo_id')" style="cursor: pointer;">
                                    Nivel Educativo
                                    @if($sortBy === 'nivel_educativo_id')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('activo')" style="cursor: pointer;">
                                    Estado
                                    @if($sortBy === 'activo')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($programas as $programa)
                            <tr>
                                <td>
                                    <div class="d-flex flex-column">
                                        <strong>{{ $programa->nombre }}</strong>
                                        @if($programa->descripcion)
                                            <small class="text-muted">{{ Str::limit($programa->descripcion, 50) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $programa->nivelEducativo->nombre ?? 'N/A' }}</td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox"
                                               id="statusSwitch{{ $programa->id }}"
                                               {{ $programa->activo ? 'checked' : '' }}
                                               @can('edit programas') wire:click="toggleStatus({{ $programa->id }})" @endcan>
                                        <label class="form-check-label" for="statusSwitch{{ $programa->id }}">
                                            {{ $programa->activo ? 'Activo' : 'Inactivo' }}
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="ri ri-more-2-line"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            @can('view programas')
                                            <a class="dropdown-item" href="{{ route('admin.programas.show', $programa) }}">
                                                <i class="ri ri-eye-line me-1"></i> Ver
                                            </a>
                                            @endcan
                                            @can('edit programas')
                                            <a class="dropdown-item" href="{{ route('admin.programas.edit', $programa) }}">
                                                <i class="ri ri-pencil-line me-1"></i> Editar
                                            </a>
                                            @endcan
                                            @can('delete programas')
                                            <button type="button" class="dropdown-item text-danger"
                                                    wire:click="delete({{ $programa->id }})"
                                                    wire:confirm="¿Estás seguro de eliminar este programa?">
                                                <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                            </button>
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No se encontraron programas</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="card-footer">
                   {{ $programas->links('vendor.pagination.materialize') }}
                </div>
            </div>
        </div>
    </div>
</div>