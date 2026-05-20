@php
    $cur = $loan->currency === 'USD' ? '$' : 'S/';
    $totalPaid = $loan->installments()->where('status', 'approved')->sum('amount');
    $totalPending = $loan->installments()->where('status', 'pending')->sum('amount');
    $overdueCount = $loan->installments()
        ->where('status', 'pending')
        ->where('due_date', '<', now()->startOfDay())
        ->count();
@endphp

<div class="space-y-4">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 rounded-2xl p-5 sm:p-6 text-white shadow-xl shadow-blue-500/25">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                <p class="text-[10px] font-bold uppercase tracking-widest text-blue-100 mb-1">Préstamo</p>
                <h2 class="text-xl sm:text-2xl font-black truncate">{{ $loan->code }}</h2>
                <div class="mt-2 flex flex-wrap items-center gap-3 text-sm">
                    <span class="flex items-center gap-1.5 bg-white/20 px-2.5 py-1 rounded-lg">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $cur }}{{ number_format($loan->amount, 2) }}
                    </span>
                    <span class="flex items-center gap-1.5 bg-white/20 px-2.5 py-1 rounded-lg">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                        </svg>
                        {{ $loan->months }} meses
                    </span>
                    <span class="flex items-center gap-1.5 bg-white/20 px-2.5 py-1 rounded-lg">
                        {{ ($loan->interest_rate ?? 0) * 100 }}%
                    </span>
                </div>
            </div>
            <div class="flex-shrink-0 text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider
                    {{ $loan->status === 'approved' ? 'bg-green-400 text-green-950' : ($loan->status === 'paid' ? 'bg-gray-200 text-gray-800' : 'bg-yellow-400 text-yellow-950') }}">
                    {{ match($loan->status) {
                        'approved' => 'Activo',
                        'paid' => 'Pagado',
                        'pending' => 'Pendiente',
                        default => $loan->status
                    } }}
                </span>
            </div>
        </div>

        @if($overdueCount > 0)
        <div class="mt-4 pt-4 border-t border-white/20 flex items-center gap-2 bg-red-500/30 px-3 py-2 rounded-xl">
            <svg class="w-4 h-4 text-red-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span class="text-xs font-bold text-red-100">{{ $overdueCount }} cuota{{ $overdueCount > 1 ? 's' : '' }} vencida{{ $overdueCount > 1 ? 's' : '' }}</span>
        </div>
        @endif
    </div>

    <div class="grid grid-cols-3 gap-3">
        <div class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-3 text-center">
            <p class="text-[9px] font-bold uppercase tracking-widest text-gray-400 mb-1">Total préstamo</p>
            <p class="text-base sm:text-lg font-black text-gray-900 dark:text-white tabular-nums">{{ $cur }}{{ number_format($loan->amount, 2) }}</p>
        </div>
        <div class="bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-900/30 rounded-xl p-3 text-center">
            <p class="text-[9px] font-bold uppercase tracking-widest text-green-600 mb-1">Pagado</p>
            <p class="text-base sm:text-lg font-black text-green-700 dark:text-green-400 tabular-nums">{{ $cur }}{{ number_format($totalPaid, 2) }}</p>
        </div>
        <div class="bg-orange-50 dark:bg-orange-950/20 border border-orange-200 dark:border-orange-900/30 rounded-xl p-3 text-center">
            <p class="text-[9px] font-bold uppercase tracking-widest text-orange-600 mb-1">Pendiente</p>
            <p class="text-base sm:text-lg font-black text-orange-700 dark:text-orange-400 tabular-nums">{{ $cur }}{{ number_format($totalPending, 2) }}</p>
        </div>
    </div>

    <div class="space-y-3">
        @foreach($userInstallments as $inst)
            @include('superadmin.loans.partials.installment-row-minimal', ['inst' => $inst])
        @endforeach
    </div>

    @if($userInstallments->hasPages())
        <div class="pt-4 border-t border-gray-200 dark:border-gray-800 flex items-center justify-between">
            @if(!$userInstallments->onFirstPage())
                <button type="button"
                    onclick="loadGroupPage(this)"
                    data-loan-id="{{ $loan->id }}"
                    data-page="{{ $userInstallments->currentPage() - 1 }}"
                    class="flex items-center gap-2 px-4 py-2.5 bg-gray-900 hover:bg-gray-800 dark:bg-white dark:hover:bg-gray-100 text-white dark:text-gray-900 text-xs font-bold uppercase tracking-wider rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Anterior
                </button>
            @else
                <div></div>
            @endif

            <div class="flex items-center gap-2 text-xs font-bold text-gray-500 dark:text-gray-400">
                <span class="tabular-nums">{{ $userInstallments->currentPage() }}</span>
                <span class="text-gray-300 dark:text-gray-600">/</span>
                <span class="tabular-nums">{{ $userInstallments->lastPage() }}</span>
            </div>

            @if($userInstallments->hasMorePages())
                <button type="button"
                    onclick="loadGroupPage(this)"
                    data-loan-id="{{ $loan->id }}"
                    data-page="{{ $userInstallments->currentPage() + 1 }}"
                    class="flex items-center gap-2 px-4 py-2.5 bg-gray-900 hover:bg-gray-800 dark:bg-white dark:hover:bg-gray-100 text-white dark:text-gray-900 text-xs font-bold uppercase tracking-wider rounded-xl transition-colors">
                    Siguiente
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            @else
                <div></div>
            @endif
        </div>
    @endif
</div>