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

<style>
    .pk-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        transition: border-color 0.2s ease;
    }
    .dark .pk-card {
        background: #111827;
        border-color: #1f2937;
    }
    .pk-card:hover { border-color: #d1d5db; }
    .dark .pk-card:hover { border-color: #374151; }

    .pk-card-cta {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        transition: all 0.2s ease;
    }
    .dark .pk-card-cta {
        background: #111827;
        border-color: #1f2937;
    }
    .pk-card-cta:hover { border-color: #006BFF; background: #f9fafb; }
    .dark .pk-card-cta:hover { background: #111827; border-color: #4A90E2; }
    .pk-card-cta.purple:hover { border-color: #8247f5; }
    .dark .pk-card-cta.purple:hover { border-color: #9B59B6; }

    .pk-status-dot { width: 6px; height: 6px; border-radius: 50%; display: inline-block; margin-right: 4px; }
    .pk-status-confirmed { background: #00D26A; }

    @keyframes pk-gold-shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    @keyframes pk-gold-pulse {
        0%, 100% { opacity: 0.6; }
        50% { opacity: 1; }
    }
    @keyframes pk-gold-float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-3px); }
    }
    .pk-loan-gold {
        background: linear-gradient(135deg, #FFFBEB 0%, #FEF3C7 50%, #FFFBEB 100%);
        border: 1.5px solid #F5A623;
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .dark .pk-loan-gold {
        background: linear-gradient(135deg, #1C1607 0%, #2D2209 50%, #1C1607 100%);
        border-color: #D4910E;
    }
    .pk-loan-gold:hover {
        border-color: #D4910E;
        box-shadow: 0 0 20px rgba(245, 166, 35, 0.15), 0 0 40px rgba(245, 166, 35, 0.05);
        transform: translateY(-2px);
    }
    .dark .pk-loan-gold:hover {
        border-color: #F5A623;
        box-shadow: 0 0 20px rgba(245, 166, 35, 0.1), 0 0 40px rgba(245, 166, 35, 0.03);
    }
    .pk-loan-gold::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent 0%, rgba(255, 215, 0, 0.15) 50%, transparent 100%);
        animation: pk-gold-shimmer 4s ease-in-out infinite;
        pointer-events: none;
    }
    .pk-loan-gold-icon {
        animation: pk-gold-float 3s ease-in-out infinite;
    }
    .pk-loan-gold-dot {
        animation: pk-gold-pulse 2s ease-in-out infinite;
    }

    .pk-input {
        appearance: none;
        -webkit-appearance: none;
    }
    .pk-input:focus {
        border-color: #006BFF;
    }
    .dark .pk-input:focus {
        border-color: #4A90E2;
    }
    .pk-input::placeholder {
        color: transparent;
    }
    .pk-input:focus::placeholder {
        color: #d1d5db;
    }
    .dark .pk-input::placeholder {
        color: transparent;
    }
    .dark .pk-input:focus::placeholder {
        color: #374151;
    }

    @media (max-width: 767px) {
        .pk-main-pb { padding-bottom: 0; }
    }

    .pk-modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 100;
        display: none;
        align-items: flex-end;
        justify-content: center;
    }
    .pk-modal-overlay.active { display: flex; }
    @media (min-width: 768px) {
        .pk-modal-overlay.active { align-items: center; padding: 1rem; }
    }
    .pk-modal-content {
        position: relative;
        z-index: 10;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 24px 24px 0 0;
        width: 100%;
        max-height: 85vh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    .dark .pk-modal-content {
        background: #111827;
        border-color: #1f2937;
    }
    @media (min-width: 768px) {
        .pk-modal-content {
            border-radius: 24px;
            max-width: 480px;
            max-height: 80vh;
        }
    }
</style>

<div class="min-h-screen bg-white dark:bg-gray-950 pk-main-pb"> 

    @if($rejectedChange)
    <div class="px-4 md:px-8 pt-4" id="rejectedNotice">
        <div class="max-w-4xl mx-auto pk-card px-4 py-3 flex items-start gap-3">
            <div class="w-8 h-8 rounded-xl bg-red-50 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-white">Solicitud rechazada</p>
                @if($rejectedChange->rejection_reason)
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">{{ $rejectedChange->rejection_reason }}</p>
                @endif
            </div>
            <button onclick="dismissRejected()" class="p-1 text-gray-400 dark:text-gray-600 hover:text-gray-900 dark:hover:text-white flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
    @endif

    @if($pendingChange)
    <div class="px-4 md:px-8 {{ $rejectedChange ? 'pt-2' : 'pt-4' }}">
        <div class="max-w-4xl mx-auto pk-card px-4 py-3 flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-sm text-gray-900 dark:text-white">Cambios de perfil pendientes de aprobación</p>
        </div>
    </div>
    @endif

    @if($hasPendingLoans)
    <div class="px-4 md:px-8 {{ $rejectedChange || $pendingChange ? 'pt-2' : 'pt-4' }}">
        <div class="max-w-4xl mx-auto pk-card px-4 py-3 flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-sm text-gray-900 dark:text-white">{{ $pendingLoans->count() }} solicitud(es) en revisión</p>
        </div>
    </div>
    @endif

    {{-- Header + Title --}}
    <div class="px-4 md:px-8 pt-6 pb-5">
        <div class="max-w-4xl mx-auto relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#006BFF]/5 via-transparent to-[#8247f5]/5 dark:from-[#006BFF]/10 dark:via-transparent dark:to-[#8247f5]/10 p-4 md:p-5">
            <div class="flex items-center justify-between relative z-10">
                <div class="flex items-center gap-4">
                    <div class="relative flex-shrink-0">
                        <div class="w-12 h-12 md:w-14 md:h-14 rounded-full p-[2.5px] bg-gradient-to-br from-[#006BFF] to-[#8247f5]">
                            <img src="{{ $profilePhoto }}" alt="{{ $user->name }}" class="w-full h-full rounded-full object-cover border-2 border-white dark:border-gray-950">
                        </div>
                        <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-[#00D26A] rounded-full border-2 border-white dark:border-gray-950"></span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider">{{ $greeting }}</p>
                        <p class="text-base md:text-lg font-bold text-gray-900 dark:text-white truncate mt-0.5">{{ $firstName }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <button type="button" onclick="openProfileModal()" title="Ver perfil" class="w-9 h-9 rounded-xl flex items-center justify-center text-gray-400 dark:text-gray-500 hover:text-[#006BFF] dark:hover:text-[#4A90E2] hover:bg-[#006BFF]/10 dark:hover:bg-[#4A90E2]/10 transition-colors duration-200">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </button>
                    <a href="{{ route('client.loans.profile.edit') }}" title="Editar perfil" class="w-9 h-9 rounded-xl flex items-center justify-center text-gray-400 dark:text-gray-500 hover:text-[#8247f5] dark:hover:text-[#9B59B6] hover:bg-[#8247f5]/10 dark:hover:bg-[#9B59B6]/10 transition-colors duration-200">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                </div>
            </div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-[#006BFF]/8 to-transparent dark:from-[#006BFF]/5 dark:to-transparent rounded-bl-full pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-[#8247f5]/6 to-transparent dark:from-[#8247f5]/4 dark:to-transparent rounded-tr-full pointer-events-none"></div>
        </div>
    </div>

    {{-- Línea de Crédito --}}
    <div class="px-4 md:px-8 pb-2">
        <div class="max-w-4xl mx-auto">
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#006BFF] to-[#8247f5] p-5 md:p-6">
                <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-bl-full pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 w-28 h-28 bg-white/5 rounded-tr-full pointer-events-none"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-5 h-5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <span class="text-xs font-medium text-white/70 uppercase tracking-wider">Línea de crédito disponible</span>
                    </div>
                    <p class="text-3xl md:text-4xl font-bold text-white tracking-tight">S/ {{ number_format($user->credit_line ?? 500, 0) }}<span class="text-lg font-medium text-white/60">.00</span></p>
                    <div class="mt-3 flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white/15 text-[10px] font-medium text-white/90">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#00D26A]"></span>
                            Activa
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bento Grid --}}
    <div class="px-4 md:px-8 pb-4">
        <div class="max-w-4xl mx-auto">
            <div class="grid grid-cols-2 gap-3">

                {{-- Card 1: Solicitar Préstamo (tall) --}}
                <a href="{{ $hasProfile ? route('client.loans.create') : route('client.loans.profile.edit') }}" class="pk-loan-gold row-span-2 p-4 flex flex-col justify-between min-h-[180px] md:min-h-[220px] relative">
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-6 h-6 text-[#D4910E] dark:text-[#F5A623]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-xs font-semibold text-[#D4910E] dark:text-[#F5A623] uppercase tracking-wider">Destacado</span>
                        </div>
                        <p class="text-base font-bold text-[#92400E] dark:text-[#FBBF24]">Solicitar<br>Préstamo</p>
                    </div>
                    <div class="relative z-10 pb-4">
                        <div class="flex items-center gap-1.5 mb-1">
                            <span class="pk-loan-gold-dot w-1.5 h-1.5 rounded-full bg-[#F5A623]"></span>
                            <p class="text-lg font-bold text-[#D4910E] dark:text-[#F5A623]">24-48h</p>
                        </div>
                        <p class="text-[10px] text-[#B45309]/70 dark:text-[#FBBF24]/50">Desembolso rápido</p>
                    </div>
                    <svg id="pkGoldWave" class="absolute bottom-0 left-0 right-0 w-full h-10" viewBox="0 0 100 30" preserveAspectRatio="none">
                        <defs><linearGradient id="goldWave" x1="0%" y1="0%" x2="0%" y2="100%"><stop offset="0%" style="stop-color:#00A854;stop-opacity:0.25"/><stop offset="100%" style="stop-color:#00A854;stop-opacity:0"/></linearGradient></defs>
                        <path id="pkGoldWaveFill" d="M0,26 Q10,22 20,24 T40,18 T60,21 T80,12 T100,10 L100,30 L0,30Z" fill="url(#goldWave)"/>
                        <path id="pkGoldWaveLine" d="M0,26 Q10,22 20,24 T40,18 T60,21 T80,12 T100,10" fill="none" stroke="#00A854" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" opacity="0.5"/>
                    </svg>
                </a>

                {{-- Card 2: Invertir --}}
                <a href="{{ $isInvestor ? route('client.investments.index') : ($hasInvestorApp ? route('client.invertir') : route('client.invest.apply')) }}" class="pk-card-cta purple p-4 flex flex-col justify-between min-h-[100px]">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#8247f5] dark:text-[#9B59B6]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ $isInvestor ? 'Activo' : 'Nuevo' }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $isInvestor ? 'Mis Inversiones' : 'Invertir' }}</p>
                        <p class="text-lg font-bold text-[#8247f5] dark:text-[#9B59B6] mt-0.5">Hasta un 2%</p>
                        <p class="text-[10px] text-gray-400 dark:text-gray-500">mensual</p>
                    </div>
                </a>

                {{-- Card 3: Mis Préstamos --}}
                <a href="{{ route('client.loans') }}" class="pk-card p-4 flex flex-col justify-between min-h-[100px]">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#00A854] dark:text-[#00D26A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Activos</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Mis Préstamos</p>
                        <p class="text-lg font-bold text-[#00A854] dark:text-[#00D26A] mt-0.5">{{ $activeLoans->count() }}</p>
                    </div>
                </a>

                @if($hasActiveLoans)
                {{-- Card 4: Resumen (wide) --}}
                <div class="pk-card col-span-2 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#E94560]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Resumen</span>
                        </div>
                        <a href="{{ route('client.loans') }}" class="text-xs text-[#006BFF] dark:text-[#4A90E2] hover:underline">Ver todo</a>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Deuda</p>
                            <p class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">S/ {{ number_format($totalDebt, 0) }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Cuota/Mes</p>
                            <p class="text-lg md:text-xl font-bold text-[#006BFF] dark:text-[#4A90E2]">S/ {{ number_format($monthlyPayment, 0) }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Estado</p>
                            <p class="text-sm font-medium text-[#00A854] dark:text-[#00D26A] flex items-center"><span class="pk-status-dot pk-status-confirmed"></span>Al día</p>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Card 5: Cuentas Banco --}}
                <button type="button" onclick="openBankModal()" class="pk-card p-4 flex flex-col justify-between min-h-[100px] text-left">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#D4910E] dark:text-[#F5A623]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Bancos</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Cuentas</p>
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">Administrar</p>
                    </div>
                </button>

                {{-- Card 6: Referidos --}}
                <a href="{{ route('client.referidos') }}" class="pk-card p-4 flex flex-col justify-between min-h-[100px]">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#006BFF] dark:text-[#4A90E2]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Red</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Referidos</p>
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">Gana comisiones</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    @if(!$hasActiveLoans && !$isInvestor)
    <div class="px-4 md:px-8 pb-6">
        <div class="max-w-4xl mx-auto">
            <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-widest font-semibold mb-3">¿Por qué PrestaKash?</p>
            <div class="space-y-2">
                <div class="pk-card p-3 flex items-center gap-3">
                    <svg class="w-5 h-5 text-[#006BFF] dark:text-[#4A90E2] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Rápido y Simple</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Solicita en minutos, sin papeleos</p>
                    </div>
                </div>
                <div class="pk-card p-3 flex items-center gap-3">
                    <svg class="w-5 h-5 text-[#8247f5] dark:text-[#9B59B6] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Seguro y Transparente</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Sin letras pequeñas</p>
                    </div>
                </div>
                <div class="pk-card p-3 flex items-center gap-3">
                    <svg class="w-5 h-5 text-[#D4910E] dark:text-[#F5A623] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Doble Oportunidad</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Préstamo o inversión</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>

{{-- Profile Modal --}}
<div id="profileModal" class="pk-modal-overlay" role="dialog" aria-modal="true">
    <div onclick="closeProfileModal()" class="absolute inset-0 bg-black/60"></div>
    <div class="pk-modal-content">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-800 flex-shrink-0">
            <div class="flex items-center gap-3">
                <img src="{{ $profilePhoto }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover border-2 border-gray-200 dark:border-gray-800">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ $user->person_type === 'juridica' ? ($user->business_name ?? $user->name) : trim(($user->first_name ?? '').' '.($user->last_name ?? '')) }}</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $user->email }}</p>
                </div>
            </div>
            <button type="button" onclick="closeProfileModal()" class="p-1 text-gray-400 dark:text-gray-600 hover:text-gray-900 dark:hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4">
            @php
                $profile = $user->clientProfile;
            @endphp

            {{-- Datos Personales --}}
            <div>
                <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest font-semibold mb-2">Datos Personales</p>
                <div class="space-y-2">
                    <div class="pk-card px-4 py-2.5 flex items-center justify-between">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Tipo de persona</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->person_type === 'juridica' ? 'Jurídica' : 'Natural' }}</span>
                    </div>
                    @if($user->person_type === 'juridica')
                        <div class="pk-card px-4 py-2.5 flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">RUC</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->ruc ?? '—' }}</span>
                        </div>
                        <div class="pk-card px-4 py-2.5 flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Razón Social</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->business_name ?? '—' }}</span>
                        </div>
                    @else
                        <div class="pk-card px-4 py-2.5 flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Nombre</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->first_name ?? '—' }}</span>
                        </div>
                        <div class="pk-card px-4 py-2.5 flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Apellido</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->last_name ?? '—' }}</span>
                        </div>
                    @endif
                    <div class="pk-card px-4 py-2.5 flex items-center justify-between">
                        <span class="text-xs text-gray-500 dark:text-gray-400">DNI</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->dni ?? '—' }}</span>
                    </div>
                    <div class="pk-card px-4 py-2.5 flex items-center justify-between">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Teléfono</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->phone ?? '—' }}</span>
                    </div>
                    <div class="pk-card px-4 py-2.5 flex items-center justify-between">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Dirección</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white text-right max-w-[60%]">{{ $user->address ?? '—' }}</span>
                    </div>
                    <div class="pk-card px-4 py-2.5 flex items-center justify-between">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Ubicación</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white text-right max-w-[60%]">{{ trim(collect([$user->district, $user->province, $user->department])->filter()->join(', ')) ?: '—' }}</span>
                    </div>
                </div>
            </div>

            @if($profile)
            {{-- Información Adicional --}}
            <div>
                <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest font-semibold mb-2">Información Adicional</p>
                <div class="space-y-2">
                    @if($profile->institution)
                    <div class="pk-card px-4 py-2.5 flex items-center justify-between">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Institución</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $profile->institution }}</span>
                    </div>
                    @endif
                    @if($profile->career)
                    <div class="pk-card px-4 py-2.5 flex items-center justify-between">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Carrera</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $profile->career }}</span>
                    </div>
                    @endif
                    @if($profile->academic_status)
                    <div class="pk-card px-4 py-2.5 flex items-center justify-between">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Estado académico</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ \App\Models\ProfileChangeRequest::formatValue('academic_status', $profile->academic_status) }}</span>
                    </div>
                    @endif
                    <div class="pk-card px-4 py-2.5 flex items-center justify-between">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Estado civil</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $profile->marital_status ? \App\Models\ProfileChangeRequest::formatValue('marital_status', $profile->marital_status) : '—' }}</span>
                    </div>
                    <div class="pk-card px-4 py-2.5 flex items-center justify-between">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Hijos</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $profile->has_children ? 'Sí (' . ($profile->children_count ?? 0) . ')' : 'No' }}</span>
                    </div>
                    @if($profile->instagram)
                    <div class="pk-card px-4 py-2.5 flex items-center justify-between">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Instagram</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">@{{ $profile->instagram }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Información Laboral --}}
            <div>
                <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest font-semibold mb-2">Información Laboral</p>
                <div class="space-y-2">
                    <div class="pk-card px-4 py-2.5 flex items-center justify-between">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Empleado</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $profile->is_employed ? 'Sí' : 'No' }}</span>
                    </div>
                    @if($profile->is_employed)
                        @if($profile->employment_type)
                        <div class="pk-card px-4 py-2.5 flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Tipo de empleo</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ \App\Models\ProfileChangeRequest::formatValue('employment_type', $profile->employment_type) }}</span>
                        </div>
                        @endif
                        @if($profile->workplace)
                        <div class="pk-card px-4 py-2.5 flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Lugar de trabajo</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white text-right max-w-[60%]">{{ $profile->workplace }}</span>
                        </div>
                        @endif
                        @if($profile->job_title)
                        <div class="pk-card px-4 py-2.5 flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Cargo</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $profile->job_title }}</span>
                        </div>
                        @endif
                    @else
                        @if($profile->independent_type)
                        <div class="pk-card px-4 py-2.5 flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Tipo independiente</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ \App\Models\ProfileChangeRequest::formatValue('independent_type', $profile->independent_type) }}</span>
                        </div>
                        @endif
                    @endif
                    @if($profile->monthly_income)
                    <div class="pk-card px-4 py-2.5 flex items-center justify-between">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Ingreso mensual</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ \App\Models\ProfileChangeRequest::formatValue('monthly_income', $profile->monthly_income) }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Cónyuge --}}
            @if($profile->marital_status && in_array($profile->marital_status, ['casado_separado', 'casado_compartido']))
            <div>
                <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest font-semibold mb-2">Cónyuge</p>
                <div class="space-y-2">
                    @if($profile->spouse_first_name)
                    <div class="pk-card px-4 py-2.5 flex items-center justify-between">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Nombre</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $profile->spouse_first_name }} {{ $profile->spouse_last_name }}</span>
                    </div>
                    @endif
                    @if($profile->spouse_dni)
                    <div class="pk-card px-4 py-2.5 flex items-center justify-between">
                        <span class="text-xs text-gray-500 dark:text-gray-400">DNI</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $profile->spouse_dni }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif
            @endif

            {{-- Código de Referido --}}
            <div>
                <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest font-semibold mb-2">Referido</p>
                <div class="pk-card px-4 py-2.5 flex items-center justify-between">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Mi código</span>
                    <span class="text-sm font-bold text-[#006BFF] dark:text-[#4A90E2]">{{ $user->referral_code ?? '—' }}</span>
                </div>
            </div>
        </div>
        <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-800 flex-shrink-0">
            <a href="{{ route('client.loans.profile.edit') }}" class="w-full py-3 rounded-xl bg-[#006BFF] dark:bg-[#4A90E2] text-white text-sm font-semibold hover:bg-[#0052CC] dark:hover:bg-[#3A7BC8] transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                Editar perfil
            </a>
        </div>
    </div>
