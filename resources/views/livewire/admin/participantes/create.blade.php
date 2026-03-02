<div>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Nuevo Participante</h5>
                    <a href="{{ route('admin.participantes.index') }}" class="btn btn-secondary">
                        <i class="ri ri-arrow-left-line me-1"></i> Regresar
                    </a>
                </div>
                <div class="card-body">
                    <form wire:submit="save">
                        <div class="row g-3">
                           <!-- Actividad y Ubicación -->
                            <div class="col-md-12">
                                <h6 class="mt-2 mb-3">Ubicación y Actividad</h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Extensión</label>
                                <select class="form-select @error('extension_id') is-invalid @enderror" wire:model.live="extension_id">
                                    <option value="">Seleccione una extensión</option>
                                    @foreach($extensiones as $extension)
                                        <option value="{{ $extension->id }}">{{ $extension->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('extension_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Actividad</label>
                                <select class="form-select @error('actividad_id') is-invalid @enderror" wire:model.live="actividad_id">
                                    <option value="">Seleccione una actividad</option>
                                    @foreach($actividades as $actividad)
                                        <option value="{{ $actividad->id }}">
                                            {{ $actividad->nombre }} ({{ $actividad->edad_desde }} - {{ $actividad->edad_hasta }} años)
                                        </option>
                                    @endforeach
                                </select>
                                @if($actividad_id)
                                    <div class="form-text">
                                        Rango de edad permitido: 
                                        {{ $actividades->where('id', $actividad_id)->first()->edad_desde ?? '?' }} - 
                                        {{ $actividades->where('id', $actividad_id)->first()->edad_hasta ?? '?' }} años
                                    </div>
                                @endif
                                @error('actividad_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Zona</label>
                                <input type="text" class="form-control bg-light" wire:model="zona" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Distrito</label>
                                <input type="text" class="form-control bg-light" wire:model="distrito" readonly>
                            </div>


                            <!-- Información Personal -->
                            <div class="col-md-12">
                                <h6 class="mt-2 mb-3">Información Personal</h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nombres</label>
                                <input type="text" class="form-control @error('nombres') is-invalid @enderror" 
                                       wire:model="nombres" placeholder="Nombres del participante">
                                @error('nombres') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Apellidos</label>
                                <input type="text" class="form-control @error('apellidos') is-invalid @enderror" 
                                       wire:model="apellidos" placeholder="Apellidos del participante">
                                @error('apellidos') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Cédula</label>
                                <input type="text" class="form-control @error('cedula') is-invalid @enderror" 
                                       wire:model="cedula" placeholder="Número de cédula">
                                @error('cedula') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Fecha de Nacimiento</label>
                                <input type="date" class="form-control @error('fecha_nacimiento') is-invalid @enderror" 
                                       wire:model.live="fecha_nacimiento" max="{{ date('Y-m-d') }}">
                                @error('fecha_nacimiento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Género</label>
                                <select class="form-select @error('genero') is-invalid @enderror" wire:model="genero">
                                    <option value="">Seleccione el género</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                </select>
                                @error('genero') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Estado Civil</label>
                                <select class="form-select @error('estado_civil') is-invalid @enderror" wire:model="estado_civil">
                                    <option value="">Seleccione estado civil</option>
                                    <option value="Soltero(a)">Soltero(a)</option>
                                    <option value="Casado(a)">Casado(a)</option>
                                    <option value="Divorciado(a)">Divorciado(a)</option>
                                    <option value="Viudo(a)">Viudo(a)</option>
                                    <option value="Unión Libre">Unión Libre</option>
                                </select>
                                @error('estado_civil') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Edad</label>
                                <input type="number" class="form-control @error('edad') is-invalid @enderror" 
                                       wire:model="edad" readonly>
                                @error('edad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Contacto -->
                            <div class="col-md-12">
                                <h6 class="mt-2 mb-3">Información de Contacto</h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Teléfono Principal</label>
                                <input type="text" class="form-control @error('telefono_principal') is-invalid @enderror" 
                                       wire:model="telefono_principal" placeholder="Teléfono principal">
                                @error('telefono_principal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Teléfono Alternativo</label>
                                <input type="text" class="form-control @error('telefono_alternativo') is-invalid @enderror" 
                                       wire:model="telefono_alternativo" placeholder="Teléfono alternativo (opcional)">
                                @error('telefono_alternativo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Dirección</label>
                                <textarea class="form-control @error('direccion') is-invalid @enderror" 
                                          wire:model="direccion" rows="2" placeholder="Dirección completa"></textarea>
                                @error('direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                           
                            

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary me-2">Guardar</button>
                                <a href="{{ route('admin.participantes.index') }}" class="btn btn-label-secondary">Cancelar</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
