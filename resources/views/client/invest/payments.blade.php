@extends('client.layout')

@section('title', 'Mis Pagos')

@section('content')
<div class="min-h-screen px-4 sm:px-6 lg:px-8 py-6 sm:py-10 max-w-5xl mx-auto font-sans">

    <div class="mb-8 sm:mb-12">
        <a href="{{ route('client.investments.index') }}" class="inline-flex items-center gap-1.5 text-xs text-gray-400 hover:text-gray-900 dark:hover:text-white transition-all duration-300 group mb-4">
            <span class="group-hover:-translate-x-1 transition-transform duration-300">←</span>
            <span class="border-b border-gray-300 dark:border-gray-600 group-hover:border-gray-900 dark:group-hover:border-white transition-colors">Volver a Mis Inversiones</span>
        </a>
        
        <div class="space-y-2">
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-light text-gray-900 dark:text-white tracking-tight">
                Mis Pagos
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 font-light">
                Seguimiento detallado de tus inversiones activas
            </p>
        </div>
    </div>

    @if($investments->isEmpty())
        <div class="text-center py-20 sm:py-32 bg-gradient-to-b from-gray-50 to-white dark:from-gray-900/50 dark:to-gray-950 rounded-3xl border border-gray-100 dark:border-gray-800/50">
            <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <p class="text-base font-medium text-gray-900 dark:text-white mb-2">Sin inversiones confirmadas</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 max-w-md mx-auto">Aún no tienes inversiones con pago confirmado. Explora las oportunidades disponibles.</p>
            <a href="{{ route('client.invertir') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-full text-sm font-medium hover:scale-105 transition-transform duration-300 shadow-lg hover:shadow-xl">
                Explorar oportunidades
                <span>→</span>
            </a>
        </div>
    @else
        <div class="space-y-4 sm:space-y-6">
            @foreach($investments as $inv)
                @php
                    $opp = $inv;
                    $isClosed = in_array($opp->status, ['closed', 'cancelled']);
                    $isFunded = $opp->status === 'funded';
                    $invPayments = $payments[$opp->id] ?? collect();
                    $totalReceived = $invPayments->where('type', 'expense')->sum('amount');
                    $currencySymbol = $opp->currency === 'USD' ? '$' : 'S/';
                    $currencyLabel = $opp->currency === 'USD' ? 'USD' : 'PEN';
                    $totalExpected = $inv->pivot->amount * (1 + ($opp->monthly_return_rate * $opp->term_months / 100));
                    $pendingAmount = max(0, $totalExpected - $totalReceived);
                    $paymentCount = $invPayments->count();
                    $expectedPayments = $opp->term_months;
                @endphp

                <div class="investment-card group bg-white dark:bg-gray-950 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-gray-800/50 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-500" data-investment-id="{{ $opp->id }}">
                    
                    <div class="card-header cursor-pointer p-5 sm:p-6 lg:p-8" onclick="toggleInvestment({{ $opp->id }})">
                        <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4 lg:gap-6">
                            
                            <div class="flex-1 min-w-0 space-y-3 sm:space-y-4">
                                <div class="flex items-center gap-3 flex-wrap">
                                    <span class="text-[10px] sm:text-xs font-mono text-gray-400 dark:text-gray-500 tracking-wide">{{ $opp->code }}</span>
                                    <span class="px-2.5 py-1 rounded-full text-[9px] sm:text-[10px] font-semibold uppercase tracking-wider
                                        {{ $isClosed ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 ring-1 ring-emerald-200 dark:ring-emerald-500/20' : ($isFunded ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 ring-1 ring-blue-200 dark:ring-blue-500/20' : 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 ring-1 ring-amber-200 dark:ring-amber-500/20') }}">
                                        {{ $isClosed ? 'Cerrada' : ($isFunded ? 'Financiada' : 'Abierta') }}
                                    </span>
                                    <span class="px-2.5 py-1 rounded-full text-[9px] sm:text-[10px] font-bold uppercase tracking-wider bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 ring-1 ring-gray-200 dark:ring-gray-700">
                                        {{ $currencyLabel }}
                                    </span>
                                </div>

                                <h2 class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-900 dark:text-white leading-tight group-hover:text-gray-700 dark:group-hover:text-gray-200 transition-colors">
                                    {{ $opp->title }}
                                </h2>

                                <div class="flex items-center gap-3 sm:gap-4 text-xs text-gray-500 dark:text-gray-400 font-light flex-wrap">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $opp->term_months }} meses
                                    </span>
                                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                        </svg>
                                        {{ $opp->monthly_return_rate }}%/mes
                                    </span>
                                    @if($inv->pivot->approved_at)
                                        <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                                        <span class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            Desde {{ \Carbon\Carbon::parse($inv->pivot->approved_at)->format('d/m/Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="lg:text-right space-y-3 sm:space-y-4 flex-shrink-0">
                                <div class="inline-flex flex-col lg:items-end">
                                    <span class="text-[10px] uppercase tracking-widest text-gray-400 dark:text-gray-500 font-medium mb-1">Invertido</span>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-2xl sm:text-3xl lg:text-4xl font-light text-gray-900 dark:text-white">{{ $currencySymbol }}</span>
                                        <span class="text-2xl sm:text-3xl lg:text-4xl font-semibold text-gray-900 dark:text-white">{{ number_format($inv->pivot->amount, 0) }}</span>
                                        <span class="text-sm text-gray-400">{{ number_format($inv->pivot->amount - floor($inv->pivot->amount), 2) }}</span>
                                    </div>
                                </div>

                                <button class="toggle-btn inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-medium text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-300 group/btn">
                                    <span class="btn-text">Ver detalles</span>
                                    <svg class="w-4 h-4 transform transition-transform duration-300 group-hover/btn:translate-x-0.5 arrow-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        @if(!$isClosed)
                            <div class="mt-4 sm:mt-6 p-4 rounded-xl bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-500/5 dark:to-orange-500/5 border border-amber-100/50 dark:border-amber-500/10">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center mt-0.5">
                                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-amber-800 dark:text-amber-300">En proceso</p>
                                        <p class="text-xs text-amber-600/80 dark:text-amber-400/70 mt-0.5 leading-relaxed">
                                            Esta oportunidad está {{ $isFunded ? 'siendo financiada' : 'abierta para recibir inversiones' }}. Los pagos comenzarán al cerrarse.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="mt-4 sm:mt-6 grid grid-cols-3 gap-3 sm:gap-4">
                            <div class="text-center p-3 sm:p-4 rounded-xl bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-800/30">
                                <p class="text-[9px] uppercase tracking-widest text-gray-400 dark:text-gray-500 font-semibold mb-1">Cuotas</p>
                                <p class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">{{ $paymentCount }}<span class="text-gray-400 font-normal">/{{ $expectedPayments }}</span></p>
                            </div>
                            <div class="text-center p-3 sm:p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/5 border border-emerald-100 dark:border-emerald-500/10">
                                <p class="text-[9px] uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-semibold mb-1">Recibido</p>
                                <p class="text-base sm:text-lg font-semibold text-emerald-700 dark:text-emerald-300">{{ $currencySymbol }}{{ number_format($totalReceived, 0) }}</p>
                            </div>
                            <div class="text-center p-3 sm:p-4 rounded-xl bg-amber-50 dark:bg-amber-500/5 border border-amber-100 dark:border-amber-500/10">
                                <p class="text-[9px] uppercase tracking-widest text-amber-600 dark:text-amber-400 font-semibold mb-1">Pendiente</p>
                                <p class="text-base sm:text-lg font-semibold text-amber-700 dark:text-amber-300">{{ $currencySymbol }}{{ number_format($pendingAmount, 0) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="card-details hidden border-t border-gray-100 dark:border-gray-800/50 bg-gradient-to-b from-gray-50/50 to-white dark:from-gray-900/30 dark:to-gray-950">
                        <div class="p-5 sm:p-6 lg:p-8 space-y-6">
                            
                            <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-800">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                    </svg>
                                    Cronograma de Pagos
                                </h3>
                                <span class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $currencyLabel }}</span>
                            </div>

                            @if($invPayments->isEmpty())
                                @if($isClosed)
                                    <div class="text-center py-12 bg-white dark:bg-gray-900/50 rounded-2xl border border-dashed border-gray-200 dark:border-gray-800">
                                        <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Sin pagos registrados</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Esta inversión aún no tiene movimientos.</p>
                                    </div>
                                @else
                                    <div class="text-center py-12 bg-white dark:bg-gray-900/50 rounded-2xl border border-dashed border-gray-200 dark:border-gray-800">
                                        <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Esperando inicio</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Los pagos aparecerán cuando se cierre la oportunidad.</p>
                                    </div>
                                @endif
                            @else
                                <div class="space-y-3">
                                    @foreach($invPayments as $index => $payment)
                                        <div class="payment-item group/item flex items-center justify-between p-4 rounded-xl bg-white dark:bg-gray-900/80 border border-gray-100 dark:border-gray-800/50 hover:border-gray-200 dark:hover:border-gray-700 hover:shadow-md transition-all duration-300">
                                            
                                            <div class="flex items-center gap-4 flex-1 min-w-0">
                                                <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold
                                                    {{ $payment->type === 'expense' ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400' }}">
                                                    {{ $index + 1 }}
                                                </div>
                                                
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Cuota {{ $index + 1 }} de {{ $expectedPayments }}</p>
                                                    <div class="flex items-center gap-2 mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                        <span class="font-mono">{{ $payment->date ? \Carbon\Carbon::parse($payment->date)->format('d/m/Y') : '—' }}</span>
                                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-semibold uppercase tracking-wider
                                                            {{ $payment->type === 'expense' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' }}">
                                                            {{ $payment->type === 'expense' ? 'Recibida' : 'Pendiente' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex-shrink-0 ml-4 text-right">
                                                <p class="text-base font-semibold {{ $payment->type === 'expense' ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-gray-400' }}">
                                                    {{ $currencySymbol }}{{ number_format($payment->amount, 2) }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-800">
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                        <div class="sm:col-span-2 p-5 rounded-2xl bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800/50 border border-gray-200 dark:border-gray-700/50">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wider mb-1">Total Recibido</p>
                                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $currencySymbol }}{{ number_format($totalReceived, 2) }}</p>
                                                </div>
                                                <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="p-5 rounded-2xl bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-500/10 dark:to-orange-500/5 border border-amber-200 dark:border-amber-500/20">
                                            <div class="text-center">
                                                <p class="text-xs text-amber-600 dark:text-amber-400 font-medium uppercase tracking-wider mb-1">Por Recuperar</p>
                                                <p class="text-xl font-bold text-amber-700 dark:text-amber-300">{{ $currencySymbol }}{{ number_format($pendingAmount, 0) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
.investment-card {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.investment-card:hover {
    transform: translateY(-2px);
}

.investment-card .card-details {
    max-height: 0;
    opacity: 0;
    overflow: hidden;
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.investment-card.expanded .card-details {
    max-height: 2000px;
    opacity: 1;
}

.investment-card.expanded .arrow-icon {
    transform: rotate(180deg);
}

.investment-card.expanded .btn-text {
    content: 'Ocultar detalles';
}

.payment-item {
    animation: slideIn 0.3s ease-out backwards;
}

.payment-item:nth-child(1) { animation-delay: 0.05s; }
.payment-item:nth-child(2) { animation-delay: 0.1s; }
.payment-item:nth-child(3) { animation-delay: 0.15s; }
.payment-item:nth-child(4) { animation-delay: 0.2s; }
.payment-item:nth-child(5) { animation-delay: 0.25s; }
.payment-item:nth-child(6) { animation-delay: 0.3s; }

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-10px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}
</style>

<script>
function toggleInvestment(id) {
    const card = document.querySelector(`[data-investment-id="${id}"]`);
    if (!card) return;

    const isExpanded = card.classList.contains('expanded');
    
    document.querySelectorAll('.investment-card.expanded').forEach(c => {
        if (c !== card) {
            c.classList.remove('expanded');
            c.querySelector('.btn-text').textContent = 'Ver detalles';
        }
    });

    if (isExpanded) {
        card.classList.remove('expanded');
        card.querySelector('.btn-text').textContent = 'Ver detalles';
    } else {
        card.classList.add('expanded');
        card.querySelector('.btn-text').textContent = 'Ocultar detalles';
        
        setTimeout(() => {
            const details = card.querySelector('.card-details');
            if (details) {
                details.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }, 100);
    }
}
</script>
@endsection