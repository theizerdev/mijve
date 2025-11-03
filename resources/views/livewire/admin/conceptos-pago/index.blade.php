<div>
    <!-- Alertas -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-check-line me-2"></i>{{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Card Principal -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">Conceptos de Pago</h5>
                <small class="text-muted">Gestión de conceptos de facturación</small>
            </div>
            @can('create conceptos pago')
            <a href="{{ route('admin.conceptos-pago.create') }}" class="btn btn-primary">
                <i class="ri-add-line me-1"></i> Nuevo Concepto
            </a>
            @endcan
        </div>

        <div class="card-body">
            <!-- Filtros -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="ri-search-line"></i></span>
                        <input type="text" wire:model.live="search" class="form-control" placeholder="Buscar conceptos...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="perPage" class="form-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
                <div class="col-md-3 text-end">
                    <div class="d-flex gap-2 justify-content-end">
                        <span class="badge bg-label-primary">Total: {{ $conceptos->total() }}</span>
                        <button wire:click="clearFilters" class="btn btn-outline-secondary">
                            <i class="ri-eraser-line me-1"></i> Limpiar
                        </button>
                        <button wire:click="export" class="btn btn-outline-success">
                            <i class="ri-file-excel-line me-1"></i> Exportar
                        </button>
                    </div>
                </div>
            </div>
            <!-- Tabla -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th wire:click="sortBy('nombre')" style="cursor: pointer;">
                                <i class="ri-price-tag-3-line me-1"></i>Nombre
                                @if($sortBy === 'nombre')
                                    <i class="ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('descripcion')" style="cursor: pointer;">
                                <i class="ri-file-text-line me-1"></i>Descripción
                                @if($sortBy === 'descripcion')
                                    <i class="ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('activo')" style="cursor: pointer;">
                                <i class="ri-toggle-line me-1"></i>Estado
                                @if($sortBy === 'activo')
                                    <i class="ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                @endif
                            </th>
                            <th width="120">Acciones</th>
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
                            <td>{{ $concepto->descripcion ?? '-' }}</td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           wire:click="toggleStatus({{ $concepto->id }})" 
                                           {{ $concepto->activo ? 'checked' : '' }}
                                           id="switch{{ $concepto->id }}">
                                    <label class="form-check-label" for="switch{{ $concepto->id }}">
                                        <span class="badge bg-label-{{ $concepto->activo ? 'success' : 'secondary' }}">
                                            {{ $concepto->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    @can('edit conceptos pago')
                                    <a href="{{ route('admin.conceptos-pago.edit', $concepto) }}" 
                                       class="btn btn-sm btn-icon btn-text-secondary rounded-pill" 
                                       title="Editar">
                                        <i class="ri-edit-line ri-20px"></i>
                                    </a>
                                    @endcan
                                    @can('delete conceptos pago')
                                    <button wire:click="delete({{ $concepto->id }})" 
                                            wire:confirm="¿Eliminar el concepto {{ $concepto->nombre }}?" 
                                            class="btn btn-sm btn-icon btn-text-danger rounded-pill" 
                                            title="Eliminar">
                                        <i class="ri-delete-bin-7-line ri-20px"></i>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4">
                            <div class="d-flex flex-column align-items-center">
                                <i class="ri-price-tag-3-line ri-48px text-muted mb-2"></i>
                                <h6 class="text-muted">No hay conceptos de pago</h6>
                                <p class="text-muted mb-0">Crea el primer concepto de pago</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

            <!-- Paginación -->
            @if($conceptos->hasPages())
            <div class="mt-4 d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Mostrando {{ $conceptos->firstItem() }} a {{ $conceptos->lastItem() }} de {{ $conceptos->total() }} resultados
                </div>
                <div>
                    {{ $conceptos->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
