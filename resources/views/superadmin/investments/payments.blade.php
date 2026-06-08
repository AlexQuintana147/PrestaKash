@extends('superadmin.layout')

@section('title', 'Pagos a Inversores')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=JetBrains+Mono:wght@400;500;600&display=swap');

    .pk-font { font-family: 'DM Sans', sans-serif; }
    .pk-mono { font-family: 'JetBrains Mono', monospace; }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .pk-fade-up {
        animation: fadeUp 0.35s ease-out both;
    }

    .pk-card-hover:hover {
        border-color: rgba(99,102,241,0.25);
        box-shadow: 0 4px 24px -6px rgba(99,102,241,0.12);
    }

    .pk-btn-confirm {
        position: relative;
        overflow: hidden;
    }

    .pk-btn-confirm::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, transparent 40%, rgba(255,255,255,0.12));
        opacity: 0;
        transition: opacity 0.2s;
    }

    .pk-btn-confirm:hover::after { opacity: 1; }
</style>
@endpush

@section('content')
<div class="min-h-screen px-5 md:px-8 lg:px-10 py-8 max-w-6xl mx-auto pk-font">

    {{-- HEADER --}}
    <div class="flex items-start justify-between mb-8">
        <div class="pk-fade-up" style="animation-delay:0ms">
            <a href="{{ route('superadmin.investments.index') }}" class="inline-flex items-center gap-1.5 text-[11px] font-medium text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors mb-3 group">
                <svg class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Inversiones
            </a>
            <h1 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-none">Pagos a inversores</h1>
            <p class="text-[11px] text-gray-400 mt-1.5">Comprobantes pendientes de confirmación</p>
        </div>

        {{-- SUMMARY STATS --}}
        <div class="hidden md:flex items-center gap-3 pk-fade-up" style="animation-delay:80ms">
            <?php
                $totalInvestors = $payments->sum(fn($op) => $op->investors->count());
                $totalPEN = $payments->sum(function($op) {
                    return $op->currency === 'PEN' ? $op->investors->sum(fn($i) => $i->pivot->amount) : 0;
                });
                $totalUSD = $payments->sum(function($op) {
                    return $op->currency === 'USD' ? $op->investors->sum(fn($i) => $i->pivot->amount) : 0;
                });
            ?>
            @if($totalPEN > 0)
                <div class="text-right px-4 py-2 rounded-xl bg-emerald-50 dark:bg-emerald-500/8 border border-emerald-100 dark:border-emerald-500/15">
                    <p class="text-[9px] uppercase tracking-widest font-semibold text-emerald-600 dark:text-emerald-400">Total PEN</p>
                    <p class="text-sm font-bold text-emerald-700 dark:text-emerald-300 pk-mono">S/ {{ number_format($totalPEN, 2) }}</p>
                </div>
            @endif
            @if($totalUSD > 0)
                <div class="text-right px-4 py-2 rounded-xl bg-blue-50 dark:bg-blue-500/8 border border-blue-100 dark:border-blue-500/15">
                    <p class="text-[9px] uppercase tracking-widest font-semibold text-blue-600 dark:text-blue-400">Total USD</p>
                    <p class="text-sm font-bold text-blue-700 dark:text-blue-300 pk-mono">${{ number_format($totalUSD, 2) }}</p>
                </div>
            @endif
            <div class="text-right px-4 py-2 rounded-xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800">
                <p class="text-[9px] uppercase tracking-widest font-semibold text-gray-400">Pendientes</p>
                <p class="text-sm font-bold text-gray-900 dark:text-white pk-mono">{{ $totalInvestors }}</p>
            </div>
        </div>
    </div>

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="mb-6 flex items-center gap-2.5 text-xs font-medium text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-500/8 p-3.5 rounded-xl border border-emerald-200 dark:border-emerald-500/20 pk-fade-up">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-3.5 bg-red-50 dark:bg-red-500/8 rounded-xl border border-red-200 dark:border-red-500/20 text-[11px] text-red-600 dark:text-red-400 space-y-1 pk-fade-up">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    {{-- MOBILE STAT --}}
    <div class="md:hidden flex gap-2 mb-6 overflow-x-auto pb-1 pk-fade-up" style="animation-delay:60ms">
        @if($totalPEN > 0)
            <span class="shrink-0 inline-flex items-center gap-1.5 text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-500/8 text-emerald-700 dark:text-emerald-300 px-3 py-1.5 rounded-lg border border-emerald-100 dark:border-emerald-500/15 pk-mono">
                S/ {{ number_format($totalPEN, 2) }} PEN
            </span>
        @endif
        @if($totalUSD > 0)
            <span class="shrink-0 inline-flex items-center gap-1.5 text-[10px] font-semibold bg-blue-50 dark:bg-blue-500/8 text-blue-700 dark:text-blue-300 px-3 py-1.5 rounded-lg border border-blue-100 dark:border-blue-500/15 pk-mono">
                ${{ number_format($totalUSD, 2) }} USD
            </span>
        @endif
        <span class="shrink-0 inline-flex items-center gap-1.5 text-[10px] font-semibold bg-gray-50 dark:bg-gray-900 text-gray-600 dark:text-gray-400 px-3 py-1.5 rounded-lg border border-gray-100 dark:border-gray-800 pk-mono">
            {{ $totalInvestors }} pendiente{{ $totalInvestors !== 1 ? 's' : '' }}
        </span>
    </div>

    {{-- PAYMENTS LIST --}}
    @forelse($payments as $opIdx => $opportunity)
        <section class="mb-7 pk-fade-up" style="animation-delay: {{ min($opIdx * 60 + 120, 500) }}ms">

            {{-- OPPORTUNITY HEADER --}}
            <div class="flex items-center justify-between mb-3 px-1">
                <div class="flex items-center gap-3">
                    <div class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></div>
                    <h2 class="text-[13px] font-semibold text-gray-900 dark:text-white tracking-tight">{{ $opportunity->title }}</h2>
                    <span class="text-[10px] font-mono text-gray-350 dark:text-gray-600 bg-gray-100 dark:bg-gray-900 px-2 py-0.5 rounded-md">{{ $opportunity->code }}</span>
                </div>
                <span class="text-[10px] font-medium tabular-nums {{ $opportunity->currency === 'USD' ? 'text-blue-600 dark:text-blue-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                    {{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($opportunity->investors->sum(fn($i) => $i->pivot->amount), 2) }}
                    <span class="text-gray-400 font-normal ml-1">· {{ $opportunity->investors->count() }} inv.</span>
                </span>
            </div>

            {{-- INVESTOR CARDS --}}
            <div class="space-y-2.5">
                @foreach($opportunity->investors as $invIdx => $investor)
                    <article class="group bg-white dark:bg-[#0c0c0f] border border-gray-100 dark:border-gray-850 rounded-2xl p-4 md:p-5 transition-all duration-200 pk-card-hover">

                        <div class="flex flex-col lg:flex-row lg:items-center gap-4">

                            {{-- LEFT: Investor info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="shrink-0 w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-50 to-violet-50 dark:from-indigo-500/10 dark:to-violet-500/10 flex items-center justify-center ring-1 ring-inset ring-indigo-100 dark:ring-indigo-500/10">
                                        <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">{{ strtoupper(substr($investor->name, 0, 1)) }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-[13px] font-semibold text-gray-900 dark:text-white truncate">{{ $investor->name }}</p>
                                        <p class="text-[10px] text-gray-400 truncate">{{ $investor->email }}</p>
                                    </div>
                                    <span class="shrink-0 text-[9px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-md {{ $investor->pivot->payment_method === 'transfer' ? 'bg-blue-50 dark:bg-blue-500/8 text-blue-600 dark:text-blue-400 ring-1 ring-inset ring-blue-100 dark:ring-blue-500/15' : 'bg-amber-50 dark:bg-amber-500/8 text-amber-700 dark:text-amber-400 ring-1 ring-inset ring-amber-100 dark:ring-amber-500/15' }}">
                                        {{ $investor->pivot->payment_method === 'transfer' ? 'Transferencia' : 'Efectivo' }}
                                    </span>
                                </div>

                                {{-- DATA GRID --}}
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-x-4 gap-y-2">
                                    {{-- Monto --}}
                                    <div class="py-1.5 border-b border-dashed border-gray-100 dark:border-gray-850">
                                        <p class="text-[9px] uppercase tracking-widest font-semibold text-gray-400 mb-0.5">Monto</p>
                                        <p class="text-sm font-bold text-gray-900 dark:text-white pk-mono">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($investor->pivot->amount, 2) }}</p>
                                    </div>

                                    {{-- N° Operación --}}
                                    <div class="py-1.5 border-b border-dashed border-gray-100 dark:border-gray-850">
                                        <p class="text-[9px] uppercase tracking-widest font-semibold text-gray-400 mb-0.5">Operación</p>
                                        @if($investor->pivot->operation_number)
                                            <p class="text-sm font-mono font-semibold text-gray-900 dark:text-white">{{ $investor->pivot->operation_number }}</p>
                                        @else
                                            <p class="text-sm text-gray-350 dark:text-gray-600">—</p>
                                        @endif
                                    </div>

                                    {{-- Fecha envío --}}
                                    <div class="py-1.5 border-b border-dashed border-gray-100 dark:border-gray-850">
                                        <p class="text-[9px] uppercase tracking-widest font-semibold text-gray-400 mb-0.5">Enviado</p>
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 pk-mono">{{ $investor->pivot->payment_submitted_at ? \Carbon\Carbon::parse($investor->pivot->payment_submitted_at)->format('d/m/y H:i') : '—' }}</p>
                                    </div>

                                    {{-- Comprobante --}}
                                    <div class="py-1.5">
                                        <p class="text-[9px] uppercase tracking-widest font-semibold text-gray-400 mb-0.5">Comprobante</p>
                                        @if($investor->pivot->voucher_path)
                                            <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($investor->pivot->voucher_path) }}" target="_blank"
                                               class="inline-flex items-center gap-1 text-[11px] font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                Ver archivo
                                            </a>
                                        @else
                                            <p class="text-sm text-gray-350 dark:text-gray-600">Sin adjunto</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- RIGHT: Actions --}}
                            <div class="lg:w-[220px] shrink-0 flex flex-col gap-2 lg:pl-5 lg:border-l lg:border-dashed lg:border-gray-100 dark:lg:border-gray-850">

                                {{-- Confirm form --}}
                                <form action="{{ route('superadmin.investments.confirm-payment', [$opportunity, $investor->id]) }}" method="POST" class="space-y-2">
                                    @csrf
                                    <div>
                                        <label class="block text-[9px] uppercase tracking-widest font-semibold text-gray-400 mb-1">Registrar en caja</label>
                                        <select name="cashbox_id" required
                                                class="w-full text-[11px] font-medium bg-gray-50/80 dark:bg-gray-900/80 border border-gray-200 dark:border-gray-800 rounded-lg px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all appearance-none cursor-pointer">
                                            <option value="">Caja destino...</option>
                                            @foreach($cashboxes->where('currency', $opportunity->currency) as $cb)
                                                <option value="{{ $cb->id }}">{{ $cb->name }} ({{ $cb->currency }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit"
                                            class="pk-btn-confirm w-full text-[11px] font-semibold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 rounded-lg px-4 py-2.5 transition-all duration-150 shadow-sm shadow-emerald-500/20 hover:shadow-emerald-500/30">
                                        Confirmar pago
                                    </button>
                                </form>

                                {{-- Reject form --}}
                                <form action="{{ route('superadmin.investments.reject-payment', [$opportunity, $investor->id]) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="w-full text-[11px] font-semibold text-red-600 dark:text-red-400 bg-red-50/80 dark:bg-red-500/8 hover:bg-red-100 dark:hover:bg-red-500/15 active:bg-red-200 dark:active:bg-red-500/20 rounded-lg px-4 py-2.5 transition-colors border border-transparent hover:border-red-200 dark:hover:border-red-500/20">
                                        Rechazar comprobante
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @empty
        <div class="text-center py-20 pk-fade-up" style="animation-delay:120ms">
            <div class="w-16 h-16 mx-auto mb-5 rounded-2xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center">
                <svg class="w-7 h-7 text-gray-250 dark:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-sm font-medium text-gray-400">No hay pagos pendientes de revisión</p>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Los comprobantes aprobados aparecerán aquí</p>
        </div>
    @endforelse
</div>
@endsection