@extends('client.layout')

@section('title', 'Solicitar Préstamo')

@section('content')
    <div class="min-h-screen px-4 md:px-10 py-6 md:py-8 max-w-6xl pb-36 md:pb-8">

        <!-- Header -->
        <div class="mb-10">
            <a href="{{ route('client.dashboard') }}"
                class="inline-flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-100 hover:text-gray-900 dark:hover:text-white transition-colors mb-4">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Volver
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Solicitar Préstamo</h1>
        </div>

        @if($errors->any())
            <div class="mb-8 text-xs text-red-500 space-y-0.5">
                @foreach($errors->all() as $error)
                    <p>• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('client.loans.store') }}" method="POST" id="loanForm">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-12 lg:gap-16">

                <!-- ══ LEFT: FORM ══ -->
                <div class="lg:col-span-3 space-y-10">

                    <!-- Importe y moneda -->
                    <section>
                        <p class="text-[10px] text-gray-500 dark:text-gray-100 uppercase tracking-widest mb-4">Importe y
                            moneda</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="amount"
                                    class="block text-xs text-gray-500 dark:text-gray-100 mb-1.5">Monto</label>
                                <div class="relative">
                                    <span id="currencySymbol"
                                        class="absolute left-0 top-1/2 -translate-y-1/2 text-sm font-semibold text-gray-500 dark:text-gray-100 pointer-events-none">S/</span>
                                    <input type="text" name="amount" id="amount" value="{{ old('amount') }}"
                                        placeholder="0.00" inputmode="decimal"
                                        class="w-full pl-8 pr-0 py-3 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-xl font-semibold text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-100 focus:outline-none focus:border-green-500 transition-colors">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-100 mb-1.5">Moneda</label>
                                <div class="flex gap-2 mt-1">
                                    <label class="cursor-pointer flex-1">
                                        <input type="radio" name="currency" value="PEN" class="hidden peer" {{ old('currency', 'PEN') === 'PEN' ? 'checked' : '' }}>
                                        <div
                                            class="flex items-center justify-center gap-2 py-2.5 text-sm text-gray-500 dark:text-gray-100 border-b-2 border-transparent peer-checked:border-green-500 peer-checked:text-green-600 dark:peer-checked:text-green-400 transition-all">
                                            <span>🇵🇪</span>
                                            <span class="font-medium">Soles</span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer flex-1">
                                        <input type="radio" name="currency" value="USD" class="hidden peer" {{ old('currency') === 'USD' ? 'checked' : '' }}>
                                        <div
                                            class="flex items-center justify-center gap-2 py-2.5 text-sm text-gray-500 dark:text-gray-100 border-b-2 border-transparent peer-checked:border-green-500 peer-checked:text-green-600 dark:peer-checked:text-green-400 transition-all">
                                            <span>🇺🇸</span>
                                            <span class="font-medium">Dólares</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Plazo -->
                    <section>
                        <p class="text-[10px] text-gray-500 dark:text-gray-100 uppercase tracking-widest mb-4">Plazo</p>

                        <div class="space-y-6">
                            <!-- Fecha de desembolso -->
                            <div class="relative group">
                                <label for="disbursement_date"
                                    class="block text-xs text-gray-500 dark:text-gray-100 mb-2">Fecha estimada de
                                    desembolso</label>
                                <div class="relative">
                                    <input type="date" name="disbursement_date" id="disbursement_date"
                                        min="{{ date('Y-m-d') }}"
                                        value="{{ old('disbursement_date', date('Y-m-d')) }}"
                                        class="w-full pl-0 pr-10 py-3 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:border-green-500 transition-all cursor-pointer appearance-none calendar-picker-indicator-none">
                                    <div
                                        class="absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400 group-focus-within:text-green-500 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Tipo de pago (primero) -->
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-100 mb-2">Tipo de pago</label>
                                <div class="flex gap-2">
                                    <label class="cursor-pointer flex-1">
                                        <input type="radio" name="type" value="cuotas" class="hidden peer" {{ old('type', 'cuotas') === 'cuotas' ? 'checked' : '' }}>
                                        <div
                                            class="text-center py-2.5 text-xs font-medium text-gray-500 dark:text-gray-100 border-b-2 border-transparent peer-checked:border-green-500 peer-checked:text-green-600 dark:peer-checked:text-green-400 transition-all">
                                            Cuotas mensuales
                                        </div>
                                    </label>
                                    <label class="cursor-pointer flex-1">
                                        <input type="radio" name="type" value="unico" class="hidden peer" {{ old('type') === 'unico' ? 'checked' : '' }}>
                                        <div
                                            class="text-center py-2.5 text-xs font-medium text-gray-500 dark:text-gray-100 border-b-2 border-transparent peer-checked:border-green-500 peer-checked:text-green-600 dark:peer-checked:text-green-400 transition-all">
                                            Pago único
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Sistema de Amortización (Solo si es cuotas) -->
                            <div id="amortizationSystemBlock">
                                <div class="flex items-center gap-1.5 mb-2">
                                    <label class="block text-xs text-gray-500 dark:text-gray-100">Sistema de
                                        amortización</label>
                                    <div class="relative group">
                                        <svg class="w-3 h-3 text-gray-400 dark:text-gray-100 cursor-help" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div
                                            class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-48 p-2 bg-gray-800 text-white text-[9px] rounded-lg shadow-2xl z-[100] text-center pointer-events-none leading-relaxed">
                                            Define cómo se distribuyen los pagos: en el <span
                                                class="text-green-400 font-bold">Francés</span> pagas cuotas iguales, en el
                                            <span class="text-amber-400 font-bold">Americano</span> pagas solo intereses y
                                            el capital al final.
                                            <div
                                                class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-800">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <label class="cursor-pointer flex-1">
                                        <input type="radio" name="amortization_system" value="frances" class="hidden peer"
                                            {{ old('amortization_system', 'frances') === 'frances' ? 'checked' : '' }}>
                                        <div
                                            class="text-center py-2.5 text-xs font-medium text-gray-500 dark:text-gray-100 border-b-2 border-transparent peer-checked:border-green-500 peer-checked:text-green-600 dark:peer-checked:text-green-400 transition-all">
                                            Francés (Cuota fija)
                                        </div>
                                    </label>
                                    <label class="cursor-pointer flex-1">
                                        <input type="radio" name="amortization_system" value="americano" class="hidden peer"
                                            {{ old('amortization_system') === 'americano' ? 'checked' : '' }}>
                                        <div
                                            class="text-center py-2.5 text-xs font-medium text-gray-500 dark:text-gray-100 border-b-2 border-transparent peer-checked:border-green-500 peer-checked:text-green-600 dark:peer-checked:text-green-400 transition-all">
                                            Americano (Interés mensual)
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Cuotas: slider de meses (3-24) -->
                            <div id="cuotasBlock">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-1.5">
                                        <label for="months" class="text-xs text-gray-500 dark:text-gray-100">Plazo del
                                            préstamo</label>
                                        <div class="relative group">
                                            <svg class="w-3 h-3 text-gray-400 dark:text-gray-100 cursor-help" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <div
                                                class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-48 p-2 bg-gray-800 text-white text-[9px] rounded-lg shadow-2xl z-[100] text-center pointer-events-none leading-relaxed">
                                                Selecciona la cantidad de <span
                                                    class="text-green-400 font-bold">meses</span> en los que deseas devolver
                                                el préstamo.
                                                <div
                                                    class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-800">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="monthsDisplay"
                                        class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">12 meses</span>
                                </div>
                                <input type="range" name="months" id="months" min="2" max="24"
                                    value="{{ old('months', 12) }}"
                                    class="w-full h-1 bg-gray-200 dark:bg-gray-800 rounded-full appearance-none cursor-pointer">
                                <div class="relative h-4 text-[10px] text-gray-400 dark:text-gray-100 mt-1.5">
                                    <span class="absolute left-0">2</span>
                                    <span class="absolute left-[18.18%] -translate-x-1/2">6</span>
                                    <span class="absolute left-[45.45%] -translate-x-1/2">12</span>
                                    <span class="absolute left-[72.72%] -translate-x-1/2">18</span>
                                    <span class="absolute right-0">24</span>
                                </div>
                            </div>

                            <!-- Frecuencia de pago (Solo para Francés) -->
                            <div id="frequencyBlock">
                                <div class="flex items-center gap-1.5 mb-2">
                                    <label class="block text-xs text-gray-500 dark:text-gray-100">Frecuencia de pago</label>
                                    <div class="relative group">
                                        <svg class="w-3 h-3 text-gray-400 dark:text-gray-100 cursor-help" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div
                                            class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-48 p-2 bg-gray-800 text-white text-[9px] rounded-lg shadow-2xl z-[100] text-center pointer-events-none leading-relaxed">
                                            Indica cada cuánto tiempo realizarás los pagos: <span
                                                class="text-green-400 font-bold">diario</span>, <span
                                                class="text-green-400 font-bold">semanal</span>, <span
                                                class="text-green-400 font-bold">quincenal</span> o <span
                                                class="text-green-400 font-bold">mensual</span>.
                                            <div
                                                class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-800">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    @foreach(['diario' => 'Diario', 'semanal' => 'Semanal', 'quincenal' => 'Quincenal', 'mensual' => 'Mensual'] as $val => $label)
                                        <label class="cursor-pointer relative group/freq">
                                            <input type="radio" name="payment_frequency" value="{{ $val }}" class="hidden peer"
                                                {{ old('payment_frequency', 'mensual') === $val ? 'checked' : '' }}>
                                            <div
                                                class="text-center py-2 text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-100 border-b-2 border-transparent peer-checked:border-green-500 peer-checked:text-green-600 dark:peer-checked:text-green-400 transition-all">
                                                {{ $label }}
                                            </div>
                                            <div data-freq-tooltip="{{ $val }}"
                                                class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 flex flex-col items-center z-[100] pointer-events-none opacity-0 scale-95 -translate-y-1 group-hover/freq:opacity-100 group-hover/freq:scale-100 group-hover/freq:translate-y-0 transition-all duration-200 ease-out">
                                                <div class="px-3 py-1.5 bg-gray-900 dark:bg-gray-100 text-white dark:text-white text-[9px] font-bold rounded-lg shadow-2xl whitespace-nowrap">
                                                    Cuota: <span class="text-green-400 dark:text-green-600 tabular-nums freq-cuota-value"></span>
                                                </div>
                                                <div class="border-4 border-transparent border-t-gray-900 dark:border-t-gray-100 transition-opacity duration-200"></div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Pago único: 15 o 30 días -->
                            <div id="unicoBlock" class="hidden">
                                <label class="block text-xs text-gray-500 dark:text-gray-100 mb-2">Pagar en</label>
                                <div class="flex gap-2">
                                    <label class="cursor-pointer flex-1">
                                        <input type="radio" name="payment_days" value="15" class="hidden peer" {{ old('payment_days', '15') == '15' ? 'checked' : '' }}>
                                        <div
                                            class="text-center py-3 border-b-2 border-transparent peer-checked:border-green-500 transition-all">
                                            <p
                                                class="text-sm font-semibold text-gray-700 dark:text-gray-100 peer-checked:text-green-600 dark:peer-checked:text-green-400">
                                                15 días</p>
                                            <p class="text-[10px] text-green-600 dark:text-green-400 mt-0.5">
                                                <span id="unico15Rate">{{ number_format((Auth::user()->loan_interest_rate ?? 20) / 2, 2, '.', '') }}%</span> comisión
                                            </p>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer flex-1">
                                        <input type="radio" name="payment_days" value="30" class="hidden peer" {{ old('payment_days') == '30' ? 'checked' : '' }}>
                                        <div
                                            class="text-center py-3 border-b-2 border-transparent peer-checked:border-green-500 transition-all">
                                            <p
                                                class="text-sm font-semibold text-gray-700 dark:text-gray-100 peer-checked:text-green-600 dark:peer-checked:text-green-400">
                                                30 días</p>
                                            <p class="text-[10px] text-amber-500 dark:text-amber-400 mt-0.5">
                                                {{ number_format(Auth::user()->loan_interest_rate ?? 20, 2, '.', '') }}%
                                                comisión
                                            </p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Desembolso -->
                    <section>
                        <p class="text-[10px] text-gray-500 dark:text-gray-100 uppercase tracking-widest mb-4">Cuenta de
                            desembolso</p>

                        @if($bankAccounts->isEmpty())
                            <div id="noAccountsWarning"
                                class="bg-amber-50 dark:bg-amber-950/20 border border-amber-100 dark:border-amber-900/50 rounded-2xl p-6">
                                <div class="flex gap-4">
                                    <div class="text-amber-500">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4
                                            class="text-sm font-bold text-amber-900 dark:text-amber-400 uppercase tracking-widest mb-1">
                                            No tienes cuentas registradas</h4>
                                        <p class="text-xs text-amber-700 dark:text-amber-500/80 mb-4 leading-relaxed">Para
                                            solicitar un préstamo, primero debes agregar una cuenta bancaria donde recibirás el
                                            dinero.</p>
                                        <button type="button" onclick="openBankModal()"
                                            class="inline-flex items-center text-[10px] font-black text-amber-900 dark:text-amber-400 uppercase tracking-[0.2em] border-b-2 border-amber-900 dark:border-amber-400 pb-0.5 hover:opacity-70 transition-opacity bg-transparent cursor-pointer">
                                            Agregar cuenta bancaria →
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="group" id="accountSelectorGroup">
                                <label for="bank_account_id"
                                    class="block text-xs text-gray-500 dark:text-gray-100 mb-2">Selecciona tu cuenta de
                                    destino</label>
                                <div class="relative">
                                    <select name="bank_account_id" id="bank_account_id" required
                                        class="w-full px-0 py-3 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:border-green-500 transition-colors appearance-none cursor-pointer">
                                        <option value="" class="dark:bg-gray-950">Seleccionar cuenta</option>
                                        @foreach($bankAccounts as $acc)
                                            <option value="{{ $acc->id }}" data-currency="{{ $acc->currency }}"
                                                class="dark:bg-gray-950" {{ old('bank_account_id') == $acc->id ? 'selected' : '' }}>
                                                {{ $acc->alias ?: $acc->bank_name }} — {{ $acc->account_number }}
                                                ({{ $acc->currency }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-3 uppercase tracking-widest">¿Quieres usar otra? <button
                                        type="button" onclick="openBankModal()"
                                        class="text-green-600 dark:text-green-400 font-bold hover:underline bg-transparent cursor-pointer">Gestiónalas
                                        aquí</button></p>
                            </div>

                            <div id="noAccountWarning"
                                class="hidden bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-900/50 rounded-2xl p-6">
                                <div class="flex gap-4">
                                    <div class="text-red-500">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4
                                            class="text-sm font-bold text-red-900 dark:text-red-400 uppercase tracking-widest mb-1">
                                            No tienes cuentas en <span id="missingCurrencyName">Soles</span>
                                        </h4>
                                        <p class="text-xs text-red-700 dark:text-red-500/80 mb-4 leading-relaxed">
                                            Para solicitar un préstamo en esta moneda, primero debes registrar una cuenta
                                            bancaria correspondiente.
                                        </p>
                                        <button type="button" onclick="openBankModal()"
                                            class="inline-flex items-center text-[10px] font-black text-red-900 dark:text-red-400 uppercase tracking-[0.2em] border-b-2 border-red-900 dark:border-red-400 pb-0.5 hover:opacity-70 transition-opacity bg-transparent cursor-pointer">
                                            Registrar cuenta ahora
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </section>

                    <!-- Motivo -->
                    <section>
                        <p class="text-[10px] text-gray-500 dark:text-gray-100 uppercase tracking-widest mb-4">Motivo</p>

                        <label for="reason" class="block text-xs text-gray-500 dark:text-gray-100 mb-1.5">¿Por qué necesitas
                            este préstamo?</label>
                        <textarea name="reason" id="reason" rows="3" placeholder="Describe brevemente el motivo..."
                            class="w-full px-0 py-3 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-100 focus:outline-none focus:border-green-500 transition-colors resize-none">{{ old('reason') }}</textarea>
                        <p class="text-[10px] text-gray-400 dark:text-gray-100 mt-1 text-right tabular-nums"><span
                                id="charCount">0</span>/1000</p>
                    </section>
                </div>

                <!-- ══ RIGHT: SIMULATION ══ -->
                <div class="lg:col-span-2">
                    <div id="simulationPanel"
                        class="md:sticky md:top-8 space-y-4 md:space-y-6 fixed bottom-0 left-0 right-0 md:relative bg-white dark:bg-gray-950 border-t border-gray-100 dark:border-gray-800 md:border-0 p-4 md:p-0 shadow-[0_-4px_20px_rgba(0,0,0,0.08)] md:shadow-none z-50 transition-all duration-300">

                        <!-- Toggle button mobile -->
                        <div class="md:hidden flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></div>
                                <span
                                    class="text-[10px] text-amber-500 dark:text-amber-400 font-bold uppercase tracking-widest">Simulación</span>
                            </div>
                            <button type="button" id="toggleSim"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 15l7-7 7 7" />
                                </svg>
                            </button>
                        </div>

                        <div id="simContent">
                            <!-- Main number -->
                            <p class="text-[10px] text-gray-500 dark:text-gray-100 mb-1">Total a devolver</p>
                            <p id="simTotal"
                                class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight tabular-nums">
                                S/ 0.00</p>

                            <div id="simMonthlyRow" class="mt-1.5 md:mt-2">
                                <p class="text-xs text-green-600 dark:text-green-400 font-medium tabular-nums">
                                    <span id="simMonthly">S/ 0.00</span> <span
                                        class="text-gray-500 dark:text-gray-100 font-normal">/ mes</span>
                                </p>
                            </div>
                        </div>

                        <!-- Breakdown (desktop only) -->
                        <div id="simBreakdown" class="hidden md:block space-y-3 pt-2">
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-500 dark:text-gray-100">Monto solicitado</span>
                                <span id="simAmount" class="text-gray-700 dark:text-gray-100 tabular-nums">S/ 0.00</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-500 dark:text-gray-100">Moneda</span>
                                <span id="simCurrency" class="text-gray-700 dark:text-gray-100">Soles</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-500 dark:text-gray-100">Plazo</span>
                                <span id="simMonths" class="text-gray-700 dark:text-gray-100 tabular-nums">12 meses</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-500 dark:text-gray-100">Tipo</span>
                                <span id="simType" class="text-gray-700 dark:text-gray-100">Cuotas</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-500 dark:text-gray-100">Desembolso</span>
                                <span id="simDisbursementDate"
                                    class="text-gray-700 dark:text-gray-100 tabular-nums">—</span>
                            </div>
                            <div class="h-px bg-gray-100 dark:bg-gray-800 my-1"></div>
                            <div class="flex justify-between text-xs">
                                <span id="simInterestLabel" class="text-amber-500 dark:text-amber-400">Comisión simulada
                                    ({{ number_format(Auth::user()->loan_interest_rate ?? 20, 2, '.', '') }}%)</span>
                                <span id="simInterest"
                                    class="text-amber-500 dark:text-amber-400 font-medium tabular-nums">S/ 0.00</span>
                            </div>

                            <!-- Cronograma Preview -->
                            <div id="scheduleSection"
                                class="hidden pt-4 border-t border-gray-100 dark:border-gray-800 mt-4">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Cronograma
                                        de pagos</span>
                                    <button type="button"
                                        onclick="document.getElementById('fullSchedule').classList.toggle('hidden')"
                                        class="text-[10px] text-green-600 font-bold hover:underline">VER DETALLE</button>
                                </div>
                                <div id="fullSchedule"
                                    class="hidden overflow-x-auto rounded-lg border border-gray-100 dark:border-gray-800">
                                    <table class="w-full text-[10px] text-left min-w-[300px]">
                                        <thead class="bg-gray-50 dark:bg-gray-900 text-gray-400">
                                            <tr>
                                                <th class="px-2 py-2 font-medium">N°</th>
                                                <th class="px-2 py-2 font-medium">
                                                    <div class="flex items-center gap-1">
                                                        Capital
                                                        <div class="relative group">
                                                            <svg class="w-2.5 h-2.5 text-gray-300 cursor-help" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            <div
                                                                class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-32 p-2 bg-gray-800 text-white text-[9px] rounded-lg shadow-2xl z-[100] text-center pointer-events-none">
                                                                Parte de la cuota que reduce la deuda principal.
                                                                <div
                                                                    class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-800">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th class="px-2 py-2 font-medium">
                                                    <div class="flex items-center gap-1">
                                                        Interés
                                                        <div class="relative group">
                                                            <svg class="w-2.5 h-2.5 text-gray-300 cursor-help" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            <div
                                                                class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-32 p-2 bg-gray-800 text-white text-[9px] rounded-lg shadow-2xl z-[100] text-center pointer-events-none">
                                                                Costo por el uso del dinero prestado en este periodo.
                                                                <div
                                                                    class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-800">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th class="px-2 py-2 font-medium">
                                                    <div class="flex items-center gap-1">
                                                        Saldo
                                                        <div class="relative group">
                                                            <svg class="w-2.5 h-2.5 text-gray-300 cursor-help" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            <div
                                                                class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-32 p-2 bg-gray-800 text-white text-[9px] rounded-lg shadow-2xl z-[100] text-center pointer-events-none">
                                                                Monto pendiente de pago después de esta cuota.
                                                                <div
                                                                    class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-800">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="scheduleBody"
                                            class="divide-y divide-gray-50 dark:divide-gray-900 text-gray-600 dark:text-gray-100">
                                            <!-- JS dynamic -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Disclaimer (desktop only) -->
                        <p
                            class="hidden md:block text-[10px] text-gray-500 dark:text-gray-100 leading-relaxed border-l-2 border-amber-300 dark:border-amber-800 pl-3">
                            Esto es solo una simulación. La <strong class="text-gray-500 dark:text-gray-100">aprobación
                                final</strong> y la <strong class="text-gray-500 dark:text-gray-100">comisión
                                definitiva</strong> son determinadas exclusivamente por la empresa.
                        </p>

                        <!-- Submit mobile -->
                        <div class="md:hidden flex gap-3">
                            <button type="button" id="toggleScheduleMobile"
                                class="flex-1 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-100 text-xs font-bold transition-colors uppercase tracking-tighter">
                                Ver cronograma
                            </button>
                            <button type="submit" id="submitBtnMobile"
                                class="flex-[2] py-3 rounded-xl bg-green-600 hover:bg-green-500 text-white text-xs font-bold transition-colors uppercase tracking-tighter disabled:opacity-30 disabled:cursor-not-allowed">
                                Enviar solicitud
                            </button>
                        </div>

                        <!-- Submit desktop -->
                        <button type="submit" id="submitBtnDesktop"
                            class="hidden md:block w-full py-3 rounded-xl bg-green-600 hover:bg-green-500 text-white text-sm font-semibold transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                            Enviar solicitud
                        </button>

                    </div>
                </div>

            </div>
        </form>

    </div>

    <!-- Modal Cronograma (Mobile) -->
    <div id="scheduleModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Overlay -->
            <div id="closeModalOverlay" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

            <!-- Modal content -->
            <div
                class="inline-block align-bottom bg-white dark:bg-gray-950 rounded-t-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:rounded-3xl">
                <div class="px-6 py-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Cronograma de Pagos</h3>
                            <p id="modalScheduleSubtitle" class="text-[10px] text-gray-400 uppercase tracking-widest mt-1">
                                Simulación Sistema Francés</p>
                        </div>
                        <button type="button" id="closeModalBtn" class="text-gray-400 hover:text-gray-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="overflow-x-auto rounded-2xl border border-gray-100 dark:border-gray-800">
                        <table class="w-full text-xs text-left min-w-[320px]">
                            <thead class="bg-gray-50 dark:bg-gray-900 text-gray-400">
                                <tr>
                                    <th class="px-3 py-3 font-medium">N°</th>
                                    <th class="px-3 py-3 font-medium">
                                        <div class="flex items-center gap-1">
                                            Capital
                                            <div class="relative group">
                                                <svg class="w-2.5 h-2.5 text-gray-300 cursor-help" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <div
                                                    class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-32 p-2 bg-gray-800 text-white text-[9px] rounded-lg shadow-2xl z-[100] text-center pointer-events-none">
                                                    Parte de la cuota que reduce la deuda principal.
                                                    <div
                                                        class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-800">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </th>
                                    <th class="px-3 py-3 font-medium">
                                        <div class="flex items-center gap-1">
                                            Interés
                                            <div class="relative group">
                                                <svg class="w-2.5 h-2.5 text-gray-300 cursor-help" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <div
                                                    class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-32 p-2 bg-gray-800 text-white text-[9px] rounded-lg shadow-2xl z-[100] text-center pointer-events-none">
                                                    Costo por el uso del dinero prestado en este periodo.
                                                    <div
                                                        class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-800">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </th>
                                    <th class="px-3 py-3 font-medium">
                                        <div class="flex items-center gap-1">
                                            Saldo
                                            <div class="relative group">
                                                <svg class="w-2.5 h-2.5 text-gray-300 cursor-help" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <div
                                                    class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-32 p-2 bg-gray-800 text-white text-[9px] rounded-lg shadow-2xl z-[100] text-center pointer-events-none">
                                                    Monto pendiente de pago después de esta cuota.
                                                    <div
                                                        class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-800">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="modalScheduleBody"
                                class="divide-y divide-gray-50 dark:divide-gray-900 text-gray-600 dark:text-gray-100">
                                <!-- JS dynamic -->
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-8">
                        <button type="button" id="closeModalBtnBottom"
                            class="w-full py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white text-sm font-bold uppercase tracking-widest">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #22c55e;
            cursor: pointer;
            border: 2px solid white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
        }

        input[type="range"]::-moz-range-thumb {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #22c55e;
            cursor: pointer;
            border: 2px solid white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
        }

        .dark input[type="range"]::-webkit-slider-thumb {
            border-color: #030712;
        }

        .dark input[type="range"]::-moz-range-thumb {
            border-color: #030712;
        }

        select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%239ca3af'%3E%3Cpath fill-rule='evenodd' d='M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0 center;
            background-size: 16px;
            padding-right: 20px;
        }

        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
        }

        /* Ocultar icono nativo de calendario para usar el personalizado que cambie de color */
        .calendar-picker-indicator-none::-webkit-calendar-picker-indicator {
            display: none;
            -webkit-appearance: none;
        }

        /* Modern Dark Date Picker Styling */
        @media (prefers-color-scheme: dark) {
            input[type="date"]::-webkit-calendar-picker-indicator {
                filter: invert(1);
            }
        }

        .dark input[type="date"] {
            color-scheme: dark;
        }

        @media (max-width: 768px) {
            [data-freq-tooltip] {
                display: none !important;
            }
        }
    </style>

    <script>
        const USER_CUSTOM_RATE = {{ number_format(Auth::user()->loan_interest_rate ?? 20, 2, '.', '') }};

        document.addEventListener('DOMContentLoaded', function () {
            const amount = document.getElementById('amount');
            const months = document.getElementById('months');
            const monthsDisplay = document.getElementById('monthsDisplay');
            const reason = document.getElementById('reason');
            const charCount = document.getElementById('charCount');
            const currencySymbol = document.getElementById('currencySymbol');
            const currencyRadios = document.querySelectorAll('input[name="currency"]');
            const typeRadios = document.querySelectorAll('input[name="type"]');
            const paymentDaysRadios = document.querySelectorAll('input[name="payment_days"]');
            const cuotasBlock = document.getElementById('cuotasBlock');
            const unicoBlock = document.getElementById('unicoBlock');
            const frequencyBlock = document.getElementById('frequencyBlock');
            const frequencyRadios = document.querySelectorAll('input[name="payment_frequency"]');
            const amortizationSystemBlock = document.getElementById('amortizationSystemBlock');
            const amortizationRadios = document.querySelectorAll('input[name="amortization_system"]');

            const simAmount = document.getElementById('simAmount');
            const simCurrency = document.getElementById('simCurrency');
            const simMonths = document.getElementById('simMonths');
            const simType = document.getElementById('simType');
            const simInterest = document.getElementById('simInterest');
            const simInterestLabel = document.getElementById('simInterestLabel');
            const simTotal = document.getElementById('simTotal');
            const simMonthly = document.getElementById('simMonthly');
            const simMonthlyRow = document.getElementById('simMonthlyRow');
            const simDisbursementDate = document.getElementById('simDisbursementDate');
            const disbursementDateInput = document.getElementById('disbursement_date');

            const sym = () => document.querySelector('input[name="currency"]:checked')?.value === 'USD' ? '$' : 'S/';
            const fmt = n => n.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            function formatNumber(n) {
                let parts = n.split('.');
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                return parts.join('.');
            }

            function toggleBlocks() {
                const isCuotas = document.querySelector('input[name="type"]:checked')?.value === 'cuotas';
                const system = document.querySelector('input[name="amortization_system"]:checked')?.value || 'frances';

                cuotasBlock.classList.toggle('hidden', !isCuotas);
                amortizationSystemBlock.classList.toggle('hidden', !isCuotas);
                unicoBlock.classList.toggle('hidden', isCuotas);

                // Frecuencia solo para Francés
                frequencyBlock.classList.toggle('hidden', !isCuotas || system !== 'frances');

                // Mostrar/Ocultar botón de cronograma en móvil
                const toggleBtn = document.getElementById('toggleScheduleMobile');
                if (toggleBtn) {
                    toggleBtn.classList.toggle('hidden', !isCuotas);
                }
            }

            function filterBankAccounts() {
                const selectedCurrency = document.querySelector('input[name="currency"]:checked')?.value;
                const select = document.getElementById('bank_account_id');
                if (!select) return;

                const options = select.querySelectorAll('option[data-currency]');
                let matchCount = 0;

                options.forEach(opt => {
                    if (opt.dataset.currency === selectedCurrency) {
                        opt.classList.remove('hidden');
                        opt.disabled = false;
                        matchCount++;
                    } else {
                        opt.classList.add('hidden');
                        opt.disabled = true;
                    }
                });

                const group = document.getElementById('accountSelectorGroup');
                const warning = document.getElementById('noAccountWarning');
                const missingName = document.getElementById('missingCurrencyName');

                if (matchCount === 0) {
                    if (group) group.classList.add('hidden');
                    if (warning) {
                        warning.classList.remove('hidden');
                    } else if (group) {
                        const noAccountDiv = document.createElement('div');
                        noAccountDiv.id = 'noAccountWarning';
                        noAccountDiv.className = 'bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-900/50 rounded-2xl p-6';
                        noAccountDiv.innerHTML = `<div class="flex gap-4"><div class="text-red-500"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div><div><h4 class="text-sm font-bold text-red-900 dark:text-red-400 uppercase tracking-widest mb-1">No tienes cuentas en <span id="missingCurrencyName">${selectedCurrency === 'USD' ? 'Dólares' : 'Soles'}</span></h4><p class="text-xs text-red-700 dark:text-red-500/80 mb-4 leading-relaxed">Para solicitar un préstamo en esta moneda, primero debes registrar una cuenta bancaria correspondiente.</p><button type="button" onclick="openBankModal('${selectedCurrency}')" class="inline-flex items-center text-[10px] font-black text-red-900 dark:text-red-400 uppercase tracking-[0.2em] border-b-2 border-red-900 dark:border-red-400 pb-0.5 hover:opacity-70 transition-opacity bg-transparent cursor-pointer">Registrar cuenta ahora</button></div></div>`;
                        group.after(noAccountDiv);
                    }
                    if (missingName) missingName.textContent = selectedCurrency === 'USD' ? 'Dólares' : 'Soles';
                    select.required = false;
                    select.value = '';
                } else {
                    if (group) group.classList.remove('hidden');
                    if (warning) warning.classList.add('hidden');
                    select.required = true;

                    const currentOpt = select.options[select.selectedIndex];
                    if (currentOpt && currentOpt.classList.contains('hidden')) {
                        select.value = '';
                    }
                }

                const submitMobile = document.getElementById('submitBtnMobile');
                const submitDesktop = document.getElementById('submitBtnDesktop');
                if (submitMobile) submitMobile.disabled = matchCount === 0;
                if (submitDesktop) submitDesktop.disabled = matchCount === 0;
            }

            function update() {
                const a = parseFloat(amount.value.replace(/,/g, '')) || 0;
                const m = parseInt(months.value) || 3;
                const s = sym();
                const isCuotas = document.querySelector('input[name="type"]:checked')?.value === 'cuotas';
                const system = document.querySelector('input[name="amortization_system"]:checked')?.value || 'frances';
                const freq = (isCuotas && system === 'frances')
                    ? (document.querySelector('input[name="payment_frequency"]:checked')?.value || 'mensual')
                    : 'mensual';

                let interestRate, interest, total, periodicPayment, plazoText, nTotal;

                if (isCuotas) {
                    const monthlyRate = USER_CUSTOM_RATE / 100;

                    let installmentsPerMonth = 1;
                    if (system === 'frances') {
                        if (freq === 'diario') installmentsPerMonth = 30;
                        else if (freq === 'semanal') installmentsPerMonth = 4;
                        else if (freq === 'quincenal') installmentsPerMonth = 2;
                    }

                    nTotal = m * installmentsPerMonth;
                    const periodicRate = monthlyRate / installmentsPerMonth;

                    if (system === 'frances') {
                        // Sistema Francés
                        if (a > 0) {
                            periodicPayment = (a * periodicRate) / (1 - Math.pow(1 + periodicRate, -nTotal));
                            total = periodicPayment * nTotal;
                            interest = total - a;
                        } else {
                            periodicPayment = 0; total = 0; interest = 0;
                        }
                        simInterestLabel.textContent = 'Interés Total (Sistema Francés)';
                        document.getElementById('modalScheduleSubtitle').textContent = 'Simulación Sistema Francés (' + freq + ')';
                    } else {
                        // Sistema Americano (Siempre mensual)
                        interest = (a * monthlyRate) * m;
                        total = a + interest;
                        periodicPayment = a * monthlyRate;
                        simInterestLabel.textContent = 'Interés Total (Sistema Americano)';
                        document.getElementById('modalScheduleSubtitle').textContent = 'Simulación Sistema Americano';
                        nTotal = m; // reset for American
                    }

                    plazoText = m + (m === 1 ? ' mes' : ' meses');
                } else {
                    const days = parseInt(document.querySelector('input[name="payment_days"]:checked')?.value) || 15;
                    const halfRate = Math.round((USER_CUSTOM_RATE / 2) * 100) / 100;
                    interestRate = days === 15 ? (halfRate / 100) : (USER_CUSTOM_RATE / 100);
                    interest = a * interestRate;
                    total = a + interest;
                    periodicPayment = 0;
                    plazoText = days + ' días';
                    simInterestLabel.textContent = 'Comisión (' + (interestRate * 100) + '%)';
                }

                currencySymbol.textContent = s;
                simAmount.textContent = s + ' ' + fmt(a);
                simCurrency.textContent = s === '$' ? 'Dólares' : 'Soles';
                simMonths.textContent = plazoText;
                simType.textContent = isCuotas ? (system === 'frances' ? 'Cuotas (Francés - ' + freq + ')' : 'Cuotas (Americano)') : 'Pago único';
                simInterest.textContent = s + ' ' + fmt(interest);
                simTotal.textContent = s + ' ' + fmt(total);
                simDisbursementDate.textContent = disbursementDateInput.value || '—';

                if (isCuotas && system === 'americano') {
                    simMonthly.textContent = s + ' ' + fmt(periodicPayment) + ' (Final: ' + s + ' ' + fmt(a + periodicPayment) + ')';
                } else {
                    simMonthly.textContent = s + ' ' + fmt(periodicPayment);
                }

                document.querySelector('#simMonthlyRow span:last-child').textContent = isCuotas ? '/ ' + freq.replace('o', '').replace('al', '') : '';
                if (freq === 'diario') document.querySelector('#simMonthlyRow span:last-child').textContent = '/ día';
                if (freq === 'quincenal') document.querySelector('#simMonthlyRow span:last-child').textContent = '/ quincena';

                simMonthlyRow.style.display = isCuotas ? '' : 'none';
                monthsDisplay.textContent = m + (m === 1 ? ' mes' : ' meses');

                // Actualizar cronograma
                if (isCuotas && a > 0) {
                    const monthlyRate = USER_CUSTOM_RATE / 100;
                    let installmentsPerMonth = 1;
                    if (system === 'frances') {
                        if (freq === 'diario') installmentsPerMonth = 30;
                        else if (freq === 'semanal') installmentsPerMonth = 4;
                        else if (freq === 'quincenal') installmentsPerMonth = 2;
                    }
                    const periodicRate = monthlyRate / installmentsPerMonth;
                    updateSchedule(a, periodicRate, nTotal, s, system);
                    document.getElementById('scheduleSection').classList.remove('hidden');
                } else {
                    document.getElementById('scheduleSection').classList.add('hidden');
                }

                updateFreqTooltips();
            }

            function updateFreqTooltips() {
                const a = parseFloat(amount.value.replace(/,/g, '')) || 0;
                const m = parseInt(months.value) || 2;
                const s = sym();
                const monthlyRate = USER_CUSTOM_RATE / 100;
                const freqMap = { diario: 30, semanal: 4, quincenal: 2, mensual: 1 };
                const fmtShort = (n) => parseFloat(n).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

                document.querySelectorAll('[data-freq-tooltip]').forEach(el => {
                    const freq = el.dataset.freqTooltip;
                    const valEl = el.querySelector('.freq-cuota-value');
                    if (a <= 0) { valEl.textContent = '—'; return; }
                    const ipm = freqMap[freq] || 1;
                    const pRate = monthlyRate / ipm;
                    const nTotal = m * ipm;
                    const cuota = (a * pRate) / (1 - Math.pow(1 + pRate, -nTotal));
                    valEl.textContent = s + ' ' + fmtShort(cuota);
                });
            }

            function updateSchedule(principal, periodicRate, totalInstallments, symbol, system) {
                const body = document.getElementById('scheduleBody');
                const modalBody = document.getElementById('modalScheduleBody');
                body.innerHTML = '';
                modalBody.innerHTML = '';

                let balance = principal;
                const SHOW_FIRST = 10;
                const SHOW_LAST = 5;
                const limitTruncate = totalInstallments > (SHOW_FIRST + SHOW_LAST);

                if (system === 'frances') {
                    const periodicPayment = (principal * periodicRate) / (1 - Math.pow(1 + periodicRate, -totalInstallments));

                    for (let i = 1; i <= totalInstallments; i++) {
                        const interest = balance * periodicRate;
                        const capital = periodicPayment - interest;
                        balance -= capital;

                        if (!limitTruncate || i <= SHOW_FIRST || i > totalInstallments - SHOW_LAST) {
                            renderRow(i, capital, interest, balance, body, modalBody, symbol);
                        } else if (limitTruncate && i === SHOW_FIRST + 1) {
                            const spacerHtml = `<td colspan="4" class="text-center py-4 text-gray-400 font-medium italic">... ${totalInstallments - SHOW_FIRST - SHOW_LAST} cuotas intermedias ocultas ...</td>`;

                            const row = document.createElement('tr');
                            row.innerHTML = spacerHtml;
                            body.appendChild(row);

                            const modalRow = document.createElement('tr');
                            modalRow.innerHTML = spacerHtml;
                            modalBody.appendChild(modalRow);
                        }
                    }
                } else {
                    // Sistema Americano
                    for (let i = 1; i <= totalInstallments; i++) {
                        const interest = principal * periodicRate;
                        const capital = (i === totalInstallments) ? principal : 0;
                        balance = (i === totalInstallments) ? 0 : principal;

                        if (!limitTruncate || i <= SHOW_FIRST || i > totalInstallments - SHOW_LAST) {
                            renderRow(i, capital, interest, balance, body, modalBody, symbol);
                        } else if (limitTruncate && i === SHOW_FIRST + 1) {
                            const spacerHtml = `<td colspan="4" class="text-center py-4 text-gray-400 font-medium italic">... ${totalInstallments - SHOW_FIRST - SHOW_LAST} cuotas intermedias ocultas ...</td>`;

                            const row = document.createElement('tr');
                            row.innerHTML = spacerHtml;
                            body.appendChild(row);

                            const modalRow = document.createElement('tr');
                            modalRow.innerHTML = spacerHtml;
                            modalBody.appendChild(modalRow);
                        }
                    }
                }
            }

            function renderRow(i, capital, interest, balance, body, modalBody, symbol) {
                const html = `
                                                                                                    <td class="px-2 py-2 font-medium text-gray-900 dark:text-white">${i}</td>
                                                                                                    <td class="px-2 py-2 whitespace-nowrap">${symbol} ${fmt(capital)}</td>
                                                                                                    <td class="px-2 py-2 whitespace-nowrap">${symbol} ${fmt(interest)}</td>
                                                                                                    <td class="px-2 py-2 whitespace-nowrap">${symbol} ${fmt(Math.max(0, balance))}</td>
                                                                                                `;

                if (body) {
                    const row = document.createElement('tr');
                    row.innerHTML = html;
                    body.appendChild(row);
                }

                if (modalBody) {
                    const modalRow = document.createElement('tr');
                    modalRow.innerHTML = html.replace(/px-2 py-2/g, 'px-3 py-3');
                    modalBody.appendChild(modalRow);
                }
            }

            amount.addEventListener('input', function (e) {
                let start = this.selectionStart;
                let oldVal = this.value;

                // Solo permitir números
                let val = oldVal.replace(/\D/g, '');

                if (val.length === 0) {
                    val = '0.00';
                } else {
                    // Asegurar que tenga al menos 3 dígitos para el formato XX.XX
                    while (val.length < 3) {
                        val = '0' + val;
                    }

                    // Insertar punto decimal antes de los últimos 2 dígitos
                    let integerPart = val.slice(0, -2);
                    let decimalPart = val.slice(-2);

                    // Eliminar ceros a la izquierda del entero
                    integerPart = parseInt(integerPart, 10).toString();

                    // Formatear miles con coma
                    integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ",");

                    val = integerPart + '.' + decimalPart;
                }

                if (oldVal !== val) {
                    this.value = val;
                    // Ajustar posición del cursor (aproximado para este tipo de entrada)
                    this.setSelectionRange(val.length, val.length);
                }

                update();
            });

            document.getElementById('loanForm').addEventListener('submit', function () {
                amount.value = amount.value.replace(/,/g, '');
            });

            disbursementDateInput.addEventListener('input', update);
            months.addEventListener('input', update);
            currencyRadios.forEach(r => r.addEventListener('change', () => { filterBankAccounts(); update(); }));
            typeRadios.forEach(r => r.addEventListener('change', () => { toggleBlocks(); update(); }));
            frequencyRadios.forEach(r => r.addEventListener('change', update));
            paymentDaysRadios.forEach(r => r.addEventListener('change', update));
            amortizationRadios.forEach(r => r.addEventListener('change', () => { toggleBlocks(); update(); }));
            reason.addEventListener('input', () => charCount.textContent = reason.value.length);

            const toggleSim = document.getElementById('toggleSim');
            const simContent = document.getElementById('simContent');
            if (toggleSim && simContent) {
                let simExpanded = true;
                toggleSim.addEventListener('click', () => {
                    simExpanded = !simExpanded;
                    simContent.classList.toggle('hidden', !simExpanded);
                    toggleSim.querySelector('svg').style.transform = simExpanded ? '' : 'rotate(180deg)';
                });
            }

            const toggleScheduleMobile = document.getElementById('toggleScheduleMobile');
            const scheduleModal = document.getElementById('scheduleModal');
            const closeModalElements = [
                document.getElementById('closeModalBtn'),
                document.getElementById('closeModalBtnBottom'),
                document.getElementById('closeModalOverlay')
            ];

            if (toggleScheduleMobile) {
                toggleScheduleMobile.addEventListener('click', () => {
                    scheduleModal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden'; // Evitar scroll de fondo
                });
            }

            closeModalElements.forEach(el => {
                if (el) {
                    el.addEventListener('click', () => {
                        scheduleModal.classList.add('hidden');
                        document.body.style.overflow = '';
                    });
                }
            });

            // Abrir selector de fecha al hacer click en el contenedor
            const disbursementContainer = document.querySelector('#disbursement_date').parentElement;
            disbursementContainer.addEventListener('click', () => {
                try {
                    disbursementDateInput.showPicker();
                } catch (e) {
                    disbursementDateInput.click();
                }
            });

            if (amount.value) {
                amount.value = formatNumber(amount.value.replace(/[^0-9.]/g, ''));
            }
            toggleBlocks();
            filterBankAccounts();
            update();
            charCount.textContent = reason.value.length;
        });
    </script>

