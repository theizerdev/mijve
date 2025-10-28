<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Estado de Cuentas</h4>
            <p class="text-muted mb-0">Consulta el estado de cuenta de cada estudiante</p>
        </div>
        <div>
            @if(session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show me-2" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <button wire:click="exportarExcel" class="btn btn-success me-2" @if(!$matriculaSeleccionada) disabled @endif>
                <i class="ri ri-file-excel-line me-1"></i> Exportar Excel
            </button>
            <button wire:click="exportarPDF" class="btn btn-danger" @if(!$matriculaSeleccionada) disabled @endif>
                <i class="ri ri-file-pdf-line me-1"></i> Exportar PDF
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Filtros</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="estudiante_id" class="form-label">Estudiante</label>
                        <select wire:model.live="estudiante_id" class="form-select" id="estudiante_id">
                            <option value="">Seleccione un estudiante</option>
                            @foreach($estudiantes as $estudiante)
                                <option value="{{ $estudiante->id }}">
                                    {{ $estudiante->nombres }} {{ $estudiante->apellidos }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if($estudianteSeleccionado && $estudianteSeleccionado->matriculas->count() > 0)
                        <div class="mb-3">
                            <label for="matricula_id" class="form-label">Matrícula</label>
                            <select wire:model.live="matricula_id" class="form-select" id="matricula_id">
                                <option value="">Seleccione una matrícula</option>
                                @foreach($estudianteSeleccionado->matriculas as $matricula)
                                    <option value="{{ $matricula->id }}">
                                        {{ $matricula->programa->nombre ?? 'Programa no definido' }} - 
                                        {{ $matricula->fecha_matricula?->format('d/m/Y') ?? 'Fecha no definida' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            @if($matriculaSeleccionada)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Información del Estudiante</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Nombre:</strong></p>
                                <p class="text-muted">{{ $estudianteSeleccionado->nombres }} {{ $estudianteSeleccionado->apellidos }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Documento:</strong></p>
                                <p class="text-muted">{{ $estudianteSeleccionado->documento_identidad }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Programa:</strong></p>
                                <p class="text-muted">{{ $matriculaSeleccionada->programa->nombre ?? 'No definido' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Fecha de Matrícula:</strong></p>
                                <p class="text-muted">{{ $matriculaSeleccionada->fecha_matricula?->format('d/m/Y') ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Resumen de Pagos</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="border rounded p-3 text-center">
                                    <p class="mb-1 text-muted">Costo Total</p>
                                    <h4 class="mb-0">${{ number_format($matriculaSeleccionada->costo ?? 0, 2) }}</h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 text-center">
                                    <p class="mb-1 text-muted">Total Pagado</p>
                                    <h4 class="mb-0 text-success">${{ number_format($totalPagado, 2) }}</h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 text-center 
                                    @if($saldoPendiente > 0) bg-warning bg-opacity-10 @else bg-success bg-opacity-10 @endif">
                                    <p class="mb-1 text-muted">Saldo Pendiente</p>
                                    <h4 class="mb-0 
                                        @if($saldoPendiente > 0) text-warning @else text-success @endif">
                                        ${{ number_format($saldoPendiente, 2) }}
                                    </h4>
                                </div>
                            </div>
                        </div>

                        @if(count($pagos) > 0)
                            <h6>Detalle de Pagos</h6>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Concepto</th>
                                            <th>Monto</th>
                                            <th>Pagado</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pagos as $pago)
                                            <tr>
                                                <td>{{ $pago->fecha_pago?->format('d/m/Y') ?? 'N/A' }}</td>
                                                <td>{{ $pago->conceptoPago->nombre ?? 'N/A' }}</td>
                                                <td>${{ number_format($pago->monto, 2) }}</td>
                                                <td>${{ number_format($pago->monto_pagado, 2) }}</td>
                                                <td>
                                                    @if($pago->estado == 'pagado')
                                                        <span class="badge bg-success">Pagado</span>
                                                    @elseif($pago->estado == 'parcial')
                                                        <span class="badge bg-warning">Parcial</span>
                                                    @else
                                                        <span class="badge bg-danger">Pendiente</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="ri ri-file-search-line ri-2x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No se encontraron pagos registrados para esta matrícula</p>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="ri ri-search-eye-line ri-3x text-muted mb-3"></i>
                        <h5 class="mb-2">Seleccione un estudiante y una matrícula</h5>
                        <p class="text-muted mb-0">Para ver el estado de cuenta, seleccione un estudiante y luego una matrícula</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>