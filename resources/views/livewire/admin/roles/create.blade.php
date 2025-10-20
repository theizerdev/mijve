<div>
    <div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Crear Nuevo Rol</h5>
                <p class="text-muted mb-0">Define un nuevo rol y asigna los permisos correspondientes</p>
            </div>
            <div class="card-body">
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

                <form wire:submit.prevent="save">
                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold">Nombre del Rol</label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               wire:model="name"
                               placeholder="Ej. editor, supervisor, etc.">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">El nombre debe ser único y descriptivo</div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-0 fw-bold">Permisos del Rol</h6>
                                <p class="text-muted mb-0">Selecciona los permisos que tendrá este rol</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary me-3">{{ count($selectedPermissions) }} permisos seleccionados</span>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="selectAll"
                                           wire:click="toggleSelectAll"
                                           @if($selectAll) checked @endif>
                                    <label class="form-check-label fw-bold" for="selectAll">
                                        Seleccionar todos
                                    </label>
                                </div>
                            </div>
                        </div>

                        @if(count($groupedPermissions) > 0)
                            <div class="row">
                                @foreach($groupedPermissions as $module => $permissions)
                                <div class="col-xl-4 col-md-6 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header ">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0">{{ ucfirst($module) }}</h6>
                                                @php
                                                    $moduleSelectedCount = 0;
                                                    foreach($permissions as $permission) {
                                                        if (in_array($permission->id, $selectedPermissions)) {
                                                            $moduleSelectedCount++;
                                                        }
                                                    }
                                                @endphp
                                                <span class="badge bg-primary">{{ $moduleSelectedCount }}/{{ count($permissions) }}</span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       id="toggleAll{{ ucfirst($module) }}"
                                                       wire:click="toggleAllPermissions('{{ $module }}')"
                                                       {{ $moduleStates[$module] ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold" for="toggleAll{{ ucfirst($module) }}">
                                                    Seleccionar/Deseleccionar todos
                                                </label>
                                            </div>

                                            <div class="permission-list" style="max-height: 200px; overflow-y: auto;">
                                                @foreach($permissions as $permission)
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input permission-checkbox"
                                                           type="checkbox"
                                                           id="permission{{ $permission->id }}"
                                                           value="{{ $permission->id }}"
                                                           wire:model="selectedPermissions">
                                                    <label class="form-check-label" for="permission{{ $permission->id }}">
                                                        {{ ucfirst(str_replace('-', ' ', $permission->name)) }}
                                                    </label>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="ri ri-information-line"></i> No hay permisos disponibles. Los permisos se crearán automáticamente cuando se definan funcionalidades en el sistema.
                            </div>
                        @endif

                        @error('selectedPermissions')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-label-secondary">
                            <i class="ri ri-arrow-left-line"></i> Volver
                        </a>
                        <button type="submit" class="btn btn-primary" @cannot('create roles') disabled @endcannot>
                            <i class="ri ri-save-line"></i> Crear Rol
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', function () {
        // Escuchar cambios en checkboxes de permisos
        document.querySelectorAll('.permission-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                // Forzar actualización de Livewire
                Livewire.dispatch('refreshPermissions');
            });
        });
    });
</script>
@endpush

<style>
    .permission-list::-webkit-scrollbar {
        width: 6px;
    }

    .permission-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .permission-list::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }

    .permission-list::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }

    .card-header. {
        background-color: rgba(105, 108, 255, 0.12) !important;
    }
</style>
</div>
