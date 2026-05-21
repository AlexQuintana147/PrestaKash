@extends('client.layout')

@section('title', $opportunity->title)

@section('content')
<div class="min-h-screen px-4 sm:px-6 lg:px-8 py-6 sm:py-10 max-w-4xl mx-auto">

    <div class="mb-6 sm:mb-8">
        <a href="{{ route('client.investments.index') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors group">
            <span class="group-hover:-translate-x-0.5 transition-transform">←</span>
            Volver a Mis Inversiones
        </a>
        <h1 class="mt-3 sm:mt-4 text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white tracking-tight leading-tight">{{ $opportunity->title }}</h1>
        <div class="flex items-center gap-2 mt-2">
            <span class="text-[10px] sm:text-xs font-mono text-gray-400">{{ $opportunity->code }}</span>
            <span class="px-2 py-0.5 text-[8px] sm:text-[9px] font-black uppercase tracking-widest bg-green-100 text-green-600 dark:bg-green-500/10 dark:text-green-400 rounded">Activa</span>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-500/10 rounded-2xl border border-red-100 dark:border-red-500/20">
            <p class="text-sm text-red-600 dark:text-red-400 font-medium">{{ session('error') }}</p>
        </div>
    @endif

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-500/10 rounded-2xl border border-green-100 dark:border-green-500/20">
            <p class="text-sm text-green-600 dark:text-green-400 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8">
        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl p-3 sm:p-4 shadow-sm">
            <span class="text-[9px] sm:text-[10px] text-gray-400 uppercase font-bold tracking-tight">Monto Objetivo</span>
            <p class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mt-2">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($opportunity->target_amount, 2) }}</p>
        </div>

        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl p-3 sm:p-4 shadow-sm">
            <span class="text-[9px] sm:text-[10px] text-gray-400 uppercase font-bold tracking-tight">Retorno</span>
            <p class="text-base sm:text-lg font-bold text-green-600 dark:text-green-400 mt-2">{{ $opportunity->monthly_return_rate }}%/mes</p>
        </div>

        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl p-3 sm:p-4 shadow-sm">
            <span class="text-[9px] sm:text-[10px] text-gray-400 uppercase font-bold tracking-tight">Plazo</span>
            <p class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mt-2">{{ $opportunity->term_months }} meses</p>
        </div>

        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl p-3 sm:p-4 shadow-sm">
            <span class="text-[9px] sm:text-[10px] text-gray-400 uppercase font-bold tracking-tight">Min Inversión</span>
            <p class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mt-2">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($opportunity->min_investment, 2) }}</p>
        </div>
    </div>

    <div class="mb-6 sm:mb-8">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Progreso de Financiamiento</span>
            <span class="text-xs sm:text-sm font-bold text-indigo-600 dark:text-indigo-400">{{ $opportunity->progress_percent }}%</span>
        </div>
        <div class="relative h-2 sm:h-3 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
            <div class="absolute inset-y-0 left-0 rounded-full bg-indigo-500" style="width: {{ min(100, $opportunity->progress_percent) }}%"></div>
        </div>
        <div class="flex items-center justify-between mt-2">
            <span class="text-[10px] sm:text-xs text-gray-400">Recaudado: {{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($opportunity->target_amount - $opportunity->remaining_amount, 2) }}</span>
            <span class="text-[10px] sm:text-xs text-gray-400">Disponible: {{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($opportunity->remaining_amount, 2) }}</span>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl p-4 sm:p-6 mb-6 sm:mb-8 shadow-sm">
        <span class="text-[10px] sm:text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Descripción del Proyecto</span>
        <p class="text-sm sm:text-base text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line mt-3">{{ $opportunity->description }}</p>

        @if($opportunity->risks)
            <div class="mt-6 pt-4 sm:pt-6 border-t border-gray-100 dark:border-gray-800">
                <span class="text-[10px] sm:text-xs font-bold text-red-400 uppercase tracking-widest">Riesgos Asociados</span>
                <p class="text-sm sm:text-base text-red-600 dark:text-red-400 leading-relaxed whitespace-pre-line mt-3">{{ $opportunity->risks }}</p>
            </div>
        @endif

        <div class="grid grid-cols-2 gap-4 mt-6 pt-4 sm:pt-6 border-t border-gray-100 dark:border-gray-800">
            <div class="space-y-1">
                <p class="text-[9px] sm:text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Fecha Límite</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $opportunity->deadline ? $opportunity->deadline->format('d/m/Y') : 'Sin límite' }}</p>
            </div>
            <div class="space-y-1">
                <p class="text-[9px] sm:text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Retorno Estimado</p>
                <p class="text-sm font-semibold text-green-600 dark:text-green-400">{{ $opportunity->estimated_return }}% en {{ $opportunity->term_months }} meses</p>
            </div>
        </div>
    </div>

    @if($existingRequest)
        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl p-4 sm:p-6 shadow-sm">
            @if($existingRequest->pivot->status === 'pending')
                <div class="border-l-4 border-yellow-500 pl-4 py-2">
                    <p class="text-sm font-bold text-yellow-600 dark:text-yellow-400">Solicitud Pendiente</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tu solicitud de {{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($existingRequest->pivot->amount, 2) }} está siendo revisada.</p>
                </div>

            @elseif($existingRequest->pivot->status === 'approved')
                <div class="border-l-4 border-green-500 pl-4 py-2 mb-6">
                    <p class="text-sm font-bold text-green-600 dark:text-green-400">Solicitud Aprobada</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tu inversión de {{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($existingRequest->pivot->amount, 2) }} ha sido aprobada.</p>
                </div>

                @if($existingRequest->pivot->payment_status === 'confirmed')
                    <div class="border-l-4 border-green-500 pl-4 py-2">
                        <p class="text-sm font-bold text-green-600 dark:text-green-400">Pago Confirmado</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tu inversión está activa y generando rendimientos.</p>
                    </div>

                @elseif($existingRequest->pivot->payment_status === 'expired')
                    <div class="border-l-4 border-red-500 pl-4 py-2">
                        <p class="text-sm font-bold text-red-600 dark:text-red-400">Tiempo de Pago Vencido</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Contacta al administrador para reiniciar el proceso.</p>
                    </div>

                @elseif($existingRequest->pivot->payment_status === 'rejected')
                    <div class="border-l-4 border-red-500 pl-4 py-2 mb-6">
                        <p class="text-sm font-bold text-red-600 dark:text-red-400">Pago Rechazado</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Verifica los datos del comprobante y vuelve a intentar.</p>
                    </div>

                    @if($opportunity->bank_accounts)
                    <div class="mb-6">
                        <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-3">Datos para Depósito</p>
                        <div class="grid gap-3">
                            @foreach($opportunity->bank_accounts as $account)
                                <div class="p-3 sm:p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-100 dark:border-gray-800">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white">{{ $account['bank'] }}</span>
                                        <span class="text-[9px] px-1.5 py-0.5 bg-blue-100 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded">Banco</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                        <div class="space-y-1">
                                            <span class="text-gray-400">N° Cuenta</span>
                                            <p class="font-mono font-semibold text-gray-900 dark:text-white">{{ $account['account_number'] }}</p>
                                        </div>
                                        <div class="space-y-1">
                                            <span class="text-gray-400">CCI</span>
                                            <p class="font-mono font-semibold text-gray-900 dark:text-white">{{ $account['cci'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="pt-4 sm:pt-6 border-t border-gray-100 dark:border-gray-800">
                        <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">Subir Nuevo Comprobante de Pago</p>

                        @if($errors->any())
                            <div class="mb-4 p-3 bg-red-50 dark:bg-red-500/10 rounded-xl border border-red-100 dark:border-red-500/20">
                                @foreach($errors->all() as $error)
                                    <p class="text-xs text-red-600 dark:text-red-400">{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif

                        <form action="{{ route('client.investments.upload-voucher', [$opportunity, auth()->id()]) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="space-y-4">
                                <div class="flex gap-2 border-b border-gray-200 dark:border-gray-800">
                                    <button type="button" onclick="selectPaymentMethod('transfer')" id="btn-transfer"
                                        class="px-4 py-2 text-sm font-bold text-gray-900 dark:text-white border-b-2 border-green-500">
                                        Transferencia / Depósito
                                    </button>
                                    <button type="button" onclick="selectPaymentMethod('cash')" id="btn-cash"
                                        class="px-4 py-2 text-sm font-bold text-gray-500 dark:text-gray-400 border-b-2 border-transparent hover:text-gray-700 dark:hover:text-gray-300">
                                        Efectivo
                                    </button>
                                    <input type="hidden" name="payment_method" id="payment_method" value="transfer">
                                </div>

                                <div id="transferFields" class="space-y-4">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">N° de Operación</label>
                                        <input type="text" name="operation_number" id="operation_number" required
                                               maxlength="30" pattern="\d*" inputmode="numeric"
                                               placeholder="Ej: 123456789"
                                               oninput="this.value=this.value.replace(/\D/g,'').slice(0,30)"
                                               class="w-full px-3 sm:px-4 py-2.5 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-500">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Comprobante (imagen)</label>
                                        <div onclick="document.getElementById('voucherInput').click()" id="dropzoneTransfer" class="flex flex-col items-center justify-center w-full h-32 sm:h-40 border-2 border-dashed border-gray-200 dark:border-gray-800 rounded-xl cursor-pointer hover:border-green-500 hover:bg-green-50 dark:hover:bg-green-500/5">
                                            <div id="placeholderTransfer" class="flex flex-col items-center justify-center py-4">
                                                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Arrastra y suelta aquí</p>
                                                <p class="text-[10px] text-gray-400 mt-1">o haz clic para seleccionar</p>
                                            </div>
                                            <img id="previewImg" src="" alt="" class="hidden w-full h-full object-contain rounded-lg p-2">
                                        </div>
                                    </div>
                                </div>

                                <div id="cashFields" class="hidden space-y-4">
                                    <div class="p-4 bg-amber-50 dark:bg-amber-500/10 rounded-xl border border-amber-100 dark:border-amber-500/20">
                                        <p class="text-xs sm:text-sm text-amber-700 dark:text-amber-400">Sube una foto del dinero depositado como comprobante de tu pago en efectivo.</p>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Comprobante de Efectivo (imagen)</label>
                                        <div onclick="document.getElementById('voucherInput').click()" id="dropzoneCash" class="hidden flex flex-col items-center justify-center w-full h-32 sm:h-40 border-2 border-dashed border-gray-200 dark:border-gray-800 rounded-xl cursor-pointer hover:border-green-500 hover:bg-green-50 dark:hover:bg-green-500/5">
                                            <div id="placeholderCash" class="flex flex-col items-center justify-center py-4">
                                                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Foto del dinero</p>
                                                <p class="text-[10px] text-gray-400 mt-1">o haz clic para seleccionar</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <input type="file" name="voucher" id="voucherInput" accept="image/*" required class="hidden">

                                <button type="submit" class="text-sm font-bold text-green-600 border-b-2 border-green-600 pb-0.5 hover:opacity-70">
                                    Enviar Comprobante
                                </button>
                            </div>
                        </form>
                    </div>

                @else
                    @php
                        $approvedAt = $existingRequest->pivot->approved_at ? \Carbon\Carbon::parse($existingRequest->pivot->approved_at) : now();
                        $deadline = $approvedAt->addDay();
                        $hoursLeft = max(0, now()->diffInHours($deadline, false));
                        $isExpired = $hoursLeft <= 0;
                    @endphp

                    @if($isExpired && !$existingRequest->pivot->payment_submitted_at)
                        <div class="border-l-4 border-red-500 pl-4 py-2">
                            <p class="text-sm font-bold text-red-600 dark:text-red-400">Tiempo de Pago Vencido</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Contacta al administrador para reiniciar el proceso.</p>
                        </div>

                    @else
                        <div class="border-l-4 border-amber-500 pl-4 py-2 mb-6">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-bold text-amber-600 dark:text-amber-400">Tiempo Restante</p>
                                <p class="text-lg font-bold text-amber-600 dark:text-amber-400">{{ floor($hoursLeft) }}h</p>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Vence: {{ $deadline->format('d/m/Y H:i') }}</p>
                        </div>

                        @if($opportunity->bank_accounts)
                            <div class="mb-6">
                                <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-3">Datos para Depósito</p>
                                <div class="grid gap-3">
                                    @foreach($opportunity->bank_accounts as $account)
                                        <div class="p-3 sm:p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-100 dark:border-gray-800">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white">{{ $account['bank'] }}</span>
                                                <span class="text-[9px] px-1.5 py-0.5 bg-blue-100 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded">Banco</span>
                                            </div>
                                            <div class="grid grid-cols-2 gap-2 text-xs">
                                                <div class="space-y-1">
                                                    <span class="text-gray-400">N° Cuenta</span>
                                                    <p class="font-mono font-semibold text-gray-900 dark:text-white">{{ $account['account_number'] }}</p>
                                                </div>
                                                <div class="space-y-1">
                                                    <span class="text-gray-400">CCI</span>
                                                    <p class="font-mono font-semibold text-gray-900 dark:text-white">{{ $account['cci'] }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if(!$existingRequest->pivot->payment_submitted_at)
                            <div class="pt-4 sm:pt-6 border-t border-gray-100 dark:border-gray-800">
                                <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">Subir Comprobante de Pago</p>

                                @if($errors->any())
                                    <div class="mb-4 p-3 bg-red-50 dark:bg-red-500/10 rounded-xl border border-red-100 dark:border-red-500/20">
                                        @foreach($errors->all() as $error)
                                            <p class="text-xs text-red-600 dark:text-red-400">{{ $error }}</p>
                                        @endforeach
                                    </div>
                                @endif

                                <form action="{{ route('client.investments.upload-voucher', [$opportunity, auth()->id()]) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <div class="space-y-4">
                                        <div class="flex gap-2 border-b border-gray-200 dark:border-gray-800">
                                            <button type="button" onclick="selectPaymentMethod('transfer')" id="btn-transfer"
                                                class="px-4 py-2 text-sm font-bold text-gray-900 dark:text-white border-b-2 border-green-500">
                                                Transferencia / Depósito
                                            </button>
                                            <button type="button" onclick="selectPaymentMethod('cash')" id="btn-cash"
                                                class="px-4 py-2 text-sm font-bold text-gray-500 dark:text-gray-400 border-b-2 border-transparent hover:text-gray-700 dark:hover:text-gray-300">
                                                Efectivo
                                            </button>
                                            <input type="hidden" name="payment_method" id="payment_method" value="transfer">
                                        </div>

                                        <div id="transferFields" class="space-y-4">
                                            <div>
                                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">N° de Operación</label>
                                                <input type="text" name="operation_number" id="operation_number" required
                                                       maxlength="30" pattern="\d*" inputmode="numeric"
                                                       placeholder="Ej: 123456789"
                                                       oninput="this.value=this.value.replace(/\D/g,'').slice(0,30)"
                                                       class="w-full px-3 sm:px-4 py-2.5 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-500">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Comprobante (imagen)</label>
                                                <div onclick="document.getElementById('voucherInput').click()" id="dropzoneTransfer" class="flex flex-col items-center justify-center w-full h-32 sm:h-40 border-2 border-dashed border-gray-200 dark:border-gray-800 rounded-xl cursor-pointer hover:border-green-500 hover:bg-green-50 dark:hover:bg-green-500/5">
                                                    <div id="placeholderTransfer" class="flex flex-col items-center justify-center py-4">
                                                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Arrastra y suelta aquí</p>
                                                        <p class="text-[10px] text-gray-400 mt-1">o haz clic para seleccionar</p>
                                                    </div>
                                                    <img id="previewImg" src="" alt="" class="hidden w-full h-full object-contain rounded-lg p-2">
                                                </div>
                                            </div>
                                        </div>

                                        <div id="cashFields" class="hidden space-y-4">
                                            <div class="p-4 bg-amber-50 dark:bg-amber-500/10 rounded-xl border border-amber-100 dark:border-amber-500/20">
                                                <p class="text-xs sm:text-sm text-amber-700 dark:text-amber-400">Sube una foto del dinero depositado como comprobante de tu pago en efectivo.</p>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Comprobante de Efectivo (imagen)</label>
                                                <div onclick="document.getElementById('voucherInput').click()" id="dropzoneCash" class="hidden flex flex-col items-center justify-center w-full h-32 sm:h-40 border-2 border-dashed border-gray-200 dark:border-gray-800 rounded-xl cursor-pointer hover:border-green-500 hover:bg-green-50 dark:hover:bg-green-500/5">
                                                    <div id="placeholderCash" class="flex flex-col items-center justify-center py-4">
                                                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Foto del dinero</p>
                                                        <p class="text-[10px] text-gray-400 mt-1">o haz clic para seleccionar</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <input type="file" name="voucher" id="voucherInput" accept="image/*" required class="hidden">

                                        <button type="submit" class="text-sm font-bold text-green-600 border-b-2 border-green-600 pb-0.5 hover:opacity-70">
                                            Enviar Comprobante
                                        </button>
                                    </div>
                                </form>
                            </div>

                        @else
                            <div class="border-l-4 border-yellow-500 pl-4 py-2 mb-4">
                                <p class="text-sm font-bold text-yellow-600 dark:text-yellow-400">Comprobante Enviado</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Esperando verificación del administrador.</p>
                            </div>
                            @if($existingRequest->pivot->voucher_path)
                                <div class="mt-4">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Tu Comprobante</p>
                                    <div class="inline-block rounded-xl overflow-hidden border border-gray-200 dark:border-gray-800 shadow-sm max-w-xs">
                                        <img src="{{ asset('storage/' . $existingRequest->pivot->voucher_path) }}" alt="Comprobante de pago" class="w-full h-auto object-cover">
                                    </div>
                                </div>
                            @endif
                        @endif
                    @endif
                @endif

            @else
                <div class="border-l-4 border-red-500 pl-4 py-2 mb-6">
                    <p class="text-sm font-bold text-red-600 dark:text-red-400">Solicitud Rechazada</p>
                    @if($existingRequest->pivot->admin_notes)
                        <p class="text-xs text-red-500 dark:text-red-400 mt-1">{{ $existingRequest->pivot->admin_notes }}</p>
                    @endif
                </div>

                <div class="pt-4 sm:pt-6 border-t border-gray-100 dark:border-gray-800">
                    <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">Intentar de Nuevo</p>

                    @if($errors->any())
                        <div class="mb-4 p-3 bg-red-50 dark:bg-red-500/10 rounded-xl border border-red-100 dark:border-red-500/20">
                            @foreach($errors->all() as $error)
                                <p class="text-xs text-red-600 dark:text-red-400">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('client.investments.apply', $opportunity) }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Monto a Invertir</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-bold text-gray-400 pointer-events-none">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}</span>
                                    <input type="text" name="amount" id="investAmount" required inputmode="decimal"
                                           placeholder="{{ number_format($opportunity->min_investment, 2) }}"
                                           oninput="if(window.formatInvestAmount){let v=window.formatInvestAmount(this.value);if(this.value!==v)this.value=v;}"
                                           class="w-full pl-8 sm:pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 rounded-xl text-base font-semibold text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-500">
                                </div>
                                <input type="hidden" id="minInvestment" value="{{ $opportunity->min_investment }}">
                                <input type="hidden" id="maxInvestment" value="{{ $opportunity->remaining_amount }}">
                                <input type="hidden" id="monthlyRate" value="{{ $opportunity->monthly_return_rate }}">
                                <input type="hidden" id="termMonths" value="{{ $opportunity->term_months }}">
                                <input type="hidden" id="currencySymbol" value="{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}">
                                <p class="text-[10px] sm:text-xs text-gray-400 mt-2">Mínimo: {{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($opportunity->min_investment, 2) }} · Máximo: {{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($opportunity->remaining_amount, 2) }}</p>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Tipo de Amortización</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <button type="button" onclick="selectAmortization('frances')" id="btn-frances"
                                        class="amort-btn px-4 py-3 rounded-xl text-sm font-bold border-2 border-indigo-500 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 transition-all">
                                        Francés
                                    </button>
                                    <button type="button" onclick="selectAmortization('americano')" id="btn-americano"
                                        class="amort-btn px-4 py-3 rounded-xl text-sm font-bold border-2 border-gray-200 dark:border-gray-800 bg-transparent text-gray-500 dark:text-gray-400 transition-all hover:border-gray-300">
                                        Americano
                                    </button>
                                </div>
                                <input type="hidden" name="amortization_type" id="amortizationType" value="frances">
                                <p id="amortDesc" class="text-[10px] sm:text-xs text-gray-400 mt-2">Cuotas fijas: pagas lo mismo cada mes (capital + interés)</p>
                            </div>
                            <div id="scheduleContainer" class="hidden">
                                <div class="border border-gray-100 dark:border-gray-800 rounded-xl overflow-hidden">
                                    <div class="px-4 py-2.5 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-800">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Cronograma de Pagos</p>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-xs">
                                            <thead>
                                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                                    <th class="px-3 py-2 text-left text-[9px] font-bold text-gray-400 uppercase">Cuota</th>
                                                    <th class="px-3 py-2 text-right text-[9px] font-bold text-gray-400 uppercase">Pago</th>
                                                    <th class="px-3 py-2 text-right text-[9px] font-bold text-gray-400 uppercase">Interés</th>
                                                    <th class="px-3 py-2 text-right text-[9px] font-bold text-gray-400 uppercase">Capital</th>
                                                    <th class="px-3 py-2 text-right text-[9px] font-bold text-gray-400 uppercase">Saldo</th>
                                                </tr>
                                            </thead>
                                            <tbody id="scheduleBody"></tbody>
                                        </table>
                                    </div>
                                    <div class="px-4 py-2.5 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-800 flex justify-between">
                                        <span class="text-[10px] font-bold text-gray-400 uppercase">Total a Recibir</span>
                                        <span id="scheduleTotal" class="text-xs font-bold text-green-600 dark:text-green-400"></span>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="text-sm font-bold text-green-600 border-b-2 border-green-600 pb-0.5 hover:opacity-70">
                                Enviar Nueva Solicitud
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

    @else
        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl p-4 sm:p-6 shadow-sm">
            <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">Participar en esta Oportunidad</p>

            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 dark:bg-red-500/10 rounded-xl border border-red-100 dark:border-red-500/20">
                    @foreach($errors->all() as $error)
                        <p class="text-xs sm:text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('client.investments.apply', $opportunity) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Monto a Invertir</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-bold text-gray-400 pointer-events-none">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}</span>
                            <input type="text" name="amount" id="investAmount" required inputmode="decimal"
                                   placeholder="{{ number_format($opportunity->min_investment, 2) }}"
                                   oninput="if(window.formatInvestAmount){let v=window.formatInvestAmount(this.value);if(this.value!==v)this.value=v;}"
                                   class="w-full pl-8 sm:pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 rounded-xl text-base font-semibold text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-500">
                        </div>
                        <input type="hidden" id="minInvestment" value="{{ $opportunity->min_investment }}">
                        <input type="hidden" id="maxInvestment" value="{{ $opportunity->remaining_amount }}">
                        <input type="hidden" id="monthlyRate" value="{{ $opportunity->monthly_return_rate }}">
                        <input type="hidden" id="termMonths" value="{{ $opportunity->term_months }}">
                        <input type="hidden" id="currencySymbol" value="{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}">
                        <p class="text-[10px] sm:text-xs text-gray-400 mt-2">Mínimo: {{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($opportunity->min_investment, 2) }} · Máximo: {{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($opportunity->remaining_amount, 2) }}</p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Tipo de Amortización</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" onclick="selectAmortization('frances')" id="btn-frances"
                                class="amort-btn px-4 py-3 rounded-xl text-sm font-bold border-2 border-indigo-500 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 transition-all">
                                Francés
                            </button>
                            <button type="button" onclick="selectAmortization('americano')" id="btn-americano"
                                class="amort-btn px-4 py-3 rounded-xl text-sm font-bold border-2 border-gray-200 dark:border-gray-800 bg-transparent text-gray-500 dark:text-gray-400 transition-all hover:border-gray-300">
                                Americano
                            </button>
                        </div>
                        <input type="hidden" name="amortization_type" id="amortizationType" value="frances">
                        <p id="amortDesc" class="text-[10px] sm:text-xs text-gray-400 mt-2">Cuotas fijas: pagas lo mismo cada mes (capital + interés)</p>
                    </div>
                    <div id="scheduleContainer" class="hidden">
                        <div class="border border-gray-100 dark:border-gray-800 rounded-xl overflow-hidden">
                            <div class="px-4 py-2.5 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-800">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Cronograma de Pagos</p>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-xs">
                                    <thead>
                                        <tr class="border-b border-gray-100 dark:border-gray-800">
                                            <th class="px-3 py-2 text-left text-[9px] font-bold text-gray-400 uppercase">Cuota</th>
                                            <th class="px-3 py-2 text-right text-[9px] font-bold text-gray-400 uppercase">Pago</th>
                                            <th class="px-3 py-2 text-right text-[9px] font-bold text-gray-400 uppercase">Interés</th>
                                            <th class="px-3 py-2 text-right text-[9px] font-bold text-gray-400 uppercase">Capital</th>
                                            <th class="px-3 py-2 text-right text-[9px] font-bold text-gray-400 uppercase">Saldo</th>
                                        </tr>
                                    </thead>
                                    <tbody id="scheduleBody"></tbody>
                                </table>
                            </div>
                            <div class="px-4 py-2.5 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-800 flex justify-between">
                                <span class="text-[10px] font-bold text-gray-400 uppercase">Total a Recibir</span>
                                <span id="scheduleTotal" class="text-xs font-bold text-green-600 dark:text-green-400"></span>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="text-sm font-bold text-green-600 border-b-2 border-green-600 pb-0.5 hover:opacity-70">
                        Enviar Solicitud de Inversión
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>

<script>
window.formatInvestAmount = function(raw) {
    let d = (raw || '').replace(/[^0-9]/g, '');
    if (!d) return '';
    d = d.padStart(3, '0');
    let i = d.slice(0, -2).replace(/^0+/, '') || '0';
    let dec = d.slice(-2);
    return i.replace(/\B(?=(\d{3})+(?!\d))/g, ',') + '.' + dec;
};

function selectPaymentMethod(method) {
    const transferFields = document.getElementById('transferFields');
    const cashFields = document.getElementById('cashFields');
    const btnTransfer = document.getElementById('btn-transfer');
    const btnCash = document.getElementById('btn-cash');
    const paymentMethodInput = document.getElementById('payment_method');
    const dropzoneTransfer = document.getElementById('dropzoneTransfer');
    const dropzoneCash = document.getElementById('dropzoneCash');

    if (!transferFields || !cashFields || !btnTransfer || !btnCash) return;
    paymentMethodInput.value = method;

    if (method === 'cash') {
        transferFields.classList.add('hidden');
        cashFields.classList.remove('hidden');
        btnTransfer.className = 'px-4 py-2 text-sm font-bold text-gray-500 dark:text-gray-400 border-b-2 border-transparent hover:text-gray-700 dark:hover:text-gray-300';
        btnCash.className = 'px-4 py-2 text-sm font-bold text-gray-900 dark:text-white border-b-2 border-green-500';
        if (dropzoneTransfer) dropzoneTransfer.classList.add('hidden');
        if (dropzoneCash) dropzoneCash.classList.remove('hidden');
    } else {
        transferFields.classList.remove('hidden');
        cashFields.classList.add('hidden');
        btnTransfer.className = 'px-4 py-2 text-sm font-bold text-gray-900 dark:text-white border-b-2 border-green-500';
        btnCash.className = 'px-4 py-2 text-sm font-bold text-gray-500 dark:text-gray-400 border-b-2 border-transparent hover:text-gray-700 dark:hover:text-gray-300';
        if (dropzoneTransfer) dropzoneTransfer.classList.remove('hidden');
        if (dropzoneCash) dropzoneCash.classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const voucherInput = document.getElementById('voucherInput');
    const previewImg = document.getElementById('previewImg');
    const placeholderTransfer = document.getElementById('placeholderTransfer');
    const placeholderCash = document.getElementById('placeholderCash');
    const dropzoneTransfer = document.getElementById('dropzoneTransfer');
    const dropzoneCash = document.getElementById('dropzoneCash');

    if (!voucherInput) return;

    voucherInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            if (dropzoneTransfer) { dropzoneTransfer.classList.add('border-green-500'); dropzoneTransfer.classList.remove('border-gray-200', 'border-dashed'); }
            if (dropzoneCash) { dropzoneCash.classList.add('border-green-500'); dropzoneCash.classList.remove('border-gray-200', 'border-dashed'); }

            const reader = new FileReader();
            reader.onload = function(e) {
                if (previewImg && placeholderTransfer && placeholderCash) {
                    previewImg.src = e.target.result;
                    previewImg.classList.remove('hidden');
                    placeholderTransfer.classList.add('hidden');
                    placeholderCash.classList.add('hidden');
                }
            };
            reader.readAsDataURL(this.files[0]);
        } else {
            if (dropzoneTransfer) { dropzoneTransfer.classList.remove('border-green-500'); dropzoneTransfer.classList.add('border-gray-200', 'border-dashed'); }
            if (dropzoneCash) { dropzoneCash.classList.remove('border-green-500'); dropzoneCash.classList.add('border-gray-200', 'border-dashed'); }
            if (previewImg) { previewImg.src = ''; previewImg.classList.add('hidden'); }
            if (placeholderTransfer) placeholderTransfer.classList.remove('hidden');
            if (placeholderCash) placeholderCash.classList.remove('hidden');
        }
    });

    const investAmount = document.getElementById('investAmount');
    if (investAmount) {
        const minVal = parseFloat(document.getElementById('minInvestment').value);
        const maxVal = parseFloat(document.getElementById('maxInvestment').value);

        investAmount.addEventListener('input', function(e) {
            e.preventDefault();
            let pos = this.selectionStart;
            let oldLen = this.value.length;
            let formatted = window.formatInvestAmount(this.value);
            this.value = formatted;
            let newLen = formatted.length;
            try { this.setSelectionRange(newLen, newLen); } catch(ex) {}
            updateSchedule();
        });

        investAmount.addEventListener('blur', function() {
            let n = parseFloat((this.value || '0').replace(/,/g, ''));
            if (isNaN(n) || n <= 0) this.value = window.formatInvestAmount(String(minVal));
            else if (n < minVal) this.value = window.formatInvestAmount(String(minVal));
            else if (n > maxVal) this.value = window.formatInvestAmount(String(maxVal));
            else this.value = window.formatInvestAmount(this.value);
            updateSchedule();
        });

        investAmount.closest('form')?.addEventListener('submit', function() {
            investAmount.value = (investAmount.value || '0').replace(/,/g, '');
        });

        updateSchedule();
    }
});

