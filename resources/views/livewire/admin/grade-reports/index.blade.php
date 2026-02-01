<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
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
                                <i class="ri ri-file-list-3-line ri-24px"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted">Total Actas</h6>
                            <h4 class="mb-0">{{ $this->stats['total'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3 mb-xl-0">
            <div class="card border-start border-secondary border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-secondary">
                                <i class="ri ri-draft-line ri-24px"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted">Borradores</h6>
                            <h4 class="mb-0">{{ $this->stats['draft'] }}</h4>
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
                                <i class="ri ri-checkbox-circle-line ri-24px"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted">Aprobadas</h6>
                            <h4 class="mb-0">{{ $this->stats['approved'] }}</h4>
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
                                <i class="ri ri-send-plane-line ri-24px"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted">Publicadas</h6>
                            <h4 class="mb-0">{{ $this->stats['published'] }}</h4>
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
                <h5 class="card-title mb-0">Actas de Notas</h5>
                <small class="text-muted">Gestión de actas de calificaciones oficiales</small>
            </div>
            <a href="{{ route('admin.grade-reports.create') }}" class="btn btn-primary">
                <i class="ri ri-add-line me-1"></i> Generar Acta
            </a>
        </div>

        <!-- Filters -->
        <div class="card-header border-bottom">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Buscar</label>
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Número o título...">
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
                    <label class="form-label">Sección</label>
                    <select class="form-select" wire:model.live="section_id">
                        <option value="">Todas</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}">{{ $section->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tipo</label>
                    <select class="form-select" wire:model.live="report_type">
                        <option value="">Todos</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
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
                <div class="col-md-2 d-flex align-items-end">
                    <button wire:click="clearFilters" class="btn btn-secondary w-100">
                        <i class="ri ri-eraser-line me-1"></i> Limpiar
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card-datatable table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>Sección</th>
                        <th>Período</th>
                        <th>Estudiantes</th>
                        <th>Promedio</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                        <tr>
                            <td><code>{{ $report->report_number }}</code></td>
                            <td>{{ Str::limit($report->title, 30) }}</td>
                            <td><span class="badge bg-label-primary">{{ $report->type_label }}</span></td>
                            <td>{{ $report->section->nombre ?? '-' }}</td>
                            <td>{{ $report->schoolPeriod->name ?? '-' }}</td>
                            <td>
                                <span class="text-success">{{ $report->approved_count }}</span> /
                                <span class="text-danger">{{ $report->failed_count }}</span>
                                <small class="text-muted">({{ $report->total_students }})</small>
                            </td>
                            <td>
                                <strong>{{ number_format($report->average_grade, 2) }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-{{ $report->status_color }}">{{ $report->status_label }}</span>
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-icon" data-bs-toggle="dropdown">
                                        <i class="ri ri-more-2-line"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ route('admin.grade-reports.show', $report->id) }}" class="dropdown-item">
                                                <i class="ri ri-eye-line me-2"></i> Ver
                                            </a>
                                        </li>
                                        @if($report->status === 'draft')
                                            <li>
                                                <button wire:click="delete({{ $report->id }})" 
                                                        wire:confirm="¿Eliminar esta acta?"
                                                        class="dropdown-item text-danger">
                                                    <i class="ri ri-delete-bin-line me-2"></i> Eliminar
                                                </button>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="ri ri-file-list-3-line ri-48px text-muted mb-2"></i>
                                <p class="text-muted mb-0">No se encontraron actas</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="card-footer d-flex justify-content-between align-items-center">
            <select class="form-select form-select-sm" wire:model.live="perPage" style="width: auto;">
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            <div>{{ $reports->links('livewire.pagination') }}</div>
        </div>
    </div>
</div>
