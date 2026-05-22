@extends('client.layout')

@section('title', 'Mis Inversiones')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8 max-w-5xl">

    <div class="mb-8">
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('client.investments.index') }}" class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">Invertir</a>
            <svg class="w-3 h-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </div>
        <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-0.5">Inversiones</p>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Mis Inversiones</h1>
    </div>

    @if(session('success'))
        <div class="mb-6 flex items-center gap-2 text-sm text-green-500 font-bold bg-green-50 dark:bg-green-500/10 p-4 rounded-2xl border border-green-100 dark:border-green-500/20">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 flex items-center gap-2 text-sm text-red-500 font-bold bg-red-50 dark:bg-red-500/10 p-4 rounded-2xl border border-red-100 dark:border-red-500/20">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    @if($myInvestments->count())
        <div class="divide-y divide-gray-100 dark:divide-gray-900">
            @foreach($myInvestments as $inv)
                @if($inv->pivot->status === 'approved')
                    <a href="{{ route('client.investments.show', $inv) }}" class="block py-6 hover:opacity-80 transition-opacity cursor-pointer">
                @else
                    <div class="py-6">
                @endif
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[9px] font-mono text-gray-400">{{ $inv->code }}</span>
                                <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-widest
                                    {{ $inv->pivot->status === 'approved' ? 'bg-green-100 text-green-600 dark:bg-green-500/10 dark:text-green-400' : '' }}
                                    {{ $inv->pivot->status === 'pending' ? 'bg-yellow-100 text-yellow-600 dark:bg-yellow-500/10 dark:text-yellow-400' : '' }}
                                    {{ $inv->pivot->status === 'rejected' ? 'bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-400' : '' }}">
                                    {{ $inv->pivot->status === 'approved' ? 'Aprobada' : ($inv->pivot->status === 'pending' ? 'Pendiente' : 'Rechazada') }}
                                </span>
                                @if(in_array($inv->pivot->status, ['approved', 'rejected']))
                                    <svg class="w-4 h-4 text-purple-600 animate-swing origin-top" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                @endif
                            </div>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white">{{ $inv->title }}</h3>
                        </div>
                        <div class="flex items-center gap-6 text-right">
                            <div>
                                <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Mi Inversión</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $inv->currency === 'USD' ? '$' : 'S/' }} {{ number_format($inv->pivot->amount, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Retorno Mensual</p>
                                <p class="text-sm font-bold text-green-600 dark:text-green-400">{{ number_format($inv->pivot->amount * $inv->monthly_return_rate / 100, 2) }} {{ $inv->currency }}</p>
                            </div>
                            <div>
                                <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Fecha Solicitud</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $inv->pivot->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>
                    @if($inv->pivot->status === 'rejected')
                        <div class="mt-3 pt-3 border-t border-red-100 dark:border-red-900/30">
                            <p class="text-[10px] font-black text-red-400 dark:text-red-500 uppercase tracking-widest mb-1">Motivo del rechazo</p>
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $inv->pivot->admin_notes ?: 'No se especificó motivo.' }}</p>
                        </div>
                    @endif
                @if($inv->pivot->status === 'approved')
                    </a>
                @else
                    </div>
                @endif
            @endforeach
        </div>

        @if($myInvestments->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $myInvestments->withQueryString()->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-20">
            <svg class="w-16 h-16 mx-auto text-gray-200 dark:text-gray-800 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <p class="text-sm text-gray-400 dark:text-gray-600">Aún no tienes inversiones</p>
            <a href="{{ route('client.investments.index') }}" class="mt-4 inline-block text-sm font-bold text-indigo-600 dark:text-indigo-400 border-b-2 border-indigo-600 dark:border-indigo-400 pb-0.5 hover:opacity-70 transition-opacity">Explorar oportunidades</a>
        </div>
    @endif
</div>
@endsection
