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
        <div class="col-md-3">
            <div class="card border-start border-primary border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Permisos</h6>
                            <h2 class="mb-0">{{ $totalPermissions }}</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="ri ri-shield-keyhole-line text-primary" style="font-size: 1.5rem;"></i>
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
                            <h6 class="text-muted mb-2">Con Roles</h6>
                            <h2 class="mb-0">{{ $permissionsWithRoles }}</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="ri ri-user-settings-line text-success" style="font-size: 1.5rem;"></i>
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
                            <h6 class="text-muted mb-2">Sin Roles</h6>
                            <h2 class="mb-0">{{ $permissionsWithoutRoles }}</h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="ri ri-shield-line text-warning" style="font-size: 1.5rem;"></i>
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
                            <h6 class="text-muted mb-2">Módulos Únicos</h6>
                            <h2 class="mb-0">{{ $uniqueModules }}</h2>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="ri ri-folder-line text-info" style="font-size: 1.5rem;"></i>
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
                            <h5 class="card-title mb-1">Lista de Permisos</h5>
                            <p class="mb-0">Administra los permisos del sistema organizados por módulos</p>
                        </div>
                        @can('create permissions')
                        <div>
                            <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
                                <i class="ri ri-add-line"></i> Nuevo Permiso
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
                            <input type="text" class="form-control" placeholder="Nombre, módulo..."
                                   wire:model.live.debounce.300ms="search">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Módulo</label>
                            <select class="form-select" wire:model.live="module">
                                <option value="">Todos</option>
                                @foreach($modules as $module)
                                    <option value="{{ $module }}">{{ ucfirst($module) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Guard</label>
                            <select class="form-select" wire:model.live="guard">
                                <option value="">Todos</option>
                                @foreach($guards as $guard)
                                    <option value="{{ $guard }}">{{ $guard }}</option>
                                @endforeach
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
                                <th wire:click="sortBy('name')" style="cursor: pointer;">
                                    Nombre @if($sortBy === 'name') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th wire:click="sortBy('module')" style="cursor: pointer;">
                                    Módulo @if($sortBy === 'module') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th wire:click="sortBy('guard_name')" style="cursor: pointer;">
                                    Guard @if($sortBy === 'guard_name') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th>Roles Asignados</th>
                                <th wire:click="sortBy('created_at')" style="cursor: pointer;">
                                    Creado @if($sortBy === 'created_at') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($permissions as $permission)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded bg-label-primary">{{ substr($permission->name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $permission->name }}</h6>
                                                <small class="text-muted">{{ Str::limit($permission->name, 30) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ ucfirst($permission->module) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $permission->guard_name }}</span>
                                    </td>
                                    <td>
                                        @if($permission->roles->count() > 0)
                                            <span class="badge bg-success">{{ $permission->roles->count() }} roles</span>
                                            <div class="mt-1">
                                                <small class="text-muted">
                                                    {{ $permission->roles->pluck('name')->take(3)->implode(', ') }}
                                                    @if($permission->roles->count() > 3)
                                                        <span class="text-muted">+{{ $permission->roles->count() - 3 }} más</span>
                                                    @endif
                                                </small>
                                            </div>
                                        @else
                                            <span class="badge bg-secondary">Sin roles</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $permission->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-text-secondary rounded-pill text-body-secondary border-0 p-1" type="button" id="actionsDropdown{{ $permission->id }}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="ri ri-more-2-fill ri-24px"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="actionsDropdown{{ $permission->id }}">
                                                @can('view permissions')
                                                <a class="dropdown-item" href="#" onclick="event.preventDefault(); alert('Función de ver permiso no implementada');">
                                                    <i class="ri ri-eye-line me-1"></i> Ver
                                                </a>
                                                @endcan
                                                @can('edit permissions')
                                                <a class="dropdown-item" href="{{ route('admin.permissions.edit', $permission) }}">
                                                    <i class="ri ri-pencil-line me-1"></i> Editar
                                                </a>
                                                @endcan
                                                @can('delete permissions')
                                                <button class="dropdown-item text-danger" wire:click="deletePermission({{ $permission->id }})" wire:confirm="¿Estás seguro de eliminar este permiso?">
                                                    <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                                </button>
                                                @endcan
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <i class="ri ri-folder-open-line ri-3x text-muted mb-3"></i>
                                        <p class="text-muted">No se encontraron permisos</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            Mostrando {{ $permissions->firstItem() }} a {{ $permissions->lastItem() }} de {{ $permissions->total() }} resultados
                        </div>
                        <div>
                            {{ $permissions->links('livewire.admin.pagination') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
