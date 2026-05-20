<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\BlockedUser;
use App\Models\Referral;
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
            $redirect = '/';

            if ($user->isSuperAdmin()) {
                $redirect = route('superadmin.dashboard');
            } elseif ($user->profile_completed && $user->documents?->status === 'approved') {
                $redirect = route('client.dashboard');
            } elseif (!$user->profile_completed) {
                $redirect = !$user->step1_completed ? route('onboarding.step1') : route('onboarding.step2');
            }

            if ($request->ajax()) {
                return response()->json(['success' => true, 'redirect' => $redirect]);
            }

            return redirect($redirect);
        }

        $userExists = User::where('email', $request->email)->exists();
        $errorMessage = $userExists
            ? 'La contraseña es incorrecta.'
            : 'Este usuario no existe en nuestros registros.';

        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => $errorMessage], 422);
        }

        return back()->withErrors([
            'email' => $errorMessage,
        ])->withInput();
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (BlockedUser::where('email', $request->email)->exists()) {
            return back()->withErrors(['email' => 'Este correo no puede ser usado para registrarse.'])->withInput();
        }

        $referredBy = null;
        if ($request->referred_by_code) {
            $referrer = User::where('referral_code', $request->referred_by_code)->first();
            if ($referrer) {
                $referredBy = $referrer->id;
            }
        }

        $user = User::create([
            'name' => explode('@', $request->email)[0],
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => \App\Models\Role::where('name', 'usuario')->value('id') ?? 3,
            'profile_completed' => false,
            'step1_completed' => false,
            'referred_by' => $referredBy,
            'referral_code' => User::generateReferralCode(),
        ]);

        if ($referredBy) {
            Referral::create([
                'referrer_id' => $referredBy,
                'referred_id' => $user->id,
                'referral_code' => $request->referred_by_code,
                'status' => 'pending',
            ]);
        }

        Auth::login($user);

        return redirect()->route('onboarding.step1');
    }

    public function showRegisterReferral(string $code)
    {
        $referrer = User::where('referral_code', $code)->first();

        if (!$referrer) {
            return redirect('/')->with('error', 'Código de referido inválido.');
        }

        return view('auth.register-referral', [
            'referralCode' => $code,
            'referrerName' => $referrer->name,
        ]);
    }

    public function showRegister()
    {
        return view('auth.register-referral', [
            'referralCode' => null,
            'referrerName' => null,
        ]);
    }

    public function checkEmail(Request $request)
    {
        $exists = User::where('email', $request->email)->exists();
        return response()->json(['exists' => $exists]);
    }
}
