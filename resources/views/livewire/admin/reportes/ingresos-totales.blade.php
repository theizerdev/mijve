<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Ingresos Totales</h4>
            <p class="text-muted mb-0">Ingresos totales por concepto</p>
        </div>
        <div>
            <button wire:click="exportarExcel" class="btn btn-success me-2" @if(count($ingresos) == 0) disabled @endif>
                <i class="ri ri-file-excel-line me-1"></i> Exportar Excel
            </button>
            <button wire:click="exportarPDF" class="btn btn-danger" @if(count($ingresos) == 0) disabled @endif>
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
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                        <input type="date" wire:model.live="fecha_inicio" class="form-control" id="fecha_inicio" value="{{ $fecha_inicio ?? '' }}">
                    </div>
                </div>
                <div class="col-md-6">
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

    @if(count($ingresos) > 0)
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <p class="mb-1 text-muted">Total Ingresos</p>
                        <h3 class="mb-0 text-success">${{ number_format($totales['total_ingresos'], 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <p class="mb-1 text-muted">Total Transacciones</p>
                        <h3 class="mb-0">{{ $totales['total_transacciones'] }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Ingresos por Concepto</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Concepto</th>
                                <th class="text-end">Cantidad</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Porcentaje</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ingresos as $ingreso)
                                <tr>
                                    <td>{{ $ingreso->concepto }}</td>
                                    <td class="text-end">{{ $ingreso->cantidad }}</td>
                                    <td class="text-end">${{ number_format($ingreso->total, 2) }}</td>
                                    <td class="text-end">
                                        @if($totales['total_ingresos'] > 0)
                                            {{ number_format(($ingreso->total / $totales['total_ingresos']) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <th>Total General</th>
                                <th class="text-end">{{ $ingresos->sum('cantidad') }}</th>
                                <th class="text-end">${{ number_format($ingresos->sum('total'), 2) }}</th>
                                <th class="text-end">100%</th>
                            </tr>
                        </tfoot>
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