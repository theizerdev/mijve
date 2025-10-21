<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Detalles del Año Escolar</h2>
        <div>
            <a href="{{ route('admin.school-years.edit', $schoolYear) }}" class="btn btn-primary me-2">
                 <i class="ri ri-pencil-line me-1"></i> Editar
            </a>
            <a href="{{ route('admin.school-years.index') }}" class="btn btn-secondary">
                 <i class="ri ri-arrow-left-line me-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Información del Año Escolar</h5>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nombre:</label>
                            <p>{{ $schoolYear->name }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Estado:</label>
                            <p>
                                @if($schoolYear->is_active)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif

                                @if($schoolYear->is_current)
                                    <span class="badge bg-primary ms-1">Actual</span>
                                @endif
                            </p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Fecha de Inicio:</label>
                            <p>{{ $schoolYear->start_date->format('d/m/Y') }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Fecha de Fin:</label>
                            <p>{{ $schoolYear->end_date->format('d/m/Y') }}</p>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Descripción:</label>
                            <p>{{ $schoolYear->description ?? 'No se ha proporcionado una descripción.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Estadísticas</h5>
                </div>

                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Duración:</span>
                        <span class="fw-bold">{{ $schoolYear->start_date->diffInDays($schoolYear->end_date) }} días</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Días transcurridos:</span>
                        <span class="fw-bold">
                            @if(now()->between($schoolYear->start_date, $schoolYear->end_date))
                                {{ $schoolYear->start_date->diffInDays(now()) }} días
                            @elseif(now()->isAfter($schoolYear->end_date))
                                {{ $schoolYear->start_date->diffInDays($schoolYear->end_date) }} días
                            @else
                                0 días
                            @endif
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span>Progreso:</span>
                        <span class="fw-bold">
                            @if(now()->between($schoolYear->start_date, $schoolYear->end_date))
                                {{ round((now()->diffInDays($schoolYear->start_date) / $schoolYear->start_date->diffInDays($schoolYear->end_date)) * 100, 2) }}%
                            @elseif(now()->isAfter($schoolYear->end_date))
                                100%
                            @else
                                0%
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Acciones</h5>
                </div>

                <div class="card-body">
                    @if(!$schoolYear->is_current)
                        <button class="btn btn-outline-primary w-100 mb-2" wire:click="$dispatch('alert', {type: 'info', message: 'Funcionalidad pendiente de implementar'})">
                             <i class="ri ri-calendar-plus-line me-1"></i> Configurar Periodos
                        </button>
                    @endif

                    <button class="btn btn-outline-info w-100" wire:click="$dispatch('alert', {type: 'info', message: 'Funcionalidad pendiente de implementar'})">
                         <i class="ri ri-file-document-line me-1"></i> Generar Reporte
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
