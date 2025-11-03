<div class="row g-0">
    <!-- Sidebar de Filtros -->
    <div class="col-12 col-lg-3 border-end bg-light">
        <div class="p-4">
            <h5 class="mb-4">Filtros</h5>
            <ul class="list-unstyled biblioteca-sidebar">
                <li wire:click="filtrar('todos')" class="{{ $filtroActivo === 'todos' ? 'active' : '' }}">
                    <i class="ri ri-folder-line me-2"></i>
                    <span>Todos los archivos</span>
                    <span class="badge bg-primary ms-auto">{{ $this->totalArchivos }}</span>
                </li>
                <li wire:click="filtrar('mis-archivos')" class="{{ $filtroActivo === 'mis-archivos' ? 'active' : '' }}">
                    <i class="ri ri-file-user-line me-2"></i>
                    <span>Mis archivos</span>
                </li>
                <li wire:click="filtrar('compartidos')" class="{{ $filtroActivo === 'compartidos' ? 'active' : '' }}">
                    <i class="ri ri-share-line me-2"></i>
                    <span>Compartidos conmigo</span>
                </li>
                <li wire:click="filtrar('recientes')" class="{{ $filtroActivo === 'recientes' ? 'active' : '' }}">
                    <i class="ri ri-time-line me-2"></i>
                    <span>Recientes</span>
                </li>
            </ul>

            <hr class="my-3">

            <h6 class="mb-3">Categorías</h6>
            <ul class="list-unstyled biblioteca-sidebar">
                @foreach($this->categorias as $cat)
                <li wire:click="filtrarCategoria({{ $cat->id }})" class="{{ $categoriaSeleccionada == $cat->id ? 'active' : '' }}">
                    <i class="ri ri-folder-2-line me-2"></i>
                    <span>{{ $cat->nombre }}</span>
                    <span class="badge bg-secondary ms-auto">{{ $cat->archivos_count }}</span>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Área Principal -->
    <div class="col-12 col-lg-9">
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Biblioteca Digital</h2>
                <button wire:click="abrirFormulario" class="btn btn-primary">
                    <i class="ri ri-upload-cloud-line me-2"></i>Subir archivo
                </button>
            </div>

            <!-- Barra de búsqueda y vista -->
            <div class="d-flex gap-2 mb-4">
                <input type="text" wire:model.live="search" class="form-control" placeholder="Buscar archivos..." />
                <div class="btn-group">
                    <button wire:click="cambiarVista('grid')" class="btn {{ $vistaActiva === 'grid' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        <i class="ri ri-grid-line"></i>
                    </button>
                    <button wire:click="cambiarVista('list')" class="btn {{ $vistaActiva === 'list' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        <i class="ri ri-list-check"></i>
                    </button>
                </div>
            </div>

            <!-- Formulario de subida -->
            @if($mostrarFormulario)
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Subir nuevo archivo</h5>
                    <form wire:submit.prevent="subirArchivo">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Archivo</label>
                                <input type="file" wire:model="nuevoArchivo" class="form-control">
                                @error('nuevoArchivo') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Título</label>
                                <input type="text" wire:model="titulo" class="form-control">
                                @error('titulo') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Descripción</label>
                                <textarea wire:model="descripcion" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Categoría</label>
                                <select wire:model="categoriaId" class="form-select">
                                    <option value="">Sin categoría</option>
                                    @foreach($this->categorias as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Visibilidad</label>
                                <select wire:model="visibilidad" class="form-select">
                                    <option value="privado">Privado</option>
                                    <option value="publico">Público</option>
                                    <option value="restringido">Restringido</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Etiquetas</label>
                                <input type="text" wire:model="etiquetas" class="form-control" placeholder="Separadas por comas">
                            </div>
                            @if($visibilidad === 'restringido')
                            <div class="col-12">
                                <label class="form-label">Usuarios autorizados</label>
                                <select wire:model="usuariosAutorizados" class="form-select" multiple>
                                    @foreach($this->usuarios as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Subir archivo</button>
                            <button type="button" wire:click="cerrarFormulario" class="btn btn-secondary">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <!-- Vista Grid -->
            @if($vistaActiva === 'grid')
            <div class="row g-3">
                @forelse($this->archivos as $archivo)
                <div class="col-md-4 col-lg-3">
                    <div class="card archivo-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <div class="bg-primary bg-opacity-10 rounded p-2">
                                    <i class="ri ri-file-line text-primary" style="font-size: 24px;"></i>
                                </div>
                                @if($archivo->visibilidad === 'publico')
                                <i class="ri ri-global-line text-success"></i>
                                @elseif($archivo->visibilidad === 'privado')
                                <i class="ri ri-lock-line text-danger"></i>
                                @else
                                <i class="ri ri-user-lock-line text-warning"></i>
                                @endif
                            </div>
                            <h6 class="card-title text-truncate" title="{{ $archivo->titulo }}">{{ $archivo->titulo }}</h6>
                            @if($archivo->descripcion)
                            <p class="card-text small text-muted" style="height: 40px; overflow: hidden;">{{ Str::limit($archivo->descripcion, 60) }}</p>
                            @endif
                            <div class="small text-muted mb-2">
                                <div>{{ $archivo->tamaño_formateado }}</div>
                                <div>{{ $archivo->descargas }} descargas</div>
                                @if($archivo->categoria)
                                <div>{{ $archivo->categoria->nombre }}</div>
                                @endif
                            </div>
                            <div class="d-flex gap-1">
                                <button wire:click="descargarArchivo({{ $archivo->id }})" class="btn btn-sm btn-primary flex-fill">
                                    <i class="ri ri-download-line"></i>
                                </button>
                                @if(auth()->id() === $archivo->usuario_subida_id)
                                <button wire:click="eliminarArchivo({{ $archivo->id }})" onclick="confirm('¿Eliminar?') || event.stopImmediatePropagation()" class="btn btn-sm btn-danger">
                                    <i class="ri ri-delete-bin-line"></i>
                                </button>
                                @endif
                            </div>
                            <div class="mt-2 pt-2 border-top small text-muted">
                                {{ $archivo->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <i class="ri ri-file-line text-muted" style="font-size: 64px;"></i>
                    <p class="text-muted mt-3">No hay archivos</p>
                </div>
                @endforelse
            </div>
            @else
            <!-- Vista Lista -->
            <div class="list-group">
                @forelse($this->archivos as $archivo)
                <div class="list-group-item">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                            <i class="ri ri-file-line text-primary" style="font-size: 24px;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $archivo->titulo }}</h6>
                            <small class="text-muted">
                                {{ $archivo->tamaño_formateado }} • {{ $archivo->descargas }} descargas • {{ $archivo->created_at->diffForHumans() }}
                                @if($archivo->categoria) • {{ $archivo->categoria->nombre }} @endif
                            </small>
                        </div>
                        <div class="d-flex gap-2">
                            @if($archivo->visibilidad === 'publico')
                            <i class="ri ri-global-line text-success"></i>
                            @elseif($archivo->visibilidad === 'privado')
                            <i class="ri ri-lock-line text-danger"></i>
                            @else
                            <i class="ri ri-user-lock-line text-warning"></i>
                            @endif
                            <button wire:click="descargarArchivo({{ $archivo->id }})" class="btn btn-sm btn-primary">
                                <i class="ri ri-download-line"></i> Descargar
                            </button>
                            @if(auth()->id() === $archivo->usuario_subida_id)
                            <button wire:click="eliminarArchivo({{ $archivo->id }})" onclick="confirm('¿Eliminar?') || event.stopImmediatePropagation()" class="btn btn-sm btn-danger">
                                <i class="ri ri-delete-bin-line"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="ri ri-file-line text-muted" style="font-size: 64px;"></i>
                    <p class="text-muted mt-3">No hay archivos</p>
                </div>
                @endforelse
            </div>
            @endif

            <div class="mt-4">
                {{ $this->archivos->links() }}
            </div>
        </div>
    </div>
</div>
