@extends('client.layout')

@section('title', 'Gestionar Préstamos')

@section('content')
    <div class="min-h-screen px-5 md:px-10 py-8">

        <!-- Header -->
        <div class="flex items-center justify-between mb-12 gap-4 flex-nowrap">
            <div class="min-w-0 flex-1">
                <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-0.5">Panel</p>
                <h1 class="text-xl md:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white tracking-tight truncate">Gestionar Préstamos
                </h1>
            </div>

            @php
                $counterOfferLoans = $loans->where('status', 'counter_offered');
            @endphp

            @if($counterOfferLoans->isNotEmpty())
                <button type="button" onclick="showCounterOffersList({{ json_encode($counterOfferLoans->values()) }})"
                    class="flex items-center gap-2 bg-white dark:bg-gray-950 border border-purple-200 dark:border-purple-800 hover:bg-gray-50 dark:hover:bg-gray-900 text-gray-900 dark:text-white px-3 py-2 rounded-xl transition-all shadow-lg shadow-purple-500/10 dark:shadow-purple-500/20 flex-shrink-0 ml-2">
                    <svg class="w-4 h-4 text-purple-600 animate-swing origin-top flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="hidden md:inline text-xs font-bold uppercase tracking-widest">Nuevas Contra-ofertas</span>
                </button>
            @endif
        </div>

        @if(session('success'))
            <div class="mb-8 flex items-center gap-2 text-sm text-green-500">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Acciones principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-px bg-gray-200 dark:bg-gray-900 rounded-2xl overflow-hidden mb-12">

            <!-- Pedir préstamo -->
            <a href="{{ route('client.loans.create') }}"
                class="bg-white dark:bg-gray-950 p-8 flex flex-col gap-6 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                <div class="flex items-center justify-between">
                    <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" viewBox="0 0 297.085 297.085" fill="currentColor">
                        <g>
                            <g>
                                <g>
                                    <path
                                        d="M92.191,94.479c27.19,0,48.49-14.624,48.49-33.292c0-18.671-21.3-33.297-48.49-33.297s-48.49,14.625-48.49,33.297
                                                                        C43.701,79.855,65.001,94.479,92.191,94.479z M92.191,42.889c17.841,0,33.49,8.55,33.49,18.297
                                                                        c0,10.78-17.649,18.292-33.49,18.292c-19.736,0-33.49-9.641-33.49-18.292C58.701,51.439,74.351,42.889,92.191,42.889z" />
                                    <path
                                        d="M184.384,114.58c0.003-4.395,0.002-3.56-0.005-53.235c0-0.052,0.003-0.104,0.003-0.156c0-34.776-42.272-60.8-92.19-60.8
                                                                        c-49.91,0-92.118,26.001-92.188,60.715c0,0.03-0.004,0.059-0.004,0.09c0,6.757,0,165.999,0,174.7
                                                                        c0,54.355,97.154,79.21,153.876,45.362c60.097,40.099,143.209-2.281,143.209-76.763
                                                                        C297.084,143.988,240.623,101.52,184.384,114.58z M92.191,15.389c41.826,0,77.162,20.958,77.189,45.772
                                                                        c0,0.011-0.002,0.022-0.002,0.033c0,0.071-0.006,0.139-0.007,0.21c-0.204,24.729-35.468,45.575-77.181,45.575
                                                                        c-41.841,0-77.189-20.969-77.189-45.789C15.002,36.363,50.351,15.389,92.191,15.389z M14.999,94.702
                                                                        c19.105,19.158,49.821,27.277,77.192,27.277c27.1,0,58.017-8.052,77.191-27.275c0.002,4.337,0.007,20.393-0.004,24.698
                                                                        c-21.378,8.913-37.929,25.42-47.329,44.645c-50.421,12.89-107.05-10.123-107.05-42.153V94.702z M14.999,155.716
                                                                        c25.141,24.246,66.848,30.805,100.843,24.986c-3.837,14.511-4.312,30.073-0.543,45.545c-42.811,8.145-96.956-9.49-100.145-40.88
                                                                        C14.94,183.269,14.999,185.322,14.999,155.716z M14.999,235.894v-19.73c24.953,24.869,70.107,31.685,105.063,24.445
                                                                        c4.948,11.646,12.347,22.149,21.126,30.521C92.218,295.792,14.999,274.319,14.999,235.894z M158.606,266.254
                                                                        c-49.647-37.26-36.844-114.353,20.651-134.586c0.005-0.002,0.009-0.005,0.014-0.006c0.032-0.011,0.065-0.021,0.097-0.033
                                                                        c49.961-17.542,102.715,19.428,102.715,72.864C282.084,269.141,208.365,303.639,158.606,266.254z" />
                                    <path
                                        d="M204.904,143.293c-33.74,0-61.195,27.449-61.195,61.201c0,33.74,27.45,61.19,61.191,61.19
                                                                        c33.745,0,61.205-27.445,61.205-61.191C266.104,170.747,238.65,143.293,204.904,143.293z M204.904,250.683
                                                                        c-25.47,0-46.191-20.721-46.191-46.19c0-25.478,20.724-46.199,46.186-46.199c25.475,0,46.199,20.725,46.199,46.2
                                                                        C251.099,229.958,230.379,250.683,204.904,250.683z" />
                                    <path
                                        d="M208.691,200.072v-12.129c2.147,0.505,4.295,1.453,6.506,2.779c4.159,2.494,7.797-3.543,3.538-5.938
                                                                        c-2.906-1.832-6.064-3.032-9.792-3.475v-1.39c0-1.516-1.2-2.716-2.716-2.716c-1.517,0-2.78,1.2-2.78,2.716v1.327
                                                                        c-7.896,0.632-13.266,5.306-13.266,12.066c0,7.138,4.296,10.612,13.519,13.013v12.445c-3.475-0.632-6.38-2.085-9.35-4.232
                                                                        c-2.101-1.635-5.559-0.281-5.559,2.779c0,1.327,0.632,2.337,1.706,3.032c3.853,2.716,8.212,4.422,12.949,4.927v3.916
                                                                        c0,3.54,5.496,3.648,5.496,0v-3.79c8.023-0.758,13.456-5.37,13.456-12.318C222.399,206.326,218.293,202.472,208.691,200.072z
                                                                         M203.7,198.745c-4.864-1.58-6.064-3.222-6.064-5.875c0-2.78,2.021-4.927,6.064-5.307V198.745z M208.691,219.023v-11.497
                                                                        c4.863,1.516,6.254,3.222,6.254,6.001C214.945,216.56,212.734,218.581,208.691,219.023z" />
                                </g>
                            </g>
                        </g>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-1.5">Pedir un préstamo</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Solicita financiamiento rápido y
                        seguro. Elige el monto y plazo que mejor se adapte a ti.</p>
                </div>
                <span
                    class="self-start text-xs font-semibold text-green-600 dark:text-green-400 hover:underline transition-all">Solicitar
                    ahora</span>
            </a>

            <!-- Historial crediticio -->
            <a href="{{ route('client.loans.history') }}"
                class="bg-white dark:bg-gray-950 p-8 flex flex-col gap-6 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                <div class="flex items-center justify-between">
                    <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" viewBox="0 0 16 16" fill="currentColor">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M1.5 14H15v-1H2V0H1v13.5l.5.5zM3 11.5v-8l.5-.5h2l.5.5v8l-.5.5h-2l-.5-.5zm2-.5V4H4v7h1zm6-9.5v10l.5.5h2l.5-.5v-10l-.5-.5h-2l-.5.5zm2 .5v9h-1V2h1zm-6 9.5v-6l.5-.5h2l.5.5v6l-.5.5h-2l-.5-.5zm2-.5V6H8v5h1z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-1.5">Historial crediticio</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Consulta tus préstamos anteriores,
                        pagos realizados y tu estado crediticio actual.</p>
                </div>
                <span
                    class="self-start text-xs font-semibold text-green-600 dark:text-green-400 hover:underline transition-all">Ver
                    historial</span>
            </a>
        </div>

        <div class="space-y-12">
            <!-- Perfil -->
            <div>
                <div class="flex items-center justify-between mb-6">
                    <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-widest">Tu perfil de préstamos</p>
                    <a href="{{ route('client.loans.profile.edit') }}"
                        class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        Editar →
                    </a>
                </div>

                @php $pendingChange = \App\Models\ProfileChangeRequest::where('user_id', Auth::id())->where('status', 'pending')->first(); @endphp
                @if($pendingChange)
                    <div class="mb-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl px-4 py-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-xs text-amber-700 dark:text-amber-400">Tienes cambios de perfil pendientes de aprobación (solicitados el {{ $pendingChange->created_at->format('d/m/Y') }}).</p>
                    </div>
                @endif

                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-y-6">
                    @php
                        $profileFields = [
                            'Situación' => $profile->academic_status,
                            'Estado civil' => match ($profile->marital_status) {
                                'soltero' => 'Soltero/a',
                                'casado_separado' => 'Casado — bienes sep.',
                                'casado_compartido' => 'Casado — bienes comp.',
                                default => $profile->marital_status,
                            },
                            'Hijos' => $profile->has_children ? ($profile->children_count . ($profile->children_count == 1 ? ' hijo' : ' hijos')) : 'No',
                            'Trabaja' => $profile->is_employed ? 'Sí' : 'No',
                            'Ingresos' => 'S/ ' . number_format($profile->monthly_income, 2),
                            'Instagram' => $profile->instagram ?? '—',
                        ];

                        if ($profile->is_employed) {
                            if ($profile->employment_type === 'dependiente') {
                                $profileFields = array_merge($profileFields, [
                                    'Empresa' => $profile->workplace,
                                    'Cargo' => $profile->job_title,
                                ]);
                            } else {
                                $profileFields = array_merge($profileFields, [
                                    'Tipo' => ucfirst($profile->independent_type),
                                ]);
                            }
                        }

                        if ($profile->institution) {
                            $profileFields = array_merge(['Institución' => $profile->institution], $profileFields);
                        }
                        if ($profile->career) {
                            $profileFields = array_merge(['Carrera' => $profile->career], $profileFields);
                        }
                    @endphp
                    @foreach($profileFields as $label => $value)
                        <div class="border-b border-gray-100 dark:border-gray-900 pb-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ $label }}</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $value }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Historial de préstamos -->
            <div>
                <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-6">Tus solicitudes recientes
                </p>

                @if($loans->isEmpty())
                    <div class="p-10 text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Aún no has solicitado ningún préstamo.</p>
                        <a href="{{ route('client.loans.create') }}"
                            class="inline-block mt-4 text-xs font-bold text-green-600 dark:text-green-400 hover:underline">Solicita
                            el primero aquí</a>
                    </div>
                @else
                    <div class="space-y-1">
                        @foreach($loans as $loan)
                                    <div
                                        class="group py-5 px-4 flex flex-col md:flex-row md:items-center justify-between gap-6 rounded-xl hover:bg-gray-50/70 dark:hover:bg-white/[0.03] transition-all duration-300 ease-in-out">

                                        <!-- Izquierda: Info Principal -->
                                        <div class="flex items-center gap-5">
                                            <svg class="w-10 h-10 text-green-500 flex-shrink-0" viewBox="0 0 489.038 489.038"
                                                fill="currentColor" xmlns="http://www.w3.org/2000/svg">
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
                                            <div>
                                                <div class="flex items-center gap-2 mb-1">
                                                    <h4 class="text-lg font-bold text-gray-900 dark:text-white">
                                                        {{ $loan->currency === 'USD' ? '$' : 'S/' }} {{ number_format($loan->amount, 2) }}
                                                    </h4>
                                                    <span
                                                        class="text-[9px] bg-black text-white dark:bg-white dark:text-black font-black uppercase tracking-[0.2em] px-2 py-0.5 rounded">
                                                        {{ $loan->type === 'cuotas' ? 'Cuotas' : 'Pago Único' }}
                                                    </span>
                                                </div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    @if($loan->type === 'cuotas')
                                                        @php
                                                            $count = $loan->installments->count();
                                                            if ($count === 0) {
                                                                $installmentsPerMonth = match ($loan->payment_frequency) {
                                                                    'diario' => 30,
                                                                    'semanal' => 4,
                                                                    'quincenal' => 2,
                                                                    default => 1,
                                                                };
                                                                $count = $loan->months * $installmentsPerMonth;
                                                            }
                                                        @endphp
                                                        {{ $count }} pagos
                                                        ({{ $loan->payment_frequency }})
                                                        — Cuotas de
                                                        {{ $loan->currency === 'USD' ? '$' : 'S/' }}
                                                        {{ number_format($loan->monthly_payment, 2) }}
                                                    @else
                                                        Pago único en {{ $loan->payment_days }} días
                                                    @endif
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Centro: Detalles -->
                                        <div
                                            class="grid grid-cols-1 sm:grid-cols-4 gap-x-8 gap-y-4 px-0 xl:px-8 flex-1">

                                            <div class="flex flex-col justify-center">
                                                <p class="text-[10px] text-gray-400 dark:text-gray-600 uppercase font-bold mb-1">Progreso
                                                </p>
                                                <div class="flex items-center gap-2">
                                                    <div class="flex-1 h-1.5 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                                                        @php
                                                            $total_i = $loan->installments->count();
                                                            if ($total_i === 0) {
                                                                $installmentsPerMonth = match ($loan->payment_frequency) {
                                                                    'diario' => 30,
                                                                    'semanal' => 4,
                                                                    'quincenal' => 2,
                                                                    default => 1,
                                                                };
                                                                $total_i = $loan->months * $installmentsPerMonth;
                                                            }
                                                            $paid_i = $loan->installments->where('status', 'approved')->count();
                                                            $perc = ($paid_i / ($total_i ?: 1)) * 100;
                                                        @endphp
                                                        <div class="h-full bg-green-500 rounded-full" style="width: {{ $perc }}%"></div>
                                                    </div>
                                                    <span
                                                        class="text-[10px] font-bold text-gray-700 dark:text-gray-300">{{ $paid_i }}/{{ $total_i }}</span>
                                                </div>
                                            </div>

                                            <div class="flex flex-col justify-center">
                                                <p class="text-[10px] text-gray-400 dark:text-gray-600 uppercase font-bold mb-1">Interés</p>
                                                <p class="text-xs text-gray-700 dark:text-gray-300">
                                                    {{ number_format($loan->interest_rate, 2) }}%
                                                </p>
                                            </div>

                                            <div class="flex flex-col justify-center">
                                                <p class="text-[10px] text-gray-400 dark:text-gray-600 uppercase font-bold mb-1">Desembolso
                                                </p>
                                                <p class="text-xs text-gray-700 dark:text-gray-300 tabular-nums">
                                                    {{ $loan->disbursement_date ? \Carbon\Carbon::parse($loan->disbursement_date)->format('d/m/Y') : '—' }}
                                                </p>
                                            </div>

                                            <div class="flex flex-col justify-center">
                                                <p class="text-[10px] text-gray-400 dark:text-gray-600 uppercase font-bold mb-1">Motivo</p>
                                                <p class="text-xs text-gray-700 dark:text-gray-300 italic" title="{{ $loan->reason }}">
                                                    "{{ Str::limit($loan->reason, 15) }}"
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Derecha: Estado -->
                                        <div
                                            class="flex flex-row md:flex-col items-center md:items-end justify-between md:justify-center gap-3 pt-4 md:pt-0">
                                            <span
                                                class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-[0.15em]
                                                                                                                                                                                {{ $loan->status === 'approved' ? 'text-green-500' : '' }}
                                                                                                                                                                                {{ $loan->status === 'pending' ? 'text-yellow-600' : '' }}
                                                                                                                                                                                {{ $loan->status === 'counter_offered' ? 'text-purple-600' : '' }}
                                                                                                                                                                                {{ $loan->status === 'rejected' ? 'text-red-500' : '' }}
                                                                                                                                                                                {{ $loan->status === 'paid' ? 'text-blue-500' : '' }}">
                                                {{ match ($loan->status) {
                                'approved' => 'Aprobado',
                                'pending' => 'En revisión',
                                'counter_offered' => 'Contra-oferta',
                                'rejected' => 'Rechazado',
                                'paid' => 'Pagado',
                                default => $loan->status
                            } }}
                                            </span>
                                            <p class="text-[10px] text-gray-400 dark:text-gray-600 font-medium">
                                                {{ $loan->created_at->format('d M, H:i') }}
                                            </p>
                                        </div>

                                    </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>

    <!-- Modal Aceptar Contra-oferta -->
    <div id="acceptCounterModal"
        class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] flex items-center justify-center p-2 sm:p-4">
        <div
            class="bg-white dark:bg-gray-950 w-full max-w-sm sm:max-w-lg rounded-2xl sm:rounded-3xl shadow-2xl relative animate-in fade-in zoom-in duration-200 p-4 sm:p-8 max-h-[95vh] sm:max-h-[90vh] overflow-y-auto custom-scrollbar">

            <button onclick="closeAcceptCounterModal()"
                class="absolute top-3 right-3 sm:top-5 sm:right-5 p-1.5 sm:p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-900 transition-colors">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div id="counterOfferListView">
                <div class="text-center mb-4 sm:mb-8">
                    <svg class="w-8 h-8 sm:w-12 sm:h-12 text-purple-600 mx-auto mb-2 sm:mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-base sm:text-xl font-bold text-gray-900 dark:text-white">Contra-ofertas Recibidas</h3>
                    <p class="text-[10px] sm:text-sm text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-1">Selecciona una solicitud para ver los cambios.
                    </p>
                </div>
                <div id="counterOfferListItems" class="space-y-2 sm:space-y-3 max-h-[35vh] sm:max-h-[40vh] overflow-y-auto custom-scrollbar pr-1 sm:pr-2">
                </div>
            </div>

            <div id="counterOfferDetailView" class="hidden">
                <button onclick="showCounterOffersList(lastLoansData)"
                    class="mb-3 sm:mb-6 flex items-center gap-1.5 text-[10px] sm:text-xs text-purple-600 font-bold uppercase tracking-widest hover:opacity-70 transition-opacity">
                    <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Volver a la lista
                </button>

                <div class="text-center mb-4 sm:mb-8">
                    <h3 class="text-base sm:text-xl font-bold text-gray-900 dark:text-white">Comparación de Términos</h3>
                    <p class="text-[10px] sm:text-sm text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-1">Revisa los cambios realizados por el administrador.</p>
                </div>

                <div class="space-y-2 sm:space-y-4 mb-4 sm:mb-8">
                    <div class="grid grid-cols-3 gap-1 sm:gap-4 py-1.5 sm:py-2 border-b border-gray-50 dark:border-gray-900">
                        <span class="text-[8px] sm:text-[10px] text-gray-400 uppercase font-black tracking-widest">Concepto</span>
                        <span class="text-[8px] sm:text-[10px] text-gray-400 uppercase font-black tracking-widest text-center">Antes</span>
                        <span class="text-[8px] sm:text-[10px] text-purple-600 uppercase font-black tracking-widest text-center">Ahora</span>
                    </div>
                    <div id="comparisonRows" class="space-y-2 sm:space-y-4">
                    </div>
                </div>

                <div id="scheduleSection" class="hidden mb-4 sm:mb-8">
                    <button type="button" onclick="openScheduleModal()" class="w-full flex items-center justify-between p-2.5 sm:p-4 rounded-xl sm:rounded-2xl border border-purple-200 dark:border-purple-900/40 bg-purple-50/50 dark:bg-purple-950/20 hover:bg-purple-100 dark:hover:bg-purple-950/30 transition-all group">
                        <div class="flex items-center gap-2 sm:gap-3">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <span class="text-[8px] sm:text-[10px] font-black text-purple-600 uppercase tracking-[0.1em] sm:tracking-[0.2em]">Ver Cronograma</span>
                        </div>
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 text-purple-400 group-hover:text-purple-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>

                <div class="flex justify-center gap-8 sm:gap-12">
                    <form id="rejectCounterForm" method="POST">
                        @csrf
                        <button type="submit"
                            class="text-[10px] sm:text-xs font-black uppercase tracking-widest text-gray-400 hover:text-red-500 border-b-2 border-transparent hover:border-red-500 pb-0.5 sm:pb-1 transition-all">
                            Rechazar
                        </button>
                    </form>
                    <form id="acceptCounterForm" method="POST">
                        @csrf
                        <button type="submit"
                            class="text-[10px] sm:text-xs font-black uppercase tracking-widest text-purple-600 hover:text-purple-400 border-b-2 border-purple-600 hover:border-purple-400 pb-0.5 sm:pb-1 transition-all">
                            Aceptar Propuesta
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="scheduleModal" class="hidden fixed inset-0 z-[110] flex items-center justify-center bg-black/50 backdrop-blur-sm p-2 sm:p-4">
        <div class="bg-white dark:bg-gray-950 rounded-2xl sm:rounded-3xl shadow-2xl w-full max-w-sm sm:max-w-2xl max-h-[90vh] sm:max-h-[85vh] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-3 sm:px-6 py-3 sm:py-4 border-b border-gray-100 dark:border-gray-900">
                <div class="flex items-center gap-2 sm:gap-3">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <h3 class="text-[10px] sm:text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Cronograma de Pagos</h3>
                </div>
                <button type="button" onclick="closeScheduleModal()" class="p-1 sm:p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-900 transition-colors">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto schedule-scrollbar px-2 sm:px-6 py-3 sm:py-4">
                <table class="w-full text-[8px] sm:text-[10px] text-left">
                    <thead class="bg-gray-50 dark:bg-gray-900 text-gray-400 dark:text-gray-600 sticky top-0">
                        <tr>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 font-black uppercase tracking-tighter text-center">N°</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 font-black uppercase tracking-tighter text-center">Capital</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 font-black uppercase tracking-tighter text-center">Interés</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 font-black uppercase tracking-tighter text-center">Cuota</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 font-black uppercase tracking-tighter text-center">Saldo</th>
                        </tr>
                    </thead>
                    <tbody id="scheduleBody" class="divide-y divide-gray-50 dark:divide-gray-900 text-gray-600 dark:text-gray-400">
                    </tbody>
                    <tfoot id="scheduleFoot" class="bg-gray-50 dark:bg-gray-900 font-black text-gray-900 dark:text-white sticky bottom-0">
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <script>
        let lastLoansData = [];

        function showCounterOffersList(loans) {
            lastLoansData = loans;
            const modal = document.getElementById('acceptCounterModal');
            const listItems = document.getElementById('counterOfferListItems');

            document.getElementById('counterOfferListView').classList.remove('hidden');
            document.getElementById('counterOfferDetailView').classList.add('hidden');

            listItems.innerHTML = '';
            loans.forEach(loan => {
                const symbol = loan.currency === 'USD' ? '$' : 'S/';
                const item = document.createElement('div');
                item.className = 'group p-2.5 sm:p-4 rounded-xl sm:rounded-2xl border border-gray-100 dark:border-gray-900 hover:border-purple-500/50 cursor-pointer transition-all flex items-center justify-between';
                item.onclick = () => openCounterOfferDetail(loan);
                item.innerHTML = `
                            <div>
                                <p class="text-[8px] sm:text-[10px] text-gray-400 uppercase font-black tracking-widest mb-0.5 sm:mb-1">Solicitud #${loan.id}</p>
                                <p class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white">${symbol} ${parseFloat(loan.amount).toLocaleString('en-US', { minimumFractionDigits: 2 })}</p>
                            </div>
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-300 group-hover:text-purple-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        `;
                listItems.appendChild(item);
            });

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function openCounterOfferDetail(loan) {
            const symbol = loan.currency === 'USD' ? '$' : 'S/';
            const original = loan.original_data || {};
            const rowsContainer = document.getElementById('comparisonRows');

            document.getElementById('counterOfferListView').classList.add('hidden');
            document.getElementById('counterOfferDetailView').classList.remove('hidden');

            const fmt = (n) => (n !== undefined && n !== null && !isNaN(parseFloat(n)))
                ? parseFloat(n).toLocaleString('en-US', { minimumFractionDigits: 2 })
                : '—';

            const dateFmt = (d) => (d && d !== '—') ? new Date(d + 'T00:00:00').toLocaleDateString('es-PE') : '—';

            const currencyLabel = (c) => c === 'USD' ? 'Dólares' : 'Soles';

            const fields = [
                { label: 'Tipo', old: original.type ? (original.type === 'cuotas' ? 'Cuotas' : 'Único') : '—', new: (loan.type === 'cuotas' ? 'Cuotas' : 'Único') },
            ];

            if (original.currency && original.currency !== loan.currency) {
                fields.push({ label: 'Moneda', old: currencyLabel(original.currency), new: currencyLabel(loan.currency) });
            }

            fields.push(
                { label: 'Monto', old: original.amount ? (original.currency === 'USD' ? '$' : 'S/') + ' ' + fmt(original.amount) : '—', new: symbol + ' ' + fmt(loan.amount) },
                { label: 'Tasa %', old: original.interest_rate ? parseFloat(original.interest_rate).toFixed(2) + '%' : '—', new: parseFloat(loan.interest_rate).toFixed(2) + '%' },
                {
                    label: 'Plazo',
                    old: original.type === 'cuotas' ? (original.months + ' meses') : (original.payment_days ? (original.payment_days + ' días') : '—'),
                    new: loan.type === 'cuotas' ? (loan.months + ' meses') : (loan.payment_days + ' días')
                }
            );

            if (loan.type === 'cuotas' || (original.type === 'cuotas')) {
                fields.push({
                    label: 'Frecuencia',
                    old: original.type === 'cuotas' ? (original.payment_frequency || 'mensual') : '—',
                    new: loan.type === 'cuotas' ? (loan.payment_frequency || 'mensual') : '—'
                });
            }

            fields.push(
                {
                    label: 'Cuota',
                    old: original.monthly_payment ? (original.currency === 'USD' ? '$' : 'S/') + ' ' + fmt(original.monthly_payment) : '—',
                    new: symbol + ' ' + fmt(loan.monthly_payment)
                },
                { label: 'Desembolso', old: dateFmt(original.disbursement_date), new: dateFmt(loan.disbursement_date) }
            );

            rowsContainer.innerHTML = '';
            fields.forEach(f => {
                const isChanged = f.old !== f.new;
                const row = document.createElement('div');
                row.className = 'grid grid-cols-3 gap-1 sm:gap-4 items-center';
                row.innerHTML = `
                            <span class="text-[8px] sm:text-[10px] text-gray-500 font-bold uppercase tracking-tight">${f.label}</span>
                            <span class="text-[10px] sm:text-xs text-gray-400 text-center tabular-nums line-through">${f.old}</span>
                            <span class="text-[10px] sm:text-xs font-black text-center tabular-nums ${isChanged ? 'text-purple-600 animate-pulse' : 'text-gray-900 dark:text-white'}">${f.new}</span>
                        `;
                rowsContainer.appendChild(row);
            });

            document.getElementById('acceptCounterForm').action = `/panel/prestamos/${loan.id}/aceptar-contraoferta`;
            document.getElementById('rejectCounterForm').action = `/panel/prestamos/${loan.id}/rechazar-contraoferta`;

            renderSchedule(loan);
        }

        function openScheduleModal() {
            document.getElementById('scheduleModal').classList.remove('hidden');
        }

        function closeScheduleModal() {
            document.getElementById('scheduleModal').classList.add('hidden');
        }

        document.getElementById('scheduleModal').addEventListener('click', e => {
            if (e.target === document.getElementById('scheduleModal')) closeScheduleModal();
        });

        function renderSchedule(loan) {
            const section = document.getElementById('scheduleSection');
            const body = document.getElementById('scheduleBody');
            const foot = document.getElementById('scheduleFoot');
            const symbol = loan.currency === 'USD' ? '$' : 'S/';
            const amount = parseFloat(loan.amount) || 0;
            const rate = (parseFloat(loan.interest_rate) || 0) / 100;
            const type = loan.type || 'cuotas';
            const system = loan.amortization_system || 'frances';
            const months = parseInt(loan.months) || 3;
            const freq = loan.payment_frequency || 'mensual';

            body.innerHTML = '';
            foot.innerHTML = '';
            section.classList.remove('hidden');

            if (type === 'unico') {
                const interest = amount * rate;
                const total = amount + interest;
                body.innerHTML = `
                    <tr class="border-b border-gray-50 dark:border-gray-900">
                        <td class="px-2 sm:px-4 py-2 sm:py-3 text-center font-bold">1</td>
                        <td class="px-2 sm:px-4 py-2 sm:py-3 text-center tabular-nums">${symbol}${amount.toFixed(2)}</td>
                        <td class="px-2 sm:px-4 py-2 sm:py-3 text-center tabular-nums">${symbol}${interest.toFixed(2)}</td>
                        <td class="px-2 sm:px-4 py-2 sm:py-3 text-center tabular-nums font-black">${symbol}${total.toFixed(2)}</td>
                        <td class="px-2 sm:px-4 py-2 sm:py-3 text-center tabular-nums font-black">0.00</td>
                    </tr>
                `;
                foot.innerHTML = `
                    <tr>
                        <td class="px-2 sm:px-4 py-2 sm:py-3 text-center">TOTAL</td>
                        <td class="px-2 sm:px-4 py-2 sm:py-3 text-center tabular-nums">${symbol}${amount.toFixed(2)}</td>
                        <td class="px-2 sm:px-4 py-2 sm:py-3 text-center tabular-nums">${symbol}${interest.toFixed(2)}</td>
                        <td class="px-2 sm:px-4 py-2 sm:py-3 text-center tabular-nums">${symbol}${total.toFixed(2)}</td>
                        <td></td>
                    </tr>
                `;
                return;
            }

            let n, periodicRate;
            if (system === 'frances') {
                const ipm = { diario: 30, semanal: 4, quincenal: 2, mensual: 1 }[freq] || 1;
                n = months * ipm;
                periodicRate = rate / ipm;
            } else {
                n = months;
                periodicRate = rate;
            }

            let schedule = [];
            let totalInterest = 0;
            let totalPrincipal = 0;
            let periodicPayment = 0;

            if (system === 'frances') {
                if (amount > 0 && rate > 0) {
                    periodicPayment = (amount * periodicRate) / (1 - Math.pow(1 + periodicRate, -n));
                } else {
                    periodicPayment = amount / n;
                }
                let balance = amount;
                for (let i = 1; i <= n; i++) {
                    const interest = balance * periodicRate;
                    const principal = periodicPayment - interest;
                    balance -= principal;
                    totalInterest += interest;
                    totalPrincipal += principal;
                    schedule.push({ n: i, principal, interest, payment: periodicPayment, balance: Math.max(0, balance) });
                }
            } else {
                const interestPmt = amount * periodicRate;
                let balance = amount;
                for (let i = 1; i <= n; i++) {
                    const principal = (i === n) ? amount : 0;
                    const payment = (i === n) ? amount + interestPmt : interestPmt;
                    balance -= principal;
                    totalInterest += interestPmt;
                    totalPrincipal += principal;
                    schedule.push({ n: i, principal, interest: interestPmt, payment, balance: Math.max(0, balance) });
                }
            }

            schedule.forEach(row => {
                body.innerHTML += `
                    <tr class="border-b border-gray-50 dark:border-gray-900">
                        <td class="px-2 sm:px-4 py-2 sm:py-3 text-center font-bold">${row.n}</td>
                        <td class="px-2 sm:px-4 py-2 sm:py-3 text-center tabular-nums">${symbol}${row.principal.toFixed(2)}</td>
                        <td class="px-2 sm:px-4 py-2 sm:py-3 text-center tabular-nums">${symbol}${row.interest.toFixed(2)}</td>
                        <td class="px-2 sm:px-4 py-2 sm:py-3 text-center tabular-nums font-black">${symbol}${row.payment.toFixed(2)}</td>
                        <td class="px-2 sm:px-4 py-2 sm:py-3 text-center tabular-nums">${symbol}${row.balance.toFixed(2)}</td>
                    </tr>
                `;
            });

            foot.innerHTML = `
                <tr>
                    <td class="px-2 sm:px-4 py-2 sm:py-3 text-center">TOTAL</td>
                    <td class="px-2 sm:px-4 py-2 sm:py-3 text-center tabular-nums">${symbol}${totalPrincipal.toFixed(2)}</td>
                    <td class="px-2 sm:px-4 py-2 sm:py-3 text-center tabular-nums">${symbol}${totalInterest.toFixed(2)}</td>
                    <td class="px-2 sm:px-4 py-2 sm:py-3 text-center tabular-nums">${symbol}${(totalPrincipal + totalInterest).toFixed(2)}</td>
                    <td></td>
                </tr>
            `;
        }

        function closeAcceptCounterModal() {
            document.getElementById('acceptCounterModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Close on overlay click
        document.getElementById('acceptCounterModal').addEventListener('click', e => {
            if (e.target === document.getElementById('acceptCounterModal')) closeAcceptCounterModal();
        });
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e5e7eb;
            border-radius: 10px;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #1f2937;
        }

        .schedule-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .schedule-scrollbar::-webkit-scrollbar-track {
            background: #f3f4f6;
            border-radius: 10px;
        }

        .schedule-scrollbar::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 10px;
        }

        .schedule-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        .dark .schedule-scrollbar::-webkit-scrollbar-track {
            background: #111827;
        }

        .dark .schedule-scrollbar::-webkit-scrollbar-thumb {
            background: #374151;
        }

        .dark .schedule-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #4b5563;
        }
    </style>
@endsection