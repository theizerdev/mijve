<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Autenticación en dos pasos</h5>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h6 class="mb-1">Estado actual</h6>
                <small class="text-muted">Añade una capa adicional de seguridad a tu cuenta</small>
            </div>
            <button 
                wire:click="toggle2FA" 
                class="btn btn-{{ $enabled ? 'danger' : 'primary' }}"
            >
                {{ $enabled ? 'Desactivar' : 'Activar' }}
            </button>
        </div>

        @if($showQrCode)
            <div class="border rounded p-4 mb-4">
                <div class="text-center mb-3">
                    <h6>Configura tu aplicación de autenticación</h6>
                    <small class="text-muted">Escanea el código QR con tu aplicación</small>
                </div>

                <div class="d-flex flex-column align-items-center">
                    <div class="mb-3">
                        {!! $qrCode !!}
                    </div>

                    <div class="mb-3 w-100">
                        <label class="form-label">Código de verificación</label>
                        <input 
                            type="text" 
                            wire:model="verificationCode" 
                            class="form-control" 
                            placeholder="Ingresa el código de tu app"
                        >
                        @error('verificationCode') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <button 
                        wire:click="verifyCode" 
                        class="btn btn-primary w-100"
                    >
                        Verificar y activar
                    </button>
                </div>
            </div>
        @endif

        @if(!empty($recoveryCodes))
            <div class="alert alert-warning">
                <h6 class="alert-heading">Códigos de recuperación</h6>
                <p class="mb-2">Guarda estos códigos en un lugar seguro. Son tu respaldo si pierdes acceso a tu dispositivo.</p>
                
                <div class="row mt-3">
                    @foreach($recoveryCodes as $code)
                        <div class="col-md-6 mb-2">
                            <code>{{ $code }}</code>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