</div>

{{-- Bank Modal --}}
<div id="bankModal" class="pk-modal-overlay" role="dialog" aria-modal="true">
    <div onclick="closeBankModal()" class="absolute inset-0 bg-black/60"></div>
    <div class="pk-modal-content">
        <div class="px-6 pt-6 pb-4 flex-shrink-0">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-[#D4910E]/10 dark:bg-[#F5A623]/10 flex items-center justify-center">
                        <svg class="w-4.5 h-4.5 text-[#D4910E] dark:text-[#F5A623]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Cuentas Bancarias</h3>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">Donde recibirás tus desembolsos</p>
                    </div>
                </div>
                <button type="button" onclick="closeBankModal()" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-300 dark:text-gray-600 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto px-6 pb-4">
            <div id="bankAccountsList" class="space-y-2">
                <div class="animate-pulse space-y-2">
                    <div class="h-16 bg-gray-50 dark:bg-gray-900 rounded-xl"></div>
                    <div class="h-16 bg-gray-50 dark:bg-gray-900 rounded-xl"></div>
                </div>
            </div>
            <div id="bankAccountForm" class="hidden pt-5 mt-5 border-t border-gray-100 dark:border-gray-800/50">
                <h4 id="formTitle" class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-5">Nueva Cuenta</h4>
                <form id="actualForm" onsubmit="saveBankAccount(event)" class="space-y-4">
                    <input type="hidden" id="editingId" value="">
                    <div>
                        <label class="block text-[11px] font-medium text-gray-500 dark:text-gray-400 mb-1.5 uppercase tracking-wider">Alias</label>
                        <input type="text" id="f_alias" placeholder="Ej: Mi cuenta BCP" class="pk-input w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm px-0 py-2.5 focus:outline-none focus:border-[#006BFF] dark:focus:border-[#4A90E2] transition-colors text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-gray-700">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-medium text-gray-500 dark:text-gray-400 mb-1.5 uppercase tracking-wider">Banco</label>
                            <select id="f_bank_name" required class="pk-input w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm px-0 py-2.5 focus:outline-none focus:border-[#006BFF] dark:focus:border-[#4A90E2] transition-colors text-gray-900 dark:text-white">
                                <option value="">Seleccionar</option>
                                @foreach(['BCP', 'Interbank', 'BBVA', 'Scotiabank', 'BanBif', 'Banco de la Nación', 'Caja Piura', 'Caja Huancayo', 'Caja Arequipa'] as $bank)
                                    <option value="{{ $bank }}">{{ $bank }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-gray-500 dark:text-gray-400 mb-1.5 uppercase tracking-wider">Tipo</label>
                            <select id="f_account_type" required class="pk-input w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm px-0 py-2.5 focus:outline-none focus:border-[#006BFF] dark:focus:border-[#4A90E2] transition-colors text-gray-900 dark:text-white">
                                <option value="ahorros">Ahorros</option>
                                <option value="corriente">Corriente</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-gray-500 dark:text-gray-400 mb-1.5 uppercase tracking-wider">Moneda</label>
                            <select id="f_currency" required class="pk-input w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm px-0 py-2.5 focus:outline-none focus:border-[#006BFF] dark:focus:border-[#4A90E2] transition-colors text-gray-900 dark:text-white">
                                <option value="PEN">Soles (S/)</option>
                                <option value="USD">Dólares ($)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-gray-500 dark:text-gray-400 mb-1.5 uppercase tracking-wider">Nro. Cuenta</label>
                            <input type="text" id="f_account_number" required placeholder="19134567890123" maxlength="14" oninput="this.value=this.value.replace(/[^0-9]/g,'')" class="pk-input w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm px-0 py-2.5 focus:outline-none focus:border-[#006BFF] dark:focus:border-[#4A90E2] transition-colors tabular-nums text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-gray-700">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-medium text-gray-500 dark:text-gray-400 mb-1.5 uppercase tracking-wider">CCI</label>
                        <input type="text" id="f_cci" required placeholder="00219100123456789012" maxlength="20" oninput="this.value=this.value.replace(/[^0-9]/g,'')" class="pk-input w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm px-0 py-2.5 focus:outline-none focus:border-[#006BFF] dark:focus:border-[#4A90E2] transition-colors tabular-nums text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-gray-700">
                    </div>
                    <div class="flex gap-3 pt-3">
                        <button type="submit" class="flex-1 py-2.5 rounded-xl bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-sm font-semibold hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors">Guardar</button>
                        <button type="button" onclick="hideForm()" class="px-5 py-2.5 rounded-xl text-gray-400 dark:text-gray-500 text-sm font-medium hover:text-gray-600 dark:hover:text-gray-300 transition-colors">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
        <div id="addBtnContainer" class="px-6 py-4 border-t border-gray-100 dark:border-gray-800/50 flex-shrink-0">
            <button type="button" onclick="showForm()" class="w-full py-2.5 rounded-xl text-sm font-medium text-[#006BFF] dark:text-[#4A90E2] hover:bg-[#006BFF]/5 dark:hover:bg-[#4A90E2]/5 transition-colors flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Agregar cuenta
            </button>
        </div>
    </div>
</div>

{{-- Delete Confirm Modal --}}
<div id="deleteConfirmModal" class="pk-modal-overlay" role="dialog" aria-modal="true" style="align-items:center;padding:1rem;">
    <div onclick="closeDeleteConfirm()" class="absolute inset-0 bg-black/60"></div>
    <div class="pk-modal-content" style="max-width:360px;">
        <div class="px-6 pt-8 pb-6 text-center">
            <div class="w-12 h-12 rounded-2xl bg-[#E94560]/10 dark:bg-[#E94560]/20 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-[#E94560]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white">Eliminar cuenta</h3>
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1.5">¿Estás seguro? Esta acción no se puede deshacer.</p>
        </div>
        <div class="px-6 pb-6 flex gap-3">
            <button type="button" onclick="closeDeleteConfirm()" class="flex-1 py-2.5 rounded-xl text-sm font-medium text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">Cancelar</button>
            <button type="button" onclick="confirmDelete()" class="flex-1 py-2.5 rounded-xl bg-[#E94560] text-white text-sm font-semibold hover:bg-[#d63d56] transition-colors">Eliminar</button>
        </div>
    </div>
</div>

<script>
    let accounts = [];

    function openProfileModal() {
        document.getElementById('profileModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeProfileModal() {
        document.getElementById('profileModal').classList.remove('active');
        document.body.style.overflow = '';
    }

    function openBankModal() {
        document.getElementById('bankModal').classList.add('active');
        document.body.style.overflow = 'hidden';
        loadAccounts();
    }

    function closeBankModal() {
        document.getElementById('bankModal').classList.remove('active');
        document.body.style.overflow = '';
        hideForm();
    }

    async function loadAccounts() {
        const list = document.getElementById('bankAccountsList');
        try {
            const response = await fetch('{{ route("client.bank-accounts.index") }}');
            accounts = await response.json();
            if (accounts.length === 0) {
                list.innerHTML = '<div class="py-10 text-center"><svg class="w-10 h-10 mx-auto text-gray-200 dark:text-gray-800 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg><p class="text-sm text-gray-300 dark:text-gray-700">Sin cuentas registradas</p></div>';
                return;
            }
            list.innerHTML = accounts.map(acc => `
                <div class="group flex items-center justify-between py-3 border-b border-gray-50 dark:border-gray-800/50 last:border-0">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">${acc.alias || acc.bank_name}</span>
                            <span class="text-[10px] font-medium text-gray-400 dark:text-gray-600 bg-gray-50 dark:bg-gray-800/50 px-1.5 py-0.5 rounded">${acc.bank_name}</span>
                        </div>
                        <p class="text-[11px] text-gray-400 dark:text-gray-600 tabular-nums mt-0.5">${acc.account_type === 'ahorros' ? 'Ahorros' : 'Corriente'} · ${acc.currency === 'PEN' ? 'S/' : '$'} · ${acc.account_number}</p>
                    </div>
                    <div class="flex gap-0.5 flex-shrink-0 ml-3">
                        <button onclick="editAccount(${acc.id})" class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-300 dark:text-gray-700 hover:text-[#006BFF] dark:hover:text-[#4A90E2] hover:bg-[#006BFF]/5 dark:hover:bg-[#4A90E2]/5 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>
                        <button onclick="deleteAccount(${acc.id})" class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-300 dark:text-gray-700 hover:text-[#E94560] hover:bg-[#E94560]/5 transition-colors">
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

    let pendingDeleteId = null;

    function deleteAccount(id) {
        pendingDeleteId = id;
        document.getElementById('deleteConfirmModal').classList.add('active');
    }

    function closeDeleteConfirm() {
        document.getElementById('deleteConfirmModal').classList.remove('active');
        pendingDeleteId = null;
    }

    async function confirmDelete() {
        if (!pendingDeleteId) return;
        const id = pendingDeleteId;
        closeDeleteConfirm();
        try {
            const response = await fetch('/panel/cuentas-bancarias/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } });
            const result = await response.json();
            if (result.success) await loadAccounts();
        } catch (e) { alert('Error al eliminar'); }
    }

    function dismissRejected() {
        var notice = document.getElementById('rejectedNotice');
        if (!notice) return;
        var token = document.querySelector('meta[name="csrf-token"]');
        fetch('{{ route("client.profile-changes.dismiss") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token ? token.content : '{{ csrf_token() }}' },
            body: JSON.stringify({})
        }).then(function () {
            notice.style.transition = 'opacity 0.2s';
            notice.style.opacity = '0';
            setTimeout(function () { notice.remove(); }, 200);
        });
    }
</script>
<script>
(function(){
    var line=document.getElementById('pkGoldWaveLine');
    var fill=document.getElementById('pkGoldWaveFill');
    if(!line||!fill)return;
    var pts=[26,22,24,18,21,12,10];
    var targets=pts.slice();
    var speed=0.015;
    function rand(){return 8+Math.random()*18;}
    function newTargets(){for(var i=0;i<targets.length;i++)targets[i]=rand();}
    function tick(){
        var changed=false;
        for(var i=0;i<pts.length;i++){
            var d=targets[i]-pts[i];
            if(Math.abs(d)>0.3){pts[i]+=d*speed;changed=true;}
        }
        if(!changed)newTargets();
        var p='M0,'+pts[0]+' Q10,'+pts[1]+' 20,'+pts[2]+' T40,'+pts[3]+' T60,'+pts[4]+' T80,'+pts[5]+' T100,'+pts[6];
        line.setAttribute('d',p);
        fill.setAttribute('d',p+' L100,30 L0,30Z');
        requestAnimationFrame(tick);
    }
    newTargets();
    tick();
})();
</script>
@endsection