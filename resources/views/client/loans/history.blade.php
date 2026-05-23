@extends('client.layout')

@section('title', 'Historial de Préstamos')

@section('content')
    <div class="min-h-screen px-5 md:px-10 py-8">

        <!-- Header -->
        <div class="mb-12 flex flex-col gap-4">
            <a href="{{ route('client.loans') }}"
                class="text-xs font-semibold text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors flex items-center gap-2 self-start">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver al panel
            </a>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Historial Crediticio
                </h1>
            </div>
        </div>

        @if($loans->isEmpty() && !request()->filled('code') && !request()->filled('filter'))
            <div
                class="text-center">
                <div class="w-20 h-20 flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-gray-300 dark:text-gray-700" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Sin actividad reciente</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 max-w-xs mx-auto mb-8">Aún no tienes préstamos registrados en
                    tu historial. Solicita uno para comenzar.</p>
                <a href="{{ route('client.loans.create') }}"
                    class="text-sm font-bold text-gray-900 dark:text-white border-b border-gray-900 dark:border-white pb-0.5 hover:opacity-60 transition-opacity">
                    Solicitar préstamo
                </a>
            </div>
        @else
            <div class="flex flex-col gap-3 mb-8">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" id="search-code" placeholder="Buscar por código..." class="w-full pl-10 pr-4 py-3 text-sm bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-black dark:focus:ring-white focus:border-transparent transition-all">
                    </div>
                    <div class="flex gap-2 overflow-x-auto -mx-5 px-5 sm:mx-0 sm:px-0 pb-1 sm:pb-0 scrollbar-none">
                        <button type="button" data-filter="all" class="filter-btn px-5 py-2.5 text-xs font-bold uppercase tracking-wider rounded-xl transition-all bg-black dark:bg-white text-white dark:text-black whitespace-nowrap flex-shrink-0">
                            Todos
                        </button>
                        <button type="button" data-filter="approved" class="filter-btn px-5 py-2.5 text-xs font-bold uppercase tracking-wider rounded-xl transition-all bg-white dark:bg-gray-900 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-800 hover:border-gray-400 dark:hover:border-gray-600 whitespace-nowrap flex-shrink-0">
                            Activos
                        </button>
                        <button type="button" data-filter="paid" class="filter-btn px-5 py-2.5 text-xs font-bold uppercase tracking-wider rounded-xl transition-all bg-white dark:bg-gray-900 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-800 hover:border-gray-400 dark:hover:border-gray-600 whitespace-nowrap flex-shrink-0">
                            Pagados
                        </button>
                    </div>
                </div>

            @if($loans->isEmpty())
                <div class="text-center py-12">
                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Sin resultados para los filtros aplicados</p>
                    <a href="{{ route('client.loans.history') }}" class="text-xs font-bold text-gray-900 dark:text-white border-b border-gray-900 dark:border-white pb-0.5 hover:opacity-60 transition-opacity mt-2 inline-block">Limpiar filtros</a>
                </div>
            @else
            <div id="no-results" class="text-center py-12" style="display:none">
                <svg class="w-8 h-8 text-gray-300 dark:text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <p class="text-sm text-gray-500 dark:text-gray-400">Sin resultados para la búsqueda</p>
            </div>
            <div class="grid grid-cols-1">
                @foreach($loans as $loan)
                    @php
                        $total_h = $loan->installments->count();
                        if ($total_h === 0) {
                            $installmentsPerMonth = match ($loan->payment_frequency) {
                                'diario' => 30, 'semanal' => 4, 'quincenal' => 2, default => 1,
                            };
                            $total_h = $loan->months * $installmentsPerMonth;
                        }
                        $paid_h = $loan->installments->where('status', 'approved')->count();
                        $percent_inst = ($paid_h / ($total_h ?: 1)) * 100;

                        $nextPending = $loan->installments->where('status', '!=', 'approved')->sortBy('due_date')->first();
                        $daysLeft = $nextPending ? (int) now()->startOfDay()->diffInDays($nextPending->due_date->startOfDay(), false) : null;

                        $pendingReview = $loan->installments->where('status', 'processing')->count();
                        $rejected = $loan->installments->where('status', 'rejected')->count();
                        $overdue = $loan->installments->where('status', '!=', 'approved')->filter(fn($i) => $i->due_date->isPast() && !$i->due_date->isToday())->count();
                    @endphp

                    <a href="{{ route('client.loans.history.show', $loan) }}"
                        data-code="{{ strtoupper($loan->code) }}" data-status="{{ $loan->status }}"
                        class="loan-card group block py-4 px-4 sm:py-5 sm:px-6 lg:py-6 lg:px-8 border-b border-gray-100 dark:border-gray-900 last:border-b-0 hover:bg-gray-50/50 dark:hover:bg-gray-900/30 active:bg-gray-100 dark:active:bg-gray-800/50 transition-all duration-300">

                        <!-- Row 1: Info principal -->
                        <div class="flex items-center gap-3 sm:gap-4 mb-3">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 lg:w-14 lg:h-14 flex items-center justify-center text-green-500 flex-shrink-0">
                                <svg class="w-6 h-6 sm:w-7 sm:h-7 lg:w-8 lg:h-8" viewBox="0 0 489.038 489.038" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <g><path d="M486,215.481l-293.4-169.3c-4-2.3-10.6-2.3-14.6,0L3.2,147.781c-4,2.3-4,6.1,0,8.5l293.3,169.3c4.1,2.4,10.6,2.3,14.6,0 l174.9-101.6C490,221.681,490.1,217.881,486,215.481z M333.1,286.481c-1.6-1.6-3.6-3-5.9-4.3c-14.9-8.6-39-8.6-53.9,0 c-1.1,0.7-2.1,1.3-3.1,2.1l-197.6-114.2c1.9-0.8,3.7-1.6,5.4-2.6c14.8-8.6,14.7-22.6-0.2-31.2c-1.6-0.9-3.4-1.8-5.2-2.5l81.5-47.3 c1.3,1,2.7,2.1,4.3,3c14.9,8.6,39.1,8.6,53.9,0c1.7-1,3.2-2,4.4-3.1l198.2,114.5c-2.6,0.9-5.1,2-7.3,3.3 c-14.8,8.6-14.7,22.6,0.2,31.2c2.3,1.3,4.9,2.4,7.6,3.4L333.1,286.481z M357,243.281l-12.9,7.5c-2,1.1-5.2,1.2-7.2,0l-22.7-13.1 c-2-1.2-2-3,0-4.2l12.9-7.5c2-1.1,5.2-1.2,7.2,0l22.7,13.1C359,240.281,359,242.181,357,243.281z M175,138.181l-12.9,7.5 c-2,1.1-5.2,1.2-7.2,0l-22.7-13.1c-2-1.2-2-3,0-4.2l12.9-7.5c2-1.1,5.2-1.2,7.2,0l22.7,13.1C177,135.181,177,137.081,175,138.181z M466.2,267.581l22.8,13.2l-183.3,106.5l-6-3.4l-16.9-9.7L0,210.581l22.6-13.1l282.9,163.4L466.2,267.581z M283.2,431.381 L0,267.581l22.6-13.1l283.3,163.8l160-93l22.8,13.2L306,444.581l-5.9-3.4L283.2,431.381z M302.8,152.081 c-32.3-18.6-84.6-18.6-116.7,0c-32,18.6-31.9,48.9,0.4,67.6c32.2,18.6,84.5,18.6,116.6,0C335.2,201.081,335,170.781,302.8,152.081z M235.4,195.581h-22.7v-13.1v-12.7l22.8,0.1v12.7l52,0.1v13.2L235.4,195.581z"/></g>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <span class="text-[9px] font-mono text-white dark:text-black bg-black dark:bg-white px-1.5 py-0.5 rounded">{{ $loan->code }}</span>
                                    <span class="text-[8px] sm:text-[9px] bg-black text-white dark:bg-white dark:text-black font-black uppercase tracking-widest px-1.5 py-0.5 rounded-md flex-shrink-0">
                                        {{ $loan->type === 'cuotas' ? 'Cuotas' : 'Único' }}
                                    </span>
                                </div>
                                <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 dark:text-white truncate">
                                    {{ $loan->currency === 'USD' ? '$' : 'S/' }}{{ number_format($loan->amount, 2) }}
                                </h3>
                                <p class="text-[10px] sm:text-[11px] text-gray-400 dark:text-gray-500">
                                    {{ $loan->created_at->format('d M, Y') }}
                                </p>
                            </div>
                            <div class="flex-shrink-0 text-right">
                                @if($loan->status === 'paid')
                                    <span class="text-[10px] font-black text-blue-500 uppercase tracking-wider">Pagado</span>
                                @elseif($daysLeft !== null)
                                    @if($daysLeft < 0)
                                        <span class="text-[10px] font-black text-red-500">Tienes {{ abs($daysLeft) }} día{{ abs($daysLeft) !== 1 ? 's' : '' }} de atraso</span>
                                    @elseif($daysLeft === 0)
                                        <span class="text-[10px] font-black text-orange-500">Tu próxima cuota vence hoy</span>
                                    @elseif($daysLeft <= 3)
                                        <span class="text-[10px] font-black text-orange-500">Te quedan {{ $daysLeft }} día{{ $daysLeft !== 1 ? 's' : '' }} para pagar</span>
                                    @else
                                        <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400">Te quedan {{ $daysLeft }} día{{ $daysLeft !== 1 ? 's' : '' }} para pagar</span>
                                    @endif
                                @endif
                                <svg class="w-4 h-4 text-gray-300 dark:text-gray-700 group-hover:text-green-500 transition-all ml-auto mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>

                        <!-- Row 2: Progreso -->
                        <div class="mb-3">
                            <div class="flex justify-between items-center mb-1.5">
                                <p class="text-[8px] sm:text-[9px] text-gray-400 dark:text-gray-600 uppercase font-bold tracking-widest">Progreso</p>
                                <p class="text-[10px] sm:text-xs font-bold text-gray-600 dark:text-gray-400">{{ $paid_h }}/{{ $total_h }} cuotas</p>
                            </div>
                            <div class="h-1.5 sm:h-2 w-full bg-gray-100 dark:bg-gray-900 rounded-full overflow-hidden">
                                <div class="h-full bg-green-500 rounded-full transition-all duration-1000" style="width: {{ $percent_inst }}%"></div>
                            </div>
                        </div>

                        <!-- Row 3: Detalles inline -->
                        <div class="flex items-center gap-4 sm:gap-6 text-[10px] sm:text-xs">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-gray-400 dark:text-gray-600 uppercase font-bold">Frec.</span>
                                <span class="text-gray-700 dark:text-gray-300 capitalize font-medium">{{ $loan->payment_frequency ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                <span class="text-gray-400 dark:text-gray-600 uppercase font-bold">Interés</span>
                                <span class="text-gray-700 dark:text-gray-300 font-medium">{{ number_format($loan->interest_rate, 2) }}%</span>
                            </div>
                        </div>

                        @if($pendingReview > 0 || $rejected > 0 || $overdue > 0)
                        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 mt-3">
                            @if($overdue > 0)
                                <span class="inline-flex items-center gap-1 text-[8px] sm:text-[9px] font-black uppercase tracking-wider bg-red-50 dark:bg-red-950/30 text-red-600 dark:text-red-400 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-md border border-red-200 dark:border-red-900/40">
                                    <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $overdue }} atrasada{{ $overdue > 1 ? 's' : '' }}
                                </span>
                            @endif
                            @if($pendingReview > 0)
                                <span class="inline-flex items-center gap-1 text-[8px] sm:text-[9px] font-black uppercase tracking-wider bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-md border border-amber-200 dark:border-amber-900/40">
                                    <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $pendingReview }} en revisión
                                </span>
                            @endif
                            @if($rejected > 0)
                                <span class="inline-flex items-center gap-1 text-[8px] sm:text-[9px] font-black uppercase tracking-wider bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-md border border-gray-200 dark:border-gray-700">
                                    <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $rejected }} rechazada{{ $rejected > 1 ? 's' : '' }}
                                </span>
                            @endif
                        </div>
                        @endif
                    </a>
                @endforeach
            </div>
            @endif
        @endif

    </div>

    <style>.scrollbar-none::-webkit-scrollbar{display:none}.scrollbar-none{-ms-overflow-style:none;scrollbar-width:none}</style>
    <script>
    (function() {
        const searchInput = document.getElementById('search-code');
        const cards = document.querySelectorAll('.loan-card');
        const filterBtns = document.querySelectorAll('.filter-btn');
        const noResults = document.getElementById('no-results');
        let activeFilter = 'all';

        function applyFilters() {
            const query = searchInput.value.trim().toUpperCase();
            let visibleCount = 0;

            cards.forEach(card => {
                const code = card.dataset.code || '';
                const status = card.dataset.status || '';
                const matchesCode = !query || code.includes(query);
                const matchesFilter = activeFilter === 'all' || status === activeFilter;
                const show = matchesCode && matchesFilter;

                card.style.display = show ? '' : 'none';
                if (show) visibleCount++;
            });

            if (noResults) noResults.style.display = visibleCount === 0 ? '' : 'none';
        }

        searchInput.addEventListener('input', applyFilters);

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                activeFilter = this.dataset.filter;
                filterBtns.forEach(b => {
                    b.className = b.className.replace(/bg-black|dark:bg-white|text-white|dark:text-black/g, '').trim();
                    if (!b.className.includes('bg-white')) {
                        b.className += ' bg-white dark:bg-gray-900 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-800';
                    }
                });
                this.className = 'filter-btn px-4 py-2.5 text-xs font-bold uppercase tracking-wider rounded-xl transition-all bg-black dark:bg-white text-white dark:text-black';
                applyFilters();
            });
        });
    })();
    </script>
@endsection