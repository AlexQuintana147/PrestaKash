@extends('client.layout')

@section('title', 'Historial de Préstamos')

@section('content')
    <div class="min-h-screen px-5 md:px-10 py-8">

        <!-- Header -->
        <div class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-0.5">Finanzas</p>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Historial Crediticio
                </h1>
            </div>
            <a href="{{ route('client.loans') }}"
                class="text-xs font-semibold text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver al panel
            </a>
        </div>

        @if($loans->isEmpty())
            <div
                class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-3xl p-16 text-center shadow-sm">
                <div class="w-20 h-20 bg-gray-50 dark:bg-gray-900 rounded-full flex items-center justify-center mx-auto mb-6">
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
                    class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-green-500/20">
                    Solicitar préstamo
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6">
                @foreach($loans as $loan)
                    <a href="{{ route('client.loans.history.show', $loan) }}"
                        class="group bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-3xl p-6 md:p-8 hover:border-green-500/30 transition-all duration-300 shadow-sm">
                        <div class="flex flex-col lg:flex-row gap-8 items-start lg:items-center">

                            <!-- Monto y Tipo -->
                            <div class="flex items-center gap-6 min-w-[240px]">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-green-50 dark:bg-green-500/10 flex items-center justify-center text-green-600 dark:text-green-400 group-hover:scale-110 transition-transform duration-500">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                            {{ $loan->currency === 'USD' ? '$' : 'S/' }} {{ number_format($loan->amount, 2) }}
                                        </h3>
                                        <span
                                            class="text-[9px] bg-black text-white dark:bg-white dark:text-black font-black uppercase tracking-widest px-2 py-0.5 rounded-md">
                                            {{ $loan->type === 'cuotas' ? 'Cuotas' : 'Único' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Solicitado el
                                        {{ $loan->created_at->format('d M, Y') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Progreso de Pagos -->
                            <div class="flex-1 w-full">
                                <div class="flex justify-between items-end mb-2">
                                    <p class="text-[10px] text-gray-400 dark:text-gray-600 uppercase font-bold tracking-widest">
                                        Progreso de pago</p>
                                    <p class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                        @php
                                            $total_h = $loan->installments->count();
                                            if ($total_h === 0) {
                                                $installmentsPerMonth = match ($loan->payment_frequency) {
                                                    'diario' => 30,
                                                    'semanal' => 4,
                                                    'quincenal' => 2,
                                                    default => 1,
                                                };
                                                $total_h = $loan->months * $installmentsPerMonth;
                                            }
                                            $paid_h = $loan->installments->where('status', 'approved')->count();
                                        @endphp
                                        {{ $paid_h }} / {{ $total_h }}
                                    </p>
                                </div>
                                <div class="h-2 w-full bg-gray-100 dark:bg-gray-900 rounded-full overflow-hidden">
                                    @php
                                        $percent_inst = ($paid_h / ($total_h ?: 1)) * 100;
                                    @endphp
                                    <div class="h-full bg-green-500 rounded-full transition-all duration-1000"
                                        style="width: {{ $percent_inst }}%"></div>
                                </div>
                            </div>

                            <!-- Detalles Rápidos -->
                            <div
                                class="grid grid-cols-2 sm:grid-cols-3 gap-8 lg:px-8 lg:border-l lg:border-gray-100 dark:lg:border-gray-900">
                                <div>
                                    <p class="text-[10px] text-gray-400 dark:text-gray-600 uppercase font-bold mb-1">Frecuencia</p>
                                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300 capitalize">
                                        {{ $loan->payment_frequency ?? 'N/A' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 dark:text-gray-600 uppercase font-bold mb-1">Interés</p>
                                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                        {{ number_format($loan->interest_rate, 2) }}%
                                    </p>
                                </div>
                                <div class="hidden sm:block">
                                    <p class="text-[10px] text-gray-400 dark:text-gray-600 uppercase font-bold mb-1">Estado</p>
                                    <span class="text-[10px] font-black uppercase tracking-wider
                                                                {{ $loan->status === 'approved' ? 'text-green-500' : '' }}
                                                                {{ $loan->status === 'pending' ? 'text-yellow-600' : '' }}
                                                                {{ $loan->status === 'paid' ? 'text-blue-500' : '' }}
                                                                {{ $loan->status === 'rejected' ? 'text-red-500' : '' }}">
                                        {{ match ($loan->status) {
                        'approved' => 'Activo',
                        'pending' => 'En revisión',
                        'paid' => 'Completado',
                        'rejected' => 'Rechazado',
                        default => $loan->status
                    } }}
                                    </span>
                                </div>
                            </div>

                            <!-- Flecha -->
                            <div class="hidden lg:block">
                                <svg class="w-5 h-5 text-gray-300 dark:text-gray-700 group-hover:text-green-500 group-hover:translate-x-1 transition-all"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

    </div>
@endsection