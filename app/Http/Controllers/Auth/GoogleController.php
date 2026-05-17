<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\BlockedUser;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function redirectWithReferral(string $code)
    {
        $referrer = User::where('referral_code', $code)->first();

        if ($referrer) {
            session(['referral_code' => $code]);
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        if (session('superadmin_register_token')) {
            return app(\App\Http\Controllers\Auth\SuperAdminRegisterController::class)->googleCallback();
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'El inicio de sesión falló. Intenta de nuevo.');
        }

        if (BlockedUser::where('email', $googleUser->getEmail())->exists()) {
            return redirect('/')->with('error', 'Esta cuenta ha sido bloqueada. Contacta al soporte.');
        }

        $existingUser = User::where('email', $googleUser->getEmail())->first();

        if (session('referral_code') && $existingUser) {
            $code = session('referral_code');
            session()->forget('referral_code');
            return redirect()->route('referral.handle', ['code' => $code])
                ->with('error', 'Este correo ya está registrado.');
        }

        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name'      => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                'avatar'    => $googleUser->getAvatar(),
                'role_id'   => 2,
                'password'  => null,
                'referral_code' => User::generateReferralCode(),
            ]
        );

        if (!$user->google_id) {
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar'    => $googleUser->getAvatar(),
            ]);
        }

        if (session('referral_code')) {
            $referrer = User::where('referral_code', session('referral_code'))->first();
            if ($referrer) {
                if ($user->wasRecentlyCreated && !$user->referred_by) {
                    $user->update(['referred_by' => $referrer->id]);

                    Referral::create([
                        'referrer_id' => $referrer->id,
                        'referred_id' => $user->id,
                        'referral_code' => session('referral_code'),
                        'status' => 'pending',
                    ]);
                }
            }
            session()->forget('referral_code');
        }

        Auth::login($user);

        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        }

        if ($user->profile_completed && $user->documents?->status === 'approved') {
            return redirect()->route('client.dashboard');
        }

        if (!$user->profile_completed) {
            return redirect()->route('onboarding.step1');
        }

        return redirect()->intended('/');
    }
}
