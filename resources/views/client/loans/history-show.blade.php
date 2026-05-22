@extends('client.layout')

@section('title', 'Detalle del Préstamo')

@section('content')
@php
    $cur = $loan->currency === 'USD' ? '$' : 'S/';
    $total_i = $loan->installments->count();
    $paid_i  = $loan->installments->where('status', 'approved')->count();
    // Una cuota es pagable si es la primera que no está aprobada ni en revisión
    $hasAnyProcessing = $loan->installments->where('status', 'processing')->count() > 0;
    $firstPayable = $hasAnyProcessing ? null : $loan->installments->whereIn('status', ['pending', 'rejected'])->sortBy('installment_number')->first();
    $firstNonApproved = $loan->installments->where('status', '!=', 'approved')->sortBy('installment_number')->first();
    $progress = $total_i > 0 ? round(($paid_i / $total_i) * 100) : 0;

    $isInDefault = $loan->in_default;
    $overdueInstallments = $loan->overdue_installments;
    $accumulatedDebt = $loan->accumulated_overdue_debt;
    $nextInst = $loan->next_installment;
    $totalLateFees = $overdueInstallments->sum('late_fee');
    $hasPreviousDebts = $overdueInstallments->count() > 1;
    $moraPlusInterest = $totalLateFees + ($nextInst ? $nextInst->interest : 0);
    $moraPlusFull = $totalLateFees + ($nextInst ? $nextInst->amount : 0);
@endphp

