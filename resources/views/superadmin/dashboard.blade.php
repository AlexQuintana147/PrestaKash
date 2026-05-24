@extends('superadmin.layout')

@section('title', 'Dashboard')

@push('styles')
<style>
    .pk-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        transition: border-color 0.2s ease;
    }
    .dark .pk-card {
        background: #030712;
        border-color: #1f2937;
    }
    .pk-card:hover { border-color: #d1d5db; }
    .dark .pk-card:hover { border-color: #374151; }

    @keyframes pkPulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
    .pk-pulse { animation: pkPulse 2s ease-in-out infinite; }

    @keyframes pkFadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .pk-fade { animation: pkFadeUp 0.4s ease-out forwards; opacity: 0; }
    .pk-fade:nth-child(1) { animation-delay: 0.02s; }
    .pk-fade:nth-child(2) { animation-delay: 0.04s; }
    .pk-fade:nth-child(3) { animation-delay: 0.06s; }
    .pk-fade:nth-child(4) { animation-delay: 0.08s; }
    .pk-fade:nth-child(5) { animation-delay: 0.10s; }
    .pk-fade:nth-child(6) { animation-delay: 0.12s; }

    .pk-list-row { transition: background-color 0.15s ease; }
    .pk-list-row:hover { background-color: #f9fafb; }
    .dark .pk-list-row:hover { background-color: rgba(255,255,255,0.03); }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-950 p-4 md:p-6 lg:p-8">
<div class="max-w-[1600px] mx-auto space-y-5">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl md:text-2xl font-black text-gray-900 dark:text-white tracking-tight">Dashboard</h1>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ now()->translatedFormat('l, d \d\e F Y') }}</p>
        </div>
        <a href="{{ route('superadmin.analytics') }}" class="hidden sm:flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-[10px] font-bold uppercase tracking-wider bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-white/10 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Análisis
        </a>
    </div>

    {{-- ALERTAS CRÍTICAS --}}
    @if($collectionStats['overdue'] > 0 || $collectionStats['processing'] > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        @if($collectionStats['overdue'] > 0)
        <a href="{{ route('superadmin.installments.index', ['filter' => 'overdue']) }}" class="pk-fade block relative overflow-hidden rounded-2xl bg-gradient-to-br from-red-500 to-red-600 p-4 md:p-5 shadow-lg shadow-red-500/20 transition-transform active:scale-[0.99]">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-bl-full pointer-events-none"></div>
            <div class="relative z-10 flex items-center gap-3">
                <svg class="w-10 h-10 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-white/80 uppercase tracking-wider">Cuotas vencidas</p>
                    <p class="text-lg font-black text-white tabular-nums">{{ $collectionStats['overdue'] }} cuotas</p>
                    <p class="text-[10px] text-white/70 font-bold">S/{{ number_format($collectionStats['overdue_amount'], 0) }} por cobrar</p>
                </div>
                <svg class="w-5 h-5 text-white/40 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
        </a>
        @endif

        @if($collectionStats['processing'] > 0)
        <a href="{{ route('superadmin.installments.index', ['filter' => 'processing']) }}" class="pk-fade block relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 p-4 md:p-5 shadow-lg shadow-amber-500/20 transition-transform active:scale-[0.99]">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-bl-full pointer-events-none"></div>
            <div class="relative z-10 flex items-center gap-3">
                <svg class="w-10 h-10 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-white/80 uppercase tracking-wider">Comprobantes por aprobar</p>
                    <p class="text-lg font-black text-white tabular-nums">{{ $collectionStats['processing'] }} pagos</p>
                    <p class="text-[10px] text-white/70 font-bold">S/{{ number_format($collectionStats['processing_amount'], 0) }} por confirmar</p>
                </div>
                <svg class="w-5 h-5 text-white/40 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
        </a>
        @endif
    </div>
    @endif

    {{-- HERO: CARTERA ACTIVA --}}
    <div class="pk-fade relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#006BFF] to-[#8247f5] p-5 md:p-6">
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-bl-full pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-28 h-28 bg-white/5 rounded-tr-full pointer-events-none"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-3">
                <svg class="w-5 h-5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-xs font-medium text-white/70 uppercase tracking-wider">Cartera activa</span>
            </div>
            <p class="text-3xl md:text-4xl font-bold text-white tracking-tight">S/{{ number_format($loanStats['active_amount'], 0) }}</p>
            <div class="mt-3 flex items-center gap-3 flex-wrap">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white/15 text-[10px] font-medium text-white/90">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#00D26A]"></span>
                    {{ $loanStats['active'] }} préstamos activos
                </span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white/15 text-[10px] font-medium text-white/90">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#006BFF]"></span>
                    {{ $stats['total'] }} clientes
                </span>
                @if($loanStats['in_default'] > 0)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-500/30 text-[10px] font-bold text-white pk-pulse">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                    {{ $loanStats['in_default'] }} en mora
                </span>
                @endif
            </div>
        </div>
    </div>

    {{-- STATS ROW --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="pk-fade pk-card p-4">
            <div class="flex items-center justify-between mb-3">
                <svg class="w-9 h-9 text-[#006BFF]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="text-[9px] font-bold text-[#00D26A] uppercase">+{{ $stats['today'] }} hoy</span>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ $stats['total'] }}</p>
            <p class="text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-widest mt-0.5">Clientes</p>
        </div>

        <div class="pk-fade pk-card p-4">
            <div class="flex items-center justify-between mb-3">
                <svg class="w-9 h-9 {{ $collectionStats['due_today'] > 0 ? 'text-orange-500' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                @if($collectionStats['due_today'] > 0)
                <span class="text-[9px] font-bold text-orange-500 uppercase pk-pulse">HOY</span>
                @endif
            </div>
            <p class="text-2xl font-black {{ $collectionStats['due_today'] > 0 ? 'text-orange-500' : 'text-gray-900 dark:text-white' }} tabular-nums">{{ $collectionStats['due_today'] }}</p>
            <p class="text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-widest mt-0.5">Cobrar hoy</p>
            @if($collectionStats['due_today'] > 0)
            <p class="text-[10px] text-orange-500/70 font-bold mt-1">S/{{ number_format($collectionStats['due_today_amount'], 0) }}</p>
            @endif
        </div>

        <div class="pk-fade pk-card p-4">
            <div class="flex items-center justify-between mb-3">
                <svg class="w-9 h-9 {{ $payoutStats['pending_payouts'] > 0 ? 'text-[#8247f5]' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                @if($payoutStats['pending_payouts'] > 0)
                <span class="text-[9px] font-bold text-[#8247f5] uppercase">Pend.</span>
                @endif
            </div>
            <p class="text-2xl font-black {{ $payoutStats['pending_payouts'] > 0 ? 'text-[#8247f5]' : 'text-gray-900 dark:text-white' }} tabular-nums">{{ $payoutStats['pending_payouts'] }}</p>
            <p class="text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-widest mt-0.5">Pagar inversores</p>
            @if($payoutStats['pending_payouts'] > 0)
            <p class="text-[10px] text-[#8247f5]/70 font-bold mt-1">S/{{ number_format($payoutStats['pending_payouts_amount'], 0) }}</p>
            @endif
        </div>

        <div class="pk-fade pk-card p-4">
            <div class="flex items-center justify-between mb-3">
                <svg class="w-9 h-9 {{ $loanStats['in_default'] > 0 ? 'text-red-500' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                @if($loanStats['in_default'] > 0)
                <span class="text-[9px] font-bold text-red-500 uppercase pk-pulse">Mora</span>
                @endif
            </div>
            <p class="text-2xl font-black {{ $loanStats['in_default'] > 0 ? 'text-red-500' : 'text-gray-900 dark:text-white' }} tabular-nums">{{ $loanStats['in_default'] }}</p>
            <p class="text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-widest mt-0.5">En mora</p>
        </div>
    </div>

    {{-- COBRANZA + PAGOS INVERSORES --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- COBRANZA PRÓXIMA --}}
        <div class="pk-fade pk-card overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <svg class="w-8 h-8 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <div>
                        <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">Cobrar esta semana</h3>
                        <p class="text-[9px] text-gray-400 dark:text-gray-500 font-bold" id="coll-count">{{ $upcomingCollections->count() }} cuotas próximas</p>
                    </div>
                </div>
                <a href="{{ route('superadmin.installments.index') }}" class="text-[10px] font-bold text-[#006BFF] dark:text-[#4A90E2] hover:underline">Ver todo</a>
            </div>
            <div id="coll-list" class="divide-y divide-gray-50 dark:divide-gray-800/50">
                @forelse($upcomingCollections as $i => $installment)
                    @php
                        $loan = $installment->loan;
                        $user = $loan?->user;
                        $isToday = $installment->due_date->isToday();
                        $isTomorrow = $installment->due_date->isTomorrow();
                    @endphp
                    <a href="{{ $loan ? route('superadmin.installments.group', $loan) . '?source=modal' : '#' }}" class="coll-item pk-list-row flex items-center gap-3 px-5 py-3 cursor-pointer" data-idx="{{ $i }}">
                        <div class="w-8 h-8 rounded-full {{ $isToday ? 'bg-orange-500/10 dark:bg-orange-500/20' : ($isTomorrow ? 'bg-amber-500/10 dark:bg-amber-500/20' : 'bg-gray-100 dark:bg-white/5') }} flex items-center justify-center flex-shrink-0">
                            @if($isToday)
                                <span class="text-[8px] font-black text-orange-500">HOY</span>
                            @elseif($isTomorrow)
                                <span class="text-[8px] font-black text-amber-500">MAÑ</span>
                            @else
                                <span class="text-[9px] font-bold text-gray-400 dark:text-gray-500">{{ $installment->due_date->format('d') }}</span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ $user?->name ?? 'Sin cliente' }}</p>
                            <p class="text-[9px] text-gray-400 dark:text-gray-500 truncate">{{ $loan?->code ?? '' }} · Cuota {{ $installment->installment_number }}</p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <div class="text-right">
                                <p class="text-xs font-black text-gray-900 dark:text-white tabular-nums">S/{{ number_format($installment->amount, 2) }}</p>
                                <p class="text-[9px] text-gray-400 dark:text-gray-500">{{ $installment->due_date->format('d/m') }}</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </a>
                @empty
                    <div class="px-5 py-10 text-center">
                        <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-xs text-gray-400 dark:text-gray-500 font-medium">Sin cobros esta semana</p>
                    </div>
                @endforelse
            </div>
            <div id="coll-pag" class="hidden px-5 py-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-center gap-1.5"></div>
        </div>

        {{-- PAGOS A INVERSORES --}}
        <div class="pk-fade pk-card overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <svg class="w-8 h-8 text-[#8247f5] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <div>
                        <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">Pagar inversores</h3>
                        <p class="text-[9px] text-gray-400 dark:text-gray-500 font-bold" id="payout-count">{{ $payoutStats['pending_payouts'] }} pagos pendientes</p>
                    </div>
                </div>
                <a href="{{ route('superadmin.investments.payouts') }}" class="text-[10px] font-bold text-[#006BFF] dark:text-[#4A90E2] hover:underline">Ver todo</a>
            </div>
            <div id="payout-list" class="divide-y divide-gray-50 dark:divide-gray-800/50">
                @forelse($upcomingPayouts as $i => $payout)
                    <a href="{{ route('superadmin.investments.payouts') }}" class="payout-item pk-list-row flex items-center gap-3 px-5 py-3 cursor-pointer" data-idx="{{ $i }}">
                        <svg class="w-8 h-8 text-[#8247f5] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ $payout->investor?->name ?? 'Sin inversor' }}</p>
                            <p class="text-[9px] text-gray-400 dark:text-gray-500 truncate">{{ $payout->opportunity?->code ?? '' }} · Mes {{ $payout->month_number }}</p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <div class="text-right">
                                <p class="text-xs font-black text-[#8247f5] tabular-nums">S/{{ number_format($payout->amount, 2) }}</p>
                                <span class="inline-flex px-1.5 py-0.5 rounded text-[8px] font-bold uppercase tracking-wider bg-amber-500/10 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400">Pend.</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </a>
                @empty
                    <div class="px-5 py-10 text-center">
                        <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-xs text-gray-400 dark:text-gray-500 font-medium">Sin pagos pendientes a inversores</p>
                    </div>
                @endforelse
            </div>
            <div id="payout-pag" class="hidden px-5 py-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-center gap-1.5"></div>
        </div>
    </div>

    {{-- DEUDORES + INVERSIONES --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- DEUDORES --}}
        <div class="pk-fade pk-card overflow-hidden">
            @if($overdueInstallments->count() > 0)
            <div class="relative overflow-hidden bg-gradient-to-br from-red-500 to-red-600 px-5 py-4">
                <div class="absolute top-0 right-0 w-24 h-24 bg-white/5 rounded-bl-full pointer-events-none"></div>
                <div class="relative z-10 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-8 h-8 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        <div>
                            <h3 class="text-xs font-black text-white uppercase tracking-tight">Deudores</h3>
                            <p class="text-[9px] text-white/70 font-bold">{{ $overdueInstallments->count() }} cuotas vencidas</p>
                        </div>
                    </div>
                    <a href="{{ route('superadmin.installments.index', ['filter' => 'overdue']) }}" class="text-[10px] font-bold text-white/80 hover:text-white hover:underline">Ver todo</a>
                </div>
            </div>
            @else
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <svg class="w-8 h-8 text-[#00D26A] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">Deudores</h3>
                        <p class="text-[9px] text-[#00D26A] font-bold">Todo al día</p>
                    </div>
                </div>
            </div>
            @endif
            <div class="divide-y divide-gray-50 dark:divide-gray-800/50 max-h-[380px] overflow-y-auto">
                @forelse($overdueInstallments as $installment)
                    @php
                        $loan = $installment->loan;
                        $user = $loan?->user;
                        $daysOverdue = $installment->due_date->startOfDay()->diffInDays(now()->startOfDay());
                        $totalDebt = $installment->total_with_mora;
                        $severityClass = $daysOverdue > 30 ? 'text-red-500' : ($daysOverdue > 7 ? 'text-orange-500' : 'text-amber-500');
                    @endphp
                    <a href="{{ $loan ? route('superadmin.installments.group', $loan) : '#' }}" class="pk-list-row flex items-center gap-3 px-5 py-3 cursor-pointer">
                        <div class="w-8 h-8 rounded-full bg-red-500/10 dark:bg-red-500/20 flex items-center justify-center flex-shrink-0">
                            <span class="text-[8px] font-black {{ $severityClass }}">{{ $daysOverdue }}d</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ $user?->name ?? 'Sin cliente' }}</p>
                            <p class="text-[9px] text-gray-400 dark:text-gray-500 truncate">{{ $loan?->code ?? '' }} · Cuota {{ $installment->installment_number }}</p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <div class="text-right">
                                <p class="text-xs font-black {{ $severityClass }} tabular-nums">S/{{ number_format($totalDebt, 2) }}</p>
                                @if($installment->late_fee > 0)
                                    <p class="text-[8px] text-red-400 font-bold">+S/{{ number_format($installment->late_fee, 2) }} mora</p>
                                @endif
                            </div>
                            <svg class="w-4 h-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </a>
                @empty
                    <div class="px-5 py-10 text-center">
                        <svg class="w-12 h-12 text-[#00D26A] mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-xs text-[#00D26A] font-medium">Sin deudores — todo al día</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- INVERSIONES FINANCIADAS --}}
        <div class="pk-fade pk-card overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <svg class="w-8 h-8 text-[#00D26A] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    <div>
                        <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">Inversiones activas</h3>
                        <p class="text-[9px] text-gray-400 dark:text-gray-500 font-bold">{{ $fundedOpportunities->count() }} oportunidades financiadas</p>
                    </div>
                </div>
                <a href="{{ route('superadmin.investments.payouts') }}" class="text-[10px] font-bold text-[#006BFF] dark:text-[#4A90E2] hover:underline">Pagos</a>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-gray-800/50 max-h-[380px] overflow-y-auto">
                @forelse($fundedOpportunities as $opp)
                    @php
                        $curSymbol = $opp->currency === 'USD' ? '$' : 'S/';
                        $investorCount = $opp->approvedInvestors->count();
                        $totalInvested = $opp->approvedInvestors->sum('pivot.amount');
                    @endphp
                    <a href="{{ route('superadmin.investments.show', $opp) }}" class="pk-list-row px-5 py-3.5 cursor-pointer block">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="flex-shrink-0 text-[8px] font-mono text-white dark:text-black bg-black dark:bg-white px-1.5 py-0.5 rounded">{{ $opp->code }}</span>
                                <p class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ $opp->title }}</p>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <span class="text-[8px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded {{ $opp->status === 'funded' ? 'bg-[#006BFF]/10 dark:bg-[#006BFF]/20 text-[#006BFF]' : 'bg-[#00D26A]/10 dark:bg-[#00D26A]/20 text-[#00D26A]' }}">{{ $opp->status === 'funded' ? 'Financiada' : 'Cerrada' }}</span>
                                <svg class="w-4 h-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 mt-2 pl-7">
                            <span class="text-[9px] text-gray-400 dark:text-gray-500 font-bold">{{ $opp->monthly_return_rate }}% · {{ $opp->term_months }}m</span>
                            <span class="text-[9px] text-gray-400 dark:text-gray-500 font-bold">{{ $investorCount }} inversor{{ $investorCount !== 1 ? 'es' : '' }}</span>
                            <span class="text-[10px] font-black {{ $opp->currency === 'USD' ? 'text-[#006BFF]' : 'text-[#00D26A]' }} tabular-nums ml-auto">{{ $curSymbol }}{{ number_format($totalInvested, 2) }}</span>
                        </div>
                    </a>
                @empty
                    <div class="px-5 py-10 text-center">
                        <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        <p class="text-xs text-gray-400 dark:text-gray-500 font-medium">Sin inversiones financiadas</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ACCESOS RÁPIDOS + USUARIOS RECIENTES --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ACCESOS RÁPIDOS --}}
        <div class="pk-fade space-y-3">
            <h3 class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest px-1">Accesos rápidos</h3>
            <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('superadmin.loans.index', ['filter' => 'pending']) }}" class="pk-card flex flex-col items-center gap-2 p-4 hover:border-[#006BFF]/40 dark:hover:border-[#006BFF]/30 transition-colors">
                    <svg class="w-9 h-9 text-[#006BFF]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span class="text-[10px] font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Préstamos</span>
                </a>
                <a href="{{ route('superadmin.installments.index') }}" class="pk-card flex flex-col items-center gap-2 p-4 hover:border-orange-500/40 dark:hover:border-orange-500/30 transition-colors">
                    <svg class="w-9 h-9 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span class="text-[10px] font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Cobranzas</span>
                </a>
                <a href="{{ route('superadmin.investments.index') }}" class="pk-card flex flex-col items-center gap-2 p-4 hover:border-[#8247f5]/40 dark:hover:border-[#8247f5]/30 transition-colors">
                    <svg class="w-9 h-9 text-[#8247f5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span class="text-[10px] font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Inversiones</span>
                </a>
                <a href="{{ route('superadmin.investments.payouts') }}" class="pk-card flex flex-col items-center gap-2 p-4 hover:border-[#00D26A]/40 dark:hover:border-[#00D26A]/30 transition-colors">
                    <svg class="w-9 h-9 text-[#00D26A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span class="text-[10px] font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Pagos Inv.</span>
                </a>
            </div>

            {{-- PENDIENTES --}}
            <div class="pk-card p-4 space-y-2.5">
                <h4 class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">Pendientes por aprobar</h4>
                @if($stats['pending'] > 0)
                <a href="{{ route('superadmin.users.index') }}" class="flex items-center justify-between py-1 group">
                    <span class="text-xs text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">Usuarios</span>
                    <span class="text-xs font-black text-amber-500 tabular-nums">{{ $stats['pending'] }}</span>
                </a>
                @endif
                @if($loanStats['pending'] > 0)
                <a href="{{ route('superadmin.loans.index', ['filter' => 'pending']) }}" class="flex items-center justify-between py-1 group">
                    <span class="text-xs text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">Préstamos</span>
                    <span class="text-xs font-black text-amber-500 tabular-nums">{{ $loanStats['pending'] }}</span>
                </a>
                @endif
                @if($collectionStats['processing'] > 0)
                <a href="{{ route('superadmin.installments.index', ['filter' => 'processing']) }}" class="flex items-center justify-between py-1 group">
                    <span class="text-xs text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">Comprobantes</span>
                    <span class="text-xs font-black text-amber-500 tabular-nums">{{ $collectionStats['processing'] }}</span>
                </a>
                @endif
                @if($stats['pending'] === 0 && $loanStats['pending'] === 0 && $collectionStats['processing'] === 0)
                <div class="flex items-center gap-2 py-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#00D26A]"></span>
                    <p class="text-xs text-[#00D26A] font-medium">Todo al día</p>
                </div>
                @endif
            </div>
        </div>

        {{-- USUARIOS RECIENTES --}}
        <div class="pk-fade lg:col-span-2 pk-card overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                <div>
                    <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">Usuarios recientes</h3>
                    <p class="text-[9px] text-gray-400 dark:text-gray-500 font-bold">Últimos 5 registros</p>
                </div>
                <a href="{{ route('superadmin.users.index') }}" class="text-[10px] font-bold text-[#006BFF] dark:text-[#4A90E2] hover:underline">Ver todos</a>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-gray-800/50">
                @foreach($recentUsers as $user)
                    @php
                        $userSelfie = $user->documents?->selfie;
                        $userPhoto = $userSelfie ? Storage::url($userSelfie) : ($user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=E7EDF6&color=476788');
                    @endphp
                    <a href="{{ route('superadmin.users.show', $user) }}" class="pk-list-row flex items-center gap-3 px-5 py-3">
                        <img src="{{ $userPhoto }}" alt="{{ $user->name }}" class="w-9 h-9 rounded-full object-cover border-2 border-gray-100 dark:border-gray-800 flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ $user->name }}</p>
                            <p class="text-[9px] text-gray-400 dark:text-gray-500 truncate">{{ $user->email }}</p>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0">
                            @if($user->profile_completed)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-[#00D26A]/10 dark:bg-[#00D26A]/20 text-[#00D26A]">
                                    <span class="w-1 h-1 rounded-full bg-[#00D26A]"></span>Completo
                                </span>
                            @elseif($user->documents && $user->documents->status === 'pending')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-500/10 dark:bg-amber-500/20 text-amber-500">
                                    <span class="w-1 h-1 rounded-full bg-amber-500"></span>Pendiente
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400">
                                    <span class="w-1 h-1 rounded-full bg-gray-400"></span>Incompleto
                                </span>
                            @endif
                            <span class="text-[9px] text-gray-400 dark:text-gray-500 hidden sm:inline">{{ $user->created_at->diffForHumans() }}</span>
                            <svg class="w-4 h-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

</div>
</div>

@push('scripts')
<script>
(function(){
    function initPag(listId, pagId, itemClass, perPage){
        const list = document.getElementById(listId);
        const pag = document.getElementById(pagId);
        if(!list || !pag) return;
        const items = Array.from(list.querySelectorAll('.'+itemClass));
        if(items.length <= perPage) return;
        const totalPages = Math.ceil(items.length / perPage);
        let current = 1;
        function render(){
            items.forEach((el,i) => {
                el.style.display = (i >= (current-1)*perPage && i < current*perPage) ? '' : 'none';
            });
            pag.classList.remove('hidden');
            pag.innerHTML = '';
            const prevBtn = document.createElement('button');
            prevBtn.className = 'w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold transition-colors ' + (current === 1 ? 'text-gray-300 dark:text-gray-600 cursor-default' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5');
            prevBtn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>';
            prevBtn.disabled = current === 1;
            prevBtn.addEventListener('click', () => { if(current > 1){ current--; render(); }});
            pag.appendChild(prevBtn);
            for(let p = 1; p <= totalPages; p++){
                const btn = document.createElement('button');
                btn.className = 'w-7 h-7 rounded-lg flex items-center justify-center text-[10px] font-bold transition-colors ' + (p === current ? 'bg-[#006BFF] text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5');
                btn.textContent = p;
                btn.addEventListener('click', () => { current = p; render(); });
                pag.appendChild(btn);
            }
            const nextBtn = document.createElement('button');
            nextBtn.className = 'w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold transition-colors ' + (current === totalPages ? 'text-gray-300 dark:text-gray-600 cursor-default' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5');
            nextBtn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>';
            nextBtn.disabled = current === totalPages;
            nextBtn.addEventListener('click', () => { if(current < totalPages){ current++; render(); }});
            pag.appendChild(nextBtn);
        }
        render();
    }
    initPag('coll-list','coll-pag','coll-item',5);
    initPag('payout-list','payout-pag','payout-item',5);
})();
</script>
@endpush
@endsection