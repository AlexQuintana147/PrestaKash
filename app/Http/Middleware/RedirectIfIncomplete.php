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

        if ($user) {
            // Si el perfil está completo pero aún en validación, mostrar pantalla de espera
            if ($user->profile_completed && $user->documents && $user->documents->status === 'pending') {
                $allowed = ['validation.pending', 'logout'];
                if (!in_array($request->route()->getName(), $allowed)) {
                    return redirect()->route('validation.pending');
                }
            }

            // Si no ha completado el perfil, redirigir al onboarding
            if (!$user->profile_completed) {
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
                    if (!$user->step1_completed) {
                        return redirect()->route('onboarding.step1');
                    }
                    return redirect()->route('onboarding.step2');
                }
            }
        }

        return $next($request);
    }
}
