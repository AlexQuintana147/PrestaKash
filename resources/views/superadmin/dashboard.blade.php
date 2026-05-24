@extends('superadmin.layout')

@section('title', 'Dashboard')

@push('styles')
<style>
    @keyframes fadeUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes pulseGlow { 0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); } 50% { box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); } }
    @keyframes slideIn { from { opacity: 0; transform: translateX(-8px); } to { opacity: 1; transform: translateX(0); } }
    .fade-up { animation: fadeUp 0.5s ease-out forwards; opacity: 0; }
    .fade-up:nth-child(1) { animation-delay: 0.03s; }
    .fade-up:nth-child(2) { animation-delay: 0.06s; }
    .fade-up:nth-child(3) { animation-delay: 0.09s; }
    .fade-up:nth-child(4) { animation-delay: 0.12s; }
    .fade-up:nth-child(5) { animation-delay: 0.15s; }
    .fade-up:nth-child(6) { animation-delay: 0.18s; }
    .pulse-glow { animation: pulseGlow 2s ease-in-out infinite; }
    .slide-in { animation: slideIn 0.3s ease-out forwards; }
    .dash-card { transition: all 0.2s ease; }
    .dash-card:hover { transform: translateY(-2px); }
    .urgency-bar { height: 3px; border-radius: 2px; transition: width 0.6s ease; }
</style>
@endpush

