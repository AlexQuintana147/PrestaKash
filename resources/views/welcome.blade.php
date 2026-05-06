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
                        primary: '#10b981',
                        'primary-dark': '#059669',
                        'primary-light': '#34d399',
                        accent: '#ec4899',
                    },
                    fontFamily: { sans: ['Roboto', 'sans-serif'] }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <style>
        html { scroll-behavior: smooth; }
        body {
            overflow-x: hidden;
            font-family: 'Roboto', sans-serif;
        }

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

<section id="calculadora" class="relative flex items-center overflow-hidden" style="min-height: 100vh;">

    <!-- Imagen de fondo desde Unsplash - ciudad/fintech -->
    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=1600&q=80');"></div>

    <!-- Overlay oscuro para legibilidad -->
    <div class="absolute inset-0 bg-gradient-to-br from-gray-950/95 via-gray-900/90 to-green-950/90"></div>

    <!-- Brillo sutil verde arriba -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-green-500 opacity-10 blur-3xl rounded-full pointer-events-none"></div>

    <!-- Contenido centrado respetando el header de 64px -->
    <div class="relative w-full max-w-7xl mx-auto px-3 sm:px-6 grid lg:grid-cols-2 gap-4 lg:gap-8 items-center" style="padding-top: calc(64px + 2rem); padding-bottom: 2rem; min-height: 100vh;">

        <!-- Columna izquierda: Contenido hero -->
        <div class="text-white space-y-2 sm:space-y-3 lg:space-y-6 text-center lg:text-left mb-4 lg:mb-0">
            <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-extrabold leading-tight drop-shadow-lg">
                Tu dinero en<br>menos de 24 horas
            </h1>
            <div class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl xl:text-8xl font-black leading-none bg-gradient-to-r from-green-400 to-emerald-300 bg-clip-text text-transparent drop-shadow-2xl">
                100%
            </div>
            <p class="text-base sm:text-lg md:text-xl lg:text-2xl font-semibold text-gray-200 drop-shadow-md">
                Todo Digital.<br class="hidden sm:block">Sin visitas al banco.
            </p>
            <p class="text-xs sm:text-sm text-gray-400 max-w-md mx-auto lg:mx-0">
                Aprobación en menos de 24 horas. Tasa fija garantizada. Cero comisiones ocultas.
            </p>
            <div class="pt-2">
                <a href="#calculadora" class="inline-block bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-400 hover:to-emerald-400 text-white font-bold px-8 py-3 rounded-xl shadow-lg shadow-green-900/40 transition-all text-sm sm:text-base">
                    Solicítalo ahora
                </a>
                <!-- Señales de confianza -->
                <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 mt-4 text-[11px] text-gray-400">
                    <span class="flex items-center gap-1"><span class="text-green-400">✓</span> Sin comisiones ocultas</span>
                    <span class="flex items-center gap-1"><span class="text-green-400">✓</span> 100% seguro</span>
                    <span class="flex items-center gap-1"><span class="text-green-400">✓</span> Respuesta en 24h</span>
                </div>
            </div>
        </div>

        <!-- Columna derecha: Calculadora -->
        <div class="bg-white rounded-2xl shadow-[0_25px_60px_rgba(0,0,0,0.5)] p-2.5 sm:p-4 max-w-xl w-full lg:max-w-xl mx-auto">
            <!-- Tabs de tipo de préstamo -->
            <div class="grid grid-cols-2 gap-1.5 mb-2">
                <button id="btnCuotas" class="bg-gradient-to-r from-green-500 to-green-600 text-white font-bold py-1.5 px-2 text-[10px] sm:text-xs rounded-lg shadow-md transition-all">
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
                    <input id="montoSlider" type="range" min="200" max="500" step="50" value="500" class="w-full h-2 bg-gray-200 rounded-full appearance-none cursor-pointer">
                </div>
                <div class="flex justify-between text-[9px] text-gray-500 mt-0.5">
                    <span>S/ 200</span>
                    <span>S/ 500</span>
                </div>
            </div>

            <!-- Selector de plazo -->
            <div class="mb-2">
                <label class="text-[10px] sm:text-xs font-bold text-gray-700 block mb-1">Plazo</label>
                <div class="bg-green-50 rounded-lg p-1.5 mb-1">
                    <div class="text-center">
                        <div class="text-base sm:text-xl font-bold text-green-600" id="plazoDisplay">3 cuotas (90 días)</div>
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
                <div class="border-t border-green-200 pt-1 mt-1" id="rowTotalMensual">
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

            <button class="w-full bg-gradient-to-r from-black to-gray-900 text-white font-bold py-2 text-xs sm:text-sm rounded-lg hover:from-gray-900 hover:to-black shadow-lg transition-all">
                Solicítalo Ahora
            </button>
        </div>
    </div>
