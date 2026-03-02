<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Estadísticas -->
        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $totalMetodos }}</h4>
                            <p class="mb-0">Total Métodos</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ri ri-bank-card-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $metodosActivos }}</h4>
                            <p class="mb-0">Métodos Activos</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ri ri-checkbox-circle-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $metodosInactivos }}</h4>
                            <p class="mb-0">Métodos Inactivos</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="ri ri-close-circle-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Métodos de Pago</h5>
                            <p class="mb-0">Administra las formas de pago disponibles</p>
                        </div>
                        @can('create metodos_pago')
                        <div>
                            <a href="{{ route('admin.metodos-pago.create') }}" class="btn btn-primary">
                                <i class="ri ri-add-line"></i> Nuevo Método
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card-header border-bottom">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Buscar</label>
                            <input type="text" class="form-control" placeholder="Banco, cédula, nombre..."
                                   wire:model.live.debounce.300ms="search">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Tipo de Pago</label>
                            <select class="form-select" wire:model.live="tipo_pago">
                                <option value="">Todos</option>
                                <option value="Divisa">Divisa</option>
                                <option value="Pago Móvil">Pago Móvil</option>
                                <option value="Transferencia Bancaria">Transferencia Bancaria</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Empresa</label>
                            <select class="form-select" wire:model.live="empresa_id">
                                <option value="">Todas las empresas</option>
                                @foreach($empresas as $empresa)
                                    <option value="{{ $empresa->id }}">{{ $empresa->razon_social }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Mostrar</label>
                            <select class="form-select" wire:model.live="perPage">
                                <option value="10">10 por página</option>
                                <option value="25">25 por página</option>
                                <option value="50">50 por página</option>
                                <option value="100">100 por página</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card-datatable table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th wire:click="sortBy('tipo_pago')" style="cursor: pointer;">
                                    Tipo
                                    @if($sortBy === 'tipo_pago')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('banco')" style="cursor: pointer;">
                                    Banco
                                    @if($sortBy === 'banco')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <th>Detalles</th>
                                <th>Empresa</th>
                                <th wire:click="sortBy('status')" style="cursor: pointer;">
                                    Estado
                                    @if($sortBy === 'status')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($metodos as $metodo)
                            <tr>
                                <td>
                                    <span class="badge bg-label-primary">{{ $metodo->tipo_pago }}</span>
                                </td>
                                <td>{{ $metodo->banco ?? 'N/A' }}</td>
                                <td>
                                    @if($metodo->tipo_pago === 'Pago Móvil')
                                        <small class="d-block"><strong>Cédula:</strong> {{ $metodo->cedula }}</small>
                                        <small class="d-block"><strong>Teléfono:</strong> {{ $metodo->telefono }}</small>
                                    @elseif($metodo->tipo_pago === 'Transferencia Bancaria')
                                        <small class="d-block"><strong>Titular:</strong> {{ $metodo->nombre }} {{ $metodo->apellido }}</small>
                                        <small class="d-block"><strong>Cédula:</strong> {{ $metodo->cedula }}</small>
                                        <small class="d-block"><strong>Cuenta:</strong> {{ $metodo->numero_cuenta }}</small>
                                        <small class="d-block"><strong>Tipo:</strong> {{ $metodo->tipo_cuenta }}</small>
                                    @else
                                        <span class="text-muted">Sin detalles adicionales</span>
                                    @endif
                                </td>
                                <td>{{ $metodo->empresa->razon_social ?? 'N/A' }}</td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox"
                                               id="statusSwitch{{ $metodo->id }}"
                                               {{ $metodo->status ? 'checked' : '' }}
                                               @can('edit metodos_pago') wire:click="toggleStatus({{ $metodo->id }})" @endcan>
                                        <label class="form-check-label" for="statusSwitch{{ $metodo->id }}">
                                            {{ $metodo->status ? 'Activo' : 'Inactivo' }}
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="ri ri-more-2-line"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            @can('edit metodos_pago')
                                            <a class="dropdown-item" href="{{ route('admin.metodos-pago.edit', $metodo->id) }}">
                                                <i class="ri ri-pencil-line me-1"></i> Editar
                                            </a>
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No se encontraron métodos de pago que coincidan con los filtros</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="card-footer">
                   {{ $metodos->links('livewire.pagination') }}
                </div>
            </div>
        </div>
    </div>
</div>
