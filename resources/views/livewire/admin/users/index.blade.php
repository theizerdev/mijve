@php
    $headers = [
        ['key' => 'id', 'label' => '#', 'sortable' => true],
        ['key' => 'name', 'label' => 'Nombre', 'sortable' => true],
        ['key' => 'email', 'label' => 'Email', 'sortable' => true],
        ['key' => 'email_verified_at', 'label' => 'Verificado', 'sortable' => true, 'class' => 'text-center'],
        ['key' => 'empresa.nombre', 'label' => 'Empresa', 'sortable' => true],
        ['key' => 'sucursal', 'label' => 'Sucursal', 'sortable' => false],
        ['key' => 'status', 'label' => 'Estado', 'sortable' => true, 'class' => 'text-center'],
        ['key' => 'created_at', 'label' => 'Registro', 'sortable' => true],
        ['key' => 'actions', 'label' => 'Acciones', 'class' => 'text-center']
    ];
@endphp

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lista de Usuarios</h5>
        <div>
            <button wire:click="clearFilters" class="btn btn-outline-secondary me-2">
                <i class="ri ri-refresh-line"></i> Limpiar
            </button>
            @can('create users')
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="ri ri-add-line"></i> Nuevo Usuario
            </a>
            @endcan
        </div>
    </div>

    <div class="card-body">
        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-start border-primary border-4 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Usuarios</h6>
                                <h2 class="mb-0">{{ $totalUsers }}</h2>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="ri ri-group-line text-primary" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="text-success fw-semibold">
                                <i class="ri ri-arrow-up-line"></i> {{ round(($activeUsers/$totalUsers)*100) }}% Activos
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-start border-success border-4 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Usuarios Activos</h6>
                                <h2 class="mb-0">{{ $activeUsers }}</h2>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="ri ri-checkbox-circle-line text-success" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-start border-warning border-4 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Usuarios Pendientes</h6>
                                <h2 class="mb-0">{{ $pendingUsers }}</h2>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="ri ri-time-line text-warning" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-start border-danger border-4 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Usuarios Inactivos</h6>
                                <h2 class="mb-0">{{ $inactiveUsers }}</h2>
                            </div>
                            <div class="bg-danger bg-opacity-10 p-3 rounded">
                                <i class="ri ri-close-circle-line text-danger" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Buscar</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri ri-search-line"></i></span>
                            <input
                                type="text"
                                wire:model.live="search"
                                class="form-control"
                                placeholder="Nombre, email..."
                            >
                            <div wire:loading wire:target="search" class="input-group-text">
                                <i class="ri ri-loader-4-line spin"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Estado</label>
                        <div wire:ignore>
                            <select wire:model.live="status" class="form-select">
                                <option value="">Todos los estados</option>
                                <option value="active">Activo</option>
                                <option value="pending">Pendiente</option>
                                <option value="inactive">Inactivo</option>
                            </select>
                        </div>
                        <div wire:loading wire:target="status" class="small text-muted mt-1">Filtrando...</div>
                    </div>
                    <div class="col-md-2">
                        <label for="empresa_id" class="form-label">Empresa</label>
                        <div wire:ignore>
                            <select wire:model.live="empresa_id" class="form-select">
                                <option value="">Todas</option>
                                @foreach($empresas as $empresa)
                                    <option value="{{ $empresa->id }}">{{ $empresa->razon_social }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div wire:loading wire:target="empresa_id" class="small text-muted mt-1">Filtrando...</div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sucursal</label>
                        <select class="form-select" wire:model.live="sucursal_id">
                            <option value="">Todas las sucursales</option>
                            @foreach($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="perPage" class="form-label">Mostrar</label>
                        <select wire:model.live="perPage" class="form-select">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button wire:click="clearFilters" class="btn btn-outline-secondary w-100">
                            <i class="ri ri-refresh-line"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        @foreach($headers as $header)
                            <th
                                wire:click="sortBy('{{ $header['key'] }}')"
                                style="cursor: pointer;"
                                @class([
                                    'text-center' => isset($header['class']),
                                    'text-nowrap' => true
                                ])
                            >
                                {{ $header['label'] }}
                                @if($sortBy === $header['key'])
                                    <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                @endif
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>
                                {{ $user->email }}

                            </td>
                            <td class="text-center">
                                @if($user->email_verified_at)
                                    <span class="badge bg-label-success">Sí</span>
                                @else
                                    <span class="badge bg-label-danger">No</span>
                                @endif
                            </td>
                            <td>{{ $user->empresa->razon_social ?? 'N/A' }}</td>
                            <td>
                                 {{ $user->sucursal->nombre ?? 'N/A' }}
                            </td>
                            <td class="text-center">
                                <div class="form-check form-switch d-flex justify-content-center">
                                    <input
                                        type="checkbox"
                                        class="form-check-input"
                                        @can('edit users') wire:change="toggleStatus({{ $user->id }})" @endcan
                                        {{ $user->status ? 'checked' : '' }}
                                    >
                                </div>
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="ri ri-more-2-line"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                             <a href="{{ route('admin.users.show', $user) }}" class="dropdown-item">
                                                    <i class="ri ri-eye-line"></i>
                                                    Ver datos

                                                </a>
                                                @can('edit users')
                                                <a href="{{ route('admin.users.edit', $user) }}" class="dropdown-item">
                                                    <i class="ri ri-edit-line"></i>
                                                    Editar registros
                                                </a>
                                                @endcan

                                                @can('delete users')
                                                <button
                                                    wire:click="delete({{ $user->id }})"
                                                    class="dropdown-item"
                                                    onclick="return confirm('¿Estás seguro de eliminar este usuario?')"
                                                >
                                                    <i class="ri ri-delete-bin-line"></i>
                                                    Eliminar
                                                </button>
                                                @endcan
                                        </div>
                                    </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No se encontraron usuarios que coincidan con los filtros</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
             {{ $users->links('vendor.pagination.materialize') }}
        </div>
    </div>
</div>

@if(session()->has('message'))
    <script>
        document.addEventListener('livewire:load', function() {
            setTimeout(() => {
                Livewire.emit('refreshComponent');
            }, 3000);
        });
    </script>
@endif
