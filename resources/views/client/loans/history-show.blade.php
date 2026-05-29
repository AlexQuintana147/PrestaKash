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
    $totalRestante = $loan->installments()->whereIn('status', ['pending', 'rejected'])->sum('amount');
    $remainingCount = $loan->installments()->whereIn('status', ['pending', 'rejected'])->count();
    $canAmortizeTotal = !$isInDefault && !$hasAnyProcessing
        && $overdueInstallments->count() === 0
        && $nextInst !== null && $nextInst->due_date->isToday();
    $canAmortizePartial = !$isInDefault && !$hasAnyProcessing
        && $overdueInstallments->count() === 0
        && $nextInst !== null;
@endphp

<div class="min-h-screen px-4 md:px-8 py-6 max-w-2xl mx-auto">

    <!-- Back -->
    <a id="backToPanel" href="{{ route('client.loans.history') }}"
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
            <div class="flex items-center gap-2 mb-1">
                <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                    Préstamo
                </h1>
                <span class="text-[10px] font-mono text-white dark:text-black bg-black dark:bg-white px-2 py-0.5 rounded">{{ $loan->code }}</span>
            </div>
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
        <div class="mb-8 bg-red-50 dark:bg-red-500/5 border border-red-100 dark:border-red-500/20 rounded-2xl p-4 sm:p-6">
            <div class="flex items-start gap-3 sm:gap-4">
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-red-500/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-xs sm:text-sm font-black text-red-600 dark:text-red-400 uppercase tracking-widest mb-1">Estado de Mora</h3>
                    <p class="text-[10px] sm:text-[11px] text-red-500/80 leading-relaxed mb-3 sm:mb-4">
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
                            onclick="window._cur='{{ $cur }}'; openUploadModal({{ $firstPayable->id }}, '{{ $cur }}{{ number_format($nextInst ? $nextInst->interest : 0, 2) }}', {{ $totalLateFees }}, {{ $nextInst ? $nextInst->interest : 0 }}, 'mora_interest')"
                            class="w-full py-3 bg-red-600 hover:bg-red-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-red-500/20 mb-3">
                            Pagar mora acumulada + interés — {{ $cur }} {{ number_format($moraPlusInterest, 2) }}
                        </button>

                        @if(!$hasPreviousDebts)
                        <button type="button"
                            onclick="window._cur='{{ $cur }}'; openUploadModal({{ $firstPayable->id }}, '{{ $cur }}{{ number_format($nextInst ? $nextInst->amount : 0, 2) }}', {{ $totalLateFees }}, {{ $nextInst ? $nextInst->interest : 0 }}, 'mora_full')"
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
        <div class="mb-8 bg-red-50 dark:bg-red-500/5 border border-red-100 dark:border-red-500/20 rounded-2xl p-4 sm:p-6">
            <div class="flex items-start gap-3 sm:gap-4">
                <div class="flex-1 min-w-0">
                    <h3 class="text-xs sm:text-sm font-black text-red-600 dark:text-red-400 uppercase tracking-widest mb-1">Deuda Acumulada</h3>
                    <p class="text-[10px] sm:text-[11px] text-red-500/80 leading-relaxed mb-3 sm:mb-4">
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
            $paidLateFees += $pi->charged_late_fee ?? 0;
            foreach ($pi->interestPayments as $ip) {
                $paidLateFees += $ip->late_fee ?? 0;
            }
        }
        $totalPaidWithFees = $totalPaidAmount + $paidLateFees;
        $pendingFromTotal = max(0, $loan->total_amount - $totalPaidAmount);
        $pendingLateFeesNow = $overdueInstallments->sum('late_fee');
        $pendingCapital = max(0, $loan->amount - $paidCapital);
        $totalRemaining = $pendingCapital + ($nextInst ? $nextInst->interest : 0);
        $pendingInterest = max(0, ($loan->total_amount - $loan->amount) - $paidInterest);
    @endphp
    
    <div class="grid grid-cols-3 gap-1 sm:gap-px bg-gray-100 dark:bg-gray-900 rounded-2xl overflow-hidden mb-6">
        <div class="bg-white dark:bg-gray-950 px-2 sm:px-4 py-3 sm:py-4">
            <p class="text-[7px] sm:text-[8px] font-black uppercase tracking-widest text-gray-400 mb-0.5 sm:mb-1">Prestado</p>
            <p class="text-sm sm:text-base font-black text-gray-900 dark:text-white tabular-nums">{{ $cur }} {{ number_format($loan->amount, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-950 px-2 sm:px-4 py-3 sm:py-4">
            <p class="text-[7px] sm:text-[8px] font-black uppercase tracking-widest text-gray-400 mb-0.5 sm:mb-1">Capital pendiente</p>
            <p class="text-sm sm:text-base font-black text-gray-900 dark:text-white tabular-nums">{{ $cur }} {{ number_format($pendingCapital, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-950 px-2 sm:px-4 py-3 sm:py-4">
            <p class="text-[7px] sm:text-[8px] font-black uppercase tracking-widest text-gray-400 mb-0.5 sm:mb-1">Por cuota</p>
            <p class="text-sm sm:text-base font-black text-gray-900 dark:text-white tabular-nums">{{ $cur }} {{ number_format($loan->monthly_payment, 2) }}</p>
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

    @php
        $daysToNext = null;
        if ($firstNonApproved) {
            $daysToNext = (int) now()->startOfDay()->diffInDays($firstNonApproved->due_date->startOfDay(), false);
        }
    @endphp

    <!-- Info clave -->
    <div class="grid grid-cols-2 gap-2 sm:gap-3 mb-8">
        @foreach([
            'Vencimiento próximo' => $firstNonApproved ? $firstNonApproved->due_date->format('d M Y') : '—',
            'Frecuencia'          => ucfirst($loan->payment_frequency),
            'Banco destino'       => $loan->bank_name,
            'Desembolso'          => $loan->disbursement_date ? \Carbon\Carbon::parse($loan->disbursement_date)->format('d M Y') : '—',
            'Tasa de interés'     => number_format($loan->interest_rate, 2) . '%',
        ] as $label => $value)
        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl px-3 sm:px-4 py-2.5 sm:py-3">
            <p class="text-[7px] sm:text-[8px] font-black uppercase tracking-widest text-gray-400 mb-0.5 sm:mb-1">{{ $label }}</p>
            <p class="text-[11px] sm:text-xs font-bold text-gray-900 dark:text-white truncate">{{ $value }}</p>
        </div>
        @endforeach

        @if($daysToNext !== null)
        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl px-3 sm:px-4 py-2.5 sm:py-3">
            @if($daysToNext < 0)
                <p class="text-[11px] sm:text-xs font-black text-red-500">Tienes {{ abs($daysToNext) }} día{{ abs($daysToNext) !== 1 ? 's' : '' }} de atraso en tu próxima cuota</p>
            @elseif($daysToNext === 0)
                <p class="text-[11px] sm:text-xs font-black text-orange-500">Tu próxima cuota vence hoy</p>
            @elseif($daysToNext <= 3)
                <p class="text-[11px] sm:text-xs font-black text-orange-500">Te quedan {{ $daysToNext }} día{{ $daysToNext !== 1 ? 's' : '' }} para pagar la siguiente cuota</p>
            @else
                <p class="text-[11px] sm:text-xs font-bold text-gray-900 dark:text-white">Te quedan {{ $daysToNext }} día{{ $daysToNext !== 1 ? 's' : '' }} para pagar la siguiente cuota</p>
            @endif
        </div>
        @endif
    </div>

    <!-- Cronograma -->
    <div>
        <!-- Título + filtros -->
        <div class="flex items-center justify-between mb-4 gap-2">
            <p class="text-[9px] font-black uppercase tracking-widest text-gray-900 dark:text-white flex-shrink-0">Cronograma</p>
            <div class="flex items-center gap-0 border-b border-gray-100 dark:border-gray-900 overflow-x-auto scrollbar-none">
                @foreach(['all' => 'Todas', 'pending' => 'Pendientes', 'processing' => 'En revisión', 'paid' => 'Pagadas'] as $fkey => $flabel)
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
                $statusKey = $inst->status === 'approved' ? 'paid' : ($inst->status === 'processing' ? 'processing' : 'pending');
            @endphp
            <div class="inst-row py-3 sm:py-4 flex items-center gap-2 sm:gap-4 {{ $inst->status === 'approved' && $inst->payment_voucher ? 'cursor-pointer hover:bg-gray-100/60 dark:hover:bg-gray-900/40 transition-all rounded-lg' : '' }}" data-status="{{ $statusKey }}"
                @if($inst->status === 'approved' && $inst->payment_voucher)
                    onclick="openVoucherModal('{{ Storage::url($inst->payment_voucher) }}')"
                    role="button"
                    tabindex="0"
                @endif
            >
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
                        @if($inst->status === 'approved' && $inst->payment_voucher)
                            <span class="block text-[6px] font-bold mt-0.5 text-green-400">👆 Ver comprobante</span>
                        @endif
                        @if($inst->interest_payments_count > 0)
                            <span class="block text-[7px] font-bold mt-0.5 text-purple-500">
                                {{ $inst->interest_payments_count }}x Interés pagado
                            </span>
                            @php
                                $ipList = $inst->interestPayments ?? collect([]);
                                $ipTotal = $ipList->count();
                            @endphp
                            @foreach($ipList as $ipIdx => $ip)
                                <span class="block text-[6px] font-semibold mt-0.5 text-purple pl-1 border-l-2 border-purple-300">
                                    Pago {{ $ipIdx + 1 }} de {{ $ipTotal }} ·
                                    {{ $cur }}{{ number_format($ip->amount + ($ip->late_fee ?? 0), 2) }}
                                    @if($ip->paid_at)
                                        · {{ $ip->paid_at->format('d/m/Y') }}
                                    @endif
                                    @if($ip->late_fee > 0)
                                        <span class="text-orange-500">(+mora)</span>
                                    @endif
                                </span>
                            @endforeach
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
                        @if($inst->status === 'approved' && $inst->operation_number)
                            <span class="block text-[7px] font-bold mt-0.5 text-gray-400">
                                N° Op: {{ $inst->operation_number }}
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
                            onclick="window._cur='{{ $cur }}'; openUploadModal({{ $inst->id }}, '{{ $cur }}{{ number_format($payAmount, 2) }}', {{ $payMora }}, {{ $inst->interest }}, null, {{ $canAmortizeTotal ? 'true' : 'false' }}, {{ $canAmortizePartial ? 'true' : 'false' }}, {{ $totalRemaining }}, {{ $remainingCount }}, {{ $pendingCapital }})"
                            class="text-[9px] font-black uppercase tracking-widest text-gray-900 dark:text-white border-b border-gray-900 dark:border-white pb-px hover:opacity-60 transition-opacity">
                            Pagar
                        </button>
                    @elseif($canPay && $isInDefault)
                        <span class="text-[8px] font-black uppercase tracking-widest text-red-400">
                            Bloqueado
                        </span>
                    @endif
                </div>
            </div>
            @if($inst->interest_payments_count > 0)
                @php
                    $ipList = $inst->interestPayments ?? collect([]);
                @endphp
                @foreach($ipList as $ipIdx => $ip)
                <div class="inst-row py-2.5 sm:py-3 flex items-center gap-2 sm:gap-4 pl-4 sm:pl-6 bg-purple-50/40 dark:bg-purple-950/10 rounded-lg my-1 cursor-pointer hover:bg-purple-100/60 dark:hover:bg-purple-950/20 transition-all" data-status="paid"
                    @if($ip->payment_voucher)
                        onclick="openVoucherModal('{{ Storage::url($ip->payment_voucher) }}')"
                        role="button"
                        tabindex="0"
                    @endif
                >
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-purple-700 dark:text-purple-300">
                            {{ $cur }} {{ number_format($ip->amount + ($ip->late_fee ?? 0), 2) }}
                            <span class="text-[8px] text-gray-400 font-normal block">
                                Pago {{ $ipIdx + 1 }} de {{ $ipList->count() }}
                                @if($ip->late_fee > 0)
                                    · <span class="text-orange-500">+Mora: {{ $cur }}{{ number_format($ip->late_fee, 2) }}</span>
                                @endif
                            </span>
                        </p>
                        <p class="text-[9px] text-purple-500 mt-0.5">
                            @if($ip->paid_at)
                                {{ $ip->paid_at->format('d M Y') }}
                            @endif
                            @if($ip->operation_number)
                                <span class="ml-2 text-gray-400">OP: {{ $ip->operation_number }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        <div class="text-right">
                            <span class="block text-[8px] font-black uppercase tracking-widest text-purple-500">Interés Pagado</span>
                            @if($ip->payment_voucher)
                                <span class="block text-[6px] text-purple-400 mt-0.5">👆 Ver comprobante</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
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
    .scrollbar-none::-webkit-scrollbar{display:none}.scrollbar-none{-ms-overflow-style:none;scrollbar-width:none}
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
    class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] flex items-end sm:items-center justify-center"
    onclick="if(event.target===this) closeUploadModal()">
    <div class="bg-white dark:bg-gray-950 w-full sm:max-w-sm rounded-t-2xl sm:rounded-2xl shadow-2xl p-5 sm:p-6 relative max-h-[90vh] overflow-y-auto">
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
                    <label id="total-payment-option" class="flex-1 cursor-pointer group hidden">
                        <input type="radio" name="requested_type" value="total" class="hidden peer" onchange="updateModalAmount('total')">
                        <div class="py-2 text-center text-[10px] font-black tracking-widest text-gray-400 dark:text-gray-600 peer-checked:text-green-600 transition-all border-b-2 border-transparent peer-checked:border-green-600 hover:text-gray-600 dark:hover:text-gray-400">
                            PAGO TOTAL
                        </div>
                    </label>
                    @if($loan->type === 'cuotas' && $loan->amortization_system === 'frances')
                    <label id="partial-amortization-option" class="flex-1 cursor-pointer group hidden">
                        <input type="radio" name="requested_type" value="partial" class="hidden peer" onchange="updateModalAmount('partial')">
                        <div class="py-2 text-center text-[10px] font-black tracking-widest text-gray-400 dark:text-gray-600 peer-checked:text-blue-600 transition-all border-b-2 border-transparent peer-checked:border-blue-600 hover:text-gray-600 dark:hover:text-gray-400">
                            AMORTIZAR
                        </div>
                    </label>
                    @endif
                </div>
                <p id="interest-only-warning" class="hidden text-[8px] text-amber-500 font-bold uppercase leading-tight italic">
                    * Esta opción es exclusiva si no puedes pagar la cuota pactada. Se cobrará solo el interés y todas las cuotas se postergarán un periodo.
                </p>
                <p id="total-payment-warning" class="hidden text-[8px] text-green-600 font-bold uppercase leading-tight italic">
                    * Esta opción cancela el capital pendiente + interés de la cuota actual, pagando todo el préstamo de una sola vez.
                </p>
                <div id="partial-amortization-fields" class="hidden space-y-3 mt-3">
                    <p class="text-[8px] text-blue-600 font-bold uppercase leading-tight italic">
                        * Paga más de tu cuota y reduce tu deuda. El exceso se aplica al capital.
                    </p>
                    <div>
                        <label class="text-[8px] font-black text-gray-400 uppercase tracking-widest block mb-1">Monto a pagar</label>
                        <input type="text" name="amortization_amount" id="amortization_amount" inputmode="numeric"
                            class="w-full bg-transparent border-0 border-b border-gray-100 dark:border-gray-900 py-2 text-sm font-black text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 transition-all placeholder:text-gray-200 dark:placeholder:text-gray-800"
                            placeholder="Ingresa el monto">
                        <input type="hidden" name="amortization_amount_raw" id="amortization_amount_raw">
                        <p id="amortization_amount_limits" class="text-[8px] text-gray-400 font-bold mt-1"></p>
                        <p id="amortization_amount_error" class="text-[8px] text-red-500 font-bold mt-1 hidden"></p>
                    </div>
                    <div>
                        <label class="text-[8px] font-black text-gray-400 uppercase tracking-widest block mb-1">¿Cómo deseas reducir tu deuda?</label>
                        <div class="flex gap-2">
                            <label class="flex-1 cursor-pointer group">
                                <input type="radio" name="amortization_type" value="reduce_amount" class="hidden peer" checked>
                                <div class="py-2 text-center text-[9px] font-black tracking-widest text-gray-400 dark:text-gray-600 peer-checked:text-blue-600 transition-all border-b-2 border-transparent peer-checked:border-blue-600 hover:text-gray-600 dark:hover:text-gray-400">
                                    REDUCIR CUOTAS
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer group">
                                <input type="radio" name="amortization_type" value="reduce_term" class="hidden peer">
                                <div class="py-2 text-center text-[9px] font-black tracking-widest text-gray-400 dark:text-gray-600 peer-checked:text-blue-600 transition-all border-b-2 border-transparent peer-checked:border-blue-600 hover:text-gray-600 dark:hover:text-gray-400">
                                    REDUCIR PLAZO
                                </div>
                            </label>
                        </div>
                        <p id="reduce-amount-desc" class="text-[8px] text-gray-400 font-bold mt-1">Se mantienen las cuotas restantes pero con un monto menor.</p>
                        <p id="reduce-term-desc" class="hidden text-[8px] text-gray-400 font-bold mt-1">Se mantiene el monto de la cuota pero se eliminan cuotas del final.</p>
                    </div>
                </div>
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

    document.querySelectorAll('.inst-row').forEach(r => r.style.display = 'none');
    const start = (currentPage - 1) * PER_PAGE;
    rows.slice(start, start + PER_PAGE).forEach(r => r.style.display = '');

    document.getElementById('page-info').textContent = totalPages > 1 ? `${currentPage} / ${totalPages}` : '';
    document.getElementById('btn-prev').disabled = currentPage === 1;
    document.getElementById('btn-next').disabled = currentPage === totalPages;
    document.getElementById('pagination').style.display = totalPages <= 1 ? 'none' : 'flex';
}

function setFilter(f) {
    currentFilter = f;
    currentPage = 1;

    // Update tab styles
    ['all', 'pending', 'processing', 'paid'].forEach(k => {
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

function openUploadModal(instId, amount, lateFee = 0, interest = 0, paymentType = null, canAmortizeTotal = false, canAmortizePartial = false, totalAmount = 0, remainingCount = 1, pendingCapital = 0) {
    const baseAmount = parseFloat(amount.replace(/[^0-9.-]+/g,""));
    _currentInstData = {
        id: instId,
        fullAmount: baseAmount + lateFee,
        interestAmount: interest + lateFee,
        totalAmount: totalAmount,
        amountStr: amount,
        lateFee: lateFee,
        paymentType: paymentType,
        canAmortizeTotal: canAmortizeTotal,
        canAmortizePartial: canAmortizePartial,
        remainingCount: remainingCount,
        pendingCapital: pendingCapital
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

    const totalOption = document.getElementById('total-payment-option');
    if (totalOption) {
        if (canAmortizeTotal && !isMoraPayment && totalAmount > (baseAmount + lateFee)) {
            totalOption.classList.remove('hidden');
        } else {
            totalOption.classList.add('hidden');
        }
    }

    const partialOption = document.getElementById('partial-amortization-option');
    if (partialOption) {
        if (canAmortizePartial && !isMoraPayment && pendingCapital > (baseAmount + lateFee)) {
            partialOption.classList.remove('hidden');
        } else {
            partialOption.classList.add('hidden');
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
    const totalWarningEl = document.getElementById('total-payment-warning');
    const partialFieldsEl = document.getElementById('partial-amortization-fields');
    const isInterest = type === 'interest';
    const isTotal = type === 'total';
    const isPartial = type === 'partial';
    const isMoraPayment = _currentInstData.paymentType === 'mora_interest' || _currentInstData.paymentType === 'mora_full';

    if (partialFieldsEl) {
        if (isPartial) {
            partialFieldsEl.classList.remove('hidden');
            const minAmt = _currentInstData.fullAmount;
            const maxAmt = _currentInstData.totalAmount - 0.01;
            const limitsEl = document.getElementById('amortization_amount_limits');
            if (limitsEl) limitsEl.textContent = `Mínimo: ${(window._cur || '')} ${minAmt.toFixed(2)} · Máximo: ${(window._cur || '')} ${maxAmt.toFixed(2)}`;
            const amtInput = document.getElementById('amortization_amount');
            if (amtInput) {
                amtInput.min = minAmt.toFixed(2);
                amtInput.max = maxAmt.toFixed(2);
                amtInput.value = '';
            }
            amountEl.textContent = (window._cur || '') + ' 0.00';
        } else {
            partialFieldsEl.classList.add('hidden');
        }
    } else {
        if (partialFieldsEl) partialFieldsEl.classList.add('hidden');
    }

    let amountToDisplay;
    if (isTotal) {
        amountToDisplay = _currentInstData.totalAmount;
    } else if (isInterest) {
        amountToDisplay = _currentInstData.interestAmount;
    } else if (isPartial) {
        amountToDisplay = 0;
    } else {
        amountToDisplay = _currentInstData.fullAmount;
    }

    if (!isPartial) {
        amountEl.textContent = (window._cur || '') + ' ' + amountToDisplay.toFixed(2);
    }

    if (!isTotal && _currentInstData.lateFee > 0) {
        amountEl.innerHTML += `<br><span class="text-[10px] text-orange-500 uppercase font-bold">Incluye ${_currentInstData.lateFee.toFixed(2)} de mora</span>`;
    }

    if (isMoraPayment) {
        const moraLabel = _currentInstData.paymentType === 'mora_full' ? 'MORA + CUOTA TOTAL' : 'MORA + INTERÉS';
        amountEl.innerHTML += `<br><span class="text-[10px] text-red-500 uppercase font-bold">${moraLabel}</span>`;
    }

    if (isTotal) {
        const rCount = _currentInstData.remainingCount || 1;
        const countLabel = rCount === 1 ? '1 cuota' : `${rCount} cuotas`;
        amountEl.innerHTML += `<br><span class="text-[10px] text-green-600 uppercase font-bold">Cancela ${countLabel} restantes</span>`;
        const countEl = document.getElementById('total-remaining-count');
        if (countEl) countEl.textContent = rCount;
    }

    const requestedTypeInput = document.querySelector('input[name="requested_type"]');
    if (requestedTypeInput) {
        if (isMoraPayment) {
            requestedTypeInput.value = _currentInstData.paymentType;
        } else if (isTotal) {
            requestedTypeInput.value = 'total';
        } else if (isPartial) {
            requestedTypeInput.value = 'partial';
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

    if (totalWarningEl) {
        if (isTotal) {
            totalWarningEl.classList.remove('hidden');
        } else {
            totalWarningEl.classList.add('hidden');
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const amtInput = document.getElementById('amortization_amount');
    const amtRawInput = document.getElementById('amortization_amount_raw');
    if (amtInput) {
        amtInput.addEventListener('input', function() {
            let raw = this.value.replace(/[^0-9]/g, '');
            if (raw.length === 0) {
                this.value = '';
                if (amtRawInput) amtRawInput.value = '';
                const amountEl = document.getElementById('modal_amount_text');
                if (amountEl && _currentInstData) {
                    amountEl.textContent = (window._cur || '') + ' 0.00';
                }
                return;
            }
            const cents = parseInt(raw, 10);
            const decimalVal = cents / 100;
            const formatted = decimalVal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            this.value = formatted;
            if (amtRawInput) amtRawInput.value = decimalVal.toFixed(2);

            const amountEl = document.getElementById('modal_amount_text');
            const errorEl = document.getElementById('amortization_amount_error');
            const min = _currentInstData ? _currentInstData.fullAmount : 0;
            const max = _currentInstData ? _currentInstData.totalAmount - 0.01 : 0;

            if (amountEl && _currentInstData) {
                amountEl.textContent = (window._cur || '') + ' ' + formatted;
            }

            if (errorEl) {
                if (decimalVal > 0 && decimalVal < min) {
                    errorEl.textContent = `El monto no puede ser menor a la cuota: ${(window._cur || '')} ${min.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                    errorEl.classList.remove('hidden');
                } else if (decimalVal > max) {
                    errorEl.textContent = `El monto no puede ser mayor a la deuda total. Usa "PAGO TOTAL" para cancelar todo.`;
                    errorEl.classList.remove('hidden');
                } else {
                    errorEl.classList.add('hidden');
                }
            }
        });

        amtInput.addEventListener('keydown', function(e) {
            if (e.key === '.' || e.key === ',' || e.key === '-' || e.key === 'e' || e.key === 'E') {
                e.preventDefault();
            }
        });
    }

    const amortTypeRadios = document.querySelectorAll('input[name="amortization_type"]');
    amortTypeRadios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            const reduceAmountDesc = document.getElementById('reduce-amount-desc');
            const reduceTermDesc = document.getElementById('reduce-term-desc');
            if (this.value === 'reduce_amount') {
                if (reduceAmountDesc) reduceAmountDesc.classList.remove('hidden');
                if (reduceTermDesc) reduceTermDesc.classList.add('hidden');
            } else {
                if (reduceAmountDesc) reduceAmountDesc.classList.add('hidden');
                if (reduceTermDesc) reduceTermDesc.classList.remove('hidden');
            }
        });
    });

    const form = document.getElementById('uploadForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const requestedType = document.querySelector('input[name="requested_type"]');
            if (requestedType && requestedType.value === 'partial') {
                const amtRawInput = document.getElementById('amortization_amount_raw');
                const amtDisplayInput = document.getElementById('amortization_amount');
                const errorEl = document.getElementById('amortization_amount_error');
                const val = parseFloat(amtRawInput.value) || 0;

                if (amtDisplayInput) amtDisplayInput.value = val.toFixed(2);

                const min = _currentInstData ? _currentInstData.fullAmount : 0;
                const max = _currentInstData ? _currentInstData.totalAmount - 0.01 : 0;

                if (val < min) {
                    e.preventDefault();
                    if (errorEl) {
                        errorEl.textContent = `El monto no puede ser menor a la cuota: ${(window._cur || '')} ${min.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                        errorEl.classList.remove('hidden');
                    }
                    return false;
                }
                if (val >= _currentInstData.totalAmount) {
                    e.preventDefault();
                    if (errorEl) {
                        errorEl.textContent = `El monto no puede ser mayor o igual a la deuda total. Usa "PAGO TOTAL" para cancelar todo.`;
                        errorEl.classList.remove('hidden');
                    }
                    return false;
                }
            }
        });
    }
});

function closeUploadModal() {
    document.getElementById('uploadModal').classList.add('hidden');
    document.body.style.overflow = '';
    document.getElementById('uploadForm').reset();
    document.getElementById('previewContainer').classList.add('hidden');
    document.getElementById('uploadPlaceholder').classList.remove('hidden');
    togglePaymentFields('transfer');
    toggleCustomBank('');
    const partialFields = document.getElementById('partial-amortization-fields');
    if (partialFields) partialFields.classList.add('hidden');
    const errorEl = document.getElementById('amortization_amount_error');
    if (errorEl) errorEl.classList.add('hidden');
    const amtRawInput = document.getElementById('amortization_amount_raw');
    if (amtRawInput) amtRawInput.value = '';
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

var back = document.getElementById('backToPanel');
if (back) {
    if (sessionStorage.getItem('from_dashboard')) {
        back.href = '{{ route('client.dashboard') }}';
        sessionStorage.removeItem('from_dashboard');
    } else if (sessionStorage.getItem('from_loans')) {
        back.href = '{{ route('client.loans') }}';
        sessionStorage.removeItem('from_loans');
    }
}

document.addEventListener('DOMContentLoaded', () => render());

function openVoucherModal(url) {
    currentScale = 1;
    panX = 0; panY = 0;
    const img = document.getElementById('lightboxImg');
    img.src = url;
    img.style.transform = `translate(0px, 0px) scale(1)`;
    document.getElementById('lightboxLabel').textContent = 'Comprobante de pago';
    document.getElementById('zoomLevel').textContent = '100%';
    document.getElementById('lightbox').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeVoucherModal(e) {
    if (e && e.target !== document.getElementById('lightbox')) return;
    document.getElementById('lightbox').classList.add('hidden');
    document.body.style.overflow = '';
}

function applyTransform() {
    const img = document.getElementById('lightboxImg');
    img.style.transform = `translate(${panX}px, ${panY}px) scale(${currentScale})`;
}

function zoom(delta) {
    currentScale = Math.min(5, Math.max(0.25, currentScale + delta));
    if (currentScale <= 1) { panX = 0; panY = 0; }
    applyTransform();
    document.getElementById('zoomLevel').textContent = Math.round(currentScale * 100) + '%';
    document.getElementById('lightboxImgContainer').style.cursor = currentScale > 1 ? 'grab' : 'default';
}

function resetZoom() {
    currentScale = 1;
    panX = 0; panY = 0;
    applyTransform();
    document.getElementById('zoomLevel').textContent = '100%';
    document.getElementById('lightboxImgContainer').style.cursor = 'default';
}

let currentScale = 1;
let panX = 0;
let panY = 0;

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeVoucherModal();
    if (e.key === '+' || e.key === '=') zoom(0.25);
    if (e.key === '-') zoom(-0.25);
});

const imgContainer = document.getElementById('lightboxImgContainer');
if (imgContainer) {
    let startX, startY;
    imgContainer.addEventListener('mousedown', e => {
        if (currentScale <= 1) return;
        e.preventDefault();
        startX = e.clientX - panX;
        startY = e.clientY - panY;
        imgContainer.style.cursor = 'grabbing';

        const onMove = e => {
            panX = e.clientX - startX;
            panY = e.clientY - startY;
            applyTransform();
        };

        const onUp = () => {
            imgContainer.style.cursor = currentScale > 1 ? 'grab' : 'default';
            document.removeEventListener('mousemove', onMove);
            document.removeEventListener('mouseup', onUp);
        };

        document.addEventListener('mousemove', onMove);
        document.addEventListener('mouseup', onUp);
    });
}
</script>
@endsection

<!-- Lightbox Modal (Ver Comprobante) -->
<div id="lightbox" onclick="closeVoucherModal(event)"
     class="hidden fixed inset-0 z-[110] bg-black/90 flex flex-col items-center justify-center p-4"
     onwheel="if(!this.classList.contains('hidden')){event.preventDefault();zoom(event.deltaY<0?0.15:-0.15)}">

    <div class="flex items-center justify-between w-full max-w-4xl mb-3 flex-shrink-0">
        <p id="lightboxLabel" class="text-sm text-gray-400"></p>
        <div class="flex items-center gap-3">
            <button onclick="zoom(-0.25)" class="p-2 text-gray-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"/></svg>
            </button>
            <span id="zoomLevel" class="text-xs text-gray-500 w-10 text-center">100%</span>
            <button onclick="zoom(0.25)" class="p-2 text-gray-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
            </button>
            <button onclick="resetZoom()" class="p-2 text-gray-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </button>
            <button onclick="closeVoucherModal()" class="p-2 text-gray-400 hover:text-white transition-colors ml-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    <div id="lightboxImgContainer"
         class="overflow-hidden w-full max-w-4xl flex items-center justify-center rounded-lg relative"
         style="max-height: 80vh; cursor: grab;">
        <img id="lightboxImg" src="" alt=""
             class="transition-transform duration-100 rounded-lg select-none"
             style="transform-origin: center; max-width: 100%; will-change: transform;"
             onwheel="event.preventDefault(); event.stopPropagation(); zoom(event.deltaY < 0 ? 0.15 : -0.15);">
    </div>
</div>
