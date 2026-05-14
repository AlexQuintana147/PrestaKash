@extends('superadmin.layout')

@section('title', 'Cobranzas')

@section('content')
    <div class="px-5 md:px-10 py-8">

        <!-- Header -->
        <div class="flex items-end justify-between mb-8">
            <div>
                <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-1">Panel Administrativo</p>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Cobranzas</h1>
                <p class="text-[10px] text-gray-500 uppercase font-medium mt-1">Gestión de pagos y validación de comprobantes</p>
            </div>
            <p class="hidden md:block text-sm text-gray-600 dark:text-gray-400">{{ now()->format('d M Y') }}</p>
        </div>

        @if(session('success'))
            <div class="mb-6 flex items-center gap-3 text-sm text-green-500 uppercase font-black tracking-widest">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Filtros -->
        <div
            class="flex items-center gap-6 border-b border-gray-100 dark:border-gray-900 mb-8 overflow-x-auto no-scrollbar">
            <a href="{{ route('superadmin.installments.index', ['filter' => 'all']) }}"
                class="pb-4 text-xs font-black uppercase tracking-widest transition-all relative {{ $filter === 'all' ? 'text-gray-900 dark:text-white' : 'text-gray-400' }}">
                Todos los Activos
                @if($filter === 'all') <span class="absolute bottom-0 left-0 w-full h-0.5 bg-gray-900 dark:bg-white"></span>
                @endif
            </a>
            <a href="{{ route('superadmin.installments.index', ['filter' => 'pending_validation']) }}"
                class="pb-4 text-xs font-black uppercase tracking-widest transition-all relative {{ $filter === 'pending_validation' ? 'text-purple-600' : 'text-gray-400' }}">
                Por Validar
                @if($filter === 'pending_validation') <span
                class="absolute bottom-0 left-0 w-full h-0.5 bg-purple-600"></span> @endif
            </a>
        </div>

        <!-- Lista de Préstamos Aprobados -->
        <div class="space-y-6">
            @forelse($loans as $loan)
                <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-[2rem] p-6 md:p-8">

                    <!-- Info del Préstamo -->
                    <div
                        class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8 pb-6 border-b border-gray-50 dark:border-gray-900">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-900 flex items-center justify-center overflow-hidden border border-gray-100 dark:border-gray-800">
                                @php
                                    $selfie = $loan->user->documents?->selfie;
                                    $photo = $selfie ? Storage::url($selfie) : ($loan->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($loan->user->name));
                                @endphp
                                <img src="{{ $photo }}" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">
                                    {{ $loan->user->name }}</h3>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">ID:
                                        #{{ str_pad($loan->id, 4, '0', STR_PAD_LEFT) }} •
                                        {{ $loan->currency === 'USD' ? '$' : 'S/' }} {{ number_format($loan->amount, 2) }}</p>
                                    
                                    @php
                                        $hasDebt = $loan->installments->where('status', '!=', 'approved')->where('due_date', '<', now()->startOfDay())->count() > 0;
                                    @endphp
                                    
                                    @if($hasDebt)
                                        <span class="px-1.5 py-0.5 bg-red-100 text-red-600 text-[8px] font-black rounded uppercase">Con Deuda</span>
                                    @else
                                        <span class="px-1.5 py-0.5 bg-green-100 text-green-600 text-[8px] font-black rounded uppercase">Al día</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:flex items-center gap-8">
                            <div class="text-center md:text-right">
                                <p class="text-[9px] text-gray-400 uppercase font-black tracking-widest mb-1">Próximo Pago</p>
                                @php
                                    $next = $loan->installments->where('status', '!=', 'approved')->sortBy('due_date')->first();
                                @endphp
                                <p
                                    class="text-xs font-bold {{ ($next && $next->due_date->isPast()) ? 'text-red-500' : 'text-gray-900 dark:text-white' }}">
                                    {{ $next ? $next->due_date->format('d/m/Y') : 'Completado' }}
                                </p>
                            </div>
                            <div class="text-center md:text-right">
                                <p class="text-[9px] text-gray-400 uppercase font-black tracking-widest mb-1">Progreso</p>
                                @php
                                    $total = $loan->installments->count();
                                    $paid = $loan->installments->where('status', 'approved')->count();
                                    $percent = ($paid / ($total ?: 1)) * 100;
                                @endphp
                                <div class="flex items-center gap-2 justify-end">
                                    <span
                                        class="text-[10px] font-black text-gray-900 dark:text-white">{{ $paid }}/{{ $total }}</span>
                                    <div class="w-16 h-1.5 bg-gray-100 dark:bg-gray-900 rounded-full overflow-hidden">
                                        <div class="h-full bg-green-500" style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cuotas -->
                    <div class="overflow-x-auto no-scrollbar">
                        <table class="w-full text-left">
                            <thead class="text-[10px] font-black uppercase tracking-widest text-gray-400">
                                <tr>
                                    <th class="pb-4 pr-4">N°</th>
                                    <th class="pb-4 px-4 text-center">Vencimiento</th>
                                    <th class="pb-4 px-4 text-center">Monto</th>
                                    <th class="pb-4 pl-4 text-right">Estado / Validación</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-900/50">
                                @foreach($loan->installments->sortBy('installment_number') as $inst)
                                                    <tr class="group">
                                                        <td class="py-4 pr-4 text-xs font-black text-gray-400">{{ $inst->installment_number }}</td>
                                                        <td
                                                            class="py-4 px-4 text-xs font-bold text-center {{ $inst->due_date->isPast() && $inst->status !== 'approved' ? 'text-red-500' : 'text-gray-600 dark:text-gray-400' }}">
                                                            {{ $inst->due_date->format('d/m/Y') }}
                                                        </td>
                                                        <td
                                                            class="py-4 px-4 text-xs font-black text-center text-gray-900 dark:text-white tabular-nums">
                                                            {{ $loan->currency === 'USD' ? '$' : 'S/' }} {{ number_format($inst->amount, 2) }}
                                                        </td>
                                                        <td class="py-4 pl-4 text-right">
                                                            <div class="flex items-center justify-end gap-4">
                                                                <span class="text-[9px] font-black uppercase tracking-widest
                                                                                {{ $inst->status === 'approved' ? 'text-green-500' : '' }}
                                                                                {{ $inst->status === 'pending' ? 'text-gray-400' : '' }}
                                                                                {{ $inst->status === 'processing' ? 'text-purple-600 animate-pulse' : '' }}
                                                                                {{ $inst->status === 'rejected' ? 'text-red-500' : '' }}">
                                                                    {{ match ($inst->status) {
                                        'approved' => 'Pagado',
                                        'pending' => 'Pendiente',
                                        'processing' => 'Validar Pago',
                                        'rejected' => 'Rechazado',
                                        default => $inst->status
                                    } }}
                                                                </span>

                                                                @if($inst->status === 'processing')
                                                                                            <button type="button" onclick="openValidateModal({{ json_encode([
                                                                        'id' => $inst->id,
                                                                        'amount' => ($loan->currency === 'USD' ? '$' : 'S/') . number_format($inst->amount, 2),
                                                                        'user' => $loan->user->name,
                                                                        'number' => $inst->installment_number,
                                                                        'voucher' => Storage::url($inst->payment_voucher)
                                                                    ]) }})"
                                                                                                class="text-[9px] font-black uppercase tracking-widest text-purple-600 border-b-2 border-purple-600 pb-0.5 hover:opacity-60 transition-opacity">
                                                                                                Revisar
                                                                                            </button>
                                                                @elseif($inst->status === 'approved')
                                                                    <form action="{{ route('superadmin.installments.unapprove', $inst) }}" method="POST"
                                                                        onsubmit="return confirm('¿Revertir aprobación?')">
                                                                        @csrf
                                                                        <button type="submit"
                                                                            class="text-[9px] font-black uppercase tracking-widest text-gray-400 hover:text-orange-500 transition-colors">
                                                                            Desaprobar
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="py-20 text-center">
                    <p class="text-sm text-gray-400 dark:text-gray-600 italic">No hay registros de cobranza para mostrar.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-10">
            {{ $loans->links() }}
        </div>
    </div>

    <!-- Modal Validación de Pago -->
    <div id="validateModal"
        class="hidden fixed inset-0 bg-black/80 backdrop-blur-md z-[100] flex items-center justify-center p-4">
        <div
            class="bg-white dark:bg-gray-950 w-full max-w-4xl max-h-[90vh] rounded-[3rem] shadow-2xl relative animate-in fade-in zoom-in duration-300 flex flex-col md:flex-row overflow-hidden">

            <!-- Boucher (Izquierda/Arriba) -->
            <div class="w-full md:w-1/2 bg-gray-50 dark:bg-gray-900 p-8 flex flex-col min-h-0">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-6">Comprobante de Pago</p>
                <div class="flex-1 min-h-0 rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-800">
                    <img id="modal_voucher_img" src="" class="w-full h-full object-contain">
                </div>
                <div class="mt-4 flex justify-center">
                    <a id="modal_voucher_link" href="" target="_blank"
                        class="text-[10px] font-bold text-purple-600 uppercase tracking-widest hover:underline">Ver tamaño
                        completo</a>
                </div>
            </div>

            <!-- Acciones (Derecha/Abajo) -->
            <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
                <button onclick="closeValidateModal()"
                    class="absolute top-8 right-8 text-gray-400 hover:text-red-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="mb-10">
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight mb-2">Validar Pago</h3>
                    <p id="modal_info_text"
                        class="text-sm text-gray-500 leading-relaxed uppercase tracking-wide font-medium"></p>
                </div>

                <div class="space-y-8">
                    <!-- Rechazar -->
                    <form id="rejectForm" method="POST" class="space-y-4">
                        @csrf
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Si rechazas,
                            indica el motivo:</label>
                        <textarea name="reason" required placeholder="Ej: Monto incorrecto, imagen no legible..."
                            class="w-full bg-transparent border-0 border-b-2 border-gray-100 dark:border-gray-800 py-3 text-sm focus:outline-none focus:border-red-500 transition-colors resize-none"></textarea>
                        <button type="submit"
                            class="w-full py-4 text-[10px] font-black uppercase tracking-[0.2em] text-red-500 border-b-2 border-red-500 hover:bg-red-50 dark:hover:bg-red-900/10 transition-all">
                            Rechazar Pago
                        </button>
                    </form>

                    <div class="flex items-center gap-4 py-4">
                        <div class="flex-1 h-px bg-gray-100 dark:bg-gray-900"></div>
                        <span class="text-[10px] font-black text-gray-300 uppercase">o</span>
                        <div class="flex-1 h-px bg-gray-100 dark:bg-gray-900"></div>
                    </div>

                    <!-- Aprobar -->
                    <form id="approveForm" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full py-5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-xs font-black uppercase tracking-[0.2em] rounded-2xl hover:scale-[1.02] active:scale-95 transition-all shadow-xl shadow-gray-900/20 dark:shadow-none">
                            Aprobar Pago
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
            document.getElementById('modal_info_text').innerHTML = `Cuota #${data.number} de ${data.amount}<br><span class="text-gray-900 dark:text-white">${data.user}</span>`;

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