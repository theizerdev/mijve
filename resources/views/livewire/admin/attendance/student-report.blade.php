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
                    <span class="badge bg-label-info">{{ $student->nivelEducativo->nombre ?? 'Sin nivel' }}</span>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Estadísticas de Asistencia</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col">
                            <h3 class="mb-0 text-primary">{{ $stats['total'] }}</h3>
                            <small class="text-muted">Total Días</small>
                        </div>
                        <div class="col">
                            <h3 class="mb-0 text-success">{{ $stats['present'] }}</h3>
                            <small class="text-muted">Presentes</small>
                        </div>
                        <div class="col">
                            <h3 class="mb-0 text-danger">{{ $stats['absent'] }}</h3>
                            <small class="text-muted">Ausentes</small>
                        </div>
                        <div class="col">
                            <h3 class="mb-0 text-warning">{{ $stats['late'] }}</h3>
                            <small class="text-muted">Tardanzas</small>
                        </div>
                        <div class="col">
                            <h3 class="mb-0 text-info">{{ $stats['attendance_rate'] }}%</h3>
                            <small class="text-muted">Asistencia</small>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 20px;">
                        @if($stats['total'] > 0)
                            <div class="progress-bar bg-success" style="width: {{ ($stats['present']/$stats['total'])*100 }}%">Presente</div>
                            <div class="progress-bar bg-warning" style="width: {{ ($stats['late']/$stats['total'])*100 }}%">Tarde</div>
                            <div class="progress-bar bg-danger" style="width: {{ ($stats['absent']/$stats['total'])*100 }}%">Ausente</div>
                            <div class="progress-bar bg-info" style="width: {{ ($stats['excused']/$stats['total'])*100 }}%">Justificado</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y tabla -->
    <div class="card">
        <div class="card-header border-bottom">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Período Escolar</label>
                    <select class="form-select" wire:model.live="school_period_id">
                        <option value="">Todos</option>
                        @foreach($schoolPeriods as $period)
                            <option value="{{ $period->id }}">{{ $period->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Desde</label>
                    <input type="date" class="form-control" wire:model.live="date_from">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Hasta</label>
                    <input type="date" class="form-control" wire:model.live="date_to">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <a href="{{ route('admin.students.show', $student->id) }}" class="btn btn-secondary w-100">
                        <i class="ri ri-arrow-left-line me-1"></i> Volver
                    </a>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Sección</th>
                        <th>Estado</th>
                        <th>Hora Llegada</th>
                        <th>Observaciones</th>
                        <th>Registrado por</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                        <tr>
                            <td>{{ $attendance->date->format('d/m/Y') }}</td>
                            <td>{{ $attendance->section->nombre ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $attendance->status_color }}">
                                    {{ $attendance->status_label }}
                                </span>
                            </td>
                            <td>{{ $attendance->arrival_time ? $attendance->arrival_time->format('H:i') : '-' }}</td>
                            <td>{{ $attendance->observations ?? '-' }}</td>
                            <td>{{ $attendance->registeredBy->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <p class="text-muted mb-0">No hay registros de asistencia</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
