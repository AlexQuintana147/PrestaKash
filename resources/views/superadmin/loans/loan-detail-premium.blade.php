@extends('superadmin.layout')

@section('title', 'Préstamo ' . $loan->code . ' - Vista Detallada')

@php
    $cur = $loan->currency === 'USD' ? '$' : 'S/';
    $totalPaid = $loan->installments()->where('status', 'approved')->sum('amount');
    $totalPending = $loan->installments()->where('status', 'pending')->sum('amount');
    $overdueCount = $loan->installments()->where('status', 'pending')->where('due_date', '<', now()->startOfDay())->count();
    $totalInstallments = $loan->installments()->count();
    $paidInstallments = $loan->installments()->where('status', 'approved')->count();
    $pendingInstallments = $loan->installments()->where('status', 'pending')->count();
    $totalLateFees = $loan->installments()->get()->sum('late_fee');
    $nextDueDate = $loan->installments()->where('status', 'pending')->orderBy('due_date', 'asc')->first()?->due_date;
@endphp

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">

    <!-- Header Premium -->
    <div class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-violet-600 via-purple-600 to-indigo-600"></div>
        <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        
        <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
            <div class="flex items-start justify-between gap-6">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 mb-3">
                        <a href="{{ URL::previous() }}" class="text-white/70 hover:text-white transition-colors flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                        <span class="px-2.5 py-1 bg-white/20 backdrop-blur-sm rounded-lg text-[10px] font-bold text-white uppercase tracking-widest">Préstamo Completo</span>
                    </div>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white mb-2 tracking-tight">{{ $loan->code }}</h1>
                    <p class="text-white/60 text-sm">Detalle completo del préstamo con todas las cuotas e información</p>
                </div>

                <div class="flex-shrink-0 text-right hidden sm:block">
                    <div class="inline-flex flex-col items-end gap-2">
                        <span class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider
                            {{ $loan->status === 'approved' ? 'bg-emerald-400 text-emerald-950' : ($loan->status === 'paid' ? 'bg-gray-200 text-gray-800' : 'bg-amber-400 text-amber-950') }}">
                            {{ match($loan->status) {
                                'approved' => 'Activo',
                                'paid' => 'Pagado',
                                'pending' => 'Pendiente',
                                default => $loan->status
                            } }}
                        </span>
                        @if($overdueCount > 0)
                            <span class="px-3 py-1.5 bg-red-500/80 backdrop-blur-sm rounded-lg text-xs font-bold text-white flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                {{ $overdueCount }} vencida{{ $overdueCount > 1 ? 's' : '' }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6 relative z-10 pb-12">

        <!-- Stats Grid Premium -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white dark:bg-slate-800/50 backdrop-blur-sm rounded-2xl p-5 border border-white/20 shadow-xl">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Monto Total</p>
                </div>
                <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ $cur }}{{ number_format($loan->amount, 2) }}</p>
                <p class="text-[10px] text-gray-500 mt-1">{{ $loan->months }} meses · {{ ($loan->interest_rate ?? 0) * 100 }}% interés</p>
            </div>

            <div class="bg-white dark:bg-slate-800/50 backdrop-blur-sm rounded-2xl p-5 border border-white/20 shadow-xl">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pagado</p>
                </div>
                <p class="text-2xl font-black text-emerald-600 tabular-nums">{{ $cur }}{{ number_format($totalPaid, 2) }}</p>
                <p class="text-[10px] text-emerald-500 mt-1">{{ $paidInstallments }}/{{ $totalInstallments }} cuotas</p>
            </div>

            <div class="bg-white dark:bg-slate-800/50 backdrop-blur-sm rounded-2xl p-5 border border-white/20 shadow-xl">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pendiente</p>
                </div>
                <p class="text-2xl font-black text-orange-600 tabular-nums">{{ $cur }}{{ number_format($totalPending, 2) }}</p>
                <p class="text-[10px] text-orange-500 mt-1">{{ $pendingInstallments }} cuotas restantes</p>
            </div>

            <div class="bg-white dark:bg-slate-800/50 backdrop-blur-sm rounded-2xl p-5 border border-white/20 shadow-xl">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                        </svg>
                    </div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Mora Acum.</p>
                </div>
                <p class="text-2xl font-black text-red-600 tabular-nums">{{ $cur }}{{ number_format($totalLateFees, 2) }}</p>
                @if($nextDueDate)
                    <p class="text-[10px] text-red-500 mt-1">Próximo: {{ $nextDueDate->format('d/m/Y') }}</p>
                @else
                    <p class="text-[10px] text-green-500 mt-1">Sin próximos vencimientos</p>
                @endif
            </div>
        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            
            <!-- Loan Details -->
            <div class="lg:col-span-2 bg-white dark:bg-slate-800/50 backdrop-blur-sm rounded-2xl border border-white/20 shadow-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700/50 bg-gradient-to-r from-slate-50 to-transparent dark:from-slate-900/30 dark:to-transparent">
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Información del Préstamo
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div class="space-y-1">
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Tipo</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $loan->type === 'cuotas' ? 'Cuotas Fijas' : 'Pago Único' }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Moneda</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $loan->currency === 'USD' ? 'Dólares (USD)' : 'Soles (PEN)' }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Plazo</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $loan->months ?? '-' }} meses</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Tasa Interés</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ ($loan->interest_rate ?? 0) * 100 }}% mensual</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Desembolso</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $loan->disbursement_date ? \Carbon\Carbon::parse($loan->disbursement_date)->format('d/m/Y') : '-' }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Estado</p>
                            <p class="text-sm font-bold {{ $loan->in_default ? 'text-red-600' : 'text-emerald-600' }}">{{ $loan->in_default ? 'En Default' : 'Normal' }}</p>
                        </div>
                    </div>

                    @if($loan->late_fee_enabled)
                    <div class="mt-6 pt-6 border-t border-gray-100 dark:border-slate-700/50">
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider font-bold mb-3 flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Configuración de Mora
                        </p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div class="bg-orange-50 dark:bg-orange-950/20 rounded-lg p-3 text-center">
                                <p class="text-[9px] text-orange-500 uppercase font-bold">Días Gracia</p>
                                <p class="text-lg font-black text-orange-700 dark:text-orange-300">{{ $loan->grace_days ?? 0 }}</p>
                            </div>
                            <div class="bg-orange-50 dark:bg-orange-950/20 rounded-lg p-3 text-center">
                                <p class="text-[9px] text-orange-500 uppercase font-bold">Tasa Mora</p>
                                <p class="text-lg font-black text-orange-700 dark:text-orange-300">{{ ($loan->late_fee_rate ?? 0) * 100 }}%</p>
                            </div>
                            <div class="bg-orange-50 dark:bg-orange-950/20 rounded-lg p-3 text-center">
                                <p class="text-[9px] text-orange-500 uppercase font-bold">Mora Diaria</p>
                                <p class="text-lg font-black text-orange-700 dark:text-orange-300">{{ $cur }}{{ number_format($loan->late_fee_daily_amount ?? 0, 2) }}</p>
                            </div>
                            <div class="bg-orange-50 dark:bg-orange-950/20 rounded-lg p-3 text-center">
                                <p class="text-[9px] text-orange-500 uppercase font-bold">Estado</p>
                                <p class="text-lg font-black text-emerald-600">Activa</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Progress / Client Info -->
            <div class="space-y-6">
                
                <div class="bg-white dark:bg-slate-800/50 backdrop-blur-sm rounded-2xl border border-white/20 shadow-xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700/50 bg-gradient-to-r from-slate-50 to-transparent dark:from-slate-900/30 dark:to-transparent">
                        <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v4m-4-2v4m12-8V8m0 0l-4-4-4 4"/>
                            </svg>
                            Progreso
                        </h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <div class="flex justify-between text-xs mb-2">
                                <span class="font-medium text-gray-600 dark:text-gray-400">Cuotas Pagadas</span>
                                <span class="font-bold text-gray-900 dark:text-white">{{ $paidInstallments }} / {{ $totalInstallments }}</span>
                            </div>
                            <div class="h-3 bg-gray-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-emerald-500 to-emerald-400 rounded-full transition-all duration-500" style="width: {{ $totalInstallments > 0 ? round(($paidInstallments / $totalInstallments) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex justify-between text-xs mb-2">
                                <span class="font-medium text-gray-600 dark:text-gray-400">Monto Recuperado</span>
                                <span class="font-bold text-gray-900 dark:text-white">{{ $totalPaid > 0 ? round(($totalPaid / $loan->amount) * 100) : 0 }}%</span>
                            </div>
                            <div class="h-3 bg-gray-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full transition-all duration-500" style="width: {{ $loan->amount > 0 ? min(round(($totalPaid / $loan->amount) * 100), 100) : 0 }}%"></div>
                            </div>
                        </div>

                        @if($overdueCount > 0)
                        <div class="mt-4 p-3 bg-red-50 dark:bg-red-950/20 rounded-xl border border-red-200 dark:border-red-900/30">
                            <div class="flex items-center gap-2 text-red-600 dark:text-red-400">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <span class="text-xs font-bold">{{ $overdueCount }} cuota{{ $overdueCount > 1 ? 's' : '' }} vencida{{ $overdueCount > 1 ? 's' : '' }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                @if($loan->user)
                <div class="bg-white dark:bg-slate-800/50 backdrop-blur-sm rounded-2xl border border-white/20 shadow-xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700/50 bg-gradient-to-r from-slate-50 to-transparent dark:from-slate-900/30 dark:to-transparent">
                        <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Cliente
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 rounded-xl overflow-hidden ring-2 ring-purple-200 dark:ring-purple-800 flex-shrink-0">
                                <img src="{{ $loan->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($loan->user->name) . '&background=a855f7&color=fff' }}" 
                                     class="w-full h-full object-cover" alt="{{ $loan->user->name }}">
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $loan->user->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $loan->user->email }}</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <a href="mailto:{{ $loan->user->email }}" 
                               class="flex items-center gap-2 w-full px-3 py-2 bg-blue-50 hover:bg-blue-100 dark:bg-blue-950/20 dark:hover:bg-blue-950/40 rounded-lg text-xs font-semibold text-blue-600 dark:text-blue-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Enviar Email
                            </a>
                            <a href="https://wa.me/51{{ preg_replace('/\D/', '', $loan->user->phone ?? '') }}" target="_blank"
                               class="flex items-center gap-2 w-full px-3 py-2 bg-green-50 hover:bg-green-100 dark:bg-green-950/20 dark:hover:bg-green-950/40 rounded-lg text-xs font-semibold text-green-600 dark:text-green-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Installments Table Premium -->
        <div class="bg-white dark:bg-slate-800/50 backdrop-blur-sm rounded-2xl border border-white/20 shadow-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700/50 bg-gradient-to-r from-slate-50 to-transparent dark:from-slate-900/30 dark:to-transparent flex items-center justify-between">
                <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    Todas las Cuotas
                </h2>
                <span class="text-xs font-medium text-gray-400">{{ $userInstallments->total() }} cuotas en total</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/50">
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">#</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Vencimiento</th>
                            <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-500 uppercase tracking-widest">Capital</th>
                            <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-500 uppercase tracking-widest">Interés</th>
                            <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-500 uppercase tracking-widest">Cuota</th>
                            <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-500 uppercase tracking-widest">Mora</th>
                            <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-500 uppercase tracking-widest">Total</th>
                            <th class="px-6 py-3 text-center text-[10px] font-bold text-gray-500 uppercase tracking-widest">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700/50">
                        @foreach($userInstallments as $inst)
                            @php
                                $isOverdue = $inst->due_date->isPast() && $inst->status !== 'approved';
                                $isDueToday = $inst->due_date->isToday() && $inst->status !== 'approved';
                                $instTotal = $inst->amount + ($inst->late_fee ?? 0);
                            @endphp
                            <tr data-installment-id="{{ $inst->id }}"
                                class="{{ $inst->status === 'approved' ? '' : ($isOverdue ? 'bg-red-50/50 dark:bg-red-950/10' : '') }} hover:bg-gray-50 dark:hover:bg-slate-900/30 transition-colors
                                {{ $isOverdue || $isDueToday ? 'overdue-installment-row' : '' }}"
                                data-is-interest-payment="{{ isset($inst->is_interest_payment) && $inst->is_interest_payment ? 'true' : 'false' }}"
                                @if(isset($inst->is_interest_payment) && $inst->is_interest_payment && isset($inst->interest_payment))
                                data-interest-payment-id="{{ $inst->interest_payment->id }}"
                                data-paid-at="{{ $inst->interest_payment->paid_at->timestamp }}"
                                @endif>
                                <td class="px-6 py-4">
                                    <span class="inline-flex w-8 h-8 items-center justify-center rounded-lg text-xs font-black tabular-nums
                                        {{ $inst->status === 'approved' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : ($isOverdue ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-700 dark:bg-slate-700 dark:text-gray-300') }}">
                                        {{ $inst->installment_number }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium {{ $isOverdue ? 'text-red-600 dark:text-red-400 font-bold' : 'text-gray-900 dark:text-white' }}">
                                        {{ $inst->due_date->format('d/m/Y') }}
                                    </p>
                                    @if($isOverdue)
                                        <p class="text-[10px] text-red-500 mt-0.5 font-medium">{{ now()->startOfDay()->diffInDays($inst->due_date) }}d vencida</p>
                                    @elseif($isDueToday)
                                        <p class="text-[10px] text-amber-500 mt-0.5 font-medium">Hoy</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white tabular-nums">{{ $cur }}{{ number_format($inst->capital ?? 0, 2) }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white tabular-nums">{{ $cur }}{{ number_format($inst->interest ?? 0, 2) }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">{{ $cur }}{{ number_format($inst->amount, 2) }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($inst->late_fee > 0)
                                        <span class="text-sm font-bold text-orange-600 dark:text-orange-400 tabular-nums">+{{ $cur }}{{ number_format($inst->late_fee, 2) }}</span>
                                    @else
                                        <span class="text-sm text-gray-300 dark:text-slate-600">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-base font-black tabular-nums {{ $isOverdue ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">{{ $cur }}{{ number_format($instTotal, 2) }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                        {{ $inst->status === 'approved' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' :
                                           ($inst->status === 'processing' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 animate-pulse' :
                                           ($inst->status === 'rejected' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' :
                                           ($isOverdue ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-gray-400'))) }}">
                                        {{ match ($inst->status) {
                                            'approved' => 'Pagado',
                                            'pending' => $isOverdue ? 'Vencida' : 'Pendiente',
                                            'processing' => 'Validar',
                                            'rejected' => 'Rechazado',
                                            default => $inst->status
                                        } }}
                                    </span>
                                    
                                    @if(in_array($inst->status, ['approved', 'processing']) && $inst->payment_method !== 'cash')
                                        <button type="button" onclick="openEditOperationModal({{ $inst->id }}, '{{ $inst->operation_number }}')"
                                            class="ml-1 text-[9px] text-purple-500 hover:text-purple-700 underline">OP</button>
                                    @endif

                                    @if($inst->can_view_payment_voucher)
                                        <button type="button" onclick="openVoucherLightbox('{{ Storage::url($inst->display_voucher_path) }}')"
                                            class="ml-1 text-[9px] text-blue-500 hover:text-blue-700 underline">Comp</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($userInstallments->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700/50 flex items-center justify-between bg-slate-50/50 dark:bg-slate-900/30">
                @if(!$userInstallments->onFirstPage())
                    <button type="button"
                        onclick="loadGroupPage(this)"
                        data-loan-id="{{ $loan->id }}"
                        data-page="{{ $userInstallments->currentPage() - 1 }}"
                        class="flex items-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Anterior
                    </button>
                @else
                    <div></div>
                @endif

                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400 tabular-nums">Página {{ $userInstallments->currentPage() }} de {{ $userInstallments->lastPage() }}</span>
                    <div class="flex items-center gap-1">
                        @for($i = 1; $i <= $userInstallments->lastPage(); $i++)
                            @if($i == $userInstallments->currentPage())
                                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-violet-600 text-white text-xs font-bold">{{ $i }}</span>
                            @elseif(abs($i - $userInstallments->currentPage()) <= 2 || $i == 1 || $i == $userInstallments->lastPage())
                                <button type="button"
                                    onclick="loadGroupPage(this)"
                                    data-loan-id="{{ $loan->id }}"
                                    data-page="{{ $i }}"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold text-gray-600 dark:text-gray-400 transition-colors">{{ $i }}</button>
                            @elseif(abs($i - $userInstallments->currentPage()) == 3)
                                <span class="text-gray-400">...</span>
                            @endif
                        @endfor
                    </div>
                </div>

                @if($userInstallments->hasMorePages())
                    <button type="button"
                        onclick="loadGroupPage(this)"
                        data-loan-id="{{ $loan->id }}"
                        data-page="{{ $userInstallments->currentPage() + 1 }}"
                        class="flex items-center gap-2 px-4 py-2.5 bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition-colors">
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

        <!-- Footer Summary -->
        <div class="mt-8 bg-gradient-to-r from-violet-600 to-indigo-600 rounded-2xl p-6 shadow-xl shadow-violet-500/25">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-white">
                <div>
                    <p class="text-[10px] uppercase tracking-widest font-bold text-white/60 mb-1">Total Préstamo</p>
                    <p class="text-xl font-black tabular-nums">{{ $cur }}{{ number_format($loan->amount, 2) }}</p>
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-widest font-bold text-white/60 mb-1">Total Pagado</p>
                    <p class="text-xl font-black tabular-nums text-emerald-300">{{ $cur }}{{ number_format($totalPaid, 2) }}</p>
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-widest font-bold text-white/60 mb-1">Mora Acumulada</p>
                    <p class="text-xl font-black tabular-nums text-orange-300">{{ $cur }}{{ number_format($totalLateFees, 2) }}</p>
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-widest font-bold text-white/60 mb-1">Saldo Restante</p>
                    <p class="text-xl font-black tabular-nums">{{ $cur }}{{ number_format(max(0, $loan->amount - $totalPaid + $totalLateFees), 2) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection