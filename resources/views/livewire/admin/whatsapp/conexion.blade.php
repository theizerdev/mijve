<div>
    <!-- Alertas -->
    @if($error)
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex">
                <i class="ri ri-error-warning-line me-2 ri-20px"></i>
                <div>{{ $error }}</div>
            </div>
            <button type="button" class="btn-close" wire:click="clearMessages"></button>
        </div>
    @endif

    @if($success)
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex">
                <i class="ri ri-checkbox-circle-line me-2 ri-20px"></i>
                <div>{{ $success }}</div>
            </div>
            <button type="button" class="btn-close" wire:click="clearMessages"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Estado de Conexión -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri ri-signal-wifi-line me-2"></i>Estado de Conexión
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Estado Visual -->
                    <div class="text-center mb-4">
                        <div class="avatar avatar-xl mb-3">
                            <span class="avatar-initial rounded-circle bg-label-{{ $statusColor }}">
                                <i class="{{ $statusIcon }} ri-36px {{ $status === 'connecting' ? 'spin-animation' : '' }}"></i>
                            </span>
                        </div>
                        <h4 class="mb-1 text-{{ $statusColor }}">{{ $statusText }}</h4>

                        @if($user && $status === 'connected')
                            <p class="text-muted mb-0">
                                <i class="ri ri-user-line me-1"></i>{{ $user['name'] ?? 'Usuario' }}
                                <br>
                                <i class="ri ri-phone-line me-1"></i>{{ $user['id'] ?? 'N/A' }}
                            </p>
                        @elseif($status === 'service_unavailable')
                            <p class="text-muted mb-0">El servidor de WhatsApp no está disponible</p>
                        @elseif($status === 'disconnected')
                            <p class="text-muted mb-0">No hay una sesión activa</p>
                        @endif
                    </div>

                    <!-- Información Adicional -->
                    @if($status === 'connected' && $lastSeen)
                        <div class="bg-light rounded p-3 mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">
                                    <i class="ri ri-time-line me-1"></i>Última actividad
                                </span>
                                <span>{{ \Carbon\Carbon::parse($lastSeen)->diffForHumans() }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Botones de Acción -->
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        @if($status === 'disconnected' || $status === 'error' || $status === 'service_unavailable')
                            <button wire:click="connect"
                                    class="btn btn-success"
                                    wire:loading.attr="disabled"
                                    wire:target="connect">
                                <span wire:loading.remove wire:target="connect">
                                    <i class="ri ri-link me-1"></i>Conectar WhatsApp
                                </span>
                                <span wire:loading wire:target="connect">
                                    <span class="spinner-border spinner-border-sm me-1"></span>Conectando...
                                </span>
                            </button>
                        @endif

                        @if($status === 'connected')
                            <button wire:click="disconnect"
                                    class="btn btn-danger"
                                    wire:loading.attr="disabled"
                                    wire:target="disconnect"
                                    onclick="return confirm('¿Está seguro de desconectar WhatsApp?')">
                                <span wire:loading.remove wire:target="disconnect">
                                    <i class="ri ri-logout-circle-line me-1"></i>Desconectar
                                </span>
                                <span wire:loading wire:target="disconnect">
                                    <span class="spinner-border spinner-border-sm me-1"></span>Desconectando...
                                </span>
                            </button>
                        @endif

                        <button wire:click="checkStatus"
                                class="btn btn-label-primary"
                                wire:loading.attr="disabled"
                                wire:target="checkStatus">
                            <span wire:loading.remove wire:target="checkStatus">
                                <i class="ri ri-refresh-line me-1"></i>Actualizar
                            </span>
                            <span wire:loading wire:target="checkStatus">
                                <span class="spinner-border spinner-border-sm me-1"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Code / Instrucciones -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        @if($qrCode && ($status === 'qr_ready' || $status === 'connecting'))
                            <i class="ri ri-qr-code-line me-2"></i>Escanear Código QR
                        @else
                            <i class="ri ri-information-line me-2"></i>Instrucciones
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    @if($qrCode && ($status === 'qr_ready' || $status === 'connecting'))
                        <!-- QR Code -->
                        <div class="text-center">
                            <div class="qr-container d-inline-block p-4 bg-white rounded shadow-sm mb-3">
                                <img src="{{ $qrCode }}" alt="Código QR WhatsApp" class="img-fluid" style="max-width: 220px;">
                            </div>

                            <div class="alert alert-warning">
                                <div class="d-flex align-items-start">
                                    <i class="ri ri-timer-line me-2 ri-20px mt-1"></i>
                                    <div class="text-start">
                                        <strong>El código expira pronto</strong>
                                        <p class="mb-0 small">Escanea rápidamente con tu teléfono</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($status === 'connecting')
                        <!-- Cargando QR -->
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;"></div>
                            <h6>Generando código QR...</h6>
                            <p class="text-muted">Por favor espere mientras se genera el código</p>
                        </div>
                    @else
                        <!-- Instrucciones -->
                        <div class="instruction-steps">
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-primary rounded-circle p-2">1</span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Abre WhatsApp</h6>
                                    <p class="text-muted mb-0 small">En tu teléfono móvil</p>
                                </div>
                            </div>

                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-primary rounded-circle p-2">2</span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Ve a Configuración</h6>
                                    <p class="text-muted mb-0 small">
                                        <strong>Android:</strong> Menú (⋮) → Dispositivos vinculados<br>
                                        <strong>iPhone:</strong> Ajustes → Dispositivos vinculados
                                    </p>
                                </div>
                            </div>

                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-primary rounded-circle p-2">3</span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Toca "Vincular un dispositivo"</h6>
                                    <p class="text-muted mb-0 small">Y escanea el código QR que aparece aquí</p>
                                </div>
                            </div>

                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-success rounded-circle p-2">
                                        <i class="ri ri-check-line"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">¡Listo!</h6>
                                    <p class="text-muted mb-0 small">La conexión se establecerá automáticamente</p>
                                </div>
                            </div>
                        </div>

                        @if($status === 'connected')
                            <div class="alert alert-success mb-0 mt-4">
                                <div class="d-flex align-items-center">
                                    <i class="ri ri-checkbox-circle-fill me-2 ri-20px"></i>
                                    <span>WhatsApp está conectado y funcionando correctamente</span>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Información Técnica -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card bg-light border-0">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="mb-1">
                                <i class="ri ri-information-line me-1"></i>Información importante
                            </h6>
                            <p class="text-muted mb-0 small">
                                La conexión se mantiene activa mientras el servidor esté funcionando.
                                Si cierra esta página, la conexión continuará activa.
                                WhatsApp puede desconectarse si el teléfono está sin internet por mucho tiempo.
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end mt-2 mt-md-0">
                            <span class="badge bg-{{ $statusColor }} fs-6">
                                <i class="{{ $statusIcon }} me-1"></i>{{ $statusText }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .spin-animation {
        animation: spin 1s linear infinite;
    }

    .qr-container {
        background: linear-gradient(135deg, #f5f5f5 0%, #ffffff 100%);
        border: 2px solid #e0e0e0;
    }
</style>
@endpush

@push('scripts')
<script>
    @if($pollingActive && ($status === 'connecting' || $status === 'qr_ready'))
        const statusInterval = setInterval(() => {
            @this.checkStatus();
        }, 5000);

        document.addEventListener('livewire:navigating', () => {
            clearInterval(statusInterval);
        });
    @endif
</script>
@endpush
