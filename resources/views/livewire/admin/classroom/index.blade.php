<div>
    <div class="mb-4">
        <div class="row">
            <div class="col-md-6">
                <h4 class="card-title">Gestión de Aulas</h4>
                <p class="card-title-desc">Administra los aulas del instituto</p>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.classrooms.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nueva Aula
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            {{-- Filtros --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" placeholder="Buscar..." wire:model.debounce.300ms="search">
                </div>
                <div class="col-md-2">
                    <select class="form-select" wire:model="empresa_id">
                        <option value="">Todas las Empresas</option>
                        @foreach($empresas as $empresa)
                            <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" wire:model="sucursal_id">
                        <option value="">Todas las Sucursales</option>
                        @foreach($sucursales as $sucursal)
                            <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" wire:model="tipo_aula">
                        <option value="">Todos los Tipos</option>
                        <option value="regular">Regular</option>
                        <option value="laboratorio">Laboratorio</option>
                        <option value="taller">Taller</option>
                        <option value="auditorio">Auditorio</option>
                        <option value="biblioteca">Biblioteca</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" wire:model="estado">
                        <option value="">Todos los Estados</option>
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-outline-secondary" wire:click="resetFilters">
                        <i class="fas fa-refresh"></i>
                    </button>
                </div>
            </div>

            {{-- Tabla --}}
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th wire:click="sortBy('codigo')" style="cursor: pointer;">
                                Código
                                @if($sortField === 'codigo')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('nombre')" style="cursor: pointer;">
                                Nombre
                                @if($sortField === 'nombre')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th>Tipo</th>
                            <th>Capacidad</th>
                            <th>Ubicación</th>
                            <th>Empresa</th>
                            <th>Sucursal</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classrooms as $classroom)
                            <tr>
                                <td>{{ $classroom->codigo }}</td>
                                <td>{{ $classroom->nombre }}</td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ ucfirst($classroom->tipo_aula) }}
                                    </span>
                                </td>
                                <td>{{ $classroom->capacidad }} estudiantes</td>
                                <td>{{ $classroom->ubicacion ?? 'N/A' }}</td>
                                <td>{{ $classroom->empresa->nombre ?? 'N/A' }}</td>
                                <td>{{ $classroom->sucursal->nombre ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $classroom->is_active ? 'success' : 'danger' }}">
                                        {{ $classroom->is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Acciones
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.classrooms.show', $classroom) }}">
                                                    <i class="fas fa-eye"></i> Ver
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.classrooms.edit', $classroom) }}">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>
                                            </li>
                                            <li>
                                                <button class="dropdown-item" wire:click="toggleStatus({{ $classroom->id }})">
                                                    <i class="fas fa-toggle-{{ $classroom->is_active ? 'off' : 'on' }}"></i>
                                                    {{ $classroom->is_active ? 'Desactivar' : 'Activar' }}
                                                </button>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button class="dropdown-item text-danger" wire:click="confirmDelete({{ $classroom->id }})">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No se encontraron aulas</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <select class="form-select" wire:model="perPage">
                        <option value="10">10 por página</option>
                        <option value="25">25 por página</option>
                        <option value="50">50 por página</option>
                        <option value="100">100 por página</option>
                    </select>
                </div>
                <div>
                    {{ $classrooms->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de confirmación de eliminación --}}
    @if($confirmingDeletion)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmar Eliminación</h5>
                        <button type="button" class="btn-close" wire:click="$set('confirmingDeletion', false)"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Está seguro de que desea eliminar este aula?</p>
                        <p class="text-warning"><strong>Nota:</strong> No se puede eliminar si tiene horarios asignados.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="$set('confirmingDeletion', false)">Cancelar</button>
                        <button type="button" class="btn btn-danger" wire:click="delete">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>