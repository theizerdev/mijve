<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Detalles del Pago</h4>
            <p class="text-muted mb-0">Información detallada del pago realizado</p>
        </div>
        <div>
            <a href="{{ route('admin.pagos.index') }}" class="btn btn-secondary">
                <i class="ri ri-arrow-left-line me-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Datos del Estudiante</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-4"><strong>Nombre:</strong></div>
                        <div class="col-sm-8">{{ $pago->matricula->student->nombre ?? '' }} {{ $pago->matricula->student->apellido ?? '' }}</div>
                        
                        <div class="col-sm-4"><strong>DNI:</strong></div>
                        <div class="col-sm-8">{{ $pago->matricula->student->dni ?? '' }}</div>
                        
                        <div class="col-sm-4"><strong>Programa:</strong></div>
                        <div class="col-sm-8">{{ $pago->matricula->programa->nombre ?? '' }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Datos del Pago</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-4"><strong>Concepto:</strong></div>
                        <div class="col-sm-8">{{ $pago->concepto->nombre ?? '' }}</div>
                        
                        <div class="col-sm-4"><strong>Monto:</strong></div>
                        <div class="col-sm-8">${{ number_format($pago->monto, 2) }}</div>
                        
                        <div class="col-sm-4"><strong>Fecha:</strong></div>
                        <div class="col-sm-8">{{ $pago->fecha_pago ? $pago->fecha_pago->format('d/m/Y') : 'N/A' }}</div>
                        
                        <div class="col-sm-4"><strong>Método:</strong></div>
                        <div class="col-sm-8">
                            @if($pago->metodo_pago === 'efectivo')
                                Efectivo
                            @elseif($pago->metodo_pago === 'transferencia')
                                Transferencia
                            @elseif($pago->metodo_pago === 'tarjeta')
                                Tarjeta
                            @endif
                        </div>
                        
                        <div class="col-sm-4"><strong>Estado:</strong></div>
                        <div class="col-sm-8">
                            @if($pago->estado === 'pendiente')
                                <span class="badge bg-warning">Pendiente</span>
                            @elseif($pago->estado === 'pagado')
                                <span class="badge bg-success">Pagado</span>
                            @elseif($pago->estado === 'cancelado')
                                <span class="badge bg-danger">Cancelado</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between">
        @can('edit pagos')
        <a href="{{ route('admin.pagos.edit', $pago) }}" class="btn btn-primary">
            <i class="ri ri-edit-line me-1"></i> Editar Pago
        </a>
        @endcan
        
        <a href="{{ route('admin.pagos.index') }}" class="btn btn-secondary">
            <i class="ri ri-arrow-left-line me-1"></i> Volver
        </a>
    </div>
</div>