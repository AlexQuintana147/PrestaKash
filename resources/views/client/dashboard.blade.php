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
        border: 1px solid #E7EDF6;
        border-radius: 20px;
        transition: border-color 0.2s ease;
    }
    .dark .pk-card {
        background: #1A1A2E;
        border-color: #2A2A3E;
    }
    .pk-card:hover { border-color: #C8D6E5; }
    .dark .pk-card:hover { border-color: #3A3A5E; }

    .pk-card-cta {
        background: white;
        border: 1px solid #E7EDF6;
        border-radius: 20px;
        transition: all 0.2s ease;
    }
    .dark .pk-card-cta {
        background: #1A1A2E;
        border-color: #2A2A3E;
    }
    .pk-card-cta:hover { border-color: #006BFF; background: #F8F9FB; }
    .dark .pk-card-cta:hover { background: #1E1E36; border-color: #4A90E2; }
    .pk-card-cta.purple:hover { border-color: #8247f5; }
    .dark .pk-card-cta.purple:hover { border-color: #9B59B6; }

    .pk-status-dot { width: 6px; height: 6px; border-radius: 50%; display: inline-block; margin-right: 4px; }
    .pk-status-confirmed { background: #00D26A; }

    .pk-bottom-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 50;
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(20px);
        border-top: 1px solid #E7EDF6;
    }
    .dark .pk-bottom-nav {
        background: rgba(13,13,13,0.95);
        border-top-color: #2A2A2A;
    }
    @media (min-width: 768px) {
        .pk-bottom-nav { display: none; }
        .pk-main-pb { padding-bottom: 0; }
    }
    @media (max-width: 767px) {
        .pk-main-pb { padding-bottom: 80px; }
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
        background: white;
        border: 1px solid #E7EDF6;
        border-radius: 24px 24px 0 0;
        width: 100%;
        max-height: 85vh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    .dark .pk-modal-content {
        background: #1A1A2E;
        border-color: #2A2A3E;
    }
    @media (min-width: 768px) {
        .pk-modal-content {
            border-radius: 24px;
            max-width: 480px;
            max-height: 80vh;
        }
    }
</style>

<div class="min-h-screen bg-[#F8F9FB] dark:bg-[#0D0D1A] pk-main-pb">

    @if($rejectedChange)
    <div class="px-4 md:px-8 pt-4" id="rejectedNotice">
        <div class="max-w-4xl mx-auto pk-card px-4 py-3 flex items-start gap-3">
            <div class="w-8 h-8 rounded-xl bg-red-50 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-[#0B3558] dark:text-white">Solicitud rechazada</p>
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
        <div class="max-w-4xl mx-auto pk-card px-4 py-3 flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-sm text-[#0B3558] dark:text-white">Cambios de perfil pendientes de aprobación</p>
        </div>
    </div>
    @endif

    @if($hasPendingLoans)
    <div class="px-4 md:px-8 {{ $rejectedChange || $pendingChange ? 'pt-2' : 'pt-4' }}">
        <div class="max-w-4xl mx-auto pk-card px-4 py-3 flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-sm text-[#0B3558] dark:text-white">{{ $pendingLoans->count() }} solicitud(es) en revisión</p>
        </div>
    </div>
    @endif

    {{-- Header --}}
    <div class="px-4 md:px-8 pt-6 pb-2">
        <div class="max-w-4xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ $profilePhoto }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover border-2 border-[#E7EDF6] dark:border-[#2A2A3E]">
                <div>
                    <p class="text-xs text-[#476788] dark:text-gray-400">{{ $greeting }}</p>
                    <p class="text-sm font-semibold text-[#0B3558] dark:text-white">{{ $firstName }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button class="w-9 h-9 rounded-xl bg-white dark:bg-[#1A1A2E] border border-[#E7EDF6] dark:border-[#2A2A3E] flex items-center justify-center relative">
                    <svg class="w-4 h-4 text-[#476788] dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    @if($pendingChange || $rejectedChange || $hasPendingLoans)
                        <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-[#E94560] border-2 border-white dark:border-[#0D0D1A]"></span>
                    @endif
                </button>
            </div>
        </div>
    </div>

    {{-- Title --}}
    <div class="px-4 md:px-8 pb-5">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-2xl md:text-3xl font-bold text-[#0B3558] dark:text-white tracking-tight leading-tight">Panel de<br>Control</h1>
        </div>
    </div>

    {{-- Bento Grid --}}
    <div class="px-4 md:px-8 pb-4">
        <div class="max-w-4xl mx-auto">
            <div class="grid grid-cols-2 gap-3">

                {{-- Card 1: Solicitar Préstamo (tall) --}}
                <a href="{{ $hasProfile ? route('client.loans.create') : route('client.loans.profile.edit') }}" class="pk-card-cta row-span-2 p-4 flex flex-col justify-between min-h-[180px] md:min-h-[220px]">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-5 h-5 text-[#006BFF] dark:text-[#4A90E2]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-xs font-medium text-[#476788] dark:text-gray-400">Nuevo</span>
                        </div>
                        <p class="text-sm font-semibold text-[#0B3558] dark:text-white">Solicitar<br>Préstamo</p>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-[#006BFF] dark:text-[#4A90E2]">24-48h</p>
                        <p class="text-[10px] text-[#A6BBD1] dark:text-gray-500 mt-0.5">Desembolso</p>
                        <svg class="w-full h-10 mt-2" viewBox="0 0 100 30" preserveAspectRatio="none">
                            <defs><linearGradient id="grad1" x1="0%" y1="0%" x2="0%" y2="100%"><stop offset="0%" style="stop-color:#006BFF;stop-opacity:0.2"/><stop offset="100%" style="stop-color:#006BFF;stop-opacity:0"/></linearGradient></defs>
                            <path d="M0,25 Q10,20 20,22 T40,15 T60,18 T80,8 T100,5 L100,30 L0,30Z" fill="url(#grad1)"/>
                            <path d="M0,25 Q10,20 20,22 T40,15 T60,18 T80,8 T100,5" fill="none" stroke="#006BFF" stroke-width="1.5"/>
                        </svg>
                    </div>
                </a>

                {{-- Card 2: Invertir --}}
                <a href="{{ $isInvestor ? route('client.investments.index') : ($hasInvestorApp ? route('client.invertir') : route('client.invest.apply')) }}" class="pk-card-cta purple p-4 flex flex-col justify-between min-h-[100px]">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#8247f5] dark:text-[#9B59B6]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        <span class="text-xs font-medium text-[#476788] dark:text-gray-400">{{ $isInvestor ? 'Activo' : 'Nuevo' }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-[#0B3558] dark:text-white">{{ $isInvestor ? 'Mis Inversiones' : 'Invertir' }}</p>
                        <p class="text-lg font-bold text-[#8247f5] dark:text-[#9B59B6] mt-0.5">2%</p>
                        <p class="text-[10px] text-[#A6BBD1] dark:text-gray-500">mensual</p>
                    </div>
                </a>

                {{-- Card 3: Mis Préstamos --}}
                <a href="{{ route('client.loans') }}" class="pk-card p-4 flex flex-col justify-between min-h-[100px]">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#00A854] dark:text-[#00D26A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span class="text-xs font-medium text-[#476788] dark:text-gray-400">Activos</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-[#0B3558] dark:text-white">Mis Préstamos</p>
                        <p class="text-lg font-bold text-[#00A854] dark:text-[#00D26A] mt-0.5">{{ $activeLoans->count() }}</p>
                    </div>
                </a>

                @if($hasActiveLoans)
                {{-- Card 4: Resumen (wide) --}}
                <div class="pk-card col-span-2 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#E94560]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            <span class="text-xs font-medium text-[#476788] dark:text-gray-400">Resumen</span>
                        </div>
                        <a href="{{ route('client.loans') }}" class="text-xs text-[#006BFF] dark:text-[#4A90E2] hover:underline">Ver todo</a>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <p class="text-[10px] text-[#A6BBD1] dark:text-gray-500 uppercase tracking-wider mb-1">Deuda</p>
                            <p class="text-lg md:text-xl font-bold text-[#0B3558] dark:text-white">S/ {{ number_format($totalDebt, 0) }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-[#A6BBD1] dark:text-gray-500 uppercase tracking-wider mb-1">Cuota/Mes</p>
                            <p class="text-lg md:text-xl font-bold text-[#006BFF] dark:text-[#4A90E2]">S/ {{ number_format($monthlyPayment, 0) }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-[#A6BBD1] dark:text-gray-500 uppercase tracking-wider mb-1">Estado</p>
                            <p class="text-sm font-medium text-[#00A854] dark:text-[#00D26A] flex items-center"><span class="pk-status-dot pk-status-confirmed"></span>Al día</p>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Card 5: Cuentas Banco --}}
                <button type="button" onclick="openBankModal()" class="pk-card p-4 flex flex-col justify-between min-h-[100px] text-left">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#D4910E] dark:text-[#F5A623]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <span class="text-xs font-medium text-[#476788] dark:text-gray-400">Bancos</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-[#0B3558] dark:text-white">Cuentas</p>
                        <p class="text-[10px] text-[#A6BBD1] dark:text-gray-500 mt-0.5">Administrar</p>
                    </div>
                </button>

                {{-- Card 6: Referidos --}}
                <a href="{{ route('client.referidos') }}" class="pk-card p-4 flex flex-col justify-between min-h-[100px]">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#006BFF] dark:text-[#4A90E2]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-xs font-medium text-[#476788] dark:text-gray-400">Red</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-[#0B3558] dark:text-white">Referidos</p>
                        <p class="text-[10px] text-[#A6BBD1] dark:text-gray-500 mt-0.5">Gana comisiones</p>
                    </div>
                </a>

                {{-- Card 7: Perfil (wide) --}}
                <a href="{{ route('client.loans.profile.edit') }}" class="pk-card col-span-2 p-4 flex items-center gap-4">
                    <img src="{{ $profilePhoto }}" alt="" class="w-12 h-12 rounded-xl object-cover">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-[#0B3558] dark:text-white">{{ $firstName }}</p>
                        <p class="text-xs text-[#476788] dark:text-gray-400">{{ $user->dni }} · {{ $user->phone }}</p>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="pk-status-dot pk-status-confirmed"></span>
                        <span class="text-xs text-[#00A854] dark:text-[#00D26A]">Verificado</span>
                    </div>
                    <svg class="w-4 h-4 text-[#A6BBD1] dark:text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>

            </div>
        </div>
    </div>

    @if(!$hasActiveLoans && !$isInvestor)
    <div class="px-4 md:px-8 pb-6">
        <div class="max-w-4xl mx-auto">
            <p class="text-xs text-[#476788] dark:text-gray-400 uppercase tracking-widest font-semibold mb-3">¿Por qué PrestaKash?</p>
            <div class="space-y-2">
                <div class="pk-card p-3 flex items-center gap-3">
                    <svg class="w-5 h-5 text-[#006BFF] dark:text-[#4A90E2] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <div>
                        <p class="text-sm font-medium text-[#0B3558] dark:text-white">Rápido y Simple</p>
                        <p class="text-xs text-[#A6BBD1] dark:text-gray-500">Solicita en minutos, sin papeleos</p>
                    </div>
                </div>
                <div class="pk-card p-3 flex items-center gap-3">
                    <svg class="w-5 h-5 text-[#8247f5] dark:text-[#9B59B6] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <div>
                        <p class="text-sm font-medium text-[#0B3558] dark:text-white">Seguro y Transparente</p>
                        <p class="text-xs text-[#A6BBD1] dark:text-gray-500">Sin letras pequeñas</p>
                    </div>
                </div>
                <div class="pk-card p-3 flex items-center gap-3">
                    <svg class="w-5 h-5 text-[#D4910E] dark:text-[#F5A623] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                    <div>
                        <p class="text-sm font-medium text-[#0B3558] dark:text-white">Doble Oportunidad</p>
                        <p class="text-xs text-[#A6BBD1] dark:text-gray-500">Préstamo o inversión</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>

{{-- Mobile Bottom Nav --}}
<div class="pk-bottom-nav px-6 py-3">
    <div class="max-w-md mx-auto flex items-center justify-around">
        <a href="{{ route('client.dashboard') }}" class="flex flex-col items-center gap-1">
            <div class="w-10 h-10 rounded-full bg-[#006BFF] dark:bg-[#E94560] flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </div>
        </a>
        <a href="{{ route('client.loans') }}" class="flex flex-col items-center gap-1 p-2">
            <svg class="w-5 h-5 text-[#476788] dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
        </a>
        <a href="{{ route('client.invertir') }}" class="flex flex-col items-center gap-1 p-2">
            <svg class="w-5 h-5 text-[#476788] dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
        </a>
        <a href="{{ route('client.referidos') }}" class="flex flex-col items-center gap-1 p-2">
            <svg class="w-5 h-5 text-[#476788] dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </a>
    </div>
</div>

{{-- Bank Modal --}}
<div id="bankModal" class="pk-modal-overlay" role="dialog" aria-modal="true">
    <div onclick="closeBankModal()" class="fixed inset-0 bg-black/60"></div>
    <div class="pk-modal-content">
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#E7EDF6] dark:border-[#2A2A3E] flex-shrink-0">
            <div>
                <h3 class="text-base font-bold text-[#0B3558] dark:text-white">Cuentas Bancarias</h3>
                <p class="text-xs text-[#A6BBD1] dark:text-gray-500 mt-0.5">Donde recibirás tus préstamos</p>
            </div>
            <button type="button" onclick="closeBankModal()" class="p-1 text-[#A6BBD1] dark:text-gray-600 hover:text-[#0B3558] dark:hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto px-5 py-4">
            <div id="bankAccountsList" class="space-y-2">
                <div class="animate-pulse space-y-2">
                    <div class="h-16 bg-[#F8F9FB] dark:bg-[#1A1A2E] rounded-xl"></div>
                    <div class="h-16 bg-[#F8F9FB] dark:bg-[#1A1A2E] rounded-xl"></div>
                </div>
            </div>
            <div id="bankAccountForm" class="hidden pt-4 mt-4 border-t border-[#E7EDF6] dark:border-[#2A2A3E]">
                <h4 id="formTitle" class="text-xs font-semibold text-[#0B3558] dark:text-white uppercase tracking-widest mb-4">Nueva Cuenta</h4>
                <form id="actualForm" onsubmit="saveBankAccount(event)" class="space-y-3">
                    <input type="hidden" id="editingId" value="">
                    <div>
                        <label class="block text-xs font-medium text-[#476788] dark:text-gray-400 mb-1">Alias</label>
                        <input type="text" id="f_alias" placeholder="Ej: Mi cuenta BCP" class="w-full bg-[#F8F9FB] dark:bg-[#0D0D1A] border border-[#E7EDF6] dark:border-[#2A2A3E] rounded-xl text-sm px-4 py-3 focus:outline-none focus:border-[#006BFF] dark:focus:border-[#4A90E2] transition-colors text-[#0B3558] dark:text-white placeholder-[#A6BBD1] dark:placeholder-gray-600">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-[#476788] dark:text-gray-400 mb-1">Banco</label>
                            <select id="f_bank_name" required class="w-full bg-[#F8F9FB] dark:bg-[#0D0D1A] border border-[#E7EDF6] dark:border-[#2A2A3E] rounded-xl text-sm px-4 py-3 focus:outline-none focus:border-[#006BFF] dark:focus:border-[#4A90E2] transition-colors text-[#0B3558] dark:text-white">
                                <option value="">Seleccionar</option>
                                @foreach(['BCP', 'Interbank', 'BBVA', 'Scotiabank', 'BanBif', 'Banco de la Nación', 'Caja Piura', 'Caja Huancayo', 'Caja Arequipa'] as $bank)
                                    <option value="{{ $bank }}">{{ $bank }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-[#476788] dark:text-gray-400 mb-1">Tipo</label>
                            <select id="f_account_type" required class="w-full bg-[#F8F9FB] dark:bg-[#0D0D1A] border border-[#E7EDF6] dark:border-[#2A2A3E] rounded-xl text-sm px-4 py-3 focus:outline-none focus:border-[#006BFF] dark:focus:border-[#4A90E2] transition-colors text-[#0B3558] dark:text-white">
                                <option value="ahorros">Ahorros</option>
                                <option value="corriente">Corriente</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-[#476788] dark:text-gray-400 mb-1">Moneda</label>
                            <select id="f_currency" required class="w-full bg-[#F8F9FB] dark:bg-[#0D0D1A] border border-[#E7EDF6] dark:border-[#2A2A3E] rounded-xl text-sm px-4 py-3 focus:outline-none focus:border-[#006BFF] dark:focus:border-[#4A90E2] transition-colors text-[#0B3558] dark:text-white">
                                <option value="PEN">Soles (S/)</option>
                                <option value="USD">Dólares ($)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-[#476788] dark:text-gray-400 mb-1">Nro. Cuenta</label>
                            <input type="text" id="f_account_number" required placeholder="19134567890123" maxlength="14" oninput="this.value=this.value.replace(/[^0-9]/g,'')" class="w-full bg-[#F8F9FB] dark:bg-[#0D0D1A] border border-[#E7EDF6] dark:border-[#2A2A3E] rounded-xl text-sm px-4 py-3 focus:outline-none focus:border-[#006BFF] dark:focus:border-[#4A90E2] transition-colors tabular-nums text-[#0B3558] dark:text-white placeholder-[#A6BBD1] dark:placeholder-gray-600">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-[#476788] dark:text-gray-400 mb-1">CCI</label>
                        <input type="text" id="f_cci" required placeholder="00219100123456789012" maxlength="20" oninput="this.value=this.value.replace(/[^0-9]/g,'')" class="w-full bg-[#F8F9FB] dark:bg-[#0D0D1A] border border-[#E7EDF6] dark:border-[#2A2A3E] rounded-xl text-sm px-4 py-3 focus:outline-none focus:border-[#006BFF] dark:focus:border-[#4A90E2] transition-colors tabular-nums text-[#0B3558] dark:text-white placeholder-[#A6BBD1] dark:placeholder-gray-600">
                    </div>
                    <div class="flex gap-2 pt-2">
                        <button type="submit" class="flex-1 py-3 rounded-xl bg-[#006BFF] dark:bg-[#4A90E2] text-white text-sm font-semibold hover:bg-[#0052CC] dark:hover:bg-[#3A7BC8] transition-colors">Guardar</button>
                        <button type="button" onclick="hideForm()" class="px-5 py-3 rounded-xl bg-white dark:bg-[#1A1A2E] border border-[#E7EDF6] dark:border-[#2A2A3E] text-[#476788] dark:text-gray-400 text-sm font-medium hover:border-[#006BFF] dark:hover:border-[#4A90E2] transition-colors">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
        <div id="addBtnContainer" class="px-5 py-4 border-t border-[#E7EDF6] dark:border-[#2A2A3E] flex-shrink-0">
            <button type="button" onclick="showForm()" class="w-full py-3 rounded-xl border border-dashed border-[#C8D6E5] dark:border-[#3A3A5E] text-[#476788] dark:text-gray-400 text-sm font-medium hover:border-[#006BFF] dark:hover:border-[#4A90E2] hover:text-[#006BFF] dark:hover:text-[#4A90E2] transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Agregar cuenta
            </button>
        </div>
    </div>
</div>

<script>
    let accounts = [];

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
                list.innerHTML = '<div class="py-8 text-center"><p class="text-sm text-[#A6BBD1] dark:text-gray-500">No tienes cuentas registradas</p></div>';
                return;
            }
            list.innerHTML = accounts.map(acc => `
                <div class="group pk-card px-4 py-3 flex items-center justify-between">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-[#0B3558] dark:text-white">${acc.alias || acc.bank_name}</span>
                            <span class="text-[10px] font-medium text-[#006BFF] dark:text-[#4A90E2] bg-[#006BFF]/10 dark:bg-[#4A90E2]/20 px-1.5 py-0.5 rounded">${acc.bank_name}</span>
                        </div>
                        <p class="text-xs text-[#476788] dark:text-gray-400 tabular-nums mt-0.5">${acc.account_type} · ${acc.currency}</p>
                        <p class="text-xs text-[#A6BBD1] dark:text-gray-500 tabular-nums">${acc.account_number}${acc.cci ? ' · CCI: ' + acc.cci : ''}</p>
                    </div>
                    <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0 ml-2">
                        <button onclick="editAccount(${acc.id})" class="p-1.5 text-[#A6BBD1] dark:text-gray-500 hover:text-[#006BFF] dark:hover:text-[#4A90E2] transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>
                        <button onclick="deleteAccount(${acc.id})" class="p-1.5 text-[#A6BBD1] dark:text-gray-500 hover:text-[#E94560] transition-colors">
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
@endsection