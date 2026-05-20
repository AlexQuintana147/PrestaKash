@extends('superadmin.layout')

@section('title', 'Préstamo ' . $loan->code)

@php
    $cur = $loan->currency === 'USD' ? '$' : 'S/';
    $totalPaid = $loan->installments()->where('status', 'approved')->sum('amount');
    $totalPending = $loan->installments()->where('status', 'pending')->sum('amount');
    $overdueCount = $loan->installments()->where('status', 'pending')->where('due_date', '<', now()->startOfDay())->count();
    $totalInstallments = $loan->installments()->count();
    $paidInstallments = $loan->installments()->where('status', 'approved')->count();
    $pendingInstallments = $loan->installments()->where('status', 'pending')->count();
    $totalLateFees = $loan->installments()->get()->sum('late_fee');
    $nextDueDate = $loan->installments()->where('status', 'pending')->orderBy('due_date', 'asc')->first()?->due_date;
@endphp

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6 md:py-10">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ URL::previous() }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="min-w-0 flex-1">
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $loan->code }}</h1>
            <p class="text-[11px] text-gray-400 mt-0.5">Préstamo completo</p>
        </div>
        <span class="flex-shrink-0 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide
            {{ $loan->status === 'approved' ? 'bg-emerald-500 text-white' : ($loan->status === 'paid' ? 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300' : 'bg-amber-500 text-white') }}">
            {{ match($loan->status) { 'approved' => 'Activo', 'paid' => 'Pagado', default => $loan->status } }}
        </span>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="p-4 bg-gray-50 dark:bg-gray-900/80 rounded-xl">
            <p class="text-[9px] text-gray-400 uppercase tracking-wider font-bold mb-1">Monto</p>
            <p class="text-lg font-black text-gray-900 dark:text-white tabular-nums">{{ $cur }}{{ number_format($loan->amount, 2) }}</p>
            <p class="text-[9px] text-gray-400 mt-0.5">{{ $loan->months ?? '-' }}m · {{ ($loan->interest_rate ?? 0)*100 }}%</p>
        </div>
        <div class="p-4 bg-gray-50 dark:bg-gray-900/80 rounded-xl">
            <p class="text-[9px] text-gray-400 uppercase tracking-wider font-bold mb-1">Pagado</p>
            <p class="text-lg font-black text-emerald-600 tabular-nums">{{ $cur }}{{ number_format($totalPaid, 2) }}</p>
            <p class="text-[9px] text-emerald-500 mt-0.5">{{ $paidInstallments }}/{{ $totalInstallments }}</p>
        </div>
        <div class="p-4 bg-gray-50 dark:bg-gray-900/80 rounded-xl">
            <p class="text-[9px] text-gray-400 uppercase tracking-wider font-bold mb-1">Pendiente</p>
            <p class="text-lg font-black text-orange-600 tabular-nums">{{ $cur }}{{ number_format($totalPending, 2) }}</p>
            <p class="text-[9px] text-orange-500 mt-0.5">{{ $pendingInstallments }} restantes</p>
        </div>
        <div class="p-4 bg-gray-50 dark:bg-gray-900/80 rounded-xl">
            <p class="text-[9px] text-gray-400 uppercase tracking-wider font-bold mb-1">Mora</p>
            <p class="text-lg font-black text-red-600 tabular-nums">{{ $cur }}{{ number_format($totalLateFees, 2) }}</p>
            @if($overdueCount > 0)
                <p class="text-[9px] text-red-500 mt-0.5">{{ $overdueCount }} vencida{{ $overdueCount > 1 ? 's' : '' }}</p>
            @endif
        </div>
    </div>

    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-900/80 rounded-xl">
        <p class="text-[9px] text-gray-400 uppercase tracking-wider font-bold mb-3">Información del préstamo</p>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-y-3 gap-x-4">
            <div><p class="text-[9px] text-gray-400 mb-0.5">Tipo</p><p class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ $loan->type === 'cuotas' ? 'Cuotas' : 'Pago único' }}</p></div>
            <div><p class="text-[9px] text-gray-400 mb-0.5">Moneda</p><p class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ $loan->currency === 'USD' ? 'USD' : 'PEN' }}</p></div>
            <div><p class="text-[9px] text-gray-400 mb-0.5">Plazo</p><p class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ $loan->months ?? '-' }} meses</p></div>
            <div><p class="text-[9px] text-gray-400 mb-0.5">Interés</p><p class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ number_format(($loan->interest_rate ?? 0), 2) }}% mensual</p></div>
            <div><p class="text-[9px] text-gray-400 mb-0.5">Desembolso</p><p class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ $loan->disbursement_date ? \Carbon\Carbon::parse($loan->disbursement_date)->format('d/m/Y') : '-' }}</p></div>
            <div><p class="text-[9px] text-gray-400 mb-0.5">Estado</p><p class="text-xs font-semibold {{ $loan->in_default ? 'text-red-600' : 'text-emerald-600' }}">{{ $loan->in_default ? 'Default' : 'Normal' }}</p></div>
        </div>

        @if($loan->late_fee_enabled)
        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
            <p class="text-[9px] text-orange-400 uppercase tracking-wider font-bold mb-2">Mora configurada</p>
            <div class="flex flex-wrap gap-3">
                <span class="px-2 py-1 bg-orange-50 dark:bg-orange-950/20 rounded text-[10px] font-bold text-orange-700 dark:text-orange-300">Gracia: {{ $loan->grace_days ?? 0 }}d</span>
                <span class="px-2 py-1 bg-orange-50 dark:bg-orange-950/20 rounded text-[10px] font-bold text-orange-700 dark:text-orange-300">Tasa: {{ ($loan->late_fee_rate ?? 0)*100 }}%</span>
                <span class="px-2 py-1 bg-orange-50 dark:bg-orange-950/20 rounded text-[10px] font-bold text-orange-700 dark:text-orange-300">Diaria: {{ $cur }}{{ number_format($loan->late_fee_daily_amount ?? 0, 2) }}</span>
            </div>
        </div>
        @endif
    </div>

    @if($loan->user)
    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-900/80 rounded-xl">
        <p class="text-[9px] text-gray-400 uppercase tracking-wider font-bold mb-3">Cliente</p>
        <div class="flex items-center gap-3">
            <img src="{{ $loan->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($loan->user->name) . '&background=6366f1&color=fff' }}" 
                 class="w-10 h-10 rounded-lg object-cover flex-shrink-0" alt="">
            <div class="min-w-0 flex-1">
                <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $loan->user->name }}</p>
                <p class="text-[11px] text-gray-500 truncate">{{ $loan->user->email }}</p>
            </div>
            <a href="mailto:{{ $loan->user->email }}" class="flex-shrink-0 px-3 py-1.5 bg-blue-50 dark:bg-blue-950/30 rounded-lg text-[10px] font-bold text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-950/50 transition-colors">Email</a>
            <a href="https://wa.me/51{{ preg_replace('/\D/', '', $loan->user->phone ?? '') }}" target="_blank" class="flex-shrink-0 px-3 py-1.5 bg-green-50 dark:bg-green-950/30 rounded-lg text-[10px] font-bold text-green-600 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-950/50 transition-colors">WhatsApp</a>
        </div>
    </div>
    @endif

    <div class="mb-6">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-bold text-gray-900 dark:text-white">Cuotas</p>
            <p class="text-[10px] text-gray-400">{{ $userInstallments->total() }} total</p>
        </div>

        <div class="space-y-2">
            @foreach($userInstallments as $inst)
                @php
                    $isOverdue = $inst->due_date->isPast() && $inst->status !== 'approved';
                    $isDueToday = $inst->due_date->isToday() && $inst->status !== 'approved';
                    $instTotal = $inst->amount + ($inst->late_fee ?? 0);
                @endphp

                <div class="group rounded-xl overflow-hidden {{ $isOverdue ? 'bg-red-50 dark:bg-red-950/15' : 'bg-gray-50 dark:bg-gray-900/60' }}
                    {{ $inst->status === 'approved' ? 'opacity-65' : '' }}"
                    data-installment-id="{{ $inst->id }}"
                    data-is-interest-payment="{{ isset($inst->is_interest_payment) && $inst->is_interest_payment ? 'true' : 'false' }}"
                    @if(isset($inst->is_interest_payment) && $inst->is_interest_payment && isset($inst->interest_payment))
                        data-interest-payment-id="{{ $inst->interest_payment->id }}"
                        data-paid-at="{{ $inst->interest_payment->paid_at->timestamp }}"
                    @endif
                    {{ $isOverdue || $isDueToday ? 'overdue-installment-row' : '' }}">

                    <div class="flex items-stretch">

                        <div class="w-12 flex items-center justify-center flex-shrink-0
                            {{ $inst->status === 'approved' ? 'bg-emerald-500' : ($isOverdue ? 'bg-red-500' : 'bg-gray-200 dark:bg-gray-700') }}">
                            <span class="text-xs font-black text-white">{{ $inst->installment_number }}</span>
                        </div>

                        <div class="flex-1 min-w-0 px-4 py-3 flex flex-col justify-center">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-bold {{ $isOverdue ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">{{ $inst->due_date->format('d/m/Y') }}</span>
                                <span class="px-1.5 py-0.5 rounded text-[8px] font-bold uppercase
                                    {{ $inst->status === 'approved' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400' :
                                       ($inst->status === 'processing' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-400 animate-pulse' :
                                       ($inst->status === 'rejected' ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400' :
                                       ($isOverdue ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-400'))) }}">
                                    {{ match ($inst->status) {
                                        'approved' => 'Pagado',
                                        'pending' => $isOverdue ? 'Vencida' : 'Pendiente',
                                        'processing' => 'Validar',
                                        'rejected' => 'Rechazado',
                                        default => $inst->status
                                    } }}
                                </span>
                            </div>
                            <div class="flex items-center gap-3 text-[10px]">
                                <span class="text-gray-500 dark:text-gray-400">Capital: <strong class="text-gray-700 dark:text-gray-300 tabular-nums">{{ $cur }}{{ number_format($inst->capital ?? 0, 2) }}</strong></span>
                                <span class="text-gray-500 dark:text-gray-400">Interés: <strong class="text-gray-700 dark:text-gray-300 tabular-nums">{{ $cur }}{{ number_format($inst->interest ?? 0, 2) }}</strong></span>
                                @if($inst->late_fee > 0)
                                    <span class="font-bold text-orange-600 dark:text-orange-400 tabular-nums">+{{ $cur }}{{ number_format($inst->late_fee, 2) }} mora</span>
                                @endif
                            </div>
                        </div>

                        <div class="w-28 flex items-center justify-end px-3 py-3 flex-shrink-0 border-l border-gray-100 dark:border-gray-800">
                            <p class="text-base font-black {{ $isOverdue ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }} tabular-nums text-right">{{ $cur }}{{ number_format($instTotal, 2) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @if($userInstallments->hasPages())
    <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-800">
        @if(!$userInstallments->onFirstPage())
            <a href="{{ route('superadmin.installments.group', $loan) }}?page={{ $userInstallments->currentPage() - 1 }}&source=modal"
                class="px-4 py-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg text-xs font-bold text-gray-700 dark:text-gray-300 transition-colors">← Anterior</a>
        @else
            <div></div>
        @endif

        <div class="flex items-center gap-1">
            @for($i = 1; $i <= $userInstallments->lastPage(); $i++)
                @if($i == $userInstallments->currentPage())
                    <span class="w-7 h-7 flex items-center justify-center rounded-md bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-[10px] font-bold">{{ $i }}</span>
                @elseif(abs($i - $userInstallments->currentPage()) <= 2 || $i == 1 || $i == $userInstallments->lastPage())
                    <a href="{{ route('superadmin.installments.group', $loan) }}?page={{ $i }}&source=modal"
                        class="w-7 h-7 flex items-center justify-center rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 text-[10px] font-bold text-gray-500 dark:text-gray-400 transition-colors">{{ $i }}</a>
                @elseif(abs($i - $userInstallments->currentPage()) == 3)
                    <span class="text-gray-300 dark:text-gray-700 text-[10px]">...</span>
                @endif
            @endfor
        </div>

        @if($userInstallments->hasMorePages())
            <a href="{{ route('superadmin.installments.group', $loan) }}?page={{ $userInstallments->currentPage() + 1 }}&source=modal"
                class="px-4 py-2 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 rounded-lg text-xs font-bold text-white dark:text-gray-900 transition-colors">Siguiente →</a>
        @else
            <div></div>
        @endif
    </div>
    @endif

</div>
@endsection