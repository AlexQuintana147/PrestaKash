@php
    $loan = $inst->loan;
    $user = $loan->user;
    $isOverdue = $inst->due_date->isPast() && $inst->status !== 'approved';
    $lateFee = $inst->late_fee;
    $currency = $loan->currency === 'USD' ? '$' : 'S/';
    $selfie = $user->documents?->selfie;
    $photo = $selfie ? Storage::url($selfie) : ($user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name));
    $detailData = [
        'photo' => $photo,
        'name' => $user->name,
        'loanId' => str_pad($loan->id, 4, '0', STR_PAD_LEFT),
        'status' => match ($loan->status) { 'approved' => 'Activo', 'paid' => 'Completado', default => $loan->status},
        'profileUrl' => route('superadmin.users.show', $user),
        'dni' => $user->dni,
        'ruc' => $user->person_type === 'juridica' ? $user->ruc : null,
        'businessName' => $user->person_type === 'juridica' ? $user->business_name : null,
        'phone' => $user->phone,
        'email' => $user->email,
        'address' => $user->address,
        'amount' => $currency . ' ' . number_format($loan->amount, 2),
        'total' => $currency . ' ' . number_format($loan->total_amount, 2),
        'rate' => number_format($loan->interest_rate, 2) . '%',
        'installmentsCount' => $loan->installments()->count() . ' cuotas',
        'frequency' => ucfirst($loan->payment_frequency),
        'system' => ucfirst($loan->amortization_system),
        'bank' => $loan->bank_name,
        'account' => $loan->account_number,
        'reason' => $loan->reason,
    ];
