<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfIncomplete
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Si está logueado y no ha completado el perfil, redirigir al onboarding
        if ($user && !$user->profile_completed) {
            // Permitir solo las rutas del onboarding, logout y ubigeo
            $allowed = [
                'onboarding.step1',
                'onboarding.step1.store',
                'onboarding.step2',
                'onboarding.step2.store',
                'logout',
                'ubigeo.departments',
                'ubigeo.provinces',
                'ubigeo.districts',
            ];

            if (!in_array($request->route()->getName(), $allowed)) {
                // Si no ha completado el paso 1, siempre ir al paso 1
                if (!$user->step1_completed) {
                    return redirect()->route('onboarding.step1');
                }
                // Si completó el paso 1 pero no el perfil completo, ir al paso 2
                return redirect()->route('onboarding.step2');
            }
        }

        return $next($request);
    }
}
