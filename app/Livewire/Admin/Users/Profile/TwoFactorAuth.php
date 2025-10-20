<?php

namespace App\Livewire\Admin\Users\Profile;

use Livewire\Component;
use App\Models\User;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class TwoFactorAuth extends Component
{
    public User $user;
    public $enabled;
    public $showQrCode = false;
    public $qrCode;
    public $secret;
    public $verificationCode;
    public $recoveryCodes = [];

    public function mount(User $user)
    {
        $this->user = $user;
        $this->enabled = $user->two_factor_enabled;
    }

    public function toggle2FA()
    {
        if ($this->enabled) {
            $this->disable2FA();
        } else {
            $this->enable2FA();
        }
    }

    public function enable2FA()
    {
        $google2fa = new Google2FA();
        $this->secret = $google2fa->generateSecretKey();

        // Generar QR Code
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $this->user->email,
            $this->secret
        );

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $this->qrCode = $writer->writeString($qrCodeUrl);
        $this->showQrCode = true;

        // Generar códigos de recuperación
        $this->generateRecoveryCodes();
    }

    public function verifyCode()
    {
        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($this->secret, $this->verificationCode);

        if ($valid) {
            $this->user->update([
                'two_factor_secret' => encrypt($this->secret),
                'two_factor_enabled' => true,
                'two_factor_recovery_codes' => $this->recoveryCodes
            ]);

            $this->enabled = true;
            $this->showQrCode = false;
            $this->dispatch('notify', ['message' => 'Autenticación en dos pasos activada']);
        } else {
            $this->addError('verificationCode', 'Código inválido');
        }
    }

    public function disable2FA()
    {
        $this->user->update([
            'two_factor_secret' => null,
            'two_factor_enabled' => false,
            'two_factor_recovery_codes' => null
        ]);

        $this->enabled = false;
        $this->dispatch('notify', ['message' => 'Autenticación en dos pasos desactivada']);
    }

    public function generateRecoveryCodes()
    {
        $this->recoveryCodes = collect()
            ->times(8, fn () => Str::random(10).'-'.Str::random(10))
            ->toArray();
    }

    public function render()
    {
        return view('livewire.admin.users.profile.two-factor-auth');
    }
}