function selectAmortization(type) {
    const btnFrances = document.getElementById('btn-frances');
    const btnAmericano = document.getElementById('btn-americano');
    const amortInput = document.getElementById('amortizationType');
    const amortDesc = document.getElementById('amortDesc');

    if (!btnFrances || !btnAmericano) return;
    amortInput.value = type;

    if (type === 'frances') {
        btnFrances.className = 'amort-btn px-4 py-3 rounded-xl text-sm font-bold border-2 border-indigo-500 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 transition-all';
        btnAmericano.className = 'amort-btn px-4 py-3 rounded-xl text-sm font-bold border-2 border-gray-200 dark:border-gray-800 bg-transparent text-gray-500 dark:text-gray-400 transition-all hover:border-gray-300';
        amortDesc.textContent = 'Cuotas fijas: recibes lo mismo cada mes (capital + interés)';
    } else {
        btnAmericano.className = 'amort-btn px-4 py-3 rounded-xl text-sm font-bold border-2 border-indigo-500 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 transition-all';
        btnFrances.className = 'amort-btn px-4 py-3 rounded-xl text-sm font-bold border-2 border-gray-200 dark:border-gray-800 bg-transparent text-gray-500 dark:text-gray-400 transition-all hover:border-gray-300';
        amortDesc.textContent = 'Solo intereses cada mes, capital completo en la última cuota';
    }

    updateSchedule();
}

