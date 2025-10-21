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
        <div class="col-lg-6 col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $totalRoles }}</h4>
                            <p class="mb-0">Total Roles</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ri ri-user-settings-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $totalPermissions }}</h4>
                            <p class="mb-0">Total Permisos</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ri ri-shield-keyhole-line ri-24px"></i>
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
                            <h5 class="card-title mb-1">Lista de Roles</h5>
                            <p class="mb-0">Administra los roles y permisos del sistema</p>
                        </div>
                        @can('create roles')
                        <div>
                            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                                <i class="ri ri-add-line"></i> Nuevo Rol
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
                            <input type="text" class="form-control" placeholder="Nombre del rol..."
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
                            <button type="button" class="btn btn-label-secondary" wire:click="clearFilters">
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
                                    Nombre
                                    @if($sortBy === 'name')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <th>Permisos</th>
                                <th>Usuarios</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $role)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <span class="avatar-initial rounded bg-label-primary">
                                                <i class="ri ri-user-settings-line"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $role->name }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-label-info">{{ $role->permissions->count() }} permisos</span>
                                </td>
                                <td>
                                    <span class="badge bg-label-success">{{ $role->users->count() }} usuarios</span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="ri ri-more-2-line"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('admin.roles.show', $role->id) }}">
                                                <i class="ri ri-eye-line me-1"></i> Ver
                                            </a>
                                            @can('edit roles')
                                            <a class="dropdown-item" href="{{ route('admin.roles.edit', $role->id) }}">
                                                <i class="ri ri-pencil-line me-1"></i> Editar
                                            </a>
                                            @endcan
                                            @can('delete roles')
                                                @if(!in_array($role->name, ['super-admin', 'admin', 'empresa-admin', 'user']))
                                                <button type="button" class="dropdown-item text-danger"
                                                        wire:click="delete({{ $role->id }})"
                                                        wire:confirm="¿Estás seguro de que deseas eliminar este rol?">
                                                    <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                                </button>
                                                @endif
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No se encontraron roles que coincidan con los filtros</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="card-footer">
                    {{ $roles->links('vendor.pagination.materialize') }}
                </div>
            </div>
        </div>
    </div>
</div>
