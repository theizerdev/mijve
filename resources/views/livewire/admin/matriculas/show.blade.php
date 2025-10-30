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
                        <div class="col-sm-8">{{ $matricula->schoolPeriod->name ?? '' }}</div>

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
                        <div class="col-sm-8">${{ number_format($matricula->costo_matricula, 2) }}</div>

                        <div class="col-sm-4"><strong>Cuota Inicial:</strong></div>
                        <div class="col-sm-8">${{ number_format($matricula->monto_inicial, 2) }}</div>

                        <div class="col-sm-4"><strong>Número de Cuotas:</strong></div>
                        <div class="col-sm-8">{{ $matricula->numero_cuotas }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de amortización -->
    @if($matricula->paymentSchedules->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tabla de Amortización</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Cuota</th>
                                    <th>Fecha de Vencimiento</th>
                                    <th>Monto</th>
                                    <th>Monto Pagado</th>
                                    <th>Saldo</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($matricula->paymentSchedules as $schedule)
                                <tr>
                                    <td>{{ $schedule->numero_cuota }}</td>
                                    <td>{{ $schedule->fecha_vencimiento->format('d/m/Y') }}</td>
                                    <td>${{ number_format($schedule->monto, 2) }}</td>
                                    <td>${{ number_format($schedule->monto_pagado, 2) }}</td>
                                    <td>${{ number_format($schedule->monto - $schedule->monto_pagado, 2) }}</td>
                                    <td>
                                        @if($schedule->estado === 'pendiente')
                                            <span class="badge bg-warning">Pendiente</span>
                                        @elseif($schedule->estado === 'pagado')
                                            <span class="badge bg-success">Pagado</span>
                                        @elseif($schedule->estado === 'vencido')
                                            <span class="badge bg-danger">Vencido</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="d-flex justify-content-end gap-2">
        @can('edit matriculas')
        <a href="{{ route('admin.matriculas.edit', $matricula) }}" class="btn btn-primary">
            <i class="ri ri-pencil-line me-1"></i> Editar Matrícula
        </a>
        @endcan

        @can('delete matriculas')
        <button class="btn btn-danger" wire:click="$dispatch('delete', { id: {{ $matricula->id }} })" wire:confirm="¿Estás seguro de eliminar esta matrícula?">
            <i class="ri ri-delete-bin-line me-1"></i> Eliminar Matrícula
        </button>
        @endcan
    </div>
</div>
