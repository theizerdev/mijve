@extends('components.layouts.admin')

@section('title', 'Sesiones Activas')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header border-bottom">
        <h5 class="card-title mb-3">Sesiones Activas</h5>
        <p class="mb-0">Administra tus sesiones activas en diferentes dispositivos</p>
      </div>
      <div class="card-datatable table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Dispositivo</th>
              <th>IP</th>
              <th>Ubicación</th>
              <th>Última Actividad</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach($activeSessions as $session)
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <div class="me-2">
                    <i class="ri ri-computer-line ri-24px text-primary"></i>
                  </div>
                  <div>
                    <span class="d-block">{{ Str::limit($session->user_agent, 50) }}</span>
                    <small class="text-muted">
                      {{ $session->is_current ? 'Esta sesión' : 'Otra sesión' }}
                    </small>
                  </div>
                </div>
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
                @if(!$session->is_current && $session->is_active)
                  <form method="POST" action="{{ route('admin.active-sessions.destroy', $session->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger"
                            onclick="return confirm('¿Estás seguro de que deseas terminar esta sesión?')">
                      Terminar Sesión
                    </button>
                  </form>
                @elseif($session->is_current)
                  <span class="text-muted">Sesión actual</span>
                @else
                  <span class="text-muted">Sesión inactiva</span>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
