<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\OnboardingController;
use App\Http\Controllers\UbigeoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Ubigeo (público para el formulario)
Route::prefix('ubigeo')->name('ubigeo.')->group(function () {
    Route::get('/departments',           [UbigeoController::class, 'departments'])->name('departments');
    Route::get('/provinces/{department}', [UbigeoController::class, 'provinces'])->name('provinces');
    Route::get('/districts/{province}',   [UbigeoController::class, 'districts'])->name('districts');
});

// Google OAuth
Route::get('/auth/google',          [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

// Onboarding (requiere estar autenticado)
Route::middleware('auth')->group(function () {
    Route::get('/onboarding/step1',  [OnboardingController::class, 'step1'])->name('onboarding.step1');
    Route::post('/onboarding/step1', [OnboardingController::class, 'step1Store'])->name('onboarding.step1.store');

    // Step 2 solo accesible si el paso 1 está completo
    Route::get('/onboarding/step2',  [OnboardingController::class, 'step2'])->name('onboarding.step2')->middleware('onboarding.step1');
    Route::post('/onboarding/step2', [OnboardingController::class, 'step2Store'])->name('onboarding.step2.store')->middleware('onboarding.step1');
});
