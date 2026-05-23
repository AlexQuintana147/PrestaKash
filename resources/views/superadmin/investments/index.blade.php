@extends('superadmin.layout')

@section('title', 'Control de Inversiones')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8">

    <div class="flex items-end justify-between mb-8">
        <div>
            <p class="text-xs text-gray-600 dark:text-gray-600 uppercase tracking-widest mb-1">Finanzas</p>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Control de Inversiones</h1>
        </div>
        <a href="{{ route('superadmin.investments.create') }}" class="text-[10px] font-black uppercase tracking-widest text-gray-900 dark:text-white border-b border-gray-900 dark:border-white pb-1 hover:opacity-60 transition-opacity">
            Nueva Oportunidad
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 text-sm text-green-500">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 flex items-center gap-3 text-sm text-red-500">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-6 relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" id="investments-search" placeholder="Buscar por título o código..."
               class="w-full pl-10 pr-10 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-sm text-gray-900 dark:text-white placeholder:text-gray-400 focus:outline-none focus:border-gray-400 dark:focus:border-gray-600 transition-colors">
        <button type="button" id="investments-search-clear" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors hidden">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <!-- Filtros Mobile -->
    <div class="md:hidden mb-6">
        <div class="grid grid-cols-2 gap-2">
            @foreach([
                'all'    => ['label' => 'Todas', 'active' => 'bg-gray-900 text-white dark:bg-white dark:text-black', 'inactive' => 'bg-transparent text-gray-400 border-gray-200 dark:border-gray-800'],
                'open'   => ['label' => 'Abiertas', 'active' => 'bg-gray-900 text-white dark:bg-white dark:text-black', 'inactive' => 'bg-transparent text-gray-400 border-gray-200 dark:border-gray-800'],
                'funded' => ['label' => 'Financiadas', 'active' => 'bg-gray-900 text-white dark:bg-white dark:text-black', 'inactive' => 'bg-transparent text-gray-400 border-gray-200 dark:border-gray-800'],
                'closed' => ['label' => 'Cerradas', 'active' => 'bg-gray-900 text-white dark:bg-white dark:text-black', 'inactive' => 'bg-transparent text-gray-400 border-gray-200 dark:border-gray-800'],
            ] as $key => $cfg)
                <a href="{{ route('superadmin.investments.index', ['filter' => $key, 'search' => request('search')]) }}"
                   class="flex items-center justify-center gap-1.5 py-2.5 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all border
                          {{ $filter === $key ? $cfg['active'] . ' border-transparent' : $cfg['inactive'] }}">
                    {{ $cfg['label'] }}
                    @if($key === 'open' && $pendingRequests > 0)
                        <svg class="w-3 h-3 {{ $filter === 'open' ? 'text-white dark:text-black' : 'text-purple-600' }} animate-swing origin-top flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341(C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    @endif
                    @if($key === 'open' && $pendingVouchers > 0)
                        <svg class="w-3 h-3 {{ $filter === 'open' ? 'text-white dark:text-black' : 'text-green-600' }} animate-swing origin-top flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341(C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    <!-- Filtros Desktop -->
    <div class="hidden md:flex flex-col border-b border-gray-200 dark:border-gray-800 mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-0 overflow-x-auto no-scrollbar">
                @foreach(['all' => 'Todas', 'open' => 'Abiertas', 'funded' => 'Financiadas', 'closed' => 'Cerradas'] as $key => $label)
                    <a href="{{ route('superadmin.investments.index', ['filter' => $key, 'search' => request('search')]) }}"
                       class="relative flex items-center gap-2 flex-shrink-0 px-6 py-4 text-sm font-medium transition-colors
                              {{ $filter === $key ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-400' }}">
                        <span class="flex items-center gap-1">
                            @if($key === 'open' && $pendingRequests > 0)<svg class="w-3.5 h-3.5 text-purple-600 animate-swing origin-top flex-shrink-0 relative top-[1px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341(C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>@endif
                            @if($key === 'open' && $pendingVouchers > 0)<svg class="w-3.5 h-3.5 text-green-600 animate-swing origin-top flex-shrink-0 relative top-[1px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341(C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>@endif
                            {{ $label }}
                        </span>
                        @if($counts[$key] > 0)
                            <span class="ml-2 text-xs {{ $filter === $key ? 'text-gray-500 dark:text-gray-400' : 'text-gray-300 dark:text-gray-500' }}">{{ $counts[$key] }}</span>
                        @endif
                        @if($filter === $key)
                            <span class="absolute bottom-0 left-0 right-0 h-px bg-gray-900 dark:bg-white"></span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        @if($pendingRequests > 0 || $pendingVouchers > 0)
            <div class="flex items-center gap-5 px-4 py-2.5 text-[10px] text-gray-500 dark:text-gray-400">
                <span class="font-bold uppercase tracking-widest">Leyenda:</span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-purple-600 animate-swing origin-top flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341(C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    Inversión pendiente de aprobación
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-green-600 animate-swing origin-top flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341(C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    Comprobante pendiente de revisión
                </span>
            </div>
        @endif
    </div>

    @if($opportunities->count())
        <div class="divide-y divide-gray-100 dark:divide-gray-900">
            @foreach($opportunities as $opp)
                <a href="{{ route('superadmin.investments.show', $opp) }}" class="investment-row block py-6 hover:opacity-80 transition-opacity group" data-search-title="{{ strtolower($opp->title) }}" data-search-code="{{ strtolower($opp->code) }}">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 mb-1">
                                <span class="px-2 py-1 rounded-md text-xs font-mono font-bold bg-gray-900 text-white dark:bg-white dark:text-gray-900">{{ $opp->code }}</span>
                                <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-widest
                                    {{ $opp->status === 'open' ? 'bg-green-100 text-green-600 dark:bg-green-500/10 dark:text-green-400' : '' }}
                                    {{ $opp->status === 'funded' ? 'bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : '' }}
                                    {{ $opp->status === 'closed' ? 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' : '' }}
                                    {{ $opp->status === 'cancelled' ? 'bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-400' : '' }}">
                                    {{ $opp->status === 'open' ? 'Abierta' : ($opp->status === 'funded' ? 'Financiada' : ($opp->status === 'closed' ? 'Cerrada' : 'Cancelada')) }}
                                </span>
                                @if($opp->pending_investors_count > 0)
                                    <svg class="w-3.5 h-3.5 text-purple-600 animate-swing origin-top" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341(C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                @endif
                                @if($opp->pending_vouchers_count > 0)
                                    <svg class="w-3.5 h-3.5 text-green-600 animate-swing origin-top" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341(C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                @endif
                            </div>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $opp->title }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-1">{{ $opp->description }}</p>
                        </div>

                        <div class="flex items-center gap-6 text-right">
                            <div>
                                <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Retorno Mensual</p>
                                <p class="text-lg font-bold text-green-600 dark:text-green-400">{{ $opp->monthly_return_rate }}%</p>
                            </div>
                            <div>
                                <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Plazo</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $opp->term_months }} meses</p>
                            </div>
                            <div>
                                <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Progreso</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $opp->progress_percent }}%</p>
                                <div class="w-24 h-1.5 bg-gray-100 dark:bg-gray-800 rounded-full mt-1 overflow-hidden">
                                    <div class="h-full rounded-full transition-all {{ $opp->progress_percent >= 100 ? 'bg-green-500' : 'bg-indigo-500' }}" style="width: {{ min(100, $opp->progress_percent) }}%"></div>
                                </div>
                            </div>
                            <div>
                                <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Monto</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $opp->currency === 'USD' ? '$' : 'S/' }} {{ number_format($opp->target_amount, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        @if($opportunities->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $opportunities->withQueryString()->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-20">
            <svg class="w-16 h-16 mx-auto text-gray-200 dark:text-gray-800 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            <p class="text-sm text-gray-400 dark:text-gray-600">No hay oportunidades de inversión</p>
            <a href="{{ route('superadmin.investments.create') }}" class="inline-block mt-4 text-sm font-bold text-indigo-600 dark:text-indigo-400 border-b-2 border-indigo-600 dark:border-indigo-400 pb-0.5 hover:opacity-70 transition-opacity">Crear primera oportunidad</a>
        </div>
    @endif
</div>

<script>
(function() {
    const searchInput = document.getElementById('investments-search');
    const clearBtn = document.getElementById('investments-search-clear');
    if (!searchInput) return;

    function filterRows() {
        const term = searchInput.value.trim().toLowerCase();
        const rows = document.querySelectorAll('.investment-row');

        clearBtn.classList.toggle('hidden', !term);

        rows.forEach(function(row) {
            if (!term) {
                row.style.display = '';
                return;
            }
            const title = (row.dataset.searchTitle || '').toLowerCase();
            const code = (row.dataset.searchCode || '').toLowerCase();
            const match = title.includes(term) || code.includes(term);
            row.style.display = match ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterRows);

    clearBtn.addEventListener('click', function() {
        searchInput.value = '';
        filterRows();
        searchInput.focus();
    });
})();
</script>
@endsection
