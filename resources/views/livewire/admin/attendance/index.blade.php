<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-sm-6 col-xl-3 mb-3 mb-xl-0">
            <div class="card border-start border-primary border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ri ri-calendar-check-line ri-24px"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted">Total Registros</h6>
                            <h4 class="mb-0">{{ $this->stats['total'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3 mb-xl-0">
            <div class="card border-start border-success border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ri ri-user-follow-line ri-24px"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted">Presentes</h6>
                            <h4 class="mb-0">{{ $this->stats['present'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3 mb-xl-0">
            <div class="card border-start border-danger border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="ri ri-user-unfollow-line ri-24px"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted">Ausentes</h6>
                            <h4 class="mb-0">{{ $this->stats['absent'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-start border-info border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="ri ri-percent-line ri-24px"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted">Tasa Asistencia</h6>
                            <h4 class="mb-0">{{ $this->stats['attendance_rate'] }}%</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-0">Control de Asistencia</h5>
                <small class="text-muted">Registro de asistencia diaria de estudiantes</small>
            </div>
            <a href="{{ route('admin.attendance.register') }}" class="btn btn-primary">
                <i class="ri ri-add-line me-1"></i> Registrar Asistencia
            </a>
        </div>

        <!-- Filters -->
        <div class="card-header border-bottom">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Buscar Estudiante</label>
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Nombre o código...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Sección</label>
                    <select class="form-select" wire:model.live="section_id">
                        <option value="">Todas</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}">{{ $section->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Período</label>
                    <select class="form-select" wire:model.live="school_period_id">
                        <option value="">Todos</option>
                        @foreach($schoolPeriods as $period)
                            <option value="{{ $period->id }}">{{ $period->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select class="form-select" wire:model.live="status">
                        <option value="">Todos</option>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Desde</label>
                    <input type="date" class="form-control" wire:model.live="date_from">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Hasta</label>
                    <input type="date" class="form-control" wire:model.live="date_to">
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12 text-end">
                    <button wire:click="clearFilters" class="btn btn-secondary btn-sm">
                        <i class="ri ri-eraser-line me-1"></i> Limpiar Filtros
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card-datatable table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th wire:click="sortBy('date')" style="cursor: pointer;">
                            Fecha @if($sortBy === 'date') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-s-line"></i> @endif
                        </th>
                        <th>Código</th>
                        <th>Estudiante</th>
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
                            <td>{{ $attendance->student->codigo ?? '-' }}</td>
                            <td>{{ $attendance->student->nombres }} {{ $attendance->student->apellidos }}</td>
                            <td>{{ $attendance->section->nombre ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $attendance->status_color }}">
                                    {{ $attendance->status_label }}
                                </span>
                            </td>
                            <td>{{ $attendance->arrival_time ? $attendance->arrival_time->format('H:i') : '-' }}</td>
                            <td>{{ Str::limit($attendance->observations, 30) ?? '-' }}</td>
                            <td>{{ $attendance->registeredBy->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="ri ri-calendar-line ri-48px text-muted mb-2"></i>
                                <p class="text-muted mb-0">No se encontraron registros de asistencia</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div>
                <select class="form-select form-select-sm" wire:model.live="perPage" style="width: auto;">
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div>
                {{ $attendances->links('livewire.pagination') }}
            </div>
        </div>
    </div>
</div>
