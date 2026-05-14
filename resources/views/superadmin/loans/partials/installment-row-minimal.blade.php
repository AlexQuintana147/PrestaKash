@php
    $loan = $inst->loan;
    $isOverdue = $inst->due_date->isPast() && $inst->status !== 'approved';
@endphp
<div class="py-3 flex items-center justify-between gap-4">
    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2">
            <span class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-tight">Cuota #{{ $inst->installment_number }}</span>
            <span class="text-[9px] text-gray-400 font-bold tabular-nums">{{ $loan->currency === 'USD' ? '$' : 'S/' }} {{ number_format($inst->amount, 2) }}</span>
        </div>
        <p class="text-[9px] {{ $isOverdue ? 'text-red-500' : 'text-gray-400' }} font-bold uppercase tracking-widest mt-0.5">
            Vence: {{ $inst->due_date->format('d/m/Y') }}
        </p>
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
                'voucher' => Storage::url($inst->payment_voucher)
            ]) }})"
            class="text-[8px] font-black uppercase tracking-widest text-purple-600 border-b border-purple-600 pb-0.5 hover:opacity-60 transition-opacity">
                Revisar
            </button>
        @endif
    </div>
</div>
