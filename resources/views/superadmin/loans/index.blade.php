@extends('superadmin.layout')

@section('title', 'Control de Préstamos')

@section('content')
    <div class="px-5 md:px-10 py-8">

        <!-- Header -->
            <div class="flex items-end justify-between mb-8">
                <div>
                    <p class="text-xs text-gray-600 dark:text-gray-600 uppercase tracking-widest mb-1">Panel Administrativo
                    </p>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Control de
                        Préstamos</h1>
                </div>
                <p class="hidden md:block text-sm text-gray-600 dark:text-gray-400">{{ now()->format('d M Y') }}</p>
            </div>

            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 text-sm text-green-500">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Filtros Mobile -->
            <div class="md:hidden">
                <div class="grid grid-cols-3 gap-2">
                    @foreach([
                        'pending'  => ['label' => 'Pendientes', 'active' => 'bg-gray-900 text-white dark:bg-white dark:text-black', 'inactive' => 'bg-transparent text-gray-400 border-gray-200 dark:border-gray-800'],
                        'approved' => ['label' => 'Aprobados',  'active' => 'bg-gray-900 text-white dark:bg-white dark:text-black', 'inactive' => 'bg-transparent text-gray-400 border-gray-200 dark:border-gray-800'],
                        'rejected' => ['label' => 'Rechazados', 'active' => 'bg-gray-900 text-white dark:bg-white dark:text-black', 'inactive' => 'bg-transparent text-gray-400 border-gray-200 dark:border-gray-800'],
                        'paid'     => ['label' => 'Pagados',    'active' => 'bg-gray-900 text-white dark:bg-white dark:text-black', 'inactive' => 'bg-transparent text-gray-400 border-gray-200 dark:border-gray-800'],
                        'all'      => ['label' => 'Todos',      'active' => 'bg-gray-900 text-white dark:bg-white dark:text-black', 'inactive' => 'bg-transparent text-gray-400 border-gray-200 dark:border-gray-800'],
                    ] as $key => $cfg)
                        <a href="{{ route('superadmin.loans.index', ['filter' => $key]) }}"
                           class="flex items-center justify-center py-2.5 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all border
                                  {{ $filter === $key ? $cfg['active'] . ' border-transparent' : $cfg['inactive'] }}">
                            {{ $cfg['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Filtros Desktop -->
            <div class="hidden md:flex items-center gap-0 border-b border-gray-200 dark:border-gray-800 overflow-x-auto no-scrollbar">
                @foreach(['pending' => 'Pendientes', 'approved' => 'Aprobados', 'rejected' => 'Rechazados', 'paid' => 'Pagados', 'all' => 'Todos'] as $key => $label)
                    <a href="{{ route('superadmin.loans.index', ['filter' => $key]) }}"
                        class="relative flex-shrink-0 px-6 py-4 text-sm font-medium transition-colors
                                  {{ $filter === $key ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-400' }}">
                        {{ $label }}
                        @if($counts[$key] > 0)
                            <span class="ml-2 text-xs {{ $filter === $key ? 'text-gray-500 dark:text-gray-400' : 'text-gray-300 dark:text-gray-500' }}">{{ $counts[$key] }}</span>
                        @endif
                        @if($filter === $key)
                            <span class="absolute bottom-0 left-0 right-0 h-px bg-gray-900 dark:bg-white"></span>
                        @endif
                    </a>
                @endforeach
            </div>

            <!-- Lista de préstamos -->
            <div class="divide-y divide-gray-100 dark:divide-gray-900">
                @forelse($loans as $loan)
                    <div class="group py-6 md:py-8 transition-colors">
                        <!-- Layout Mobile: Stack vertical -->
                        <div class="flex flex-col gap-6 lg:hidden">
                            <!-- Usuario e Info Principal -->
                            <div class="flex items-start gap-3">
                                <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-900 flex items-center justify-center overflow-hidden flex-shrink-0 border border-gray-100 dark:border-gray-800">
                                    @if($loan->user->is_ghost)
                                        <svg fill="#9333ea" width="24" height="24" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M511.984-.128c-229.216 0-415.681 199.903-415.681 445.6v546.672c0 13.216 8.16 25.088 20.496 29.84 3.712 1.471 7.632 2.16 11.504 2.16 8.848 0 17.536-3.68 23.712-10.527l120.592-133.12 94.431 130.432a31.918 31.918 0 0 0 25.68 13.215h.224a31.98 31.98 0 0 0 25.664-12.912l94.816-127.344 93.184 127.152a31.993 31.993 0 0 0 25.809 13.088 32.028 32.028 0 0 0 25.808-13.055l95.569-130.288 118 132.624c8.816 9.904 22.944 13.376 35.28 8.624 12.4-4.72 20.624-16.624 20.624-29.905V445.456C927.696 199.776 741.2-.128 511.984-.128zm351.711 908.16l-88.402-99.376c-6.432-7.216-15.808-11.311-25.407-10.687a32.105 32.105 0 0 0-24.32 13.024l-93.12 127.008-93.008-126.912A31.975 31.975 0 0 0 513.758 798h-.127a31.935 31.935 0 0 0-25.664 12.912l-94.689 127.152-92-127.088c-5.664-7.807-14.528-12.655-24.16-13.151-.592-.032-1.151-.065-1.743-.065a31.984 31.984 0 0 0-23.712 10.528l-91.376 100.848v-463.68c0-210.4 157.776-381.601 351.68-381.601 193.937 0 351.713 171.184 351.713 381.6V908.03h.015zM671.997 352.16c-35.28 0-63.84 28.592-63.84 63.808 0 35.248 28.56 63.84 63.84 63.84s63.84-28.592 63.84-63.84c0-35.216-28.56-63.808-63.84-63.808zm-320 0c-35.28 0-63.84 28.592-63.84 63.808 0 35.248 28.576 63.84 63.84 63.84s63.84-28.592 63.84-63.84c0-35.216-28.56-63.808-63.84-63.808z" />
                                        </svg>
                                    @else
                                        @php
                                            $selfie = $loan->user->documents?->selfie;
                                            $photo = $selfie ? Storage::url($selfie) : ($loan->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($loan->user->name));
                                        @endphp
                                        <img src="{{ $photo }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <p class="text-sm font-bold text-gray-900 dark:text-white truncate">
                                            @if($loan->user->person_type === 'juridica' && $loan->user->business_name)
                                                {{ $loan->user->business_name }}
                                            @else
                                                {{ $loan->user->name }}
                                            @endif
                                        </p>
                                        <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-widest {{ $loan->user->person_type === 'juridica' ? 'bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' }}">
                                            {{ $loan->user->person_type === 'juridica' ? 'JUR' : 'NAT' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <p class="text-lg font-black text-gray-900 dark:text-white tracking-tighter">
                                            {{ $loan->currency === 'USD' ? '$' : 'S/' }} {{ number_format($loan->amount, 2) }}
                                        </p>
                                        <span class="text-[8px] bg-black text-white dark:bg-white dark:text-black px-1.5 py-0.5 rounded font-black uppercase tracking-widest">
                                            {{ $loan->type === 'cuotas' ? 'Cuotas' : 'Unico' }}
                                        </span>
                                    </div>
                                    <p class="text-[9px] text-gray-400 dark:text-gray-600 font-medium">
                                        {{ $loan->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Perfil del Solicitante Mobile -->
                            @if($loan->user->clientProfile)
                                @php $profile = $loan->user->clientProfile; @endphp
                                <div class="space-y-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-4">
                                    <!-- Educación y Trabajo -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-[9px] text-gray-400 dark:text-gray-600 uppercase font-black mb-1.5 tracking-widest">Educación</p>
                                            <p class="text-[10px] text-gray-900 dark:text-white font-bold leading-tight">{{ Str::limit($profile->academic_status, 25) }}</p>
                                            @if($profile->institution)
                                                <p class="text-[9px] text-gray-500 dark:text-gray-500 leading-tight mt-0.5">{{ Str::limit($profile->institution, 20) }}</p>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-[9px] text-gray-400 dark:text-gray-600 uppercase font-black mb-1.5 tracking-widest">Trabajo</p>
                                            @if($profile->is_employed)
                                                <p class="text-[10px] text-gray-900 dark:text-white font-bold leading-tight">{{ ucfirst($profile->employment_type) }}</p>
                                            @else
                                                <p class="text-[10px] text-gray-500 dark:text-gray-500 leading-tight">No trabaja</p>
                                            @endif
                                            <p class="text-[9px] text-green-600 dark:text-green-500 font-bold leading-tight mt-0.5">S/ {{ number_format($profile->monthly_income, 0) }}</p>
                                        </div>
                                    </div>

                                    <!-- Estado Civil e Hijos -->
                                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200 dark:border-gray-800">
                                        <div>
                                            <p class="text-[9px] text-gray-400 dark:text-gray-600 uppercase font-black mb-1.5 tracking-widest">Estado Civil</p>
                                            <p class="text-[10px] text-gray-900 dark:text-white font-bold leading-tight">
                                                {{ match($profile->marital_status) {
                                                    'soltero' => 'Soltero/a',
                                                    'casado_separado' => 'Casado (B.S)',
                                                    'casado_compartido' => 'Casado (B.C)',
                                                    default => $profile->marital_status
                                                } }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-[9px] text-gray-400 dark:text-gray-600 uppercase font-black mb-1.5 tracking-widest">Hijos</p>
                                            <p class="text-[10px] text-gray-900 dark:text-white font-bold leading-tight">
                                                @if($profile->has_children)
                                                    {{ $profile->children_count }}
                                                @else
                                                    No
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Banco y Términos -->
                                    <div class="pt-4 border-t border-gray-200 dark:border-gray-800">
                                        <p class="text-[9px] text-gray-400 dark:text-gray-600 uppercase font-black mb-1.5 tracking-widest">Destino</p>
                                        <p class="text-[10px] font-bold text-gray-900 dark:text-white">{{ $loan->bank_name }}</p>
                                        <p class="text-[9px] text-gray-500 dark:text-gray-500 mt-0.5">{{ ucfirst($loan->account_type) }} • {{ $loan->account_number }}</p>
                                    </div>

                                    <div class="pt-4 border-t border-gray-200 dark:border-gray-800">
                                        <div class="flex items-center gap-1.5 mb-1.5">
                                            <p class="text-[9px] text-gray-400 dark:text-gray-600 uppercase font-black tracking-widest">Términos</p>
                                            @if($loan->type === 'cuotas')
                                                <button type="button"
                                                    onclick="openScheduleModal({{ json_encode([
                                                        'amount' => $loan->amount,
                                                        'currency' => $loan->currency,
                                                        'type' => $loan->type,
                                                        'months' => $loan->months,
                                                        'payment_days' => $loan->payment_days,
                                                        'interest_rate' => $loan->interest_rate,
                                                        'system' => $loan->amortization_system ?? 'frances',
                                                        'monthly_payment' => $loan->monthly_payment ?? 0,
                                                        'payment_frequency' => $loan->payment_frequency ?? 'mensual',
                                                        'user_name' => $loan->user->name,
                                                    ]) }})"
                                                    class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors"
                                                    title="Ver cronograma">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </button>
                                            @endif
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-[10px] text-gray-900 dark:text-white font-bold">
                                                    @if($loan->type === 'cuotas')
                                                        {{ $loan->months }} meses
                                                        @php
                                                            $freqLabel = match($loan->payment_frequency ?? 'mensual') {
                                                                'diario' => 'día',
                                                                'semanal' => 'sem',
                                                                'quincenal' => 'quin',
                                                                default => 'mes'
                                                            };
                                                        @endphp
                                                        <span class="text-[8px] text-gray-400 font-normal">({{ $loan->amortization_system === 'americano' ? 'americano' : $loan->payment_frequency }})</span>
                                                    @else
                                                        {{ $loan->payment_days }} días
                                                    @endif
                                                </p>
                                                <p class="text-[9px] text-gray-500 dark:text-gray-500 mt-0.5">
                                                    Cuota/{{ $freqLabel ?? 'pago' }}: <span class="font-bold text-gray-900 dark:text-white">{{ $loan->currency === 'USD' ? '$' : 'S/' }}{{ number_format($loan->monthly_payment, 2) }}</span>
                                                </p>
                                            </div>
                                            <p class="text-[10px] text-green-600 dark:text-green-500 font-bold">
                                                +{{ $loan->currency === 'USD' ? '$' : 'S/' }}{{ number_format($loan->total_amount - $loan->amount, 2) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl">
                                    <p class="text-xs text-gray-400 dark:text-gray-600 italic">Perfil incompleto</p>
                                </div>
                            @endif

                            <!-- Acciones Mobile -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-800">
                                <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-600">Estado</span>
                                @if($loan->status === 'pending')
                                    <div class="flex items-center gap-4">
                                        <button type="button"
                                                onclick="openCounterOfferModal({{ json_encode([
                                                    'id' => $loan->id,
                                                    'amount' => $loan->amount,
                                                    'currency' => $loan->currency,
                                                    'type' => $loan->type,
                                                    'months' => $loan->months,
                                                    'payment_days' => $loan->payment_days,
                                                    'interest_rate' => $loan->interest_rate,
                                                    'system' => $loan->amortization_system ?? 'frances',
                                                    'monthly_payment' => $loan->monthly_payment ?? 0,
                                                    'payment_frequency' => $loan->payment_frequency ?? 'mensual'
                                                ]) }})"
                                                class="text-purple-600 hover:text-purple-400 transition-colors active:scale-[0.95]"
                                                title="Contra-oferta">
                                            <svg class="w-5 h-5" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" stroke-width="3" stroke="currentColor" fill="none"><polyline points="46.81 40.84 46.81 56.26 9.19 56.26 9.19 20.6 21.15 7.74 46.81 7.74 46.81 29.71" stroke-linecap="round"/><polyline points="21.17 7.74 21.15 20.6 9.19 20.6"/><line x1="14.34" y1="47.67" x2="31.35" y2="47.67"/><line x1="14.34" y1="41.14" x2="41.35" y2="41.14"/><line x1="14.34" y1="35.04" x2="35.35" y2="35.04"/><line x1="14.34" y1="28.94" x2="41.35" y2="28.94"/><polyline points="52.21 30.24 57.01 35.04 52.21 39.84" stroke-linecap="round"/><line x1="57.01" y1="35.04" x2="41.38" y2="35.04" stroke-linecap="round"/></svg>
                                        </button>
                                        <button type="button"
                                                onclick="openApproveModal('{{ $loan->id }}', '{{ $loan->currency === 'USD' ? '$' : 'S/' }}{{ number_format($loan->amount, 2) }}', '{{ $loan->user->name }}')"
                                                class="text-green-600 hover:text-green-400 transition-colors active:scale-[0.95]"
                                                title="Aprobar">
                                            <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><path d="M20,2H6A2,2,0,0,0,4,4V17H4a2,2,0,0,0-2,2H2a3,3,0,0,0,3,3H19.5A2.5,2.5,0,0,0,22,19.5V4A2,2,0,0,0,20,2Z" opacity="0.3"/><path d="M19,22v0H5a3,3,0,0,1-3-3H2a2,2,0,0,1,2-2H15a2,2,0,0,1,2,2v.5A2.52,2.52,0,0,0,19,22ZM12.71,11.71l4-4a1,1,0,1,0-1.42-1.42L12,9.59l-1.29-1.3A1,1,0,0,0,9.29,9.71l2,2a1,1,0,0,0,1.42,0Z"/></svg>
                                        </button>
                                        <button type="button"
                                                onclick="openRejectModal('{{ $loan->id }}', '{{ $loan->currency === 'USD' ? '$' : 'S/' }}{{ number_format($loan->amount, 2) }}', '{{ $loan->user->name }}')"
                                                class="text-red-600 hover:text-red-400 transition-colors active:scale-[0.95]"
                                                title="Rechazar">
                                            <svg class="w-5 h-5" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.25 1.5a.75.75 0 00-.75.75v11.5c0 .414.336.75.75.75h4.5a.75.75 0 010 1.5h-4.5A2.25 2.25 0 011 13.75V2.25A2.25 2.25 0 013.25 0h6.293c.331 0 .65.132.884.366l4.207 4.207c.234.235.366.553.366.884V7.5a.75.75 0 01-1.5 0V6H9.75A.75.75 0 019 5.25V1.5H3.25zm7.25 1.06l1.94 1.94H10.5V2.56z"/><path d="M11.53 14.53l.97-.97.97.97a.75.75 0 101.06-1.06l-.97-.97.97-.97a.75.75 0 10-1.06-1.06l-.97.97-.97-.97a.75.75 0 10-1.06 1.06l.97.97-.97.97a.75.75 0 101.06 1.06z"/></svg>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-xs font-black uppercase tracking-widest
                                                {{ $loan->status === 'approved' ? 'text-green-500' : '' }}
                                                {{ $loan->status === 'rejected' ? 'text-red-500' : '' }}
                                                {{ $loan->status === 'paid' ? 'text-blue-500' : '' }}">
                                        {{ match($loan->status) {
                                            'approved' => 'Aprobado',
                                            'pending'  => 'En revisión',
                                            'rejected' => 'Rechazado',
                                            'paid'     => 'Pagado',
                                            default    => $loan->status
                                        } }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Layout Desktop: Row by Row -->
                        <div class="hidden lg:block space-y-6">
                            <!-- Fila 1: Header con Usuario y Monto -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-900 flex items-center justify-center overflow-hidden flex-shrink-0 border-2 border-gray-200 dark:border-gray-800">
                                        @if($loan->user->is_ghost)
                                            <svg fill="#9333ea" width="28" height="28" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M511.984-.128c-229.216 0-415.681 199.903-415.681 445.6v546.672c0 13.216 8.16 25.088 20.496 29.84 3.712 1.471 7.632 2.16 11.504 2.16 8.848 0 17.536-3.68 23.712-10.527l120.592-133.12 94.431 130.432a31.918 31.918 0 0 0 25.68 13.215h.224a31.98 31.98 0 0 0 25.664-12.912l94.816-127.344 93.184 127.152a31.993 31.993 0 0 0 25.809 13.088 32.028 32.028 0 0 0 25.808-13.055l95.569-130.288 118 132.624c8.816 9.904 22.944 13.376 35.28 8.624 12.4-4.72 20.624-16.624 20.624-29.905V445.456C927.696 199.776 741.2-.128 511.984-.128zm351.711 908.16l-88.402-99.376c-6.432-7.216-15.808-11.311-25.407-10.687a32.105 32.105 0 0 0-24.32 13.024l-93.12 127.008-93.008-126.912A31.975 31.975 0 0 0 513.758 798h-.127a31.935 31.935 0 0 0-25.664 12.912l-94.689 127.152-92-127.088c-5.664-7.807-14.528-12.655-24.16-13.151-.592-.032-1.151-.065-1.743-.065a31.984 31.984 0 0 0-23.712 10.528l-91.376 100.848v-463.68c0-210.4 157.776-381.601 351.68-381.601 193.937 0 351.713 171.184 351.713 381.6V908.03h.015zM671.997 352.16c-35.28 0-63.84 28.592-63.84 63.808 0 35.248 28.56 63.84 63.84 63.84s63.84-28.592 63.84-63.84c0-35.216-28.56-63.808-63.84-63.808zm-320 0c-35.28 0-63.84 28.592-63.84 63.808 0 35.248 28.576 63.84 63.84 63.84s63.84-28.592 63.84-63.84c0-35.216-28.56-63.808-63.84-63.808z" />
                                            </svg>
                                        @else
                                            @php
                                                $selfie = $loan->user->documents?->selfie;
                                                $photo = $selfie ? Storage::url($selfie) : ($loan->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($loan->user->name));
                                            @endphp
                                            <img src="{{ $photo }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <p class="text-base font-bold text-gray-900 dark:text-white">
                                                @if($loan->user->person_type === 'juridica' && $loan->user->business_name)
                                                    {{ $loan->user->business_name }}
                                                @else
                                                    {{ $loan->user->name }}
                                                @endif
                                            </p>
                                            <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest {{ $loan->user->person_type === 'juridica' ? 'bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' }}">
                                                {{ $loan->user->person_type === 'juridica' ? 'JURÍDICA' : 'NATURAL' }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2 text-[10px]">
                                            <span class="text-gray-500 dark:text-gray-500">
                                                @if($loan->user->person_type === 'juridica')
                                                    RUC: <span class="font-bold text-gray-900 dark:text-white">{{ $loan->user->ruc ?? '—' }}</span>
                                                @else
                                                    DNI: <span class="font-bold text-gray-900 dark:text-white">{{ $loan->user->dni ?? '—' }}</span>
                                                @endif
                                            </span>
                                            <a href="mailto:{{ $loan->user->email }}" class="flex items-center gap-1.5 bg-blue-500 hover:bg-blue-600 transition-colors px-2 py-1 text-white font-bold">
                                                {{ $loan->user->email }}
                                                <svg class="w-3.5 h-3.5 flex-shrink-0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
                                                    <rect x="7.904" y="119.947" style="fill:#5EBAE7;" width="496.195" height="272.109"/>
                                                    <polygon style="fill:#36A9E1;" points="256,280.01 23.91,127.951 7.904,135.954 256,312.022 352.037,223.987 "/>
                                                    <path style="fill:#1D1D1B;" d="M0,112.043v287.913h512V112.043H0z M477.266,127.851L256,270.603L34.733,127.851H477.266z M15.807,384.149V134.452L256,289.414l240.193-154.962v249.696H15.807V384.149z"/>
                                                    <g>
                                                        <rect x="215.982" y="344.138" style="fill:#FFFFFF;" width="16.007" height="15.807"/>
                                                        <rect x="247.997" y="344.138" style="fill:#FFFFFF;" width="16.006" height="15.807"/>
                                                        <rect x="280.013" y="344.138" style="fill:#FFFFFF;" width="16.007" height="15.807"/>
                                                    </g>
                                                </svg>
                                            </a>
                                            <a href="https://wa.me/51{{ preg_replace('/\D/', '', $loan->user->phone) }}" target="_blank" class="flex items-center gap-1.5 bg-green-500 hover:bg-green-600 transition-colors px-2 py-1 text-white font-bold">
                                                {{ $loan->user->phone }}
                                                <svg class="w-3.5 h-3.5 flex-shrink-0" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M28 16C28 22.6274 22.6274 28 16 28C13.4722 28 11.1269 27.2184 9.19266 25.8837L5.09091 26.9091L6.16576 22.8784C4.80092 20.9307 4 18.5589 4 16C4 9.37258 9.37258 4 16 4C22.6274 4 28 9.37258 28 16Z" fill="url(#wssp_grad_loan)"/>
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M16 30C23.732 30 30 23.732 30 16C30 8.26801 23.732 2 16 2C8.26801 2 2 8.26801 2 16C2 18.5109 2.661 20.8674 3.81847 22.905L2 30L9.31486 28.3038C11.1894 29.3854 13.5789 30 16 30ZM16 27.8462C22.5425 27.8462 27.8462 22.5425 27.8462 16C27.8462 9.45755 22.5425 4.15385 16 4.15385C9.45755 4.15385 4.15385 9.45755 4.15385 16C4.15385 18.5261 4.9445 20.8675 6.29184 22.7902L5.23077 26.7692L9.27993 25.7569C11.1894 27.0746 13.5046 27.8462 16 27.8462Z" fill="white"/>
                                                    <path d="M12.5 9.49989C12.1672 8.83131 11.6565 8.8905 11.1407 8.8905C10.2188 8.8905 8.78125 9.99478 8.78125 12.05C8.78125 13.7343 9.52345 15.578 12.0244 18.3361C14.438 20.9979 17.6094 22.3748 20.2422 22.3279C22.875 22.2811 23.4167 20.0154 23.4167 19.2503C23.4167 18.9112 23.2062 18.742 23.0613 18.696C22.1641 18.2654 20.5093 17.4631 20.1328 17.3124C19.7563 17.1617 19.5597 17.3656 19.4375 17.4765C19.0961 17.8018 18.4193 18.7608 18.1875 18.9765C17.9558 19.1922 17.6103 19.083 17.4665 19.0015C16.9374 18.7892 15.5029 18.1511 14.3595 17.0426C12.9453 15.6718 12.8623 15.2001 12.5959 14.7803C12.3828 14.4444 12.5392 14.2384 12.6172 14.1483C12.9219 13.7968 13.3426 13.254 13.5313 12.9843C13.7199 12.7145 13.5702 12.305 13.4803 12.05C13.0938 10.953 12.7663 10.0347 12.5 9.49989Z" fill="white"/>
                                                    <defs>
                                                        <linearGradient id="wssp_grad_loan" x1="26.5" y1="7" x2="4" y2="28" gradientUnits="userSpaceOnUse">
                                                            <stop stop-color="#5BD066"/>
                                                            <stop offset="1" stop-color="#27B43E"/>
                                                        </linearGradient>
                                                    </defs>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-6">
                                    <div class="text-right">
                                        <div class="flex items-center gap-2 justify-end mb-1">
                                            <p class="text-2xl font-black text-gray-900 dark:text-white tracking-tighter">
                                                {{ $loan->currency === 'USD' ? '$' : 'S/' }} {{ number_format($loan->amount, 2) }}
                                            </p>
                                            <span class="text-[8px] bg-black text-white dark:bg-white dark:text-black px-2 py-1 rounded font-black uppercase tracking-widest">
                                                {{ $loan->type === 'cuotas' ? 'CUOTAS' : 'ÚNICO' }}
                                            </span>
                                        </div>
                                        <p class="text-[10px] text-gray-400 dark:text-gray-600 font-medium">
                                            {{ $loan->created_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                    @if($loan->status === 'pending')
                                        <div class="flex items-center gap-3 pl-6 border-l border-gray-200 dark:border-gray-800">
                                            <button type="button"
                                                    onclick="openCounterOfferModal({{ json_encode([
                                                        'id' => $loan->id,
                                                        'amount' => $loan->amount,
                                                        'currency' => $loan->currency,
                                                        'type' => $loan->type,
                                                        'months' => $loan->months,
                                                        'payment_days' => $loan->payment_days,
                                                        'interest_rate' => $loan->interest_rate,
                                                        'system' => $loan->amortization_system ?? 'frances',
                                                        'monthly_payment' => $loan->monthly_payment ?? 0,
                                                        'payment_frequency' => $loan->payment_frequency ?? 'mensual'
                                                    ]) }})"
                                                    class="text-purple-600 hover:text-purple-400 transition-colors active:scale-[0.95]"
                                                    title="Contra-oferta">
                                                <svg class="w-6 h-6" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" stroke-width="3" stroke="currentColor" fill="none"><polyline points="46.81 40.84 46.81 56.26 9.19 56.26 9.19 20.6 21.15 7.74 46.81 7.74 46.81 29.71" stroke-linecap="round"/><polyline points="21.17 7.74 21.15 20.6 9.19 20.6"/><line x1="14.34" y1="47.67" x2="31.35" y2="47.67"/><line x1="14.34" y1="41.14" x2="41.35" y2="41.14"/><line x1="14.34" y1="35.04" x2="35.35" y2="35.04"/><line x1="14.34" y1="28.94" x2="41.35" y2="28.94"/><polyline points="52.21 30.24 57.01 35.04 52.21 39.84" stroke-linecap="round"/><line x1="57.01" y1="35.04" x2="41.38" y2="35.04" stroke-linecap="round"/></svg>
                                            </button>
                                            <button type="button"
                                                    onclick="openApproveModal('{{ $loan->id }}', '{{ $loan->currency === 'USD' ? '$' : 'S/' }}{{ number_format($loan->amount, 2) }}', '{{ $loan->user->name }}')"
                                                    class="text-green-600 hover:text-green-400 transition-colors active:scale-[0.95]"
                                                    title="Aprobar">
                                                <svg class="w-6 h-6" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><path d="M20,2H6A2,2,0,0,0,4,4V17H4a2,2,0,0,0-2,2H2a3,3,0,0,0,3,3H19.5A2.5,2.5,0,0,0,22,19.5V4A2,2,0,0,0,20,2Z" opacity="0.3"/><path d="M19,22v0H5a3,3,0,0,1-3-3H2a2,2,0,0,1,2-2H15a2,2,0,0,1,2,2v.5A2.52,2.52,0,0,0,19,22ZM12.71,11.71l4-4a1,1,0,1,0-1.42-1.42L12,9.59l-1.29-1.3A1,1,0,0,0,9.29,9.71l2,2a1,1,0,0,0,1.42,0Z"/></svg>
                                            </button>
                                            <button type="button"
                                                    onclick="openRejectModal('{{ $loan->id }}', '{{ $loan->currency === 'USD' ? '$' : 'S/' }}{{ number_format($loan->amount, 2) }}', '{{ $loan->user->name }}')"
                                                    class="text-red-600 hover:text-red-400 transition-colors active:scale-[0.95]"
                                                    title="Rechazar">
                                                <svg class="w-6 h-6" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.25 1.5a.75.75 0 00-.75.75v11.5c0 .414.336.75.75.75h4.5a.75.75 0 010 1.5h-4.5A2.25 2.25 0 011 13.75V2.25A2.25 2.25 0 013.25 0h6.293c.331 0 .65.132.884.366l4.207 4.207c.234.235.366.553.366.884V7.5a.75.75 0 01-1.5 0V6H9.75A.75.75 0 019 5.25V1.5H3.25zm7.25 1.06l1.94 1.94H10.5V2.56z"/><path d="M11.53 14.53l.97-.97.97.97a.75.75 0 101.06-1.06l-.97-.97.97-.97a.75.75 0 10-1.06-1.06l-.97.97-.97-.97a.75.75 0 10-1.06 1.06l.97.97-.97.97a.75.75 0 101.06 1.06z"/></svg>
                                            </button>
                                        </div>
                                    @else
                                        <div class="pl-6 border-l border-gray-200 dark:border-gray-800">
                                            <span class="text-xs font-black uppercase tracking-widest
                                                        {{ $loan->status === 'approved' ? 'text-green-500' : '' }}
                                                        {{ $loan->status === 'rejected' ? 'text-red-500' : '' }}
                                                        {{ $loan->status === 'paid' ? 'text-blue-500' : '' }}">
                                                {{ match($loan->status) {
                                                    'approved' => 'APROBADO',
                                                    'pending'  => 'EN REVISIÓN',
                                                    'rejected' => 'RECHAZADO',
                                                    'paid'     => 'PAGADO',
                                                    default    => $loan->status
                                                } }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if($loan->user->clientProfile)
                                @php $profile = $loan->user->clientProfile; @endphp

                                <!-- Fila 2: Perfil Académico y Laboral -->
                                <div class="grid grid-cols-4 gap-6 py-4 border-y border-gray-100 dark:border-gray-800">
                                    <div>
                                        <p class="text-[9px] text-gray-400 dark:text-gray-600 uppercase font-black mb-1.5 tracking-[0.15em]">Educación</p>
                                        <p class="text-[10px] text-gray-900 dark:text-white font-bold leading-tight mb-0.5">{{ $profile->academic_status }}</p>
                                        @if($profile->institution)
                                            <p class="text-[9px] text-gray-500 dark:text-gray-500 leading-tight">{{ $profile->institution }}</p>
                                        @endif
                                        @if($profile->career)
                                            <p class="text-[9px] text-gray-500 dark:text-gray-500 leading-tight">{{ $profile->career }}</p>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-[9px] text-gray-400 dark:text-gray-600 uppercase font-black mb-1.5 tracking-[0.15em]">Trabajo</p>
                                        @if($profile->is_employed)
                                            <p class="text-[10px] text-gray-900 dark:text-white font-bold leading-tight mb-0.5">{{ ucfirst($profile->employment_type) }}</p>
                                            @if($profile->employment_type === 'dependiente')
                                                @if($profile->workplace)
                                                    <p class="text-[9px] text-gray-500 dark:text-gray-500 leading-tight">{{ $profile->workplace }}</p>
                                                @endif
                                                @if($profile->job_title)
                                                    <p class="text-[9px] text-gray-500 dark:text-gray-500 leading-tight">{{ $profile->job_title }}</p>
                                                @endif
                                            @elseif($profile->independent_type)
                                                <p class="text-[9px] text-gray-500 dark:text-gray-500 leading-tight">{{ ucfirst($profile->independent_type) }}</p>
                                            @endif
                                        @else
                                            <p class="text-[9px] text-gray-500 dark:text-gray-500 leading-tight">No trabaja</p>
                                        @endif
                                        <p class="text-[10px] text-green-600 dark:text-green-500 font-bold leading-tight mt-1">S/ {{ number_format($profile->monthly_income, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[9px] text-gray-400 dark:text-gray-600 uppercase font-black mb-1.5 tracking-[0.15em]">Estado Civil</p>
                                        <p class="text-[10px] text-gray-900 dark:text-white font-bold leading-tight mb-0.5">
                                            {{ match($profile->marital_status) {
                                                'soltero' => 'Soltero/a',
                                                'casado_separado' => 'Casado (B.S)',
                                                'casado_compartido' => 'Casado (B.C)',
                                                default => $profile->marital_status
                                            } }}
                                        </p>
                                        @if($profile->marital_status === 'casado_compartido' && ($profile->spouse_first_name || $profile->spouse_last_name))
                                            <p class="text-[9px] text-gray-500 dark:text-gray-500 leading-tight">{{ $profile->spouse_first_name }} {{ $profile->spouse_last_name }}</p>
                                            @if($profile->spouse_dni)
                                                <p class="text-[9px] text-gray-500 dark:text-gray-500 leading-tight">DNI: {{ $profile->spouse_dni }}</p>
                                            @endif
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-[9px] text-gray-400 dark:text-gray-600 uppercase font-black mb-1.5 tracking-[0.15em]">Hijos</p>
                                        <p class="text-[10px] text-gray-900 dark:text-white font-bold leading-tight">
                                            @if($profile->has_children)
                                                {{ $profile->children_count }} {{ $profile->children_count == 1 ? 'hijo' : 'hijos' }}
                                            @else
                                                Sin hijos
                                            @endif
                                        </p>
                                        @if($profile->instagram)
                                            <a href="https://instagram.com/{{ ltrim($profile->instagram, '@') }}" target="_blank" class="text-[9px] text-purple-600 dark:text-purple-400 hover:text-purple-500 transition-colors leading-tight flex items-center gap-1 mt-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                                {{ $profile->instagram }}
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                <!-- Fila 3: Detalles del Préstamo -->
                                <div class="grid grid-cols-3 gap-6">
                                    <div>
                                        <p class="text-[9px] text-gray-400 dark:text-gray-600 uppercase font-black mb-1.5 tracking-[0.15em]">Destino</p>
                                        <p class="text-[10px] font-bold text-gray-900 dark:text-white uppercase leading-tight mb-0.5">{{ $loan->bank_name }}</p>
                                        <p class="text-[9px] text-gray-500 dark:text-gray-500 leading-tight">{{ ucfirst($loan->account_type) }} • {{ $loan->account_number }}</p>
                                        @if($loan->cci)
                                            <p class="text-[8px] font-mono text-gray-400 dark:text-gray-600 leading-tight mt-0.5">{{ $loan->cci }}</p>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-1.5 mb-1.5">
                                            <p class="text-[9px] text-gray-400 dark:text-gray-600 uppercase font-black tracking-[0.15em]">Términos</p>
                                            @if($loan->type === 'cuotas')
                                                <button type="button"
                                                    onclick="openScheduleModal({{ json_encode([
                                                        'amount' => $loan->amount,
                                                        'currency' => $loan->currency,
                                                        'type' => $loan->type,
                                                        'months' => $loan->months,
                                                        'payment_days' => $loan->payment_days,
                                                        'interest_rate' => $loan->interest_rate,
                                                        'system' => $loan->amortization_system ?? 'frances',
                                                        'monthly_payment' => $loan->monthly_payment ?? 0,
                                                        'payment_frequency' => $loan->payment_frequency ?? 'mensual',
                                                        'user_name' => $loan->user->name,
                                                    ]) }})"
                                                    class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors"
                                                    title="Ver cronograma">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </button>
                                            @endif
                                        </div>
                                        <p class="text-[10px] text-gray-900 dark:text-white font-bold leading-tight mb-0.5">
                                            @if($loan->type === 'cuotas')
                                                {{ $loan->months }} meses
                                                @if($loan->amortization_system === 'frances')
                                                    <span class="text-[9px] text-gray-500 font-normal">({{ $loan->payment_frequency }})</span>
                                                @else
                                                    <span class="text-[9px] text-gray-500 font-normal">(americano)</span>
                                                @endif
                                            @else
                                                {{ $loan->payment_days }} días
                                            @endif
                                        </p>
                                        @php
                                            $freqLabelDesktop = match($loan->payment_frequency ?? 'mensual') {
                                                'diario' => 'día',
                                                'semanal' => 'semana',
                                                'quincenal' => 'quincena',
                                                default => 'mes'
                                            };
                                        @endphp
                                        <p class="text-[9px] text-gray-600 dark:text-gray-400 leading-tight">
                                            Cuota/{{ $loan->type === 'cuotas' ? $freqLabelDesktop : 'pago' }}: <span class="font-bold text-gray-900 dark:text-white">{{ $loan->currency === 'USD' ? '$' : 'S/' }}{{ number_format($loan->monthly_payment, 2) }}</span>
                                        </p>
                                        <p class="text-[9px] text-green-600 dark:text-green-500 font-bold leading-tight mt-0.5">
                                            Ganancia: {{ $loan->currency === 'USD' ? '$' : 'S/' }}{{ number_format($loan->total_amount - $loan->amount, 2) }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-[9px] text-gray-400 dark:text-gray-600 uppercase font-black mb-1.5 tracking-[0.15em]">Motivo</p>
                                        <p class="text-[9px] text-gray-500 dark:text-gray-500 italic leading-relaxed">
                                            "{{ $loan->reason }}"
                                        </p>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-6 border-y border-gray-100 dark:border-gray-800">
                                    <p class="text-sm text-gray-400 dark:text-gray-600 italic">Perfil incompleto</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-16 text-center">
                        <p class="text-sm text-gray-400 dark:text-gray-600 italic">No hay préstamos para mostrar.</p>
                    </div>
                @endforelse
            </div>

            <!-- Paginación -->
            <div class="mt-10">
                {{ $loans->links() }}
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
    </style>
<!-- Modal Contra-Oferta -->
<div id="counterOfferModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-950 w-full max-w-3xl max-h-[90vh] flex flex-col rounded-3xl md:rounded-[2.5rem] shadow-2xl relative animate-in fade-in zoom-in duration-200">
        <!-- Close -->
        <button onclick="closeCounterOfferModal()" class="absolute top-5 right-5 md:top-8 md:right-8 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-900 transition-colors z-20">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <form id="counterOfferForm" method="POST" class="flex flex-col min-h-0 overflow-hidden">
            @csrf
            <div class="p-5 md:p-8 flex flex-col min-h-0 overflow-hidden">
                <div class="mb-6 pr-12 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                    <div>
                        <h3 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Contra-oferta</h3>
                        <p class="text-[10px] text-gray-500 mt-1 uppercase tracking-widest font-medium leading-relaxed">Ajuste de términos financieros</p>
                    </div>
                    <div id="modal_display_amount" class="font-black text-right space-y-0.5"></div>
                </div>

                <div id="modalInputsGrid" class="grid grid-cols-1 md:grid-cols-5 gap-4 md:gap-6 mb-6">
                    <!-- Monto -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-[0.2em] block">Monto</label>
                        <div class="relative">
                            <input type="text" name="amount" id="modal_amount"
                                   class="w-full bg-transparent border-0 border-b-2 border-gray-100 dark:border-gray-900 py-2 pr-10 text-lg font-black text-gray-900 dark:text-white focus:outline-none focus:border-purple-500 transition-colors">
                            <span id="modal_currency_symbol" class="absolute right-0 top-1/2 -translate-y-1/2 text-gray-400 font-bold"></span>
                        </div>
                    </div>

                    <!-- Meses -->
                    <div id="modalMonthsWrapper" class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-[0.2em] block">Plazo (Meses)</label>
                        <div class="relative">
                            <input type="number" name="months" id="modal_months" min="3" max="24" step="1"
                                   onkeypress="return (event.charCode >= 48 && event.charCode <= 57)"
                                   oninput="if(this.value > 24) this.value = 24; calculatePreview();"
                                   onblur="if(this.value && this.value < 3) this.value = 3; calculatePreview();"
                                   class="w-full bg-transparent border-0 border-b-2 border-gray-100 dark:border-gray-900 py-2 pr-10 text-lg font-black text-gray-900 dark:text-white focus:outline-none focus:border-purple-500 transition-colors">
                            <span class="absolute right-0 top-1/2 -translate-y-1/2 text-gray-400 font-bold">M</span>
                        </div>
                    </div>

                    <!-- Tasa -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-[0.2em] block">Tasa (%)</label>
                        <div class="relative">
                            <input type="number" name="interest_rate" id="modal_interest_rate" step="0.01" min="0" max="100"
                                   onkeypress="return (event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46)"
                                   oninput="let start = this.selectionStart;
                                            let oldVal = this.value;
                                            let val = oldVal.replace(/[^0-9.]/g, '');
                                            let parts = val.split('.');
                                            if(parts[0].length > 2) {
                                                val = parts[0].slice(0, 2) + '.' + parts[0].slice(2) + (parts[1] || '');
                                            }
                                            parts = val.split('.');
                                            if(parts[1] && parts[1].length > 2) {
                                                val = parts[0] + '.' + parts[1].slice(0, 2);
                                            }
                                            if(val.length > 1 && val.startsWith('0') && val[1] !== '.') val = val.replace(/^0+/, '');
                                            if (oldVal !== val) {
                                                this.value = val;
                                                this.setSelectionRange(start, start);
                                            }
                                            calculatePreview();"
                                   class="w-full bg-transparent border-0 border-b-2 border-gray-100 dark:border-gray-900 py-2 pr-10 text-lg font-black text-gray-900 dark:text-white focus:outline-none focus:border-purple-500 transition-colors">
                            <span class="absolute right-0 top-1/2 -translate-y-1/2 text-gray-400 font-bold">%</span>
                        </div>
                    </div>

                    <!-- Sistema -->
                    <div id="modalSystemWrapper" class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-[0.2em] block">Sistema</label>
                        <div class="flex gap-2 border-b border-gray-100 dark:border-gray-900">
                            <label class="flex-1 cursor-pointer group">
                                <input type="radio" name="amortization_system" value="frances" class="hidden peer" onchange="toggleModalFrequency(); calculatePreview()">
                                <div class="py-2 text-center text-[10px] font-black tracking-widest text-gray-400 dark:text-gray-600 peer-checked:text-purple-600 transition-all border-b-2 border-transparent peer-checked:border-purple-600 hover:text-gray-600 dark:hover:text-gray-400">
                                    FRANCÉS
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer group">
                                <input type="radio" name="amortization_system" value="americano" class="hidden peer" onchange="toggleModalFrequency(); calculatePreview()">
                                <div class="py-2 text-center text-[10px] font-black tracking-widest text-gray-400 dark:text-gray-600 peer-checked:text-purple-600 transition-all border-b-2 border-transparent peer-checked:border-purple-600 hover:text-gray-600 dark:hover:text-gray-400">
                                    AMERICANO
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Frecuencia -->
                    <div id="modalFrequencyWrapper" class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-[0.2em] block">Frecuencia</label>
                        <div class="flex gap-2 border-b border-gray-100 dark:border-gray-900">
                            @foreach(['diario' => 'DÍA', 'semanal' => 'SEM', 'quincenal' => 'QUIN', 'mensual' => 'MES'] as $val => $label)
                                <label class="flex-1 cursor-pointer group">
                                    <input type="radio" name="payment_frequency" value="{{ $val }}" class="hidden peer" onchange="calculatePreview()">
                                    <div class="py-2 text-center text-[10px] font-black tracking-widest text-gray-400 dark:text-gray-600 peer-checked:text-purple-600 transition-all border-b-2 border-transparent peer-checked:border-purple-600 hover:text-gray-600 dark:hover:text-gray-400">
                                        {{ $label }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Previews -->
                <div class="flex-1 min-h-0 flex flex-col space-y-4">
                    <div class="flex items-center justify-between px-1 flex-shrink-0">
                        <h4 class="text-[10px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-[0.2em]">Simulación de Resultados</h4>
                        <div class="flex gap-4">
                            <p class="text-[10px] text-gray-500 font-bold uppercase">Total: <span id="previewTotal" class="text-gray-900 dark:text-white tabular-nums"></span></p>
                            <p class="text-[10px] text-gray-500 font-bold uppercase">Cuota: <span id="previewMonthly" class="text-gray-900 dark:text-white tabular-nums"></span></p>
                            <p class="text-[10px] text-green-600 font-black uppercase">Ganancia: <span id="previewGain" class="tabular-nums"></span></p>
                        </div>
                    </div>

                    <!-- Cuotas Preview -->
                    <div id="previewCuotasContainer" class="flex-1 min-h-0 flex flex-col">
                        <div class="flex-1 overflow-y-auto custom-scrollbar">
                            <table class="w-full text-[10px] text-left">
                                <thead class="sticky top-0 bg-white dark:bg-gray-950 text-gray-400 dark:text-gray-600 shadow-sm">
                                    <tr>
                                        <th class="px-4 py-3 font-black uppercase tracking-tighter text-center">N°</th>
                                        <th class="px-4 py-3 font-black uppercase tracking-tighter text-center">Capital</th>
                                        <th class="px-4 py-3 font-black uppercase tracking-tighter text-center">Interés</th>
                                        <th class="px-4 py-3 font-black uppercase tracking-tighter text-center">Saldo</th>
                                    </tr>
                                </thead>
                                <tbody id="previewScheduleBody" class="divide-y divide-gray-50 dark:divide-gray-900 text-gray-600 dark:text-gray-400">
                                    <!-- JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Único Preview -->
                    <div id="previewUnicoContainer" class="hidden space-y-4 py-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 rounded-2xl border border-gray-100 dark:border-gray-900 relative">
                                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest mb-1">Original (<span id="unicoClientRate"></span>%)</p>
                                <p id="unicoClientTotal" class="text-xl font-bold text-gray-400 line-through tabular-nums text-center"></p>
                            </div>
                            <div class="p-4 rounded-2xl bg-purple-50 dark:bg-purple-900/10 border border-purple-100 dark:border-purple-900/40 relative">
                                <p class="text-[10px] text-purple-600 uppercase font-black tracking-widest mb-1">Nueva Oferta (<span id="unicoAdminRate"></span>%)</p>
                                <p id="unicoAdminTotal" class="text-xl font-black text-purple-600 tabular-nums text-center"></p>
                            </div>
                        </div>
                        <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-900 text-center border border-gray-100 dark:border-gray-800">
                            <p class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-1">Fecha límite de pago estimada</p>
                            <p id="unicoDeadline" class="text-sm font-bold text-gray-900 dark:text-white capitalize"></p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end flex-shrink-0">
                    <button type="button" onclick="openCounterOfferConfirmModal()" class="text-sm font-bold text-purple-600 dark:text-purple-400 border-b-2 border-purple-600 dark:border-purple-400 pb-1 hover:opacity-70 transition-all uppercase tracking-widest bg-transparent">
                        Confirmar y Enviar Contra-oferta
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    let currentLoan = null;

    function openCounterOfferModal(loan) {
        currentLoan = loan;
        const modal = document.getElementById('counterOfferModal');
        const form = document.getElementById('counterOfferForm');

        form.action = `/gestion-interna/prestamos/${loan.id}/counter-offer`;

        // Set initial values
        const systemWrapper = document.getElementById('modalSystemWrapper');
        const freqWrapper = document.getElementById('modalFrequencyWrapper');
        const monthsWrapper = document.getElementById('modalMonthsWrapper');
        const inputsGrid = document.getElementById('modalInputsGrid');

        // Format initial amount
        const initialAmount = parseFloat(loan.amount).toFixed(2).replace('.', '');
        formatAmountInput(initialAmount);

        document.getElementById('modal_currency_symbol').textContent = loan.currency === 'USD' ? '$' : 'S/';

        if (loan.type === 'unico') {
            systemWrapper.classList.add('hidden');
            freqWrapper.classList.add('hidden');
            monthsWrapper.classList.add('hidden');
            inputsGrid.classList.remove('md:grid-cols-5');
            inputsGrid.classList.add('md:grid-cols-2');
            // Set default to pass backend validation even if hidden
            const radio = document.querySelector('input[name="amortization_system"][value="frances"]');
            if (radio) radio.checked = true;
        } else {
            systemWrapper.classList.remove('hidden');
            freqWrapper.classList.remove('hidden');
            monthsWrapper.classList.remove('hidden');
            inputsGrid.classList.remove('md:grid-cols-2');
            inputsGrid.classList.add('md:grid-cols-5');

            document.getElementById('modal_months').value = loan.months;

            const systemToSet = loan.system || 'frances';
            const radio = document.querySelector(`input[name="amortization_system"][value="${systemToSet}"]`);
            if (radio) radio.checked = true;

            const freqToSet = loan.payment_frequency || 'mensual';
            const freqRadio = document.querySelector(`input[name="payment_frequency"][value="${freqToSet}"]`);
            if (freqRadio) freqRadio.checked = true;

            toggleModalFrequency();
        }

        document.getElementById('modal_interest_rate').value = parseFloat(loan.interest_rate).toFixed(2);

        // Show amount
        const amountFormatted = parseFloat(loan.amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        const currencySymbol = loan.currency === 'USD' ? '$' : 'S/';
        let subtitleText = `<span class="block text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest">PEDIDO ORIGINAL: ${currencySymbol} ${amountFormatted}</span>`;
        if (loan.type === 'cuotas' && loan.monthly_payment) {
            const monthlyFormatted = parseFloat(loan.monthly_payment).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            subtitleText += `<span class="block text-[10px] text-amber-500 dark:text-amber-400 uppercase tracking-widest">CUOTA ORIGINAL: ${currencySymbol} ${monthlyFormatted}</span>`;
        }
        document.getElementById('modal_display_amount').innerHTML = subtitleText;

        calculatePreview();

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function toggleModalFrequency() {
        const system = document.querySelector('input[name="amortization_system"]:checked')?.value;
        const freqWrapper = document.getElementById('modalFrequencyWrapper');
        if (freqWrapper) {
            freqWrapper.style.display = system === 'frances' ? 'block' : 'none';
        }
    }

    function closeCounterOfferModal() {
        document.getElementById('counterOfferModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function formatAmountInput(val) {
        const input = document.getElementById('modal_amount');
        let cleanVal = val.replace(/\D/g, '');

        if (cleanVal.length === 0) {
            cleanVal = '000';
        } else {
            while (cleanVal.length < 3) {
                cleanVal = '0' + cleanVal;
            }
        }

        let integerPart = cleanVal.slice(0, -2);
        let decimalPart = cleanVal.slice(-2);

        integerPart = parseInt(integerPart, 10).toString();
        integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ",");

        input.value = integerPart + '.' + decimalPart;
    }

    document.getElementById('modal_amount').addEventListener('input', function(e) {
        formatAmountInput(this.value);
        calculatePreview();
    });

    document.getElementById('modal_amount').addEventListener('keydown', function(e) {
        if (e.key === '-' || e.key === 'e') {
            e.preventDefault();
        }
    });

    document.getElementById('counterOfferForm').addEventListener('submit', function() {
        const input = document.getElementById('modal_amount');
        input.value = input.value.replace(/,/g, '');
    });

    function calculatePreview() {
        if (!currentLoan) return;

        let monthsInput = document.getElementById('modal_months');
        let months = parseInt(monthsInput.value) || 3;

        if (months < 3) months = 3;
        if (months > 24) months = 24;

        const amount = parseFloat(document.getElementById('modal_amount').value.replace(/,/g, '')) || 0;
        const rate = parseFloat(document.getElementById('modal_interest_rate').value) / 100 || 0;
        const systemRadio = document.querySelector('input[name="amortization_system"]:checked');
        const system = systemRadio ? systemRadio.value : 'frances';
        const freqRadio = document.querySelector('input[name="payment_frequency"]:checked');
        const freq = freqRadio ? freqRadio.value : 'mensual';
        const currency = currentLoan.currency === 'USD' ? '$' : 'S/';

        const body = document.getElementById('previewScheduleBody');
        const totalEl = document.getElementById('previewTotal');
        const monthlyEl = document.getElementById('previewMonthly');
        const gainEl = document.getElementById('previewGain');
        const subtitleEl = document.getElementById('modal_display_amount');

        body.innerHTML = '';

        const amountObj = { minimumFractionDigits: 2, maximumFractionDigits: 2 };

        if (currentLoan.type === 'unico') {
            document.getElementById('previewCuotasContainer').classList.add('hidden');
            document.getElementById('previewUnicoContainer').classList.remove('hidden');

            const rateClient = parseFloat(currentLoan.interest_rate) || 0;
            const clientTotal = parseFloat(currentLoan.amount) + (parseFloat(currentLoan.amount) * (rateClient / 100));
            const adminTotal = amount + (amount * rate);

            document.getElementById('unicoClientRate').textContent = rateClient.toFixed(2);
            document.getElementById('unicoClientTotal').textContent = `${currency} ${clientTotal.toLocaleString('en-US', amountObj)}`;

            document.getElementById('unicoAdminRate').textContent = (rate * 100).toFixed(2);
            document.getElementById('unicoAdminTotal').textContent = `${currency} ${adminTotal.toLocaleString('en-US', amountObj)}`;

            const days = parseInt(currentLoan.payment_days) || 15;
            const date = new Date();
            date.setDate(date.getDate() + days);

            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('unicoDeadline').textContent = date.toLocaleDateString('es-PE', options);

            gainEl.textContent = `${currency} ${(adminTotal - amount).toLocaleString('en-US', amountObj)}`;
            return;
        } else {
            document.getElementById('previewCuotasContainer').classList.remove('hidden');
            document.getElementById('previewUnicoContainer').classList.add('hidden');
        }

        // Loan with installments
        let installmentsPerMonth = 1;
        if (system === 'frances') {
            if (freq === 'diario') installmentsPerMonth = 30;
            else if (freq === 'semanal') installmentsPerMonth = 4;
            else if (freq === 'quincenal') installmentsPerMonth = 2;
        }

        const n = months * installmentsPerMonth;
        const periodicRate = rate / installmentsPerMonth;

        let schedule = [];
        let totalInterest = 0;
        let periodicPayment = 0;
        let pmtText = '';

        if (system === 'frances') {
            // French: Fixed installments
            if (amount > 0 && rate > 0) {
                periodicPayment = amount * (periodicRate * Math.pow(1 + periodicRate, n)) / (Math.pow(1 + periodicRate, n) - 1);
            } else {
                periodicPayment = amount / n;
            }

            let balance = amount;
            for (let i = 1; i <= n; i++) {
                const interest = balance * periodicRate;
                const principal = periodicPayment - interest;
                balance -= principal;
                totalInterest += interest;
                if (i <= 10 || i > n - 5) {
                    schedule.push({ n: i, principal, interest, balance: Math.max(0, balance) });
                } else if (i === 11) {
                    schedule.push({ n: '...', principal: 0, interest: 0, balance: 0, spacer: true });
                }
            }
            pmtText = `${currency}${periodicPayment.toFixed(2)} / ${freq}`;
        } else {
            // American: Fixed interest payments, principal at the end
            const interestPerMonth = amount * rate;
            periodicPayment = interestPerMonth;
            let balance = amount;
            for (let i = 1; i <= months; i++) {
                const interest = interestPerMonth;
                const principal = (i === months) ? amount : 0;
                balance -= principal;
                totalInterest += interest;
                schedule.push({ n: i, principal, interest, balance: Math.max(0, balance) });
            }
            const finalPmt = amount + periodicPayment;
            pmtText = `${currency}${periodicPayment.toFixed(2)} (Final: ${currency}${finalPmt.toFixed(2)})`;
        }

        schedule.forEach(row => {
            if (row.spacer) {
                body.innerHTML += `<tr><td colspan="4" class="text-center py-2 text-gray-400">...</td></tr>`;
                return;
            }
            body.innerHTML += `
                <tr class="border-b border-gray-50 dark:border-gray-900/50 last:border-0">
                    <td class="py-3 font-bold text-center">${row.n}</td>
                    <td class="py-3 tabular-nums text-center">${currency}${row.principal.toFixed(2)}</td>
                    <td class="py-3 tabular-nums text-center">${currency}${row.interest.toFixed(2)}</td>
                    <td class="py-3 tabular-nums text-center font-black text-gray-900 dark:text-white">${currency}${row.balance.toFixed(2)}</td>
                </tr>
            `;
        });

        totalEl.textContent = `${currency}${(amount + totalInterest).toFixed(2)}`;
        monthlyEl.textContent = pmtText;
        gainEl.textContent = `${currency} ${totalInterest.toLocaleString('en-US', amountObj)}`;
    }

    // Close on overlay click
    document.getElementById('counterOfferModal').addEventListener('click', e => {
        if (e.target === document.getElementById('counterOfferModal')) closeCounterOfferModal();
    });

    function openApproveModal(loanId, amount, user) {
        document.getElementById('approve_modal_amount').textContent = amount;
        document.getElementById('approve_modal_user').textContent = user;
        document.getElementById('approveConfirmForm').action = `/gestion-interna/prestamos/${loanId}/approve`;
        document.getElementById('approveConfirmModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeApproveModal() {
        document.getElementById('approveConfirmModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function openRejectModal(loanId, amount, user) {
        document.getElementById('reject_modal_amount').textContent = amount;
        document.getElementById('reject_modal_user').textContent = user;
        document.getElementById('rejectConfirmForm').action = `/gestion-interna/prestamos/${loanId}/reject`;
        document.getElementById('rejectConfirmModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeRejectModal() {
        document.getElementById('rejectConfirmModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function openCounterOfferConfirmModal() {
        document.getElementById('counterOfferConfirmModal').classList.remove('hidden');
    }

    function closeCounterOfferConfirmModal() {
        document.getElementById('counterOfferConfirmModal').classList.add('hidden');
    }

    function submitCounterOffer() {
        const form = document.getElementById('counterOfferForm');
        const input = document.getElementById('modal_amount');
        input.value = input.value.replace(/,/g, '');
        form.submit();
    }
</script>

<!-- Modal Confirmar Contra-oferta -->
<div id="counterOfferConfirmModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[120] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-950 w-full max-w-sm rounded-3xl shadow-2xl relative animate-in fade-in zoom-in duration-200 p-8 text-center">
        <div class="w-16 h-16 bg-purple-50 dark:bg-purple-900/20 text-purple-600 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Enviar Contra-oferta</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
            ¿Estás seguro de enviar esta nueva propuesta de términos financieros al cliente?
        </p>
        <div class="flex gap-3">
            <button onclick="closeCounterOfferConfirmModal()" class="flex-1 py-3 rounded-xl bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400 text-xs font-black uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-gray-800 transition-colors">
                Revisar
            </button>
            <button onclick="submitCounterOffer()" class="flex-1 py-3 rounded-xl bg-purple-600 text-white text-xs font-black uppercase tracking-widest hover:bg-purple-700 transition-colors shadow-lg shadow-purple-500/20">
                Enviar
            </button>
        </div>
    </div>
</div>

<!-- Modal Confirmar Aprobación -->
<div id="approveConfirmModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[110] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-950 w-full max-w-sm rounded-3xl shadow-2xl relative animate-in fade-in zoom-in duration-200 p-8 text-center">
        <div class="w-16 h-16 bg-green-50 dark:bg-green-900/20 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><path d="M20,2H6A2,2,0,0,0,4,4V17H4a2,2,0,0,0-2,2H2a3,3,0,0,0,3,3H19.5A2.5,2.5,0,0,0,22,19.5V4A2,2,0,0,0,20,2Z" opacity="0.3"/><path d="M19,22v0H5a3,3,0,0,1-3-3H2a2,2,0,0,1,2-2H15a2,2,0,0,1,2,2v.5A2.52,2.52,0,0,0,19,22ZM12.71,11.71l4-4a1,1,0,1,0-1.42-1.42L12,9.59l-1.29-1.3A1,1,0,0,0,9.29,9.71l2,2a1,1,0,0,0,1.42,0Z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Aprobar Préstamo</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
            ¿Confirmas la aprobación del préstamo por <span id="approve_modal_amount" class="font-bold text-gray-900 dark:text-white"></span> para <span id="approve_modal_user" class="font-bold text-gray-900 dark:text-white"></span>?
        </p>
        <div class="flex gap-3">
            <button onclick="closeApproveModal()" class="flex-1 py-3 rounded-xl bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400 text-xs font-black uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-gray-800 transition-colors">
                Cancelar
            </button>
            <form id="approveConfirmForm" method="POST" class="flex-1">
                @csrf
                <button type="submit" class="w-full py-3 rounded-xl bg-green-600 text-white text-xs font-black uppercase tracking-widest hover:bg-green-700 transition-colors shadow-lg shadow-green-500/20">
                    Aprobar
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal Confirmar Rechazo -->
<div id="rejectConfirmModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[110] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-950 w-full max-w-sm rounded-3xl shadow-2xl relative animate-in fade-in zoom-in duration-200 p-8 text-center">
        <div class="w-16 h-16 bg-red-50 dark:bg-red-900/20 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.25 1.5a.75.75 0 00-.75.75v11.5c0 .414.336.75.75.75h4.5a.75.75 0 010 1.5h-4.5A2.25 2.25 0 011 13.75V2.25A2.25 2.25 0 013.25 0h6.293c.331 0 .65.132.884.366l4.207 4.207c.234.235.366.553.366.884V7.5a.75.75 0 01-1.5 0V6H9.75A.75.75 0 019 5.25V1.5H3.25zm7.25 1.06l1.94 1.94H10.5V2.56z"/><path d="M11.53 14.53l.97-.97.97.97a.75.75 0 101.06-1.06l-.97-.97.97-.97a.75.75 0 10-1.06-1.06l-.97.97-.97-.97a.75.75 0 10-1.06 1.06l.97.97-.97.97a.75.75 0 101.06 1.06z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Rechazar Préstamo</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
            ¿Estás seguro de que deseas rechazar la solicitud de <span id="reject_modal_user" class="font-bold text-gray-900 dark:text-white"></span> por <span id="reject_modal_amount" class="font-bold text-gray-900 dark:text-white"></span>?
        </p>
        <div class="flex gap-3">
            <button onclick="closeRejectModal()" class="flex-1 py-3 rounded-xl bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400 text-xs font-black uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-gray-800 transition-colors">
                Cancelar
            </button>
            <form id="rejectConfirmForm" method="POST" class="flex-1">
                @csrf
                <button type="submit" class="w-full py-3 rounded-xl bg-red-600 text-white text-xs font-black uppercase tracking-widest hover:bg-red-700 transition-colors shadow-lg shadow-red-500/20">
                    Rechazar
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #1f2937; }
</style>

<!-- Modal Cronograma -->
<div id="scheduleModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[130] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-black border border-gray-200 dark:border-gray-900 w-full max-w-2xl max-h-[90vh] flex flex-col rounded-3xl md:rounded-[2.5rem] shadow-2xl relative animate-in fade-in zoom-in duration-200">
        <button onclick="closeScheduleModal()" class="absolute top-5 right-5 md:top-7 md:right-7 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-900 transition-colors z-20">
            <svg class="w-4 h-4 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div class="p-6 md:p-8 flex flex-col min-h-0 overflow-hidden">
            <!-- Header -->
            <div class="mb-5 pr-10">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white tracking-tight">Cronograma de Pagos</h3>
                <div id="scheduleModalMeta" class="flex flex-wrap items-center gap-2 mt-2"></div>
            </div>
            <!-- Summary -->
            <div id="scheduleModalSummary" class="grid grid-cols-3 gap-3 mb-5"></div>
            <!-- Table -->
            <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar rounded-xl border border-gray-100 dark:border-gray-900">
                <table class="w-full text-[11px] text-left">
                    <thead class="sticky top-0 bg-gray-50 dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800">
                        <tr class="text-gray-400 dark:text-gray-600">
                            <th class="px-4 py-3 font-black uppercase tracking-tighter text-center">N°</th>
                            <th class="px-4 py-3 font-black uppercase tracking-tighter text-center">Capital</th>
                            <th class="px-4 py-3 font-black uppercase tracking-tighter text-center">Interés</th>
                            <th class="px-4 py-3 font-black uppercase tracking-tighter text-center">Cuota</th>
                            <th class="px-4 py-3 font-black uppercase tracking-tighter text-center">Saldo</th>
                        </tr>
                    </thead>
                    <tbody id="scheduleModalBody" class="divide-y divide-gray-50 dark:divide-gray-900"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function openScheduleModal(loan) {
        const modal = document.getElementById('scheduleModal');
        const body = document.getElementById('scheduleModalBody');
        const meta = document.getElementById('scheduleModalMeta');
        const summary = document.getElementById('scheduleModalSummary');

        const currency = loan.currency === 'USD' ? '$' : 'S/';
        const amount = parseFloat(loan.amount);
        const rate = parseFloat(loan.interest_rate) / 100;
        const system = loan.system || 'frances';
        const freq = loan.payment_frequency || 'mensual';
        const months = parseInt(loan.months) || 1;

        const freqLabels = { diario: 'Diario', semanal: 'Semanal', quincenal: 'Quincenal', mensual: 'Mensual' };
        const freqLabel = freqLabels[freq] || 'Mensual';

        // Meta info
        meta.innerHTML = `
            <span class="text-[10px] text-gray-500 dark:text-gray-400 font-medium">${loan.user_name}</span>
            <span class="text-gray-300 dark:text-gray-700">•</span>
            <span class="text-[10px] font-black text-gray-900 dark:text-white">${currency} ${amount.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</span>
            <span class="text-[10px] px-2 py-0.5 rounded-md bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400 font-bold uppercase border border-gray-200 dark:border-gray-800">${system}</span>
            <span class="text-[10px] px-2 py-0.5 rounded-md bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400 font-bold uppercase border border-gray-200 dark:border-gray-800">${freqLabel}</span>
        `;

        body.innerHTML = '';
        let fullSchedule = [];
        let totalInterest = 0;
        let periodicPayment = 0;

        const fmt = (n) => parseFloat(n).toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2});

        if (system === 'frances') {
            const installmentsPerMonth = { diario: 30, semanal: 4, quincenal: 2, mensual: 1 }[freq] || 1;
            const nTotal = months * installmentsPerMonth;
            const periodicRate = rate / installmentsPerMonth;

            periodicPayment = (amount > 0 && rate > 0)
                ? amount * (periodicRate * Math.pow(1 + periodicRate, nTotal)) / (Math.pow(1 + periodicRate, nTotal) - 1)
                : amount / nTotal;

            let balance = amount;
            for (let i = 1; i <= nTotal; i++) {
                const interest = balance * periodicRate;
                const principal = periodicPayment - interest;
                balance = Math.max(0, balance - principal);
                totalInterest += interest;
                fullSchedule.push({ n: i, principal, interest, cuota: periodicPayment, balance });
            }
        } else {
            const interestPerMonth = amount * rate;
            periodicPayment = interestPerMonth;
            let balance = amount;
            for (let i = 1; i <= months; i++) {
                const principal = (i === months) ? amount : 0;
                const cuota = (i === months) ? amount + interestPerMonth : interestPerMonth;
                balance = Math.max(0, balance - principal);
                totalInterest += interestPerMonth;
                fullSchedule.push({ n: i, principal, interest: interestPerMonth, cuota, balance });
            }
        }

        const totalAmount = amount + totalInterest;

        // Summary cards
        summary.innerHTML = `
            <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 text-center">
                <p class="text-[9px] text-gray-400 dark:text-gray-600 uppercase font-black tracking-widest mb-1">Total a pagar</p>
                <p class="text-sm font-black text-gray-900 dark:text-white tabular-nums">${currency} ${fmt(totalAmount)}</p>
            </div>
            <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 text-center">
                <p class="text-[9px] text-gray-400 dark:text-gray-600 uppercase font-black tracking-widest mb-1">Cuota / ${freqLabel}</p>
                <p class="text-sm font-black text-gray-900 dark:text-white tabular-nums">${currency} ${fmt(periodicPayment)}</p>
            </div>
            <div class="p-3 rounded-xl bg-green-50 dark:bg-green-950/30 border border-green-100 dark:border-green-900/40 text-center">
                <p class="text-[9px] text-green-600 dark:text-green-500 uppercase font-black tracking-widest mb-1">Ganancia</p>
                <p class="text-sm font-black text-green-600 dark:text-green-500 tabular-nums">${currency} ${fmt(totalInterest)}</p>
            </div>
        `;

        // Truncate: show first 10, ellipsis, last 5 (only if total > 15)
        const SHOW_FIRST = 10;
        const SHOW_LAST = 5;
        let displayRows = fullSchedule;
        let truncated = false;

        if (fullSchedule.length > SHOW_FIRST + SHOW_LAST) {
            displayRows = [
                ...fullSchedule.slice(0, SHOW_FIRST),
                null, // spacer
                ...fullSchedule.slice(-SHOW_LAST)
            ];
            truncated = true;
        }

        displayRows.forEach(row => {
            if (row === null) {
                body.innerHTML += `
                    <tr class="bg-gray-50 dark:bg-gray-900/50">
                        <td colspan="5" class="px-4 py-2 text-center text-[10px] text-gray-400 dark:text-gray-600 font-bold tracking-widest">
                            · · · ${fullSchedule.length - SHOW_FIRST - SHOW_LAST} cuotas intermedias · · ·
                        </td>
                    </tr>
                `;
                return;
            }
            body.innerHTML += `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/50 transition-colors">
                    <td class="px-4 py-2.5 font-bold text-center text-gray-900 dark:text-white">${row.n}</td>
                    <td class="px-4 py-2.5 tabular-nums text-center text-gray-600 dark:text-gray-400">${currency}${fmt(row.principal)}</td>
                    <td class="px-4 py-2.5 tabular-nums text-center text-amber-600 dark:text-amber-500">${currency}${fmt(row.interest)}</td>
                    <td class="px-4 py-2.5 tabular-nums text-center font-bold text-gray-900 dark:text-white">${currency}${fmt(row.cuota)}</td>
                    <td class="px-4 py-2.5 tabular-nums text-center text-gray-600 dark:text-gray-400">${currency}${fmt(row.balance)}</td>
                </tr>
            `;
        });

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeScheduleModal() {
        document.getElementById('scheduleModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    document.getElementById('scheduleModal').addEventListener('click', e => {
        if (e.target === document.getElementById('scheduleModal')) closeScheduleModal();
    });
</script>

@endsection
