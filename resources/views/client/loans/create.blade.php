@extends('client.layout')

@section('title', 'Solicitar Préstamo')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8">
    <!-- Header -->
    <div class="mb-12">
        <a href="{{ route('client.loans') }}" class="text-xs text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors mb-4 inline-block">← Volver</a>
        <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-0.5">Préstamos</p>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Solicitar nuevo préstamo</h1>
    </div>

    <div class="max-w-4xl mx-auto">
        <form action="{{ route('client.loans.store') }}" method="POST" id="loanForm" class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @csrf
            <!-- Columna Izquierda: Configuración -->
            <div class="space-y-8">
                <!-- Tipo de Pago -->
                <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm transition-colors">
                    <label class="text-sm font-semibold text-gray-900 dark:text-white block mb-4">Modalidad de pago</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" id="btnCuotas" onclick="setTipo('cuotas')" class="py-2.5 px-4 rounded-xl text-sm font-bold transition-all border-2 border-green-500 bg-green-500 text-white shadow-lg shadow-green-500/20">
                            Pago a Cuotas
                        </button>
                        <button type="button" id="btnUnico" onclick="setTipo('unico')" class="py-2.5 px-4 rounded-xl text-sm font-bold transition-all border-2 border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-700">
                            Pago Único
                        </button>
                    </div>
                    <input type="hidden" name="type" id="typeInput" value="cuotas">
                </div>

                <!-- Monto -->
                <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm transition-colors">
                    <div class="flex justify-between items-baseline mb-4">
                        <label class="text-sm font-semibold text-gray-900 dark:text-white">¿Cuánto necesitas?</label>
                        <span id="montoDisplay" class="text-xl font-bold text-green-600 dark:text-green-400 tabular-nums">S/ 500.00</span>
                    </div>
                    <input type="range" name="amount" id="montoSlider" min="200" max="500" step="10" value="500" 
                           class="w-full h-2 bg-gray-200 dark:bg-gray-800 rounded-full appearance-none cursor-pointer accent-green-500">
                    <div class="flex justify-between text-xs text-gray-500 mt-3 font-medium">
                        <span>S/ 200</span>
                        <span>S/ 500</span>
                    </div>
                </div>

                <!-- Plazo -->
                <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm transition-colors">
                    <label class="text-sm font-semibold text-gray-900 dark:text-white block mb-4">Plazo de pago (Meses)</label>
                    <div id="plazoButtons" class="grid grid-cols-4 sm:grid-cols-6 gap-2">
                        <!-- Generado por JS -->
                    </div>
                    <input type="hidden" name="months" id="monthsInput" value="3">
                    <p class="text-[10px] text-gray-500 mt-4 leading-relaxed italic">
                        * Puedes elegir pagar el total en 1 solo mes o dividirlo en hasta 6 cuotas mensuales.
                    </p>
                </div>
            </div>

            <!-- Columna Derecha: Resumen -->
            <div class="bg-white dark:bg-gray-900 p-8 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm transition-colors h-fit lg:sticky lg:top-8">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Resumen de solicitud</h3>
                
                <div class="space-y-4 text-sm">
                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Monto solicitado</span>
                        <span id="resumenMonto" class="font-bold text-gray-900 dark:text-white">S/ 0.00</span>
                    </div>
                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Interés estimado (20%)</span>
                        <span id="resumenInteres" class="font-bold text-gray-900 dark:text-white">S/ 0.00</span>
                    </div>
                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Tarifa de tecnología</span>
                        <span id="resumenTarifa" class="font-bold text-gray-900 dark:text-white">S/ 0.00</span>
                    </div>
                    <div class="flex justify-between text-gray-600 dark:text-gray-400 border-b border-gray-50 dark:border-gray-800 pb-2">
                        <span>IGV (18%)</span>
                        <span id="resumenIgv" class="font-bold text-gray-900 dark:text-white">S/ 0.00</span>
                    </div>

                    <div class="pt-2">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-900 dark:text-white font-bold">Total a pagar</span>
                            <span id="resumenTotal" class="text-2xl font-black text-green-600 dark:text-green-400">S/ 0.00</span>
                        </div>
                    </div>

                    <div id="cuotaContainer" class="bg-green-50 dark:bg-green-900/20 p-4 rounded-xl border border-green-100 dark:border-green-900/30">
                        <div class="flex justify-between items-center">
                            <span class="text-green-800 dark:text-green-300 font-semibold text-xs">Cuota mensual aprox.</span>
                            <span id="resumenCuota" class="text-lg font-bold text-green-700 dark:text-green-400">S/ 0.00</span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 space-y-4">
                    <button type="submit" class="w-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-bold py-4 rounded-xl hover:opacity-90 transition-all shadow-xl shadow-gray-900/10 dark:shadow-none">
                        Confirmar solicitud
                    </button>
                    
                    <p class="text-[10px] text-gray-500 dark:text-gray-500 text-center leading-relaxed font-medium">
                        El porcentaje de interés y cargos pueden variar según la evaluación interna de la empresa. <br>
                        <span class="text-gray-700 dark:text-gray-300 uppercase tracking-tighter">Este simulador es referencial.</span>
                    </p>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const montoSlider = document.getElementById('montoSlider');
    const montoDisplay = document.getElementById('montoDisplay');
    const typeInput   = document.getElementById('typeInput');
    const monthsInput = document.getElementById('monthsInput');
    const plazoButtons = document.getElementById('plazoButtons');

    const resumenMonto   = document.getElementById('resumenMonto');
    const resumenInteres = document.getElementById('resumenInteres');
    const resumenTarifa  = document.getElementById('resumenTarifa');
    const resumenIgv     = document.getElementById('resumenIgv');
    const resumenTotal   = document.getElementById('resumenTotal');
    const resumenCuota   = document.getElementById('resumenCuota');
    const cuotaContainer = document.getElementById('cuotaContainer');

    let currentTipo   = 'cuotas';
    let currentMonths = 3;

    const fmt = n => 'S/ ' + n.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    function setTipo(tipo) {
        currentTipo = tipo;
        typeInput.value = tipo;

        const btnC = document.getElementById('btnCuotas');
        const btnU = document.getElementById('btnUnico');

        if (tipo === 'cuotas') {
            btnC.className = 'py-2.5 px-4 rounded-xl text-sm font-bold transition-all border-2 border-green-500 bg-green-500 text-white shadow-lg shadow-green-500/20';
            btnU.className = 'py-2.5 px-4 rounded-xl text-sm font-bold transition-all border-2 border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-700';
            cuotaContainer.classList.remove('hidden');
        } else {
            btnU.className = 'py-2.5 px-4 rounded-xl text-sm font-bold transition-all border-2 border-green-500 bg-green-500 text-white shadow-lg shadow-green-500/20';
            btnC.className = 'py-2.5 px-4 rounded-xl text-sm font-bold transition-all border-2 border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-700';
            cuotaContainer.classList.add('hidden');
            currentMonths = 1;
            renderPlazo();
        }
        calcular();
    }

    function renderPlazo() {
        plazoButtons.innerHTML = '';
        const limit = currentTipo === 'unico' ? 1 : 6;
        for (let i = 1; i <= limit; i++) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = i + (i === 1 ? ' mes' : ' m');
            btn.className = `py-2 rounded-lg text-xs font-bold transition-all border ${i === currentMonths ? 'bg-green-500 border-green-500 text-white shadow-md' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 hover:border-green-400'}`;
            btn.onclick = () => {
                currentMonths = i;
                monthsInput.value = i;
                renderPlazo();
                calcular();
            };
            plazoButtons.appendChild(btn);
        }
    }

    function calcular() {
        const monto = parseFloat(montoSlider.value);
        const tasaInteres = 0.20;
        const interes = monto * tasaInteres;

        const tarifa = monto * 0.10;
        const igv = tarifa * 0.18;
        const cargos = tarifa + igv;

        const total = monto + interes + cargos;
        const cuota = total / currentMonths;

        montoDisplay.textContent = fmt(monto);
        resumenMonto.textContent = fmt(monto);
        resumenInteres.textContent = fmt(interes);
        resumenTarifa.textContent = fmt(tarifa);
        resumenIgv.textContent = fmt(igv);
        resumenTotal.textContent = fmt(total);
        resumenCuota.textContent = fmt(cuota);
    }

    montoSlider.addEventListener('input', calcular);

    // Init
    renderPlazo();
    calcular();
</script>

<style>
    input[type=range]::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 18px;
        height: 18px;
        background: #10b981;
        cursor: pointer;
        border-radius: 50%;
        border: 3px solid white;
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.3);
    }
    .dark input[type=range]::-webkit-slider-thumb {
        border-color: #000;
    }
    input[type=range] {
        background: #e5e7eb;
    }
    .dark input[type=range] {
        background: #1f2937;
    }
</style>
@endsection
