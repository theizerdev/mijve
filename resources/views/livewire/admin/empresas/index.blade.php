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
        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $totalEmpresas }}</h4>
                            <p class="mb-0">Total Empresas</p>
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
                            <h4 class="mb-1">{{ $empresasActivas }}</h4>
                            <p class="mb-0">Empresas Activas</p>
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
                            <h4 class="mb-1">{{ $empresasInactivas }}</h4>
                            <p class="mb-0">Empresas Inactivas</p>
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
                            <h5 class="card-title mb-1">Lista de Empresas</h5>
                            <p class="mb-0">Administra las empresas registradas en el sistema</p>
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
                        <div class="col-md-3">
                            <label class="form-label">Buscar</label>
                            <input type="text" class="form-control" placeholder="Razón social, documento..."
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
                                <th wire:click="sortBy('razon_social')" style="cursor: pointer;">
                                    Razón Social
                                    @if($sortBy === 'razon_social')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('documento')" style="cursor: pointer;">
                                    Documento
                                    @if($sortBy === 'documento')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <th>Representante Legal</th>
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
                            @forelse($empresas as $empresa)
                            <tr>
                                <td>{{ $empresa->razon_social }}</td>
                                <td>{{ $empresa->documento }}</td>
                                <td>{{ $empresa->representante_legal ?? 'No especificado' }}</td>
                                <td>{{ $empresa->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox"
                                               id="statusSwitch{{ $empresa->id }}"
                                               {{ $empresa->status ? 'checked' : '' }}
                                               @can('edit empresas') wire:click="toggleStatus({{ $empresa->id }})" @endcan>
                                        <label class="form-check-label" for="statusSwitch{{ $empresa->id }}">

                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="ri ri-more-2-line"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('admin.empresas.show', $empresa->id) }}">
                                                <i class="ri ri-eye-line me-1"></i> Ver
                                            </a>
                                            @can('edit empresas')
                                            <a class="dropdown-item" href="{{ route('admin.empresas.edit', $empresa->id) }}">
                                                <i class="ri ri-pencil-line me-1"></i> Editar
                                            </a>
                                            @endcan
                                            @can('delete empresas')
                                            <button type="button" class="dropdown-item text-danger"
                                                    wire:click="delete({{ $empresa->id }})"
                                                    wire:confirm="¿Estás seguro de que deseas eliminar esta empresa?">
                                                <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                            </button>
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No se encontraron empresas que coincidan con los filtros</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="card-footer">
                    {{ $empresas->links('vendor.pagination.materialize') }}
                </div>
            </div>
        </div>
    </div>
</div>
