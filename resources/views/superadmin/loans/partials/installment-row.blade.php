@php
    $loan = $inst->loan;
    $user = $loan->user;
    $isOverdue = $inst->due_date->isPast() && $inst->status !== 'approved';
@endphp
<div class="py-6 transition-colors hover:bg-gray-50/50 dark:hover:bg-gray-900/20">
    <div class="flex flex-col md:flex-row md:items-center gap-6">

        <!-- Usuario & Préstamo -->
        <div class="flex items-center gap-4 flex-1">
            <div class="relative">
                <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-900 flex items-center justify-center overflow-hidden border border-gray-100 dark:border-gray-800">
                    @php
                        $selfie = $user->documents?->selfie;
                        $photo = $selfie ? Storage::url($selfie) : ($user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name));
                    @endphp
                    <img src="{{ $photo }}" class="w-full h-full object-cover">
                </div>
                @if($isOverdue)
                    <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 border-2 border-white dark:border-gray-950 rounded-full"></span>
                @endif
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">
                        {{ $user->name }}
                    </h3>
                    <button type="button" onclick="openLoanDetailModal({{ json_encode([
                        'photo'             => $user->documents?->selfie ? Storage::url($user->documents->selfie) : ($user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name)),
                        'name'              => $user->name,
                        'loanId'            => str_pad($loan->id, 4, '0', STR_PAD_LEFT),
                        'status'            => match($loan->status) { 'approved' => 'Activo', 'paid' => 'Completado', default => $loan->status },
                        'profileUrl'        => route('superadmin.users.show', $user),
                        'dni'               => $user->dni,
                        'phone'             => $user->phone,
                        'email'             => $user->email,
                        'address'           => $user->address,
                        'amount'            => ($loan->currency === 'USD' ? '$' : 'S/') . ' ' . number_format($loan->amount, 2),
                        'total'             => ($loan->currency === 'USD' ? '$' : 'S/') . ' ' . number_format($loan->total_amount, 2),
                        'rate'              => number_format($loan->interest_rate, 2) . '%',
                        'installmentsCount' => $loan->installments()->count() . ' cuotas',
                        'frequency'         => ucfirst($loan->payment_frequency),
                        'system'            => ucfirst($loan->amortization_system),
                        'bank'              => $loan->bank_name,
                        'account'           => $loan->account_number,
                        'reason'            => $loan->reason,
                    ]) }})"
                    class="text-gray-300 dark:text-gray-700 hover:text-gray-600 dark:hover:text-gray-400 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1">
                    <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">L-{{ str_pad($loan->id, 4, '0', STR_PAD_LEFT) }}</span>
                    <span class="text-[9px] text-gray-300 dark:text-gray-700">•</span>
                    <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">Cuota #{{ $inst->installment_number }}</span>
                    <span class="text-[9px] text-gray-300 dark:text-gray-700">•</span>
                    <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">{{ $loan->bank_name }}</span>
                </div>
            </div>
        </div>

        <!-- Detalles de Pago -->
        <div class="grid grid-cols-2 md:flex items-center gap-8 md:gap-12">
            <div>
                <p class="text-[9px] text-gray-400 uppercase font-black tracking-widest mb-1.5">Vencimiento</p>
                <p class="text-xs font-bold {{ $isOverdue ? 'text-red-500' : 'text-gray-900 dark:text-white' }}">
                    {{ $inst->due_date->format('d/m/Y') }}
                </p>
            </div>
            <div>
                <p class="text-[9px] text-gray-400 uppercase font-black tracking-widest mb-1.5">Monto</p>
                <p class="text-sm font-black text-gray-900 dark:text-white tabular-nums">
                    {{ $loan->currency === 'USD' ? '$' : 'S/' }} {{ number_format($inst->amount, 2) }}
                </p>
            </div>
        </div>

        <!-- Estado & Acciones -->
        <div class="flex items-center justify-between md:justify-end gap-6 border-t md:border-t-0 pt-4 md:pt-0 border-gray-50 dark:border-gray-900">
            <div class="text-right md:text-right">
                <p class="text-[9px] text-gray-400 uppercase font-black tracking-widest mb-1.5">Estado</p>
                <span class="text-[9px] font-black uppercase tracking-widest
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
            </div>

            <div class="flex items-center gap-3">
                @if($inst->status === 'processing')
                    <button type="button" onclick="openValidateModal({{ json_encode([
                        'id' => $inst->id,
                        'amount' => ($loan->currency === 'USD' ? '$' : 'S/') . number_format($inst->amount, 2),
                        'user' => $user->name,
                        'number' => $inst->installment_number,
                        'voucher' => Storage::url($inst->payment_voucher)
                    ]) }})"
                    class="text-[10px] font-black uppercase tracking-widest text-purple-600 border-b-2 border-purple-600 pb-0.5 hover:opacity-60 transition-opacity">
                        Revisar
                    </button>
                @elseif($inst->status === 'approved')
                    <form action="{{ route('superadmin.installments.unapprove', $inst) }}" method="POST"
                        onsubmit="return confirm('¿Revertir aprobación de este pago?')">
                        @csrf
                        <button type="submit"
                            class="text-[10px] font-black uppercase tracking-widest text-gray-400 border-b-2 border-gray-300 dark:border-gray-800 pb-0.5 hover:text-orange-500 hover:border-orange-500 transition-all">
                            Revertir
                        </button>
                    </form>
                @endif
            </div>
        </div>

    </div>
</div>
