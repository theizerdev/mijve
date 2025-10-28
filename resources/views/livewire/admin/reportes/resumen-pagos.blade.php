<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Resumen de Pagos</h4>
            <p class="text-muted mb-0">Resumen de pagos por período</p>
        </div>
        <div>
            @if(session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show me-2" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <button wire:click="exportarExcel" class="btn btn-success me-2" @if(count($pagos) == 0) disabled @endif>
                <i class="ri ri-file-excel-line me-1"></i> Exportar Excel
            </button>
            <button wire:click="exportarPDF" class="btn btn-danger" @if(count($pagos) == 0) disabled @endif>
                <i class="ri ri-file-pdf-line me-1"></i> Exportar PDF
            </button>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Filtros</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="periodo_id" class="form-label">Período Escolar</label>
                        <select wire:model.live="periodo_id" class="form-select" id="periodo_id">
                            <option value="">Seleccione un período</option>
                            @foreach($periodos as $periodo)
                                <option value="{{ $periodo->id }}">
                                    {{ $periodo->nombre }} ({{ $periodo->fecha_inicio?->format('d/m/Y') ?? 'N/A' }} - {{ $periodo->fecha_fin?->format('d/m/Y') ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                        <input type="date" wire:model.live="fecha_inicio" class="form-control" id="fecha_inicio" value="{{ $fecha_inicio ?? '' }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="fecha_fin" class="form-label">Fecha Fin</label>
                        <input type="date" wire:model.live="fecha_fin" class="form-control" id="fecha_fin" value="{{ $fecha_fin ?? '' }}">
                    </div>
                </div>
            </div>
            
            <button wire:click="cargarReporte" class="btn btn-primary">
                <i class="ri ri-search-line me-1"></i> Generar Reporte
            </button>
        </div>
    </div>

    @if(count($pagos) > 0)
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Totales por Concepto</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Concepto</th>
                                        <th class="text-end">Cantidad</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($totales as $total)
                                        <tr>
                                            <td>{{ $total->concepto }}</td>
                                            <td class="text-end">{{ $total->cantidad }}</td>
                                            <td class="text-end">${{ number_format($total->total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-light">
                                        <th>Total General</th>
                                        <th class="text-end">{{ $totales->sum('cantidad') }}</th>
                                        <th class="text-end">${{ number_format($totales->sum('total'), 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Resumen</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="border rounded p-3 text-center">
                                    <p class="mb-1 text-muted">Total Pagos</p>
                                    <h4 class="mb-0">{{ count($pagos) }}</h4>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="border rounded p-3 text-center">
                                    <p class="mb-1 text-muted">Total Ingresos</p>
                                    <h4 class="mb-0 text-success">${{ number_format($pagos->sum('monto_pagado'), 2) }}</h4>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="border rounded p-3 text-center">
                                    <p class="mb-1 text-muted">Período</p>
                                    <h6 class="mb-0">
                                        {{ $fecha_inicio ? \Carbon\Carbon::createFromFormat('Y-m-d', $fecha_inicio)->format('d/m/Y') : 'N/A' }} - 
                                        {{ $fecha_fin ? \Carbon\Carbon::createFromFormat('Y-m-d', $fecha_fin)->format('d/m/Y') : 'N/A' }}
                                    </h6>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="border rounded p-3 text-center">
                                    <p class="mb-1 text-muted">Conceptos Únicos</p>
                                    <h4 class="mb-0">{{ $totales->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Detalle de Pagos</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Estudiante</th>
                                <th>Concepto</th>
                                <th class="text-end">Monto</th>
                                <th class="text-end">Pagado</th>
                                <th>Método</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pagos as $pago)
                                <tr>
                                    <td>{{ $pago->fecha_pago?->format('d/m/Y') ?? 'N/A' }}</td>
                                    <td>{{ $pago->matricula?->student?->nombres ?? '' }} {{ $pago->matricula?->student?->apellidos ?? '' }}</td>
                                    <td>{{ $pago->conceptoPago?->nombre ?? 'N/A' }}</td>
                                    <td class="text-end">${{ number_format($pago->monto, 2) }}</td>
                                    <td class="text-end">${{ number_format($pago->monto_pagado, 2) }}</td>
                                    <td>
                                        @if($pago->metodo_pago == 'efectivo')
                                            <span class="badge bg-success">Efectivo</span>
                                        @elseif($pago->metodo_pago == 'transferencia')
                                            <span class="badge bg-info">Transferencia</span>
                                        @elseif($pago->metodo_pago == 'tarjeta')
                                            <span class="badge bg-warning">Tarjeta</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $pago->metodo_pago ?? 'N/A' }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="ri ri-search-eye-line ri-3x text-muted mb-3"></i>
                <h5 class="mb-2">No hay datos para mostrar</h5>
                <p class="text-muted mb-0">Configure los filtros y genere el reporte</p>
            </div>
        </div>
    @endif
</div>