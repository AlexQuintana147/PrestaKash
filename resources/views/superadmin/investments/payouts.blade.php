@extends('superadmin.layout')

@section('title', 'Pagos a Inversores')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=JetBrains+Mono:wght@400;500;600&display=swap');
    .pk-font { font-family: 'DM Sans', sans-serif; }
    .pk-mono { font-family: 'JetBrains Mono', monospace; }
    @keyframes fadeUp { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
    .pk-fade-up { animation: fadeUp .35s ease-out both; }
    .modal-backdrop { background: rgba(0,0,0,.5); backdrop-filter: blur(4px); }
    .pk-modal-enter { animation: modalIn .3s ease-out both; }
    @keyframes modalIn { from { opacity:0; transform:scale(.95) translateY(10px); } to { opacity:1; transform:scale(1) translateY(0); } }
    .inv-page { display: none; }
    .inv-page.active { display: block; }
    .opp-page { display: none; }
    .opp-page.active { display: block; }
    @media (max-width: 639px) {
        .pk-modal-wrap { position: fixed; inset: 0; padding: 0; }
        .pk-modal-wrap .pk-modal-enter { max-width: 100%; max-height: 100vh; border-radius: 0; height: 100vh; }
        .pk-schedule-card { display: block; }
        .pk-schedule-table { display: none; }
    }
    @media (min-width: 640px) {
        .pk-schedule-card { display: none; }
        .pk-schedule-table { display: block; }
    }
</style>

<div class="min-h-screen px-3 sm:px-5 md:px-8 lg:px-10 py-5 sm:py-8 max-w-7xl mx-auto pk-font">

    {{-- HEADER --}}
    <div class="flex items-start justify-between mb-5 sm:mb-8 pk-fade-up">
        <div>
            <a href="{{ route('superadmin.investments.index') }}" class="inline-flex items-center gap-1.5 text-[11px] font-medium text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors mb-2 sm:mb-3 group">
                <svg class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Inversiones
            </a>
            <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-none">Pagos a inversores</h1>
            <p class="text-[10px] sm:text-[11px] text-gray-400 mt-1">Inversiones cerradas · Cronograma de retornos</p>
        </div>
        <p class="hidden md:block text-[11px] font-medium text-gray-400 pk-mono mt-1">{{ now()->format('d.m.Y') }}</p>
    </div>

    @if(session('success'))
        <div class="mb-6 flex items-center gap-2.5 text-xs font-medium text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-500/8 p-3.5 rounded-xl border border-emerald-200 dark:border-emerald-500/20 pk-fade-up">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if($opportunitiesData->isEmpty())
        <div class="text-center py-20 pk-fade-up">
            <div class="w-16 h-16 mx-auto mb-5 rounded-2xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center">
                <svg class="w-7 h-7 text-gray-300 dark:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <p class="text-sm font-medium text-gray-400">No hay inversiones cerradas con inversores</p>
            <p class="text-[11px] text-gray-300 dark:text-gray-600 mt-1">Aparecerán cuando se cierre una oportunidad con inversores confirmados</p>
        </div>
    @else

        {{-- GLOBAL STATS --}}
        @php
            $totalInvestors = $opportunitiesData->sum(fn($o) => $o->investors->count());
            $totalOverdue = $opportunitiesData->sum('overdue_count');
            $totalReceived = $opportunitiesData->sum(fn($o) => $o->investors->sum('total_received'));
            $firstOpp = $opportunitiesData->first();
            $symbol = $firstOpp ? ($firstOpp->opportunity->currency === 'USD' ? '$' : 'S/') : 'S/';
            $oppChunks = $opportunitiesData->chunk(6);
            $totalOppPages = $oppChunks->count();
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 sm:gap-2.5 mb-5 sm:mb-8 pk-fade-up" style="animation-delay:60ms">
            <div class="px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl bg-white dark:bg-[#0c0c0f] border border-gray-100 dark:border-gray-850">
                <p class="text-[8px] sm:text-[9px] uppercase tracking-widest font-semibold text-gray-400">Inversiones</p>
                <p class="text-base sm:text-lg font-bold text-gray-900 dark:text-white pk-mono mt-0.5">{{ $opportunitiesData->count() }}</p>
            </div>
            <div class="px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl bg-white dark:bg-[#0c0c0f] border border-gray-100 dark:border-gray-850">
                <p class="text-[8px] sm:text-[9px] uppercase tracking-widest font-semibold text-gray-400">Inversores</p>
                <p class="text-base sm:text-lg font-bold text-indigo-600 dark:text-indigo-400 pk-mono mt-0.5">{{ $totalInvestors }}</p>
            </div>
            <div class="px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl {{ $totalOverdue > 0 ? 'bg-red-50 dark:bg-red-500/5 border border-red-100 dark:border-red-500/15' : 'bg-white dark:bg-[#0c0c0f] border border-gray-100 dark:border-gray-850' }}">
                <p class="text-[8px] sm:text-[9px] uppercase tracking-widest font-semibold {{ $totalOverdue > 0 ? 'text-red-500' : 'text-gray-400' }}">Cuotas vencidas</p>
                <p class="text-base sm:text-lg font-bold {{ $totalOverdue > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }} pk-mono mt-0.5">{{ $totalOverdue }}</p>
            </div>
            <div class="px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl bg-white dark:bg-[#0c0c0f] border border-gray-100 dark:border-gray-850">
                <p class="text-[8px] sm:text-[9px] uppercase tracking-widest font-semibold text-gray-400">Total pagado</p>
                <p class="text-base sm:text-lg font-bold text-emerald-600 dark:text-emerald-400 pk-mono mt-0.5">{{ $symbol }}{{ number_format($totalReceived, 2) }}</p>
            </div>
        </div>

        {{-- OPPORTUNITY PAGES --}}
        @foreach($oppChunks as $pageIndex => $pageOpportunities)
            <div class="opp-page {{ $pageIndex === 0 ? 'active' : '' }}" data-opp-page="{{ $pageIndex }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-5">
                    @foreach($pageOpportunities as $oppData)
                        @php
                            $opportunity = $oppData->opportunity;
                            $curSymbol = $opportunity->currency === 'USD' ? '$' : 'S/';
                            $investorChunks = $oppData->investors->chunk(5);
                            $totalInvPages = $investorChunks->count();
                            $oppUid = 'opp_' . $opportunity->id;
                        @endphp

                        <section class="pk-fade-up" style="animation-delay: {{ min($loop->index * 60 + 80, 400) }}ms">
                            <div class="bg-white dark:bg-[#0c0c0f] border border-gray-100 dark:border-gray-850 rounded-2xl overflow-hidden h-full flex flex-col">
                                {{-- OPPORTUNITY HEADER --}}
                                <div class="px-3 sm:px-4 py-2.5 sm:py-3 flex items-center justify-between border-b border-gray-100 dark:border-gray-850">
                                    <div class="flex items-center gap-2 sm:gap-2.5 min-w-0">
                                        <div class="shrink-0 w-7 h-7 sm:w-8 sm:h-8 rounded-lg {{ $oppData->overdue_count > 0 ? 'bg-red-50 dark:bg-red-500/10 ring-1 ring-inset ring-red-200 dark:ring-red-500/15' : 'bg-indigo-50 dark:bg-indigo-500/10 ring-1 ring-inset ring-indigo-100 dark:ring-indigo-500/15' }} flex items-center justify-center">
                                            @if($oppData->overdue_count > 0)
                                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @else
                                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-1 sm:gap-1.5">
                                                <h2 class="text-[11px] sm:text-[12px] font-bold text-gray-900 dark:text-white tracking-tight truncate">{{ $opportunity->title }}</h2>
                                                <span class="shrink-0 text-[7px] sm:text-[8px] font-mono text-gray-400 bg-gray-100 dark:bg-gray-900 px-1 py-0.5 rounded">{{ $opportunity->code }}</span>
                                            </div>
                                            <div class="flex items-center gap-1 sm:gap-1.5 mt-0.5 text-[8px] sm:text-[9px] text-gray-400 flex-wrap">
                                                <span>{{ $opportunity->monthly_return_rate }}%</span>
                                                <span class="text-gray-200 dark:text-gray-700">·</span>
                                                <span>{{ $opportunity->term_months }}m</span>
                                                <span class="text-gray-200 dark:text-gray-700">·</span>
                                                <span>{{ $opportunity->closed_at->format('d/m/y') }}</span>
                                                @if($oppData->overdue_count > 0)
                                                    <span class="text-gray-200 dark:text-gray-700">·</span>
                                                    <span class="text-red-500 font-bold">{{ $oppData->overdue_count }} venc.</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <span class="shrink-0 text-[7px] sm:text-[8px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded {{ $opportunity->currency === 'USD' ? 'bg-blue-50 dark:bg-blue-500/8 text-blue-600 dark:text-blue-400' : 'bg-emerald-50 dark:bg-emerald-500/8 text-emerald-600 dark:text-emerald-400' }}">{{ $opportunity->currency }}</span>
                                </div>

                                {{-- INVESTOR LIST (paginated) --}}
                                <div class="flex-1">
                                    @foreach($investorChunks as $invPageIndex => $invPageInvestors)
                                        <div class="inv-page {{ $invPageIndex === 0 ? 'active' : '' }}" data-opp-uid="{{ $oppUid }}" data-inv-page="{{ $invPageIndex }}">
                                            <div class="divide-y divide-gray-50 dark:divide-gray-900/60">
                                                @foreach($invPageInvestors as $invData)
                                                    @php
                                                        $investor = $invData->investor;
                                                        $selfie = $investor->documents?->selfie;
                                                        $photo = $selfie ? Storage::url($selfie) : ($investor->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($investor->name) . '&background=e5e7eb&color=6b7280');
                                                        $uid = 'inv_' . $opportunity->id . '_' . $investor->id;
                                                        $progressPct = $invData->total_expected_return > 0 ? round($invData->total_received / $invData->total_expected_return * 100) : 0;
                                                    @endphp

                                                    <div class="hover:bg-gray-50/50 dark:hover:bg-gray-900/20 transition-colors">
                                                        <button onclick="openInvestorModal('{{ $uid }}')" class="w-full text-left px-3 sm:px-4 py-2.5 sm:py-3 flex items-center justify-between cursor-pointer group focus:outline-none">
                                                            <div class="flex items-center gap-2 sm:gap-2.5 min-w-0">
                                                                <img src="{{ $photo }}" alt="{{ $investor->name }}" class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg object-cover ring-1 ring-inset ring-gray-100 dark:ring-gray-800 shrink-0">
                                                                <div class="min-w-0">
                                                                    <div class="flex items-center gap-1 sm:gap-1.5">
                                                                        <h3 class="text-[11px] sm:text-[12px] font-semibold text-gray-900 dark:text-white truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $investor->name }}</h3>
                                                                        @if($invData->overdue_count > 0)
                                                                            <span class="shrink-0 text-[6px] sm:text-[7px] font-bold uppercase tracking-wider px-1 py-px rounded bg-red-100 dark:bg-red-500/15 text-red-600 dark:text-red-400">{{ $invData->overdue_count }}v</span>
                                                                        @endif
                                                                    </div>
                                                                    <p class="text-[8px] sm:text-[9px] text-gray-400 truncate">{{ $investor->email }}</p>
                                                                </div>
                                                            </div>

                                                            <div class="flex items-center gap-2 sm:gap-3">
                                                                <div class="hidden sm:flex items-center gap-3">
                                                                    <div class="text-right">
                                                                        <p class="text-[7px] uppercase tracking-widest font-semibold text-gray-400">Invertido</p>
                                                                        <p class="text-[11px] font-bold text-gray-900 dark:text-white pk-mono">{{ $curSymbol }}{{ number_format($invData->amount, 2) }}</p>
                                                                    </div>
                                                                    <div class="text-right">
                                                                        <p class="text-[7px] uppercase tracking-widest font-semibold text-gray-400">Pagado</p>
                                                                        <p class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 pk-mono">{{ $curSymbol }}{{ number_format($invData->total_received, 2) }}</p>
                                                                    </div>
                                                                    <div class="text-right">
                                                                        <p class="text-[7px] uppercase tracking-widest font-semibold text-gray-400">Progreso</p>
                                                                        <p class="text-[11px] font-bold {{ $progressPct >= 100 ? 'text-emerald-600' : 'text-gray-900 dark:text-white' }} pk-mono">{{ $progressPct }}%</p>
                                                                    </div>
                                                                </div>
                                                                <span class="text-gray-300 dark:text-gray-600 group-hover:text-indigo-500 transition-colors">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                                </span>
                                                            </div>
                                                        </button>

                                                        {{-- MOBILE SUMMARY --}}
                                                        <div class="sm:hidden px-3 pb-2 -mt-0.5 grid grid-cols-3 gap-1.5">
                                                            <div>
                                                                <p class="text-[6px] uppercase tracking-widest font-semibold text-gray-400">Invertido</p>
                                                                <p class="text-[9px] font-bold text-gray-900 dark:text-white pk-mono">{{ $curSymbol }}{{ number_format($invData->amount, 0) }}</p>
                                                            </div>
                                                            <div>
                                                                <p class="text-[6px] uppercase tracking-widest font-semibold text-gray-400">Pagado</p>
                                                                <p class="text-[9px] font-bold text-emerald-600 dark:text-emerald-400 pk-mono">{{ $curSymbol }}{{ number_format($invData->total_received, 0) }}</p>
                                                            </div>
                                                            <div>
                                                                <p class="text-[6px] uppercase tracking-widest font-semibold text-gray-400">Progreso</p>
                                                                <p class="text-[9px] font-bold text-gray-900 dark:text-white pk-mono">{{ $progressPct }}%</p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- HIDDEN MODAL DATA --}}
                                                    <div id="modal_data_{{ $uid }}" class="hidden"
                                                        data-name="{{ $investor->name }}"
                                                        data-email="{{ $investor->email }}"
                                                        data-photo="{{ $photo }}"
                                                        data-amount="{{ $curSymbol . number_format($invData->amount, 2) }}"
                                                        data-received="{{ $curSymbol . number_format($invData->total_received, 2) }}"
                                                        data-expected="{{ $curSymbol . number_format($invData->total_expected_return, 2) }}"
                                                        data-overdue="{{ $invData->overdue_count }}"
                                                        data-pending="{{ $invData->pending_count }}"
                                                        data-amort="{{ $invData->amortization_type === 'frances' ? 'Francés' : 'Americano' }}"
                                                        data-rate="{{ $opportunity->monthly_return_rate }}%"
                                                        data-term="{{ $opportunity->term_months }}"
                                                        data-currency="{{ $curSymbol }}"
                                                        data-code="{{ $opportunity->code }}"
                                                    ></div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- INVESTOR PAGINATION --}}
                                @if($totalInvPages > 1)
                                    <div class="px-3 sm:px-4 py-2 border-t border-gray-100 dark:border-gray-850 flex items-center justify-between bg-gray-50/30 dark:bg-gray-900/10">
                                        <span class="text-[8px] sm:text-[9px] text-gray-400">{{ $oppData->investors->count() }} inversor{{ $oppData->investors->count() !== 1 ? 'es' : '' }}</span>
                                        <div class="flex items-center gap-1">
                                            @for($p = 0; $p < $totalInvPages; $p++)
                                                <button onclick="switchInvPage('{{ $oppUid }}', {{ $p }})" class="inv-page-btn w-6 h-6 rounded-md text-[9px] font-bold transition-colors {{ $p === 0 ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-indigo-600' }}" data-opp-uid="{{ $oppUid }}" data-inv-page-btn="{{ $p }}">{{ $p + 1 }}</button>
                                            @endfor
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </section>
                    @endforeach
                </div>
            </div>
        @endforeach

        {{-- OPPORTUNITY PAGINATION --}}
        @if($totalOppPages > 1)
            <div class="mt-5 sm:mt-8 flex items-center justify-center gap-1.5 sm:gap-2 pk-fade-up overflow-x-auto px-2">
                <button onclick="switchOppPage({{ 0 }})" class="opp-page-btn-first shrink-0 px-2.5 sm:px-3 py-2 rounded-lg text-[10px] font-bold bg-gray-100 dark:bg-gray-800 text-gray-500 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-indigo-600 transition-colors">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7"/></svg>
                </button>
                @for($p = 0; $p < $totalOppPages; $p++)
                    <button onclick="switchOppPage({{ $p }})" class="opp-page-btn shrink-0 px-2.5 sm:px-3 py-2 rounded-lg text-[10px] font-bold transition-colors {{ $p === 0 ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-indigo-600' }}" data-opp-page-btn="{{ $p }}">{{ $p + 1 }}</button>
                @endfor
                <button onclick="switchOppPage({{ $totalOppPages - 1 }})" class="opp-page-btn-last shrink-0 px-2.5 sm:px-3 py-2 rounded-lg text-[10px] font-bold bg-gray-100 dark:bg-gray-800 text-gray-500 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-indigo-600 transition-colors">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7"/></svg>
                </button>
            </div>
        @endif
    @endif
