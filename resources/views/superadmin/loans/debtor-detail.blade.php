@extends('superadmin.layout')

@section('title', 'Deudas pendientes - ' . $fullName)

@section('content')
<div class="px-4 md:px-10 py-6 md:py-8">

    <!-- Header -->
    <div class="flex items-center gap-3 mb-6 md:mb-8">
        <a href="{{ route('superadmin.installments.index', ['filter' => 'overdue']) }}"
           class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0 ring-2 ring-red-200 dark:ring-red-800">
            <img src="{{ $photo }}" class="w-full h-full object-cover" alt="{{ $fullName }}">
        </div>
        <div class="min-w-0 flex-1">
            <h1 class="text-base md:text-xl font-bold text-gray-900 dark:text-white truncate">{{ $fullName }}</h1>
            <div class="flex flex-wrap items-center gap-2 md:gap-3 mt-0.5">
                <a href="mailto:{{ $user->email }}"
                   class="flex items-center gap-1 bg-blue-500 hover:bg-blue-600 transition-colors px-2 py-1 text-white font-bold text-[10px]">
                    {{ $user->email }}
                    <svg class="w-3 h-3 flex-shrink-0" viewBox="0 0 512 512" xml:space="preserve"><rect x="7.904" y="119.947" style="fill:#5EBAE7;" width="496.195" height="272.109"/><polygon style="fill:#36A9E1;" points="256,280.01 23.91,127.951 7.904 135.954 256,312.022 352.037 223.987 "/><path style="fill:#1D1D1B;" d="M0,112.043v287.913h512V112.043H0z M477.266,127.851L256,270.603L34.733,127.851H477.266z M15.807,384.149V134.452L256,289.414l240.193-154.962v249.696H15.807V384.149z"/><g><rect x="215.982" y="344.138" style="fill:#FFFFFF;" width="16.007" height="15.807"/><rect x="247.997" y="344.138" style="fill:#FFFFFF;" width="16.006" height="15.807"/><rect x="280.013" y="344.138" style="fill:#FFFFFF;" width="16.007" height="15.807"/></g></svg>
                </a>
                <a href="https://wa.me/51{{ preg_replace('/\D/', '', $user->phone) }}" target="_blank"
                   class="flex items-center gap-1 bg-green-500 hover:bg-green-600 transition-colors px-2 py-1 text-white font-bold text-[10px]">
                    {{ $user->phone }}
                    <svg class="w-3 h-3 flex-shrink-0" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M28 16C28 22.6274 22.6274 28 16 28C13.4722 28 11.1269 27.2184 9.19266 25.8837L5.09091 26.9091L6.16576 22.8784C4.80092 20.9307 4 18.5589 4 16C4 9.37258 9.37258 4 16 4C22.6274 4 28 9.37258 28 16Z" fill="#27B43E"/><path fill-rule="evenodd" clip-rule="evenodd" d="M16 30C23.732 30 30 23.732 30 16C30 8.26801 23.732 2 16 2C8.26801 2 2 8.26801 2 16C2 18.5109 2.661 20.8674 3.81847 22.905L2 30L9.31486 28.3038C11.1894 29.3854 13.5789 30 16 30ZM16 27.8462C22.5425 27.8462 27.8462 22.5425 27.8462 16C27.8462 9.45755 22.5425 4.15385 16 4.15385C9.45755 4.15385 4.15385 9.45755 4.15385 16C4.15385 18.5261 4.9445 20.8675 6.29184 22.7902L5.23077 26.7692L9.27993 25.7569C11.1894 27.0746 13.5046 27.8462 16 27.8462Z" fill="white"/><path d="M12.5 9.49989C12.1672 8.83131 11.6565 8.8905 11.1407 8.8905C10.2188 8.8905 8.78125 9.99478 8.78125 12.05C8.78125 13.7343 9.52345 15.578 12.0244 18.3361C14.438 20.9979 17.6094 22.3748 20.2422 22.3279C22.875 22.2811 23.4167 20.0154 23.4167 19.2503C23.4167 18.9112 23.2062 18.742 23.0613 18.696C22.1641 18.2654 20.5093 17.4631 20.1328 17.3124C19.7563 17.1617 19.5597 17.3656 19.4375 17.4765C19.0961 17.8018 18.4193 18.7608 18.1875 18.9765C17.9558 19.1922 17.6103 19.083 17.4665 19.0015C16.9374 18.7892 15.5029 18.1511 14.3595 17.0426C12.9453 15.6718 12.8623 15.2001 12.5959 14.7803C12.3828 14.4444 12.5392 14.2384 12.6172 14.1483C12.9219 13.7968 13.3426 13.254 13.5313 12.9843C13.7199 12.7145 13.5702 12.305 13.4803 12.05C13.0938 10.953 12.7663 10.0347 12.5 9.49989Z" fill="white"/></svg>
                </a>
                <span class="inline-flex items-center bg-gray-900 dark:bg-white text-white dark:text-black font-bold text-[9px] px-2 py-1 rounded-lg">DNI: {{ $user->dni ?? '' }}</span>
            </div>
        </div>
    </div>

    @if($pendingInstallments->isEmpty())
        <div class="py-24 text-center">
            <svg class="w-12 h-12 mx-auto text-green-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm text-gray-400 dark:text-gray-500 font-medium uppercase tracking-widest">Sin deudas pendientes</p>
        </div>
    @else

        <!-- Resumen -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4 mb-6 md:mb-8">
            <div class="bg-red-500 dark:bg-red-600 rounded-2xl p-4 text-center shadow-lg shadow-red-500/25">
                <p class="text-[10px] text-red-100 uppercase tracking-widest font-bold mb-1">Cuotas pendientes</p>
                <p class="text-2xl font-black text-white">{{ $pendingInstallments->count() }}</p>
            </div>
            <div class="bg-gray-900 dark:bg-black rounded-2xl p-4 text-center shadow-lg shadow-gray-900/25">
                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold mb-1">Capital + Interés</p>
                <p class="text-2xl font-black text-white">S/ {{ number_format($totalDebt, 2, '.', ',') }}</p>
            </div>
            <div class="bg-orange-500 dark:bg-orange-600 rounded-2xl p-4 text-center shadow-lg shadow-orange-500/25">
                <p class="text-[10px] text-orange-100 uppercase tracking-widest font-bold mb-1">Mora acumulada</p>
                <p class="text-2xl font-black text-white">+ S/ {{ number_format($totalLateFee, 2, '.', ',') }}</p>
            </div>
        </div>

        <!-- Tabla desktop -->
        <div class="hidden md:block bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden">
            <div class="grid grid-cols-12 gap-4 px-4 py-3 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                <div class="col-span-2">Préstamo</div>
                <div class="col-span-1 text-center">N°</div>
                <div class="col-span-2">Vencimiento</div>
                <div class="col-span-2 text-right">Cuota</div>
                <div class="col-span-2 text-right">Mora</div>
                <div class="col-span-2 text-right">Total</div>
                <div class="col-span-1 text-center">Estado</div>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-gray-900">
                @foreach($pendingInstallments as $inst)
                    @php
                        $isOverdue = $inst->due_date->lt(now()->startOfDay());
                        $cur = $inst->loan->currency === 'USD' ? '$' : 'S/';
                        $totalWithFee = $inst->amount + ($inst->late_fee ?? 0);
                        $daysOverdue = $isOverdue ? now()->startOfDay()->diffInDays($inst->due_date) : 0;
                        $loan = $inst->loan;
                        $accDebt = $inst->accumulated_debt;
                    @endphp
                    <div class="grid grid-cols-12 gap-4 px-4 py-3 items-center cursor-pointer
                        {{ $isOverdue ? 'bg-red-50/50 dark:bg-red-950/10' : '' }} hover:bg-gray-50 dark:hover:bg-gray-900/50 transition-colors installment-row"
                        data-installment-id="{{ $inst->id }}"
                        data-loan-id="{{ $loan->id }}"
                        data-loan-code="{{ $loan->code }}"
                        data-loan-type="{{ $loan->type }}"
                        data-loan-currency="{{ $loan->currency }}"
                        data-loan-amount="{{ $loan->amount }}"
                        data-loan-total="{{ $loan->total_amount ?? $loan->amount }}"
                        data-loan-interest-rate="{{ $loan->interest_rate ?? 0 }}"
                        data-loan-months="{{ $loan->months ?? '-' }}"
                        data-loan-disbursement="{{ $loan->disbursement_date ? (\Carbon\Carbon::parse($loan->disbursement_date)->format('d/m/Y')) : '-' }}"
                        data-loan-status="{{ $loan->status }}"
                        data-late-fee-enabled="{{ $loan->late_fee_enabled ? 'true' : 'false' }}"
                        data-grace-days="{{ $loan->grace_days ?? 0 }}"
                        data-late-fee-rate="{{ $loan->late_fee_rate ?? 0 }}"
                        data-late-daily="{{ $loan->late_fee_daily_amount ?? 0 }}"
                        data-in-default="{{ $loan->in_default ? 'true' : 'false' }}"
                        data-inst-number="{{ $inst->installment_number }}"
                        data-inst-amount="{{ $inst->amount }}"
                        data-inst-capital="{{ $inst->capital ?? 0 }}"
                        data-inst-interest="{{ $inst->interest ?? 0 }}"
                        data-inst-due-date="{{ $inst->due_date->format('d/m/Y') }}"
                        data-inst-status="{{ $inst->status }}"
                        data-inst-late-fee="{{ $inst->late_fee ?? 0 }}"
                        data-total-with-fee="{{ $totalWithFee }}"
                        data-days-overdue="{{ $daysOverdue }}"
                        data-acc-base="{{ $accDebt['base'] }}"
                        data-acc-mora="{{ $accDebt['mora'] }}"
                        data-acc-total="{{ $accDebt['total'] }}"
                        data-acc-count="{{ $accDebt['count'] }}"
                        data-user-email="{{ $user->email }}"
                        data-user-phone="{{ $user->phone }}"
                        data-user-name="{{ $fullName }}">
                        <div class="col-span-2">
                            <span class="text-xs font-bold text-blue-500 dark:text-blue-400 truncate block">{{ $inst->loan->code }}</span>
                        </div>
                        <div class="col-span-1 text-center">
                            <span class="inline-flex w-6 h-6 rounded-full items-center justify-center text-[9px] font-black tabular-nums bg-red-500 text-white">
                                {{ $inst->installment_number }}
                            </span>
                        </div>
                        <div class="col-span-2">
                            <p class="text-xs font-medium {{ $isOverdue ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                {{ $inst->due_date->format('d/m/Y') }}
                            </p>
                            @if($isOverdue)
                                <p class="text-[9px] text-red-400 dark:text-red-500 mt-0.5">
                                    Hace {{ $daysOverdue }} días
                                </p>
                            @endif
                        </div>
                        <div class="col-span-2 text-right">
                            <p class="text-xs font-bold text-gray-900 dark:text-white">{{ $cur }} {{ number_format($inst->amount, 2) }}</p>
                            <p class="text-[8px] text-gray-400">Cap: {{ $cur }}{{ number_format($inst->capital ?? 0, 2) }} • Int: {{ $cur }}{{ number_format($inst->interest ?? 0, 2) }}</p>
                        </div>
                        <div class="col-span-2 text-right">
                            @if($inst->late_fee > 0)
                                <p class="text-xs font-bold text-orange-500">{{ $cur }} {{ number_format($inst->late_fee, 2) }}</p>
                            @else
                                <p class="text-xs text-gray-300 dark:text-gray-600">—</p>
                            @endif
                        </div>
                        <div class="col-span-2 text-right">
                            <p class="text-xs font-black {{ $isOverdue ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">{{ $cur }} {{ number_format($totalWithFee, 2) }}</p>
                        </div>
                        <div class="col-span-1 text-center">
                            @if($isOverdue)
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-red-500 text-white text-[8px] font-bold">VENCIDA</span>
                            @elseif($inst->status === 'processing')
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-red-500 text-white text-[8px] font-bold">VALIDANDO</span>
                            @else
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-red-500 text-white text-[8px] font-bold">PENDIENTE</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Total final desktop -->
            <div class="grid grid-cols-12 gap-4 px-4 py-4 bg-gray-100 dark:bg-white text-gray-900 dark:text-gray-900 items-center">
                <div class="col-span-8 text-right pr-4">
                    <span class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-600">Total a cobrar</span>
                </div>
                <div class="col-span-4 text-right">
                    <p class="text-lg font-black">S/ {{ number_format($totalDebt + $totalLateFee, 2, '.', ',') }}</p>
                </div>
            </div>
        </div>

        <!-- Cards mobile -->
        <div class="md:hidden space-y-3">
            @foreach($pendingInstallments as $inst)
                @php
                    $isOverdue = $inst->due_date->lt(now()->startOfDay());
                    $cur = $inst->loan->currency === 'USD' ? '$' : 'S/';
                    $totalWithFee = $inst->amount + ($inst->late_fee ?? 0);
                    $daysOverdue = $isOverdue ? now()->startOfDay()->diffInDays($inst->due_date) : 0;
                    $loan = $inst->loan;
                    $accDebt = $inst->accumulated_debt;
                @endphp
                <div class="bg-white dark:bg-gray-950 border rounded-2xl overflow-hidden shadow-sm cursor-pointer installment-row
                    {{ $isOverdue ? 'border-red-200 dark:border-red-800' : 'border-gray-200 dark:border-gray-800' }}"
                    data-installment-id="{{ $inst->id }}"
                    data-loan-id="{{ $loan->id }}"
                    data-loan-code="{{ $loan->code }}"
                    data-loan-type="{{ $loan->type }}"
                    data-loan-currency="{{ $loan->currency }}"
                    data-loan-amount="{{ $loan->amount }}"
                    data-loan-total="{{ $loan->total_amount ?? $loan->amount }}"
                    data-loan-interest-rate="{{ $loan->interest_rate ?? 0 }}"
                    data-loan-months="{{ $loan->months ?? '-' }}"
                    data-loan-disbursement="{{ $loan->disbursement_date ? (\Carbon\Carbon::parse($loan->disbursement_date)->format('d/m/Y')) : '-' }}"
                    data-loan-status="{{ $loan->status }}"
                    data-late-fee-enabled="{{ $loan->late_fee_enabled ? 'true' : 'false' }}"
                    data-grace-days="{{ $loan->grace_days ?? 0 }}"
                    data-late-fee-rate="{{ $loan->late_fee_rate ?? 0 }}"
                    data-late-daily="{{ $loan->late_fee_daily_amount ?? 0 }}"
                    data-in-default="{{ $loan->in_default ? 'true' : 'false' }}"
                    data-inst-number="{{ $inst->installment_number }}"
                    data-inst-amount="{{ $inst->amount }}"
                    data-inst-capital="{{ $inst->capital ?? 0 }}"
                    data-inst-interest="{{ $inst->interest ?? 0 }}"
                    data-inst-due-date="{{ $inst->due_date->format('d/m/Y') }}"
                    data-inst-status="{{ $inst->status }}"
                    data-inst-late-fee="{{ $inst->late_fee ?? 0 }}"
                    data-total-with-fee="{{ $totalWithFee }}"
                    data-days-overdue="{{ $daysOverdue }}"
                    data-acc-base="{{ $accDebt['base'] }}"
                    data-acc-mora="{{ $accDebt['mora'] }}"
                    data-acc-total="{{ $accDebt['total'] }}"
                    data-acc-count="{{ $accDebt['count'] }}"
                    data-user-email="{{ $user->email }}"
                    data-user-phone="{{ $user->phone }}"
                    data-user-name="{{ $fullName }}">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-800
                        {{ $isOverdue ? 'bg-red-50/70 dark:bg-red-950/20' : 'bg-gray-50/50 dark:bg-gray-900/30' }}">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="inline-flex w-7 h-7 rounded-lg items-center justify-center text-[10px] font-black tabular-nums flex-shrink-0 bg-red-500 text-white">
                                #{{ $inst->installment_number }}
                            </span>
                            <span class="text-xs font-bold text-blue-500 dark:text-blue-400 truncate">{{ $inst->loan->code }}</span>
                        </div>
                        @if($isOverdue)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-500 text-white text-[9px] font-bold flex-shrink-0">VENCIDA</span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-500 text-white text-[9px] font-bold flex-shrink-0">PENDIENTE</span>
                        @endif
                    </div>
                    <div class="p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Vencimiento</span>
                            <div class="text-right">
                                <p class="text-sm font-bold {{ $isOverdue ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">{{ $inst->due_date->format('d/m/Y') }}</p>
                                @if($isOverdue)
                                    <p class="text-[9px] text-red-400 dark:text-red-500 font-medium">Hace {{ $daysOverdue }} días</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Cuota</span>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $cur }} {{ number_format($inst->amount, 2) }}</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Mora</span>
                            <p class="text-sm font-bold {{ $inst->late_fee > 0 ? 'text-orange-500' : 'text-gray-300 dark:text-gray-600' }}">{{ $inst->late_fee > 0 ? $cur . ' ' . number_format($inst->late_fee, 2) : '—' }}</p>
                        </div>
                        <div class="pt-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
                            <span class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Total</span>
                            <p class="text-base font-black {{ $isOverdue ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">{{ $cur }} {{ number_format($totalWithFee, 2) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Total final mobile -->
            <div class="bg-gray-100 dark:bg-white border border-gray-200 dark:border-gray-300 rounded-2xl p-4 flex items-center justify-between shadow-sm">
                <span class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-700">Total a cobrar</span>
                <p class="text-xl font-black text-gray-900 dark:text-gray-900">S/ {{ number_format($totalDebt + $totalLateFee, 2, '.', ',') }}</p>
            </div>
        </div>
    @endif
</div>

<!-- Modal Detalle Cuota -->
<div id="installment-modal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeInstallmentModal()"></div>
    <div class="absolute inset-2 sm:inset-4 md:inset-8 lg:inset-16 flex items-center justify-center pointer-events-none">
        <div id="installment-modal-content" class="bg-white dark:bg-gray-900 rounded-2xl sm:rounded-3xl shadow-2xl w-full max-w-2xl max-h-full overflow-hidden pointer-events-auto transform transition-all duration-300 opacity-0 scale-95 flex flex-col">
            <!-- Modal Header -->
            <div class="relative px-4 sm:px-6 py-4 sm:py-5 border-b border-gray-100 dark:border-gray-800 bg-gradient-to-r from-red-500 to-red-600 flex-shrink-0">
                <button onclick="closeInstallmentModal()" class="absolute top-3 right-3 sm:top-4 sm:right-4 w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 transition-colors flex items-center justify-center text-white flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <div class="flex items-center gap-3 sm:gap-4 pr-10">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-red-100 text-[9px] sm:text-[10px] font-bold uppercase tracking-widest">Detalle de Cuota Vencida</p>
                        <h2 id="modal-title" class="text-sm sm:text-lg font-black text-white truncate"></h2>
                    </div>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="overflow-y-auto overscroll-contain p-4 sm:p-6 space-y-4 sm:space-y-6 flex-1 min-h-0">

                <!-- Info Cliente -->
                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl sm:rounded-2xl p-3 sm:p-4">
                    <p class="text-[9px] sm:text-[10px] text-gray-400 uppercase tracking-widest font-bold mb-1.5 sm:mb-2">Cliente</p>
                    <p id="modal-client-name" class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white truncate"></p>
                    <div class="flex flex-wrap gap-1.5 sm:gap-2 mt-2">
                        <a id="modal-client-email" href="#" target="_blank" class="inline-flex items-center gap-1 bg-blue-500 hover:bg-blue-600 text-white text-[8px] sm:text-[9px] font-bold px-2 py-1 rounded-lg transition-colors">
                            <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            Email
                        </a>
                        <a id="modal-client-whatsapp" href="#" target="_blank" class="inline-flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-[8px] sm:text-[9px] font-bold px-2 py-1 rounded-lg transition-colors">
                            <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.587 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            WhatsApp
                        </a>
                    </div>
                </div>

                <!-- Grid: Préstamo + Cuota -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                    <!-- Datos Préstamo -->
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl sm:rounded-2xl p-3 sm:p-4">
                        <p class="text-[9px] sm:text-[10px] text-gray-400 uppercase tracking-widest font-bold mb-2 sm:mb-3 flex items-center gap-1.5">
                            <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Datos del Préstamo
                        </p>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-[9px] sm:text-[10px] text-gray-500 flex-shrink-0">Código</span>
                                <span id="modal-loan-code" class="text-[10px] sm:text-xs font-bold text-blue-500 truncate"></span>
                            </div>
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-[9px] sm:text-[10px] text-gray-500 flex-shrink-0">Tipo</span>
                                <span id="modal-loan-type" class="text-[10px] sm:text-xs font-bold text-gray-900 dark:text-white uppercase"></span>
                            </div>
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-[9px] sm:text-[10px] text-gray-500 flex-shrink-0">Moneda</span>
                                <span id="modal-loan-currency" class="text-[10px] sm:text-xs font-bold text-gray-900 dark:text-white"></span>
                            </div>
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-[9px] sm:text-[10px] text-gray-500 flex-shrink-0">Monto prestado</span>
                                <span id="modal-loan-amount" class="text-[10px] sm:text-xs font-bold text-gray-900 dark:text-white tabular-nums"></span>
                            </div>
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-[9px] sm:text-[10px] text-gray-500 flex-shrink-0">Total + intereses</span>
                                <span id="modal-loan-total" class="text-[10px] sm:text-xs font-bold text-gray-900 dark:text-white tabular-nums"></span>
                            </div>
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-[9px] sm:text-[10px] text-gray-500 flex-shrink-0">Tasa interés</span>
                                <span id="modal-loan-rate" class="text-[10px] sm:text-xs font-bold text-gray-900 dark:text-white"></span>
                            </div>
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-[9px] sm:text-[10px] text-gray-500 flex-shrink-0">Plazo</span>
                                <span id="modal-loan-months" class="text-[10px] sm:text-xs font-bold text-gray-900 dark:text-white"></span>
                            </div>
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-[9px] sm:text-[10px] text-gray-500 flex-shrink-0">Desembolso</span>
                                <span id="modal-disbursement" class="text-[10px] sm:text-xs font-bold text-gray-900 dark:text-white"></span>
                            </div>
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-[9px] sm:text-[10px] text-gray-500 flex-shrink-0">Estado préstamo</span>
                                <span id="modal-loan-status" class="text-[8px] sm:text-[9px] font-bold uppercase px-1.5 sm:px-2 py-0.5 rounded-full"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Datos Cuota -->
                    <div class="bg-red-500 rounded-xl sm:rounded-2xl p-3 sm:p-4 flex flex-col">
                        <p class="text-[9px] sm:text-[10px] text-white uppercase tracking-widest font-bold mb-2 sm:mb-3 flex items-center gap-1.5">
                            <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Detalle de la Cuota
                        </p>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-[9px] sm:text-[10px] text-white font-bold flex-shrink-0">N° Cuota</span>
                                <span id="modal-inst-number" class="text-[10px] sm:text-xs font-black text-white"></span>
                            </div>
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-[9px] sm:text-[10px] text-white font-bold flex-shrink-0">Vencimiento</span>
                                <span id="modal-due-date" class="text-[10px] sm:text-xs font-bold text-white"></span>
                            </div>
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-[9px] sm:text-[10px] text-white font-bold flex-shrink-0">Días de mora</span>
                                <span id="modal-days-overdue" class="text-[10px] sm:text-xs font-black text-white"></span>
                            </div>
                            <div class="border-t border-white/20 pt-2 mt-2 space-y-2">
                                <div class="flex justify-between items-center gap-2">
                                    <span class="text-[9px] sm:text-[10px] text-white font-bold flex-shrink-0">Capital</span>
                                    <span id="modal-capital" class="text-[10px] sm:text-xs font-bold text-white tabular-nums"></span>
                                </div>
                                <div class="flex justify-between items-center gap-2">
                                    <span class="text-[9px] sm:text-[10px] text-white font-bold flex-shrink-0">Interés</span>
                                    <span id="modal-interest" class="text-[10px] sm:text-xs font-bold text-white tabular-nums"></span>
                                </div>
                                <div class="flex justify-between items-center gap-2">
                                    <span class="text-[9px] sm:text-[10px] text-white font-bold flex-shrink-0">Cuota total</span>
                                    <span id="modal-amount" class="text-[10px] sm:text-xs font-bold text-white tabular-nums"></span>
                                </div>
                            </div>
                            <div class="flex justify-between items-center gap-2 pt-2 border-t border-white/20">
                                <span class="text-[9px] sm:text-[10px] text-white font-bold flex-shrink-0">Mora acumulada</span>
                                <span id="modal-late-fee" class="text-[10px] sm:text-xs font-black text-white tabular-nums"></span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center bg-black/20 -mx-3 sm:-mx-4 -mb-3 sm:-mb-4 px-3 sm:px-4 py-2 rounded-b-xl sm:rounded-b-2xl mt-auto">
                            <span class="text-[9px] sm:text-[10px] text-white font-bold uppercase tracking-wide flex-shrink-0">Total a pagar</span>
                            <span id="modal-total" class="text-xs sm:text-sm font-black text-white tabular-nums"></span>
                        </div>
                    </div>
                </div>

                <!-- Configuración de Mora -->
                <div class="bg-orange-500 rounded-xl sm:rounded-2xl p-3 sm:p-4">
                    <p class="text-[9px] sm:text-[10px] text-white uppercase tracking-widest font-bold mb-2 sm:mb-3 flex items-center gap-1.5">
                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 488.5 488.5"><path d="M196.7 169.5c41.2 0 74.7 33.4 74.7 74.7 0 41.2-33.4 74.7-74.7 74.7S122 285.5 122 244.2C122 203 155.4 169.5 196.7 169.5zM196.8 193.1c-.9 0-1.9 0-2.8 0c-3 .1-3.5.6-3.6 3.6c0 1.4 0 2.7 0 4.1c0 4 0 4-3.9 5.4c-9.3 3.4-15 9.7-15.7 19.9c-.5 9 4.1 15 11.5 19.5c4.5 2.7 9.6 4.3 14.4 6.5c1.9.8 3.7 1.8 5.2 3.1c4.6 3.8 3.8 10.2-1.7 12.6c-2.9 1.3-6 1.6-9.2 1.2c-4.9-.6-9.6-1.9-14.1-4.2c-2.6-1.4-3.4-1-4.2 1.8c-.8 2.4-1.4 4.9-2.1 7.3c-.9 3.3-.6 4.1 2.6 5.6c4 1.9 8.3 2.9 12.7 3.6c3.4.5 3.5.7 3.6 4.2c0 1.6 0 3.2 0 4.8c0 2 1 3.2 3.1 3.2c2.4 0 4.8 0 7.1 0c1.9 0 2.9-1.1 2.9-3.1c0-2.2.1-4.4 0-6.6s.9-3.4 3-4c5-1.4 9.2-4 12.5-8c9-11 5.6-27-7.2-34.1c-4-2.2-8.3-3.9-12.5-5.7c-2.4-1-4.8-2.2-6.9-3.9c-4.1-3.3-3.3-8.5 1.5-10.6c1.3-.6 2.8-.8 4.2-.9c5.5-.3 10.8.7 15.8 3.1c2.5 1.2 3.3.8 4.2-1.8c.9-2.7 1.6-5.5 2.4-8.3c.5-1.9-.1-3.1-1.9-3.9c-3.2-1.4-6.5-2.4-10-3c-4.5-.7-4.5-.7-4.5-5.3C203.2 193.1 203.2 193.1 196.8 193.1z"/></svg>
                        Configuración de Mora
                    </p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3">
                        <div class="text-center bg-black rounded-lg p-2">
                            <p class="text-[8px] sm:text-[9px] text-orange-400/70 uppercase">Mora activa</p>
                            <p id="modal-late-enabled" class="text-[10px] sm:text-xs font-bold text-orange-400 mt-0.5"></p>
                        </div>
                        <div class="text-center bg-black rounded-lg p-2">
                            <p class="text-[8px] sm:text-[9px] text-orange-400/70 uppercase">Días gracia</p>
                            <p id="modal-grace-days" class="text-[10px] sm:text-xs font-bold text-orange-400 mt-0.5"></p>
                        </div>
                        <div class="text-center bg-black rounded-lg p-2">
                            <p class="text-[8px] sm:text-[9px] text-orange-400/70 uppercase">Tasa mora</p>
                            <p id="modal-late-rate" class="text-[10px] sm:text-xs font-bold text-orange-400 mt-0.5"></p>
                        </div>
                        <div class="text-center bg-black rounded-lg p-2">
                            <p class="text-[8px] sm:text-[9px] text-orange-400/70 uppercase">Mora diaria</p>
                            <p id="modal-late-daily" class="text-[10px] sm:text-xs font-bold text-orange-400 mt-0.5"></p>
                        </div>
                    </div>
                </div>

                <!-- Deuda Acumulada -->
                <div class="bg-gray-900 dark:bg-black rounded-xl sm:rounded-2xl p-3 sm:p-4">
                    <p class="text-[9px] sm:text-[10px] text-gray-400 uppercase tracking-widest font-bold mb-2 sm:mb-3 flex items-center gap-1.5">
                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        Deuda Acumulada (esta + anteriores)
                    </p>
                    <div class="grid grid-cols-3 gap-2 sm:gap-3 text-center">
                        <div class="bg-white/5 rounded-lg p-2">
                            <p class="text-[8px] sm:text-[9px] text-gray-500 uppercase">Base</p>
                            <p id="modal-acc-base" class="text-xs sm:text-sm font-black text-white mt-1 tabular-nums"></p>
                        </div>
                        <div class="bg-white/5 rounded-lg p-2">
                            <p class="text-[8px] sm:text-[9px] text-gray-500 uppercase">Mora total</p>
                            <p id="modal-acc-mora" class="text-xs sm:text-sm font-black text-orange-400 mt-1 tabular-nums"></p>
                        </div>
                        <div class="bg-white/5 rounded-lg p-2">
                            <p class="text-[8px] sm:text-[9px] text-gray-500 uppercase">Total</p>
                            <p id="modal-acc-total" class="text-xs sm:text-sm font-black text-red-400 mt-1 tabular-nums"></p>
                        </div>
                    </div>
                    <p id="modal-acc-count" class="text-center text-[8px] sm:text-[9px] text-gray-500 mt-2"></p>
                </div>

                <!-- Alerta Default -->
                <div id="modal-default-alert" class="hidden bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-xl sm:rounded-2xl p-3 sm:p-4 flex items-start gap-2 sm:gap-3">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <p class="text-[10px] sm:text-xs font-bold text-red-600 dark:text-red-400">Préstamo en estado de DEFAULT</p>
                        <p class="text-[8px] sm:text-[10px] text-red-500/80 mt-0.5">Este préstamo ha sido marcado como en default. Se requieren acciones inmediatas.</p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50 flex items-center justify-between flex-shrink-0">
                <a id="modal-loan-link" href="#" class="text-[9px] sm:text-[10px] font-bold text-blue-500 hover:text-blue-600 dark:text-blue-400 uppercase tracking-widest flex items-center gap-1 transition-colors">
                    Ver préstamo completo
                    <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
                <button onclick="closeInstallmentModal()" class="text-[9px] sm:text-[10px] font-bold bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-gray-900 uppercase tracking-widest px-3 sm:px-4 py-1.5 sm:py-2 rounded-xl transition-colors">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openInstallmentModal(el) {
    const d = el.dataset;
    const cur = d.loanCurrency === 'USD' ? '$' : 'S/';

    document.getElementById('modal-title').textContent = `Cuota #${d.instNumber} — ${d.loanCode}`;
    document.getElementById('modal-client-name').textContent = d.userName;
    document.getElementById('modal-client-email').href = 'mailto:' + d.userEmail;
    document.getElementById('modal-client-whatsapp').href = 'https://wa.me/51' + d.userPhone.replace(/\D/g, '');

    document.getElementById('modal-loan-code').textContent = d.loanCode;
    document.getElementById('modal-loan-type').textContent = d.loanType === 'cuotas' ? 'Cuotas' : 'Único';
    document.getElementById('modal-loan-currency').textContent = cur;
    document.getElementById('modal-loan-amount').textContent = cur + ' ' + parseFloat(d.loanAmount).toFixed(2);
    document.getElementById('modal-loan-total').textContent = cur + ' ' + parseFloat(d.loanTotal).toFixed(2);
    document.getElementById('modal-loan-rate').textContent = parseFloat(d.loanInterestRate).toFixed(2) + '%';
    document.getElementById('modal-loan-months').textContent = d.loanMonths + ' meses';
    document.getElementById('modal-disbursement').textContent = d.loanDisbursement;

    const statusEl = document.getElementById('modal-loan-status');
    const statusMap = {
        'approved': 'Aprobado',
        'paid': 'Pagado',
        'pending': 'Pendiente',
        'processing': 'Procesando',
        'rejected': 'Rechazado'
    };
    statusEl.textContent = statusMap[d.loanStatus] || d.loanStatus;
    statusEl.className = 'text-[9px] font-bold uppercase px-2 py-0.5 rounded-full ' +
        (d.loanStatus === 'approved' ? 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400' :
         d.loanStatus === 'paid' ? 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' :
         'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400');

    document.getElementById('modal-inst-number').textContent = '#' + d.instNumber;
    document.getElementById('modal-due-date').textContent = d.instDueDate;
    document.getElementById('modal-days-overdue').textContent = d.daysOverdue + ' días';
    document.getElementById('modal-capital').textContent = cur + ' ' + parseFloat(d.instCapital).toFixed(2);
    document.getElementById('modal-interest').textContent = cur + ' ' + parseFloat(d.instInterest).toFixed(2);
    document.getElementById('modal-amount').textContent = cur + ' ' + parseFloat(d.instAmount).toFixed(2);
    document.getElementById('modal-late-fee').textContent = cur + ' ' + parseFloat(d.instLateFee).toFixed(2);
    document.getElementById('modal-total').textContent = cur + ' ' + parseFloat(d.totalWithFee).toFixed(2);

    document.getElementById('modal-late-enabled').textContent = d.lateFeeEnabled === 'true' ? 'SÍ' : 'NO';
    document.getElementById('modal-grace-days').textContent = d.graceDays + ' días';
    document.getElementById('modal-late-rate').textContent = parseFloat(d.lateFeeRate).toFixed(2) + '%';
    document.getElementById('modal-late-daily').textContent = cur + ' ' + parseFloat(d.lateDaily).toFixed(2);

    document.getElementById('modal-acc-base').textContent = cur + ' ' + parseFloat(d.accBase).toFixed(2);
    document.getElementById('modal-acc-mora').textContent = cur + ' ' + parseFloat(d.accMora).toFixed(2);
    document.getElementById('modal-acc-total').textContent = cur + ' ' + parseFloat(d.accTotal).toFixed(2);
    document.getElementById('modal-acc-count').textContent = '(incluye ' + d.accCount + ' cuota' + (d.accCount > 1 ? 's' : '') + ' pendiente' + (d.accCount > 1 ? 's' : '') + ')';

    const defaultAlert = document.getElementById('modal-default-alert');
    if (d.inDefault === 'true') {
        defaultAlert.classList.remove('hidden');
    } else {
        defaultAlert.classList.add('hidden');
    }

    document.getElementById('modal-loan-link').href = '{{ route('superadmin.installments.group', ['loan' => 'LOAN_ID_PLACEHOLDER']) }}'.replace('LOAN_ID_PLACEHOLDER', d.loanId) + '?source=modal';

    const modal = document.getElementById('installment-modal');
    const content = document.getElementById('installment-modal-content');
    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        content.classList.remove('opacity-0', 'scale-95');
        content.classList.add('opacity-100', 'scale-100');
    });
}

function closeInstallmentModal() {
    const content = document.getElementById('installment-modal-content');
    content.classList.remove('opacity-100', 'scale-100');
    content.classList.add('opacity-0', 'scale-95');
    setTimeout(() => {
        document.getElementById('installment-modal').classList.add('hidden');
    }, 200);
}

document.querySelectorAll('.installment-row').forEach(row => {
    row.addEventListener('click', () => openInstallmentModal(row));
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeInstallmentModal();
});
</script>
@endsection