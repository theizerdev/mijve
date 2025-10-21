<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\Auth\TwoFactorLogin;
use App\Livewire\SuperAdmin\Dashboard as SuperAdminDashboard;

Route::get('/', function () {
   if (\Auth::check() && \Auth::user()->id == 1) {

    return redirect()->to('superadmin/dashboard');
   } else {
    # code...
    return redirect()->to('admin/dashboard');
   }

});

// Rutas de autenticación con Livewire
Route::middleware('guest')->group(function () {
    Route::get('login', \App\Livewire\Auth\Login::class)->name('login');
    Route::get('register', \App\Livewire\Auth\Register::class)->name('register');
    Route::get('password/reset', \App\Livewire\Auth\ForgotPassword::class)->name('password.request');
    Route::get('password/reset/{token}', \App\Livewire\Auth\ResetPassword::class)->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', \App\Livewire\Auth\VerifyCode::class)->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', function (string $id, string $hash) {
        // Esta ruta se usa para la verificación real del correo electrónico
        // Pero como estamos usando Livewire, simplemente redirigimos al componente Verify
        return redirect()->route('verification.notice');
    })->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
});

// Ruta de logout
Route::post('logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})->name('logout');

// Ruta para verificación 2FA
Route::get('/two-factor-login', \App\Livewire\Auth\TwoFactorLogin::class)->name('two-factor.login');

// Super Admin routes
Route::group(['prefix' => 'superadmin', 'as' => 'superadmin.', 'middleware' => ['auth', 'verified']], function () {
    Route::get('/dashboard', SuperAdminDashboard::class)->name('dashboard');
});


    require __DIR__.'/admin.php';


