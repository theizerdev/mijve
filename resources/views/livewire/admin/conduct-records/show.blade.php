<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Detalle de Observación</h5>
                        <small class="text-muted">{{ $record->date->format('d/m/Y') }}</small>
                    </div>
                    <div>
                        <span class="badge bg-{{ $record->type_color }} fs-6 me-2">{{ $record->type_label }}</span>
                        <span class="badge bg-{{ $record->severity_color }} fs-6">{{ $record->severity_label }}</span>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Estudiante -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-lg me-3">
                                <span class="avatar-initial rounded-circle bg-primary">
                                    {{ substr($record->student->nombres, 0, 1) }}{{ substr($record->student->apellidos, 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $record->student->nombres }} {{ $record->student->apellidos }}</h6>
                                <small class="text-muted">Código: {{ $record->student->codigo }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Descripción</h6>
                        <p class="mb-0">{{ $record->description }}</p>
                    </div>

                    @if($record->actions_taken)
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Acciones Tomadas</h6>
                            <p class="mb-0">{{ $record->actions_taken }}</p>
                        </div>
                    @endif

                    @if($record->parent_notified)
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Notificación al Representante</h6>
                            <p class="mb-0">{{ $record->parent_notified }}</p>
                            @if($record->parent_notification_date)
                                <small class="text-muted">Fecha: {{ $record->parent_notification_date->format('d/m/Y') }}</small>
                            @endif
                        </div>
                    @endif

                    @if($record->follow_up_notes)
                        <div class="mb-4 p-3 border-start border-info border-4 bg-light">
                            <h6 class="text-info mb-2"><i class="ri ri-history-line me-1"></i> Seguimiento</h6>
                            <p class="mb-1">{{ $record->follow_up_notes }}</p>
                            @if($record->follow_up_date)
                                <small class="text-muted">Fecha: {{ $record->follow_up_date->format('d/m/Y') }}</small>
                            @endif
                        </div>
                    @endif

                    @if($record->resolved)
                        <div class="p-3 border-start border-success border-4 bg-light">
                            <h6 class="text-success mb-2"><i class="ri ri-checkbox-circle-line me-1"></i> Resuelto</h6>
                            <p class="mb-1">{{ $record->resolution_notes }}</p>
                            <small class="text-muted">
                                Fecha: {{ $record->resolution_date->format('d/m/Y') }} |
                                Por: {{ $record->resolvedByUser->name ?? '-' }}
                            </small>
                        </div>
                    @endif
                </div>

                <div class="card-footer">
                    <a href="{{ route('admin.conduct-records.index') }}" class="btn btn-secondary">
                        <i class="ri ri-arrow-left-line me-1"></i> Volver
                    </a>
                    <a href="{{ route('admin.conduct-records.student', $record->student_id) }}" class="btn btn-outline-primary">
                        <i class="ri ri-history-line me-1"></i> Ver Historial Completo
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Info adicional -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">Información</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted">Período:</td>
                            <td>{{ $record->schoolPeriod->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Sección:</td>
                            <td>{{ $record->section->nombre ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Categoría:</td>
                            <td>{{ $record->category_label ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Registrado por:</td>
                            <td>{{ $record->registeredByUser->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Fecha registro:</td>
                            <td>{{ $record->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Agregar seguimiento -->
            @if(!$record->resolved)
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Agregar Seguimiento</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Notas de Seguimiento</label>
                            <textarea class="form-control" wire:model="follow_up_notes" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha</label>
                            <input type="date" class="form-control" wire:model="follow_up_date">
                        </div>
                        <button class="btn btn-info w-100" wire:click="addFollowUp">
                            <i class="ri ri-add-line me-1"></i> Agregar
                        </button>
                    </div>
                </div>

                <!-- Marcar como resuelto -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">Marcar como Resuelto</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Notas de Resolución</label>
                            <textarea class="form-control" wire:model="resolution_notes" rows="3" placeholder="Describa cómo se resolvió el caso..."></textarea>
                        </div>
                        <button class="btn btn-success w-100" wire:click="markAsResolved">
                            <i class="ri ri-checkbox-circle-line me-1"></i> Resolver Caso
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
