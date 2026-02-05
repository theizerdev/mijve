<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        
        // Configurar vista de paginación personalizada para Livewire
        Paginator::defaultView('livewire.pagination');
        Paginator::defaultSimpleView('livewire.pagination');
    }
}