</div>

{{-- INVESTOR DETAIL MODAL --}}
<div id="investorModal" class="fixed inset-0 z-50 hidden">
    <div class="modal-backdrop absolute inset-0" onclick="closeInvestorModal()"></div>
    <div class="pk-modal-wrap relative flex items-start justify-center min-h-screen p-0 sm:p-4 sm:pt-8">
        <div class="pk-modal-enter bg-white dark:bg-gray-950 sm:rounded-2xl rounded-none border-0 sm:border sm:border-gray-200 dark:sm:border-gray-800 w-full sm:max-w-4xl h-screen sm:h-auto sm:max-h-[90vh] overflow-hidden shadow-none sm:shadow-2xl flex flex-col">
            <div class="shrink-0 bg-white dark:bg-gray-950 border-b border-gray-100 dark:border-gray-800 px-4 sm:px-5 py-3 sm:py-4 flex items-center justify-between">
                <div class="flex items-center gap-2.5 sm:gap-3">
                    <img id="modal_photo" src="" alt="" class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl object-cover ring-1 ring-inset ring-gray-100 dark:ring-gray-800">
                    <div>
                        <h3 id="modal_name" class="text-[13px] sm:text-sm font-bold text-gray-900 dark:text-white"></h3>
                        <p id="modal_email" class="text-[9px] sm:text-[10px] text-gray-400"></p>
                    </div>
                </div>
                <button onclick="closeInvestorModal()" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="overflow-y-auto sm:max-h-[calc(90vh-80px)] flex-1 min-h-0">
                {{-- STATS GRID --}}
                <div class="px-4 sm:px-5 py-3 sm:py-4 grid grid-cols-2 sm:grid-cols-5 gap-2.5 sm:gap-3 bg-gray-50/50 dark:bg-gray-900/20 border-b border-gray-100 dark:border-gray-800">
                    <div>
                        <p class="text-[7px] sm:text-[8px] uppercase tracking-widest font-semibold text-gray-400">Invertido</p>
                        <p id="modal_amount" class="text-[13px] sm:text-sm font-bold text-gray-900 dark:text-white pk-mono"></p>
                    </div>
                    <div>
                        <p class="text-[7px] sm:text-[8px] uppercase tracking-widest font-semibold text-gray-400">Pagado</p>
                        <p id="modal_received" class="text-[13px] sm:text-sm font-bold text-emerald-600 dark:text-emerald-400 pk-mono"></p>
                    </div>
                    <div>
                        <p class="text-[7px] sm:text-[8px] uppercase tracking-widest font-semibold text-gray-400">Retorno total</p>
                        <p id="modal_expected" class="text-[13px] sm:text-sm font-bold text-indigo-600 dark:text-indigo-400 pk-mono"></p>
                    </div>
                    <div>
                        <p class="text-[7px] sm:text-[8px] uppercase tracking-widest font-semibold text-gray-400">Sistema</p>
                        <p id="modal_amort" class="text-[13px] sm:text-sm font-bold text-gray-900 dark:text-white"></p>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <p class="text-[7px] sm:text-[8px] uppercase tracking-widest font-semibold text-gray-400">Tasa / Plazo</p>
                        <p class="text-[13px] sm:text-sm font-bold text-gray-900 dark:text-white"><span id="modal_rate"></span> · <span id="modal_term"></span> meses</p>
                    </div>
                </div>

                {{-- PROGRESS BAR --}}
                <div class="px-4 sm:px-5 py-3 border-b border-gray-100 dark:border-gray-800">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[8px] sm:text-[9px] uppercase tracking-widest font-semibold text-gray-400">Progreso de pagos</span>
                        <span id="modal_progress_text" class="text-[10px] sm:text-[11px] font-bold pk-mono text-gray-900 dark:text-white"></span>
                    </div>
                    <div class="w-full h-2 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                        <div id="modal_progress_bar" class="h-full bg-emerald-500 rounded-full transition-all duration-500" style="width:0%"></div>
                    </div>
                </div>

                {{-- OVERDUE ALERT --}}
                <div id="modal_overdue_alert" class="hidden mx-4 sm:mx-5 mt-3 sm:mt-4 p-2.5 sm:p-3 bg-red-50 dark:bg-red-500/8 border border-red-200 dark:border-red-500/15 rounded-xl">
                    <div class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        <p class="text-[10px] sm:text-[11px] font-semibold text-red-600 dark:text-red-400"><span id="modal_overdue_count"></span> cuota(s) vencida(s) sin pago</p>
                    </div>
                </div>

                {{-- SCHEDULE --}}
                <div class="px-4 sm:px-5 py-3 sm:py-4">
                    <h4 class="text-[10px] sm:text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-3">Cronograma de pagos</h4>
                    <div id="modal_schedule_container"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- PAY MODAL --}}