@endphp
<div class="py-6 transition-colors hover:bg-gray-50/50 dark:hover:bg-gray-900/20">
    <div class="flex flex-col md:flex-row md:items-center gap-6">

        <!-- Usuario & Préstamo -->
        <div class="flex items-center gap-4 flex-1">
            <div class="relative">
                <div
                    class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-900 flex items-center justify-center overflow-hidden border border-gray-100 dark:border-gray-800">
                    <img src="{{ $photo }}" class="w-full h-full object-cover">
                </div>
                @if($isOverdue)
                    <span
                        class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 border-2 border-white dark:border-gray-950 rounded-full"></span>
                @endif
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">
                        {{ $user->name }}
                    </h3>
                    <button type="button" onclick="openLoanDetailModal({{ json_encode($detailData) }})"
                        class="text-gray-900 dark:text-white hover:opacity-60 transition-opacity">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M 12 4 C 8.6666667 4 5.7335794 5.6316279 3.6191406 7.3496094 C 2.5619212 8.2086001 1.7059096 9.0941755 1.09375 9.859375 C 0.78767021 10.241975 0.54228306 10.592509 0.35742188 10.916016 C 0.17256067 11.239523 3.7007434e-17 11.458333 0 12 C 0 12.541667 0.17256067 12.760477 0.35742188 13.083984 C 0.54228307 13.407491 0.78767021 13.758025 1.09375 14.140625 C 1.7059096 14.905824 2.5619212 15.7914 3.6191406 16.650391 C 5.7335794 18.368372 8.6666667 20 12 20 C 15.333333 20 18.266421 18.368372 20.380859 16.650391 C 21.438079 15.7914 22.29409 14.905824 22.90625 14.140625 C 23.21233 13.758025 23.457717 13.407491 23.642578 13.083984 C 23.827439 12.760477 24 12.541667 24 12 C 24 11.458333 23.827439 11.239523 23.642578 10.916016 C 23.457717 10.592509 23.21233 10.241975 22.90625 9.859375 C 22.29409 9.0941755 21.438079 8.2086001 20.380859 7.3496094 C 18.266421 5.6316279 15.333333 4 12 4 z M 12 6 C 14.666667 6 17.233579 7.3683721 19.119141 8.9003906 C 20.061921 9.6663999 20.83091 10.468324 21.34375 11.109375 C 21.60017 11.4299 21.792283 11.712179 21.904297 11.908203 C 21.926717 11.947439 21.926155 11.965225 21.939453 12 C 21.926155 12.034775 21.926717 12.052561 21.904297 12.091797 C 21.792283 12.287821 21.60017 12.5701 21.34375 12.890625 C 20.83091 13.531676 20.061921 14.3336 19.119141 15.099609 C 17.233579 16.631628 14.666667 18 12 18 C 9.3333333 18 6.7664206 16.631628 4.8808594 15.099609 C 3.9380788 14.3336 3.1690904 13.531676 2.65625 12.890625 C 2.3998298 12.5701 2.2077169 12.287821 2.0957031 12.091797 C 2.0732826 12.052561 2.073845 12.034775 2.0605469 12 C 2.073845 11.965225 2.0732826 11.947439 2.0957031 11.908203 C 2.2077169 11.712179 2.3998298 11.4299 2.65625 11.109375 C 3.1690904 10.468324 3.9380788 9.6663999 4.8808594 8.9003906 C 6.7664206 7.3683721 9.3333333 6 12 6 z M 11.939453 8 A 4 4 0 0 0 8 12 A 4 4 0 0 0 12 16 A 4 4 0 0 0 16 12 A 4 4 0 0 0 15.822266 10.824219 A 2 2 0 0 1 14 12 A 2 2 0 0 1 12 10 A 2 2 0 0 1 13.177734 8.1777344 A 4 4 0 0 0 12 8 A 4 4 0 0 0 11.939453 8 z" />
                        </svg>
                    </button>
                </div>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1">
                    <span
                        class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">L-{{ str_pad($loan->id, 4, '0', STR_PAD_LEFT) }}</span>
                    <span class="text-[9px] text-gray-300 dark:text-gray-700">•</span>
                    <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">Cuota
                        #{{ $inst->installment_number }}</span>
                    <span class="text-[9px] text-gray-300 dark:text-gray-700">•</span>
                    <span
                        class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">{{ $loan->bank_name }}</span>
                </div>
                <div class="flex flex-wrap gap-1.5 mt-2">
                    <a href="mailto:{{ $user->email }}"
                        class="flex items-center gap-1 bg-blue-500 hover:bg-blue-600 transition-colors px-2 py-1 text-white font-bold text-[9px]">
                        {{ $user->email }}
                        <svg class="w-3 h-3 flex-shrink-0" viewBox="0 0 512 512" xml:space="preserve">
                            <rect x="7.904" y="119.947" style="fill:#5EBAE7;" width="496.195" height="272.109" />
                            <polygon style="fill:#36A9E1;"
                                points="256,280.01 23.91,127.951 7.904,135.954 256,312.022 352.037,223.987 " />
                            <path style="fill:#1D1D1B;"
                                d="M0,112.043v287.913h512V112.043H0z M477.266,127.851L256,270.603L34.733,127.851H477.266z M15.807,384.149V134.452L256,289.414l240.193-154.962v249.696H15.807V384.149z" />
                            <g>
                                <rect x="215.982" y="344.138" style="fill:#FFFFFF;" width="16.007" height="15.807" />
                                <rect x="247.997" y="344.138" style="fill:#FFFFFF;" width="16.006" height="15.807" />
                                <rect x="280.013" y="344.138" style="fill:#FFFFFF;" width="16.007" height="15.807" />
                            </g>
                        </svg>
                    </a>
                    <a href="https://wa.me/51{{ preg_replace('/\D/', '', $user->phone) }}" target="_blank"
                        class="flex items-center gap-1 bg-green-500 hover:bg-green-600 transition-colors px-2 py-1 text-white font-bold text-[9px]">
                        {{ $user->phone }}
                        <svg class="w-3 h-3 flex-shrink-0" viewBox="0 0 32 32" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M28 16C28 22.6274 22.6274 28 16 28C13.4722 28 11.1269 27.2184 9.19266 25.8837L5.09091 26.9091L6.16576 22.8784C4.80092 20.9307 4 18.5589 4 16C4 9.37258 9.37258 4 16 4C22.6274 4 28 9.37258 28 16Z"
                                fill="url(#wrow)" />
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M16 30C23.732 30 30 23.732 30 16C30 8.26801 23.732 2 16 2C8.26801 2 2 8.26801 2 16C2 18.5109 2.661 20.8674 3.81847 22.905L2 30L9.31486 28.3038C11.1894 29.3854 13.5789 30 16 30ZM16 27.8462C22.5425 27.8462 27.8462 22.5425 27.8462 16C27.8462 9.45755 22.5425 4.15385 16 4.15385C9.45755 4.15385 4.15385 9.45755 4.15385 16C4.15385 18.5261 4.9445 20.8675 6.29184 22.7902L5.23077 26.7692L9.27993 25.7569C11.1894 27.0746 13.5046 27.8462 16 27.8462Z"
                                fill="white" />
                            <path
                                d="M12.5 9.49989C12.1672 8.83131 11.6565 8.8905 11.1407 8.8905C10.2188 8.8905 8.78125 9.99478 8.78125 12.05C8.78125 13.7343 9.52345 15.578 12.0244 18.3361C14.438 20.9979 17.6094 22.3748 20.2422 22.3279C22.875 22.2811 23.4167 20.0154 23.4167 19.2503C23.4167 18.9112 23.2062 18.742 23.0613 18.696C22.1641 18.2654 20.5093 17.4631 20.1328 17.3124C19.7563 17.1617 19.5597 17.3656 19.4375 17.4765C19.0961 17.8018 18.4193 18.7608 18.1875 18.9765C17.9558 19.1922 17.6103 19.083 17.4665 19.0015C16.9374 18.7892 15.5029 18.1511 14.3595 17.0426C12.9453 15.6718 12.8623 15.2001 12.5959 14.7803C12.3828 14.4444 12.5392 14.2384 12.6172 14.1483C12.9219 13.7968 13.3426 13.254 13.5313 12.9843C13.7199 12.7145 13.5702 12.305 13.4803 12.05C13.0938 10.953 12.7663 10.0347 12.5 9.49989Z"
                                fill="white" />
                            <defs>
                                <linearGradient id="wrow" x1="26.5" y1="7" x2="4" y2="28"
                                    gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#5BD066" />
                                    <stop offset="1" stop-color="#27B43E" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </a>
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
            @if($inst->status === 'approved' && $inst->paid_at)
                <div>
                    <p class="text-[9px] text-gray-400 uppercase font-black tracking-widest mb-1.5">Pagó el</p>
                    <p class="text-xs font-bold text-green-500 tabular-nums">
                        {{ $inst->paid_at->format('d/m/Y') }}
                    </p>
                    @php 
                        $detail = $inst->payment_status_detail;
                    @endphp
                    @if($detail)
                        <p class="text-[8px] font-bold mt-0.5 
                            {{ $detail['type'] === 'before' || $detail['type'] === 'same_day' ? 'text-green-400' : '' }}
                            {{ $detail['type'] === 'grace' ? 'text-blue-500' : '' }}
                            {{ $detail['type'] === 'late' ? 'text-orange-500' : '' }}">
                            {{ $detail['label'] }}
                        </p>
                    @endif
                </div>
            @endif
            <div>
                <p class="text-[9px] text-gray-400 uppercase font-black tracking-widest mb-1.5">Monto</p>
                <p class="text-sm font-black text-gray-900 dark:text-white tabular-nums">
                    {{ $currency }} {{ number_format($inst->amount, 2) }}
                </p>
                <p class="text-[8px] text-gray-400 font-bold mt-0.5">
                    C: {{ $currency }}{{ number_format($inst->capital, 2) }} • I: {{ $currency }}{{ number_format($inst->interest, 2) }}
                </p>
                @if($lateFee > 0)
                    <p class="text-[8px] font-bold text-orange-500 mt-0.5">+ {{ number_format($lateFee, 2) }} mora</p>
                    <p class="text-[9px] font-black text-gray-900 dark:text-white mt-0.5">Total: {{ $currency }}
                        {{ number_format($inst->amount + $lateFee, 2) }}</p>
                @endif
            </div>
        </div>

        <!-- Estado & Acciones -->
        <div
            class="flex items-center justify-between md:justify-end gap-6 border-t md:border-t-0 pt-4 md:pt-0 border-gray-50 dark:border-gray-900">
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
                            'id' => $inst->id,
                            'amount' => $currency . number_format($inst->amount, 2),
                            'user' => $user->name,
                            'number' => $inst->installment_number,
                            'voucher' => Storage::url($inst->payment_voucher),
                        ];
                    @endphp
                    <button type="button" onclick="openValidateModal({{ json_encode($validateData) }})"
                        class="text-[10px] font-black uppercase tracking-widest text-purple-600 border-b-2 border-purple-600 pb-0.5 hover:opacity-60 transition-opacity">
                        Revisar
                    </button>
                @elseif($inst->status === 'approved')
                    @php
                        $lastApprovedId = $loan->installments()
                            ->where('status', 'approved')
                            ->orderBy('installment_number', 'desc')
                            ->value('id');
                    @endphp
                    @if($inst->payment_voucher)
                        <button type="button" onclick="openVoucherLightbox('{{ Storage::url($inst->payment_voucher) }}')"
                            class="text-gray-900 dark:text-white hover:opacity-60 transition-opacity" title="Ver comprobante">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </button>
                    @endif
                    @if($lastApprovedId === $inst->id)
                        <button type="button"
                            onclick="openUnapproveModal('{{ route('superadmin.installments.unapprove', $inst) }}')"
                            class="text-[10px] font-black uppercase tracking-widest text-red-500 border-b border-red-400 pb-px hover:opacity-60 transition-opacity">
                            Revertir
                        </button>
                    @endif
                @endif
            </div>
        </div>

    </div>
</div>