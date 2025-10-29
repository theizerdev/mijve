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
            <h5 class="card-title mb-1">Lista de Programas</h5>
            <p class="mb-0">Administra los programas académicos</p>
        </div>
        @can('create programas')
        <div>
            <a href="{{ route('admin.programas.create') }}" class="btn btn-primary">
                <i class="ri ri-add-line"></i> Nuevo Programa
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
                <div class="col-md-3">
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

    <!-- Tabla de programas -->
    <div class="card">
        <div class="card-datatable table-responsive">
            <table class="table">
                <thead>
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
                        <th wire:click="sortBy('costo_matricula')" style="cursor: pointer;">
                            Costo Matrícula
                            @if($sortBy === 'costo_matricula')
                                <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('costo_mensualidad')" style="cursor: pointer;">
                            Costo Mensualidad
                            @if($sortBy === 'costo_mensualidad')
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
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded bg-label-primary">{{ substr($programa->nombre, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $programa->nombre }}</h6>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $programa->nivelEducativo->nombre ?? '' }}</td>
                            <td>${{ number_format($programa->costo_matricula, 2) }}</td>
                            <td>${{ number_format($programa->costo_mensualidad, 2) }}</td>
                            <td>
                                @if($programa->activo)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-text-secondary rounded-pill text-body-secondary border-0 p-1" type="button" id="actionsDropdown{{ $programa->id }}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="ri ri-more-2-fill ri-24px"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="actionsDropdown{{ $programa->id }}">
                                        @can('edit programas')
                                        <a class="dropdown-item" href="{{ route('admin.programas.edit', $programa) }}">
                                            <i class="ri ri-edit-line me-1"></i> Editar
                                        </a>
                                        @endcan
                                        @can('delete programas')
                                        <button 
                                            class="dropdown-item text-danger" 
                                            wire:click="delete({{ $programa }})"
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
                            <td colspan="6" class="text-center">No se encontraron programas</td>
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