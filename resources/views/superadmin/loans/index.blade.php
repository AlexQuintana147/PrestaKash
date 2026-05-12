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
                                class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-10 px-0 lg:px-12">
                                <div>
                                    <p class="text-[10px] text-gray-400 dark:text-gray-600 uppercase font-black mb-2 tracking-widest">Destino completo</p>
                                    <div class="space-y-1">
                                        <p class="text-xs font-bold text-gray-900 dark:text-white uppercase leading-tight">{{ $loan->bank_name }}</p>
                                        <p class="text-[10px] text-gray-500 dark:text-gray-500 leading-tight">{{ ucfirst($loan->account_type) }} • {{ $loan->account_number }}</p>
                                        @if($loan->cci)
                                            <p class="text-[9px] font-mono text-gray-400 dark:text-gray-600 leading-tight">CCI: {{ $loan->cci }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 dark:text-gray-600 uppercase font-black mb-2 tracking-widest">Plazo y Motivo</p>
                                    <p class="text-xs text-gray-900 dark:text-white font-bold">
                                        @if($loan->type === 'cuotas')
                                            {{ $loan->months }} meses
                                        @else
                                            {{ $loan->payment_days }} días
                                        @endif
                                    </p>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 italic mt-1 leading-relaxed">
                                        "{{ Str::limit($loan->reason, 80) }}"
                                    </p>
                                </div>
                            </div>

                        <!-- Columna 3: Acciones -->
                        <div class="lg:w-1/4 flex flex-col items-end justify-center gap-4">
                            @if($loan->status === 'pending')
                                <div class="flex items-center gap-3">
                                    <!-- Contra-oferta -->
                                    <button type="button" 
                                            onclick="openCounterOfferModal({{ json_encode([
                                                'id' => $loan->id,
                                                'amount' => $loan->amount,
                                                'currency' => $loan->currency,
                                                'type' => $loan->type,
                                                'months' => $loan->months,
                                                'payment_days' => $loan->payment_days,
                                                'interest_rate' => $loan->interest_rate,
                                                'system' => $loan->amortization_system ?? 'frances'
                                            ]) }})"
                                            class="p-3 rounded-xl bg-purple-500/10 text-purple-600 hover:bg-purple-600 hover:text-white transition-all shadow-sm group/btn" 
                                            title="Enviar Contra-oferta">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                    </button>

                                    <!-- Aprobar -->
                                    <form action="{{ route('superadmin.loans.approve', $loan) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="p-3 rounded-xl bg-green-500/10 text-green-600 hover:bg-green-600 hover:text-white transition-all shadow-sm" title="Aprobar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    </form>

                                    <!-- Rechazar -->
                                    <form action="{{ route('superadmin.loans.reject', $loan) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="p-3 rounded-xl bg-red-500/10 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm" title="Rechazar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="px-4 py-2 rounded-2xl text-xs font-black uppercase tracking-widest
                                            {{ $loan->status === 'approved' ? 'bg-green-500/10 text-green-500' : '' }}
                                            {{ $loan->status === 'rejected' ? 'bg-red-500/10 text-red-500' : '' }}
                                            {{ $loan->status === 'paid' ? 'bg-blue-500/10 text-blue-500' : '' }}">
                                    {{ $loan->status }}
                                </span>
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

