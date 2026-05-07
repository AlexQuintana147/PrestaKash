@extends('client.layout')

@section('title', 'Solicitar Préstamo')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8">

    <!-- Header -->
    <div class="mb-10">
        <a href="{{ route('client.loans') }}"
           class="inline-flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors mb-4">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver a préstamos
        </a>
        <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-0.5">Nueva solicitud</p>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Solicitar Préstamo</h1>
    </div>

    <!-- Validation errors -->
    @if($errors->any())
        <div class="mb-8 p-4 rounded-xl bg-red-50 dark:bg-red-950/30 border border-red-100 dark:border-red-900/40">
            <div class="flex items-start gap-2">
                <svg class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-xs font-semibold text-red-700 dark:text-red-400 mb-1">Corrige los siguientes errores:</p>
                    <ul class="text-xs text-red-600 dark:text-red-400 space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('client.loans.store') }}" method="POST" id="loanForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

            <!-- ═══════════ LEFT: FORM ═══════════ -->
            <div class="lg:col-span-3 space-y-8">

                <!-- Bloque 1: Importe y moneda -->
                <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-6 md:p-8">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-6 h-6 rounded-full bg-green-50 dark:bg-green-950 flex items-center justify-center text-green-600 dark:text-green-400 text-xs font-bold">1</div>
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Importe y moneda</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Importe -->
                        <div>
                            <label for="amount" class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Importe solicitado</label>
                            <div class="relative">
                                <span id="currencySymbol" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-semibold text-gray-400 dark:text-gray-500 pointer-events-none">S/</span>
                                <input type="number" name="amount" id="amount" min="100" step="0.01" value="{{ old('amount') }}"
                                       placeholder="0.00"
                                       class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-950 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 transition-all">
                            </div>
                            <p class="text-[10px] text-gray-400 dark:text-gray-600 mt-1">Mínimo: 100</p>
                        </div>

                        <!-- Moneda -->
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Moneda</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="cursor-pointer">
                                    <input type="radio" name="currency" value="PEN" class="hidden peer" {{ old('currency', 'PEN') === 'PEN' ? 'checked' : '' }}>
                                    <div class="flex items-center justify-center gap-2 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-950 text-sm text-gray-600 dark:text-gray-400 peer-checked:border-green-500 peer-checked:bg-green-50 dark:peer-checked:bg-green-950/40 peer-checked:text-green-700 dark:peer-checked:text-green-400 transition-all">
                                        <span class="text-base">🇵🇪</span>
                                        <span class="font-medium">Soles</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="currency" value="USD" class="hidden peer" {{ old('currency') === 'USD' ? 'checked' : '' }}>
                                    <div class="flex items-center justify-center gap-2 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-950 text-sm text-gray-600 dark:text-gray-400 peer-checked:border-green-500 peer-checked:bg-green-50 dark:peer-checked:bg-green-950/40 peer-checked:text-green-700 dark:peer-checked:text-green-400 transition-all">
                                        <span class="text-base">🇺🇸</span>
                                        <span class="font-medium">Dólares</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bloque 2: Plazo y tipo -->
                <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-6 md:p-8">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-6 h-6 rounded-full bg-green-50 dark:bg-green-950 flex items-center justify-center text-green-600 dark:text-green-400 text-xs font-bold">2</div>
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Plazo de pago</h2>
                    </div>

                    <div class="space-y-5">
                        <!-- Slider de meses -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="months" class="text-xs text-gray-500 dark:text-gray-400">Plazo en meses</label>
                                <span id="monthsDisplay" class="text-sm font-bold text-green-600 dark:text-green-400">12 meses</span>
                            </div>
                            <input type="range" name="months" id="months" min="1" max="24" value="{{ old('months', 12) }}"
                                   class="w-full h-1.5 bg-gray-200 dark:bg-gray-800 rounded-full appearance-none cursor-pointer accent-green-500"
                                   style="accent-color: #22c55e;">
                            <div class="flex justify-between text-[10px] text-gray-400 dark:text-gray-600 mt-1">
                                <span>1 mes</span>
                                <span>12 meses</span>
                                <span>24 meses</span>
                            </div>
                        </div>

                        <!-- Tipo de pago -->
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Tipo de pago</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="cuotas" class="hidden peer" {{ old('type', 'cuotas') === 'cuotas' ? 'checked' : '' }}>
                                    <div class="text-center py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-950 peer-checked:border-green-500 peer-checked:bg-green-50 dark:peer-checked:bg-green-950/40 transition-all">
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 peer-checked:text-green-700 dark:peer-checked:text-green-400">Cuotas mensuales</p>
                                        <p class="text-[10px] text-gray-400 dark:text-gray-600 mt-0.5">Divide tu pago mensualmente</p>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="unico" class="hidden peer" {{ old('type') === 'unico' ? 'checked' : '' }}>
                                    <div class="text-center py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-950 peer-checked:border-green-500 peer-checked:bg-green-50 dark:peer-checked:bg-green-950/40 transition-all">
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 peer-checked:text-green-700 dark:peer-checked:text-green-400">Pago único</p>
                                        <p class="text-[10px] text-gray-400 dark:text-gray-600 mt-0.5">Un solo pago al vencimiento</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bloque 3: Datos bancarios -->
                <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-6 md:p-8">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-6 h-6 rounded-full bg-green-50 dark:bg-green-950 flex items-center justify-center text-green-600 dark:text-green-400 text-xs font-bold">3</div>
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Cuenta de desembolso</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Banco -->
                        <div>
                            <label for="bank_name" class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Banco</label>
                            <select name="bank_name" id="bank_name"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-950 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 transition-all appearance-none">
                                <option value="">Seleccionar banco</option>
                                @foreach(['BCP', 'Interbank', 'BBVA', 'Scotiabank', 'BanBif', 'Banco de la Nación', 'Caja Piura', 'Caja Huancayo', 'Caja Arequipa', 'Otro'] as $bank)
                                    <option value="{{ $bank }}" {{ old('bank_name') === $bank ? 'selected' : '' }}>{{ $bank }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tipo de cuenta -->
                        <div>
                            <label for="account_type" class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Tipo de cuenta</label>
                            <select name="account_type" id="account_type"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-950 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 transition-all appearance-none">
                                <option value="ahorros" {{ old('account_type', 'ahorros') === 'ahorros' ? 'selected' : '' }}>Ahorros</option>
                                <option value="corriente" {{ old('account_type') === 'corriente' ? 'selected' : '' }}>Corriente</option>
                            </select>
                        </div>

                        <!-- Número de cuenta -->
                        <div>
                            <label for="account_number" class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Número de cuenta</label>
                            <input type="text" name="account_number" id="account_number" value="{{ old('account_number') }}"
                                   placeholder="Ej: 191-12345678-0-01"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-950 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 transition-all">
                        </div>

                        <!-- CCI -->
                        <div>
                            <label for="cci" class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">CCI <span class="text-gray-300 dark:text-gray-600">(opcional)</span></label>
                            <input type="text" name="cci" id="cci" value="{{ old('cci') }}"
                                   placeholder="Código Interbancario de 20 dígitos"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-950 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 transition-all">
                        </div>
                    </div>
                </div>

                <!-- Bloque 4: Motivo -->
                <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-6 md:p-8">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-6 h-6 rounded-full bg-green-50 dark:bg-green-950 flex items-center justify-center text-green-600 dark:text-green-400 text-xs font-bold">4</div>
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Motivo de la solicitud</h2>
                    </div>

                    <div>
                        <label for="reason" class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">¿Por qué necesitas este préstamo?</label>
                        <textarea name="reason" id="reason" rows="4"
                                  placeholder="Describe brevemente el motivo por el cual necesitas este financiamiento..."
                                  class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-950 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 transition-all resize-none">{{ old('reason') }}</textarea>
                        <div class="flex justify-between mt-1">
                            <p class="text-[10px] text-gray-400 dark:text-gray-600">Mínimo 10 caracteres</p>
                            <p class="text-[10px] text-gray-400 dark:text-gray-600"><span id="charCount">0</span>/1000</p>
                        </div>
                    </div>
                </div>

                <!-- Submit (mobile) -->
                <div class="lg:hidden">
                    <button type="submit" id="submitBtnMobile"
                            class="w-full py-3.5 rounded-xl bg-green-600 hover:bg-green-700 text-white text-sm font-semibold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-green-500/30">
                        Enviar solicitud
                    </button>
                </div>
            </div>

            <!-- ═══════════ RIGHT: SIMULATION ═══════════ -->
            <div class="lg:col-span-2">
                <div class="lg:sticky lg:top-8 space-y-4">

                    <!-- Simulation card -->
                    <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-6 md:p-8">

                        <!-- Badge -->
                        <div class="flex items-center gap-2 mb-6">
                            <div class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></div>
                            <span class="text-[10px] text-amber-600 dark:text-amber-400 font-bold uppercase tracking-widest">Simulación</span>
                        </div>

                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Resumen de tu préstamo</h3>
                        <p class="text-[10px] text-gray-400 dark:text-gray-600 mb-6 leading-relaxed">Los valores se actualizan en tiempo real conforme completas el formulario.</p>

                        <!-- Simulation data -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-800">
                                <span class="text-xs text-gray-500 dark:text-gray-400">Monto solicitado</span>
                                <span id="simAmount" class="text-sm font-semibold text-gray-900 dark:text-white">S/ 0.00</span>
                            </div>
                            <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-800">
                                <span class="text-xs text-gray-500 dark:text-gray-400">Moneda</span>
                                <span id="simCurrency" class="text-sm font-medium text-gray-700 dark:text-gray-300">Soles (PEN)</span>
                            </div>
                            <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-800">
                                <span class="text-xs text-gray-500 dark:text-gray-400">Plazo</span>
                                <span id="simMonths" class="text-sm font-medium text-gray-700 dark:text-gray-300">12 meses</span>
                            </div>
                            <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-800">
                                <span class="text-xs text-gray-500 dark:text-gray-400">Tipo de pago</span>
                                <span id="simType" class="text-sm font-medium text-gray-700 dark:text-gray-300">Cuotas mensuales</span>
                            </div>
                            <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-800">
                                <span class="text-xs text-gray-500 dark:text-gray-400">Comisión simulada (20%)</span>
                                <span id="simInterest" class="text-sm font-medium text-amber-600 dark:text-amber-400">S/ 0.00</span>
                            </div>

                            <!-- Total -->
                            <div class="flex items-center justify-between pt-2">
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Total a devolver</span>
                                <span id="simTotal" class="text-lg font-bold text-green-600 dark:text-green-400">S/ 0.00</span>
                            </div>

                            <!-- Cuota mensual -->
                            <div id="simMonthlyRow" class="flex items-center justify-between bg-green-50 dark:bg-green-950/30 rounded-xl px-4 py-3">
                                <span class="text-xs text-green-700 dark:text-green-400">Cuota mensual aprox.</span>
                                <span id="simMonthly" class="text-sm font-bold text-green-700 dark:text-green-400">S/ 0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- Disclaimer -->
                    <div class="bg-amber-50 dark:bg-amber-950/20 border border-amber-100 dark:border-amber-900/30 rounded-2xl p-5">
                        <div class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-xs font-semibold text-amber-800 dark:text-amber-300 mb-1">Esto es solo una simulación</p>
                                <p class="text-[10px] text-amber-700 dark:text-amber-400 leading-relaxed">La aprobación final del préstamo, así como la comisión definitiva aplicada, son determinadas exclusivamente por la empresa tras revisar tu solicitud.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit (desktop) -->
                    <button type="submit" id="submitBtnDesktop"
                            class="hidden lg:block w-full py-3.5 rounded-xl bg-green-600 hover:bg-green-700 text-white text-sm font-semibold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-green-500/30">
                        Enviar solicitud
                    </button>

                </div>
            </div>

        </div>
    </form>

</div>

<style>
    /* Custom range slider thumb */
    input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #22c55e;
        cursor: pointer;
        border: 3px solid white;
        box-shadow: 0 1px 4px rgba(0,0,0,0.15);
    }
    input[type="range"]::-moz-range-thumb {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #22c55e;
        cursor: pointer;
        border: 3px solid white;
        box-shadow: 0 1px 4px rgba(0,0,0,0.15);
    }
    .dark input[type="range"]::-webkit-slider-thumb {
        border-color: #111827;
    }
    .dark input[type="range"]::-moz-range-thumb {
        border-color: #111827;
    }

    /* Custom select arrow */
    select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%239ca3af'%3E%3Cpath fill-rule='evenodd' d='M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 16px;
        padding-right: 36px;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const amountInput     = document.getElementById('amount');
    const monthsInput     = document.getElementById('months');
    const monthsDisplay   = document.getElementById('monthsDisplay');
    const reasonInput     = document.getElementById('reason');
    const charCount       = document.getElementById('charCount');
    const currencySymbol  = document.getElementById('currencySymbol');
    const currencyRadios  = document.querySelectorAll('input[name="currency"]');
    const typeRadios      = document.querySelectorAll('input[name="type"]');

    // Simulation elements
    const simAmount       = document.getElementById('simAmount');
    const simCurrency     = document.getElementById('simCurrency');
    const simMonths       = document.getElementById('simMonths');
    const simType         = document.getElementById('simType');
    const simInterest     = document.getElementById('simInterest');
    const simTotal        = document.getElementById('simTotal');
    const simMonthly      = document.getElementById('simMonthly');
    const simMonthlyRow   = document.getElementById('simMonthlyRow');

    function getSymbol() {
        const checked = document.querySelector('input[name="currency"]:checked');
        return checked && checked.value === 'USD' ? '$' : 'S/';
    }

    function getCurrencyLabel() {
        const checked = document.querySelector('input[name="currency"]:checked');
        return checked && checked.value === 'USD' ? 'Dólares (USD)' : 'Soles (PEN)';
    }

    function getTypeLabel() {
        const checked = document.querySelector('input[name="type"]:checked');
        return checked && checked.value === 'unico' ? 'Pago único' : 'Cuotas mensuales';
    }

    function formatNumber(n) {
        return n.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function updateSimulation() {
        const amount = parseFloat(amountInput.value) || 0;
        const months = parseInt(monthsInput.value) || 1;
        const symbol = getSymbol();
        const isCuotas = document.querySelector('input[name="type"]:checked')?.value === 'cuotas';

        const interest = amount * 0.20;
        const total = amount + interest;
        const monthly = total / months;

        // Update currency symbol in input
        currencySymbol.textContent = symbol;

        // Update simulation panel
        simAmount.textContent   = symbol + ' ' + formatNumber(amount);
        simCurrency.textContent = getCurrencyLabel();
        simMonths.textContent   = months + (months === 1 ? ' mes' : ' meses');
        simType.textContent     = getTypeLabel();
        simInterest.textContent = symbol + ' ' + formatNumber(interest);
        simTotal.textContent    = symbol + ' ' + formatNumber(total);
        simMonthly.textContent  = symbol + ' ' + formatNumber(monthly);

        // Show/hide monthly row
        simMonthlyRow.style.display = isCuotas ? 'flex' : 'none';

        // Update months display
        monthsDisplay.textContent = months + (months === 1 ? ' mes' : ' meses');
    }

    // Event listeners
    amountInput.addEventListener('input', updateSimulation);
    monthsInput.addEventListener('input', updateSimulation);
    currencyRadios.forEach(r => r.addEventListener('change', updateSimulation));
    typeRadios.forEach(r => r.addEventListener('change', updateSimulation));

    // Char count
    reasonInput.addEventListener('input', function () {
        charCount.textContent = this.value.length;
    });

    // Initial state
    updateSimulation();
    charCount.textContent = reasonInput.value.length;
});
</script>
@endsection
