<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            // Redirigir superadmin y trabajadores al panel de gestión
            if ($user->isSuperAdmin() || $user->isTrabajador()) {
                // Permitir rutas del panel superadmin y logout
                if (
                    !$request->routeIs('superadmin.*') &&
                    !$request->routeIs('ubigeo.*') &&
 !$request->routeIs('logout')
                ) {
                    return redirect()->route('superadmin.dashboard');
                }
            }
        }

        return $next($request);
    }
}
