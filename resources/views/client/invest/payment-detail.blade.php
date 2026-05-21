@extends('client.layout')

@section('title', 'Detalle de Pagos — ' . $opportunity->title)

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8 max-w-5xl">

    <div class="mb-8">
        <a href="{{ route('client.investments.index') }}" class="inline-flex items-center gap-1.5 text-xs text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors group mb-4">
            <span class="group-hover:-translate-x-0.5 transition-transform">←</span>
            <span class="border-b border-gray-300 dark:border-gray-600 group-hover:border-gray-900 dark:group-hover:border-white transition-colors">Volver a Control</span>
        </a>

        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[10px] font-mono text-gray-400">{{ $opportunity->code }}</span>
                    <span class="px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wider
                        {{ $isClosed ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400' }}">
                        {{ $isClosed ? 'Cerrada' : 'En proceso' }}
                    </span>
                </div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">{{ $opportunity->title }}</h1>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-8">
        <div class="bg-white dark:bg-gray-950 rounded-xl border border-gray-100 dark:border-gray-800/60 p-4">
            <p class="text-[9px] text-gray-400 uppercase tracking-wider font-semibold mb-1">Invertido</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white tabular-nums">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format($amount, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-950 rounded-xl border border-gray-100 dark:border-gray-800/60 p-4">
            <p class="text-[9px] text-gray-400 uppercase tracking-wider font-semibold mb-1">Tasa Mensual</p>
            <p class="text-lg font-bold text-indigo-600 dark:text-indigo-400 tabular-nums">{{ $opportunity->monthly_return_rate }}%</p>
        </div>
        <div class="bg-white dark:bg-gray-950 rounded-xl border border-gray-100 dark:border-gray-800/60 p-4">
            <p class="text-[9px] text-gray-400 uppercase tracking-wider font-semibold mb-1">Plazo</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white tabular-nums">{{ $opportunity->term_months }} meses</p>
        </div>
        <div class="bg-white dark:bg-gray-950 rounded-xl border border-gray-100 dark:border-gray-800/60 p-4">
            <p class="text-[9px] text-gray-400 uppercase tracking-wider font-semibold mb-1">Sistema</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $amortizationType === 'frances' ? 'Francés' : 'Americano' }}</p>
        </div>
    </div>

    @if(!$isClosed)
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl bg-amber-50 dark:bg-amber-500/5 border border-amber-100 dark:border-amber-500/10 mb-8">
            <svg class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-xs text-amber-700 dark:text-amber-300 leading-relaxed">
                Esta oportunidad aún no está cerrada. Las fechas de pago son estimadas y comenzarán a confirmarse una vez cerrada.
            </p>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-8">
        <div class="bg-emerald-50 dark:bg-emerald-500/5 rounded-xl border border-emerald-100 dark:border-emerald-500/10 p-4">
            <p class="text-[9px] text-emerald-600 dark:text-emerald-400 uppercase tracking-wider font-semibold mb-1">Recibido</p>
            <p class="text-lg font-bold text-emerald-700 dark:text-emerald-300 tabular-nums">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format($totalReceived, 2) }}</p>
        </div>
        <div class="bg-amber-50 dark:bg-amber-500/5 rounded-xl border border-amber-100 dark:border-amber-500/10 p-4">
            <p class="text-[9px] text-amber-600 dark:text-amber-400 uppercase tracking-wider font-semibold mb-1">Pendiente</p>
            <p class="text-lg font-bold text-amber-700 dark:text-amber-300 tabular-nums">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format(max(0, $totalExpectedReturn - $totalReceived), 2) }}</p>
        </div>
        <div class="bg-indigo-50 dark:bg-indigo-500/5 rounded-xl border border-indigo-100 dark:border-indigo-500/10 p-4">
            <p class="text-[9px] text-indigo-600 dark:text-indigo-400 uppercase tracking-wider font-semibold mb-1">Retorno Total</p>
            <p class="text-lg font-bold text-indigo-700 dark:text-indigo-300 tabular-nums">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format($totalExpectedReturn, 2) }}</p>
        </div>
    </div>

    @php
        $progressPct = $totalExpectedReturn > 0 ? round($totalReceived / $totalExpectedReturn * 100) : 0;
    @endphp
    <div class="mb-8">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Progreso de recuperación</span>
            <span class="text-xs font-bold {{ $progressPct >= 100 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-900 dark:text-white' }}">{{ $progressPct }}%</span>
        </div>
        <div class="w-full h-2 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
            <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-emerald-400 transition-all duration-700" style="width: {{ min(100, $progressPct) }}%"></div>
        </div>
    </div>

    <div class="mb-8">
        <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Cronograma de Pagos</h2>
        <div class="bg-white dark:bg-gray-950 rounded-2xl border border-gray-100 dark:border-gray-800/60 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="px-4 py-3 text-left text-[9px] font-bold text-gray-400 uppercase tracking-wider">Cuota</th>
                            <th class="px-4 py-3 text-left text-[9px] font-bold text-gray-400 uppercase tracking-wider">Fecha</th>
                            <th class="px-4 py-3 text-right text-[9px] font-bold text-gray-400 uppercase tracking-wider">Interés</th>
                            <th class="px-4 py-3 text-right text-[9px] font-bold text-gray-400 uppercase tracking-wider">Capital</th>
                            <th class="px-4 py-3 text-right text-[9px] font-bold text-gray-400 uppercase tracking-wider">Cuota Total</th>
                            <th class="px-4 py-3 text-right text-[9px] font-bold text-gray-400 uppercase tracking-wider">Saldo</th>
                            <th class="px-4 py-3 text-center text-[9px] font-bold text-gray-400 uppercase tracking-wider">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schedule as $item)
                            @php
                                $isPaid = false;
                                $isOverdue = false;
                                if ($isClosed && $item->date) {
                                    $isOverdue = $item->date->lt(now());
                                }
                            @endphp
                            <tr class="border-b border-gray-50 dark:border-gray-900/50 hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-colors">
                                <td class="px-4 py-3 font-bold text-gray-900 dark:text-white">{{ $item->month }}</td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 font-mono">
                                    {{ $item->date ? $item->date->format('d/m/Y') : '—' }}
                                </td>
                                <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300 tabular-nums">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format($item->interest, 2) }}</td>
                                <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300 tabular-nums">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format($item->principal, 2) }}</td>
                                <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white tabular-nums">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format($item->payment, 2) }}</td>
                                <td class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 tabular-nums">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format($item->balance, 2) }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if(!$isClosed)
                                        <span class="px-2 py-0.5 rounded-md text-[8px] font-bold uppercase tracking-wider bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-500">Pendiente</span>
                                    @elseif($isOverdue)
                                        <span class="px-2 py-0.5 rounded-md text-[8px] font-bold uppercase tracking-wider bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">Vencida</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-md text-[8px] font-bold uppercase tracking-wider bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-500">Por vencer</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($payments->count() > 0)
        <div class="mb-8">
            <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Pagos Recibidos</h2>
            <div class="bg-white dark:bg-gray-950 rounded-2xl border border-gray-100 dark:border-gray-800/60 overflow-hidden divide-y divide-gray-50 dark:divide-gray-900/50">
                @foreach($payments as $payment)
                    <div class="flex items-center justify-between px-4 py-3 hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-medium text-gray-900 dark:text-white truncate">{{ $payment->description }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[10px] text-gray-400 font-mono">{{ $payment->date ? \Carbon\Carbon::parse($payment->date)->format('d/m/Y') : '—' }}</span>
                                    @if($payment->cashbox)
                                        <span class="text-[10px] text-gray-300 dark:text-gray-600">·</span>
                                        <span class="text-[10px] text-gray-400">{{ $payment->cashbox->name }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <span class="flex-shrink-0 ml-3 text-sm font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">
                            +{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format($payment->amount, 2) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-950 rounded-2xl border border-gray-100 dark:border-gray-800/60 p-5">
        <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-4">Resumen de la Inversión</h3>
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500 dark:text-gray-400">Capital invertido</span>
                <span class="text-xs font-bold text-gray-900 dark:text-white tabular-nums">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format($amount, 2) }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500 dark:text-gray-400">Tasa de retorno mensual</span>
                <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 tabular-nums">{{ $opportunity->monthly_return_rate }}%</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500 dark:text-gray-400">Retorno estimado total</span>
                <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 tabular-nums">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format($totalExpectedReturn - $amount, 2) }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500 dark:text-gray-400">Sistema de amortización</span>
                <span class="text-xs font-bold text-gray-900 dark:text-white">{{ $amortizationType === 'frances' ? 'Francés' : 'Americano' }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500 dark:text-gray-400">Moneda</span>
                <span class="text-xs font-bold text-gray-900 dark:text-white">{{ $opportunity->currency }}</span>
            </div>
            @if($startDate)
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Inicio de pagos</span>
                    <span class="text-xs font-bold text-gray-900 dark:text-white">{{ $startDate->format('d/m/Y') }}</span>
                </div>
            @endif
            @if($investor->pivot->approved_at)
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Fecha de aprobación</span>
                    <span class="text-xs font-bold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($investor->pivot->approved_at)->format('d/m/Y') }}</span>
                </div>
            @endif
            <div class="h-px bg-gray-100 dark:bg-gray-800 my-1"></div>
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-900 dark:text-white">Total a recibir</span>
                <span class="text-sm font-bold text-indigo-600 dark:text-indigo-400 tabular-nums">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format($totalExpectedReturn, 2) }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Total recibido</span>
                <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format($totalReceived, 2) }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-amber-600 dark:text-amber-400">Pendiente por recibir</span>
                <span class="text-sm font-bold text-amber-600 dark:text-amber-400 tabular-nums">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format(max(0, $totalExpectedReturn - $totalReceived), 2) }}</span>
            </div>
        </div>
    </div>

</div>
@endsection
