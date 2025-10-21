<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Años Escolares</h2>
        <a href="{{ route('admin.school-years.create') }}" class="btn btn-primary">
            <i class="ri ri-add-circle-line me-1"></i> Nuevo Año Escolar
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Lista de Años Escolares</h5>
                <div class="d-flex">
                    <input wire:model.live.debounce.300ms="search" type="text" class="form-control form-control-sm me-2" placeholder="Buscar...">
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
                        @forelse($schoolYears as $schoolYear)
                            <tr>
                                <td>
                                    {{ $schoolYear->name }}
                                    @if($schoolYear->is_current)
                                        <span class="badge bg-primary">Actual</span>
                                    @endif
                                </td>
                                <td>{{ $schoolYear->start_date->format('d/m/Y') }}</td>
                                <td>{{ $schoolYear->end_date->format('d/m/Y') }}</td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            id="activeSwitch{{ $schoolYear->id }}"
                                            wire:click="toggleActive({{ $schoolYear->id }})"
                                            {{ $schoolYear->is_active ? 'checked' : '' }}
                                            {{ $schoolYear->is_current ? 'disabled' : '' }}
                                        >
                                        <label class="form-check-label" for="activeSwitch{{ $schoolYear->id }}">
                                            {{ $schoolYear->is_active ? 'Activo' : 'Inactivo' }}
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-text-secondary rounded-pill text-body-secondary border-0 p-1" type="button" id="actionsDropdown{{ $schoolYear->id }}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="ri ri-more-2-fill ri-24px"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="actionsDropdown{{ $schoolYear->id }}">
                                            <a class="dropdown-item" href="{{ route('admin.school-years.show', $schoolYear) }}">
                                                <i class="ri ri-eye-line me-1"></i> Ver
                                            </a>
                                            <a class="dropdown-item" href="{{ route('admin.school-years.edit', $schoolYear) }}">
                                                <i class="ri ri-pencil-line me-1"></i> Editar
                                            </a>
                                            @if(!$schoolYear->is_current)
                                                <button class="dropdown-item" wire:click="setCurrent({{ $schoolYear->id }})" wire:confirm="¿Estás seguro de establecer este año como el actual?">
                                                    <i class="ri ri-check-line me-1"></i> Establecer como actual
                                                </button>
                                            @endif
                                            @if(!$schoolYear->is_current)
                                                <button class="dropdown-item text-danger" wire:click="delete({{ $schoolYear->id }})" wire:confirm="¿Estás seguro de eliminar este año escolar?">
                                                    <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No se encontraron años escolares</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <div>

                </div>
                <div>
                     {{ $schoolYears->links('vendor.pagination.materialize') }}
                </div>
            </div>
        </div>
    </div>
</div>
