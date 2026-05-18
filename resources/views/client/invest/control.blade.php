@extends('client.layout')

@section('title', 'Control de Inversiones')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8 max-w-5xl">

    <div class="mb-8">
        <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-0.5">Inversiones</p>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Control de Inversiones</h1>
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

    <div class="flex border-b border-gray-200 dark:border-gray-800 mb-8">
        <button onclick="switchTab('opportunities')" id="tab-opportunities"
            class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white border-b-2 border-gray-900 dark:border-white transition-colors">
            Oportunidades
        </button>
        <button onclick="switchTab('my-investments')" id="tab-my-investments"
            class="flex items-center gap-2 px-4 py-3 text-sm font-medium text-gray-600 dark:text-gray-400 border-b-2 border-transparent hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
            Mis Inversiones
            @php
                $notificationCount = $myInvestments->filter(function($inv) {
                    return in_array($inv->pivot->status, ['rejected']) ||
                           ($inv->pivot->status === 'approved' && $inv->pivot->payment_status !== 'confirmed');
                })->count();
            @endphp
            @if($notificationCount > 0)
                <svg class="w-4 h-4 text-purple-600 animate-swing origin-top" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            @endif
        </button>
        <button onclick="switchTab('my-payments')" id="tab-my-payments"
            class="flex items-center gap-2 px-4 py-3 text-sm font-medium text-gray-600 dark:text-gray-400 border-b-2 border-transparent hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
            Mis Pagos
        </button>
    </div>

    <div id="panel-opportunities">
        @if($opportunities->count())
            <div class="divide-y divide-gray-100 dark:divide-gray-900">
                @foreach($opportunities as $opp)
                    <a href="{{ route('client.investments.show', $opp) }}" class="block py-6 hover:opacity-80 transition-opacity group">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-1">
                                    <span class="text-[9px] font-mono text-gray-400">{{ $opp->code }}</span>
                                    <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-widest bg-green-100 text-green-600 dark:bg-green-500/10 dark:text-green-400">Abierta</span>
                                </div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $opp->title }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-1">{{ $opp->description }}</p>
                            </div>
                            <div class="flex items-center gap-6 text-right">
                                <div>
                                    <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Retorno Mensual</p>
                                    <p class="text-lg font-bold text-green-600 dark:text-green-400">{{ $opp->monthly_return_rate }}%</p>
                                </div>
                                <div>
                                    <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Plazo</p>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $opp->term_months }} meses</p>
                                </div>
                                <div>
                                    <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Progreso</p>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $opp->progress_percent }}%</p>
                                    <div class="w-24 h-1.5 bg-gray-100 dark:bg-gray-800 rounded-full mt-1 overflow-hidden">
                                        <div class="h-full rounded-full bg-indigo-500" style="width: {{ min(100, $opp->progress_percent) }}%"></div>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Monto</p>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $opp->currency === 'USD' ? '$' : 'S/' }} {{ number_format($opp->target_amount, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            @if($opportunities->hasPages())
                <div class="mt-12 flex justify-center">
                    {{ $opportunities->withQueryString()->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-20">
                <svg class="w-16 h-16 mx-auto text-gray-200 dark:text-gray-800 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                <p class="text-sm text-gray-400 dark:text-gray-600">No hay oportunidades disponibles</p>
            </div>
        @endif
    </div>

    <div id="panel-my-investments" class="hidden">
        @if($myInvestments->count())
            <div class="divide-y divide-gray-100 dark:divide-gray-900">
                @foreach($myInvestments as $inv)
                    @if($inv->pivot->status === 'approved')
                        <a href="{{ route('client.investments.show', $inv) }}" class="block py-6 hover:opacity-80 transition-opacity cursor-pointer">
                    @else
                        <div class="py-6">
                    @endif
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[9px] font-mono text-gray-400">{{ $inv->code }}</span>
                                    <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-widest
                                        {{ $inv->pivot->status === 'approved' ? 'bg-green-100 text-green-600 dark:bg-green-500/10 dark:text-green-400' : '' }}
                                        {{ $inv->pivot->status === 'pending' ? 'bg-yellow-100 text-yellow-600 dark:bg-yellow-500/10 dark:text-yellow-400' : '' }}
                                        {{ $inv->pivot->status === 'rejected' ? 'bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-400' : '' }}">
                                        {{ $inv->pivot->status === 'approved' ? 'Aprobada' : ($inv->pivot->status === 'pending' ? 'Pendiente' : 'Rechazada') }}
                                    </span>
                                    @if(in_array($inv->pivot->status, ['approved', 'rejected']))
                                        <svg class="w-4 h-4 text-purple-600 animate-swing origin-top" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                    @endif
                                </div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">{{ $inv->title }}</h3>
                            </div>
                            <div class="flex items-center gap-6 text-right">
                                <div>
                                    <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Mi Inversión</p>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $inv->currency === 'USD' ? '$' : 'S/' }} {{ number_format($inv->pivot->amount, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Retorno Mensual</p>
                                    <p class="text-sm font-bold text-green-600 dark:text-green-400">{{ number_format($inv->pivot->amount * $inv->monthly_return_rate / 100, 2) }} {{ $inv->currency }}</p>
                                </div>
                                <div>
                                    <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Fecha Solicitud</p>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $inv->pivot->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                        @if($inv->pivot->status === 'rejected')
                            <div class="mt-3 pt-3 border-t border-red-100 dark:border-red-900/30">
                                <p class="text-[10px] font-black text-red-400 dark:text-red-500 uppercase tracking-widest mb-1">Motivo del rechazo</p>
                                <p class="text-sm text-red-600 dark:text-red-400">{{ $inv->pivot->admin_notes ?: 'No se especificó motivo.' }}</p>
                            </div>
                        @endif
                    @if($inv->pivot->status === 'approved')
                        </a>
                    @else
                        </div>
                    @endif
                @endforeach
            </div>

            @if($myInvestments->hasPages())
                <div class="mt-12 flex justify-center">
                    {{ $myInvestments->withQueryString()->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-20">
                <svg class="w-16 h-16 mx-auto text-gray-200 dark:text-gray-800 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="text-sm text-gray-400 dark:text-gray-600">Aún no tienes inversiones</p>
                <button onclick="switchTab('opportunities')" class="mt-4 text-sm font-bold text-indigo-600 dark:text-indigo-400 border-b-2 border-indigo-600 dark:border-indigo-400 pb-0.5 hover:opacity-70 transition-opacity">Explorar oportunidades</button>
            </div>
        @endif
    </div>

    <div id="panel-my-payments" class="hidden">
        @if($paymentInvestments->isEmpty())
            <div class="text-center py-16 bg-gray-50 dark:bg-gray-900/50 rounded-2xl">
                <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <p class="text-sm font-bold text-gray-900 dark:text-white mb-1">Sin inversiones confirmadas</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Aún no tienes inversiones con pago confirmado.</p>
                <button onclick="switchTab('opportunities')" class="mt-4 inline-flex items-center gap-1.5 text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                    Explorar oportunidades →
                </button>
            </div>
        @else
            <div class="space-y-6">
                @foreach($paymentInvestments as $inv)
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

                            <div class="flex items-center gap-4 mt-3 text-[11px] text-gray-500 dark:text-gray-400 flex-wrap">
                                <span>Plazo: {{ $opp->term_months }} meses</span>
                                <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                                <span>Tasa: {{ $opp->monthly_return_rate }}% mensual</span>
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
</div>

<script>
function switchTab(tab) {
    const inactiveClass = 'px-4 py-3 text-sm font-medium text-gray-600 dark:text-gray-400 border-b-2 border-transparent hover:text-gray-700 dark:hover:text-gray-300 transition-colors';
    const activeClass = 'px-4 py-3 text-sm font-medium text-gray-900 dark:text-white border-b-2 border-gray-900 dark:border-white transition-colors';
    const activeWithIcon = 'flex items-center gap-2 px-4 py-3 text-sm font-medium text-gray-900 dark:text-white border-b-2 border-gray-900 dark:border-white transition-colors';

    document.getElementById('panel-opportunities').classList.toggle('hidden', tab !== 'opportunities');
    document.getElementById('panel-my-investments').classList.toggle('hidden', tab !== 'my-investments');
    document.getElementById('panel-my-payments').classList.toggle('hidden', tab !== 'my-payments');

    document.getElementById('tab-opportunities').className = tab === 'opportunities' ? activeClass : inactiveClass;
    document.getElementById('tab-my-investments').className = tab === 'my-investments' ? activeWithIcon : inactiveClass;
    document.getElementById('tab-my-payments').className = tab === 'my-payments' ? activeWithIcon : inactiveClass;
}
</script>
@endsection
