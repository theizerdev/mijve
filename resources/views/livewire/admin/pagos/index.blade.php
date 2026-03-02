<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Estadísticas -->
        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $totalPagos }}</h4>
                            <p class="mb-0">Transacciones</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ri ri-exchange-dollar-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $pagosPendientes }}</h4>
                            <p class="mb-0">Pendientes</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="ri ri-time-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">$ {{ number_format($totalEur, 2) }}</h4>
                            <p class="mb-0">Total Recaudado (USD)</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ri ri-money-dollar-circle-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">Bs {{ number_format($totalBs, 2) }}</h4>
                            <p class="mb-0">Total Recaudado (Bs)</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="ri ri-money-dollar-circle-line ri-24px"></i>
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
                            <h5 class="card-title mb-1">Reporte de Pagos</h5>
                            <p class="mb-0">Gestión de transacciones y comprobantes</p>
                        </div>
                        @can('create pagos')
                        <div>
                            <a href="{{ route('admin.pagos.create') }}" class="btn btn-primary">
                                <i class="ri-add-line"></i> Registrar Pago
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
                            <input type="text" class="form-control" placeholder="Referencia, participante..."
                                   wire:model.live.debounce.300ms="search">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Estado</label>
                            <select class="form-select" wire:model.live="status">
                                <option value="">Todos</option>
                                <option value="Aprobado">Aprobado</option>
                                <option value="Pendiente">Pendiente</option>
                                <option value="Rechazado">Rechazado</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Actividad</label>
                            <select class="form-select" wire:model.live="actividad_id">
                                <option value="">Todas</option>
                                @foreach($actividades as $actividad)
                                    <option value="{{ $actividad->id }}">{{ $actividad->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Desde</label>
                            <input type="date" class="form-control" wire:model.live="fecha_inicio">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Hasta</label>
                            <input type="date" class="form-control" wire:model.live="fecha_fin">
                        </div>
                    </div>
                </div>

                <div class="card-datatable table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th wire:click="sortBy('id')" style="cursor: pointer;">ID</th>
                                <th wire:click="sortBy('fecha_pago')" style="cursor: pointer;">Fecha</th>
                                <th>Participante</th>
                                <th>Actividad</th>
                                <th>Método / Ref</th>
                                <th>Montos</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pagos as $pago)
                            <tr>
                                <td>#{{ $pago->id }}</td>
                                <td>{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold">{{ $pago->participante->nombres }} {{ $pago->participante->apellidos }}</span>
                                        <small class="text-muted">{{ $pago->participante->cedula }}</small>
                                    </div>
                                </td>
                                <td>{{ $pago->actividad->nombre }}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="badge bg-label-info">{{ $pago->metodoPago->tipo_pago }}</span>
                                        @if($pago->referencia_bancaria)
                                            <small class="text-muted mt-1">Ref: {{ $pago->referencia_bancaria }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold">$ {{ number_format($pago->monto_euro, 2) }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($pago->status === 'Aprobado')
                                        <span class="badge bg-label-success">Aprobado</span>
                                    @elseif($pago->status === 'Pendiente')
                                        <span class="badge bg-label-warning">Pendiente</span>
                                    @else
                                        <span class="badge bg-label-danger">Rechazado</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="ri ri-more-2-line"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('admin.pagos.show', $pago->id) }}">
                                                <i class="ri-file-list-3-line me-1"></i> Ver Comprobante
                                            </a>
                                            @if($pago->status === 'Pendiente')
                                              
                                                <button class="dropdown-item text-success"
                                                        x-on:click.stop="if(confirm('¿Estás seguro de aprobar este pago? Se asociará a tu caja abierta.')) { $wire.confirmPayment({{ $pago->id }}) }">
                                                    <i class="ri-check-line me-1"></i> Aprobar Pago
                                                </button>
                                              
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">No se encontraron pagos registrados</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="card-footer">
                   {{ $pagos->links('livewire.pagination') }}
                </div>
            </div>
        </div>
    </div>
</div>
