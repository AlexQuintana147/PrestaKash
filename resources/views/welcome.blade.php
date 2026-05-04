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

        /* Slider custom con gradiente rosa */
        input[type=range] {
            -webkit-appearance: none;
            appearance: none;
            height: 8px;
            background: linear-gradient(to right, #ec4899 0%, #f97316 100%);
            outline: none;
            cursor: pointer;
            border-radius: 999px;
        }
        input[type=range]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
            cursor: pointer;
            border: 4px solid white;
            box-shadow: 0 4px 12px rgba(236, 72, 153, 0.4);
            border-radius: 50%;
        }
        input[type=range]::-moz-range-thumb {
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
            cursor: pointer;
            border: 4px solid white;
            box-shadow: 0 4px 12px rgba(236, 72, 153, 0.4);
            border-radius: 50%;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .animate-bounce {
            animation: bounce 2s infinite;
        }
    </style>
</head>
<body class="font-sans text-gray-900 bg-white antialiased">

@include('partials.header')

<section id="calculadora" class="relative min-h-screen flex items-center pt-16 overflow-hidden">
    <!-- Gradiente de fondo -->
    <div class="absolute inset-0 bg-gradient-to-r from-pink-500 via-pink-400 to-orange-400"></div>

    <!-- Imagen de fondo con overlay -->
    <div class="absolute inset-0 bg-cover bg-center opacity-20" style="background-image: url('https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=1200&q=80');"></div>

    <div class="relative max-w-7xl mx-auto px-3 sm:px-6 w-full grid lg:grid-cols-2 gap-4 lg:gap-8 items-center py-8 lg:py-12">

        <!-- Columna izquierda: Contenido hero -->
        <div class="text-white space-y-2 sm:space-y-4 lg:space-y-6 hidden lg:block">
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight drop-shadow-lg">
                Préstamos personales
            </h1>
            <div class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none drop-shadow-2xl">
                50%
            </div>
            <p class="text-lg sm:text-xl md:text-2xl font-semibold drop-shadow-md">
                De descuento en intereses en tu primer<br class="hidden sm:block">préstamo online
            </p>
            <p class="text-xs sm:text-sm opacity-90 max-w-md">
                *El descuento se aplica a la tasa de interés después de la fecha de desembolso.
            </p>
        </div>

        <!-- Columna derecha: Calculadora -->
        <div class="bg-white rounded-lg shadow-2xl p-2.5 sm:p-4 max-w-xl w-full lg:max-w-xl mx-auto">
            <!-- Tabs de tipo de préstamo -->
            <div class="grid grid-cols-2 gap-1.5 mb-2">
                <button id="btnCuotas" class="bg-gradient-to-r from-pink-500 to-pink-600 text-white font-bold py-1.5 px-2 text-[10px] sm:text-xs rounded-lg shadow-md transition-all">
                    Pago a Cuotas
                </button>
                <button id="btnUnico" class="bg-gray-100 text-gray-700 font-bold py-1.5 px-2 text-[10px] sm:text-xs rounded-lg hover:bg-gray-200 transition-all">
                    Pago Único
                </button>
            </div>

            <!-- Slider monto -->
            <div class="mb-2">
                <div class="flex justify-between items-baseline mb-1">
                    <label class="text-[10px] sm:text-xs font-bold text-gray-700">¿Cuánto necesitas?</label>
                    <div class="text-right">
                        <span id="montoDisplay" class="text-base sm:text-xl font-bold text-gray-900 tabular-nums">S/ 500.00</span>
                    </div>
                </div>
                <div class="relative">
                    <input id="montoSlider" type="range" min="200" max="20000" step="100" value="500" class="w-full h-2 bg-gray-200 rounded-full appearance-none cursor-pointer">
                </div>
                <div class="flex justify-between text-[9px] text-gray-500 mt-0.5">
                    <span>S/ 200</span>
                    <span>S/ 20,000</span>
                </div>
            </div>

            <!-- Selector de plazo -->
            <div class="mb-2">
                <label class="text-[10px] sm:text-xs font-bold text-gray-700 block mb-1">Plazo</label>
                <div class="bg-pink-50 rounded-lg p-1.5 mb-1">
                    <div class="text-center">
                        <div class="text-base sm:text-xl font-bold text-pink-600" id="plazoDisplay">3 cuotas (90 días)</div>
                    </div>
                </div>
                <div id="plazoButtons" class="grid grid-cols-3 gap-1"></div>
            </div>

            <!-- Desglose de pago -->
            <div class="bg-gray-50 rounded-lg p-2 sm:p-3 mb-2 space-y-1 text-[9px] sm:text-xs">
                <div class="flex justify-between">
                    <span class="text-gray-600">Cantidad Solicitada</span>
                    <span id="resumenMonto" class="font-bold text-gray-900">S/ 500.00</span>
                </div>
                <div class="flex justify-between" id="rowCuotaDigital">
                    <span class="text-gray-600">Cuota que se pagará digital</span>
                    <span id="cuotaDigital" class="font-bold text-gray-900">S/ 100.00</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Tarifa Tecnología</span>
                    <span id="tarifaTecnologia" class="font-bold text-gray-900">S/ 50.00</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Importe Total</span>
                    <span id="importeTotal" class="font-bold text-gray-900">S/ 60.33</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">IGV</span>
                    <span id="igv" class="font-bold text-gray-900">S/ 24.88</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Valor Primera Cuota</span>
                    <span id="valorPrimeraCuota" class="font-bold text-gray-900">S/ 228.28</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Fecha de Pago Primera Cuota</span>
                    <span id="fechaPrimeraCuota" class="font-bold text-gray-900">03/06/2026</span>
                </div>

                <!-- Total a Pagar por Mes (solo en modo cuotas) -->
                <div class="border-t border-pink-200 pt-1 mt-1" id="rowTotalMensual">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700 font-bold text-[10px] sm:text-sm">Total a Pagar por Mes</span>
                        <span id="totalMensual" class="text-sm sm:text-lg font-bold text-pink-600">S/ 219.67</span>
                    </div>
                </div>

                <div class="border-t border-gray-300 pt-1 mt-1">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700 font-bold text-[10px] sm:text-sm">Total a Pagar</span>
                        <span id="resumenTotal" class="text-sm sm:text-lg font-bold text-pink-600">S/ 600.00</span>
                    </div>
                </div>
            </div>

            <button class="w-full bg-gradient-to-r from-purple-700 to-purple-900 text-white font-bold py-2 text-xs sm:text-sm rounded-lg hover:from-purple-800 hover:to-purple-950 shadow-lg transition-all">
                Solicítalo Ahora
            </button>
        </div>
    </div>
</section>

<section id="como-funciona" class="py-16 sm:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-12 sm:mb-16">
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-3">Obtén tu préstamo en 3 pasos</h2>
            <p class="text-gray-600 text-base sm:text-lg">Simple, rápido y sin complicaciones</p>
        </div>
        <div class="grid md:grid-cols-3 gap-8 lg:gap-12">
            <!-- Paso 1 -->
            <div class="group relative">
                <div class="bg-gradient-to-br from-gray-50 to-white border border-gray-200 rounded-2xl p-6 sm:p-8 hover:shadow-xl hover:border-pink-200 transition-all duration-300">
                    <div class="flex items-start gap-4 mb-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg shadow-pink-500/30">
                            <span class="text-white font-bold text-xl">1</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Calcula y elige</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">Usa nuestra calculadora para elegir el monto y plazo que mejor se adapte a ti.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paso 2 -->
            <div class="group relative">
                <div class="bg-gradient-to-br from-gray-50 to-white border border-gray-200 rounded-2xl p-6 sm:p-8 hover:shadow-xl hover:border-pink-200 transition-all duration-300">
                    <div class="flex items-start gap-4 mb-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg shadow-pink-500/30">
                            <span class="text-white font-bold text-xl">2</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Completa tu solicitud</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">Llena el formulario con tu información. Solo toma 5 minutos.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paso 3 -->
            <div class="group relative">
                <div class="bg-gradient-to-br from-pink-50 to-white border-2 border-pink-200 rounded-2xl p-6 sm:p-8 shadow-lg shadow-pink-500/10 hover:shadow-xl hover:border-pink-300 transition-all duration-300">
                    <div class="flex items-start gap-4 mb-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg shadow-pink-500/40">
                            <span class="text-white font-bold text-xl">3</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Recibe tu dinero</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">Aprobación en menos de 24 horas. Depósito directo en tu cuenta.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="beneficios" class="py-16 sm:py-20 bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-12 sm:mb-16">
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-white mb-3">¿Por qué PrestaKash?</h2>
            <p class="text-gray-400 text-base sm:text-lg">Transparencia y rapidez en cada préstamo</p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Beneficio 1 -->
            <div class="group bg-gray-800/50 backdrop-blur border border-gray-700 rounded-2xl p-6 hover:bg-gray-800 hover:border-pink-500/50 transition-all duration-300">
                <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h3 class="text-white font-bold text-lg mb-2">Aprobación rápida</h3>
                <p class="text-gray-400 text-sm leading-relaxed">Respuesta en menos de 24 horas. Sin esperas innecesarias.</p>
            </div>

            <!-- Beneficio 2 -->
            <div class="group bg-gray-800/50 backdrop-blur border border-gray-700 rounded-2xl p-6 hover:bg-gray-800 hover:border-pink-500/50 transition-all duration-300">
                <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h3 class="text-white font-bold text-lg mb-2">100% seguro</h3>
                <p class="text-gray-400 text-sm leading-relaxed">Tus datos protegidos con encriptación de nivel bancario.</p>
            </div>

            <!-- Beneficio 3 -->
            <div class="group bg-gradient-to-br from-pink-900/30 to-gray-800/50 backdrop-blur border-2 border-pink-500/50 rounded-2xl p-6 hover:border-pink-500 transition-all duration-300 shadow-lg shadow-pink-500/10">
                <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300 shadow-lg shadow-pink-500/50">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-white font-bold text-lg mb-2">Tasa fija 20%</h3>
                <p class="text-gray-400 text-sm leading-relaxed">Sin tasas variables ni cargos ocultos. Siempre sabes cuánto pagas.</p>
            </div>

            <!-- Beneficio 4 -->
            <div class="group bg-gray-800/50 backdrop-blur border border-gray-700 rounded-2xl p-6 hover:bg-gray-800 hover:border-pink-500/50 transition-all duration-300">
                <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-white font-bold text-lg mb-2">Hasta 6 meses</h3>
                <p class="text-gray-400 text-sm leading-relaxed">Elige el plazo que mejor se ajuste a tu presupuesto.</p>
            </div>
        </div>
    </div>
</section>

@include('partials.footer')


<script>
    const montoSlider = document.getElementById('montoSlider');
    const montoDisplay = document.getElementById('montoDisplay');
    const plazoButtons = document.getElementById('plazoButtons');
    const plazoDisplay = document.getElementById('plazoDisplay');
    const resumenMonto = document.getElementById('resumenMonto');
    const resumenTotal = document.getElementById('resumenTotal');
    const totalMensual = document.getElementById('totalMensual');
    const cuotaDigital = document.getElementById('cuotaDigital');
    const tarifaTecnologia = document.getElementById('tarifaTecnologia');
    const importeTotal = document.getElementById('importeTotal');
    const igv = document.getElementById('igv');
    const valorPrimeraCuota = document.getElementById('valorPrimeraCuota');
    const fechaPrimeraCuota = document.getElementById('fechaPrimeraCuota');
    const fechaUltimoPago = document.getElementById('fechaUltimoPago');
    const btnCuotas = document.getElementById('btnCuotas');
    const btnUnico = document.getElementById('btnUnico');
    const rowCuotaDigital = document.getElementById('rowCuotaDigital');
    const rowTotalMensual = document.getElementById('rowTotalMensual');

    let plazo = 3;
    let tipoPago = 'cuotas'; // 'cuotas' o 'unico'

    const fmt = n => 'S/ ' + n.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    const plazosData = [
        { meses: 1, dias: 30 },
        { meses: 2, dias: 60 },
        { meses: 3, dias: 90 },
        { meses: 4, dias: 120 },
        { meses: 5, dias: 150 },
        { meses: 6, dias: 180 }
    ];

    function getFecha(offsetMeses) {
        const d = new Date();
        d.setMonth(d.getMonth() + offsetMeses);
        const dia = String(d.getDate()).padStart(2, '0');
        const mes = String(d.getMonth() + 1).padStart(2, '0');
        const año = d.getFullYear();
        return `${dia}/${mes}/${año}`;
    }

    function calcular() {
        const monto = parseFloat(montoSlider.value);
        const tasaInteres = 0.20; // 20% anual
        const interes = monto * tasaInteres;

        // Tarifa de tecnología (ejemplo: 10% del monto)
        const tarifa = monto * 0.10;
        const igvMonto = tarifa * 0.18; // IGV 18%
        const importeTotalMonto = tarifa + igvMonto;

        const totalAPagar = monto + interes + importeTotalMonto;
        const cuotaMensual = totalAPagar / plazo;

        // Actualizar displays
        montoDisplay.textContent = fmt(monto);
        resumenMonto.textContent = fmt(monto);
        tarifaTecnologia.textContent = fmt(tarifa);
        igv.textContent = fmt(igvMonto);
        importeTotal.textContent = fmt(importeTotalMonto);
        resumenTotal.textContent = fmt(totalAPagar);

        // Actualizar según tipo de pago
        if (tipoPago === 'cuotas') {
            cuotaDigital.textContent = fmt(cuotaMensual);
            valorPrimeraCuota.textContent = fmt(cuotaMensual);
            totalMensual.textContent = fmt(cuotaMensual);
            rowCuotaDigital.style.display = 'flex';
            rowTotalMensual.style.display = 'block';
        } else {
            rowCuotaDigital.style.display = 'none';
            rowTotalMensual.style.display = 'none';
            valorPrimeraCuota.textContent = fmt(totalAPagar);
        }

        // Actualizar fechas
        const fechaPrimera = getFecha(1);
        fechaPrimeraCuota.textContent = fechaPrimera;

        // Actualizar display de plazo
        const plazoInfo = plazosData.find(p => p.meses === plazo);
        if (plazoInfo) {
            plazoDisplay.textContent = tipoPago === 'cuotas'
                ? `${plazo} cuotas (${plazoInfo.dias} días)`
                : `Pago único (${plazoInfo.dias} días)`;
        }
    }

    function renderPlazo() {
        plazoButtons.innerHTML = '';
        plazosData.forEach(p => {
            const btn = document.createElement('button');
            btn.textContent = p.meses + (p.meses === 1 ? ' mes' : ' m');
            btn.className = 'py-1 sm:py-1.5 text-[10px] sm:text-xs font-bold rounded-lg transition-all ' + (
                p.meses === plazo
                    ? 'bg-gradient-to-r from-pink-500 to-pink-600 text-white shadow-md'
                    : 'bg-white text-gray-700 border border-gray-200 hover:border-pink-400'
            );
            btn.onclick = () => {
                plazo = p.meses;
                renderPlazo();
                calcular();
            };
            plazoButtons.appendChild(btn);
        });
    }

    function cambiarTipoPago(tipo) {
        tipoPago = tipo;

        if (tipo === 'cuotas') {
            btnCuotas.className = 'bg-gradient-to-r from-pink-500 to-pink-600 text-white font-bold py-1.5 px-2 text-[10px] sm:text-xs rounded-lg shadow-md transition-all';
            btnUnico.className = 'bg-gray-100 text-gray-700 font-bold py-1.5 px-2 text-[10px] sm:text-xs rounded-lg hover:bg-gray-200 transition-all';
        } else {
            btnUnico.className = 'bg-gradient-to-r from-pink-500 to-pink-600 text-white font-bold py-1.5 px-2 text-[10px] sm:text-xs rounded-lg shadow-md transition-all';
            btnCuotas.className = 'bg-gray-100 text-gray-700 font-bold py-1.5 px-2 text-[10px] sm:text-xs rounded-lg hover:bg-gray-200 transition-all';
        }

        calcular();
    }

    // Event listeners
    montoSlider.addEventListener('input', calcular);
    btnCuotas.addEventListener('click', () => cambiarTipoPago('cuotas'));
    btnUnico.addEventListener('click', () => cambiarTipoPago('unico'));

    // Inicializar
    renderPlazo();
    calcular();
</script>
</body>
</html>
