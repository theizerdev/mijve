<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Editar Permiso</h5>
                    <a href="{{ route('admin.permissions.index') }}" class="btn btn-label-secondary">
                        <i class="ri ri-arrow-left-line"></i> Volver
                    </a>
                </div>
                <div class="card-body">
                    @if(session()->has('message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session()->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <form wire:submit.prevent="save">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nombre del Permiso</label>
                                    <input wire:model="name" id="name" type="text" class="form-control @error('name') is-invalid @enderror" placeholder="Ejemplo: access reports, create users, delete posts">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Sigue la convención: access [módulo], create [módulo], edit [módulo], delete [módulo], view [módulo]</div>
                                </div>

                                <div class="mb-3">
                                    <label for="module" class="form-label">Módulo</label>
                                    <input wire:model="module" id="module" type="text" class="form-control @error('module') is-invalid @enderror" placeholder="Ejemplo: users, posts, reports">
                                    @error('module')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Agrupa permisos por módulos para mejor organización</div>
                                </div>

                                <div class="mb-3">
                                    <label for="guard_name" class="form-label">Guard Name</label>
                                    <input wire:model="guard_name" id="guard_name" type="text" class="form-control @error('guard_name') is-invalid @enderror">
                                    @error('guard_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Normalmente 'web' para aplicaciones web estándar</div>
                                </div>

                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    <h6 class="alert-heading mb-2">
                                        <i class="ri ri-information-line"></i>
                                        Información Importante
                                    </h6>
                                    <p class="mb-2">Los permisos deben seguir una convención de nombres clara:</p>
                                    <ul class="mb-0">
                                        <li><strong>access [módulo]</strong> - Para acceder a un módulo</li>
                                        <li><strong>create [módulo]</strong> - Para crear registros</li>
                                        <li><strong>edit [módulo]</strong> - Para editar registros</li>
                                        <li><strong>delete [módulo]</strong> - Para eliminar registros</li>
                                        <li><strong>view [módulo]</strong> - Para ver registros</li>
                                    </ul>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.permissions.index') }}" class="btn btn-label-secondary">
                                        <i class="ri ri-arrow-left-line"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri ri-save-line"></i> Actualizar Permiso
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
