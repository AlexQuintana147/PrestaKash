@extends('superadmin.layout')

@section('title', 'Pagos a Inversores')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8 max-w-6xl mx-auto">

    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Pagos a Inversores</h1>
        <p class="text-xs text-gray-400 mt-1">Retornos mensuales pendientes de pago — solo inversiones cerradas con pagos confirmados</p>
    </div>

    @if(session('success'))
        <div class="mb-6 flex items-center gap-2 text-sm text-green-600 font-bold bg-green-50 dark:bg-green-500/10 p-4 rounded-2xl border border-green-100 dark:border-green-500/20">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 flex items-center gap-2 text-sm text-red-600 font-bold bg-red-50 dark:bg-red-500/10 p-4 rounded-2xl border border-red-100 dark:border-red-500/20">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            {{ session('error') }}
        </div>
    @endif

    @if($payouts->isEmpty())
        <div class="text-center py-16">
            <svg class="w-12 h-12 text-gray-200 dark:text-gray-800 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <p class="text-sm text-gray-400 font-medium">No hay pagos pendientes</p>
            <p class="text-[10px] text-gray-300 mt-1">Los pagos aparecerán cuando una oportunidad cerrada alcance su fecha de vencimiento mensual</p>
        </div>
    @else
        @php
            $grouped = $payouts->groupBy(fn($p) => $p->opportunity->id);
            $totalPendingAmount = $payouts->sum('monthly_amount');
            $overdueCount = $payouts->where('is_overdue', true)->count();
        @endphp

        <div class="grid grid-cols-3 gap-3 mb-6">
            <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-xl p-4">
                <p class="text-[8px] text-gray-400 uppercase font-bold tracking-tighter">Total Pagos Pendientes</p>
                <p class="text-base font-black text-gray-900 dark:text-white mt-1">{{ $payouts->count() }}</p>
            </div>
            <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-xl p-4">
                <p class="text-[8px] text-gray-400 uppercase font-bold tracking-tighter">Monto Total a Pagar</p>
                <p class="text-base font-black text-green-600 dark:text-green-400 mt-1">{{ $payouts->first()->currency === 'USD' ? '$' : 'S/' }} {{ number_format($totalPendingAmount, 2) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-xl p-4">
                <p class="text-[8px] text-gray-400 uppercase font-bold tracking-tighter">Vencidos</p>
                <p class="text-base font-black {{ $overdueCount > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }} mt-1">{{ $overdueCount }}</p>
            </div>
        </div>

        <div class="space-y-5">
            @foreach($grouped as $opportunityId => $opportunityPayouts)
                @php
                    $opportunity = $opportunityPayouts->first()->opportunity;
                    $opportunityTotal = $opportunityPayouts->sum('monthly_amount');
                    $uniqueInvestors = $opportunityPayouts->unique(fn($p) => $p->investor->id);
                @endphp

                <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl overflow-hidden shadow-sm">

                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-900 bg-gray-50/50 dark:bg-gray-900/30">
                        <div class="flex flex-col md:flex-row md:items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-500/10 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-sm font-bold text-gray-900 dark:text-white">{{ $opportunity->title }}</h2>
                                    <p class="text-[9px] font-mono text-gray-400 mt-0.5">{{ $opportunity->code }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-[8px] font-black uppercase tracking-wider px-2 py-1 rounded-lg bg-gray-800 text-white dark:bg-gray-700 dark:text-gray-200">
                                    {{ $opportunity->currency }}
                                </span>
                                <span class="text-[8px] font-bold px-2 py-1 rounded-lg bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400">
                                    {{ $uniqueInvestors->count() }} inversor{{ $uniqueInvestors->count() !== 1 ? 'es' : '' }}
                                </span>
                                <span class="text-[8px] font-black uppercase tracking-wider px-2.5 py-1 rounded-lg
                                    {{ $opportunityPayouts->contains('is_overdue', true) ? 'bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-400' : 'bg-green-100 text-green-600 dark:bg-green-500/10 dark:text-green-400' }}">
                                    {{ $opportunityPayouts->count() }} pago{{ $opportunityPayouts->count() !== 1 ? 's' : '' }} · {{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format($opportunityTotal, 2) }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-3 grid grid-cols-2 md:grid-cols-5 gap-x-4 gap-y-1.5 text-xs pl-13">
                            <div>
                                <p class="text-[7px] text-gray-400 uppercase font-bold tracking-tighter">Cerrada</p>
                                <p class="font-semibold text-gray-700 dark:text-gray-300">{{ $opportunity->closed_at->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <p class="text-[7px] text-gray-400 uppercase font-bold tracking-tighter">Tasa Mensual</p>
                                <p class="font-semibold text-indigo-600 dark:text-indigo-400">{{ $opportunity->monthly_return_rate }}%</p>
                            </div>
                            <div>
                                <p class="text-[7px] text-gray-400 uppercase font-bold tracking-tighter">Plazo</p>
                                <p class="font-semibold text-gray-700 dark:text-gray-300">{{ $opportunity->term_months }} meses</p>
                            </div>
                            <div>
                                <p class="text-[7px] text-gray-400 uppercase font-bold tracking-tighter">Retorno Estimado</p>
                                <p class="font-semibold text-green-600 dark:text-green-400">{{ $opportunity->estimated_return }}%</p>
                            </div>
                            <div>
                                <p class="text-[7px] text-gray-400 uppercase font-bold tracking-tighter">Monto Objetivo</p>
                                <p class="font-semibold text-gray-700 dark:text-gray-300">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($opportunity->target_amount, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="divide-y divide-gray-50 dark:divide-gray-900/50">
                        @php
                            $investorGrouped = $opportunityPayouts->groupBy(fn($p) => $p->investor->id);
                        @endphp

                        @foreach($investorGrouped as $investorId => $investorPayouts)
                            @php
                                $investor = $investorPayouts->first()->investor;
                                $investorTotal = $investorPayouts->sum('monthly_amount');
                                $investorOverdue = $investorPayouts->where('is_overdue', true)->count();
                            @endphp

                            <div class="{{ $loop->last ? '' : 'border-b border-gray-50 dark:border-gray-900/50' }}">
                                <div class="px-5 py-4">
                                    <div class="flex items-start gap-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-11 h-11 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center shadow-md">
                                                <span class="text-sm font-black text-white">{{ strtoupper(substr($investor->name, 0, 1)) }}</span>
                                            </div>
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">{{ $investor->name }}</h3>
                                                @if($investorOverdue > 0)
                                                    <span class="text-[8px] font-black uppercase tracking-wider bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-400 px-1.5 py-0.5 rounded">{{ $investorOverdue }} vencido{{ $investorOverdue !== 1 ? 's' : '' }}</span>
                                                @endif
                                            </div>

                                            <p class="text-[10px] text-gray-400 mt-0.5">{{ $investor->email }}</p>

                                            <div class="mt-3 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-x-4 gap-y-2 text-xs">
                                                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-2.5">
                                                    <p class="text-[7px] text-gray-400 uppercase font-bold tracking-tighter">Invertido</p>
                                                    <p class="font-bold text-gray-900 dark:text-white mt-0.5">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($investor->pivot->amount, 2) }}</p>
                                                </div>
                                                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-2.5">
                                                    <p class="text-[7px] text-gray-400 uppercase font-bold tracking-tighter">% de Inversión</p>
                                                    <p class="font-bold text-indigo-600 dark:text-indigo-400 mt-0.5">{{ $investorPayouts->first()->percentage_of_total }}%</p>
                                                </div>
                                                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-2.5">
                                                    <p class="text-[7px] text-gray-400 uppercase font-bold tracking-tighter">Retorno/Mes</p>
                                                    <p class="font-bold text-green-600 dark:text-green-400 mt-0.5">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($investorPayouts->first()->monthly_amount, 2) }}</p>
                                                </div>
                                                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-2.5">
                                                    <p class="text-[7px] text-gray-400 uppercase font-bold tracking-tighter">Retorno Total</p>
                                                    <p class="font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($investorPayouts->first()->total_return, 2) }}</p>
                                                </div>
                                                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-2.5">
                                                    <p class="text-[7px] text-gray-400 uppercase font-bold tracking-tighter">Pago Aprobado</p>
                                                    <p class="font-semibold text-gray-700 dark:text-gray-300 mt-0.5">{{ $investor->pivot->approved_at ? \Carbon\Carbon::parse($investor->pivot->approved_at)->format('d/m/Y') : '—' }}</p>
                                                </div>
                                                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-2.5">
                                                    <p class="text-[7px] text-gray-400 uppercase font-bold tracking-tighter">A Pagar Ahora</p>
                                                    <p class="font-black text-gray-900 dark:text-white mt-0.5">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($investorTotal, 2) }}</p>
                                                </div>
                                            </div>

                                            <div class="mt-3 space-y-1.5">
                                                <p class="text-[8px] text-gray-400 uppercase font-bold tracking-wider">Detalle por Mes</p>
                                                @foreach($investorPayouts as $idx => $payout)
                                                    <div class="flex items-center justify-between px-3 py-2 rounded-lg {{ $payout->is_overdue ? 'bg-red-50 dark:bg-red-500/5 border border-red-100 dark:border-red-500/10' : 'bg-gray-50/80 dark:bg-gray-900/50' }}">
                                                        <div class="flex items-center gap-3">
                                                            <span class="text-[9px] font-mono font-bold {{ $payout->is_overdue ? 'text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-400' }}">{{ str_pad($payout->month, 2, '0', STR_PAD_LEFT) }}/</span>
                                                            <span class="text-[9px] font-medium text-gray-600 dark:text-gray-400">{{ str_pad($payout->total_months, 2, '0', STR_PAD_LEFT) }}</span>
                                                            <span class="text-[9px] text-gray-400">·</span>
                                                            <span class="text-[9px] font-mono {{ $payout->is_overdue ? 'text-red-600 dark:text-red-400 font-bold' : 'text-gray-500 dark:text-gray-400' }}">{{ $payout->due_date->format('d/m/Y') }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            @if($payout->is_overdue)
                                                                <span class="text-[7px] font-black uppercase tracking-wider bg-red-200 text-red-700 dark:bg-red-500/20 dark:text-red-300 px-1.5 py-0.5 rounded">Vencido</span>
                                                            @else
                                                                <span class="text-[7px] font-bold uppercase tracking-wider bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400 px-1.5 py-0.5 rounded">Pendiente</span>
                                                            @endif
                                                            <span class="text-xs font-bold text-gray-900 dark:text-white">{{ $payout->currency === 'USD' ? '$' : 'S/' }} {{ number_format($payout->monthly_amount, 2) }}</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="flex-shrink-0 ml-2 hidden sm:block">
                                            <div class="bg-gradient-to-b from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 rounded-xl p-3 text-right min-w-[120px] border border-gray-100 dark:border-gray-800">
                                                <p class="text-[7px] text-gray-400 uppercase font-bold tracking-tighter">Total a Pagar</p>
                                                <p class="text-lg font-black text-gray-900 dark:text-white mt-1">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($investorTotal, 2) }}</p>
                                                <p class="text-[8px] text-gray-400 mt-1">{{ $investorPayouts->count() }} mes{{ $investorPayouts->count() !== 1 ? 'es' : '' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
