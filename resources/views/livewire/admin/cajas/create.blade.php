<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Apertura de Caja</h5>
                            <p class="mb-0">Abre una nueva caja para el día {{ now()->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.cajas.index') }}" class="btn btn-secondary">
                                <i class="ri ri-arrow-left-line"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>

                <form wire:submit="save">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Monto Inicial <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control @error('monto_inicial') is-invalid @enderror" 
                                               wire:model.blur="monto_inicial" step="0.01" min="0" placeholder="0.00">
                                    </div>
                                    @error('monto_inicial')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Monto en efectivo con el que se inicia la caja</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Fecha de Apertura</label>
                                    <input type="text" class="form-control" value="{{ now()->format('d/m/Y H:i') }}" readonly>
                                    <small class="form-text text-muted">Fecha y hora actual del sistema</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Observaciones de Apertura</label>
                                    <textarea class="form-control @error('observaciones_apertura') is-invalid @enderror" 
                                              wire:model.blur="observaciones_apertura" rows="3" 
                                              placeholder="Observaciones adicionales sobre la apertura de caja..."></textarea>
                                    @error('observaciones_apertura')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Información adicional -->
                        <div class="alert alert-info">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="ri ri-information-line"></i>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <h6 class="alert-heading">Información importante:</h6>
                                    <ul class="mb-0">
                                        <li>Solo se puede tener una caja abierta por día</li>
                                        <li>Todos los pagos del día se asociarán automáticamente a esta caja</li>
                                        <li>El monto inicial debe corresponder al efectivo físico en caja</li>
                                        <li>Una vez abierta, la caja debe cerrarse al final del día</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.cajas.index') }}" class="btn btn-secondary">
                                <i class="ri ri-close-line"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri ri-save-line"></i> Abrir Caja
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>