<div class="min-h-screen px-4 md:px-8 py-6 max-w-2xl mx-auto">

    <!-- Back -->
    <a href="{{ route('client.loans.history') }}"
        class="inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors mb-8">
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
        </svg>
        Volver
    </a>

    @if(session('success'))
        <div class="mb-6 flex items-center gap-2 text-xs font-bold text-green-500 uppercase tracking-widest">
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Header -->
    <div class="flex items-start justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                Préstamo
            </h1>
            <p class="text-[10px] text-gray-400 uppercase tracking-widest mt-1">{{ $loan->created_at->format('d M Y') }}</p>
        </div>
        <span class="flex-shrink-0 px-3 py-1 text-[9px] font-black uppercase tracking-widest rounded-full mt-1
            {{ $loan->status === 'approved' ? 'text-green-600 dark:text-green-400' : '' }}
            {{ $loan->status === 'paid'     ? 'text-blue-600 dark:text-blue-400' : '' }}
            {{ $loan->status === 'pending'  ? 'text-yellow-600 dark:text-yellow-400' : '' }}">
            {{ match($loan->status) { 'approved' => 'Activo', 'paid' => 'Completado', 'pending' => 'En revisión', default => $loan->status } }}
        </span>
    </div>

    <!-- Resumen financiero -->
    @php
        $totalToPayNow = $accumulatedDebt + ($nextInst ? $nextInst->amount : 0);
    @endphp

    @if($isInDefault && $overdueInstallments->count() > 0)
        <div class="mb-8 bg-red-50 dark:bg-red-500/5 border border-red-100 dark:border-red-500/20 rounded-2xl p-6">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-red-500/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-black text-red-600 dark:text-red-400 uppercase tracking-widest mb-1">Estado de Mora</h3>
                    <p class="text-[11px] text-red-500/80 leading-relaxed mb-4">
                        Tienes mora pendiente. Debes regularizar tu situación antes de realizar otros pagos.
                    </p>

                    <div class="space-y-2 mb-6">
                        <div class="flex justify-between text-[10px]">
                            <span class="text-gray-500 uppercase font-bold tracking-widest">Cuotas vencidas ({{ $overdueInstallments->count() }})</span>
                            <span class="font-black text-gray-900 dark:text-white">{{ $cur }} {{ number_format($overdueInstallments->sum('amount'), 2) }}</span>
                        </div>
                        <div class="flex justify-between text-[10px]">
                            <span class="text-gray-500 uppercase font-bold tracking-widest">Mora acumulada</span>
                            <span class="font-black text-orange-500">+ {{ $cur }} {{ number_format($totalLateFees, 2) }}</span>
                        </div>
                        @if($nextInst)
                        <div class="flex justify-between text-[10px]">
                            <span class="text-gray-500 uppercase font-bold tracking-widest">Interés de cuota actual</span>
                            <span class="font-black text-gray-900 dark:text-white">{{ $cur }} {{ number_format($nextInst->interest, 2) }}</span>
                        </div>
                        @endif
                    </div>

                    @if($firstPayable && in_array($firstPayable->status, ['pending', 'rejected']))
                        <button type="button"
                            onclick="window._cur='{{ $cur }}'; openUploadModal({{ $firstPayable->id }}, '{{ $cur }}{{ number_format($moraPlusInterest, 2) }}', {{ $totalLateFees }}, {{ $nextInst ? $nextInst->interest : 0 }}, 'mora_interest')"
                            class="w-full py-3 bg-red-600 hover:bg-red-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-red-500/20 mb-3">
                            Pagar mora acumulada + interés — {{ $cur }} {{ number_format($moraPlusInterest, 2) }}
                        </button>

                        @if(!$hasPreviousDebts)
                        <button type="button"
                            onclick="window._cur='{{ $cur }}'; openUploadModal({{ $firstPayable->id }}, '{{ $cur }}{{ number_format($moraPlusFull, 2) }}', {{ $totalLateFees }}, {{ $nextInst ? $nextInst->interest : 0 }}, 'mora_full')"
                            class="w-full py-3 bg-orange-500 hover:bg-orange-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-orange-500/20 mb-3">
                            Pagar mora + cuota total — {{ $cur }} {{ number_format($moraPlusFull, 2) }}
                        </button>
                        @endif
                    @endif

                    <div class="bg-white/50 dark:bg-black/20 rounded-xl p-4">
                        <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-2">¿Qué significa?</p>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 leading-relaxed">
                            Al estar en mora, primero debes pagar la mora acumulada. "Mora + interés" cubre la mora y el interés de tu cuota actual. "Mora + cuota total" cubre la mora y la cuota completa (solo disponible si no tienes deudas anteriores).
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @elseif($accumulatedDebt > 0)
        <div class="mb-8 bg-red-50 dark:bg-red-500/5 border border-red-100 dark:border-red-500/20 rounded-2xl p-6">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-red-500/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-black text-red-600 dark:text-red-400 uppercase tracking-widest mb-1">Deuda Acumulada</h3>
                    <p class="text-[11px] text-red-500/80 leading-relaxed mb-4">
                        Tienes cuotas vencidas. Para regularizar tu situación, debes pagar las cuotas atrasadas más la mora generada y, si lo deseas, la cuota actual.
                    </p>

                    <div class="space-y-2 mb-6">
                        <div class="flex justify-between text-[10px]">
                            <span class="text-gray-500 uppercase font-bold tracking-widest">Cuotas vencidas ({{ $overdueInstallments->count() }})</span>
                            <span class="font-black text-gray-900 dark:text-white">{{ $cur }} {{ number_format($overdueInstallments->sum('amount'), 2) }}</span>
                        </div>
                        <div class="flex justify-between text-[10px]">
                            <span class="text-gray-500 uppercase font-bold tracking-widest">Mora total</span>
                            <span class="font-black text-orange-500">+ {{ $cur }} {{ number_format($overdueInstallments->sum('late_fee'), 2) }}</span>
                        </div>
                        @if($nextInst)
                        <div class="flex justify-between text-[10px] pt-2 border-t border-red-100 dark:border-red-500/10">
                            <span class="text-gray-500 uppercase font-bold tracking-widest">Siguiente cuota ({{ $nextInst->due_date->format('d/m') }})</span>
                            <span class="font-black text-gray-900 dark:text-white">{{ $cur }} {{ number_format($nextInst->amount, 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-xs pt-2 border-t border-red-200 dark:border-red-500/30">
                            <span class="text-gray-900 dark:text-white uppercase font-black tracking-widest">Total a pagar hoy</span>
                            <span class="font-black text-red-600 dark:text-red-400">{{ $cur }} {{ number_format($totalToPayNow, 2) }}</span>
                        </div>
                    </div>

                    @if($firstPayable && in_array($firstPayable->status, ['pending', 'rejected']))
                    <button type="button"
                        onclick="window._cur='{{ $cur }}'; openUploadModal({{ $firstPayable->id }}, '{{ $cur }}{{ number_format($firstPayable->amount, 2) }}', {{ $firstPayable->late_fee }}, {{ $firstPayable->interest }})"
                        class="w-full py-3 bg-red-600 hover:bg-red-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-red-500/20 mb-6">
                        Pagar cuota pendiente
                    </button>
                    @endif

                    <div class="bg-white/50 dark:bg-black/20 rounded-xl p-4">
                        <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-2">¿Cómo funciona?</p>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 leading-relaxed">
                            Al no pagar en la fecha correspondiente, se genera una mora diaria según lo acordado. La mora se acumula hasta la fecha de la siguiente cuota. Para evitar que la deuda siga creciendo, te recomendamos realizar el pago total lo antes posible.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @php
        $totalPaidAmount = $loan->installments->where('status', 'approved')->sum('amount');
        $paidCapital = $loan->installments->where('status', 'approved')->sum('capital');
        $paidInterest = $loan->installments->where('status', 'approved')->sum('interest');
        $paidLateFees = 0;
        foreach ($loan->installments->where('status', 'approved') as $pi) {
            if ($pi->paid_at && $loan->late_fee_enabled) {
                $paidDate = $pi->paid_at->startOfDay();
                $dueDate = $pi->due_date->startOfDay();
                if ($paidDate->isAfter($dueDate)) {
                    $daysLate = $dueDate->diffInDays($paidDate);
                    if ($daysLate > ($loan->grace_days ?? 0)) {
                        $daysForCalc = $daysLate - ($loan->grace_days ?? 0);
                        $paidLateFees += round($pi->amount * ($loan->late_fee_rate / 100) * $daysForCalc, 2);
                    }
                }
            }
        }
        $totalPaidWithFees = $totalPaidAmount + $paidLateFees;
        $pendingFromTotal = max(0, $loan->total_amount - $totalPaidAmount);
        $pendingLateFeesNow = $overdueInstallments->sum('late_fee');
        $totalRemaining = $pendingFromTotal + $pendingLateFeesNow;
        $pendingCapital = max(0, $loan->amount - $paidCapital);
        $pendingInterest = max(0, ($loan->total_amount - $loan->amount) - $paidInterest);
    @endphp

    <div class="grid grid-cols-2 gap-3 mb-4">
        <div class="bg-emerald-50 dark:bg-emerald-950/15 rounded-2xl px-4 py-4 border border-emerald-100 dark:border-emerald-900/30">
            <p class="text-[8px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400 mb-1">Pagado</p>
            <p class="text-lg font-black text-emerald-700 dark:text-emerald-300 tabular-nums">{{ $cur }} {{ number_format($totalPaidWithFees, 2) }}</p>
            <div class="mt-1.5 space-y-0.5">
                <p class="text-[8px] text-emerald-500/70">Capital: {{ $cur }}{{ number_format($paidCapital, 2) }}</p>
                <p class="text-[8px] text-emerald-500/70">Interés: {{ $cur }}{{ number_format($paidInterest, 2) }}</p>
                @if($paidLateFees > 0)
                <p class="text-[8px] text-orange-500/70">Mora: {{ $cur }}{{ number_format($paidLateFees, 2) }}</p>
                @endif
            </div>
        </div>
        <div class="bg-orange-50 dark:bg-orange-950/15 rounded-2xl px-4 py-4 border border-orange-100 dark:border-orange-900/30">
            <p class="text-[8px] font-black uppercase tracking-widest text-orange-600 dark:text-orange-400 mb-1">Pendiente</p>
            <p class="text-lg font-black text-orange-700 dark:text-orange-300 tabular-nums">{{ $cur }} {{ number_format($totalRemaining, 2) }}</p>
            <div class="mt-1.5 space-y-0.5">
                <p class="text-[8px] text-orange-500/70">Capital: {{ $cur }}{{ number_format($pendingCapital, 2) }}</p>
                <p class="text-[8px] text-orange-500/70">Interés: {{ $cur }}{{ number_format($pendingInterest, 2) }}</p>
                @if($overdueInstallments->sum('late_fee') > 0)
                <p class="text-[8px] text-red-500/70">Mora: {{ $cur }}{{ number_format($overdueInstallments->sum('late_fee'), 2) }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-px bg-gray-100 dark:bg-gray-900 rounded-2xl overflow-hidden mb-6">
        <div class="bg-white dark:bg-gray-950 px-4 py-4">
            <p class="text-[8px] font-black uppercase tracking-widest text-gray-400 mb-1">Prestado</p>
            <p class="text-base font-black text-gray-900 dark:text-white tabular-nums">{{ $cur }} {{ number_format($loan->amount, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-950 px-4 py-4">
            <p class="text-[8px] font-black uppercase tracking-widest text-gray-400 mb-1">Total a pagar</p>
            <p class="text-base font-black text-gray-900 dark:text-white tabular-nums">{{ $cur }} {{ number_format($loan->total_amount, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-950 px-4 py-4">
            <p class="text-[8px] font-black uppercase tracking-widest text-gray-400 mb-1">Por cuota</p>
            <p class="text-base font-black text-gray-900 dark:text-white tabular-nums">{{ $cur }} {{ number_format($loan->monthly_payment, 2) }}</p>
        </div>
    </div>

    <!-- Progreso -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Progreso</p>
            <p class="text-[9px] font-black uppercase tracking-widest text-gray-900 dark:text-white">{{ $paid_i }} / {{ $total_i }} cuotas</p>
        </div>
        <div class="h-1 bg-gray-100 dark:bg-gray-900 rounded-full overflow-hidden">
            <div class="h-full bg-gray-900 dark:bg-white rounded-full transition-all" style="width: {{ $progress }}%"></div>
        </div>
    </div>

    <!-- Info clave -->
    <div class="grid grid-cols-2 gap-3 mb-8">
        @foreach([
            'Vencimiento próximo' => $firstNonApproved ? $firstNonApproved->due_date->format('d M Y') : '—',
            'Frecuencia'          => ucfirst($loan->payment_frequency),
            'Banco destino'       => $loan->bank_name,
            'Desembolso'          => $loan->disbursement_date ? \Carbon\Carbon::parse($loan->disbursement_date)->format('d M Y') : '—',
        ] as $label => $value)
        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl px-4 py-3">
            <p class="text-[8px] font-black uppercase tracking-widest text-gray-400 mb-1">{{ $label }}</p>
            <p class="text-xs font-bold text-gray-900 dark:text-white">{{ $value }}</p>
        </div>
        @endforeach
    </div>

    <!-- Cronograma -->
    <div>
        <!-- Título + filtros -->
        <div class="flex items-center justify-between mb-4">
            <p class="text-[9px] font-black uppercase tracking-widest text-gray-900 dark:text-white">Cronograma</p>
            <div class="flex items-center gap-0 border-b border-gray-100 dark:border-gray-900">
                @foreach(['all' => 'Todas', 'pending' => 'Pendientes', 'paid' => 'Pagadas'] as $fkey => $flabel)
                <button type="button"
                    onclick="setFilter('{{ $fkey }}')"
                    id="filter-{{ $fkey }}"
                    class="filter-btn relative px-3 py-2 text-[9px] font-black uppercase tracking-widest transition-colors
                        {{ $fkey === 'all' ? 'text-gray-900 dark:text-white' : 'text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                    {{ $flabel }}
                    <span id="filter-line-{{ $fkey }}"
                        class="absolute bottom-0 left-0 right-0 h-px bg-gray-900 dark:bg-white {{ $fkey !== 'all' ? 'hidden' : '' }}"></span>
                </button>
                @endforeach
            </div>
        </div>

        <!-- Lista de cuotas -->
        <div id="installments-list" class="divide-y divide-gray-100 dark:divide-gray-900">
            @foreach($loan->installments->sortBy('installment_number') as $inst)
            @php
                $isOverdue = $inst->due_date->isPast() && $inst->status !== 'approved';
                $canPay = $firstPayable && $firstPayable->id === $inst->id;
                $statusKey = $inst->status === 'approved' ? 'paid' : 'pending';
            @endphp
            <div class="inst-row py-4 flex items-center gap-4" data-status="{{ $statusKey }}">
                <!-- Número -->
                <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0
                    {{ $inst->status === 'approved' ? 'bg-green-500/10' : ($isOverdue ? 'bg-red-500/10' : 'bg-gray-100 dark:bg-gray-900') }}">
                    <span class="text-[9px] font-black tabular-nums
                        {{ $inst->status === 'approved' ? 'text-green-500' : ($isOverdue ? 'text-red-500' : 'text-gray-400') }}">
                        {{ $inst->installment_number }}
                    </span>
                </div>

                <!-- Fecha + monto -->
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-gray-900 dark:text-white">
                        @if($inst->id === ($nextInst ? $nextInst->id : null) && $overdueInstallments->count() > 0)
                            {{ $cur }} {{ number_format($inst->accumulated_debt['total'], 2) }}
                            <span class="text-[8px] text-gray-400 font-normal block">Incluye {{ $overdueInstallments->count() }} cuotas anteriores + mora</span>
                        @else
                            {{ $cur }} {{ number_format($inst->amount, 2) }}
                            <span class="text-[8px] text-gray-400 font-normal block">
                                Capital: {{ $cur }}{{ number_format($inst->capital, 2) }} • 
                                Interés: {{ $cur }}{{ number_format($inst->interest, 2) }}
                                @if($inst->late_fee > 0)
                                    • <span class="text-orange-500">Mora: {{ $cur }}{{ number_format($inst->late_fee, 2) }}</span>
                                @endif
                            </span>
                        @endif
                    </p>
                    <p class="text-[9px] {{ $isOverdue ? 'text-red-500' : 'text-gray-400' }} mt-0.5">
                        {{ $inst->due_date->format('d M Y') }}
                        @if($inst->late_fee > 0 && !($inst->id === ($nextInst ? $nextInst->id : null) && $overdueInstallments->count() > 0))
                            <span class="ml-2 text-gray-900 dark:text-white">Total: {{ $cur }} {{ number_format($inst->total_with_mora, 2) }}</span>
                        @elseif($inst->grace_days_remaining > 0)
                            <span class="ml-2 text-blue-500 font-bold uppercase tracking-widest">Quedan {{ $inst->grace_days_remaining }} días de gracia</span>
                        @endif
                    </p>
                    @if($inst->status === 'rejected' && $inst->admin_notes)
                        <p class="text-[8px] text-red-500 italic mt-0.5">"{{ $inst->admin_notes }}"</p>
                    @endif
                </div>

                <!-- Estado / Acción -->
                <div class="flex items-center gap-3 flex-shrink-0">
                    <div class="text-right">
                        <span class="block text-[8px] font-black uppercase tracking-widest
                            {{ $inst->status === 'approved'   ? 'text-green-500' : '' }}
                            {{ $inst->status === 'pending'    ? 'text-gray-300 dark:text-gray-700' : '' }}
                            {{ $inst->status === 'processing' ? 'text-yellow-500' : '' }}
                            {{ $inst->status === 'rejected'   ? 'text-red-500' : '' }}">
                            {{ match($inst->status) {
                                'approved'   => 'Pagado',
                                'pending'    => 'Pendiente',
                                'processing' => 'En revisión',
                                'rejected'   => 'Rechazado',
                                default      => $inst->status
                            } }}
                        </span>
                        @if($inst->interest_payments_count > 0)
                            <span class="block text-[7px] font-bold mt-0.5 text-purple-500">
                                {{ $inst->interest_payments_count }}x Interés pagado
                            </span>
                        @endif
                        @if($inst->status === 'approved' && $inst->payment_status_detail)
                            @php $detail = $inst->payment_status_detail; @endphp
                            <span class="block text-[7px] font-bold mt-0.5
                                {{ $detail['type'] === 'before' || $detail['type'] === 'same_day' ? 'text-green-400' : '' }}
                                {{ $detail['type'] === 'grace' ? 'text-blue-500' : '' }}
                                {{ $detail['type'] === 'late' ? 'text-orange-500' : '' }}">
                                {{ $detail['label'] }}
                            </span>
                        @endif
                    </div>

                    @if($canPay && !$isInDefault)
                        @php
                            $isNextWithOverdue = $inst->id === ($nextInst ? $nextInst->id : null) && $overdueInstallments->count() > 0;
                            $payAmount = $isNextWithOverdue ? $inst->accumulated_debt['base'] : $inst->amount;
                            $payMora = $isNextWithOverdue ? $inst->accumulated_debt['mora'] : $inst->late_fee;
                            $payInterest = $isNextWithOverdue ? $inst->accumulated_debt['mora'] + $inst->interest : $inst->interest;
                        @endphp
                        <button type="button"
                            onclick="window._cur='{{ $cur }}'; openUploadModal({{ $inst->id }}, '{{ $cur }}{{ number_format($payAmount, 2) }}', {{ $payMora }}, {{ $inst->interest }})"
                            class="text-[9px] font-black uppercase tracking-widest text-gray-900 dark:text-white border-b border-gray-900 dark:border-white pb-px hover:opacity-60 transition-opacity">
                            Pagar
                        </button>
                    @elseif($canPay && $isInDefault)
                        <span class="text-[8px] font-black uppercase tracking-widest text-red-400">
                            Bloqueado
                        </span>
                    @elseif($inst->payment_voucher && $inst->status !== 'pending')
                        <a href="{{ Storage::url($inst->payment_voucher) }}" target="_blank"
                            class="text-gray-300 dark:text-gray-700 hover:text-gray-600 dark:hover:text-gray-400 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <!-- Paginación -->
        <div id="pagination" class="mt-6 flex items-center justify-between">
            <button id="btn-prev" onclick="changePage(-1)"
                class="text-[9px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-300 dark:border-gray-700 pb-px hover:text-gray-900 dark:hover:text-white hover:border-gray-900 dark:hover:border-white transition-colors disabled:opacity-30 disabled:pointer-events-none">
                ← Anterior
            </button>
            <span id="page-info" class="text-[9px] font-bold text-gray-400 tabular-nums"></span>
            <button id="btn-next" onclick="changePage(1)"
                class="text-[9px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-300 dark:border-gray-700 pb-px hover:text-gray-900 dark:hover:text-white hover:border-gray-900 dark:hover:border-white transition-colors disabled:opacity-30 disabled:pointer-events-none">
                Siguiente →
            </button>
        </div>
    </div>
</div>

<!-- Modal Pago -->
<style>
    #uploadModal select {
        color-scheme: light;
    }
    .dark #uploadModal select {
        color-scheme: dark;
    }
    #uploadModal select option {
        background-color: #ffffff;
        color: #111827;
    }
    .dark #uploadModal select option {
        background-color: #030712;
        color: #f3f4f6;
    }
</style>
<div id="uploadModal"
    class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4"
    onclick="if(event.target===this) closeUploadModal()">
    <div class="bg-white dark:bg-gray-950 w-full max-w-sm rounded-2xl shadow-2xl p-6 relative">
        <button onclick="closeUploadModal()"
            class="absolute top-5 right-5 text-gray-300 dark:text-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <p class="text-[8px] font-black uppercase tracking-widest text-gray-400 mb-1">Confirmar pago</p>
        <p class="text-lg font-black text-gray-900 dark:text-white mb-1">
            Monto: <span id="modal_amount_text" class="text-gray-900 dark:text-white"></span>
        </p>
        <p class="text-[9px] font-bold uppercase tracking-widest text-gray-400 mb-6">
            Sistema: 
            @if($loan->type === 'unico')
                <span class="text-blue-500">Único pago</span>
            @elseif($loan->amortization_system === 'americano')
                <span class="text-amber-500">Americano</span>
            @else
                <span class="text-purple-500">Francés</span>
            @endif
        </p>

        <form id="uploadForm" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Payment Type -->
            <div class="space-y-3">
                <p class="text-[8px] font-black uppercase tracking-widest text-gray-400">Tipo de pago</p>
                <div class="flex gap-2">
                    <label class="flex-1 cursor-pointer group">
                        <input type="radio" name="requested_type" value="full" class="hidden peer" checked onchange="updateModalAmount('full')">
                        <div class="py-2 text-center text-[10px] font-black tracking-widest text-gray-400 dark:text-gray-600 peer-checked:text-purple-600 transition-all border-b-2 border-transparent peer-checked:border-purple-600 hover:text-gray-600 dark:hover:text-gray-400">
                            CUOTA COMPLETA
                        </div>
                    </label>
                    @if($loan->type === 'cuotas' && $loan->amortization_system === 'frances')
                    <label id="interest-only-option" class="flex-1 cursor-pointer group">
                        <input type="radio" name="requested_type" value="interest" class="hidden peer" onchange="updateModalAmount('interest')">
                        <div class="py-2 text-center text-[10px] font-black tracking-widest text-gray-400 dark:text-gray-600 peer-checked:text-amber-600 transition-all border-b-2 border-transparent peer-checked:border-amber-600 hover:text-gray-600 dark:hover:text-gray-400">
                            SOLO INTERÉS
                        </div>
                    </label>
                    @endif
                </div>
                <p id="interest-only-warning" class="hidden text-[8px] text-amber-500 font-bold uppercase leading-tight italic">
                    * Esta opción es exclusiva si no puedes pagar la cuota pactada. Se cobrará solo el interés y todas las cuotas se postergarán un periodo.
                </p>
            </div>
            
            <!-- Payment Method -->
            <div class="space-y-3">
                <p class="text-[8px] font-black uppercase tracking-widest text-gray-400">Método de pago</p>
                <div class="flex gap-2">
                    <label class="flex-1 cursor-pointer group">
                        <input type="radio" name="payment_method" value="transfer" class="hidden peer" checked onchange="togglePaymentFields(this.value)">
                        <div class="py-2 text-center text-[10px] font-black tracking-widest text-gray-400 dark:text-gray-600 peer-checked:text-blue-600 transition-all border-b-2 border-transparent peer-checked:border-blue-600 hover:text-gray-600 dark:hover:text-gray-400">
                            TRANSFERENCIA
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer group">
                        <input type="radio" name="payment_method" value="cash" class="hidden peer" onchange="togglePaymentFields(this.value)">
                        <div class="py-2 text-center text-[10px] font-black tracking-widest text-gray-400 dark:text-gray-600 peer-checked:text-green-600 transition-all border-b-2 border-transparent peer-checked:border-green-600 hover:text-gray-600 dark:hover:text-gray-400">
                            EFECTIVO
                        </div>
                    </label>
                </div>
            </div>

            <!-- Transfer Details -->
            <div id="transfer-details" class="space-y-4">
                <div class="group relative">
                    <label class="text-[8px] font-black text-gray-400 uppercase tracking-widest block mb-1">Banco emisor</label>
                    <select name="bank_name" id="modal_bank_name"
                            onchange="toggleCustomBank(this.value)"
                            class="w-full bg-transparent border-0 border-b border-gray-100 dark:border-gray-900 py-2 pr-6 text-xs font-bold text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition-all appearance-none cursor-pointer relative z-10">
                        <option value="" disabled selected class="bg-white dark:bg-gray-950 text-gray-400 dark:text-gray-500">Elegir banco</option>
                        @foreach(['BCP', 'Interbank', 'BBVA', 'Scotiabank', 'Banco de la Nación', 'Yape', 'Plin', 'Otros'] as $bank)
                            <option value="{{ $bank }}" class="bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 py-2">{{ $bank }}</option>
                        @endforeach
                    </select>
                    <div class="absolute right-0 bottom-2 pointer-events-none text-gray-400 dark:text-gray-500 z-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
                <div id="modal_bank_custom_container" class="group hidden">
                    <label class="text-[8px] font-black text-gray-400 uppercase tracking-widest block mb-1">Nombre del banco</label>
                    <input type="text" name="bank_name_custom" id="modal_bank_name_custom" placeholder="Ej: Mi Banco"
                           oninput="this.value = this.value.replace(/[^a-zA-ZñÑáéíóúÁÉÍÓÚ\s]/g, '')"
                           class="w-full bg-transparent border-0 border-b border-gray-100 dark:border-gray-900 py-2 text-xs font-bold text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 transition-all placeholder:text-gray-200 dark:placeholder:text-gray-800">
                </div>
                <div class="group">
                    <label class="text-[8px] font-black text-gray-400 uppercase tracking-widest block mb-1">N° de operación</label>
                    <input type="text" name="operation_number" id="modal_operation_number" placeholder="Ej: 123456"
                           maxlength="25"
                           oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')"
                           class="w-full bg-transparent border-0 border-b border-gray-100 dark:border-gray-900 py-2 text-xs font-bold text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 transition-all placeholder:text-gray-200 dark:placeholder:text-gray-800">
                </div>
            </div>

            <label for="payment_voucher" class="cursor-pointer block">
                <div class="border border-dashed border-gray-200 dark:border-gray-800 rounded-xl p-8 text-center hover:border-gray-400 dark:hover:border-gray-600 transition-colors">
                    <div id="previewContainer" class="hidden mb-3">
                        <img id="voucherPreview" class="w-full h-28 object-contain rounded-lg mx-auto">
                    </div>
                    <div id="uploadPlaceholder">
                        <svg class="w-6 h-6 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p id="upload-label" class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Subir comprobante</p>
                    </div>
                </div>
                <input type="file" name="payment_voucher" id="payment_voucher" class="hidden" accept="image/*" onchange="previewImage(this)">
            </label>

            <button type="submit"
                class="w-full text-[9px] font-black uppercase tracking-widest text-gray-900 dark:text-white border-b border-gray-900 dark:border-white pb-px hover:opacity-60 transition-opacity text-center">
                Enviar para verificación
            </button>
        </form>
    </div>
</div>

<script>
const PER_PAGE = 5;
let currentPage = 1;
let currentFilter = 'all';

function getRows() {
    return Array.from(document.querySelectorAll('.inst-row')).filter(r => {
        if (currentFilter === 'all') return true;
        return r.dataset.status === currentFilter;
    });
}

function render() {
    const rows = getRows();
    const total = rows.length;
    const totalPages = Math.max(1, Math.ceil(total / PER_PAGE));
    if (currentPage > totalPages) currentPage = totalPages;

    // Hide all, show current page
    document.querySelectorAll('.inst-row').forEach(r => r.classList.add('hidden'));
    const start = (currentPage - 1) * PER_PAGE;
    rows.slice(start, start + PER_PAGE).forEach(r => r.classList.remove('hidden'));

    // Pagination controls
    document.getElementById('page-info').textContent = totalPages > 1 ? `${currentPage} / ${totalPages}` : '';
    document.getElementById('btn-prev').disabled = currentPage === 1;
    document.getElementById('btn-next').disabled = currentPage === totalPages;
    document.getElementById('pagination').style.display = totalPages <= 1 ? 'none' : 'flex';
}

function setFilter(f) {
    currentFilter = f;
    currentPage = 1;

    // Update tab styles
    ['all', 'pending', 'paid'].forEach(k => {
        const btn = document.getElementById('filter-' + k);
        const line = document.getElementById('filter-line-' + k);
        if (k === f) {
            btn.classList.replace('text-gray-400', 'text-gray-900');
            btn.classList.add('dark:text-white');
            line.classList.remove('hidden');
        } else {
            btn.classList.replace('text-gray-900', 'text-gray-400');
            btn.classList.remove('dark:text-white');
            line.classList.add('hidden');
        }
    });

    render();
}

function changePage(delta) {
    currentPage += delta;
    render();
    document.getElementById('installments-list').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

let _currentInstData = null;

function openUploadModal(instId, amount, lateFee = 0, interest = 0, paymentType = null) {
    const baseAmount = parseFloat(amount.replace(/[^0-9.-]+/g,""));
    _currentInstData = {
        id: instId,
        fullAmount: baseAmount + lateFee,
        interestAmount: interest + lateFee,
        amountStr: amount,
        lateFee: lateFee,
        paymentType: paymentType
    };

    const isMoraPayment = paymentType === 'mora_interest' || paymentType === 'mora_full';

    if (isMoraPayment) {
        updateModalAmount(paymentType === 'mora_full' ? 'full' : 'interest');
    } else {
        updateModalAmount('full');
    }

    const interestOption = document.getElementById('interest-only-option');
    if (interestOption) {
        if (isMoraPayment) {
            interestOption.classList.add('hidden');
        } else if (interest <= 0 && lateFee <= 0) {
            interestOption.classList.add('hidden');
        } else {
            interestOption.classList.remove('hidden');
        }
    }

    document.getElementById('uploadForm').action = `/panel/historial-crediticio/pagar/${instId}`;
    document.getElementById('uploadModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function updateModalAmount(type) {
    if (!_currentInstData) return;

    const amountEl = document.getElementById('modal_amount_text');
    const warningEl = document.getElementById('interest-only-warning');
    const isInterest = type === 'interest';
    const isMoraPayment = _currentInstData.paymentType === 'mora_interest' || _currentInstData.paymentType === 'mora_full';

    const amountToDisplay = isInterest ? _currentInstData.interestAmount : _currentInstData.fullAmount;
    
    amountEl.textContent = (window._cur || '') + ' ' + amountToDisplay.toFixed(2);
    
    if (_currentInstData.lateFee > 0) {
        amountEl.innerHTML += `<br><span class="text-[10px] text-orange-500 uppercase font-bold">Incluye ${_currentInstData.lateFee.toFixed(2)} de mora</span>`;
    }

    if (isMoraPayment) {
        const moraLabel = _currentInstData.paymentType === 'mora_full' ? 'MORA + CUOTA TOTAL' : 'MORA + INTERÉS';
        amountEl.innerHTML += `<br><span class="text-[10px] text-red-500 uppercase font-bold">${moraLabel}</span>`;
    }

    const requestedTypeInput = document.querySelector('input[name="requested_type"]');
    if (requestedTypeInput) {
        if (isMoraPayment) {
            requestedTypeInput.value = _currentInstData.paymentType;
        } else {
            requestedTypeInput.value = isInterest ? 'interest' : 'full';
        }
    }

    if (warningEl) {
        if (isInterest && !isMoraPayment) {
            warningEl.classList.remove('hidden');
        } else {
            warningEl.classList.add('hidden');
        }
    }
}

function closeUploadModal() {
    document.getElementById('uploadModal').classList.add('hidden');
    document.body.style.overflow = '';
    document.getElementById('uploadForm').reset();
    document.getElementById('previewContainer').classList.add('hidden');
    document.getElementById('uploadPlaceholder').classList.remove('hidden');
    togglePaymentFields('transfer');
    toggleCustomBank('');
}

function togglePaymentFields(method) {
    const details = document.getElementById('transfer-details');
    const label = document.getElementById('upload-label');
    const bankInput = document.getElementById('modal_bank_name');
    const opInput = document.getElementById('modal_operation_number');

    if (method === 'cash') {
        details.classList.add('hidden');
        label.textContent = 'Subir foto del dinero';
        bankInput.required = false;
        opInput.required = false;
        toggleCustomBank('');
    } else {
        details.classList.remove('hidden');
        label.textContent = 'Subir comprobante';
        bankInput.required = true;
        opInput.required = true;
        toggleCustomBank(bankInput.value);
    }
}

function toggleCustomBank(value) {
    const container = document.getElementById('modal_bank_custom_container');
    const input = document.getElementById('modal_bank_name_custom');
    
    if (value === 'Otros') {
        container.classList.remove('hidden');
        input.required = true;
    } else {
        container.classList.add('hidden');
        input.required = false;
        input.value = '';
    }
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('voucherPreview').src = e.target.result;
            document.getElementById('previewContainer').classList.remove('hidden');
            document.getElementById('uploadPlaceholder').classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

document.addEventListener('DOMContentLoaded', () => render());
</script>
@endsection
