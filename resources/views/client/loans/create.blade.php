@extends('client.layout')

@section('title', 'Solicitar Préstamo')

@section('content')
<div class="h-screen px-6 py-6 bg-[#f8fafc] dark:bg-[#020617] transition-all duration-700 flex flex-col overflow-hidden">
    
    <!-- Header Minimalista -->
    <div class="max-w-7xl mx-auto w-full mb-6 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-6">
            <a href="{{ route('client.loans') }}" class="w-8 h-8 flex items-center justify-center rounded-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 text-gray-500 hover:text-gray-900 dark:hover:text-white transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                Simulador <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 to-teal-400">Premium</span>
            </h1>
        </div>
        <div class="flex items-center gap-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
            <span>Tasa Anual Garantizada</span>
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
        </div>
    </div>

    <div class="max-w-7xl mx-auto w-full flex-1 min-h-0 overflow-hidden">
        <form action="{{ route('client.loans.store') }}" method="POST" id="loanForm" class="grid lg:grid-cols-12 gap-8 h-full">
            @csrf
            
            <!-- Configurador Central (Izquierda + Centro) -->
            <div class="lg:col-span-8 flex flex-col gap-6 overflow-y-auto pr-2 custom-scrollbar">
                
                <!-- Card Principal: Monto y Plazo -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 shrink-0">
                    <div class="bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800/50 p-6 rounded-[2rem] shadow-sm">
                        <div class="flex justify-between items-center mb-4">
                            <label class="text-[10px] uppercase font-black text-slate-400 tracking-widest">Importe solicitado</label>
                            <span class="text-xs font-bold text-emerald-500">Soles (PEN)</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-2xl font-black text-slate-300">S/</span>
                            <input type="number" name="amount" id="amountInput" value="1000" min="100" step="100"
                                   class="bg-transparent border-none p-0 text-4xl font-black text-slate-900 dark:text-white focus:ring-0 w-full tabular-nums">
                        </div>
                    </div>

                    <div class="bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800/50 p-6 rounded-[2rem] shadow-sm">
                        <div class="flex justify-between items-center mb-4">
                            <label class="text-[10px] uppercase font-black text-slate-400 tracking-widest">Plazo preferente</label>
                            <span id="monthsDisplay" class="text-xs font-bold text-emerald-500 whitespace-nowrap tabular-nums">12 meses</span>
                        </div>
                        <div class="py-2">
                            <input type="range" id="monthsSlider" min="1" max="24" value="12" 
                                   class="w-full h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full appearance-none cursor-pointer accent-emerald-500">
                            <input type="hidden" name="months" id="monthsHidden" value="12">
                        </div>
                    </div>
                </div>

                <!-- Card Cuenta Bancaria: NUEVO APARTADO PROFESIONAL -->
                <div class="bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800/50 p-8 rounded-[2.5rem] shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-6 opacity-5 dark:opacity-10">
                        <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 24 24"><path d="M21 18H3V6h18v12zM3 4c-1.104 0-2 .896-2 2v12c0 1.104.896 2 2 2h18c1.104 0 2-.896 2-2V6c0-1.104-.896-2-2-2H3z"/></svg>
                    </div>
                    
                    <h3 class="text-xs uppercase font-black text-slate-400 tracking-widest mb-6">Información de Desembolso</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                        <div class="space-y-4">
                            <div>
                                <label class="text-[10px] font-bold text-slate-500 mb-1.5 block">Banco receptor</label>
                                <select name="bank_name" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 transition-all">
                                    <option value="BCP">BCP — Banco de Crédito</option>
                                    <option value="BBVA">BBVA</option>
                                    <option value="Interbank">Interbank</option>
                                    <option value="Scotiabank">Scotiabank</option>
                                    <option value="Banco de la Nación">Banco de la Nación</option>
                                    <option value="Otros">Otros (Cajas / Cooperativas)</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-slate-500 mb-1.5 block">Tipo de cuenta</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="account_type" value="ahorros" checked class="hidden peer">
                                        <div class="py-2.5 text-center rounded-xl border border-slate-200 dark:border-slate-800 text-[10px] font-black uppercase peer-checked:bg-slate-900 dark:peer-checked:bg-white peer-checked:text-white dark:peer-checked:text-slate-900 transition-all">Ahorros</div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="account_type" value="corriente" class="hidden peer">
                                        <div class="py-2.5 text-center rounded-xl border border-slate-200 dark:border-slate-800 text-[10px] font-black uppercase peer-checked:bg-slate-900 dark:peer-checked:bg-white peer-checked:text-white dark:peer-checked:text-slate-900 transition-all">Corriente</div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="text-[10px] font-bold text-slate-500 mb-1.5 block">Número de cuenta</label>
                                <input type="text" name="account_number" required placeholder="000-0000000-00"
                                       class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm font-semibold tabular-nums focus:ring-2 focus:ring-emerald-500/20 placeholder-slate-300 dark:placeholder-slate-700">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-slate-500 mb-1.5 block">CCI (Opcional)</label>
                                <input type="text" name="cci" placeholder="Código de Cuenta Interbancario"
                                       class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm font-semibold tabular-nums focus:ring-2 focus:ring-emerald-500/20 placeholder-slate-300 dark:placeholder-slate-700">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Motivo y Motivo Ampliado -->
                <div class="bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800/50 p-8 rounded-[2.5rem] flex-1 min-h-[160px] flex flex-col">
                    <label class="text-[10px] uppercase font-black text-slate-400 tracking-widest mb-4">Propósito del financiamiento</label>
                    <textarea name="reason" id="reason" required minlength="10"
                              placeholder="Describe brevemente en qué invertirás el capital..."
                              class="w-full flex-1 bg-transparent border-none p-0 text-base font-medium text-slate-600 dark:text-slate-300 placeholder-slate-300 dark:placeholder-slate-700 focus:ring-0 resize-none"></textarea>
                </div>
            </div>

            <!-- Panel de Control y Resumen (Derecha) -->
            <div class="lg:col-span-4 h-full">
                <div class="bg-slate-950 dark:bg-white text-white dark:text-slate-950 h-full rounded-[3rem] p-10 flex flex-col shadow-2xl relative overflow-hidden transition-all duration-500">
                    
                    <div class="relative z-10 flex-1">
                        <div class="flex items-center justify-between mb-10">
                            <h3 class="text-xs font-black uppercase tracking-[0.3em] text-emerald-500">Liquidación</h3>
                            <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>
                        </div>

                        <div class="space-y-6">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-slate-500 font-bold">Capital</span>
                                <span id="resumenCapital" class="font-black tabular-nums">S/ 0.00</span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-slate-500 font-bold">Ganancia empresa (20%)</span>
                                <span id="resumenInteres" class="font-black text-emerald-500 tabular-nums">+ S/ 0.00</span>
                            </div>
                            
                            <div class="h-px bg-white/10 dark:bg-slate-200 my-4"></div>

                            <div>
                                <span class="text-[10px] uppercase font-black text-slate-500 block mb-2 tracking-[0.1em]">Reembolso Total</span>
                                <span id="resumenTotal" class="text-5xl font-black tabular-nums tracking-tighter block mb-8">S/ 0.00</span>
                            </div>

                            <div class="bg-emerald-500/10 dark:bg-emerald-500/5 p-6 rounded-[2rem] border border-emerald-500/20 dark:border-emerald-500/10">
                                <p class="text-[10px] uppercase font-black text-emerald-500/80 mb-2 tracking-widest text-center">Cuota Mensual Fija</p>
                                <div class="flex items-center justify-center gap-2">
                                    <span class="text-4xl font-black tracking-tighter" id="resumenCuota">S/ 0.00</span>
                                    <small class="text-[10px] font-bold text-emerald-500/60 uppercase">/ Mes</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="relative z-10 pt-8 mt-6">
                        <input type="hidden" name="type" value="cuotas">
                        <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black py-5 rounded-[1.5rem] transition-all hover:scale-[1.03] active:scale-95 shadow-[0_15px_30px_rgba(16,185,129,0.3)] group">
                            <span class="inline-flex items-center gap-2">
                                Solicitar Fondos
                                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </span>
                        </button>
                        <p class="text-[9px] text-slate-500 text-center mt-6 font-bold leading-tight opacity-60 uppercase tracking-widest">
                            Desembolso sujeto a validación de identidad y perfil financiero.
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

    const resumenCapital = document.getElementById('resumenCapital');
    const resumenInteres = document.getElementById('resumenInteres');
    const resumenTotal   = document.getElementById('resumenTotal');
    const resumenCuota   = document.getElementById('resumenCuota');

    const fmt = n => 'S/ ' + n.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    function calcular() {
        let monto = parseFloat(amountInput.value) || 0;
        let meses = parseInt(monthsSlider.value);
        
        monthsDisplay.textContent = meses + (meses === 1 ? ' mes' : ' meses');
        monthsHidden.value = meses;

        if (monto <= 0) {
            updateResumen(0, 0, 0, 0);
            return;
        }

        // Simulación según regla del usuario: 20% ganancia total
        // Ejemplo: 100 capital -> 20 ganancia -> 120 total
        const tasaInteres = 0.20;
        const ganancia = monto * tasaInteres;
        const total = monto + ganancia;
        const cuota = total / meses;

        updateResumen(monto, ganancia, total, cuota);
    }

    function updateResumen(monto, interes, total, cuota) {
        resumenCapital.textContent = fmt(monto);
        resumenInteres.textContent = '+ ' + fmt(interes);
        resumenTotal.textContent    = fmt(total);
        resumenCuota.textContent    = fmt(cuota);
    }

    amountInput.addEventListener('input', calcular);
    monthsSlider.addEventListener('input', calcular);

    // Inicializar
    calcular();
</script>

<style>
    /* Custom Modern Range Slider */
    input[type=range]::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 14px;
        height: 14px;
        background: #10b981;
        cursor: pointer;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.3);
        transition: all 0.2s;
    }
    input[type=range]::-webkit-slider-thumb:hover {
        transform: scale(1.3);
        background: #059669;
    }

    /* Hide Number Spinners */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Custom Scrollbar for hidden elements */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #1e293b; }
</style>
@endsection
