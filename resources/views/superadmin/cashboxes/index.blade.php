@extends('superadmin.layout')

@section('title', 'Gestión de Cajas')

@section('content')
    <div class="px-4 md:px-12 py-6 md:py-10 max-w-[1600px] mx-auto">

        <div class="flex flex-col md:flex-row md:items-end justify-between mb-4 gap-3">
            <div>
                <p class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.3em] mb-1">Panel
                    Administrativo</p>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Gestión de Cajas
                </h1>
            </div>
            <div class="flex items-center gap-4 md:gap-6">
                <button onclick="openCashboxModal()"
                    class="text-[10px] font-black uppercase tracking-widest text-gray-900 dark:text-white border-b border-gray-900 dark:border-white pb-1 hover:opacity-60 transition-opacity">
                    Nueva Caja
                </button>
                <button onclick="openTagsModal()"
                    class="text-[10px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400 border-b border-indigo-600 dark:border-indigo-400 pb-1 hover:opacity-60 transition-opacity">
                    Gestionar Etiquetas
                </button>
            </div>
        </div>

        @if($cashboxes->count() > 1)
        <div class="mb-6 relative max-w-xs">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" id="cashboxSearch" placeholder="Buscar caja por nombre..."
                   class="w-full pl-10 pr-10 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-sm text-gray-900 dark:text-white placeholder:text-gray-400 focus:outline-none focus:border-gray-400 dark:focus:border-gray-600 transition-colors">
            <button type="button" id="cashboxSearchClear" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors hidden">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <div id="cashboxSearchResults" class="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-lg z-50 hidden max-h-60 overflow-y-auto [&::-webkit-scrollbar]:w-[6px] [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-thumb]:bg-gray-700"></div>
        </div>
        @endif

        @if(session('success'))
            <div
                class="mb-12 flex items-center gap-3 text-xs font-bold text-green-500 uppercase tracking-widest animate-in fade-in slide-in-from-top-2 duration-500">
                <div class="w-1 h-1 rounded-full bg-green-500"></div>
                {{ session('success') }}
            </div>
        @endif

        <!-- Main Workspace: Card + Movements -->
        <div class="space-y-12">

            <!-- Carousel Section -->
            <div class="relative @if($cashboxes->count() > 0) group/carousel @endif">
                @if($cashboxes->count() > 0)
                <div
                    class="absolute inset-y-0 left-0 right-0 pointer-events-none z-20 hidden md:flex justify-between items-center px-4 md:px-10">
                    <button id="prevBtn" onclick="scrollCarousel(-1)"
                        class="pointer-events-auto w-12 h-12 flex items-center justify-center rounded-full bg-white/10 dark:bg-white/5 backdrop-blur-md border border-gray-200 dark:border-white/10 text-gray-400 hover:text-gray-900 dark:hover:text-white transition-all opacity-0 group-hover/carousel:opacity-100 -translate-x-4 group-hover/carousel:translate-x-0 disabled:hidden">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button id="nextBtn" onclick="scrollCarousel(1)"
                        class="pointer-events-auto w-12 h-12 flex items-center justify-center rounded-full bg-white/10 dark:bg-white/5 backdrop-blur-md border border-gray-200 dark:border-white/10 text-gray-400 hover:text-gray-900 dark:hover:text-white transition-all opacity-0 group-hover/carousel:opacity-100 translate-x-4 group-hover/carousel:translate-x-0 disabled:hidden">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <div
                    class="absolute inset-y-0 left-0 w-32 bg-gradient-to-r from-white dark:from-gray-950 via-white/80 dark:via-gray-950/80 to-transparent z-10 pointer-events-none backdrop-blur-[2px] hidden md:block">
                </div>
                <div
                    class="absolute inset-y-0 right-0 w-32 bg-gradient-to-l from-white dark:from-gray-950 via-white/80 dark:via-gray-950/80 to-transparent z-10 pointer-events-none backdrop-blur-[2px] hidden md:block">
                </div>
                @endif

                <!-- Carousel Track -->
                <div class="text-center mb-2 md:block hidden">
                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-[0.2em] opacity-50">
                        <svg class="w-3 h-3 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                        Desliza para navegar entre cajas
                    </p>
                </div>
                <div id="cashboxCarousel"
                    class="flex gap-4 md:gap-12 overflow-x-auto no-scrollbar snap-x snap-mandatory px-[7.5%] md:px-[20%] py-2 md:py-4 items-center min-h-[220px] md:min-h-[280px]">
                    @forelse($cashboxes as $cb)
                        <div class="cashbox-card flex-shrink-0 w-[85%] md:w-full max-w-2xl snap-center transition-all duration-700 scale-95 md:scale-90 opacity-40 md:opacity-20"
                            data-id="{{ $cb->id }}" data-movements='@json($cb->movements)'>

                            <!-- The "Physical Card" Design -->
                            <div
                                class="relative bg-white dark:bg-gray-900 rounded-[1.25rem] md:rounded-[2rem] border-2 border-gray-100 dark:border-gray-800 p-4 md:p-8 overflow-hidden transition-all duration-500 hover:border-gray-300 dark:hover:border-gray-600">
                                <!-- Prominent Color Accent: Left vertical bar -->
                                <div class="absolute inset-y-0 left-0 w-1.5 md:w-2.5 transition-all duration-500"
                                    style="background-color: {{ $cb->color }}"></div>

                                <!-- Subtle Background Glow -->
                                <div class="absolute -right-20 -top-20 w-64 h-64 rounded-full opacity-[0.03] dark:opacity-[0.05] pointer-events-none"
                                    style="background-color: {{ $cb->color }}"></div>

                                <!-- Card Header -->
                                <div class="flex justify-between items-start mb-3 md:mb-6 pl-2 md:pl-4">
                                    <div class="space-y-0.5 md:space-y-1">
                                        <div class="flex items-center gap-2 md:gap-3">
                                            <!-- Card Chip Simulation -->
                                            <div
                                                class="w-7 h-4.5 md:w-10 md:h-7 rounded-sm md:rounded-md bg-yellow-400 border border-yellow-500 flex flex-col justify-around p-1 md:p-1.5 opacity-100">
                                                <div class="w-full h-px bg-yellow-700"></div>
                                                <div class="w-full h-px bg-yellow-700"></div>
                                                <div class="w-full h-px bg-yellow-700"></div>
                                            </div>
                                            <h3
                                                class="cashbox-name text-base md:text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter leading-none">
                                                {{ $cb->name }}
                                            </h3>
                                        </div>
                                        <div class="flex items-center gap-2 md:gap-3 ml-0.5 md:ml-1">
                                            <span
                                                class="text-[7px] md:text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">{{ $cb->bank_name }}</span>
                                            <div class="w-0.5 h-0.5 rounded-full bg-gray-200 dark:bg-gray-800"></div>
                                            <span
                                                class="cb-currency-label text-[7px] md:text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">{{ $cb->currency }}</span>
                                        </div>
                                    </div>
                                    <div class="flex gap-0 md:gap-1">
                                        <!-- Add Movement Button -->
                                        <button
                                            onclick="openMovementModal({{ $cb->id }}, 'income', '{{ $cb->currency }}', {{ $cb->balance }})"
                                            class="w-6 h-6 md:w-8 md:h-8 flex items-center justify-center rounded-full text-green-500 hover:bg-green-500/10 transition-all"
                                            title="Registrar Ingreso">
                                            <svg class="w-3.5 h-3.5 md:w-5 md:h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                        <button
                                            onclick="openMovementModal({{ $cb->id }}, 'expense', '{{ $cb->currency }}', {{ $cb->balance }})"
                                            class="w-6 h-6 md:w-8 md:h-8 flex items-center justify-center rounded-full text-red-500 hover:bg-red-500/10 transition-all"
                                            title="Registrar Egreso">
                                            <svg class="w-3.5 h-3.5 md:w-5 md:h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M20 12H4" />
                                            </svg>
                                        </button>
                                        <button
                                            onclick="openTransferModal({{ $cb->id }}, '{{ $cb->currency }}', {{ $cb->balance }})"
                                            class="w-6 h-6 md:w-8 md:h-8 flex items-center justify-center rounded-full text-blue-500 hover:bg-blue-500/10 transition-all"
                                            title="Desplazar a otra caja">
                                            <svg class="w-3.5 h-3.5 md:w-5 md:h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                            </svg>
                                        </button>

                                        <div class="w-px h-3.5 md:h-5 bg-gray-100 dark:bg-gray-800 mx-0.5 md:mx-1 self-center">
                                        </div>

                                        <button onclick='openCashboxModal(@json($cb))'
                                            class="w-6 h-6 md:w-8 md:h-8 flex items-center justify-center rounded-full text-gray-300 hover:text-gray-900 dark:hover:text-white transition-all">
                                            <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button type="button"
                                            onclick="confirmDelete({{ $cb->id }}, {{ count($cb->movements) }})"
                                            class="w-6 h-6 md:w-8 md:h-8 flex items-center justify-center rounded-full text-gray-300 hover:text-red-500 transition-all">
                                            <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                        <form id="delete-form-{{ $cb->id }}"
                                            action="{{ route('superadmin.cashboxes.destroy', $cb) }}" method="POST"
                                            class="hidden">
                                            @csrf @method('DELETE')
                                        </form>
                                    </div>
                                </div>

                                <!-- Balance: Hero Typography -->
                                <div class="space-y-2 md:space-y-4 pl-2 md:pl-4">
                                    <div>
                                        <p
                                            class="text-[7px] md:text-[9px] text-gray-400 font-black uppercase tracking-[0.3em] mb-0.5 md:mb-2 opacity-60">
                                            Balance Total</p>
                                        <div class="flex items-baseline gap-1 md:gap-2">
                                            <span
                                                class="text-lg md:text-2xl font-light text-gray-400 dark:text-gray-600 tabular-nums">{{ $cb->currency === 'USD' ? '$' : 'S/' }}</span>
                                            <span
                                                class="cashbox-balance text-3xl md:text-6xl font-bold text-gray-900 dark:text-white tracking-tighter tabular-nums leading-none">
                                                {{ number_format($cb->balance, 2) }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Footer Data: Monospaced -->
                                    <div
                                        class="grid grid-cols-2 gap-2 md:gap-8 pt-3 md:pt-6 border-t border-gray-50 dark:border-gray-900">
                                        @if($cb->account_number)
                                            <div class="space-y-1">
                                                <p class="text-[8px] text-gray-400 font-black uppercase tracking-[0.2em]">N° Cuenta
                                                </p>
                                                <p
                                                    class="text-xs font-bold text-gray-900 dark:text-gray-300 font-mono tracking-tighter">
                                                    {{ $cb->account_number }}
                                                </p>
                                            </div>
                                        @endif
                                        @if($cb->cci)
                                            <div class="space-y-1">
                                                <p class="text-[8px] text-gray-400 font-black uppercase tracking-[0.2em]">CCI</p>
                                                <p
                                                    class="text-xs font-bold text-gray-900 dark:text-gray-300 font-mono tracking-tighter">
                                                    {{ $cb->cci }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Manual Movements Buttons -->
                                    <div
                                        class="mt-6 flex gap-4 pt-4 border-t border-gray-50 dark:border-gray-900 opacity-0 translate-y-4 transition-all duration-500 group-hover:opacity-100 group-hover:translate-y-0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-32 text-center w-full">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">No hay cajas registradas
                            </p>
                        </div>
                    @endforelse
                </div>

                @if($cashboxes->count() > 0)
                <div class="flex justify-center gap-3 mt-10">
                    @foreach($cashboxes as $index => $cb)
                        <button onclick="scrollToIndex({{ $index }})"
                            class="carousel-dot w-2 h-2 rounded-full bg-gray-200 dark:bg-gray-800 transition-all duration-500"
                            data-index="{{ $index }}"></button>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Movements Section: Editorial List -->
            <div id="movementsSection" class="max-w-4xl mx-auto opacity-0 translate-y-8 transition-all duration-1000">
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 md:mb-12 border-b border-gray-100 dark:border-gray-900 pb-4 md:pb-6 gap-3">
                    <h2 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white uppercase tracking-tighter">Actividad
                        Reciente</h2>
                    <div class="flex flex-wrap items-center gap-3 md:gap-4">
                        <button onclick="openCodesModal()"
                            class="text-[9px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400 hover:opacity-60 transition-opacity">
                            Ver Movimientos
                        </button>
                        <button onclick="exportPDF()"
                            class="text-[9px] font-black uppercase tracking-widest text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                            Exportar PDF
                        </button>
                        <button onclick="exportExcel()"
                            class="text-[9px] font-black uppercase tracking-widest text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-colors">
                            Exportar Excel
                        </button>
                        <button onclick="openHistoryModal()"
                            class="text-[9px] font-black uppercase tracking-widest text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                            Historial Completo
                        </button>
                    </div>
                </div>

                <div id="filterBar" class="mb-6 space-y-3">
                    <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar pb-1 -mx-1 px-1">
                        <button onclick="setFilter('type','all')" data-filter-type="all"
                            class="filter-type-btn px-3 py-1.5 rounded-lg text-[10px] font-bold tracking-wide transition-all bg-gray-900 dark:bg-white text-white dark:text-gray-900 flex-shrink-0">
                            Todos
                        </button>
                        <button onclick="setFilter('type','income')" data-filter-type="income"
                            class="filter-type-btn px-3 py-1.5 rounded-lg text-[10px] font-bold tracking-wide transition-all bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 hover:text-emerald-600 dark:hover:text-emerald-400 flex-shrink-0">
                            Ingresos
                        </button>
                        <button onclick="setFilter('type','expense')" data-filter-type="expense"
                            class="filter-type-btn px-3 py-1.5 rounded-lg text-[10px] font-bold tracking-wide transition-all bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-red-100 dark:hover:bg-red-900/30 hover:text-red-600 dark:hover:text-red-400 flex-shrink-0">
                            Egresos
                        </button>

                        <div class="w-px h-5 bg-gray-200 dark:bg-gray-800 flex-shrink-0"></div>

                        <button onclick="setFilter('ref','all')" data-filter-ref="all"
                            class="filter-ref-btn px-3 py-1.5 rounded-lg text-[10px] font-bold tracking-wide transition-all bg-gray-900 dark:bg-white text-white dark:text-gray-900 flex-shrink-0">
                            Todo
                        </button>
                        <button onclick="setFilter('ref','loan')" data-filter-ref="loan"
                            class="filter-ref-btn px-3 py-1.5 rounded-lg text-[10px] font-bold tracking-wide transition-all bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-amber-100 dark:hover:bg-amber-900/30 hover:text-amber-600 dark:hover:text-amber-400 flex-shrink-0">
                            Préstamos
                        </button>
                        <button onclick="setFilter('ref','loan_installment')" data-filter-ref="loan_installment"
                            class="filter-ref-btn px-3 py-1.5 rounded-lg text-[10px] font-bold tracking-wide transition-all bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 hover:text-emerald-600 dark:hover:text-emerald-400 flex-shrink-0">
                            Cobranzas
                        </button>
                        <button onclick="setFilter('ref','investment')" data-filter-ref="investment"
                            class="filter-ref-btn px-3 py-1.5 rounded-lg text-[10px] font-bold tracking-wide transition-all bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-blue-100 dark:hover:bg-blue-900/30 hover:text-blue-600 dark:hover:text-blue-400 flex-shrink-0">
                            Inversiones
                        </button>
                        <button onclick="setFilter('ref','manual')" data-filter-ref="manual"
                            class="filter-ref-btn px-3 py-1.5 rounded-lg text-[10px] font-bold tracking-wide transition-all bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-purple-100 dark:hover:bg-purple-900/30 hover:text-purple-600 dark:hover:text-purple-400 flex-shrink-0">
                            Manual
                        </button>
                        <button onclick="setFilter('ref','transfer')" data-filter-ref="transfer"
                            class="filter-ref-btn px-3 py-1.5 rounded-lg text-[10px] font-bold tracking-wide transition-all bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-cyan-100 dark:hover:bg-cyan-900/30 hover:text-cyan-600 dark:hover:text-cyan-400 flex-shrink-0">
                            Desplazamientos
                        </button>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <input type="date" id="filter_from_date" onchange="recentCurrentPage = 1; renderMovements()"
                            class="bg-gray-100 dark:bg-gray-800 text-[10px] font-bold text-gray-600 dark:text-gray-300 rounded-lg px-2 py-1.5 border-0 focus:outline-none focus:ring-1 focus:ring-gray-400 dark:focus:ring-gray-600 flex-1 min-w-0 sm:flex-none sm:w-[120px]">
                        <span class="text-[9px] text-gray-400 font-bold flex-shrink-0">→</span>
                        <input type="date" id="filter_to_date" onchange="recentCurrentPage = 1; renderMovements()"
                            class="bg-gray-100 dark:bg-gray-800 text-[10px] font-bold text-gray-600 dark:text-gray-300 rounded-lg px-2 py-1.5 border-0 focus:outline-none focus:ring-1 focus:ring-gray-400 dark:focus:ring-gray-600 flex-1 min-w-0 sm:flex-none sm:w-[120px]">
                        <button onclick="clearDateFilter()" id="clearDateBtn" class="hidden px-2 py-1.5 rounded-lg text-[9px] font-bold text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors flex-shrink-0">✕</button>
                    </div>
                </div>

                <div id="movementsList" class="space-y-2">
                    <!-- JS populated -->
                </div>

                <!-- Pagination Container -->
                <div id="movementsPagination" class="mt-12 flex items-center justify-center gap-4">
                    <!-- JS populated -->
                </div>

                <div id="noMovements" class="hidden py-32 text-center">
                    <p class="text-[10px] font-black text-gray-300 dark:text-gray-700 uppercase tracking-[0.3em]">Sin
                        movimientos</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Caja Minimalista -->
    <div id="cashboxModal"
        class="hidden fixed inset-0 bg-white/40 dark:bg-black/60 backdrop-blur-md z-[100] flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-950 w-full max-w-lg animate-in fade-in zoom-in duration-500 max-h-[90vh] overflow-y-auto">
            <div class="px-5 md:px-10 py-6 md:py-12">
                <div class="flex items-center justify-between mb-6 md:mb-16">
                    <h3 id="modalTitle" class="text-xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tighter uppercase">
                        Nueva Caja</h3>
                    <button onclick="closeCashboxModal()"
                        class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="cashboxForm" method="POST" class="space-y-6 md:space-y-12">
                    @csrf
                    <div id="methodField"></div>

                    <div class="space-y-6 md:space-y-10">
                        <!-- Inputs: Underline only, bold text -->
                        <div class="space-y-4 md:space-y-8">
                            <div class="group">
                                <label
                                    class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] block mb-2">Nombre</label>
                                <input type="text" name="name" id="cb_name" required placeholder="Letras únicamente"
                                    oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')"
                                    class="w-full bg-transparent border-0 border-b-2 border-gray-100 dark:border-gray-900 py-4 text-xl font-bold text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all placeholder:text-gray-200 dark:placeholder:text-gray-800">
                            </div>

                            <div class="group">
                                <label
                                    class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] block mb-2">Entidad</label>
                                <select id="cb_bank_select" required onchange="toggleCustomBank(this.value)"
                                    class="w-full bg-transparent dark:bg-gray-950 border-0 border-b-2 border-gray-100 dark:border-gray-900 py-4 text-xl font-bold text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all appearance-none cursor-pointer">
                                    <option value="" disabled selected class="dark:bg-gray-950">Elegir banco</option>
                                    <option value="BCP" class="dark:bg-gray-950">BCP</option>
                                    <option value="Interbank" class="dark:bg-gray-950">Interbank</option>
                                    <option value="BBVA" class="dark:bg-gray-950">BBVA</option>
                                    <option value="Scotiabank" class="dark:bg-gray-950">Scotiabank</option>
                                    <option value="BanBif" class="dark:bg-gray-950">BanBif</option>
                                    <option value="Banco de la Nación" class="dark:bg-gray-950">Banco de la Nación</option>
                                    <option value="Mibanco" class="dark:bg-gray-950">Mibanco</option>
                                    <option value="GNB" class="dark:bg-gray-950">GNB</option>
                                    <option value="Falabella" class="dark:bg-gray-950">Falabella</option>
                                    <option value="Ripley" class="dark:bg-gray-950">Ripley</option>
                                    <option value="Pichincha" class="dark:bg-gray-950">Pichincha</option>
                                    <option value="Comercio" class="dark:bg-gray-950">Comercio</option>
                                    <option value="Efectivo" class="dark:bg-gray-950">Efectivo</option>
                                    <option value="Otros" class="dark:bg-gray-950">Otros</option>
                                </select>
                                <input type="hidden" name="bank_name" id="cb_bank">
                            </div>

                            <div id="customBankWrapper" class="hidden animate-in slide-in-from-top-2 duration-300">
                                <label
                                    class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] block mb-1">Nombre
                                    del Banco</label>
                                <input type="text" id="cb_bank_custom" placeholder="Solo letras"
                                    oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')"
                                    class="w-full bg-transparent border-0 border-b-2 border-gray-100 dark:border-gray-900 py-4 text-xl font-bold text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all placeholder:text-gray-200 dark:placeholder:text-gray-800">
                            </div>

                            <div class="grid grid-cols-2 gap-4 md:gap-16">
                                <div>
                                    <label
                                        class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] block mb-1">Moneda</label>
                                    <select name="currency" id="cb_currency" required
                                        class="w-full bg-transparent border-0 border-b-2 border-gray-100 dark:border-gray-900 py-4 text-xl font-bold text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all appearance-none cursor-pointer">
                                        <option value="PEN">Soles (S/)</option>
                                        <option value="USD">Dólares ($)</option>
                                    </select>
                                </div>
                                <!-- Color -->
                                <div>
                                    <label
                                        class="text-[9px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-[0.3em] block mb-1">Color</label>
                                    <div
                                        class="relative h-[60px] border-b-2 border-gray-100 dark:border-gray-900 group/color">
                                        <input type="color" name="color" id="cb_color" value="#000000"
                                            class="absolute inset-0 w-full h-full bg-transparent border-0 cursor-pointer overflow-hidden p-0 opacity-0 z-10">
                                        <div id="colorPreview"
                                            class="absolute inset-y-3 left-0 w-full rounded-2xl transition-all border border-gray-100 dark:border-white/10 flex items-center justify-center"
                                            style="background-color: #000000">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-8">
                                <div>
                                    <label
                                        class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] block mb-1">N°
                                        Cuenta</label>
                                    <input type="text" name="account_number" id="cb_account" placeholder="Opcional"
                                        class="w-full bg-transparent border-0 border-b-2 border-gray-100 dark:border-gray-900 py-4 text-sm font-bold text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all">
                                </div>
                                <div>
                                    <label
                                        class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] block mb-1">CCI</label>
                                    <input type="text" name="cci" id="cb_cci" placeholder="Opcional"
                                        class="w-full bg-transparent border-0 border-b-2 border-gray-100 dark:border-gray-900 py-4 text-sm font-bold text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 md:pt-10 flex items-center justify-between">
                        <button type="button" onclick="closeCashboxModal()"
                            class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 hover:text-gray-900 dark:hover:text-white border-b border-transparent hover:border-gray-900 dark:hover:border-white pb-1 transition-all">
                            Cerrar
                        </button>
                        <button type="submit"
                            class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-900 dark:text-white border-b-2 border-gray-900 dark:border-white pb-1 hover:opacity-60 transition-opacity">
                            Confirmar Caja
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Movimiento Minimalista -->
    <div id="movementModal"
        class="hidden fixed inset-0 bg-white/40 dark:bg-black/60 backdrop-blur-md z-[100] flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-950 w-full max-w-md animate-in fade-in zoom-in duration-500 max-h-[90vh] overflow-y-auto">
            <div class="px-5 md:px-10 py-6 md:py-12">
                <div class="flex items-center justify-between mb-6 md:mb-16">
                    <h3 id="movModalTitle"
                        class="text-xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tighter uppercase">Movimiento</h3>
                    <button onclick="closeMovementModal()"
                        class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="movementForm" method="POST" class="space-y-6 md:space-y-12">
                    @csrf
                    <input type="hidden" name="type" id="mov_type">

                    <div class="space-y-6 md:space-y-10">
                        <!-- Monto con Máscara -->
                        <div>
                            <label class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] block mb-1">Monto a
                                Registrar</label>
                            <div class="relative">
                                <input type="text" name="amount" id="mov_amount" required placeholder="0.00"
                                    class="w-full bg-transparent border-0 border-b-2 border-gray-100 dark:border-gray-900 py-3 md:py-4 text-2xl md:text-4xl font-black text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all tabular-nums">
                                <span id="mov_currency_label"
                                    class="absolute right-0 bottom-3 md:bottom-4 text-base md:text-xl font-bold text-gray-300 dark:text-gray-700">PEN</span>
                            </div>
                            <p id="mov_error"
                                class="hidden text-[10px] font-bold text-red-500 uppercase tracking-widest mt-2">
                                Saldo insuficiente en caja
                            </p>
                        </div>

                        <!-- Etiqueta -->
                        <div>
                            <label class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] block mb-1">Etiqueta</label>
                            @if($tags->count() > 0)
                            <select name="cashbox_tag_id" id="mov_tag" required
                                class="w-full bg-white dark:bg-gray-950 border-0 border-b-2 border-gray-100 dark:border-gray-900 py-4 text-sm font-bold text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all">
                                <option value="" disabled selected class="dark:bg-gray-950 dark:text-gray-400">Seleccionar etiqueta</option>
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}" class="dark:bg-gray-950 dark:text-white">{{ $tag->name }}</option>
                                @endforeach
                            </select>
                            @else
                            <div class="py-4 flex items-center gap-2">
                                <p class="text-xs text-red-400 font-bold">Crea una etiqueta primero</p>
                                <button type="button" onclick="closeMovementModal(); openTagsModal();"
                                    class="text-[10px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400 border-b border-indigo-600 dark:border-indigo-400 pb-0.5 hover:opacity-60 transition-opacity">
                                    Crear etiqueta
                                </button>
                            </div>
                            @endif
                            <p id="mov_tag_error" class="hidden text-[8px] text-red-500 font-bold mt-1 uppercase tracking-widest">Selecciona una etiqueta o crea una nueva</p>
                        </div>

                        <!-- Motivo / Descripción -->
                        <div>
                            <label class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] block mb-1">Motivo
                                / Origen</label>
                            <input type="text" name="description" id="mov_description" required
                                placeholder="Ej: Pago de servicios, Inyección de capital..."
                                oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')"
                                class="w-full bg-transparent border-0 border-b-2 border-gray-100 dark:border-gray-900 py-4 text-sm font-bold text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all">
                        </div>
                    </div>

                    <div class="pt-6 md:pt-10 flex items-center justify-between">
                        <button type="button" onclick="closeMovementModal()"
                            class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 hover:text-gray-900 dark:hover:text-white border-b border-transparent hover:border-gray-900 dark:hover:border-white pb-1 transition-all">
                            Cancelar
                        </button>
                        <button type="submit" id="movSubmitBtn"
                            class="text-[10px] font-black uppercase tracking-widest text-gray-900 dark:text-white border-b-2 border-gray-900 dark:border-white pb-1 hover:opacity-60 transition-opacity">
                            Confirmar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Desplazar entre Cajas -->
    <div id="transferModal"
        class="hidden fixed inset-0 bg-white/40 dark:bg-black/60 backdrop-blur-md z-[100] flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-950 w-full max-w-md animate-in fade-in zoom-in duration-500 max-h-[90vh] overflow-y-auto">
            <div class="px-5 md:px-10 py-6 md:py-12">
                <div class="flex items-center justify-between mb-6 md:mb-16">
                    <h3 class="text-xl md:text-3xl font-bold text-blue-600 dark:text-blue-400 tracking-tighter uppercase">Desplazar</h3>
                    <button onclick="closeTransferModal()"
                        class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="transferForm" method="POST" class="space-y-6 md:space-y-12">
                    @csrf
                    <input type="hidden" name="from_cashbox_id" id="transfer_from_id">

                    <div class="space-y-6 md:space-y-10">
                        <div>
                            <label class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] block mb-1">Caja Destino</label>
                            <select name="to_cashbox_id" id="transfer_to_cashbox" required
                                class="w-full bg-white dark:bg-gray-950 border-0 border-b-2 border-gray-100 dark:border-gray-900 py-4 text-sm font-bold text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition-all">
                                <option value="" disabled selected>Seleccionar caja destino</option>
                            </select>
                            <p id="transfer_currency_hint"
                                class="text-[9px] font-bold text-blue-500 uppercase tracking-widest mt-2">
                            </p>
                        </div>

                        <div>
                            <label class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] block mb-1">Monto a Desplazar</label>
                            <div class="relative">
                                <input type="text" name="amount" id="transfer_amount" required placeholder="0.00"
                                    class="w-full bg-transparent border-0 border-b-2 border-gray-100 dark:border-gray-900 py-3 md:py-4 text-2xl md:text-4xl font-black text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition-all tabular-nums">
                                <span id="transfer_currency_label"
                                    class="absolute right-0 bottom-3 md:bottom-4 text-base md:text-xl font-bold text-gray-300 dark:text-gray-700">PEN</span>
                            </div>
                            <p id="transfer_error"
                                class="hidden text-[10px] font-bold text-red-500 uppercase tracking-widest mt-2">
                                Saldo insuficiente en caja origen
                            </p>
                        </div>

                        <div>
                            <label class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] block mb-1">Etiqueta</label>
                            @if($tags->count() > 0)
                            <select name="cashbox_tag_id" id="transfer_tag" required
                                class="w-full bg-white dark:bg-gray-950 border-0 border-b-2 border-gray-100 dark:border-gray-900 py-4 text-sm font-bold text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition-all">
                                <option value="" disabled selected class="dark:bg-gray-950 dark:text-gray-400">Seleccionar etiqueta</option>
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}" class="dark:bg-gray-950 dark:text-white">{{ $tag->name }}</option>
                                @endforeach
                            </select>
                            @else
                            <div class="py-4 flex items-center gap-2">
                                <p class="text-xs text-red-400 font-bold">Crea una etiqueta primero</p>
                                <button type="button" onclick="closeTransferModal(); openTagsModal();"
                                    class="text-[10px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400 border-b border-indigo-600 dark:border-indigo-400 pb-0.5 hover:opacity-60 transition-opacity">
                                    Crear etiqueta
                                </button>
                            </div>
                            @endif
                            <p id="transfer_tag_error" class="hidden text-[8px] text-red-500 font-bold mt-1 uppercase tracking-widest">Selecciona una etiqueta o crea una nueva</p>
                        </div>

                        <div>
                            <label class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] block mb-1">Descripción</label>
                            <input type="text" name="description" id="transfer_description" required
                                placeholder="Ej: Traslado de fondos..."
                                class="w-full bg-transparent border-0 border-b-2 border-gray-100 dark:border-gray-900 py-4 text-sm font-bold text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition-all">
                        </div>
                    </div>

                    <div class="pt-6 md:pt-10 flex items-center justify-between">
                        <button type="button" onclick="closeTransferModal()"
                            class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 hover:text-gray-900 dark:hover:text-white border-b border-transparent hover:border-gray-900 dark:hover:border-white pb-1 transition-all">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="text-[10px] font-black uppercase tracking-widest text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400 pb-1 hover:opacity-60 transition-opacity">
                            Desplazar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Gestionar Etiquetas -->
    <div id="tagsModal"
        class="hidden fixed inset-0 bg-white/40 dark:bg-black/60 backdrop-blur-md z-[100] flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-950 w-full max-w-lg animate-in fade-in zoom-in duration-500 max-h-[90vh] flex flex-col">
            <div class="px-5 md:px-10 py-6 md:py-12 overflow-y-auto">
                <div class="flex items-center justify-between mb-6 md:mb-16">
                    <h3 id="tagModalTitle" class="text-xl md:text-3xl font-bold text-indigo-600 dark:text-indigo-400 tracking-tighter uppercase">Etiquetas</h3>
                    <button onclick="closeTagsModal()"
                        class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="tagForm" method="POST" class="space-y-6 md:space-y-12 mb-6 md:mb-12">
                    @csrf
                    <input type="hidden" name="tag_id" id="tag_id">
                    <input type="hidden" name="_method" id="tag_method">

                    <div class="space-y-6 md:space-y-10">
                        <div>
                            <label class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] block mb-1">Nombre</label>
                            <input type="text" name="name" id="tag_name" required placeholder="Ej: Operativo, Inversión..."
                                oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, '')"
                                class="w-full bg-transparent border-0 border-b-2 border-gray-100 dark:border-gray-900 py-3 md:py-4 text-sm font-bold text-gray-900 dark:text-white focus:outline-none focus:border-indigo-500 dark:focus:border-indigo-400 transition-all">
                        </div>

                        <div>
                            <label class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] block mb-1">Color</label>
                            <div class="flex items-center gap-4">
                                <input type="color" name="color" id="tag_color" value="#6366f1"
                                    class="w-12 h-12 rounded cursor-pointer border-0 bg-transparent">
                                <span id="tag_color_hex" class="text-sm font-mono font-bold text-gray-500 dark:text-gray-400">#6366f1</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 md:pt-10 flex items-center justify-between">
                        <button type="button" onclick="resetTagForm()"
                            class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 hover:text-gray-900 dark:hover:text-white border-b border-transparent hover:border-gray-900 dark:hover:border-white pb-1 transition-all">
                            Limpiar
                        </button>
                        <button type="submit" id="tagSubmitBtn"
                            class="text-[10px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400 border-b-2 border-indigo-600 dark:border-indigo-400 pb-1 hover:opacity-60 transition-opacity">
                            Guardar Etiqueta
                        </button>
                    </div>
                </form>

                <div id="tagsList" class="space-y-3">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Eliminación Peligrosa -->
    <div id="dangerousDeleteModal"
        class="hidden fixed inset-0 bg-white/40 dark:bg-black/60 backdrop-blur-md z-[100] flex items-center justify-center p-4">
        <div
            class="bg-white dark:bg-gray-950 w-full max-w-lg animate-in fade-in zoom-in duration-500 border-2 border-red-500/20">
            <div class="px-5 md:px-10 py-6 md:py-12">
                <div class="flex items-center justify-between mb-6 md:mb-12">
                    <h3 class="text-xl md:text-3xl font-bold text-red-600 tracking-tighter uppercase">Acción Peligrosa</h3>
                    <button onclick="closeDangerousDeleteModal()"
                        class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-8">
                    <div class="p-6 bg-red-50 dark:bg-red-500/5 rounded-3xl border border-red-100 dark:border-red-500/10">
                        <p class="text-sm font-bold text-red-900 dark:text-red-200 leading-relaxed">
                            Esta caja contiene movimientos registrados. Eliminarla causará:
                        </p>
                        <ul class="mt-4 space-y-3">
                            <li
                                class="flex items-start gap-3 text-[10px] font-black text-red-700 dark:text-red-400 uppercase tracking-widest">
                                <div class="w-1.5 h-1.5 rounded-full bg-red-500 mt-1"></div>
                                Pérdida total del historial de transacciones.
                            </li>
                            <li
                                class="flex items-start gap-3 text-[10px] font-black text-red-700 dark:text-red-400 uppercase tracking-widest">
                                <div class="w-1.5 h-1.5 rounded-full bg-red-500 mt-1"></div>
                                Desajuste en los balances generales del sistema.
                            </li>
                            <li
                                class="flex items-start gap-3 text-[10px] font-black text-red-700 dark:text-red-400 uppercase tracking-widest">
                                <div class="w-1.5 h-1.5 rounded-full bg-red-500 mt-1"></div>
                                Esta acción es irreversible y no se puede recuperar.
                            </li>
                        </ul>
                    </div>

                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] text-center">
                        ¿Estás absolutamente seguro de continuar?
                    </p>
                </div>

                <div class="pt-6 md:pt-12 flex items-center justify-between">
                    <button type="button" onclick="closeDangerousDeleteModal()"
                        class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 hover:text-gray-900 dark:hover:text-white border-b border-transparent hover:border-gray-900 dark:hover:border-white pb-1 transition-all">
                        Cancelar
                    </button>
                    <button id="confirmDangerousDeleteBtn"
                        class="text-[10px] font-black uppercase tracking-[0.3em] text-red-600 border-b-2 border-red-600 pb-1 hover:opacity-60 transition-opacity">
                        Eliminar Definitivamente
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Eliminación Simple -->
    <div id="simpleDeleteModal"
        class="hidden fixed inset-0 bg-white/40 dark:bg-black/60 backdrop-blur-md z-[100] flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-950 w-full max-w-sm animate-in fade-in zoom-in duration-500">
            <div class="px-5 md:px-10 py-6 md:py-12">
                <div class="flex items-center justify-between mb-6 md:mb-12">
                    <h3 class="text-xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tighter uppercase">Eliminar Caja
                    </h3>
                    <button onclick="closeSimpleDeleteModal()"
                        class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] leading-relaxed mb-6 md:mb-12">
                    ¿Confirmas que deseas eliminar esta caja? Esta acción no se puede deshacer.
                </p>

                <div class="flex items-center justify-between">
                    <button type="button" onclick="closeSimpleDeleteModal()"
                        class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 hover:text-gray-900 dark:hover:text-white border-b border-transparent hover:border-gray-900 dark:hover:border-white pb-1 transition-all">
                        Cerrar
                    </button>
                    <button id="confirmSimpleDeleteBtn"
                        class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-900 dark:text-white border-b-2 border-gray-900 dark:border-white pb-1 hover:opacity-60 transition-opacity">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar Etiqueta -->
    <div id="deleteTagModal"
        class="hidden fixed inset-0 bg-white/40 dark:bg-black/60 backdrop-blur-md z-[100] flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-950 w-full max-w-sm animate-in fade-in zoom-in duration-500">
            <div class="px-5 md:px-10 py-6 md:py-12">
                <div class="flex items-center justify-between mb-6 md:mb-12">
                    <h3 class="text-xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tighter uppercase">Eliminar Etiqueta</h3>
                    <button onclick="closeDeleteTagModal()"
                        class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="mb-6 md:mb-12">
                    <p class="text-sm font-bold text-gray-900 dark:text-white mb-2">¿Eliminar la etiqueta <span id="deleteTagName" class="text-red-600 dark:text-red-400"></span>?</p>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] leading-relaxed">Esta acción no se puede deshacer. Las cajas que usen esta etiqueta quedarán sin ella.</p>
                </div>

                <div class="flex items-center justify-between">
                    <button type="button" onclick="closeDeleteTagModal()"
                        class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 hover:text-gray-900 dark:hover:text-white border-b border-transparent hover:border-gray-900 dark:hover:border-white pb-1 transition-all">
                        Cancelar
                    </button>
                    <button id="confirmDeleteTagBtn"
                        class="text-[10px] font-black uppercase tracking-[0.3em] text-red-600 dark:text-red-400 border-b-2 border-red-600 dark:border-red-400 pb-1 hover:opacity-60 transition-opacity">
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detalle de Préstamo -->
    <div id="deleteMovementModal"
        class="hidden fixed inset-0 bg-white/40 dark:bg-black/60 backdrop-blur-md z-[100] flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-950 w-full max-w-sm animate-in fade-in zoom-in duration-500">
            <div class="px-5 md:px-10 py-6 md:py-12">
                <div class="flex items-center justify-between mb-6 md:mb-12">
                    <h3 class="text-xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tighter uppercase">Deshacer Movimiento</h3>
                    <button onclick="closeDeleteMovementModal()"
                        class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="mb-4 md:mb-8">
                    <p id="deleteMovementDesc" class="text-sm font-bold text-gray-900 dark:text-white mb-2"></p>
                    <p id="deleteMovementAmount" class="text-2xl font-black tabular-nums mb-4"></p>
                    <p class="text-[10px] font-black text-red-500 uppercase tracking-[0.3em] leading-relaxed">
                        Esta acción revertirá el saldo de la caja y no se puede deshacer.
                    </p>
                </div>

                <div class="flex items-center justify-between">
                    <button type="button" onclick="closeDeleteMovementModal()"
                        class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 hover:text-gray-900 dark:hover:text-white border-b border-transparent hover:border-gray-900 dark:hover:border-white pb-1 transition-all">
                        Cerrar
                    </button>
                    <form id="deleteMovementForm" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="text-[10px] font-black uppercase tracking-[0.3em] text-red-600 dark:text-red-400 border-b-2 border-red-600 dark:border-red-400 pb-1 hover:opacity-60 transition-opacity">
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detalle de Préstamo -->
    <div id="loanDetailModal"
        class="hidden fixed inset-0 bg-white/40 dark:bg-black/60 backdrop-blur-md z-[100] flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-950 w-full max-w-lg animate-in fade-in zoom-in duration-500 max-h-[90vh] overflow-y-auto">
            <div class="px-5 md:px-10 py-6 md:py-12">
                <div class="flex items-center justify-between mb-6 md:mb-12">
                    <h3 class="text-xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tighter uppercase">Detalle del
                        Egreso</h3>
                    <button onclick="closeLoanDetailModal()"
                        class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div id="loanDetailContent" class="space-y-4 md:space-y-8">
                    <!-- JS populated -->
                </div>

                <div class="pt-6 md:pt-12 flex justify-center">
                    <button type="button" onclick="closeLoanDetailModal()"
                        class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 hover:text-gray-900 dark:hover:text-white border-b border-transparent hover:border-gray-900 dark:hover:border-white pb-1 transition-all">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ver Movimientos -->
    <div id="codesModal"
        class="hidden fixed inset-0 bg-white/40 dark:bg-black/60 backdrop-blur-md z-[100] flex items-center justify-center p-4">
        <div
            class="bg-white dark:bg-gray-950 w-full max-w-4xl max-h-[90vh] flex flex-col animate-in fade-in zoom-in duration-500 rounded-xl md:rounded-[2rem] overflow-hidden">
            <div class="px-5 md:px-10 py-6 md:py-12 flex flex-col h-full overflow-hidden">
                <div class="flex items-center justify-between mb-4 md:mb-8">
                    <h3 class="text-lg md:text-3xl font-bold text-gray-900 dark:text-white tracking-tighter uppercase">Códigos de
                        Movimientos</h3>
                    <button onclick="closeCodesModal()"
                        class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="mb-6">
                    <input type="text" id="codeSearchInput" placeholder="Buscar por código o descripción..."
                        oninput="filterCodes()"
                        class="w-full bg-transparent border-0 border-b-2 border-gray-100 dark:border-gray-900 py-3 text-sm font-bold text-gray-900 dark:text-white focus:outline-none focus:border-indigo-500 dark:focus:border-indigo-400 transition-all placeholder:text-gray-300 dark:placeholder:text-gray-700">
                </div>
                <div id="codesList" class="flex-1 overflow-y-auto space-y-2 pr-2">
                </div>
                <div id="codesPagination" class="mt-4 flex items-center justify-center gap-4">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Historial Completo -->
    <div id="historyModal"
        class="hidden fixed inset-0 bg-white/40 dark:bg-black/60 backdrop-blur-md z-[100] flex items-center justify-center p-4">
        <div
            class="bg-white dark:bg-gray-950 w-full max-w-4xl max-h-[90vh] flex flex-col animate-in fade-in zoom-in duration-500 rounded-xl md:rounded-[2rem] overflow-hidden">
            <div class="px-5 md:px-10 py-6 md:py-12 flex flex-col h-full overflow-hidden">
                <div class="flex items-center justify-between mb-4 md:mb-8">
                    <h3 class="text-lg md:text-3xl font-bold text-gray-900 dark:text-white tracking-tighter uppercase">Historial de
                        Transacciones</h3>
                    <button onclick="closeHistoryModal()"
                        class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Filtros -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div onclick="this.querySelector('input').showPicker()" class="cursor-pointer group">
                        <label
                            class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] block mb-2 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">Desde
                            Fecha</label>
                        <input type="date" id="history_from_date" onchange="fetchHistory(1)"
                            class="w-full bg-transparent border-0 border-b-2 border-gray-100 dark:border-gray-900 py-2 text-sm font-bold text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all calendar-picker-indicator-none">
                    </div>
                    <div>
                        <label
                            class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] block mb-2">Tipo</label>
                        <select id="history_type" onchange="fetchHistory(1)"
                            class="w-full bg-transparent dark:bg-gray-950 border-0 border-b-2 border-gray-100 dark:border-gray-900 py-2 text-sm font-bold text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all appearance-none cursor-pointer">
                            <option value="" class="dark:bg-gray-950">Todos</option>
                            <option value="income" class="dark:bg-gray-950">Ingresos</option>
                            <option value="expense" class="dark:bg-gray-950">Egresos</option>
                        </select>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto custom-scrollbar pr-2">
                    <div id="historyList" class="space-y-2">
                        <!-- JS populated -->
                    </div>
                    <div id="historyNoData" class="hidden py-24 text-center">
                        <p class="text-[10px] font-black text-gray-300 dark:text-gray-700 uppercase tracking-[0.3em]">Sin
                            movimientos</p>
                    </div>
                </div>

                <!-- Pagination -->
                <div id="historyPagination" class="mt-8 flex items-center justify-center gap-4">
                    <!-- JS populated -->
                </div>
            </div>
        </div>
    </div>

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        #cashboxCarousel {
            -webkit-overflow-scrolling: touch;
            scroll-behavior: smooth;
            scroll-padding: 0 7.5%;
            cursor: grab;
            user-select: none;
        }

        #cashboxCarousel:active {
            cursor: grabbing;
        }

        @media (min-width: 768px) {
            #cashboxCarousel {
                scroll-padding: 0 20%;
            }
        }

        .cashbox-card {
            filter: blur(8px);
            transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .cashbox-card.active {
            scale: 1;
            opacity: 1;
            filter: blur(0);
        }

        .cashbox-card.active>div {
            border-color: #facc15 !important;
            box-shadow: 0 10px 30px -10px rgba(250, 204, 21, 0.3);
        }

        .cashbox-card.active .mt-12 {
            opacity: 1;
            transform: translateY(0);
        }

        .movement-item.loan-related {
            border-left-color: #facc15 !important;
            background-color: rgba(250, 204, 21, 0.05);
        }

        .dark .movement-item.loan-related {
            background-color: rgba(250, 204, 21, 0.02);
        }

        .movement-item.loan-related:hover {
            background-color: rgba(250, 204, 21, 0.1);
        }

        .movement-item {
            animation: slideIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .calendar-picker-indicator-none::-webkit-calendar-picker-indicator {
            display: none;
            -webkit-appearance: none;
        }

        #history_from_date {
            color-scheme: light;
        }

        .dark #history_from_date {
            color-scheme: dark;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.2/dist/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.3/dist/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js"></script>

    <script>
        const carousel = document.getElementById('cashboxCarousel');
        const cards = document.querySelectorAll('.cashbox-card');
        const dots = document.querySelectorAll('.carousel-dot');
        const movementsSection = document.getElementById('movementsSection');
        const movementsList = document.getElementById('movementsList');
        const noMovements = document.getElementById('noMovements');
        const movementsPagination = document.getElementById('movementsPagination');

        let currentMovements = [];
        let currentSymbol = 'S/';
        const itemsPerPage = 10;
        const recentItemsCount = 5;
        let recentCurrentPage = 1;
        let codesCurrentPage = 1;
        let codesFilteredMovements = [];
        let activeFilterType = 'all';
        let activeFilterRef = 'all';

        function setFilter(group, value) {
            if (group === 'type') {
                activeFilterType = value;
                document.querySelectorAll('.filter-type-btn').forEach(btn => {
                    const isActive = btn.dataset.filterType === value;
                    btn.className = `filter-type-btn px-3 py-1.5 rounded-lg text-[10px] font-bold tracking-wide transition-all ${
                        isActive ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 hover:text-emerald-600 dark:hover:text-emerald-400'
                    }`;
                });
            } else if (group === 'ref') {
                activeFilterRef = value;
                document.querySelectorAll('.filter-ref-btn').forEach(btn => {
                    const isActive = btn.dataset.filterRef === value;
                    btn.className = `filter-ref-btn px-3 py-1.5 rounded-lg text-[10px] font-bold tracking-wide transition-all ${
                        isActive ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700'
                    }`;
                });
            }
            recentCurrentPage = 1;
            renderMovements();
        }

        function getFilteredMovements() {
            const fromDate = document.getElementById('filter_from_date').value;
            const toDate = document.getElementById('filter_to_date').value;
            const clearBtn = document.getElementById('clearDateBtn');
            clearBtn.classList.toggle('hidden', !fromDate && !toDate);

            return currentMovements.filter(m => {
                if (activeFilterType !== 'all' && m.type !== activeFilterType) return false;
                if (activeFilterRef !== 'all' && m.reference_type !== activeFilterRef) return false;
                if (fromDate) {
                    const mDate = new Date(m.date).toISOString().slice(0, 10);
                    if (mDate < fromDate) return false;
                }
                if (toDate) {
                    const mDate = new Date(m.date).toISOString().slice(0, 10);
                    if (mDate > toDate) return false;
                }
                return true;
            });
        }

        function clearDateFilter() {
            document.getElementById('filter_from_date').value = '';
            document.getElementById('filter_to_date').value = '';
            document.getElementById('clearDateBtn').classList.add('hidden');
            recentCurrentPage = 1;
            renderMovements();
        }

        function updateActiveCard() {
            let activeIndex = 0;
            let minDistance = Infinity;
            const carouselRect = carousel.getBoundingClientRect();
            const carouselCenter = carouselRect.left + carouselRect.width / 2;

            cards.forEach((card, index) => {
                const rect = card.getBoundingClientRect();
                const cardCenter = rect.left + rect.width / 2;
                const distance = Math.abs(cardCenter - carouselCenter);

                if (distance < minDistance) {
                    minDistance = distance;
                    activeIndex = index;
                }
                card.classList.remove('active');
            });

            if (cards[activeIndex]) {
                const activeCard = cards[activeIndex];
                activeCard.classList.add('active');

                // Update dots
                dots.forEach((dot, index) => {
                    dot.classList.toggle('bg-yellow-400', index === activeIndex);
                    dot.classList.toggle('w-6', index === activeIndex);
                    dot.classList.toggle('opacity-100', index === activeIndex);
                    dot.classList.toggle('opacity-20', index !== activeIndex);
                });

                // Update arrows visibility
                const prevBtn = document.getElementById('prevBtn');
                const nextBtn = document.getElementById('nextBtn');
                if (prevBtn) prevBtn.disabled = activeIndex === 0;
                if (nextBtn) nextBtn.disabled = activeIndex === cards.length - 1;

                // Update movements
                currentMovements = JSON.parse(activeCard.dataset.movements);
                const currency = activeCard.querySelector('.cb-currency-label').textContent;
                currentSymbol = currency === 'USD' ? '$' : 'S/';
                activeFilterType = 'all';
                activeFilterRef = 'all';
                recentCurrentPage = 1;
                document.querySelectorAll('.filter-type-btn').forEach(btn => {
                    btn.className = `filter-type-btn px-3 py-1.5 rounded-lg text-[10px] font-bold tracking-wide transition-all ${
                        btn.dataset.filterType === 'all' ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 hover:text-emerald-600 dark:hover:text-emerald-400'
                    }`;
                });
                document.querySelectorAll('.filter-ref-btn').forEach(btn => {
                    btn.className = `filter-ref-btn px-3 py-1.5 rounded-lg text-[10px] font-bold tracking-wide transition-all ${
                        btn.dataset.filterRef === 'all' ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700'
                    }`;
                });
                renderMovements();
            }
        }

        function renderMovements() {
            movementsSection.classList.remove('opacity-0', 'translate-y-8');
            movementsList.innerHTML = '';
            movementsPagination.innerHTML = '';

            const filtered = getFilteredMovements();

            if (filtered.length === 0) {
                noMovements.classList.remove('hidden');
                return;
            }

            noMovements.classList.add('hidden');

            const totalPages = Math.ceil(filtered.length / recentItemsCount);
            if (recentCurrentPage > totalPages) recentCurrentPage = totalPages;
            if (recentCurrentPage < 1) recentCurrentPage = 1;

            const start = (recentCurrentPage - 1) * recentItemsCount;
            const end = start + recentItemsCount;
            const paginatedItems = filtered.slice(start, end);

            paginatedItems.forEach((m, i) => {
                const isIncome = m.type === 'income';
                const dateObj = new Date(m.date);
                const date = dateObj.toLocaleDateString('es-PE', { day: '2-digit', month: 'short' });
                const time = dateObj.toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit' });
                const userName = m.user ? m.user.name : 'Sistema';

                const typeLabels = {
                    'loan': 'PRÉSTAMO',
                    'loan_installment': 'COBRANZA',
                    'investment': 'INVERSIÓN',
                    'investment_payout': 'PAGO INVERSOR',
                    'manual': 'MANUAL',
                    'transfer': 'DESPLAZAMIENTO',
                    'ghost': 'FANTASMA'
                };
                const displayType = typeLabels[m.reference_type] || 'OPERACIÓN';
                const isLoanRelated = m.reference_type === 'loan' || m.reference_type === 'loan_installment';
                const isInvestmentRelated = m.reference_type === 'investment' || m.reference_type === 'investment_payout';
                const isGhostRelated = m.reference_type === 'ghost';
                let redirectUrl = '';
                if (m.reference_type === 'loan') {
                    redirectUrl = `{{ route('superadmin.loans.index') }}?filter=all&loan_id=${m.reference_id}`;
                } else if (m.reference_type === 'loan_installment') {
                    const isInterestOnly = m.description && (m.description.includes('SOLO INTERÉS') || m.description.includes('MORA + INTERÉS'));
                    if (isInterestOnly) {
                        const ipId = m.interest_payment_id || '';
                        redirectUrl = `{{ route('superadmin.installments.index') }}?filter=paid&installment_id=${m.reference_id}&highlight_interest=true&interest_payment_id=${ipId}`;
                    } else {
                        redirectUrl = `{{ route('superadmin.installments.index') }}?filter=paid&installment_id=${m.reference_id}`;
                    }
                } else if (m.reference_type === 'investment') {
                    redirectUrl = `/gestion-interna/inversiones/${m.reference_id}?highlight_investor=${m.investor_user_id}`;
                } else if (m.reference_type === 'investment_payout') {
                    const payoutId = m.reference_id;
                    const oppId = m.investor_payout ? m.investor_payout.investment_opportunity_id : '';
                    const invId = m.investor_user_id || (m.investor_payout ? m.investor_payout.investor_user_id : '');
                    const monthNum = m.investor_payout ? m.investor_payout.month_number : '';
                    redirectUrl = `/gestion-interna/pagos-inversores?highlight_payout=${payoutId}&opp_id=${oppId}&inv_id=${invId}&month=${monthNum}`;
                } else if (m.reference_type === 'ghost') {
                    const ghostUserId = m.user_id || m.reference_id;
                    redirectUrl = `/gestion-interna/usuarios/${ghostUserId}?highlight_movement=${m.id}`;
                }

                const tagBorderColor = m.tag ? `border-color:${m.tag.color}` : '';
                const tagBorderClass = m.tag ? '' : (isLoanRelated || isInvestmentRelated || isGhostRelated ? '' : 'border-transparent');
                const isDeletable = m.reference_type === 'manual' || m.reference_type === 'transfer';
                const clickAction = isLoanRelated || isInvestmentRelated || isGhostRelated
                    ? `onclick="window.location.href='${redirectUrl}'"`
                    : (isDeletable ? `onclick="openDeleteMovementModal(${m.id}, '${m.description.replace(/'/g, "\\'")}', '${currentSymbol} ${parseFloat(m.amount).toLocaleString('en-US', { minimumFractionDigits: 2 })}', '${m.type}')"` : '');

                const html = `
                                            <div class="movement-item px-3 md:px-12 py-4 md:py-8 flex items-center justify-between group transition-all duration-500 rounded-xl md:rounded-3xl border-l-4 ${isLoanRelated || isInvestmentRelated || isGhostRelated ? 'loan-related cursor-pointer' : (isDeletable ? 'cursor-pointer hover:bg-red-50 dark:hover:bg-red-500/5' : (m.tag ? '' : 'hover:bg-gray-50 dark:hover:bg-white/5'))} ${tagBorderClass}"
                                                 style="animation-delay: ${i * 0.05}s; ${tagBorderColor}"
                                                 ${clickAction}>
                                                    <div class="flex items-center gap-2 md:gap-8 min-w-0 flex-1">
                                                        <div class="flex flex-col items-center w-8 md:w-12 flex-shrink-0">
                                                            <span class="text-[7px] md:text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest tabular-nums leading-none">${date}</span>
                                                            <span class="text-[6px] md:text-[8px] font-bold text-gray-400 uppercase tracking-widest tabular-nums mt-0.5 md:mt-1">${time}</span>
                                                        </div>
                                                        <div class="min-w-0 flex-1">
                                                            <div class="flex items-center gap-1 md:gap-2">
                                                                <p class="text-xs md:text-base font-bold text-gray-900 dark:text-white tracking-tight leading-tight truncate">${m.description}</p>
                                                                ${(m.reference_type === 'loan' && m.loan) || (m.reference_type === 'loan_installment' && m.installment) ? `
                                                                    <button onclick="event.stopPropagation(); showLoanDetail(${m.id})" class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors flex-shrink-0">
                                                                        <svg class="w-3 md:w-3.5 h-3 md:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                        </svg>
                                                                    </button>
                                                                ` : (m.reference_type === 'investment' && m.investment ? `
                                                                    <button onclick="event.stopPropagation(); showInvestmentDetail(${m.id})" class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors flex-shrink-0">
                                                                        <svg class="w-3 md:w-3.5 h-3 md:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                        </svg>
                                                                    </button>
                                                                ` : (m.reference_type === 'investment_payout' ? `
                                                                    <button onclick="event.stopPropagation(); window.location.href='${redirectUrl}'" class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors flex-shrink-0">
                                                                        <svg class="w-3 md:w-3.5 h-3 md:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                        </svg>
                                                                    </button>
                                                                ` : ''))}
                                                            </div>
                                                            <p class="text-[7px] md:text-[9px] text-gray-400 font-black uppercase tracking-[0.15em] md:tracking-[0.2em] mt-0.5 md:mt-1 leading-none truncate">
                                                                ${displayType}${m.tag ? ` <span class="inline-flex items-center gap-1 ml-1"><span class="w-1.5 h-1.5 rounded-full inline-block" style="background-color:${m.tag.color}"></span><span style="color:${m.tag.color}">${m.tag.name}</span></span>` : ''} <span class="hidden md:inline">• Registrado por:</span><span class="md:hidden"> • </span><span class="text-gray-600 dark:text-gray-300 font-bold">${userName}</span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="text-right ml-2 flex-shrink-0">
                                                        <p class="text-xs md:text-xl font-black tabular-nums ${isIncome ? 'text-green-500' : 'text-red-500'} leading-none">
                                                            ${isIncome ? '+' : '-'} ${currentSymbol} ${parseFloat(m.amount).toLocaleString('en-US', { minimumFractionDigits: 2 })}
                                                        </p>
                                                    </div>
                                                </div>
                                            `;
                movementsList.insertAdjacentHTML('beforeend', html);
            });

            if (totalPages > 1) {
                const prevDisabled = recentCurrentPage <= 1;
                const nextDisabled = recentCurrentPage >= totalPages;

                let paginationHtml = `
                    <button onclick="recentCurrentPage = ${recentCurrentPage - 1}; renderMovements(); movementsSection.scrollIntoView({behavior:'smooth',block:'start'})" 
                            ${prevDisabled ? 'disabled' : ''} 
                            class="p-2 text-gray-400 hover:text-gray-900 dark:hover:text-white disabled:opacity-0 disabled:pointer-events-none transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    </button>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest tabular-nums">${recentCurrentPage} / ${totalPages}</span>
                    <button onclick="recentCurrentPage = ${recentCurrentPage + 1}; renderMovements(); movementsSection.scrollIntoView({behavior:'smooth',block:'start'})" 
                            ${nextDisabled ? 'disabled' : ''} 
                            class="p-2 text-gray-400 hover:text-gray-900 dark:hover:text-white disabled:opacity-0 disabled:pointer-events-none transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </button>
                `;
                movementsPagination.innerHTML = paginationHtml;
            }
        }

        function scrollCarousel(direction) {
            const activeCard = document.querySelector('.cashbox-card.active');
            if (!activeCard) return;

            let currentIndex = Array.from(cards).indexOf(activeCard);
            let nextIndex = currentIndex + direction;

            if (nextIndex >= 0 && nextIndex < cards.length) {
                scrollToIndex(nextIndex);
            }
        }

        function scrollToIndex(index) {
            const card = cards[index];
            if (!card) return;

            const scrollLeft = card.offsetLeft - (carousel.offsetWidth / 2) + (card.offsetWidth / 2);
            carousel.scrollTo({ left: scrollLeft, behavior: 'smooth' });
        }

        carousel.addEventListener('scroll', () => {
            clearTimeout(window.scrollTimeout);
            window.scrollTimeout = setTimeout(updateActiveCard, 50);
        });

        // Mouse Drag-to-Scroll logic
        let isDown = false;
        let startX;
        let scrollLeft;
        let isDragging = false;

        carousel.addEventListener('mousedown', (e) => {
            isDown = true;
            isDragging = false;
            carousel.style.scrollBehavior = 'auto';
            carousel.style.scrollSnapType = 'none';
            startX = e.pageX - carousel.offsetLeft;
            scrollLeft = carousel.scrollLeft;
        });

        carousel.addEventListener('mouseleave', () => {
            if (!isDown) return;
            isDown = false;
            carousel.style.scrollBehavior = 'smooth';
            carousel.style.scrollSnapType = 'x mandatory';
        });

        carousel.addEventListener('mouseup', (e) => {
            if (!isDown) return;
            isDown = false;
            carousel.style.scrollBehavior = 'smooth';
            carousel.style.scrollSnapType = 'x mandatory';

            if (isDragging) {
                // Prevent accidental clicks if we were dragging
                e.preventDefault();
            }

            // Snap to nearest card
            updateActiveCard();
            const activeCard = document.querySelector('.cashbox-card.active');
            if (activeCard) {
                scrollToIndex(Array.from(cards).indexOf(activeCard));
            }
        });

        carousel.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            const x = e.pageX - carousel.offsetLeft;
            const walk = (x - startX); // 1:1 ratio
            if (Math.abs(walk) > 5) {
                isDragging = true;
            }
            if (isDragging) {
                e.preventDefault();
                carousel.scrollLeft = scrollLeft - walk;
            }
        });

        // Prevent clicks on children while dragging
        carousel.addEventListener('click', (e) => {
            if (isDragging) {
                e.preventDefault();
                e.stopPropagation();
            }
        }, true);

        // Init
        if (cards.length > 0) {
            setTimeout(updateActiveCard, 100);
        }

        function toggleCustomBank(value) {
            const wrapper = document.getElementById('customBankWrapper');
            const customInput = document.getElementById('cb_bank_custom');
            const hiddenInput = document.getElementById('cb_bank');

            if (value === 'Otros') {
                wrapper.classList.remove('hidden');
                customInput.required = true;
                hiddenInput.value = customInput.value;
            } else {
                wrapper.classList.add('hidden');
                customInput.required = false;
                hiddenInput.value = value;
            }
        }

        document.getElementById('cb_bank_custom').addEventListener('input', function () {
            if (document.getElementById('cb_bank_select').value === 'Otros') {
                document.getElementById('cb_bank').value = this.value;
            }
        });

        document.getElementById('cb_color').addEventListener('input', function () {
            document.getElementById('colorPreview').style.backgroundColor = this.value;
        });

        function openCashboxModal(cb = null) {
            const modal = document.getElementById('cashboxModal');
            const form = document.getElementById('cashboxForm');
            const title = document.getElementById('modalTitle');
            const methodField = document.getElementById('methodField');
            const bankSelect = document.getElementById('cb_bank_select');
            const customInput = document.getElementById('cb_bank_custom');
            const colorInput = document.getElementById('cb_color');
            const colorPreview = document.getElementById('colorPreview');

            if (cb) {
                title.textContent = 'Editar Caja';
                form.action = `/gestion-interna/cajas/${cb.id}`;
                methodField.innerHTML = '@method("PUT")';
                document.getElementById('cb_name').value = cb.name;

                const banks = ['BCP', 'Interbank', 'BBVA', 'Scotiabank', 'BanBif', 'Banco de la Nación', 'Mibanco', 'GNB', 'Falabella', 'Ripley', 'Pichincha', 'Comercio', 'Efectivo'];
                if (banks.includes(cb.bank_name)) {
                    bankSelect.value = cb.bank_name;
                    toggleCustomBank(cb.bank_name);
                } else {
                    bankSelect.value = 'Otros';
                    toggleCustomBank('Otros');
                    customInput.value = cb.bank_name;
                    document.getElementById('cb_bank').value = cb.bank_name;
                }

                document.getElementById('cb_currency').value = cb.currency;
                colorInput.value = cb.color;
                colorPreview.style.backgroundColor = cb.color;
                document.getElementById('cb_account').value = cb.account_number || '';
                document.getElementById('cb_cci').value = cb.cci || '';
            } else {
                title.textContent = 'Nueva Caja';
                form.action = "{{ route('superadmin.cashboxes.store') }}";
                methodField.innerHTML = '';
                form.reset();
                bankSelect.value = "";
                toggleCustomBank("");
                colorInput.value = '#000000';
                colorPreview.style.backgroundColor = '#000000';
            }

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeCashboxModal() {
            document.getElementById('cashboxModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        let currentCashboxBalance = 0;

        function openMovementModal(cashboxId, type, currency, balance) {
            const modal = document.getElementById('movementModal');
            const form = document.getElementById('movementForm');
            const title = document.getElementById('movModalTitle');
            const typeInput = document.getElementById('mov_type');
            const currencyLabel = document.getElementById('mov_currency_label');
            const amountInput = document.getElementById('mov_amount');
            const errorMsg = document.getElementById('mov_error');

            currentCashboxBalance = balance;
            typeInput.value = type;
            currencyLabel.textContent = currency;
            form.action = `/gestion-interna/cajas/${cashboxId}/movimiento`;
            title.textContent = type === 'income' ? 'Registrar Ingreso' : 'Registrar Egreso';

            amountInput.value = '';
            document.getElementById('mov_description').value = '';
            errorMsg.classList.add('hidden');

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeMovementModal() {
            document.getElementById('movementModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        let transferSourceBalance = 0;
        const allCashboxesData = @json($cashboxes);

        function openTransferModal(cashboxId, currency, balance) {
            const modal = document.getElementById('transferModal');
            const form = document.getElementById('transferForm');
            const select = document.getElementById('transfer_to_cashbox');
            const currencyLabel = document.getElementById('transfer_currency_label');
            const currencyHint = document.getElementById('transfer_currency_hint');
            const errorMsg = document.getElementById('transfer_error');

            transferSourceBalance = balance;
            document.getElementById('transfer_from_id').value = cashboxId;
            currencyLabel.textContent = currency;
            currencyHint.textContent = `Solo cajas en ${currency === 'PEN' ? 'Soles' : 'Dólares'}`;

            select.innerHTML = '<option value="" disabled selected class="dark:bg-gray-950 dark:text-gray-400">Seleccionar caja destino</option>';
            allCashboxesData.forEach(cb => {
                if (cb.id !== cashboxId && cb.currency === currency) {
                    const opt = document.createElement('option');
                    opt.value = cb.id;
                    opt.className = 'dark:bg-gray-950 dark:text-white';
                    opt.textContent = `${cb.name} — ${cb.bank_name} (${currency} ${parseFloat(cb.balance).toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})})`;
                    select.appendChild(opt);
                }
            });

            document.getElementById('transfer_amount').value = '';
            document.getElementById('transfer_description').value = '';
            errorMsg.classList.add('hidden');

            form.action = "{{ route('superadmin.cashboxes.transfer') }}";

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeTransferModal() {
            document.getElementById('transferModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        const allTagsData = @json($tags ?? []);

        function openTagsModal() {
            const modal = document.getElementById('tagsModal');
            resetTagForm();
            renderTagsList();
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeTagsModal() {
            document.getElementById('tagsModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function resetTagForm() {
            document.getElementById('tag_id').value = '';
            document.getElementById('tag_name').value = '';
            document.getElementById('tag_color').value = '#6366f1';
            document.getElementById('tag_color_hex').textContent = '#6366f1';
            document.getElementById('tagForm').action = "{{ route('superadmin.cashbox-tags.store') }}";
            document.getElementById('tagForm').method = 'POST';
            document.getElementById('tag_method').value = '';
            document.getElementById('tagModalTitle').textContent = 'Etiquetas';
            document.getElementById('tagSubmitBtn').textContent = 'Guardar Etiqueta';
        }

        function editTag(id, name, color) {
            document.getElementById('tag_id').value = id;
            document.getElementById('tag_name').value = name;
            document.getElementById('tag_color').value = color;
            document.getElementById('tag_color_hex').textContent = color;
            document.getElementById('tagForm').action = "{{ route('superadmin.cashbox-tags.update', ['cajas_etiqueta' => '__ID__']) }}".replace('__ID__', id);
            document.getElementById('tagForm').method = 'POST';
            document.getElementById('tag_method').value = 'PUT';
            document.getElementById('tagModalTitle').textContent = 'Editar Etiqueta';
            document.getElementById('tagSubmitBtn').textContent = 'Editar Etiqueta';
        }

        function deleteTag(id) {
            const tag = allTagsData.find(t => t.id === id);
            if (!tag) return;
            document.getElementById('deleteTagName').textContent = `"${tag.name}"`;
            document.getElementById('confirmDeleteTagBtn').onclick = function() {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ route('superadmin.cashbox-tags.destroy', ['cajas_etiqueta' => '__ID__']) }}".replace('__ID__', id);
                form.innerHTML = `@csrf <input type="hidden" name="_method" value="DELETE">`;
                document.body.appendChild(form);
                form.submit();
            };
            document.getElementById('deleteTagModal').classList.remove('hidden');
        }

        function closeDeleteTagModal() {
            document.getElementById('deleteTagModal').classList.add('hidden');
        }

        function renderTagsList() {
            const container = document.getElementById('tagsList');
            if (allTagsData.length === 0) {
                container.innerHTML = '<p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center py-8">No hay etiquetas creadas</p>';
                return;
            }
            container.innerHTML = allTagsData.map(tag => `
                <div class="flex items-center justify-between py-3 border-b border-gray-50 dark:border-gray-900">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full" style="background-color: ${tag.color}"></div>
                        <span class="text-sm font-bold text-gray-900 dark:text-white">${tag.name}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="editTag(${tag.id}, '${tag.name.replace(/'/g, "\\'")}', '${tag.color}')"
                            class="text-[9px] font-black uppercase tracking-widest text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                            Editar
                        </button>
                        <button type="button" onclick="deleteTag(${tag.id})"
                            class="text-[9px] font-black uppercase tracking-widest text-red-400 hover:text-red-600 transition-colors">
                            Eliminar
                        </button>
                    </div>
                </div>
            `).join('');
        }

        document.getElementById('tag_color').addEventListener('input', function() {
            document.getElementById('tag_color_hex').textContent = this.value;
        });

        function confirmDelete(id, movementsCount) {
            if (movementsCount > 0) {
                const modal = document.getElementById('dangerousDeleteModal');
                const confirmBtn = document.getElementById('confirmDangerousDeleteBtn');

                confirmBtn.onclick = () => {
                    document.getElementById(`delete-form-${id}`).submit();
                };

                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                const modal = document.getElementById('simpleDeleteModal');
                const confirmBtn = document.getElementById('confirmSimpleDeleteBtn');

                confirmBtn.onclick = () => {
                    document.getElementById(`delete-form-${id}`).submit();
                };

                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeDangerousDeleteModal() {
            document.getElementById('dangerousDeleteModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function closeSimpleDeleteModal() {
            document.getElementById('simpleDeleteModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function openDeleteMovementModal(movementId, description, amount, type) {
            document.getElementById('deleteMovementDesc').textContent = description;
            const amountEl = document.getElementById('deleteMovementAmount');
            const isIncome = type === 'income';
            amountEl.textContent = (isIncome ? '+' : '-') + ' ' + amount;
            amountEl.className = `text-2xl font-black tabular-nums mb-4 ${isIncome ? 'text-green-500' : 'text-red-500'}`;
            document.getElementById('deleteMovementForm').action = `{{ route('superadmin.cashboxes.delete-movement', ['movement' => '__ID__']) }}`.replace('__ID__', movementId);
            document.getElementById('deleteMovementModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteMovementModal() {
            document.getElementById('deleteMovementModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function renderCodesList(movements) {
            const codesList = document.getElementById('codesList');
            const codesPagination = document.getElementById('codesPagination');
            codesList.innerHTML = '';
            codesPagination.innerHTML = '';

            if (movements.length === 0) {
                codesList.innerHTML = '<p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] text-center py-16">Sin resultados</p>';
                return;
            }

            const typeLabels = {
                'loan': 'PRÉSTAMO',
                'loan_installment': 'COBRANZA',
                'investment': 'INVERSIÓN',
                'manual': 'MANUAL',
                'transfer': 'DESPLAZAMIENTO'
            };

            const start = (codesCurrentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            const paginatedItems = movements.slice(start, end);

            paginatedItems.forEach(m => {
                const isIncome = m.type === 'income';
                const dateObj = new Date(m.date);
                const date = dateObj.toLocaleDateString('es-PE', { day: '2-digit', month: 'short' });
                const time = dateObj.toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit' });
                const displayType = typeLabels[m.reference_type] || 'OPERACIÓN';
                const tagHtml = m.tag ? ` <span class="inline-flex items-center gap-1 ml-1"><span class="w-1.5 h-1.5 rounded-full inline-block" style="background-color:${m.tag.color}"></span><span style="color:${m.tag.color}">${m.tag.name}</span></span>` : '';

                codesList.insertAdjacentHTML('beforeend', `
                    <div class="flex items-center justify-between py-3 md:py-4 border-b border-gray-100 dark:border-gray-900 last:border-0">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs md:text-sm font-bold text-gray-900 dark:text-white truncate">${m.description}</p>
                            <div class="flex flex-wrap items-center gap-1 md:gap-2 mt-1">
                                <span class="text-[8px] md:text-[9px] font-black text-indigo-600 dark:text-indigo-400 tracking-widest">${m.code || '—'}</span>
                                <span class="text-[7px] md:text-[8px] font-bold text-gray-400 uppercase tracking-[0.15em] md:tracking-[0.2em]">${displayType}${tagHtml}</span>
                                <span class="text-[7px] md:text-[8px] text-gray-400">${date} ${time}</span>
                            </div>
                        </div>
                        <p class="text-xs md:text-sm font-black tabular-nums ml-2 md:ml-4 flex-shrink-0 ${isIncome ? 'text-green-500' : 'text-red-500'}">
                            ${isIncome ? '+' : '-'} ${currentSymbol} ${parseFloat(m.amount).toLocaleString('en-US', { minimumFractionDigits: 2 })}
                        </p>
                    </div>
                `);
            });

            renderCodesPagination(movements);
        }

        function renderCodesPagination(movements) {
            const codesPagination = document.getElementById('codesPagination');
            const totalPages = Math.ceil(movements.length / itemsPerPage);
            if (totalPages <= 1) {
                codesPagination.innerHTML = '';
                return;
            }

            let html = '';

            html += `<button onclick="changeCodesPage(${codesCurrentPage - 1})" ${codesCurrentPage === 1 ? 'disabled' : ''} class="p-2 text-gray-400 hover:text-gray-900 dark:hover:text-white disabled:opacity-0 transition-all"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg></button>`;

            let startPage = Math.max(1, codesCurrentPage - 2);
            let endPage = Math.min(totalPages, startPage + 4);
            if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);

            for (let i = startPage; i <= endPage; i++) {
                const isActive = i === codesCurrentPage;
                html += `<button onclick="changeCodesPage(${i})" class="w-8 h-8 rounded-full text-[10px] font-black uppercase tracking-widest transition-all ${isActive ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900' : 'text-gray-400 hover:text-gray-900 dark:hover:text-white'}">${i}</button>`;
            }

            html += `<button onclick="changeCodesPage(${codesCurrentPage + 1})" ${codesCurrentPage === totalPages ? 'disabled' : ''} class="p-2 text-gray-400 hover:text-gray-900 dark:hover:text-white disabled:opacity-0 transition-all"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></button>`;

            codesPagination.innerHTML = html;
        }

        function changeCodesPage(page) {
            const totalPages = Math.ceil(codesFilteredMovements.length / itemsPerPage);
            if (page < 1 || page > totalPages) return;
            codesCurrentPage = page;
            renderCodesList(codesFilteredMovements);
            document.getElementById('codesList').scrollTop = 0;
        }

        function openCodesModal() {
            document.getElementById('codeSearchInput').value = '';
            codesCurrentPage = 1;
            codesFilteredMovements = currentMovements;
            renderCodesList(currentMovements);
            document.getElementById('codesModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function filterCodes() {
            const query = document.getElementById('codeSearchInput').value.toLowerCase().trim();
            codesCurrentPage = 1;
            if (!query) {
                codesFilteredMovements = currentMovements;
                renderCodesList(currentMovements);
                return;
            }
            codesFilteredMovements = currentMovements.filter(m =>
                (m.code && m.code.toLowerCase().includes(query)) ||
                (m.description && m.description.toLowerCase().includes(query))
            );
            renderCodesList(codesFilteredMovements);
        }

        function closeCodesModal() {
            document.getElementById('codesModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        async function exportPDF() {
            const activeCard = document.querySelector('.cashbox-card.active');
            if (!activeCard) return;

            const cashboxId = activeCard.dataset.id;
            const btn = event.target;
            const originalText = btn.textContent;
            btn.textContent = 'Generando...';
            btn.disabled = true;

            try {
                const res = await fetch(`/gestion-interna/cajas/${cashboxId}/all-movements`);
                const data = await res.json();
                const jsPDF = window.jspdf?.jsPDF || window.jsPDF;
                const doc = new jsPDF();

                const cb = data.cashbox;
                const symbol = cb.currency === 'USD' ? '$' : 'S/';
                const movements = data.movements;

                doc.setFillColor(30, 30, 30);
                doc.rect(0, 0, 210, 45, 'F');

                doc.setTextColor(255, 255, 255);
                doc.setFontSize(22);
                doc.setFont('helvetica', 'bold');
                doc.text('PrestaKash', 14, 18);

                doc.setFontSize(10);
                doc.setFont('helvetica', 'normal');
                doc.text('Reporte de Movimientos', 14, 27);

                doc.setFontSize(8);
                doc.text(`Generado: ${new Date().toLocaleDateString('es-PE', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })}`, 14, 36);

                doc.setTextColor(255, 255, 255);
                doc.setFontSize(9);
                doc.setFont('helvetica', 'bold');
                doc.text(`${cb.name}`, 140, 18);
                doc.setFont('helvetica', 'normal');
                doc.text(`${cb.bank_name}`, 140, 24);
                doc.text(`Cta: ${cb.account_number || '—'}`, 140, 30);
                doc.text(`Saldo: ${symbol} ${parseFloat(cb.balance).toLocaleString('en-US', { minimumFractionDigits: 2 })}`, 140, 36);

                let totalIncome = 0;
                let totalExpense = 0;
                movements.forEach(m => {
                    if (m.type === 'income') totalIncome += parseFloat(m.amount);
                    else totalExpense += parseFloat(m.amount);
                });

                const summaryY = 52;

                doc.setTextColor(100, 100, 100);
                doc.setFontSize(7);
                doc.setFont('helvetica', 'bold');
                doc.text('TOTAL INGRESOS', 41.5, summaryY + 7, { align: 'center' });
                doc.text('TOTAL EGRESOS', 104.5, summaryY + 7, { align: 'center' });
                doc.text('BALANCE NETO', 167.5, summaryY + 7, { align: 'center' });

                doc.setTextColor(22, 163, 74);
                doc.setFontSize(11);
                doc.text(`+ ${symbol} ${totalIncome.toLocaleString('en-US', { minimumFractionDigits: 2 })}`, 41.5, summaryY + 16, { align: 'center' });

                doc.setTextColor(220, 38, 38);
                doc.text(`- ${symbol} ${totalExpense.toLocaleString('en-US', { minimumFractionDigits: 2 })}`, 104.5, summaryY + 16, { align: 'center' });

                const net = totalIncome - totalExpense;
                doc.setTextColor(net >= 0 ? 22 : 220, net >= 0 ? 163 : 38, net >= 0 ? 74 : 38);
                doc.text(`${net >= 0 ? '+' : '-'} ${symbol} ${Math.abs(net).toLocaleString('en-US', { minimumFractionDigits: 2 })}`, 167.5, summaryY + 16, { align: 'center' });

                const typeLabels = {
                    'loan': 'Préstamo',
                    'loan_installment': 'Cobranza',
                    'investment': 'Inversión',
                    'investment_payout': 'Pago Inversor',
                    'manual': 'Manual',
                    'transfer': 'Desplazamiento'
                };

                const rows = movements.map(m => {
                    const dateObj = new Date(m.date);
                    const date = dateObj.toLocaleDateString('es-PE', { day: '2-digit', month: 'short', year: 'numeric' });
                    const time = dateObj.toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit' });
                    const type = m.type === 'income' ? 'Ingreso' : 'Egreso';
                    const displayType = typeLabels[m.reference_type] || 'Operación';
                    const amount = `${m.type === 'income' ? '+' : '-'} ${symbol} ${parseFloat(m.amount).toLocaleString('en-US', { minimumFractionDigits: 2 })}`;
                    const userName = m.user ? m.user.name : 'Sistema';

                    return [m.code || '—', date + ' ' + time, m.description, displayType, type, userName, amount];
                });

                doc.autoTable({
                    startY: summaryY + 28,
                    head: [['Código', 'Fecha', 'Descripción', 'Tipo', 'Clase', 'Registrado por', 'Monto']],
                    body: rows,
                    styles: { fontSize: 7.5, cellPadding: 3, font: 'helvetica', lineColor: [200, 200, 200], lineWidth: 0.1 },
                    headStyles: { fillColor: [30, 30, 30], textColor: 255, fontStyle: 'bold', fontSize: 8 },
                    alternateRowStyles: { fillColor: [245, 245, 245] },
                    columnStyles: {
                        0: { cellWidth: 26, fontStyle: 'bold' },
                        1: { cellWidth: 30 },
                        2: { cellWidth: 'auto' },
                        3: { cellWidth: 22 },
                        4: { cellWidth: 16 },
                        5: { cellWidth: 28 },
                        6: { cellWidth: 28, halign: 'right', fontStyle: 'bold' }
                    },
                    didParseCell: function(data) {
                        if (data.column.index === 6 && data.section === 'body') {
                            const val = data.cell.raw || '';
                            const row = data.row.raw || [];
                            const refType = row[3] || '';
                            if (refType === 'Desplazamiento') {
                                data.cell.styles.textColor = [37, 99, 235];
                            } else if (val.startsWith('+')) {
                                data.cell.styles.textColor = [22, 163, 74];
                            } else if (val.startsWith('-')) {
                                data.cell.styles.textColor = [220, 38, 38];
                            }
                        }
                    },
                    didDrawPage: function(data) {
                        doc.setFillColor(30, 30, 30);
                        doc.rect(0, 287, 210, 10, 'F');
                        doc.setTextColor(255, 255, 255);
                        doc.setFontSize(7);
                        doc.text('PrestaKash — Reporte confidencial', 14, 293);
                        doc.text(`Página ${data.pageNumber}`, 196, 293, { align: 'right' });
                    }
                });

                const fileName = `movimientos_${cb.name.replace(/\s+/g, '_').toLowerCase()}_${new Date().toISOString().slice(0, 10)}.pdf`;
                doc.save(fileName);
            } catch (e) {
                console.error('Error generando PDF:', e);
            } finally {
                btn.textContent = originalText;
                btn.disabled = false;
            }
        }

        async function exportExcel() {
            const activeCard = document.querySelector('.cashbox-card.active');
            if (!activeCard) return;

            const cashboxId = activeCard.dataset.id;
            const btn = event.target;
            const originalText = btn.textContent;
            btn.textContent = 'Generando...';
            btn.disabled = true;

            try {
                const res = await fetch(`/gestion-interna/cajas/${cashboxId}/all-movements`);
                const data = await res.json();

                const cb = data.cashbox;
                const symbol = cb.currency === 'USD' ? '$' : 'S/';
                const movements = data.movements;

                const typeLabels = {
                    'loan': 'Préstamo',
                    'loan_installment': 'Cobranza',
                    'investment': 'Inversión',
                    'investment_payout': 'Pago Inversor',
                    'manual': 'Manual',
                    'transfer': 'Desplazamiento'
                };

                let totalIncome = 0;
                let totalExpense = 0;
                movements.forEach(m => {
                    if (m.type === 'income') totalIncome += parseFloat(m.amount);
                    else totalExpense += parseFloat(m.amount);
                });

                const wb = new ExcelJS.Workbook();
                wb.creator = 'PrestaKash';
                wb.created = new Date();

                const wsSummary = wb.addWorksheet('Resumen', {
                    properties: { tabColor: { argb: 'FF4F46E5' } }
                });

                wsSummary.columns = [
                    { width: 22 },
                    { width: 42 }
                ];

                const titleRow = wsSummary.addRow(['PrestaKash — Reporte de Movimientos']);
                titleRow.getCell(1).font = { size: 16, bold: true, color: { argb: 'FF4F46E5' } };
                titleRow.getCell(1).alignment = { horizontal: 'left' };
                wsSummary.mergeCells('A1:B1');

                wsSummary.addRow([]);

                const summaryItems = [
                    { label: 'Caja', value: cb.name },
                    { label: 'Banco', value: cb.bank_name },
                    { label: 'Cuenta', value: cb.account_number || '—' },
                    { label: 'Saldo Actual', value: `${symbol} ${parseFloat(cb.balance).toLocaleString('en-US', { minimumFractionDigits: 2 })}` },
                    { label: 'Moneda', value: cb.currency }
                ];

                summaryItems.forEach(item => {
                    const row = wsSummary.addRow([item.label, item.value]);
                    row.getCell(1).font = { bold: true, size: 11, color: { argb: 'FF374151' } };
                    row.getCell(1).alignment = { horizontal: 'right' };
                    row.getCell(1).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFF3F4F6' } };
                    row.getCell(2).font = { size: 11, color: { argb: 'FF111827' } };
                    row.getCell(2).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFF3F4F6' } };
                });

                wsSummary.addRow([]);

                const totalsTitle = wsSummary.addRow(['Resumen Financiero']);
                totalsTitle.getCell(1).font = { size: 13, bold: true, color: { argb: 'FF4F46E5' } };
                wsSummary.mergeCells(`A${totalsTitle.number}:B${totalsTitle.number}`);

                const incomeRow = wsSummary.addRow(['Total Ingresos', `${symbol} ${totalIncome.toLocaleString('en-US', { minimumFractionDigits: 2 })}`]);
                incomeRow.getCell(1).font = { bold: true, size: 11, color: { argb: 'FF065F46' } };
                incomeRow.getCell(1).alignment = { horizontal: 'right' };
                incomeRow.getCell(1).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFECFDF5' } };
                incomeRow.getCell(2).font = { bold: true, size: 12, color: { argb: 'FF059669' } };
                incomeRow.getCell(2).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFECFDF5' } };

                const expenseRow = wsSummary.addRow(['Total Egresos', `${symbol} ${totalExpense.toLocaleString('en-US', { minimumFractionDigits: 2 })}`]);
                expenseRow.getCell(1).font = { bold: true, size: 11, color: { argb: 'FF991B1B' } };
                expenseRow.getCell(1).alignment = { horizontal: 'right' };
                expenseRow.getCell(1).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFFEF2F2' } };
                expenseRow.getCell(2).font = { bold: true, size: 12, color: { argb: 'FFDC2626' } };
                expenseRow.getCell(2).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFFEF2F2' } };

                const netRow = wsSummary.addRow(['Balance Neto', `${symbol} ${(totalIncome - totalExpense).toLocaleString('en-US', { minimumFractionDigits: 2 })}`]);
                const isNetPositive = (totalIncome - totalExpense) >= 0;
                netRow.getCell(1).font = { bold: true, size: 11, color: { argb: isNetPositive ? 'FF065F46' : 'FF991B1B' } };
                netRow.getCell(1).alignment = { horizontal: 'right' };
                netRow.getCell(1).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: isNetPositive ? 'FFD1FAE5' : 'FFFEE2E2' } };
                netRow.getCell(1).border = { top: { style: 'medium', color: { argb: isNetPositive ? 'FF059669' : 'FFDC2626' } }, bottom: { style: 'medium', color: { argb: isNetPositive ? 'FF059669' : 'FFDC2626' } } };
                netRow.getCell(2).font = { bold: true, size: 13, color: { argb: isNetPositive ? 'FF059669' : 'FFDC2626' } };
                netRow.getCell(2).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: isNetPositive ? 'FFD1FAE5' : 'FFFEE2E2' } };
                netRow.getCell(2).border = { top: { style: 'medium', color: { argb: isNetPositive ? 'FF059669' : 'FFDC2626' } }, bottom: { style: 'medium', color: { argb: isNetPositive ? 'FF059669' : 'FFDC2626' } } };

                wsSummary.addRow([]);

                const dateRow = wsSummary.addRow(['Generado', new Date().toLocaleDateString('es-PE', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })]);
                dateRow.getCell(1).font = { size: 9, italic: true, color: { argb: 'FF9CA3AF' } };
                dateRow.getCell(2).font = { size: 9, italic: true, color: { argb: 'FF9CA3AF' } };

                const wsMovements = wb.addWorksheet('Movimientos', {
                    properties: { tabColor: { argb: 'FF10B981' } }
                });

                wsMovements.columns = [
                    { header: 'Código', key: 'code', width: 16 },
                    { header: 'Fecha', key: 'date', width: 16 },
                    { header: 'Hora', key: 'time', width: 10 },
                    { header: 'Descripción', key: 'description', width: 38 },
                    { header: 'Tipo', key: 'type', width: 16 },
                    { header: 'Clase', key: 'class', width: 12 },
                    { header: 'Registrado por', key: 'user', width: 22 },
                    { header: 'Monto', key: 'amount', width: 16 },
                    { header: 'Moneda', key: 'currency', width: 10 }
                ];

                const headerRow = wsMovements.getRow(1);
                headerRow.eachCell((cell) => {
                    cell.font = { bold: true, size: 11, color: { argb: 'FFFFFFFF' } };
                    cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF4F46E5' } };
                    cell.alignment = { horizontal: 'center', vertical: 'middle' };
                    cell.border = {
                        bottom: { style: 'medium', color: { argb: 'FF3730A3' } }
                    };
                });
                headerRow.height = 28;

                movements.forEach((m, idx) => {
                    const dateObj = new Date(m.date);
                    const date = dateObj.toLocaleDateString('es-PE', { day: '2-digit', month: 'short', year: 'numeric' });
                    const time = dateObj.toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit' });
                    const isIncome = m.type === 'income';
                    const type = isIncome ? 'Ingreso' : 'Egreso';
                    const displayType = typeLabels[m.reference_type] || 'Operación';
                    const amount = parseFloat(m.amount);
                    const userName = m.user ? m.user.name : 'Sistema';

                    const row = wsMovements.addRow({
                        code: m.code || '—',
                        date: date,
                        time: time,
                        description: m.description,
                        type: displayType,
                        class: type,
                        user: userName,
                        amount: amount,
                        currency: cb.currency
                    });

                    const isEven = idx % 2 === 0;
                    const rowFill = isEven
                        ? { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFFFFFFF' } }
                        : { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFF9FAFB' } };

                    row.eachCell((cell, colNumber) => {
                        cell.fill = rowFill;
                        cell.font = { size: 10, color: { argb: 'FF374151' } };
                        cell.alignment = { vertical: 'middle' };
                        cell.border = {
                            bottom: { style: 'thin', color: { argb: 'FFE5E7EB' } }
                        };

                        if (colNumber === 6) {
                            if (isIncome) {
                                cell.font = { size: 10, bold: true, color: { argb: 'FF059669' } };
                                cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFECFDF5' } };
                            } else {
                                cell.font = { size: 10, bold: true, color: { argb: 'FFDC2626' } };
                                cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFFEF2F2' } };
                            }
                        }

                        if (colNumber === 8) {
                            if (isIncome) {
                                cell.font = { size: 10, bold: true, color: { argb: 'FF059669' } };
                            } else {
                                cell.font = { size: 10, bold: true, color: { argb: 'FFDC2626' } };
                            }
                            cell.numFmt = '#,##0.00';
                            cell.alignment = { horizontal: 'right', vertical: 'middle' };
                        }
                    });
                });

                const totalRowNum = movements.length + 2;
                wsMovements.addRow([]);
                const totalsRow = wsMovements.addRow(['', '', '', '', '', 'TOTAL INGRESOS', '', totalIncome, cb.currency]);
                totalsRow.getCell(7).font = { bold: true, size: 10, color: { argb: 'FF059669' } };
                totalsRow.getCell(7).alignment = { horizontal: 'right' };
                totalsRow.getCell(7).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFECFDF5' } };
                totalsRow.getCell(8).font = { bold: true, size: 11, color: { argb: 'FF059669' } };
                totalsRow.getCell(8).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFECFDF5' } };
                totalsRow.getCell(8).numFmt = '#,##0.00';
                totalsRow.getCell(8).alignment = { horizontal: 'right' };

                const totalsRow2 = wsMovements.addRow(['', '', '', '', '', 'TOTAL EGRESOS', '', totalExpense, cb.currency]);
                totalsRow2.getCell(7).font = { bold: true, size: 10, color: { argb: 'FFDC2626' } };
                totalsRow2.getCell(7).alignment = { horizontal: 'right' };
                totalsRow2.getCell(7).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFFEF2F2' } };
                totalsRow2.getCell(8).font = { bold: true, size: 11, color: { argb: 'FFDC2626' } };
                totalsRow2.getCell(8).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFFEF2F2' } };
                totalsRow2.getCell(8).numFmt = '#,##0.00';
                totalsRow2.getCell(8).alignment = { horizontal: 'right' };

                const netRowM = wsMovements.addRow(['', '', '', '', '', 'BALANCE NETO', '', totalIncome - totalExpense, cb.currency]);
                const isNetPositiveM = (totalIncome - totalExpense) >= 0;
                netRowM.getCell(7).font = { bold: true, size: 10, color: { argb: isNetPositiveM ? 'FF059669' : 'FFDC2626' } };
                netRowM.getCell(7).alignment = { horizontal: 'right' };
                netRowM.getCell(7).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: isNetPositiveM ? 'FFD1FAE5' : 'FFFEE2E2' } };
                netRowM.getCell(7).border = { top: { style: 'medium', color: { argb: isNetPositiveM ? 'FF059669' : 'FFDC2626' } } };
                netRowM.getCell(8).font = { bold: true, size: 12, color: { argb: isNetPositiveM ? 'FF059669' : 'FFDC2626' } };
                netRowM.getCell(8).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: isNetPositiveM ? 'FFD1FAE5' : 'FFFEE2E2' } };
                netRowM.getCell(8).numFmt = '#,##0.00';
                netRowM.getCell(8).alignment = { horizontal: 'right' };
                netRowM.getCell(8).border = { top: { style: 'medium', color: { argb: isNetPositiveM ? 'FF059669' : 'FFDC2626' } } };

                const buffer = await wb.xlsx.writeBuffer();
                const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `movimientos_${cb.name.replace(/\s+/g, '_').toLowerCase()}_${new Date().toISOString().slice(0, 10)}.xlsx`;
                a.click();
                URL.revokeObjectURL(url);
            } catch (e) {
                console.error('Error generando Excel:', e);
            } finally {
                btn.textContent = originalText;
                btn.disabled = false;
            }
        }

        function openHistoryModal() {
            const activeCard = document.querySelector('.cashbox-card.active');
            if (!activeCard) return;

            const cashboxId = activeCard.dataset.id;
            window.currentHistoryCashboxId = cashboxId;

            document.getElementById('history_from_date').value = '';
            document.getElementById('history_type').value = '';

            fetchHistory(1);

            document.getElementById('historyModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeHistoryModal() {
            document.getElementById('historyModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function fetchHistory(page = 1) {
            const id = window.currentHistoryCashboxId;
            const fromDate = document.getElementById('history_from_date').value;
            const type = document.getElementById('history_type').value;

            const list = document.getElementById('historyList');
            const noData = document.getElementById('historyNoData');
            const pagination = document.getElementById('historyPagination');

            list.innerHTML =
                '<div class="py-12 text-center animate-pulse text-[10px] font-black text-gray-400 uppercase tracking-widest">Cargando...</div>';
            noData.classList.add('hidden');
            pagination.innerHTML = '';

            fetch(`/gestion-interna/cajas/${id}/historial?page=${page}&from_date=${fromDate}&type=${type}`)
                .then(r => r.json())
                .then(data => {
                    list.innerHTML = '';
                    if (data.data.length === 0) {
                        noData.classList.remove('hidden');
                        return;
                    }

                    data.data.forEach((m, i) => {
                        const isIncome = m.type === 'income';
                        const dateObj = new Date(m.date);
                        const date = dateObj.toLocaleDateString('es-PE', {
                            day: '2-digit',
                            month: 'short'
                        });
                        const time = dateObj.toLocaleTimeString('es-PE', {
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                        const userName = m.user ? m.user.name : 'Sistema';

                        const typeLabels = {
                            'loan': 'PRÉSTAMO',
                            'loan_installment': 'COBRANZA',
                            'investment': 'INVERSIÓN',
                            'investment_payout': 'PAGO INVERSOR',
                            'manual': 'MANUAL',
                            'transfer': 'DESPLAZAMIENTO'
                        };
                        const displayType = typeLabels[m.reference_type] || 'OPERACIÓN';
                        const isLoanRelated = m.reference_type === 'loan' || m.reference_type ===
                            'loan_installment';
                        const isInvestmentRelated = m.reference_type === 'investment' || m.reference_type === 'investment_payout';

                        let redirectUrl = '';
                        if (m.reference_type === 'loan') {
                            redirectUrl =
                                `{{ route('superadmin.loans.index') }}?filter=all&loan_id=${m.reference_id}`;
                        } else if (m.reference_type === 'loan_installment') {
                            const isInterestOnly = m.description && (m.description.includes('SOLO INTERÉS') || m.description.includes('MORA + INTERÉS'));
                            if (isInterestOnly) {
                                const ipId = m.interest_payment_id || '';
                                redirectUrl = `{{ route('superadmin.installments.index') }}?filter=paid&installment_id=${m.reference_id}&highlight_interest=true&interest_payment_id=${ipId}`;
                            } else {
                                redirectUrl = `{{ route('superadmin.installments.index') }}?filter=paid&installment_id=${m.reference_id}`;
                            }
                        } else if (m.reference_type === 'investment') {
                            redirectUrl = `/gestion-interna/inversiones/${m.reference_id}?highlight_investor=${m.investor_user_id}`;
                        } else if (m.reference_type === 'investment_payout') {
                            const payoutId = m.reference_id;
                            const oppId = m.investor_payout ? m.investor_payout.investment_opportunity_id : '';
                            const invId = m.investor_user_id || (m.investor_payout ? m.investor_payout.investor_user_id : '');
                            const monthNum = m.investor_payout ? m.investor_payout.month_number : '';
                            redirectUrl = `/gestion-interna/pagos-inversores?highlight_payout=${payoutId}&opp_id=${oppId}&inv_id=${invId}&month=${monthNum}`;
                        }

                        const tagBorderColor = m.tag ? `border-color:${m.tag.color}` : '';
                        const tagBorderClass = m.tag ? '' : (isLoanRelated || isInvestmentRelated ? '' : 'border-transparent');
                        const isDeletable = m.reference_type === 'manual' || m.reference_type === 'transfer';
                        const clickAction = isLoanRelated || isInvestmentRelated
                            ? `onclick="window.location.href='${redirectUrl}'"`
                            : (isDeletable ? `onclick="openDeleteMovementModal(${m.id}, '${m.description.replace(/'/g, "\\'")}', '${currentSymbol} ${parseFloat(m.amount).toLocaleString('en-US', { minimumFractionDigits: 2 })}', '${m.type}')"` : '');

                        const html = `
                                        <div class="movement-item px-3 py-3 md:py-4 flex items-center justify-between group transition-all duration-500 rounded-xl md:rounded-2xl border-l-4 ${isLoanRelated || isInvestmentRelated ? 'loan-related cursor-pointer' : (isDeletable ? 'cursor-pointer hover:bg-red-50 dark:hover:bg-red-500/5' : (m.tag ? '' : 'hover:bg-gray-50 dark:hover:bg-white/5'))} ${tagBorderClass}"
                                             style="${tagBorderColor}"
                                             ${clickAction}>
                                            <div class="flex items-center gap-2 md:gap-8 min-w-0 flex-1">
                                                <div class="flex flex-col items-center w-8 md:w-12 flex-shrink-0">
                                                    <span class="text-[7px] md:text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest tabular-nums leading-none">${date}</span>
                                                    <span class="text-[6px] md:text-[7px] font-bold text-gray-400 uppercase tracking-widest tabular-nums mt-0.5 md:mt-1">${time}</span>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <div class="flex items-center gap-1 md:gap-2">
                                                        <p class="text-xs md:text-base font-bold text-gray-900 dark:text-white tracking-tight leading-tight truncate">${m.description}</p>
                                                        ${(m.reference_type === 'loan' && m.loan) || (m.reference_type === 'loan_installment' && m.installment) ? `
                                                            <button onclick="event.stopPropagation(); showLoanDetailFromHistory(${m.id}, ${JSON.stringify(m).replace(/'/g, "&apos;")})" class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors flex-shrink-0">
                                                                <svg class="w-3 md:w-3.5 h-3 md:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                            </button>
                                                        ` : (m.reference_type === 'investment' && m.investment ? `
                                                            <button onclick="event.stopPropagation(); showInvestmentDetailFromHistory(${m.id}, ${JSON.stringify(m).replace(/'/g, "&apos;")})" class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors flex-shrink-0">
                                                                <svg class="w-3 md:w-3.5 h-3 md:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                            </button>
                                                        ` : (m.reference_type === 'investment_payout' ? `
                                                            <button onclick="event.stopPropagation(); window.location.href='${redirectUrl}'" class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors flex-shrink-0">
                                                                <svg class="w-3 md:w-3.5 h-3 md:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                            </button>
                                                        ` : ''))}
                                                    </div>
                                                    <p class="text-[7px] md:text-[8px] text-gray-400 font-black uppercase tracking-[0.15em] md:tracking-[0.2em] mt-0.5 md:mt-1 leading-none truncate">
                                                        ${displayType}${m.tag ? ` <span class="inline-flex items-center gap-1 ml-1"><span class="w-1.5 h-1.5 rounded-full inline-block" style="background-color:${m.tag.color}"></span><span style="color:${m.tag.color}">${m.tag.name}</span></span>` : ''} • <span class="text-gray-600 dark:text-gray-300 font-bold">${userName}</span>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-right ml-2 flex-shrink-0">
                                                <p class="text-xs md:text-base font-black tabular-nums ${isIncome ? 'text-green-500' : 'text-red-500'} leading-none">
                                                    ${isIncome ? '+' : '-'} ${currentSymbol} ${parseFloat(m.amount).toLocaleString('en-US', { minimumFractionDigits: 2 })}
                                                </p>
                                            </div>
                                        </div>
                                    `;
                        list.insertAdjacentHTML('beforeend', html);
                    });

                    renderHistoryPagination(data);
                });
        }

        function renderHistoryPagination(data) {
            const pagination = document.getElementById('historyPagination');
            if (data.last_page <= 1) return;

            let html = '';
            // Prev
            html +=
                `<button onclick="fetchHistory(${data.current_page - 1})" ${data.current_page === 1 ? 'disabled' : ''} class="p-2 text-gray-400 hover:text-gray-900 dark:hover:text-white disabled:opacity-0 transition-all"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg></button>`;

            // Current / Total
            html +=
                `<span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">${data.current_page} / ${data.last_page}</span>`;

            // Next
            html +=
                `<button onclick="fetchHistory(${data.current_page + 1})" ${data.current_page === data.last_page ? 'disabled' : ''} class="p-2 text-gray-400 hover:text-gray-900 dark:hover:text-white disabled:opacity-0 transition-all"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></button>`;

            pagination.innerHTML = html;
        }

        function showLoanDetailFromHistory(movementId, m) {
            const modal = document.getElementById('loanDetailModal');
            const content = document.getElementById('loanDetailContent');
            const title = document.querySelector('#loanDetailModal h3');

            if (m.reference_type === 'loan' && m.loan) {
                const loan = m.loan;
                const currencySymbol = loan.currency === 'USD' ? '$' : 'S/';
                title.textContent = 'Detalle de Aprobación';

                content.innerHTML = `
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Cliente</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${loan.user ? loan.user.name : 'N/A'}</p>
                                                </div>
                                                <div>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Monto Solicitado</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${currencySymbol} ${parseFloat(loan.amount).toLocaleString('en-US', { minimumFractionDigits: 2 })}</p>
                                                </div>
                                                <div>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Tipo de Préstamo</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white uppercase">${loan.type === 'cuotas' ? 'Cuotas' : 'Pago Único'}</p>
                                                </div>
                                                <div>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Tasa Aplicada</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${loan.interest_rate}%</p>
                                                </div>
                                                <div class="col-span-1 md:col-span-2">
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Motivo del Préstamo</p>
                                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400 italic">"${loan.reason || 'Sin motivo especificado'}"</p>
                                                </div>
                                                <div>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Banco Destino</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${loan.bank_name}</p>
                                                </div>
                                                <div>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">N° Cuenta / CCI</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${loan.account_number} ${loan.cci ? ` / ${loan.cci}` : ''}</p>
                                                </div>
                                            </div>
                                        `;
            } else if (m.reference_type === 'loan_installment' && m.installment) {
                const inst = m.installment;
                const loan = inst.loan;
                const currencySymbol = loan.currency === 'USD' ? '$' : 'S/';
                const isInterestOnly = m.description && m.description.includes('SOLO INTERÉS');
                const isTotalPayment = m.description && m.description.includes('PAGO TOTAL');
                
                title.textContent = isInterestOnly ? 'Detalle de Pago de Interés' : (isTotalPayment ? 'Detalle de Pago Total' : 'Detalle de Cobranza');

                if (isInterestOnly) {
                    content.innerHTML = `
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Cliente</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${loan.user ? loan.user.name : 'N/A'}</p>
                                                </div>
                                                <div>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Tipo de Pago</p>
                                                    <p class="text-sm font-bold text-amber-600 dark:text-amber-400 uppercase">Solo Interés</p>
                                                </div>
                                                <div>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Monto del Interés</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${currencySymbol} ${parseFloat(inst.interest).toLocaleString('en-US', { minimumFractionDigits: 2 })}</p>
                                                </div>
                                                <div>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Capital de Cuota</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${currencySymbol} ${parseFloat(inst.capital).toLocaleString('en-US', { minimumFractionDigits: 2 })}</p>
                                                </div>
                                                <div>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">N° de Cuota</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">Cuota #${inst.installment_number}</p>
                                                </div>
                                                <div>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Código Préstamo</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${loan.code}</p>
                                                </div>
                                                <div>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Banco Receptor</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${loan.bank_name}</p>
                                                </div>
                                                <div>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Fecha de Pago</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${new Date(m.date).toLocaleDateString('es-PE', { day: '2-digit', month: 'long', year: 'numeric' })}</p>
                                                </div>
                                            </div>
                                        `;
                } else if (isTotalPayment) {
                    content.innerHTML = `
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Cliente</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${loan.user ? loan.user.name : 'N/A'}</p>
                                                </div>
                                                <div>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Tipo de Pago</p>
                                                    <p class="text-sm font-bold text-green-600 dark:text-green-400 uppercase">Pago Total</p>
                                                </div>
                                                <div>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Monto Total Pagado</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${currencySymbol} ${parseFloat(m.amount).toLocaleString('en-US', { minimumFractionDigits: 2 })}</p>
                                                </div>
                                                <div>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">N° de Cuota</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">Cuota #${inst.installment_number}</p>
                                                </div>
                                                <div>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Código Préstamo</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${loan.code}</p>
                                                </div>
                                                <div>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Banco Receptor</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${loan.bank_name}</p>
                                                </div>
                                                <div>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Fecha de Pago</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${new Date(m.date).toLocaleDateString('es-PE', { day: '2-digit', month: 'long', year: 'numeric' })}</p>
                                                </div>
                                            </div>
                                        `;
                } else {
                    content.innerHTML = `
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Cliente</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${loan.user ? loan.user.name : 'N/A'}</p>
                                                </div>
                                                <div>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Monto de Cuota</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${currencySymbol} ${parseFloat(inst.amount).toLocaleString('en-US', { minimumFractionDigits: 2 })}</p>
                                                </div>
                                                <div>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">N° de Cuota</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">Cuota #${inst.installment_number}</p>
                                                </div>
                                                <div>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Código Préstamo</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${loan.code}</p>
                                                </div>
                                                <div>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Banco Receptor</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${loan.bank_name}</p>
                                                </div>
                                                <div>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Fecha de Pago</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${new Date(m.date).toLocaleDateString('es-PE', { day: '2-digit', month: 'long', year: 'numeric' })}</p>
                                                </div>
                                            </div>
                                        `;
                }
            } else {
                return;
            }

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function showInvestmentDetailFromHistory(movementId, m) {
            if (!m || !m.investment) return;

            const modal = document.getElementById('loanDetailModal');
            const content = document.getElementById('loanDetailContent');
            const title = document.querySelector('#loanDetailModal h3');
            const inv = m.investment;
            const currencySymbol = inv.currency === 'USD' ? '$' : 'S/';

            title.textContent = 'Detalle de Inversión';

            content.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Oportunidad</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">${inv.title}</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Monto Objetivo</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">${currencySymbol} ${parseFloat(inv.target_amount).toLocaleString('en-US', { minimumFractionDigits: 2 })}</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Retorno Mensual</p>
                        <p class="text-sm font-bold text-green-600 dark:text-green-400">${inv.monthly_return_rate}%</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Plazo</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">${inv.term_months} meses</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Estado</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white uppercase">${inv.status === 'open' ? 'Abierta' : inv.status === 'funded' ? 'Financiada' : inv.status === 'closed' ? 'Cerrada' : 'Cancelada'}</p>
                    </div>
                    ${inv.description ? `<div class="md:col-span-2"><p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Descripción</p><p class="text-sm font-medium text-gray-600 dark:text-gray-400 italic">"${inv.description}"</p></div>` : ''}
                </div>
            `;

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function showInvestmentDetail(movementId) {
            const movement = currentMovements.find(m => m.id === movementId);
            if (!movement || !movement.investment) return;

            const modal = document.getElementById('loanDetailModal');
            const content = document.getElementById('loanDetailContent');
            const title = document.querySelector('#loanDetailModal h3');
            const inv = movement.investment;
            const currencySymbol = inv.currency === 'USD' ? '$' : 'S/';

            title.textContent = 'Detalle de Inversión';

            content.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Oportunidad</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">${inv.title}</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Monto Objetivo</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">${currencySymbol} ${parseFloat(inv.target_amount).toLocaleString('en-US', { minimumFractionDigits: 2 })}</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Retorno Mensual</p>
                        <p class="text-sm font-bold text-green-600 dark:text-green-400">${inv.monthly_return_rate}%</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Plazo</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">${inv.term_months} meses</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Estado</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white uppercase">${inv.status === 'open' ? 'Abierta' : inv.status === 'funded' ? 'Financiada' : inv.status === 'closed' ? 'Cerrada' : 'Cancelada'}</p>
                    </div>
                    ${inv.description ? `<div class="md:col-span-2"><p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Descripción</p><p class="text-sm font-medium text-gray-600 dark:text-gray-400 italic">"${inv.description}"</p></div>` : ''}
                </div>
            `;

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function showLoanDetail(movementId) {
            const movement = currentMovements.find(m => m.id === movementId);
            if (!movement) return;

            const modal = document.getElementById('loanDetailModal');
            const content = document.getElementById('loanDetailContent');
            const title = document.querySelector('#loanDetailModal h3');

            if (movement.reference_type === 'loan' && movement.loan) {
                const loan = movement.loan;
                const currencySymbol = loan.currency === 'USD' ? '$' : 'S/';
                title.textContent = 'Detalle de Aprobación';

                content.innerHTML = `
                                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                                                <div>
                                                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Cliente</p>
                                                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${loan.user ? loan.user.name : 'N/A'}</p>
                                                                                </div>
                                                                                <div>
                                                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Monto Solicitado</p>
                                                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${currencySymbol} ${parseFloat(loan.amount).toLocaleString('en-US', { minimumFractionDigits: 2 })}</p>
                                                                                </div>
                                                                                <div>
                                                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Tipo de Préstamo</p>
                                                                                    <p class="text-sm font-bold text-gray-900 dark:text-white uppercase">${loan.type === 'cuotas' ? 'Cuotas' : 'Pago Único'}</p>
                                                                                </div>
                                                                                <div>
                                                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Tasa Aplicada</p>
                                                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${loan.interest_rate}%</p>
                                                                                </div>
                                                                                <div class="col-span-1 md:col-span-2">
                                                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Motivo del Préstamo</p>
                                                                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400 italic">"${loan.reason || 'Sin motivo especificado'}"</p>
                                                                                </div>
                                                                                <div>
                                                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Banco Destino</p>
                                                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${loan.bank_name}</p>
                                                                                </div>
                                                                                <div>
                                                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">N° Cuenta / CCI</p>
                                                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${loan.account_number} ${loan.cci ? ` / ${loan.cci}` : ''}</p>
                                                                                </div>
                                                                            </div>
                                                                        `;
            } else if (movement.reference_type === 'loan_installment' && movement.installment) {
                const inst = movement.installment;
                const loan = inst.loan;
                const currencySymbol = loan.currency === 'USD' ? '$' : 'S/';
                const isInterestOnly = movement.description && movement.description.includes('SOLO INTERÉS');
                const isTotalPayment = movement.description && movement.description.includes('PAGO TOTAL');
                
                title.textContent = isInterestOnly ? 'Detalle de Pago de Interés' : (isTotalPayment ? 'Detalle de Pago Total' : 'Detalle de Cobranza');

                if (isInterestOnly) {
                    content.innerHTML = `
                                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                                                <div>
                                                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Cliente</p>
                                                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${loan.user ? loan.user.name : 'N/A'}</p>
                                                                                </div>
                                                                                <div>
                                                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Tipo de Pago</p>
                                                                                    <p class="text-sm font-bold text-amber-600 dark:text-amber-400 uppercase">Solo Interés</p>
                                                                                </div>
                                                                                <div>
                                                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Monto del Interés</p>
                                                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${currencySymbol} ${parseFloat(inst.interest).toLocaleString('en-US', { minimumFractionDigits: 2 })}</p>
                                                                                </div>
                                                                                <div>
                                                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Capital de Cuota</p>
                                                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${currencySymbol} ${parseFloat(inst.capital).toLocaleString('en-US', { minimumFractionDigits: 2 })}</p>
                                                                                </div>
                                                                                <div>
                                                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">N° de Cuota</p>
                                                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">Cuota #${inst.installment_number}</p>
                                                                                </div>
                                                                                <div>
                                                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Código Préstamo</p>
                                                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${loan.code}</p>
                                                                                </div>
                                                                                <div>
                                                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Banco Receptor</p>
                                                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${loan.bank_name}</p>
                                                                                </div>
                                                                                <div>
                                                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Fecha de Pago</p>
                                                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${new Date(movement.date).toLocaleDateString('es-PE', { day: '2-digit', month: 'long', year: 'numeric' })}</p>
                                                                                </div>
                                                                            </div>
                                                                        `;
                } else if (isTotalPayment) {
                    content.innerHTML = `
                                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                                                <div>
                                                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Cliente</p>
                                                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${loan.user ? loan.user.name : 'N/A'}</p>
                                                                                </div>
                                                                                <div>
                                                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Tipo de Pago</p>
                                                                                    <p class="text-sm font-bold text-green-600 dark:text-green-400 uppercase">Pago Total</p>
                                                                                </div>
                                                                                <div>
                                                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Monto Total Pagado</p>
                                                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${currencySymbol} ${parseFloat(movement.amount).toLocaleString('en-US', { minimumFractionDigits: 2 })}</p>
                                                                                </div>
                                                                                <div>
                                                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">N° de Cuota</p>
                                                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">Cuota #${inst.installment_number}</p>
                                                                                </div>
                                                                                <div>
                                                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Código Préstamo</p>
                                                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${loan.code}</p>
                                                                                </div>
                                                                                <div>
                                                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Banco Receptor</p>
                                                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${loan.bank_name}</p>
                                                                                </div>
                                                                                <div>
                                                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Fecha de Pago</p>
                                                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${new Date(movement.date).toLocaleDateString('es-PE', { day: '2-digit', month: 'long', year: 'numeric' })}</p>
                                                                                </div>
                                                                            </div>
                                                                        `;
                } else {
                    content.innerHTML = `
                                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                                                <div>
                                                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Cliente</p>
                                                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${loan.user ? loan.user.name : 'N/A'}</p>
                                                                                </div>
                                                                                <div>
                                                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Monto de Cuota</p>
                                                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${currencySymbol} ${parseFloat(inst.amount).toLocaleString('en-US', { minimumFractionDigits: 2 })}</p>
                                                                                </div>
                                                                                <div>
                                                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">N° de Cuota</p>
                                                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">Cuota #${inst.installment_number}</p>
                                                                                </div>
                                                                                <div>
                                                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Código Préstamo</p>
                                                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${loan.code}</p>
                                                                                </div>
                                                                                <div>
                                                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Banco Receptor</p>
                                                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${loan.bank_name}</p>
                                                                                </div>
                                                                                <div>
                                                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Fecha de Pago</p>
                                                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${new Date(movement.date).toLocaleDateString('es-PE', { day: '2-digit', month: 'long', year: 'numeric' })}</p>
                                                                                </div>
                                                                            </div>
                                                                        `;
                }
            } else {
                return;
            }

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeLoanDetailModal() {
            document.getElementById('loanDetailModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        document.getElementById('mov_amount').addEventListener('input', function (e) {
            let value = this.value.replace(/\D/g, '');
            if (value === '') {
                this.value = '';
                return;
            }

            let cents = parseInt(value);
            let formatted = (cents / 100).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            this.value = formatted;
        });

        document.getElementById('movementForm').addEventListener('submit', function (e) {
            const amountInput = document.getElementById('mov_amount');
            const typeInput = document.getElementById('mov_type');
            const errorMsg = document.getElementById('mov_error');
            const tagSelect = document.getElementById('mov_tag');

            if (!tagSelect || !tagSelect.value) {
                e.preventDefault();
                const tagError = document.getElementById('mov_tag_error');
                if (tagError) tagError.classList.remove('hidden');
                return;
            }
            const tagError = document.getElementById('mov_tag_error');
            if (tagError) tagError.classList.add('hidden');

            // Remove commas before submit so Laravel validates numeric
            const cleanAmount = parseFloat(amountInput.value.replace(/,/g, ''));

            if (typeInput.value === 'expense' && cleanAmount > currentCashboxBalance) {
                e.preventDefault();
                errorMsg.classList.remove('hidden');
                return;
            }

            amountInput.value = cleanAmount;
            const btn = document.getElementById('movSubmitBtn');
            btn.disabled = true;
            btn.opacity = '0.5';
        });

        document.getElementById('transfer_amount').addEventListener('input', function (e) {
            let value = this.value.replace(/\D/g, '');
            if (value === '') {
                this.value = '';
                return;
            }
            const num = parseInt(value) / 100;
            this.value = num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        });

        document.getElementById('transferForm').addEventListener('submit', function (e) {
            const amountInput = document.getElementById('transfer_amount');
            const errorMsg = document.getElementById('transfer_error');
            const tagSelect = document.getElementById('transfer_tag');

            if (!tagSelect || !tagSelect.value) {
                e.preventDefault();
                const tagError = document.getElementById('transfer_tag_error');
                if (tagError) tagError.classList.remove('hidden');
                return;
            }
            const tagError = document.getElementById('transfer_tag_error');
            if (tagError) tagError.classList.add('hidden');

            const cleanAmount = parseFloat(amountInput.value.replace(/,/g, ''));

            if (cleanAmount > transferSourceBalance) {
                e.preventDefault();
                errorMsg.classList.remove('hidden');
                return;
            }

            amountInput.value = cleanAmount;
        });

        // Cashbox search
        (function() {
            const searchInput = document.getElementById('cashboxSearch');
            const clearBtn = document.getElementById('cashboxSearchClear');
            const resultsDiv = document.getElementById('cashboxSearchResults');
            if (!searchInput) return;

            const cashboxNames = [];
            cards.forEach(function(card, i) {
                const nameEl = card.querySelector('.cashbox-name');
                if (nameEl) cashboxNames.push({ name: nameEl.textContent.trim(), index: i, id: card.dataset.id });
            });

            searchInput.addEventListener('input', function() {
                const q = this.value.trim().toLowerCase();
                clearBtn.classList.toggle('hidden', !q);

                if (!q) {
                    resultsDiv.classList.add('hidden');
                    return;
                }

                const matches = cashboxNames.filter(function(c) { return c.name.toLowerCase().includes(q); });

                if (matches.length === 0) {
                    resultsDiv.innerHTML = '<div class="px-4 py-3 text-xs text-gray-400 dark:text-gray-500">Sin resultados</div>';
                } else {
                    resultsDiv.innerHTML = matches.map(function(m) {
                        return '<button type="button" data-idx="' + m.index + '" class="w-full text-left px-4 py-2.5 text-sm text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors flex items-center justify-between"><span>' + m.name + '</span><svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>';
                    }).join('');
                }
                resultsDiv.classList.remove('hidden');
            });

            resultsDiv.addEventListener('click', function(e) {
                const btn = e.target.closest('button[data-idx]');
                if (!btn) return;
                const idx = parseInt(btn.dataset.idx);
                scrollToIndex(idx);
                searchInput.value = '';
                clearBtn.classList.add('hidden');
                resultsDiv.classList.add('hidden');
            });

            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                clearBtn.classList.add('hidden');
                resultsDiv.classList.add('hidden');
                searchInput.focus();
            });

            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
                    resultsDiv.classList.add('hidden');
                }
            });
        })();
    </script>
@endsection