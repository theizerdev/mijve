# Internacionalización del Sistema de Autenticación

## Reemplazos Necesarios en las Vistas

### Register (register.blade.php)
```blade
<!-- Reemplazar -->
"Adventure starts here 🚀" → "{{ __('auth_ui.register_title') }} 🚀"
"Make your app management easy and fun!" → "{{ __('auth_ui.register_subtitle') }}"
"Username" → "{{ __('auth_ui.name') }}"
"Email" → "{{ __('auth_ui.email') }}"
"Password" → "{{ __('auth_ui.password') }}"
"Confirm Password" → "{{ __('auth_ui.confirm_password') }}"
"Sign up" → "{{ __('auth_ui.register_button') }}"
"Already have an account?" → "{{ __('auth_ui.already_have_account') }}"
"Sign in instead" → "{{ __('auth_ui.login_here') }}"
```

### Forgot Password (forgot-password.blade.php)
```blade
<!-- Reemplazar -->
"Forgot Password? 🔒" → "{{ __('auth_ui.forgot_password_title') }} 🔒"
"Enter your email and we'll send you instructions to reset your password" → "{{ __('auth_ui.forgot_password_subtitle') }}"
"Email" → "{{ __('auth_ui.email') }}"
"Send Reset Link" → "{{ __('auth_ui.send_reset_link') }}"
"Back to login" → "{{ __('auth_ui.back_to_login') }}"
```

### Reset Password (reset-password.blade.php)
```blade
<!-- Reemplazar -->
"Reset Password 🔒" → "{{ __('auth_ui.reset_password_title') }} 🔒"
"Email" → "{{ __('auth_ui.email') }}"
"New Password" → "{{ __('auth_ui.new_password') }}"
"Confirm Password" → "{{ __('auth_ui.confirm_password') }}"
"Set New Password" → "{{ __('auth_ui.reset_button') }}"
"Back to login" → "{{ __('auth_ui.back_to_login') }}"
```

### Verify Email (verify-code.blade.php)
```blade
<!-- Reemplazar -->
"Verifica tu correo electrónico ✉️" → "{{ __('auth_ui.verify_email_title') }} ✉️"
"Hemos enviado un código de verificación a tu correo:" → "{{ __('auth_ui.verify_email_subtitle') }}"
"Código de verificación" → "{{ __('auth_ui.verification_code') }}"
"Verificar correo" → "{{ __('auth_ui.verify_button') }}"
"Reenviar código" → "{{ __('auth_ui.resend_code') }}"
"Cerrar sesión" → "{{ __('auth_ui.logout') }}"
```

### Two Factor (two-factor-login.blade.php)
```blade
<!-- Reemplazar -->
"Verificación en dos pasos" → "{{ __('auth_ui.two_factor_title') }}"
"Ingrese el código de verificación de su aplicación de autenticación" → "{{ __('auth_ui.two_factor_subtitle') }}"
"Código de verificación" → "{{ __('auth_ui.two_factor_code') }}"
"Verificar" → "{{ __('auth_ui.verify_2fa') }}"
"Volver al inicio de sesión" → "{{ __('auth_ui.back_to_login') }}"
```

## Comando para Aplicar Cambios

Ejecuta estos comandos en tu terminal para aplicar los cambios automáticamente:

```bash
# Register
sed -i 's/Adventure starts here 🚀/{{ __("auth_ui.register_title") }} 🚀/g' resources/views/livewire/auth/register.blade.php

# Forgot Password  
sed -i 's/Forgot Password? 🔒/{{ __("auth_ui.forgot_password_title") }} 🔒/g' resources/views/livewire/auth/forgot-password.blade.php

# Reset Password
sed -i 's/Reset Password 🔒/{{ __("auth_ui.reset_password_title") }} 🔒/g' resources/views/livewire/auth/reset-password.blade.php

# Verify Email
sed -i 's/Verifica tu correo electrónico ✉️/{{ __("auth_ui.verify_email_title") }} ✉️/g' resources/views/livewire/auth/verify-code.blade.php

# Two Factor
sed -i 's/Verificación en dos pasos/{{ __("auth_ui.two_factor_title") }}/g' resources/views/livewire/auth/two-factor-login.blade.php
```

## Estado Actual

✅ Login - Completamente internacionalizado
✅ Archivos de traducción creados (es/en)
⏳ Register - Pendiente
⏳ Forgot Password - Pendiente  
⏳ Reset Password - Pendiente
⏳ Verify Email - Pendiente
⏳ Two Factor - Pendiente

## Próximos Pasos

1. Aplicar los reemplazos manualmente en cada vista
2. Probar el cambio de idioma en cada pantalla
3. Agregar más traducciones según sea necesario
