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
            <h5 class="card-title mb-1">Lista de Pagos</h5>
            <p class="mb-0">Administra los pagos de las matrículas</p>
        </div>
        @can('create pagos')
        <div>
            <a href="{{ route('admin.pagos.create') }}" class="btn btn-primary">
                <i class="ri ri-add-line"></i> Nuevo Pago
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
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" id="search" placeholder="Nombre, apellido o DNI del estudiante...">
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">Estado</label>
                    <select wire:model.live="status" class="form-select" id="status">
                        <option value="">Todos</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="pagado">Pagado</option>
                        <option value="cancelado">Cancelado</option>
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

    <!-- Tabla de pagos -->
    <div class="card">
        <div class="card-datatable table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th wire:click="sortBy('matriculas.student.nombres')" style="cursor: pointer;">
                            Estudiante
                            @if($sortBy === 'matriculas.student.nombres')
                                <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                            @endif
                        </th>
                        <th>Concepto</th>
                        <th>Monto</th>
                        <th wire:click="sortBy('fecha_pago')" style="cursor: pointer;">
                            Fecha
                            @if($sortBy === 'fecha_pago')
                                <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('estado')" style="cursor: pointer;">
                            Estado
                            @if($sortBy === 'estado')
                                <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                            @endif
                        </th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pagos as $pago)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded bg-label-primary">{{ substr($pago->matricula->estudiante->nombres ?? '', 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $pago->matricula->estudiante->nombres ?? '' }} {{ $pago->matricula->estudiante->apellidos ?? '' }}</h6>
                                        <small class="text-muted">{{ $pago->matricula->estudiante->documento_identidad ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>{{ $pago->conceptoPago->nombre ?? '' }}</div>
                                <small class="text-muted">{{ $pago->referencia }}</small>
                            </td>
                            <td>${{ number_format($pago->monto, 2) }}</td>
                            <td>{{ $pago->fecha_pago ? $pago->fecha_pago->format('d/m/Y') : 'N/A' }}</td>
                            <td>
                                @if($pago->estado === 'pendiente')
                                    <span class="badge bg-warning">Pendiente</span>
                                @elseif($pago->estado === 'pagado')
                                    <span class="badge bg-success">Pagado</span>
                                @elseif($pago->estado === 'cancelado')
                                    <span class="badge bg-danger">Cancelado</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-text-secondary rounded-pill text-body-secondary border-0 p-1" type="button" id="actionsDropdown{{ $pago->id }}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="ri ri-more-2-fill ri-24px"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="actionsDropdown{{ $pago->id }}">
                                        @can('view pagos')
                                        <a class="dropdown-item" href="{{ route('admin.pagos.show', $pago) }}">
                                            <i class="ri ri-eye-line me-1"></i> Ver
                                        </a>
                                        @endcan
                                        @can('edit pagos')
                                        <a class="dropdown-item" href="{{ route('admin.pagos.edit', $pago) }}">
                                            <i class="ri ri-edit-line me-1"></i> Editar
                                        </a>
                                        @endcan
                                        @can('delete pagos')
                                        <button 
                                            class="dropdown-item text-danger" 
                                            wire:click="delete({{ $pago }})"
                                            wire:confirm="¿Estás seguro de eliminar este pago?">
                                            <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                        </button>
                                        @endcan
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No se encontraron pagos</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    Mostrando {{ $pagos->firstItem() }} a {{ $pagos->lastItem() }} de {{ $pagos->total() }} pagos
                </div>
                <div>
                    {{ $pagos->links('vendor.pagination.materialize') }}
                </div>
            </div>
        </div>
    </div>
</div>