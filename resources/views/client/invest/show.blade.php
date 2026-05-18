@extends('client.layout')

@section('title', $opportunity->title)

@section('content')
<div class="min-h-screen px-4 sm:px-6 lg:px-8 py-6 sm:py-10 max-w-4xl mx-auto">

    <div class="mb-6 sm:mb-8">
        <a href="{{ route('client.investments.index') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors group">
            <svg class="w-3 h-3 sm:w-4 sm:h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Volver a Mis Inversiones
        </a>
        <h1 class="mt-3 sm:mt-4 text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white tracking-tight leading-tight">{{ $opportunity->title }}</h1>
        <div class="flex items-center gap-2 mt-2">
            <span class="text-[10px] sm:text-xs font-mono text-gray-400">{{ $opportunity->code }}</span>
            <span class="px-2 py-0.5 text-[8px] sm:text-[9px] font-black uppercase tracking-widest bg-green-100 text-green-600 dark:bg-green-500/10 dark:text-green-400 rounded">Activa</span>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-500/10 rounded-2xl border border-red-100 dark:border-red-500/20 flex items-start gap-3">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <p class="text-sm text-red-600 dark:text-red-400 font-medium">{{ session('error') }}</p>
        </div>
    @endif

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-500/10 rounded-2xl border border-green-100 dark:border-green-500/20 flex items-start gap-3">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <p class="text-sm text-green-600 dark:text-green-400 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8">
        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl p-3 sm:p-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-blue-100 dark:bg-blue-500/10 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[9px] sm:text-[10px] text-gray-400 uppercase font-bold tracking-tight">Monto Objetivo</span>
            </div>
            <p class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($opportunity->target_amount, 2) }}</p>
        </div>

        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl p-3 sm:p-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-green-100 dark:bg-green-500/10 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <span class="text-[9px] sm:text-[10px] text-gray-400 uppercase font-bold tracking-tight">Retorno</span>
            </div>
            <p class="text-base sm:text-lg font-bold text-green-600 dark:text-green-400">{{ $opportunity->annual_return_rate }}%/mes</p>
        </div>

        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl p-3 sm:p-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-purple-100 dark:bg-purple-500/10 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[9px] sm:text-[10px] text-gray-400 uppercase font-bold tracking-tight">Plazo</span>
            </div>
            <p class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">{{ $opportunity->term_months }} meses</p>
        </div>

        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl p-3 sm:p-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-amber-100 dark:bg-amber-500/10 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <span class="text-[9px] sm:text-[10px] text-gray-400 uppercase font-bold tracking-tight">Min Inversión</span>
            </div>
            <p class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($opportunity->min_investment, 2) }}</p>
        </div>
    </div>

    <div class="mb-6 sm:mb-8">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Progreso de Financiamiento</span>
            <span class="text-xs sm:text-sm font-bold text-indigo-600 dark:text-indigo-400">{{ $opportunity->progress_percent }}%</span>
        </div>
        <div class="relative h-2 sm:h-3 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
            <div class="absolute inset-y-0 left-0 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 transition-all duration-500 ease-out" style="width: {{ min(100, $opportunity->progress_percent) }}%"></div>
            <div class="absolute inset-y-0 left-0 rounded-full bg-gradient-to-r from-indigo-300 to-purple-300 opacity-50 blur-sm transition-all duration-500 ease-out" style="width: {{ min(100, $opportunity->progress_percent) }}%"></div>
        </div>
        <div class="flex items-center justify-between mt-2">
            <span class="text-[10px] sm:text-xs text-gray-400">Recaudado: {{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($opportunity->target_amount - $opportunity->remaining_amount, 2) }}</span>
            <span class="text-[10px] sm:text-xs text-gray-400">Disponible: {{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($opportunity->remaining_amount, 2) }}</span>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl p-4 sm:p-6 mb-6 sm:mb-8 shadow-sm">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span class="text-[10px] sm:text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Descripción del Proyecto</span>
        </div>
        <p class="text-sm sm:text-base text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $opportunity->description }}</p>

        @if($opportunity->risks)
            <div class="mt-6 pt-4 sm:pt-6 border-t border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span class="text-[10px] sm:text-xs font-bold text-red-400 uppercase tracking-widest">Riesgos Asociados</span>
                </div>
                <p class="text-sm sm:text-base text-red-600 dark:text-red-400 leading-relaxed whitespace-pre-line">{{ $opportunity->risks }}</p>
            </div>
        @endif

        <div class="grid grid-cols-2 gap-4 mt-6 pt-4 sm:pt-6 border-t border-gray-100 dark:border-gray-800">
            <div class="space-y-1">
                <p class="text-[9px] sm:text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Fecha Límite</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $opportunity->deadline ? $opportunity->deadline->format('d/m/Y') : 'Sin límite' }}</p>
            </div>
            <div class="space-y-1">
                <p class="text-[9px] sm:text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Retorno Estimado</p>
                <p class="text-sm font-semibold text-green-600 dark:text-green-400">{{ $opportunity->estimated_return }}% en {{ $opportunity->term_months }} meses</p>
            </div>
        </div>
    </div>

    @if($existingRequest)
        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl p-4 sm:p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
                @if($existingRequest->pivot->status === 'pending')
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-yellow-100 dark:bg-yellow-500/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm sm:text-base font-bold text-yellow-600 dark:text-yellow-400">Solicitud Pendiente</p>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-0.5">Tu solicitud de {{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($existingRequest->pivot->amount, 2) }} está siendo revisada.</p>
                    </div>
                @elseif($existingRequest->pivot->status === 'approved')
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-green-100 dark:bg-green-500/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <p class="text-sm sm:text-base font-bold text-green-600 dark:text-green-400">Solicitud Aprobada</p>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-0.5">Tu inversión de {{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($existingRequest->pivot->amount, 2) }} ha sido aprobada.</p>
                    </div>
                @else
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-red-100 dark:bg-red-500/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <div>
                        <p class="text-sm sm:text-base font-bold text-red-600 dark:text-red-400">Solicitud Rechazada</p>
                        @if($existingRequest->pivot->admin_notes)
                            <p class="text-xs sm:text-sm text-red-500 dark:text-red-400 mt-0.5">{{ $existingRequest->pivot->admin_notes }}</p>
                        @endif
                    </div>
                @endif
            </div>

            @if($existingRequest->pivot->status === 'approved')
                @if($existingRequest->pivot->payment_status === 'confirmed')
                    <div class="p-4 sm:p-5 bg-green-50 dark:bg-green-500/10 rounded-xl border border-green-100 dark:border-green-500/20 flex items-start gap-3">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <p class="text-sm font-bold text-green-600 dark:text-green-400">Pago Confirmado</p>
                            <p class="text-xs text-green-500 dark:text-green-400/70 mt-0.5">Tu inversión está activa y generando rendimientos.</p>
                        </div>
                    </div>

                @elseif($existingRequest->pivot->payment_status === 'expired')
                    <div class="p-4 sm:p-5 bg-red-50 dark:bg-red-500/10 rounded-xl border border-red-100 dark:border-red-500/20 flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <p class="text-sm font-bold text-red-600 dark:text-red-400">Tiempo de Pago Vencido</p>
                            <p class="text-xs text-red-500 dark:text-red-400/70 mt-0.5">Contacta al administrador para reiniciar el proceso.</p>
                        </div>
                    </div>

                @elseif($existingRequest->pivot->payment_status === 'rejected')
                    <div class="p-4 sm:p-5 bg-red-50 dark:bg-red-500/10 rounded-xl border border-red-100 dark:border-red-500/20 flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <div>
                            <p class="text-sm font-bold text-red-600 dark:text-red-400">Pago Rechazado</p>
                            <p class="text-xs text-red-500 dark:text-red-400/70 mt-0.5">Verifica los datos del comprobante y vuelve a intentar.</p>
                        </div>
                    </div>

                @else
                    @php
                        $approvedAt = $existingRequest->pivot->approved_at ? \Carbon\Carbon::parse($existingRequest->pivot->approved_at) : now();
                        $deadline = $approvedAt->addDay();
                        $hoursLeft = max(0, now()->diffInHours($deadline, false));
                        $isExpired = $hoursLeft <= 0;
                    @endphp

                    @if($isExpired && !$existingRequest->pivot->payment_submitted_at)
                        <div class="p-4 sm:p-5 bg-red-50 dark:bg-red-500/10 rounded-xl border border-red-100 dark:border-red-500/20 flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <p class="text-sm font-bold text-red-600 dark:text-red-400">Tiempo de Pago Vencido</p>
                                <p class="text-xs text-red-500 dark:text-red-400/70 mt-0.5">Contacta al administrador para reiniciar el proceso.</p>
                            </div>
                        </div>

                    @else
                        <div class="p-4 sm:p-5 bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-500/10 dark:to-orange-500/10 rounded-xl border border-amber-100 dark:border-amber-500/20">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-sm font-bold text-amber-700 dark:text-amber-400">Tiempo Restante para Pagar</span>
                                </div>
                                <span class="text-lg sm:text-xl font-bold text-amber-600 dark:text-amber-400">{{ floor($hoursLeft) }}h</span>
                            </div>
                            <p class="text-xs text-amber-600 dark:text-amber-400/70">Vence: {{ $deadline->format('d/m/Y H:i') }}</p>
                        </div>

                        @if($opportunity->bank_accounts)
                            <div class="mt-6">
                                <div class="flex items-center gap-2 mb-3">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    <span class="text-[10px] sm:text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Datos para Depósito</span>
                                </div>
                                <div class="grid gap-3">
                                    @foreach($opportunity->bank_accounts as $account)
                                        <div class="p-3 sm:p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-100 dark:border-gray-800 hover:border-gray-200 dark:hover:border-gray-700 transition-colors">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white">{{ $account['bank'] }}</span>
                                                <span class="text-[9px] px-1.5 py-0.5 bg-blue-100 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded">Banco</span>
                                            </div>
                                            <div class="grid grid-cols-2 gap-2 text-xs">
                                                <div class="space-y-1">
                                                    <span class="text-gray-400">N° Cuenta</span>
                                                    <p class="font-mono font-semibold text-gray-900 dark:text-white">{{ $account['account_number'] }}</p>
                                                </div>
                                                <div class="space-y-1">
                                                    <span class="text-gray-400">CCI</span>
                                                    <p class="font-mono font-semibold text-gray-900 dark:text-white">{{ $account['cci'] }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if(!$existingRequest->pivot->payment_submitted_at)
                            <div class="mt-6 pt-4 sm:pt-6 border-t border-gray-100 dark:border-gray-800">
                                <div class="flex items-center gap-2 mb-4">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span class="text-[10px] sm:text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Subir Comprobante de Pago</span>
                                </div>

                                @if($errors->any())
                                    <div class="mb-4 p-3 bg-red-50 dark:bg-red-500/10 rounded-xl border border-red-100 dark:border-red-500/20">
                                        @foreach($errors->all() as $error)
                                            <p class="text-xs text-red-600 dark:text-red-400">{{ $error }}</p>
                                        @endforeach
                                    </div>
                                @endif

                                <form action="{{ route('client.investments.upload-voucher', [$opportunity, auth()->id()]) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <div class="space-y-4">
                                        <div class="flex gap-2 border-b border-gray-200 dark:border-gray-800">
                                            <button type="button" onclick="selectPaymentMethod('transfer')" id="btn-transfer"
                                                class="px-4 py-2 text-sm font-bold text-gray-900 dark:text-white border-b-2 border-green-500 transition-colors">
                                                Transferencia / Depósito
                                            </button>
                                            <button type="button" onclick="selectPaymentMethod('cash')" id="btn-cash"
                                                class="px-4 py-2 text-sm font-bold text-gray-500 dark:text-gray-400 border-b-2 border-transparent hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                                                Efectivo
                                            </button>
                                            <input type="hidden" name="payment_method" id="payment_method" value="transfer">
                                        </div>

                                        <div id="transferFields" class="space-y-4">
                                            <div>
                                                <label class="block text-[10px] sm:text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">N° de Operación</label>
                                                <input type="text" name="operation_number" id="operation_number" required
                                                       maxlength="30" pattern="\d*" inputmode="numeric"
                                                       placeholder="Ej: 123456789"
                                                       oninput="this.value=this.value.replace(/\D/g,'').slice(0,30)"
                                                       class="w-full px-3 sm:px-4 py-2.5 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-all">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] sm:text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Comprobante (imagen)</label>
                                                <label class="flex flex-col items-center justify-center w-full h-32 sm:h-40 border-2 border-dashed border-gray-200 dark:border-gray-800 rounded-xl cursor-pointer hover:border-green-500 hover:bg-green-50 dark:hover:bg-green-500/5 transition-all">
                                                    <div class="flex flex-col items-center justify-center py-4">
                                                        <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Arrastra y suelta aquí</p>
                                                        <p class="text-[10px] text-gray-400 mt-1">o haz clic para seleccionar</p>
                                                    </div>
                                                    <input type="file" name="voucher" accept="image/*" required class="hidden">
                                                </label>
                                            </div>
                                        </div>

                                        <div id="cashFields" class="hidden space-y-4">
                                            <div class="p-4 bg-amber-50 dark:bg-amber-500/10 rounded-xl border border-amber-100 dark:border-amber-500/20">
                                                <div class="flex items-start gap-2">
                                                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <p class="text-xs sm:text-sm text-amber-700 dark:text-amber-400">Sube una foto del dinero depositado como comprobante de tu pago en efectivo.</p>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] sm:text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Comprobante de Efectivo (imagen)</label>
                                                <label class="flex flex-col items-center justify-center w-full h-32 sm:h-40 border-2 border-dashed border-gray-200 dark:border-gray-800 rounded-xl cursor-pointer hover:border-green-500 hover:bg-green-50 dark:hover:bg-green-500/5 transition-all">
                                                    <div class="flex flex-col items-center justify-center py-4">
                                                        <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Foto del dinero</p>
                                                        <p class="text-[10px] text-gray-400 mt-1">o haz clic para seleccionar</p>
                                                    </div>
                                                    <input type="file" name="voucher" accept="image/*" required class="hidden">
                                                </label>
                                            </div>
                                        </div>

                                        <button type="submit" class="text-sm font-bold text-green-600 border-b-2 border-green-600 pb-0.5 hover:opacity-70 transition-opacity">
                                            Enviar Comprobante
                                        </button>
                                    </div>
                                </form>
                            </div>

                        @else
                            <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-500/10 rounded-xl border border-yellow-100 dark:border-yellow-500/20 flex items-center gap-3">
                                <svg class="w-5 h-5 text-yellow-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div>
                                    <p class="text-sm font-bold text-yellow-600 dark:text-yellow-400">Comprobante Enviado</p>
                                    <p class="text-xs text-yellow-500 dark:text-yellow-400/70 mt-0.5">Esperando verificación del administrador.</p>
                                </div>
                            </div>
                        @endif
                    @endif
                @endif

            @endif

            @if($existingRequest->pivot->status === 'rejected')
                <div class="mt-6 pt-4 sm:pt-6 border-t border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span class="text-[10px] sm:text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Intentar de Nuevo</span>
                    </div>

                    @if($errors->any())
                        <div class="mb-4 p-3 bg-red-50 dark:bg-red-500/10 rounded-xl border border-red-100 dark:border-red-500/20">
                            @foreach($errors->all() as $error)
                                <p class="text-xs text-red-600 dark:text-red-400">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('client.investments.apply', $opportunity) }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] sm:text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Monto a Invertir</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-bold text-gray-400 pointer-events-none">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}</span>
                                    <input type="text" name="amount" id="investAmount" required inputmode="decimal"
                                           placeholder="{{ number_format($opportunity->min_investment, 2) }}"
                                           class="w-full pl-8 sm:pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 rounded-xl text-base font-semibold text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-all">
                                </div>
                                <input type="hidden" id="minInvestment" value="{{ $opportunity->min_investment }}">
                                <input type="hidden" id="maxInvestment" value="{{ $opportunity->remaining_amount }}">
                                <p class="text-[10px] sm:text-xs text-gray-400 mt-2">Mínimo: {{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($opportunity->min_investment, 2) }} · Máximo: {{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($opportunity->remaining_amount, 2) }}</p>
                            </div>
                            <button type="submit" class="w-full py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Enviar Nueva Solicitud
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

    @else
        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl p-4 sm:p-6 shadow-sm">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span class="text-[10px] sm:text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Participar en esta Oportunidad</span>
            </div>

            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 dark:bg-red-500/10 rounded-xl border border-red-100 dark:border-red-500/20">
                    @foreach($errors->all() as $error)
                        <p class="text-xs sm:text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('client.investments.apply', $opportunity) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] sm:text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Monto a Invertir</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-bold text-gray-400 pointer-events-none">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}</span>
                            <input type="text" name="amount" id="investAmount" required inputmode="decimal"
                                   placeholder="{{ number_format($opportunity->min_investment, 2) }}"
                                   class="w-full pl-8 sm:pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 rounded-xl text-base font-semibold text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-all">
                        </div>
                        <input type="hidden" id="minInvestment" value="{{ $opportunity->min_investment }}">
                        <input type="hidden" id="maxInvestment" value="{{ $opportunity->remaining_amount }}">
                        <p class="text-[10px] sm:text-xs text-gray-400 mt-2">Mínimo: {{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($opportunity->min_investment, 2) }} · Máximo: {{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($opportunity->remaining_amount, 2) }}</p>
                    </div>
                    <button type="submit" class="w-full py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Enviar Solicitud de Inversión
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>

