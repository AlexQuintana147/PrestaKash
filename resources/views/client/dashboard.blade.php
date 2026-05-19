@extends('client.layout')

@section('title', 'Inicio')

@section('content')
@php
    $selfie = $user->documents?->selfie;
    $profilePhoto = $selfie ? Storage::url($selfie) : ($user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=E7EDF6&color=476788');
    $firstName = $user->person_type === 'juridica' ? ($user->business_name ?? $user->name) : ($user->first_name ?? $user->name);
    $greeting = now()->hour < 12 ? 'Buenos días' : (now()->hour < 18 ? 'Buenas tardes' : 'Buenas noches');
    $hasActiveLoans = $activeLoans->count() > 0;
    $hasPendingLoans = $pendingLoans->count() > 0;
@endphp

<div class="min-h-screen bg-[#F8F9FB] dark:bg-gray-950">

    @if($rejectedChange)
    <div id="rejectedNotice" class="px-4 md:px-8 pt-4">
        <div class="max-w-4xl mx-auto flex items-start gap-3 bg-white dark:bg-gray-900 border border-red-200 dark:border-red-800/60 rounded-xl px-4 py-3">
            <div class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-[#0B3558] dark:text-white">Solicitud de cambio rechazada</p>
                @if($rejectedChange->rejection_reason)
                    <p class="text-xs text-[#476788] dark:text-gray-400 mt-0.5">{{ $rejectedChange->rejection_reason }}</p>
                @endif
            </div>
            <button onclick="dismissRejected()" class="p-1 text-[#A6BBD1] dark:text-gray-600 hover:text-[#0B3558] dark:hover:text-white flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
    @endif

    @if($pendingChange)
    <div class="px-4 md:px-8 {{ $rejectedChange ? 'pt-2' : 'pt-4' }}">
        <div class="max-w-4xl mx-auto flex items-center gap-3 bg-white dark:bg-gray-900 border border-amber-200 dark:border-amber-800/60 rounded-xl px-4 py-3">
            <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-sm text-[#0B3558] dark:text-white">Cambios de perfil pendientes de aprobación</p>
        </div>
    </div>
    @endif

    @if($hasPendingLoans)
    <div class="px-4 md:px-8 {{ $rejectedChange || $pendingChange ? 'pt-2' : 'pt-4' }}">
        <div class="max-w-4xl mx-auto flex items-center gap-3 bg-white dark:bg-gray-900 border border-amber-200 dark:border-amber-800/60 rounded-xl px-4 py-3">
            <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-sm text-[#0B3558] dark:text-white">{{ $pendingLoans->count() }} solicitud(es) de préstamo en revisión</p>
        </div>
    </div>
    @endif

    <div class="max-w-4xl mx-auto px-4 md:px-8 pt-8 pb-4">
        <div class="flex items-center gap-4">
            <img src="{{ $profilePhoto }}" alt="{{ $user->name }}" class="w-12 h-12 md:w-14 md:h-14 rounded-xl object-cover flex-shrink-0">
            <div class="min-w-0">
                <p class="text-sm text-[#476788] dark:text-gray-400">{{ $greeting }}</p>
                <h1 class="text-xl md:text-2xl font-bold text-[#0B3558] dark:text-white tracking-tight truncate">{{ $firstName }}</h1>
            </div>
        </div>
    </div>

    @if($hasActiveLoans)
    <div class="max-w-4xl mx-auto px-4 md:px-8 pb-6">
        <div class="bg-[#0B3558] dark:bg-white rounded-2xl p-5 md:p-7">
            <div class="flex items-center justify-between mb-5">
                <p class="text-xs text-[#A6BBD1] dark:text-[#476788] uppercase tracking-widest font-semibold">Mis Préstamos</p>
                <a href="{{ route('client.loans') }}" class="text-xs font-semibold text-[#006BFF] hover:text-white dark:hover:text-[#0B3558] transition-colors">Ver todo</a>
            </div>
            <div class="grid grid-cols-3 gap-4 md:gap-8">
                <div>
                    <p class="text-[10px] md:text-xs text-[#476788] dark:text-[#A6BBD1] uppercase tracking-wider font-medium mb-1">Activos</p>
                    <p class="text-2xl md:text-4xl font-bold text-white dark:text-[#0B3558] tracking-tight leading-none">{{ $activeLoans->count() }}</p>
                </div>
                <div>
                    <p class="text-[10px] md:text-xs text-[#476788] dark:text-[#A6BBD1] uppercase tracking-wider font-medium mb-1">Deuda Total</p>
                    <p class="text-lg md:text-2xl font-bold text-white dark:text-[#0B3558] tracking-tight leading-none">S/ {{ number_format($totalDebt, 0) }}</p>
                </div>
                <div>
                    <p class="text-[10px] md:text-xs text-[#476788] dark:text-[#A6BBD1] uppercase tracking-wider font-medium mb-1">Cuota/Mes</p>
                    <p class="text-lg md:text-2xl font-bold text-[#006BFF] tracking-tight leading-none">S/ {{ number_format($monthlyPayment, 0) }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="max-w-4xl mx-auto px-4 md:px-8 pb-6">
        <p class="text-xs text-[#476788] dark:text-gray-400 uppercase tracking-widest font-semibold mb-3">Acciones</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <a href="{{ $hasProfile ? route('client.loans.create') : route('client.loans.profile.edit') }}" class="flex items-center gap-4 bg-white dark:bg-gray-900 border border-[#E7EDF6] dark:border-gray-800 rounded-xl px-5 py-4 hover:border-[#006BFF] dark:hover:border-[#006BFF] transition-colors">
                <div class="w-10 h-10 rounded-lg bg-[#006BFF]/8 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-[#006BFF]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-[#0B3558] dark:text-white">Solicitar Préstamo</p>
                    <p class="text-xs text-[#476788] dark:text-gray-400 mt-0.5">Desembolso en 24-48h · Tasas desde 5%</p>
                </div>
                <svg class="w-4 h-4 text-[#A6BBD1] dark:text-gray-600 flex-shrink-0 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>

            <a href="{{ $isInvestor ? route('client.investments.index') : ($hasInvestorApp ? route('client.invertir') : route('client.invest.apply')) }}" class="flex items-center gap-4 bg-white dark:bg-gray-900 border border-[#E7EDF6] dark:border-gray-800 rounded-xl px-5 py-4 hover:border-[#8247f5] dark:hover:border-[#8247f5] transition-colors">
                <div class="w-10 h-10 rounded-lg bg-[#8247f5]/8 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-[#8247f5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-[#0B3558] dark:text-white">{{ $isInvestor ? 'Mis Inversiones' : 'Invertir' }}</p>
                    <p class="text-xs text-[#476788] dark:text-gray-400 mt-0.5">{{ $isInvestor ? 'Revisa rendimientos y oportunidades' : '2% retorno mensual · Capital protegido' }}</p>
                </div>
                <svg class="w-4 h-4 text-[#A6BBD1] dark:text-gray-600 flex-shrink-0 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 md:px-8 pb-6">
        <p class="text-xs text-[#476788] dark:text-gray-400 uppercase tracking-widest font-semibold mb-3">Acceso rápido</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-3">
            <a href="{{ route('client.loans') }}" class="flex items-center gap-3 bg-white dark:bg-gray-900 border border-[#E7EDF6] dark:border-gray-800 rounded-xl px-4 py-3.5 hover:border-[#006BFF] dark:hover:border-[#006BFF] transition-colors">
                <svg class="w-4 h-4 text-[#476788] dark:text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span class="text-xs font-medium text-[#0B3558] dark:text-white truncate">Préstamos</span>
            </a>
            <button type="button" onclick="openBankModal()" class="flex items-center gap-3 bg-white dark:bg-gray-900 border border-[#E7EDF6] dark:border-gray-800 rounded-xl px-4 py-3.5 hover:border-[#006BFF] dark:hover:border-[#006BFF] transition-colors text-left">
                <svg class="w-4 h-4 text-[#476788] dark:text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <span class="text-xs font-medium text-[#0B3558] dark:text-white truncate">Cuentas</span>
            </button>
            <a href="{{ route('client.referidos') }}" class="flex items-center gap-3 bg-white dark:bg-gray-900 border border-[#E7EDF6] dark:border-gray-800 rounded-xl px-4 py-3.5 hover:border-[#006BFF] dark:hover:border-[#006BFF] transition-colors">
                <svg class="w-4 h-4 text-[#476788] dark:text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="text-xs font-medium text-[#0B3558] dark:text-white truncate">Referidos</span>
            </a>
            <a href="{{ route('client.loans.profile.edit') }}" class="flex items-center gap-3 bg-white dark:bg-gray-900 border border-[#E7EDF6] dark:border-gray-800 rounded-xl px-4 py-3.5 hover:border-[#006BFF] dark:hover:border-[#006BFF] transition-colors">
                <svg class="w-4 h-4 text-[#476788] dark:text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="text-xs font-medium text-[#0B3558] dark:text-white truncate">Perfil</span>
            </a>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 md:px-8 pb-6">
        <div class="bg-white dark:bg-gray-900 border border-[#E7EDF6] dark:border-gray-800 rounded-xl overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E7EDF6] dark:border-gray-800">
                <p class="text-sm font-semibold text-[#0B3558] dark:text-white">Información personal</p>
                <a href="{{ route('client.loans.profile.edit') }}" class="text-xs font-semibold text-[#006BFF] hover:text-[#0B3558] dark:hover:text-white transition-colors">Editar</a>
            </div>
            <div class="divide-y divide-[#E7EDF6] dark:divide-gray-800">
                @php
                    $infoItems = [
                        ($user->person_type === 'juridica' ? 'Razón Social' : 'Nombre') => $user->person_type === 'juridica' ? ($user->business_name ?? $user->name) : trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
                        'DNI' => $user->dni,
                        'Teléfono' => $user->phone,
                        'Correo' => $user->email,
                    ];
                    if ($user->person_type === 'juridica') { $infoItems['RUC'] = $user->ruc; }
                @endphp
                @foreach($infoItems as $label => $value)
                <div class="flex items-center justify-between px-5 py-3">
                    <span class="text-xs text-[#476788] dark:text-gray-400">{{ $label }}</span>
                    <span class="text-sm font-medium text-[#0B3558] dark:text-white truncate max-w-[220px]">{{ $value ?? '—' }}</span>
                </div>
                @endforeach
            </div>
            <div class="px-5 py-3 border-t border-[#E7EDF6] dark:border-gray-800 flex items-center gap-2 text-xs text-[#A6BBD1] dark:text-gray-500">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                Verificado · Miembro desde {{ $user->created_at->format('M Y') }}
            </div>
        </div>
    </div>

    @if(!$hasActiveLoans && !$isInvestor)
    <div class="max-w-4xl mx-auto px-4 md:px-8 pb-8">
        <div class="bg-white dark:bg-gray-900 border border-[#E7EDF6] dark:border-gray-800 rounded-xl p-5 md:p-6">
            <p class="text-sm font-semibold text-[#0B3558] dark:text-white mb-4">¿Por qué PrestaKash?</p>
            <div class="space-y-4">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[#006BFF]/8 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-[#006BFF]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-[#0B3558] dark:text-white">Rápido y Simple</p>
                        <p class="text-xs text-[#476788] dark:text-gray-400 mt-0.5">Solicita en minutos, sin papeleos.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[#8247f5]/8 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-[#8247f5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-[#0B3558] dark:text-white">Seguro y Transparente</p>
                        <p class="text-xs text-[#476788] dark:text-gray-400 mt-0.5">Sin letras pequeñas. Sabes cuánto pagas.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[#ffa600]/8 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-[#ffa600]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-[#0B3558] dark:text-white">Doble Oportunidad</p>
                        <p class="text-xs text-[#476788] dark:text-gray-400 mt-0.5">Pide un préstamo o invierte. Gana desde ambos lados.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<div id="bankModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-end md:items-center justify-center min-h-screen p-0 md:p-4">
        <div onclick="closeBankModal()" class="fixed inset-0 bg-[#0B3558]/30 backdrop-blur-sm"></div>
        <div class="relative z-10 bg-white dark:bg-gray-950 w-full md:max-w-lg md:rounded-2xl rounded-t-2xl text-left overflow-hidden max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E7EDF6] dark:border-gray-800 flex-shrink-0">
                <div>
                    <h3 class="text-base font-bold text-[#0B3558] dark:text-white">Cuentas Bancarias</h3>
                    <p class="text-xs text-[#A6BBD1] dark:text-gray-500 mt-0.5">Donde recibirás tus préstamos</p>
                </div>
                <button type="button" onclick="closeBankModal()" class="p-1 text-[#A6BBD1] dark:text-gray-600 hover:text-[#0B3558] dark:hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto px-5 py-4">
                <div id="bankAccountsList" class="space-y-3">
                    <div class="animate-pulse space-y-3">
                        <div class="h-16 bg-[#F8F9FB] dark:bg-gray-900 rounded-xl"></div>
                        <div class="h-16 bg-[#F8F9FB] dark:bg-gray-900 rounded-xl"></div>
                    </div>
                </div>
                <div id="bankAccountForm" class="hidden pt-5 mt-5 border-t border-[#E7EDF6] dark:border-gray-800">
                    <h4 id="formTitle" class="text-xs font-semibold text-[#0B3558] dark:text-white uppercase tracking-widest mb-4">Nueva Cuenta</h4>
                    <form id="actualForm" onsubmit="saveBankAccount(event)" class="space-y-4">
                        <input type="hidden" id="editingId" value="">
                        <div>
                            <label class="block text-xs font-medium text-[#476788] dark:text-gray-400 mb-1">Alias</label>
                            <input type="text" id="f_alias" placeholder="Ej: Mi cuenta BCP" class="w-full bg-[#F8F9FB] dark:bg-gray-900 border border-[#E7EDF6] dark:border-gray-800 rounded-lg text-sm px-3 py-2.5 focus:outline-none focus:border-[#006BFF] dark:focus:border-[#006BFF] transition-colors text-[#0B3558] dark:text-white placeholder-[#A6BBD1] dark:placeholder-gray-600">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-[#476788] dark:text-gray-400 mb-1">Banco</label>
                                <select id="f_bank_name" required class="w-full bg-[#F8F9FB] dark:bg-gray-900 border border-[#E7EDF6] dark:border-gray-800 rounded-lg text-sm px-3 py-2.5 focus:outline-none focus:border-[#006BFF] dark:focus:border-[#006BFF] transition-colors text-[#0B3558] dark:text-white">
                                    <option value="" class="dark:bg-gray-950">Seleccionar</option>
                                    @foreach(['BCP', 'Interbank', 'BBVA', 'Scotiabank', 'BanBif', 'Banco de la Nación', 'Caja Piura', 'Caja Huancayo', 'Caja Arequipa'] as $bank)
                                        <option value="{{ $bank }}" class="dark:bg-gray-950">{{ $bank }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-[#476788] dark:text-gray-400 mb-1">Tipo</label>
                                <select id="f_account_type" required class="w-full bg-[#F8F9FB] dark:bg-gray-900 border border-[#E7EDF6] dark:border-gray-800 rounded-lg text-sm px-3 py-2.5 focus:outline-none focus:border-[#006BFF] dark:focus:border-[#006BFF] transition-colors text-[#0B3558] dark:text-white">
                                    <option value="ahorros" class="dark:bg-gray-950">Ahorros</option>
                                    <option value="corriente" class="dark:bg-gray-950">Corriente</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-[#476788] dark:text-gray-400 mb-1">Moneda</label>
                                <select id="f_currency" required class="w-full bg-[#F8F9FB] dark:bg-gray-900 border border-[#E7EDF6] dark:border-gray-800 rounded-lg text-sm px-3 py-2.5 focus:outline-none focus:border-[#006BFF] dark:focus:border-[#006BFF] transition-colors text-[#0B3558] dark:text-white">
                                    <option value="PEN" class="dark:bg-gray-950">Soles (S/)</option>
                                    <option value="USD" class="dark:bg-gray-950">Dólares ($)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-[#476788] dark:text-gray-400 mb-1">Nro. Cuenta</label>
                                <input type="text" id="f_account_number" required placeholder="19134567890123" maxlength="14" oninput="this.value=this.value.replace(/[^0-9]/g,'')" class="w-full bg-[#F8F9FB] dark:bg-gray-900 border border-[#E7EDF6] dark:border-gray-800 rounded-lg text-sm px-3 py-2.5 focus:outline-none focus:border-[#006BFF] dark:focus:border-[#006BFF] transition-colors tabular-nums text-[#0B3558] dark:text-white placeholder-[#A6BBD1] dark:placeholder-gray-600">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-[#476788] dark:text-gray-400 mb-1">CCI</label>
                            <input type="text" id="f_cci" required placeholder="00219100123456789012" maxlength="20" oninput="this.value=this.value.replace(/[^0-9]/g,'')" class="w-full bg-[#F8F9FB] dark:bg-gray-900 border border-[#E7EDF6] dark:border-gray-800 rounded-lg text-sm px-3 py-2.5 focus:outline-none focus:border-[#006BFF] dark:focus:border-[#006BFF] transition-colors tabular-nums text-[#0B3558] dark:text-white placeholder-[#A6BBD1] dark:placeholder-gray-600">
                        </div>
                        <div class="flex gap-2 pt-2">
                            <button type="submit" class="flex-1 py-2.5 rounded-lg bg-[#006BFF] text-white text-sm font-semibold hover:bg-[#0B3558] transition-colors">Guardar</button>
                            <button type="button" onclick="hideForm()" class="px-5 py-2.5 rounded-lg bg-[#E7EDF6] dark:bg-gray-800 text-[#476788] dark:text-gray-400 text-sm font-medium hover:bg-[#D4E0ED] dark:hover:bg-gray-700 transition-colors">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div id="addBtnContainer" class="px-5 py-4 border-t border-[#E7EDF6] dark:border-gray-800 flex-shrink-0">
                <button type="button" onclick="showForm()" class="w-full py-2.5 rounded-lg border border-dashed border-[#D4E0ED] dark:border-gray-700 text-[#476788] dark:text-gray-400 text-sm font-medium hover:border-[#006BFF] hover:text-[#006BFF] dark:hover:border-[#006BFF] dark:hover:text-[#006BFF] transition-colors flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Agregar cuenta
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let accounts = [];

    async function openBankModal() {
        document.getElementById('bankModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        await loadAccounts();
    }

    function closeBankModal() {
        document.getElementById('bankModal').classList.add('hidden');
        document.body.style.overflow = '';
        hideForm();
    }

    async function loadAccounts() {
        const list = document.getElementById('bankAccountsList');
        try {
            const response = await fetch('{{ route("client.bank-accounts.index") }}');
            accounts = await response.json();
            if (accounts.length === 0) {
                list.innerHTML = '<div class="py-8 text-center"><p class="text-sm text-[#A6BBD1] dark:text-gray-500">No tienes cuentas registradas</p></div>';
                return;
            }
            list.innerHTML = accounts.map(acc => `
                <div class="group flex items-center justify-between bg-[#F8F9FB] dark:bg-gray-900 rounded-xl px-4 py-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-[#0B3558] dark:text-white">${acc.alias || acc.bank_name}</span>
                            <span class="text-[10px] font-medium text-[#004EBA] dark:text-gray-500 bg-[#E7EDF6] dark:bg-gray-800 px-1.5 py-0.5 rounded">${acc.bank_name} · ${acc.account_type}</span>
                        </div>
                        <p class="text-xs text-[#476788] dark:text-gray-400 tabular-nums mt-0.5">${acc.account_number}${acc.cci ? ' · CCI: ' + acc.cci : ''}</p>
                    </div>
                    <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0 ml-2">
                        <button onclick="editAccount(${acc.id})" class="p-1.5 text-[#A6BBD1] hover:text-[#006BFF] dark:hover:text-[#006BFF] transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>
                        <button onclick="deleteAccount(${acc.id})" class="p-1.5 text-[#A6BBD1] hover:text-red-500 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            `).join('');
        } catch (e) {
            list.innerHTML = '<p class="text-red-500 text-sm">Error al cargar</p>';
        }
    }

    function showForm() {
        document.getElementById('bankAccountForm').classList.remove('hidden');
        document.getElementById('addBtnContainer').classList.add('hidden');
    }

    function hideForm() {
        document.getElementById('bankAccountForm').classList.add('hidden');
        document.getElementById('addBtnContainer').classList.remove('hidden');
        document.getElementById('actualForm').reset();
        document.getElementById('editingId').value = '';
        document.getElementById('formTitle').textContent = 'Nueva Cuenta';
    }

    function editAccount(id) {
        const acc = accounts.find(a => a.id === id);
        if (!acc) return;
        document.getElementById('editingId').value = acc.id;
        document.getElementById('f_alias').value = acc.alias || '';
        document.getElementById('f_bank_name').value = acc.bank_name;
        document.getElementById('f_account_type').value = acc.account_type;
        document.getElementById('f_currency').value = acc.currency || 'PEN';
        document.getElementById('f_account_number').value = acc.account_number;
        document.getElementById('f_cci').value = acc.cci || '';
        document.getElementById('formTitle').textContent = 'Editar Cuenta';
        showForm();
    }

    async function saveBankAccount(e) {
        e.preventDefault();
        const id = document.getElementById('editingId').value;
        const data = {
            alias: document.getElementById('f_alias').value,
            bank_name: document.getElementById('f_bank_name').value,
            account_type: document.getElementById('f_account_type').value,
            currency: document.getElementById('f_currency').value,
            account_number: document.getElementById('f_account_number').value,
            cci: document.getElementById('f_cci').value,
            _token: '{{ csrf_token() }}'
        };
        const url = id ? '/panel/cuentas-bancarias/' + id : '{{ route("client.bank-accounts.store") }}';
        const method = id ? 'PUT' : 'POST';
        try {
            const response = await fetch(url, { method, headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify(data) });
            const result = await response.json();
            if (result.success) { await loadAccounts(); hideForm(); }
            else { alert(result.message || 'Error al guardar'); }
        } catch (e) { alert('Error de conexión'); }
    }

    async function deleteAccount(id) {
        if (!confirm('¿Eliminar esta cuenta?')) return;
        try {
            const response = await fetch('/panel/cuentas-bancarias/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } });
            const result = await response.json();
            if (result.success) await loadAccounts();
        } catch (e) { alert('Error al eliminar'); }
    }

    function dismissRejected() {
        var notice = document.getElementById('rejectedNotice');
        if (!notice) return;
        var token = document.querySelector('input[name="_token"]');
        fetch('{{ route("client.profile-changes.dismiss") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token ? token.value : '' },
            body: JSON.stringify({})
        }).then(function () {
            notice.style.transition = 'opacity 0.2s';
            notice.style.opacity = '0';
            setTimeout(function () { notice.remove(); }, 200);
        });
    }
</script>
@endsection