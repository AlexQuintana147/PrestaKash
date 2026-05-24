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
    Route::post('/check-ruc', [OnboardingController::class, 'checkRuc'])->name('check.ruc');

    // Step 2 solo accesible si el paso 1 está completo
    Route::get('/onboarding/step2', [OnboardingController::class, 'step2'])->name('onboarding.step2')->middleware('onboarding.step1');
    Route::post('/onboarding/step2', [OnboardingController::class, 'step2Store'])->name('onboarding.step2.store')->middleware('onboarding.step1');
});

// SuperAdmin (ruta secreta)
Route::middleware(['auth', 'superadmin', 'prevent.back', 'cleanup.rejected'])->prefix('gestion-interna')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/sidebar-stats', [DashboardController::class, 'sidebarStats'])->name('sidebar-stats');

    // Gestión de usuarios
    Route::get('/usuarios', [\App\Http\Controllers\SuperAdmin\UsersController::class, 'index'])->name('users.index');
    Route::get('/usuarios/crear-fantasma', [\App\Http\Controllers\SuperAdmin\UsersController::class, 'createGhost'])->name('users.create-ghost');
    Route::post('/usuarios/crear-fantasma', [\App\Http\Controllers\SuperAdmin\UsersController::class, 'storeGhost'])->name('users.store-ghost');
    Route::get('/usuarios/fantasma/{user}/editar', [\App\Http\Controllers\SuperAdmin\UsersController::class, 'editGhost'])->name('users.edit-ghost');
    Route::put('/usuarios/fantasma/{user}', [\App\Http\Controllers\SuperAdmin\UsersController::class, 'updateGhost'])->name('users.update-ghost');
    Route::delete('/usuarios/fantasma/{user}', [\App\Http\Controllers\SuperAdmin\UsersController::class, 'destroyGhost'])->name('users.destroy-ghost');
    Route::get('/usuarios/{user}', [\App\Http\Controllers\SuperAdmin\UsersController::class, 'show'])->name('users.show');
    Route::post('/usuarios/{user}/aprobar', [\App\Http\Controllers\SuperAdmin\UsersController::class, 'approve'])->name('users.approve');
    Route::post('/usuarios/{user}/rechazar', [\App\Http\Controllers\SuperAdmin\UsersController::class, 'reject'])->name('users.reject');
    Route::post('/usuarios/{user}/desaprobar', [\App\Http\Controllers\SuperAdmin\UsersController::class, 'unapprove'])->name('users.unapprove');
    Route::post('/usuarios/{user}/bloquear', [\App\Http\Controllers\SuperAdmin\UsersController::class, 'block'])->name('users.block');
    Route::patch('/usuarios/{user}/field', [\App\Http\Controllers\SuperAdmin\UsersController::class, 'updateField'])->name('users.update-field');
    Route::post('/solicitudes-cambio/{profileChangeRequest}/aprobar', [\App\Http\Controllers\SuperAdmin\UsersController::class, 'approveProfileChange'])->name('profile-changes.approve');
    Route::post('/solicitudes-cambio/{profileChangeRequest}/rechazar', [\App\Http\Controllers\SuperAdmin\UsersController::class, 'rejectProfileChange'])->name('profile-changes.reject');

    // Gestión de Trabajadores
    Route::get('/trabajadores', [\App\Http\Controllers\SuperAdmin\WorkerController::class, 'index'])->name('workers.index');
    Route::get('/trabajadores/crear', [\App\Http\Controllers\SuperAdmin\WorkerController::class, 'create'])->name('workers.create');
    Route::post('/trabajadores', [\App\Http\Controllers\SuperAdmin\WorkerController::class, 'store'])->name('workers.store');
    Route::get('/trabajadores/{worker}/editar', [\App\Http\Controllers\SuperAdmin\WorkerController::class, 'edit'])->name('workers.edit');
    Route::put('/trabajadores/{worker}', [\App\Http\Controllers\SuperAdmin\WorkerController::class, 'update'])->name('workers.update');
    Route::delete('/trabajadores/{worker}', [\App\Http\Controllers\SuperAdmin\WorkerController::class, 'destroy'])->name('workers.destroy');

    // Gestión de Inversores
    Route::get('/inversores', [\App\Http\Controllers\SuperAdmin\InvestorController::class, 'index'])->name('investors.index');
    Route::post('/inversores/{application}/aprobar', [\App\Http\Controllers\SuperAdmin\InvestorController::class, 'approve'])->name('investors.approve');
    Route::post('/inversores/{application}/desaprobar', [\App\Http\Controllers\SuperAdmin\InvestorController::class, 'unapprove'])->name('investors.unapprove');
    Route::post('/inversores/{application}/rechazar', [\App\Http\Controllers\SuperAdmin\InvestorController::class, 'reject'])->name('investors.reject');

    // Control de Inversiones
    Route::get('/inversiones', [\App\Http\Controllers\SuperAdmin\InvestmentOpportunityController::class, 'index'])->name('investments.index');
    Route::get('/inversiones/crear', [\App\Http\Controllers\SuperAdmin\InvestmentOpportunityController::class, 'create'])->name('investments.create');
    Route::post('/inversiones', [\App\Http\Controllers\SuperAdmin\InvestmentOpportunityController::class, 'store'])->name('investments.store');
    Route::get('/inversiones/{investment}', [\App\Http\Controllers\SuperAdmin\InvestmentOpportunityController::class, 'show'])->name('investments.show');
    Route::put('/inversiones/{investment}', [\App\Http\Controllers\SuperAdmin\InvestmentOpportunityController::class, 'update'])->name('investments.update');
    Route::delete('/inversiones/{investment}', [\App\Http\Controllers\SuperAdmin\InvestmentOpportunityController::class, 'destroy'])->name('investments.destroy');
    Route::post('/inversiones/{investment}/inversor/{userId}/aprobar', [\App\Http\Controllers\SuperAdmin\InvestmentOpportunityController::class, 'approveInvestor'])->name('investments.approve-investor');
    Route::post('/inversiones/{investment}/inversor/{userId}/rechazar', [\App\Http\Controllers\SuperAdmin\InvestmentOpportunityController::class, 'rejectInvestor'])->name('investments.reject-investor');
    Route::post('/inversiones/{investment}/inversor/{userId}/desaprobar', [\App\Http\Controllers\SuperAdmin\InvestmentOpportunityController::class, 'disapproveInvestor'])->name('investments.disapprove-investor');
    Route::put('/inversiones/{investment}/inversor/{userId}/revisar-comprobante', [\App\Http\Controllers\SuperAdmin\InvestmentOpportunityController::class, 'reviewVoucher'])->name('investments.review-voucher');
    Route::post('/inversiones/{investment}/inversor/{userId}/desaprobar-pago', [\App\Http\Controllers\SuperAdmin\InvestmentOpportunityController::class, 'disapprovePayment'])->name('investments.disapprove-payment');
    Route::get('/inversiones/pagos', [\App\Http\Controllers\SuperAdmin\InvestmentPaymentController::class, 'index'])->name('investments.payments');
    Route::post('/inversiones/{investment}/inversor/{userId}/confirmar-pago', [\App\Http\Controllers\SuperAdmin\InvestmentPaymentController::class, 'confirm'])->name('investments.confirm-payment');
    Route::post('/inversiones/{investment}/inversor/{userId}/rechazar-pago', [\App\Http\Controllers\SuperAdmin\InvestmentPaymentController::class, 'reject'])->name('investments.reject-payment');
    Route::get('/pagos-inversores', [\App\Http\Controllers\SuperAdmin\InvestmentPaymentController::class, 'payouts'])->name('investments.payouts');
    Route::get('/inversiones/pagos/detalle/{opportunity}/{investor}', [\App\Http\Controllers\SuperAdmin\InvestmentPaymentController::class, 'scheduleDetail'])->name('investments.schedule-detail');
    Route::post('/inversiones/pagos/registrar', [\App\Http\Controllers\SuperAdmin\InvestmentPaymentController::class, 'storePayout'])->name('investments.payouts.store');
    Route::post('/inversiones/pagos/{payout}/confirmar', [\App\Http\Controllers\SuperAdmin\InvestmentPaymentController::class, 'confirmPayout'])->name('investments.payouts.confirm');
    Route::post('/inversiones/pagos/{payout}/rechazar', [\App\Http\Controllers\SuperAdmin\InvestmentPaymentController::class, 'rejectPayout'])->name('investments.payouts.reject');

    // Control de préstamos
    Route::get('/prestamos', [\App\Http\Controllers\SuperAdmin\LoanController::class, 'index'])->name('loans.index');
    Route::post('/prestamos/{loan}/counter-offer', [\App\Http\Controllers\SuperAdmin\LoanController::class, 'counterOffer'])->name('loans.counter-offer');
    Route::post('/prestamos/{loan}/approve', [\App\Http\Controllers\SuperAdmin\LoanController::class, 'approve'])->name('loans.approve');
    Route::post('/prestamos/{loan}/reject', [\App\Http\Controllers\SuperAdmin\LoanController::class, 'reject'])->name('loans.reject');
    Route::post('/prestamos/{loan}/toggle-default', [\App\Http\Controllers\SuperAdmin\LoanController::class, 'toggleDefault'])->name('loans.toggle-default');

    // Gestión de pagos
    Route::get('/pagos', [\App\Http\Controllers\SuperAdmin\LoanController::class, 'installmentsIndex'])->name('installments.index');
    Route::get('/pagos/deudor/{user}', [\App\Http\Controllers\SuperAdmin\LoanController::class, 'debtorDetail'])->name('installments.debtor');
    Route::get('/pagos/prestamos/{user}', [\App\Http\Controllers\SuperAdmin\LoanController::class, 'userLoans'])->name('installments.user-loans');
    Route::get('/pagos/grupo/{loan}', [\App\Http\Controllers\SuperAdmin\LoanController::class, 'installmentsGroup'])->name('installments.group');
    Route::post('/pagos/mora/{loan}', [\App\Http\Controllers\SuperAdmin\LoanController::class, 'updateLateFee'])->name('installments.late-fee');
    Route::patch('/pagos/{installment}/numero-operacion', [\App\Http\Controllers\SuperAdmin\LoanController::class, 'updateOperationNumber'])->name('installments.update-operation');
    Route::post('/pagos/{installment}/approve', [\App\Http\Controllers\SuperAdmin\LoanController::class, 'approveInstallment'])->name('installments.approve');
    Route::post('/pagos/{installment}/reject', [\App\Http\Controllers\SuperAdmin\LoanController::class, 'rejectInstallment'])->name('installments.reject');
    Route::post('/pagos/{installment}/unapprove', [\App\Http\Controllers\SuperAdmin\LoanController::class, 'unapproveInstallment'])->name('installments.unapprove');
    Route::post('/pagos/comprobantes/vista-previa', [\App\Http\Controllers\SuperAdmin\LoanController::class, 'previewVoucherCleanup'])->name('installments.vouchers.preview');
    Route::post('/pagos/comprobantes/limpiar', [\App\Http\Controllers\SuperAdmin\LoanController::class, 'cleanupVouchers'])->name('installments.vouchers.cleanup');

    // Gestión de cajas
    Route::get('/cajas', [\App\Http\Controllers\SuperAdmin\CashboxController::class, 'index'])->name('cashboxes.index');
    Route::get('/cajas/{cashbox}/historial', [\App\Http\Controllers\SuperAdmin\CashboxController::class, 'history'])->name('cashboxes.history');
    Route::get('/cajas/{cashbox}/all-movements', [\App\Http\Controllers\SuperAdmin\CashboxController::class, 'allMovements'])->name('cashboxes.all-movements');
    Route::post('/cajas', [\App\Http\Controllers\SuperAdmin\CashboxController::class, 'store'])->name('cashboxes.store');
    Route::put('/cajas/{cashbox}', [\App\Http\Controllers\SuperAdmin\CashboxController::class, 'update'])->name('cashboxes.update');
    Route::delete('/cajas/{cashbox}', [\App\Http\Controllers\SuperAdmin\CashboxController::class, 'destroy'])->name('cashboxes.destroy');
    Route::post('/cajas/{cashbox}/movimiento', [\App\Http\Controllers\SuperAdmin\CashboxController::class, 'addMovement'])->name('cashboxes.movement');
    Route::post('/cajas/desplazar', [\App\Http\Controllers\SuperAdmin\CashboxController::class, 'transfer'])->name('cashboxes.transfer');
    Route::delete('/cajas/movimiento/{movement}', [\App\Http\Controllers\SuperAdmin\CashboxController::class, 'deleteMovement'])->name('cashboxes.delete-movement');
    Route::resource('cajas-etiquetas', \App\Http\Controllers\SuperAdmin\CashboxTagController::class)->names('cashbox-tags')->except(['index', 'create', 'show', 'edit']);

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
    Route::post('/prestamos/solicitudes-cambio/descartar', [\App\Http\Controllers\Client\LoanController::class, 'dismissProfileChange'])->name('profile-changes.dismiss');
    Route::post('/prestamos/{loan}/aceptar-contraoferta', [\App\Http\Controllers\Client\LoanController::class, 'acceptCounterOffer'])->name('loans.accept-counter');
    Route::post('/prestamos/{loan}/rechazar-contraoferta', [\App\Http\Controllers\Client\LoanController::class, 'rejectCounterOffer'])->name('loans.reject-counter');
    Route::get('/referidos', [\App\Http\Controllers\Client\ReferralController::class, 'index'])->name('referidos');
    Route::get('/invertir', [\App\Http\Controllers\Client\DashboardController::class, 'invest'])->name('invertir');
    Route::get('/invertir/postular', [\App\Http\Controllers\Client\InvestorApplicationController::class, 'create'])->name('invest.apply');
    Route::post('/invertir/postular', [\App\Http\Controllers\Client\InvestorApplicationController::class, 'store'])->name('invest.store');
    Route::get('/invertir/control', [\App\Http\Controllers\Client\InvestmentController::class, 'index'])->name('investments.index');
    Route::get('/invertir/mis-inversiones', [\App\Http\Controllers\Client\InvestmentController::class, 'myInvestments'])->name('investments.my-investments');
    Route::get('/invertir/control/{opportunity}', [\App\Http\Controllers\Client\InvestmentController::class, 'show'])->name('investments.show');
    Route::post('/invertir/control/{opportunity}/aplicar', [\App\Http\Controllers\Client\InvestmentController::class, 'apply'])->name('investments.apply');
    Route::put('/invertir/control/{opportunity}/voucher/{userId}', [\App\Http\Controllers\Client\InvestmentController::class, 'uploadVoucher'])->name('investments.upload-voucher');
    Route::get('/invertir/mis-pagos', [\App\Http\Controllers\Client\InvestmentController::class, 'payments'])->name('investments.payments');
    Route::get('/invertir/control/{opportunity}/pagos', [\App\Http\Controllers\Client\InvestmentController::class, 'paymentDetail'])->name('investments.payment-detail');
    Route::post('/invertir/pagos/{payout}/aceptar', [\App\Http\Controllers\Client\InvestmentController::class, 'acceptPayout'])->name('investments.payouts.accept');
    Route::post('/invertir/pagos/{payout}/rechazar', [\App\Http\Controllers\Client\InvestmentController::class, 'rejectPayout'])->name('investments.payouts.reject');

    // Historial crediticio
    Route::get('/historial-crediticio', [\App\Http\Controllers\Client\LoanController::class, 'history'])->name('loans.history');
    Route::get('/historial-crediticio/{loan}', [\App\Http\Controllers\Client\LoanController::class, 'historyShow'])->name('loans.history.show');
    Route::post('/historial-crediticio/pagar/{installment}', [\App\Http\Controllers\Client\LoanController::class, 'uploadVoucher'])->name('loans.history.upload');

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