<script>
function selectPaymentMethod(method) {
    const transferFields = document.getElementById('transferFields');
    const cashFields = document.getElementById('cashFields');
    const btnTransfer = document.getElementById('btn-transfer');
    const btnCash = document.getElementById('btn-cash');
    const paymentMethodInput = document.getElementById('payment_method');
    
    if (!transferFields || !cashFields || !btnTransfer || !btnCash) return;
    
    paymentMethodInput.value = method;
    
    if (method === 'cash') {
        transferFields.classList.add('hidden');
        cashFields.classList.remove('hidden');
        btnTransfer.className = 'px-4 py-2 text-sm font-bold text-gray-500 dark:text-gray-400 border-b-2 border-transparent hover:text-gray-700 dark:hover:text-gray-300 transition-colors';
        btnCash.className = 'px-4 py-2 text-sm font-bold text-gray-900 dark:text-white border-b-2 border-green-500 transition-colors';
    } else {
        transferFields.classList.remove('hidden');
        cashFields.classList.add('hidden');
        btnTransfer.className = 'px-4 py-2 text-sm font-bold text-gray-900 dark:text-white border-b-2 border-green-500 transition-colors';
        btnCash.className = 'px-4 py-2 text-sm font-bold text-gray-500 dark:text-gray-400 border-b-2 border-transparent hover:text-gray-700 dark:hover:text-gray-300 transition-colors';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const investAmount = document.getElementById('investAmount');
    if (investAmount) {
        const minVal = parseFloat(document.getElementById('minInvestment').value);
        const maxVal = parseFloat(document.getElementById('maxInvestment').value);

        function formatAmount(num) {
            let integerPart = Math.floor(num).toString();
            let decimalPart = Math.round((num - Math.floor(num)) * 100).toString().padStart(2, '0');
            integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            return integerPart + '.' + decimalPart;
        }

        investAmount.addEventListener('input', function () {
            let oldVal = this.value;
            let val = oldVal.replace(/\D/g, '');

            if (val.length === 0) {
                val = '0.00';
            } else {
                while (val.length < 3) {
                    val = '0' + val;
                }
                let integerPart = val.slice(0, -2);
                let decimalPart = val.slice(-2);
                integerPart = parseInt(integerPart, 10).toString();
                integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                val = integerPart + '.' + decimalPart;
            }

            if (oldVal !== val) {
                this.value = val;
                this.setSelectionRange(val.length, val.length);
            }
        });

        investAmount.addEventListener('blur', function () {
            let rawNum = parseFloat(this.value.replace(/,/g, ''));
            if (isNaN(rawNum) || rawNum < minVal) {
                this.value = formatAmount(minVal);
            } else if (rawNum > maxVal) {
                this.value = formatAmount(maxVal);
            }
        });

        investAmount.closest('form')?.addEventListener('submit', function () {
            investAmount.value = investAmount.value.replace(/,/g, '');
        });
    }

    document.querySelectorAll('input[name="voucher"]').forEach(function(fileInput) {
        fileInput.addEventListener('change', function(e) {
            const label = this.closest('label');
            if (this.files.length > 0) {
                label.classList.add('border-green-500');
                label.classList.remove('border-gray-200', 'border-dashed');
            } else {
                label.classList.remove('border-green-500');
                label.classList.add('border-gray-200', 'border-dashed');
            }
        });
    });
});
</script>
@endsection