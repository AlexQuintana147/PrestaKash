@extends('superadmin.layout')

@section('title', 'Pagos a Inversores')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=JetBrains+Mono:wght@400;500;600&display=swap');

    .pk-font { font-family: 'DM Sans', sans-serif; }
    .pk-mono { font-family: 'JetBrains Mono', monospace; }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .pk-fade-up { animation: fadeUp 0.35s ease-out both; }

    @keyframes pulseDot {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }

    .pk-pulse-dot { animation: pulseDot 2s ease-in-out infinite; }

    .pk-detail-panel {
        display: none;
    }

    .pk-detail-panel.pk-open {
        display: block;
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-8px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .pk-chevron {
        transition: transform 0.25s ease;
    }

    .pk-chevron.pk-rotated {
        transform: rotate(180deg);
    }
</style>
<div class="min-h-screen px-5 md:px-8 lg:px-10 py-8 max-w-6xl mx-auto pk-font">

    {{-- HEADER --}}
    <div class="flex items-start justify-between mb-8 pk-fade-up" style="animation-delay:0ms">
        <div>
            <a href="{{ route('superadmin.investments.index') }}" class="inline-flex items-center gap-1.5 text-[11px] font-medium text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors mb-3 group">
                <svg class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Inversiones
            </a>
            <h1 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-none">Pagos a inversores</h1>
            <p class="text-[11px] text-gray-400 mt-1.5">Retornos mensuales pendientes de pago</p>
        </div>
        <p class="hidden md:block text-[11px] font-medium text-gray-400 pk-mono mt-1">{{ now()->format('d.m.Y') }}</p>
    </div>

    {{-- FLASH --}}
    @if(session('success'))
        <div class="mb-6 flex items-center gap-2.5 text-xs font-medium text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-500/8 p-3.5 rounded-xl border border-emerald-200 dark:border-emerald-500/20 pk-fade-up">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 flex items-center gap-2.5 text-xs font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-500/8 p-3.5 rounded-xl border border-red-200 dark:border-red-500/20 pk-fade-up">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            {{ session('error') }}
        </div>
    @endif

    @if($payouts->isEmpty())
        <div class="text-center py-20 pk-fade-up" style="animation-delay:80ms">
            <div class="w-16 h-16 mx-auto mb-5 rounded-2xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center">
                <svg class="w-7 h-7 text-gray-250 dark:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <p class="text-sm font-medium text-gray-400">No hay pagos pendientes</p>
            <p class="text-[11px] text-gray-300 dark:text-gray-600 mt-1">Aparecerán cuando una oportunidad cerrada alcance su fecha de vencimiento mensual</p>
        </div>
    @else
        @php
            $allPayouts = $payouts;
            $grouped = $payouts->groupBy(fn($p) => $p->opportunity->id);
            $totalPendingAmount = $payouts->sum('monthly_amount');
            $overdueCount = $payouts->where('is_overdue', true)->count();
            $overdueAmount = $payouts->where('is_overdue', true)->sum('monthly_amount');
            $currency = $payouts->first()->currency;
            $symbol = $currency === 'USD' ? '$' : 'S/';
        @endphp

        {{-- SUMMARY STATS --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2.5 mb-8 pk-fade-up" style="animation-delay:60ms">
            <div class="px-4 py-3 rounded-xl bg-white dark:bg-[#0c0c0f] border border-gray-100 dark:border-gray-850">
                <p class="text-[9px] uppercase tracking-widest font-semibold text-gray-400">Pagos</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white pk-mono mt-0.5">{{ $payouts->total() }}</p>
            </div>
            <div class="px-4 py-3 rounded-xl bg-white dark:bg-[#0c0c0f] border border-gray-100 dark:border-gray-850">
                <p class="text-[9px] uppercase tracking-widest font-semibold text-gray-400">Monto total</p>
                <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400 pk-mono mt-0.5">{{ $symbol }}{{ number_format($totalPendingAmount, 2) }}</p>
            </div>
            <div class="px-4 py-3 rounded-xl {{ $overdueCount > 0 ? 'bg-red-50 dark:bg-red-500/5 border border-red-100 dark:border-red-500/15' : 'bg-white dark:bg-[#0c0c0f] border border-gray-100 dark:border-gray-850' }}">
                <p class="text-[9px] uppercase tracking-widest font-semibold {{ $overdueCount > 0 ? 'text-red-500' : 'text-gray-400' }}">Vencidos</p>
                <p class="text-lg font-bold {{ $overdueCount > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }} pk-mono mt-0.5">{{ $overdueCount }}</p>
            </div>
            <div class="px-4 py-3 rounded-xl {{ $overdueAmount > 0 ? 'bg-red-50 dark:bg-red-500/5 border border-red-100 dark:border-red-500/15' : 'bg-white dark:bg-[#0c0c0f] border border-gray-100 dark:border-gray-850' }}">
                <p class="text-[9px] uppercase tracking-widest font-semibold {{ $overdueAmount > 0 ? 'text-red-500' : 'text-gray-400' }}">Monto vencido</p>
                <p class="text-lg font-bold {{ $overdueAmount > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }} pk-mono mt-0.5">{{ $symbol }}{{ number_format($overdueAmount, 2) }}</p>
            </div>
        </div>

        {{-- OPPORTUNITIES --}}
        @foreach($grouped as $opportunityId => $opportunityPayouts)
            @php
                $opportunity = $opportunityPayouts->first()->opportunity;
                $opportunityTotal = $opportunityPayouts->sum('monthly_amount');
                $opportunityOverdue = $opportunityPayouts->where('is_overdue', true)->count();
                $investorGrouped = $opportunityPayouts->groupBy(fn($p) => $p->investor->id);
            @endphp

            <section class="mb-8 pk-fade-up" style="animation-delay: {{ min($loop->index * 80 + 120, 500) }}ms">

                {{-- OPPORTUNITY HEADER --}}
                <div class="flex items-center justify-between mb-4 px-1">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="shrink-0 w-8 h-8 rounded-lg {{ $opportunityOverdue > 0 ? 'bg-red-50 dark:bg-red-500/10 ring-1 ring-inset ring-red-200 dark:ring-red-500/15' : 'bg-indigo-50 dark:bg-indigo-500/10 ring-1 ring-inset ring-indigo-100 dark:ring-indigo-500/15' }} flex items-center justify-center">
                            <span class="text-[10px] font-bold {{ $opportunityOverdue > 0 ? 'text-red-500' : 'text-indigo-500' }}">{{ $investorGrouped->count() }}</span>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <h2 class="text-[13px] font-semibold text-gray-900 dark:text-white tracking-tight truncate">{{ $opportunity->title }}</h2>
                                <span class="shrink-0 text-[9px] font-mono text-gray-400 bg-gray-100 dark:bg-gray-900 px-1.5 py-0.5 rounded">{{ $opportunity->code }}</span>
                            </div>
                            <div class="flex items-center gap-2 mt-0.5 text-[10px] text-gray-400">
                                <span>{{ $opportunity->monthly_return_rate }}% mensual</span>
                                <span class="text-gray-200 dark:text-gray-700">·</span>
                                <span>{{ $opportunity->term_months }} meses</span>
                                <span class="text-gray-200 dark:text-gray-700">·</span>
                                <span>Cerrada {{ $opportunity->closed_at->format('d/m/y') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="shrink-0 flex items-center gap-2">
                        <span class="text-[9px] font-bold uppercase tracking-wider px-2 py-1 rounded-md {{ $opportunity->currency === 'USD' ? 'bg-blue-50 dark:bg-blue-500/8 text-blue-600 dark:text-blue-400 ring-1 ring-inset ring-blue-100 dark:ring-blue-500/15' : 'bg-emerald-50 dark:bg-emerald-500/8 text-emerald-600 dark:text-emerald-400 ring-1 ring-inset ring-emerald-100 dark:ring-emerald-500/15' }}">{{ $opportunity->currency }}</span>
                        <span class="text-[11px] font-bold pk-mono {{ $opportunityOverdue > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">{{ $symbol }}{{ number_format($opportunityTotal, 2) }}</span>
                    </div>
                </div>

                {{-- INVESTOR CARDS --}}
                <div class="space-y-2">
                    @foreach($investorGrouped as $investorId => $investorPayouts)
                        @php
                            $investor = $investorPayouts->first()->investor;
                            $investorTotal = $investorPayouts->sum('monthly_amount');
                            $investorOverdue = $investorPayouts->where('is_overdue', true)->count();
                            $selfie = $investor->documents?->selfie;
                            $photo = $selfie ? Storage::url($selfie) : ($investor->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($investor->name) . '&background=e5e7eb&color=6b7280');
                            $uid = 'inv_' . $opportunityId . '_' . $investorId;
                        @endphp

                        <article class="bg-white dark:bg-[#0c0c0f] border border-gray-100 dark:border-gray-850 rounded-2xl overflow-hidden transition-all duration-200 hover:border-gray-200 dark:hover:border-gray-750">

                            {{-- CLICKABLE HEADER --}}
                            <button onclick="togglePanel('{{ $uid }}')" class="w-full text-left p-4 md:px-5 md:py-4 flex items-center justify-between cursor-pointer group focus:outline-none">
                                <div class="flex items-center gap-3 min-w-0">
                                    <img src="{{ $photo }}" alt="{{ $investor->name }}" class="w-9 h-9 rounded-xl object-cover ring-1 ring-inset ring-gray-100 dark:ring-gray-800 shrink-0">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-[13px] font-semibold text-gray-900 dark:text-white truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $investor->name }}</h3>
                                            @if($investorOverdue > 0)
                                                <span class="shrink-0 text-[8px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-red-100 dark:bg-red-500/15 text-red-600 dark:text-red-400">{{ $investorOverdue }} vencido{{ $investorOverdue !== 1 ? 's' : '' }}</span>
                                            @endif
                                        </div>
                                        <p class="text-[10px] text-gray-400 truncate">{{ $investor->email }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4">
                                    {{-- COMPACT SUMMARY --}}
                                    <div class="hidden sm:flex items-center gap-4">
                                        <div class="text-right">
                                            <p class="text-[8px] uppercase tracking-widest font-semibold text-gray-400">Invertido</p>
                                            <p class="text-[12px] font-bold text-gray-900 dark:text-white pk-mono">{{ $symbol }}{{ number_format($investor->pivot->amount, 2) }}</p>
                                        </div>
                                        <div class="w-px h-7 bg-gray-100 dark:bg-gray-850"></div>
                                        <div class="text-right">
                                            <p class="text-[8px] uppercase tracking-widest font-semibold text-gray-400">Retorno/mes</p>
                                            <p class="text-[12px] font-bold text-emerald-600 dark:text-emerald-400 pk-mono">{{ $symbol }}{{ number_format($investorPayouts->first()->monthly_amount, 2) }}</p>
                                        </div>
                                        <div class="w-px h-7 bg-gray-100 dark:bg-gray-850"></div>
                                        <div class="text-right">
                                            <p class="text-[8px] uppercase tracking-widest font-semibold text-gray-400">A pagar</p>
                                            <p class="text-[12px] font-bold {{ $investorOverdue > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }} pk-mono">{{ $symbol }}{{ number_format($investorTotal, 2) }}</p>
                                        </div>
                                    </div>

                                    {{-- CHEVRON --}}
                                    <span id="chevron_{{ $uid }}" class="pk-chevron text-gray-300 dark:text-gray-600 group-hover:text-gray-500 dark:group-hover:text-gray-400 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </span>
                                </div>
                            </button>

                            {{-- MOBILE COMPACT SUMMARY --}}
                            <div class="sm:hidden px-4 pb-3 -mt-1 grid grid-cols-3 gap-3">
                                <div>
                                    <p class="text-[8px] uppercase tracking-widest font-semibold text-gray-400">Invertido</p>
                                    <p class="text-[11px] font-bold text-gray-900 dark:text-white pk-mono">{{ $symbol }}{{ number_format($investor->pivot->amount, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-[8px] uppercase tracking-widest font-semibold text-gray-400">Retorno/mes</p>
                                    <p class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 pk-mono">{{ $symbol }}{{ number_format($investorPayouts->first()->monthly_amount, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-[8px] uppercase tracking-widest font-semibold text-gray-400">A pagar</p>
                                    <p class="text-[11px] font-bold {{ $investorOverdue > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }} pk-mono">{{ $symbol }}{{ number_format($investorTotal, 2) }}</p>
                                </div>
                            </div>

                            {{-- EXPANDABLE DETAIL PANEL --}}
                            <div id="panel_{{ $uid }}" class="pk-detail-panel">
                                <div class="border-t border-gray-100 dark:border-gray-850">

                                    {{-- DETAIL STATS --}}
                                    <div class="px-4 md:px-5 py-4 grid grid-cols-2 md:grid-cols-5 gap-4 bg-gray-50/40 dark:bg-gray-900/20">
                                        <div>
                                            <p class="text-[8px] uppercase tracking-widest font-semibold text-gray-400">Invertido</p>
                                            <p class="text-sm font-bold text-gray-900 dark:text-white pk-mono">{{ $symbol }}{{ number_format($investor->pivot->amount, 2) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[8px] uppercase tracking-widest font-semibold text-gray-400">Retorno/mes</p>
                                            <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400 pk-mono">{{ $symbol }}{{ number_format($investorPayouts->first()->monthly_amount, 2) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[8px] uppercase tracking-widest font-semibold text-gray-400">Total retorno</p>
                                            <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400 pk-mono">{{ $symbol }}{{ number_format($investorPayouts->first()->total_return, 2) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[8px] uppercase tracking-widest font-semibold text-gray-400">% del total</p>
                                            <p class="text-sm font-bold text-gray-900 dark:text-white pk-mono">{{ $investorPayouts->first()->percentage_of_total }}%</p>
                                        </div>
                                        <div>
                                            <p class="text-[8px] uppercase tracking-widest font-semibold text-gray-400">Cuotas</p>
                                            <p class="text-sm font-bold {{ $investorOverdue > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">{{ $investorPayouts->count() }} <span class="font-normal text-gray-400 text-[10px]">@if($investorOverdue > 0)({{ $investorOverdue }} vencida{{ $investorOverdue !== 1 ? 's' : '' }})@endif</span></p>
                                        </div>
                                    </div>

                                    {{-- MONTHLY PAYMENTS TABLE --}}
                                    <div class="border-t border-gray-100 dark:border-gray-850">
                                        <div class="px-4 md:px-5 py-2 bg-gray-50/60 dark:bg-gray-900/40 flex items-center gap-4 text-[8px] uppercase tracking-widest font-semibold text-gray-400">
                                            <span class="w-16">Cuota</span>
                                            <span class="flex-1">Vencimiento</span>
                                            <span class="w-20 text-right">Monto</span>
                                            <span class="w-16 text-right">Estado</span>
                                        </div>

                                        @foreach($investorPayouts as $payout)
                                            <div class="px-4 md:px-5 py-2.5 flex items-center gap-4 border-t border-dashed border-gray-50 dark:border-gray-900/60 hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-colors {{ $payout->is_overdue ? 'bg-red-50/40 dark:bg-red-500/3' : '' }}">
                                                <span class="w-16 text-[11px] font-semibold pk-mono {{ $payout->is_overdue ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400' }}">
                                                    {{ str_pad($payout->month, 2, '0', STR_PAD_LEFT) }}/{{ str_pad($payout->total_months, 2, '0', STR_PAD_LEFT) }}
                                                </span>
                                                <span class="flex-1 text-[11px] font-medium pk-mono {{ $payout->is_overdue ? 'text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-400' }}">
                                                    {{ $payout->due_date->format('d/m/Y') }}
                                                </span>
                                                <span class="w-20 text-right text-[11px] font-bold pk-mono {{ $payout->is_overdue ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                                    {{ $payout->currency === 'USD' ? '$' : 'S/' }}{{ number_format($payout->monthly_amount, 2) }}
                                                </span>
                                                <span class="w-16 text-right">
                                                    @if($payout->is_overdue)
                                                        <span class="inline-flex items-center gap-1 text-[8px] font-bold uppercase tracking-wider text-red-600 dark:text-red-400">
                                                            <span class="w-1 h-1 rounded-full bg-red-500 pk-pulse-dot"></span>
                                                            Vencido
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 text-[8px] font-semibold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                                                            <span class="w-1 h-1 rounded-full bg-amber-400"></span>
                                                            Pendiente
                                                        </span>
                                                    @endif
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>

                                    {{-- FOOTER --}}
                                    <div class="px-4 md:px-5 py-3 border-t border-gray-100 dark:border-gray-850 bg-gray-50/40 dark:bg-gray-900/20 flex items-center justify-between">
                                        <span class="text-[10px] text-gray-400">{{ $investorPayouts->count() }} cuota{{ $investorPayouts->count() !== 1 ? 's' : '' }} pendiente{{ $investorPayouts->count() !== 1 ? 's' : '' }}</span>
                                        <span class="text-[11px] font-bold pk-mono {{ $investorOverdue > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">{{ $symbol }}{{ number_format($investorTotal, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endforeach

        {{-- PAGINATION --}}
        @if(method_exists($payouts, 'links'))
            <div class="mt-8 flex justify-center pk-fade-up">
                {{ $payouts->withQueryString()->links() }}
            </div>
        @endif
    @endif
</div>

<script>
function togglePanel(uid) {
    const panel = document.getElementById('panel_' + uid);
    const chevron = document.getElementById('chevron_' + uid);
    if (!panel || !chevron) return;

    const isOpen = panel.classList.contains('pk-open');

    if (isOpen) {
        panel.classList.remove('pk-open');
        chevron.classList.remove('pk-rotated');
    } else {
        panel.classList.add('pk-open');
        chevron.classList.add('pk-rotated');
    }
}
</script>
@endsection