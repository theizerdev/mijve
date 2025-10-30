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

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Programas</h4>
            <p class="text-muted mb-0">Gestión de programas académicos</p>
        </div>
        <div class="d-flex gap-2">
            @can('create programas')
            <a href="{{ route('admin.programas.create') }}" class="btn btn-primary">
                <i class="ri ri-add-line me-1"></i> Nuevo Programa
            </a>
            @endcan
            
            @can('export programas')
            <button wire:click="export" class="btn btn-outline-primary">
                <i class="ri ri-download-line me-1"></i> Exportar
            </button>
            @endcan
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Filtros</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" id="search" placeholder="Nombre o descripción...">
                </div>
                <div class="col-md-3">
                    <label for="nivel_educativo_id" class="form-label">Nivel Educativo</label>
                    <select wire:model.live="nivel_educativo_id" class="form-select" id="nivel_educativo_id">
                        <option value="">Todos los niveles</option>
                        @foreach($nivelesEducativos as $nivel)
                            <option value="{{ $nivel->id }}">{{ $nivel->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Estado</label>
                    <select wire:model.live="status" class="form-select" id="status">
                        <option value="">Todos</option>
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button wire:click="clearFilters" class="btn btn-secondary me-2">
                        <i class="ri ri-delete-bin-line me-1"></i> Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de programas -->
    <div class="card">
        <div class="card-datatable table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th wire:click="sortBy('nombre')" style="cursor: pointer;">
                            Nombre
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
                            <td>{{ $programa->nivelEducativo->nombre }}</td>
                            <td>
                                <div class="form-check form-switch">
                                    <input 
                                        type="checkbox" 
                                        class="form-check-input" 
                                        id="activo_{{ $programa->id }}"
                                        wire:click="toggleStatus({{ $programa }})"
                                        {{ $programa->activo ? 'checked' : '' }}
                                        @cannot('edit programas') disabled @endcannot>
                                    <label class="form-check-label" for="activo_{{ $programa->id }}">
                                        {{ $programa->activo ? 'Activo' : 'Inactivo' }}
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-text-secondary rounded-pill text-body-secondary border-0 p-1" type="button" id="actionsDropdown{{ $programa->id }}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="ri ri-more-2-fill ri-24px"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="actionsDropdown{{ $programa->id }}">
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
                                        <button 
                                            class="dropdown-item text-danger" 
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
        
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    Mostrando {{ $programas->firstItem() }} a {{ $programas->lastItem() }} de {{ $programas->total() }} programas
                </div>
                <div>
                    {{ $programas->links('vendor.pagination.materialize') }}
                </div>
            </div>
        </div>
    </div>
</div>