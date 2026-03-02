<div class="position-relative">
  <div class="authentication-wrapper authentication-basic container-p-y p-4 p-sm-0">
    <div class="authentication-inner py-6">
      <!-- Forgot Password -->
      <div class="card p-md-7 p-1">
        <!-- Logo -->
        <div class="app-brand justify-content-center mt-5">
          <a href="{{ url('/') }}" class="app-brand-link gap-2">
            <img src="{{ asset('logo/logo.png') }}" alt="logo" style="height: 120px;" />
           
          </a>
        </div>
        <!-- /Logo -->

        <div class="card-body mt-1">
          <h4 class="mb-1">{{ __('auth_ui.forgot_password_title') }} 🔒</h4>
          <p class="mb-5">{{ __('auth_ui.forgot_password_subtitle') }}</p>

          @if (session()->has('status'))
            <div class="alert alert-success" role="alert">
              {{ session('status') }}
            </div>
          @endif

          @if ($successMessage)
            <div class="alert alert-success" role="alert">
              {{ $successMessage }}
            </div>
          @endif

          <form wire:submit="sendResetLink">
            <div class="form-floating form-floating-outline mb-5 form-control-validation">
              <input
                type="text"
                class="form-control @if($hasError('email')) is-invalid @endif"
                id="email"
                name="email"
                wire:model="email"
                placeholder="{{ __('auth_ui.email') }}"
                autofocus />
              <label for="email">{{ __('auth_ui.email') }}</label>
              @if($hasError('email'))
                <div class="invalid-feedback d-block">{{ $getError('email') }}</div>
              @endif
            </div>
            <div class="mb-5">
              <button class="btn btn-primary d-grid w-100" type="submit">{{ __('auth_ui.send_reset_link') }}</button>
            </div>
          </form>

          <div class="text-center">
            <a href="{{ route('login') }}" class="d-flex align-items-center justify-content-center">
              <i class="icon-base ri ri-arrow-left-s-line"></i>
              {{ __('auth_ui.back_to_login') }}
            </a>
          </div>
        </div>
      </div>
      <!-- /Forgot Password -->

  </div>
</div>