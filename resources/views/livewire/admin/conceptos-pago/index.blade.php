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
            <h5 class="card-title mb-1">Lista de Conceptos de Pago</h5>
            <p class="mb-0">Administra los conceptos de pago</p>
        </div>
        @can('create conceptos_pago')
        <div>
            <a href="{{ route('admin.conceptos-pago.create') }}" class="btn btn-primary">
                <i class="ri ri-add-line"></i> Nuevo Concepto
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
                <div class="col-md-4">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" id="search" placeholder="Nombre o descripción...">
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">Estado</label>
                    <select wire:model.live="status" class="form-select" id="status">
                        <option value="">Todos</option>
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
                <div class="col-md-4">
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

    <!-- Tabla de conceptos de pago -->
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
                        <th wire:click="sortBy('descripcion')" style="cursor: pointer;">
                            Descripción
                            @if($sortBy === 'descripcion')
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
                    @forelse($conceptos as $concepto)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded bg-label-primary">{{ substr($concepto->nombre, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $concepto->nombre }}</h6>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $concepto->descripcion }}</td>
                            <td>
                                @if($concepto->activo)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-text-secondary rounded-pill text-body-secondary border-0 p-1" type="button" id="actionsDropdown{{ $concepto->id }}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="ri ri-more-2-fill ri-24px"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="actionsDropdown{{ $concepto->id }}">
                                        @can('edit conceptos_pago')
                                        <a class="dropdown-item" href="{{ route('admin.conceptos-pago.edit', $concepto) }}">
                                            <i class="ri ri-edit-line me-1"></i> Editar
                                        </a>
                                        @endcan
                                        @can('delete conceptos_pago')
                                        <button 
                                            class="dropdown-item text-danger" 
                                            wire:click="delete({{ $concepto }})"
                                            wire:confirm="¿Estás seguro de eliminar este concepto de pago?">
                                            <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                        </button>
                                        @endcan
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No se encontraron conceptos de pago</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    Mostrando {{ $conceptos->firstItem() }} a {{ $conceptos->lastItem() }} de {{ $conceptos->total() }} conceptos
                </div>
                <div>
                    {{ $conceptos->links('livewire.admin.pagination') }}
                </div>
            </div>
        </div>
    </div>
</div>