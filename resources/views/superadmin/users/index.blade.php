@extends('superadmin.layout')

@section('title', 'Gestión de Usuarios')

@section('content')
    <div class="min-h-screen px-5 md:px-10 py-8">

        <!-- Header -->
        <div class="flex items-end justify-between mb-8">
            <div>
                <p class="text-xs text-gray-600 dark:text-gray-100 uppercase tracking-widest mb-1">Panel</p>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Clientes</h1>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('superadmin.users.create-ghost') }}"
                    class="text-blue-600 hover:text-blue-700 border-b border-blue-600 hover:border-blue-700 pb-0.5 text-sm font-medium transition-colors flex items-center gap-2">
                    Crear usuario personalizado
                </a>
                <p class="hidden md:block text-sm text-gray-600 dark:text-gray-100">{{ now()->format('d M Y') }}</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 flex items-center gap-3 text-sm text-green-500">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Búsqueda -->
        <div class="mb-6 relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" id="users-search" placeholder="Buscar por nombre, DNI o RUC..."
                   value="{{ request('search') }}"
                   class="w-full pl-10 pr-10 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-sm text-gray-900 dark:text-white placeholder:text-gray-400 focus:outline-none focus:border-gray-400 dark:focus:border-gray-600 transition-colors">
            <button type="button" id="users-search-clear"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors {{ request('search') ? '' : 'hidden' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <div id="users-search-spinner"
                 class="absolute right-3 top-1/2 -translate-y-1/2 hidden">
                <svg class="w-4 h-4 animate-spin text-gray-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </div>
        </div>

        <div id="users-search-badge" class="{{ request('search') ? '' : 'hidden' }} mb-4 flex items-center gap-2 text-xs text-gray-500 dark:text-gray-100">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
            </svg>
            Mostrando resultados para "<span id="users-search-term" class="font-medium text-gray-700 dark:text-gray-100">{{ request('search') }}</span>"
            — <span id="users-search-count">{{ $filter === 'blocked' ? $blockedUsers->total() : $users->total() }}</span> encontrado(s)
        </div>

        <!-- Filtros Mobile -->
        <div class="md:hidden mb-6">
            <div class="grid grid-cols-3 gap-2">
                @foreach([
                    'all'      => ['label' => 'Todos',      'active' => 'bg-gray-900 text-white dark:bg-white dark:text-black', 'inactive' => 'bg-transparent text-gray-400 border-gray-200 dark:border-gray-800'],
                    'pending'  => ['label' => 'Pendientes', 'active' => 'bg-gray-900 text-white dark:bg-white dark:text-black', 'inactive' => 'bg-transparent text-gray-400 border-gray-200 dark:border-gray-800'],
                    'approved' => ['label' => 'Aprobados',  'active' => 'bg-gray-900 text-white dark:bg-white dark:text-black', 'inactive' => 'bg-transparent text-gray-400 border-gray-200 dark:border-gray-800'],
                    'rejected' => ['label' => 'Rechazados', 'active' => 'bg-gray-900 text-white dark:bg-white dark:text-black', 'inactive' => 'bg-transparent text-gray-400 border-gray-200 dark:border-gray-800'],
                    'profile-changes' => ['label' => 'Cambios', 'active' => 'bg-amber-500 text-white', 'inactive' => 'bg-transparent text-amber-500/50 border-amber-200 dark:border-amber-900/30'],
                    'ghosts'   => ['label' => 'Fantasmas',  'active' => 'bg-purple-600 text-white', 'inactive' => 'bg-transparent text-purple-500/50 border-purple-200 dark:border-purple-900/30'],
                    'blocked'  => ['label' => 'Bloqueados', 'active' => 'bg-red-600 text-white',    'inactive' => 'bg-transparent text-red-500/50 border-red-200 dark:border-red-900/30'],
                ] as $key => $cfg)
                    <a href="{{ route('superadmin.users.index', array_filter(['filter' => $key, 'search' => request('search')])) }}"
                       class="flex items-center justify-center gap-1 py-2.5 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all border
                              {{ $filter === $key ? $cfg['active'] . ' border-transparent' : $cfg['inactive'] }}">
                        {{ $cfg['label'] }}
                        @if($key === 'pending' && ($counts['pending'] ?? 0) > 0)
                            <svg class="w-3 h-3 text-purple-600 animate-swing origin-top flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        @endif
                        @if($key === 'profile-changes' && ($counts['profile-changes'] ?? 0) > 0)
                            <span class="w-3 h-3 text-[8px] font-bold bg-amber-500 text-white rounded-full flex items-center justify-center flex-shrink-0">{{ $counts['profile-changes'] }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Filtros Desktop -->
        <div class="hidden md:flex flex-col border-b border-gray-200 dark:border-gray-800 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-0 overflow-x-auto no-scrollbar">
                    @foreach([
                        'all'      => 'Todos', 
                        'pending'  => 'Pendientes', 
                        'approved' => 'Aprobados', 
                        'rejected' => 'Rechazados',
                        'profile-changes' => 'Cambios perfil',
                        'ghosts'   => 'Fantasmas',
                        'blocked'  => 'Bloqueados'
                    ] as $key => $label)
                        @php
                            $isActive = $filter === $key;
                            $colorClass = match($key) {
                                'profile-changes' => $isActive ? 'text-amber-600' : 'text-gray-500 dark:text-gray-100 hover:text-amber-500',
                                'ghosts'  => $isActive ? 'text-purple-600' : 'text-gray-500 dark:text-gray-100 hover:text-purple-500',
                                'blocked' => $isActive ? 'text-red-500'    : 'text-gray-500 dark:text-gray-100 hover:text-red-500',
                                default   => $isActive ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-100 hover:text-gray-700 dark:hover:text-gray-400',
                            };
                            $lineClass = match($key) {
                                'profile-changes' => 'bg-amber-500',
                                'ghosts'  => 'bg-purple-600',
                                'blocked' => 'bg-red-500',
                                default   => 'bg-gray-900 dark:bg-white',
                            };
                        @endphp
                        <a href="{{ route('superadmin.users.index', array_filter(['filter' => $key, 'search' => request('search')])) }}" 
                           class="relative flex items-center gap-2 flex-shrink-0 px-6 py-4 text-sm font-medium transition-colors {{ $colorClass }}">
                            <span class="flex items-center gap-1">
                                {{ $label }}
                                @if($key === 'pending' && ($counts['pending'] ?? 0) > 0)
                                    <svg class="w-3.5 h-3.5 text-purple-600 animate-swing origin-top flex-shrink-0 relative top-[1px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                @endif
                            </span>
                            @if($counts[$key] > 0)
                                <span class="ml-2 text-xs {{ $isActive ? 'text-gray-500 dark:text-gray-100' : 'text-gray-400 dark:text-gray-100' }}">{{ $counts[$key] }}</span>
                            @endif
                            @if($isActive)
                                <span class="absolute bottom-0 left-0 right-0 h-px {{ $lineClass }}"></span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div id="users-list">
        <!-- Cabecera de columnas (solo desktop) -->
        @if($filter !== 'blocked' && $filter !== 'ghosts')
            <div class="hidden md:grid py-2 mb-1 px-2 text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider items-center"
                style="grid-template-columns: 1fr 100px 100px 110px 110px 80px 100px 90px 90px 24px;">
                <span>Usuario</span>
                <span class="text-center">Tipo</span>
                <span class="text-center">DNI</span>
                <span class="text-center">RUC</span>
                <span class="text-center">Teléfono</span>
                <span class="text-center">Tasa %</span>
                <span class="text-center">Registro</span>
                <span class="text-center">Estado</span>
                <span class="text-center">Referidor</span>
                <span></span>
            </div>
        @elseif($filter === 'ghosts')
            <div class="hidden md:grid py-2 mb-1 px-2 text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider items-center"
                style="grid-template-columns: 1fr 100px 110px 120px 130px 120px 100px 24px;">
                <span>Usuario</span>
                <span class="text-center">Tipo</span>
                <span class="text-center">DNI</span>
                <span class="text-center">RUC</span>
                <span class="text-center">Teléfono</span>
                <span class="text-center">Registro</span>
                <span class="text-center">Estado</span>
                <span></span>
            </div>
        @endif

        @if($filter === 'blocked')
            <!-- Lista de bloqueados -->
            <div class="hidden md:grid py-2 mb-1 px-2 text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider items-center"
                style="grid-template-columns: 1fr 100px 110px 120px 130px 120px 24px;">
                <span>Usuario</span>
                <span class="text-center">Tipo</span>
                <span class="text-center">DNI</span>
                <span class="text-center">RUC</span>
                <span class="text-center">Teléfono</span>
                <span class="text-center">Bloqueado</span>
                <span></span>
            </div>

            @forelse($blockedUsers as $blocked)
                <div class="user-row flex md:grid items-center py-4 border-b border-gray-200 dark:border-gray-900 -mx-2 px-2"
                    style="grid-template-columns: 1fr 100px 110px 120px 130px 120px 24px;">

                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-full bg-red-100 dark:bg-red-500/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $blocked->name }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-100 truncate">{{ $blocked->email }}</p>
                            @if($blocked->reason)
                                <p class="text-xs text-red-400 truncate mt-0.5">{{ $blocked->reason }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="hidden md:flex justify-center">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ ($blocked->person_type ?? 'natural') === 'juridica' ? 'bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-100' }}">
                            {{ ($blocked->person_type ?? 'natural') === 'juridica' ? 'JUR' : 'NAT' }}
                        </span>
                    </div>

                    <span
                        class="hidden md:block text-xs text-gray-900 dark:text-white text-center">{{ $blocked->dni ?? '—' }}</span>
                    <span
                        class="hidden md:block text-xs text-gray-900 dark:text-white text-center">{{ ($blocked->person_type ?? 'natural') === 'juridica' ? ($blocked->ruc ?? '—') : '—' }}</span>
                    <span
                        class="hidden md:block text-xs text-gray-900 dark:text-white text-center">{{ $blocked->phone ?? '—' }}</span>
                    <span
                        class="hidden md:block text-xs text-gray-900 dark:text-white text-center">{{ $blocked->created_at->format('d/m/Y') }}</span>

                    <div class="ml-auto md:ml-0 flex justify-center">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 dark:bg-gray-600"></span>
                    </div>
                </div>
            @empty
                <div class="py-20 text-center">
                    <p class="text-gray-500 dark:text-gray-100 text-sm">No hay usuarios bloqueados.</p>
                </div>
            @endforelse

        @else
            <!-- Lista normal -->
            @forelse($users as $user)
                @php $status = $user->documents?->status @endphp
                @php $hasPendingChanges = $user->profileChangeRequests()->where('status', 'pending')->exists() @endphp
                <div class="user-row flex md:grid items-center py-4 border-b border-gray-200 dark:border-gray-900 hover:bg-gray-50 dark:hover:bg-gray-900/30 -mx-2 px-2 rounded-lg transition-colors group cursor-pointer"
                    data-show-url="{{ route('superadmin.users.show', $user) }}"
                    style="grid-template-columns: {{ $user->is_ghost ? '1fr 100px 110px 120px 130px 120px 100px 24px' : '1fr 100px 100px 110px 110px 80px 100px 90px 90px 24px' }};">

                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div class="w-9 h-9 flex-shrink-0">
                            @if($user->documents && $user->documents->selfie)
                                <img src="{{ asset('storage/' . $user->documents->selfie) }}" alt="{{ $user->name }}" class="w-9 h-9 rounded-full object-cover ring-2 ring-offset-1 {{ $status === 'approved' ? 'ring-green-500' : ($status === 'pending' ? 'ring-yellow-500' : ($status === 'rejected' ? 'ring-red-500' : 'ring-gray-300 dark:ring-gray-700')) }} ring-offset-white dark:ring-offset-gray-950">
                            @elseif($user->is_ghost)
                                <div class="w-9 h-9 rounded-full bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </div>
                            @else
                                <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold text-white {{ $status === 'approved' ? 'bg-green-500' : ($status === 'pending' ? 'bg-yellow-500' : ($status === 'rejected' ? 'bg-red-500' : 'bg-gray-400')) }}">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $user->name }}</p>
                                @if($user->is_ghost)
                                    <span
                                        class="px-1.5 py-0.5 rounded text-[10px] bg-purple-100 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 font-medium">Fantasma</span>
                                @endif
                                @if($hasPendingChanges)
                                    <span class="px-1.5 py-0.5 rounded text-[10px] bg-amber-100 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 font-medium">✎ Cambio</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-100 truncate">{{ $user->email }}</p>
                        </div>
                    </div>

                    <div class="hidden md:flex justify-center">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $user->person_type === 'juridica' ? 'bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-100' }}">
                            {{ $user->person_type === 'juridica' ? 'JUR' : 'NAT' }}
                        </span>
                    </div>

                    <div class="md:hidden flex items-center ml-2">
                        @if($status === 'approved' && !$user->is_ghost)
                            <form action="{{ route('superadmin.users.update-field', $user) }}" method="POST"
                                class="flex items-center gap-1">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="field" value="loan_interest_rate">
                                <input type="number" name="value"
                                    value="{{ number_format($user->loan_interest_rate ?? 20, 2, '.', '') }}"
                                    class="w-12 text-center text-[10px] bg-gray-100 dark:bg-gray-800 rounded border-none focus:ring-1 focus:ring-blue-500 p-0.5"
                                    step="0.01" min="0" max="99.99"
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
                                                                                                                                                                                                                                }">
                                <span class="text-[10px] text-gray-400">%</span>
                                <button type="submit" class="text-blue-500 hover:text-blue-600 p-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </div>

                    <span class="hidden md:block text-xs text-gray-900 dark:text-white text-center">{{ $user->dni ?? '—' }}</span>
                    <span class="hidden md:block text-xs text-gray-900 dark:text-white text-center">{{ $user->person_type === 'juridica' ? ($user->ruc ?? '—') : '—' }}</span>
                    <span class="hidden md:block text-xs text-gray-900 dark:text-white text-center">{{ $user->phone ?? '—' }}</span>

                    <!-- Tasa % -->
                    @if(!$user->is_ghost)
                        <div class="hidden md:flex items-center justify-center" onclick="event.stopPropagation()">
                            @if($status === 'approved')
                                <form action="{{ route('superadmin.users.update-field', $user) }}" method="POST"
                                    class="flex items-center gap-1">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="field" value="loan_interest_rate">
                                    <input type="number" name="value"
                                        value="{{ number_format($user->loan_interest_rate ?? 20, 2, '.', '') }}"
                                        class="w-16 text-center text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:outline-none focus:border-blue-500 p-0"
                                        step="0.01" min="0" max="99.99"
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
                                                                                                                                                                                                                                                                }">
                                    <button type="submit"
                                        class="text-blue-500 hover:text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400 dark:text-gray-100">—</span>
                            @endif
                        </div>
                    @endif

                    <span
                        class="hidden md:block text-xs text-gray-900 dark:text-white text-center">{{ $user->created_at->format('d/m/Y') }}</span>

                    <!-- Estado -->
                    <div class="hidden md:flex items-center justify-center gap-1.5 ml-auto md:ml-0">
                        @if($status === 'pending')
                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 flex-shrink-0"></span>
                            <span class="text-xs text-yellow-500 hidden sm:inline">Pendiente</span>
                        @elseif($status === 'approved')
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 flex-shrink-0"></span>
                            <span class="text-xs text-green-500 hidden sm:inline">Aprobado</span>
                        @elseif($status === 'rejected')
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 flex-shrink-0"></span>
                            <span class="text-xs text-red-500 hidden sm:inline">Rechazado</span>
                        @else
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-300 dark:bg-gray-700 flex-shrink-0"></span>
                            <span class="text-xs text-gray-600 dark:text-gray-100 hidden sm:inline">Sin docs</span>
                        @endif
                    </div>

                    <!-- Referido por -->
                    @if(!$user->is_ghost)
                        <div class="hidden md:flex items-center justify-center h-full">
                            @if($user->referrer)
                                <span class="text-xs text-gray-600 dark:text-gray-100 font-medium" title="{{ $user->referrer->name }}">
                                    {{ $user->referrer->first_name }} {{ Str::substr($user->referrer->last_name, 0, 1) }}.
                                </span>
                            @else
                                <span class="text-xs text-gray-500 dark:text-gray-100">—</span>
                            @endif
                        </div>
                    @endif

                </div>
            @empty
                <div class="py-20 text-center">
                    <p class="text-gray-500 dark:text-gray-100 text-sm">No hay usuarios en esta categoría.</p>
                </div>
            @endforelse

        @endif

        <!-- Paginación -->
        @if($filter === 'blocked' && $blockedUsers->hasPages())
            <div class="mt-8 flex items-center justify-between text-xs text-gray-600 dark:text-gray-100">
                <span>{{ $blockedUsers->firstItem() }}–{{ $blockedUsers->lastItem() }} de {{ $blockedUsers->total() }}</span>
                <div class="flex items-center gap-5">
                    @if($blockedUsers->onFirstPage())
                        <span class="opacity-30">← Anterior</span>
                    @else
                        <a href="{{ $blockedUsers->appends(request()->query())->previousPageUrl() }}"
                            class="hover:text-gray-900 dark:hover:text-white transition-colors">← Anterior</a>
                    @endif
                    @if($blockedUsers->hasMorePages())
                        <a href="{{ $blockedUsers->appends(request()->query())->nextPageUrl() }}"
                            class="hover:text-gray-900 dark:hover:text-white transition-colors">Siguiente →</a>
                    @else
                        <span class="opacity-30">Siguiente →</span>
                    @endif
                </div>
            </div>
        @elseif($filter !== 'blocked' && $users->hasPages())
            <div class="mt-8 flex items-center justify-between text-xs text-gray-600 dark:text-gray-100">
                <span>{{ $users->firstItem() }}–{{ $users->lastItem() }} de {{ $users->total() }}</span>
                <div class="flex items-center gap-5">
                    @if($users->onFirstPage())
                        <span class="opacity-30">← Anterior</span>
                    @else
                        <a href="{{ $users->appends(request()->query())->previousPageUrl() }}"
                            class="hover:text-gray-900 dark:hover:text-white transition-colors">← Anterior</a>
                    @endif
                    @if($users->hasMorePages())
                        <a href="{{ $users->appends(request()->query())->nextPageUrl() }}"
                            class="hover:text-gray-900 dark:hover:text-white transition-colors">Siguiente →</a>
                    @else
                        <span class="opacity-30">Siguiente →</span>
                    @endif
                </div>
            </div>
        @endif
        </div>

    </div>

    <script>
    (function() {
        // Row click → navigate to show (except tasa column which stops propagation)
        document.querySelectorAll('.user-row[data-show-url]').forEach(function(row) {
            row.addEventListener('click', function(e) {
                // Don't navigate if clicking on a form, input, button, or link inside the row
                if (e.target.closest('form, input, button, a')) return;
                window.location.href = this.dataset.showUrl;
            });
        });

        const searchInput = document.getElementById('users-search');
        const clearBtn = document.getElementById('users-search-clear');
        const spinner = document.getElementById('users-search-spinner');
        const badge = document.getElementById('users-search-badge');
        const searchTerm = document.getElementById('users-search-term');
        const searchCount = document.getElementById('users-search-count');
        const usersList = document.getElementById('users-list');
        if (!searchInput || !usersList) return;

        const currentFilter = '{{ $filter }}';
        const baseUrl = '{{ route("superadmin.users.index") }}';
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
                badge.classList.add('hidden');
            } else {
                clearBtn.classList.add('hidden');
                spinner.classList.remove('hidden');
                searchTerm.textContent = term;
                badge.classList.remove('hidden');
            }

            currentRequest = new AbortController();

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-Live-Search': 'true' },
                signal: currentRequest.signal
            })
            .then(function(response) { return response.text(); })
            .then(function(html) {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newList = doc.getElementById('users-list');
                if (newList) {
                    usersList.innerHTML = newList.innerHTML;
                }
                const newCount = doc.getElementById('users-search-count');
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