<!-- Modal Contra-Oferta -->
<div id="counterOfferModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-950 w-full max-w-2xl rounded-[2.5rem] shadow-2xl overflow-hidden relative animate-in fade-in zoom-in duration-200">
        <!-- Close -->
        <button onclick="closeCounterOfferModal()" class="absolute top-8 right-8 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-900 transition-colors z-20">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <form id="counterOfferForm" method="POST">
            @csrf
            <div class="p-10 md:p-12">
                <div class="mb-10">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Enviar Contra-Oferta</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1 uppercase tracking-widest font-medium">Ajuste de términos financieros</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-12">
                    <!-- Sistema -->
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-[0.2em] block">Sistema de Amortización</label>
                        <div class="flex gap-2">
                            <label class="flex-1 cursor-pointer group">
                                <input type="radio" name="amortization_system" value="frances" class="hidden peer" onchange="calculatePreview()">
                                <div class="py-3 text-center rounded-2xl border border-gray-100 dark:border-gray-900 text-xs font-bold text-gray-400 dark:text-gray-600 peer-checked:border-purple-500 peer-checked:bg-purple-500/5 peer-checked:text-purple-600 transition-all group-hover:border-gray-200 dark:group-hover:border-gray-800">
                                    FRANCÉS
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer group">
                                <input type="radio" name="amortization_system" value="americano" class="hidden peer" onchange="calculatePreview()">
                                <div class="py-3 text-center rounded-2xl border border-gray-100 dark:border-gray-900 text-xs font-bold text-gray-400 dark:text-gray-600 peer-checked:border-purple-500 peer-checked:bg-purple-500/5 peer-checked:text-purple-600 transition-all group-hover:border-gray-200 dark:group-hover:border-gray-800">
                                    AMERICANO
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Tasa -->
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-[0.2em] block">Nueva Tasa de Interés (%)</label>
                        <div class="relative">
                            <input type="number" name="interest_rate" id="modal_interest_rate" step="0.01" min="0" max="100" oninput="calculatePreview()"
                                   class="w-full bg-transparent border-0 border-b-2 border-gray-100 dark:border-gray-900 py-3 text-xl font-black text-gray-900 dark:text-white focus:outline-none focus:border-purple-500 transition-colors">
                            <span class="absolute right-0 top-1/2 -translate-y-1/2 text-gray-400 font-bold">%</span>
                        </div>
                    </div>
                </div>

                <!-- Preview Cronograma -->
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h4 class="text-[10px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-[0.2em]">Vista Previa del Cronograma</h4>
                        <div class="flex items-center gap-4">
                            <p class="text-[10px] text-gray-500"><span class="font-bold text-gray-900 dark:text-white uppercase">Total:</span> <span id="previewTotal" class="font-black tabular-nums"></span></p>
                            <p class="text-[10px] text-gray-500"><span class="font-bold text-gray-900 dark:text-white uppercase">Cuota:</span> <span id="previewMonthly" class="font-black tabular-nums"></span></p>
                        </div>
                    </div>
                    
                    <div class="max-height-[200px] overflow-y-auto pr-2 custom-scrollbar">
                        <table class="w-full text-[10px] text-left">
                            <thead class="sticky top-0 bg-white dark:bg-gray-950 text-gray-400 dark:text-gray-600">
                                <tr>
                                    <th class="py-2 font-black uppercase tracking-tighter">N°</th>
                                    <th class="py-2 font-black uppercase tracking-tighter">Capital</th>
                                    <th class="py-2 font-black uppercase tracking-tighter">Interés</th>
                                    <th class="py-2 font-black uppercase tracking-tighter text-right">Saldo</th>
                                </tr>
                            </thead>
                            <tbody id="previewScheduleBody" class="divide-y divide-gray-50 dark:divide-gray-900 text-gray-600 dark:text-gray-400">
                                <!-- JS -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-12">
                    <button type="submit" class="w-full py-4 bg-purple-600 hover:bg-purple-700 text-white text-xs font-black uppercase tracking-[0.2em] rounded-2xl transition-all shadow-xl shadow-purple-500/20 active:scale-[0.99]">
                        Confirmar y Enviar Contra-Oferta
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
        document.querySelector(`input[name="amortization_system"][value="${loan.system}"]`).checked = true;
        document.getElementById('modal_interest_rate').value = parseFloat(loan.interest_rate).toFixed(2);
        
        calculatePreview();
        
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeCounterOfferModal() {
        document.getElementById('counterOfferModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function calculatePreview() {
        if (!currentLoan) return;

        const amount = parseFloat(currentLoan.amount);
        const rate = parseFloat(document.getElementById('modal_interest_rate').value) / 100;
        const system = document.querySelector('input[name="amortization_system"]:checked').value;
        const currency = currentLoan.currency === 'USD' ? '$' : 'S/';
        
        const body = document.getElementById('previewScheduleBody');
        const totalEl = document.getElementById('previewTotal');
        const monthlyEl = document.getElementById('previewMonthly');
        
        body.innerHTML = '';
        
        if (currentLoan.type === 'unico') {
            const interest = amount * rate;
            const total = amount + interest;
            
            body.innerHTML = `
                <tr>
                    <td class="py-3 font-bold">1</td>
                    <td class="py-3 tabular-nums">${currency}${amount.toFixed(2)}</td>
                    <td class="py-3 tabular-nums">${currency}${interest.toFixed(2)}</td>
                    <td class="py-3 tabular-nums text-right font-black text-gray-900 dark:text-white">${currency}0.00</td>
                </tr>
            `;
            totalEl.textContent = `${currency}${total.toFixed(2)}`;
            monthlyEl.textContent = 'N/A';
            return;
        }

        // Loan with installments
        const n = parseInt(currentLoan.months);
        let schedule = [];
        let totalInterest = 0;
        let monthlyPayment = 0;

        if (system === 'frances') {
            // French: Fixed installments
            monthlyPayment = amount * (rate * Math.pow(1 + rate, n)) / (Math.pow(1 + rate, n) - 1);
            let balance = amount;
            for (let i = 1; i <= n; i++) {
                const interest = balance * rate;
                const principal = monthlyPayment - interest;
                balance -= principal;
                totalInterest += interest;
                schedule.push({ n: i, principal, interest, balance: Math.max(0, balance) });
            }
        } else {
            // American: Fixed interest payments, principal at the end
            const interestPerMonth = amount * rate;
            monthlyPayment = interestPerMonth;
            let balance = amount;
            for (let i = 1; i <= n; i++) {
                const interest = interestPerMonth;
                const principal = (i === n) ? amount : 0;
                balance -= principal;
                totalInterest += interest;
                schedule.push({ n: i, principal, interest, balance: Math.max(0, balance) });
            }
        }

        schedule.forEach(row => {
            body.innerHTML += `
                <tr class="border-b border-gray-50 dark:border-gray-900/50 last:border-0">
                    <td class="py-3 font-bold">${row.n}</td>
                    <td class="py-3 tabular-nums">${currency}${row.principal.toFixed(2)}</td>
                    <td class="py-3 tabular-nums">${currency}${row.interest.toFixed(2)}</td>
                    <td class="py-3 tabular-nums text-right font-black text-gray-900 dark:text-white">${currency}${row.balance.toFixed(2)}</td>
                </tr>
            `;
        });

        totalEl.textContent = `${currency}${(amount + totalInterest).toFixed(2)}`;
        monthlyEl.textContent = `${currency}${monthlyPayment.toFixed(2)}`;
    }

    // Close on overlay click
    document.getElementById('counterOfferModal').addEventListener('click', e => {
        if (e.target === document.getElementById('counterOfferModal')) closeCounterOfferModal();
    });
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #1f2937; }
</style>