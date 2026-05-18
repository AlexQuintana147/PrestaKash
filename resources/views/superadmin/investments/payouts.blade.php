@extends('superadmin.layout')

@section('title', 'Pagos a Inversores')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8">

    <div class="flex items-end justify-between mb-8">
        <div>
            <p class="text-xs text-gray-600 dark:text-gray-600 uppercase tracking-widest mb-1">Finanzas</p>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Pagos a Inversores</h1>
        </div>
        <p class="hidden md:block text-sm text-gray-600 dark:text-gray-400">{{ now()->format('d M Y') }}</p>
    </div>

    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 text-sm text-green-500">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 flex items-center gap-3 text-sm text-red-500">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            {{ session('error') }}
        </div>
    @endif

    @if($payouts->isEmpty())
        <div class="text-center py-20">
            <svg class="w-16 h-16 mx-auto text-gray-200 dark:text-gray-800 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <p class="text-sm text-gray-400 dark:text-gray-600">No hay pagos pendientes</p>
            <p class="text-[10px] text-gray-300 dark:text-gray-700 mt-1">Los pagos aparecerán cuando una oportunidad cerrada alcance su fecha de vencimiento mensual</p>
        </div>
    @else
        @php
            $allPayouts = $payouts;
            $grouped = $payouts->groupBy(fn($p) => $p->opportunity->id);
            $totalPendingAmount = $payouts->sum('monthly_amount');
            $overdueCount = $payouts->where('is_overdue', true)->count();
            $currency = $payouts->first()->currency;
        @endphp

        <div class="grid grid-cols-3 gap-3 mb-8">
            <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-xl p-4">
                <p class="text-[8px] text-gray-400 uppercase font-bold tracking-tighter">Total Pagos Pendientes</p>
                <p class="text-base font-black text-gray-900 dark:text-white mt-1">{{ $payouts->total() }}</p>
            </div>
            <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-xl p-4">
                <p class="text-[8px] text-gray-400 uppercase font-bold tracking-tighter">Monto Total a Pagar</p>
                <p class="text-base font-black text-green-600 dark:text-green-400 mt-1">{{ $currency === 'USD' ? '$' : 'S/' }} {{ number_format($totalPendingAmount, 2) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-xl p-4">
                <p class="text-[8px] text-gray-400 uppercase font-bold tracking-tighter">Vencidos</p>
                <p class="text-base font-black {{ $overdueCount > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }} mt-1">{{ $overdueCount }}</p>
            </div>
        </div>

        <div class="divide-y divide-gray-100 dark:divide-gray-900">
            @foreach($grouped as $opportunityId => $opportunityPayouts)
                @php
                    $opportunity = $opportunityPayouts->first()->opportunity;
                    $opportunityTotal = $opportunityPayouts->sum('monthly_amount');
                    $uniqueInvestors = $opportunityPayouts->unique(fn($p) => $p->investor->id);
                @endphp

                <div class="py-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-indigo-100 dark:bg-indigo-500/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">{{ $opportunity->title }}</h3>
                                    <span class="text-[9px] font-mono text-gray-400">{{ $opportunity->code }}</span>
                                </div>
                                <div class="flex items-center gap-3 mt-0.5 text-[10px] text-gray-400">
                                    <span>{{ $opportunity->monthly_return_rate }}% mensual</span>
                                    <span>·</span>
                                    <span>{{ $opportunity->term_months }} meses</span>
                                    <span>·</span>
                                    <span>Cerrada {{ $opportunity->closed_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[8px] font-black uppercase tracking-wider px-2 py-1 rounded-lg bg-gray-800 text-white dark:bg-gray-700 dark:text-gray-200">
                                {{ $opportunity->currency }}
                            </span>
                            <span class="text-[8px] font-bold px-2 py-1 rounded-lg
                                {{ $opportunityPayouts->contains('is_overdue', true) ? 'bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-400' : 'bg-green-100 text-green-600 dark:bg-green-500/10 dark:text-green-400' }}">
                                {{ $opportunityPayouts->count() }} pago{{ $opportunityPayouts->count() !== 1 ? 's' : '' }} · {{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format($opportunityTotal, 2) }}
                            </span>
                        </div>
                    </div>

                    @php
                        $investorGrouped = $opportunityPayouts->groupBy(fn($p) => $p->investor->id);
                    @endphp

                    @foreach($investorGrouped as $investorId => $investorPayouts)
                        @php
                            $investor = $investorPayouts->first()->investor;
                            $investorTotal = $investorPayouts->sum('monthly_amount');
                            $investorOverdue = $investorPayouts->where('is_overdue', true)->count();
                            $selfie = $investor->documents?->selfie;
                            $photo = $selfie ? Storage::url($selfie) : ($investor->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($investor->name) . '&background=e5e7eb&color=6b7280');
                        @endphp

                        <div class="ml-4 md:ml-8 mb-4 last:mb-0 bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl overflow-hidden">
                            <div class="p-4">
                                <div class="flex items-start gap-3">
                                    <img src="{{ $photo }}" alt="{{ $investor->name }}" class="w-10 h-10 rounded-full object-cover border border-gray-100 dark:border-gray-800 flex-shrink-0">

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ $investor->name }}</h4>
                                            @if($investorOverdue > 0)
                                                <span class="text-[8px] font-black uppercase tracking-wider bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-400 px-1.5 py-0.5 rounded">{{ $investorOverdue }} vencido{{ $investorOverdue !== 1 ? 's' : '' }}</span>
                                            @endif
                                        </div>
                                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $investor->email }}</p>

                                        <div class="mt-3 flex flex-wrap gap-3 text-xs">
                                            <div>
                                                <span class="text-[8px] text-gray-400 uppercase font-bold tracking-tighter">Invertido</span>
                                                <p class="font-bold text-gray-900 dark:text-white">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($investor->pivot->amount, 2) }}</p>
                                            </div>
                                            <div>
                                                <span class="text-[8px] text-gray-400 uppercase font-bold tracking-tighter">Retorno/Mes</span>
                                                <p class="font-bold text-green-600 dark:text-green-400">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($investorPayouts->first()->monthly_amount, 2) }}</p>
                                            </div>
                                            <div>
                                                <span class="text-[8px] text-gray-400 uppercase font-bold tracking-tighter">Retorno Total</span>
                                                <p class="font-bold text-emerald-600 dark:text-emerald-400">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($investorPayouts->first()->total_return, 2) }}</p>
                                            </div>
                                            <div>
                                                <span class="text-[8px] text-gray-400 uppercase font-bold tracking-tighter">A Pagar</span>
                                                <p class="font-black text-gray-900 dark:text-white">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($investorTotal, 2) }}</p>
                                            </div>
                                        </div>

                                        <div class="mt-3 space-y-1">
                                            @foreach($investorPayouts as $payout)
                                                <div class="flex items-center justify-between px-3 py-2 rounded-lg {{ $payout->is_overdue ? 'bg-red-50 dark:bg-red-500/5 border border-red-100 dark:border-red-500/10' : 'bg-gray-50/80 dark:bg-gray-900/50' }}">
                                                    <div class="flex items-center gap-3">
                                                        <span class="text-[9px] font-mono font-bold {{ $payout->is_overdue ? 'text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-400' }}">{{ str_pad($payout->month, 2, '0', STR_PAD_LEFT) }}/{{ str_pad($payout->total_months, 2, '0', STR_PAD_LEFT) }}</span>
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

                                    <div class="flex-shrink-0 hidden sm:block">
                                        <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-3 text-right min-w-[100px]">
                                            <p class="text-[7px] text-gray-400 uppercase font-bold tracking-tighter">Total</p>
                                            <p class="text-base font-black text-gray-900 dark:text-white mt-0.5">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($investorTotal, 2) }}</p>
                                            <p class="text-[8px] text-gray-400 mt-0.5">{{ $investorPayouts->count() }} mes{{ $investorPayouts->count() !== 1 ? 'es' : '' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        @if(method_exists($payouts, 'links'))
            <div class="mt-12 flex justify-center">
                {{ $payouts->withQueryString()->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
