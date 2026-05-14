<!-- Modal Configurar Mora -->
<style>
    /* Hide spinners */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type=number] {
        -moz-appearance: textfield;
    }
</style>
<div id="lateFeeModal"
    class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-[100] flex items-center justify-center p-4"
    onclick="if(event.target===this) closeLateFeeModal()">
    <div class="bg-white dark:bg-gray-950 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden">

        <!-- Header -->
        <div class="px-6 pt-6 pb-5 border-b border-gray-100 dark:border-gray-900">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[8px] font-black uppercase tracking-widest text-orange-500 mb-1">Interés por mora</p>
                    <h3 class="text-base font-black text-gray-900 dark:text-white tracking-tight" id="lfm_title">
                        Configurar mo
                        ra</h3>
                    <p class="text-[9px] text-gray-400 mt-0.5" id="lfm_subtitle"></p>
                </div>
                <button onclick="closeLateFeeModal()"
                    class="text-gray-300 dark:text-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors flex-shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <form id="lateFeeForm" method="POST">
            @csrf
            <div id="lfm_body" class="px-6 py-5 space-y-5 overflow-y-auto"
                style="max-height: 70vh; scrollbar-width: thin; scrollbar-color: rgba(156,163,175,0.4) transparent;">

                <!-- Toggle activar -->
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-900 dark:text-white">Habilitar Interés por Mora</p>
                        <p class="text-[9px] text-gray-400 mt-0.5">Cobrar intereses sobre montos vencidos después del
                            período de gracia</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 ml-4">
                        <input type="checkbox" name="late_fee_enabled" id="lfm_enabled" value="1" class="sr-only peer">
                        <div
                            class="w-10 h-5 bg-gray-200 dark:bg-gray-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-5 peer-checked:bg-gray-900 dark:peer-checked:bg-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white dark:after:bg-gray-900 after:rounded-full after:h-4 after:w-4 after:transition-all">
                        </div>
                    </label>
                </div>

                <!-- Campos (se ocultan si está desactivado) -->
                <div id="lfm_fields" class="space-y-4">

                    <!-- Días de gracia -->
                    <div>
                        <label class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-2">Días de
                            Gracia</label>
                        <div class="relative">
                            <input type="number" name="grace_days" id="lfm_grace" min="0" max="30" value="0"
                                onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:outline-none focus:border-gray-400 dark:focus:border-gray-600 transition-colors">
                            <span
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-[9px] font-black text-gray-400 uppercase">días</span>
                        </div>
                        <p class="text-[8px] text-gray-400 mt-1">Días que se perdonan antes de aplicar mora (Mínimo 0,
                            Máximo 30).</p>
                    </div>

                    <!-- Tasa de mora -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-2">Tasa
                                de Interés (%)</label>
                            <div class="relative">
                                <input type="text" name="late_fee_rate" id="lfm_rate" inputmode="numeric"
                                    class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:outline-none focus:border-gray-400 dark:focus:border-gray-600 transition-colors">
                                <span
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-[9px] font-black text-gray-400 uppercase">%</span>
                            </div>
                        </div>
                        <div>
                            <label
                                class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-2">Monto
                                Fijo Diario</label>
                            <div class="relative">
                                <input type="text" name="late_fee_daily_amount" id="lfm_daily" inputmode="numeric"
                                    class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:outline-none focus:border-gray-400 dark:focus:border-gray-600 transition-colors">
                                <span
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-[9px] font-black text-gray-400 uppercase"
                                    id="lfm_daily_cur">S/</span>
                            </div>
                        </div>
                    </div>

                    <!-- Simulación -->
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 space-y-1.5">
                        <p class="text-[8px] font-black uppercase tracking-widest text-gray-400 mb-2">Ver simulación</p>
                        <div class="flex justify-between">
                            <span class="text-[9px] text-gray-500">Cuota base</span>
                            <span class="text-[9px] font-bold text-gray-900 dark:text-white" id="lfm_sim_base">—</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[9px] text-gray-500">Mora estimada (1 día después de gracia)</span>
                            <span class="text-[9px] font-bold text-orange-500" id="lfm_sim_mora">—</span>
                        </div>
                        <div class="flex justify-between border-t border-gray-100 dark:border-gray-800 pt-1.5 mt-1.5">
                            <span class="text-[9px] font-black text-gray-900 dark:text-white uppercase">Total a
                                pagar</span>
                            <span class="text-[9px] font-black text-gray-900 dark:text-white"
                                id="lfm_sim_total">—</span>
                        </div>
                    </div>

                    <!-- Explicación -->
                    <div class="border border-gray-100 dark:border-gray-800 rounded-xl p-4 space-y-2">
                        <p class="text-[8px] font-black uppercase tracking-widest text-gray-400 mb-2">¿Cómo funciona?
                        </p>
                        <p class="text-[9px] text-gray-500 leading-relaxed">
                            <span class="font-bold text-gray-900 dark:text-white">1. Días de gracia:</span> Si el
                            cliente paga dentro de este período, no se cobra mora.
                        </p>
                        <p class="text-[9px] text-gray-500 leading-relaxed">
                            <span class="font-bold text-gray-900 dark:text-white">2. Mora diaria:</span> Pasados los
                            días de gracia, se cobra <span class="font-bold text-orange-500"
                                id="lfm_rate_label">0%</span> por cada día de atraso.
                        </p>
                        <p class="text-[9px] text-gray-500 leading-relaxed">
                            <span class="font-bold text-gray-900 dark:text-white">3. Acumulación:</span> La mora se
                            calcula desde el vencimiento hasta la siguiente fecha de pago. Si el cliente no paga en la
                            siguiente fecha, la mora se congela para esa cuota y comienza a correr la mora de la nueva
                            cuota vencida.
                        </p>
                        <p class="text-[9px] text-gray-500 leading-relaxed">
                            <span class="font-bold text-gray-900 dark:text-white">Fórmula:</span> Mora = (Cuota × Tasa%
                            + Monto Fijo) × Días de atraso
                        </p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-900 flex items-center justify-between">
                <button type="button" onclick="closeLateFeeModal()"
                    class="text-[9px] font-black uppercase tracking-widest text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                    class="text-[9px] font-black uppercase tracking-widest text-gray-900 dark:text-white border-b border-gray-900 dark:border-white pb-px hover:opacity-60 transition-opacity">
                    Guardar configuración
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function formatCentsInput(el) {
        let val = el.value.replace(/\D/g, '');
        if (val === '') {
            el.value = '';
            return 0;
        }

        // Remove leading zeros
        val = parseInt(val, 10).toString();

        let numericValue = parseFloat(val) / 100;
        el.value = numericValue.toFixed(2);
        return numericValue;
    }

    function openLateFeeModal(loanId, loanLabel, currency, installmentAmount, currentEnabled, currentGrace, currentRate, currentDaily) {
        document.getElementById('lfm_title').textContent = 'Mora — Préstamo #' + loanId;
        document.getElementById('lfm_subtitle').textContent = loanLabel;
        document.getElementById('lateFeeForm').action = '/gestion-interna/pagos/mora/' + loanId;

        const enabledEl = document.getElementById('lfm_enabled');
        enabledEl.checked = !!currentEnabled;

        document.getElementById('lfm_grace').value = currentGrace !== undefined ? currentGrace : 0;

        const rateEl = document.getElementById('lfm_rate');
        const dailyEl = document.getElementById('lfm_daily');

        rateEl.value = (parseFloat(currentRate) || 0).toFixed(2);
        dailyEl.value = (parseFloat(currentDaily) || 0).toFixed(2);

        window._lfm_currency = currency;
        window._lfm_amount = parseFloat(installmentAmount) || 0;
        document.getElementById('lfm_daily_cur').textContent = currency;

        updateLateFeeSimulation();
        toggleLateFeeFields();

        document.getElementById('lateFeeModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeLateFeeModal() {
        document.getElementById('lateFeeModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function toggleLateFeeFields() {
        const enabled = document.getElementById('lfm_enabled').checked;
        document.getElementById('lfm_fields').style.opacity = enabled ? '1' : '0.4';
        document.getElementById('lfm_fields').style.pointerEvents = enabled ? '' : 'none';
    }

    function updateLateFeeSimulation() {
        const rate = parseFloat(document.getElementById('lfm_rate').value) || 0;
        const daily = parseFloat(document.getElementById('lfm_daily').value) || 0;
        const amount = window._lfm_amount || 0;
        const cur = window._lfm_currency || 'S/';

        const moraRate = amount * (rate / 100);
        const totalMora = moraRate + daily; // 1 día después de gracia
        const total = amount + totalMora;

        document.getElementById('lfm_sim_base').textContent = cur + ' ' + amount.toFixed(2);
        document.getElementById('lfm_sim_mora').textContent = '+ ' + cur + ' ' + totalMora.toFixed(2);
        document.getElementById('lfm_sim_total').textContent = cur + ' ' + total.toFixed(2);
        document.getElementById('lfm_rate_label').textContent = rate.toFixed(2) + '% + ' + cur + ' ' + daily.toFixed(2);
    }

    document.getElementById('lfm_enabled').addEventListener('change', toggleLateFeeFields);

    document.getElementById('lfm_rate').addEventListener('input', function (e) {
        const rate = formatCentsInput(this);

        // Automatic daily amount calculation
        const amount = window._lfm_amount || 0;
        const daily = amount * (rate / 100);
        document.getElementById('lfm_daily').value = daily.toFixed(2);

        updateLateFeeSimulation();
    });

    document.getElementById('lfm_daily').addEventListener('input', function (e) {
        formatCentsInput(this);
        updateLateFeeSimulation();
    });

    document.getElementById('lfm_grace').addEventListener('input', function (e) {
        let val = parseInt(this.value);
        if (isNaN(val) || val < 0) this.value = 0;
        if (val > 30) this.value = 30;
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !document.getElementById('lateFeeModal').classList.contains('hidden')) {
            closeLateFeeModal();
        }
    });
</script>