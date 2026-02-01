<div>
    <div class="row mb-4">
        <!-- Info del estudiante -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar avatar-xl mb-3">
                        <span class="avatar-initial rounded-circle bg-primary fs-1">
                            {{ substr($student->nombres, 0, 1) }}{{ substr($student->apellidos, 0, 1) }}
                        </span>
                    </div>
                    <h5 class="mb-1">{{ $student->nombres }} {{ $student->apellidos }}</h5>
                    <p class="text-muted mb-2"><code>{{ $student->codigo }}</code></p>
                    <a href="{{ route('admin.conduct-records.create') }}?student_id={{ $student->id }}" class="btn btn-sm btn-primary">
                        <i class="ri ri-add-line me-1"></i> Nueva Observación
                    </a>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Resumen de Conducta</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col">
                            <h3 class="mb-0 text-primary">{{ $stats['total'] }}</h3>
                            <small class="text-muted">Total</small>
                        </div>
                        <div class="col">
                            <h3 class="mb-0 text-success">{{ $stats['positive'] }}</h3>
                            <small class="text-muted">Positivos</small>
                        </div>
                        <div class="col">
                            <h3 class="mb-0 text-danger">{{ $stats['negative'] }}</h3>
                            <small class="text-muted">Negativos</small>
                        </div>
                        <div class="col">
                            <h3 class="mb-0 text-warning">{{ $stats['warnings'] }}</h3>
                            <small class="text-muted">Amonestaciones</small>
                        </div>
                        <div class="col">
                            <h3 class="mb-0 text-dark">{{ $stats['sanctions'] }}</h3>
                            <small class="text-muted">Sanciones</small>
                        </div>
                    </div>
                    @if($stats['unresolved'] > 0)
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="ri ri-alert-line me-1"></i>
                            {{ $stats['unresolved'] }} caso(s) pendiente(s) de resolver
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y lista -->
    <div class="card">
        <div class="card-header border-bottom">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Período Escolar</label>
                    <select class="form-select" wire:model.live="school_period_id">
                        <option value="">Todos</option>
                        @foreach($schoolPeriods as $period)
                            <option value="{{ $period->id }}">{{ $period->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tipo</label>
                    <select class="form-select" wire:model.live="type">
                        <option value="">Todos</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <a href="{{ route('admin.students.show', $student->id) }}" class="btn btn-secondary w-100">
                        <i class="ri ri-arrow-left-line me-1"></i> Volver al Estudiante
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="timeline-vertical p-4">
                @forelse($records as $record)
                    <div class="timeline-item mb-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <span class="badge bg-{{ $record->type_color }} rounded-circle p-2">
                                    @if($record->type === 'positive')
                                        <i class="ri ri-thumb-up-line"></i>
                                    @elseif($record->type === 'sanction')
                                        <i class="ri ri-error-warning-line"></i>
                                    @else
                                        <i class="ri ri-file-text-line"></i>
                                    @endif
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <span class="badge bg-{{ $record->type_color }}">{{ $record->type_label }}</span>
                                        <span class="badge bg-{{ $record->severity_color }}">{{ $record->severity_label }}</span>
                                        @if($record->category)
                                            <span class="badge bg-label-secondary">{{ $record->category_label }}</span>
                                        @endif
                                    </div>
                                    <small class="text-muted">{{ $record->date->format('d/m/Y') }}</small>
                                </div>
                                <p class="mb-2">{{ $record->description }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Por: {{ $record->registeredByUser->name ?? '-' }}</small>
                                    <div>
                                        @if($record->resolved)
                                            <span class="badge bg-success"><i class="ri ri-check-line"></i> Resuelto</span>
                                        @else
                                            <span class="badge bg-warning">Pendiente</span>
                                        @endif
                                        <a href="{{ route('admin.conduct-records.show', $record->id) }}" class="btn btn-sm btn-outline-primary ms-2">
                                            Ver
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="ri ri-book-2-line ri-48px text-muted"></i>
                        <p class="text-muted mt-2">No hay registros de conducta</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
