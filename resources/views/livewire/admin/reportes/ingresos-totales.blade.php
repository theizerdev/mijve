<div>
    <!-- Alertas -->
    @if(session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri ri-error-warning-line me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri ri-check-line me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session()->has('message'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="ri ri-information-line me-2"></i>
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Ingresos Totales</h4>
            <p class="text-muted mb-0">Ingresos totales por concepto</p>
        </div>
        <div>
            <button 
                wire:click="exportarExcel" 
                wire:loading.attr="disabled"
                wire:loading.class="opacity-50"
                class="btn btn-success me-2" 
                @if(count($ingresos) == 0) disabled @endif
            >
                <span wire:loading.remove wire:target="exportarExcel">
                    <i class="ri ri-file-excel-line me-1"></i> Exportar Excel
                </span>
                <span wire:loading wire:target="exportarExcel">
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Exportando...
                </span>
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
            
            <button 
                wire:click="cargarReporte" 
                wire:loading.attr="disabled"
                wire:loading.class="opacity-50"
                class="btn btn-primary"
            >
                <span wire:loading.remove wire:target="cargarReporte">
                    <i class="ri ri-search-line me-1"></i> Generar Reporte
                </span>
                <span wire:loading wire:target="cargarReporte">
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Procesando...
                </span>
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
                                    <td>
                                        <i class="ri ri-money-dollar-circle-line text-success me-2"></i>
                                        {{ $ingreso->concepto }}
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-primary">{{ $ingreso->cantidad }}</span>
                                    </td>
                                    <td class="text-end fw-bold text-success">${{ number_format($ingreso->total, 2) }}</td>
                                    <td class="text-end">
                                        @php
                                            $porcentaje = $totales['total_ingresos'] > 0 ? ($ingreso->total / $totales['total_ingresos']) * 100 : 0;
                                        @endphp
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $porcentaje }}%" aria-valuenow="{{ $porcentaje }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ number_format($porcentaje, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-success">
                                <th>
                                    <i class="ri ri-calculator-line me-2"></i>
                                    Total General
                                </th>
                                <th class="text-end">
                                    <span class="badge bg-success">{{ $ingresos->sum('cantidad') }}</span>
                                </th>
                                <th class="text-end fw-bold">${{ number_format($ingresos->sum('total'), 2) }}</th>
                                <th class="text-end fw-bold">100%</th>
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