function updateSchedule() {
    const amountInput = document.getElementById('investAmount');
    const rateInput = document.getElementById('monthlyRate');
    const termInput = document.getElementById('termMonths');
    const typeInput = document.getElementById('amortizationType');
    const symbolInput = document.getElementById('currencySymbol');
    const container = document.getElementById('scheduleContainer');
    const tbody = document.getElementById('scheduleBody');
    const totalEl = document.getElementById('scheduleTotal');

    if (!amountInput || !rateInput || !termInput || !typeInput || !container || !tbody || !totalEl) return;

    const amount = parseFloat(amountInput.value.replace(/,/g, ''));
    if (isNaN(amount) || amount <= 0) {
        container.classList.add('hidden');
        return;
    }

    const rate = parseFloat(rateInput.value) / 100;
    const term = parseInt(termInput.value);
    const type = typeInput.value;
    const symbol = symbolInput.value;

    container.classList.remove('hidden');
    tbody.innerHTML = '';

    let balance = amount;
    let totalPayment = 0;
    let rows = '';

    for (let i = 1; i <= term; i++) {
        let interest, principal, payment;

        if (type === 'frances') {
            payment = amount * (rate * Math.pow(1 + rate, term)) / (Math.pow(1 + rate, term) - 1);
            interest = balance * rate;
            principal = payment - interest;
        } else {
            interest = amount * rate;
            if (i === term) {
                principal = balance;
                payment = interest + principal;
            } else {
                principal = 0;
                payment = interest;
            }
        }

        balance = Math.max(0, balance - principal);
        totalPayment += payment;

        rows += '<tr class="border-b border-gray-50 dark:border-gray-900">' +
            '<td class="px-3 py-1.5 text-gray-700 dark:text-gray-300">' + i + '</td>' +
            '<td class="px-3 py-1.5 text-right font-medium text-gray-900 dark:text-white">' + symbol + ' ' + payment.toFixed(2) + '</td>' +
            '<td class="px-3 py-1.5 text-right text-amber-600 dark:text-amber-400">' + symbol + ' ' + interest.toFixed(2) + '</td>' +
            '<td class="px-3 py-1.5 text-right text-indigo-600 dark:text-indigo-400">' + symbol + ' ' + principal.toFixed(2) + '</td>' +
            '<td class="px-3 py-1.5 text-right text-gray-500 dark:text-gray-400">' + symbol + ' ' + balance.toFixed(2) + '</td>' +
            '</tr>';
    }

    tbody.innerHTML = rows;
    totalEl.textContent = symbol + ' ' + totalPayment.toFixed(2);
}
</script>
@endsection