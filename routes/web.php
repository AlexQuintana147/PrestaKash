<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\OnboardingController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\UbigeoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check() && auth()->user()->isSuperAdmin()) {
        return redirect()->route('superadmin.dashboard');
    }
    return view('welcome');
});

// Ruta login requerida por el middleware auth de Laravel
Route::get('/login', function () {
    return redirect('/');
})->name('login');

// Ubigeo (público para el formulario)
Route::prefix('ubigeo')->name('ubigeo.')->group(function () {
    Route::get('/departments',           [UbigeoController::class, 'departments'])->name('departments');
    Route::get('/provinces/{department}', [UbigeoController::class, 'provinces'])->name('provinces');
    Route::get('/districts/{province}',   [UbigeoController::class, 'districts'])->name('districts');
});

// Logout
Route::post('/logout', function () {
    \Illuminate\Support\Facades\Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout')->middleware('auth');

// Validation pending
Route::get('/validation-pending', function () {
    return view('auth.validation-pending');
})->name('validation.pending')->middleware('auth');

// Google OAuth
Route::get('/auth/google',          [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

// Auth manual
Route::post('/login', [\App\Http\Controllers\Auth\AuthController::class, 'login'])->name('login.post');
Route::post('/register', [\App\Http\Controllers\Auth\AuthController::class, 'register'])->name('register');
Route::post('/check-email', [\App\Http\Controllers\Auth\AuthController::class, 'checkEmail'])->name('check.email');

// Onboarding (requiere estar autenticado)
Route::middleware('auth')->group(function () {
    Route::get('/onboarding/step1',  [OnboardingController::class, 'step1'])->name('onboarding.step1');
    Route::post('/onboarding/step1', [OnboardingController::class, 'step1Store'])->name('onboarding.step1.store');
    Route::post('/check-dni', [OnboardingController::class, 'checkDni'])->name('check.dni');
    Route::post('/check-phone', [OnboardingController::class, 'checkPhone'])->name('check.phone');

    // Step 2 solo accesible si el paso 1 está completo
    Route::get('/onboarding/step2',  [OnboardingController::class, 'step2'])->name('onboarding.step2')->middleware('onboarding.step1');
    Route::post('/onboarding/step2', [OnboardingController::class, 'step2Store'])->name('onboarding.step2.store')->middleware('onboarding.step1');
});

// SuperAdmin (ruta secreta)
Route::middleware(['auth', 'superadmin', 'prevent.back', 'cleanup.rejected'])->prefix('gestion-interna')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Gestión de usuarios
    Route::get('/usuarios',                    [\App\Http\Controllers\SuperAdmin\UsersController::class, 'index'])->name('users.index');
    Route::get('/usuarios/{user}',             [\App\Http\Controllers\SuperAdmin\UsersController::class, 'show'])->name('users.show');
    Route::post('/usuarios/{user}/aprobar',    [\App\Http\Controllers\SuperAdmin\UsersController::class, 'approve'])->name('users.approve');
    Route::post('/usuarios/{user}/rechazar',   [\App\Http\Controllers\SuperAdmin\UsersController::class, 'reject'])->name('users.reject');
    Route::post('/usuarios/{user}/desaprobar', [\App\Http\Controllers\SuperAdmin\UsersController::class, 'unapprove'])->name('users.unapprove');
    Route::post('/usuarios/{user}/bloquear',   [\App\Http\Controllers\SuperAdmin\UsersController::class, 'block'])->name('users.block');

    // Configuración
    Route::get('/configuracion',              [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'index'])->name('settings');
    Route::post('/configuracion/nombre',      [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'updateName'])->name('settings.name');
    Route::post('/configuracion/contrasena',  [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'updatePassword'])->name('settings.password');
});

// Panel Cliente (usuarios aprobados)
Route::middleware(['auth'])->prefix('panel')->name('client.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Client\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/prestamos',         [\App\Http\Controllers\Client\LoanController::class, 'index'])->name('loans');
    Route::post('/prestamos/perfil', [\App\Http\Controllers\Client\LoanController::class, 'storeProfile'])->name('loans.profile.store');
});
Route::prefix('registro-maestro/{token}')->name('superadmin.register.')->group(function () {
    Route::get('/',         [\App\Http\Controllers\Auth\SuperAdminRegisterController::class, 'show'])->name('show');
    Route::post('/',        [\App\Http\Controllers\Auth\SuperAdminRegisterController::class, 'register'])->name('store');
    Route::get('/google',   function (string $token) {
        session(['superadmin_register_token' => $token]);
        return \Laravel\Socialite\Facades\Socialite::driver('google')
            ->redirectUrl(route('superadmin.register.google.callback'))
            ->redirect();
    })->name('google');
});

// Callback de Google para superadmin (sin token en URL, viene de sesión)
Route::get('/auth/google/superadmin-callback', [\App\Http\Controllers\Auth\SuperAdminRegisterController::class, 'googleCallback'])
    ->name('superadmin.register.google.callback');
