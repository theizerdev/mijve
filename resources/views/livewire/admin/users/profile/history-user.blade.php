<div>
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="user-profile-header-banner">
                    <img src="{{ asset('materialize/assets/img/pages/profile-banner.png') }}" alt="Banner image" class="rounded-top">
                </div>
                <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
                    <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
                        <img src="{{ asset('materialize/assets/img/avatars/1.png') }}" alt="user image" class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img">
                    </div>
                    <div class="flex-grow-1 mt-3 mt-sm-5">
                        <div class="d-flex align-items-md-end align-items-sm-start align-items-center flex-md-row flex-column flex-sm-row flex-column justify-content-between mx-4 mx-sm-0">
                            <div class="user-profile-info">
                                <h4>Historial de Sesiones</h4>
                                <small class="text-muted">Últimos 30 días</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nav tabs -->
    <ul class="nav nav-pills flex-column flex-sm-row mb-4">
        <li class="nav-item">
            <a class="nav-link " href="/admin/profile">
                <i class="ri ri-user-line me-1"></i> Resumen
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.users.password', $user->id) }}">
                <i class="ri ri-lock-line me-1"></i> Cambio de contraseña
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('admin.users.history', $user->id) }}">
                <i class="ri ri-history-line me-1"></i> Historial
            </a>
        </li>
    </ul>

    <!-- Sessions Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Registro de Sesiones</h5>
            <small class="text-muted">Mostrando {{ $sessions->count() }} de {{ $sessions->total() }} registros</small>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>IP</th>
                            <th>Ubicación</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $session)
                        <tr>
                            <td>{{ $session->login_at->format('d/m/Y') }}</td>
                            <td>{{ $session->login_at->format('H:i:s') }}</td>
                            <td>{{ $session->ip_address }}</td>
                            <td>{{ $session->location ?? 'Desconocido' }}</td>
                            <td>
                                <span class="badge bg-{{ $session->is_active ? 'success' : 'secondary' }}">
                                    {{ $session->is_active ? 'Activa' : 'Finalizada' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No hay registros de sesiones</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $sessions->links() }}
            </div>
        </div>
    </div>

    <!-- Common Locations -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Ubicaciones Comunes</h5>
        </div>
        <div class="card-body">
            @if($user->common_locations->count() > 0)
                <ul class="list-group">
                    @foreach($user->common_locations as $location)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="ri ri-map-pin-line me-2"></i>
                            {{ $location['city'] }}, {{ $location['state'] }}, {{ $location['country'] }}
                        </div>
                        <span class="badge bg-primary rounded-pill">{{ $loop->iteration }}</span>
                    </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted mb-0">No hay datos de ubicaciones disponibles</p>
            @endif
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="/materialize/assets/vendor/css/pages/page-profile.css" />
@endpush