<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingStep1Completed
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user || !$user->step1_completed) {
            return redirect()->route('onboarding.step1')
                ->with('error', 'Debes completar tus datos personales primero.');
        }

        return $next($request);
    }
}
