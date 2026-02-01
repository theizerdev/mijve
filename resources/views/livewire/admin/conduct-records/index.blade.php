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
                                <i class="ri ri-book-2-line ri-24px"></i>
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
                                <i class="ri ri-thumb-up-line ri-24px"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted">Positivos</h6>
                            <h4 class="mb-0">{{ $this->stats['positive'] }}</h4>
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
                                <i class="ri ri-thumb-down-line ri-24px"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted">Negativos</h6>
                            <h4 class="mb-0">{{ $this->stats['negative'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-start border-warning border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="ri ri-alert-line ri-24px"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted">Sin Resolver</h6>
                            <h4 class="mb-0">{{ $this->stats['unresolved'] }}</h4>
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
                <h5 class="card-title mb-0">Libro de Vida</h5>
                <small class="text-muted">Registro de observaciones de conducta estudiantil</small>
            </div>
            <a href="{{ route('admin.conduct-records.create') }}" class="btn btn-primary">
                <i class="ri ri-add-line me-1"></i> Nueva Observación
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
                    <label class="form-label">Período</label>
                    <select class="form-select" wire:model.live="school_period_id">
                        <option value="">Todos</option>
                        @foreach($schoolPeriods as $period)
                            <option value="{{ $period->id }}">{{ $period->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tipo</label>
                    <select class="form-select" wire:model.live="type">
                        <option value="">Todos</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Gravedad</label>
                    <select class="form-select" wire:model.live="severity">
                        <option value="">Todas</option>
                        @foreach($severities as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select class="form-select" wire:model.live="resolved">
                        <option value="">Todos</option>
                        <option value="0">Pendiente</option>
                        <option value="1">Resuelto</option>
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
                        <th wire:click="sortBy('date')" style="cursor: pointer;">
                            Fecha @if($sortBy === 'date') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-s-line"></i> @endif
                        </th>
                        <th>Estudiante</th>
                        <th>Tipo</th>
                        <th>Gravedad</th>
                        <th>Categoría</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        <tr>
                            <td>{{ $record->date->format('d/m/Y') }}</td>
                            <td>
                                <strong>{{ $record->student->apellidos ?? '-' }}</strong>, {{ $record->student->nombres ?? '' }}
                                <br><small class="text-muted">{{ $record->student->codigo ?? '' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ $record->type_color }}">{{ $record->type_label }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $record->severity_color }}">{{ $record->severity_label }}</span>
                            </td>
                            <td>{{ $record->category_label ?? '-' }}</td>
                            <td>{{ Str::limit($record->description, 40) }}</td>
                            <td>
                                @if($record->resolved)
                                    <span class="badge bg-success">Resuelto</span>
                                @else
                                    <span class="badge bg-warning">Pendiente</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-icon" data-bs-toggle="dropdown">
                                        <i class="ri ri-more-2-line"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ route('admin.conduct-records.show', $record->id) }}" class="dropdown-item">
                                                <i class="ri ri-eye-line me-2"></i> Ver
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.conduct-records.student', $record->student_id) }}" class="dropdown-item">
                                                <i class="ri ri-history-line me-2"></i> Historial
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <button wire:click="delete({{ $record->id }})" 
                                                    wire:confirm="¿Está seguro de eliminar este registro?"
                                                    class="dropdown-item text-danger">
                                                <i class="ri ri-delete-bin-line me-2"></i> Eliminar
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="ri ri-book-2-line ri-48px text-muted mb-2"></i>
                                <p class="text-muted mb-0">No se encontraron registros</p>
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
                <option value="100">100</option>
            </select>
            <div>{{ $records->links('livewire.pagination') }}</div>
        </div>
    </div>
</div>
