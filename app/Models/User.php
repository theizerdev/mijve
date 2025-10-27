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
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles, LogsActivity;

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
        'status',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
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
            'two_factor_enabled' => 'boolean',
            'two_factor_recovery_codes' => 'array',
            'verification_code_sent_at' => 'datetime',
        ];
    }

    /**
     * Generar un código de verificación de 8 caracteres (alfanumérico)
     */
    public function generateVerificationCode()
    {
        // Generar código alfanumérico de 8 caracteres
        $code = strtoupper(Str::random(6));

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
        if (method_exists($this, 'sessions')) {
            return $this->sessions()
                ->whereNotNull('location')
                ->select('location')
                ->distinct()
                ->limit(5)
                ->get()
                ->toArray();
        }

        return [];
    }

    public function scopeForUser($query)
    {
        if (auth()->check() && !auth()->user()->hasRole('Super Administrador')) {
            if (auth()->user()->empresa_id) {
                $query->where('empresa_id', auth()->user()->empresa_id);
            }
            if (auth()->user()->sucursal_id) {
                $query->where('sucursal_id', auth()->user()->sucursal_id);
            }
        }
        return $query;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'empresa_id', 'sucursal_id', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
