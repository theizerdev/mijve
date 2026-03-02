<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Models\Extension;

class ScopeByExtension
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->hasRole('Líder de Jóvenes')) {
                $extensionIds = Extension::where('user_id', $user->id)->pluck('id')->toArray();
                $request->attributes->set('user_extension_ids', $extensionIds);
                View::share('userExtensionIds', $extensionIds);
                View::share('isLider', true);
            } else {
                View::share('isLider', false);
                View::share('userExtensionIds', []);
            }

            View::share('isAdmin', $user->hasRole(['Super Administrador', 'Administrador']));
        }

        return $next($request);
    }
}
