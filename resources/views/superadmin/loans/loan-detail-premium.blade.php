@extends('superadmin.layout')

@section('title', 'Préstamo ' . $loan->code)

@php
    $cur = $loan->currency === 'USD' ? '$' : 'S/';
    $totalPaid = $loan->installments()->where('status', 'approved')->sum('amount');
    $totalPending = $loan->installments()->where('status', 'pending')->sum('amount');
    $overdueCount = $loan->installments()->where('status', 'pending')->where('due_date', '<', now()->startOfDay())->count();
    $totalInstallments = $loan->installments()->count();
    $paidInstallments = $loan->installments()->where('status', 'approved')->count();
    $pendingInstallments = $loan->installments()->where('status', 'pending')->count();
    $totalLateFees = $loan->installments()->get()->sum('late_fee');
    $nextDueDate = $loan->installments()->where('status', 'pending')->orderBy('due_date', 'asc')->first()?->due_date;
    $activeLoansCount = $loan->user ? \App\Models\Loan::where('user_id', $loan->user_id)->where('status', '!=', 'paid')->count() : 0;
    $activeLoans = $loan->user ? \App\Models\Loan::where('user_id', $loan->user_id)->where('status', '!=', 'paid')->orderBy('created_at', 'desc')->get() : collect([]);

    $allInstallments = $loan->installments()->get();
    $paidCapital = $allInstallments->where('status', 'approved')->sum('capital');
    $paidInterest = $allInstallments->where('status', 'approved')->sum('interest');
    $paidLateFees = 0;
    foreach ($allInstallments->where('status', 'approved') as $pi) {
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
    $pendingCapital = $allInstallments->where('status', 'pending')->sum('capital');
    $pendingInterest = $allInstallments->where('status', 'pending')->sum('interest');
    $pendingLateFees = $allInstallments->where('status', 'pending')->sum('late_fee');
    $totalPaidWithFees = $totalPaid + $paidLateFees;
    $totalExpectedReturn = $allInstallments->sum('amount') + $pendingLateFees + $paidLateFees;
    $balance = $pendingCapital + $pendingInterest + $pendingLateFees;
@endphp

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6 md:py-10">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ URL::previous() }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="min-w-0 flex-1">
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $loan->code }}</h1>
            <p class="text-[11px] text-gray-400 mt-0.5">Préstamo completo</p>
        </div>
        <span class="flex-shrink-0 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide
            {{ $loan->status === 'approved' ? 'bg-emerald-500 text-white' : ($loan->status === 'paid' ? 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300' : 'bg-amber-500 text-white') }}">
            {{ match($loan->status) { 'approved' => 'Activo', 'paid' => 'Pagado', default => $loan->status } }}
        </span>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="p-4 bg-gray-50 dark:bg-gray-900/80 rounded-xl">
            <p class="text-[9px] text-gray-400 uppercase tracking-wider font-bold mb-1">Monto</p>
            <p class="text-lg font-black text-gray-900 dark:text-white tabular-nums">{{ $cur }}{{ number_format($loan->amount, 2) }}</p>
            <p class="text-[9px] text-gray-400 mt-0.5">{{ $loan->months ?? '-' }}m · {{ number_format($loan->interest_rate ?? 0, 2) }}%</p>
        </div>
        <div class="p-4 bg-gray-50 dark:bg-gray-900/80 rounded-xl cursor-pointer hover:bg-emerald-50 dark:hover:bg-emerald-950/15 transition-colors group" onclick="openPaidModal()">
            <div class="flex items-start justify-between mb-1">
                <p class="text-[9px] text-gray-400 uppercase tracking-wider font-bold">Pagado</p>
                <svg class="w-3.5 h-3.5 text-gray-300 group-hover:text-emerald-400 transition-colors flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-lg font-black text-emerald-600 tabular-nums">{{ $cur }}{{ number_format($totalPaid, 2) }}</p>
            <div class="flex items-center gap-1.5 mt-0.5">
                <p class="text-[9px] text-emerald-500">{{ $paidInstallments }}/{{ $totalInstallments }}</p>
                <span class="px-1.5 py-0.5 bg-emerald-100/60 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 text-[7px] font-bold rounded opacity-70 group-hover:opacity-100 transition-opacity">click</span>
            </div>
        </div>
        <div class="p-4 bg-gray-50 dark:bg-gray-900/80 rounded-xl cursor-pointer hover:bg-orange-50 dark:hover:bg-orange-950/15 transition-colors group" onclick="openLoansModal()">
            <div class="flex items-start justify-between mb-1">
                <p class="text-[9px] text-gray-400 uppercase tracking-wider font-bold">Pendiente</p>
                <svg class="w-3.5 h-3.5 text-gray-300 group-hover:text-orange-400 transition-colors flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-lg font-black text-orange-600 tabular-nums">{{ $cur }}{{ number_format($totalPending, 2) }}</p>
            <div class="flex items-center gap-1.5 mt-0.5">
                <p class="text-[9px] text-orange-500">{{ $activeLoansCount }} préstamo{{ $activeLoansCount > 1 ? 's' : '' }} activo{{ $activeLoansCount > 1 ? 's' : '' }}</p>
                <span class="px-1.5 py-0.5 bg-orange-100/60 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 text-[7px] font-bold rounded opacity-70 group-hover:opacity-100 transition-opacity">click</span>
            </div>
        </div>
        <div class="p-4 bg-gray-50 dark:bg-gray-900/80 rounded-xl">
            <p class="text-[9px] text-gray-400 uppercase tracking-wider font-bold mb-1">Mora</p>
            <p class="text-lg font-black text-red-600 tabular-nums">{{ $cur }}{{ number_format($totalLateFees, 2) }}</p>
            @if($overdueCount > 0)
                <p class="text-[9px] text-red-500 mt-0.5">{{ $overdueCount }} vencida{{ $overdueCount > 1 ? 's' : '' }}</p>
            @endif
        </div>
    </div>

    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-900/80 rounded-xl">
        <p class="text-[9px] text-gray-400 uppercase tracking-wider font-bold mb-3">Información del préstamo</p>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-y-3 gap-x-4">
            <div><p class="text-[9px] text-gray-400 mb-0.5">Tipo</p><p class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ $loan->type === 'cuotas' ? 'Cuotas' : 'Pago único' }}</p></div>
            <div><p class="text-[9px] text-gray-400 mb-0.5">Moneda</p><p class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ $loan->currency === 'USD' ? 'USD' : 'PEN' }}</p></div>
            <div><p class="text-[9px] text-gray-400 mb-0.5">Plazo</p><p class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ $loan->months ?? '-' }} meses</p></div>
            <div><p class="text-[9px] text-gray-400 mb-0.5">Interés</p><p class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ number_format(($loan->interest_rate ?? 0), 2) }}% mensual</p></div>
            <div><p class="text-[9px] text-gray-400 mb-0.5">Desembolso</p><p class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ $loan->disbursement_date ? \Carbon\Carbon::parse($loan->disbursement_date)->format('d/m/Y') : '-' }}</p></div>
            <div><p class="text-[9px] text-gray-400 mb-0.5">Estado</p><p class="text-xs font-semibold {{ $loan->in_default ? 'text-red-600' : 'text-emerald-600' }}">{{ $loan->in_default ? 'Default' : 'Normal' }}</p></div>
        </div>

        @if($loan->late_fee_enabled)
        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
            <p class="text-[9px] text-orange-400 uppercase tracking-wider font-bold mb-2">Mora configurada</p>
            <div class="flex flex-wrap gap-3">
                <span class="px-2 py-1 bg-orange-50 dark:bg-orange-950/20 rounded text-[10px] font-bold text-orange-700 dark:text-orange-300">Gracia: {{ $loan->grace_days ?? 0 }}d</span>
                <span class="px-2 py-1 bg-orange-50 dark:bg-orange-950/20 rounded text-[10px] font-bold text-orange-700 dark:text-orange-300">Tasa: {{ number_format($loan->late_fee_rate ?? 0, 2) }}%</span>
                <span class="px-2 py-1 bg-orange-50 dark:bg-orange-950/20 rounded text-[10px] font-bold text-orange-700 dark:text-orange-300">Diaria: {{ $cur }}{{ number_format($loan->late_fee_daily_amount ?? 0, 2) }}</span>
            </div>
        </div>
        @endif
    </div>

    @if($loan->user)
    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-900/80 rounded-xl">
        <p class="text-[9px] text-gray-400 uppercase tracking-wider font-bold mb-3">Cliente</p>
        <div class="flex items-center gap-3">
            @php
                $selfie = $loan->user->documents?->selfie;
                $userPhoto = $selfie ? \Storage::url($selfie) : ($loan->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($loan->user->name) . '&background=6366f1&color=fff');
            @endphp
            <img src="{{ $userPhoto }}" 
                 class="w-10 h-10 rounded-lg object-cover flex-shrink-0" alt="">
            <div class="min-w-0 flex-1">
                <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $loan->user->name }}</p>
                <p class="text-[11px] text-gray-500 truncate">{{ $loan->user->email }}</p>
            </div>
            <a href="mailto:{{ $loan->user->email }}" class="flex-shrink-0 flex items-center gap-1 bg-blue-500 hover:bg-blue-600 transition-colors px-2.5 py-1.5 text-white font-bold text-[9px] rounded-lg">
                <svg class="w-3 h-3 flex-shrink-0" viewBox="0 0 512 512" xml:space="preserve"><rect x="7.904" y="119.947" style="fill:#5EBAE7;" width="496.195" height="272.109"/><polygon style="fill:#36A9E1;" points="256,280.01 23.91,127.951 7.904,135.954 256,312.022 352.037,223.987 "/><path style="fill:#1D1D1B;" d="M0,112.043v287.913h512V112.043H0z M477.266,127.851L256,270.603L34.733,127.851H477.266z M15.807,384.149V134.452L256,289.414l240.193-154.962v249.696H15.807V384.149z"/><g><rect x="215.982" y="344.138" style="fill:#FFFFFF;" width="16.007" height="15.807"/><rect x="247.997" y="344.138" style="fill:#FFFFFF;" width="16.006" height="15.807"/><rect x="280.013" y="344.138" style="fill:#FFFFFF;" width="16.007" height="15.807"/></g></svg>
                Email
            </a>
            <a href="https://wa.me/51{{ preg_replace('/\D/', '', $loan->user->phone ?? '') }}" target="_blank" class="flex-shrink-0 flex items-center gap-1 bg-green-500 hover:bg-green-600 transition-colors px-2.5 py-1.5 text-white font-bold text-[9px] rounded-lg">
                <svg class="w-3 h-3 flex-shrink-0" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M28 16C28 22.6274 22.6274 28 16 28C13.4722 28 11.1269 27.2184 9.19266 25.8837L5.09091 26.9091L6.16576 22.8784C4.80092 20.9307 4 18.5589 4 16C4 9.37258 9.37258 4 16 4C22.6274 4 28 9.37258 28 16Z" fill="#27B43E"/><path fill-rule="evenodd" clip-rule="evenodd" d="M16 30C23.732 30 30 23.732 30 16C30 8.26801 23.732 2 16 2C8.26801 2 2 8.26801 2 16C2 18.5109 2.661 20.8674 3.81847 22.905L2 30L9.31486 28.3038C11.1894 29.3854 13.5789 30 16 30ZM16 27.8462C22.5425 27.8462 27.8462 22.5425 27.8462 16C27.8462 9.45755 22.5425 4.15385 16 4.15385C9.45755 4.15385 4.15385 9.45755 4.15385 16C4.15385 18.5261 4.9445 20.8675 6.29184 22.7902L5.23077 26.7692L9.27993 25.7569C11.1894 27.0746 13.5046 27.8462 16 27.8462Z" fill="white"/><path d="M12.5 9.49989C12.1672 8.83131 11.6565 8.8905 11.1407 8.8905C10.2188 8.8905 8.78125 9.99478 8.78125 12.05C8.78125 13.7343 9.52345 15.578 12.0244 18.3361C14.438 20.9979 17.6094 22.3748 20.2422 22.3279C22.875 22.2811 23.4167 20.0154 23.4167 19.2503C23.4167 18.9112 23.2062 18.742 23.0613 18.696C22.1641 18.2654 20.5093 17.4631 20.1328 17.3124C19.7563 17.1617 19.5597 17.3656 19.4375 17.4765C19.0961 17.8018 18.4193 18.7608 18.1875 18.9765C17.9558 19.1922 17.6103 19.083 17.4665 19.0015C16.9374 18.7892 15.5029 18.1511 14.3595 17.0426C12.9453 15.6718 12.8623 15.2001 12.5959 14.7803C12.3828 14.4444 12.5392 14.2384 12.6172 14.1483C12.9219 13.7968 13.3426 13.254 13.5313 12.9843C13.7199 12.7145 13.5702 12.305 13.4803 12.05C13.0938 10.953 12.7663 10.0347 12.5 9.49989Z" fill="white"/></svg>
                WhatsApp
            </a>
        </div>
    </div>
    @endif

    <div class="mb-6">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-bold text-gray-900 dark:text-white">Cuotas</p>
            <p class="text-[10px] text-gray-400">{{ $userInstallments->total() }} total</p>
        </div>

        <div class="space-y-2">
            @foreach($userInstallments as $inst)
                @php
                    $isOverdue = $inst->due_date->isPast() && $inst->status !== 'approved';
                    $isDueToday = $inst->due_date->isToday() && $inst->status !== 'approved';
                    $instTotal = $inst->amount + ($inst->late_fee ?? 0);
                @endphp

                <div class="group rounded-xl overflow-hidden {{ $isOverdue ? 'bg-red-50 dark:bg-red-950/15' : 'bg-gray-50 dark:bg-gray-900/60' }}
                    {{ $inst->status === 'approved' || (isset($inst->is_interest_payment) && $inst->is_interest_payment && isset($inst->interest_payment) && $inst->interest_payment->paid_at) ? 'opacity-65' : '' }}"
                    data-installment-id="{{ $inst->id }}"
                    data-is-interest-payment="{{ isset($inst->is_interest_payment) && $inst->is_interest_payment ? 'true' : 'false' }}"
                    @if(isset($inst->is_interest_payment) && $inst->is_interest_payment && isset($inst->interest_payment))
                        data-interest-payment-id="{{ $inst->interest_payment->id }}"
                        data-paid-at="{{ $inst->interest_payment->paid_at->timestamp }}"
                    @endif
                    {{ $isOverdue || $isDueToday ? 'overdue-installment-row' : '' }}">

                    <div class="flex items-stretch">

                        <div class="w-12 flex items-center justify-center flex-shrink-0
                            {{ $inst->status === 'approved' ? 'bg-emerald-500' : ((isset($inst->is_interest_payment) && $inst->is_interest_payment && isset($inst->interest_payment) && $inst->interest_payment->paid_at) ? 'bg-emerald-500' : ($isOverdue ? 'bg-red-500' : 'bg-gray-200 dark:bg-gray-700')) }}">
                            <span class="text-xs font-black text-white">{{ $inst->installment_number }}</span>
                        </div>

                        <div class="flex-1 min-w-0 px-4 py-3 flex flex-col justify-center">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-bold {{ $isOverdue ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">{{ $inst->due_date->format('d/m/Y') }}</span>
                                <span class="px-1.5 py-0.5 rounded text-[8px] font-bold uppercase
                                    {{ $inst->status === 'approved' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400' :
                                       ((isset($inst->is_interest_payment) && $inst->is_interest_payment && isset($inst->interest_payment) && $inst->interest_payment->paid_at) ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400' :
                                       ($inst->status === 'processing' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-400 animate-pulse' :
                                       ($inst->status === 'rejected' ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400' :
                                       ($isOverdue ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-400')))) }}">
                                    @if($inst->status === 'approved')
                                        Pagado
                                    @elseif(isset($inst->is_interest_payment) && $inst->is_interest_payment && isset($inst->interest_payment) && $inst->interest_payment->paid_at)
                                        Pagado
                                    @else
                                        {{ match ($inst->status) {
                                        'pending' => $isOverdue ? 'Vencida' : 'Pendiente',
                                        'processing' => 'Validar',
                                        'rejected' => 'Rechazado',
                                        default => $inst->status
                                    } }}
                                    @endif
                                </span>
                            </div>
                            <div class="flex items-center gap-3 text-[10px]">
                                <span class="text-gray-500 dark:text-gray-400">Capital: <strong class="text-gray-700 dark:text-gray-300 tabular-nums">{{ $cur }}{{ number_format($inst->capital ?? 0, 2) }}</strong></span>
                                <span class="text-gray-500 dark:text-gray-400">Interés: <strong class="text-gray-700 dark:text-gray-300 tabular-nums">{{ $cur }}{{ number_format($inst->interest ?? 0, 2) }}</strong></span>
                                @if($inst->late_fee > 0)
                                    <span class="font-bold text-orange-600 dark:text-orange-400 tabular-nums">+{{ $cur }}{{ number_format($inst->late_fee, 2) }} mora</span>
                                @endif
                            </div>
                        </div>

                        <div class="w-28 flex items-center justify-end px-3 py-3 flex-shrink-0 border-l border-gray-100 dark:border-gray-800">
                            <p class="text-base font-black {{ $isOverdue ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }} tabular-nums text-right">{{ $cur }}{{ number_format($instTotal, 2) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @if($userInstallments->hasPages())
    <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-800">
        @if(!$userInstallments->onFirstPage())
            <a href="{{ route('superadmin.installments.group', $loan) }}?page={{ $userInstallments->currentPage() - 1 }}&source=modal"
                class="px-4 py-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg text-xs font-bold text-gray-700 dark:text-gray-300 transition-colors">← Anterior</a>
        @else
            <div></div>
        @endif

        <div class="flex items-center gap-1">
            @for($i = 1; $i <= $userInstallments->lastPage(); $i++)
                @if($i == $userInstallments->currentPage())
                    <span class="w-7 h-7 flex items-center justify-center rounded-md bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-[10px] font-bold">{{ $i }}</span>
                @elseif(abs($i - $userInstallments->currentPage()) <= 2 || $i == 1 || $i == $userInstallments->lastPage())
                    <a href="{{ route('superadmin.installments.group', $loan) }}?page={{ $i }}&source=modal"
                        class="w-7 h-7 flex items-center justify-center rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 text-[10px] font-bold text-gray-500 dark:text-gray-400 transition-colors">{{ $i }}</a>
                @elseif(abs($i - $userInstallments->currentPage()) == 3)
                    <span class="text-gray-300 dark:text-gray-700 text-[10px]">...</span>
                @endif
            @endfor
        </div>

        @if($userInstallments->hasMorePages())
            <a href="{{ route('superadmin.installments.group', $loan) }}?page={{ $userInstallments->currentPage() + 1 }}&source=modal"
                class="px-4 py-2 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 rounded-lg text-xs font-bold text-white dark:text-gray-900 transition-colors">Siguiente →</a>
        @else
            <div></div>
        @endif
    </div>
    @endif

</div>

<div id="paid-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closePaidModal()"></div>
    <div class="absolute bottom-0 left-0 right-0 sm:bottom-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 sm:max-w-md sm:w-full bg-white dark:bg-gray-900 rounded-t-2xl sm:rounded-2xl overflow-hidden flex flex-col max-h-[85vh] sm:max-h-[80vh]">
        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 dark:border-gray-800 flex-shrink-0">
            <div>
                <h2 class="text-sm font-bold text-gray-900 dark:text-white">Detalle de pagos</h2>
                <p class="text-[10px] text-gray-400">{{ $loan->code }} · {{ $paidInstallments }}/{{ $totalInstallments }} cuotas pagadas</p>
            </div>
            <button onclick="closePaidModal()" class="w-8 h-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 flex items-center justify-center transition-colors -mr-1">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-3">

            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/15 rounded-xl border border-emerald-100 dark:border-emerald-900/30">
                <p class="text-[9px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-3">Abonos Realizados</p>
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] text-gray-500 dark:text-gray-400">Abono a Capital</span>
                        <span class="text-xs font-bold text-gray-900 dark:text-white tabular-nums">{{ $cur }}{{ number_format($paidCapital, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] text-gray-500 dark:text-gray-400">Abono Intereses</span>
                        <span class="text-xs font-bold text-gray-900 dark:text-white tabular-nums">{{ $cur }}{{ number_format($paidInterest, 2) }}</span>
                    </div>
                    @if($paidLateFees > 0)
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] text-gray-500 dark:text-gray-400">Intereses por Mora pagados</span>
                        <span class="text-xs font-bold text-orange-600 dark:text-orange-400 tabular-nums">{{ $cur }}{{ number_format($paidLateFees, 2) }}</span>
                    </div>
                    @endif
                    <div class="pt-2 mt-1 border-t border-emerald-200/60 dark:border-emerald-800/30">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-300">Total Pagado</span>
                            <span class="text-sm font-black text-emerald-700 dark:text-emerald-300 tabular-nums">{{ $cur }}{{ number_format($totalPaidWithFees, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700/50">
                <p class="text-[9px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Balance Pendiente</p>
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] text-gray-500 dark:text-gray-400">Deuda de Capital</span>
                        <span class="text-xs font-bold text-gray-900 dark:text-white tabular-nums">{{ $cur }}{{ number_format($pendingCapital, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] text-gray-500 dark:text-gray-400">Intereses pendientes</span>
                        <span class="text-xs font-bold text-gray-900 dark:text-white tabular-nums">{{ $cur }}{{ number_format($pendingInterest, 2) }}</span>
                    </div>
                    @if($pendingLateFees > 0)
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] text-orange-500 dark:text-orange-400">Mora pendiente</span>
                        <span class="text-xs font-bold text-orange-600 dark:text-orange-400 tabular-nums">{{ $cur }}{{ number_format($pendingLateFees, 2) }}</span>
                    </div>
                    @endif
                    <div class="pt-2 mt-1 border-t border-gray-200 dark:border-gray-700/50">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-gray-700 dark:text-gray-300">Balance Total</span>
                            <span class="text-sm font-black text-gray-900 dark:text-white tabular-nums">{{ $cur }}{{ number_format($balance, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-blue-50 dark:bg-blue-950/15 rounded-xl border border-blue-100 dark:border-blue-900/30">
                <p class="text-[9px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-3">Resumen del Préstamo</p>
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] text-gray-500 dark:text-gray-400">Capital prestado</span>
                        <span class="text-xs font-bold text-gray-900 dark:text-white tabular-nums">{{ $cur }}{{ number_format($loan->amount, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] text-gray-500 dark:text-gray-400">Retorno esperado total</span>
                        <span class="text-xs font-bold text-blue-700 dark:text-blue-300 tabular-nums">{{ $cur }}{{ number_format($totalExpectedReturn, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] text-gray-500 dark:text-gray-400">% Recuperado</span>
                        <span class="text-xs font-bold {{ $totalPaidWithFees >= $totalExpectedReturn ? 'text-emerald-600' : 'text-blue-600' }} dark:text-blue-300 tabular-nums">{{ $totalExpectedReturn > 0 ? round($totalPaidWithFees / $totalExpectedReturn * 100, 1) : 0 }}%</span>
                    </div>
                </div>
            </div>

        </div>

        <div class="flex-shrink-0 px-5 py-3 border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50">
            <div class="grid grid-cols-3 gap-3">
                <div class="text-center">
                    <p class="text-[8px] text-gray-400 uppercase tracking-wider">Cuotas</p>
                    <p class="text-sm font-black text-emerald-600 dark:text-emerald-400 tabular-nums">{{ $paidInstallments }}/{{ $totalInstallments }}</p>
                </div>
                <div class="text-center">
                    <p class="text-[8px] text-gray-400 uppercase tracking-wider">Pagado</p>
                    <p class="text-sm font-black text-emerald-600 dark:text-emerald-400 tabular-nums">{{ $cur }}{{ number_format($totalPaidWithFees, 2) }}</p>
                </div>
                <div class="text-center">
                    <p class="text-[8px] text-gray-400 uppercase tracking-wider">Pendiente</p>
                    <p class="text-sm font-black text-orange-600 dark:text-orange-400 tabular-nums">{{ $cur }}{{ number_format($balance, 2) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="loans-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeLoansModal()"></div>
    <div class="absolute bottom-0 left-0 right-0 sm:bottom-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 sm:max-w-lg sm:w-full bg-white dark:bg-gray-900 rounded-t-2xl sm:rounded-2xl overflow-hidden flex flex-col max-h-[85vh] sm:max-h-[80vh]">
        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 dark:border-gray-800 flex-shrink-0">
            <div>
                <h2 class="text-sm font-bold text-gray-900 dark:text-white">Préstamos activos</h2>
                <p class="text-[10px] text-gray-400">{{ $loan->user->name ?? 'Cliente' }}</p>
            </div>
            <button onclick="closeLoansModal()" class="w-8 h-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 flex items-center justify-center transition-colors -mr-1">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div id="loans-list" class="flex-1 overflow-y-auto overscroll-contain divide-y divide-gray-100 dark:divide-gray-800/60"></div>

        <div id="loans-pagination" class="px-4 py-2.5 border-t border-gray-100 dark:border-gray-800 bg-gray-50/80 dark:bg-gray-900/30 flex-shrink-0 hidden">
            <div class="flex items-center justify-between gap-3">
                <button onclick="loansPrevPage()" id="loans-prev-btn"
                    class="px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg text-[10px] font-bold text-gray-600 dark:text-gray-300 transition-colors disabled:opacity-30 disabled:pointer-events-none">← Anterior</button>
                <div id="loans-page-dots" class="flex items-center gap-1"></div>
                <button onclick="loansNextPage()" id="loans-next-btn"
                    class="px-3 py-1.5 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 border border-gray-900 dark:border-gray-200 rounded-lg text-[10px] font-bold text-white dark:text-gray-900 transition-colors disabled:opacity-30 disabled:pointer-events-none">Siguiente →</button>
            </div>
        </div>
    </div>
</div>

<script>
const loansData = [
    @foreach($activeLoans as $activeLoan)
        @php
            $aCur = $activeLoan->currency === 'USD' ? '$' : 'S/';
            $aPaid = $activeLoan->installments()->where('status', 'approved')->sum('amount');
            $aPending = $activeLoan->installments()->where('status', 'pending')->sum('amount');
            $aOverdue = $activeLoan->installments()->where('status', 'pending')->where('due_date', '<', now()->startOfDay())->count();
            $aTotal = $activeLoan->installments()->get()->sum('late_fee') + $aPending;
            $isCurrent = $activeLoan->id === $loan->id;
        @endphp
        {
            code: '{{ $activeLoan->code }}',
            url: '{{ route('superadmin.installments.group', $activeLoan) }}?source=modal',
            num: {{ $loop->index + 1 }},
            isCurrent: {{ $isCurrent ? 'true' : 'false' }},
            overdue: {{ $aOverdue }},
            cur: '{{ $aCur }}',
            amount: {{ number_format($activeLoan->amount, 2, '.', '') }},
            paid: {{ number_format($aPaid, 2, '.', '') }},
            debt: {{ number_format($aTotal, 2, '.', '') }}
        },
    @endforeach
];
let loansPage = 1;
const loansPerPage = 5;
const loansTotalPages = Math.ceil(loansData.length / loansPerPage);

function renderLoansPage() {
    const container = document.getElementById('loans-list');
    if (loansData.length === 0) {
        container.innerHTML = `<div class="py-16 text-center"><div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center"><svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg></div><p class="text-sm font-medium text-gray-400">Sin préstamos</p><p class="text-[11px] text-gray-300 mt-0.5">No hay préstamos activos para este cliente</p></div>`;
        document.getElementById('loans-pagination').classList.add('hidden');
        return;
    }
    const start = (loansPage - 1) * loansPerPage;
    const pageItems = loansData.slice(start, start + loansPerPage);
    let html = '';
    pageItems.forEach(function(l) {
        const badgeColor = l.isCurrent ? 'bg-orange-500' : (l.overdue > 0 ? 'bg-red-500' : 'bg-gray-200 dark:bg-gray-700');
        const rowBg = l.isCurrent ? 'bg-orange-50/70 dark:bg-orange-950/10' : '';
        const codeColor = l.isCurrent ? 'text-orange-600 dark:text-orange-400' : 'text-gray-900 dark:text-white';
        const debtColor = l.overdue > 0 ? 'text-red-600 dark:text-red-400' : 'text-orange-600 dark:text-orange-400';
        let badges = '';
        if (l.isCurrent) badges += `<span class="px-1.5 py-px bg-orange-500 text-white text-[8px] font-bold rounded">Actual</span>`;
        if (l.overdue > 0) badges += `<span class="px-1.5 py-px bg-red-500 text-white text-[8px] font-bold rounded">${l.overdue}</span>`;

        html += `<a href="${l.url}" class="flex items-center gap-3 px-5 py-3.5 ${rowBg} hover:bg-gray-50 dark:hover:bg-gray-900/50 transition-colors active:bg-gray-100 dark:active:bg-gray-800/50 group">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 ${badgeColor}">
                <span class="text-xs font-black text-white tabular-nums">#${l.num}</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-1.5 mb-1">
                    <span class="text-sm font-bold ${codeColor}">${l.code}</span>${badges}
                </div>
                <div class="flex items-center gap-x-4 gap-y-1 flex-wrap text-[11px] text-gray-400 dark:text-gray-500">
                    <span>${l.cur}<strong class="text-gray-600 dark:text-gray-300 tabular-nums ml-0.5">${Number(l.amount).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2})}</strong></span>
                    <span>Pagado: <strong class="text-emerald-600 dark:text-emerald-400 tabular-nums">${Number(l.paid).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2})}</strong></span>
                    <span>Deuda: <strong class="${debtColor} tabular-nums">${Number(l.debt).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2})}</strong></span>
                </div>
            </div>
            <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 group-hover:text-gray-400 dark:group-hover:text-gray-500 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>`;
    });
    container.innerHTML = html;

    const pagDiv = document.getElementById('loans-pagination');
    if (loansTotalPages > 1) {
        pagDiv.classList.remove('hidden');
        document.getElementById('loans-prev-btn').disabled = loansPage <= 1;
        document.getElementById('loans-next-btn').disabled = loansPage >= loansTotalPages;

        const dotsContainer = document.getElementById('loans-page-dots');
        dotsContainer.innerHTML = '';
        for (let i = 1; i <= loansTotalPages; i++) {
            const dot = document.createElement('button');
            dot.className = `w-7 h-7 flex items-center justify-center rounded-md text-[10px] font-bold transition-colors ${
                i === loansPage ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900' : 'hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-400'
            }`;
            dot.textContent = i;
            dot.onclick = function() { loansPage = i; renderLoansPage(); };
            dotsContainer.appendChild(dot);
        }
    } else {
        pagDiv.classList.add('hidden');
    }
}

function loansPrevPage() { if (loansPage > 1) { loansPage--; renderLoansPage(); } }
function loansNextPage() { if (loansPage < loansTotalPages) { loansPage++; renderLoansPage(); } }

function openLoansModal() {
    loansPage = 1;
    renderLoansPage();
    document.getElementById('loans-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeLoansModal() {
    document.getElementById('loans-modal').classList.add('hidden');
    document.body.style.overflow = '';
}

function openPaidModal() {
    document.getElementById('paid-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closePaidModal() {
    document.getElementById('paid-modal').classList.add('hidden');
    document.body.style.overflow = '';
}
</script>

@endsection