<style>
    .pk-modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 100;
        display: none;
        align-items: flex-end;
        justify-content: center;
    }
    .pk-modal-overlay.active { display: flex; }
    @media (min-width: 768px) {
        .pk-modal-overlay.active { align-items: center; padding: 1rem; }
    }
    .pk-modal-content {
        position: relative;
        z-index: 10;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 24px 24px 0 0;
        width: 100%;
        max-height: 85vh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    .dark .pk-modal-content {
        background: #111827;
        border-color: #1f2937;
    }
    @media (min-width: 768px) {
        .pk-modal-content {
            border-radius: 24px;
            max-width: 480px;
            max-height: 80vh;
        }
    }
</style>

{{-- Modal Cuentas Bancarias --}}
<div id="bankModal" class="pk-modal-overlay" role="dialog" aria-modal="true">
    <div onclick="closeBankModal()" class="absolute inset-0 bg-black/60"></div>
    <div class="pk-modal-content">
        <div class="px-6 pt-6 pb-4 flex-shrink-0">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-[#D4910E]/10 dark:bg-[#F5A623]/10 flex items-center justify-center">
                        <svg class="w-4.5 h-4.5 text-[#D4910E] dark:text-[#F5A623]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 003-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Cuentas Bancarias</h3>
                        <p class="text-[11px] text-gray-500 dark:text-gray-100 mt-0.5">Donde recibirás tus desembolsos</p>
                    </div>
                </div>
                <button type="button" onclick="closeBankModal()" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-500 dark:text-gray-100 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto px-6 pb-4">
            <div id="bankAccountsList" class="space-y-2">
                <div class="animate-pulse space-y-2">
                    <div class="h-16 bg-gray-50 dark:bg-gray-900 rounded-xl"></div>
                    <div class="h-16 bg-gray-50 dark:bg-gray-900 rounded-xl"></div>
                </div>
            </div>
            <div id="bankAccountForm" class="hidden pt-5 mt-5 border-t border-gray-100 dark:border-gray-800/50">
                <h4 id="formTitle" class="text-[11px] font-semibold text-gray-500 dark:text-gray-100 uppercase tracking-widest mb-5">Nueva Cuenta</h4>
                <form id="actualBankForm" onsubmit="saveBankAccount(event)" data-no-loading class="space-y-4">
                    <input type="hidden" id="editingId" value="">
                    <div>
                        <label class="block text-[11px] font-medium text-gray-500 dark:text-gray-100 mb-1.5 uppercase tracking-wider">Alias</label>
                        <input type="text" id="f_alias" placeholder="Ej: Mi cuenta BCP" class="pk-input w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm px-0 py-2.5 focus:outline-none focus:border-[#006BFF] dark:focus:border-[#4A90E2] transition-colors text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-100">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-medium text-gray-500 dark:text-gray-100 mb-1.5 uppercase tracking-wider">Banco</label>
                            <select id="f_bank_name" required class="pk-input w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm px-0 py-2.5 focus:outline-none focus:border-[#006BFF] dark:focus:border-[#4A90E2] transition-colors text-gray-900 dark:text-white">
                                <option value="" class="dark:bg-gray-900">Seleccionar</option>
                                @foreach(['BCP', 'Interbank', 'BBVA', 'Scotiabank', 'BanBif', 'Banco de la Nación', 'Caja Piura', 'Caja Huancayo', 'Caja Arequipa'] as $bank)
                                    <option value="{{ $bank }}" class="dark:bg-gray-900">{{ $bank }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-gray-500 dark:text-gray-100 mb-1.5 uppercase tracking-wider">Tipo</label>
                            <select id="f_account_type" required class="pk-input w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm px-0 py-2.5 focus:outline-none focus:border-[#006BFF] dark:focus:border-[#4A90E2] transition-colors text-gray-900 dark:text-white">
                                <option value="ahorros" class="dark:bg-gray-900">Ahorros</option>
                                <option value="corriente" class="dark:bg-gray-900">Corriente</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-gray-500 dark:text-gray-100 mb-1.5 uppercase tracking-wider">Moneda</label>
                            <select id="f_currency" required class="pk-input w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm px-0 py-2.5 focus:outline-none focus:border-[#006BFF] dark:focus:border-[#4A90E2] transition-colors text-gray-900 dark:text-white">
                                <option value="PEN" class="dark:bg-gray-900">Soles (S/)</option>
                                <option value="USD" class="dark:bg-gray-900">Dólares ($)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-gray-500 dark:text-gray-100 mb-1.5 uppercase tracking-wider">Nro. Cuenta</label>
                            <input type="text" id="f_account_number" required placeholder="19134567890123" maxlength="14" oninput="this.value=this.value.replace(/[^0-9]/g,'')" class="pk-input w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm px-0 py-2.5 focus:outline-none focus:border-[#006BFF] dark:focus:border-[#4A90E2] transition-colors tabular-nums text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-100">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-medium text-gray-500 dark:text-gray-100 mb-1.5 uppercase tracking-wider">CCI</label>
                        <input type="text" id="f_cci" required placeholder="00219100123456789012" maxlength="20" oninput="this.value=this.value.replace(/[^0-9]/g,'')" class="pk-input w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm px-0 py-2.5 focus:outline-none focus:border-[#006BFF] dark:focus:border-[#4A90E2] transition-colors tabular-nums text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-100">
                    </div>
                    <div class="flex gap-3 pt-3">
                        <button type="submit" class="flex-1 py-2.5 rounded-xl bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-sm font-semibold hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors">Guardar</button>
                        <button type="button" onclick="hideBankForm()" class="px-5 py-2.5 rounded-xl text-gray-500 dark:text-gray-100 text-sm font-medium hover:text-gray-600 dark:hover:text-gray-300 transition-colors">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
        <div id="addBtnContainer" class="px-6 py-4 border-t border-gray-100 dark:border-gray-800/50 flex-shrink-0">
            <button type="button" onclick="showBankForm()" class="w-full py-2.5 rounded-xl text-sm font-medium text-[#006BFF] dark:text-[#4A90E2] hover:bg-[#006BFF]/5 dark:hover:bg-[#4A90E2]/5 transition-colors flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Agregar cuenta
            </button>
        </div>
    </div>
</div>

{{-- Delete Confirm Modal --}}
<div id="deleteConfirmModal" class="pk-modal-overlay" role="dialog" aria-modal="true" style="align-items:center;padding:1rem;">
    <div onclick="closeDeleteConfirm()" class="absolute inset-0 bg-black/60"></div>
    <div class="pk-modal-content" style="max-width:360px;">
        <div class="px-6 pt-8 pb-6 text-center">
            <div class="w-12 h-12 rounded-2xl bg-[#E94560]/10 dark:bg-[#E94560]/20 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-[#E94560]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white">Eliminar cuenta</h3>
            <p class="text-sm text-gray-500 dark:text-gray-100 mt-1.5">¿Estás seguro? Esta acción no se puede deshacer.</p>
        </div>
        <div class="px-6 pb-6 flex gap-3">
            <button type="button" onclick="closeDeleteConfirm()" class="flex-1 py-2.5 rounded-xl text-sm font-medium text-gray-500 dark:text-gray-100 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">Cancelar</button>
            <button type="button" onclick="confirmDeleteAccount()" class="flex-1 py-2.5 rounded-xl bg-[#E94560] text-white text-sm font-semibold hover:bg-[#d63d56] transition-colors">Eliminar</button>
        </div>
    </div>
</div>

<div id="deleteErrorModal" class="pk-modal-overlay" role="dialog" aria-modal="true" style="align-items:center;padding:1rem;">
    <div onclick="closeDeleteError()" class="absolute inset-0 bg-black/60"></div>
    <div class="pk-modal-content" style="max-width:360px;">
        <div class="px-6 pt-8 pb-6 text-center">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 dark:bg-amber-500/20 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white">No se puede eliminar</h3>
            <p class="text-sm text-gray-500 dark:text-gray-100 mt-1.5">Esta cuenta está vinculada a las siguientes operaciones:</p>
            <ul id="deleteErrorList" class="mt-3 space-y-1.5 text-left"></ul>
        </div>
        <div class="px-6 pb-6">
            <button type="button" onclick="closeDeleteError()" class="w-full py-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 text-sm font-semibold text-gray-700 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">Entendido</button>
        </div>
    </div>
</div>

<script>
    let bankAccounts = [];

    function openBankModal(currency) {
        document.getElementById('bankModal').classList.add('active');
        document.body.style.overflow = 'hidden';
        loadBankAccounts();
        if (currency) {
            showBankForm();
            document.getElementById('f_currency').value = currency;
        }
    }

    function closeBankModal() {
        document.getElementById('bankModal').classList.remove('active');
        document.body.style.overflow = '';
        hideBankForm();
    }

    async function loadBankAccounts() {
        const list = document.getElementById('bankAccountsList');
        try {
            const response = await fetch('{{ route("client.bank-accounts.index") }}');
            bankAccounts = await response.json();
            if (bankAccounts.length === 0) {
                list.innerHTML = '<div class="py-10 text-center"><svg class="w-10 h-10 mx-auto text-gray-400 dark:text-gray-100 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 003-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg><p class="text-sm text-gray-400 dark:text-gray-100">Sin cuentas registradas</p></div>';
                return;
            }
            list.innerHTML = bankAccounts.map(acc => `
                <div class="group flex items-center justify-between py-3 border-b border-gray-50 dark:border-gray-800/50 last:border-0">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">${acc.alias || acc.bank_name}</span>
                            <span class="text-[10px] font-medium text-gray-500 dark:text-gray-100 bg-gray-50 dark:bg-gray-800/50 px-1.5 py-0.5 rounded">${acc.bank_name}</span>
                        </div>
                        <p class="text-[11px] text-gray-500 dark:text-gray-100 tabular-nums mt-0.5">${acc.account_type === 'ahorros' ? 'Ahorros' : 'Corriente'} · ${acc.currency === 'PEN' ? 'S/' : '$'} · ${acc.account_number}</p>
                    </div>
                    <div class="flex gap-0.5 flex-shrink-0 ml-3">
                        <button onclick="editBankAccount(${acc.id})" class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 dark:text-gray-100 hover:text-[#006BFF] dark:hover:text-[#4A90E2] hover:bg-[#006BFF]/5 dark:hover:bg-[#4A90E2]/5 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>
                        <button onclick="deleteBankAccount(${acc.id})" class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 dark:text-gray-100 hover:text-[#E94560] hover:bg-[#E94560]/5 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            `).join('');
        } catch (e) {
            list.innerHTML = '<p class="text-red-500 text-sm">Error al cargar</p>';
        }
    }

    function showBankForm() {
        document.getElementById('bankAccountForm').classList.remove('hidden');
        document.getElementById('addBtnContainer').classList.add('hidden');
    }

    function hideBankForm() {
        document.getElementById('bankAccountForm').classList.add('hidden');
        document.getElementById('addBtnContainer').classList.remove('hidden');
        document.getElementById('actualBankForm').reset();
        document.getElementById('editingId').value = '';
        document.getElementById('formTitle').textContent = 'Nueva Cuenta';
    }

    function editBankAccount(id) {
        const acc = bankAccounts.find(a => a.id === id);
        if (!acc) return;
        document.getElementById('editingId').value = acc.id;
        document.getElementById('f_alias').value = acc.alias || '';
        document.getElementById('f_bank_name').value = acc.bank_name;
        document.getElementById('f_account_type').value = acc.account_type;
        document.getElementById('f_currency').value = acc.currency || 'PEN';
        document.getElementById('f_account_number').value = acc.account_number;
        document.getElementById('f_cci').value = acc.cci || '';
        document.getElementById('formTitle').textContent = 'Editar Cuenta';
        showBankForm();
    }

    async function saveBankAccount(e) {
        e.preventDefault();
        e.stopPropagation();
        const id = document.getElementById('editingId').value;
        const data = {
            alias: document.getElementById('f_alias').value,
            bank_name: document.getElementById('f_bank_name').value,
            account_type: document.getElementById('f_account_type').value,
            currency: document.getElementById('f_currency').value,
            account_number: document.getElementById('f_account_number').value,
            cci: document.getElementById('f_cci').value,
            _token: '{{ csrf_token() }}'
        };
        const url = id ? '/panel/cuentas-bancarias/' + id : '{{ route("client.bank-accounts.store") }}';
        const method = id ? 'PUT' : 'POST';
        try {
            const response = await fetch(url, { method, headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify(data) });
            if (!response.ok) {
                let msg = 'Error al guardar';
                try { const err = await response.json(); if (err.message) msg = err.message; } catch (_) {}
                pkHideLoading();
                return alert(msg);
            }
            const result = await response.json();
            if (result.success) {
                hideBankForm();
                refreshBankSelect(result.account || null);
                loadBankAccounts();
            } else {
                alert(result.message || 'Error al guardar');
            }
            pkHideLoading();
        } catch (e) { console.error(e); alert('Error de conexión'); pkHideLoading(); }
    }

    function pkHideLoading() {
        var overlay = document.getElementById('pk-loading');
        if (overlay) {
            overlay.classList.remove('opacity-100', 'pointer-events-auto');
            overlay.classList.add('opacity-0');
        }
    }

    function refreshBankSelect(newAccount) {
        let select = document.getElementById('bank_account_id');
        const warningSection = document.getElementById('noAccountsWarning');
        const noAccountWarning = document.getElementById('noAccountWarning');

        fetch('{{ route("client.bank-accounts.index") }}')
            .then(r => r.json())
            .then(accounts => {
                const targetEl = warningSection || noAccountWarning;

                if (targetEl && accounts.length > 0) {
                    const existingGroup = document.getElementById('accountSelectorGroup');
                    if (existingGroup) existingGroup.remove();
                    const selectorHTML = `
                        <div class="group" id="accountSelectorGroup">
                            <label for="bank_account_id" class="block text-xs text-gray-500 dark:text-gray-100 mb-2">Selecciona tu cuenta de destino</label>
                            <div class="relative">
                                <select name="bank_account_id" id="bank_account_id" required
                                    class="w-full px-0 py-3 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:border-green-500 transition-colors appearance-none cursor-pointer">
                                    <option value="" class="dark:bg-gray-950">Seleccionar cuenta</option>
                                </select>
                                <div class="absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-3 uppercase tracking-widest">¿Quieres usar otra? <button type="button" onclick="openBankModal()" class="text-green-600 dark:text-green-400 font-bold hover:underline bg-transparent cursor-pointer">Gestiónalas aquí</button></p>
                        </div>`;
                    targetEl.outerHTML = selectorHTML;
                    select = document.getElementById('bank_account_id');
                }

                if (!select) return;

                select.innerHTML = '<option value="" class="dark:bg-gray-950">Seleccionar cuenta</option>';

                if (newAccount && newAccount.id && !accounts.find(a => a.id === newAccount.id)) {
                    accounts.push(newAccount);
                }

                accounts.forEach(acc => {
                    const opt = document.createElement('option');
                    opt.value = acc.id;
                    opt.dataset.currency = acc.currency;
                    opt.className = 'dark:bg-gray-950';
                    opt.textContent = (acc.alias || acc.bank_name) + ' — ' + acc.account_number + ' (' + acc.currency + ')';
                    select.appendChild(opt);
                });

                if (newAccount && newAccount.id) {
                    select.value = newAccount.id;
                } else if (accounts.length > 0) {
                    select.value = accounts[accounts.length - 1].id;
                }

                filterBankAccounts();
            })
            .catch(() => {});
    }

    let pendingDeleteId = null;

    function deleteBankAccount(id) {
        pendingDeleteId = id;
        document.getElementById('deleteConfirmModal').classList.add('active');
    }

    function closeDeleteConfirm() {
        document.getElementById('deleteConfirmModal').classList.remove('active');
        pendingDeleteId = null;
    }

    async function confirmDeleteAccount() {
        if (!pendingDeleteId) return;
        const id = pendingDeleteId;
        closeDeleteConfirm();
        try {
            const response = await fetch('/panel/cuentas-bancarias/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } });
            const result = await response.json();
            if (result.success) {
                loadBankAccounts();
                refreshBankSelect();
                checkAccountsEmpty();
            } else {
                showDeleteError(result.message || 'No se pudo eliminar la cuenta', result.linked || []);
            }
        } catch (e) { showDeleteError('Error de conexión'); }
    }

    function showDeleteError(msg, linked) {
        const list = document.getElementById('deleteErrorList');
        list.innerHTML = '';
        if (linked && linked.length > 0) {
            linked.forEach(op => {
                const li = document.createElement('li');
                li.className = 'flex items-center gap-2 text-sm text-amber-600 dark:text-amber-400 font-medium';
                li.innerHTML = '<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' + op;
                list.appendChild(li);
            });
        } else {
            const li = document.createElement('li');
            li.className = 'text-sm text-gray-500 dark:text-gray-100';
            li.textContent = msg || 'Esta cuenta está asociada a otras operaciones.';
            list.appendChild(li);
        }
        document.getElementById('deleteErrorModal').classList.add('active');
    }

    function closeDeleteError() {
        document.getElementById('deleteErrorModal').classList.remove('active');
    }

    async function checkAccountsEmpty() {
        try {
            const response = await fetch('{{ route("client.bank-accounts.index") }}');
            const accounts = await response.json();
            if (accounts.length === 0) {
                const selectorGroup = document.getElementById('accountSelectorGroup');
                if (selectorGroup) {
                    selectorGroup.outerHTML = `
                        <div id="noAccountsWarning" class="bg-amber-50 dark:bg-amber-950/20 border border-amber-100 dark:border-amber-900/50 rounded-2xl p-6">
                            <div class="flex gap-4">
                                <div class="text-amber-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-amber-900 dark:text-amber-400 uppercase tracking-widest mb-1">No tienes cuentas registradas</h4>
                                    <p class="text-xs text-amber-700 dark:text-amber-500/80 mb-4 leading-relaxed">Para solicitar un préstamo, primero debes agregar una cuenta bancaria donde recibirás el dinero.</p>
                                    <button type="button" onclick="openBankModal()" class="inline-flex items-center text-[10px] font-black text-amber-900 dark:text-amber-400 uppercase tracking-[0.2em] border-b-2 border-amber-900 dark:border-amber-400 pb-0.5 hover:opacity-70 transition-opacity bg-transparent cursor-pointer">Agregar cuenta bancaria →</button>
                                </div>
                            </div>
                        </div>`;
                }
            }
        } catch (_) {}
    }
</script>
@endsection