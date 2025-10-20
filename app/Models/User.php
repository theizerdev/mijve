<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'verification_code',
        'verification_code_sent_at',
        'empresa_id',
        'sucursal_id',
        'avatar',
        'two_factor_enabled',
        'preferred_devices',
        'common_locations',
        'total_session_time',
        'security_alerts',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_code',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'verification_code_sent_at' => 'datetime',
        ];
    }

    /**
     * Generar un código de verificación de 6 dígitos numéricos
     */
    public function generateVerificationCode()
    {
        // Generar código numérico de 6 dígitos
        $code = '';
        for ($i = 0; $i < 6; $i++) {
            $code .= rand(0, 9);
        }
        
        // Almacenar el código cifrado
        $this->verification_code = Hash::make($code);
        $this->verification_code_sent_at = Carbon::now();
        $this->save();
        
        // Devolver el código sin cifrar para enviar por correo
        return $code;
    }

    /**
     * Verificar si el código proporcionado es válido
     */
    public function isVerificationCodeValid($code)
    {
        // Verificar que el código exista y no haya expirado (15 minutos)
        if (!$this->verification_code || !$this->verification_code_sent_at) {
            return false;
        }

        if (Carbon::now()->diffInMinutes($this->verification_code_sent_at) > 15) {
            return false;
        }

        // Verificar que el código coincida
        return Hash::check($code, $this->verification_code);
    }

    /**
     * Marcar el correo electrónico como verificado
     */
    public function markEmailAsVerified()
    {
        $this->email_verified_at = Carbon::now();
        $this->verification_code = null;
        $this->verification_code_sent_at = null;
        $this->save();
    }

    /**
     * Get the active sessions for the user.
     */
    public function activeSessions()
    {
        return $this->hasMany(ActiveSession::class);
    }

    /**
     * Get the empresa that owns the user.
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Get the sucursal that belongs to the user.
     */
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    /**
     * Get the user's common locations from active sessions
     */
    public function getCommonLocationsAttribute()
    {
        return $this->activeSessions()
            ->select('location')
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->groupBy('location')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(5)
            ->get()
            ->map(function ($session) {
                // Dividir la cadena de ubicación en partes
                $parts = explode(', ', $session->location);
                
                // Asegurarse de que tenemos al menos 3 partes (ciudad, estado, país)
                $city = count($parts) >= 1 ? $parts[0] : 'Desconocido';
                $state = count($parts) >= 2 ? $parts[1] : 'Desconocido';
                $country = count($parts) >= 3 ? $parts[2] : 'Desconocido';
                
                return [
                    'city' => $city,
                    'state' => $state,
                    'country' => $country
                ];
            });
    }
}