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
            <div class="grid grid-cols-2 gap-2">
                @foreach([
                    'all' => ['label' => 'Todas las cuotas', 'active' => 'bg-gray-900 text-white dark:bg-white dark:text-black', 'inactive' => 'bg-transparent text-gray-400 border-gray-200 dark:border-gray-800'],
                    'pending_validation' => ['label' => 'Por Validar', 'active' => 'bg-purple-600 text-white', 'inactive' => 'bg-transparent text-gray-400 border-gray-200 dark:border-gray-800'],
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
            @foreach(['all' => 'Todas las cuotas', 'pending_validation' => 'Por Validar'] as $key => $label)
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

        <!-- Lista de Cuotas -->
        <div class="divide-y divide-gray-100 dark:divide-gray-900">
            @forelse($installments as $inst)
                @php
                    $loan = $inst->loan;
                    $user = $loan->user;
                    $isOverdue = $inst->due_date->isPast() && $inst->status !== 'approved';
                @endphp
                <div class="py-6 transition-colors hover:bg-gray-50/50 dark:hover:bg-gray-900/20">
                    <div class="flex flex-col md:flex-row md:items-center gap-6">
                        
                        <!-- Usuario & Préstamo -->
                        <div class="flex items-center gap-4 flex-1">
                            <div class="relative">
                                <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-900 flex items-center justify-center overflow-hidden border border-gray-100 dark:border-gray-800">
                                    @php
                                        $selfie = $user->documents?->selfie;
                                        $photo = $selfie ? Storage::url($selfie) : ($user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name));
                                    @endphp
                                    <img src="{{ $photo }}" class="w-full h-full object-cover">
                                </div>
                                @if($isOverdue)
                                    <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 border-2 border-white dark:border-gray-950 rounded-full"></span>
                                @endif
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">
                                    {{ $user->name }}
                                </h3>
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1">
                                    <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">L-{{ str_pad($loan->id, 4, '0', STR_PAD_LEFT) }}</span>
                                    <span class="text-[9px] text-gray-300 dark:text-gray-700">•</span>
                                    <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">Cuota #{{ $inst->installment_number }}</span>
                                    <span class="text-[9px] text-gray-300 dark:text-gray-700">•</span>
                                    <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">{{ $loan->bank_name }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Detalles de Pago -->
                        <div class="grid grid-cols-2 md:flex items-center gap-8 md:gap-12">
                            <div>
                                <p class="text-[9px] text-gray-400 uppercase font-black tracking-widest mb-1.5">Vencimiento</p>
                                <p class="text-xs font-bold {{ $isOverdue ? 'text-red-500' : 'text-gray-900 dark:text-white' }}">
                                    {{ $inst->due_date->format('d/m/Y') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-[9px] text-gray-400 uppercase font-black tracking-widest mb-1.5">Monto</p>
                                <p class="text-sm font-black text-gray-900 dark:text-white tabular-nums">
                                    {{ $loan->currency === 'USD' ? '$' : 'S/' }} {{ number_format($inst->amount, 2) }}
                                </p>
                            </div>
                        </div>

                        <!-- Estado & Acciones -->
                        <div class="flex items-center justify-between md:justify-end gap-6 border-t md:border-t-0 pt-4 md:pt-0 border-gray-50 dark:border-gray-900">
                            <div class="text-right md:text-right">
                                <p class="text-[9px] text-gray-400 uppercase font-black tracking-widest mb-1.5">Estado</p>
                                <span class="text-[9px] font-black uppercase tracking-widest
                                    {{ $inst->status === 'approved' ? 'text-green-500' : '' }}
                                    {{ $inst->status === 'pending' ? 'text-gray-400' : '' }}
                                    {{ $inst->status === 'processing' ? 'text-purple-600' : '' }}
                                    {{ $inst->status === 'rejected' ? 'text-red-500' : '' }}">
                                    {{ match ($inst->status) {
                                        'approved' => 'Pagado',
                                        'pending' => 'Pendiente',
                                        'processing' => 'Validar',
                                        'rejected' => 'Rechazado',
                                        default => $inst->status
                                    } }}
                                </span>
                            </div>

                            <div class="flex items-center gap-3">
                                @if($inst->status === 'processing')
                                    <button type="button" onclick="openValidateModal({{ json_encode([
                                        'id' => $inst->id,
                                        'amount' => ($loan->currency === 'USD' ? '$' : 'S/') . number_format($inst->amount, 2),
                                        'user' => $user->name,
                                        'number' => $inst->installment_number,
                                        'voucher' => Storage::url($inst->payment_voucher)
                                    ]) }})"
                                    class="text-[10px] font-black uppercase tracking-widest text-purple-600 border-b-2 border-purple-600 pb-0.5 hover:opacity-60 transition-opacity">
                                        Revisar
                                    </button>
                                @elseif($inst->status === 'approved')
                                    <form action="{{ route('superadmin.installments.unapprove', $inst) }}" method="POST"
                                        onsubmit="return confirm('¿Revertir aprobación de este pago?')">
                                        @csrf
                                        <button type="submit"
                                            class="text-[10px] font-black uppercase tracking-widest text-gray-400 border-b-2 border-gray-300 dark:border-gray-800 pb-0.5 hover:text-orange-500 hover:border-orange-500 transition-all">
                                            Revertir
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            @empty
                <div class="py-24 text-center">
                    <p class="text-sm text-gray-400 dark:text-gray-500 font-medium uppercase tracking-widest">No hay cuotas registradas</p>
                </div>
            @endforelse
        </div>

        <!-- Paginación -->
        <div class="mt-12 flex justify-center">
            {{ $installments->links() }}
        </div>
    </div>

    <!-- Modal Validación de Pago -->
    <div id="validateModal"
        class="hidden fixed inset-0 bg-black/80 backdrop-blur-xl z-[100] flex items-center justify-center p-4">
        <div
            class="bg-white dark:bg-gray-950 w-full max-w-5xl max-h-[90vh] rounded-[3rem] shadow-2xl relative flex flex-col md:flex-row overflow-hidden">

            <!-- Voucher (Izquierda) -->
            <div class="w-full md:w-3/5 bg-gray-50 dark:bg-gray-900 p-6 md:p-10 flex flex-col min-h-0">
                <div class="flex items-center justify-between mb-6">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Comprobante de Pago</p>
                    <a id="modal_voucher_link" href="" target="_blank"
                        class="p-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 text-gray-500 hover:text-purple-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                </div>
                <div class="flex-1 min-h-0 rounded-[2rem] overflow-hidden border border-gray-100 dark:border-gray-800 bg-white dark:bg-black shadow-inner">
                    <img id="modal_voucher_img" src="" class="w-full h-full object-contain">
                </div>
            </div>

            <!-- Acciones (Derecha) -->
            <div class="w-full md:w-2/5 p-8 md:p-12 flex flex-col relative">
                <button onclick="closeValidateModal()"
                    class="absolute top-8 right-8 w-10 h-10 rounded-full bg-gray-50 dark:bg-gray-900 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="mb-12">
                    <span class="px-3 py-1 bg-purple-100 text-purple-600 dark:bg-purple-900/20 dark:text-purple-400 text-[8px] font-black uppercase tracking-[0.2em] rounded-full mb-4 inline-block">Proceso de Validación</span>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight mb-4">Revisar Pago</h3>
                    <div id="modal_info_text" class="space-y-2"></div>
                </div>

                <div class="mt-auto space-y-6">
                    <!-- Rechazar -->
                    <form id="rejectForm" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-3">Motivo del rechazo</label>
                            <textarea name="reason" required placeholder="Ej: Monto incorrecto o imagen borrosa..."
                                class="w-full bg-gray-50 dark:bg-gray-900 rounded-2xl p-4 text-sm border-2 border-transparent focus:border-red-500 focus:bg-white dark:focus:bg-gray-950 transition-all resize-none h-24 outline-none"></textarea>
                        </div>
                        <button type="submit"
                            class="w-full py-4 text-[10px] font-black uppercase tracking-[0.2em] text-red-500 bg-red-50 dark:bg-red-900/10 rounded-2xl hover:bg-red-100 dark:hover:bg-red-900/20 transition-all">
                            Rechazar y Notificar
                        </button>
                    </form>

                    <div class="relative flex items-center gap-4">
                        <div class="flex-1 h-px bg-gray-100 dark:bg-gray-900"></div>
                        <span class="text-[10px] font-black text-gray-300 uppercase">o</span>
                        <div class="flex-1 h-px bg-gray-100 dark:bg-gray-900"></div>
                    </div>

                    <!-- Aprobar -->
                    <form id="approveForm" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full py-5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl transition-all shadow-xl shadow-gray-900/20 dark:shadow-none">
                            Confirmar Aprobación
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
