<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Detalles de Matrícula</h4>
            <p class="text-muted mb-0">Información detallada de la matrícula</p>
        </div>
        <div>
            <a href="{{ route('admin.matriculas.index') }}" class="btn btn-secondary">
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
                        <div class="col-sm-8">{{ $matricula->estudiante->nombres ?? '' }} {{ $matricula->estudiante->apellidos ?? '' }}</div>
                        
                        <div class="col-sm-4"><strong>DNI:</strong></div>
                        <div class="col-sm-8">{{ $matricula->estudiante->documento_identidad ?? '' }}</div>
                        
                        <div class="col-sm-4"><strong>Email:</strong></div>
                        <div class="col-sm-8">{{ $matricula->estudiante->correo_electronico ?? '' }}</div>
                        
                        <div class="col-sm-4"><strong>Teléfono:</strong></div>
                        <div class="col-sm-8">{{ $matricula->estudiante->telefono ?? '' }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Datos de Matrícula</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-4"><strong>Programa:</strong></div>
                        <div class="col-sm-8">{{ $matricula->programa->nombre ?? '' }}</div>
                        
                        <div class="col-sm-4"><strong>Período:</strong></div>
                        <div class="col-sm-8">{{ $matricula->periodo->name ?? '' }}</div>
                        
                        <div class="col-sm-4"><strong>Fecha:</strong></div>
                        <div class="col-sm-8">{{ $matricula->fecha_matricula->format('d/m/Y') }}</div>
                        
                        <div class="col-sm-4"><strong>Estado:</strong></div>
                        <div class="col-sm-8">
                            @if($matricula->estado === 'activo')
                                <span class="badge bg-success">Activo</span>
                            @elseif($matricula->estado === 'inactivo')
                                <span class="badge bg-secondary">Inactivo</span>
                            @elseif($matricula->estado === 'graduado')
                                <span class="badge bg-primary">Graduado</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Información de Costos</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-4"><strong>Costo Total:</strong></div>
                        <div class="col-sm-8">${{ number_format($matricula->costo, 2) }}</div>
                        
                        <div class="col-sm-4"><strong>Cuota Inicial:</strong></div>
                        <div class="col-sm-8">${{ number_format($matricula->cuota_inicial, 2) }}</div>
                        
                        <div class="col-sm-4"><strong>Número de Cuotas:</strong></div>
                        <div class="col-sm-8">{{ $matricula->numero_cuotas }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between">
        @can('edit matriculas')
        <a href="{{ route('admin.matriculas.edit', $matricula) }}" class="btn btn-primary">
            <i class="ri ri-edit-line me-1"></i> Editar Matrícula
        </a>
        @endcan
        
        <a href="{{ route('admin.matriculas.index') }}" class="btn btn-secondary">
            <i class="ri ri-arrow-left-line me-1"></i> Volver
        </a>
    </div>
</div>