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
                        @if($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}" alt="user image" class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img">
                        @else
                            <div class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img bg-light d-flex justify-content-center align-items-center">
                                <i class="ri ri-user-line" style="font-size: 4rem;"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-grow-1 mt-3 mt-sm-5">
                        <div class="d-flex align-items-md-end align-items-sm-start align-items-center flex-md-row flex-column flex-sm-row flex-column justify-content-between mx-4 mx-sm-0">
                            <div class="user-profile-info">
                                <h4>{{ $user->name }}</h4>
                                <small class="text-muted">{{ $user->email }}</small>
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
            <a class="nav-link active" href="javascript:void(0);">
                <i class="ri ri-user-line me-1"></i> Resumen
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.users.password', $user->id) }}">
                <i class="ri ri-lock-line me-1"></i> Cambio de contraseña
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.users.history', $user->id) }}">
                <i class="ri ri-history-line me-1"></i> Historial
            </a>
        </li>
    </ul>

    <!-- Avatar Upload -->
    <livewire:admin.users.profile.avatar-upload :user="$user" />

    <!-- Two Factor Auth -->
    <livewire:admin.users.profile.two-factor-auth :user="$user" />

    <!-- Profile Info -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Información del Perfil</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" value="{{ $user->name }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="text" class="form-control" value="{{ $user->email }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Rol</label>
                        <input type="text" class="form-control" value="{{ $user->role }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Fecha de Registro</label>
                        <input type="text" class="form-control" value="{{ $user->created_at->format('d/m/Y H:i') }}" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="/materialize/assets/vendor/css/pages/page-profile.css" />
@endpush
