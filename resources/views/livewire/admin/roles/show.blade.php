<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detalles del Rol</h5>
                <div>
                    @can('edit roles')
                        @if(!in_array($role->name, ['super-admin', 'admin', 'empresa-admin', 'user']))
                            <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-primary">
                                <i class="ri ri-pencil-line"></i> Editar
                            </a>
                        @endif
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre del Rol</label>
                            <p class="form-control-plaintext">{{ $role->name }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Fecha de Creación</label>
                            <p class="form-control-plaintext">{{ $role->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="row">
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1">{{ $totalUsers }}</h4>
                                <p class="mb-0">Usuarios</p>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="ri ri-group-line ri-24px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1">{{ $totalPermissions }}</h4>
                                <p class="mb-0">Permisos</p>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="ri ri-shield-keyhole-line ri-24px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Usuarios con este rol -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Usuarios con este Rol</h5>
            </div>
            <div class="card-body">
                @if($role->users->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Empresa</th>
                                    <th>Fecha de Registro</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($role->users->take(10) as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->empresa->razon_social ?? 'N/A' }}</td>
                                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                </tr>
                                @endforeach
                                
                                @if($role->users->count() > 10)
                                <tr>
                                    <td colspan="4" class="text-center">
                                        <span class="text-muted">Mostrando 10 de {{ $role->users->count() }} usuarios</span>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">No hay usuarios asignados a este rol.</p>
                @endif
            </div>
        </div>

        <!-- Permisos del rol -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Permisos Asignados</h5>
            </div>
            <div class="card-body">
                @if(count($groupedPermissions) > 0)
                    <div class="accordion" id="permissionsAccordion">
                        @foreach($groupedPermissions as $module => $permissions)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ ucfirst($module) }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ ucfirst($module) }}" aria-expanded="false" aria-controls="collapse{{ ucfirst($module) }}">
                                    <strong>{{ ucfirst($module) }}</strong> <span class="badge bg-primary ms-2">{{ count($permissions) }}</span>
                                </button>
                            </h2>
                            <div id="collapse{{ ucfirst($module) }}" class="accordion-collapse collapse" aria-labelledby="heading{{ ucfirst($module) }}" data-bs-parent="#permissionsAccordion">
                                <div class="accordion-body">
                                    <div class="row">
                                        @foreach($permissions as $permission)
                                        <div class="col-md-6 col-lg-4 mb-2">
                                            <div class="d-flex align-items-center">
                                                <i class="ri ri-check-line text-success me-2"></i>
                                                <span>{{ ucfirst(str_replace('-', ' ', $permission->name)) }}</span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0">Este rol no tiene permisos asignados.</p>
                @endif
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                <i class="ri ri-arrow-left-line"></i> Volver
            </a>
        </div>
    </div>
</div>