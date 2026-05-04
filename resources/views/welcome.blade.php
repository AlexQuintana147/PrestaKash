<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PrestaKash — Préstamos rápidos y seguros</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1D4ED8',
                        'primary-dark': '#1E3A8A',
                        'primary-light': '#3B82F6',
                        accent: '#60A5FA',
                    },
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        html { scroll-behavior: smooth; }
        body { overflow-x: hidden; }
        .gradient-hero { background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 60%, #BFDBFE 100%); }
        .gradient-btn  { background: linear-gradient(135deg, #1D4ED8 0%, #2563EB 100%); }
        .glass { backdrop-filter: blur(14px); background: rgba(255,255,255,0.88); }

        /* Slider custom */
        input[type=range] { -webkit-appearance: none; appearance: none; height: 6px; border-radius: 9999px; background: #BFDBFE; outline: none; cursor: pointer; }
        input[type=range]::-webkit-slider-thumb { -webkit-appearance: none; appearance: none; width: 22px; height: 22px; border-radius: 50%; background: #1D4ED8; border: 3px solid #fff; box-shadow: 0 2px 8px rgba(29,78,216,.35); cursor: pointer; transition: transform .15s; }
        input[type=range]::-webkit-slider-thumb:hover { transform: scale(1.15); }

        /* Línea de cuotas */
        .cuota-line { position: relative; }
        .cuota-line::before { content:''; position:absolute; top:50%; left:0; right:0; height:2px; background: linear-gradient(90deg,#BFDBFE,#1D4ED8); transform:translateY(-50%); z-index:0; }

        /* Animación número */
        @keyframes pop { 0%{transform:scale(1)} 50%{transform:scale(1.08)} 100%{transform:scale(1)} }
        .pop { animation: pop .25s ease; }

        /* Blob decorativo */
        .blob { position:absolute; border-radius:50%; filter:blur(80px); opacity:.18; pointer-events:none; }
    </style>
</head>
<body class="font-sans text-gray-900 bg-white antialiased">

<!-- ═══════════════════════════ NAVBAR ═══════════════════════════ -->
<nav class="fixed top-0 w-full z-50 glass border-b border-blue-100 shadow-sm">
    <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 gradient-btn rounded-xl flex items-center justify-center shadow">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-xl font-extrabold text-primary-dark tracking-tight">Presta<span class="text-primary-light">Kash</span></span>
        </div>
        <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-500">
            <a href="#como-funciona" class="hover:text-primary transition-colors duration-200">¿Cómo funciona?</a>
            <a href="#beneficios"    class="hover:text-primary transition-colors duration-200">Beneficios</a>
            <a href="#calculadora"  class="hover:text-primary transition-colors duration-200">Calculadora</a>
        </div>
        <a href="#calculadora"
           class="gradient-btn text-white text-sm font-semibold px-5 py-2.5 rounded-full shadow-md hover:shadow-lg hover:opacity-90 transition-all duration-200">
            Solicitar ahora
        </a>
    </div>
</nav>

<!-- ═══════════════════════════ HERO ═══════════════════════════ -->
<section id="calculadora" class="gradient-hero min-h-screen flex flex-col justify-center pt-16 relative overflow-hidden">

    <!-- Blobs decorativos -->
    <div class="blob w-96 h-96 bg-blue-400 top-10 -left-24"></div>
    <div class="blob w-80 h-80 bg-primary top-1/2 -right-20"></div>

    <div class="max-w-6xl mx-auto px-6 w-full py-12 flex flex-col gap-12">

        <!-- Copy superior -->
        <div class="text-center space-y-4">
            <span class="inline-flex items-center gap-2 bg-white/70 border border-blue-200 text-primary text-xs font-semibold px-4 py-1.5 rounded-full shadow-sm">
                <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                Rápido · Seguro · Sin complicaciones
            </span>
            <h1 class="text-5xl md:text-7xl font-extrabold text-primary-dark leading-none tracking-tight">
                El dinero que necesitas,<br>
                <span class="text-primary-light">hoy mismo.</span>
            </h1>
            <p class="text-gray-500 text-lg max-w-xl mx-auto leading-relaxed">
                Préstamos personales con tasa fija del <strong class="text-primary font-bold">20%</strong>.
                Sin sorpresas, sin letra pequeña. Paga cómodamente cada mes.
            </p>
        </div>

        <!-- ─── CALCULADORA ─── -->
        <div class="bg-white/80 backdrop-blur-xl border border-blue-100 rounded-3xl shadow-2xl p-8 md:p-10 max-w-3xl mx-auto w-full">

            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-xl font-bold text-primary-dark">Calcula tu préstamo</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Tasa fija 20% · Hasta 6 meses · Moneda: Soles (S/)</p>
                </div>
                <div class="hidden sm:flex items-center gap-1.5 bg-blue-50 border border-blue-100 rounded-full px-3 py-1.5">
                    <span class="text-xs font-semibold text-primary">S/</span>
                    <span class="text-xs text-gray-500">Soles peruanos</span>
                </div>
            </div>

            <!-- Slider monto -->
            <div class="mb-8">
                <div class="flex justify-between items-end mb-3">
                    <label class="text-sm font-semibold text-gray-600">Monto a solicitar</label>
                    <div class="text-right">
                        <span id="montoDisplay" class="text-3xl font-extrabold text-primary-dark leading-none">S/ 1,000</span>
                    </div>
                </div>
                <input id="montoSlider" type="range" min="200" max="20000" step="100" value="1000" class="w-full">
                <div class="flex justify-between text-xs text-gray-400 mt-2">
                    <span>S/ 200</span>
                    <span>S/ 20,000</span>
                </div>
            </div>

            <!-- Selector de plazo -->
            <div class="mb-8">
                <label class="text-sm font-semibold text-gray-600 block mb-3">Plazo de pago</label>
                <div id="plazoButtons" class="grid grid-cols-6 gap-2"></div>
            </div>

            <!-- Cronograma de pagos -->
            <div class="mb-8">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">Cronograma de pagos</p>

                <!-- Timeline horizontal (puntos + línea) -->
                <div class="relative mb-5">
                    <!-- línea de fondo -->
                    <div class="absolute top-4 left-0 right-0 h-0.5 bg-blue-100 z-0"></div>
                    <!-- línea de progreso animada -->
                    <div id="lineaProgreso" class="absolute top-4 left-0 h-0.5 bg-gradient-to-r from-primary to-primary-light z-0 transition-all duration-500" style="width:0%"></div>
                    <!-- dots -->
                    <div id="cuotaDots" class="relative z-10 flex justify-between"></div>
                </div>

                <!-- Tabla de cuotas -->
                <div id="cuotasTabla" class="space-y-1.5"></div>
            </div>

            <!-- Resumen en línea -->
            <div class="grid grid-cols-3 gap-px bg-blue-100 rounded-2xl overflow-hidden border border-blue-100">
                <div class="bg-white px-5 py-4 text-center">
                    <p class="text-xs text-gray-400 mb-1">Monto solicitado</p>
                    <p id="resumenMonto" class="text-lg font-bold text-gray-800">S/ 1,000.00</p>
                </div>
                <div class="bg-white px-5 py-4 text-center">
                    <p class="text-xs text-gray-400 mb-1">Interés (20%)</p>
                    <p id="resumenInteres" class="text-lg font-bold text-blue-500">S/ 200.00</p>
                </div>
                <div class="bg-blue-600 px-5 py-4 text-center">
                    <p class="text-xs text-blue-200 mb-1">Cuota mensual</p>
                    <p id="resumenCuota" class="text-lg font-bold text-white">S/ 200.00</p>
                </div>
            </div>

            <div class="flex items-center justify-between mt-4 px-1">
                <span class="text-sm text-gray-400">Total a devolver</span>
                <span id="resumenTotal" class="text-xl font-extrabold text-primary-dark">S/ 1,200.00</span>
            </div>

            <button class="mt-6 w-full gradient-btn text-white font-bold py-4 rounded-2xl hover:opacity-90 transition-all duration-200 shadow-lg hover:shadow-xl text-base tracking-wide">
                Solicitar este préstamo →
            </button>
        </div>

        <!-- Social proof -->
        <div class="flex items-center justify-center gap-3">
            <div class="flex -space-x-2">
                <div class="w-8 h-8 rounded-full bg-blue-300 border-2 border-white"></div>
                <div class="w-8 h-8 rounded-full bg-blue-400 border-2 border-white"></div>
                <div class="w-8 h-8 rounded-full bg-blue-500 border-2 border-white"></div>
            </div>
            <p class="text-sm text-gray-500"><span class="font-semibold text-gray-700">+2,400 personas</span> ya confiaron en nosotros</p>
        </div>

    </div>
</section>

<!-- ═══════════════════════════ CÓMO FUNCIONA ═══════════════════════════ -->
<section id="como-funciona" class="py-24 bg-white">
    <div class="max-w-6xl mx-auto px-6">
        <div class="text-center mb-16">
            <span class="text-primary text-xs font-semibold uppercase tracking-widest">Proceso simple</span>
            <h2 class="text-4xl font-extrabold text-primary-dark mt-2">3 pasos para tu préstamo</h2>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="group text-center space-y-4 p-8 rounded-2xl hover:bg-blue-50 transition-all duration-300 border border-transparent hover:border-blue-100">
                <div class="w-14 h-14 gradient-btn rounded-2xl flex items-center justify-center mx-auto shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="text-5xl font-black text-blue-100 leading-none">01</div>
                <h3 class="text-lg font-bold text-primary-dark">Calcula tu préstamo</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Elige el monto y el plazo que mejor se adapte a ti. Sin compromisos.</p>
            </div>
            <div class="group text-center space-y-4 p-8 rounded-2xl hover:bg-blue-50 transition-all duration-300 border border-transparent hover:border-blue-100">
                <div class="w-14 h-14 gradient-btn rounded-2xl flex items-center justify-center mx-auto shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div class="text-5xl font-black text-blue-100 leading-none">02</div>
                <h3 class="text-lg font-bold text-primary-dark">Completa tu solicitud</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Llena el formulario en minutos. Solo necesitas tu información básica.</p>
            </div>
            <div class="group text-center space-y-4 p-8 rounded-2xl hover:bg-blue-50 transition-all duration-300 border border-transparent hover:border-blue-100">
                <div class="w-14 h-14 gradient-btn rounded-2xl flex items-center justify-center mx-auto shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div class="text-5xl font-black text-blue-100 leading-none">03</div>
                <h3 class="text-lg font-bold text-primary-dark">Recibe tu dinero</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Aprobación rápida y depósito directo a tu cuenta. Así de fácil.</p>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════ BENEFICIOS ═══════════════════════════ -->
<section id="beneficios" class="py-24 bg-primary-dark relative overflow-hidden">
    <div class="blob w-96 h-96 bg-blue-400 -top-20 -right-20"></div>
    <div class="max-w-6xl mx-auto px-6 relative z-10">
        <div class="text-center mb-16">
            <span class="text-accent text-xs font-semibold uppercase tracking-widest">¿Por qué elegirnos?</span>
            <h2 class="text-4xl font-extrabold text-white mt-2">Todo lo que necesitas</h2>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="bg-white/10 backdrop-blur rounded-2xl p-6 border border-white/10 hover:bg-white/15 hover:-translate-y-1 transition-all duration-300">
                <div class="text-3xl mb-4">⚡</div>
                <h3 class="text-white font-bold mb-2">Aprobación rápida</h3>
                <p class="text-blue-200 text-sm leading-relaxed">Respuesta en menos de 24 horas hábiles.</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-2xl p-6 border border-white/10 hover:bg-white/15 hover:-translate-y-1 transition-all duration-300">
                <div class="text-3xl mb-4">🔒</div>
                <h3 class="text-white font-bold mb-2">100% seguro</h3>
                <p class="text-blue-200 text-sm leading-relaxed">Datos protegidos con encriptación de nivel bancario.</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-2xl p-6 border border-white/10 hover:bg-white/15 hover:-translate-y-1 transition-all duration-300">
                <div class="text-3xl mb-4">📊</div>
                <h3 class="text-white font-bold mb-2">Tasa fija 20%</h3>
                <p class="text-blue-200 text-sm leading-relaxed">Sin tasas variables ni cargos ocultos. Siempre sabes cuánto pagas.</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-2xl p-6 border border-white/10 hover:bg-white/15 hover:-translate-y-1 transition-all duration-300">
                <div class="text-3xl mb-4">📅</div>
                <h3 class="text-white font-bold mb-2">Hasta 6 meses</h3>
                <p class="text-blue-200 text-sm leading-relaxed">Elige el plazo que más te convenga, de 1 a 6 meses.</p>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════ FOOTER ═══════════════════════════ -->
<footer class="bg-white border-t border-gray-100 py-10">
    <div class="max-w-6xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 gradient-btn rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="font-extrabold text-primary-dark tracking-tight">Presta<span class="text-primary-light">Kash</span></span>
        </div>
        <p class="text-sm text-gray-400">© 2026 PrestaKash. Todos los derechos reservados.</p>
        <div class="flex gap-6 text-sm text-gray-400">
            <a href="#" class="hover:text-primary transition-colors">Privacidad</a>
            <a href="#" class="hover:text-primary transition-colors">Términos</a>
            <a href="#" class="hover:text-primary transition-colors">Contacto</a>
        </div>
    </div>
</footer>


<script>
    const montoSlider    = document.getElementById('montoSlider');
    const montoDisplay   = document.getElementById('montoDisplay');
    const plazoButtons   = document.getElementById('plazoButtons');
    const cuotaDots      = document.getElementById('cuotaDots');
    const cuotasTabla    = document.getElementById('cuotasTabla');
    const lineaProgreso  = document.getElementById('lineaProgreso');
    const resumenMonto   = document.getElementById('resumenMonto');
    const resumenInteres = document.getElementById('resumenInteres');
    const resumenTotal   = document.getElementById('resumenTotal');
    const resumenCuota   = document.getElementById('resumenCuota');

    let plazo = 3;

    const fmt = n => 'S/ ' + n.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    const MESES = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

    function mesLabel(offset) {
        const d = new Date();
        d.setMonth(d.getMonth() + offset);
        return MESES[d.getMonth()] + ' ' + d.getFullYear();
    }

    function animatePop(el) {
        el.classList.remove('pop');
        void el.offsetWidth;
        el.classList.add('pop');
    }

    function calcular() {
        const monto   = parseFloat(montoSlider.value);
        const interes = monto * 0.20;
        const total   = monto + interes;
        // La cuota es el total dividido entre los meses — el 20% ya está incluido en el total
        const cuota   = total / plazo;

        montoDisplay.textContent    = fmt(monto);
        resumenMonto.textContent    = fmt(monto);
        resumenInteres.textContent  = fmt(interes);
        resumenTotal.textContent    = fmt(total);
        resumenCuota.textContent    = fmt(cuota);

        [resumenMonto, resumenInteres, resumenTotal, resumenCuota].forEach(animatePop);

        const pct = ((monto - 200) / (20000 - 200)) * 100;
        montoSlider.style.background = `linear-gradient(90deg, #1D4ED8 ${pct}%, #BFDBFE ${pct}%)`;

        renderCronograma(cuota, total);
    }

    function renderCronograma(cuota, total) {
        // ── Dots de la línea ──
        cuotaDots.innerHTML = '';
        for (let i = 1; i <= plazo; i++) {
            const col = document.createElement('div');
            col.className = 'flex flex-col items-center gap-1';

            const dot = document.createElement('div');
            dot.className = 'w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border-2 border-white shadow transition-all duration-300 ' +
                (i === 1 ? 'bg-primary text-white scale-110 shadow-primary/40 shadow-lg' : 'bg-blue-100 text-primary');
            dot.textContent = i;

            const lbl = document.createElement('span');
            lbl.className = 'text-[10px] text-gray-400 font-medium';
            lbl.textContent = mesLabel(i);

            col.appendChild(dot);
            col.appendChild(lbl);
            cuotaDots.appendChild(col);
        }

        // Línea de progreso al 100% (todos los meses activos)
        lineaProgreso.style.width = '100%';

        // ── Tabla de cuotas ──
        cuotasTabla.innerHTML = '';
        let acumulado = 0;

        for (let i = 1; i <= plazo; i++) {
            acumulado += cuota;
            const row = document.createElement('div');
            row.className = 'flex items-center gap-3 bg-blue-50/60 hover:bg-blue-100/60 rounded-xl px-4 py-2.5 transition-colors duration-150 group';

            // Número
            const num = document.createElement('span');
            num.className = 'w-6 h-6 rounded-full bg-primary text-white text-xs font-bold flex items-center justify-center flex-shrink-0';
            num.textContent = i;

            // Fecha
            const fecha = document.createElement('span');
            fecha.className = 'text-xs text-gray-500 w-20 flex-shrink-0';
            fecha.textContent = mesLabel(i);

            // Barra de progreso
            const barWrap = document.createElement('div');
            barWrap.className = 'flex-1 h-1.5 bg-blue-100 rounded-full overflow-hidden';
            const barFill = document.createElement('div');
            const pct = (acumulado / total) * 100;
            barFill.className = 'h-full bg-gradient-to-r from-primary to-primary-light rounded-full transition-all duration-500';
            barFill.style.width = pct + '%';
            barWrap.appendChild(barFill);

            // Porcentaje acumulado
            const pctLabel = document.createElement('span');
            pctLabel.className = 'text-[10px] text-gray-400 w-8 text-right flex-shrink-0';
            pctLabel.textContent = Math.round(pct) + '%';

            // Monto cuota
            const monto = document.createElement('span');
            monto.className = 'text-sm font-bold text-primary-dark w-24 text-right flex-shrink-0';
            monto.textContent = fmt(cuota);

            row.appendChild(num);
            row.appendChild(fecha);
            row.appendChild(barWrap);
            row.appendChild(pctLabel);
            row.appendChild(monto);
            cuotasTabla.appendChild(row);
        }
    }

    function renderPlazo() {
        plazoButtons.innerHTML = '';
        for (let i = 1; i <= 6; i++) {
            const btn = document.createElement('button');
            btn.textContent = i + (i === 1 ? ' mes' : ' m');
            btn.className = 'py-2.5 rounded-xl text-sm font-semibold border transition-all duration-150 ' + (
                i === plazo
                    ? 'gradient-btn text-white border-primary shadow-md scale-105'
                    : 'bg-white text-gray-500 border-gray-200 hover:border-primary hover:text-primary'
            );
            btn.onclick = () => { plazo = i; renderPlazo(); calcular(); };
            plazoButtons.appendChild(btn);
        }
    }

    montoSlider.addEventListener('input', calcular);
    renderPlazo();
    calcular();
</script>
</body>
</html>