<div id="payModal" class="fixed inset-0 z-[60] hidden">
    <div class="modal-backdrop absolute inset-0" onclick="closePayModal()"></div>
    <div class="relative flex items-start justify-center min-h-screen p-0 sm:p-4 sm:pt-8">
        <div class="pk-modal-enter bg-white dark:bg-gray-950 sm:rounded-2xl rounded-none border-0 sm:border sm:border-gray-200 dark:sm:border-gray-800 w-full sm:max-w-lg h-screen sm:h-auto sm:max-h-[90vh] overflow-y-auto shadow-none sm:shadow-2xl">
            <div class="shrink-0 bg-white dark:bg-gray-950 border-b border-gray-100 dark:border-gray-800 px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 ring-1 ring-inset ring-indigo-100 dark:ring-indigo-500/15 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h3 class="text-[12px] font-bold text-gray-900 dark:text-white">Pagar cuota #<span id="pay_month_display"></span></h3>
                </div>
                <button onclick="closePayModal()" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="pay_form" method="POST" enctype="multipart/form-data" class="p-4 space-y-4">
                @csrf
                <input type="hidden" id="pay_opportunity_id" name="opportunity_id">
                <input type="hidden" id="pay_investor_id" name="investor_id">
                <input type="hidden" id="pay_month" name="month_number">

                {{-- AMOUNT INFO --}}
                <div class="p-3 rounded-xl bg-indigo-50 dark:bg-indigo-500/5 border border-indigo-100 dark:border-indigo-500/15">
                    <div class="flex items-center justify-between mb-1">
                        <span id="pay_interest_label" class="text-[9px] uppercase tracking-widest font-semibold text-indigo-500">Interés de la cuota:</span>
                        <span id="pay_interest_display" class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 pk-mono"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[9px] uppercase tracking-widest font-semibold text-gray-500">Total cuota:</span>
                        <span id="pay_total_display" class="text-[13px] font-bold text-gray-900 dark:text-white pk-mono"></span>
                    </div>
                </div>

                {{-- PAYMENT TYPE --}}
                <div id="pay_interest_only_wrap" class="hidden">
                    <label class="text-[9px] uppercase tracking-widest font-semibold text-gray-400 block mb-2">Tipo de pago</label>
                    <div class="flex gap-2">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="payment_type" value="full" id="pay_type_full" checked class="peer hidden" onchange="togglePaymentType()">
                            <div class="p-2.5 rounded-xl border-2 border-gray-200 dark:border-gray-800 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-500/5 transition-all text-center">
                                <p class="text-[10px] font-bold text-gray-900 dark:text-white">Cuota completa</p>
                                <p class="text-[8px] text-gray-400">Interés + Capital</p>
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="payment_type" value="interest_only" class="peer hidden" onchange="togglePaymentType()">
                            <div class="p-2.5 rounded-xl border-2 border-gray-200 dark:border-gray-800 peer-checked:border-amber-500 peer-checked:bg-amber-50 dark:peer-checked:bg-amber-500/5 transition-all text-center">
                                <p class="text-[10px] font-bold text-gray-900 dark:text-white">Solo intereses</p>
                                <p class="text-[8px] text-gray-400">Cuota se posterga 1 mes</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- PAYMENT METHOD --}}
                <div>
                    <label class="text-[9px] uppercase tracking-widest font-semibold text-gray-400 block mb-2">Método de pago</label>
                    <div class="flex gap-2">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="payment_method" value="transfer" checked class="peer hidden" onchange="togglePaymentMethod()">
                            <div class="p-2.5 rounded-xl border-2 border-gray-200 dark:border-gray-800 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-500/5 transition-all text-center">
                                <p class="text-[10px] font-bold text-gray-900 dark:text-white">Transferencia</p>
                                <p class="text-[8px] text-gray-400">Depósito bancario</p>
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="payment_method" value="cash" class="peer hidden" onchange="togglePaymentMethod()">
                            <div class="p-2.5 rounded-xl border-2 border-gray-200 dark:border-gray-800 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-500/5 transition-all text-center">
                                <p class="text-[10px] font-bold text-gray-900 dark:text-white">Efectivo</p>
                                <p class="text-[8px] text-gray-400">Pago en mano</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- BANK ACCOUNTS --}}
                <div id="pay_bank_section">
                    <label class="text-[9px] uppercase tracking-widest font-semibold text-gray-400 block mb-2">Cuentas bancarias del inversor</label>
                    <div id="pay_bank_accounts" class="space-y-2"></div>
                </div>

                {{-- VOUCHER --}}
                <div id="pay_voucher_section">
                    <label class="text-[9px] uppercase tracking-widest font-semibold text-gray-400 block mb-2">Comprobante de pago</label>
                    <div class="relative">
                        <input type="file" id="pay_voucher" name="voucher" accept="image/*" onchange="previewVoucher(event)" class="block w-full text-[10px] text-gray-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:uppercase file:tracking-wider file:bg-indigo-50 file:text-indigo-600 dark:file:bg-indigo-500/10 dark:file:text-indigo-400 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-500/15 file:transition-colors file:cursor-pointer">
                    </div>
                    <div id="pay_voucher_preview" class="hidden mt-2">
                        <img id="pay_voucher_preview_img" src="" alt="Preview" class="w-full max-h-48 object-contain rounded-xl border border-gray-100 dark:border-gray-800">
                    </div>
                    <p class="text-[8px] text-gray-400 mt-1">La imagen se convertirá a WebP automáticamente. Máx. 5MB.</p>
                </div>

                {{-- OPERATION NUMBER --}}
                <div>
                    <label class="text-[9px] uppercase tracking-widest font-semibold text-gray-400 block mb-2">Número de operación <span class="text-gray-300">(opcional)</span></label>
                    <input type="text" id="pay_operation_number" name="operation_number" maxlength="50" placeholder="Ej: OP-123456" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-[11px] text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition-colors pk-mono">
                </div>

                {{-- SUBMIT --}}
                <button type="submit" id="pay_submit_btn" disabled class="w-full py-3 rounded-xl bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-500 text-[11px] font-bold uppercase tracking-widest transition-colors cursor-not-allowed">
                    Registrar pago
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function switchOppPage(pageIndex) {
    document.querySelectorAll('.opp-page').forEach(el => el.classList.remove('active'));
    const target = document.querySelector(`.opp-page[data-opp-page="${pageIndex}"]`);
    if (target) target.classList.add('active');

    document.querySelectorAll('.opp-page-btn').forEach(btn => {
        btn.classList.remove('bg-indigo-600', 'text-white');
        btn.classList.add('bg-gray-100', 'dark:bg-gray-800', 'text-gray-500');
    });
    const activeBtn = document.querySelector(`.opp-page-btn[data-opp-page-btn="${pageIndex}"]`);
    if (activeBtn) {
        activeBtn.classList.remove('bg-gray-100', 'dark:bg-gray-800', 'text-gray-500');
        activeBtn.classList.add('bg-indigo-600', 'text-white');
    }
}

