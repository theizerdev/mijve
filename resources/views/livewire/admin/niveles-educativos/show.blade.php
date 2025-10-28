<div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Detalles del Nivel Educativo</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Nombre:</div>
                <div class="col-md-8">{{ $nivel->nombre }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Descripción:</div>
                <div class="col-md-8">{{ $nivel->descripcion ?? 'N/A' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Costo:</div>
                <div class="col-md-8">${{ number_format($nivel->costo, 2) }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Costo de Matrícula:</div>
                <div class="col-md-8">${{ number_format($nivel->costo_matricula, 2) }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Costo de Mensualidad:</div>
                <div class="col-md-8">${{ number_format($nivel->costo_mensualidad, 2) }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Número de Cuotas:</div>
                <div class="col-md-8">{{ $nivel->numero_cuotas }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Cuota Inicial:</div>
                <div class="col-md-8">${{ number_format($nivel->cuota_inicial, 2) }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Estado:</div>
                <div class="col-md-8">
                    @if($nivel->status)
                        <span class="badge bg-success">Activo</span>
                    @else
                        <span class="badge bg-danger">Inactivo</span>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 fw-bold">Creado:</div>
                <div class="col-md-8">{{ $nivel->created_at->format('d/m/Y H:i') }}</div>
            </div>
            <div class="row mt-2">
                <div class="col-md-4 fw-bold">Actualizado:</div>
                <div class="col-md-8">{{ $nivel->updated_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('admin.niveles-educativos.index') }}" class="btn btn-secondary">
                Volver
            </a>
        </div>
    </div>
</div>