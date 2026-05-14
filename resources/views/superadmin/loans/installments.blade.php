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
                    @endphp
                    <div>
                        <div class="flex items-center gap-4 pb-4 border-b border-gray-100 dark:border-gray-900 mb-2">
                            <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-900 flex items-center justify-center overflow-hidden border border-gray-100 dark:border-gray-800">
                                @php
                                    $selfie = $user->documents?->selfie;
                                    $photo = $selfie ? Storage::url($selfie) : ($user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name));
                                @endphp
                                <img src="{{ $photo }}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight truncate">{{ $user->name }}</h3>
                                    <button type="button" onclick="openLoanDetailModal({{ json_encode([
                                        'photo'             => $photo,
                                        'name'              => $user->name,
                                        'loanId'            => str_pad($loan->id, 4, '0', STR_PAD_LEFT),
                                        'status'            => match($loan->status) { 'approved' => 'Activo', 'paid' => 'Completado', default => $loan->status },
                                        'profileUrl'        => route('superadmin.users.show', $user),
                                        'dni'               => $user->dni,
                                        'phone'             => $user->phone,
                                        'email'             => $user->email,
                                        'address'           => $user->address,
                                        'amount'            => ($loan->currency === 'USD' ? '$' : 'S/') . ' ' . number_format($loan->amount, 2),
                                        'total'             => ($loan->currency === 'USD' ? '$' : 'S/') . ' ' . number_format($loan->total_amount, 2),
                                        'rate'              => number_format($loan->interest_rate, 2) . '%',
                                        'installmentsCount' => $loan->installments()->count() . ' cuotas',
                                        'frequency'         => ucfirst($loan->payment_frequency),
                                        'system'            => ucfirst($loan->amortization_system),
                                        'bank'              => $loan->bank_name,
                                        'account'           => $loan->account_number,
                                        'reason'            => $loan->reason,
                                    ]) }})"
                                    class="text-gray-300 dark:text-gray-700 hover:text-gray-600 dark:hover:text-gray-400 transition-colors flex-shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
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
            // Cerrar también el lightbox si está abierto
            document.getElementById('voucherLightbox').classList.add('hidden');
            document.getElementById('validateModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        document.getElementById('validateModal').addEventListener('click', e => {
            if (e.target === document.getElementById('validateModal')) closeValidateModal();
        });
    </script>
@endsection
