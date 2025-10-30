<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Detalles del Programa</h4>
            <p class="text-muted mb-0">Información completa del programa académico</p>
        </div>
        <div>
            <a href="{{ route('admin.programas.index') }}" class="btn btn-secondary">
                <i class="ri ri-arrow-left-line me-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Información del Programa</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Nombre</label>
                            <p class="fw-bold mb-0">{{ $programa->nombre }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Nivel Educativo</label>
                            <p class="fw-bold mb-0">{{ $programa->nivelEducativo->nombre ?? 'No asignado' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Estado</label>
                            <p class="fw-bold mb-0">
                                @if($programa->activo)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Fecha de Creación</label>
                            <p class="fw-bold mb-0">{{ $programa->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="text-muted">Descripción</label>
                            <p class="fw-bold mb-0">{{ $programa->descripcion ?? 'Sin descripción' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Estadísticas</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Matrículas asociadas</span>
                        <span class="badge bg-primary">{{ $programa->matriculas()->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Última actualización</span>
                        <span>{{ $programa->updated_at->format('d/m/Y H:i:s') }}</span>
                    </div>
                </div>
            </div>

            @can('edit programas')
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('admin.programas.edit', $programa) }}" class="btn btn-primary w-100">
                        <i class="ri ri-pencil-line me-1"></i> Editar Programa
                    </a>
                </div>
            </div>
            @endcan
        </div>
    </div>
</div>