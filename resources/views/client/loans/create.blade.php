@extends('client.layout')

@section('title', 'Solicitar Préstamo')

@section('content')
<div class="h-[calc(100vh-64px)] md:h-screen px-4 md:px-8 py-6 bg-gray-50 dark:bg-gray-950 transition-colors duration-500 flex flex-col">
    
    <!-- Header Compacto -->
    <div class="max-w-6xl mx-auto w-full mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('client.loans') }}" class="inline-flex items-center gap-1 text-[10px] font-bold text-gray-400 hover:text-gray-900 dark:hover:text-white transition-all uppercase tracking-tighter mb-1">
                ← Volver
            </a>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                Nueva <span class="text-green-600">Solicitud</span>
            </h1>
        </div>
        <div class="hidden md:block text-right">
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Simulador Digital</p>
            <p class="text-xs text-gray-500">Tasa fija 20%</p>
        </div>
    </div>

    <div class="max-w-6xl mx-auto w-full flex-1 min-h-0">
        <form action="{{ route('client.loans.store') }}" method="POST" id="loanForm" class="grid lg:grid-cols-12 gap-6 h-full items-stretch">
            @csrf
            
            <!-- Configurador (Izquierda) -->
            <div class="lg:col-span-7 space-y-4 flex flex-col justify-between">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Monto -->
                    <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 rounded-3xl shadow-sm">
                        <label class="text-[10px] uppercase font-black text-gray-400 block mb-2 tracking-widest">¿Cuánto necesitas?</label>
                        <div class="flex items-center gap-2">
                            <span class="text-xl font-bold text-gray-400">S/</span>
                            <input type="number" name="amount" id="amountInput" value="1000" min="100" step="100"
                                   class="bg-transparent border-none p-0 text-3xl font-black text-gray-900 dark:text-white focus:ring-0 w-full tabular-nums">
                        </div>
                    </div>

                    <!-- Plazo -->
                    <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 rounded-3xl shadow-sm">
                        <label class="text-[10px] uppercase font-black text-gray-400 block mb-2 tracking-widest">Plazo de pago</label>
                        <div class="flex items-center justify-between gap-4">
                            <input type="range" id="monthsSlider" min="1" max="24" value="12" 
                                   class="flex-1 h-1 bg-gray-200 dark:bg-gray-800 rounded-full appearance-none cursor-pointer accent-green-500">
                            <span id="monthsDisplay" class="text-xl font-black text-green-600 dark:text-green-400 tabular-nums whitespace-nowrap">12 meses</span>
                            <input type="hidden" name="months" id="monthsHidden" value="12">
                        </div>
                    </div>
                </div>

                <!-- Motivo -->
                <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 rounded-3xl shadow-sm flex-1 flex flex-col">
                    <label class="text-[10px] uppercase font-black text-gray-400 block mb-3 tracking-widest">Motivo del préstamo</label>
                    <textarea name="reason" id="reason" required minlength="10"
                              placeholder="Describe brevemente el uso del dinero..."
                              class="w-full flex-1 bg-gray-50 dark:bg-gray-950 border-none rounded-2xl p-4 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-1 focus:ring-green-500 transition-all resize-none"></textarea>
                </div>

                <!-- Info Alert -->
                <div class="hidden md:flex items-center gap-3 p-3 bg-gray-100 dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800">
                    <div class="w-8 h-8 rounded-full bg-white dark:bg-black flex items-center justify-center shrink-0 shadow-sm text-green-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/></svg>
                    </div>
                    <p class="text-[10px] text-gray-500 dark:text-gray-400 leading-tight">
                        La aprobación se realiza en menos de 24 horas hábiles tras validar tu perfil.
                    </p>
                </div>
            </div>

            <!-- Resumen & Acción (Derecha) -->
            <div class="lg:col-span-5 h-full">
                <div class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 h-full rounded-[2.5rem] p-8 flex flex-col shadow-2xl relative overflow-hidden transition-all duration-300">
                    
                    <!-- Decorative element -->
                    <div class="absolute top-0 right-0 w-32 h-32 bg-green-500/20 blur-[60px] rounded-full -mr-16 -mt-16"></div>

                    <div class="relative z-10 flex-1 flex flex-col">
                        <h3 class="text-lg font-black uppercase tracking-widest text-green-400 mb-8">Resumen</h3>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center group">
                                <span class="text-xs text-gray-400 dark:text-gray-500 font-medium">Intereses</span>
                                <span id="resumenInteres" class="text-sm font-bold tabular-nums">S/ 0.00</span>
                            </div>
                            <div class="flex justify-between items-center group">
                                <span class="text-xs text-gray-400 dark:text-gray-500 font-medium">Cargos Operativos</span>
                                <span id="resumenCargos" class="text-sm font-bold tabular-nums">S/ 0.00</span>
                            </div>
                            <div class="flex justify-between items-center group border-b border-white/10 dark:border-black/5 pb-4">
                                <span class="text-xs text-gray-400 dark:text-gray-500 font-medium">IGV</span>
                                <span id="resumenIgv" class="text-sm font-bold tabular-nums">S/ 0.00</span>
                            </div>

                            <div class="pt-2">
                                <span class="text-[10px] uppercase font-black text-green-500 block mb-1">Total a devolver</span>
                                <span id="resumenTotal" class="text-4xl font-black tabular-nums tracking-tighter">S/ 0.00</span>
                            </div>

                            <div class="bg-white/5 dark:bg-black/5 p-5 rounded-3xl border border-white/10 dark:border-black/10 mt-6 translate-y-0 transition-transform">
                                <p class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 mb-1">Cuota mensual estimada</p>
                                <div class="flex items-baseline gap-1">
                                    <span class="text-3xl font-black text-green-400" id="resumenCuota">S/ 0.00</span>
                                    <span class="text-[10px] font-bold text-gray-500">/ mes</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-white/10 dark:border-black/5 relative z-10">
                        <button type="submit" class="w-full bg-green-500 hover:bg-green-400 text-gray-900 font-black py-4 rounded-2xl transition-all hover:scale-[1.02] active:scale-95 shadow-lg shadow-green-500/20">
                            Confirmar Solicitud
                        </button>
                        <p class="text-[9px] text-gray-500 text-center mt-4 leading-tight opacity-70 italic font-medium">
                            Sujeto a evaluación. La tasa final puede variar.<br>Referencial y sin carácter contractual.
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

    // Re-calcular al cargar para inicializar con valores por defecto
    calcular();
</script>

<style>
    /* Estilos personalizados para un look aún más limpio */
    input[type=range]::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 18px;
        height: 18px;
        background: #10b981;
        cursor: pointer;
        border-radius: 50%;
        border: 3px solid white;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    }
    .dark input[type=range]::-webkit-slider-thumb { border-color: #111827; }

    /* Eliminar flechas de input number */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>
@endsection
