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

        <!-- Filtros Mobile -->
        <div class="md:hidden mb-8">
            <div class="grid grid-cols-3 gap-2">
                @foreach([
                    'all' => ['label' => 'Todas', 'active' => 'bg-gray-900 text-white dark:bg-white dark:text-black', 'inactive' => 'bg-transparent text-gray-400 border-gray-200 dark:border-gray-800'],
                    'grouped' => ['label' => 'Por Grupo', 'active' => 'bg-gray-900 text-white dark:bg-white dark:text-black', 'inactive' => 'bg-transparent text-gray-400 border-gray-200 dark:border-gray-800'],
                    'pending_validation' => ['label' => 'Validar', 'active' => 'bg-purple-600 text-white', 'inactive' => 'bg-transparent text-gray-400 border-gray-200 dark:border-gray-800'],
                ] as $key => $cfg)
                    <a href="{{ route('superadmin.installments.index', ['filter' => $key]) }}"
                       class="flex items-center justify-center py-2.5 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all border
                              {{ $filter === $key ? $cfg['active'] . ' border-transparent' : $cfg['inactive'] }}">
                        {{ $cfg['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Filtros Desktop -->
        <div class="hidden md:flex items-center gap-0 border-b border-gray-200 dark:border-gray-800 mb-8 overflow-x-auto no-scrollbar">
            @foreach(['all' => 'Todas las cuotas', 'grouped' => 'Por Grupo', 'pending_validation' => 'Por Validar'] as $key => $label)
                <a href="{{ route('superadmin.installments.index', ['filter' => $key]) }}"
                    class="relative flex-shrink-0 px-6 py-4 text-sm font-medium transition-colors
                              {{ $filter === $key ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-400' }}">
                    {{ $label }}
                    @if($filter === $key)
                        <span class="absolute bottom-0 left-0 right-0 h-px bg-gray-900 dark:bg-white"></span>
                    @endif
                </a>
            @endforeach
        </div>

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
                            'loanId'            => str_pad($loan->id, 4, '0', STR_PAD_LEFT),
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
                    <div>
                        <div class="flex items-center gap-4 pb-4 border-b border-gray-100 dark:border-gray-900 mb-2">
                            <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-900 flex items-center justify-center overflow-hidden border border-gray-100 dark:border-gray-800">
                                <img src="{{ $photo }}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight truncate">{{ $user->name }}</h3>
                                    <button type="button"
                                        onclick="openLoanDetailModal({{ json_encode($groupDetailData) }})"
                                        class="text-gray-900 dark:text-white hover:opacity-60 transition-opacity flex-shrink-0">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg"><g><path d="M32,47c8.271,0,15-6.729,15-15s-6.729-15-15-15s-15,6.729-15,15S23.729,47,32,47z M32,19c7.168,0,13,5.832,13,13s-5.832,13-13,13s-13-5.832-13-13S24.832,19,32,19z"/><path d="M32,41c4.962,0,9-4.037,9-9s-4.038-9-9-9s-9,4.037-9,9S27.038,41,32,41z M32,25c3.86,0,7,3.141,7,7s-3.14,7-7,7s-7-3.141-7-7S28.14,25,32,25z"/><path d="M32,37v-2c-1.654,0-3-1.346-3-3s1.346-3,3-3s3,1.346,3,3h2c0-2.757-2.243-5-5-5s-5,2.243-5,5S29.243,37,32,37z"/><path d="M62.53,30.74C57.134,18.748,45.15,11,32,11S6.866,18.748,1.47,30.74L0.903,32l0.567,1.26C6.866,45.252,18.85,53,32,53s25.134-7.748,30.53-19.74L63.097,32L62.53,30.74z M51,32c0,10.477-8.523,19-19,19s-19-8.523-19-19s8.523-19,19-19S51,21.523,51,32z M3.294,32.439L3.097,32l0.198-0.439c3.07-6.824,8.418-12.172,14.897-15.342C13.794,20.071,11,25.709,11,32s2.794,11.929,7.191,15.782C11.713,44.611,6.365,39.264,3.294,32.439z M60.706,32.439c-3.07,6.824-8.418,12.172-14.897,15.342C50.206,43.929,53,38.291,53,32s-2.794-11.929-7.191-15.782c6.478,3.17,11.826,8.518,14.897,15.342L60.903,32L60.706,32.439z"/></g></svg>
                                    </button>
                                </div>
                                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">Préstamo #{{ str_pad($loan->id, 4, '0', STR_PAD_LEFT) }} • Total: {{ $userInstallments->total() }}</p>
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
        @else
            <!-- Vista Lista Simple -->
            <div class="divide-y divide-gray-100 dark:divide-gray-900">
                @forelse($installments as $inst)
                    @include('superadmin.loans.partials.installment-row', ['inst' => $inst])
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

    <!-- Modal Validación de Pago -->
    @include('superadmin.loans.partials.validate-modal')

    <!-- Modal Detalle Préstamo -->
    @include('superadmin.loans.partials.loan-detail-modal')

    <script>
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
            });
        }

        function openValidateModal(data) {
            document.getElementById('modal_voucher_img').src = data.voucher;
            document.getElementById('modal_voucher_link').href = data.voucher;

            document.getElementById('modal_info_text').innerHTML = `
                <p class="text-xs text-gray-500">Usuario: <span class="text-gray-900 dark:text-white font-black">${data.user}</span></p>
                <p class="text-xs text-gray-500">Cuota: <span class="text-gray-900 dark:text-white font-black">#${data.number}</span></p>
                <p class="text-xs text-gray-500">Monto esperado: <span class="text-purple-600 font-black">${data.amount}</span></p>
            `;

            document.getElementById('approveForm').action = `/gestion-interna/pagos/${data.id}/approve`;
            document.getElementById('rejectForm').action = `/gestion-interna/pagos/${data.id}/reject`;

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
    </script>
@endsection
