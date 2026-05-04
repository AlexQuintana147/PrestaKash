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

        /* Slider custom */
        input[type=range] { -webkit-appearance: none; appearance: none; height: 6px; background: #D1D5DB; outline: none; cursor: pointer; }
        input[type=range]::-webkit-slider-thumb { -webkit-appearance: none; appearance: none; width: 24px; height: 24px; background: #1D4ED8; cursor: pointer; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.2); }
    </style>
</head>
<body class="font-sans text-gray-900 bg-white antialiased">

@include('partials.header')

<section id="calculadora" class="bg-white min-h-screen flex flex-col justify-center pt-20">
    <div class="max-w-7xl mx-auto px-6 w-full py-16 flex flex-col gap-16">

        <div class="text-center space-y-6">
            <h1 class="text-6xl md:text-7xl font-bold text-gray-900 leading-tight tracking-tight">
                Préstamos desde<br>
                <span class="text-primary">S/ 200 hasta más de S/ 20,000</span>
            </h1>
            <p class="text-gray-600 text-xl max-w-2xl mx-auto leading-relaxed">
                Tasa fija del <span class="font-bold text-gray-900 text-2xl">20%</span> anual.
                Sin comisiones ocultas. Paga en cuotas mensuales de 1 o a más 6 meses.
            </p>
        </div>

        <div class="bg-gray-50 border-2 border-gray-900 p-10 md:p-12 max-w-4xl mx-auto w-full">

            <div class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Calcula tu préstamo</h2>
                <p class="text-gray-600">Ajusta el monto y plazo para ver tu cuota mensual</p>
            </div>

            <!-- Slider monto -->
            <div class="mb-10">
                <div class="flex justify-between items-baseline mb-4">
                    <label class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Monto a solicitar</label>
                    <div class="text-right">
                        <span id="montoDisplay" class="text-5xl font-bold text-gray-900 tabular-nums">S/ 1,000</span>
                    </div>
                </div>
                <input id="montoSlider" type="range" min="200" max="20000" step="100" value="1000" class="w-full h-2 bg-gray-300">
                <div class="flex justify-between text-sm text-gray-500 mt-3 font-medium">
                    <span>S/ 200</span>
                    <span>S/ 20,000</span>
                </div>
            </div>

            <!-- Selector de plazo -->
            <div class="mb-10">
                <label class="text-sm font-semibold text-gray-700 uppercase tracking-wide block mb-4">Plazo de pago</label>
                <div id="plazoButtons" class="grid grid-cols-6 gap-3"></div>
            </div>

            <!-- Resumen destacado -->
            <div class="bg-white border-2 border-gray-900 p-8 mb-8">
                <div class="grid md:grid-cols-3 gap-8 mb-6">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Monto solicitado</p>
                        <p id="resumenMonto" class="text-2xl font-bold text-gray-900 tabular-nums">S/ 1,000.00</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Interés total (20%)</p>
                        <p id="resumenInteres" class="text-2xl font-bold text-gray-900 tabular-nums">S/ 200.00</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Total a pagar</p>
                        <p id="resumenTotal" class="text-2xl font-bold text-gray-900 tabular-nums">S/ 1,200.00</p>
                    </div>
                </div>

                <div class="pt-6 border-t-2 border-gray-200">
                    <div class="flex justify-between items-baseline">
                        <span class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Tu cuota mensual</span>
                        <span id="resumenCuota" class="text-4xl font-bold text-primary tabular-nums">S/ 200.00</span>
                    </div>
                </div>
            </div>

            <!-- Cronograma de pagos -->
            <div class="mb-8">
                <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-4">Cronograma de pagos</p>
                <div id="cuotasTabla" class="space-y-2 bg-white border border-gray-200 p-4"></div>
            </div>

            <button class="w-full bg-gray-900 text-white font-bold py-5 text-lg hover:bg-gray-800">
                Solicitar préstamo ahora →
            </button>
        </div>
    </div>
</section>

<section id="como-funciona" class="py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-20">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Obtén tu préstamo en 3 pasos</h2>
            <p class="text-gray-600 text-lg">Proceso simple y rápido, sin complicaciones</p>
        </div>
        <div class="grid md:grid-cols-3 gap-12">
            <div class="text-center space-y-4">
                <div class="w-16 h-16 bg-gray-900 text-white text-2xl font-bold flex items-center justify-center mx-auto mb-6">
                    01
                </div>
                <h3 class="text-xl font-bold text-gray-900">Calcula y elige</h3>
                <p class="text-gray-600 leading-relaxed">Usa nuestra calculadora para elegir el monto y plazo que mejor se adapte a tus necesidades.</p>
            </div>
            <div class="text-center space-y-4">
                <div class="w-16 h-16 bg-gray-900 text-white text-2xl font-bold flex items-center justify-center mx-auto mb-6">
                    02
                </div>
                <h3 class="text-xl font-bold text-gray-900">Completa tu solicitud</h3>
                <p class="text-gray-600 leading-relaxed">Llena el formulario con tu información personal. Solo toma 5 minutos.</p>
            </div>
            <div class="text-center space-y-4">
                <div class="w-16 h-16 bg-primary text-white text-2xl font-bold flex items-center justify-center mx-auto mb-6">
                    03
                </div>
                <h3 class="text-xl font-bold text-gray-900">Recibe tu dinero</h3>
                <p class="text-gray-600 leading-relaxed">Aprobación en menos de 24 horas. El dinero se deposita directo en tu cuenta.</p>
            </div>
        </div>
    </div>
</section>

<section id="beneficios" class="py-24 bg-gray-900">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-20">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">¿Por qué PrestaKash?</h2>
            <p class="text-gray-400 text-lg">Transparencia y rapidez en cada préstamo</p>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="bg-gray-800 border-2 border-gray-700 p-8">
                <div class="text-4xl mb-4">⚡</div>
                <h3 class="text-white font-bold text-lg mb-3">Aprobación rápida</h3>
                <p class="text-gray-400 leading-relaxed">Respuesta en menos de 24 horas hábiles. Sin esperas innecesarias.</p>
            </div>
            <div class="bg-gray-800 border-2 border-gray-700 p-8">
                <div class="text-4xl mb-4">🔒</div>
                <h3 class="text-white font-bold text-lg mb-3">100% seguro</h3>
                <p class="text-gray-400 leading-relaxed">Tus datos protegidos con encriptación de nivel bancario.</p>
            </div>
            <div class="bg-gray-800 border-2 border-primary p-8">
                <div class="text-4xl mb-4">📊</div>
                <h3 class="text-white font-bold text-lg mb-3">Tasa fija 20%</h3>
                <p class="text-gray-400 leading-relaxed">Sin tasas variables ni cargos ocultos. Siempre sabes cuánto pagas.</p>
            </div>
            <div class="bg-gray-800 border-2 border-gray-700 p-8">
                <div class="text-4xl mb-4">📅</div>
                <h3 class="text-white font-bold text-lg mb-3">Hasta 6 meses</h3>
                <p class="text-gray-400 leading-relaxed">Elige el plazo que mejor se ajuste a tu presupuesto.</p>
            </div>
        </div>
    </div>
</section>

@include('partials.footer')


<script>
    const montoSlider    = document.getElementById('montoSlider');
    const montoDisplay   = document.getElementById('montoDisplay');
    const plazoButtons   = document.getElementById('plazoButtons');
    const cuotasTabla    = document.getElementById('cuotasTabla');
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

    function calcular() {
        const monto   = parseFloat(montoSlider.value);
        const interes = monto * 0.20;
        const total   = monto + interes;
        const cuota   = total / plazo;

        montoDisplay.textContent    = fmt(monto);
        resumenMonto.textContent    = fmt(monto);
        resumenInteres.textContent  = fmt(interes);
        resumenTotal.textContent    = fmt(total);
        resumenCuota.textContent    = fmt(cuota);

        renderCronograma(cuota);
    }

    function renderCronograma(cuota) {
        cuotasTabla.innerHTML = '';

        for (let i = 1; i <= plazo; i++) {
            const row = document.createElement('div');
            row.className = 'flex items-center justify-between py-3 border-b border-gray-200 last:border-0';

            const info = document.createElement('div');
            info.className = 'flex items-center gap-4';

            const num = document.createElement('span');
            num.className = 'w-8 h-8 bg-gray-900 text-white text-sm font-bold flex items-center justify-center';
            num.textContent = i;

            const fecha = document.createElement('span');
            fecha.className = 'text-sm font-medium text-gray-700';
            fecha.textContent = mesLabel(i);

            info.appendChild(num);
            info.appendChild(fecha);

            const monto = document.createElement('span');
            monto.className = 'text-base font-bold text-gray-900 tabular-nums';
            monto.textContent = fmt(cuota);

            row.appendChild(info);
            row.appendChild(monto);
            cuotasTabla.appendChild(row);
        }
    }

    function renderPlazo() {
        plazoButtons.innerHTML = '';
        for (let i = 1; i <= 6; i++) {
            const btn = document.createElement('button');
            btn.textContent = i + (i === 1 ? ' mes' : ' m');
            btn.className = 'py-3 text-sm font-bold border-2 ' + (
                i === plazo
                    ? 'bg-gray-900 text-white border-gray-900'
                    : 'bg-white text-gray-700 border-gray-300 hover:border-gray-900'
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
