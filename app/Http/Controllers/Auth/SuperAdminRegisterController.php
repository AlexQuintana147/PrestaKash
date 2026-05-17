<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SuperAdminRegisterController extends Controller
{
    private function superAdminExists(): bool
    {
        $role = Role::where('name', 'superadmin')->first();
        return $role && User::where('role_id', $role->id)->exists();
    }

    private function validateToken(string $token): bool
    {
        return $token === config('app.superadmin_token');
    }

    public function show(string $token)
    {
        if (!$this->validateToken($token)) {
            abort(404);
        }

        if ($this->superAdminExists()) {
            return view('auth.superadmin-register', ['unavailable' => true]);
        }

        return view('auth.superadmin-register', ['unavailable' => false, 'token' => $token]);
    }

    public function register(Request $request, string $token)
    {
        if (!$this->validateToken($token) || $this->superAdminExists()) {
            abort(404);
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $role = Role::where('name', 'superadmin')->firstOrFail();

        $user = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'role_id'           => $role->id,
            'profile_completed' => true,
            'step1_completed'   => true,
        ]);

        Auth::login($user);

        return redirect()->route('superadmin.dashboard');
    }

    // Callback de Google cuando viene desde el flujo superadmin
    public function googleCallback()
    {
        $token = session('superadmin_register_token');

        if (!$token || !$this->validateToken($token) || $this->superAdminExists()) {
            abort(404);
        }

        try {
            $googleUser = \Laravel\Socialite\Facades\Socialite::driver('google')
                ->redirectUrl(env('GOOGLE_REDIRECT_URI_SUPERADMIN'))
                ->user();
        } catch (\Exception $e) {
            return redirect()->route('superadmin.register.show', $token)
                ->with('error', 'El inicio de sesión con Google falló. Intenta de nuevo.');
        }

        $role = Role::where('name', 'superadmin')->firstOrFail();

        // Si el email ya existe en la BD, no permitir
        if (User::where('email', $googleUser->getEmail())->exists()) {
            return redirect()->route('superadmin.register.show', $token)
                ->with('error', 'Este correo ya está registrado como usuario. Usa un correo diferente.');
        }

        $user = User::create([
            'name'              => $googleUser->getName(),
            'google_id'         => $googleUser->getId(),
            'avatar'            => $googleUser->getAvatar(),
            'email'             => $googleUser->getEmail(),
            'role_id'           => $role->id,
            'profile_completed' => true,
            'step1_completed'   => true,
            'password'          => null,
        ]);

        session()->forget('superadmin_register_token');
        Auth::login($user);

        return redirect()->route('superadmin.dashboard');
    }
}
