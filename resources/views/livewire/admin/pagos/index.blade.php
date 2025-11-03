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
                            <h6 class="text-muted mb-2">Total Pagos</h6>
                            <h2 class="mb-0">{{ number_format($this->stats['total']) }}</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="ri ri-file-list-3-line text-primary" style="font-size: 1.5rem;"></i>
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
                            <h6 class="text-muted mb-2">Aprobados</h6>
                            <h2 class="mb-0">{{ number_format($this->stats['aprobados']) }}</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="ri ri-check-double-line text-success" style="font-size: 1.5rem;"></i>
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
                            <h6 class="text-muted mb-2">Pendientes</h6>
                            <h2 class="mb-0">{{ number_format($this->stats['pendientes']) }}</h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="ri ri-time-line text-warning" style="font-size: 1.5rem;"></i>
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
                            <h6 class="text-muted mb-2">Ingresos Totales</h6>
                            <h2 class="mb-0">${{ number_format($this->stats['ingresos_totales'], 2) }}</h2>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="ri ri-money-dollar-circle-line text-info" style="font-size: 1.5rem;"></i>
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
                            <h5 class="card-title mb-1">Lista de Pagos</h5>
                            <p class="mb-0">Administra los pagos registrados en el sistema</p>
                        </div>
                        @can('create pagos')
                        <div>
                            <a href="{{ route('admin.pagos.create') }}" class="btn btn-primary">
                                <i class="ri ri-add-line"></i> Nuevo Pago
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
                            <input type="text" class="form-control" placeholder="Número, estudiante, referencia..."
                                   wire:model.live.debounce.300ms="search">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" wire:model.live="status">
                                <option value="">Todos los estados</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="aprobado">Aprobado</option>
                                <option value="cancelado">Cancelado</option>
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
                        <div class="col-md-3 d-flex align-items-end gap-2">
                            <button type="button" class="btn btn-label-secondary" wire:click="clearFilters">
                                <i class="ri ri-eraser-line"></i> Limpiar
                            </button>
                            <button type="button" class="btn btn-label-success" wire:click="export">
                                <i class="mdi mdi-file-excel"></i> Exportar
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-datatable table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th wire:click="sortBy('serie')" style="cursor: pointer;">
                                    Documento
                                    @if($sortBy === 'serie')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <th>Estudiante</th>
                                <th>Método Pago</th>
                                <th wire:click="sortBy('total')" style="cursor: pointer;">
                                    Total
                                    @if($sortBy === 'total')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('fecha')" style="cursor: pointer;">
                                    Fecha
                                    @if($sortBy === 'fecha')
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
                                        <div class="fw-medium text-primary">{{ $pago->numero_completo }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded bg-label-primary">{{ substr($pago->matricula->student->nombres ?? '', 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $pago->matricula->student->nombres ?? '' }} {{ $pago->matricula->student->apellidos ?? '' }}</h6>
                                                <small class="text-muted">{{ $pago->matricula->student->documento_identidad ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @php
                                                $iconClass = match($pago->metodo_pago) {
                                                    'efectivo' => 'ri ri-money-dollar-circle-line text-success',
                                                    'transferencia' => 'ri ri-bank-line text-info',
                                                    'tarjeta' => 'ri ri-bank-card-line text-primary',
                                                    default => 'ri ri-question-line text-muted'
                                                };
                                            @endphp
                                            <i class="{{ $iconClass }} me-2" style="font-size: 1.2rem;"></i>
                                            <div>
                                                <div>{{ ucfirst($pago->metodo_pago ?? 'N/A') }}</div>
                                                @if($pago->referencia)
                                                    <small class="text-muted">Ref: {{ $pago->referencia }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">${{ number_format($pago->total, 2) }}</div>
                                        @if($pago->descuento > 0)
                                            <small class="text-muted">Desc: ${{ number_format($pago->descuento, 2) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $pago->fecha->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $pago->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                   id="statusSwitch{{ $pago->id }}"
                                                   {{ $pago->estado === 'aprobado' ? 'checked' : '' }}
                                                   @can('edit pagos') wire:click="toggleStatus({{ $pago->id }})" @endcan>
                                            <label class="form-check-label" for="statusSwitch{{ $pago->id }}">
                                                {{ ucfirst($pago->estado) }}
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="ri ri-more-2-line"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                @can('view pagos')
                                                <a class="dropdown-item" href="{{ route('admin.pagos.show', $pago) }}">
                                                    <i class="ri ri-eye-line me-1"></i> Ver
                                                </a>
                                                @endcan
                                                @can('edit pagos')
                                                <a class="dropdown-item" href="#">
                                                    <i class="ri ri-printer-line me-1"></i> Imprimir
                                                </a>
                                                @endcan
                                                @can('delete pagos')
                                                <button type="button" class="dropdown-item text-danger"
                                                        wire:click="delete({{ $pago->id }})"
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
                                    <td colspan="7" class="text-center">No se encontraron pagos que coincidan con los filtros</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="card-footer">
                   {{ $pagos->links('vendor.pagination.materialize') }}
                </div>
            </div>
        </div>
    </div>
</div>
