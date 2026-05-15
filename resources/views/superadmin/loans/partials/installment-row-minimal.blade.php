@php
    $loan = $inst->loan;
    $isOverdue = $inst->due_date->isPast() && $inst->status !== 'approved';
    $lateFee = $inst->late_fee;
@endphp
<div class="py-3 flex items-center justify-between gap-4">
    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2">
            <span class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-tight">Cuota
                #{{ $inst->installment_number }}</span>
            <span class="text-[9px] text-gray-400 font-bold tabular-nums">
                {{ $loan->currency === 'USD' ? '$' : 'S/' }} {{ number_format($inst->amount, 2) }}
                @if($lateFee > 0)
                    <span class="text-orange-500 ml-1">+ {{ number_format($lateFee, 2) }} mora</span>
                @endif
            </span>
        </div>
        <p
            class="text-[9px] {{ $isOverdue ? 'text-red-500' : 'text-gray-400' }} font-bold uppercase tracking-widest mt-0.5">
            Vence: {{ $inst->due_date->format('d/m/Y') }}
            @if($lateFee > 0)
                <span class="ml-2 text-gray-900 dark:text-white">Total: {{ $loan->currency === 'USD' ? '$' : 'S/' }}
                    {{ number_format($inst->amount + $lateFee, 2) }}</span>
            @endif
        </p>
        @if(in_array($inst->status, ['approved', 'processing']) && $inst->payment_method === 'transfer')
            <p class="text-[8px] text-purple-500 font-black uppercase tracking-widest mt-1 flex items-center gap-1">
                @if($inst->operation_number)
                    OP: {{ $inst->operation_number }}
                @else
                    Sin OP
                @endif
                <button type="button" onclick="openEditOperationModal({{ $inst->id }}, '{{ $inst->operation_number }}')"
                    class="text-gray-400 hover:text-purple-500 transition-colors">
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </button>
            </p>
        @endif
    </div>

    <div class="flex items-center gap-4">
        <span class="text-[8px] font-black uppercase tracking-widest
            {{ $inst->status === 'approved' ? 'text-green-500' : '' }}
            {{ $inst->status === 'pending' ? 'text-gray-400' : '' }}
            {{ $inst->status === 'processing' ? 'text-purple-600 animate-pulse' : '' }}
            {{ $inst->status === 'rejected' ? 'text-red-500' : '' }}">
            {{ match ($inst->status) {
    'approved' => 'Pagado',
    'pending' => 'Pendiente',
    'processing' => 'Validar',
    'rejected' => 'Rechazado',
    default => $inst->status
} }}
        </span>

        @if($inst->status === 'processing')
                <button type="button" onclick="openValidateModal({{ json_encode([
                'id' => $inst->id,
                'amount' => ($loan->currency === 'USD' ? '$' : 'S/') . number_format($inst->amount, 2),
                'user' => $inst->loan->user->name,
                'number' => $inst->installment_number,
                'voucher' => Storage::url($inst->payment_voucher),
                'currency' => $loan->currency,
                'method' => $inst->payment_method,
                'bank' => $inst->bank_name,
                'operation' => $inst->operation_number,
            ]) }})"
                    class="text-[8px] font-black uppercase tracking-widest text-purple-600 border-b border-purple-600 pb-0.5 hover:opacity-60 transition-opacity">
                    Revisar
                </button>
        @endif
    </div>
</div>