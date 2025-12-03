<div wire:poll.30s="$dispatch('refresh-rates')">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h4 class="mb-1">Tasas de Cambio BCV</h4>
                            <p class="mb-0 text-muted">Banco Central de Venezuela - Actualización automática</p>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <div class="text-end me-3">
                                <small class="text-muted d-block">Última actualización: {{ $lastUpdate }}</small>
                                <small class="text-muted">Horarios: 10:00 AM y 2:00 PM</small>
                            </div>
                            @can('edit exchange-rates')
                                <button wire:click="editRate" class="btn btn-warning">
                                    <i class="ri ri-edit-line me-1"></i>Editar Tasa
                                </button>
                            @endcan
                            <button wire:click="fetchNow" class="btn btn-primary">
                                <i class="ri ri-refresh-line me-1"></i>Actualizar Ahora
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri ri-check-line me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri ri-error-warning-line me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Métricas principales -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-success rounded-3">
                                <i class="ri ri-money-dollar-circle-line ri-26px"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-info mt-4">
                        <h4 class="mb-1">{{ number_format($stats['usd_rate'] ?? 0, 4) }}</h4>
                        <p class="mb-0">Bolívares por USD</p>
                        <small class="text-muted">{{ $stats['last_fetch'] ?? 'N/A' }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-primary rounded-3">
                                <i class="ri ri-money-euro-circle-line ri-26px"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-info mt-4">
                        <h4 class="mb-1">{{ number_format($stats['eur_rate'] ?? 0, 4) }}</h4>
                        <p class="mb-0">Bolívares por EUR</p>
                        <small class="text-muted">{{ $stats['last_fetch'] ?? 'N/A' }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-info rounded-3">
                                <i class="ri ri-calendar-line ri-26px"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-info mt-4">
                        <h4 class="mb-1">{{ $stats['date'] ?? 'N/A' }}</h4>
                        <p class="mb-0">Fecha de la Tasa</p>
                        <small class="text-muted">Verificada</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-warning rounded-3">
                                <i class="ri ri-database-line ri-26px"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-info mt-4">
                        <h4 class="mb-1">Fuente Confiable</h4>
                        <p class="mb-0">Fuente de Datos</p>
                        <small class="text-muted">10:00 AM - 2:00 PM</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tasa activa del día -->
    @if($todayRate)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary border-2">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="ri ri-exchange-dollar-line me-2"></i>Tasa del Día</h5>
                        @can('edit exchange-rates')
                        <button wire:click="editRate({{ $todayRate->id }})" class="btn btn-light btn-sm">
                            <i class="ri ri-edit-line me-1"></i>Editar Tasa
                        </button>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-6 border-end">
                            <h2 class="display-6 text-primary">{{ number_format($todayRate->usd_rate, 4) }}</h2>
                            <p class="mb-0 text-muted">Bolívares por Dólar (USD)</p>
                            <small class="text-muted">Fuente: {{ $todayRate->source }}</small>
                        </div>
                        <div class="col-md-6">
                            <h2 class="display-6 text-primary">{{ number_format($todayRate->eur_rate, 4) }}</h2>
                            <p class="mb-0 text-muted">Bolívares por Euro (EUR)</p>
                            <small class="text-muted">Actualizado: {{ $todayRate->fetch_time->format('H:i') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-warning border-2">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="ri ri-alert-line me-2"></i>No hay tasa registrada para hoy</h5>
                </div>
                <div class="card-body text-center">
                    <p class="mb-3">Aún no se ha registrado una tasa de cambio para el día de hoy.</p>
                    @can('edit exchange-rates')
                    <button wire:click="editRate()" class="btn btn-primary">
                        <i class="ri ri-add-line me-1"></i>Registrar Tasa Manual
                    </button>
                    @endcan
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal de Edición -->
    @if($showEditModal)
        <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ri ri-edit-line me-2"></i>
                            {{ $editingRate ? 'Editar Tasa de Cambio' : 'Crear Tasa Manual' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeEditModal"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit="saveRate">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Tasa USD <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Bs.</span>
                                        <input type="number"
                                               class="form-control @error('usd_rate') is-invalid @enderror"
                                               wire:model="usd_rate"
                                               step="0.0001"
                                               min="0.0001"
                                               max="999999.9999"
                                               placeholder="0.0000">
                                    </div>
                                    @error('usd_rate')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Tasa EUR <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Bs.</span>
                                        <input type="number"
                                               class="form-control @error('eur_rate') is-invalid @enderror"
                                               wire:model="eur_rate"
                                               step="0.0001"
                                               min="0.0001"
                                               max="999999.9999"
                                               placeholder="0.0000">
                                    </div>
                                    @error('eur_rate')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Motivo de la {{ $editingRate ? 'edición' : 'creación' }} <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('edit_reason') is-invalid @enderror"
                                              wire:model="edit_reason"
                                              rows="3"
                                              placeholder="Explique el motivo de esta {{ $editingRate ? 'modificación' : 'creación manual' }}..."></textarea>
                                    @error('edit_reason')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </form>

                        @if($editingRate && isset($editingRate->raw_data['edited_by']))
                            <div class="alert alert-info mt-3">
                                <small>
                                    <strong>Última edición:</strong> {{ $editingRate->raw_data['edited_by'] ?? 'N/A' }}<br>
                                    <strong>Motivo:</strong> {{ $editingRate->raw_data['edit_reason'] ?? 'N/A' }}
                                </small>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" wire:click="closeEditModal">
                            <i class="ri ri-close-line me-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-primary" wire:click="saveRate">
                            <i class="ri ri-save-line me-1"></i>Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
