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
            <div class="md:hidden mb-6">
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
            <div class="hidden md:flex items-center gap-0 border-b border-gray-200 dark:border-gray-800 mb-8 overflow-x-auto no-scrollbar">
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
                    <div class="group py-8 transition-colors">
                        <div class="flex flex-col lg:flex-row gap-8 items-start">

                            <!-- Columna 1: Usuario e Info Principal -->
                            <div class="lg:w-1/4 flex flex-col gap-5">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-900 flex items-center justify-center overflow-hidden flex-shrink-0 border border-gray-100 dark:border-gray-800">
                                        @if($loan->user->is_ghost)
                                            <svg fill="#9333ea" width="22" height="22" viewBox="0 0 1024 1024"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M511.984-.128c-229.216 0-415.681 199.903-415.681 445.6v546.672c0 13.216 8.16 25.088 20.496 29.84 3.712 1.471 7.632 2.16 11.504 2.16 8.848 0 17.536-3.68 23.712-10.527l120.592-133.12 94.431 130.432a31.918 31.918 0 0 0 25.68 13.215h.224a31.98 31.98 0 0 0 25.664-12.912l94.816-127.344 93.184 127.152a31.993 31.993 0 0 0 25.809 13.088 32.028 32.028 0 0 0 25.808-13.055l95.569-130.288 118 132.624c8.816 9.904 22.944 13.376 35.28 8.624 12.4-4.72 20.624-16.624 20.624-29.905V445.456C927.696 199.776 741.2-.128 511.984-.128zm351.711 908.16l-88.402-99.376c-6.432-7.216-15.808-11.311-25.407-10.687a32.105 32.105 0 0 0-24.32 13.024l-93.12 127.008-93.008-126.912A31.975 31.975 0 0 0 513.758 798h-.127a31.935 31.935 0 0 0-25.664 12.912l-94.689 127.152-92-127.088c-5.664-7.807-14.528-12.655-24.16-13.151-.592-.032-1.151-.065-1.743-.065a31.984 31.984 0 0 0-23.712 10.528l-91.376 100.848v-463.68c0-210.4 157.776-381.601 351.68-381.601 193.937 0 351.713 171.184 351.713 381.6V908.03h.015zM671.997 352.16c-35.28 0-63.84 28.592-63.84 63.808 0 35.248 28.56 63.84 63.84 63.84s63.84-28.592 63.84-63.84c0-35.216-28.56-63.808-63.84-63.808zm-320 0c-35.28 0-63.84 28.592-63.84 63.808 0 35.248 28.576 63.84 63.84 63.84s63.84-28.592 63.84-63.84c0-35.216-28.56-63.808-63.84-63.808z" />
                                            </svg>
                                        @else
                                            @php
                                                $selfie = $loan->user->documents?->selfie;
                                                $photo = $selfie ? Storage::url($selfie) : ($loan->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($loan->user->name));
                                            @endphp
                                            <img src="{{ $photo }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-gray-900 dark:text-white truncate">
                                            {{ $loan->user->name }}</p>
                                        <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-tight">
                                            {{ $loan->user->email }}</p>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <p class="text-xl font-black text-gray-900 dark:text-white tracking-tighter">
                                            {{ $loan->currency === 'USD' ? '$' : 'S/' }} {{ number_format($loan->amount, 2) }}
                                        </p>
                                        <span
                                            class="text-[8px] bg-black text-white dark:bg-white dark:text-black px-1.5 py-0.5 rounded font-black uppercase tracking-widest">
                                            {{ $loan->type === 'cuotas' ? 'Cuotas' : 'Unico' }}
                                        </span>
                                    </div>
                                    <p class="text-[10px] text-gray-400 dark:text-gray-600 font-medium">
                                        Solicitado el {{ $loan->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Columna 2: Detalles Técnicos -->
                            <div
                                class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-6 px-0 lg:px-8">
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-[10px] text-gray-400 dark:text-gray-600 uppercase font-bold mb-1">Destino completo</p>
                                        <div class="space-y-0.5">
                                            <p class="text-[11px] font-bold text-gray-800 dark:text-gray-200 uppercase leading-tight">{{ $loan->bank_name }}</p>
                                            <p class="text-[10px] text-gray-500 dark:text-gray-400 leading-tight">{{ ucfirst($loan->account_type) }}</p>
                                            <p class="text-[10px] font-mono text-gray-600 dark:text-gray-500 leading-tight">{{ $loan->account_number }}</p>
                                            @if($loan->cci)
                                                <p class="text-[9px] font-mono text-gray-400 dark:text-gray-600 leading-tight">CCI: {{ $loan->cci }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray-400 dark:text-gray-600 uppercase font-bold mb-1">Plazo</p>
                                        <p class="text-xs text-gray-900 dark:text-gray-300 font-semibold">
                                            @if($loan->type === 'cuotas')
                                                {{ $loan->months }} meses
                                            @else
                                                {{ $loan->payment_days }} días
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-[10px] text-gray-400 dark:text-gray-600 uppercase font-bold mb-1">Tasa
                                            actual</p>
                                        <p class="text-xs text-amber-600 dark:text-amber-500 font-black">
                                            {{ number_format($loan->interest_rate, 2) }}%</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray-400 dark:text-gray-600 uppercase font-bold mb-1">Sistema
                                        </p>
                                        <p class="text-xs text-gray-900 dark:text-gray-300 font-semibold uppercase">
                                            {{ $loan->amortization_system ?? 'Francés' }}</p>
                                    </div>
                                </div>
                                <div class="col-span-1 sm:col-span-1">
                                    <p class="text-[10px] text-gray-400 dark:text-gray-600 uppercase font-bold mb-1">Motivo</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 italic leading-relaxed">
                                        "{{ Str::limit($loan->reason, 150) }}"
                                    </p>
                                </div>
                            </div>

                            <!-- Columna 3: Contra-Oferta / Acciones -->
                            <div class="lg:w-1/3 flex flex-col justify-between gap-6">
                                @if($loan->status === 'pending')
                                    <form action="{{ route('superadmin.loans.counter-offer', $loan) }}" method="POST"
                                        class="space-y-4">
                                        @csrf
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="text-[9px] text-gray-400 uppercase font-bold mb-1 block">Nuevo
                                                    Sistema</label>
                                                <select name="amortization_system"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl text-[10px] font-bold py-2 focus:ring-1 focus:ring-purple-500 dark:text-white">
                                                    <option value="frances" {{ ($loan->amortization_system ?? 'frances') === 'frances' ? 'selected' : '' }}>FRANCÉS</option>
                                                    <option value="americano" {{ $loan->amortization_system === 'americano' ? 'selected' : '' }}>AMERICANO</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="text-[9px] text-gray-400 uppercase font-bold mb-1 block">Nueva Tasa
                                                    %</label>
                                                <input type="number" name="interest_rate"
                                                    value="{{ number_format($loan->interest_rate, 2, '.', '') }}" step="0.01"
                                                    min="0" max="100"
                                                    class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl text-[10px] font-bold py-2 focus:ring-1 focus:ring-purple-500 dark:text-white">
                                            </div>
                                        </div>
                                        <button type="submit" class="w-full py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-md shadow-purple-500/10 active:scale-[0.98]">
                                            Enviar Contra-Oferta
                                        </button>
                                    </form>

                                <div class="flex gap-2">
                                    <form action="{{ route('superadmin.loans.approve', $loan) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full py-2.5 bg-green-500/10 text-green-600 dark:text-green-400 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-green-500 hover:text-white transition-all">
                                            Aprobar
                                        </button>
                                    </form>
                                    <form action="{{ route('superadmin.loans.reject', $loan) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full py-2.5 bg-red-500/10 text-red-600 dark:text-red-400 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-red-500 hover:text-white transition-all">
                                            Rechazar
                                        </button>
                                    </form>
                                </div>
                                @else
                                    <div class="h-full flex flex-col items-center justify-center text-center">
                                        <span class="px-4 py-2 rounded-2xl text-xs font-black uppercase tracking-widest
                                                    {{ $loan->status === 'approved' ? 'bg-green-500/10 text-green-500' : '' }}
                                                    {{ $loan->status === 'rejected' ? 'bg-red-500/10 text-red-500' : '' }}
                                                    {{ $loan->status === 'paid' ? 'bg-blue-500/10 text-blue-500' : '' }}">
                                            {{ $loan->status }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>
                @empty
                    <div
                        class="py-20 text-center bg-gray-50 dark:bg-gray-900/30 rounded-3xl border-2 border-dashed border-gray-100 dark:border-gray-800">
                        <p class="text-gray-400 dark:text-gray-500 text-sm font-medium">No se encontraron préstamos en esta
                            categoría.</p>
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
@endsection