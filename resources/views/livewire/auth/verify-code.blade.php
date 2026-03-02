<div class="position-relative">
  <div class="authentication-wrapper authentication-basic container-p-y p-4 p-sm-0">
    <div class="authentication-inner py-6">
      <!-- Verify WhatsApp Code -->
      <div class="card p-md-7 p-1">
        <!-- Logo -->
        <div class="app-brand justify-content-center mt-5">
          <a href="{{ url('/') }}" class="app-brand-link gap-2">
           <img src="{{ asset('logo/logo.png') }}" alt="logo" style="height: 120px;" />
          </a>
        </div>
        <!-- /Logo -->

        <div class="card-body mt-1">
          <h4 class="mb-1">Verificación por WhatsApp 📱</h4>
          <p class="text-start mb-2">
            Hemos enviado un código de 6 dígitos a tu WhatsApp registrado:
          </p>
          <p class="text-start mb-4">
            <span class="fw-medium text-success">
              <i class="ri ri-whatsapp-line me-1"></i>{{ $maskedPhone }}
            </span>
          </p>

          @if (session('resent'))
            <div class="alert alert-success d-flex align-items-center" role="alert">
              <i class="ri ri-checkbox-circle-line me-2"></i>
              {{ session('resent') }}
            </div>
          @endif

          <form wire:submit="verifyCode">
            <div class="mb-5 form-control-validation">
              <label class="form-label">Código de verificación</label>
              <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                @for ($i = 0; $i < 6; $i++)
                  <input
                    type="text"
                    class="form-control text-center @if($hasError('code')) is-invalid @endif"
                    maxlength="1"
                    wire:model.live="codeInputs.{{ $i }}"
                    wire:key="code-input-{{ $i }}"
                    inputmode="numeric"
                    pattern="[0-9]"
                    autocomplete="one-time-code"
                    style="flex: 1; min-width: 40px; max-width: 50px; height: 3rem; font-size: 1.5rem; font-weight: 600;"
                    x-data
                    x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')"
                    x-on:keydown.backspace="
                      if ($el.value === '' && {{ $i }} > 0) {
                        $el.previousElementSibling && $el.previousElementSibling.focus();
                      }
                    "
                    x-init="
                      $watch('$wire.codeInputs.{{ $i }}', value => {
                        if (value && value.length === 1 && {{ $i }} < 5) {
                          $nextTick(() => {
                            $el.nextElementSibling && $el.nextElementSibling.focus();
                          });
                        }
                      });
                    "
                  />
                @endfor
              </div>
              @if($hasError('code'))
                <div class="alert alert-danger d-flex align-items-start mt-3" role="alert">
                  <i class="ri ri-error-warning-line me-2 mt-1"></i>
                  <div class="small">{{ $getError('code') }}</div>
                </div>
              @endif
            </div>

            <div class="mb-4">
              <button class="btn btn-primary d-grid w-100" type="submit">
                Verificar Código
              </button>
            </div>
          </form>

          <form wire:submit="sendCode">
            <div class="mb-4">
              @if($canResend)
                <button class="btn btn-outline-success d-grid w-100" type="submit">
                  Enviar código por WhatsApp
                </button>
              @else
                <button class="btn btn-outline-secondary d-grid w-100" type="button" disabled>
                  <i class="ri ri-time-line me-1"></i> Reenviar en {{ floor($resendCountdown/60) }}:{{ str_pad($resendCountdown%60, 2, '0', STR_PAD_LEFT) }}
                </button>
              @endif
            </div>
          </form>

          <div class="text-center mt-3">
            <small class="text-muted d-block mb-3">
              <i class="ri ri-information-line me-1"></i>
              El código expira en 15 minutos. Si no lo recibes, presiona "Enviar código por WhatsApp".
            </small>
          </div>

          <div class="text-start">
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="d-flex align-items-center justify-content-center">
              <i class="icon-base ri ri-arrow-left-s-line"></i>
              Cerrar sesión
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
              @csrf
            </form>
          </div>
        </div>
      </div>
      <!-- /Verify WhatsApp Code -->

    </div>
  </div>
</div>

@push('scripts')
<script>
  document.addEventListener('livewire:load', function () {
    Livewire.on('focus-next', index => {
      const nextInput = document.querySelector(`[wire\\:model="codeInputs.${index}"]`);
      if (nextInput) {
        nextInput.focus();
      }
    });
  });
</script>
@endpush
