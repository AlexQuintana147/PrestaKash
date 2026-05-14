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
                @forelse($installments as $userId => $userInstallments)
                    @php
                        $user = $userInstallments->first()->loan->user;
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
                            <div>
                                <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $user->name }}</h3>
                                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">Total Cuotas: {{ $userInstallments->count() }}</p>
                            </div>
                        </div>
                        <div class="divide-y divide-gray-50 dark:divide-gray-900/50">
                            @foreach($userInstallments as $inst)
                                @include('superadmin.loans.partials.installment-row-minimal', ['inst' => $inst])
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-24 text-center">
                        <p class="text-sm text-gray-400 dark:text-gray-500 font-medium uppercase tracking-widest">No hay cuotas registradas</p>
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

    <script>
        function openValidateModal(data) {
            document.getElementById('modal_voucher_img').src = data.voucher;
            document.getElementById('modal_voucher_link').href = data.voucher;
            
            document.getElementById('modal_info_text').innerHTML = `
                <p class="text-sm text-gray-500 font-medium">Usuario: <span class="text-gray-900 dark:text-white font-black">${data.user}</span></p>
                <p class="text-sm text-gray-500 font-medium">Cuota: <span class="text-gray-900 dark:text-white font-black">#${data.number}</span></p>
                <p class="text-sm text-gray-500 font-medium">Monto esperado: <span class="text-purple-600 font-black">${data.amount}</span></p>
            `;

            document.getElementById('approveForm').action = `/gestion-interna/pagos/${data.id}/approve`;
            document.getElementById('rejectForm').action = `/gestion-interna/pagos/${data.id}/reject`;

            document.getElementById('validateModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeValidateModal() {
            document.getElementById('validateModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        document.getElementById('validateModal').addEventListener('click', e => {
            if (e.target === document.getElementById('validateModal')) closeValidateModal();
        });
    </script>
@endsection
