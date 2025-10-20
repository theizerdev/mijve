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
              <input type="text" class="form-control" placeholder="IP, ubicación, dispositivo..." wire:model.live.debounce.300ms="search">
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
                  Usuario
                  @if($sortBy === 'user.name')
                    <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                  @endif
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
                  @if($session->user_agent)
                    @if(Str::contains($session->user_agent, 'Windows'))
                      <i class="ri ri-windows-line text-primary me-1"></i> Windows
                    @elseif(Str::contains($session->user_agent, 'Mac'))
                      <i class="ri ri-mac-line text-secondary me-1"></i> Mac
                    @elseif(Str::contains($session->user_agent, 'Linux'))
                      <i class="ri ri-ubuntu-line text-warning me-1"></i> Linux
                    @elseif(Str::contains($session->user_agent, 'Android'))
                      <i class="ri ri-android-line text-success me-1"></i> Android
                    @elseif(Str::contains($session->user_agent, 'iPhone') || Str::contains($session->user_agent, 'iPad'))
                      <i class="ri ri-apple-line text-info me-1"></i> iOS
                    @else
                      <i class="ri ri-computer-line me-1"></i> Otro
                    @endif
                  @else
                    <span class="text-muted">Desconocido</span>
                  @endif
                </td>
                <td>{{ $session->ip_address }}</td>
                <td>
                  @if($session->location)
                    {{ $session->location }}
                  @else
                    Desconocida
                  @endif
                  @if($session->latitude && $session->longitude)
                    <br><small class="text-muted">({{ $session->latitude }}, {{ $session->longitude }})</small>
                  @endif
                </td>
                <td>{{ $session->last_activity->diffForHumans() }}</td>
                <td>
                  @if($session->is_active)
                    <span class="badge bg-label-success">Activa</span>
                  @else
                    <span class="badge bg-label-secondary">Inactiva</span>
                  @endif
                </td>
                <td>
                  @can('terminate active sessions')
                    @if(!$session->is_current && $session->is_active)
                      <button type="button"
                              class="btn btn-sm btn-danger"
                              wire:click="terminateSession({{ $session->id }})"
                              wire:confirm="¿Estás seguro de que deseas terminar esta sesión?">
                        Terminar Sesión
                      </button>
                    @elseif($session->is_current)
                      <button type="button"
                              class="btn btn-sm btn-danger"
                              wire:click="terminateSession({{ $session->id }})"
                              wire:confirm="¿Estás seguro de que deseas cerrar esta sesión?">
                        Cerrar Sesión
                      </button>
                    @else
                      <span class="text-muted">Sesión inactiva</span>
                    @endif
                  @else
                    @if($session->is_current)
                      <span class="badge bg-label-primary">Sesión actual</span>
                    @else
                      <span class="text-muted">Sin permisos</span>
                    @endif
                  @endcan
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center">No se encontraron sesiones que coincidan con los filtros</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Paginación -->
        <div class="card-footer">
          {{ $sessions->links('vendor.pagination.materialize') }}
        </div>
      </div>
    </div>
  </div>
</div>