</section>

<!-- Franja SBS -->
<section style="background: linear-gradient(90deg, #003294 0%, #4ab8d8 100%);">
    <div class="flex items-stretch">
        <div class="relative flex-shrink-0">
            <img src="/images/SBS.png" alt="SBS" class="h-16 object-contain object-left bg-[#003294] px-6 py-2"
                style="-webkit-mask-image: linear-gradient(to right, black 80%, transparent 100%); mask-image: linear-gradient(to right, black 80%, transparent 100%);">
        </div>

        <!-- Texto desktop -->
        <div class="hidden sm:flex items-center flex-1 px-4 py-4">
            <p class="text-white text-sm font-medium w-full">
                Supervisado y regulado por la Superintendencia de Banca, Seguros y AFP del Perú (SBS). Tu dinero y datos están protegidos.
            </p>
        </div>

        <!-- Carrusel móvil -->
        <div class="flex sm:hidden items-center justify-center flex-1 px-4 py-4">
            <span id="sbs-carousel" class="text-white text-xl font-bold tracking-wide text-center" style="transition: opacity 0.5s;">Supervisado</span>
        </div>
    </div>
</section>

<script>
    const sbsWords = ['Supervisado', 'Regulado', 'Seguridad'];
    let sbsIndex = 0;
    const sbsEl = document.getElementById('sbs-carousel');
    if (sbsEl) {
        setInterval(() => {
            sbsEl.style.opacity = '0';
            setTimeout(() => {
                sbsIndex = (sbsIndex + 1) % sbsWords.length;
                sbsEl.textContent = sbsWords[sbsIndex];
                sbsEl.style.opacity = '1';
            }, 600);
        }, 3000);
    }
</script>

