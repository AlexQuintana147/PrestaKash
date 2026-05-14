@extends('client.layout')

@section('title', 'Historial de Préstamos')

@section('content')
    <div class="min-h-screen px-5 md:px-10 py-8">

        <!-- Header -->
        <div class="mb-12 flex flex-col gap-4">
            <a href="{{ route('client.loans') }}"
                class="text-xs font-semibold text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors flex items-center gap-2 self-start">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver al panel
            </a>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Historial Crediticio
                </h1>
            </div>
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
            <div class="grid grid-cols-1">
                @foreach($loans as $loan)
                    <a href="{{ route('client.loans.history.show', $loan) }}"
                        class="group block py-6 lg:py-8 border-b border-gray-100 dark:border-gray-900 last:border-b-0 hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-all duration-300">
                        <div class="flex flex-col lg:flex-row gap-6 lg:gap-8 items-start lg:items-center">

                            <!-- Monto y Tipo -->
                            <div class="flex items-center gap-4 lg:gap-6 w-full lg:w-auto lg:min-w-[240px]">
                                <div
                                    class="w-12 h-12 lg:w-14 lg:h-14 flex items-center justify-center text-green-500 flex-shrink-0">
                                    <svg class="w-7 h-7 lg:w-8 lg:h-8" viewBox="0 0 489.038 489.038" fill="currentColor"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g>
                                            <path
                                                d="M486,215.481l-293.4-169.3c-4-2.3-10.6-2.3-14.6,0L3.2,147.781c-4,2.3-4,6.1,0,8.5l293.3,169.3c4.1,2.4,10.6,2.3,14.6,0
                                                                    l174.9-101.6C490,221.681,490.1,217.881,486,215.481z M333.1,286.481c-1.6-1.6-3.6-3-5.9-4.3c-14.9-8.6-39-8.6-53.9,0
                                                                    c-1.1,0.7-2.1,1.3-3.1,2.1l-197.6-114.2c1.9-0.8,3.7-1.6,5.4-2.6c14.8-8.6,14.7-22.6-0.2-31.2c-1.6-0.9-3.4-1.8-5.2-2.5l81.5-47.3
                                                                    c1.3,1,2.7,2.1,4.3,3c14.9,8.6,39.1,8.6,53.9,0c1.7-1,3.2-2,4.4-3.1l198.2,114.5c-2.6,0.9-5.1,2-7.3,3.3
                                                                    c-14.8,8.6-14.7,22.6,0.2,31.2c2.3,1.3,4.9,2.4,7.6,3.4L333.1,286.481z M357,243.281l-12.9,7.5c-2,1.1-5.2,1.2-7.2,0l-22.7-13.1
                                                                    c-2-1.2-2-3,0-4.2l12.9-7.5c2-1.1,5.2-1.2,7.2,0l22.7,13.1C359,240.281,359,242.181,357,243.281z M175,138.181l-12.9,7.5
                                                                    c-2,1.1-5.2,1.2-7.2,0l-22.7-13.1c-2-1.2-2-3,0-4.2l12.9-7.5c2-1.1,5.2-1.2,7.2,0l22.7,13.1C177,135.181,177,137.081,175,138.181z
                                                                     M466.2,267.581l22.8,13.2l-183.3,106.5l-6-3.4l-16.9-9.7L0,210.581l22.6-13.1l282.9,163.4L466.2,267.581z M283.2,431.381
                                                                    L0,267.581l22.6-13.1l283.3,163.8l160-93l22.8,13.2L306,444.581l-5.9-3.4L283.2,431.381z M302.8,152.081
                                                                    c-32.3-18.6-84.6-18.6-116.7,0c-32,18.6-31.9,48.9,0.4,67.6c32.2,18.6,84.5,18.6,116.6,0C335.2,201.081,335,170.781,302.8,152.081z
                                                                     M235.4,195.581h-22.7v-13.1v-12.7l22.8,0.1v12.7l52,0.1v13.2L235.4,195.581z" />
                                        </g>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <h3 class="text-lg lg:text-xl font-bold text-gray-900 dark:text-white truncate">
                                            {{ $loan->currency === 'USD' ? '$' : 'S/' }} {{ number_format($loan->amount, 2) }}
                                        </h3>
                                        <span
                                            class="text-[8px] lg:text-[9px] bg-black text-white dark:bg-white dark:text-black font-black uppercase tracking-widest px-1.5 py-0.5 rounded-md flex-shrink-0">
                                            {{ $loan->type === 'cuotas' ? 'Cuotas' : 'Único' }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] lg:text-xs text-gray-500 dark:text-gray-400">Solicitado el
                                        {{ $loan->created_at->format('d M, Y') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Progreso de Pagos -->
                            <div class="flex-1 w-full">
                                <div class="flex justify-between items-end mb-2">
                                    <p
                                        class="text-[9px] lg:text-[10px] text-gray-400 dark:text-gray-600 uppercase font-bold tracking-widest">
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
                                <div class="h-1.5 lg:h-2 w-full bg-gray-100 dark:bg-gray-900 rounded-full overflow-hidden">
                                    @php
                                        $percent_inst = ($paid_h / ($total_h ?: 1)) * 100;
                                    @endphp
                                    <div class="h-full bg-green-500 rounded-full transition-all duration-1000"
                                        style="width: {{ $percent_inst }}%"></div>
                                </div>
                            </div>

                            <!-- Detalles Rápidos -->
                            <div
                                class="grid grid-cols-2 sm:grid-cols-3 gap-y-4 gap-x-6 lg:gap-8 w-full lg:w-auto lg:px-8 lg:border-l lg:border-gray-100 dark:lg:border-gray-900">
                                <div>
                                    <p
                                        class="text-[9px] lg:text-[10px] text-gray-400 dark:text-gray-600 uppercase font-bold mb-0.5">
                                        Frecuencia</p>
                                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300 capitalize">
                                        {{ $loan->payment_frequency ?? 'N/A' }}
                                    </p>
                                </div>
                                <div>
                                    <p
                                        class="text-[9px] lg:text-[10px] text-gray-400 dark:text-gray-600 uppercase font-bold mb-0.5">
                                        Interés</p>
                                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                        {{ number_format($loan->interest_rate, 2) }}%
                                    </p>
                                </div>
                                <div class="col-span-2 sm:col-span-1">
                                    <p
                                        class="text-[9px] lg:text-[10px] text-gray-400 dark:text-gray-600 uppercase font-bold mb-0.5">
                                        Estado</p>
                                    <span
                                        class="text-[10px] font-black uppercase tracking-wider
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