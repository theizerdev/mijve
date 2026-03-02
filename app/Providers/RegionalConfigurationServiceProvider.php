<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Services\RegionalConfigurationService;
use App\Models\Empresa;
use App\Models\Pais;

class RegionalConfigurationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(RegionalConfigurationService::class, function ($app) {
            return new RegionalConfigurationService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Registrar eventos para cambios de empresa
        $this->registerEvents();
    }

    /**
     * Registrar eventos para cambios de configuración
     */
    private function registerEvents(): void
    {
        // Evento cuando se cambia la empresa seleccionada
        \Illuminate\Support\Facades\Event::listen(
            'empresa.changed',
            function ($empresa) {
                RegionalConfigurationService::setRegionalConfiguration($empresa);
            }
        );
    }
}

// Helpers globales definidos fuera de la clase para evitar redeclaración en tests
if (!function_exists('App\Providers\current_regional_config')) {
    function current_regional_config() {
        return \App\Services\RegionalConfigurationService::getCurrentConfiguration();
    }
}

if (!function_exists('App\Providers\current_pais')) {
    function current_pais() {
        $config = \App\Services\RegionalConfigurationService::getCurrentConfiguration();
        return $config['pais'] ?? null;
    }
}

if (!function_exists('App\Providers\current_currency')) {
    function current_currency() {
        $config = \App\Services\RegionalConfigurationService::getCurrentConfiguration();
        return $config['currency'] ?? config('app.currency', 'USD');
    }
}

if (!function_exists('App\Providers\current_currency_symbol')) {
    function current_currency_symbol() {
        $config = \App\Services\RegionalConfigurationService::getCurrentConfiguration();
        return $config['currency_symbol'] ?? '$';
    }
}