<!-- Sección de características destacadas -->
<section class="py-16 sm:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <h2 class="text-3xl sm:text-4xl font-extrabold text-center text-gray-900 mb-12">¿Por qué PrestaKash?</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">
            <!-- Característica 1 -->
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 flex items-center justify-center">
                        <svg class="w-8 h-8" fill="#ec4899" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                            <path d="m473.6,256.6c0-99.3-67.1-185.9-163.3-210.6-9.2-2.4-18.6,3.2-20.9,12.4-2.4,9.2 3.2,18.6 12.4,20.9 80.9,20.7 137.4,93.7 137.4,177.3 0,73.7-43.9,139-109.6,167.7l15.9-37c3.7-8.7-0.3-18.8-9-22.6-8.7-3.7-18.8,0.3-22.6,9l-31,72.2c-3.7,8.7 0.3,18.8 9,22.6l72.2,31c10.8,3.8 19.8-2.5 22.6-9 3.7-8.7-0.3-18.8-9-22.6l-31.4-13.5c76.5-34.6 127.3-111.4 127.3-197.8z"/>
                            <path d="m210.4,433.9c-80.9-20.8-137.4-93.7-137.4-177.3 0-72.7 42.7-137.2 106.9-166.5l-14.9,34.6c-3.7,8.7 0.1,19.2 9,22.6 10.4,3.9 19.2-0.8 22.6-9l31-72.3c3-7 2.9-17.5-9-22.6l-72.2-31c-8.7-3.7-18.8,0.3-22.6,9-3.7,8.7 0.3,18.8 9,22.6l33.6,14.4c-76.8,34.7-127.8,111.6-127.8,198.2 7.10543e-15,99.3 67.2,185.8 163.3,210.6 11.6,3 18.9-4.6 20.9-12.4 2.3-9.2-3.2-18.5-12.4-20.9z"/>
                            <path d="m296.6,321.4c8.8-9.2 13.2-20.8 13.2-34.6 0-6.3-1.1-12.1-3.2-17.5-2.1-5.4-5.1-10-9-14-3.9-3.9-9-7.2-15.2-10-3.3-1.4-9.9-3.4-20-5.9v-57.4c6.5,1.3 11.6,4.1 15.4,8.3 3.7,4.2 6.2,10.2 7.4,18l20.6-3.1c-1.8-12.6-7-22.6-15.7-29.8-6.8-5.7-16-9.1-27.6-10.2v-9.5h-11.8v9.5c-13.2,1.3-23.2,5-29.9,10.9-10.1,8.8-15.1,20.3-15.1,34.5 0,8 1.7,15.1 5.2,21.4 3.4,6.3 8.3,11.2 14.7,14.7 8.6,4.8 17,8 25.2,9.5v63.5c-7.8-0.8-14.6-4.8-20.2-11.9-3.9-5-6.7-12.9-8.2-23.6l-20.1,3.8c0.7,10.3 3.4,19.1 7.9,26.3 4.5,7.2 9.9,12.4 16.2,15.6 6.2,3.2 14.4,5.4 24.3,6.7v19.9h11.8v-20.2c14-0.7 25.4-5.7 34.1-14.9zm-45.8-84.5c-9.1-2.7-15.5-6.4-19.3-11-3.8-4.6-5.7-10.3-5.7-17.1 0-6.9 2.2-12.8 6.7-17.8 4.5-5 10.6-8.1 18.3-9.3v55.2zm30.9,72.6c-5.1,5.8-11.5,9.1-19.2,10.1v-60.7c10.7,3.5 17.9,7.4 21.5,11.8 3.6,4.4 5.4,10.2 5.4,17.5-0.1,8.4-2.6,15.6-7.7,21.3z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Tasa fija 20%</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Sin sorpresas. Sabes exactamente cuánto pagarás desde el inicio.</p>
                </div>
            </div>

            <!-- Característica 2 -->
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 flex items-center justify-center">
                        <svg fill="#ec4899" width="32px" height="32px" viewBox="-2 -2 24 24" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMinYMin" class="jam jam-clock">
                            <path d='M11 9h4a1 1 0 0 1 0 2h-5a1 1 0 0 1-1-1V4a1 1 0 1 1 2 0v5zm-1 11C4.477 20 0 15.523 0 10S4.477 0 10 0s10 4.477 10 10-4.477 10-10 10zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16z'/>
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Menos de 24 horas</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">100% digitales. Sin muchos papeles para aprobar tu préstamo online.</p>
                </div>
            </div>

            <!-- Característica 3 -->
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 flex items-center justify-center">
                        <svg width="32px" height="32px" viewBox="0 0 1800 1800" xmlns="http://www.w3.org/2000/svg">
                            <g>
                                <g>
                                    <path fill="#ec4899" d="M872.831,1326.446c-1.999,0.113-3.853-0.183-5.733-0.53c-6.064-1.076-11.863-3.945-16.53-8.611l-514.859-514.86c-5.852-5.855-9.138-13.792-9.138-22.068c0-8.275,3.287-16.212,9.142-22.067L455.5,638.535c12.189-12.185,31.954-12.185,44.126,0l373.109,373.1l727.884-727.89c12.189-12.185,31.949-12.185,44.131,0l119.788,119.784c5.855,5.851,9.142,13.792,9.142,22.067s-3.286,16.212-9.137,22.063l-869.649,869.645C889.043,1323.155,881.106,1326.446,872.831,1326.446z M401.905,780.377l470.825,470.83l825.614-825.61l-75.657-75.653l-727.885,727.889c-12.19,12.186-31.95,12.186-44.13,0L477.563,704.729L401.905,780.377z"/>
                                </g>
                                <g>
                                    <g>
                                        <path fill="#ec4899" d="M1692.376,662.67c22.568,75.209,34.758,154.872,34.758,237.334c0,456.08-371.054,827.129-827.138,827.129c-456.08,0-827.129-371.05-827.129-827.129c0-456.084,371.049-827.138,827.129-827.138c213.186,0,407.787,81.086,554.605,214.022l44.161-44.165C1340.604,98.513,1130.391,10.456,899.996,10.456c-490.494,0-889.54,399.051-889.54,889.548s399.046,889.54,889.54,889.54c490.498,0,889.548-399.042,889.548-889.54c0-100.372-16.734-196.909-47.517-286.985L1692.376,662.67z"/>
                                    </g>
                                </g>
                            </g>
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">¿Por qué elegirnos?</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Nos preocupamos por ti y te ofrecemos el préstamo perfecto para tus necesidades.</p>
                </div>
            </div>

            <!-- Característica 4 -->
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 flex items-center justify-center">
                        <svg fill="#ec4899" width="32px" height="32px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19,8H14.724l3.144-5.5A1,1,0,0,0,17,1H10a1,1,0,0,0-.895.553l-5,10A1,1,0,0,0,5,13H9.656L7.042,21.713a1,1,0,0,0,1.722.933l11-13A1,1,0,0,0,19,8Zm-8.663,9.689,1.621-5.4A1,1,0,0,0,11,11H6.618l4-8h4.658L12.132,8.5A1,1,0,0,0,13,10h3.844Z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Respuesta inmediata</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">El mismo día sabrás si tu préstamo fue aprobado o no.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sección de requisitos -->
