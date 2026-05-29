@extends('superadmin.layout')

@section('title', 'Cobranzas')

@section('content')
    <div class="px-5 md:px-10 py-8">

        <!-- Header -->
        <div class="flex items-end justify-between mb-8">
            <div>
                <p class="text-xs text-gray-600 dark:text-gray-600 uppercase tracking-widest mb-1">Panel Administrativo</p>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Cobranzas</h1>
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

        @if($errors->any())
            <div class="mb-6 flex items-center gap-3 text-sm text-red-500">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                {{ $errors->first() }}
            </div>
        @endif

        @php $pendingCount = \App\Models\LoanInstallment::where('status', 'processing')->count(); @endphp

        <div class="mb-6 relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" id="cobranzas-search" placeholder="Buscar por nombre o DNI{{ in_array($filter, ['grouped', 'pending_validation', 'paid']) ? ', o código de préstamo' : '' }}..."
                   value="{{ request('search') }}"
                   class="w-full pl-10 pr-10 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-sm text-gray-900 dark:text-white placeholder:text-gray-400 focus:outline-none focus:border-gray-400 dark:focus:border-gray-600 transition-colors">
            <button type="button" id="cobranzas-search-clear" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors {{ request('search') ? '' : 'hidden' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <div id="cobranzas-search-spinner" class="absolute right-3 top-1/2 -translate-y-1/2 hidden">
                <svg class="w-4 h-4 animate-spin text-gray-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </div>
        </div>

        <!-- Filtros Mobile -->
        <div class="md:hidden mb-8">
            <div class="grid grid-cols-2 gap-2 mb-2">
                @foreach([
                    'all'     => ['label' => 'Personas',     'active' => 'bg-gray-900 text-white dark:bg-white dark:text-black', 'inactive' => 'bg-transparent text-gray-400 border-gray-200 dark:border-gray-800'],
                    'grouped' => ['label' => 'Por Grupo', 'active' => 'bg-gray-900 text-white dark:bg-white dark:text-black', 'inactive' => 'bg-transparent text-gray-400 border-gray-200 dark:border-gray-800'],
                ] as $key => $cfg)
                    <a href="{{ route('superadmin.installments.index', array_filter(['filter' => $key, 'search' => request('search')])) }}"
                       class="flex items-center justify-center py-2.5 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all border
                              {{ $filter === $key ? $cfg['active'] . ' border-transparent' : $cfg['inactive'] }}">
                        {{ $cfg['label'] }}
                    </a>
                @endforeach
            </div>
            <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('superadmin.installments.index', array_filter(['filter' => 'pending_validation', 'search' => request('search')])) }}"
                   class="flex items-center justify-center gap-2 py-2.5 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all border
                          {{ $filter === 'pending_validation' ? 'bg-purple-600 text-white border-transparent' : 'bg-transparent text-gray-400 border-gray-200 dark:border-gray-800' }}">
                    Por Validar
                    @if($pendingCount > 0)
                        <svg class="w-3.5 h-3.5 {{ $filter === 'pending_validation' ? 'text-white' : 'text-purple-600' }} animate-swing origin-top flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    @endif
                </a>
                <a href="{{ route('superadmin.installments.index', array_filter(['filter' => 'paid', 'search' => request('search')])) }}"
                   class="flex items-center justify-center py-2.5 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all border
                          {{ $filter === 'paid' ? 'bg-green-600 text-white border-transparent' : 'bg-transparent text-gray-400 border-gray-200 dark:border-gray-800' }}">
                    Pagadas
                </a>
            </div>
            <a href="{{ route('superadmin.installments.index', array_filter(['filter' => 'overdue', 'search' => request('search')])) }}"
               class="flex items-center justify-center gap-2 py-3 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all border w-full mt-2
                      {{ $totalOverdueCount > 0 ? 'bg-red-600 text-white border-transparent shadow-lg shadow-red-500/30' : ($filter === 'overdue' ? 'bg-red-600 text-white border-transparent' : 'bg-transparent text-red-500 border-red-200 dark:border-red-900') }}">
                <span>Cuotas vencidas</span>
                @if($totalOverdueCount > 0)
                    <span class="flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-white/20 text-[9px] font-black tabular-nums">{{ $totalOverdueCount }}</span>
                @endif
            </a>
        </div>

        @if($filter === 'paid')
        <div class="md:hidden flex items-center gap-2 mb-6">
            <input type="date" id="paid-date-filter-mobile"
                value="{{ request('paid_date') && request('paid_date') !== 'today' ? request('paid_date') : (request('paid_date') === 'today' ? now()->format('Y-m-d') : '') }}"
                class="flex-1 px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg text-sm text-gray-900 dark:text-white focus:outline-none focus:border-green-500 transition-colors">
            <button type="button" onclick="filterByPaidDate('today')"
                class="px-3 py-2.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-all border
                       {{ request('paid_date') === 'today' ? 'bg-green-600 text-white border-transparent' : 'bg-green-50 dark:bg-green-950/20 text-green-700 dark:text-green-400 border-green-200 dark:border-green-800' }}">
                Hoy
            </button>
            @if(request('paid_date'))
                <button type="button" onclick="clearPaidDateFilter()" class="p-2 rounded-lg text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            @endif
        </div>
        @endif

        <!-- Filtros Desktop -->
        <div class="hidden md:flex items-center gap-0 border-b border-gray-200 dark:border-gray-800 mb-8 overflow-x-auto no-scrollbar">
            @foreach(['all' => 'Personas', 'grouped' => 'Por Grupo', 'pending_validation' => 'Por Validar', 'paid' => 'Pagadas', 'overdue' => 'Cuotas vencidas'] as $key => $label)
                <a href="{{ route('superadmin.installments.index', array_filter(['filter' => $key, 'search' => request('search')])) }}"
                    class="relative flex-shrink-0 px-6 py-4 text-sm font-medium transition-colors flex items-center gap-2
                              {{ $filter === $key ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-400' }}">
                    {{ $label }}
                    @if($key === 'pending_validation' && $pendingCount > 0)
                        <svg class="w-3.5 h-3.5 text-purple-600 animate-swing origin-top flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    @endif
                    @if($key === 'overdue' && $totalOverdueCount > 0)
                        <span class="flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full {{ $filter === 'overdue' ? 'bg-red-500 text-white' : 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400' }} text-[9px] font-bold tabular-nums">{{ $totalOverdueCount }}</span>
                    @endif
                    @if($filter === $key)
                        <span class="absolute bottom-0 left-0 right-0 h-px {{ $key === 'paid' ? 'bg-green-500' : ($key === 'overdue' ? 'bg-red-600' : 'bg-gray-900 dark:bg-white') }}"></span>
                    @endif
                </a>
            @endforeach
        </div>

        @if($filter === 'paid')
        <div class="flex items-center gap-3 mb-6">
            <div class="flex items-center gap-2 flex-1 max-w-xs">
                <label class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">Fecha:</label>
                <input type="date" id="paid-date-filter"
                    value="{{ request('paid_date') && request('paid_date') !== 'today' ? request('paid_date') : (request('paid_date') === 'today' ? now()->format('Y-m-d') : '') }}"
                    class="flex-1 px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg text-sm text-gray-900 dark:text-white focus:outline-none focus:border-green-500 transition-colors">
            </div>
            <button type="button" onclick="filterByPaidDate('today')"
                class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all border
                       {{ request('paid_date') === 'today' ? 'bg-green-600 text-white border-transparent' : 'bg-green-50 dark:bg-green-950/20 text-green-700 dark:text-green-400 border-green-200 dark:border-green-800 hover:bg-green-100 dark:hover:bg-green-900/30' }}">
                Hoy
            </button>
            @if(request('paid_date'))
                <button type="button" onclick="clearPaidDateFilter()"
                    class="p-2 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            @endif
        </div>
        @endif

        @php
            $borrowers = $borrowers ?? null;
            $borrowersWithLoans = $borrowersWithLoans ?? [];
            $groupedInstallments = $groupedInstallments ?? [];
            $overdueDebtors = $overdueDebtors ?? [];
            $installments = $installments ?? collect();
            $loansPaginator = $loansPaginator ?? null;
        @endphp

        <div id="cobranzas-list">
        @if($filter === 'grouped')
            <!-- Vista Agrupada (2 columnas) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-16">
                @forelse($groupedInstallments as $group)
                    @php
                        $loan = $group['loan'];
                        $user = $loan->user;
                        $userInstallments = $group['installments'];
                        $selfie = $user->documents?->selfie;
                        $photo = $selfie ? Storage::url($selfie) : ($user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name));
                        $cur = $loan->currency === 'USD' ? '$' : 'S/';
                        $groupDetailData = [
                            'photo'             => $photo,
                            'name'              => $user->name,
                            'loanId'            => $loan->code,
                            'status'            => match($loan->status) { 'approved' => 'Activo', 'paid' => 'Completado', default => $loan->status },
                            'profileUrl'        => route('superadmin.users.show', $user),
                            'dni'               => $user->dni,
                            'ruc'               => $user->person_type === 'juridica' ? $user->ruc : null,
                            'businessName'      => $user->person_type === 'juridica' ? $user->business_name : null,
                            'phone'             => $user->phone,
                            'email'             => $user->email,
                            'address'           => $user->address,
                            'amount'            => $cur . ' ' . number_format($loan->amount, 2),
                            'total'             => $cur . ' ' . number_format($loan->total_amount, 2),
                            'rate'              => number_format($loan->interest_rate, 2) . '%',
                            'installmentsCount' => $loan->installments()->count() . ' cuotas',
                            'frequency'         => ucfirst($loan->payment_frequency),
                            'system'            => ucfirst($loan->amortization_system),
                            'bank'              => $loan->bank_name,
                            'account'           => $loan->account_number,
                            'reason'            => $loan->reason,
                        ];
                    @endphp
                    <div class="cobranzas-row" data-search-name="{{ strtolower($user->name) }}" data-search-dni="{{ $user->dni ?? '' }}" data-search-code="{{ strtolower($loan->code) }}">
                        <div class="flex items-center gap-4 pb-4 border-b border-gray-100 dark:border-gray-900 mb-2">
                            <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-900 flex items-center justify-center overflow-hidden border border-gray-100 dark:border-gray-800">
                                <img src="{{ $photo }}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight truncate">{{ $user->name }}</h3>
                                    <div class="flex items-center gap-1">
                                        <button type="button"
                                            onclick="openLoanDetailModal({{ json_encode($groupDetailData) }})"
                                            class="text-gray-900 dark:text-white hover:opacity-60 transition-opacity flex-shrink-0"
                                            title="Ver detalle">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M 12 4 C 8.6666667 4 5.7335794 5.6316279 3.6191406 7.3496094 C 2.5619212 8.2086001 1.7059096 9.0941755 1.09375 9.859375 C 0.78767021 10.241975 0.54228306 10.592509 0.35742188 10.916016 C 0.17256067 11.239523 3.7007434e-17 11.458333 0 12 C 0 12.541667 0.17256067 12.760477 0.35742188 13.083984 C 0.54228307 13.407491 0.78767021 13.758025 1.09375 14.140625 C 1.7059096 14.905824 2.5619212 15.7914 3.6191406 16.650391 C 5.7335794 18.368372 8.6666667 20 12 20 C 15.333333 20 18.266421 18.368372 20.380859 16.650391 C 21.438079 15.7914 22.29409 14.905824 22.90625 14.140625 C 23.21233 13.758025 23.457717 13.407491 23.642578 13.083984 C 23.827439 12.760477 24 12.541667 24 12 C 24 11.458333 23.827439 11.239523 23.642578 10.916016 C 23.457717 10.592509 23.21233 10.241975 22.90625 9.859375 C 22.29409 9.0941755 21.438079 8.2086001 20.380859 7.3496094 C 18.266421 5.6316279 15.333333 4 12 4 z M 12 6 C 14.666667 6 17.233579 7.3683721 19.119141 8.9003906 C 20.061921 9.6663999 20.83091 10.468324 21.34375 11.109375 C 21.60017 11.4299 21.792283 11.712179 21.904297 11.908203 C 21.926717 11.947439 21.926155 11.965225 21.939453 12 C 21.926155 12.034775 21.926717 12.052561 21.904297 12.091797 C 21.792283 12.287821 21.60017 12.5701 21.34375 12.890625 C 20.83091 13.531676 20.061921 14.3336 19.119141 15.099609 C 17.233579 16.631628 14.666667 18 12 18 C 9.3333333 18 6.7664206 16.631628 4.8808594 15.099609 C 3.9380788 14.3336 3.1690904 13.531676 2.65625 12.890625 C 2.3998298 12.5701 2.2077169 12.287821 2.0957031 12.091797 C 2.0732826 12.052561 2.073845 12.034775 2.0605469 12 C 2.073845 11.965225 2.0732826 11.947439 2.0957031 11.908203 C 2.2077169 11.712179 2.3998298 11.4299 2.65625 11.109375 C 3.1690904 10.468324 3.9380788 9.6663999 4.8808594 8.9003906 C 6.7664206 7.3683721 9.3333333 6 12 6 z M 11.939453 8 A 4 4 0 0 0 8 12 A 4 4 0 0 0 12 16 A 4 4 0 0 0 16 12 A 4 4 0 0 0 15.822266 10.824219 A 2 2 0 0 1 14 12 A 2 2 0 0 1 12 10 A 2 2 0 0 1 13.177734 8.1777344 A 4 4 0 0 0 12 8 A 4 4 0 0 0 11.939453 8 z"/></svg>
                                        </button>
                                        <button type="button"
                                            onclick="openLateFeeModal('{{ $loan->id }}', '{{ $loan->code }}', '{{ addslashes($user->name) }}', '{{ $loan->currency === 'USD' ? '$' : 'S/' }}', '{{ $loan->monthly_payment ?? 0 }}', {{ $loan->late_fee_enabled ? 'true' : 'false' }}, {{ $loan->grace_days ?? 0 }}, {{ $loan->late_fee_rate ?? 0 }}, {{ $loan->late_fee_daily_amount ?? 0 }})"
                                            class="text-orange-500 hover:opacity-60 transition-opacity flex-shrink-0"
                                            title="Configurar mora">
                                            <svg class="w-5 h-5 fill-current" viewBox="0 0 488.5 488.5" xmlns="http://www.w3.org/2000/svg" xml:space="preserve">
                                                <path d="M0,331.9V156.5c0-18.8,15.3-34.1,34.1-34.1h325.2c18.8,0,34.1,15.3,34.1,34.1v10.3h-22.6c-11.8,0-21.3,9.6-21.3,21.3v5.7
                                                    c-16.1-3-28.2-17-28.2-33.9c0-2.9,0.4-5.7,1-8.4H69.1c0.4,2.1,0.6,4.2,0.6,6.3c0,19.1-15.5,34.5-34.5,34.5c-2.1,0-4.1-0.2-6.1-0.5
                                                    v104.8c2-0.3,4-0.5,6.1-0.5c19.1,0,34.5,15.4,34.5,34.5c0,2.2-0.2,4.3-0.6,6.3h212l-0.1,12.4v13.4c0,1.1,0.2,2.3,0.3,3.4H34.1
                                                    C15.3,366,0,350.7,0,331.9z M415.9,345.9h-45.2c-1.9,0-3.4,1.5-3.4,3.4v13.4c0,1.9,1.5,3.4,3.4,3.4h45.2c1.9,0,3.4-1.5,3.4-3.4
                                                    v-13.5C419.3,347.4,417.8,345.9,415.9,345.9z M415.9,319h-45.2c-1.9,0-3.4,1.5-3.4,3.4v13.4c0,1.9,1.5,3.4,3.4,3.4h45.2
                                                    c1.9,0,3.4-1.5,3.4-3.4v-13.4C419.3,320.5,417.8,319,415.9,319z M415.9,292.2h-45.2c-1.9,0-3.4,1.5-3.4,3.4V309
                                                    c0,1.9,1.5,3.4,3.4,3.4h45.2c1.9,0,3.4-1.5,3.4-3.4v-13.4C419.3,293.7,417.8,292.2,415.9,292.2z M415.9,265.3h-45.2
                                                    c-1.9,0-3.4,1.5-3.4,3.4v13.4c0,1.9,1.5,3.4,3.4,3.4h45.2c1.9,0,3.4-1.5,3.4-3.4v-13.4C419.3,266.8,417.8,265.3,415.9,265.3z
                                                    M415.9,238.5h-45.2c-1.9,0-3.4,1.5-3.4,3.4v13.3c0,1.9,1.5,3.4,3.4,3.4h45.2c1.9,0,3.4-1.5,3.4-3.4v-13.4
                                                    C419.3,240,417.8,238.5,415.9,238.5z M415.9,211.6h-45.2c-1.9,0-3.4,1.5-3.4,3.4v13.4c0,1.9,1.5,3.4,3.4,3.4h45.2
                                                    c1.9,0,3.4-1.5,3.4-3.4V215C419.3,213.1,417.8,211.6,415.9,211.6z M415.9,184.8h-45.2c-1.9,0-3.4,1.5-3.4,3.4v13.4
                                                    c0,1.9,1.5,3.4,3.4,3.4h45.2c1.9,0,3.4-1.5,3.4-3.4v-13.4C419.3,186.3,417.8,184.8,415.9,184.8z M485.1,345.9h-45.2
                                                    c-1.9,0-3.4,1.5-3.4,3.4v13.4c0,1.9,1.5,3.4,3.4,3.4h45.2c1.9,0,3.4-1.5,3.4-3.4v-13.5C488.4,347.4,486.9,345.9,485.1,345.9z
                                                    M485.1,319h-45.2c-1.9,0-3.4,1.5-3.4,3.4v13.4c0,1.9,1.5,3.4,3.4,3.4h45.2c1.9,0,3.4-1.5,3.4-3.4v-13.4
                                                    C488.4,320.5,486.9,319,485.1,319z M485.1,292.2h-45.2c-1.9,0-3.4,1.5-3.4,3.4V309c0,1.9,1.5,3.4,3.4,3.4h45.2
                                                    c1.9,0,3.4-1.5,3.4-3.4v-13.4C488.4,293.7,486.9,292.2,485.1,292.2z M346.8,345.9h-45.2c-1.9,0-3.4,1.5-3.4,3.4v13.4
                                                    c0,1.9,1.5,3.4,3.4,3.4h45.2c1.9,0,3.4-1.5,3.4-3.4v-13.5C350.2,347.4,348.7,345.9,346.8,345.9z M350.2,322.4
                                                    c0-1.9-1.5-3.4-3.4-3.4h-45.2c-1.9,0-3.4,1.5-3.4,3.4v13.4c0,1.9,1.5,3.4,3.4,3.4h45.2c1.9,0,3.4-1.5,3.4-3.4V322.4z M350.2,295.6
                                                    c0-1.9-1.5-3.4-3.4-3.4h-45.2c-1.9,0-3.4,1.5-3.4,3.4V309c0,1.9,1.5,3.4,3.4,3.4h45.2c1.9,0,3.4-1.5,3.4-3.4V295.6z M350.2,268.7
                                                    c0-1.9-1.5-3.4-3.4-3.4h-45.2c-1.9,0-3.4,1.5-3.4,3.4v13.4c0,1.9,1.5,3.4,3.4,3.4h45.2c1.9,0,3.4-1.5,3.4-3.4V268.7z M350.2,241.8
                                                    c0-1.9-1.5-3.4-3.4-3.4h-45.2c-1.9,0-3.4,1.5-3.4,3.4v13.4c0,1.9,1.5,3.4,3.4,3.4h45.2c1.9,0,3.4-1.5,3.4-3.4V241.8z M196.7,169.5
                                                    c41.2,0,74.7,33.4,74.7,74.7c0,41.2-33.4,74.7-74.7,74.7S122,285.5,122,244.2C122,203,155.4,169.5,196.7,169.5z M196.8,193.1
                                                    c-0.9,0-1.9,0-2.8,0c-3,0.1-3.5,0.6-3.6,3.6c0,1.4,0,2.7,0,4.1c0,4,0,4-3.9,5.4c-9.3,3.4-15,9.7-15.7,19.9c-0.5,9,4.1,15,11.5,19.5
                                                    c4.5,2.7,9.6,4.3,14.4,6.5c1.9,0.8,3.7,1.8,5.2,3.1c4.6,3.8,3.8,10.2-1.7,12.6c-2.9,1.3-6,1.6-9.2,1.2c-4.9-0.6-9.6-1.9-14.1-4.2
                                                    c-2.6-1.4-3.4-1-4.2,1.8c-0.8,2.4-1.4,4.9-2.1,7.3c-0.9,3.3-0.6,4.1,2.6,5.6c4,1.9,8.3,2.9,12.7,3.6c3.4,0.5,3.5,0.7,3.6,4.2
                                                    c0,1.6,0,3.2,0,4.8c0,2,1,3.2,3.1,3.2c2.4,0,4.8,0,7.1,0c1.9,0,2.9-1.1,2.9-3.1c0-2.2,0.1-4.4,0-6.6s0.9-3.4,3-4
                                                    c5-1.4,9.2-4,12.5-8c9-11,5.6-27-7.2-34.1c-4-2.2-8.3-3.9-12.5-5.7c-2.4-1-4.8-2.2-6.9-3.9c-4.1-3.3-3.3-8.5,1.5-10.6
                                                    c1.3-0.6,2.8-0.8,4.2-0.9c5.5-0.3,10.8,0.7,15.8,3.1c2.5,1.2,3.3,0.8,4.2-1.8c0.9-2.7,1.6-5.5,2.4-8.3c0.5-1.9-0.1-3.1-1.9-3.9
                                                    c-3.2-1.4-6.5-2.4-10-3c-4.5-0.7-4.5-0.7-4.5-5.3C203.2,193.1,203.2,193.1,196.8,193.1z M67.4,244.2c0,11.4,9.3,20.7,20.7,20.7
                                                    s20.7-9.3,20.7-20.7s-9.3-20.7-20.7-20.7S67.4,232.8,67.4,244.2z"/></svg>
                                        </button>
                                    </div>
                                </div>
                                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">Préstamo {{ $loan->code }} • Total: {{ $userInstallments->total() }}</p>
                                <div class="flex flex-wrap gap-1.5 mt-2">
                                    <a href="mailto:{{ $user->email }}"
                                        class="flex items-center gap-1 bg-blue-500 hover:bg-blue-600 transition-colors px-2 py-1 text-white font-bold text-[9px]">
                                        {{ $user->email }}
                                        <svg class="w-3 h-3 flex-shrink-0" viewBox="0 0 512 512" xml:space="preserve"><rect x="7.904" y="119.947" style="fill:#5EBAE7;" width="496.195" height="272.109"/><polygon style="fill:#36A9E1;" points="256,280.01 23.91,127.951 7.904,135.954 256,312.022 352.037,223.987 "/><path style="fill:#1D1D1B;" d="M0,112.043v287.913h512V112.043H0z M477.266,127.851L256,270.603L34.733,127.851H477.266z M15.807,384.149V134.452L256,289.414l240.193-154.962v249.696H15.807V384.149z"/><g><rect x="215.982" y="344.138" style="fill:#FFFFFF;" width="16.007" height="15.807"/><rect x="247.997" y="344.138" style="fill:#FFFFFF;" width="16.006" height="15.807"/><rect x="280.013" y="344.138" style="fill:#FFFFFF;" width="16.007" height="15.807"/></g></svg>
                                    </a>
                                    <a href="https://wa.me/51{{ preg_replace('/\D/', '', $user->phone) }}" target="_blank"
                                        class="flex items-center gap-1 bg-green-500 hover:bg-green-600 transition-colors px-2 py-1 text-white font-bold text-[9px]">
                                        {{ $user->phone }}
                                        <svg class="w-3 h-3 flex-shrink-0" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M28 16C28 22.6274 22.6274 28 16 28C13.4722 28 11.1269 27.2184 9.19266 25.8837L5.09091 26.9091L6.16576 22.8784C4.80092 20.9307 4 18.5589 4 16C4 9.37258 9.37258 4 16 4C22.6274 4 28 9.37258 28 16Z" fill="url(#wgrp)"/><path fill-rule="evenodd" clip-rule="evenodd" d="M16 30C23.732 30 30 23.732 30 16C30 8.26801 23.732 2 16 2C8.26801 2 2 8.26801 2 16C2 18.5109 2.661 20.8674 3.81847 22.905L2 30L9.31486 28.3038C11.1894 29.3854 13.5789 30 16 30ZM16 27.8462C22.5425 27.8462 27.8462 22.5425 27.8462 16C27.8462 9.45755 22.5425 4.15385 16 4.15385C9.45755 4.15385 4.15385 9.45755 4.15385 16C4.15385 18.5261 4.9445 20.8675 6.29184 22.7902L5.23077 26.7692L9.27993 25.7569C11.1894 27.0746 13.5046 27.8462 16 27.8462Z" fill="white"/><path d="M12.5 9.49989C12.1672 8.83131 11.6565 8.8905 11.1407 8.8905C10.2188 8.8905 8.78125 9.99478 8.78125 12.05C8.78125 13.7343 9.52345 15.578 12.0244 18.3361C14.438 20.9979 17.6094 22.3748 20.2422 22.3279C22.875 22.2811 23.4167 20.0154 23.4167 19.2503C23.4167 18.9112 23.2062 18.742 23.0613 18.696C22.1641 18.2654 20.5093 17.4631 20.1328 17.3124C19.7563 17.1617 19.5597 17.3656 19.4375 17.4765C19.0961 17.8018 18.4193 18.7608 18.1875 18.9765C17.9558 19.1922 17.6103 19.083 17.4665 19.0015C16.9374 18.7892 15.5029 18.1511 14.3595 17.0426C12.9453 15.6718 12.8623 15.2001 12.5959 14.7803C12.3828 14.4444 12.5392 14.2384 12.6172 14.1483C12.9219 13.7968 13.3426 13.254 13.5313 12.9843C13.7199 12.7145 13.5702 12.305 13.4803 12.05C13.0938 10.953 12.7663 10.0347 12.5 9.49989Z" fill="white"/><defs><linearGradient id="wgrp" x1="26.5" y1="7" x2="4" y2="28" gradientUnits="userSpaceOnUse"><stop stop-color="#5BD066"/><stop offset="1" stop-color="#27B43E"/></linearGradient></defs></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- Contenedor de filas + paginación, reemplazado por fetch -->
                        <div id="group-rows-{{ $loan->id }}">
                            @include('superadmin.loans.partials.installments-group-rows', [
                                'loan' => $loan,
                                'userInstallments' => $userInstallments,
                            ])
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-24 text-center">
                        <p class="text-sm text-gray-400 dark:text-gray-500 font-medium uppercase tracking-widest">No hay préstamos activos</p>
                    </div>
                @endforelse
            </div>

            @if(isset($loansPaginator) && $loansPaginator->hasPages())
                <div class="mt-12 flex justify-center">
                    {{ $loansPaginator->links() }}
                </div>
            @endif
        @elseif($filter === 'all')
            <div class="divide-y divide-gray-100 dark:divide-gray-900">
                @forelse($borrowersWithLoans as $bw)
                    <div class="flex items-center gap-4 px-2 py-4 hover:bg-gray-50 dark:hover:bg-gray-900/40 transition-colors cursor-pointer group cobranzas-row"
                         data-search-name="{{ strtolower($bw['user']->name) }}"
                         data-search-dni="{{ $bw['user']->dni ?? '' }}"
                         onclick="openBorrowerLoansModal({{ $bw['user']->id }})">
                        <div class="w-11 h-11 rounded-xl bg-gray-100 dark:bg-gray-900 flex items-center justify-center overflow-hidden border border-gray-100 dark:border-gray-800 flex-shrink-0">
                            <img src="{{ $bw['photo'] }}" class="w-full h-full object-cover" alt="">
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight truncate">{{ $bw['user']->name }}</h3>
                                @if($bw['overdue_count'] > 0)
                                    <span class="px-1.5 py-px bg-red-500 text-white text-[8px] font-bold rounded flex-shrink-0">{{ $bw['overdue_count'] }} vencida{{ $bw['overdue_count'] > 1 ? 's' : '' }}</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-x-4 gap-y-1 mt-1 text-[10px] text-gray-400">
                                <span>{{ $bw['loans_count'] }} préstamo{{ $bw['loans_count'] > 1 ? 's' : '' }}</span>
                                <span>{{ $bw['active_count'] }} activo{{ $bw['active_count'] > 1 ? 's' : '' }}</span>
                                @if($bw['total_debt'] > 0)
                                    <span>Deuda: <strong class="text-orange-600 dark:text-orange-400 tabular-nums">{{ number_format($bw['total_debt'], 2) }}</strong></span>
                                @endif
                            </div>
                            <div class="flex flex-wrap gap-1.5 mt-2">
                                <a href="mailto:{{ $bw['email'] }}" onclick="event.stopPropagation()"
                                   class="flex items-center gap-1 bg-blue-500 hover:bg-blue-600 transition-colors px-2 py-1 text-white font-bold text-[9px] rounded-lg">
                                    <svg class="w-3 h-3 flex-shrink-0" viewBox="0 0 512 512" xml:space="preserve"><rect x="7.904" y="119.947" style="fill:#5EBAE7;" width="496.195" height="272.109"/><polygon style="fill:#36A9E1;" points="256,280.01 23.91,127.951 7.904,135.954 256,312.022 352.037,223.987 "/><path style="fill:#1D1D1B;" d="M0,112.043v287.913h512V112.043H0z M477.266,127.851L256,270.603L34.733,127.851H477.266z M15.807,384.149V134.452L256,289.414l240.193-154.962v249.696H15.807V384.149z"/><g><rect x="215.982" y="344.138" style="fill:#FFFFFF;" width="16.007" height="15.807"/><rect x="247.997" y="344.138" style="fill:#FFFFFF;" width="16.006" height="15.807"/><rect x="280.013" y="344.138" style="fill:#FFFFFF;" width="16.007" height="15.807"/></g></svg>
                                    Email
                                </a>
                                <a href="https://wa.me/51{{ preg_replace('/\D/', '', $bw['phone']) }}" target="_blank" onclick="event.stopPropagation()"
                                   class="flex items-center gap-1 bg-green-500 hover:bg-green-600 transition-colors px-2 py-1 text-white font-bold text-[9px] rounded-lg">
                                    <svg class="w-3 h-3 flex-shrink-0" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M28 16C28 22.6274 22.6274 28 16 28C13.4722 28 11.1269 27.2184 9.19266 25.8837L5.09091 26.9091L6.16576 22.8784C4.80092 20.9307 4 18.5589 4 16C4 9.37258 9.37258 4 16 4C22.6274 4 28 9.37258 28 16Z" fill="#27B43E"/><path fill-rule="evenodd" clip-rule="evenodd" d="M16 30C23.732 30 30 23.732 30 16C30 8.26801 23.732 2 16 2C8.26801 2 2 8.26801 2 16C2 18.5109 2.661 20.8674 3.81847 22.905L2 30L9.31486 28.3038C11.1894 29.3854 13.5789 30 16 30ZM16 27.8462C22.5425 27.8462 27.8462 22.5425 27.8462 16C27.8462 9.45755 22.5425 4.15385 16 4.15385C9.45755 4.15385 4.15385 9.45755 4.15385 16C4.15385 18.5261 4.9445 20.8675 6.29184 22.7902L5.23077 26.7692L9.27993 25.7569C11.1894 27.0746 13.5046 27.8462 16 27.8462Z" fill="white"/><path d="M12.5 9.49989C12.1672 8.83131 11.6565 8.8905 11.1407 8.8905C10.2188 8.8905 8.78125 9.99478 8.78125 12.05C8.78125 13.7343 9.52345 15.578 12.0244 18.3361C14.438 20.9979 17.6094 22.3748 20.2422 22.3279C22.875 22.2811 23.4167 20.0154 23.4167 19.2503C23.4167 18.9112 23.2062 18.742 23.0613 18.696C22.1641 18.2654 20.5093 17.4631 20.1328 17.3124C19.7563 17.1617 19.5597 17.3656 19.4375 17.4765C19.0961 17.8018 18.4193 18.7608 18.1875 18.9765C17.9558 19.1922 17.6103 19.083 17.4665 19.0015C16.9374 18.7892 15.5029 18.1511 14.3595 17.0426C12.9453 15.6718 12.8623 15.2001 12.5959 14.7803C12.3828 14.4444 12.5392 14.2384 12.6172 14.1483C12.9219 13.7968 13.3426 13.254 13.5313 12.9843C13.7199 12.7145 13.5702 12.305 13.4803 12.05C13.0938 10.953 12.7663 10.0347 12.5 9.49989Z" fill="white"/></svg>
                                    WhatsApp
                                </a>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 group-hover:text-gray-500 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                @empty
                    <div class="py-24 text-center">
                        <p class="text-sm text-gray-400 dark:text-gray-500 font-medium uppercase tracking-widest">No hay prestatarios registrados</p>
                    </div>
                @endforelse
            </div>

            @if($borrowers && method_exists($borrowers, 'links') && $borrowers->hasPages())
                <div class="mt-12 flex justify-center">
                    {{ $borrowers->links() }}
                </div>
            @endif
        @elseif($filter === 'overdue')
            <div class="space-y-3">
                @forelse($overdueDebtors as $debtor)
                    @php
                        $user = $debtor['user'];
                        $selfie = $user->documents?->selfie;
                        $photo = $selfie ? Storage::url($selfie) : ($user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=fee2e2&color=b91c1c');
                        $fullName = $user->person_type === 'juridica' ? ($user->business_name ?? $user->name) : trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '') ?: $user->name);
                        $daysOverdue = now()->startOfDay()->diffInDays($debtor['first_due_date']);
                    @endphp
                    <div class="p-4 rounded-2xl bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-900/30 hover:bg-red-100/70 dark:hover:bg-red-950/40 transition-colors cursor-pointer cobranzas-row"
                         data-search-name="{{ strtolower($fullName) }}"
                         data-search-dni="{{ $user->dni ?? '' }}"
                         onclick="window.location.href='{{ route('superadmin.installments.debtor', $user) }}'">
                        <div class="flex items-center gap-3">
                            <img src="{{ $photo }}" class="w-11 h-11 rounded-xl object-cover flex-shrink-0 ring-2 ring-red-200 dark:ring-red-800" alt="{{ $fullName }}">
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $fullName }}</div>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[10px] font-medium text-red-600 dark:text-red-400">
                                        {{ $debtor['first_due_date']->format('d/m/Y') }} ({{ $daysOverdue }}d)
                                    </span>
                                    <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400">
                                        · {{ $debtor['installments_count'] }} cuota{{ $debtor['installments_count'] > 1 ? 's' : '' }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <a href="mailto:{{ $user->email }}" onclick="event.stopPropagation()"
                                   class="flex items-center gap-1 bg-blue-500 hover:bg-blue-600 transition-colors px-2 py-1 text-white font-bold text-[9px] rounded-lg">
                                    <svg class="w-3 h-3 flex-shrink-0" viewBox="0 0 512 512" xml:space="preserve"><rect x="7.904" y="119.947" style="fill:#5EBAE7;" width="496.195" height="272.109"/><polygon style="fill:#36A9E1;" points="256,280.01 23.91,127.951 7.904 135.954 256,312.022 352.037 223.987 "/><path style="fill:#1D1D1B;" d="M0,112.043v287.913h512V112.043H0z M477.266,127.851L256,270.603L34.733,127.851H477.266z M15.807,384.149V134.452L256,289.414l240.193-154.962v249.696H15.807V384.149z"/><g><rect x="215.982" y="344.138" style="fill:#FFFFFF;" width="16.007" height="15.807"/><rect x="247.997" y="344.138" style="fill:#FFFFFF;" width="16.006" height="15.807"/><rect x="280.013" y="344.138" style="fill:#FFFFFF;" width="16.007" height="15.807"/></g></svg>
                                    Email
                                </a>
                                <a href="https://wa.me/51{{ preg_replace('/\D/', '', $user->phone) }}" target="_blank" onclick="event.stopPropagation()"
                                   class="flex items-center gap-1 bg-green-500 hover:bg-green-600 transition-colors px-2 py-1 text-white font-bold text-[9px] rounded-lg">
                                    <svg class="w-3 h-3 flex-shrink-0" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M28 16C28 22.6274 22.6274 28 16 28C13.4722 28 11.1269 27.2184 9.19266 25.8837L5.09091 26.9091L6.16576 22.8784C4.80092 20.9307 4 18.5589 4 16C4 9.37258 9.37258 4 16 4C22.6274 4 28 9.37258 28 16Z" fill="#27B43E"/><path fill-rule="evenodd" clip-rule="evenodd" d="M16 30C23.732 30 30 23.732 30 16C30 8.26801 23.732 2 16 2C8.26801 2 2 8.26801 2 16C2 18.5109 2.661 20.8674 3.81847 22.905L2 30L9.31486 28.3038C11.1894 29.3854 13.5789 30 16 30ZM16 27.8462C22.5425 27.8462 27.8462 22.5425 27.8462 16C27.8462 9.45755 22.5425 4.15385 16 4.15385C9.45755 4.15385 4.15385 9.45755 4.15385 16C4.15385 18.5261 4.9445 20.8675 6.29184 22.7902L5.23077 26.7692L9.27993 25.7569C11.1894 27.0746 13.5046 27.8462 16 27.8462Z" fill="white"/><path d="M12.5 9.49989C12.1672 8.83131 11.6565 8.8905 11.1407 8.8905C10.2188 8.8905 8.78125 9.99478 8.78125 12.05C8.78125 13.7343 9.52345 15.578 12.0244 18.3361C14.438 20.9979 17.6094 22.3748 20.2422 22.3279C22.875 22.2811 23.4167 20.0154 23.4167 19.2503C23.4167 18.9112 23.2062 18.742 23.0613 18.696C22.1641 18.2654 20.5093 17.4631 20.1328 17.3124C19.7563 17.1617 19.5597 17.3656 19.4375 17.4765C19.0961 17.8018 18.4193 18.7608 18.1875 18.9765C17.9558 19.1922 17.6103 19.083 17.4665 19.0015C16.9374 18.7892 15.5029 18.1511 14.3595 17.0426C12.9453 15.6718 12.8623 15.2001 12.5959 14.7803C12.3828 14.4444 12.5392 14.2384 12.6172 14.1483C12.9219 13.7968 13.3426 13.254 13.5313 12.9843C13.7199 12.7145 13.5702 12.305 13.4803 12.05C13.0938 10.953 12.7663 10.0347 12.5 9.49989Z" fill="white"/></svg>
                                    WhatsApp
                                </a>
                            </div>
                            <p class="text-sm font-black text-red-600 dark:text-red-400 flex-shrink-0">S/ {{ number_format($debtor['total_debt'], 2, '.', ',') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="py-24 text-center">
                        <svg class="w-12 h-12 mx-auto text-green-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm text-gray-400 dark:text-gray-500 font-medium uppercase tracking-widest">Sin cuotas vencidas</p>
                    </div>
                @endforelse
            </div>

            @if(method_exists($overdueDebtors, 'links') && $overdueDebtors->hasPages())
                <div class="mt-12 flex justify-center">
                    {{ $overdueDebtors->links() }}
                </div>
            @endif
        @else
            @if($filter === 'paid')
                <div class="flex justify-end mb-6">
                    <button type="button" onclick="openVoucherCleanupModal()"
                        class="text-[10px] font-black uppercase tracking-widest text-green-600 border-b-2 border-green-600 pb-0.5 hover:opacity-60 transition-opacity">
                        Limpiar comprobantes
                    </button>
                </div>
            @endif

            <!-- Vista Lista Simple -->
            @php
                $_lastPaymentByLoan = [];

                // Agrupar installments por loan_id para calcular último pago POR PRÉSTAMO
                $loansInList = $installments->pluck('loan_id')->unique();

                foreach ($loansInList as $lid) {
                    $loanForCalc = \App\Models\Loan::find($lid);
                    if (!$loanForCalc) continue;

                    $pList = collect();

                    // Cuotas aprobadas
                    foreach ($loanForCalc->installments()->where('status', 'approved')->get() as $ai) {
                        if ($ai->paid_at) {
                            $pList->push(['date' => $ai->paid_at, 'type' => 'installment', 'id' => $ai->id, 'interest_id' => null]);
                        }
                    }

                    // Intereses pagados
                    foreach ($loanForCalc->installments()->where('interest_payments_count', '>', 0)->with('interestPayments')->get() as $iw) {
                        foreach ($iw->interestPayments as $ip) {
                            if ($ip->paid_at) {
                                $pList->push(['date' => $ip->paid_at, 'type' => 'interest', 'id' => $iw->id, 'interest_id' => $ip->id]);
                            }
                        }
                    }

                    $pList = $pList->sortByDesc('date')->values();
                    $_lastPaymentByLoan[$lid] = $pList->first();
                }
            @endphp
            <div class="divide-y divide-gray-100 dark:divide-gray-900">
                @forelse($installments as $inst)
                    @include('superadmin.loans.partials.installment-row', [
                        'inst' => $inst,
                        '_lastPaymentByLoan' => $_lastPaymentByLoan,
                    ])
                @empty
                    <div class="py-24 text-center">
                        <p class="text-sm text-gray-400 dark:text-gray-500 font-medium uppercase tracking-widest">No hay cuotas registradas</p>
                    </div>
                @endforelse
            </div>

            <!-- Paginación -->
            @if(method_exists($installments, 'links'))
                <div class="mt-12 flex justify-center">
                    {{ $installments->links() }}
                </div>
            @endif
        @endif
        </div>
    </div>
    @include('superadmin.loans.partials.validate-modal')

    <!-- Modal Detalle Préstamo -->
    @include('superadmin.loans.partials.loan-detail-modal')

    <!-- Modal Configurar Mora -->
    @include('superadmin.loans.partials.late-fee-modal')

    @if($filter === 'paid')
        @include('superadmin.loans.partials.voucher-cleanup-modal')
    @endif

    <!-- Modal Préstamos del Prestatario -->
    <div id="borrower-loans-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/40" onclick="closeBorrowerLoansModal()"></div>
        <div class="absolute bottom-0 left-0 right-0 sm:bottom-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 sm:max-w-lg sm:w-full bg-white dark:bg-gray-900 rounded-t-2xl sm:rounded-2xl overflow-hidden flex flex-col max-h-[85vh] sm:max-h-[80vh]">
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 dark:border-gray-800 flex-shrink-0">
                <div>
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white">Préstamos activos</h2>
                    <p id="borrower-loans-name" class="text-[10px] text-gray-400"></p>
                </div>
                <button onclick="closeBorrowerLoansModal()" class="w-8 h-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 flex items-center justify-center transition-colors -mr-1">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="px-5 py-2.5 border-b border-gray-100 dark:border-gray-800 flex-shrink-0">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 dark:text-gray-600 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" id="borrower-loans-search" placeholder="Buscar por código..." oninput="filterBorrowerLoans()" class="w-full pl-9 pr-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-lg text-xs font-medium text-gray-900 dark:text-white placeholder:text-gray-300 dark:placeholder:text-gray-600 focus:outline-none focus:border-gray-300 dark:focus:border-gray-500 transition-colors">
                </div>
            </div>

            <div id="borrower-loans-list" class="flex-1 overflow-y-auto overscroll-contain divide-y divide-gray-100 dark:divide-gray-800/60">
                <div class="py-16 flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-300 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                </div>
            </div>

            <div id="borrower-loans-pagination" class="px-4 py-2.5 border-t border-gray-100 dark:border-gray-800 bg-gray-50/80 dark:bg-gray-900/30 flex-shrink-0 hidden">
                <div class="flex items-center justify-between gap-3">
                    <button onclick="borrowerLoansPrevPage()" id="borrower-loans-prev-btn"
                        class="px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg text-[10px] font-bold text-gray-600 dark:text-gray-300 transition-colors disabled:opacity-30 disabled:pointer-events-none">← Anterior</button>
                    <div id="borrower-loans-page-dots" class="flex items-center gap-1"></div>
                    <button onclick="borrowerLoansNextPage()" id="borrower-loans-next-btn"
                        class="px-3 py-1.5 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 border border-gray-900 dark:border-gray-200 rounded-lg text-[10px] font-bold text-white dark:text-gray-900 transition-colors disabled:opacity-30 disabled:pointer-events-none">Siguiente →</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Número de Operación -->
    <div id="editOperationModal"
        class="hidden fixed inset-0 bg-white/40 dark:bg-black/60 backdrop-blur-md z-[130] flex items-center justify-center p-4">
        <div
            class="bg-white dark:bg-gray-950 w-full max-w-sm rounded-[2rem] p-8 shadow-2xl relative animate-in fade-in zoom-in duration-200">
            <button onclick="closeEditOperationModal()"
                class="absolute top-6 right-6 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-900 transition-colors">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 uppercase tracking-tight">Editar Operación</h3>

            <form id="editOperationForm" method="POST" class="space-y-6">
                @csrf
                @method('PATCH')
                <div>
                    <label
                        class="text-[10px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-[0.2em] block mb-2">N°
                        de operación</label>
                    <input type="text" name="operation_number" id="edit_modal_operation_number" placeholder="Ej: 123456"
                        maxlength="25" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')"
                        class="w-full bg-transparent border-0 border-b-2 border-gray-100 dark:border-gray-900 py-2 text-sm font-bold text-gray-900 dark:text-white focus:outline-none focus:border-purple-500 transition-all placeholder:text-gray-200 dark:placeholder:text-gray-800">
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeEditOperationModal()"
                        class="flex-1 py-3 rounded-xl bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400 text-xs font-black uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-gray-800 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 py-3 rounded-xl bg-purple-600 text-white text-xs font-black uppercase tracking-widest hover:bg-purple-700 transition-colors shadow-lg shadow-purple-500/20">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const allCashboxes = @json($cashboxes);

        let _borrowerLoansData = [];
        let _borrowerLoansPage = 1;
        const _borrowerLoansPerPage = 5;
        let _borrowerLoansFiltered = [];

        function filterBorrowerLoans() {
            const q = (document.getElementById('borrower-loans-search').value || '').toLowerCase().trim();
            if (!q) {
                _borrowerLoansFiltered = _borrowerLoansData;
            } else {
                _borrowerLoansFiltered = _borrowerLoansData.filter(l => l.code.toLowerCase().includes(q));
            }
            _borrowerLoansPage = 1;
            renderBorrowerLoansPage();
        }

        function renderBorrowerLoansPage() {
            const container = document.getElementById('borrower-loans-list');
            const data = _borrowerLoansFiltered;
            if (data.length === 0) {
                const q = (document.getElementById('borrower-loans-search').value || '').trim();
                container.innerHTML = '<div class="py-16 text-center"><div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center"><svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg></div><p class="text-sm font-medium text-gray-400">' + (q ? 'Sin resultados para "' + q + '"' : 'Sin préstamos') + '</p><p class="text-[11px] text-gray-300 mt-0.5">' + (q ? 'Intenta con otro código' : 'No hay préstamos activos para este cliente') + '</p></div>';
                document.getElementById('borrower-loans-pagination').classList.add('hidden');
                return;
            }
            const totalPages = Math.ceil(data.length / _borrowerLoansPerPage);
            const start = (_borrowerLoansPage - 1) * _borrowerLoansPerPage;
            const pageItems = data.slice(start, start + _borrowerLoansPerPage);
            let html = '';
            pageItems.forEach(function(l, idx) {
                const num = start + idx + 1;
                const badgeColor = l.overdue > 0 ? 'bg-red-500' : 'bg-gray-200 dark:bg-gray-700';
                const debtColor = l.overdue > 0 ? 'text-red-600 dark:text-red-400' : 'text-orange-600 dark:text-orange-400';
                let badges = '';
                if (l.overdue > 0) badges += '<span class="px-1.5 py-px bg-red-500 text-white text-[8px] font-bold rounded">' + l.overdue + '</span>';

                html += '<a href="' + l.url + '" class="flex items-center gap-3 px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-900/50 transition-colors active:bg-gray-100 dark:active:bg-gray-800/50 group">';
                html += '<div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 ' + badgeColor + '">';
                html += '<span class="text-xs font-black text-white tabular-nums">#' + num + '</span>';
                html += '</div>';
                html += '<div class="flex-1 min-w-0">';
                html += '<div class="flex items-center gap-1.5 mb-1">';
                html += '<span class="text-sm font-bold text-gray-900 dark:text-white">' + l.code + '</span>' + badges;
                html += '</div>';
                html += '<div class="flex items-center gap-x-4 gap-y-1 flex-wrap text-[11px] text-gray-400 dark:text-gray-500">';
                html += '<span>' + l.cur + '<strong class="text-gray-600 dark:text-gray-300 tabular-nums ml-0.5">' + Number(l.amount).toLocaleString("en-US",{minimumFractionDigits:2,maximumFractionDigits:2}) + '</strong></span>';
                html += '<span>Pagado: <strong class="text-emerald-600 dark:text-emerald-400 tabular-nums">' + Number(l.paid).toLocaleString("en-US",{minimumFractionDigits:2,maximumFractionDigits:2}) + '</strong></span>';
                html += '<span>Deuda: <strong class="' + debtColor + ' tabular-nums">' + Number(l.debt).toLocaleString("en-US",{minimumFractionDigits:2,maximumFractionDigits:2}) + '</strong></span>';
                html += '</div></div>';
                html += '<svg class="w-4 h-4 text-gray-300 dark:text-gray-600 group-hover:text-gray-400 dark:group-hover:text-gray-500 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>';
                html += '</a>';
            });
            container.innerHTML = html;

            const pagDiv = document.getElementById('borrower-loans-pagination');
            if (totalPages > 1) {
                pagDiv.classList.remove('hidden');
                document.getElementById('borrower-loans-prev-btn').disabled = _borrowerLoansPage <= 1;
                document.getElementById('borrower-loans-next-btn').disabled = _borrowerLoansPage >= totalPages;
                const dotsContainer = document.getElementById('borrower-loans-page-dots');
                dotsContainer.innerHTML = '';
                for (let i = 1; i <= totalPages; i++) {
                    const dot = document.createElement('button');
                    dot.className = 'w-7 h-7 flex items-center justify-center rounded-md text-[10px] font-bold transition-colors ' + (i === _borrowerLoansPage ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900' : 'hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-400');
                    dot.textContent = i;
                    dot.onclick = function() { _borrowerLoansPage = i; renderBorrowerLoansPage(); };
                    dotsContainer.appendChild(dot);
                }
            } else {
                pagDiv.classList.add('hidden');
            }
        }

        function borrowerLoansPrevPage() { if (_borrowerLoansPage > 1) { _borrowerLoansPage--; renderBorrowerLoansPage(); } }
        function borrowerLoansNextPage() { const tp = Math.ceil(_borrowerLoansFiltered.length / _borrowerLoansPerPage); if (_borrowerLoansPage < tp) { _borrowerLoansPage++; renderBorrowerLoansPage(); } }

        function openBorrowerLoansModal(userId) {
            _borrowerLoansData = [];
            _borrowerLoansFiltered = [];
            _borrowerLoansPage = 1;
            document.getElementById('borrower-loans-search').value = '';
            document.getElementById('borrower-loans-name').textContent = 'Cargando...';
            document.getElementById('borrower-loans-list').innerHTML = '<div class="py-16 flex items-center justify-center"><svg class="w-6 h-6 text-gray-300 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg></div>';
            document.getElementById('borrower-loans-pagination').classList.add('hidden');
            document.getElementById('borrower-loans-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            fetch('/gestion-interna/pagos/prestamos/' + userId, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(res => {
                document.getElementById('borrower-loans-name').textContent = res.user;
                _borrowerLoansData = res.loans;
                _borrowerLoansFiltered = res.loans;
                renderBorrowerLoansPage();
            })
            .catch(() => {
                document.getElementById('borrower-loans-name').textContent = 'Error al cargar';
                document.getElementById('borrower-loans-list').innerHTML = '<div class="py-16 text-center"><p class="text-sm text-red-400">Error al cargar los préstamos</p></div>';
            });
        }

        function closeBorrowerLoansModal() {
            document.getElementById('borrower-loans-modal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function loadGroupPage(btn) {
            const loanId = btn.dataset.loanId;
            const page   = btn.dataset.page;
            const container = document.getElementById('group-rows-' + loanId);
            if (!container) return;

            container.style.opacity = '0.4';
            container.style.pointerEvents = 'none';

            fetch(`/gestion-interna/pagos/grupo/${loanId}?page=${page}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.text())
            .then(html => {
                container.innerHTML = html;
                container.style.opacity = '';
                container.style.pointerEvents = '';
                if (window.tailwind) {
                    try { tailwind.process(); } catch(e) { try { document.dispatchEvent(new CustomEvent('tailwind:process')); } catch(e2) {} }
                }
            });
        }

        function openValidateModal(data) {
            window.currentValidateData = data;
            document.getElementById('modal_voucher_img').src = data.voucher;
            document.getElementById('modal_voucher_link').href = data.voucher;

            document.getElementById('modal_info_text').innerHTML = `
                <p class="text-xs text-gray-500">Usuario: <span class="text-gray-900 dark:text-white font-black">${data.user}</span></p>
                <p class="text-xs text-gray-500">Cuota: <span class="text-gray-900 dark:text-white font-black">#${data.number}</span></p>
                ${data.lateFee ? `
                    <p class="text-xs text-gray-500">Monto cuota: <span class="text-gray-900 dark:text-white font-black">${data.baseAmount}</span></p>
                    <p class="text-xs text-gray-500">Mora: <span class="text-orange-500 font-black">${data.lateFee}</span></p>
                    <p class="text-xs text-gray-500">Total esperado: <span class="text-purple-600 font-black">${data.amount}</span></p>
                ` : `
                    <p class="text-xs text-gray-500">Monto esperado: <span class="text-purple-600 font-black">${data.amount}</span></p>
                `}
            `;

            // Populate new payment details
            const methodLabel = data.method === 'cash' ? 'Efectivo' : 'Transferencia';
            document.getElementById('v_method').textContent = methodLabel;
            
            const typeLabel = data.requested_type === 'interest' ? 'Solo Interés (Postergación)' : (data.requested_type === 'total' ? 'Pago Total' : 'Cuota Completa');
            const vType = document.getElementById('v_type');
            vType.textContent = typeLabel;
            vType.className = data.requested_type === 'total' ? 'font-black text-green-600 dark:text-green-400 uppercase' : 'font-black text-purple-600 dark:text-purple-400 uppercase';

            const interestNotice = document.getElementById('interest_voucher_notice');
            if (interestNotice) {
                interestNotice.classList.toggle('hidden', data.requested_type !== 'interest');
            }

            const transferFields = document.getElementById('v_transfer_fields');
            if (data.method === 'cash') {
                transferFields.classList.add('hidden');
            } else {
                transferFields.classList.remove('hidden');
                document.getElementById('v_bank').textContent = data.bank || 'N/A';
                document.getElementById('v_op').textContent = data.operation || 'Sin OP';
            }

            document.getElementById('approveForm').action = `/gestion-interna/pagos/${data.id}/approve`;
            document.getElementById('rejectForm').action = `/gestion-interna/pagos/${data.id}/reject`;

            // Populate cashboxes
            const cashboxSelect = document.getElementById('validate_cashbox_id');
            const cashboxContainer = document.getElementById('validate_cashbox_container');
            const createBtn = document.getElementById('validate_create_cashbox_btn');
            
            // Extract currency from amount string (e.g., "$ 1,000.00" or "S/ 1,000.00")
            const currency = data.amount.includes('$') ? 'USD' : 'PEN';
            const filteredCashboxes = allCashboxes.filter(cb => cb.currency === currency);
            
            if (filteredCashboxes.length === 0) {
                cashboxContainer.classList.add('hidden');
                createBtn.classList.remove('hidden');
                createBtn.querySelector('.currency-label').textContent = currency;
                cashboxSelect.innerHTML = '';
                cashboxSelect.required = false;
            } else {
                cashboxContainer.classList.remove('hidden');
                createBtn.classList.add('hidden');
                cashboxSelect.required = true;
                cashboxSelect.innerHTML = filteredCashboxes.map(cb => 
                    `<option value="${cb.id}">${cb.name} (${cb.currency} ${parseFloat(cb.balance).toLocaleString('en-US', {minimumFractionDigits:2})})</option>`
                ).join('');
            }

            if (typeof _resetVoucherZoom === 'function') _resetVoucherZoom();

            document.getElementById('validateModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeValidateModal() {
            document.getElementById('voucherLightbox').classList.add('hidden');
            document.getElementById('validateModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        document.getElementById('validateModal').addEventListener('click', e => {
            if (e.target === document.getElementById('validateModal')) closeValidateModal();
        });

        function openEditOperationModal(id, currentOp) {
            const modal = document.getElementById('editOperationModal');
            const form = document.getElementById('editOperationForm');
            const input = document.getElementById('edit_modal_operation_number');

            form.action = `/gestion-interna/pagos/${id}/numero-operacion`;
            input.value = currentOp || '';

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeEditOperationModal() {
            document.getElementById('editOperationModal').classList.add('hidden');
            if (!document.getElementById('validateModal').classList.contains('hidden')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }

        function openEditOperationModalFromValidate() {
            if (window.currentValidateData) {
                openEditOperationModal(window.currentValidateData.id, window.currentValidateData.operation);
            }
        }

        // Highlight from URL parameter
        window.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const instId = urlParams.get('installment_id');
            const highlightInterest = urlParams.get('highlight_interest');

            if (instId && highlightInterest === 'true') {
                const virtualRows = document.querySelectorAll('[data-installment-id="' + instId + '"][data-is-interest-payment="true"]');
                let row = null;
                const ipId = urlParams.get('interest_payment_id');
                if (ipId) {
                    row = document.querySelector('[data-interest-payment-id="' + ipId + '"]');
                }
                if (!row && virtualRows.length > 0) {
                    row = virtualRows[0];
                }
                if (row) {
                    row.classList.add('bg-amber-50', 'dark:bg-amber-900/10', 'ring-2', 'ring-amber-400', 'rounded-2xl');
                    row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    setTimeout(() => {
                        row.classList.remove('bg-amber-50', 'dark:bg-amber-900/10', 'ring-2', 'ring-amber-400');
                    }, 4000);
                }
            } else if (instId) {
                const el = document.getElementById('installment-' + instId);
                if (el) {
                    el.classList.add('bg-yellow-50', 'dark:bg-yellow-900/10', 'ring-2', 'ring-yellow-400', 'rounded-2xl');
                    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    setTimeout(() => {
                        el.classList.remove('bg-yellow-50', 'dark:bg-yellow-900/10', 'ring-2', 'ring-yellow-400');
                    }, 3000);
                }
            }
        });

        function filterByPaidDate(value) {
            const params = new URLSearchParams(window.location.search);
            if (value) {
                params.set('paid_date', value);
            } else {
                params.delete('paid_date');
            }
            params.set('page', '1');
            window.location.href = window.location.pathname + '?' + params.toString();
        }

        function clearPaidDateFilter() {
            filterByPaidDate(null);
        }

        document.getElementById('paid-date-filter')?.addEventListener('change', function() {
            filterByPaidDate(this.value || null);
        });
        document.getElementById('paid-date-filter-mobile')?.addEventListener('change', function() {
            filterByPaidDate(this.value || null);
        });
    </script>

    <style>
        .overdue-installment-row::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: -1.25rem;
            right: 0;
            background: linear-gradient(to right, rgba(239, 68, 68, 0.18), rgba(239, 68, 68, 0.06) 40%, transparent);
            pointer-events: none;
            z-index: 0;
        }

        @media (min-width: 768px) {
            .overdue-installment-row::before {
                left: -2.5rem;
                background: linear-gradient(to right, rgba(239, 68, 68, 0.15), rgba(239, 68, 68, 0.04) 35%, transparent);
            }
        }

        .dark .overdue-installment-row::before {
            background: linear-gradient(to right, rgba(239, 68, 68, 0.22), rgba(239, 68, 68, 0.08) 40%, transparent);
        }

        @media (min-width: 768px) {
            .dark .overdue-installment-row::before {
                background: linear-gradient(to right, rgba(239, 68, 68, 0.2), rgba(239, 68, 68, 0.05) 35%, transparent);
            }
        }

        .overdue-installment-row::after {
            content: '';
            position: absolute;
            left: -1.25rem;
            top: 0;
            bottom: 0;
            width: 3px;
            background: #ef4444;
            border-radius: 0 2px 2px 0;
            pointer-events: none;
            z-index: 1;
        }

        @media (min-width: 768px) {
            .overdue-installment-row::after {
                left: -2.5rem;
                width: 4px;
            }
        }

        .overdue-installment-row > * {
            position: relative;
            z-index: 2;
        }
    </style>

    <!-- Modal de Confirmación para Revertir Pago -->
    <div id="unapproveModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all">
            <div class="p-6">
                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 dark:bg-red-900/30">
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white text-center mb-2">¿Revertir pago?</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 text-center leading-relaxed mb-6">
                    Esta acción eliminará el ingreso de caja y el pago volverá a estar en revisión. Solo se puede revertir el último pago aprobado.
                </p>
                <form id="unapproveForm" method="POST" action="{{ route('superadmin.installments.unapprove', 0) }}">
                    @csrf
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="button" onclick="closeUnapproveModal()"
                            class="flex-1 px-6 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="flex-1 px-6 py-3 text-sm font-bold text-white bg-red-500 rounded-xl hover:bg-red-600 transition-colors">
                            Sí, revertir
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openUnapproveModal(url) {
            document.getElementById('unapproveForm').action = url;
            document.getElementById('unapproveModal').classList.remove('hidden');
            document.getElementById('unapproveModal').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeUnapproveModal() {
            document.getElementById('unapproveModal').classList.add('hidden');
            document.getElementById('unapproveModal').classList.remove('flex');
            document.body.style.overflow = '';
        }

        // Cerrar modal con tecla Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeUnapproveModal();
            }
        });

        // Cerrar modal al hacer click fuera
        document.getElementById('unapproveModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeUnapproveModal();
            }
        });
    </script>

    <script>
        (function() {
            const searchInput = document.getElementById('cobranzas-search');
            const clearBtn = document.getElementById('cobranzas-search-clear');
            const spinner = document.getElementById('cobranzas-search-spinner');
            const cobranzasList = document.getElementById('cobranzas-list');
            if (!searchInput || !cobranzasList) return;

            const currentFilter = '{{ $filter }}';
            const baseUrl = '{{ route("superadmin.installments.index") }}';
            let debounceTimer = null;
            let currentRequest = null;

            function performSearch(term) {
                if (currentRequest) currentRequest.abort();

                const params = new URLSearchParams();
                if (term) params.set('search', term);
                if (currentFilter !== 'all') params.set('filter', currentFilter);

                const url = baseUrl + (params.toString() ? '?' + params.toString() : '');

                if (!term) {
                    clearBtn.classList.add('hidden');
                } else {
                    clearBtn.classList.add('hidden');
                    spinner.classList.remove('hidden');
                }

                currentRequest = new AbortController();

                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    signal: currentRequest.signal
                })
                .then(function(response) { return response.text(); })
                .then(function(html) {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newList = doc.getElementById('cobranzas-list');
                    if (newList) cobranzasList.innerHTML = newList.innerHTML;
                })
                .catch(function(err) {
                    if (err.name !== 'AbortError') console.error(err);
                })
                .finally(function() {
                    spinner.classList.add('hidden');
                    if (term) clearBtn.classList.remove('hidden');
                });
            }

            searchInput.addEventListener('input', function() {
                const term = this.value.trim();
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function() { performSearch(term); }, 300);
            });

            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                performSearch('');
                searchInput.focus();
            });
        })();
    </script>
@endsection
