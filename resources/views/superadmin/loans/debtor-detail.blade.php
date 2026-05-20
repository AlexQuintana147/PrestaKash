@extends('superadmin.layout')

@section('title', 'Deudas pendientes - ' . $fullName)

@section('content')
<div class="px-5 md:px-10 py-8">

    <!-- Header -->
    <div class="flex items-center gap-3 mb-8">
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
            <h1 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white truncate">{{ $fullName }}</h1>
            <div class="flex items-center gap-3 mt-0.5">
                <a href="mailto:{{ $user->email }}"
                   class="text-xs text-blue-500 hover:text-blue-600 transition-colors flex items-center gap-1">
                    {{ $user->email }}
                    <svg class="w-3 h-3" viewBox="0 0 512 512" xml:space="preserve"><rect x="7.904" y="119.947" style="fill:#5EBAE7;" width="496.195" height="272.109"/><polygon style="fill:#36A9E1;" points="256,280.01 23.91,127.951 7.904 135.954 256,312.022 352.037 223.987 "/><path style="fill:#1D1D1B;" d="M0,112.043v287.913h512V112.043H0z M477.266,127.851L256,270.603L34.733,127.851H477.266z M15.807,384.149V134.452L256,289.414l240.193-154.962v249.696H15.807V384.149z"/></svg>
                </a>
                <a href="https://wa.me/51{{ preg_replace('/\D/', '', $user->phone) }}" target="_blank"
                   class="text-xs text-green-500 hover:text-green-600 transition-colors flex items-center gap-1">
                    {{ $user->phone }}
                    <svg class="w-3 h-3" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M28 16C28 22.6274 22.6274 28 16 28C13.4722 28 11.1269 27.2184 9.19266 25.8837L5.09091 26.9091L6.16576 22.8784C4.80092 20.9307 4 18.5589 4 16C4 9.37258 9.37258 4 16 4C22.6274 4 28 9.37258 28 16Z" fill="#27B43E"/><path fill-rule="evenodd" clip-rule="evenodd" d="M16 30C23.732 30 30 23.732 30 16C30 8.26801 23.732 2 16 2C8.26801 2 2 8.26801 2 16C2 18.5109 2.661 20.8674 3.81847 22.905L2 30L9.31486 28.3038C11.1894 29.3854 13.5789 30 16 30ZM16 27.8462C22.5425 27.8462 27.8462 22.5425 27.8462 16C27.8462 9.45755 22.5425 4.15385 16 4.15385C9.45755 4.15385 4.15385 9.45755 4.15385 16C4.15385 18.5261 4.9445 20.8675 6.29184 22.7902L5.23077 26.7692L9.27993 25.7569C11.1894 27.0746 13.5046 27.8462 16 27.8462Z" fill="white"/><path d="M12.5 9.49989C12.1672 8.83131 11.6565 8.8905 11.1407 8.8905C10.2188 8.8905 8.78125 9.99478 8.78125 12.05C8.78125 13.7343 9.52345 15.578 12.0244 18.3361C14.438 20.9979 17.6094 22.3748 20.2422 22.3279C22.875 22.2811 23.4167 20.0154 23.4167 19.2503C23.4167 18.9112 23.2062 18.742 23.0613 18.696C22.1641 18.2654 20.5093 17.4631 20.1328 17.3124C19.7563 17.1617 19.5597 17.3656 19.4375 17.4765C19.0961 17.8018 18.4193 18.7608 18.1875 18.9765C17.9558 19.1922 17.6103 19.083 17.4665 19.0015C16.9374 18.7892 15.5029 18.1511 14.3595 17.0426C12.9453 15.6718 12.8623 15.2001 12.5959 14.7803C12.3828 14.4444 12.5392 14.2384 12.6172 14.1483C12.9219 13.7968 13.3426 13.254 13.5313 12.9843C13.7199 12.7145 13.5702 12.305 13.4803 12.05C13.0938 10.953 12.7663 10.0347 12.5 9.49989Z" fill="white"/></svg>
                </a>
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
        <div class="grid grid-cols-3 gap-4 mb-8">
            <div class="bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-900/30 rounded-2xl p-4 text-center">
                <p class="text-[10px] text-red-400 uppercase tracking-widest font-bold mb-1">Cuotas pendientes</p>
                <p class="text-2xl font-black text-red-600 dark:text-red-400">{{ $pendingInstallments->count() }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-4 text-center">
                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold mb-1">Capital + Interés</p>
                <p class="text-2xl font-black text-gray-900 dark:text-white">S/ {{ number_format($totalDebt, 2, '.', ',') }}</p>
            </div>
            <div class="bg-orange-50 dark:bg-orange-950/20 border border-orange-100 dark:border-orange-900/30 rounded-2xl p-4 text-center">
                <p class="text-[10px] text-orange-400 uppercase tracking-widest font-bold mb-1">Mora acumulada</p>
                <p class="text-2xl font-black text-orange-500">+ S/ {{ number_format($totalLateFee, 2, '.', ',') }}</p>
            </div>
        </div>

        <!-- Tabla de cuotas -->
        <div class="bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden">
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
                    @endphp
                    <div class="grid grid-cols-12 gap-4 px-4 py-3 items-center
                        {{ $isOverdue ? 'bg-red-50/50 dark:bg-red-950/10' : '' }} hover:bg-gray-50 dark:hover:bg-gray-900/50 transition-colors">
                        <div class="col-span-2">
                            <a href="{{ route('superadmin.installments.index', ['filter' => 'grouped']) }}#loan-{{ $inst->loan_id }}"
                               class="text-xs font-bold text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300 truncate block">
                                {{ $inst->loan->code }}
                            </a>
                        </div>
                        <div class="col-span-1 text-center">
                            <span class="inline-flex w-6 h-6 rounded-full items-center justify-center text-[9px] font-black tabular-nums
                                {{ $isOverdue ? 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400' }}">
                                {{ $inst->installment_number }}
                            </span>
                        </div>
                        <div class="col-span-2">
                            <p class="text-xs font-medium {{ $isOverdue ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                {{ $inst->due_date->format('d/m/Y') }}
                            </p>
                            @if($isOverdue)
                                <p class="text-[9px] text-red-400 dark:text-red-500 mt-0.5">
                                    Hace {{ now()->startOfDay()->diffInDays($inst->due_date) }} días
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
                                <span class="inline-flex items-center gap-0.5 text-[8px] font-bold text-orange-500 mt-0.5">
                                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 488.5 488.5"><path d="M196.7 169.5c41.2 0 74.7 33.4 74.7 74.7 0 41.2-33.4 74.7-74.7 74.7S122 285.5 122 244.2C122 203 155.4 169.5 196.7 169.5zM196.8 193.1c-.9 0-1.9 0-2.8 0c-3 .1-3.5.6-3.6 3.6c0 1.4 0 2.7 0 4.1c0 4 0 4-3.9 5.4c-9.3 3.4-15 9.7-15.7 19.9c-.5 9 4.1 15 11.5 19.5c4.5 2.7 9.6 4.3 14.4 6.5c1.9.8 3.7 1.8 5.2 3.1c4.6 3.8 3.8 10.2-1.7 12.6c-2.9 1.3-6 1.6-9.2 1.2c-4.9-.6-9.6-1.9-14.1-4.2c-2.6-1.4-3.4-1-4.2 1.8c-.8 2.4-1.4 4.9-2.1 7.3c-.9 3.3-.6 4.1 2.6 5.6c4 1.9 8.3 2.9 12.7 3.6c3.4.5 3.5.7 3.6 4.2c0 1.6 0 3.2 0 4.8c0 2 1 3.2 3.1 3.2c2.4 0 4.8 0 7.1 0c1.9 0 2.9-1.1 2.9-3.1c0-2.2.1-4.4 0-6.6s.9-3.4 3-4c5-1.4 9.2-4 12.5-8c9-11 5.6-27-7.2-34.1c-4-2.2-8.3-3.9-12.5-5.7c-2.4-1-4.8-2.2-6.9-3.9c-4.1-3.3-3.3-8.5 1.5-10.6c1.3-.6 2.8-.8 4.2-.9c5.5-.3 10.8.7 15.8 3.1c2.5 1.2 3.3.8 4.2-1.8c.9-2.7 1.6-5.5 2.4-8.3c.5-1.9-.1-3.1-1.9-3.9c-3.2-1.4-6.5-2.4-10-3c-4.5-.7-4.5-.7-4.5-5.3C203.2 193.1 203.2 193.1 196.8 193.1z"/></svg>
                                    CON MORA
                                </span>
                            @else
                                <p class="text-xs text-gray-300 dark:text-gray-600">—</p>
                            @endif
                        </div>
                        <div class="col-span-2 text-right">
                            <p class="text-xs font-black {{ $isOverdue ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">{{ $cur }} {{ number_format($totalWithFee, 2) }}</p>
                        </div>
                        <div class="col-span-1 text-center">
                            @if($isOverdue)
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400 text-[8px] font-bold">
                                    VENCIDA
                                </span>
                            @elseif($inst->status === 'processing')
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400 text-[8px] font-bold">
                                    VALIDANDO
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 text-[8px] font-bold">
                                    PENDIENTE
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Total final -->
            <div class="grid grid-cols-12 gap-4 px-4 py-4 bg-gray-900 dark:bg-gray-800 text-white items-center">
                <div class="col-span-8 text-right pr-4">
                    <span class="text-xs font-bold uppercase tracking-widest text-gray-400">Total a cobrar</span>
                </div>
                <div class="col-span-4 text-right">
                    <p class="text-lg font-black">S/ {{ number_format($totalDebt + $totalLateFee, 2, '.', ',') }}</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection