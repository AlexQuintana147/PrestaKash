@extends('client.layout')

@section('title', 'Detalle del Préstamo')

@section('content')
    <div class="min-h-screen px-5 md:px-10 py-8">

        <!-- Header -->
        <div class="mb-12 flex flex-col md:flex-row md:items-start justify-between gap-6">
            <div>
                <a href="{{ route('client.loans.history') }}"
                    class="inline-flex items-center gap-2 text-xs font-semibold text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors mb-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver al historial
                </a>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Detalle de Préstamo
                </h1>
                <p class="text-sm text-gray-500 mt-1">ID: #{{ str_pad($loan->id, 6, '0', STR_PAD_LEFT) }} —
                    {{ $loan->currency === 'USD' ? '$' : 'S/' }} {{ number_format($loan->amount, 2) }}</p>
            </div>

            <div class="flex items-center gap-3">
                <span
                    class="px-4 py-2 rounded-2xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 text-[10px] font-black uppercase tracking-widest text-gray-500">
                    {{ $loan->type === 'cuotas' ? 'Pago en Cuotas' : 'Pago Único' }}
                </span>
                <span class="px-4 py-2 rounded-2xl text-[10px] font-black uppercase tracking-widest
                        {{ $loan->status === 'approved' ? 'bg-green-50 dark:bg-green-900/20 text-green-600' : '' }}
                        {{ $loan->status === 'paid' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600' : '' }}
                        {{ $loan->status === 'pending' ? 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600' : '' }}">
                    {{ match ($loan->status) {
        'approved' => 'Activo',
        'paid' => 'Completado',
        'pending' => 'En revisión',
        default => $loan->status
    } }}
                </span>
            </div>
        </div>

        @if(session('success'))
            <div
                class="mb-8 p-4 bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-900/40 rounded-3xl flex items-center gap-3 text-sm text-green-600 dark:text-green-400 animate-in slide-in-from-top-4 duration-500">
                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

            <!-- Cronograma de Pagos -->
            <div class="lg:col-span-8">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white tracking-tight">Cronograma de Pagos</h2>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                        @php
                            $total_i = $loan->installments->count();
                            if ($total_i === 0) {
                                $installmentsPerMonth = match ($loan->payment_frequency) {
                                    'diario' => 30,
                                    'semanal' => 4,
                                    'quincenal' => 2,
                                    default => 1,
                                };
                                $total_i = $loan->months * $installmentsPerMonth;
                            }
                            $paid_i = $loan->installments->where('status', 'approved')->count();
                        @endphp
                        {{ $paid_i }} de {{ $total_i }} Pagados
                    </p>
                </div>

                <div class="space-y-4">
                    @foreach($loan->installments as $inst)
                                    <div
                                        class="group bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-[2rem] p-6 hover:shadow-xl hover:shadow-gray-200/20 dark:hover:shadow-none transition-all duration-300 relative overflow-hidden">
                                        @if($inst->status === 'approved')
                                            <div class="absolute top-0 right-0 w-32 h-32 bg-green-500/5 rounded-full -mr-16 -mt-16"></div>
                                        @endif

                                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 relative z-10">

                                            <!-- Cuota Info -->
                                            <div class="flex items-center gap-6">
                                                <div
                                                    class="w-12 h-12 rounded-2xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center text-sm font-black text-gray-400 group-hover:bg-green-500 group-hover:text-white transition-colors duration-300">
                                                    {{ $inst->installment_number }}
                                                </div>
                                                <div>
                                                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest mb-0.5">
                                                        Vencimiento</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">
                                                        {{ $inst->due_date->format('d M, Y') }}</p>
                                                </div>
                                            </div>

                                            <!-- Monto -->
                                            <div>
                                                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest mb-0.5">Cuota</p>
                                                <p class="text-lg font-black text-gray-900 dark:text-white">
                                                    {{ $loan->currency === 'USD' ? '$' : 'S/' }} {{ number_format($inst->amount, 2) }}
                                                </p>
                                            </div>

                                            <!-- Estado y Acciones -->
                                            <div class="flex items-center gap-6">
                                                <div class="text-right">
                                                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest mb-1">Estado</p>
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest
                                                                {{ $inst->status === 'approved' ? 'bg-green-100 text-green-600 dark:bg-green-500/10' : '' }}
                                                                {{ $inst->status === 'pending' ? 'bg-gray-100 text-gray-400 dark:bg-gray-900' : '' }}
                                                                {{ $inst->status === 'processing' ? 'bg-yellow-100 text-yellow-600 dark:bg-yellow-500/10' : '' }}
                                                                {{ $inst->status === 'rejected' ? 'bg-red-100 text-red-600 dark:bg-red-500/10' : '' }}">
                                                        {{ match ($inst->status) {
                            'approved' => 'Pagado',
                            'pending' => 'Pendiente',
                            'processing' => 'En revisión',
                            'rejected' => 'Rechazado',
                            default => $inst->status
                        } }}
                                                    </span>
                                                </div>

                                                @if($inst->status === 'pending' || $inst->status === 'rejected')
                                                    <button type="button"
                                                        onclick="openUploadModal({{ $inst->id }}, '{{ $loan->currency === 'USD' ? '$' : 'S/' }}{{ number_format($inst->amount, 2) }}')"
                                                        class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:scale-105 transition-all shadow-lg shadow-gray-900/10 dark:shadow-none">
                                                        Pagar ahora
                                                    </button>
                                                @elseif($inst->payment_voucher)
                                                    <a href="{{ Storage::url($inst->payment_voucher) }}" target="_blank"
                                                        class="w-12 h-12 rounded-2xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center text-gray-400 hover:text-green-500 hover:bg-green-50 dark:hover:bg-green-500/10 transition-all">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Resumen y Detalles -->
            <div class="lg:col-span-4 space-y-8">

                <!-- Card Resumen -->
                <div
                    class="bg-gray-900 dark:bg-white rounded-[2.5rem] p-8 text-white dark:text-gray-900 shadow-2xl shadow-gray-900/20">
                    <h3 class="text-sm font-bold uppercase tracking-[0.2em] opacity-60 mb-8">Resumen Financiero</h3>

                    <div class="space-y-6">
                        <div class="flex justify-between items-end border-b border-white/10 dark:border-gray-100 pb-4">
                            <p class="text-xs opacity-60">Total Prestado</p>
                            <p class="text-xl font-black tracking-tighter">{{ $loan->currency === 'USD' ? '$' : 'S/' }}
                                {{ number_format($loan->amount, 2) }}</p>
                        </div>
                        <div class="flex justify-between items-end border-b border-white/10 dark:border-gray-100 pb-4">
                            <p class="text-xs opacity-60">Interés Total ({{ number_format($loan->interest_rate, 2) }}%)</p>
                            <p class="text-xl font-black tracking-tighter text-green-400 dark:text-green-600">+
                                {{ $loan->currency === 'USD' ? '$' : 'S/' }}
                                {{ number_format($loan->total_amount - $loan->amount, 2) }}</p>
                        </div>
                        <div class="flex justify-between items-end pt-2">
                            <p class="text-xs font-bold uppercase tracking-widest">Total a Pagar</p>
                            <p class="text-3xl font-black tracking-tighter">{{ $loan->currency === 'USD' ? '$' : 'S/' }}
                                {{ number_format($loan->total_amount, 2) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Card Info Adicional -->
                <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-[2.5rem] p-8">
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-6">Detalles del Préstamo
                    </h3>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold mb-1">Motivo</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300 italic">"{{ $loan->reason }}"</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-bold mb-1">Sistema</p>
                                <p class="text-xs font-bold text-gray-700 dark:text-gray-300 capitalize">
                                    {{ $loan->amortization_system }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-bold mb-1">Frecuencia</p>
                                <p class="text-xs font-bold text-gray-700 dark:text-gray-300 capitalize">
                                    {{ $loan->payment_frequency }}</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold mb-1">Destino</p>
                            <p class="text-xs font-bold text-gray-900 dark:text-white uppercase">{{ $loan->bank_name }}</p>
                            <p class="text-[10px] font-mono text-gray-500">{{ $loan->account_number }}</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- Modal Adjuntar Comprobante -->
    <div id="uploadModal"
        class="hidden fixed inset-0 bg-black/80 backdrop-blur-md z-[100] flex items-center justify-center p-4">
        <div
            class="bg-white dark:bg-gray-950 w-full max-w-md rounded-[3rem] shadow-2xl relative animate-in fade-in zoom-in duration-300 overflow-hidden">

            <!-- Header Modal -->
            <div class="bg-gray-50 dark:bg-gray-900 p-8 pb-12">
                <button onclick="closeUploadModal()"
                    class="absolute top-8 right-8 p-3 rounded-2xl bg-white dark:bg-gray-950 shadow-lg text-gray-400 hover:text-red-500 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="flex items-center gap-4 mb-2">
                    <div
                        class="w-12 h-12 bg-green-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-green-500/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight">Adjuntar Pago</h3>
                        <p class="text-xs text-gray-500">Confirma tu depósito enviando el boucher</p>
                    </div>
                </div>
            </div>

            <!-- Body Modal -->
            <div class="p-8 -mt-6 bg-white dark:bg-gray-950 rounded-t-[3rem]">
                <div
                    class="mb-8 p-6 bg-gray-50 dark:bg-gray-900 rounded-3xl flex justify-between items-center border border-gray-100 dark:border-gray-800">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Monto a confirmar</span>
                    <span id="modal_amount_text"
                        class="text-2xl font-black text-gray-900 dark:text-white tracking-tighter"></span>
                </div>

                <form id="uploadForm" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    <div class="relative group">
                        <label for="payment_voucher" class="cursor-pointer block">
                            <div
                                class="border-3 border-dashed border-gray-100 dark:border-gray-800 rounded-[2.5rem] p-12 text-center group-hover:border-green-500 group-hover:bg-green-50/30 dark:group-hover:bg-green-500/5 transition-all duration-300">
                                <div id="previewContainer" class="hidden mb-4">
                                    <img id="voucherPreview"
                                        class="w-full h-48 object-contain rounded-2xl mx-auto shadow-xl">
                                </div>
                                <div id="uploadPlaceholder">
                                    <div
                                        class="w-16 h-16 bg-gray-50 dark:bg-gray-900 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                        <svg class="w-8 h-8 text-gray-300 group-hover:text-green-500" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Toca para
                                        seleccionar boucher</p>
                                    <p class="text-[9px] text-gray-300 mt-2">JPG, PNG o WEBP (Máx. 5MB)</p>
                                </div>
                            </div>
                            <input type="file" name="payment_voucher" id="payment_voucher" class="hidden" accept="image/*"
                                onchange="previewImage(this)">
                        </label>
                    </div>

                    <button type="submit"
                        class="w-full py-5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-[11px] font-black uppercase tracking-[0.2em] rounded-[1.5rem] hover:scale-[1.02] active:scale-95 transition-all shadow-xl shadow-gray-900/20 dark:shadow-none">
                        Enviar para Verificación
                    </button>
                </form>
            </div>
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