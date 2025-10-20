<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
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
                            <h4 class="mb-1">{{ $totalSucursales }}</h4>
                            <p class="mb-0">Total Sucursales</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ri ri-building-line ri-24px"></i>
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
                            <h4 class="mb-1">{{ $sucursalesActivas }}</h4>
                            <p class="mb-0">Sucursales Activas</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ri ri-checkbox-circle-line ri-24px"></i>
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
                            <h4 class="mb-1">{{ $sucursalesInactivas }}</h4>
                            <p class="mb-0">Sucursales Inactivas</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="ri ri-close-circle-line ri-24px"></i>
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
                            <h5 class="card-title mb-1">Lista de Sucursales</h5>
                            <p class="mb-0">Administra las sucursales registradas en el sistema</p>
                        </div>
                        @can('create sucursales')
                        <div>
                            <a href="{{ route('admin.sucursales.create') }}" class="btn btn-primary">
                                <i class="ri ri-add-line"></i> Nueva Sucursal
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
                            <input type="text" class="form-control" placeholder="Nombre, teléfono, dirección..."
                                   wire:model.live.debounce.300ms="search">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" wire:model.live="status">
                                <option value="">Todos los estados</option>
                                <option value="active">Activa</option>
                                <option value="inactive">Inactiva</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Empresa</label>
                            <select class="form-select" wire:model.live="empresa_id">
                                <option value="">Todas las empresas</option>
                                @foreach($empresas as $empresa)
                                    <option value="{{ $empresa->id }}">{{ $empresa->razon_social }}</option>
                                @endforeach
                            </select>
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
                                <th wire:click="sortBy('nombre')" style="cursor: pointer;">
                                    Nombre
                                    @if($sortBy === 'nombre')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <th>Empresa</th>
                                <th wire:click="sortBy('telefono')" style="cursor: pointer;">
                                    Teléfono
                                    @if($sortBy === 'telefono')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <th>Dirección</th>
                                <th wire:click="sortBy('created_at')" style="cursor: pointer;">
                                    Fecha de Registro
                                    @if($sortBy === 'created_at')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('status')" style="cursor: pointer;">
                                    Estado
                                    @if($sortBy === 'status')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sucursales as $sucursal)
                            <tr>
                                <td>{{ $sucursal->nombre }}</td>
                                <td>{{ $sucursal->empresa->razon_social ?? 'N/A' }}</td>
                                <td>{{ $sucursal->telefono ?? 'No especificado' }}</td>
                                <td>
                                    @if($sucursal->direccion)
                                        <span data-bs-toggle="tooltip" data-bs-title="{{ $sucursal->direccion }}">
                                            {{ Str::limit($sucursal->direccion, 50) }}
                                        </span>
                                    @else
                                        No especificada
                                    @endif
                                </td>
                                <td>{{ $sucursal->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox"
                                               id="statusSwitch{{ $sucursal->id }}"
                                               {{ $sucursal->status ? 'checked' : '' }}
                                               @can('edit sucursales') wire:click="toggleStatus({{ $sucursal->id }})" @endcan>
                                        <label class="form-check-label" for="statusSwitch{{ $sucursal->id }}">
                                            {{ $sucursal->status ? 'Activa' : 'Inactiva' }}
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="ri ri-more-2-line"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('admin.sucursales.show', $sucursal->id) }}">
                                                <i class="ri ri-eye-line me-1"></i> Ver
                                            </a>
                                            @can('edit sucursales')
                                            <a class="dropdown-item" href="{{ route('admin.sucursales.edit', $sucursal->id) }}">
                                                <i class="ri ri-pencil-line me-1"></i> Editar
                                            </a>
                                            @endcan
                                            @can('delete sucursales')
                                            <button type="button" class="dropdown-item text-danger"
                                                    wire:click="delete({{ $sucursal->id }})"
                                                    wire:confirm="¿Estás seguro de que deseas eliminar esta sucursal?">
                                                <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                            </button>
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">No se encontraron sucursales que coincidan con los filtros</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="card-footer">
                   {{ $sucursales->links('vendor.pagination.materialize') }}
                </div>
            </div>
        </div>
    </div>
</div>