<section class="py-16 sm:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <h2 class="text-3xl sm:text-4xl font-extrabold text-center text-gray-900 mb-12">
            ¿Qué requisitos debes cumplir?
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">
            <!-- Requisito 1: Cuenta bancaria -->
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 flex items-center justify-center">
                        <svg fill="#ec4899" width="32px" height="32px" viewBox="0 0 36 36" version="1.1" preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3.5,13.56,5,12.68V16H31V12.71l1.48.85a1,1,0,0,0,1-1.73L18,2.92,2.5,11.83a1,1,0,1,0,1,1.73ZM17.85,7.11a.8.8,0,0,1,.8,0L25.37,11H22.15l-3.9-2.24L14.35,11H11.14Z"/>
                            <path d="M32.85,27H32v-.85A1.15,1.15,0,0,0,30.85,25H28V17.63H24V25H20V17.63H16V25H12V17.63H8V25H5.15A1.15,1.15,0,0,0,4,26.15V27H3.15A1.15,1.15,0,0,0,2,28.15V31H34V28.15A1.15,1.15,0,0,0,32.85,27Z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Tener cuenta bancaria</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Necesitas una cuenta bancaria activa para recibir el préstamo.</p>
                </div>
            </div>

            <!-- Requisito 2: Más de 21 años -->
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 flex items-center justify-center">
                        <svg fill="#ec4899" width="32px" height="32px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12,2C6.48,2,2,6.48,2,12s4.48,10,10,10s10-4.48,10-10S17.52,2,12,2z M12,20c-4.41,0-8-3.59-8-8s3.59-8,8-8s8,3.59,8,8 S16.41,20,12,20z M12.5,7H11v6l5.25,3.15l0.75-1.23l-4.5-2.67V7z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Tener más de 21 años</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Debes ser mayor de edad y tener al menos 21 años cumplidos.</p>
                </div>
            </div>

            <!-- Requisito 3: Ser peruano y tener DNI -->
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 flex items-center justify-center">
                        <svg fill="#ec4899" width="32px" height="32px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20,4H4C2.9,4,2,4.9,2,6v12c0,1.1,0.9,2,2,2h16c1.1,0,2-0.9,2-2V6C22,4.9,21.1,4,20,4z M20,18H4V6h16V18z M6,10h4v2H6V10z M6,14h8v2H6V14z M14,10h4v4h-4V10z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Ser peruano y tener DNI</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Debes contar con tu Documento Nacional de Identidad vigente.</p>
                </div>
            </div>

            <!-- Requisito 4: Correo electrónico y celular -->
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 flex items-center justify-center">
                        <svg fill="#ec4899" width="32px" height="32px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20,4H4C2.9,4,2.01,4.9,2.01,6L2,18c0,1.1,0.9,2,2,2h16c1.1,0,2-0.9,2-2V6C22,4.9,21.1,4,20,4z M20,8l-8,5L4,8V6l8,5l8-5V8z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Tener correo electrónico y celular</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Necesitamos tu correo y número de celular para comunicarnos contigo.</p>
                </div>
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
                    ? 'bg-gradient-to-r from-green-500 to-green-600 text-white shadow-md'
                    : 'bg-white text-gray-700 border border-gray-200 hover:border-green-400'
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
            btnCuotas.className = 'bg-gradient-to-r from-green-500 to-green-600 text-white font-bold py-1.5 px-2 text-[10px] sm:text-xs rounded-lg shadow-md transition-all';
            btnUnico.className = 'bg-gray-100 text-gray-700 font-bold py-1.5 px-2 text-[10px] sm:text-xs rounded-lg hover:bg-gray-200 transition-all';
        } else {
            btnUnico.className = 'bg-gradient-to-r from-green-500 to-green-600 text-white font-bold py-1.5 px-2 text-[10px] sm:text-xs rounded-lg shadow-md transition-all';
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