function switchInvPage(oppUid, pageIndex) {
    document.querySelectorAll(`.inv-page[data-opp-uid="${oppUid}"]`).forEach(el => el.classList.remove('active'));
    const target = document.querySelector(`.inv-page[data-opp-uid="${oppUid}"][data-inv-page="${pageIndex}"]`);
    if (target) target.classList.add('active');

    document.querySelectorAll(`.inv-page-btn[data-opp-uid="${oppUid}"]`).forEach(btn => {
        btn.classList.remove('bg-indigo-600', 'text-white');
        btn.classList.add('bg-gray-100', 'dark:bg-gray-800', 'text-gray-500');
    });
    const activeBtn = document.querySelector(`.inv-page-btn[data-opp-uid="${oppUid}"][data-inv-page-btn="${pageIndex}"]`);
    if (activeBtn) {
        activeBtn.classList.remove('bg-gray-100', 'dark:bg-gray-800', 'text-gray-500');
        activeBtn.classList.add('bg-indigo-600', 'text-white');
    }
}

function openInvestorModal(uid) {
    const el = document.getElementById('modal_data_' + uid);
    if (!el) return;

    const data = el.dataset;

    document.getElementById('modal_photo').src = data.photo;
    document.getElementById('modal_name').textContent = data.name;
    document.getElementById('modal_email').textContent = data.email;
    document.getElementById('modal_amount').textContent = data.amount;
    document.getElementById('modal_received').textContent = data.received;
    document.getElementById('modal_expected').textContent = data.expected;
    document.getElementById('modal_amort').textContent = data.amort;
    document.getElementById('modal_rate').textContent = data.rate;
    document.getElementById('modal_term').textContent = data.term;

    const received = parseFloat(data.received.replace(/[^0-9.-]/g, ''));
    const expected = parseFloat(data.expected.replace(/[^0-9.-]/g, ''));
    const pct = expected > 0 ? Math.round((received / expected) * 100) : 0;
    document.getElementById('modal_progress_bar').style.width = pct + '%';
    document.getElementById('modal_progress_text').textContent = pct + '%';

    const overdueCount = parseInt(data.overdue);
    const overdueAlert = document.getElementById('modal_overdue_alert');
    if (overdueCount > 0) {
        overdueAlert.classList.remove('hidden');
        document.getElementById('modal_overdue_count').textContent = overdueCount;
    } else {
        overdueAlert.classList.add('hidden');
    }

    buildScheduleTable(uid, data);

    document.getElementById('investorModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeInvestorModal() {
    document.getElementById('investorModal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeInvestorModal();
});

let _scheduleData = null;
let _scheduleCurrency = '';
let _schedulePage = 0;
const _schedulePerPage = 10;
let _scheduleInvestor = null;
let _scheduleAmortType = '';
let _scheduleOppId = null;
let _scheduleNextPayable = null;

function renderSchedulePage() {
    if (!_scheduleData) return;
    const container = document.getElementById('modal_schedule_container');
    const schedule = _scheduleData;
    const data = { currency: _scheduleCurrency };
    const totalPages = Math.ceil(schedule.length / _schedulePerPage);
    const start = _schedulePage * _schedulePerPage;
    const pageItems = schedule.slice(start, start + _schedulePerPage);

    let html = '';

    html += '<div class="pk-schedule-table overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-800">';
    html += '<table class="w-full text-[11px]">';
    html += '<thead><tr class="bg-gray-50 dark:bg-gray-900/40">';
    html += '<th class="px-3 py-2 text-left text-[8px] uppercase tracking-widest font-semibold text-gray-400">Cuota</th>';
    html += '<th class="px-3 py-2 text-left text-[8px] uppercase tracking-widest font-semibold text-gray-400">Vencimiento</th>';
    html += '<th class="px-3 py-2 text-right text-[8px] uppercase tracking-widest font-semibold text-gray-400">Interés</th>';
    html += '<th class="px-3 py-2 text-right text-[8px] uppercase tracking-widest font-semibold text-gray-400">Capital</th>';
    html += '<th class="px-3 py-2 text-right text-[8px] uppercase tracking-widest font-semibold text-gray-400">Total</th>';
    html += '<th class="px-3 py-2 text-right text-[8px] uppercase tracking-widest font-semibold text-gray-400">Saldo</th>';
    html += '<th class="px-3 py-2 text-center text-[8px] uppercase tracking-widest font-semibold text-gray-400">Estado</th>';
    html += '<th class="px-3 py-2 text-center text-[8px] uppercase tracking-widest font-semibold text-gray-400">Acción</th>';
    html += '</tr></thead><tbody>';

    pageItems.forEach(item => {
        const isOverdue = item.is_overdue && !item.is_paid;
        const isPendingPayout = item.payout_status === 'pending';
        const isPayable = !item.is_paid && !isPendingPayout && item.month === _scheduleNextPayable;
        const rowClass = isPendingPayout ? 'bg-amber-50/50 dark:bg-amber-500/5' : (isOverdue ? 'bg-red-600 text-white' : (item.is_paid ? 'bg-emerald-50/50 dark:bg-emerald-500/5' : 'hover:bg-gray-50/50 dark:hover:bg-gray-900/20'));
        const textClass = isOverdue ? 'text-white' : 'text-gray-600 dark:text-gray-300';
        const boldClass = isOverdue ? 'text-white font-bold' : 'text-gray-900 dark:text-white font-bold';

        html += `<tr class="${rowClass} border-t border-gray-50 dark:border-gray-900/40 transition-colors">`;
        html += `<td class="px-3 py-2.5 font-bold ${isOverdue ? 'text-white' : 'text-gray-900 dark:text-white'} pk-mono">${item.month}</td>`;
        html += `<td class="px-3 py-2.5 ${isOverdue ? 'text-white/80' : 'text-gray-500 dark:text-gray-400'} pk-mono">${item.date || '—'}</td>`;
        html += `<td class="px-3 py-2.5 text-right ${textClass} pk-mono">${data.currency}${item.interest}</td>`;
        html += `<td class="px-3 py-2.5 text-right ${textClass} pk-mono">${data.currency}${item.principal}</td>`;
        html += `<td class="px-3 py-2.5 text-right ${boldClass} pk-mono">${data.currency}${item.payment}</td>`;
        html += `<td class="px-3 py-2.5 text-right ${isOverdue ? 'text-white/80' : 'text-gray-500 dark:text-gray-400'} pk-mono">${data.currency}${item.balance}</td>`;
        html += '<td class="px-3 py-2.5 text-center">';
        if (item.is_paid) {
            html += '<span class="px-2 py-0.5 rounded-md text-[8px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">Pagada</span>';
        } else if (isPendingPayout) {
            html += '<span class="px-2 py-0.5 rounded-md text-[8px] font-bold uppercase tracking-wider bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">Por confirmar</span>';
        } else if (isOverdue) {
            html += '<span class="px-2 py-0.5 rounded-md text-[8px] font-bold uppercase tracking-wider bg-white/20 text-white">Vencida</span>';
        } else {
            html += '<span class="px-2 py-0.5 rounded-md text-[8px] font-bold uppercase tracking-wider bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-500">Pendiente</span>';
        }
        html += '</td>';
        html += '<td class="px-3 py-2.5 text-center">';
        if (isPayable) {
            html += `<button onclick="openPayModal(${item.month}, '${data.currency}${item.payment}', '${data.currency}${item.interest}')" class="px-2 py-1 rounded-lg text-[8px] font-bold uppercase tracking-wider bg-indigo-600 hover:bg-indigo-700 text-white transition-colors">Pagar</button>`;
        } else if (isPendingPayout) {
            html += `<span class="text-[8px] text-amber-500 font-semibold">Pendiente</span>`;
        }
        html += '</td></tr>';
    });

    html += '</tbody></table></div>';

    html += '<div class="pk-schedule-card space-y-2">';
    pageItems.forEach(item => {
        const isOverdue = item.is_overdue && !item.is_paid;
        const isPendingPayout = item.payout_status === 'pending';
        const isPayable = !item.is_paid && !isPendingPayout && item.month === _scheduleNextPayable;
        const cardBg = isPendingPayout ? 'bg-amber-50/80 dark:bg-amber-500/5' : (isOverdue ? 'bg-red-600' : (item.is_paid ? 'bg-emerald-50/80 dark:bg-emerald-500/5' : 'bg-gray-50/50 dark:bg-gray-900/20'));
        const textColor = isOverdue ? 'text-white' : 'text-gray-900 dark:text-white';
        const subTextColor = isOverdue ? 'text-white/70' : 'text-gray-500 dark:text-gray-400';
        const monoText = isOverdue ? 'text-white' : 'text-gray-900 dark:text-white';

        let badge = '';
        if (item.is_paid) {
            badge = '<span class="px-1.5 py-0.5 rounded text-[7px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400">Pagada</span>';
        } else if (isPendingPayout) {
            badge = '<span class="px-1.5 py-0.5 rounded text-[7px] font-bold uppercase tracking-wider bg-amber-100 text-amber-600 dark:bg-amber-500/15 dark:text-amber-400">Por confirmar</span>';
        } else if (isOverdue) {
            badge = '<span class="px-1.5 py-0.5 rounded text-[7px] font-bold uppercase tracking-wider bg-white/20 text-white">Vencida</span>';
        } else {
            badge = '<span class="px-1.5 py-0.5 rounded text-[7px] font-bold uppercase tracking-wider bg-gray-200 text-gray-500 dark:bg-gray-800 dark:text-gray-500">Pendiente</span>';
        }

        html += `<div class="${cardBg} rounded-xl p-3 border ${isPendingPayout ? 'border-amber-200 dark:border-amber-500/20' : (isOverdue ? 'border-red-500' : 'border-gray-100 dark:border-gray-800')}">`;
        html += `<div class="flex items-center justify-between mb-2">`;
        html += `<div class="flex items-center gap-2">`;
        html += `<span class="text-[11px] font-bold ${monoText} pk-mono">Cuota ${item.month}</span>`;
        html += badge;
        html += `</div>`;
        html += `<span class="text-[9px] ${subTextColor} pk-mono">${item.date || '—'}</span>`;
        html += `</div>`;
        html += `<div class="grid grid-cols-2 gap-x-4 gap-y-1">`;
        html += `<div class="flex justify-between"><span class="text-[8px] ${subTextColor}">Interés</span><span class="text-[10px] font-bold ${monoText} pk-mono">${data.currency}${item.interest}</span></div>`;
        html += `<div class="flex justify-between"><span class="text-[8px] ${subTextColor}">Capital</span><span class="text-[10px] font-bold ${monoText} pk-mono">${data.currency}${item.principal}</span></div>`;
        html += `<div class="flex justify-between"><span class="text-[8px] ${subTextColor}">Total</span><span class="text-[10px] font-bold ${monoText} pk-mono">${data.currency}${item.payment}</span></div>`;
        html += `<div class="flex justify-between"><span class="text-[8px] ${subTextColor}">Saldo</span><span class="text-[10px] font-bold ${subTextColor} pk-mono">${data.currency}${item.balance}</span></div>`;
        html += `</div>`;
        if (isPayable) {
            html += `<button onclick="openPayModal(${item.month}, '${data.currency}${item.payment}', '${data.currency}${item.interest}')" class="mt-2 w-full py-1.5 rounded-lg text-[9px] font-bold uppercase tracking-wider bg-indigo-600 hover:bg-indigo-700 text-white transition-colors">Pagar cuota</button>`;
        } else if (isPendingPayout) {
            html += `<p class="mt-2 text-[9px] text-amber-500 font-semibold text-center">Pago pendiente de confirmación</p>`;
        }
        html += `</div>`;
    });
    html += '</div>';

    if (totalPages > 1) {
        html += '<div class="flex items-center justify-center gap-1.5 mt-4">';
        html += `<button onclick="goSchedulePage(0)" class="shrink-0 px-2 py-1.5 rounded-lg text-[9px] font-bold transition-colors ${_schedulePage === 0 ? 'bg-gray-100 dark:bg-gray-800 text-gray-300 dark:text-gray-600 cursor-default' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-indigo-600'}" ${_schedulePage === 0 ? 'disabled' : ''}>`;
        html += '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7"/></svg></button>';
        html += `<button onclick="goSchedulePage(${_schedulePage - 1})" class="shrink-0 px-2 py-1.5 rounded-lg text-[9px] font-bold transition-colors ${_schedulePage === 0 ? 'bg-gray-100 dark:bg-gray-800 text-gray-300 dark:text-gray-600 cursor-default' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-indigo-600'}" ${_schedulePage === 0 ? 'disabled' : ''}>`;
        html += '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></button>';
        for (let p = 0; p < totalPages; p++) {
            const active = p === _schedulePage;
            html += `<button onclick="goSchedulePage(${p})" class="shrink-0 px-2.5 py-1.5 rounded-lg text-[9px] font-bold transition-colors ${active ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-indigo-600'}">${p + 1}</button>`;
        }
        html += `<button onclick="goSchedulePage(${_schedulePage + 1})" class="shrink-0 px-2 py-1.5 rounded-lg text-[9px] font-bold transition-colors ${_schedulePage === totalPages - 1 ? 'bg-gray-100 dark:bg-gray-800 text-gray-300 dark:text-gray-600 cursor-default' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-indigo-600'}" ${_schedulePage === totalPages - 1 ? 'disabled' : ''}>`;
        html += '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>';
        html += `<button onclick="goSchedulePage(${totalPages - 1})" class="shrink-0 px-2 py-1.5 rounded-lg text-[9px] font-bold transition-colors ${_schedulePage === totalPages - 1 ? 'bg-gray-100 dark:bg-gray-800 text-gray-300 dark:text-gray-600 cursor-default' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-indigo-600'}" ${_schedulePage === totalPages - 1 ? 'disabled' : ''}>`;
        html += '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7"/></svg></button>';
        html += '</div>';
    }

    container.innerHTML = html;
}

function goSchedulePage(page) {
    if (!_scheduleData) return;
    const totalPages = Math.ceil(_scheduleData.length / _schedulePerPage);
    if (page < 0 || page >= totalPages) return;
    _schedulePage = page;
    renderSchedulePage();
    const scrollArea = document.querySelector('#investorModal .overflow-y-auto');
    if (scrollArea) scrollArea.scrollTop = 0;
}

function buildScheduleTable(uid, data) {
    const container = document.getElementById('modal_schedule_container');
    const oppId = uid.replace('inv_', '').split('_')[0];
    const investorId = uid.replace('inv_', '').split('_')[1];

    container.innerHTML = '<p class="text-xs text-gray-400 text-center py-4">Cargando...</p>';
    _schedulePage = 0;

    fetch(`{{ url('/gestion-interna') }}/inversiones/pagos/detalle/${oppId}/${investorId}`)
        .then(r => r.json())
        .then(res => {
            _scheduleData = res.schedule;
            _scheduleCurrency = data.currency;
            _scheduleInvestor = res.investor;
            _scheduleAmortType = res.amortization_type;
            _scheduleOppId = res.opportunity_id;
            _scheduleNextPayable = res.next_payable_month;
            renderSchedulePage();
        })
        .catch(() => {
            container.innerHTML = '<p class="text-xs text-gray-400 text-center py-8">Error al cargar cronograma</p>';
        });
}

function openPayModal(month, totalAmount, interestAmount) {
    const modal = document.getElementById('payModal');
    document.getElementById('pay_month').value = month;
    document.getElementById('pay_month_display').textContent = month;
    document.getElementById('pay_total_display').textContent = totalAmount;
    document.getElementById('pay_interest_display').textContent = interestAmount;

    const interestOnlyWrap = document.getElementById('pay_interest_only_wrap');
    if (_scheduleAmortType === 'frances') {
        interestOnlyWrap.classList.remove('hidden');
    } else {
        interestOnlyWrap.classList.add('hidden');
    }

    const bankContainer = document.getElementById('pay_bank_accounts');
    if (_scheduleInvestor && _scheduleInvestor.bank_accounts && _scheduleInvestor.bank_accounts.length > 0) {
        let bankHtml = '';
        _scheduleInvestor.bank_accounts.forEach(ba => {
            bankHtml += `<div class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-800">`;
            bankHtml += `<div class="flex items-center justify-between mb-1">`;
            bankHtml += `<span class="text-[10px] font-bold text-gray-900 dark:text-white">${ba.bank_name}</span>`;
            bankHtml += `<span class="text-[7px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400">${ba.account_type}</span>`;
            bankHtml += `</div>`;
            bankHtml += `<p class="text-[9px] text-gray-500 dark:text-gray-400">Cuenta: <span class="font-mono font-bold text-gray-900 dark:text-white">${ba.account_number}</span></p>`;
            if (ba.cci) {
                bankHtml += `<p class="text-[9px] text-gray-500 dark:text-gray-400">CCI: <span class="font-mono font-bold text-gray-900 dark:text-white">${ba.cci}</span></p>`;
            }
            bankHtml += `<p class="text-[8px] text-gray-400 mt-1">${ba.currency}</p>`;
            bankHtml += `</div>`;
        });
        bankContainer.innerHTML = bankHtml;
        document.getElementById('pay_bank_section').classList.remove('hidden');
    } else {
        bankContainer.innerHTML = '<p class="text-[10px] text-gray-400">El inversor no tiene cuentas bancarias registradas.</p>';
        document.getElementById('pay_bank_section').classList.remove('hidden');
    }

    document.getElementById('pay_form').action = '{{ route("superadmin.investments.payouts.store") }}';
    document.getElementById('pay_opportunity_id').value = _scheduleOppId;
    document.getElementById('pay_investor_id').value = _scheduleInvestor.id;

    modal.classList.remove('hidden');
}

function closePayModal() {
    document.getElementById('payModal').classList.add('hidden');
    document.getElementById('pay_voucher').value = '';
    document.getElementById('pay_operation_number').value = '';
    document.getElementById('pay_type_full').checked = true;
    document.getElementById('pay_voucher_preview').classList.add('hidden');
    document.getElementById('pay_voucher_preview_img').src = '';
    resetSubmitBtn();
    togglePaymentMethod();
}

function previewVoucher(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('pay_voucher_preview');
    const previewImg = document.getElementById('pay_voucher_preview_img');
    const btn = document.getElementById('pay_submit_btn');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
        btn.disabled = false;
        btn.classList.remove('bg-gray-300', 'dark:bg-gray-700', 'text-gray-500', 'dark:text-gray-500', 'cursor-not-allowed');
        btn.classList.add('bg-indigo-600', 'hover:bg-indigo-700', 'text-white');
    } else {
        preview.classList.add('hidden');
        previewImg.src = '';
        resetSubmitBtn();
    }
}

