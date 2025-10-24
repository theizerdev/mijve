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

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-start border-primary border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Empresas</h6>
                            <h2 class="mb-0">{{ $totalEmpresas }}</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="ri ri-building-line text-primary" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-start border-success border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Activas</h6>
                            <h2 class="mb-0">{{ $empresasActivas }}</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="ri ri-check-circle-line text-success" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-start border-danger border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Inactivas</h6>
                            <h2 class="mb-0">{{ $empresasInactivas }}</h2>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="ri ri-close-circle-line text-danger" style="font-size: 1.5rem;"></i>
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
                            <h5 class="card-title mb-1">Lista de Empresas</h5>
                            <p class="mb-0">Administra las empresas del sistema</p>
                        </div>
                        @can('create empresas')
                        <div>
                            <a href="{{ route('admin.empresas.create') }}" class="btn btn-primary">
                                <i class="ri ri-add-line"></i> Nueva Empresa
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card-header border-bottom">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Buscar</label>
                            <input type="text" class="form-control" placeholder="Nombre, RUC, email..."
                                   wire:model.live.debounce.300ms="search">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" wire:model.live="status">
                                <option value="">Todos</option>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Mostrar</label>
                            <select class="form-select" wire:model.live="perPage">
                                <option value="10">10 por página</option>
                                <option value="25">25 por página</option>
                                <option value="50">50 por página</option>
                                <option value="100">100 por página</option>
                            </select>
                        </div>

                        <div class="col-md-3 d-flex align-items-end gap-2">
                            <button type="button" class="btn btn-label-secondary" wire:click="clearFilters">
                                <i class="ri ri-eraser-line"></i> Limpiar
                            </button>
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
                                    Nombre @if($sortBy === 'nombre') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th wire:click="sortBy('ruc')" style="cursor: pointer;">
                                    Documento @if($sortBy === 'ruc') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th wire:click="sortBy('email')" style="cursor: pointer;">
                                    Email @if($sortBy === 'email') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th wire:click="sortBy('telefono')" style="cursor: pointer;">
                                    Teléfono @if($sortBy === 'telefono') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th wire:click="sortBy('estado')" style="cursor: pointer;">
                                    Estado @if($sortBy === 'estado') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th wire:click="sortBy('created_at')" style="cursor: pointer;">
                                    Creado @if($sortBy === 'created_at') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($empresas as $empresa)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded bg-label-primary">{{ substr($empresa->nombre, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $empresa->nombre }}</h6>
                                                <small class="text-muted">{{ $empresa->razon_social ?? $empresa->nombre }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $empresa->documento }}</span>
                                    </td>
                                    <td>
                                        <a href="mailto:{{ $empresa->email }}">{{ $empresa->email }}</a>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $empresa->telefono }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $empresa->status === true ? 'success' : 'danger' }}">
                                            {{ ucfirst($empresa->status === true ? 'activo' : 'inactivo') }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $empresa->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-text-secondary rounded-pill text-body-secondary border-0 p-1" type="button" id="actionsDropdown{{ $empresa->id }}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="ri ri-more-2-fill ri-24px"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="actionsDropdown{{ $empresa->id }}">
                                                @can('view empresas')
                                                <a class="dropdown-item" href="{{ route('admin.empresas.show', $empresa) }}">
                                                    <i class="ri ri-eye-line me-1"></i> Ver
                                                </a>
                                                @endcan
                                                @can('edit empresas')
                                                <a class="dropdown-item" href="{{ route('admin.empresas.edit', $empresa) }}">
                                                    <i class="ri ri-pencil-line me-1"></i> Editar
                                                </a>
                                                <button class="dropdown-item" wire:click="toggleStatus({{ $empresa->id }})" wire:confirm="¿Estás seguro de cambiar el estado?">
                                                    <i class="ri ri-toggle-{{ $empresa->estado === 'activo' ? 'off' : 'on' }}-line me-1"></i>
                                                    {{ $empresa->estado === 'activo' ? 'Desactivar' : 'Activar' }}
                                                </button>
                                                @endcan
                                                @can('delete empresas')
                                                <button class="dropdown-item text-danger" wire:click="deleteEmpresa({{ $empresa->id }})" wire:confirm="¿Estás seguro de eliminar esta empresa?">
                                                    <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                                </button>
                                                @endcan
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <i class="ri ri-folder-open-line ri-3x text-muted mb-3"></i>
                                        <p class="text-muted">No se encontraron empresas</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            Mostrando {{ $empresas->firstItem() }} a {{ $empresas->lastItem() }} de {{ $empresas->total() }} resultados
                        </div>
                        <div>
                            {{ $empresas->links('livewire.admin.pagination') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
