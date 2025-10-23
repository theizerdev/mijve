<div>
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

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="card-title mb-1">Lista de Estudiantes</h5>
            <p class="mb-0">Administra los estudiantes del sistema</p>
        </div>
        @can('create students')
        <div>
            <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
                <i class="ri ri-add-line"></i> Nuevo Estudiante
            </a>
        </div>
        @endcan
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0">Filtros</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text" class="form-control" id="search" placeholder="Nombre, código, documento..." wire:model.live.debounce.300ms="search">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Estado</label>
                    <select class="form-select" id="status" wire:model.live="status">
                        <option value="">Todos</option>
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="nivelEducativoId" class="form-label">Nivel Educativo</label>
                    <select class="form-select" id="nivelEducativoId" wire:model.live="nivelEducativoId">
                        <option value="">Todos</option>
                        @foreach($nivelesEducativos as $nivel)
                            <option value="{{ $nivel->id }}">{{ $nivel->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="perPage" class="form-label">Mostrar</label>
                    <select class="form-select" id="perPage" wire:model.live="perPage">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-outline-secondary" wire:click="clearFilters">
                    <i class="ri ri-delete-bin-line"></i> Limpiar Filtros
                </button>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-primary"><i class="ri ri-group-line ri-24px"></i></span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <h6 class="mb-0">Total Estudiantes</h6>
                            </div>
                            <div class="user-progress">
                                <h4 class="mb-0">{{ $totalStudents }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-success"><i class="ri ri-checkbox-circle-line ri-24px"></i></span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <h6 class="mb-0">Activos</h6>
                            </div>
                            <div class="user-progress">
                                <h4 class="mb-0">{{ $activeStudents }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-danger"><i class="ri ri-close-circle-line ri-24px"></i></span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <h6 class="mb-0">Inactivos</h6>
                            </div>
                            <div class="user-progress">
                                <h4 class="mb-0">{{ $inactiveStudents }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de estudiantes -->
    <div class="card">
        <div class="card-datatable table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th wire:click="sortBy('codigo')" style="cursor: pointer;">
                            Código QR @if($sortBy === 'codigo') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                        </th>
                        <th wire:click="sortBy('nombres')" style="cursor: pointer;">
                            Estudiante @if($sortBy === 'nombres') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                        </th>
                        <th wire:click="sortBy('grado')" style="cursor: pointer;">
                            Grado/Sección @if($sortBy === 'grado') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                        </th>
                        <th>Nivel Educativo</th>
                        <th>Contacto</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td>
                                <div class="d-flex flex-column align-items-center">
                                    <!-- Mostrar código QR directamente en la tabla -->
                                    <img src="{{ $student->generateQrCode(80) }}"
                                         alt="Código QR"
                                         class="img-fluid mb-1"
                                         style="max-width: 40px; cursor: pointer;"
                                         wire:click="downloadQrCode({{ $student->id }})"
                                         title="Haz clic para descargar el código QR">

                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($student->foto)
                                        <img src="{{ asset('storage/' . $student->foto) }}" alt="Foto" class="rounded-circle me-2" width="40" height="40">
                                    @else
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-initial rounded bg-label-primary">{{ substr($student->nombres, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <h6 class="mb-0">{{ $student->nombres }} {{ $student->apellidos }}</h6>
                                        <small class="text-muted">{{ $student->documento_identidad }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>{{ $student->grado }} - {{ $student->seccion }}</div>
                                <small class="text-muted">{{ $student->fecha_nacimiento->format('d/m/Y') }} ({{ $student->edad_con_meses }})</small>
                            </td>
                            <td>
                                @if($student->nivelEducativo)
                                    <div>{{ $student->nivelEducativo->nombre }}</div>
                                @else
                                    <span class="text-muted">No asignado</span>
                                @endif
                                @if($student->turno)
                                    <small class="text-muted">{{ $student->turno->nombre }}</small>
                                @endif
                            </td>
                            <td>
                                @if($student->esMenorDeEdad)
                                    @if($student->representante_nombres)
                                        <div class="d-flex align-items-center">
                                            <i class="ri ri-user-line text-muted me-1"></i>
                                            <span>{{ $student->representante_nombres }} {{ $student->representante_apellidos }}</span>
                                        </div>
                                        @if($student->representante_telefonos)
                                            <small class="text-muted">
                                                <i class="ri ri-phone-line me-1"></i>
                                                @if(is_array($student->representante_telefonos))
                                                    {{ implode(', ', $student->representante_telefonos) }}
                                                @else
                                                    {{ $student->representante_telefonos }}
                                                @endif
                                            </small>
                                        @endif
                                    @else
                                        <span class="badge bg-warning">Sin representante</span>
                                    @endif
                                @else
                                    @if($student->correo_electronico)
                                        <div class="d-flex align-items-center">
                                            <i class="ri ri-mail-line text-muted me-1"></i>
                                            <span>{{ $student->correo_electronico }}</span>
                                        </div>
                                    @else
                                        <span class="badge bg-warning">Sin correo</span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if($student->status)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-text-secondary rounded-pill text-body-secondary border-0 p-1" type="button" id="actionsDropdown{{ $student->id }}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="ri ri-more-2-fill ri-24px"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="actionsDropdown{{ $student->id }}">
                                        @can('view students')
                                        <a class="dropdown-item" href="{{ route('admin.students.show', $student) }}">
                                            <i class="ri ri-eye-line me-1"></i> Ver
                                        </a>
                                        @endcan
                                        @can('edit students')
                                        <a class="dropdown-item" href="{{ route('admin.students.edit', $student) }}">
                                            <i class="ri ri-pencil-line me-1"></i> Editar
                                        </a>
                                        {{-- Opción para enviar correo de bienvenida --}}
                                        <button class="dropdown-item" wire:click="sendWelcomeEmail({{ $student->id }})"
                                                wire:confirm="¿Estás seguro de enviar el correo de bienvenida a {{ $student->esMenorDeEdad ? 'su representante' : 'este estudiante' }}?">
                                            <i class="ri ri-mail-line me-1"></i> Correo de Bienvenida
                                        </button>
                                        @endcan
                                        @can('delete students')
                                        <button class="dropdown-item text-danger" wire:click="delete({{ $student->id }})" wire:confirm="¿Estás seguro de eliminar este estudiante?">
                                            <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                        </button>
                                        @endcan
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No se encontraron estudiantes</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    Mostrando {{ $students->firstItem() }} a {{ $students->lastItem() }} de {{ $students->total() }} estudiantes
                </div>
                <div>
                    {{ $students->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para mostrar el código QR (opcional, se puede eliminar si no se necesita) -->
    @if($showQrModal && $selectedStudent)
    <div class="modal fade show" tabindex="-1" style="display: block;" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Código QR del Estudiante</h5>
                    <button type="button" class="btn-close" wire:click="closeQrModal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <h6>{{ $selectedStudent->nombres }} {{ $selectedStudent->apellidos }}</h6>
                    <p class="text-muted">{{ $selectedStudent->codigo }}</p>

                    <img src="{{ $selectedStudent->generateQrCode(200) }}" alt="Código QR" class="img-fluid mb-3">

                    <p class="text-muted small">
                        Escanea este código QR para acceder a la información del estudiante
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeQrModal">Cerrar</button>
                    <button type="button" class="btn btn-primary" wire:click="downloadQrCode({{ $selectedStudent->id }})">
                        <i class="ri ri-download-line me-1"></i> Descargar QR
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif
</div>
