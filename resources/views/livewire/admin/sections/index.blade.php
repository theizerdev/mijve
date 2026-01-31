<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Secciones</h4>
        @can('create sections')
        <a href="{{ route('admin.sections.create') }}" class="btn btn-primary">
            <i class="ri ri-add-line me-1"></i> Nueva Sección
        </a>
        @endcan
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text" id="search" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Código, nombre, materia, profesor...">
                </div>
                <div class="col-md-2">
                    <label for="empresa_id" class="form-label">Empresa</label>
                    <select id="empresa_id" class="form-select" wire:model.live="empresa_id">
                        <option value="">Todas</option>
                        @foreach($empresas as $empresa)
                        <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="sucursal_id" class="form-label">Sucursal</label>
                    <select id="sucursal_id" class="form-select" wire:model.live="sucursal_id" @empty($sucursales) disabled @endempty>
                        <option value="">Todas</option>
                        @foreach($sucursales as $sucursal)
                        <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="school_period_id" class="form-label">Período Escolar</label>
                    <select id="school_period_id" class="form-select" wire:model.live="school_period_id">
                        <option value="">Todos</option>
                        @foreach($school_periods as $period)
                        <option value="{{ $period->id }}">{{ $period->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="classroom_id" class="form-label">Aula</label>
                    <select id="classroom_id" class="form-select" wire:model.live="classroom_id" @empty($classrooms) disabled @endempty>
                        <option value="">Todas</option>
                        @foreach($classrooms as $classroom)
                        <option value="{{ $classroom->id }}">{{ $classroom->name }} - {{ $classroom->code }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="subject_id" class="form-label">Materia</label>
                    <select id="subject_id" class="form-select" wire:model.live="subject_id">
                        <option value="">Todas</option>
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="teacher_id" class="form-label">Profesor</label>
                    <select id="teacher_id" class="form-select" wire:model.live="teacher_id">
                        <option value="">Todos</option>
                        @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Estado</label>
                    <select id="status" class="form-select" wire:model.live="status">
                        <option value="">Todos</option>
                        <option value="active">Activo</option>
                        <option value="inactive">Inactivo</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Secciones -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th wire:click="sortBy('code')" style="cursor: pointer;">
                                Código
                                @if($sortField === 'code')
                                <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('name')" style="cursor: pointer;">
                                Nombre
                                @if($sortField === 'name')
                                <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                @endif
                            </th>
                            <th>Materia</th>
                            <th>Profesor</th>
                            <th>Aula</th>
                            <th>Período</th>
                            <th>Capacidad</th>
                            <th>Inscritos</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sections as $section)
                        <tr>
                            <td>{{ $section->code }}</td>
                            <td>{{ $section->name }}</td>
                            <td>{{ $section->subject->name }}</td>
                            <td>{{ $section->teacher->name }}</td>
                            <td>{{ $section->classroom->name }}</td>
                            <td>{{ $section->schoolPeriod->name }}</td>
                            <td>{{ $section->capacity }}</td>
                            <td>
                                <span class="badge bg-info">{{ $section->enrollments()->count() }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $section->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ $section->status === 'active' ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="ri ri-more-2-line"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @can('view sections')
                                        <a class="dropdown-item" href="{{ route('admin.sections.show', $section->id) }}">
                                            <i class="ri ri-eye-line me-1"></i> Ver
                                        </a>
                                        @endcan
                                        @can('edit sections')
                                        <a class="dropdown-item" href="{{ route('admin.sections.edit', $section->id) }}">
                                            <i class="ri ri-edit-line me-1"></i> Editar
                                        </a>
                                        @endcan
                                        @can('manage sections')
                                        <button class="dropdown-item" wire:click="toggleStatus({{ $section->id }})" onclick="confirm('¿Está seguro de cambiar el estado de esta sección?') || event.stopImmediatePropagation()">
                                            <i class="ri ri-toggle-line me-1"></i> {{ $section->status === 'active' ? 'Desactivar' : 'Activar' }}
                                        </button>
                                        @can('delete sections')
                                        <button class="dropdown-item text-danger" wire:click="deleteSection({{ $section->id }})" onclick="confirm('¿Está seguro de eliminar esta sección?') || event.stopImmediatePropagation()">
                                            <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                        </button>
                                        @endcan
                                        @endcan
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center">No se encontraron secciones</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $sections->links() }}
            </div>
        </div>
    </div>

    <!-- Mensajes Flash -->
    @if(session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
</div>