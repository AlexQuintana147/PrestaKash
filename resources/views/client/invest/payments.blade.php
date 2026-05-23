@extends('client.layout')

@section('title', 'Mis Pagos')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8 max-w-5xl">

    <div class="mb-8">
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('client.investments.index') }}" class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">Invertir</a>
            <svg class="w-3 h-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </div>
        <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-0.5">Pagos</p>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Mis Pagos</h1>
    </div>

    @if(session('success'))
        <div class="mb-6 flex items-center gap-2 text-sm text-green-500 font-bold bg-green-50 dark:bg-green-500/10 p-4 rounded-2xl border border-green-100 dark:border-green-500/20">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 flex items-center gap-2 text-sm text-red-500 font-bold bg-red-50 dark:bg-red-500/10 p-4 rounded-2xl border border-red-100 dark:border-red-500/20">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    @if($investments->isEmpty())
        <div class="text-center py-20">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center">
                <svg class="w-7 h-7 text-gray-300 dark:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <p class="text-sm font-bold text-gray-900 dark:text-white">Sin inversiones confirmadas</p>
            <p class="text-xs text-gray-400 mt-1">Tus pagos aparecerán cuando confirmen tu inversión.</p>
            <a href="{{ route('client.investments.index') }}" class="mt-4 inline-block text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline tracking-wide">EXPLORAR OPORTUNIDADES →</a>
        </div>
    @else
        <div class="space-y-3">
            @foreach($investments as $inv)
                @php
                    $opp = $inv;
                    $isClosed = in_array($opp->status, ['closed', 'cancelled']);
                    $isFunded = $opp->status === 'funded';
                    $invPayments = $payments[$opp->id] ?? collect();
                    $cashboxReceived = $invPayments->where('type', 'expense')->sum('amount');
                    $confirmedPayoutTotal = $confirmedPayoutsByOpportunity[$opp->id] ?? 0;
                    $totalReceived = max($cashboxReceived, $confirmedPayoutTotal);
                    $currencySymbol = $opp->currency === 'USD' ? '$' : 'S/';
                    $extraMonths = $interestOnlyByOpportunity[$opp->id] ?? 0;
                    $effectiveTermMonths = $opp->term_months + $extraMonths;
                    $totalExpected = $inv->pivot->amount * (1 + ($opp->monthly_return_rate * $effectiveTermMonths / 100));
                    $pendingAmount = max(0, $totalExpected - $totalReceived);
                    $progressPercent = $totalExpected > 0 ? min(100, round(($totalReceived / $totalExpected) * 100)) : 0;
                    $hasPendingPayout = ($pendingPayoutsByOpportunity[$opp->id] ?? 0) > 0;
                    $pendingCount = $pendingPayoutsByOpportunity[$opp->id] ?? 0;
                    $lastPayment = $invPayments->where('type', 'expense')->sortByDesc('date')->first();
                    $paidQuotas = 0;
                    foreach ($invPayments->where('type', 'expense') as $p) {
                        $paidQuotas++;
                    }
                    $payoutPaidQuotas = $paidQuotasByOpportunity[$opp->id] ?? 0;
                    $totalPaidQuotas = max($paidQuotas, $payoutPaidQuotas);
                @endphp

                <a href="{{ route('client.investments.payment-detail', $opp) }}" class="block group">
                    <div class="relative bg-white dark:bg-gray-950 rounded-2xl border {{ $hasPendingPayout ? 'border-amber-300 dark:border-amber-500/30 bg-amber-50/50 dark:bg-amber-500/5' : 'border-gray-100 dark:border-gray-800/60' }} p-5 hover:border-indigo-200 dark:hover:border-indigo-500/30 hover:shadow-lg hover:shadow-indigo-500/5 transition-all duration-300 overflow-hidden">
                        @if($hasPendingPayout)
                            <div class="absolute top-0 left-0 w-full h-0.5 bg-gradient-to-r from-amber-400 to-orange-400"></div>
                        @else
                            <div class="absolute top-0 left-0 w-full h-0.5 bg-gray-100 dark:bg-gray-800">
                                <div class="h-full bg-gradient-to-r from-indigo-500 to-emerald-400 transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
                            </div>
                        @endif

                        <div class="flex items-center justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                                    <span class="text-[9px] font-mono text-gray-400">{{ $opp->code }}</span>
                                    <span class="px-1.5 py-0.5 rounded text-[8px] font-bold uppercase tracking-wider
                                        {{ $isClosed ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : ($isFunded ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400') }}">
                                        {{ $isClosed ? 'Cerrada' : ($isFunded ? 'Financiada' : 'Abierta') }}
                                    </span>
                                    @if($hasPendingPayout)
                                        <span class="px-1.5 py-0.5 rounded text-[8px] font-bold uppercase tracking-wider bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400 animate-pulse">
                                            ⚠ {{ $pendingCount }} pago{{ $pendingCount > 1 ? 's' : '' }} por confirmar
                                        </span>
                                    @endif
                                </div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $opp->title }}</h3>
                                <div class="flex items-center gap-3 mt-1.5 text-[10px] text-gray-400">
                                    <span>{{ $opp->monthly_return_rate }}%/mes</span>
                                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                                    <span>{{ $effectiveTermMonths }} meses</span>
                                    @if($lastPayment)
                                        <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                                        <span>Último pago {{ \Carbon\Carbon::parse($lastPayment->date)->format('d/m/Y') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-6 shrink-0">
                                <div class="text-right hidden sm:block">
                                    <p class="text-[9px] text-gray-400 uppercase tracking-wider font-semibold">Invertido</p>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">{{ $currencySymbol }}{{ number_format($inv->pivot->amount, 0) }}</p>
                                </div>
                                <div class="text-right hidden sm:block">
                                    <p class="text-[9px] text-gray-400 uppercase tracking-wider font-semibold">Recibido</p>
                                    <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">{{ $currencySymbol }}{{ number_format($totalReceived, 0) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[9px] text-gray-400 uppercase tracking-wider font-semibold">Cuotas</p>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">{{ $totalPaidQuotas }}<span class="text-gray-400 font-normal">/{{ $effectiveTermMonths }}</span></p>
                                </div>
                                <div class="text-right w-16">
                                    <p class="text-[9px] text-gray-400 uppercase tracking-wider font-semibold">Progreso</p>
                                    <p class="text-sm font-bold {{ $progressPercent >= 100 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-900 dark:text-white' }}">{{ $progressPercent }}%</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-300 dark:text-gray-700 group-hover:text-indigo-400 dark:group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </div>

                        <div class="mt-3 h-1.5 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500 {{ $progressPercent >= 100 ? 'bg-emerald-500' : 'bg-gradient-to-r from-indigo-500 to-emerald-400' }}" style="width: {{ $progressPercent }}%"></div>
                        </div>

                        <div class="mt-2 flex items-center justify-between text-[10px] text-gray-400">
                            <span>{{ $currencySymbol }}{{ number_format($totalReceived, 0) }} recibido</span>
                            <span>{{ $currencySymbol }}{{ number_format($pendingAmount, 0) }} pendiente</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
