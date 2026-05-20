@php
    $loan = $inst->loan;
    $isOverdue = $inst->due_date->isPast() && $inst->status !== 'approved';
    $isDueToday = $inst->due_date->isToday() && $inst->status !== 'approved';
    $isDueOrOverdue = ($isOverdue || $isDueToday);
    $lateFee = $inst->late_fee;
    $cur = $loan->currency === 'USD' ? '$' : 'S/';
@endphp

<div data-installment-id="{{ $inst->id }}"
     data-is-interest-payment="{{ isset($inst->is_interest_payment) && $inst->is_interest_payment ? 'true' : 'false' }}"
     @if(isset($inst->is_interest_payment) && $inst->is_interest_payment && isset($inst->interest_payment))
     data-interest-payment-id="{{ $inst->interest_payment->id }}"
     data-paid-at="{{ $inst->interest_payment->paid_at->timestamp }}"
     @endif
     class="group rounded-2xl border overflow-hidden transition-all duration-200
        {{ $inst->status === 'approved' 
            ? 'bg-green-50/50 dark:bg-green-950/10 border-green-200 dark:border-green-900/30 hover:shadow-md hover:shadow-green-500/10' 
            : ($isDueOrOverdue 
                ? 'bg-red-50/70 dark:bg-red-950/20 border-red-200 dark:border-red-800/40 hover:shadow-md hover:shadow-red-500/10' 
                : 'bg-white dark:bg-gray-950 border-gray-200 dark:border-gray-800 hover:shadow-md hover:shadow-gray-500/10') }}
        {{ $isDueOrOverdue ? 'overdue-installment-row' : '' }}">

    <div class="p-4 sm:p-5">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-3 min-w-0 flex-1">
                <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center
                    {{ $inst->status === 'approved' 
                        ? 'bg-green-500 text-white' 
                        : ($isDueOrOverdue 
                            ? 'bg-red-500 text-white' 
                            : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400') }}">
                    @if($inst->status === 'approved')
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    @elseif($isDueOrOverdue)
                        <span class="text-sm sm:text-base font-black">#{{ $inst->installment_number }}</span>
                    @else
                        <span class="text-sm sm:text-base font-black">#{{ $inst->installment_number }}</span>
                    @endif
                </div>

                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white truncate">
                            @if(isset($inst->is_interest_payment) && $inst->is_interest_payment)
                                Interés pago {{ $inst->interest_payment_number }} de {{ $inst->total_interest_payments }}
                            @else
                                Cuota #{{ $inst->installment_number }}
                            @endif
                        </h3>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-black uppercase tracking-wider
                            {{ $inst->status === 'approved' 
                                ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400' 
                                : ($inst->status === 'processing' 
                                    ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-400 animate-pulse' 
                                    : ($inst->status === 'rejected' 
                                        ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400' 
                                        : ($isDueOrOverdue 
                                            ? 'bg-red-500 text-white' 
                                            : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'))) }}">
                            @if($inst->status === 'pending' && ((isset($inst->is_interest_payment) && $inst->is_interest_payment) || $inst->requested_type === 'interest'))
                                Interés pagado
                            @else
                                {{ match ($inst->status) {
                                    'approved' => 'Pagado',
                                    'pending' => 'Pendiente',
                                    'processing' => 'Validar',
                                    'rejected' => 'Rechazado',
                                    default => $inst->status
                                } }}
                            @endif
                        </span>
                    </div>

                    <div class="mt-1.5 flex items-center gap-3 text-[10px] sm:text-xs">
                        <span class="{{ $isOverdue ? 'text-red-600 dark:text-red-400 font-bold' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $inst->due_date->format('d/m/Y') }}
                        </span>
                        @if($isOverdue)
                            <span class="text-red-500 font-bold tabular-nums">
                                {{ now()->startOfDay()->diffInDays($inst->due_date) }} días vencida
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex-shrink-0 text-right">
                <p class="text-base sm:text-lg font-black text-gray-900 dark:text-white tabular-nums">
                    @if((isset($inst->is_interest_payment) && $inst->is_interest_payment) || $inst->requested_type === 'interest')
                        {{ $cur }}{{ number_format($inst->interest + ($inst->late_fee ?? 0), 2) }}
                    @else
                        {{ $cur }}{{ number_format($inst->amount, 2) }}
                    @endif
                </p>
                @if($lateFee > 0)
                    <p class="text-[10px] sm:text-xs font-bold text-orange-500 mt-0.5 tabular-nums">
                        + {{ $cur }}{{ number_format($lateFee, 2) }} mora
                    </p>
                @endif
            </div>
        </div>

        @if(in_array($inst->status, ['approved', 'processing']) && $inst->payment_method !== 'cash')
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800/50 flex items-center justify-between">
                <p class="text-[9px] sm:text-[10px] text-purple-600 dark:text-purple-400 font-bold uppercase tracking-wider flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    @if($inst->operation_number)
                        OP: {{ $inst->operation_number }}
                    @else
                        Sin número de operación
                    @endif
                </p>
                <button type="button" onclick="openEditOperationModal({{ $inst->id }}, '{{ $inst->operation_number }}')"
                    class="text-[9px] font-bold text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 transition-colors flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar
                </button>
            </div>
        @endif

        @if($inst->interest_payments_count > 0 && !(isset($inst->is_interest_payment) && $inst->is_interest_payment))
            <div class="mt-2 px-3 py-1.5 bg-purple-50 dark:bg-purple-950/20 rounded-lg inline-flex items-center gap-1.5">
                <svg class="w-3 h-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-[9px] font-bold text-purple-600 dark:text-purple-400">{{ $inst->interest_payments_count }}x Solo interés</span>
            </div>
        @endif
    </div>

    <div class="flex items-center gap-2 px-4 sm:px-5 py-2.5 bg-gray-50/80 dark:bg-gray-900/30 border-t border-gray-100 dark:border-gray-800/50">
        @if($inst->can_view_payment_voucher)
            <button type="button" onclick="openVoucherLightbox('{{ Storage::url($inst->display_voucher_path) }}')"
                class="flex items-center gap-1.5 text-[9px] sm:text-[10px] font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Comprobante
            </button>
        @endif

        @if($inst->status === 'processing')
            @php
                $displayAmount = $inst->requested_type === 'interest' 
                    ? $inst->interest + ($inst->late_fee ?? 0)
                    : $inst->amount + ($inst->late_fee ?? 0);
            @endphp
            <button type="button" onclick="openValidateModal({{ json_encode([
                'id' => $inst->id,
                'amount' => $cur . number_format($displayAmount, 2),
                'user' => $inst->loan->user->name,
                'number' => $inst->installment_number,
                'voucher' => Storage::url($inst->payment_voucher),
                'currency' => $loan->currency,
                'method' => $inst->payment_method,
                'bank' => $inst->bank_name,
                'operation' => $inst->operation_number,
                'requested_type' => $inst->requested_type,
            ]) }})"
                class="ml-auto flex items-center gap-1.5 px-3 py-1.5 bg-purple-500 hover:bg-purple-600 text-white text-[9px] sm:text-[10px] font-black uppercase tracking-wider rounded-lg transition-colors">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Revisar
            </button>
        @endif
    </div>
</div>