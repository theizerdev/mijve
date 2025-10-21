<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        // Verificar si el usuario está autenticado y es el super admin (ID = 1)
        if (auth()->check() && auth()->id() === 1) {
            // Verificar si la ruta actual NO es la del super admin dashboard

                // Redirigir al dashboard del super admin
                return redirect()->route('superadmin.dashboard');

        }

        return $next($request);
    }
}
