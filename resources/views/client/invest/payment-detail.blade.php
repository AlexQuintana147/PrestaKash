@extends('client.layout')

@section('title', 'Detalle de Pagos — ' . $opportunity->title)

@section('content')
<div class="min-h-screen px-4 sm:px-5 md:px-10 py-6 sm:py-8 max-w-5xl">

    <div class="mb-6 sm:mb-8">
        <a href="{{ route('client.investments.index') }}" class="inline-flex items-center gap-1.5 text-xs text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors group mb-3">
            <span class="group-hover:-translate-x-0.5 transition-transform">←</span>
            <span class="border-b border-gray-300 dark:border-gray-600 group-hover:border-gray-900 dark:group-hover:border-white transition-colors">Volver a Control</span>
        </a>

        <div class="flex items-start justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[10px] font-mono text-gray-400">{{ $opportunity->code }}</span>
                    <span class="px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wider
                        {{ $isClosed ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400' }}">
                        {{ $isClosed ? 'Cerrada' : 'En proceso' }}
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">{{ $opportunity->title }}</h1>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3 mb-6 sm:mb-8">
        <div class="bg-white dark:bg-gray-950 rounded-xl border border-gray-100 dark:border-gray-800/60 p-3 sm:p-4">
            <p class="text-[8px] sm:text-[9px] text-gray-400 uppercase tracking-wider font-semibold mb-0.5 sm:mb-1">Invertido</p>
            <p class="text-base sm:text-lg font-bold text-gray-900 dark:text-white tabular-nums">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format($amount, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-950 rounded-xl border border-gray-100 dark:border-gray-800/60 p-3 sm:p-4">
            <p class="text-[8px] sm:text-[9px] text-gray-400 uppercase tracking-wider font-semibold mb-0.5 sm:mb-1">Tasa Mensual</p>
            <p class="text-base sm:text-lg font-bold text-indigo-600 dark:text-indigo-400 tabular-nums">{{ $opportunity->monthly_return_rate }}%</p>
        </div>
        <div class="bg-white dark:bg-gray-950 rounded-xl border border-gray-100 dark:border-gray-800/60 p-3 sm:p-4">
            <p class="text-[8px] sm:text-[9px] text-gray-400 uppercase tracking-wider font-semibold mb-0.5 sm:mb-1">Plazo</p>
            <p class="text-base sm:text-lg font-bold text-gray-900 dark:text-white tabular-nums">{{ $opportunity->term_months }}m</p>
        </div>
        <div class="bg-white dark:bg-gray-950 rounded-xl border border-gray-100 dark:border-gray-800/60 p-3 sm:p-4">
            <p class="text-[8px] sm:text-[9px] text-gray-400 uppercase tracking-wider font-semibold mb-0.5 sm:mb-1">Sistema</p>
            <p class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">{{ $amortizationType === 'frances' ? 'Francés' : 'Americano' }}</p>
        </div>
    </div>

    @if(!$isClosed)
        <div class="flex items-start gap-2.5 px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl bg-amber-50 dark:bg-amber-500/5 border border-amber-100 dark:border-amber-500/10 mb-6 sm:mb-8">
            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-[11px] sm:text-xs text-amber-700 dark:text-amber-300 leading-relaxed">
                Esta oportunidad aún no está cerrada. Las fechas son estimadas.
            </p>
        </div>
    @endif

    <div class="grid grid-cols-3 gap-2 sm:gap-3 mb-6 sm:mb-8">
        <div class="bg-emerald-500 rounded-xl p-3 sm:p-4">
            <p class="text-[8px] sm:text-[9px] text-white/80 uppercase tracking-wider font-semibold mb-0.5 sm:mb-1">Recibido</p>
            <p class="text-sm sm:text-lg font-bold text-white tabular-nums">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format($totalReceived, 2) }}</p>
        </div>
        <div class="bg-amber-500 rounded-xl p-3 sm:p-4">
            <p class="text-[8px] sm:text-[9px] text-white/80 uppercase tracking-wider font-semibold mb-0.5 sm:mb-1">Pendiente</p>
            <p class="text-sm sm:text-lg font-bold text-white tabular-nums">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format(max(0, $totalExpectedReturn - $totalReceived), 2) }}</p>
        </div>
        <div class="bg-blue-500 rounded-xl p-3 sm:p-4">
            <p class="text-[8px] sm:text-[9px] text-white/80 uppercase tracking-wider font-semibold mb-0.5 sm:mb-1">Retorno</p>
            <p class="text-sm sm:text-lg font-bold text-white tabular-nums">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format($totalExpectedReturn, 2) }}</p>
        </div>
    </div>

    @php
        $progressPct = $totalExpectedReturn > 0 ? round($totalReceived / $totalExpectedReturn * 100) : 0;
        $paidMonths = [];
        foreach ($payments as $p) {
            $paidMonths[] = $p->date ? \Carbon\Carbon::parse($p->date)->format('Y-m') : null;
        }
    @endphp
    <div class="mb-6 sm:mb-8">
        <div class="flex items-center justify-between mb-1.5 sm:mb-2">
            <span class="text-[9px] sm:text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Progreso</span>
            <span class="text-[11px] sm:text-xs font-bold {{ $progressPct >= 100 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-900 dark:text-white' }}">{{ $progressPct }}%</span>
        </div>
        <div class="w-full h-1.5 sm:h-2 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
            <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-emerald-400 transition-all duration-700" style="width: {{ min(100, $progressPct) }}%"></div>
        </div>
    </div>

    <div class="mb-6 sm:mb-8">
        <h2 class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-3 sm:mb-4">Cronograma de Pagos</h2>

        <div id="schedule-mobile" class="sm:hidden space-y-2">
            @foreach($schedule as $i => $item)
                @php
                    $isOverdue = false;
                    $isPaid = false;
                    if ($isClosed && $item->date) {
                        $isOverdue = $item->date->lt(now());
                        $isPaid = in_array($item->date->format('Y-m'), $paidMonths);
                    }
                @endphp
                <div class="schedule-item {{ $isOverdue && !$isPaid ? 'bg-red-600 text-white' : 'bg-white dark:bg-gray-950' }} rounded-xl border {{ $isOverdue && !$isPaid ? 'border-red-500' : 'border-gray-100 dark:border-gray-800/60' }} p-3" data-idx="{{ $i }}" style="display:none">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold {{ $isOverdue && !$isPaid ? 'text-white' : 'text-gray-900 dark:text-white' }}">Cuota {{ $item->month }}</span>
                            @if(!$isClosed)
                                <span class="px-1.5 py-px rounded text-[8px] font-bold uppercase tracking-wider bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-500">Pend.</span>
                            @elseif($isPaid)
                                <span class="px-1.5 py-px rounded text-[8px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">Pagada</span>
                            @elseif($isOverdue)
                                <span class="px-1.5 py-px rounded text-[8px] font-bold uppercase tracking-wider bg-white/20 text-white">Vencida</span>
                            @else
                                <span class="px-1.5 py-px rounded text-[8px] font-bold uppercase tracking-wider bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-500">Por vencer</span>
                            @endif
                        </div>
                        <span class="text-[10px] {{ $isOverdue && !$isPaid ? 'text-white/70' : 'text-gray-400' }} font-mono">{{ $item->date ? $item->date->format('d/m/y') : '—' }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <p class="text-[8px] {{ $isOverdue && !$isPaid ? 'text-white/60' : 'text-gray-400' }} uppercase tracking-wider">Interés</p>
                            <p class="text-[11px] font-medium {{ $isOverdue && !$isPaid ? 'text-white' : 'text-gray-600 dark:text-gray-300' }} tabular-nums">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format($item->interest, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-[8px] {{ $isOverdue && !$isPaid ? 'text-white/60' : 'text-gray-400' }} uppercase tracking-wider">Capital</p>
                            <p class="text-[11px] font-medium {{ $isOverdue && !$isPaid ? 'text-white' : 'text-gray-600 dark:text-gray-300' }} tabular-nums">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format($item->principal, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-[8px] {{ $isOverdue && !$isPaid ? 'text-white/60' : 'text-gray-400' }} uppercase tracking-wider">Total</p>
                            <p class="text-[11px] font-bold {{ $isOverdue && !$isPaid ? 'text-white' : 'text-gray-900 dark:text-white' }} tabular-nums">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format($item->payment, 2) }}</p>
                        </div>
                    </div>
                    <div class="mt-2 pt-2 border-t {{ $isOverdue && !$isPaid ? 'border-white/20' : 'border-gray-50 dark:border-gray-900/50' }}">
                        <div class="flex items-center justify-between">
                            <span class="text-[8px] {{ $isOverdue && !$isPaid ? 'text-white/60' : 'text-gray-400' }} uppercase tracking-wider">Saldo</span>
                            <span class="text-[11px] {{ $isOverdue && !$isPaid ? 'text-white/80' : 'text-gray-500 dark:text-gray-400' }} tabular-nums">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format($item->balance, 2) }}</span>
                        </div>
                    </div>
                    @if($isOverdue && !$isPaid)
                    <div class="mt-2 pt-2 border-t border-white/20">
                        <a href="https://wa.me/51917047937?text={{ urlencode('Hola, soy inversor de la oportunidad ' . $opportunity->code . '. Tengo un pago vencido correspondiente a la cuota ' . $item->month . ' con fecha ' . ($item->date ? $item->date->format('d/m/Y') : '—') . '. Me gustaría información sobre el estado de mi pago.') }}" target="_blank" class="flex items-center justify-center gap-2 w-full py-2 bg-green-500 hover:bg-green-600 rounded-lg text-[9px] font-bold uppercase tracking-widest text-white transition-colors">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            Consultar pago
                        </a>
                    </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="hidden sm:block bg-white dark:bg-gray-950 rounded-2xl border border-gray-100 dark:border-gray-800/60 overflow-hidden">
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
                    <tbody id="schedule-table-body">
                        @foreach($schedule as $i => $item)
                            @php
                                $isOverdue = false;
                                $isPaid = false;
                                if ($isClosed && $item->date) {
                                    $isOverdue = $item->date->lt(now());
                                    $isPaid = in_array($item->date->format('Y-m'), $paidMonths);
                                }
                                $overdueRow = $isOverdue && !$isPaid;
                            @endphp
                            <tr class="schedule-item border-b {{ $overdueRow ? 'border-red-500/30' : 'border-gray-50 dark:border-gray-900/50' }} {{ $overdueRow ? 'bg-red-600' : 'hover:bg-gray-50/50 dark:hover:bg-gray-900/30' }} transition-colors" data-idx="{{ $i }}" style="display:none">
                                <td class="px-4 py-3 font-bold {{ $overdueRow ? 'text-white' : 'text-gray-900 dark:text-white' }}">{{ $item->month }}</td>
                                <td class="px-4 py-3 {{ $overdueRow ? 'text-white/80' : 'text-gray-500 dark:text-gray-400' }} font-mono">{{ $item->date ? $item->date->format('d/m/Y') : '—' }}</td>
                                <td class="px-4 py-3 text-right {{ $overdueRow ? 'text-white' : 'text-gray-600 dark:text-gray-300' }} tabular-nums">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format($item->interest, 2) }}</td>
                                <td class="px-4 py-3 text-right {{ $overdueRow ? 'text-white' : 'text-gray-600 dark:text-gray-300' }} tabular-nums">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format($item->principal, 2) }}</td>
                                <td class="px-4 py-3 text-right font-bold {{ $overdueRow ? 'text-white' : 'text-gray-900 dark:text-white' }} tabular-nums">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format($item->payment, 2) }}</td>
                                <td class="px-4 py-3 text-right {{ $overdueRow ? 'text-white/80' : 'text-gray-500 dark:text-gray-400' }} tabular-nums">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format($item->balance, 2) }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if(!$isClosed)
                                        <span class="px-2 py-0.5 rounded-md text-[8px] font-bold uppercase tracking-wider bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-500">Pendiente</span>
                                    @elseif($isPaid)
                                        <span class="px-2 py-0.5 rounded-md text-[8px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">Pagada</span>
                                    @elseif($isOverdue)
                                        <div class="flex flex-col items-center gap-1.5">
                                            <span class="px-2 py-0.5 rounded-md text-[8px] font-bold uppercase tracking-wider bg-white/20 text-white">Vencida</span>
                                            <a href="https://wa.me/51917047937?text={{ urlencode('Hola, soy inversor de la oportunidad ' . $opportunity->code . '. Tengo un pago vencido correspondiente a la cuota ' . $item->month . ' con fecha ' . ($item->date ? $item->date->format('d/m/Y') : '—') . '. Me gustaría información sobre el estado de mi pago.') }}" target="_blank" class="inline-flex items-center gap-1 px-2 py-1 bg-green-500 hover:bg-green-600 rounded-md text-[8px] font-bold uppercase tracking-wider text-white transition-colors">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                                WhatsApp
                                            </a>
                                        </div>
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

        <div id="schedule-pagination" class="flex items-center justify-center gap-1.5 mt-4"></div>
    </div>

    @if($payments->count() > 0)
        <div class="mb-6 sm:mb-8">
            <h2 class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-3 sm:mb-4">Pagos Recibidos</h2>
            <div class="bg-white dark:bg-gray-950 rounded-2xl border border-gray-100 dark:border-gray-800/60 overflow-hidden divide-y divide-gray-50 dark:divide-gray-900/50">
                @foreach($payments as $payment)
                    <div class="flex items-center justify-between px-3 sm:px-4 py-2.5 sm:py-3 hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-colors">
                        <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[11px] sm:text-xs font-medium text-gray-900 dark:text-white truncate">{{ $payment->description }}</p>
                                <div class="flex items-center gap-1.5 sm:gap-2 mt-0.5">
                                    <span class="text-[9px] sm:text-[10px] text-gray-400 font-mono">{{ $payment->date ? \Carbon\Carbon::parse($payment->date)->format('d/m/Y') : '—' }}</span>
                                    @if($payment->cashbox)
                                        <span class="text-[9px] sm:text-[10px] text-gray-300 dark:text-gray-600">·</span>
                                        <span class="text-[9px] sm:text-[10px] text-gray-400">{{ $payment->cashbox->name }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <span class="flex-shrink-0 ml-2 sm:ml-3 text-xs sm:text-sm font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">
                            +{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format($payment->amount, 2) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-950 rounded-2xl border border-gray-100 dark:border-gray-800/60 p-4 sm:p-5">
        <h3 class="text-[9px] sm:text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 sm:mb-4">Resumen de la Inversión</h3>
        <div class="space-y-2.5 sm:space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400">Capital invertido</span>
                <span class="text-[11px] sm:text-xs font-bold text-gray-900 dark:text-white tabular-nums">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format($amount, 2) }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400">Tasa de retorno mensual</span>
                <span class="text-[11px] sm:text-xs font-bold text-indigo-600 dark:text-indigo-400 tabular-nums">{{ $opportunity->monthly_return_rate }}%</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400">Retorno estimado total</span>
                <span class="text-[11px] sm:text-xs font-bold text-indigo-600 dark:text-indigo-400 tabular-nums">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format($totalExpectedReturn - $amount, 2) }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400">Sistema de amortización</span>
                <span class="text-[11px] sm:text-xs font-bold text-gray-900 dark:text-white">{{ $amortizationType === 'frances' ? 'Francés' : 'Americano' }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400">Moneda</span>
                <span class="text-[11px] sm:text-xs font-bold text-gray-900 dark:text-white">{{ $opportunity->currency }}</span>
            </div>
            @if($startDate)
                <div class="flex items-center justify-between">
                    <span class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400">Inicio de pagos</span>
                    <span class="text-[11px] sm:text-xs font-bold text-gray-900 dark:text-white">{{ $startDate->format('d/m/Y') }}</span>
                </div>
            @endif
            @if($investor->pivot->approved_at)
                <div class="flex items-center justify-between">
                    <span class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400">Fecha de aprobación</span>
                    <span class="text-[11px] sm:text-xs font-bold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($investor->pivot->approved_at)->format('d/m/Y') }}</span>
                </div>
            @endif
            <div class="h-px bg-gray-100 dark:bg-gray-800 my-1"></div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] sm:text-xs font-bold text-gray-900 dark:text-white">Total a recibir</span>
                <span class="text-sm font-bold text-indigo-600 dark:text-indigo-400 tabular-nums">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format($totalExpectedReturn, 2) }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] sm:text-xs font-bold text-emerald-600 dark:text-emerald-400">Total recibido</span>
                <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format($totalReceived, 2) }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] sm:text-xs font-bold text-amber-600 dark:text-amber-400">Pendiente por recibir</span>
                <span class="text-sm font-bold text-amber-600 dark:text-amber-400 tabular-nums">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}{{ number_format(max(0, $totalExpectedReturn - $totalReceived), 2) }}</span>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const perPage = 10;
    const mobileItems = document.querySelectorAll('#schedule-mobile .schedule-item');
    const desktopItems = document.querySelectorAll('#schedule-table-body .schedule-item');
    const total = mobileItems.length;
    const totalPages = Math.ceil(total / perPage);
    const paginationEl = document.getElementById('schedule-pagination');
    let currentPage = 1;

    function showPage(page) {
        currentPage = page;
        const start = (page - 1) * perPage;
        const end = start + perPage;

        mobileItems.forEach(function(item) {
            const idx = parseInt(item.getAttribute('data-idx'));
            item.style.display = (idx >= start && idx < end) ? '' : 'none';
        });

        desktopItems.forEach(function(item) {
            const idx = parseInt(item.getAttribute('data-idx'));
            item.style.display = (idx >= start && idx < end) ? '' : 'none';
        });

        renderPagination();
    }

    function renderPagination() {
        paginationEl.innerHTML = '';

        if (totalPages <= 1) return;

        const prevBtn = document.createElement('button');
        prevBtn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>';
        prevBtn.className = 'w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 dark:border-gray-700 text-gray-400 hover:text-gray-900 dark:hover:text-white hover:border-gray-300 dark:hover:border-gray-600 transition-colors disabled:opacity-30 disabled:cursor-not-allowed';
        prevBtn.disabled = currentPage === 1;
        prevBtn.addEventListener('click', function() { showPage(currentPage - 1); });
        paginationEl.appendChild(prevBtn);

        var pages = getVisiblePages();

        pages.forEach(function(p) {
            if (p === '...') {
                var dots = document.createElement('span');
                dots.textContent = '...';
                dots.className = 'w-8 h-8 flex items-center justify-center text-[10px] text-gray-400';
                paginationEl.appendChild(dots);
            } else {
                var btn = document.createElement('button');
                btn.textContent = p;
                btn.className = p === currentPage
                    ? 'w-8 h-8 flex items-center justify-center rounded-lg text-[11px] font-bold bg-indigo-600 text-white'
                    : 'w-8 h-8 flex items-center justify-center rounded-lg text-[11px] font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors';
                btn.addEventListener('click', function() { showPage(p); });
                paginationEl.appendChild(btn);
            }
        });

        var nextBtn = document.createElement('button');
        nextBtn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>';
        nextBtn.className = 'w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 dark:border-gray-700 text-gray-400 hover:text-gray-900 dark:hover:text-white hover:border-gray-300 dark:hover:border-gray-600 transition-colors disabled:opacity-30 disabled:cursor-not-allowed';
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.addEventListener('click', function() { showPage(currentPage + 1); });
        paginationEl.appendChild(nextBtn);
    }

    function getVisiblePages() {
        if (totalPages <= 7) {
            var arr = [];
            for (var i = 1; i <= totalPages; i++) arr.push(i);
            return arr;
        }

        var pages = [1];

        if (currentPage > 3) pages.push('...');

        var rangeStart = Math.max(2, currentPage - 1);
        var rangeEnd = Math.min(totalPages - 1, currentPage + 1);

        for (var j = rangeStart; j <= rangeEnd; j++) pages.push(j);

        if (currentPage < totalPages - 2) pages.push('...');

        pages.push(totalPages);

        return pages;
    }

    showPage(1);
});
</script>
@endsection
