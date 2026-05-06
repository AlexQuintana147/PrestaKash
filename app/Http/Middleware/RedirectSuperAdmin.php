<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            // Permitir rutas del panel superadmin y logout
            if (
                !$request->routeIs('superadmin.*') &&
                !$request->routeIs('logout')
            ) {
                return redirect()->route('superadmin.dashboard');
            }
        }

        return $next($request);
    }
}
