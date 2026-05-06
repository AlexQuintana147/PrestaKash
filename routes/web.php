<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\OnboardingController;
use App\Http\Controllers\UbigeoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
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

    // Step 2 solo accesible si el paso 1 está completo
    Route::get('/onboarding/step2',  [OnboardingController::class, 'step2'])->name('onboarding.step2')->middleware('onboarding.step1');
    Route::post('/onboarding/step2', [OnboardingController::class, 'step2Store'])->name('onboarding.step2.store')->middleware('onboarding.step1');
});
