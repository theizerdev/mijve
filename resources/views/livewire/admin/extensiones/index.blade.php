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
                            <h4 class="mb-1">{{ $totalExtensiones }}</h4>
                            <p class="mb-0">Total Extensiones</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ri ri-building-2-line ri-24px"></i>
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
                            <h4 class="mb-1">{{ $extensionesActivas }}</h4>
                            <p class="mb-0">Extensiones Activas</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ri ri-check-line ri-24px"></i>
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
                            <h4 class="mb-1">{{ $extensionesInactivas }}</h4>
                            <p class="mb-0">Extensiones Inactivas</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="ri ri-close-line ri-24px"></i>
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
                            <h5 class="card-title mb-1">Lista de Extensiones</h5>
                            <p class="mb-0">Administra las extensiones registradas en el sistema</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.extensiones.create') }}" class="btn btn-primary">
                                <i class="ri ri-add-line"></i> Nueva Extensión
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card-header border-bottom">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Buscar</label>
                            <input type="text" class="form-control" placeholder="Nombre, zona, distrito..."
                                   wire:model.live.debounce.300ms="search">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" wire:model.live="status">
                                <option value="">Todos los estados</option>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                                <option value="Pendiente">Pendiente</option>
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
                                <th wire:click="sortBy('zona')" style="cursor: pointer;">
                                    Zona
                                    @if($sortBy === 'zona')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('distrito')" style="cursor: pointer;">
                                    Distrito
                                    @if($sortBy === 'distrito')
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
                            @forelse($extensiones as $extension)
                            <tr>
                                <td>{{ $extension->nombre }}</td>
                                <td>{{ $extension->empresa->razon_social ?? 'N/A' }}</td>
                                <td>{{ $extension->zona }}</td>
                                <td>{{ $extension->distrito }}</td>
                                <td>
                                    @if($extension->status === 'Activo')
                                        <span class="badge bg-label-success">Activo</span>
                                    @elseif($extension->status === 'Inactivo')
                                        <span class="badge bg-label-danger">Inactivo</span>
                                    @else
                                        <span class="badge bg-label-warning">{{ $extension->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="ri ri-more-2-line"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('admin.extensiones.edit', $extension->id) }}">
                                                <i class="ri ri-pencil-line me-1"></i> Editar
                                            </a>
                                            <button class="dropdown-item text-danger" wire:click="delete({{ $extension->id }})" 
                                                    wire:confirm="¿Estás seguro de eliminar esta extensión?">
                                                <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No se encontraron extensiones registradas</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer border-top">
                    {{ $extensiones->links('livewire.pagination') }}
                </div>
            </div>
        </div>
    </div>
</div>
