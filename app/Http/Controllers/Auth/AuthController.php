<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\BlockedUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Verificar si está bloqueado
        if (BlockedUser::where('email', $request->email)->exists()) {
            return back()->withErrors(['email' => 'Esta cuenta ha sido bloqueada. Contacta al soporte.'])->withInput();
        }

        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->isSuperAdmin()) {
                return redirect()->route('superadmin.dashboard');
            }

            // Cliente aprobado
            if ($user->profile_completed && $user->documents?->status === 'approved') {
                return redirect()->route('client.dashboard');
            }

            // Redirigir según el estado del perfil
            if (!$user->profile_completed) {
                if (!$user->step1_completed) {
                    return redirect()->route('onboarding.step1');
                }
                return redirect()->route('onboarding.step2');
            }

            return redirect('/');
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->withInput();
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Verificar si está bloqueado
        if (BlockedUser::where('email', $request->email)->exists()) {
            return back()->withErrors(['email' => 'Este correo no puede ser usado para registrarse.'])->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 2, // Trabajador por defecto
            'profile_completed' => false,
            'step1_completed' => false,
        ]);

        Auth::login($user);

        return redirect()->route('onboarding.step1');
    }

    public function checkEmail(Request $request)
    {
        $exists = User::where('email', $request->email)->exists();
        return response()->json(['exists' => $exists]);
    }
}
