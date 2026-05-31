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

            <div class="mb-6 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" id="loans-search" placeholder="Buscar por nombre, DNI o código de préstamo..."
                       value="{{ request('search') }}"
                       class="w-full pl-10 pr-10 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-sm text-gray-900 dark:text-white placeholder:text-gray-400 focus:outline-none focus:border-gray-400 dark:focus:border-gray-600 transition-colors">
                <button type="button" id="loans-search-clear" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors {{ request('search') ? '' : 'hidden' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <div id="loans-search-spinner" class="absolute right-3 top-1/2 -translate-y-1/2 hidden">
                    <svg class="w-4 h-4 animate-spin text-gray-400" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </div>
            </div>

            @if(request('search'))
                <div id="loans-search-badge" class="mb-4 flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Mostrando resultados para "<span class="font-medium text-gray-700 dark:text-gray-300">{{ request('search') }}</span>"
                    — <span id="loans-search-count">{{ $loans->total() }}</span> encontrado(s)
                </div>
            @else
                <div id="loans-search-badge" class="hidden mb-4 flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Mostrando resultados para "<span id="loans-search-term" class="font-medium text-gray-700 dark:text-gray-300"></span>"
                    — <span id="loans-search-count">{{ $loans->total() }}</span> encontrado(s)
                </div>
            @endif

            <!-- Filtros Mobile -->
            <div class="md:hidden">
                <div class="grid grid-cols-3 gap-2">
                    @foreach([
                        'pending'  => ['label' => 'Pendientes', 'active' => 'bg-gray-900 text-white dark:bg-white dark:text-black', 'inactive' => 'bg-transparent text-gray-400 border-gray-200 dark:border-gray-800'],
                        'counter_offered' => ['label' => 'Contra-ofertas', 'active' => 'bg-gray-900 text-white dark:bg-white dark:text-black', 'inactive' => 'bg-transparent text-gray-400 border-gray-200 dark:border-gray-800'],
                        'approved' => ['label' => 'Aprobados',  'active' => 'bg-gray-900 text-white dark:bg-white dark:text-black', 'inactive' => 'bg-transparent text-gray-400 border-gray-200 dark:border-gray-800'],
                        'rejected' => ['label' => 'Rechazados', 'active' => 'bg-gray-900 text-white dark:bg-white dark:text-black', 'inactive' => 'bg-transparent text-gray-400 border-gray-200 dark:border-gray-800'],
                        'paid'     => ['label' => 'Pagados',    'active' => 'bg-gray-900 text-white dark:bg-white dark:text-black', 'inactive' => 'bg-transparent text-gray-400 border-gray-200 dark:border-gray-800'],
                        'all'      => ['label' => 'Todos',      'active' => 'bg-gray-900 text-white dark:bg-white dark:text-black', 'inactive' => 'bg-transparent text-gray-400 border-gray-200 dark:border-gray-800'],
                    ] as $key => $cfg)
                        <a href="{{ route('superadmin.loans.index', array_filter(['filter' => $key, 'search' => request('search')])) }}"
                           class="flex items-center justify-center gap-1.5 py-2.5 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all border
                                  {{ $filter === $key ? $cfg['active'] . ' border-transparent' : $cfg['inactive'] }}">
                            {{ $cfg['label'] }}
                            @if($key === 'pending' && $counts['pending'] > 0)
                                <svg class="w-3 h-3 {{ $filter === 'pending' ? 'text-white dark:text-black' : 'text-purple-600' }} animate-swing origin-top flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Filtros Desktop -->
<div class="hidden md:flex flex-col border-b border-gray-200 dark:border-gray-800 mb-8">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-0 overflow-x-auto no-scrollbar">
            @foreach(['pending' => 'Pendientes', 'counter_offered' => 'Contra-ofertas', 'approved' => 'Aprobados', 'rejected' => 'Rechazados', 'paid' => 'Pagados', 'all' => 'Todos'] as $key => $label)
                <a href="{{ route('superadmin.loans.index', ['filter' => $key, 'search' => request('search'), 'currency' => request('currency'), 'from_date' => request('from_date'), 'to_date' => request('to_date')]) }}"
                    class="relative flex items-center gap-2 flex-shrink-0 px-6 py-4 text-sm font-medium transition-colors
                              {{ $filter === $key ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-400' }}">

                    <span class="flex items-center gap-1">
                        {{ $label }}

                        @if($key === 'pending' && $counts['pending'] > 0)
                            <svg class="w-3.5 h-3.5 text-purple-600 animate-swing origin-top flex-shrink-0 relative top-[1px]"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        @endif
                    </span>

                    @if($counts[$key] > 0)
                        <span class="ml-2 text-xs {{ $filter === $key ? 'text-gray-500 dark:text-gray-400' : 'text-gray-300 dark:text-gray-500' }}">
                            {{ $counts[$key] }}
                        </span>
                    @endif

                    @if($filter === $key)
                        <span class="absolute bottom-0 left-0 right-0 h-px bg-gray-900 dark:bg-white"></span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>

            <div id="loans-list">
            <!-- Lista de préstamos -->
            <div class="divide-y divide-gray-100 dark:divide-gray-900">
                @forelse($loans as $loan)
                    @php
                        $selfie = $loan->user->documents?->selfie;
                        $rowPhoto = $selfie ? Storage::url($selfie) : ($loan->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($loan->user->name));
                        $rowName = $loan->user->person_type === 'juridica' && $loan->user->business_name ? $loan->user->business_name : $loan->user->name;
                        $rowStatusColor = match($loan->status) {
                            'approved' => 'text-green-500',
                            'counter_offered' => 'text-purple-600',
                            'rejected' => 'text-red-500',
                            'paid' => 'text-blue-500',
                            default => 'text-amber-500'
                        };
                        $rowStatusLabel = match($loan->status) {
                            'approved' => 'Aprobado',
                            'pending' => 'En revisión',
                            'counter_offered' => 'Contra-oferta',
                            'rejected' => 'Rechazado',
                            'paid' => 'Pagado',
                            default => $loan->status
                        };
                    @endphp
                    <div id="loan-{{ $loan->id }}" class="loan-row group transition-colors" data-search-name="{{ strtolower($rowName) }}" data-search-dni="{{ $loan->user->dni ?? '' }}" data-search-code="{{ strtolower($loan->code) }}">
                        <div class="flex items-center justify-between gap-3 py-4 md:py-5 cursor-pointer select-none hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-colors" onclick="toggleLoanRow({{ $loan->id }})">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-900 flex items-center justify-center overflow-hidden flex-shrink-0 border border-gray-200 dark:border-gray-800">
                                    @if($loan->user->is_ghost)
                                        <svg fill="#9333ea" width="20" height="20" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg"><path d="M511.984-.128c-229.216 0-415.681 199.903-415.681 445.6v546.672c0 13.216 8.16 25.088 20.496 29.84 3.712 1.471 7.632 2.16 11.504 2.16 8.848 0 17.536-3.68 23.712-10.527l120.592-133.12 94.431 130.432a31.918 31.918 0 0 0 25.68 13.215h.224a31.98 31.98 0 0 0 25.664-12.912l94.816-127.344 93.184 127.152a31.993 31.993 0 0 0 25.809 13.088 32.028 32.028 0 0 0 25.808-13.055l95.569-130.288 118 132.624c8.816 9.904 22.944 13.376 35.28 8.624 12.4-4.72 20.624-16.624 20.624-29.905V445.456C927.696 199.776 741.2-.128 511.984-.128zm351.711 908.16l-88.402-99.376c-6.432-7.216-15.808-11.311-25.407-10.687a32.105 32.105 0 0 0-24.32 13.024l-93.12 127.008-93.008-126.912A31.975 31.975 0 0 0 513.758 798h-.127a31.935 31.935 0 0 0-25.664 12.912l-94.689 127.152-92-127.088c-5.664-7.807-14.528-12.655-24.16-13.151-.592-.032-1.151-.065-1.743-.065a31.984 31.984 0 0 0-23.712 10.528l-91.376 100.848v-463.68c0-210.4 157.776-381.601 351.68-381.601 193.937 0 351.713 171.184 351.713 381.6V908.03h.015zM671.997 352.16c-35.28 0-63.84 28.592-63.84 63.808 0 35.248 28.56 63.84 63.84 63.84s63.84-28.592 63.84-63.84c0-35.216-28.56-63.808-63.84-63.808zm-320 0c-35.28 0-63.84 28.592-63.84 63.808 0 35.248 28.576 63.84 63.84 63.84s63.84-28.592 63.84-63.84c0-35.216-28.56-63.808-63.84-63.808z"/></svg>
                                    @else
                                        <img src="{{ $rowPhoto }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $rowName }}</p>
                                        <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-widest flex-shrink-0 {{ $loan->user->person_type === 'juridica' ? 'bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' }}">{{ $loan->user->person_type === 'juridica' ? 'JUR' : 'NAT' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <p class="text-sm font-black text-gray-900 dark:text-white tracking-tighter">{{ $loan->currency === 'USD' ? '$' : 'S/' }}{{ number_format($loan->amount, 2) }}</p>
                                        <span class="text-[8px] bg-black text-white dark:bg-white dark:text-black px-1.5 py-0.5 rounded font-black uppercase tracking-widest flex-shrink-0">{{ $loan->type === 'cuotas' ? 'Cuotas' : 'Único' }}</span>
                                        <span class="text-[8px] bg-black text-white dark:bg-white dark:text-black px-1.5 py-0.5 rounded font-bold tracking-wider flex-shrink-0">{{ $loan->code }}</span>
                                        @php $hasProcessing = $loan->installments->where('status', 'processing')->count() > 0; @endphp
                                        @if($hasProcessing)
                                            <a href="{{ route('superadmin.installments.index', ['filter' => 'pending_validation']) }}" onclick="event.stopPropagation()" class="px-1.5 py-0.5 text-purple-600 text-[8px] font-black rounded-full bg-purple-50 dark:bg-purple-500/10 flex-shrink-0">PAGO PEND.</a>
                                        @endif
                                        @if($loan->in_default)
                                            <span class="px-1.5 py-0.5 text-[8px] font-black rounded-full bg-red-500/10 text-red-500 flex-shrink-0">EN MORA</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 flex-shrink-0">
                                <span class="text-xs font-black uppercase tracking-widest {{ $rowStatusColor }}">{{ $rowStatusLabel }}</span>
                                <p class="hidden md:block text-[10px] text-gray-400 dark:text-gray-600 font-medium">{{ $loan->created_at->format('d/m/Y') }}</p>
                                <svg id="loan-chevron-{{ $loan->id }}" class="w-4 h-4 text-gray-400 dark:text-gray-600 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                        <div id="loan-details-{{ $loan->id }}" class="loan-details-wrapper">
                            <div class="loan-details-inner">
                                <div class="pb-6 md:pb-8">
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
                                        <span class="text-[8px] bg-black text-white dark:bg-white dark:text-black px-1.5 py-0.5 rounded font-bold tracking-wider">{{ $loan->code }}</span>
                                    </div>
                                    <p class="text-[9px] text-gray-400 dark:text-gray-600 font-medium">
                                        {{ $loan->created_at->format('d/m/Y H:i') }}
                                    </p>
                                    <div class="flex flex-wrap gap-1.5 mt-2">
                                        <a href="mailto:{{ $loan->user->email }}" class="flex items-center gap-1 bg-blue-500 hover:bg-blue-600 transition-colors px-2 py-1 text-white font-bold text-[9px]">
                                            {{ $loan->user->email }}
                                            <svg class="w-3 h-3 flex-shrink-0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
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
                                        <a href="https://wa.me/51{{ preg_replace('/\D/', '', $loan->user->phone) }}" target="_blank" class="flex items-center gap-1 bg-green-500 hover:bg-green-600 transition-colors px-2 py-1 text-white font-bold text-[9px]">
                                            {{ $loan->user->phone }}
                                            <svg class="w-3 h-3 flex-shrink-0" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M28 16C28 22.6274 22.6274 28 16 28C13.4722 28 11.1269 27.2184 9.19266 25.8837L5.09091 26.9091L6.16576 22.8784C4.80092 20.9307 4 18.5589 4 16C4 9.37258 9.37258 4 16 4C22.6274 4 28 9.37258 28 16Z" fill="url(#wssp_mob)"/>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M16 30C23.732 30 30 23.732 30 16C30 8.26801 23.732 2 16 2C8.26801 2 2 8.26801 2 16C2 18.5109 2.661 20.8674 3.81847 22.905L2 30L9.31486 28.3038C11.1894 29.3854 13.5789 30 16 30ZM16 27.8462C22.5425 27.8462 27.8462 22.5425 27.8462 16C27.8462 9.45755 22.5425 4.15385 16 4.15385C9.45755 4.15385 4.15385 9.45755 4.15385 16C4.15385 18.5261 4.9445 20.8675 6.29184 22.7902L5.23077 26.7692L9.27993 25.7569C11.1894 27.0746 13.5046 27.8462 16 27.8462Z" fill="white"/>
                                                <path d="M12.5 9.49989C12.1672 8.83131 11.6565 8.8905 11.1407 8.8905C10.2188 8.8905 8.78125 9.99478 8.78125 12.05C8.78125 13.7343 9.52345 15.578 12.0244 18.3361C14.438 20.9979 17.6094 22.3748 20.2422 22.3279C22.875 22.2811 23.4167 20.0154 23.4167 19.2503C23.4167 18.9112 23.2062 18.742 23.0613 18.696C22.1641 18.2654 20.5093 17.4631 20.1328 17.3124C19.7563 17.1617 19.5597 17.3656 19.4375 17.4765C19.0961 17.8018 18.4193 18.7608 18.1875 18.9765C17.9558 19.1922 17.6103 19.083 17.4665 19.0015C16.9374 18.7892 15.5029 18.1511 14.3595 17.0426C12.9453 15.6718 12.8623 15.2001 12.5959 14.7803C12.3828 14.4444 12.5392 14.2384 12.6172 14.1483C12.9219 13.7968 13.3426 13.254 13.5313 12.9843C13.7199 12.7145 13.5702 12.305 13.4803 12.05C13.0938 10.953 12.7663 10.0347 12.5 9.49989Z" fill="white"/>
                                                <defs>
                                                    <linearGradient id="wssp_mob" x1="26.5" y1="7" x2="4" y2="28" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#5BD066"/>
                                                        <stop offset="1" stop-color="#27B43E"/>
                                                    </linearGradient>
                                                </defs>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Perfil del Solicitante Mobile -->
                            @if($loan->user->clientProfile)
                                @php $profile = $loan->user->clientProfile; @endphp
                                <div class="space-y-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-4">
                                    <!-- Educación Mobile -->
                                    <div class="pb-4 border-b border-gray-200 dark:border-gray-800">
                                        <p class="text-[9px] text-gray-400 dark:text-gray-600 uppercase font-black mb-2 tracking-widest">Educación</p>
                                        @php
                                            $mEduLabels = ['primaria' => 'Primaria', 'secundaria' => 'Secundaria', 'tecnico' => 'Técnico', 'universitario' => 'Universitario', 'postgrado' => 'Postgrado'];
                                            $mEduCfg = $mEduLabels[$profile->academic_status ?? ''] ?? $profile->academic_status;
                                            $mEduColor = match($profile->academic_status ?? '') {
                                                'primaria' => 'gray', 'secundaria' => 'blue', 'tecnico' => 'amber', 'universitario' => 'purple', 'postgrado' => 'emerald', default => 'gray'
                                            };
                                            $mEduC = match($mEduColor) {
                                                'gray' => 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700',
                                                'blue' => 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-500/20',
                                                'amber' => 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-500/20',
                                                'purple' => 'bg-purple-50 dark:bg-purple-500/10 text-purple-700 dark:text-purple-300 border-purple-200 dark:border-purple-500/20',
                                                'emerald' => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-500/20',
                                                default => 'bg-gray-100 dark:bg-gray-800'
                                            };
                                        @endphp
                                        <div class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-lg {{ $mEduC }} border mb-1.5">
                                            <span class="text-[9px] font-bold">{{ $mEduCfg }}</span>
                                        </div>
                                        @if($profile->institution)
                                            <p class="text-[9px] text-gray-600 dark:text-gray-400"><span class="text-gray-400">🏛</span> {{ Str::limit($profile->institution, 28) }}</p>
                                        @endif
                                        @if($profile->career)
                                            <p class="text-[9px] text-gray-500 dark:text-gray-500 mt-0.5">{{ Str::limit($profile->career, 28) }}</p>
                                        @endif
                                    </div>

                                    <!-- Info Laboral Mobile -->
                                    <div class="pb-4 border-b border-gray-200 dark:border-gray-800">
                                        <p class="text-[9px] text-gray-400 dark:text-gray-600 uppercase font-black mb-2 tracking-widest">Información Laboral</p>
                                        @if($profile->is_employed)
                                            @php
                                                $mobileEmploymentLabels = ['dependiente' => 'Dependente', 'independiente' => 'Independiente', 'empresario' => 'Empresario', 'profesional' => 'Profesional'];
                                                $mobileLabel = $mobileEmploymentLabels[$profile->employment_type] ?? ucfirst($profile->employment_type);
                                            @endphp
                                            <div class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20 mb-1.5">
                                                <span class="text-[9px] font-bold text-indigo-700 dark:text-indigo-300">{{ $mobileLabel }}</span>
                                            </div>
                                            @if($profile->employment_type === 'dependiente')
                                                @if($profile->workplace)
                                                    <p class="text-[9px] text-gray-600 dark:text-gray-400 leading-tight mt-0.5"><span class="text-gray-400 font-medium">Empresa:</span> {{ Str::limit($profile->workplace, 24) }}</p>
                                                @endif
                                                @if($profile->job_title)
                                                    <p class="text-[9px] text-gray-500 dark:text-gray-500 leading-tight"><span class="text-gray-400 font-medium">Cargo:</span> <span class="font-bold text-gray-700 dark:text-gray-300">{{ $profile->job_title }}</span></p>
                                                @endif
                                            @elseif($profile->business_name || $profile->independent_type)
                                                @if($profile->independent_type)
                                                    <p class="text-[9px] text-gray-600 dark:text-gray-400 leading-tight mt-0.5"><span class="text-gray-400 font-medium">Actividad:</span> {{ ucfirst($profile->independent_type) }}</p>
                                                @endif
                                                @if($profile->business_name)
                                                    <p class="text-[9px] text-gray-500 dark:text-gray-500 leading-tight"><span class="text-gray-400 font-medium">Negocio:</span> {{ Str::limit($profile->business_name, 22) }}</p>
                                                @endif
                                            @endif
                                        @else
                                            <div class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-red-50 dark:bg-red-500/10 border border-red-100 dark:border-red-500/20">
                                                <span class="text-[9px] font-bold text-red-600 dark:text-red-400">Sin empleo</span>
                                            </div>
                                        @endif
                                        <div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                                            <p class="text-[9px] font-bold text-green-600 dark:text-green-500">{{ $loan->currency === 'USD' ? '$' : 'S/' }}{{ number_format($profile->monthly_income, 0) }} <span class="font-normal text-gray-400">/mes</span></p>
                                        </div>
                                    </div>

                                    <!-- Estado Civil + Familia Mobile -->
                                    <div class="pb-4 border-b border-gray-200 dark:border-gray-800 grid grid-cols-2 gap-3">
                                        <div>
                                            <p class="text-[9px] text-gray-400 dark:text-gray-600 uppercase font-black mb-1.5 tracking-widest">Estado Civil</p>
                                            @php
                                                $mMaritalLabels = ['soltero' => ['l' => 'Soltero/a', 'c' => 'slate'], 'casado_separado' => ['l' => 'Casado (B.S)', 'c' => 'rose'], 'casado_compartido' => ['l' => 'Casado (B.C)', 'c' => 'pink'], 'divorciado' => ['l' => 'Divorciado/a', 'c' => 'orange'], 'viudo' => ['l' => 'Viudo/a', 'c' => 'stone']];
                                                $mMarital = $mMaritalLabels[$profile->marital_status ?? ''] ?? ['l' => ucfirst($profile->marital_status), 'c' => 'gray'];
                                                $mMc = match($mMarital['c']) {
                                                    'slate' => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200',
                                                    'rose' => 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-300 border-rose-200',
                                                    'pink' => 'bg-pink-50 dark:bg-pink-500/10 text-pink-700 dark:text-pink-300 border-pink-200',
                                                    'orange' => 'bg-orange-50 dark:bg-orange-500/10 text-orange-700 dark:text-orange-300 border-orange-200',
                                                    default => 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200'
                                                };
                                            @endphp
                                            <div class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-lg {{ $mMc }} border">
                                                <span class="text-[9px] font-bold">{{ $mMarital['l'] }}</span>
                                            </div>
                                            @if($profile->marital_status === 'casado_compartido' && ($profile->spouse_first_name || $profile->spouse_last_name))
                                                <p class="text-[8px] text-gray-500 mt-1">{{ $profile->spouse_first_name }} {{ $profile->spouse_last_name }}</p>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-[9px] text-gray-400 dark:text-gray-600 uppercase font-black mb-1.5 tracking-widest">Familia</p>
                                            @if($profile->has_children)
                                                <div class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-lg bg-cyan-50 dark:bg-cyan-500/10 border border-cyan-200 dark:border-cyan-500/20">
                                                    <span class="text-[9px] font-bold text-cyan-700 dark:text-cyan-300">{{ $profile->children_count }} {{ $profile->children_count == 1 ? 'hijo' : 'hijos' }}</span>
                                                </div>
                                            @else
                                                <div class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200">
                                                    <span class="text-[9px] font-bold text-slate-600 dark:text-slate-400">Sin hijos</span>
                                                </div>
                                            @endif
                                            @if($profile->instagram)
                                                {{-- IG-FIX --}}
                                                <a href="https://instagram.com/{{ $profile->instagram }}" target="_blank" class="flex items-center gap-1 text-[8px] text-purple-600 dark:text-purple-400 mt-1 hover:underline">{{ $profile->instagram }}</a>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Destino + Desembolso Mobile -->
                                    <div class="pb-4 border-b border-gray-200 dark:border-gray-800 grid grid-cols-2 gap-3">
                                        <div>
                                            <p class="text-[9px] text-gray-400 dark:text-gray-600 uppercase font-black mb-1.5 tracking-widest">Destino</p>
                                            <div class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 mb-1">
                                                <span class="text-[9px] font-bold text-emerald-700 dark:text-emerald-300 uppercase">{{ $loan->bank_name }}</span>
                                            </div>
                                            <p class="text-[8px] text-gray-500 dark:text-gray-500 mt-0.5">{{ ucfirst($loan->account_type) }}</p>
                                            <p class="text-[8px] font-mono text-gray-600 dark:text-gray-400">{{ Str::limit($loan->account_number, 14) }}</p>
                                            @if($loan->cci)
                                                <p class="text-[7px] font-mono text-gray-400 dark:text-gray-500 mt-0.5">CCI: {{ Str::limit($loan->cci, 16) }}</p>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-[9px] text-gray-400 dark:text-gray-600 uppercase font-black mb-1.5 tracking-widest">Desembolso</p>
                                            @if($loan->disbursement_date)
                                                @php
                                                    $mDisbDate = \Carbon\Carbon::parse($loan->disbursement_date);
                                                    $mIsDisbursed = $mDisbDate->isPast();
                                                @endphp
                                                <div class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-lg {{ $mIsDisbursed ? 'bg-green-50 dark:bg-green-500/10 border-green-200 text-green-700 dark:text-green-300' : 'bg-amber-50 dark:bg-amber-500/10 border-amber-200 text-amber-700 dark:text-amber-300' }} border">
                                                    <span class="text-[9px] font-bold">{{ $mDisbDate->format('d/m/Y') }}</span>
                                                    <span class="text-[7px] font-normal opacity-70">{{ $mIsDisbursed ? 'ok' : 'pend' }}</span>
                                                </div>
                                            @else
                                                <p class="text-[9px] text-gray-400 font-medium">—</p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Términos Mobile -->
                                    <div class="pb-4 border-b border-gray-200 dark:border-gray-800">
                                        <div class="flex items-center justify-between mb-2">
                                            <p class="text-[9px] text-gray-400 dark:text-gray-600 uppercase font-black tracking-widest">Términos</p>
                                            @if($loan->type === 'cuotas')
                                                <button type="button"
                                                    onclick="openScheduleModal({{ json_encode([
                                                        'amount' => $loan->amount,
                                                        'total_amount' => $loan->total_amount,
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
                                                    class="px-2 py-1 bg-amber-400 text-black text-[8px] font-black uppercase tracking-wider hover:bg-amber-500 transition-colors"
                                                    title="Ver datos">
                                                    Ver datos
                                                </button>
                                            @endif
                                        </div>
                                        @php
                                            $mFreqLabel = match($loan->payment_frequency ?? 'mensual') { 'diario' => 'día', 'semanal' => 'sem', 'quincenal' => 'quin', default => 'mes' };
                                            $mTermBadge = $loan->type === 'unico' ? 'bg-violet-100 dark:bg-violet-500/10 text-violet-700 dark:text-violet-300 border-violet-200' : 'bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-300 border-blue-200';
                                        @endphp
                                        <div class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-lg {{ $mTermBadge }} border mb-1.5">
                                            <span class="text-[9px] font-bold">
                                                @if($loan->type === 'cuotas')
                                                    {{ $loan->months }}m ({{ $loan->amortization_system === 'frances' ? ucfirst($loan->payment_frequency) : 'Américano' }})
                                                @else
                                                    Único • {{ $loan->payment_days }}d
                                                @endif
                                            </span>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2 mt-1">
                                            <div class="bg-white dark:bg-gray-800 rounded-lg p-2 border border-gray-100 dark:border-gray-700">
                                                <p class="text-[7px] text-gray-400 uppercase">Cuota/{{ $mFreqLabel }}</p>
                                                <p class="text-[10px] font-bold text-gray-900 dark:text-white">{{ $loan->currency === 'USD' ? '$' : 'S/' }}{{ number_format($loan->monthly_payment, 0) }}</p>
                                            </div>
                                            <div class="bg-white dark:bg-gray-800 rounded-lg p-2 border border-gray-100 dark:border-gray-700">
                                                <p class="text-[7px] text-green-500 uppercase font-bold">Ganancia</p>
                                                <p class="text-[10px] font-black text-green-600 dark:text-green-400">{{ $loan->currency === 'USD' ? '$' : 'S/' }}{{ number_format($loan->total_amount - $loan->amount, 0) }}</p>
                                            </div>
                                        </div>
                                        <p class="text-[8px] text-gray-400 mt-1">Tasa: <span class="font-bold text-gray-600 dark:text-gray-300">{{ $loan->interest_rate }}%</span></p>
                                    </div>

                                    <!-- Motivo Mobile -->
                                    <div>
                                        <p class="text-[9px] text-gray-400 dark:text-gray-600 uppercase font-black mb-1.5 tracking-widest">Motivo del Préstamo</p>
                                        <div class="p-2.5 rounded-xl bg-orange-50/50 dark:bg-orange-500/5 border border-orange-100 dark:border-orange-500/10">
                                            <p class="text-[9px] text-gray-600 dark:text-gray-400 italic leading-relaxed">"{{ Str::limit($loan->reason, 80) }}"</p>
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
                                                    'payment_frequency' => $loan->payment_frequency ?? 'mensual',
                                                    'disbursement_date' => $loan->disbursement_date
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
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs font-black uppercase tracking-widest
                                                    {{ $loan->status === 'approved' ? 'text-green-500' : '' }}
                                                    {{ $loan->status === 'counter_offered' ? 'text-purple-600' : '' }}
                                                    {{ $loan->status === 'rejected' ? 'text-red-500' : '' }}
                                                    {{ $loan->status === 'paid' ? 'text-blue-500' : '' }}">
                                            {{ match($loan->status) {
                                                'approved' => 'Aprobado',
                                                'pending'  => 'En revisión',
                                                'counter_offered' => 'Contra-oferta',
                                                'rejected' => 'Rechazado',
                                                'paid'     => 'Pagado',
                                                default    => $loan->status
                                            } }}
                                        </span>
                                        @php
                                            $hasProcessing = $loan->installments->where('status', 'processing')->count() > 0;
                                        @endphp
                                        @if($hasProcessing)
                                            <a href="{{ route('superadmin.installments.index', ['filter' => 'pending_validation']) }}" 
                                               class="px-2 py-0.5 text-purple-600 text-[8px] font-black rounded-full">
                                                PAGO PENDIENTE
                                            </a>
                                        @endif
                                        @if(in_array($loan->status, ['approved', 'paid']))
                                            <form method="POST" action="{{ route('superadmin.loans.toggle-default', $loan) }}" class="inline-block">
                                                @csrf
                                                <button type="submit"
                                                    class="px-2 py-0.5 text-[8px] font-black rounded-full transition-colors {{ $loan->in_default ? 'bg-red-500/10 text-red-500 hover:bg-red-500/20' : 'bg-gray-100 dark:bg-gray-800 text-gray-400 hover:text-orange-500' }}">
                                                    {{ $loan->in_default ? 'EN MORA' : 'MARCAR MORA' }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
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
                                            <span class="text-[8px] bg-black text-white dark:bg-white dark:text-black px-1.5 py-0.5 rounded font-bold tracking-wider">{{ $loan->code }}</span>
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
                                                    'payment_frequency' => $loan->payment_frequency ?? 'mensual',
                                                    'disbursement_date' => $loan->disbursement_date
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
                                        <div class="pl-6 border-l border-gray-200 dark:border-gray-800 flex flex-col items-end gap-1">
                                            <span class="text-xs font-black uppercase tracking-widest
                                                        {{ $loan->status === 'approved' ? 'text-green-500' : '' }}
                                                        {{ $loan->status === 'rejected' ? 'text-red-500' : '' }}
                                                        {{ $loan->status === 'paid' ? 'text-blue-500' : '' }}">
                                                {{ match($loan->status) {
                                                    'approved' => 'APROBADO',
                                                    'pending'  => 'EN REVISIÓN',
                                                    'counter_offered' => 'CONTRA-OFERTA',
                                                    'rejected' => 'RECHAZADO',
                                                    'paid'     => 'PAGADO',
                                                    default    => $loan->status
                                                } }}
                                            </span>
                                            @php
                                                $hasProcessing = $loan->installments->where('status', 'processing')->count() > 0;
                                            @endphp
                                            @if($hasProcessing)
                                                <a href="{{ route('superadmin.installments.index', ['filter' => 'pending_validation']) }}" 
                                                   class="px-2 py-0.5 text-purple-600 text-[8px] font-black rounded-full">
                                                    PAGO PENDIENTE
                                                </a>
                                            @endif
                                            @if(in_array($loan->status, ['approved', 'paid']))
                                                <form method="POST" action="{{ route('superadmin.loans.toggle-default', $loan) }}" class="inline-block">
                                                    @csrf
                                                    <button type="submit"
                                                        class="px-2 py-0.5 text-[8px] font-black rounded-full transition-colors {{ $loan->in_default ? 'bg-red-500/10 text-red-500 hover:bg-red-500/20' : 'bg-gray-100 dark:bg-gray-800 text-gray-400 hover:text-orange-500' }}">
                                                        {{ $loan->in_default ? 'EN MORA' : 'MARCAR MORA' }}
                                                    </button>
                                                </form>
                                            @endif
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
                                        @php
                                            $eduLabels = [
                                                'primaria' => ['label' => 'Primaria', 'color' => 'gray'],
                                                'secundaria' => ['label' => 'Secundaria', 'color' => 'blue'],
                                                'tecnico' => ['label' => 'Técnico', 'color' => 'amber'],
                                                'universitario' => ['label' => 'Universitario', 'color' => 'purple'],
                                                'postgrado' => ['label' => 'Postgrado', 'color' => 'emerald'],
                                            ];
                                            $eduCfg = $eduLabels[$profile->academic_status ?? ''] ?? ['label' => $profile->academic_status, 'color' => 'gray'];
                                            $eduColors = [
                                                'gray' => ['bg' => 'bg-gray-50 dark:bg-gray-500/10', 'border' => 'border-gray-200 dark:border-gray-500/20', 'text' => 'text-gray-700 dark:text-gray-300', 'badge' => 'text-gray-600 dark:text-gray-400'],
                                                'blue' => ['bg' => 'bg-blue-50 dark:bg-blue-500/10', 'border' => 'border-blue-200 dark:border-blue-500/20', 'text' => 'text-blue-700 dark:text-blue-300', 'badge' => 'text-blue-600 dark:text-blue-400'],
                                                'amber' => ['bg' => 'bg-amber-50 dark:bg-amber-500/10', 'border' => 'border-amber-200 dark:border-amber-500/20', 'text' => 'text-amber-700 dark:text-amber-300', 'badge' => 'text-amber-600 dark:text-amber-400'],
                                                'purple' => ['bg' => 'bg-purple-50 dark:bg-purple-500/10', 'border' => 'border-purple-200 dark:border-purple-500/20', 'text' => 'text-purple-700 dark:text-purple-300', 'badge' => 'text-purple-600 dark:text-purple-400'],
                                                'emerald' => ['bg' => 'bg-emerald-50 dark:bg-emerald-500/10', 'border' => 'border-emerald-200 dark:border-emerald-500/20', 'text' => 'text-emerald-700 dark:text-emerald-300', 'badge' => 'text-emerald-600 dark:text-emerald-400'],
                                            ];
                                            $c = $eduColors[$eduCfg['color']] ?? $eduColors['gray'];
                                        @endphp
                                        <div class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg {{ $c['bg'] }} border {{ $c['border'] }} mb-2">
                                            <svg class="w-3 h-3 {{ $c['badge'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                                            <span class="text-[10px] font-bold {{ $c['text'] }}">{{ $eduCfg['label'] }}</span>
                                        </div>
                                        @if($profile->institution || $profile->career)
                                            <div class="space-y-1.5 mt-2 pl-2 border-l-2 {{ str_replace('border-', 'border-l-', $c['border']) }}">
                                                @if($profile->institution)
                                                    <div class="flex items-start gap-1.5">
                                                        <svg class="w-3 h-3 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                        <div>
                                                            <p class="text-[8px] text-gray-400 uppercase tracking-wider">Institución</p>
                                                            <p class="text-[10px] font-bold text-gray-900 dark:text-white">{{ $profile->institution }}</p>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if($profile->career)
                                                    <div class="flex items-start gap-1.5">
                                                        <svg class="w-3 h-3 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                        <div>
                                                            <p class="text-[8px] text-gray-400 uppercase tracking-wider">Carrera / Estudio</p>
                                                            <p class="text-[10px] font-bold text-gray-900 dark:text-white">{{ $profile->career }}</p>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-[9px] text-gray-400 dark:text-gray-600 uppercase font-black mb-1.5 tracking-[0.15em]">Información Laboral</p>
                                        @if($profile->is_employed)
                                            @php
                                                $employmentLabels = [
                                                    'dependiente' => 'Dependente',
                                                    'independiente' => 'Independiente',
                                                    'empresario' => 'Empresario',
                                                    'profesional' => 'Profesional Independiente',
                                                ];
                                                $employmentLabel = $employmentLabels[$profile->employment_type] ?? ucfirst($profile->employment_type);
                                            @endphp
                                            <div class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20 mb-2">
                                                <svg class="w-3 h-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.9 23.9 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                <span class="text-[10px] font-bold text-indigo-700 dark:text-indigo-300">{{ $employmentLabel }}</span>
                                            </div>
                                            @if($profile->employment_type === 'dependiente')
                                                @if($profile->workplace || $profile->job_title)
                                                    <div class="space-y-1.5 mt-2 pl-2 border-l-2 border-indigo-200 dark:border-indigo-500/30">
                                                        @if($profile->workplace)
                                                            <div class="flex items-start gap-1.5">
                                                                <svg class="w-3 h-3 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                                <div>
                                                                    <p class="text-[8px] text-gray-400 uppercase tracking-wider">Empresa</p>
                                                                    <p class="text-[10px] font-bold text-gray-900 dark:text-white">{{ $profile->workplace }}</p>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if($profile->job_title)
                                                            <div class="flex items-start gap-1.5">
                                                                <svg class="w-3 h-3 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                                <div>
                                                                    <p class="text-[8px] text-gray-400 uppercase tracking-wider">Cargo</p>
                                                                    <p class="text-[10px] font-bold text-gray-900 dark:text-white">{{ $profile->job_title }}</p>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            @elseif($profile->independent_type || $profile->business_name || $profile->business_activity)
                                                <div class="space-y-1.5 mt-2 pl-2 border-l-2 border-indigo-200 dark:border-indigo-500/30">
                                                    @if($profile->independent_type)
                                                        <div class="flex items-start gap-1.5">
                                                            <svg class="w-3 h-3 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                                            <div>
                                                                <p class="text-[8px] text-gray-400 uppercase tracking-wider">Tipo de Actividad</p>
                                                                <p class="text-[10px] font-bold text-gray-900 dark:text-white">{{ ucfirst($profile->independent_type) }}</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if($profile->business_name)
                                                        <div class="flex items-start gap-1.5">
                                                            <svg class="w-3 h-3 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                            <div>
                                                                <p class="text-[8px] text-gray-400 uppercase tracking-wider">Negocio</p>
                                                                <p class="text-[10px] font-bold text-gray-900 dark:text-white">{{ $profile->business_name }}</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if($profile->business_activity)
                                                        <div class="flex items-start gap-1.5">
                                                            <svg class="w-3 h-3 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                            <div>
                                                                <p class="text-[8px] text-gray-400 uppercase tracking-wider">Actividad</p>
                                                                <p class="text-[10px] font-bold text-gray-900 dark:text-white">{{ $profile->business_activity }}</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        @else
                                            <div class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg bg-red-50 dark:bg-red-500/10 border border-red-100 dark:border-red-500/20">
                                                <svg class="w-3 h-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                <span class="text-[10px] font-bold text-red-600 dark:text-red-400">Sin empleo</span>
                                            </div>
                                        @endif
                                        <div class="mt-2 pt-2 border-t border-gray-100 dark:border-gray-800">
                                            <div class="flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <p class="text-[10px] font-black text-green-600 dark:text-green-400">{{ $loan->currency === 'USD' ? '$' : 'S/' }} {{ number_format($profile->monthly_income, 2) }} <span class="font-normal text-gray-400">/mes</span></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-[9px] text-gray-400 dark:text-gray-600 uppercase font-black mb-1.5 tracking-[0.15em]">Estado Civil</p>
                                        @php
                                            $maritalLabels = [
                                                'soltero' => ['label' => 'Soltero/a', 'color' => 'slate', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                                                'casado_separado' => ['label' => 'Casado (B.S)', 'color' => 'rose', 'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
                                                'casado_compartido' => ['label' => 'Casado (B.C)', 'color' => 'pink', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
                                                'divorciado' => ['label' => 'Divorciado/a', 'color' => 'orange', 'icon' => 'M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25'],
                                                'viudo' => ['label' => 'Viudo/a', 'color' => 'stone', 'icon' => 'M9 9l10.5-3m0 0l-10.5 3m10.5-3v6m0-6H9'],
                                            ];
                                            $maritalCfg = $maritalLabels[$profile->marital_status ?? ''] ?? ['label' => ucfirst($profile->marital_status), 'color' => 'gray', 'icon' => ''];
                                            $mc = match($maritalCfg['color']) {
                                                'slate' => ['bg' => 'bg-slate-50 dark:bg-slate-500/10', 'border' => 'border-slate-200 dark:border-slate-500/20', 'text' => 'text-slate-700 dark:text-slate-300', 'badge' => 'text-slate-600 dark:text-slate-400'],
                                                'rose' => ['bg' => 'bg-rose-50 dark:bg-rose-500/10', 'border' => 'border-rose-200 dark:border-rose-500/20', 'text' => 'text-rose-700 dark:text-rose-300', 'badge' => 'text-rose-600 dark:text-rose-400'],
                                                'pink' => ['bg' => 'bg-pink-50 dark:bg-pink-500/10', 'border' => 'border-pink-200 dark:border-pink-500/20', 'text' => 'text-pink-700 dark:text-pink-300', 'badge' => 'text-pink-600 dark:text-pink-400'],
                                                'orange' => ['bg' => 'bg-orange-50 dark:bg-orange-500/10', 'border' => 'border-orange-200 dark:border-orange-500/20', 'text' => 'text-orange-700 dark:text-orange-300', 'badge' => 'text-orange-600 dark:text-orange-400'],
                                                'stone' => ['bg' => 'bg-stone-50 dark:bg-stone-500/10', 'border' => 'border-stone-200 dark:border-stone-500/20', 'text' => 'text-stone-700 dark:text-stone-300', 'badge' => 'text-stone-600 dark:text-stone-400'],
                                                default => ['bg' => 'bg-gray-50 dark:bg-gray-500/10', 'border' => 'border-gray-200 dark:border-gray-500/20', 'text' => 'text-gray-700 dark:text-gray-300', 'badge' => 'text-gray-600 dark:text-gray-400'],
                                            };
                                        @endphp
                                        <div class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg {{ $mc['bg'] }} border {{ $mc['border'] }} mb-2">
                                            @if($maritalCfg['icon'])
                                                <svg class="w-3 h-3 {{ $mc['badge'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $maritalCfg['icon'] }}"/></svg>
                                            @endif
                                            <span class="text-[10px] font-bold {{ $mc['text'] }}">{{ $maritalCfg['label'] }}</span>
                                        </div>
                                        @if($profile->marital_status === 'casado_compartido' && ($profile->spouse_first_name || $profile->spouse_last_name))
                                            <div class="space-y-1.5 mt-2 pl-2 border-l-2 border-pink-200 dark:border-pink-500/30">
                                                <div class="flex items-start gap-1.5">
                                                    <svg class="w-3 h-3 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                    <div>
                                                        <p class="text-[8px] text-gray-400 uppercase tracking-wider">Cónyuge</p>
                                                        <p class="text-[10px] font-bold text-gray-900 dark:text-white">{{ $profile->spouse_first_name }} {{ $profile->spouse_last_name }}</p>
                                                        @if($profile->spouse_dni)
                                                            <p class="text-[9px] text-gray-500 dark:text-gray-500 mt-0.5"><span class="font-mono">DNI: {{ $profile->spouse_dni }}</span></p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-[9px] text-gray-400 dark:text-gray-600 uppercase font-black mb-1.5 tracking-[0.15em]">Familia</p>
                                        @if($profile->has_children)
                                            <div class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg bg-cyan-50 dark:bg-cyan-500/10 border border-cyan-200 dark:border-cyan-500/20 mb-2">
                                                <svg class="w-3 h-3 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                <span class="text-[10px] font-bold text-cyan-700 dark:text-cyan-300">{{ $profile->children_count }} {{ $profile->children_count == 1 ? 'hijo' : 'hijos' }}</span>
                                            </div>
                                        @else
                                            <div class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg bg-slate-50 dark:bg-slate-500/10 border border-slate-200 dark:border-slate-500/20 mb-2">
                                                <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                <span class="text-[10px] font-bold text-slate-600 dark:text-slate-400">Sin hijos</span>
                                            </div>
                                        @endif
                                        @if($profile->instagram)
                                            {{-- IG-FIX-DESKTOP --}}
                                            <div class="mt-2 pt-2 border-t border-gray-100 dark:border-gray-800">
                                                <a href="https://instagram.com/{{ $profile->instagram }}" target="_blank" class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-500/10 dark:to-pink-500/10 border border-purple-200 dark:border-purple-500/20 hover:border-purple-400 transition-colors group">
                                                    <svg class="w-3.5 h-3.5 text-purple-500 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                                    <span class="text-[10px] font-bold text-purple-600 dark:text-purple-400 group-hover:text-purple-500 transition-colors">{{ $profile->instagram }}</span>
                                                    <svg class="w-2.5 h-2.5 text-purple-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Fila 3: Detalles del Préstamo -->
                                <div class="grid grid-cols-4 gap-6">
                                    <div>
                                        <p class="text-[9px] text-gray-400 dark:text-gray-600 uppercase font-black mb-1.5 tracking-[0.15em]">Destino de Fondos</p>
                                        <div class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 mb-2">
                                            <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                            <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-300 uppercase">{{ $loan->bank_name }}</span>
                                        </div>
                                        <div class="space-y-1.5 mt-2 pl-2 border-l-2 border-emerald-200 dark:border-emerald-500/30">
                                            <div class="flex items-start gap-1.5">
                                                <svg class="w-3 h-3 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                                <div>
                                                    <p class="text-[8px] text-gray-400 uppercase tracking-wider">Tipo de Cuenta</p>
                                                    <p class="text-[10px] font-bold text-gray-900 dark:text-white">{{ ucfirst($loan->account_type) }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-start gap-1.5">
                                                <svg class="w-3 h-3 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                                <div>
                                                    <p class="text-[8px] text-gray-400 uppercase tracking-wider">N° Cuenta</p>
                                                    <p class="text-[10px] font-mono font-bold text-gray-900 dark:text-white">{{ $loan->account_number }}</p>
                                                </div>
                                            </div>
                                            @if($loan->cci)
                                                <div class="flex items-start gap-1.5">
                                                    <svg class="w-3 h-3 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                                    <div>
                                                        <p class="text-[8px] text-gray-400 uppercase tracking-wider">CCI Interbancario</p>
                                                        <p class="text-[9px] font-mono text-gray-600 dark:text-gray-400">{{ $loan->cci }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-[9px] text-gray-400 dark:text-gray-600 uppercase font-black mb-1.5 tracking-[0.15em]">Desembolso</p>
                                        @if($loan->disbursement_date)
                                            @php
                                                $disbDate = \Carbon\Carbon::parse($loan->disbursement_date);
                                                $isDisbursed = $disbDate->isPast();
                                            @endphp
                                            <div class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg {{ $isDisbursed ? 'bg-green-50 dark:bg-green-500/10 border-green-200 dark:border-green-500/20' : 'bg-amber-50 dark:bg-amber-500/10 border-amber-200 dark:border-amber-500/20' }} border mb-2">
                                                <svg class="w-3 h-3 {{ $isDisbursed ? 'text-green-600 dark:text-green-400' : 'text-amber-600 dark:text-amber-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                <span class="text-[10px] font-bold {{ $isDisbursed ? 'text-green-700 dark:text-green-300' : 'text-amber-700 dark:text-amber-300' }}">{{ $disbDate->format('d/m/Y') }}</span>
                                                <span class="text-[8px] font-normal {{ $isDisbursed ? 'text-green-500' : 'text-amber-500' }}">{{ $isDisbursed ? 'desembolsado' : 'pendiente' }}</span>
                                            </div>
                                        @else
                                            <div class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg bg-gray-50 dark:bg-gray-500/10 border border-gray-200 dark:border-gray-500/20">
                                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span class="text-[10px] font-bold text-gray-500">Sin fecha</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-1.5 mb-1.5">
                                            <p class="text-[9px] text-gray-400 dark:text-gray-600 uppercase font-black tracking-[0.15em]">Términos</p>
                                            @if($loan->type === 'cuotas')
                                                <button type="button"
                                                    onclick="openScheduleModal({{ json_encode([
                                                        'amount' => $loan->amount,
                                                        'total_amount' => $loan->total_amount,
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
                                                    class="px-2.5 py-1.5 bg-amber-400 text-black text-[9px] font-black uppercase tracking-wider hover:bg-amber-500 transition-colors"
                                                    title="Ver datos">
                                                    Ver datos
                                                </button>
                                            @endif
                                        </div>
                                        @php
                                            $termBadgeColor = $loan->type === 'unico' ? 'bg-violet-50 dark:bg-violet-500/10 border-violet-200 dark:border-violet-500/20 text-violet-700 dark:text-violet-300' : 'bg-blue-50 dark:bg-blue-500/10 border-blue-200 dark:border-blue-500/20 text-blue-700 dark:text-blue-300';
                                            $termIcon = $loan->type === 'unico' ? 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6' : 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10';
                                            $freqLabelDesktop = match($loan->payment_frequency ?? 'mensual') {
                                                'diario' => 'día',
                                                'semanal' => 'semana',
                                                'quincenal' => 'quincena',
                                                default => 'mes'
                                            };
                                        @endphp
                                        <div class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg {{ $termBadgeColor }} border mb-2">
                                            <svg class="w-3 h-3 {{ str_replace(['bg-', 'dark:', '/'], '', explode(' ', $termBadgeColor)[0]) === 'violet' ? 'text-violet-600 dark:text-violet-400' : 'text-blue-600 dark:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $termIcon }}"/></svg>
                                            <span class="text-[10px] font-bold {{ $termBadgeColor }}">
                                                @if($loan->type === 'cuotas')
                                                    {{ $loan->months }} meses ({{ $loan->amortization_system === 'frances' ? ucfirst($loan->payment_frequency) : 'Americano' }})
                                                @else
                                                    Pago único • {{ $loan->payment_days }} días
                                                @endif
                                            </span>
                                        </div>
                                        <div class="space-y-1.5 mt-2 pl-2 border-l-2 border-blue-200 dark:border-blue-500/30">
                                            <div class="flex items-start gap-1.5">
                                                <svg class="w-3 h-3 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <div>
                                                    <p class="text-[8px] text-gray-400 uppercase tracking-wider">Cuota / {{ $loan->type === 'cuotas' ? $freqLabelDesktop : 'pago' }}</p>
                                                    <p class="text-[10px] font-bold text-gray-900 dark:text-white">{{ $loan->currency === 'USD' ? '$' : 'S/' }}{{ number_format($loan->monthly_payment, 2) }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-start gap-1.5">
                                                <svg class="w-3 h-3 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                                <div>
                                                    <p class="text-[8px] text-green-500 uppercase tracking-wider font-bold">Ganancia Total</p>
                                                    <p class="text-[10px] font-black text-green-600 dark:text-green-400">{{ $loan->currency === 'USD' ? '$' : 'S/' }}{{ number_format($loan->total_amount - $loan->amount, 2) }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-start gap-1.5">
                                                <svg class="w-3 h-3 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                <div>
                                                    <p class="text-[8px] text-gray-400 uppercase tracking-wider">Tasa de Interés</p>
                                                    <p class="text-[10px] font-bold text-gray-900 dark:text-white">{{ $loan->interest_rate }}%</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-[9px] text-gray-400 dark:text-gray-600 uppercase font-black mb-1.5 tracking-[0.15em]">Motivo del Préstamo</p>
                                        <div class="p-3 rounded-xl bg-orange-50/50 dark:bg-orange-500/5 border border-orange-100 dark:border-orange-500/10">
                                            <div class="flex items-start gap-2">
                                                <svg class="w-3.5 h-3.5 text-orange-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                                <p class="text-[10px] text-gray-700 dark:text-gray-300 italic leading-relaxed">"{{ $loan->reason }}"</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-6 border-y border-gray-100 dark:border-gray-800">
                                    <p class="text-sm text-gray-400 dark:text-gray-600 italic">Perfil incompleto</p>
                                </div>
                            @endif
                        </div>
                                </div>
                            </div>
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
                {{ $loans->appends(request()->query())->links() }}
            </div>
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

        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
        }

        .loan-details-wrapper {
            display: grid;
            grid-template-rows: 0fr;
            transition: grid-template-rows 0.35s ease-out;
        }

        .loan-details-wrapper.expanded {
            grid-template-rows: 1fr;
        }

        .loan-details-inner {
            overflow: hidden;
        }
    </style>
<!-- Modal Contra-Oferta -->
<div id="counterOfferModal" class="hidden fixed inset-0 bg-white/40 dark:bg-black/60 backdrop-blur-md z-[100] flex items-center justify-center p-2 sm:p-4">
    <div class="bg-white dark:bg-gray-950 w-full max-w-3xl max-h-[95vh] sm:max-h-[90vh] flex flex-col rounded-2xl sm:rounded-3xl md:rounded-[2.5rem] shadow-2xl relative animate-in fade-in zoom-in duration-200">
        <button onclick="closeCounterOfferModal()" class="absolute top-3 right-3 sm:top-5 sm:right-5 md:top-8 md:right-8 p-1.5 sm:p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-900 transition-colors z-20">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <form id="counterOfferForm" method="POST" class="flex flex-col min-h-0 overflow-hidden">
            @csrf
            <div class="p-3 sm:p-4 md:p-8 flex flex-col min-h-0 overflow-hidden">
                <div class="mb-4 sm:mb-6 pr-8 sm:pr-10 md:pr-12 flex flex-col md:flex-row items-start md:items-center md:justify-between gap-3 sm:gap-6">
                    <div class="flex flex-col md:flex-row md:items-center gap-3 sm:gap-4 md:gap-8 w-full md:w-auto">
                        <div>
                            <h3 class="text-base sm:text-xl md:text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Contra-oferta</h3>
                            <p class="text-[8px] sm:text-[10px] text-gray-500 mt-0.5 sm:mt-1 uppercase tracking-widest font-medium leading-relaxed">Ajuste de términos financieros</p>
                        </div>
                        <!-- Fecha Desembolso (Desktop) -->
                        <div class="hidden md:block w-40 space-y-1">
                            <label class="text-[8px] sm:text-[9px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-[0.2em] block">Desembolso</label>
                            <div class="relative" onclick="this.querySelector('input').showPicker()">
                                <input type="date" name="disbursement_date_desktop" id="modal_disbursement_date_desktop"
                                       min="{{ date('Y-m-d') }}"
                                       onchange="document.getElementById('modal_disbursement_date').value = this.value"
                                       class="w-full bg-transparent border-0 border-b border-gray-100 dark:border-gray-900 py-1 pr-8 text-xs sm:text-sm font-bold text-gray-900 dark:text-white focus:outline-none focus:border-purple-500 transition-colors cursor-pointer appearance-none calendar-picker-indicator-none"
                                       style="color-scheme: dark;">
                                <div class="absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="modal_display_amount" class="font-black text-left md:text-right space-y-0.5 text-[10px] sm:text-xs md:text-base w-full md:w-auto"></div>
                </div>

                <div id="modalInputsGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-2 sm:gap-3 md:gap-6 mb-3 sm:mb-4 md:mb-6">
                    <!-- Tipo -->
                    <div class="space-y-1 sm:space-y-2 col-span-2 sm:col-span-1 md:col-span-1">
                        <label class="text-[8px] sm:text-[10px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-[0.2em] block">Tipo</label>
                        <div class="flex gap-2 border-b border-gray-100 dark:border-gray-900">
                            <label class="flex-1 cursor-pointer group">
                                <input type="radio" name="type" value="cuotas" class="hidden peer" onchange="toggleModalTypeFields(); calculatePreview()">
                                <div class="py-1.5 sm:py-2 text-center text-[8px] sm:text-[10px] font-black tracking-widest text-gray-400 dark:text-gray-600 peer-checked:text-purple-600 transition-all border-b-2 border-transparent peer-checked:border-purple-600 hover:text-gray-600 dark:hover:text-gray-400">
                                    CUOTAS
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer group">
                                <input type="radio" name="type" value="unico" class="hidden peer" onchange="toggleModalTypeFields(); calculatePreview()">
                                <div class="py-1.5 sm:py-2 text-center text-[8px] sm:text-[10px] font-black tracking-widest text-gray-400 dark:text-gray-600 peer-checked:text-purple-600 transition-all border-b-2 border-transparent peer-checked:border-purple-600 hover:text-gray-600 dark:hover:text-gray-400">
                                    ÚNICO
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Moneda -->
                    <div class="space-y-1 sm:space-y-2 col-span-1">
                        <label class="text-[8px] sm:text-[10px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-[0.2em] block">Moneda</label>
                        <div class="flex gap-2 border-b border-gray-100 dark:border-gray-900">
                            <label class="flex-1 cursor-pointer group">
                                <input type="radio" name="currency" value="PEN" class="hidden peer" onchange="onCounterCurrencyChange()">
                                <div class="py-1.5 sm:py-2 text-center text-[8px] sm:text-[10px] font-black tracking-widest text-gray-400 dark:text-gray-600 peer-checked:text-purple-600 transition-all border-b-2 border-transparent peer-checked:border-purple-600 hover:text-gray-600 dark:hover:text-gray-400">
                                    SOLES
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer group">
                                <input type="radio" name="currency" value="USD" class="hidden peer" onchange="onCounterCurrencyChange()">
                                <div class="py-1.5 sm:py-2 text-center text-[8px] sm:text-[10px] font-black tracking-widest text-gray-400 dark:text-gray-600 peer-checked:text-purple-600 transition-all border-b-2 border-transparent peer-checked:border-purple-600 hover:text-gray-600 dark:hover:text-gray-400">
                                    DÓLARES
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Monto -->
                    <div class="space-y-1 sm:space-y-2 col-span-1 md:col-span-1">
                        <label class="text-[8px] sm:text-[10px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-[0.2em] block">Monto</label>
                        <div class="relative">
                            <input type="text" name="amount" id="modal_amount"
                                   class="w-full bg-transparent border-0 border-b-2 border-gray-100 dark:border-gray-900 py-1.5 sm:py-2 pr-6 text-base sm:text-lg font-black text-gray-900 dark:text-white focus:outline-none focus:border-purple-500 transition-colors text-right">
                            <span id="modal_currency_symbol" class="absolute right-0 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-xs sm:text-base"></span>
                        </div>
                    </div>

                    <!-- Fecha Desembolso (Mobile) -->
                    <div class="md:hidden space-y-1 sm:space-y-2 col-span-1">
                        <label class="text-[8px] sm:text-[10px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-[0.2em] block">Desembolso</label>
                        <div class="relative" onclick="this.querySelector('input').showPicker()">
                            <input type="date" name="disbursement_date" id="modal_disbursement_date"
                                   min="{{ date('Y-m-d') }}"
                                   onchange="document.getElementById('modal_disbursement_date_desktop').value = this.value"
                                   class="w-full bg-transparent border-0 border-b border-gray-100 dark:border-gray-900 py-1.5 sm:py-2 pr-10 text-base sm:text-lg font-black text-gray-900 dark:text-white focus:outline-none focus:border-purple-500 transition-colors cursor-pointer appearance-none calendar-picker-indicator-none"
                                   style="color-scheme: dark;">
                            <div class="absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Meses -->
                    <div id="modalMonthsWrapper" class="space-y-1 sm:space-y-2 col-span-1">
                        <label class="text-[8px] sm:text-[10px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-[0.2em] block">Plazo (M)</label>
                        <div class="relative">
                            <input type="number" name="months" id="modal_months" min="2" max="24" step="1"
                                   onkeypress="return (event.charCode >= 48 && event.charCode <= 57)"
                                   oninput="if(this.value > 24) this.value = 24; calculatePreview();"
                                   onblur="if(this.value && this.value < 2) this.value = 2; calculatePreview();"
                                   class="w-full bg-transparent border-0 border-b-2 border-gray-100 dark:border-gray-900 py-1.5 sm:py-2 pr-10 text-base sm:text-lg font-black text-gray-900 dark:text-white focus:outline-none focus:border-purple-500 transition-colors">
                            <span class="absolute right-0 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-xs sm:text-base">M</span>
                        </div>
                    </div>

                    <!-- Pago Único Días -->
                    <div id="modalDaysWrapper" class="space-y-1 sm:space-y-2 col-span-1 hidden">
                        <label class="text-[8px] sm:text-[10px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-[0.2em] block">Días</label>
                        <div class="flex gap-2 border-b border-gray-100 dark:border-gray-900">
                            <label class="flex-1 cursor-pointer group">
                                <input type="radio" name="payment_days" value="15" class="hidden peer" onchange="calculatePreview()">
                                <div class="py-1.5 sm:py-2 text-center text-[8px] sm:text-[10px] font-black tracking-widest text-gray-400 dark:text-gray-600 peer-checked:text-purple-600 transition-all border-b-2 border-transparent peer-checked:border-purple-600 hover:text-gray-600 dark:hover:text-gray-400">
                                    15 D
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer group">
                                <input type="radio" name="payment_days" value="30" class="hidden peer" onchange="calculatePreview()">
                                <div class="py-1.5 sm:py-2 text-center text-[8px] sm:text-[10px] font-black tracking-widest text-gray-400 dark:text-gray-600 peer-checked:text-purple-600 transition-all border-b-2 border-transparent peer-checked:border-purple-600 hover:text-gray-600 dark:hover:text-gray-400">
                                    30 D
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Tasa -->
                    <div class="space-y-1 sm:space-y-2 col-span-1">
                        <label class="text-[8px] sm:text-[10px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-[0.2em] block">Tasa (%)</label>
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
                                   class="w-full bg-transparent border-0 border-b-2 border-gray-100 dark:border-gray-900 py-1.5 sm:py-2 pr-4 text-base sm:text-lg font-black text-gray-900 dark:text-white focus:outline-none focus:border-purple-500 transition-colors text-right">
                            <span class="absolute right-0 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-xs sm:text-base">%</span>
                        </div>
                    </div>

                    <!-- Sistema y Frecuencia (Agrupados para escritorio) -->
                    <div class="col-span-2 sm:col-span-1 md:col-span-1 space-y-3 sm:space-y-4">
                        <!-- Sistema -->
                        <div id="modalSystemWrapper" class="space-y-1 sm:space-y-2">
                            <label class="text-[8px] sm:text-[10px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-[0.2em] block">Sistema</label>
                            <div class="flex gap-2 border-b border-gray-100 dark:border-gray-900">
                                <label class="flex-1 cursor-pointer group">
                                    <input type="radio" name="amortization_system" value="frances" class="hidden peer" onchange="toggleModalFrequency(); calculatePreview()">
                                    <div class="py-1.5 sm:py-2 text-center text-[8px] sm:text-[10px] font-black tracking-widest text-gray-400 dark:text-gray-600 peer-checked:text-purple-600 transition-all border-b-2 border-transparent peer-checked:border-purple-600 hover:text-gray-600 dark:hover:text-gray-400">
                                        FRANCÉS
                                    </div>
                                </label>
                                <label class="flex-1 cursor-pointer group">
                                    <input type="radio" name="amortization_system" value="americano" class="hidden peer" onchange="toggleModalFrequency(); calculatePreview()">
                                    <div class="py-1.5 sm:py-2 text-center text-[8px] sm:text-[10px] font-black tracking-widest text-gray-400 dark:text-gray-600 peer-checked:text-purple-600 transition-all border-b-2 border-transparent peer-checked:border-purple-600 hover:text-gray-600 dark:hover:text-gray-400">
                                        AMERICANO
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Frecuencia -->
                        <div id="modalFrequencyWrapper" class="space-y-1 sm:space-y-2">
                            <label class="text-[8px] sm:text-[10px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-[0.2em] block">Frecuencia</label>
                            <div class="flex gap-1 sm:gap-2 border-b border-gray-100 dark:border-gray-900">
                                @foreach(['diario' => 'DÍA', 'semanal' => 'SEM', 'quincenal' => 'QUIN', 'mensual' => 'MES'] as $val => $label)
                                    <label class="flex-1 cursor-pointer group">
                                        <input type="radio" name="payment_frequency" value="{{ $val }}" class="hidden peer" onchange="calculatePreview()">
                                        <div class="py-1.5 sm:py-2 text-center text-[7px] sm:text-[10px] font-black tracking-widest text-gray-400 dark:text-gray-600 peer-checked:text-purple-600 transition-all border-b-2 border-transparent peer-checked:border-purple-600 hover:text-gray-600 dark:hover:text-gray-400">
                                            {{ $label }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Previews -->
                <div class="flex-1 min-h-0 flex flex-col space-y-3 sm:space-y-4">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between px-1 flex-shrink-0 gap-2 sm:gap-3">
                        <h4 class="text-[8px] sm:text-[9px] md:text-[10px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-[0.2em]">Simulación de Resultados</h4>
                        <div class="grid grid-cols-2 sm:flex gap-x-3 sm:gap-x-4 gap-y-1 sm:gap-y-2">
                            <p class="text-[8px] sm:text-[9px] md:text-[10px] text-gray-500 font-bold uppercase">Total: <span id="previewTotal" class="text-gray-900 dark:text-white tabular-nums"></span></p>
                            <p class="text-[8px] sm:text-[9px] md:text-[10px] text-gray-500 font-bold uppercase">Cuota: <span id="previewMonthly" class="text-gray-900 dark:text-white tabular-nums"></span></p>
                            <p class="text-[8px] sm:text-[9px] md:text-[10px] text-green-600 font-black uppercase col-span-2 sm:col-span-1">Ganancia: <span id="previewGain" class="tabular-nums"></span></p>
                        </div>
                    </div>

                    <!-- Cuotas Preview -->
                    <div id="previewCuotasContainer" class="flex-1 min-h-0 flex flex-col">
                        <div class="flex-1 overflow-x-auto overflow-y-auto custom-scrollbar">
                            <table class="w-full text-[8px] sm:text-[10px] text-left min-w-[280px] sm:min-w-[320px] md:min-w-0">
                                <thead class="sticky top-0 bg-white dark:bg-gray-950 text-gray-400 dark:text-gray-600 shadow-sm">
                                    <tr>
                                        <th class="px-2 sm:px-4 py-2 sm:py-3 font-black uppercase tracking-tighter text-center">N°</th>
                                        <th class="px-2 sm:px-4 py-2 sm:py-3 font-black uppercase tracking-tighter text-center">Capital</th>
                                        <th class="px-2 sm:px-4 py-2 sm:py-3 font-black uppercase tracking-tighter text-center">Interés</th>
                                        <th class="px-2 sm:px-4 py-2 sm:py-3 font-black uppercase tracking-tighter text-center">Saldo</th>
                                    </tr>
                                </thead>
                                <tbody id="previewScheduleBody" class="divide-y divide-gray-50 dark:divide-gray-900 text-gray-600 dark:text-gray-400">
                                    <!-- JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Único Preview -->
                    <div id="previewUnicoContainer" class="hidden space-y-3 sm:space-y-4 py-3 sm:py-4">
                        <div class="grid grid-cols-2 gap-2 sm:gap-4">
                            <div class="p-2 sm:p-4 rounded-xl sm:rounded-2xl border border-gray-100 dark:border-gray-900 relative">
                                <p class="text-[8px] sm:text-[10px] text-gray-400 uppercase font-bold tracking-widest mb-1">Original (<span id="unicoClientRate"></span>%)</p>
                                <p id="unicoClientTotal" class="text-base sm:text-xl font-bold text-gray-400 line-through tabular-nums text-center"></p>
                            </div>
                            <div class="p-2 sm:p-4 rounded-xl sm:rounded-2xl bg-purple-50 dark:bg-purple-900/10 border border-purple-100 dark:border-purple-900/40 relative">
                                <p class="text-[8px] sm:text-[10px] text-purple-600 uppercase font-black tracking-widest mb-1">Nueva Oferta (<span id="unicoAdminRate"></span>%)</p>
                                <p id="unicoAdminTotal" class="text-base sm:text-xl font-black text-purple-600 tabular-nums text-center"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 sm:mt-4 md:mt-6 flex flex-col md:flex-row items-end justify-between gap-3 sm:gap-4 md:gap-6 flex-shrink-0">
                    <div class="w-full md:w-64 text-left space-y-1 sm:space-y-2">
                        <label class="text-[8px] sm:text-[10px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-[0.2em] block">Caja de Egreso</label>
                        <div id="counter_cashbox_container">
                            <select name="cashbox_id" id="counter_cashbox_id" required
                                    class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg py-1.5 sm:py-2 px-2 sm:px-3 text-xs sm:text-sm font-bold text-gray-900 dark:text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-colors appearance-none cursor-pointer">
                                <!-- JS populated -->
                            </select>
                        </div>
                        <a href="{{ route('superadmin.cashboxes.index') }}?from=loans" id="counter_create_cashbox_btn"
                           class="hidden w-full block text-center py-1.5 sm:py-2 text-[8px] sm:text-[10px] font-black uppercase tracking-widest text-amber-600 border-b-2 border-amber-600 hover:opacity-70 transition-all">
                            Crear caja de <span class="currency-label"></span>
                        </a>
                        <a href="{{ route('superadmin.cashboxes.index') }}?from=loans" id="counter_add_cashbox_btn"
                           class="w-full block text-center py-1 text-[8px] sm:text-[9px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400 border-b border-indigo-600 dark:border-indigo-400 hover:opacity-60 transition-opacity mt-1">
                            + Crear nueva caja
                        </a>
                        <p id="counter_cashbox_error" class="hidden text-[8px] sm:text-[10px] font-bold text-red-500 uppercase tracking-widest mt-1">
                            Saldo insuficiente en caja
                        </p>
                    </div>
                    <button type="button" onclick="openCounterOfferConfirmModal()" class="w-full md:w-auto text-xs sm:text-sm font-bold text-purple-600 dark:text-purple-400 border-b-2 border-purple-600 dark:border-purple-400 pb-1 hover:opacity-70 transition-all uppercase tracking-widest bg-transparent">
                        Confirmar y Enviar Contra-oferta
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

    <script>
        let currentLoan = null;
        const allCashboxes = @json($cashboxes);

        let debounceTimer;
        function debounceSubmit() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 500);
        }

        function openCounterOfferModal(loan) {
            currentLoan = loan;
            const modal = document.getElementById('counterOfferModal');
            const form = document.getElementById('counterOfferForm');

            form.action = `/gestion-interna/prestamos/${loan.id}/counter-offer`;

            const currencyRadio = document.querySelector(`input[name="currency"][value="${loan.currency}"]`);
            if (currencyRadio) currencyRadio.checked = true;

            refreshCounterCashboxes();

            // Set initial values
            const systemWrapper = document.getElementById('modalSystemWrapper');
        const freqWrapper = document.getElementById('modalFrequencyWrapper');
        const monthsWrapper = document.getElementById('modalMonthsWrapper');
        const inputsGrid = document.getElementById('modalInputsGrid');

        // Format initial amount
        const initialAmount = parseFloat(loan.amount).toFixed(2).replace('.', '');
        formatAmountInput(initialAmount);

        document.getElementById('modal_currency_symbol').textContent = loan.currency === 'USD' ? '$' : 'S/';

        // Set type
        const typeToSet = loan.type || 'cuotas';
        const typeRadio = document.querySelector(`input[name="type"][value="${typeToSet}"]`);
        if (typeRadio) typeRadio.checked = true;

        document.getElementById('modal_months').value = loan.months || 2;

        const systemToSet = loan.system || 'frances';
        const radio = document.querySelector(`input[name="amortization_system"][value="${systemToSet}"]`);
        if (radio) radio.checked = true;

        const freqToSet = loan.payment_frequency || 'mensual';
        const freqRadio = document.querySelector(`input[name="payment_frequency"][value="${freqToSet}"]`);
        if (freqRadio) freqRadio.checked = true;

        const daysToSet = loan.payment_days || 15;
        const daysRadio = document.querySelector(`input[name="payment_days"][value="${daysToSet}"]`);
        if (daysRadio) daysRadio.checked = true;

        toggleModalTypeFields();

        document.getElementById('modal_interest_rate').value = parseFloat(loan.interest_rate).toFixed(2);
        document.getElementById('modal_disbursement_date').value = loan.disbursement_date || '';
        document.getElementById('modal_disbursement_date_desktop').value = loan.disbursement_date || '';

        // Show amount
        const amountFormatted = parseFloat(loan.amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        const currencySymbol = loan.currency === 'USD' ? '$' : 'S/';
        let subtitleText = `<span class="block text-[9px] md:text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest">PEDIDO ORIGINAL: ${currencySymbol} ${amountFormatted}</span>`;
        if (loan.type === 'cuotas' && loan.monthly_payment) {
            const monthlyFormatted = parseFloat(loan.monthly_payment).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            subtitleText += `<span class="block text-[9px] md:text-[10px] text-amber-500 dark:text-amber-400 uppercase tracking-widest">CUOTA ORIGINAL: ${currencySymbol} ${monthlyFormatted}</span>`;
        }
        document.getElementById('modal_display_amount').innerHTML = subtitleText;

        calculatePreview();

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function toggleModalTypeFields() {
        const type = document.querySelector('input[name="type"]:checked')?.value;
        const systemWrapper = document.getElementById('modalSystemWrapper');
        const freqWrapper = document.getElementById('modalFrequencyWrapper');
        const monthsWrapper = document.getElementById('modalMonthsWrapper');
        const daysWrapper = document.getElementById('modalDaysWrapper');

        if (type === 'unico') {
            systemWrapper.classList.add('hidden');
            freqWrapper.classList.add('hidden');
            monthsWrapper.classList.add('hidden');
            daysWrapper.classList.remove('hidden');
            // Force monthly for single payment
            const monthlyRadio = document.querySelector('input[name="payment_frequency"][value="mensual"]');
            if (monthlyRadio) monthlyRadio.checked = true;
        } else {
            systemWrapper.classList.remove('hidden');
            monthsWrapper.classList.remove('hidden');
            daysWrapper.classList.add('hidden');
            toggleModalFrequency();
        }
    }

    function toggleModalFrequency() {
        const type = document.querySelector('input[name="type"]:checked')?.value;
        const system = document.querySelector('input[name="amortization_system"]:checked')?.value;
        const freqWrapper = document.getElementById('modalFrequencyWrapper');
        if (freqWrapper) {
            if (type === 'cuotas' && system === 'frances') {
                freqWrapper.classList.remove('hidden');
            } else {
                freqWrapper.classList.add('hidden');
                // Ensure it stays monthly if hidden
                const monthlyRadio = document.querySelector('input[name="payment_frequency"][value="mensual"]');
                if (monthlyRadio) monthlyRadio.checked = true;
            }
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
        validateCounterCashbox();
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
        let months = parseInt(monthsInput.value) || 2;

        if (months < 2) months = 2;
        if (months > 24) months = 24;

        const amount = parseFloat(document.getElementById('modal_amount').value.replace(/,/g, '')) || 0;
        const rate = (parseFloat(document.getElementById('modal_interest_rate').value) / 100 || 0);
        const type = document.querySelector('input[name="type"]:checked')?.value || 'cuotas';
        const systemRadio = document.querySelector('input[name="amortization_system"]:checked');
        const system = systemRadio ? systemRadio.value : 'frances';
        const freqRadio = document.querySelector('input[name="payment_frequency"]:checked');
        const freq = freqRadio ? freqRadio.value : 'mensual';
        const currency = getCounterCurrency() === 'USD' ? '$' : 'S/';

        const body = document.getElementById('previewScheduleBody');
        const totalEl = document.getElementById('previewTotal');
        const monthlyEl = document.getElementById('previewMonthly');
        const gainEl = document.getElementById('previewGain');
        const subtitleEl = document.getElementById('modal_display_amount');

        body.innerHTML = '';

        const amountObj = { minimumFractionDigits: 2, maximumFractionDigits: 2 };

        if (type === 'unico') {
            document.getElementById('previewCuotasContainer').classList.add('hidden');
            document.getElementById('previewUnicoContainer').classList.remove('hidden');

            const rateClient = parseFloat(currentLoan.interest_rate) || 0;
            const clientTotal = parseFloat(currentLoan.amount) + (parseFloat(currentLoan.amount) * (rateClient / 100));
            
            // Para préstamo único, la tasa suele ser flat (no anualizada por defecto en este contexto)
            // Si el admin ingresa una nueva tasa, la tratamos como flat para tipo único
            const flatRate = (parseFloat(document.getElementById('modal_interest_rate').value) / 100 || 0);
            const adminTotal = amount + (amount * flatRate);

            document.getElementById('unicoClientRate').textContent = rateClient.toFixed(2);
            document.getElementById('unicoClientTotal').textContent = `${currency} ${clientTotal.toLocaleString('en-US', amountObj)}`;

            document.getElementById('unicoAdminRate').textContent = (flatRate * 100).toFixed(2);
            document.getElementById('unicoAdminTotal').textContent = `${currency} ${adminTotal.toLocaleString('en-US', amountObj)}`;

            gainEl.textContent = `${currency} ${(adminTotal - amount).toLocaleString('en-US', amountObj)}`;
            return;
        } else {
            document.getElementById('previewCuotasContainer').classList.remove('hidden');
            document.getElementById('previewUnicoContainer').classList.add('hidden');
        }

        // Loan with installments
        let n, periodicRate, installmentsPerMonth;

        if (system === 'frances') {
            installmentsPerMonth = { diario: 30, semanal: 4, quincenal: 2, mensual: 1 }[freq] || 1;
            n = months * installmentsPerMonth;
            periodicRate = rate / installmentsPerMonth;
        } else {
            installmentsPerMonth = 1;
            n = months;
            periodicRate = rate;
        }

        let schedule = [];
        let totalInterest = 0;
        let periodicPayment = 0;
        let pmtText = '';

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
                if (i <= 10 || i > n - 5) {
                    schedule.push({ n: i, principal, interest, balance: Math.max(0, balance) });
                } else if (i === 11) {
                    schedule.push({ n: '...', principal: 0, interest: 0, balance: 0, spacer: true });
                }
            }
            pmtText = `${currency}${periodicPayment.toFixed(2)} / ${freq}`;
        } else {
            periodicPayment = amount * periodicRate;
            let balance = amount;
            for (let i = 1; i <= n; i++) {
                const interest = periodicPayment;
                const principal = (i === n) ? amount : 0;
                balance -= principal;
                totalInterest += interest;
                if (i <= 10 || i > n - 5) {
                    schedule.push({ n: i, principal, interest, balance: Math.max(0, balance) });
                } else if (i === 11) {
                    schedule.push({ n: '...', principal: 0, interest: 0, balance: 0, spacer: true });
                }
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
                    <td class="py-3 tabular-nums text-center">${currency}${row.principal.toLocaleString('en-US', amountObj)}</td>
                    <td class="py-3 tabular-nums text-center">${currency}${row.interest.toLocaleString('en-US', amountObj)}</td>
                    <td class="py-3 tabular-nums text-center font-black text-gray-900 dark:text-white">${currency}${row.balance.toLocaleString('en-US', amountObj)}</td>
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
        // Find loan in the list to get currency
        const loanData = @json($loans->items()).find(l => l.id == loanId);
        if (!loanData) return;

        document.getElementById('approve_modal_amount').textContent = amount;
        document.getElementById('approve_modal_user').textContent = user;
        document.getElementById('approveConfirmForm').action = `/gestion-interna/prestamos/${loanId}/approve`;

        // Show loan-specific bank account
        const bankAccountsContainer = document.getElementById('approve_bank_accounts');
        const noBankAccount = document.getElementById('approve_no_bank_account');

        if (loanData.account_number && loanData.cci) {
            bankAccountsContainer.classList.remove('hidden');
            noBankAccount.classList.add('hidden');
            bankAccountsContainer.innerHTML =
                `<div class="text-[9px] font-bold text-gray-600 dark:text-gray-300">
                    <span class="text-gray-400 dark:text-gray-500">${loanData.bank_name || 'Banco'} (${loanData.currency}):</span>
                    <span class="text-gray-700 dark:text-gray-200">CTA: ${loanData.account_number}</span>
                    <span class="ml-2 text-gray-700 dark:text-gray-200">CCI: ${loanData.cci}</span>
                </div>`;
        } else {
            bankAccountsContainer.innerHTML = '';
            bankAccountsContainer.classList.add('hidden');
            noBankAccount.classList.remove('hidden');
        }

        // Reset voucher
        document.getElementById('approve_voucher').value = '';
        document.getElementById('approve_voucher_error').classList.add('hidden');
        document.getElementById('approve_voucher_preview').classList.add('hidden');
        document.getElementById('approve_voucher_preview_img').src = '';
        document.getElementById('approve_operation_number').value = '';
        document.getElementById('approve_operation_error').classList.add('hidden');

        // Populate cashboxes
        const cashboxSelect = document.getElementById('approve_cashbox_id');
        const cashboxContainer = document.getElementById('approve_cashbox_container');
        const createBtn = document.getElementById('approve_create_cashbox_btn');
        const errorMsg = document.getElementById('approve_cashbox_error');
        errorMsg.classList.add('hidden');
        
        const filteredCashboxes = allCashboxes.filter(cb => cb.currency === loanData.currency);
        
        if (filteredCashboxes.length === 0) {
            cashboxContainer.classList.add('hidden');
            createBtn.classList.remove('hidden');
            createBtn.querySelector('.currency-label').textContent = loanData.currency;
            cashboxSelect.innerHTML = '';
            cashboxSelect.required = false;
            const addBtn = document.getElementById('approve_add_cashbox_btn');
            if (addBtn) addBtn.classList.add('hidden');
        } else {
            cashboxContainer.classList.remove('hidden');
            createBtn.classList.add('hidden');
            cashboxSelect.required = true;
            cashboxSelect.innerHTML = filteredCashboxes.map(cb => 
                `<option value="${cb.id}" data-balance="${cb.balance}">${cb.name} (${cb.currency} ${parseFloat(cb.balance).toLocaleString('en-US', {minimumFractionDigits:2})})</option>`
            ).join('');
            const addBtn = document.getElementById('approve_add_cashbox_btn');
            if (addBtn) addBtn.classList.remove('hidden');
        }

        cashboxSelect.onchange = () => validateApproveCashbox();
        validateApproveCashbox();

        document.getElementById('approveConfirmModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function validateApproveCashbox() {
        const select = document.getElementById('approve_cashbox_id');
        const btn = document.getElementById('approve_submit_btn');
        const errorMsg = document.getElementById('approve_cashbox_error');
        const createBtn = document.getElementById('approve_create_cashbox_btn');
        
        if (createBtn && !createBtn.classList.contains('hidden')) {
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
            btn.classList.remove('bg-green-600', 'hover:bg-green-700');
            return;
        }

        if (!select.value) {
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            return;
        }

        const selectedOption = select.options[select.selectedIndex];
        const balance = parseFloat(selectedOption.dataset.balance);
        const amountStr = document.getElementById('approve_modal_amount').textContent.replace(/[^0-9.]/g, '');
        const amount = parseFloat(amountStr);

        if (amount > balance) {
            errorMsg.classList.remove('hidden');
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            btn.classList.remove('bg-green-600', 'hover:bg-green-700');
            btn.classList.add('bg-gray-400');
        } else {
            errorMsg.classList.add('hidden');
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
            btn.classList.add('bg-green-600', 'hover:bg-green-700');
        }
    }

    function getCounterCurrency() {
        const checked = document.querySelector('input[name="currency"]:checked');
        return checked ? checked.value : (currentLoan ? currentLoan.currency : 'PEN');
    }

    function refreshCounterCashboxes() {
        const currency = getCounterCurrency();
        const cashboxSelect = document.getElementById('counter_cashbox_id');
        const cashboxContainer = document.getElementById('counter_cashbox_container');
        const createBtn = document.getElementById('counter_create_cashbox_btn');
        const errorMsg = document.getElementById('counter_cashbox_error');
        errorMsg.classList.add('hidden');

        const filteredCashboxes = allCashboxes.filter(cb => cb.currency === currency);

        if (filteredCashboxes.length === 0) {
            cashboxContainer.classList.add('hidden');
            createBtn.classList.remove('hidden');
            createBtn.querySelector('.currency-label').textContent = currency;
            cashboxSelect.innerHTML = '';
            cashboxSelect.required = false;
            const addBtn = document.getElementById('counter_add_cashbox_btn');
            if (addBtn) addBtn.classList.add('hidden');
        } else {
            cashboxContainer.classList.remove('hidden');
            createBtn.classList.add('hidden');
            cashboxSelect.required = true;
            cashboxSelect.innerHTML = filteredCashboxes.map(cb =>
                `<option value="${cb.id}" data-balance="${cb.balance}">${cb.name} (${cb.currency} ${parseFloat(cb.balance).toLocaleString('en-US', {minimumFractionDigits:2})})</option>`
            ).join('');
            const addBtn = document.getElementById('counter_add_cashbox_btn');
            if (addBtn) addBtn.classList.remove('hidden');
        }

        cashboxSelect.onchange = () => validateCounterCashbox();
        validateCounterCashbox();
    }

    function onCounterCurrencyChange() {
        const currency = getCounterCurrency();
        document.getElementById('modal_currency_symbol').textContent = currency === 'USD' ? '$' : 'S/';
        refreshCounterCashboxes();
        calculatePreview();
    }

    function validateCounterCashbox() {
        const select = document.getElementById('counter_cashbox_id');
        const errorMsg = document.getElementById('counter_cashbox_error');
        const btn = document.querySelector('button[onclick="openCounterOfferConfirmModal()"]');
        const createBtn = document.getElementById('counter_create_cashbox_btn');

        if (createBtn && !createBtn.classList.contains('hidden')) {
            if (btn) {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
                btn.classList.remove('text-purple-600', 'dark:text-purple-400', 'border-purple-600', 'dark:border-purple-400');
                btn.classList.add('text-gray-400', 'border-gray-400');
            }
            return;
        }
        
        if (!select.value) {
            if (btn) btn.disabled = true;
            return;
        }

        const selectedOption = select.options[select.selectedIndex];
        const balance = parseFloat(selectedOption.dataset.balance);
        const amount = parseFloat(document.getElementById('modal_amount').value.replace(/,/g, '')) || 0;

        if (amount > balance) {
            errorMsg.classList.remove('hidden');
            if (btn) {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
                btn.classList.remove('text-purple-600', 'dark:text-purple-400', 'border-purple-600', 'dark:border-purple-400');
                btn.classList.add('text-gray-400', 'border-gray-400');
            }
        } else {
            errorMsg.classList.add('hidden');
            if (btn) {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed', 'text-gray-400', 'border-gray-400');
                btn.classList.add('text-purple-600', 'dark:text-purple-400', 'border-purple-600', 'dark:border-purple-400');
            }
        }
    }

    document.getElementById('approveConfirmForm').addEventListener('submit', function(e) {
        const select = document.getElementById('approve_cashbox_id');
        if (!select.value) {
            e.preventDefault();
            alert('Debe seleccionar una caja');
            return;
        }

        const selectedOption = select.options[select.selectedIndex];
        const balance = parseFloat(selectedOption.dataset.balance);
        
        // Get amount from text (hacky but works if we don't have it elsewhere)
        // Better: store it in a variable when opening modal
        const amountStr = document.getElementById('approve_modal_amount').textContent.replace(/[^0-9.]/g, '');
        const amount = parseFloat(amountStr);

        if (amount > balance) {
            e.preventDefault();
            document.getElementById('approve_cashbox_error').classList.remove('hidden');
            return;
        }

        const voucherInput = document.getElementById('approve_voucher');
        if (!voucherInput.files || voucherInput.files.length === 0) {
            e.preventDefault();
            document.getElementById('approve_voucher_error').classList.remove('hidden');
            return;
        }
        document.getElementById('approve_voucher_error').classList.add('hidden');

        const operationInput = document.getElementById('approve_operation_number');
        if (!operationInput.value.trim()) {
            e.preventDefault();
            document.getElementById('approve_operation_error').classList.remove('hidden');
            return;
        }
        document.getElementById('approve_operation_error').classList.add('hidden');

        document.getElementById('approve_submit_btn').disabled = true;
        document.getElementById('approve_submit_btn').opacity = '0.5';
    });

    function closeApproveModal() {
        document.getElementById('approveConfirmModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function previewApproveVoucher(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('approve_voucher_preview');
        const previewImg = document.getElementById('approve_voucher_preview_img');
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            preview.classList.add('hidden');
            previewImg.src = '';
        }
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
        const select = document.getElementById('counter_cashbox_id');
        if (!select.value) {
            alert('Debe seleccionar una caja');
            return;
        }

        const selectedOption = select.options[select.selectedIndex];
        const balance = parseFloat(selectedOption.dataset.balance);
        const amount = parseFloat(document.getElementById('modal_amount').value.replace(/,/g, ''));

        if (amount > balance) {
            document.getElementById('counter_cashbox_error').classList.remove('hidden');
            return;
        }

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
<div id="counterOfferConfirmModal" class="hidden fixed inset-0 bg-white/40 dark:bg-black/60 backdrop-blur-md z-[120] flex items-center justify-center p-4">
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
<div id="approveConfirmModal" class="hidden fixed inset-0 bg-white/40 dark:bg-black/60 backdrop-blur-md z-[110] flex items-end sm:items-center justify-center">
    <div class="bg-white dark:bg-gray-950 w-full sm:max-w-md rounded-t-2xl sm:rounded-2xl shadow-2xl relative max-h-[90vh] overflow-y-auto p-5 sm:p-6 text-center">
        <div class="w-16 h-16 bg-green-50 dark:bg-green-900/20 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><path d="M20,2H6A2,2,0,0,0,4,4V17H4a2,2,0,0,0-2,2H2a3,3,0,0,0,3,3H19.5A2.5,2.5,0,0,0,22,19.5V4A2,2,0,0,0,20,2Z" opacity="0.3"/><path d="M19,22v0H5a3,3,0,0,1-3-3H2a2,2,0,0,1,2-2H15a2,2,0,0,1,2,2v.5A2.52,2.52,0,0,0,19,22ZM12.71,11.71l4-4a1,1,0,1,0-1.42-1.42L12,9.59l-1.29-1.3A1,1,0,0,0,9.29,9.71l2,2a1,1,0,0,0,1.42,0Z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Aprobar Préstamo</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
            ¿Confirmas la aprobación del préstamo por <span id="approve_modal_amount" class="font-bold text-gray-900 dark:text-white"></span> para <span id="approve_modal_user" class="font-bold text-gray-900 dark:text-white"></span>?
        </p>

        <div id="approve_bank_info" class="mb-6 bg-gray-50 dark:bg-gray-900 rounded-xl p-4 text-left">
            <p class="text-[8px] font-black uppercase tracking-widest text-gray-400 mb-2">Cuenta del Cliente para Depósito</p>
            <div id="approve_bank_accounts" class="space-y-1">
                <!-- JS populated -->
            </div>
            <p id="approve_no_bank_account" class="hidden text-[9px] font-bold text-red-500">El cliente no tiene cuentas bancarias registradas</p>
        </div>

        <form id="approveConfirmForm" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div class="text-left space-y-2">
                <label class="text-[10px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-[0.2em] block">Caja de Egreso</label>
                <div id="approve_cashbox_container">
                    <select name="cashbox_id" id="approve_cashbox_id" required
                            class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg py-2 px-3 text-sm font-bold text-gray-900 dark:text-white focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 transition-colors appearance-none cursor-pointer">
                        <!-- JS populated -->
                    </select>
                </div>
                <a href="{{ route('superadmin.cashboxes.index') }}?from=loans" id="approve_create_cashbox_btn" 
                   class="hidden w-full block text-center py-2 text-[10px] font-black uppercase tracking-widest text-amber-600 border-b-2 border-amber-600 hover:opacity-70 transition-all">
                    Crear caja de <span class="currency-label"></span>
                </a>
                <a href="{{ route('superadmin.cashboxes.index') }}?from=loans" id="approve_add_cashbox_btn"
                   class="w-full block text-center py-1 text-[9px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400 border-b border-indigo-600 dark:border-indigo-400 hover:opacity-60 transition-opacity mt-1">
                    + Crear nueva caja
                </a>
                <p id="approve_cashbox_error" class="hidden text-[10px] font-bold text-red-500 uppercase tracking-widest mt-1 text-center">
                    Saldo insuficiente en caja
                </p>
            </div>

            <div class="text-left space-y-2">
                <label class="text-[10px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-[0.2em] block">Comprobante de Depósito (Voucher)</label>
                <input type="file" name="disbursement_voucher" id="approve_voucher" accept="image/*,.pdf"
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs font-black file:bg-green-50 file:text-green-700 hover:file:bg-green-100 file:uppercase file:tracking-widest cursor-pointer"
                    onchange="previewApproveVoucher(event)">
                <div id="approve_voucher_preview" class="hidden mt-2 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-800">
                    <img id="approve_voucher_preview_img" src="" alt="Vista previa" class="w-full h-auto max-h-40 object-contain bg-gray-50 dark:bg-gray-900">
                </div>
                <p class="text-[8px] text-gray-400">Se convertirá automáticamente a formato WebP</p>
                <p id="approve_voucher_error" class="hidden text-[10px] font-bold text-red-500 uppercase tracking-widest mt-1 text-center">
                    Debes subir el comprobante de depósito
                </p>
            </div>

            <div class="text-left space-y-2">
                <label class="text-[10px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-[0.2em] block">Número de Operación</label>
                <input type="text" name="disbursement_operation_number" id="approve_operation_number" required placeholder="Ej: 123456789"
                    class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg py-2 px-3 text-sm font-bold text-gray-900 dark:text-white focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 transition-colors tabular-nums"
                    maxlength="30" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                <p id="approve_operation_error" class="hidden text-[10px] font-bold text-red-500 uppercase tracking-widest mt-1 text-center">
                    Debes ingresar el número de operación
                </p>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeApproveModal()" class="flex-1 py-3 rounded-xl bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400 text-xs font-black uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-gray-800 transition-colors">
                    Cancelar
                </button>
                <button type="submit" id="approve_submit_btn" class="flex-1 py-3 rounded-xl bg-green-600 text-white text-xs font-black uppercase tracking-widest hover:bg-green-700 transition-colors shadow-lg shadow-green-500/20">
                    Aprobar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Confirmar Rechazo -->
<div id="rejectConfirmModal" class="hidden fixed inset-0 bg-white/40 dark:bg-black/60 backdrop-blur-md z-[110] flex items-center justify-center p-4">
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

    /* Ocultar icono nativo de calendario */
    .calendar-picker-indicator-none::-webkit-calendar-picker-indicator {
        display: none;
        -webkit-appearance: none;
    }
</style>

<div id="scheduleModal" class="hidden fixed inset-0 bg-white/40 dark:bg-black/60 backdrop-blur-md z-[130] flex items-center justify-center p-2 md:p-4">
    <div class="bg-white dark:bg-gray-950 w-full max-w-2xl max-h-[95vh] md:max-h-[90vh] flex flex-col rounded-2xl md:rounded-[2.5rem] shadow-2xl relative animate-in fade-in zoom-in duration-200">
        <button onclick="closeScheduleModal()" class="absolute top-3 right-3 md:top-7 md:right-7 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-900 transition-colors z-20">
            <svg class="w-4 h-4 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div class="p-4 md:p-8 flex flex-col min-h-0 overflow-hidden">
            <h3 class="text-base md:text-lg font-bold text-gray-900 dark:text-white tracking-tight pr-8 md:pr-10">Cronograma de Pagos</h3>
            <div id="scheduleModalMeta" class="flex flex-wrap items-center gap-x-2 gap-y-1 mt-1 md:mt-2"></div>
            <div id="scheduleModalSummary" class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-3 md:mt-4 mb-4 md:mb-5"></div>
            <div class="flex-1 min-h-0 overflow-x-auto overflow-y-auto custom-scrollbar">
                <table class="w-full text-[10px] md:text-[11px] text-left min-w-[400px] md:min-w-0">
                    <thead class="sticky top-0 bg-white dark:bg-gray-950 border-b border-gray-100 dark:border-gray-800">
                        <tr class="text-gray-400 dark:text-gray-600">
                            <th class="px-2 md:px-4 py-2 md:py-3 font-black uppercase tracking-tighter text-center">N°</th>
                            <th class="px-2 md:px-4 py-2 md:py-3 font-black uppercase tracking-tighter text-center">Capital</th>
                            <th class="px-2 md:px-4 py-2 md:py-3 font-black uppercase tracking-tighter text-center">Interés</th>
                            <th class="px-2 md:px-4 py-2 md:py-3 font-black uppercase tracking-tighter text-center">Cuota</th>
                            <th class="px-2 md:px-4 py-2 md:py-3 font-black uppercase tracking-tighter text-center">Saldo</th>
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
        const rate = (parseFloat(loan.interest_rate) / 100);
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
            <span class="text-[10px] px-2 py-0.5 rounded-md bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400 font-bold uppercase border border-gray-200 dark:border-gray-800">${system} · ${months} cuotas</span>
            <span class="text-[10px] px-2 py-0.5 rounded-md bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400 font-bold uppercase border border-gray-200 dark:border-gray-800">${freqLabel}</span>
            <span class="text-[10px] px-2 py-0.5 rounded-md bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 font-bold uppercase border border-purple-200 dark:border-purple-800">${parseFloat(loan.interest_rate).toFixed(2)}%</span>
        `;

        body.innerHTML = '';
        let fullSchedule = [];
        let totalInterest = 0;
        let periodicPayment = 0;

        const fmt = (n) => parseFloat(n).toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2});

        let nTotal, periodicRate;
        if (system === 'frances') {
            const installmentsPerMonth = { diario: 30, semanal: 4, quincenal: 2, mensual: 1 }[freq] || 1;
            nTotal = months * installmentsPerMonth;
            periodicRate = rate / installmentsPerMonth;
        } else {
            nTotal = months;
            periodicRate = rate;
        }

        if (system === 'frances') {
            periodicPayment = (amount > 0 && rate > 0)
                ? (amount * periodicRate) / (1 - Math.pow(1 + periodicRate, -nTotal))
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
            periodicPayment = amount * periodicRate;
            let balance = amount;
            for (let i = 1; i <= nTotal; i++) {
                const interest = periodicPayment;
                const principal = (i === nTotal) ? amount : 0;
                const cuota = (i === nTotal) ? amount + interest : interest;
                balance = Math.max(0, balance - principal);
                totalInterest += interest;
                fullSchedule.push({ n: i, principal, interest, cuota, balance });
            }
        }

        const totalAmountStored = parseFloat(loan.total_amount) || (amount + totalInterest);
        const totalGainStored = totalAmountStored - amount;
        const monthlyPaymentStored = parseFloat(loan.monthly_payment) || periodicPayment;

        // Summary cards
        summary.innerHTML = `
            <span class="text-[10px] md:text-[11px] text-gray-500 dark:text-gray-400 font-bold uppercase">Total: <span class="text-gray-900 dark:text-white tabular-nums">${currency} ${fmt(totalAmountStored)}</span></span>
            <span class="text-[10px] md:text-[11px] text-gray-500 dark:text-gray-400 font-bold uppercase">Cuota/${freqLabel}: <span class="text-gray-900 dark:text-white tabular-nums">${currency} ${fmt(monthlyPaymentStored)}</span></span>
            <span class="text-[10px] md:text-[11px] text-green-600 dark:text-green-500 font-black uppercase">Ganancia: <span class="tabular-nums">${currency} ${fmt(totalGainStored)}</span></span>
        `;

        // Truncate: show first 10, ellipsis, last 5 (only if total > 15)
        const SHOW_FIRST = 10;
        const SHOW_LAST = 5;
        let displayRows = fullSchedule;

        if (fullSchedule.length > SHOW_FIRST + SHOW_LAST) {
            displayRows = [
                ...fullSchedule.slice(0, SHOW_FIRST),
                null, // spacer
                ...fullSchedule.slice(-SHOW_LAST)
            ];
        }

        body.innerHTML = ''; // Clear body before adding rows
        displayRows.forEach(row => {
            if (row === null) {
                body.innerHTML += `
                    <tr class="bg-gray-50 dark:bg-gray-900/50">
                        <td colspan="5" class="px-2 md:px-4 py-2 text-center text-[9px] md:text-[10px] text-gray-400 dark:text-gray-600 font-bold tracking-widest">
                            · · · ${fullSchedule.length - SHOW_FIRST - SHOW_LAST} cuotas intermedias · · ·
                        </td>
                    </tr>
                `;
                return;
            }
            body.innerHTML += `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/50 transition-colors">
                    <td class="px-2 md:px-4 py-2 md:py-2.5 font-bold text-center text-gray-900 dark:text-white">${row.n}</td>
                    <td class="px-2 md:px-4 py-2 md:py-2.5 tabular-nums text-center text-gray-600 dark:text-gray-400">${currency}${fmt(row.principal)}</td>
                    <td class="px-2 md:px-4 py-2 md:py-2.5 tabular-nums text-center text-amber-600 dark:text-amber-500">${currency}${fmt(row.interest)}</td>
                    <td class="px-2 md:px-4 py-2 md:py-2.5 tabular-nums text-center font-bold text-gray-900 dark:text-white">${currency}${fmt(row.cuota)}</td>
                    <td class="px-2 md:px-4 py-2 md:py-2.5 tabular-nums text-center text-gray-600 dark:text-gray-400">${currency}${fmt(row.balance)}</td>
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

    function toggleLoanRow(loanId) {
        const wrapper = document.getElementById('loan-details-' + loanId);
        const chevron = document.getElementById('loan-chevron-' + loanId);
        if (!wrapper || !chevron) return;
        wrapper.classList.toggle('expanded');
        chevron.style.transform = wrapper.classList.contains('expanded') ? 'rotate(180deg)' : 'rotate(0deg)';
    }

    window.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const loanId = urlParams.get('loan_id');
        if (loanId) {
            const el = document.getElementById('loan-' + loanId);
            if (el) {
                const wrapper = document.getElementById('loan-details-' + loanId);
                const chevron = document.getElementById('loan-chevron-' + loanId);
                if (wrapper) {
                    wrapper.classList.add('expanded');
                    if (chevron) chevron.style.transform = 'rotate(180deg)';
                }
                el.classList.add('bg-yellow-50', 'dark:bg-yellow-900/10', 'ring-2', 'ring-yellow-400', 'rounded-2xl');
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(() => {
                    el.classList.remove('bg-yellow-50', 'dark:bg-yellow-900/10', 'ring-2', 'ring-yellow-400');
                }, 3000);
            }
        }
    });

    (function() {
        const searchInput = document.getElementById('loans-search');
        const clearBtn = document.getElementById('loans-search-clear');
        const spinner = document.getElementById('loans-search-spinner');
        const badge = document.getElementById('loans-search-badge');
        const searchTerm = document.getElementById('loans-search-term');
        const searchCount = document.getElementById('loans-search-count');
        const loansList = document.getElementById('loans-list');
        if (!searchInput || !loansList) return;

        const currentFilter = '{{ $filter }}';
        const baseUrl = '{{ route("superadmin.loans.index") }}';
        let debounceTimer = null;
        let currentRequest = null;

        function performSearch(term) {
            if (currentRequest) currentRequest.abort();

            const params = new URLSearchParams();
            if (term) params.set('search', term);
            if (currentFilter !== 'pending') params.set('filter', currentFilter);
            const currency = '{{ request("currency") }}';
            const fromDate = '{{ request("from_date") }}';
            const toDate = '{{ request("to_date") }}';
            if (currency) params.set('currency', currency);
            if (fromDate) params.set('from_date', fromDate);
            if (toDate) params.set('to_date', toDate);

            const url = baseUrl + (params.toString() ? '?' + params.toString() : '');

            if (!term) {
                clearBtn.classList.add('hidden');
                badge.classList.add('hidden');
            } else {
                clearBtn.classList.add('hidden');
                spinner.classList.remove('hidden');
                if (searchTerm) searchTerm.textContent = term;
                badge.classList.remove('hidden');
                badge.style.display = '';
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
                const newList = doc.getElementById('loans-list');
                if (newList) loansList.innerHTML = newList.innerHTML;
                const newCount = doc.getElementById('loans-search-count');
                if (newCount && searchCount) searchCount.textContent = newCount.textContent;
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
