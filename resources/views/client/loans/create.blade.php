@extends('client.layout')

@section('title', 'Solicitar Préstamo')

@section('content')
<div class="min-h-screen px-4 md:px-10 py-6 md:py-8 max-w-6xl pb-36 md:pb-8">

    <!-- Header -->
    <div class="mb-10">
        <a href="{{ route('client.loans') }}"
           class="inline-flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors mb-4">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
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
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">Importe y moneda</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="amount" class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Monto</label>
                            <div class="relative">
                                <span id="currencySymbol" class="absolute left-0 top-1/2 -translate-y-1/2 text-sm font-semibold text-gray-400 dark:text-gray-500 pointer-events-none">S/</span>
                                <input type="text" name="amount" id="amount" value="{{ old('amount') }}"
                                       placeholder="0.00"
                                       inputmode="decimal"
                                       class="w-full pl-8 pr-0 py-3 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-xl font-semibold text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-gray-700 focus:outline-none focus:border-green-500 transition-colors">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Moneda</label>
                            <div class="flex gap-2 mt-1">
                                <label class="cursor-pointer flex-1">
                                    <input type="radio" name="currency" value="PEN" class="hidden peer" {{ old('currency', 'PEN') === 'PEN' ? 'checked' : '' }}>
                                    <div class="flex items-center justify-center gap-2 py-2.5 rounded-lg text-sm text-gray-500 dark:text-gray-400 border border-transparent peer-checked:border-green-500/40 peer-checked:text-green-600 dark:peer-checked:text-green-400 peer-checked:bg-green-500/5 transition-all">
                                        <span>🇵🇪</span>
                                        <span class="font-medium">Soles</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer flex-1">
                                    <input type="radio" name="currency" value="USD" class="hidden peer" {{ old('currency') === 'USD' ? 'checked' : '' }}>
                                    <div class="flex items-center justify-center gap-2 py-2.5 rounded-lg text-sm text-gray-500 dark:text-gray-400 border border-transparent peer-checked:border-green-500/40 peer-checked:text-green-600 dark:peer-checked:text-green-400 peer-checked:bg-green-500/5 transition-all">
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
                        <!-- Tipo de pago (primero) -->
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-2">Tipo de pago</label>
                            <div class="flex gap-2">
                                <label class="cursor-pointer flex-1">
                                    <input type="radio" name="type" value="cuotas" class="hidden peer" {{ old('type', 'cuotas') === 'cuotas' ? 'checked' : '' }}>
                                    <div class="text-center py-2.5 rounded-lg text-xs font-medium text-gray-500 dark:text-gray-400 border border-transparent peer-checked:border-green-500/40 peer-checked:text-green-600 dark:peer-checked:text-green-400 peer-checked:bg-green-500/5 transition-all">
                                        Cuotas mensuales
                                    </div>
                                </label>
                                <label class="cursor-pointer flex-1">
                                    <input type="radio" name="type" value="unico" class="hidden peer" {{ old('type') === 'unico' ? 'checked' : '' }}>
                                    <div class="text-center py-2.5 rounded-lg text-xs font-medium text-gray-500 dark:text-gray-400 border border-transparent peer-checked:border-green-500/40 peer-checked:text-green-600 dark:peer-checked:text-green-400 peer-checked:bg-green-500/5 transition-all">
                                        Pago único
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Sistema de Amortización (Solo si es cuotas) -->
                        <div id="amortizationSystemBlock">
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-2">Sistema de amortización</label>
                            <div class="flex gap-2">
                                <label class="cursor-pointer flex-1">
                                    <input type="radio" name="amortization_system" value="french" class="hidden peer" {{ old('amortization_system', 'french') === 'french' ? 'checked' : '' }}>
                                    <div class="text-center py-2.5 rounded-lg text-xs font-medium text-gray-500 dark:text-gray-400 border border-transparent peer-checked:border-green-500/40 peer-checked:text-green-600 dark:peer-checked:text-green-400 peer-checked:bg-green-500/5 transition-all">
                                        Francés (Cuota fija)
                                    </div>
                                </label>
                                <label class="cursor-pointer flex-1">
                                    <input type="radio" name="amortization_system" value="american" class="hidden peer" {{ old('amortization_system') === 'american' ? 'checked' : '' }}>
                                    <div class="text-center py-2.5 rounded-lg text-xs font-medium text-gray-500 dark:text-gray-400 border border-transparent peer-checked:border-green-500/40 peer-checked:text-green-600 dark:peer-checked:text-green-400 peer-checked:bg-green-500/5 transition-all">
                                        Americano (Interés mensual)
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Cuotas: slider de meses (3-24) -->
                        <div id="cuotasBlock">
                            <div class="flex items-center justify-between mb-3">
                                <label for="months" class="text-xs text-gray-500 dark:text-gray-400">Fraccionar en</label>
                                <span id="monthsDisplay" class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">12 meses</span>
                            </div>
                            <input type="range" name="months" id="months" min="3" max="24" value="{{ old('months', 12) }}"
                                   class="w-full h-1 bg-gray-200 dark:bg-gray-800 rounded-full appearance-none cursor-pointer">
                            <div class="flex justify-between text-[10px] text-gray-300 dark:text-gray-700 mt-1.5">
                                <span>3</span>
                                <span>6</span>
                                <span>12</span>
                                <span>18</span>
                                <span>24</span>
                            </div>
                        </div>

                        <!-- Pago único: 15 o 30 días -->
                        <div id="unicoBlock" class="hidden">
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-2">Pagar en</label>
                            <div class="flex gap-2">
                                <label class="cursor-pointer flex-1">
                                    <input type="radio" name="payment_days" value="15" class="hidden peer" {{ old('payment_days', '15') == '15' ? 'checked' : '' }}>
                                    <div class="text-center py-3 rounded-lg border border-transparent peer-checked:border-green-500/40 peer-checked:bg-green-500/5 transition-all">
                                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 peer-checked:text-green-600 dark:peer-checked:text-green-400">15 días</p>
                                        <p class="text-[10px] text-green-600 dark:text-green-400 mt-0.5">10% comisión</p>
                                    </div>
                                </label>
                                <label class="cursor-pointer flex-1">
                                    <input type="radio" name="payment_days" value="30" class="hidden peer" {{ old('payment_days') == '30' ? 'checked' : '' }}>
                                    <div class="text-center py-3 rounded-lg border border-transparent peer-checked:border-green-500/40 peer-checked:bg-green-500/5 transition-all">
                                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 peer-checked:text-green-600 dark:peer-checked:text-green-400">30 días</p>
                                        <p class="text-[10px] text-amber-500 dark:text-amber-400 mt-0.5">20% comisión</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Desembolso -->
                <section>
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">Cuenta de desembolso</p>

                    @if($bankAccounts->isEmpty())
                        <div class="bg-amber-50 dark:bg-amber-950/20 border border-amber-100 dark:border-amber-900/50 rounded-2xl p-6">
                            <div class="flex gap-4">
                                <div class="text-amber-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-amber-900 dark:text-amber-400 uppercase tracking-widest mb-1">No tienes cuentas registradas</h4>
                                    <p class="text-xs text-amber-700 dark:text-amber-500/80 mb-4 leading-relaxed">Para solicitar un préstamo, primero debes agregar una cuenta bancaria donde recibirás el dinero.</p>
                                    <a href="{{ route('client.dashboard') }}" class="inline-flex items-center text-[10px] font-black text-amber-900 dark:text-amber-400 uppercase tracking-[0.2em] border-b-2 border-amber-900 dark:border-amber-400 pb-0.5 hover:opacity-70 transition-opacity">
                                        Ir al inicio para agregar cuenta
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="group">
                            <label for="bank_account_id" class="block text-xs text-gray-500 dark:text-gray-400 mb-2">Selecciona tu cuenta de destino</label>
                            <div class="relative">
                                <select name="bank_account_id" id="bank_account_id" required
                                        class="w-full px-0 py-3 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:border-green-500 transition-colors appearance-none cursor-pointer">
                                    <option value="" class="dark:bg-gray-950">Seleccionar cuenta</option>
                                    @foreach($bankAccounts as $acc)
                                        <option value="{{ $acc->id }}" class="dark:bg-gray-950" {{ old('bank_account_id') == $acc->id ? 'selected' : '' }}>
                                            {{ $acc->alias ?: $acc->bank_name }} — {{ $acc->account_number }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-3 uppercase tracking-widest">¿Quieres usar otra? <a href="{{ route('client.dashboard') }}" class="text-green-600 dark:text-green-400 font-bold hover:underline">Gestiónalas aquí</a></p>
                        </div>
                    @endif
                </section>

                <!-- Motivo -->
                <section>
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">Motivo</p>

                    <label for="reason" class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">¿Por qué necesitas este préstamo?</label>
                    <textarea name="reason" id="reason" rows="3"
                              placeholder="Describe brevemente el motivo..."
                              class="w-full px-0 py-3 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-gray-700 focus:outline-none focus:border-green-500 transition-colors resize-none">{{ old('reason') }}</textarea>
                    <p class="text-[10px] text-gray-300 dark:text-gray-700 mt-1 text-right tabular-nums"><span id="charCount">0</span>/1000</p>
                </section>
            </div>

            <!-- ══ RIGHT: SIMULATION ══ -->
            <div class="lg:col-span-2">
                <div id="simulationPanel" class="md:sticky md:top-8 space-y-4 md:space-y-6 fixed bottom-0 left-0 right-0 md:relative bg-white dark:bg-gray-950 border-t border-gray-100 dark:border-gray-800 md:border-0 p-4 md:p-0 shadow-[0_-4px_20px_rgba(0,0,0,0.08)] md:shadow-none z-50 transition-all duration-300">

                    <!-- Toggle button mobile -->
                    <div class="md:hidden flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></div>
                            <span class="text-[10px] text-amber-500 dark:text-amber-400 font-bold uppercase tracking-widest">Simulación</span>
                        </div>
                        <button type="button" id="toggleSim" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                            </svg>
                        </button>
                    </div>

                    <div id="simContent">
                        <!-- Main number -->
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 mb-1">Total a devolver</p>
                        <p id="simTotal" class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight tabular-nums">S/ 0.00</p>

                        <div id="simMonthlyRow" class="mt-1.5 md:mt-2">
                            <p class="text-xs text-green-600 dark:text-green-400 font-medium tabular-nums">
                                <span id="simMonthly">S/ 0.00</span> <span class="text-gray-400 dark:text-gray-600 font-normal">/ mes</span>
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
                        <div class="h-px bg-gray-100 dark:bg-gray-800 my-1"></div>
                        <div class="flex justify-between text-xs">
                            <span id="simInterestLabel" class="text-amber-500 dark:text-amber-400">Comisión simulada (20%)</span>
                            <span id="simInterest" class="text-amber-500 dark:text-amber-400 font-medium tabular-nums">S/ 0.00</span>
                        </div>

                        <!-- Cronograma Preview -->
                        <div id="scheduleSection" class="hidden pt-4 border-t border-gray-100 dark:border-gray-800 mt-4">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Cronograma de pagos</span>
                                <button type="button" onclick="document.getElementById('fullSchedule').classList.toggle('hidden')" class="text-[10px] text-green-600 font-bold hover:underline">VER DETALLE</button>
                            </div>
                            <div id="fullSchedule" class="hidden overflow-visible rounded-lg border border-gray-100 dark:border-gray-800">
                                <table class="w-full text-[10px] text-left">
                                    <thead class="bg-gray-50 dark:bg-gray-900 text-gray-400">
                                        <tr>
                                            <th class="px-2 py-2 font-medium">N°</th>
                                            <th class="px-2 py-2 font-medium">
                                                <div class="flex items-center gap-1">
                                                    Capital
                                                    <div class="relative group">
                                                        <svg class="w-2.5 h-2.5 text-gray-300 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-32 p-2 bg-gray-800 text-white text-[9px] rounded-lg shadow-2xl z-[100] text-center pointer-events-none">
                                                            Parte de la cuota que reduce la deuda principal.
                                                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-800"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </th>
                                            <th class="px-2 py-2 font-medium">
                                                <div class="flex items-center gap-1">
                                                    Interés
                                                    <div class="relative group">
                                                        <svg class="w-2.5 h-2.5 text-gray-300 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-32 p-2 bg-gray-800 text-white text-[9px] rounded-lg shadow-2xl z-[100] text-center pointer-events-none">
                                                            Costo por el uso del dinero prestado en este periodo.
                                                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-800"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </th>
                                            <th class="px-2 py-2 font-medium">
                                                <div class="flex items-center gap-1">
                                                    Saldo
                                                    <div class="relative group">
                                                        <svg class="w-2.5 h-2.5 text-gray-300 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-32 p-2 bg-gray-800 text-white text-[9px] rounded-lg shadow-2xl z-[100] text-center pointer-events-none">
                                                            Monto pendiente de pago después de esta cuota.
                                                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-800"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="scheduleBody" class="divide-y divide-gray-50 dark:divide-gray-900 text-gray-600 dark:text-gray-400">
                                        <!-- JS dynamic -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Disclaimer (desktop only) -->
                    <p class="hidden md:block text-[10px] text-gray-400 dark:text-gray-600 leading-relaxed border-l-2 border-amber-300 dark:border-amber-800 pl-3">
                        Esto es solo una simulación. La <strong class="text-gray-500 dark:text-gray-400">aprobación final</strong> y la <strong class="text-gray-500 dark:text-gray-400">comisión definitiva</strong> son determinadas exclusivamente por la empresa.
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
<div id="scheduleModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Overlay -->
        <div id="closeModalOverlay" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

        <!-- Modal content -->
        <div class="inline-block align-bottom bg-white dark:bg-gray-950 rounded-t-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:rounded-3xl">
            <div class="px-6 py-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Cronograma de Pagos</h3>
                        <p id="modalScheduleSubtitle" class="text-[10px] text-gray-400 uppercase tracking-widest mt-1">Simulación Sistema Francés</p>
                    </div>
                    <button type="button" id="closeModalBtn" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="overflow-visible rounded-2xl border border-gray-100 dark:border-gray-800">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-gray-50 dark:bg-gray-900 text-gray-400">
                            <tr>
                                <th class="px-3 py-3 font-medium">N°</th>
                                <th class="px-3 py-3 font-medium">
                                    <div class="flex items-center gap-1">
                                        Capital
                                        <div class="relative group">
                                            <svg class="w-2.5 h-2.5 text-gray-300 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-32 p-2 bg-gray-800 text-white text-[9px] rounded-lg shadow-2xl z-[100] text-center pointer-events-none">
                                                Parte de la cuota que reduce la deuda principal.
                                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-800"></div>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th class="px-3 py-3 font-medium">
                                    <div class="flex items-center gap-1">
                                        Interés
                                        <div class="relative group">
                                            <svg class="w-2.5 h-2.5 text-gray-300 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-32 p-2 bg-gray-800 text-white text-[9px] rounded-lg shadow-2xl z-[100] text-center pointer-events-none">
                                                Costo por el uso del dinero prestado en este periodo.
                                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-800"></div>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th class="px-3 py-3 font-medium">
                                    <div class="flex items-center gap-1">
                                        Saldo
                                        <div class="relative group">
                                            <svg class="w-2.5 h-2.5 text-gray-300 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-32 p-2 bg-gray-800 text-white text-[9px] rounded-lg shadow-2xl z-[100] text-center pointer-events-none">
                                                Monto pendiente de pago después de esta cuota.
                                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-800"></div>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="modalScheduleBody" class="divide-y divide-gray-50 dark:divide-gray-900 text-gray-600 dark:text-gray-400">
                            <!-- JS dynamic -->
                        </tbody>
                    </table>
                </div>

                <div class="mt-8">
                    <button type="button" id="closeModalBtnBottom" class="w-full py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white text-sm font-bold uppercase tracking-widest">
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
        box-shadow: 0 1px 3px rgba(0,0,0,0.12);
    }
    input[type="range"]::-moz-range-thumb {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #22c55e;
        cursor: pointer;
        border: 2px solid white;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12);
    }
    .dark input[type="range"]::-webkit-slider-thumb { border-color: #030712; }
    .dark input[type="range"]::-moz-range-thumb { border-color: #030712; }

    select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%239ca3af'%3E%3Cpath fill-rule='evenodd' d='M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0 center;
        background-size: 16px;
        padding-right: 20px;
    }

    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
    input[type="number"] { -moz-appearance: textfield; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const amount         = document.getElementById('amount');
    const months         = document.getElementById('months');
    const monthsDisplay  = document.getElementById('monthsDisplay');
    const reason         = document.getElementById('reason');
    const charCount      = document.getElementById('charCount');
    const currencySymbol = document.getElementById('currencySymbol');
    const currencyRadios = document.querySelectorAll('input[name="currency"]');
    const typeRadios     = document.querySelectorAll('input[name="type"]');
    const paymentDaysRadios = document.querySelectorAll('input[name="payment_days"]');
    const cuotasBlock    = document.getElementById('cuotasBlock');
    const unicoBlock      = document.getElementById('unicoBlock');
    const amortizationSystemBlock = document.getElementById('amortizationSystemBlock');
    const amortizationRadios = document.querySelectorAll('input[name="amortization_system"]');

    const simAmount      = document.getElementById('simAmount');
    const simCurrency    = document.getElementById('simCurrency');
    const simMonths      = document.getElementById('simMonths');
    const simType        = document.getElementById('simType');
    const simInterest    = document.getElementById('simInterest');
    const simInterestLabel = document.getElementById('simInterestLabel');
    const simTotal       = document.getElementById('simTotal');
    const simMonthly     = document.getElementById('simMonthly');
    const simMonthlyRow  = document.getElementById('simMonthlyRow');

    const sym = () => document.querySelector('input[name="currency"]:checked')?.value === 'USD' ? '$' : 'S/';
    const fmt = n => n.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    function formatNumber(n) {
        let parts = n.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join('.');
    }

    function toggleBlocks() {
        const isCuotas = document.querySelector('input[name="type"]:checked')?.value === 'cuotas';
        cuotasBlock.classList.toggle('hidden', !isCuotas);
        amortizationSystemBlock.classList.toggle('hidden', !isCuotas);
        unicoBlock.classList.toggle('hidden', isCuotas);
        
        // Mostrar/Ocultar botón de cronograma en móvil
        const toggleBtn = document.getElementById('toggleScheduleMobile');
        if (toggleBtn) {
            toggleBtn.classList.toggle('hidden', !isCuotas);
        }
    }

    function update() {
        const a = parseFloat(amount.value.replace(/,/g, '')) || 0;
        const m = parseInt(months.value) || 3;
        const s = sym();
        const isCuotas = document.querySelector('input[name="type"]:checked')?.value === 'cuotas';
        const system = document.querySelector('input[name="amortization_system"]:checked')?.value || 'french';

        let interestRate, interest, total, monthly, plazoText;

        if (isCuotas) {
            interestRate = 0.20; // 20% mensual
            
            if (system === 'french') {
                // Sistema Francés
                if (a > 0) {
                    monthly = (a * interestRate) / (1 - Math.pow(1 + interestRate, -m));
                    total = monthly * m;
                    interest = total - a;
                } else {
                    monthly = 0; total = 0; interest = 0;
                }
                simInterestLabel.textContent = 'Interés Total (Sistema Francés)';
                document.getElementById('modalScheduleSubtitle').textContent = 'Simulación Sistema Francés';
            } else {
                // Sistema Americano
                interest = (a * interestRate) * m;
                total = a + interest;
                monthly = a * interestRate; // Pago mensual es solo el interés
                simInterestLabel.textContent = 'Interés Total (Sistema Americano)';
                document.getElementById('modalScheduleSubtitle').textContent = 'Simulación Sistema Americano';
            }
            
            plazoText = m + (m === 1 ? ' mes' : ' meses');
        } else {
            const days = parseInt(document.querySelector('input[name="payment_days"]:checked')?.value) || 15;
            interestRate = days === 15 ? 0.10 : 0.20;
            interest = a * interestRate;
            total = a + interest;
            monthly = 0;
            plazoText = days + ' días';
            simInterestLabel.textContent = 'Comisión (' + (interestRate * 100) + '%)';
        }

        currencySymbol.textContent = s;
        simAmount.textContent      = s + ' ' + fmt(a);
        simCurrency.textContent    = s === '$' ? 'Dólares' : 'Soles';
        simMonths.textContent      = plazoText;
        simType.textContent        = isCuotas ? (system === 'french' ? 'Cuotas (Francés)' : 'Cuotas (Americano)') : 'Pago único';
        simInterest.textContent    = s + ' ' + fmt(interest);
        simTotal.textContent       = s + ' ' + fmt(total);
        
        if (isCuotas && system === 'american') {
            simMonthly.textContent = s + ' ' + fmt(monthly) + ' (Final: ' + s + ' ' + fmt(a + monthly) + ')';
        } else {
            simMonthly.textContent = s + ' ' + fmt(monthly);
        }

        simMonthlyRow.style.display = isCuotas ? '' : 'none';
        monthsDisplay.textContent  = m + (m === 1 ? ' mes' : ' meses');

        // Actualizar cronograma
        if (isCuotas && a > 0) {
            updateSchedule(a, interestRate, m, s, system);
            document.getElementById('scheduleSection').classList.remove('hidden');
        } else {
            document.getElementById('scheduleSection').classList.add('hidden');
        }
    }

    function updateSchedule(principal, monthlyRate, months, symbol, system) {
        const body = document.getElementById('scheduleBody');
        const modalBody = document.getElementById('modalScheduleBody');
        body.innerHTML = '';
        modalBody.innerHTML = '';
        
        let balance = principal;

        if (system === 'french') {
            const monthlyPayment = (principal * monthlyRate) / (1 - Math.pow(1 + monthlyRate, -months));
            for (let i = 1; i <= months; i++) {
                const interest = balance * monthlyRate;
                const capital = monthlyPayment - interest;
                balance -= capital;
                renderRow(i, capital, interest, balance, body, modalBody, symbol);
            }
        } else {
            // Sistema Americano
            for (let i = 1; i <= months; i++) {
                const interest = principal * monthlyRate;
                const capital = (i === months) ? principal : 0;
                balance = (i === months) ? 0 : principal;
                renderRow(i, capital, interest, balance, body, modalBody, symbol);
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

        const row = document.createElement('tr');
        row.innerHTML = html;
        body.appendChild(row);

        const modalRow = document.createElement('tr');
        modalRow.innerHTML = html.replace(/px-2 py-2/g, 'px-3 py-3');
        modalBody.appendChild(modalRow);
    }

    amount.addEventListener('input', function(e) {
        let cursorPosition = this.selectionStart;
        let originalLength = this.value.length;
        
        let value = this.value.replace(/[^0-9.]/g, '');

        // Eliminar ceros a la izquierda, excepto si es "0."
        if (value.length > 1 && value.startsWith('0') && value[1] !== '.') {
            value = value.replace(/^0+/, '');
        }
        
        let parts = value.split('.');
        if (parts.length > 2) value = parts[0] + '.' + parts.slice(1).join('');
        
        let formattedValue = formatNumber(value);
        this.value = formattedValue;
        
        let newLength = this.value.length;
        cursorPosition = cursorPosition + (newLength - originalLength);
        this.setSelectionRange(cursorPosition, cursorPosition);
        
        update();
    });

    document.getElementById('loanForm').addEventListener('submit', function() {
        amount.value = amount.value.replace(/,/g, '');
    });

    months.addEventListener('input', update);
    currencyRadios.forEach(r => r.addEventListener('change', update));
    typeRadios.forEach(r => {
        r.addEventListener('change', () => {
            toggleBlocks();
            update();
        });
    });
    paymentDaysRadios.forEach(r => r.addEventListener('change', update));
    amortizationRadios.forEach(r => r.addEventListener('change', update));
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

    if (amount.value) {
        amount.value = formatNumber(amount.value.replace(/[^0-9.]/g, ''));
    }
    toggleBlocks();
    update();
    charCount.textContent = reason.value.length;
});
</script>
@endsection
