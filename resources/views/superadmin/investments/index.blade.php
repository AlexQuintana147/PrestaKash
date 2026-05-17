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

    <div class="mb-6">
        <div class="hidden md:flex flex-col border-b border-gray-200 dark:border-gray-800 mb-8">
            <div class="flex items-center gap-0">
                @foreach(['all' => 'Todas', 'open' => 'Abiertas', 'funded' => 'Financiadas', 'closed' => 'Cerradas'] as $key => $label)
                    <a href="{{ route('superadmin.investments.index', ['filter' => $key, 'search' => request('search')]) }}"
                       class="relative flex-shrink-0 px-4 py-3 text-sm font-medium transition-colors
                              {{ $filter === $key ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-400' }}">
                        @if($key === 'open' && $pendingRequests > 0)<svg class="w-3.5 h-3.5 text-purple-600 animate-swing origin-top inline -mt-0.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>@endif{{ $label }}
                        @if($counts[$key] > 0)
                            <span class="ml-1.5 text-xs {{ $filter === $key ? 'text-gray-500 dark:text-gray-400' : 'text-gray-300 dark:text-gray-500' }}">{{ $counts[$key] }}</span>
                        @endif
                        @if($filter === $key)
                            <span class="absolute bottom-0 left-0 right-0 h-px bg-gray-900 dark:bg-white"></span>
                        @endif
                    </a>
                @endforeach
            </div>

            <form action="{{ route('superadmin.investments.index') }}" method="GET" class="flex items-center gap-4 px-4 py-4 bg-gray-50/50 dark:bg-white/5 rounded-2xl mt-2">
                <input type="hidden" name="filter" value="{{ $filter }}">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Título o código..."
                           class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-900 dark:focus:ring-white w-64 transition-all placeholder:text-gray-400">
                </div>
            </form>
        </div>
    </div>

    @if($opportunities->count())
        <div class="space-y-3">
            @foreach($opportunities as $opp)
                <a href="{{ route('superadmin.investments.show', $opp) }}" class="block bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl p-5 hover:border-gray-300 dark:hover:border-gray-700 transition-all group">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="text-[9px] font-mono text-gray-400">{{ $opp->code }}</span>
                                <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest
                                    {{ $opp->status === 'open' ? 'bg-green-100 text-green-600 dark:bg-green-500/10 dark:text-green-400' : '' }}
                                    {{ $opp->status === 'funded' ? 'bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : '' }}
                                    {{ $opp->status === 'closed' ? 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' : '' }}
                                    {{ $opp->status === 'cancelled' ? 'bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-400' : '' }}">
                                    {{ $opp->status === 'open' ? 'Abierta' : ($opp->status === 'funded' ? 'Financiada' : ($opp->status === 'closed' ? 'Cerrada' : 'Cancelada')) }}
                                </span>
                            </div>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $opp->title }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-1">{{ $opp->description }}</p>
                        </div>

                        <div class="flex items-center gap-6 text-right">
                            <div>
                                <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Retorno Anual</p>
                                <p class="text-lg font-bold text-green-600 dark:text-green-400">{{ $opp->annual_return_rate }}%</p>
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

        <div class="mt-8">
            {{ $opportunities->withQueryString()->links() }}
        </div>
    @else
        <div class="text-center py-20">
            <svg class="w-16 h-16 mx-auto text-gray-200 dark:text-gray-800 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            <p class="text-sm text-gray-400 dark:text-gray-600">No hay oportunidades de inversión</p>
            <a href="{{ route('superadmin.investments.create') }}" class="inline-block mt-4 text-sm font-bold text-indigo-600 dark:text-indigo-400 border-b-2 border-indigo-600 dark:border-indigo-400 pb-0.5 hover:opacity-70 transition-opacity">Crear primera oportunidad</a>
        </div>
    @endif
</div>
@endsection
