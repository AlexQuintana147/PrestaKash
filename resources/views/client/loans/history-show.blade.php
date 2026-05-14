@extends('client.layout')

@section('title', 'Detalle del Préstamo')

@section('content')
    <div class="min-h-screen px-4 md:px-10 py-6 md:py-8 max-w-5xl mx-auto">

        <!-- Header -->
        <div class="mb-10">
            <a href="{{ route('client.loans.history') }}"
                class="inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors mb-6">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
                Volver
            </a>

            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Préstamo
                        #{{ str_pad($loan->id, 4, '0', STR_PAD_LEFT) }}</h1>
                    <p class="text-xs text-gray-500 mt-1 uppercase tracking-widest font-medium">
                        Solicitado el {{ $loan->created_at->format('d/m/Y') }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span
                        class="px-3 py-1 text-[10px] font-black uppercase tracking-widest border border-gray-100 dark:border-gray-800 text-gray-500 rounded-full">
                        {{ $loan->type === 'cuotas' ? 'Cuotas' : 'Único' }}
                    </span>
                    <span class="px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-full
                            {{ $loan->status === 'approved' ? 'bg-green-500/10 text-green-600' : '' }}
                            {{ $loan->status === 'paid' ? 'bg-blue-500/10 text-blue-600' : '' }}
                            {{ $loan->status === 'pending' ? 'bg-yellow-500/10 text-yellow-600' : '' }}">
                        {{ match ($loan->status) {
        'approved' => 'Activo',
        'paid' => 'Completado',
        'pending' => 'En revisión',
        default => $loan->status
    } }}
                    </span>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-8 flex items-center gap-3 text-xs font-bold text-green-500 uppercase tracking-widest">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

            <!-- Left: Details -->
            <div class="lg:col-span-1 space-y-10">

                <!-- Resumen Monetario -->
                <div class="space-y-4">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Resumen</p>
                    <div class="space-y-3">
                        <div class="flex justify-between items-end">
                            <span class="text-xs text-gray-500">Principal</span>
                            <span
                                class="text-lg font-bold text-gray-900 dark:text-white tabular-nums">{{ $loan->currency === 'USD' ? '$' : 'S/' }}
                                {{ number_format($loan->amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-end">
                            <span class="text-xs text-gray-500">Intereses
                                ({{ number_format($loan->interest_rate, 2) }}%)</span>
                            <span class="text-xs font-bold text-green-600 tabular-nums">+
                                {{ $loan->currency === 'USD' ? '$' : 'S/' }}
                                {{ number_format($loan->total_amount - $loan->amount, 2) }}</span>
                        </div>
                        <div class="pt-3 border-t border-gray-100 dark:border-gray-900 flex justify-between items-end">
                            <span
                                class="text-xs font-black uppercase tracking-widest text-gray-900 dark:text-white">Total</span>
                            <span
                                class="text-xl font-black text-gray-900 dark:text-white tabular-nums">{{ $loan->currency === 'USD' ? '$' : 'S/' }}
                                {{ number_format($loan->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Info Técnica -->
                <div class="space-y-6">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Información</p>
                    <div class="grid grid-cols-1 gap-5">
                        <div class="flex justify-between">
                            <span class="text-xs text-gray-500">Desembolso</span>
                            <span
                                class="text-xs font-bold text-gray-900 dark:text-white">{{ $loan->disbursement_date ? \Carbon\Carbon::parse($loan->disbursement_date)->format('d/m/Y') : '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs text-gray-500">Sistema</span>
                            <span
                                class="text-xs font-bold text-gray-900 dark:text-white capitalize">{{ $loan->amortization_system }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs text-gray-500">Frecuencia</span>
                            <span
                                class="text-xs font-bold text-gray-900 dark:text-white capitalize">{{ $loan->payment_frequency }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-xs text-gray-500 flex-shrink-0">Banco</span>
                            <span
                                class="text-xs font-bold text-gray-900 dark:text-white text-right">{{ $loan->bank_name }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-xs text-gray-500 flex-shrink-0">Cuenta</span>
                            <span
                                class="text-[10px] font-mono font-bold text-gray-900 dark:text-white text-right">{{ $loan->account_number }}</span>
                        </div>
                        @if($loan->cci)
                            <div class="flex justify-between gap-4">
                                <span class="text-xs text-gray-500 flex-shrink-0">CCI</span>
                                <span
                                    class="text-[10px] font-mono font-bold text-gray-900 dark:text-white text-right">{{ $loan->cci }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Motivo -->
                <div class="space-y-3">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Motivo</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 italic leading-relaxed">
                        "{{ $loan->reason }}"
                    </p>
                </div>
            </div>

            <!-- Right: Installments -->
            <div class="lg:col-span-2 space-y-8">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-black uppercase tracking-[0.2em] text-gray-900 dark:text-white">Cronograma</h2>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        @php
                            $total_i = $loan->installments->count();
                            $paid_i = $loan->installments->where('status', 'approved')->count();
                            $hasAnyProcessing = $loan->installments->where('status', 'processing')->count() > 0;
                            $firstNonApproved = $loan->installments->where('status', '!=', 'approved')->sortBy('installment_number')->first();
                        @endphp
                        {{ $paid_i }} / {{ $total_i }} Completados
                    </span>
                </div>

                <div class="overflow-hidden">
                    <table class="w-full text-left">
                        <thead
                            class="text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100 dark:border-gray-900">
                            <tr>
                                <th class="py-4 pr-4">N°</th>
                                <th class="py-4 px-4">Vencimiento</th>
                                <th class="py-4 px-4">Cuota</th>
                                <th class="py-4 pl-4 text-right">Estado / Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-900/50">
                            @foreach($loan->installments as $inst)
                                                    <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-900/20 transition-colors">
                                                        <td
                                                            class="py-5 pr-4 text-xs font-black text-gray-400 group-hover:text-green-500 transition-colors">
                                                            {{ $inst->installment_number }}</td>
                                                        <td class="py-5 px-4 text-xs font-bold text-gray-700 dark:text-gray-300">
                                                            {{ $inst->due_date->format('d/m/Y') }}</td>
                                                        <td class="py-5 px-4 text-xs font-black text-gray-900 dark:text-white tabular-nums">
                                                            {{ $loan->currency === 'USD' ? '$' : 'S/' }} {{ number_format($inst->amount, 2) }}</td>
                                                        <td class="py-5 pl-4 text-right">
                                                            <div class="flex items-center justify-end gap-4">
                                                                <div class="flex flex-col items-end gap-1">
                                                                    <span class="text-[9px] font-black uppercase tracking-widest
                                                                                {{ $inst->status === 'approved' ? 'text-green-500' : '' }}
                                                                                {{ $inst->status === 'pending' ? 'text-gray-400' : '' }}
                                                                                {{ $inst->status === 'processing' ? 'text-yellow-500' : '' }}
                                                                                {{ $inst->status === 'rejected' ? 'text-red-500' : '' }}">
                                                                        {{ match ($inst->status) {
                                                                            'approved' => 'Pagado',
                                                                            'pending' => 'Pendiente',
                                                                            'processing' => 'Revisión',
                                                                            'rejected' => 'Rechazado',
                                                                            default => $inst->status
                                                                        } }}
                                                                    </span>
                                                                    @if($inst->status === 'rejected' && $inst->admin_notes)
                                                                        <p class="text-[8px] text-red-500 font-bold max-w-[120px] leading-tight text-right italic">
                                                                            "{{ $inst->admin_notes }}"
                                                                        </p>
                                                                    @endif
                                                                </div>

                                                                @if($inst->status === 'pending' || $inst->status === 'rejected')
                                                                    @php $canPay = !$hasAnyProcessing && ($firstNonApproved && $firstNonApproved->id === $inst->id); @endphp
                                                                    @if($canPay)
                                                                        <button type="button"
                                                                            onclick="openUploadModal({{ $inst->id }}, '{{ $loan->currency === 'USD' ? '$' : 'S/' }}{{ number_format($inst->amount, 2) }}')"
                                                                            class="text-[9px] font-black uppercase tracking-widest text-gray-900 dark:text-white border-b-2 border-gray-900 dark:border-white pb-0.5 hover:opacity-60 transition-opacity">
                                                                            Pagar
                                                                        </button>
                                                                    @endif
                                                                @elseif($inst->payment_voucher)
                                                                    <a href="{{ Storage::url($inst->payment_voucher) }}" target="_blank"
                                                                        class="text-gray-400 hover:text-green-500 transition-colors">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                        </svg>
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Adjuntar Comprobante -->
    <div id="uploadModal"
        class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
        <div
            class="bg-white dark:bg-gray-950 w-full max-w-sm rounded-3xl shadow-2xl relative animate-in fade-in zoom-in duration-200 p-8">
            <button onclick="closeUploadModal()"
                class="absolute top-6 right-6 text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="text-center mb-8">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight">Confirmar Pago</h3>
                <p class="text-xs text-gray-500 mt-1 uppercase tracking-widest font-medium">Monto: <span
                        id="modal_amount_text" class="text-gray-900 dark:text-white font-black"></span></p>
            </div>

            <form id="uploadForm" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                <div class="relative group">
                    <label for="payment_voucher" class="cursor-pointer block">
                        <div
                            class="border-2 border-dashed border-gray-100 dark:border-gray-900 rounded-2xl p-8 text-center group-hover:border-green-500 transition-all">
                            <div id="previewContainer" class="hidden mb-4">
                                <img id="voucherPreview" class="w-full h-32 object-contain rounded-xl mx-auto">
                            </div>
                            <div id="uploadPlaceholder">
                                <svg class="w-8 h-8 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Subir Boucher</p>
                            </div>
                        </div>
                        <input type="file" name="payment_voucher" id="payment_voucher" class="hidden" accept="image/*"
                            onchange="previewImage(this)">
                    </label>
                </div>

                <button type="submit"
                    class="w-full py-4 text-[10px] font-black uppercase tracking-[0.2em] text-purple-600 hover:text-purple-400 border-b-2 border-purple-600 hover:border-purple-400 transition-all">
                    Enviar para Verificación
                </button>
            </form>
        </div>
    </div>

    <script>
        function openUploadModal(instId, amount) {
            document.getElementById('modal_amount_text').textContent = amount;
            document.getElementById('uploadForm').action = `/panel/historial-crediticio/pagar/${instId}`;
            document.getElementById('uploadModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeUploadModal() {
            document.getElementById('uploadModal').classList.add('hidden');
            document.body.style.overflow = '';
            document.getElementById('uploadForm').reset();
            document.getElementById('previewContainer').classList.add('hidden');
            document.getElementById('uploadPlaceholder').classList.remove('hidden');
        }

        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('voucherPreview').src = e.target.result;
                    document.getElementById('previewContainer').classList.remove('hidden');
                    document.getElementById('uploadPlaceholder').classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        document.getElementById('uploadModal').addEventListener('click', e => {
            if (e.target === document.getElementById('uploadModal')) closeUploadModal();
        });
    </script>
@endsection