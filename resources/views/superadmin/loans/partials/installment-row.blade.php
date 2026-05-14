@php
    $loan = $inst->loan;
    $user = $loan->user;
    $isOverdue = $inst->due_date->isPast() && $inst->status !== 'approved';
    $currency = $loan->currency === 'USD' ? '$' : 'S/';
    $selfie = $user->documents?->selfie;
    $photo = $selfie ? Storage::url($selfie) : ($user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name));
    $detailData = [
        'photo'             => $photo,
        'name'              => $user->name,
        'loanId'            => str_pad($loan->id, 4, '0', STR_PAD_LEFT),
        'status'            => match($loan->status) { 'approved' => 'Activo', 'paid' => 'Completado', default => $loan->status },
        'profileUrl'        => route('superadmin.users.show', $user),
        'dni'               => $user->dni,
        'ruc'               => $user->person_type === 'juridica' ? $user->ruc : null,
        'businessName'      => $user->person_type === 'juridica' ? $user->business_name : null,
        'phone'             => $user->phone,
        'email'             => $user->email,
        'address'           => $user->address,
        'amount'            => $currency . ' ' . number_format($loan->amount, 2),
        'total'             => $currency . ' ' . number_format($loan->total_amount, 2),
        'rate'              => number_format($loan->interest_rate, 2) . '%',
        'installmentsCount' => $loan->installments()->count() . ' cuotas',
        'frequency'         => ucfirst($loan->payment_frequency),
        'system'            => ucfirst($loan->amortization_system),
        'bank'              => $loan->bank_name,
        'account'           => $loan->account_number,
        'reason'            => $loan->reason,
    ];
@endphp
<div class="py-6 transition-colors hover:bg-gray-50/50 dark:hover:bg-gray-900/20">
    <div class="flex flex-col md:flex-row md:items-center gap-6">

        <!-- Usuario & Préstamo -->
        <div class="flex items-center gap-4 flex-1">
            <div class="relative">
                <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-900 flex items-center justify-center overflow-hidden border border-gray-100 dark:border-gray-800">
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
                    <button type="button"
                        onclick="openLoanDetailModal({{ json_encode($detailData) }})"
                        class="text-gray-900 dark:text-white hover:opacity-60 transition-opacity">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg"><g><path d="M32,47c8.271,0,15-6.729,15-15s-6.729-15-15-15s-15,6.729-15,15S23.729,47,32,47z M32,19c7.168,0,13,5.832,13,13s-5.832,13-13,13s-13-5.832-13-13S24.832,19,32,19z"/><path d="M32,41c4.962,0,9-4.037,9-9s-4.038-9-9-9s-9,4.037-9,9S27.038,41,32,41z M32,25c3.86,0,7,3.141,7,7s-3.14,7-7,7s-7-3.141-7-7S28.14,25,32,25z"/><path d="M32,37v-2c-1.654,0-3-1.346-3-3s1.346-3,3-3s3,1.346,3,3h2c0-2.757-2.243-5-5-5s-5,2.243-5,5S29.243,37,32,37z"/><path d="M62.53,30.74C57.134,18.748,45.15,11,32,11S6.866,18.748,1.47,30.74L0.903,32l0.567,1.26C6.866,45.252,18.85,53,32,53s25.134-7.748,30.53-19.74L63.097,32L62.53,30.74z M51,32c0,10.477-8.523,19-19,19s-19-8.523-19-19s8.523-19,19-19S51,21.523,51,32z M3.294,32.439L3.097,32l0.198-0.439c3.07-6.824,8.418-12.172,14.897-15.342C13.794,20.071,11,25.709,11,32s2.794,11.929,7.191,15.782C11.713,44.611,6.365,39.264,3.294,32.439z M60.706,32.439c-3.07,6.824-8.418,12.172-14.897,15.342C50.206,43.929,53,38.291,53,32s-2.794-11.929-7.191-15.782c6.478,3.17,11.826,8.518,14.897,15.342L60.903,32L60.706,32.439z"/></g></svg>
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
                    {{ $currency }} {{ number_format($inst->amount, 2) }}
                </p>
            </div>
        </div>

        <!-- Estado & Acciones -->
        <div class="flex items-center justify-between md:justify-end gap-6 border-t md:border-t-0 pt-4 md:pt-0 border-gray-50 dark:border-gray-900">
            <div class="text-right">
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
                    @php
                        $validateData = [
                            'id'     => $inst->id,
                            'amount' => $currency . number_format($inst->amount, 2),
                            'user'   => $user->name,
                            'number' => $inst->installment_number,
                            'voucher'=> Storage::url($inst->payment_voucher),
                        ];
                    @endphp
                    <button type="button"
                        onclick="openValidateModal({{ json_encode($validateData) }})"
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
