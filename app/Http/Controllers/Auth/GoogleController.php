<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'El inicio de sesión falló. Intenta de nuevo.');
        }

        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name'      => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                'avatar'    => $googleUser->getAvatar(),
                'role_id'   => 3, // usuario
                'password'  => null,
            ]
        );

        // Si ya existía pero no tenía google_id, lo actualizamos
        if (!$user->google_id) {
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar'    => $googleUser->getAvatar(),
            ]);
        }

        Auth::login($user);

        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        }

        // Si el perfil no está completo, redirigir al onboarding
        if (!$user->profile_completed) {
            return redirect()->route('onboarding.step1');
        }

        return redirect()->intended('/');
    }
}
