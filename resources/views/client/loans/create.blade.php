@extends('client.layout')

@section('title', 'Solicitar Préstamo')

@section('content')
    <div class="min-h-screen px-4 md:px-10 py-6 md:py-8 max-w-6xl pb-36 md:pb-8">

        <!-- Header -->
        <div class="mb-10">
            <a href="{{ route('client.loans') }}"
                class="inline-flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors mb-4">
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
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">Importe y
                            moneda</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="amount"
                                    class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Monto</label>
                                <div class="relative">
                                    <span id="currencySymbol"
                                        class="absolute left-0 top-1/2 -translate-y-1/2 text-sm font-semibold text-gray-400 dark:text-gray-500 pointer-events-none">S/</span>
                                    <input type="text" name="amount" id="amount" value="{{ old('amount') }}"
                                        placeholder="0.00" inputmode="decimal"
                                        class="w-full pl-8 pr-0 py-3 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-xl font-semibold text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-gray-700 focus:outline-none focus:border-green-500 transition-colors">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Moneda</label>
                                <div class="flex gap-2 mt-1">
                                    <label class="cursor-pointer flex-1">
                                        <input type="radio" name="currency" value="PEN" class="hidden peer" {{ old('currency', 'PEN') === 'PEN' ? 'checked' : '' }}>
                                        <div
                                            class="flex items-center justify-center gap-2 py-2.5 text-sm text-gray-500 dark:text-gray-400 border-b-2 border-transparent peer-checked:border-green-500 peer-checked:text-green-600 dark:peer-checked:text-green-400 transition-all">
                                            <span>🇵🇪</span>
                                            <span class="font-medium">Soles</span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer flex-1">
                                        <input type="radio" name="currency" value="USD" class="hidden peer" {{ old('currency') === 'USD' ? 'checked' : '' }}>
                                        <div
                                            class="flex items-center justify-center gap-2 py-2.5 text-sm text-gray-500 dark:text-gray-400 border-b-2 border-transparent peer-checked:border-green-500 peer-checked:text-green-600 dark:peer-checked:text-green-400 transition-all">
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
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">Plazo</p>

                        <div class="space-y-6">
                            <!-- Fecha de desembolso -->
                            <div class="relative group">
                                <label for="disbursement_date"
                                    class="block text-xs text-gray-500 dark:text-gray-400 mb-2">Fecha estimada de
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
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-2">Tipo de pago</label>
                                <div class="flex gap-2">
                                    <label class="cursor-pointer flex-1">
                                        <input type="radio" name="type" value="cuotas" class="hidden peer" {{ old('type', 'cuotas') === 'cuotas' ? 'checked' : '' }}>
                                        <div
                                            class="text-center py-2.5 text-xs font-medium text-gray-500 dark:text-gray-400 border-b-2 border-transparent peer-checked:border-green-500 peer-checked:text-green-600 dark:peer-checked:text-green-400 transition-all">
                                            Cuotas mensuales
                                        </div>
                                    </label>
                                    <label class="cursor-pointer flex-1">
                                        <input type="radio" name="type" value="unico" class="hidden peer" {{ old('type') === 'unico' ? 'checked' : '' }}>
                                        <div
                                            class="text-center py-2.5 text-xs font-medium text-gray-500 dark:text-gray-400 border-b-2 border-transparent peer-checked:border-green-500 peer-checked:text-green-600 dark:peer-checked:text-green-400 transition-all">
                                            Pago único
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Sistema de Amortización (Solo si es cuotas) -->
                            <div id="amortizationSystemBlock">
                                <div class="flex items-center gap-1.5 mb-2">
                                    <label class="block text-xs text-gray-500 dark:text-gray-400">Sistema de
                                        amortización</label>
                                    <div class="relative group">
                                        <svg class="w-3 h-3 text-gray-300 dark:text-gray-700 cursor-help" fill="none"
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
                                            class="text-center py-2.5 text-xs font-medium text-gray-500 dark:text-gray-400 border-b-2 border-transparent peer-checked:border-green-500 peer-checked:text-green-600 dark:peer-checked:text-green-400 transition-all">
                                            Francés (Cuota fija)
                                        </div>
                                    </label>
                                    <label class="cursor-pointer flex-1">
                                        <input type="radio" name="amortization_system" value="americano" class="hidden peer"
                                            {{ old('amortization_system') === 'americano' ? 'checked' : '' }}>
                                        <div
                                            class="text-center py-2.5 text-xs font-medium text-gray-500 dark:text-gray-400 border-b-2 border-transparent peer-checked:border-green-500 peer-checked:text-green-600 dark:peer-checked:text-green-400 transition-all">
                                            Americano (Interés mensual)
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Cuotas: slider de meses (3-24) -->
                            <div id="cuotasBlock">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-1.5">
                                        <label for="months" class="text-xs text-gray-500 dark:text-gray-400">Plazo del
                                            préstamo</label>
                                        <div class="relative group">
                                            <svg class="w-3 h-3 text-gray-300 dark:text-gray-700 cursor-help" fill="none"
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
                                <div class="relative h-4 text-[10px] text-gray-300 dark:text-gray-700 mt-1.5">
                                    <span class="absolute left-0">3</span>
                                    <span class="absolute left-[14.28%] -translate-x-1/2">6</span>
                                    <span class="absolute left-[42.85%] -translate-x-1/2">12</span>
                                    <span class="absolute left-[71.42%] -translate-x-1/2">18</span>
                                    <span class="absolute right-0">24</span>
                                </div>
                            </div>

                            <!-- Frecuencia de pago (Solo para Francés) -->
                            <div id="frequencyBlock">
                                <div class="flex items-center gap-1.5 mb-2">
                                    <label class="block text-xs text-gray-500 dark:text-gray-400">Frecuencia de pago</label>
                                    <div class="relative group">
                                        <svg class="w-3 h-3 text-gray-300 dark:text-gray-700 cursor-help" fill="none"
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
                                                class="text-center py-2 text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 border-b-2 border-transparent peer-checked:border-green-500 peer-checked:text-green-600 dark:peer-checked:text-green-400 transition-all">
                                                {{ $label }}
                                            </div>
                                            <div data-freq-tooltip="{{ $val }}"
                                                class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 flex flex-col items-center z-[100] pointer-events-none opacity-0 scale-95 -translate-y-1 group-hover/freq:opacity-100 group-hover/freq:scale-100 group-hover/freq:translate-y-0 transition-all duration-200 ease-out">
                                                <div class="px-3 py-1.5 bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 text-[9px] font-bold rounded-lg shadow-2xl whitespace-nowrap">
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
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-2">Pagar en</label>
                                <div class="flex gap-2">
                                    <label class="cursor-pointer flex-1">
                                        <input type="radio" name="payment_days" value="15" class="hidden peer" {{ old('payment_days', '15') == '15' ? 'checked' : '' }}>
                                        <div
                                            class="text-center py-3 border-b-2 border-transparent peer-checked:border-green-500 transition-all">
                                            <p
                                                class="text-sm font-semibold text-gray-700 dark:text-gray-300 peer-checked:text-green-600 dark:peer-checked:text-green-400">
                                                15 días</p>
                                            <p class="text-[10px] text-green-600 dark:text-green-400 mt-0.5">10% comisión
                                            </p>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer flex-1">
                                        <input type="radio" name="payment_days" value="30" class="hidden peer" {{ old('payment_days') == '30' ? 'checked' : '' }}>
                                        <div
                                            class="text-center py-3 border-b-2 border-transparent peer-checked:border-green-500 transition-all">
                                            <p
                                                class="text-sm font-semibold text-gray-700 dark:text-gray-300 peer-checked:text-green-600 dark:peer-checked:text-green-400">
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
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">Cuenta de
                            desembolso</p>

                        @if($bankAccounts->isEmpty())
                            <div
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
                                        <a href="{{ route('client.dashboard', ['open_bank' => 1]) }}"
                                            class="inline-flex items-center text-[10px] font-black text-amber-900 dark:text-amber-400 uppercase tracking-[0.2em] border-b-2 border-amber-900 dark:border-amber-400 pb-0.5 hover:opacity-70 transition-opacity">
                                            Ir al inicio para agregar cuenta
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="group" id="accountSelectorGroup">
                                <label for="bank_account_id"
                                    class="block text-xs text-gray-500 dark:text-gray-400 mb-2">Selecciona tu cuenta de
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
                                <p class="text-[10px] text-gray-400 mt-3 uppercase tracking-widest">¿Quieres usar otra? <a
                                        href="{{ route('client.dashboard', ['open_bank' => 1]) }}"
                                        class="text-green-600 dark:text-green-400 font-bold hover:underline">Gestiónalas
                                        aquí</a></p>
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
                                        <a href="{{ route('client.dashboard') }}"
                                            class="inline-flex items-center text-[10px] font-black text-red-900 dark:text-red-400 uppercase tracking-[0.2em] border-b-2 border-red-900 dark:border-red-400 pb-0.5 hover:opacity-70 transition-opacity">
                                            Registrar cuenta ahora
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </section>

                    <!-- Motivo -->
                    <section>
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">Motivo</p>

                        <label for="reason" class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">¿Por qué necesitas
                            este préstamo?</label>
                        <textarea name="reason" id="reason" rows="3" placeholder="Describe brevemente el motivo..."
                            class="w-full px-0 py-3 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-gray-700 focus:outline-none focus:border-green-500 transition-colors resize-none">{{ old('reason') }}</textarea>
                        <p class="text-[10px] text-gray-300 dark:text-gray-700 mt-1 text-right tabular-nums"><span
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
                            <p class="text-[10px] text-gray-400 dark:text-gray-500 mb-1">Total a devolver</p>
                            <p id="simTotal"
                                class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight tabular-nums">
                                S/ 0.00</p>

                            <div id="simMonthlyRow" class="mt-1.5 md:mt-2">
                                <p class="text-xs text-green-600 dark:text-green-400 font-medium tabular-nums">
                                    <span id="simMonthly">S/ 0.00</span> <span
                                        class="text-gray-400 dark:text-gray-600 font-normal">/ mes</span>
                                </p>
                            </div>
                        </div>

                        <!-- Breakdown (desktop only) -->
                        <div id="simBreakdown" class="hidden md:block space-y-3 pt-2">
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-400 dark:text-gray-500">Monto solicitado</span>
                                <span id="simAmount" class="text-gray-700 dark:text-gray-300 tabular-nums">S/ 0.00</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-400 dark:text-gray-500">Moneda</span>
                                <span id="simCurrency" class="text-gray-700 dark:text-gray-300">Soles</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-400 dark:text-gray-500">Plazo</span>
                                <span id="simMonths" class="text-gray-700 dark:text-gray-300 tabular-nums">12 meses</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-400 dark:text-gray-500">Tipo</span>
                                <span id="simType" class="text-gray-700 dark:text-gray-300">Cuotas</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-400 dark:text-gray-500">Desembolso</span>
                                <span id="simDisbursementDate"
                                    class="text-gray-700 dark:text-gray-300 tabular-nums">—</span>
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
                                            class="divide-y divide-gray-50 dark:divide-gray-900 text-gray-600 dark:text-gray-400">
                                            <!-- JS dynamic -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Disclaimer (desktop only) -->
                        <p
                            class="hidden md:block text-[10px] text-gray-400 dark:text-gray-600 leading-relaxed border-l-2 border-amber-300 dark:border-amber-800 pl-3">
                            Esto es solo una simulación. La <strong class="text-gray-500 dark:text-gray-400">aprobación
                                final</strong> y la <strong class="text-gray-500 dark:text-gray-400">comisión
                                definitiva</strong> son determinadas exclusivamente por la empresa.
                        </p>

                        <!-- Submit mobile -->
                        <div class="md:hidden flex gap-3">
                            @if($bankAccounts->isNotEmpty())
                                <button type="button" id="toggleScheduleMobile"
                                    class="flex-1 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-xs font-bold transition-colors uppercase tracking-tighter">
                                    Ver cronograma
                                </button>
                                <button type="submit"
                                    class="flex-[2] py-3 rounded-xl bg-green-600 hover:bg-green-500 text-white text-xs font-bold transition-colors uppercase tracking-tighter">
                                    Enviar solicitud
                                </button>
                            @endif
                        </div>

                        <!-- Submit desktop -->
                        @if($bankAccounts->isNotEmpty())
                            <button type="submit"
                                class="hidden md:block w-full py-3 rounded-xl bg-green-600 hover:bg-green-500 text-white text-sm font-semibold transition-colors">
                                Enviar solicitud
                            </button>
                        @endif

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
                                class="divide-y divide-gray-50 dark:divide-gray-900 text-gray-600 dark:text-gray-400">
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
                    group.classList.add('hidden');
                    warning.classList.remove('hidden');
                    missingName.textContent = selectedCurrency === 'USD' ? 'Dólares' : 'Soles';
                    select.required = false;
                    select.value = '';
                } else {
                    group.classList.remove('hidden');
                    warning.classList.add('hidden');
                    select.required = true;

                    // Si la opción seleccionada quedó oculta, resetear select
                    const currentOpt = select.options[select.selectedIndex];
                    if (currentOpt && currentOpt.classList.contains('hidden')) {
                        select.value = '';
                    }
                }
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
                    interestRate = days === 15 ? 0.10 : (USER_CUSTOM_RATE / 100);
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
@endsection