function resetSubmitBtn() {
    const btn = document.getElementById('pay_submit_btn');
    btn.disabled = true;
    btn.classList.add('bg-gray-300', 'dark:bg-gray-700', 'text-gray-500', 'dark:text-gray-500', 'cursor-not-allowed');
    btn.classList.remove('bg-indigo-600', 'hover:bg-indigo-700', 'text-white');
}

function togglePaymentMethod() {
    const method = document.querySelector('input[name="payment_method"]:checked')?.value;
    const voucherSection = document.getElementById('pay_voucher_section');
    const bankSection = document.getElementById('pay_bank_section');

    if (method === 'cash') {
        voucherSection.classList.remove('hidden');
        bankSection.classList.add('hidden');
    } else {
        voucherSection.classList.remove('hidden');
        bankSection.classList.remove('hidden');
    }
}

function togglePaymentType() {
    const type = document.querySelector('input[name="payment_type"]:checked')?.value;
    const interestDisplay = document.getElementById('pay_interest_display');
    const totalDisplay = document.getElementById('pay_total_display');
    const interestLabel = document.getElementById('pay_interest_label');

    if (type === 'interest_only') {
        interestLabel.textContent = 'Monto a pagar (solo intereses):';
        totalDisplay.textContent = interestDisplay.textContent;
    } else {
        interestLabel.textContent = 'Interés de la cuota:';
    }
}
</script>
@endsection
