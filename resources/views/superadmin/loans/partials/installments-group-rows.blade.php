<div class="divide-y divide-gray-50 dark:divide-gray-900/50">
    @foreach($userInstallments as $inst)
        @include('superadmin.loans.partials.installment-row-minimal', ['inst' => $inst])
    @endforeach
</div>

@if($userInstallments->hasPages())
    <div class="mt-4 flex items-center justify-end gap-6">
        @if($userInstallments->onFirstPage())
            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-100 cursor-default">← Anterior</span>
        @else
            <button type="button"
                onclick="loadGroupPage(this)"
                data-loan-id="{{ $loan->id }}"
                data-page="{{ $userInstallments->currentPage() - 1 }}"
                class="text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-100 border-b border-gray-400 dark:border-gray-500 pb-px hover:text-gray-900 dark:hover:text-white hover:border-gray-900 dark:hover:border-white transition-colors">
                ← Anterior
            </button>
        @endif

        <span class="text-[9px] text-gray-400 font-bold tabular-nums">{{ $userInstallments->currentPage() }} / {{ $userInstallments->lastPage() }}</span>

        @if($userInstallments->hasMorePages())
            <button type="button"
                onclick="loadGroupPage(this)"
                data-loan-id="{{ $loan->id }}"
                data-page="{{ $userInstallments->currentPage() + 1 }}"
                class="text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-100 border-b border-gray-400 dark:border-gray-500 pb-px hover:text-gray-900 dark:hover:text-white hover:border-gray-900 dark:hover:border-white transition-colors">
                Siguiente →
            </button>
        @else
            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-100 cursor-default">Siguiente →</span>
        @endif
    </div>
@endif