@section('content')
<div class="p-4 md:p-6 lg:p-8 space-y-6 max-w-[1600px] mx-auto">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl md:text-2xl font-black text-gray-900 dark:text-white tracking-tight">Dashboard</h1>
            <p class="text-sm text-gray-400 mt-0.5">{{ now()->translatedFormat('l, d \d\e F Y') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('superadmin.analytics') }}" class="hidden sm:flex items-center gap-1.5 px-3 py-2 rounded-xl text-[10px] font-bold uppercase tracking-wider bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Análisis
            </a>
        </div>
    </div>

    {{-- ALERTAS CRÍTICAS --}}
    @if($collectionStats['overdue'] > 0 || $collectionStats['processing'] > 0)
    <div class="fade-up grid grid-cols-1 sm:grid-cols-2 gap-3">
        @if($collectionStats['overdue'] > 0)
        <a href="{{ route('superadmin.installments.index', ['filter' => 'overdue']) }}" class="dash-card flex items-center gap-4 bg-gradient-to-r from-red-50 to-red-100/50 dark:from-red-950/50 dark:to-red-900/20 border border-red-200 dark:border-red-800/50 rounded-2xl p-4 hover:border-red-300 dark:hover:border-red-700">
            <div class="w-11 h-11 rounded-xl bg-red-500 flex items-center justify-center flex-shrink-0 pulse-glow">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-black text-red-700 dark:text-red-400 uppercase tracking-wider">Cuotas vencidas</p>
                <p class="text-lg font-black text-red-900 dark:text-red-300 tabular-nums">{{ $collectionStats['overdue'] }} cuotas</p>
                <p class="text-[10px] text-red-600/70 dark:text-red-400/60 font-bold">S/{{ number_format($collectionStats['overdue_amount'], 0) }} por cobrar</p>
            </div>
            <svg class="w-5 h-5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        @endif

        @if($collectionStats['processing'] > 0)
        <a href="{{ route('superadmin.installments.index', ['filter' => 'processing']) }}" class="dash-card flex items-center gap-4 bg-gradient-to-r from-amber-50 to-amber-100/50 dark:from-amber-950/50 dark:to-amber-900/20 border border-amber-200 dark:border-amber-800/50 rounded-2xl p-4 hover:border-amber-300 dark:hover:border-amber-700">
            <div class="w-11 h-11 rounded-xl bg-amber-500 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-black text-amber-700 dark:text-amber-400 uppercase tracking-wider">Comprobantes por aprobar</p>
                <p class="text-lg font-black text-amber-900 dark:text-amber-300 tabular-nums">{{ $collectionStats['processing'] }} pagos</p>
                <p class="text-[10px] text-amber-600/70 dark:text-amber-400/60 font-bold">S/{{ number_format($collectionStats['processing_amount'], 0) }} por confirmar</p>
            </div>
            <svg class="w-5 h-5 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        @endif
    </div>
    @endif

    {{-- STATS PRINCIPALES --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
        <div class="fade-up dash-card bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                </div>
                <span class="text-[9px] font-bold text-green-600 dark:text-green-400 uppercase">+{{ $stats['today'] }} hoy</span>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ $stats['total'] }}</p>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">Clientes</p>
        </div>

        <div class="fade-up dash-card bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <span class="text-[9px] font-bold text-emerald-600 dark:text-emerald-400 uppercase">{{ $loanStats['active'] }} activos</span>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">S/{{ number_format($loanStats['active_amount'] / 1000, 1) }}k</p>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">Cartera activa</p>
        </div>

        <div class="fade-up dash-card bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 rounded-xl {{ $collectionStats['due_today'] > 0 ? 'bg-orange-50 dark:bg-orange-900/30' : 'bg-gray-50 dark:bg-gray-800' }} flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 {{ $collectionStats['due_today'] > 0 ? 'text-orange-600 dark:text-orange-400' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
                @if($collectionStats['due_today'] > 0)
                <span class="text-[9px] font-bold text-orange-600 dark:text-orange-400 uppercase">HOY</span>
                @endif
            </div>
            <p class="text-2xl font-black {{ $collectionStats['due_today'] > 0 ? 'text-orange-600 dark:text-orange-400' : 'text-gray-900 dark:text-white' }} tabular-nums">{{ $collectionStats['due_today'] }}</p>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">Cobrar hoy</p>
            @if($collectionStats['due_today'] > 0)
            <p class="text-[10px] text-orange-500/70 font-bold mt-1">S/{{ number_format($collectionStats['due_today_amount'], 0) }}</p>
            @endif
        </div>

        <div class="fade-up dash-card bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 rounded-xl {{ $payoutStats['pending_payouts'] > 0 ? 'bg-violet-50 dark:bg-violet-900/30' : 'bg-gray-50 dark:bg-gray-800' }} flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 {{ $payoutStats['pending_payouts'] > 0 ? 'text-violet-600 dark:text-violet-400' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
                @if($payoutStats['pending_payouts'] > 0)
                <span class="text-[9px] font-bold text-violet-600 dark:text-violet-400 uppercase">Pend.</span>
                @endif
            </div>
            <p class="text-2xl font-black {{ $payoutStats['pending_payouts'] > 0 ? 'text-violet-600 dark:text-violet-400' : 'text-gray-900 dark:text-white' }} tabular-nums">{{ $payoutStats['pending_payouts'] }}</p>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">Pagar inversores</p>
            @if($payoutStats['pending_payouts'] > 0)
            <p class="text-[10px] text-violet-500/70 font-bold mt-1">S/{{ number_format($payoutStats['pending_payouts_amount'], 0) }}</p>
            @endif
        </div>

        <div class="fade-up dash-card bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 rounded-xl {{ $loanStats['in_default'] > 0 ? 'bg-red-50 dark:bg-red-900/30' : 'bg-gray-50 dark:bg-gray-800' }} flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 {{ $loanStats['in_default'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" /></svg>
                </div>
                @if($loanStats['in_default'] > 0)
                <span class="text-[9px] font-bold text-red-600 dark:text-red-400 uppercase">Morosidad</span>
                @endif
            </div>
            <p class="text-2xl font-black {{ $loanStats['in_default'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }} tabular-nums">{{ $loanStats['in_default'] }}</p>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">En mora</p>
        </div>
    </div>

    {{-- COBRANZA + PAGOS INVERSORES --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- COBRANZA PRÓXIMA --}}
        <div class="fade-up bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">Cobrar esta semana</h3>
                        <p class="text-[9px] text-gray-400 font-bold">{{ $upcomingCollections->count() }} cuotas próximas</p>
                    </div>
                </div>
                <a href="{{ route('superadmin.installments.index') }}" class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline">Ver todo</a>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-gray-800/50 max-h-[380px] overflow-y-auto">
                @forelse($upcomingCollections as $installment)
                    @php
                        $loan = $installment->loan;
                        $user = $loan?->user;
                        $isToday = $installment->due_date->isToday();
                        $isTomorrow = $installment->due_date->isTomorrow();
                        $daysLeft = now()->startOfDay()->diffInDays($installment->due_date->startOfDay(), false);
                        $urgencyClass = $isToday ? 'bg-orange-500' : ($daysLeft <= 2 ? 'bg-amber-400' : 'bg-gray-200 dark:bg-gray-700');
                    @endphp
                    <div class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50/50 dark:hover:bg-gray-800/20 transition-colors">
                        <div class="w-8 h-8 rounded-full {{ $isToday ? 'bg-orange-100 dark:bg-orange-900/30' : 'bg-gray-100 dark:bg-gray-800' }} flex items-center justify-center flex-shrink-0">
                            @if($isToday)
                                <span class="text-[8px] font-black text-orange-600 dark:text-orange-400">HOY</span>
                            @elseif($isTomorrow)
                                <span class="text-[8px] font-black text-amber-600 dark:text-amber-400">MAÑ</span>
                            @else
                                <span class="text-[9px] font-bold text-gray-500 dark:text-gray-400">{{ $installment->due_date->format('d') }}</span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ $user?->name ?? 'Sin cliente' }}</p>
                            <p class="text-[9px] text-gray-400 truncate">{{ $loan?->code ?? '' }} · Cuota {{ $installment->installment_number }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-xs font-black text-gray-900 dark:text-white tabular-nums">S/{{ number_format($installment->amount, 0) }}</p>
                            <p class="text-[9px] text-gray-400">{{ $installment->due_date->format('d/m') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center">
                        <svg class="w-8 h-8 text-gray-200 dark:text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-xs text-gray-400 font-medium">Sin cobros esta semana</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- PAGOS A INVERSORES --}}
        <div class="fade-up bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-violet-50 dark:bg-violet-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">Pagar inversores</h3>
                        <p class="text-[9px] text-gray-400 font-bold">{{ $payoutStats['pending_payouts'] }} pagos pendientes</p>
                    </div>
                </div>
                <a href="{{ route('superadmin.investments.payouts') }}" class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline">Ver todo</a>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-gray-800/50 max-h-[380px] overflow-y-auto">
                @forelse($upcomingPayouts as $payout)
                    <div class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50/50 dark:hover:bg-gray-800/20 transition-colors">
                        <div class="w-8 h-8 rounded-full bg-violet-50 dark:bg-violet-900/30 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ $payout->investor?->name ?? 'Sin inversor' }}</p>
                            <p class="text-[9px] text-gray-400 truncate">{{ $payout->opportunity?->code ?? '' }} · Mes {{ $payout->month_number }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-xs font-black text-violet-600 dark:text-violet-400 tabular-nums">S/{{ number_format($payout->amount, 0) }}</p>
                            <span class="inline-flex px-1.5 py-0.5 rounded text-[8px] font-bold uppercase tracking-wider bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400">Pend.</span>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center">
                        <svg class="w-8 h-8 text-gray-200 dark:text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-xs text-gray-400 font-medium">Sin pagos pendientes a inversores</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- DEUDORES + INVERSIONES FINANCIADAS --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- DEUDORES / MOROSIDAD --}}
        <div class="fade-up bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">Deudores</h3>
                        <p class="text-[9px] text-gray-400 font-bold">{{ $overdueInstallments->count() }} cuotas vencidas</p>
                    </div>
                </div>
                <a href="{{ route('superadmin.installments.index', ['filter' => 'overdue']) }}" class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline">Ver todo</a>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-gray-800/50 max-h-[380px] overflow-y-auto">
                @forelse($overdueInstallments as $installment)
                    @php
                        $loan = $installment->loan;
                        $user = $loan?->user;
                        $daysOverdue = $installment->due_date->startOfDay()->diffInDays(now()->startOfDay());
                        $totalDebt = $installment->total_with_mora;
                        $severityClass = $daysOverdue > 30 ? 'text-red-600 dark:text-red-400' : ($daysOverdue > 7 ? 'text-orange-600 dark:text-orange-400' : 'text-amber-600 dark:text-amber-400');
                    @endphp
                    <div class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50/50 dark:hover:bg-gray-800/20 transition-colors">
                        <div class="w-8 h-8 rounded-full bg-red-50 dark:bg-red-900/20 flex items-center justify-center flex-shrink-0">
                            <span class="text-[8px] font-black {{ $severityClass }}">{{ $daysOverdue }}d</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ $user?->name ?? 'Sin cliente' }}</p>
                            <p class="text-[9px] text-gray-400 truncate">{{ $loan?->code ?? '' }} · Cuota {{ $installment->installment_number }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-xs font-black {{ $severityClass }} tabular-nums">S/{{ number_format($totalDebt, 0) }}</p>
                            @if($installment->late_fee > 0)
                                <p class="text-[8px] text-red-400 font-bold">+S/{{ number_format($installment->late_fee, 0) }} mora</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center">
                        <svg class="w-8 h-8 text-gray-200 dark:text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-xs text-gray-400 font-medium">Sin deudores — todo al día</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- INVERSIONES FINANCIADAS --}}
        <div class="fade-up bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">Inversiones activas</h3>
                        <p class="text-[9px] text-gray-400 font-bold">{{ $fundedOpportunities->count() }} oportunidades financiadas</p>
                    </div>
                </div>
                <a href="{{ route('superadmin.investments.payouts') }}" class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline">Pagos</a>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-gray-800/50 max-h-[380px] overflow-y-auto">
                @forelse($fundedOpportunities as $opp)
                    @php
                        $curSymbol = $opp->currency === 'USD' ? '$' : 'S/';
                        $investorCount = $opp->approvedInvestors->count();
                        $totalInvested = $opp->approvedInvestors->sum('pivot.amount');
                    @endphp
                    <div class="px-5 py-3.5 hover:bg-gray-50/50 dark:hover:bg-gray-800/20 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="flex-shrink-0 text-[8px] font-mono text-white dark:text-black bg-black dark:bg-white px-1.5 py-0.5 rounded">{{ $opp->code }}</span>
                                <p class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ $opp->title }}</p>
                            </div>
                            <span class="flex-shrink-0 text-[8px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded {{ $opp->status === 'funded' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400' }}">{{ $opp->status === 'funded' ? 'Financiada' : 'Cerrada' }}</span>
                        </div>
                        <div class="flex items-center gap-4 mt-2 pl-7">
                            <span class="text-[9px] text-gray-400 font-bold">{{ $opp->monthly_return_rate }}% · {{ $opp->term_months }}m</span>
                            <span class="text-[9px] text-gray-400 font-bold">{{ $investorCount }} inversor{{ $investorCount !== 1 ? 'es' : '' }}</span>
                            <span class="text-[10px] font-black {{ $opp->currency === 'USD' ? 'text-blue-600 dark:text-blue-400' : 'text-emerald-600 dark:text-emerald-400' }} tabular-nums ml-auto">{{ $curSymbol }}{{ number_format($totalInvested, 0) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center">
                        <svg class="w-8 h-8 text-gray-200 dark:text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-xs text-gray-400 font-medium">Sin inversiones financiadas</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ACCESOS RÁPIDOS + USUARIOS RECIENTES --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ACCESOS RÁPIDOS --}}
        <div class="fade-up space-y-3">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Accesos rápidos</h3>
            <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('superadmin.loans.index', ['filter' => 'pending']) }}" class="dash-card flex flex-col items-center gap-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-4 hover:border-blue-300 dark:hover:border-blue-700 transition-colors">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                    <span class="text-[10px] font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Préstamos</span>
                </a>
                <a href="{{ route('superadmin.installments.index') }}" class="dash-card flex flex-col items-center gap-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-4 hover:border-emerald-300 dark:hover:border-emerald-700 transition-colors">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                    </div>
                    <span class="text-[10px] font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Cobranzas</span>
                </a>
                <a href="{{ route('superadmin.investments.index') }}" class="dash-card flex flex-col items-center gap-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-4 hover:border-violet-300 dark:hover:border-violet-700 transition-colors">
                    <div class="w-9 h-9 rounded-xl bg-violet-50 dark:bg-violet-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    </div>
                    <span class="text-[10px] font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Inversiones</span>
                </a>
                <a href="{{ route('superadmin.investments.payouts') }}" class="dash-card flex flex-col items-center gap-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-4 hover:border-amber-300 dark:hover:border-amber-700 transition-colors">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                    <span class="text-[10px] font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Pagos Inv.</span>
                </a>
            </div>

            {{-- PENDIENTES --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-4 space-y-2.5">
                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Pendientes por aprobar</h4>
                @if($stats['pending'] > 0)
                <a href="{{ route('superadmin.users.index') }}" class="flex items-center justify-between py-1 group">
                    <span class="text-xs text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">Usuarios</span>
                    <span class="text-xs font-black text-amber-600 dark:text-amber-400 tabular-nums">{{ $stats['pending'] }}</span>
                </a>
                @endif
                @if($loanStats['pending'] > 0)
                <a href="{{ route('superadmin.loans.index', ['filter' => 'pending']) }}" class="flex items-center justify-between py-1 group">
                    <span class="text-xs text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">Préstamos</span>
                    <span class="text-xs font-black text-amber-600 dark:text-amber-400 tabular-nums">{{ $loanStats['pending'] }}</span>
                </a>
                @endif
                @if($collectionStats['processing'] > 0)
                <a href="{{ route('superadmin.installments.index', ['filter' => 'processing']) }}" class="flex items-center justify-between py-1 group">
                    <span class="text-xs text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">Comprobantes</span>
                    <span class="text-xs font-black text-amber-600 dark:text-amber-400 tabular-nums">{{ $collectionStats['processing'] }}</span>
                </a>
                @endif
                @if($stats['pending'] === 0 && $loanStats['pending'] === 0 && $collectionStats['processing'] === 0)
                <p class="text-xs text-gray-400 text-center py-2">Todo al día ✓</p>
                @endif
            </div>
        </div>

        {{-- USUARIOS RECIENTES --}}
        <div class="fade-up lg:col-span-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                <div>
                    <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">Usuarios recientes</h3>
                    <p class="text-[9px] text-gray-400 font-bold">Últimos registros</p>
                </div>
                <a href="{{ route('superadmin.users.index') }}" class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline">Ver todos</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="text-left py-2.5 px-5 font-bold text-gray-400 uppercase tracking-wider text-[9px]">Usuario</th>
                            <th class="text-left py-2.5 px-5 font-bold text-gray-400 uppercase tracking-wider text-[9px] hidden sm:table-cell">Rol</th>
                            <th class="text-left py-2.5 px-5 font-bold text-gray-400 uppercase tracking-wider text-[9px]">Estado</th>
                            <th class="text-left py-2.5 px-5 font-bold text-gray-400 uppercase tracking-wider text-[9px] hidden md:table-cell">Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentUsers as $user)
                            <tr class="border-b border-gray-50 dark:border-gray-800/50 hover:bg-gray-50/50 dark:hover:bg-gray-800/20 transition-colors">
                                <td class="py-2.5 px-5">
                                    <a href="{{ route('superadmin.users.show', $user) }}" class="flex items-center gap-2.5 hover:underline">
                                        <div class="w-7 h-7 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center flex-shrink-0">
                                            <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-gray-900 dark:text-white truncate max-w-[140px]">{{ $user->name }}</p>
                                            <p class="text-gray-400 truncate max-w-[140px]">{{ $user->email }}</p>
                                        </div>
                                    </a>
                                </td>
                                <td class="py-2.5 px-5 hidden sm:table-cell">
                                    <span class="text-gray-500 dark:text-gray-400">{{ $user->role->name ?? '—' }}</span>
                                </td>
                                <td class="py-2.5 px-5">
                                    @if($user->profile_completed)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400">Completo</span>
                                    @elseif($user->documents && $user->documents->where('status', 'pending')->count() > 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400">Pendiente</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-400">Incompleto</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-5 text-gray-400 hidden md:table-cell">{{ $user->created_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
