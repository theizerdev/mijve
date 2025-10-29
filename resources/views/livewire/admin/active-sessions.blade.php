<div>
    @if (session()->has('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Sesiones Activas</h5>
                            <p class="mb-0">Administra tus sesiones activas en diferentes dispositivos</p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary" wire:click="loadSessions">
                                <i class="ri ri-refresh-line"></i> Actualizar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card-header border-bottom">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Buscar</label>
                            <input type="text" class="form-control" placeholder="IP, ubicación, dispositivo..." 
                                   wire:model.live.debounce.300ms="search">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" wire:model.live="status">
                                <option value="">Todos los estados</option>
                                <option value="active">Activa</option>
                                <option value="inactive">Inactiva</option>
                                <option value="current">Sesión actual</option>
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

                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-label-secondary" wire:click="clearFilters">
                                <i class="ri ri-eraser-line"></i> Limpiar filtros
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-datatable table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th wire:click="sortBy('user.name')" style="cursor: pointer;">
                                    Usuario @if($sortBy === 'user.name') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th>Dispositivo</th>
                                <th>IP</th>
                                <th>Ubicación</th>
                                <th>Última Actividad</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sessions as $session)
                                <tr>
                                    <td>
                                        @if($session->user)
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3">
                                                    <span class="avatar-initial rounded-circle bg-label-primary">{{ substr($session->user->name, 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $session->user->name }}</h6>
                                                    <small class="text-muted">{{ $session->user->email }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">Usuario no encontrado</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded bg-label-secondary">
                                                    @if(str_contains(strtolower($session->user_agent), 'mobile'))
                                                        <i class="ri ri-smartphone-line"></i>
                                                    @elseif(str_contains(strtolower($session->user_agent), 'tablet'))
                                                        <i class="ri ri-tablet-line"></i>
                                                    @else
                                                        <i class="ri ri-computer-line"></i>
                                                    @endif
                                                </span>
                                            </div>
                                            <div>
                                                @if(str_contains(strtolower($session->user_agent), 'mobile'))
                                                    Móvil
                                                @elseif(str_contains(strtolower($session->user_agent), 'tablet'))
                                                    Tablet
                                                @else
                                                    Computadora
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $session->ip_address }}</td>
                                    <td>
                                        @if($session->location)
                                            {{ $session->location }}
                                        @else
                                            <span class="text-muted">Desconocida</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($session->last_activity)
                                            {{ $session->last_activity->diffForHumans() }}
                                        @else
                                            <span class="text-muted">Desconocida</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($session->is_current_device)
                                            <span class="badge bg-primary">Sesión actual</span>
                                        @elseif($session->is_active)
                                            <span class="badge bg-success">Activa</span>
                                        @else
                                            <span class="badge bg-secondary">Inactiva</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$session->is_current_device)
                                            <button class="btn btn-sm btn-danger" wire:click="terminateSession('{{ $session->id }}')" wire:confirm="¿Estás seguro de terminar esta sesión?">
                                                <i class="ri ri-logout-box-line me-1"></i> Terminar
                                            </button>
                                        @else
                                            <span class="text-muted">Sesión actual</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No se encontraron sesiones activas</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            Mostrando {{ $sessions->firstItem() }} a {{ $sessions->lastItem() }} de {{ $sessions->total() }} resultados
                        </div>
                        <div>
                            {{ $sessions->links('vendor.pagination.materialize') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>