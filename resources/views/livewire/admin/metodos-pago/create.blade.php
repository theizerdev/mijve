<div>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Nuevo Método de Pago</h5>
                    <a href="{{ route('admin.metodos-pago.index') }}" class="btn btn-secondary">
                        <i class="ri ri-arrow-left-line me-1"></i> Regresar
                    </a>
                </div>
                <div class="card-body">
                    <form wire:submit="save">
                        <div class="row g-3">
                            
                            

                            <!-- Tipo de Pago -->
                            <div class="col-md-12">
                                <label class="form-label">Tipo de Pago</label>
                                <select class="form-select @error('tipo_pago') is-invalid @enderror" wire:model.live="tipo_pago">
                                    <option value="Pago Móvil">Pago Móvil</option>
                                    <option value="Transferencia Bancaria">Transferencia Bancaria</option>
                                    <option value="Divisa">Divisa</option>
                                </select>
                                @error('tipo_pago') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Campos Condicionales -->
                            @if($tipo_pago === 'Pago Móvil')
                                <div class="col-md-4">
                                    <label class="form-label">Banco</label>
                                    <input type="text" class="form-control @error('banco') is-invalid @enderror" 
                                           wire:model="banco" placeholder="Nombre del banco" autocomplete="off">
                                    @error('banco') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Cédula</label>
                                    <input type="text" class="form-control @error('cedula') is-invalid @enderror" 
                                           wire:model="cedula" placeholder="Cédula del titular" autocomplete="off">
                                    @error('cedula') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" class="form-control @error('telefono') is-invalid @enderror" 
                                           wire:model="telefono" placeholder="Número de teléfono" autocomplete="off">
                                    @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            @elseif($tipo_pago === 'Transferencia Bancaria')
                                <div class="col-md-6">
                                    <label class="form-label">Nombre del Titular</label>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                           wire:model="nombre" placeholder="Nombre" autocomplete="off">
                                    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Apellido del Titular</label>
                                    <input type="text" class="form-control @error('apellido') is-invalid @enderror" 
                                           wire:model="apellido" placeholder="Apellido" autocomplete="off">
                                    @error('apellido') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Cédula</label>
                                    <input type="text" class="form-control @error('cedula') is-invalid @enderror" 
                                           wire:model="cedula" placeholder="Cédula" autocomplete="off">
                                    @error('cedula') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Banco</label>
                                    <input type="text" class="form-control @error('banco') is-invalid @enderror" 
                                           wire:model="banco" placeholder="Banco" autocomplete="off">
                                    @error('banco') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tipo de Cuenta</label>
                                    <select class="form-select @error('tipo_cuenta') is-invalid @enderror" wire:model="tipo_cuenta">
                                        <option value="">Seleccione tipo</option>
                                        <option value="Ahorro">Ahorro</option>
                                        <option value="Corriente">Corriente</option>
                                    </select>
                                    @error('tipo_cuenta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Número de Cuenta</label>
                                    <input type="text" class="form-control @error('numero_cuenta') is-invalid @enderror" 
                                           wire:model="numero_cuenta" placeholder="Número de cuenta bancaria" autocomplete="off">
                                    @error('numero_cuenta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            @elseif($tipo_pago === 'Divisa')
                                <div class="col-12">
                                    <div class="alert alert-info d-flex align-items-center" role="alert">
                                        <i class="ri ri-information-line me-2"></i>
                                        <div>
                                            Para pagos en divisa no es necesario registrar información adicional.
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary me-2">Guardar</button>
                                <a href="{{ route('admin.metodos-pago.index') }}" class="btn btn-label-secondary">Cancelar</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
