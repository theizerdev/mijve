<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;

class AuditAccess
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (Auth::check() && $request->isMethod('GET')) {
            try {
                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'page.access',
                    'auditable_type' => 'App\\Models\\User',
                    'auditable_id' => Auth::id(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'metadata' => [
                        'route' => $request->route()?->getName(),
                        'timestamp' => now()->toIso8601String(),
                    ],
                ]);
            } catch (\Exception $e) {
                \Log::warning('Failed to log page access: ' . $e->getMessage());
            }
        }

        return $response;
    }
}
