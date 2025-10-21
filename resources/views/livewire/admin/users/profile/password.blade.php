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
                                <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2">
                                    <li class="list-inline-item">
                                        <i class="ri ri-mail-line me-1"></i>
                                        <span class="text-muted">{{ $user->email }}</span>
                                    </li>
                                </ul>
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
            <a class="nav-link" href="{{ route('admin.users.profile', $user->id) }}">
                <i class="ri ri-user-line me-1"></i> Resumen
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('admin.users.password', $user->id) }}">
                <i class="ri ri-lock-line me-1"></i> Cambio de contraseña
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.users.history', $user->id) }}">
                <i class="ri ri-history-line me-1"></i> Historial
            </a>
        </li>
    </ul>

    <!-- Change Password Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Cambiar contraseña</h5>
        </div>
        <div class="card-body">
            @if(session('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif

            <form wire:submit.prevent="updatePassword">
                <div class="row">
                    <div class="mb-3 col-12">
                        <label class="form-label" for="current_password">Contraseña actual</label>
                        <input
                            type="password"
                            class="form-control @error('current_password') is-invalid @enderror"
                            id="current_password"
                            wire:model="current_password"
                            placeholder="Ingrese su contraseña actual"
                        />
                        @error('current_password') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3 col-md-6 col-12">
                        <label class="form-label" for="password">Nueva contraseña</label>
                        <input
                            type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            id="password"
                            wire:model="password"
                            placeholder="Ingrese nueva contraseña"
                        />
                        @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3 col-md-6 col-12">
                        <label class="form-label" for="password_confirmation">Confirmar contraseña</label>
                        <input
                            type="password"
                            class="form-control @error('password_confirmation') is-invalid @enderror"
                            id="password_confirmation"
                            wire:model="password_confirmation"
                            placeholder="Confirme la nueva contraseña"
                        />
                        @error('password_confirmation') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <span wire:loading.remove>Actualizar contraseña</span>
                            <span wire:loading>Actualizando...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="/materialize/assets/vendor/css/pages/page-profile.css" />
@endpush