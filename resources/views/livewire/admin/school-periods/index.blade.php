<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Períodos Escolares</h2>
        <a href="{{ route('admin.school-periods.create') }}" class="btn btn-primary">
            <i class="ri ri-add-circle-line me-1"></i> Nuevo Período Escolar
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Lista de Períodos Escolares</h5>
                <div class="d-flex gap-2">
                    <!-- Filtros avanzados -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="ri ri-filter-line me-1"></i> Filtros
                        </button>
                        <div class="dropdown-menu dropdown-menu-end p-3" style="width: 300px;">
                            <div class="mb-3">
                                <label class="form-label">Estado</label>
                                <select wire:model="filters.status" class="form-select form-select-sm">
                                    <option value="">Todos</option>
                                    <option value="active">Activos</option>
                                    <option value="inactive">Inactivos</option>
                                    <option value="current">Actual</option>
                                    <option value="past">Pasados</option>
                                    <option value="future">Futuros</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Rango de fechas</label>
                                <input type="text" class="form-control form-control-sm date-range-picker" 
                                       wire:ignore
                                       wire:model="filters.date_range">
                            </div>
                            <button wire:click="resetFilters" class="btn btn-sm btn-outline-danger w-100">
                                <i class="ri ri-close-line me-1"></i> Limpiar filtros
                            </button>
                        </div>
                    </div>

                    <!-- Exportar -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="ri ri-download-line me-1"></i> Exportar
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <button wire:click="exportExcel" class="dropdown-item">
                                <i class="ri ri-file-excel-line me-1"></i> Excel
                            </button>
                            <button wire:click="exportPDF" class="dropdown-item">
                                <i class="ri ri-file-pdf-line me-1"></i> PDF
                            </button>
                        </div>
                    </div>

                    <!-- Búsqueda y paginación -->
                    <input wire:model.live.debounce.300ms="search" type="text" class="form-control form-control-sm" placeholder="Buscar..." style="width: 200px;">
                    <select wire:model.live="perPage" class="form-select form-select-sm" style="width: auto;">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card-body">
            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th wire:click="sortBy('name')" style="cursor: pointer;">
                                Nombre @if($sortField == 'name') <i class="ri ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> @endif
                            </th>
                            <th wire:click="sortBy('start_date')" style="cursor: pointer;">
                                Fecha Inicio @if($sortField == 'start_date') <i class="ri ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> @endif
                            </th>
                            <th wire:click="sortBy('end_date')" style="cursor: pointer;">
                                Fecha Fin @if($sortField == 'end_date') <i class="ri ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> @endif
                            </th>
                            <th wire:click="sortBy('is_active')" style="cursor: pointer;">
                                Estado @if($sortField == 'is_active') <i class="ri ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> @endif
                            </th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schoolPeriods as $schoolPeriod)
                            <tr>
                                <td>
                                    {{ $schoolPeriod->name }}
                                    @if($schoolPeriod->is_current)
                                        <span class="badge bg-primary">Actual</span>
                                    @endif
                                </td>
                                <td>{{ $schoolPeriod->start_date->format('d/m/Y') }}</td>
                                <td>{{ $schoolPeriod->end_date->format('d/m/Y') }}</td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            id="activeSwitch{{ $schoolPeriod->id }}"
                                            wire:click="toggleActive({{ $schoolPeriod->id }})"
                                            {{ $schoolPeriod->is_active ? 'checked' : '' }}
                                            {{ $schoolPeriod->is_current ? 'disabled' : '' }}
                                        >
                                        <label class="form-check-label" for="activeSwitch{{ $schoolPeriod->id }}">
                                            {{ $schoolPeriod->is_active ? 'Activo' : 'Inactivo' }}
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-text-secondary rounded-pill text-body-secondary border-0 p-1" type="button" id="actionsDropdown{{ $schoolPeriod->id }}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="ri ri-more-2-fill ri-24px"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="actionsDropdown{{ $schoolPeriod->id }}">
                                            <a class="dropdown-item" href="{{ route('admin.school-periods.show', $schoolPeriod) }}">
                                                <i class="ri ri-eye-line me-1"></i> Ver
                                            </a>
                                            <a class="dropdown-item" href="{{ route('admin.school-periods.edit', $schoolPeriod) }}">
                                                <i class="ri ri-pencil-line me-1"></i> Editar
                                            </a>
                                            @if(!$schoolPeriod->is_current)
                                                <button class="dropdown-item" wire:click="setCurrent({{ $schoolPeriod->id }})" wire:confirm="¿Estás seguro de establecer este período como el actual?">
                                                    <i class="ri ri-check-line me-1"></i> Establecer como actual
                                                </button>
                                            @endif
                                            @if(!$schoolPeriod->is_current)
                                                <button class="dropdown-item text-danger" wire:click="delete({{ $schoolPeriod->id }})" wire:confirm="¿Estás seguro de eliminar este período escolar?">
                                                    <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No se encontraron períodos escolares</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
                <div class="card-footer">
                    {{ $schoolPeriods->links('vendor.pagination.materialize') }}
                </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('materialize/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />
@endpush

@push('scripts')
<script src="{{ asset('materialize/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
<script>
    document.addEventListener('livewire:initialized', () => {
        // Esperar a que jQuery y el plugin estén disponibles
        if (typeof $().daterangepicker === 'function') {
            initDateRangePicker();
        } else {
            const interval = setInterval(() => {
                if (typeof $().daterangepicker === 'function') {
                    clearInterval(interval);
                    initDateRangePicker();
                }
            }, 100);
        }

        function initDateRangePicker() {
            $('.date-range-picker').daterangepicker({
                locale: {
                    format: 'DD/MM/YYYY',
                    applyLabel: 'Aplicar',
                    cancelLabel: 'Cancelar',
                    fromLabel: 'Desde',
                    toLabel: 'Hasta',
                    customRangeLabel: 'Personalizado',
                    daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                    monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                                'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                    firstDay: 1
                },
                opens: 'left',
                autoUpdateInput: false
            });

            // Actualizar el input cuando se selecciona un rango
            $('.date-range-picker').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                @this.set('filters.date_range', $(this).val());
            });

            // Limpiar el input cuando se cancela
            $('.date-range-picker').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                @this.set('filters.date_range', '');
            });
        }
    });
</script>
@endpush
