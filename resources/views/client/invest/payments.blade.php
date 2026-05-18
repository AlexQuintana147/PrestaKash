@extends('client.layout')

@section('title', 'Mis Pagos')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8 max-w-5xl">

    <div class="mb-8">
        <a href="{{ route('client.investments.index') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors group">
            <span class="group-hover:-translate-x-0.5 transition-transform">←</span>
            Volver a Mis Inversiones
        </a>
        <h1 class="mt-3 sm:mt-4 text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Mis Pagos</h1>
        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1.5">Revisa los pagos que has recibido por cada inversión activa</p>
    </div>

    @if($investments->isEmpty())
        <div class="text-center py-16 bg-gray-50 dark:bg-gray-900/50 rounded-2xl">
            <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <p class="text-sm font-bold text-gray-900 dark:text-white mb-1">Sin inversiones confirmadas</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Aún no tienes inversiones con pago confirmado.</p>
            <a href="{{ route('client.invertir') }}" class="mt-4 inline-flex items-center gap-1.5 text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                Explorar oportunidades →
            </a>
        </div>
    @else
        <div class="space-y-6">
            @foreach($investments as $inv)
                @php
                    $opp = $inv;
                    $isClosed = in_array($opp->status, ['closed', 'cancelled']);
                    $isFunded = $opp->status === 'funded';
                    $invPayments = $payments[$opp->id] ?? collect();
                    $totalReceived = $invPayments->where('type', 'expense')->sum('amount');
                @endphp

                <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl overflow-hidden">

                    <div class="p-5 sm:p-6 border-b border-gray-100 dark:border-gray-900">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[10px] font-mono text-gray-400">{{ $opp->code }}</span>
                                    <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-widest
                                        {{ $isClosed ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : ($isFunded ? 'bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'bg-amber-100 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400') }}">
                                        {{ $isClosed ? 'Cerrada' : ($isFunded ? 'Financiada' : 'Abierta') }}
                                    </span>
                                </div>
                                <h2 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white truncate">{{ $opp->title }}</h2>
                            </div>
                            <div class="flex-shrink-0 text-right">
                                <p class="text-[9px] text-gray-400 uppercase tracking-wider font-bold">Invertido</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $opp->currency === 'USD' ? '$' : 'S/' }} {{ number_format($inv->pivot->amount, 2) }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 mt-3 text-[11px] text-gray-500 dark:text-gray-400">
                            <span>Plazo: {{ $opp->term_months }} meses</span>
                            <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                            <span>Tasa: {{ $opp->annual_return_rate }}% anual</span>
                            @if($inv->pivot->approved_at)
                                <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                                <span>Desde: {{ \Carbon\Carbon::parse($inv->pivot->approved_at)->format('d/m/Y') }}</span>
                            @endif
                        </div>
                    </div>

                    @if(!$isClosed)
                        <div class="p-5 sm:p-6 bg-amber-50/50 dark:bg-amber-500/5 border-b border-amber-100 dark:border-amber-500/10">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center mt-0.5">
                                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-amber-800 dark:text-amber-300">Esperando cierre de oportunidad</p>
                                    <p class="text-xs text-amber-600 dark:text-amber-400/80 mt-0.5 leading-relaxed">
                                        Esta oportunidad aún está {{ $isFunded ? 'en proceso de financiación' : 'abierta para recibir inversiones' }}.
                                        Los pagos mensuales comenzarán una vez que la oportunidad se cierre y se inicie el período de recuperación.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="p-5 sm:p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-widest">Pagos Recibidos</h3>
                            @if($totalReceived > 0)
                                <span class="text-sm font-bold text-green-600 dark:text-green-400">
                                    Total: {{ $opp->currency === 'USD' ? '$' : 'S/' }} {{ number_format($totalReceived, 2) }}
                                </span>
                            @endif
                        </div>

                        @if($invPayments->isEmpty())
                            @if($isClosed)
                                <div class="text-center py-8 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                                    <svg class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Aún no hay pagos registrados para esta inversión.</p>
                                </div>
                            @else
                                <div class="text-center py-6">
                                    <p class="text-xs text-gray-400 dark:text-gray-500 italic">Los pagos aparecerán aquí una vez iniciado el período de recuperación.</p>
                                </div>
                            @endif
                        @else
                            <div class="overflow-x-auto -mx-5 sm:-mx-6 px-5 sm:px-6">
                                <table class="w-full min-w-[500px]">
                                    <thead>
                                        <tr class="border-b border-gray-100 dark:border-gray-900">
                                            <th class="text-left text-[9px] uppercase tracking-wider text-gray-400 font-black pb-3">Fecha</th>
                                            <th class="text-left text-[9px] uppercase tracking-wider text-gray-400 font-black pb-3">Descripción</th>
                                            <th class="text-right text-[9px] uppercase tracking-wider text-gray-400 font-black pb-3">Monto</th>
                                            <th class="text-left text-[9px] uppercase tracking-wider text-gray-400 font-black pb-3">Caja</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50 dark:divide-gray-900/50">
                                        @foreach($invPayments as $payment)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                                                <td class="py-3">
                                                    <p class="text-xs font-medium text-gray-900 dark:text-white">{{ $payment->date ? \Carbon\Carbon::parse($payment->date)->format('d/m/Y') : '—' }}</p>
                                                </td>
                                                <td class="py-3">
                                                    <p class="text-xs text-gray-700 dark:text-gray-300 truncate max-w-[200px]" title="{{ $payment->description }}">{{ $payment->description }}</p>
                                                    <p class="text-[10px] text-gray-400 font-mono">{{ $payment->code }}</p>
                                                </td>
                                                <td class="py-3 text-right">
                                                    <span class="text-xs font-bold {{ $payment->type === 'expense' ? 'text-green-600 dark:text-green-400' : 'text-gray-900 dark:text-white' }}">
                                                        {{ $payment->type === 'expense' ? '+' : '' }}{{ $opp->currency === 'USD' ? '$' : 'S/' }} {{ number_format($payment->amount, 2) }}
                                                    </span>
                                                </td>
                                                <td class="py-3">
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $payment->cashbox->name ?? '—' }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-900 grid grid-cols-2 sm:grid-cols-3 gap-4 text-center">
                                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-3">
                                    <p class="text-[9px] text-gray-400 uppercase font-bold tracking-wider">Pagos recibidos</p>
                                    <p class="text-sm font-bold text-green-600 dark:text-green-400 mt-0.5">{{ $invPayments->count() }}</p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-3">
                                    <p class="text-[9px] text-gray-400 uppercase font-bold tracking-wider">Total recibido</p>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">{{ $opp->currency === 'USD' ? '$' : 'S/' }} {{ number_format($totalReceived, 2) }}</p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-3 col-span-2 sm:col-span-1">
                                    <p class="text-[9px] text-gray-400 uppercase font-bold tracking-wider">Por recuperar</p>
                                    <p class="text-sm font-bold text-amber-600 dark:text-amber-400 mt-0.5">{{ $opp->currency === 'USD' ? '$' : 'S/' }} {{ number_format(max(0, $inv->pivot->amount - $totalReceived), 2) }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection