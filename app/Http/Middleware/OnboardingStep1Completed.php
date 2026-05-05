<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OnboardingStep1Completed
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('onboarding_step1_done')) {
            return redirect()->route('onboarding.step1')
                ->with('error', 'Debes completar tus datos personales primero.');
        }

        return $next($request);
    }
}
