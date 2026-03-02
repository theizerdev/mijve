<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use App\Services\WhatsAppService;

class VerifyCode extends Component
{
    public $code = '';
    public $codeInputs = ['', '', '', '', '', ''];
    public $resent = false;
    public $errors = [];
    public $canResend = true;
    public $resendCountdown = 0;
    public $maskedPhone = '';

    protected $rules = [
        'code' => 'required|string|size:6',
    ];

    public function mount()
    {
        if (Auth::user()->hasVerifiedEmail()) {
            return redirect()->intended('/');
        }

        $this->maskedPhone = $this->getMaskedPhone();

        $throttleKey = $this->throttleKey();
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $this->canResend = false;
            $this->resendCountdown = RateLimiter::availableIn($throttleKey);
        }
    }

    protected function throttleKey()
    {
        return 'resend-verification-code|' . Auth::id();
    }

    /**
     * Formatea el número de teléfono del usuario según el código telefónico del país de su empresa.
     */
    private function formatPhoneNumber(): ?string
    {
        $user = Auth::user();
        $phone = $user->telefono;

        if (empty($phone)) {
            return null;
        }

        // Limpiar: solo dígitos
        $phone = preg_replace('/\D/', '', $phone);

        // Obtener código de país desde empresa->pais->codigo_telefonico
        $countryCode = '58'; // fallback Venezuela
        if ($user->empresa && $user->empresa->pais && $user->empresa->pais->codigo_telefonico) {
            $countryCode = preg_replace('/\D/', '', $user->empresa->pais->codigo_telefonico);
        }

        // Si ya empieza con el código de país, verificar que no tenga 0 después
        if (str_starts_with($phone, $countryCode)) {
            $numberPart = substr($phone, strlen($countryCode));
            if (str_starts_with($numberPart, '0')) {
                $phone = $countryCode . substr($numberPart, 1);
            }
            return $phone;
        }

        // Quitar 0 inicial si existe
        if (str_starts_with($phone, '0')) {
            $phone = substr($phone, 1);
        }

        return $countryCode . $phone;
    }

    /**
     * Devuelve el número de teléfono enmascarado para mostrar en la UI.
     */
    private function getMaskedPhone(): string
    {
        $formatted = $this->formatPhoneNumber();
        if (!$formatted || strlen($formatted) < 6) {
            return '***';
        }

        // Mostrar los primeros 4 y últimos 2 dígitos
        $visible = substr($formatted, 0, 4);
        $tail = substr($formatted, -2);
        $masked = str_repeat('*', strlen($formatted) - 6);

        return '+' . $visible . $masked . $tail;
    }

    public function updatedCodeInputs($value, $index)
    {
        unset($this->errors['code']);

        // Solo permitir números
        $value = preg_replace('/[^0-9]/', '', $value);
        $this->codeInputs[$index] = $value;
        $this->code = implode('', $this->codeInputs);

        if (strlen($this->code) == 6 && !in_array('', $this->codeInputs)) {
            $this->verifyCode();
        }

        if (strlen($value) == 1 && $index < 5) {
            $this->dispatch('focus-next', $index + 1);
        }
    }

    /**
     * Enviar o reenviar el código de verificación por WhatsApp.
     */
    public function sendCode()
    {
        $this->errors = [];

        $throttleKey = $this->throttleKey();
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $this->canResend = false;
            $this->resendCountdown = RateLimiter::availableIn($throttleKey);
            return;
        }

        $user = Auth::user();

        // Validar que el usuario tenga teléfono
        $formattedPhone = $this->formatPhoneNumber();
        if (!$formattedPhone) {
            $this->errors['code'] = 'No tienes un número de teléfono registrado. Contacta al administrador.';
            return;
        }

        RateLimiter::hit($throttleKey, 300); // 5 minutos

        // Generar código
        $plainCode = $user->generateVerificationCode();

        // Construir mensaje
        $appName = config('app.name', 'MIJVE');
        $mensaje = "🔐 *Código de Verificación - {$appName}*\n\n";
        $mensaje .= "Hola *{$user->name}*,\n\n";
        $mensaje .= "Tu código de verificación es:\n\n";
        $mensaje .= "📌 *{$plainCode}*\n\n";
        $mensaje .= "⏰ Este código expira en *15 minutos*.\n\n";
        $mensaje .= "Si no solicitaste este código, ignora este mensaje.";

        // Enviar por WhatsApp
        try {
            $whatsapp = new WhatsAppService($user->empresa_id);
            $result = $whatsapp->sendMessage($formattedPhone, $mensaje);

            if ($result) {
                $this->resent = true;
                session()->flash('resent', 'Se ha enviado un código de verificación a tu WhatsApp.');
                Log::info('Código de verificación enviado por WhatsApp', [
                    'user_id' => $user->id,
                    'phone' => $formattedPhone,
                ]);
            } else {
                $this->errors['code'] = 'No se pudo enviar el código por WhatsApp. Intenta nuevamente.';
                Log::error('Fallo al enviar código WhatsApp - resultado nulo', [
                    'user_id' => $user->id,
                    'phone' => $formattedPhone,
                ]);
            }
        } catch (\Exception $e) {
            $this->errors['code'] = 'Error al enviar el código. Intenta nuevamente.';
            Log::error('Excepción enviando código WhatsApp: ' . $e->getMessage(), [
                'user_id' => $user->id,
            ]);
        }

        $this->canResend = false;
        $this->codeInputs = ['', '', '', '', '', ''];
        $this->code = '';
        $this->resendCountdown = 300;

        $this->js('
            let countdown = ' . $this->resendCountdown . ';
            const interval = setInterval(() => {
                countdown--;
                if (countdown <= 0) {
                    clearInterval(interval);
                    window.livewire.emit("countdownFinished");
                }
            }, 1000);
        ');
    }

    protected $listeners = ['countdownFinished' => 'enableResend'];

    public function enableResend()
    {
        $this->canResend = true;
        $this->resendCountdown = 0;
    }

    public function verifyCode()
    {
        $this->errors = [];

        if (strlen($this->code) != 6) {
            $this->errors['code'] = 'El código debe tener 6 caracteres.';
            return;
        }

        if (Auth::user()->isVerificationCodeValid($this->code)) {
            Auth::user()->markEmailAsVerified();
            request()->session()->regenerate();
            return redirect()->intended('/');
        } else {
            $this->errors['code'] = 'El código ingresado no es válido o ha expirado.';
        }
    }

    public function hasError($field)
    {
        return isset($this->errors[$field]) && !empty($this->errors[$field]);
    }

    public function getError($field)
    {
        if (!$this->hasError($field)) {
            return '';
        }
        
        $error = $this->errors[$field];
        
        // Si es un array, devolver el primer elemento
        if (is_array($error)) {
            return $error[0] ?? '';
        }
        
        return $error;
    }

    public function render()
    {
        return view('livewire.auth.verify-code', [
            'hasError' => $this->hasError(...),
            'getError' => $this->getError(...),
            'canResend' => $this->canResend,
            'resendCountdown' => $this->resendCountdown,
        ])->layout('components.layouts.auth-basic', ['title' => 'Verificar WhatsApp']);
    }
}
