@extends('superadmin.layout')

@section('title', 'Pagos a Inversores')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8 max-w-5xl mx-auto">

    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Pagos a Inversores</h1>
        <p class="text-xs text-gray-400 mt-1">Retornos mensuales pendientes de pago a inversores</p>
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
            <p class="text-[10px] text-gray-300 mt-1">Los pagos aparecerán aquí cuando una oportunidad cerrada alcance su fecha de vencimiento mensual</p>
        </div>
    @else
        <div class="space-y-3">
            @php
                $grouped = $payouts->groupBy(fn($p) => $p->opportunity->id);
            @endphp

            @foreach($grouped as $opportunityId => $opportunityPayouts)
                $opportunity = $opportunityPayouts->first()->opportunity;
                <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-900 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-bold text-gray-900 dark:text-white">{{ $opportunity->title }}</h2>
                            <p class="text-[9px] text-gray-400 mt-0.5">
                                Cerrada el {{ $opportunity->closed_at->format('d/m/Y') }} · Tasa {{ $opportunity->monthly_return_rate }}% mensual · {{ $opportunity->term_months }} meses
                            </p>
                        </div>
                        <span class="text-[9px] font-black uppercase tracking-wider px-2.5 py-1 rounded-lg
                            {{ $opportunityPayouts->contains('is_overdue', true) ? 'bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-400' : 'bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' }}">
                            {{ $opportunityPayouts->count() }} pago{{ $opportunityPayouts->count() !== 1 ? 's' : '' }}
                        </span>
                    </div>

                    <div class="divide-y divide-gray-50 dark:divide-gray-900/50">
                        @foreach($opportunityPayouts as $payout)
                            <div class="px-5 py-4 {{ $payout->is_overdue ? 'bg-red-50/30 dark:bg-red-500/5' : '' }}">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-500/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <span class="text-[10px] font-black text-purple-600 dark:text-purple-400">{{ strtoupper(substr($payout->investor->name, 0, 1)) }}</span>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $payout->investor->name }}</span>
                                            @if($payout->is_overdue)
                                                <span class="text-[8px] font-black uppercase tracking-wider bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-400 px-1.5 py-0.5 rounded">Vencido</span>
                                            @else
                                                <span class="text-[8px] font-black uppercase tracking-wider bg-amber-100 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400 px-1.5 py-0.5 rounded">Pendiente</span>
                                            @endif
                                        </div>

                                        <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-1.5 text-xs">
                                            <div>
                                                <p class="text-[8px] text-gray-400 uppercase font-bold tracking-tighter">Mes</p>
                                                <p class="font-semibold text-gray-700 dark:text-gray-300">{{ $payout->month }} / {{ $payout->total_months }}</p>
                                            </div>
                                            <div>
                                                <p class="text-[8px] text-gray-400 uppercase font-bold tracking-tighter">Monto invertido</p>
                                                <p class="font-semibold text-gray-700 dark:text-gray-300">{{ $payout->currency === 'USD' ? '$' : 'S/' }} {{ number_format($payout->investor->pivot->amount, 2) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-[8px] text-gray-400 uppercase font-bold tracking-tighter">Retorno mensual</p>
                                                <p class="font-bold text-green-600 dark:text-green-400">{{ $payout->currency === 'USD' ? '$' : 'S/' }} {{ number_format($payout->monthly_amount, 2) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-[8px] text-gray-400 uppercase font-bold tracking-tighter">Fecha límite</p>
                                                <p class="font-mono {{ $payout->is_overdue ? 'font-bold text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">{{ $payout->due_date->format('d/m/Y') }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex-shrink-0 ml-2">
                                        <div class="text-right">
                                            <p class="text-lg font-black text-gray-900 dark:text-white">{{ $payout->currency === 'USD' ? '$' : 'S/' }} {{ number_format($payout->monthly_amount, 2) }}</p>
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
