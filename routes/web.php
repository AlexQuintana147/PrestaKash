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
    Route::get('/departments', [UbigeoController::class, 'departments'])->name('departments');
    Route::get('/provinces/{department}', [UbigeoController::class, 'provinces'])->name('provinces');
    Route::get('/districts/{province}', [UbigeoController::class, 'districts'])->name('districts');
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

// Google OAuth - el callback debe ir ANTES de {code} para evitar conflictos
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/{code}', [GoogleController::class, 'redirectWithReferral'])->name('auth.google.referral');

// Referral link - muestra página de registro
Route::get('/ref/{code}', [\App\Http\Controllers\Auth\AuthController::class, 'showRegisterReferral'])->name('referral.handle');

// Auth manual
Route::post('/login', [\App\Http\Controllers\Auth\AuthController::class, 'login'])->name('login.post');
Route::get('/registro', [\App\Http\Controllers\Auth\AuthController::class, 'showRegister'])->name('register.show');
Route::post('/register', [\App\Http\Controllers\Auth\AuthController::class, 'register'])->name('register');
Route::post('/check-email', [\App\Http\Controllers\Auth\AuthController::class, 'checkEmail'])->name('check.email');

// Onboarding (requiere estar autenticado)
Route::middleware('auth')->group(function () {
    Route::get('/onboarding/step1', [OnboardingController::class, 'step1'])->name('onboarding.step1');
    Route::post('/onboarding/step1', [OnboardingController::class, 'step1Store'])->name('onboarding.step1.store');
    Route::post('/check-dni', [OnboardingController::class, 'checkDni'])->name('check.dni');
    Route::post('/check-phone', [OnboardingController::class, 'checkPhone'])->name('check.phone');

    // Step 2 solo accesible si el paso 1 está completo
    Route::get('/onboarding/step2', [OnboardingController::class, 'step2'])->name('onboarding.step2')->middleware('onboarding.step1');
    Route::post('/onboarding/step2', [OnboardingController::class, 'step2Store'])->name('onboarding.step2.store')->middleware('onboarding.step1');
});

// SuperAdmin (ruta secreta)
Route::middleware(['auth', 'superadmin', 'prevent.back', 'cleanup.rejected'])->prefix('gestion-interna')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Gestión de usuarios
    Route::get('/usuarios', [\App\Http\Controllers\SuperAdmin\UsersController::class, 'index'])->name('users.index');
    Route::get('/usuarios/crear-fantasma', [\App\Http\Controllers\SuperAdmin\UsersController::class, 'createGhost'])->name('users.create-ghost');
    Route::post('/usuarios/crear-fantasma', [\App\Http\Controllers\SuperAdmin\UsersController::class, 'storeGhost'])->name('users.store-ghost');
    Route::get('/usuarios/{user}', [\App\Http\Controllers\SuperAdmin\UsersController::class, 'show'])->name('users.show');
    Route::post('/usuarios/{user}/aprobar', [\App\Http\Controllers\SuperAdmin\UsersController::class, 'approve'])->name('users.approve');
    Route::post('/usuarios/{user}/rechazar', [\App\Http\Controllers\SuperAdmin\UsersController::class, 'reject'])->name('users.reject');
    Route::post('/usuarios/{user}/desaprobar', [\App\Http\Controllers\SuperAdmin\UsersController::class, 'unapprove'])->name('users.unapprove');
    Route::post('/usuarios/{user}/bloquear', [\App\Http\Controllers\SuperAdmin\UsersController::class, 'block'])->name('users.block');
    Route::patch('/usuarios/{user}/field', [\App\Http\Controllers\SuperAdmin\UsersController::class, 'updateField'])->name('users.update-field');

    // Gestión de Inversores
    Route::get('/inversores', [\App\Http\Controllers\SuperAdmin\InvestorController::class, 'index'])->name('investors.index');
    Route::post('/inversores/{application}/aprobar', [\App\Http\Controllers\SuperAdmin\InvestorController::class, 'approve'])->name('investors.approve');
    Route::post('/inversores/{application}/desaprobar', [\App\Http\Controllers\SuperAdmin\InvestorController::class, 'unapprove'])->name('investors.unapprove');
    Route::post('/inversores/{application}/rechazar', [\App\Http\Controllers\SuperAdmin\InvestorController::class, 'reject'])->name('investors.reject');

    // Configuración
    Route::get('/configuracion', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'index'])->name('settings');
    Route::post('/configuracion/nombre', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'updateName'])->name('settings.name');
    Route::post('/configuracion/contrasena', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'updatePassword'])->name('settings.password');
    Route::post('/configuracion/empresa', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'updateCompanySettings'])->name('settings.company');
});

// Panel Cliente (usuarios aprobados)
Route::middleware(['auth'])->prefix('panel')->name('client.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Client\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/prestamos', [\App\Http\Controllers\Client\LoanController::class, 'index'])->name('loans');
    Route::get('/prestamos/pedir', [\App\Http\Controllers\Client\LoanController::class, 'create'])->name('loans.create');
    Route::post('/prestamos/pedir', [\App\Http\Controllers\Client\LoanController::class, 'store'])->name('loans.store');
    Route::post('/prestamos/perfil', [\App\Http\Controllers\Client\LoanController::class, 'storeProfile'])->name('loans.profile.store');
    Route::get('/prestamos/perfil/editar', [\App\Http\Controllers\Client\LoanController::class, 'editProfile'])->name('loans.profile.edit');
    Route::put('/prestamos/perfil', [\App\Http\Controllers\Client\LoanController::class, 'updateProfile'])->name('loans.profile.update');
    Route::get('/referidos', [\App\Http\Controllers\Client\ReferralController::class, 'index'])->name('referidos');
    Route::get('/invertir', [\App\Http\Controllers\Client\DashboardController::class, 'invest'])->name('invertir');
    Route::get('/invertir/postular', [\App\Http\Controllers\Client\InvestorApplicationController::class, 'create'])->name('invest.apply');
    Route::post('/invertir/postular', [\App\Http\Controllers\Client\InvestorApplicationController::class, 'store'])->name('invest.store');

    // Cuentas bancarias
    Route::get('/cuentas-bancarias', [\App\Http\Controllers\Client\BankAccountController::class, 'index'])->name('bank-accounts.index');
    Route::post('/cuentas-bancarias', [\App\Http\Controllers\Client\BankAccountController::class, 'store'])->name('bank-accounts.store');
    Route::put('/cuentas-bancarias/{bankAccount}', [\App\Http\Controllers\Client\BankAccountController::class, 'update'])->name('bank-accounts.update');
    Route::delete('/cuentas-bancarias/{bankAccount}', [\App\Http\Controllers\Client\BankAccountController::class, 'destroy'])->name('bank-accounts.destroy');
});
Route::prefix('registro-maestro/{token}')->name('superadmin.register.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Auth\SuperAdminRegisterController::class, 'show'])->name('show');
    Route::post('/', [\App\Http\Controllers\Auth\SuperAdminRegisterController::class, 'register'])->name('store');
    Route::get('/google', function (string $token) {
        session(['superadmin_register_token' => $token]);
        return \Laravel\Socialite\Facades\Socialite::driver('google')
            ->redirectUrl(route('superadmin.register.google.callback'))
            ->redirect();
    })->name('google');
});

// Callback de Google para superadmin (sin token en URL, viene de sesión)
Route::get('/auth/google/superadmin-callback', [\App\Http\Controllers\Auth\SuperAdminRegisterController::class, 'googleCallback'])
    ->name('superadmin.register.google.callback');
