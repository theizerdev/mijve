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
                                <i class="ri ri-file-text-line ri-24px"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted">Total Certificados</h6>
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
                                <i class="ri ri-checkbox-circle-line ri-24px"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted">Activos</h6>
                            <h4 class="mb-0">{{ $this->stats['active'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3 mb-xl-0">
            <div class="card border-start border-info border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="ri ri-calendar-line ri-24px"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted">Este Mes</h6>
                            <h4 class="mb-0">{{ $this->stats['this_month'] }}</h4>
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
                                <i class="ri ri-award-line ri-24px"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted">Constancias</h6>
                            <h4 class="mb-0">{{ $this->stats['by_type']['enrollment'] ?? 0 }}</h4>
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
                <h5 class="card-title mb-0">Constancias y Certificados</h5>
                <small class="text-muted">Gestión de documentos académicos oficiales</small>
            </div>
            <a href="{{ route('admin.certificates.create') }}" class="btn btn-primary">
                <i class="ri ri-add-line me-1"></i> Generar Certificado
            </a>
        </div>

        <!-- Filters -->
        <div class="card-header border-bottom">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Buscar</label>
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Estudiante o número...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tipo</label>
                    <select class="form-select" wire:model.live="certificate_type">
                        <option value="">Todos</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
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
                <div class="col-md-3 d-flex align-items-end">
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
                        <th>Estudiante</th>
                        <th>Tipo</th>
                        <th>Período</th>
                        <th>Fecha Emisión</th>
                        <th>Estado</th>
                        <th>Emitido por</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($certificates as $certificate)
                        <tr>
                            <td><code>{{ $certificate->certificate_number }}</code></td>
                            <td>
                                <strong>{{ $certificate->student->apellidos ?? '-' }}</strong>, 
                                {{ $certificate->student->nombres ?? '' }}
                                <br><small class="text-muted">{{ $certificate->student->codigo ?? '' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-label-primary">{{ $certificate->type_label }}</span>
                            </td>
                            <td>{{ $certificate->schoolPeriod->name ?? '-' }}</td>
                            <td>{{ $certificate->issue_date ? $certificate->issue_date->format('d/m/Y') : '-' }}</td>
                            <td>
                                @php
                                    $statusColors = ['active' => 'success', 'revoked' => 'danger', 'expired' => 'warning'];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$certificate->status] ?? 'secondary' }}">
                                    {{ $certificate->status_label }}
                                </span>
                            </td>
                            <td>{{ $certificate->issuedBy->name ?? $certificate->issued_by ?? '-' }}</td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-icon" data-bs-toggle="dropdown">
                                        <i class="ri ri-more-2-line"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ route('admin.certificates.show', $certificate->id) }}" class="dropdown-item">
                                                <i class="ri ri-eye-line me-2"></i> Ver
                                            </a>
                                        </li>
                                        @if($certificate->status === 'active')
                                            <li>
                                                <button wire:click="revoke({{ $certificate->id }})" 
                                                        wire:confirm="¿Está seguro de revocar este certificado?"
                                                        class="dropdown-item text-danger">
                                                    <i class="ri ri-close-circle-line me-2"></i> Revocar
                                                </button>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="ri ri-file-text-line ri-48px text-muted mb-2"></i>
                                <p class="text-muted mb-0">No se encontraron certificados</p>
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
                {{ $certificates->links('livewire.pagination') }}
            </div>
        </div>
    </div>
</div>
