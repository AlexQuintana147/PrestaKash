@extends('superadmin.layout')

@section('title', 'Pagos a Inversores')

@section('content')
<div class="px-5 md:px-10 py-8">

    <div class="flex items-end justify-between mb-8">
        <div>
            <p class="text-xs text-gray-600 dark:text-gray-600 uppercase tracking-widest mb-1">Panel Administrativo</p>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Pagos a inversores</h1>
        </div>
        <p class="hidden md:block text-sm text-gray-600 dark:text-gray-400">{{ now()->format('d M Y') }}</p>
    </div>

    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 text-sm text-green-500">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 flex items-center gap-3 text-sm text-red-500">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            {{ $errors->first() }}
        </div>
    @endif

    @if($opportunitiesData->isEmpty())
        <div class="text-center py-20">
            <div class="w-16 h-16 mx-auto mb-5 rounded-2xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center">
                <svg class="w-7 h-7 text-gray-300 dark:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <p class="text-sm font-medium text-gray-400">No hay inversiones cerradas con inversores</p>
            <p class="text-[11px] text-gray-300 dark:text-gray-600 mt-1">Aparecerán cuando se cierre una oportunidad con inversores confirmados</p>
        </div>
    @else

        @php
            $totalInvestors = $opportunitiesData->sum(fn($o) => $o->investors->count());
            $totalOverdue = $opportunitiesData->sum('overdue_count');
            $totalReceived = $opportunitiesData->sum(fn($o) => $o->investors->sum('total_received'));
            $firstOpp = $opportunitiesData->first();
            $symbol = $firstOpp ? ($firstOpp->opportunity->currency === 'USD' ? '$' : 'S/') : 'S/';
            $oppChunks = $opportunitiesData->chunk(6);
            $totalOppPages = $oppChunks->count();
        @endphp

        <div class="flex flex-wrap items-center gap-6 mb-8 pb-6 border-b border-gray-100 dark:border-gray-900">
            <div>
                <p class="text-[9px] text-gray-400 uppercase font-black tracking-widest mb-1">Inversiones</p>
                <p class="text-sm font-black text-gray-900 dark:text-white tabular-nums">{{ $opportunitiesData->count() }}</p>
            </div>
            <div>
                <p class="text-[9px] text-gray-400 uppercase font-black tracking-widest mb-1">Inversores</p>
                <p class="text-sm font-black text-indigo-600 dark:text-indigo-400 tabular-nums">{{ $totalInvestors }}</p>
            </div>
            @if($totalOverdue > 0)
                <div>
                    <p class="text-[9px] text-red-500 uppercase font-black tracking-widest mb-1">Cuotas vencidas</p>
                    <p class="text-sm font-black text-red-600 dark:text-red-400 tabular-nums">{{ $totalOverdue }}</p>
                </div>
            @endif
            <div>
                <p class="text-[9px] text-gray-400 uppercase font-black tracking-widest mb-1">Total pagado</p>
                <p class="text-sm font-black text-emerald-600 dark:text-emerald-400 tabular-nums">{{ $symbol }}{{ number_format($totalReceived, 2) }}</p>
            </div>
        </div>

        @foreach($oppChunks as $pageIndex => $pageOpportunities)
            <div class="opp-page {{ $pageIndex === 0 ? '' : 'hidden' }}" data-opp-page="{{ $pageIndex }}">
                <div class="space-y-6">
                    @foreach($pageOpportunities as $oppData)
                        @php
                            $opportunity = $oppData->opportunity;
                            $curSymbol = $opportunity->currency === 'USD' ? '$' : 'S/';
                            $investorChunks = $oppData->investors->chunk(5);
                            $totalInvPages = $investorChunks->count();
                            $oppUid = 'opp_' . $opportunity->id;
                        @endphp

                        <div class="bg-white dark:bg-gray-950 rounded-xl border border-gray-100 dark:border-gray-900 overflow-hidden">
                            <div class="flex items-center gap-3 px-5 py-4 bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-900">
                                <div class="w-2 h-2 rounded-full {{ $oppData->overdue_count > 0 ? 'bg-red-500 animate-pulse' : 'bg-indigo-500 animate-pulse' }}"></div>
                                <h2 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $opportunity->title }}</h2>
                                <span class="text-[9px] font-mono text-white dark:text-black bg-black dark:bg-white px-1.5 py-0.5 rounded">{{ $opportunity->code }}</span>
                                <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">{{ $opportunity->monthly_return_rate }}% · {{ $opportunity->term_months }}m · {{ $opportunity->closed_at->format('d/m/y') }}</span>
                                @if($oppData->overdue_count > 0)
                                    <span class="text-[9px] text-red-500 font-black">{{ $oppData->overdue_count }} venc.</span>
                                @endif
                                <span class="ml-auto text-[10px] font-bold text-gray-500 dark:text-gray-400">{{ $oppData->investors->count() }} inversor{{ $oppData->investors->count() !== 1 ? 'es' : '' }}</span>
                                <span class="text-[11px] font-black {{ $opportunity->currency === 'USD' ? 'text-blue-600 dark:text-blue-400' : 'text-emerald-600 dark:text-emerald-400' }} tabular-nums">
                                    {{ $curSymbol }}{{ number_format($oppData->investors->sum(fn($i) => $i->amount), 2) }}
                                </span>
                            </div>

                            <div class="divide-y divide-gray-100 dark:divide-gray-900">
                                @foreach($investorChunks as $invPageIndex => $invPageInvestors)
                                    <div class="inv-page {{ $invPageIndex === 0 ? '' : 'hidden' }}" data-opp-uid="{{ $oppUid }}" data-inv-page="{{ $invPageIndex }}">
                                        @foreach($invPageInvestors as $invData)
                                            @php
                                                $investor = $invData->investor;
                                                $selfie = $investor->documents?->selfie;
                                                $photo = $selfie ? Storage::url($selfie) : ($investor->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($investor->name) . '&background=e5e7eb&color=6b7280');
                                                $uid = 'inv_' . $opportunity->id . '_' . $investor->id;
                                                $progressPct = $invData->total_expected_return > 0 ? round($invData->total_received / $invData->total_expected_return * 100) : 0;
                                            @endphp

                                            <div class="py-5 px-5 transition-colors hover:bg-gray-50/50 dark:hover:bg-gray-900/20">
                                                <div class="flex flex-col gap-4">
                                                    <div class="flex items-center gap-4">
                                                        <div class="relative">
                                                            <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-900 flex items-center justify-center overflow-hidden border border-gray-100 dark:border-gray-800">
                                                                <img src="{{ $photo }}" alt="{{ $investor->name }}" class="w-full h-full object-cover">
                                                            </div>
                                                            @if($invData->overdue_count > 0)
                                                                <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 border-2 border-white dark:border-gray-950 rounded-full"></span>
                                                            @endif
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <div class="flex items-center gap-2">
                                                                <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $investor->name }}</h3>
                                                                @if($invData->overdue_count > 0)
                                                                    <span class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-red-100 dark:bg-red-500/15 text-red-600 dark:text-red-400">{{ $invData->overdue_count }}v</span>
                                                                @endif
                                                            </div>
                                                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1">
                                                                <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">{{ $investor->email }}</span>
                                                            </div>
                                                        </div>

                                                        <div class="flex items-center gap-6 flex-shrink-0">
                                                            <div class="text-right hidden sm:block">
                                                                <p class="text-[9px] text-gray-400 uppercase font-black tracking-widest mb-0.5">Invertido</p>
                                                                <p class="text-sm font-black text-gray-900 dark:text-white tabular-nums">{{ $curSymbol }}{{ number_format($invData->amount, 2) }}</p>
                                                            </div>
                                                            <div class="text-right hidden sm:block">
                                                                <p class="text-[9px] text-gray-400 uppercase font-black tracking-widest mb-0.5">Pagado</p>
                                                                <p class="text-sm font-black text-emerald-600 dark:text-emerald-400 tabular-nums">{{ $curSymbol }}{{ number_format($invData->total_received, 2) }}</p>
                                                            </div>
                                                            <div class="text-right hidden sm:block">
                                                                <p class="text-[9px] text-gray-400 uppercase font-black tracking-widest mb-0.5">Progreso</p>
                                                                <p class="text-sm font-black {{ $progressPct >= 100 ? 'text-emerald-600' : 'text-gray-900 dark:text-white' }} tabular-nums">{{ $progressPct }}%</p>
                                                            </div>
                                                            <button onclick="openInvestorModal('{{ $uid }}')" class="text-gray-300 dark:text-gray-600 hover:text-indigo-500 dark:hover:text-indigo-400 transition-colors">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <div class="sm:hidden grid grid-cols-3 gap-3 pl-16">
                                                        <div>
                                                            <p class="text-[9px] text-gray-400 uppercase font-black tracking-widest mb-0.5">Invertido</p>
                                                            <p class="text-xs font-black text-gray-900 dark:text-white tabular-nums">{{ $curSymbol }}{{ number_format($invData->amount, 0) }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-[9px] text-gray-400 uppercase font-black tracking-widest mb-0.5">Pagado</p>
                                                            <p class="text-xs font-black text-emerald-600 dark:text-emerald-400 tabular-nums">{{ $curSymbol }}{{ number_format($invData->total_received, 0) }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-[9px] text-gray-400 uppercase font-black tracking-widest mb-0.5">Progreso</p>
                                                            <p class="text-xs font-black text-gray-900 dark:text-white tabular-nums">{{ $progressPct }}%</p>
                                                        </div>
                                                    </div>

                                                    <div class="flex flex-wrap gap-1.5 pl-16">
                                                        <a href="mailto:{{ $investor->email }}"
                                                           class="inline-flex items-center gap-1.5 bg-blue-500 hover:bg-blue-600 transition-colors px-2.5 py-1 text-white font-bold text-[9px] rounded-md">
                                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>
                                                            Email
                                                        </a>
                                                        @if($investor->phone)
                                                        <a href="https://wa.me/51{{ preg_replace('/\D/', '', $investor->phone) }}" target="_blank"
                                                           class="inline-flex items-center gap-1.5 bg-green-500 hover:bg-green-600 transition-colors px-2.5 py-1 text-white font-bold text-[9px] rounded-md">
                                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                                            WhatsApp
                                                        </a>
                                                        @endif
                                                    </div>
                                                </div>

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
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>

                            @if($totalInvPages > 1)
                                <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-900 flex items-center justify-between bg-gray-50/30 dark:bg-gray-900/10">
                                    <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">{{ $oppData->investors->count() }} inversor{{ $oppData->investors->count() !== 1 ? 'es' : '' }}</span>
                                    <div class="flex items-center gap-1">
                                        @for($p = 0; $p < $totalInvPages; $p++)
                                            <button onclick="switchInvPage('{{ $oppUid }}', {{ $p }})" class="inv-page-btn w-6 h-6 rounded-md text-[9px] font-bold transition-colors {{ $p === 0 ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-indigo-600' }}" data-opp-uid="{{ $oppUid }}" data-inv-page-btn="{{ $p }}">{{ $p + 1 }}</button>
                                        @endfor
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        @if($totalOppPages > 1)
            <div class="mt-8 flex items-center justify-center gap-2">
                <button onclick="switchOppPage(0)" class="opp-page-btn-first shrink-0 px-3 py-2 rounded-lg text-[10px] font-bold bg-gray-100 dark:bg-gray-800 text-gray-500 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-indigo-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7"/></svg>
                </button>
                @for($p = 0; $p < $totalOppPages; $p++)
                    <button onclick="switchOppPage({{ $p }})" class="opp-page-btn shrink-0 px-3 py-2 rounded-lg text-[10px] font-bold transition-colors {{ $p === 0 ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-indigo-600' }}" data-opp-page-btn="{{ $p }}">{{ $p + 1 }}</button>
                @endfor
                <button onclick="switchOppPage({{ $totalOppPages - 1 }})" class="opp-page-btn-last shrink-0 px-3 py-2 rounded-lg text-[10px] font-bold bg-gray-100 dark:bg-gray-800 text-gray-500 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-indigo-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7"/></svg>
                </button>
            </div>
        @endif
    @endif
</div>

<div id="investorModal" class="fixed inset-0 z-50 hidden bg-white/40 dark:bg-black/60 backdrop-blur-md flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-950 w-full max-w-4xl rounded-[2rem] shadow-2xl relative overflow-hidden max-h-[90vh] flex flex-col">
        <div class="shrink-0 bg-white dark:bg-gray-950 border-b border-gray-100 dark:border-gray-900 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-900 flex items-center justify-center overflow-hidden border border-gray-100 dark:border-gray-800">
                    <img id="modal_photo" src="" alt="" class="w-full h-full object-cover">
                </div>
                <div>
                    <h3 id="modal_name" class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight"></h3>
                    <p id="modal_email" class="text-[9px] text-gray-400 font-bold uppercase tracking-widest"></p>
                </div>
            </div>
            <button onclick="closeInvestorModal()" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="overflow-y-auto flex-1 min-h-0">
            <div class="px-6 py-4 flex flex-wrap items-center gap-6 border-b border-gray-100 dark:border-gray-900 bg-gray-50/50 dark:bg-gray-900/20">
                <div>
                    <p class="text-[9px] text-gray-400 uppercase font-black tracking-widest mb-0.5">Invertido</p>
                    <p id="modal_amount" class="text-sm font-black text-gray-900 dark:text-white tabular-nums"></p>
                </div>
                <div>
                    <p class="text-[9px] text-gray-400 uppercase font-black tracking-widest mb-0.5">Pagado</p>
                    <p id="modal_received" class="text-sm font-black text-emerald-600 dark:text-emerald-400 tabular-nums"></p>
                </div>
                <div>
                    <p class="text-[9px] text-gray-400 uppercase font-black tracking-widest mb-0.5">Retorno total</p>
                    <p id="modal_expected" class="text-sm font-black text-indigo-600 dark:text-indigo-400 tabular-nums"></p>
                </div>
                <div>
                    <p class="text-[9px] text-gray-400 uppercase font-black tracking-widest mb-0.5">Sistema</p>
                    <p id="modal_amort" class="text-sm font-black text-gray-900 dark:text-white"></p>
                </div>
                <div>
                    <p class="text-[9px] text-gray-400 uppercase font-black tracking-widest mb-0.5">Tasa / Plazo</p>
                    <p class="text-sm font-black text-gray-900 dark:text-white"><span id="modal_rate"></span> · <span id="modal_term"></span> meses</p>
                </div>
            </div>

            <div class="px-6 py-3 border-b border-gray-100 dark:border-gray-900">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[9px] text-gray-400 uppercase font-black tracking-widest">Progreso de pagos</span>
                    <span id="modal_progress_text" class="text-[11px] font-black text-gray-900 dark:text-white tabular-nums"></span>
                </div>
                <div class="w-full h-2 bg-gray-100 dark:bg-gray-900 rounded-full overflow-hidden">
                    <div id="modal_progress_bar" class="h-full bg-emerald-500 rounded-full transition-all duration-500" style="width:0%"></div>
                </div>
            </div>

            <div id="modal_overdue_alert" class="hidden mx-6 mt-4 p-3 bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-900/30 rounded-xl">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <p class="text-xs font-bold text-red-600 dark:text-red-400"><span id="modal_overdue_count"></span> cuota(s) vencida(s) sin pago</p>
                </div>
            </div>

            <div class="px-6 py-4">
                <h4 class="text-[9px] text-gray-400 uppercase font-black tracking-widest mb-3">Cronograma de pagos</h4>
                <div id="modal_schedule_container"></div>
            </div>
        </div>
    </div>
</div>

<div id="payModal" class="fixed inset-0 z-[60] hidden bg-white/40 dark:bg-black/60 backdrop-blur-md flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-950 w-full max-w-lg rounded-[2rem] shadow-2xl relative overflow-hidden max-h-[90vh] overflow-y-auto">
        <div class="shrink-0 bg-white dark:bg-gray-950 border-b border-gray-100 dark:border-gray-900 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">Pagar cuota #<span id="pay_month_display"></span></h3>
            </div>
            <button onclick="closePayModal()" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="pay_form" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf
            <input type="hidden" id="pay_opportunity_id" name="opportunity_id">
            <input type="hidden" id="pay_investor_id" name="investor_id">
            <input type="hidden" id="pay_month" name="month_number">

            <div class="p-4 rounded-xl bg-indigo-50 dark:bg-indigo-500/5 border border-indigo-100 dark:border-indigo-500/15">
                <div class="flex items-center justify-between mb-1">
                    <span id="pay_interest_label" class="text-[9px] text-indigo-500 uppercase font-black tracking-widest">Interés de la cuota:</span>
                    <span id="pay_interest_display" class="text-xs font-black text-indigo-600 dark:text-indigo-400 tabular-nums"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[9px] text-gray-500 uppercase font-black tracking-widest">Total cuota:</span>
                    <span id="pay_total_display" class="text-sm font-black text-gray-900 dark:text-white tabular-nums"></span>
                </div>
            </div>

            <div id="pay_interest_only_wrap" class="hidden">
                <label class="text-[9px] text-gray-400 uppercase font-black tracking-widest block mb-2">Tipo de pago</label>
                <div class="flex gap-2">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="payment_type" value="full" id="pay_type_full" checked class="peer hidden" onchange="togglePaymentType()">
                        <div class="p-3 rounded-xl border-2 border-gray-200 dark:border-gray-800 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-500/5 transition-all text-center">
                            <p class="text-[10px] font-bold text-gray-900 dark:text-white">Cuota completa</p>
                            <p class="text-[8px] text-gray-400">Interés + Capital</p>
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="payment_type" value="interest_only" class="peer hidden" onchange="togglePaymentType()">
                        <div class="p-3 rounded-xl border-2 border-gray-200 dark:border-gray-800 peer-checked:border-amber-500 peer-checked:bg-amber-50 dark:peer-checked:bg-amber-500/5 transition-all text-center">
                            <p class="text-[10px] font-bold text-gray-900 dark:text-white">Solo intereses</p>
                            <p class="text-[8px] text-gray-400">Cuota se posterga 1 mes</p>
                        </div>
                    </label>
                </div>
            </div>

            <div>
                <label class="text-[9px] text-gray-400 uppercase font-black tracking-widest block mb-2">Método de pago</label>
                <div class="flex gap-2">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="payment_method" value="transfer" checked class="peer hidden" onchange="togglePaymentMethod()">
                        <div class="p-3 rounded-xl border-2 border-gray-200 dark:border-gray-800 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-500/5 transition-all text-center">
                            <p class="text-[10px] font-bold text-gray-900 dark:text-white">Transferencia</p>
                            <p class="text-[8px] text-gray-400">Depósito bancario</p>
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="payment_method" value="cash" class="peer hidden" onchange="togglePaymentMethod()">
                        <div class="p-3 rounded-xl border-2 border-gray-200 dark:border-gray-800 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-500/5 transition-all text-center">
                            <p class="text-[10px] font-bold text-gray-900 dark:text-white">Efectivo</p>
                            <p class="text-[8px] text-gray-400">Pago en mano</p>
                        </div>
                    </label>
                </div>
            </div>

            <div id="pay_bank_section">
                <label class="text-[9px] text-gray-400 uppercase font-black tracking-widest block mb-2">Cuentas bancarias del inversor</label>
                <div id="pay_bank_accounts" class="space-y-2"></div>
            </div>

            <div id="pay_voucher_section">
                <label class="text-[9px] text-gray-400 uppercase font-black tracking-widest block mb-2">Comprobante de pago</label>
                <div class="relative">
                    <input type="file" id="pay_voucher" name="voucher" accept="image/*" onchange="previewVoucher(event)" class="block w-full text-[10px] text-gray-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:uppercase file:tracking-wider file:bg-indigo-50 file:text-indigo-600 dark:file:bg-indigo-500/10 dark:file:text-indigo-400 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-500/15 file:transition-colors file:cursor-pointer">
                </div>
                <div id="pay_voucher_preview" class="hidden mt-2">
                    <img id="pay_voucher_preview_img" src="" alt="Preview" class="w-full max-h-48 object-contain rounded-xl border border-gray-100 dark:border-gray-800">
                </div>
                <p class="text-[8px] text-gray-400 mt-1">La imagen se convertirá a WebP automáticamente. Máx. 5MB.</p>
            </div>

            <div>
                <label class="text-[9px] text-gray-400 uppercase font-black tracking-widest block mb-2">Número de operación <span class="text-gray-300">(opcional)</span></label>
                <input type="text" id="pay_operation_number" name="operation_number" maxlength="50" placeholder="Ej: OP-123456" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-xs text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition-colors tabular-nums">
            </div>

            <div>
                <label class="text-[9px] text-gray-400 uppercase font-black tracking-widest block mb-2">Caja de egreso <span class="text-red-400">*</span></label>
                <select id="pay_cashbox_id" name="cashbox_id" required class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition-colors">
                    <option value="" disabled selected>Selecciona la caja</option>
                    @foreach($cashboxes as $cb)
                        <option value="{{ $cb->id }}" data-currency="{{ $cb->currency }}">{{ $cb->name }} ({{ $cb->currency }})</option>
                    @endforeach
                </select>
                <p id="pay_cashbox_warning" class="hidden text-[8px] text-red-500 mt-1">La moneda de la caja no coincide con la de la oportunidad</p>
            </div>

            <button type="submit" id="pay_submit_btn" disabled class="w-full py-3 rounded-xl bg-gray-300 dark:bg-gray-700 text-gray-500 text-xs font-black uppercase tracking-widest transition-colors cursor-not-allowed">
                Registrar pago
            </button>
        </form>
    </div>
</div>

<script>
function switchOppPage(pageIndex) {
    document.querySelectorAll('.opp-page').forEach(el => el.classList.add('hidden'));
    const target = document.querySelector(`.opp-page[data-opp-page="${pageIndex}"]`);
    if (target) target.classList.remove('hidden');

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
    document.querySelectorAll(`.inv-page[data-opp-uid="${oppUid}"]`).forEach(el => el.classList.add('hidden'));
    const target = document.querySelector(`.inv-page[data-opp-uid="${oppUid}"][data-inv-page="${pageIndex}"]`);
    if (target) target.classList.remove('hidden');

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

    html += '<div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-900">';
    html += '<table class="w-full text-xs">';
    html += '<thead><tr class="bg-gray-50 dark:bg-gray-900/50">';
    html += '<th class="px-3 py-2 text-left text-[9px] text-gray-400 uppercase font-black tracking-widest">Cuota</th>';
    html += '<th class="px-3 py-2 text-left text-[9px] text-gray-400 uppercase font-black tracking-widest">Vencimiento</th>';
    html += '<th class="px-3 py-2 text-right text-[9px] text-gray-400 uppercase font-black tracking-widest">Interés</th>';
    html += '<th class="px-3 py-2 text-right text-[9px] text-gray-400 uppercase font-black tracking-widest">Capital</th>';
    html += '<th class="px-3 py-2 text-right text-[9px] text-gray-400 uppercase font-black tracking-widest">Total</th>';
    html += '<th class="px-3 py-2 text-right text-[9px] text-gray-400 uppercase font-black tracking-widest">Saldo</th>';
    html += '<th class="px-3 py-2 text-center text-[9px] text-gray-400 uppercase font-black tracking-widest">Estado</th>';
    html += '<th class="px-3 py-2 text-center text-[9px] text-gray-400 uppercase font-black tracking-widest">Acción</th>';
    html += '</tr></thead><tbody>';

    pageItems.forEach(item => {
        const isOverdue = item.is_overdue && !item.is_paid;
        const isPendingPayout = item.payout_status === 'pending';
        const isPayable = !item.is_paid && !isPendingPayout && item.month === _scheduleNextPayable;
        const rowClass = isPendingPayout ? 'bg-amber-50/50 dark:bg-amber-500/5' : (isOverdue ? 'bg-red-600 text-white' : (item.is_paid ? 'bg-emerald-50/50 dark:bg-emerald-500/5' : 'hover:bg-gray-50/50 dark:hover:bg-gray-900/20'));
        const textClass = isOverdue ? 'text-white' : 'text-gray-500 dark:text-gray-400';
        const boldClass = isOverdue ? 'text-white font-bold' : 'text-gray-900 dark:text-white font-bold';

        html += `<tr data-month="${item.month}" class="${rowClass} border-t border-gray-100 dark:border-gray-900 transition-colors">`;
        html += `<td class="px-3 py-2.5 font-bold ${isOverdue ? 'text-white' : 'text-gray-900 dark:text-white'} tabular-nums">${item.month}</td>`;
        html += `<td class="px-3 py-2.5 ${isOverdue ? 'text-white/80' : 'text-gray-500 dark:text-gray-400'} tabular-nums">${item.date || '—'}</td>`;
        html += `<td class="px-3 py-2.5 text-right ${textClass} tabular-nums">${data.currency}${item.interest}</td>`;
        html += `<td class="px-3 py-2.5 text-right ${textClass} tabular-nums">${data.currency}${item.principal}</td>`;
        html += `<td class="px-3 py-2.5 text-right ${boldClass} tabular-nums">${data.currency}${item.payment}</td>`;
        html += `<td class="px-3 py-2.5 text-right ${isOverdue ? 'text-white/80' : 'text-gray-500 dark:text-gray-400'} tabular-nums">${data.currency}${item.balance}</td>`;
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

    html += '<div class="sm:hidden space-y-2 mt-3">';
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

        html += `<div data-month="${item.month}" class="${cardBg} rounded-xl p-3 border ${isPendingPayout ? 'border-amber-200 dark:border-amber-500/20' : (isOverdue ? 'border-red-500' : 'border-gray-100 dark:border-gray-900')}">`;
        html += `<div class="flex items-center justify-between mb-2">`;
        html += `<div class="flex items-center gap-2">`;
        html += `<span class="text-[11px] font-bold ${monoText} tabular-nums">Cuota ${item.month}</span>`;
        html += badge;
        html += `</div>`;
        html += `<span class="text-[9px] ${subTextColor} tabular-nums">${item.date || '—'}</span>`;
        html += `</div>`;
        html += `<div class="grid grid-cols-2 gap-x-4 gap-y-1">`;
        html += `<div class="flex justify-between"><span class="text-[8px] ${subTextColor}">Interés</span><span class="text-[10px] font-bold ${monoText} tabular-nums">${data.currency}${item.interest}</span></div>`;
        html += `<div class="flex justify-between"><span class="text-[8px] ${subTextColor}">Capital</span><span class="text-[10px] font-bold ${monoText} tabular-nums">${data.currency}${item.principal}</span></div>`;
        html += `<div class="flex justify-between"><span class="text-[8px] ${subTextColor}">Total</span><span class="text-[10px] font-bold ${monoText} tabular-nums">${data.currency}${item.payment}</span></div>`;
        html += `<div class="flex justify-between"><span class="text-[8px] ${subTextColor}">Saldo</span><span class="text-[10px] font-bold ${subTextColor} tabular-nums">${data.currency}${item.balance}</span></div>`;
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

let _payOriginalTotal = '';
let _payOriginalInterest = '';

function openPayModal(month, totalAmount, interestAmount) {
    const modal = document.getElementById('payModal');
    document.getElementById('pay_month').value = month;
    document.getElementById('pay_month_display').textContent = month;
    document.getElementById('pay_total_display').textContent = totalAmount;
    document.getElementById('pay_interest_display').textContent = interestAmount;
    _payOriginalTotal = totalAmount;
    _payOriginalInterest = interestAmount;

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
            bankHtml += `<div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-900">`;
            bankHtml += `<div class="flex items-center justify-between mb-1">`;
            bankHtml += `<span class="text-[10px] font-bold text-gray-900 dark:text-white">${ba.bank_name}</span>`;
            bankHtml += `<span class="text-[7px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400">${ba.account_type}</span>`;
            bankHtml += `</div>`;
            bankHtml += `<p class="text-[9px] text-gray-500 dark:text-gray-400">Cuenta: <span class="font-bold text-gray-900 dark:text-white tabular-nums">${ba.account_number}</span></p>`;
            if (ba.cci) {
                bankHtml += `<p class="text-[9px] text-gray-500 dark:text-gray-400">CCI: <span class="font-bold text-gray-900 dark:text-white tabular-nums">${ba.cci}</span></p>`;
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

    const cashboxSelect = document.getElementById('pay_cashbox_id');
    cashboxSelect.value = '';
    document.getElementById('pay_cashbox_warning').classList.add('hidden');
    const oppCurrency = (_scheduleCurrency || '').replace('$','USD').replace('S/','PEN');
    Array.from(cashboxSelect.options).forEach(opt => {
        if (!opt.value) { opt.classList.remove('hidden'); return; }
        const cbCurrency = (opt.dataset.currency || '').replace('$','USD').replace('S/','PEN');
        if (cbCurrency === oppCurrency) {
            opt.classList.remove('hidden');
            opt.disabled = false;
        } else {
            opt.classList.add('hidden');
            opt.disabled = true;
        }
    });
    cashboxSelect.onchange = null;

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
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
        btn.classList.remove('bg-gray-300', 'dark:bg-gray-700', 'text-gray-500', 'cursor-not-allowed');
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
    btn.classList.add('bg-gray-300', 'dark:bg-gray-700', 'text-gray-500', 'cursor-not-allowed');
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
    const totalDisplay = document.getElementById('pay_total_display');
    const interestLabel = document.getElementById('pay_interest_label');

    if (type === 'interest_only') {
        interestLabel.textContent = 'Monto a pagar (solo intereses):';
        totalDisplay.textContent = _payOriginalInterest;
    } else {
        interestLabel.textContent = 'Interés de la cuota:';
        totalDisplay.textContent = _payOriginalTotal;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const highlightPayout = params.get('highlight_payout');
    const oppId = params.get('opp_id');
    const invId = params.get('inv_id');
    const highlightMonth = params.get('month');

    if (highlightPayout && oppId && invId) {
        const uid = 'inv_' + oppId + '_' + invId;
        setTimeout(() => {
            openInvestorModal(uid);
            const waitForSchedule = setInterval(() => {
                if (_scheduleData && _scheduleData.length > 0) {
                    clearInterval(waitForSchedule);
                    const targetPage = Math.floor((parseInt(highlightMonth) - 1) / _schedulePerPage);
                    _schedulePage = targetPage;
                    renderSchedulePage();
                    setTimeout(() => {
                        const rows = document.querySelectorAll('#modal_schedule_container [data-month]');
                        rows.forEach(row => {
                            if (parseInt(row.dataset.month) === parseInt(highlightMonth)) {
                                row.classList.add('ring-2', 'ring-yellow-400', 'bg-yellow-50', 'dark:bg-yellow-500/10');
                                row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                setTimeout(() => {
                                    row.classList.remove('ring-2', 'ring-yellow-400', 'bg-yellow-50', 'dark:bg-yellow-500/10');
                                }, 5000);
                            }
                        });
                    }, 300);
                }
            }, 200);
        }, 500);
    }
});
</script>
@endsection
