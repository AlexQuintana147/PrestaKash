@extends('client.layout')

@section('title', 'Solicitar Préstamo')

@section('content')
<div class="min-h-screen px-6 md:px-12 py-12 bg-gray-50 dark:bg-gray-950 transition-colors duration-500">
    
    <!-- Navigation & Header -->
    <div class="max-w-5xl mx-auto mb-16">
        <a href="{{ route('client.loans') }}" class="group flex items-center gap-2 text-xs font-semibold text-gray-500 hover:text-gray-900 dark:hover:text-white transition-all mb-8">
            <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver al panel
        </a>
        
        <h1 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white tracking-tight leading-none mb-4">
            Solicitud de <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-600 to-emerald-400">Financiamiento</span>
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 max-w-2xl leading-relaxed">
            Personaliza tu préstamo de manera flexible. Sin límites de monto y con plazos de hasta 24 meses diseñados para tu comodidad.
        </p>
    </div>

    <div class="max-w-5xl mx-auto">
        <form action="{{ route('client.loans.store') }}" method="POST" id="loanForm" class="grid lg:grid-cols-5 gap-12 items-start">
            @csrf
            
            <!-- Configurador (Izquierda) -->
            <div class="lg:col-span-3 space-y-12">
                
                <!-- Monto -->
                <div class="space-y-6">
                    <div class="flex justify-between items-end">
                        <label class="text-xs uppercase tracking-widest font-black text-gray-400 dark:text-gray-500">¿Cuánto necesitas?</label>
                        <div class="flex items-baseline gap-1">
                            <span class="text-lg font-bold text-gray-900 dark:text-white">S/</span>
                            <input type="number" name="amount" id="amountInput" value="1000" min="100" step="100"
                                   class="bg-transparent border-none p-0 text-4xl font-black text-green-600 dark:text-green-400 focus:ring-0 w-40 tabular-nums">
                        </div>
                    </div>
                    <div class="h-px bg-gray-200 dark:bg-gray-800 w-full"></div>
                    <p class="text-[10px] text-gray-400">Ingresa el monto exacto que requieres. Sin límites mínimos restrictivos.</p>
                </div>

                <!-- Plazo -->
                <div class="space-y-6">
                    <label class="text-xs uppercase tracking-widest font-black text-gray-400 dark:text-gray-500 block">Plazo de pago (Meses)</label>
                    <div class="relative pt-2">
                        <input type="range" id="monthsSlider" min="1" max="24" value="12" 
                               class="w-full h-1.5 bg-gray-200 dark:bg-gray-800 rounded-full appearance-none cursor-pointer accent-green-500 transition-all">
                        <div class="flex justify-between mt-4 text-[10px] font-bold text-gray-400 dark:text-gray-600 uppercase tracking-tighter">
                            <span>1 Mes</span>
                            <span>12 Meses</span>
                            <span>24 Meses</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-4 rounded-2xl w-fit shadow-sm">
                        <span class="text-xs font-bold text-gray-500">Seleccionado</span>
                        <span id="monthsDisplay" class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">12 meses</span>
                        <input type="hidden" name="months" id="monthsHidden" value="12">
                    </div>
                </div>

                <!-- Motivo -->
                <div class="space-y-6">
                    <label class="text-xs uppercase tracking-widest font-black text-gray-400 dark:text-gray-500 block">¿Para qué usarás el dinero?</label>
                    <textarea name="reason" id="reason" rows="3" required minlength="10"
                              placeholder="Ej: Inversión en estudios, capital de trabajo, emergencia médica..."
                              class="w-full bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-5 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-all resize-none shadow-sm"></textarea>
                </div>

                <div class="pt-4">
                    <div class="flex items-center gap-2 p-4 bg-blue-50/50 dark:bg-blue-900/10 border border-blue-100/50 dark:border-blue-900/20 rounded-2xl">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-[11px] text-blue-600 dark:text-blue-400 leading-tight">
                            Tu solicitud será evaluada en base a tu perfil crediticio y capacidad de pago informada.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Resumen & Acción (Derecha) -->
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-[2.5rem] p-10 shadow-2xl shadow-gray-200/50 dark:shadow-none transition-all sticky top-12">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-8 flex items-center justify-between">
                        Tu Simulación
                        <span class="text-[10px] font-black uppercase tracking-widest bg-green-100 dark:bg-green-950 text-green-600 dark:text-green-400 px-2 py-1 rounded">Referencial</span>
                    </h3>

                    <div class="space-y-5">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Total Intereses (20%)</span>
                            <span id="resumenInteres" class="font-bold text-gray-900 dark:text-white">S/ 0.00</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Cargos Operativos</span>
                            <span id="resumenCargos" class="font-bold text-gray-900 dark:text-white">S/ 0.00</span>
                        </div>
                        <div class="flex justify-between text-sm border-b border-gray-50 dark:border-gray-800 pb-5">
                            <span class="text-gray-500 dark:text-gray-400">IGV sobre cargos</span>
                            <span id="resumenIgv" class="font-bold text-gray-900 dark:text-white">S/ 0.00</span>
                        </div>

                        <div class="pt-5">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs uppercase tracking-widest font-black text-gray-400">Total a devolver</span>
                                <span id="resumenTotal" class="text-3xl font-black text-green-600 dark:text-green-400 tabular-nums">S/ 0.00</span>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-800/50 p-6 rounded-3xl border border-gray-100 dark:border-gray-800">
                            <p class="text-[10px] uppercase font-black text-gray-400 dark:text-gray-500 mb-1">Pago mensual aprox.</p>
                            <div class="flex items-baseline gap-1">
                                <span class="text-3xl font-black text-gray-900 dark:text-white" id="resumenCuota">S/ 0.00</span>
                                <span class="text-xs font-bold text-gray-500">/ mes</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 space-y-6">
                        <input type="hidden" name="type" value="cuotas">
                        <button type="submit" class="group relative w-full overflow-hidden bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-black py-5 rounded-2xl transition-all hover:scale-[1.02] active:scale-[0.98]">
                            <span class="relative z-10">Confirmar Préstamo</span>
                            <div class="absolute inset-0 bg-gradient-to-r from-green-600 to-emerald-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        </button>
                        
                        <p class="text-[9px] text-gray-400 dark:text-gray-600 text-center leading-relaxed">
                            Al hacer clic en confirmar, aceptas que la tasa final puede variar tras el análisis de riesgo. 
                            <span class="block mt-1 font-bold">SUJETO A EVALUACIÓN DE LA EMPRESA.</span>
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const amountInput  = document.getElementById('amountInput');
    const monthsSlider = document.getElementById('monthsSlider');
    const monthsDisplay = document.getElementById('monthsDisplay');
    const monthsHidden  = document.getElementById('monthsHidden');

    const resumenInteres = document.getElementById('resumenInteres');
    const resumenCargos  = document.getElementById('resumenCargos');
    const resumenIgv     = document.getElementById('resumenIgv');
    const resumenTotal   = document.getElementById('resumenTotal');
    const resumenCuota   = document.getElementById('resumenCuota');

    const fmt = n => 'S/ ' + n.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    function calcular() {
        let monto = parseFloat(amountInput.value) || 0;
        let meses = parseInt(monthsSlider.value);
        
        monthsDisplay.textContent = meses + (meses === 1 ? ' mes' : ' meses');
        monthsHidden.value = meses;

        if (monto <= 0) {
            updateResumen(0, 0, 0, 0, 0);
            return;
        }

        const tasaInteres = 0.20;
        const interes = monto * tasaInteres;

        const tarifa = monto * 0.10;
        const igv = tarifa * 0.18;
        const totalCargos = tarifa + igv;

        const total = monto + interes + totalCargos;
        const cuota = total / meses;

        updateResumen(interes, tarifa, igv, total, cuota);
    }

    function updateResumen(interes, tarifa, igv, total, cuota) {
        resumenInteres.textContent = fmt(interes);
        resumenCargos.textContent   = fmt(tarifa);
        resumenIgv.textContent      = fmt(igv);
        resumenTotal.textContent    = fmt(total);
        resumenCuota.textContent    = fmt(cuota);
    }

    amountInput.addEventListener('input', calcular);
    monthsSlider.addEventListener('input', calcular);

    // Init
    calcular();
</script>

<style>
    /* Slider minimalista */
    input[type=range]::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 24px;
        height: 24px;
        background: #10b981;
        cursor: pointer;
        border-radius: 50%;
        border: 4px solid white;
        transition: transform 0.2s;
    }
    .dark input[type=range]::-webkit-slider-thumb {
        border-color: #000;
    }
    input[type=range]::-webkit-slider-thumb:hover {
        transform: scale(1.15);
    }
</style>
@endsection
