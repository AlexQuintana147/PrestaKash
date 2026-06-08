<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PrestaKash — Sube tus documentos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; }
        .doc-card {
            background: #111827;
            border: 1px solid #1f2937;
            transition: all 0.2s;
        }
        .doc-card:hover { border-color: #374151; }
        .doc-card.captured { border-color: #10b981; background: linear-gradient(135deg, #064e3b 0%, #111827 100%); }
        .doc-card.captured:hover { border-color: #059669; }
        #cameraModal { display: none; }
        #cameraModal.active { display: flex !important; }
        @keyframes pulse-check { 0%,100%{transform:scale(1)}50%{transform:scale(1.15)} }
        .check-pulse { animation: pulse-check 0.4s ease-out; }
    </style>
</head>

<body class="bg-gray-950 min-h-screen flex items-center justify-center px-4 py-8" onload="noBack();"
    onpageshow="if(event.persisted) noBack();">
    <div class="w-full max-w-lg">

        <!-- Header -->
        <div class="mb-8">
            <p class="text-xs font-semibold text-green-400 uppercase tracking-widest mb-1">PrestaKash</p>
            <h1 class="text-2xl font-bold text-white">Verifica tu identidad</h1>
            <p class="text-gray-500 text-sm mt-1">Paso 2 de 2 — Documentos</p>

            <!-- Barra de progreso -->
            <div class="mt-5">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-medium text-gray-400">Progreso de Documentos</span>
                    <span id="progressText" class="text-[11px] font-bold text-green-400 tabular-nums">0% (0 de 4)</span>
                </div>
                <div class="h-2 bg-gray-800 rounded-full overflow-hidden">
                    <div id="progressBar" class="h-full bg-gradient-to-r from-emerald-500 to-green-400 rounded-full transition-all duration-500 ease-out" style="width: 0%"></div>
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 border-l-2 border-red-500 bg-red-500/5 rounded-r-lg">
                @foreach($errors->all() as $error)
                    <p class="text-red-400 text-sm">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('onboarding.step2.store') }}" method="POST" enctype="multipart/form-data"
            class="space-y-4" id="mainForm">
            @csrf

            <!-- ══ Selfie Card ══ -->
            <div id="card-selfie" class="doc-card p-4 flex items-center gap-4">
                <div class="flex-shrink-0 w-12 h-12 bg-gray-800/80 border border-gray-700/50 flex items-center justify-center">
                    <svg id="icon-selfie" class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="text-sm font-semibold text-white">Foto Selfie</span>
                    </div>
                    <p class="text-[11px] text-gray-500 leading-relaxed">Captura tu rostro de frente con buena iluminación.</p>
                </div>
                <button type="button" onclick="openCamera()" id="btn-selfie"
                    class="flex-shrink-0 flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3" stroke-width="2"/></svg>
                    Capturar
                </button>
                <input type="hidden" name="selfie" id="selfieInput">
            </div>

            <!-- ══ DNI Frontal Card ══ -->
            <div id="card-dni-front" class="doc-card p-4 flex items-center gap-4">
                <div class="flex-shrink-0 w-12 h-12 bg-gray-800/80 border border-gray-700/50 flex items-center justify-center">
                    <svg id="icon-dni-front" class="w-6 h-6 text-gray-500" viewBox="0 0 32 32"><path fill="currentColor" d="M13.219,15.087l-0.049-0.024C13.681,14.524,14,13.801,14,13c0-1.657-1.343-3-3-3s-3,1.343-3,3c0,0.801,0.319,1.524,0.83,2.062l-0.049,0.024C7.668,15.829,7,17.078,7,18.415V21c0,0.552,0.448,1,1,1h6c0.552,0,1-0.448,1-1v-2.585C15,17.078,14.332,15.829,13.219,15.087z M11,11c1.103,0,2,0.897,2,2c0,1.103-0.897,2-2,2s-2-0.897-2-2C9,11.897,9.897,11,11,11z M14,21H8v-2.585c0-0.986,0.48-1.903,1.288-2.464l0.463-0.231C10.132,15.896,10.553,16,11,16s0.868-0.104,1.249-0.28l0.463,0.231C13.52,16.512,14,17.429,14,18.415V21z M28,5H4C2.343,5,1,6.343,1,8v16c0,1.657,1.343,3,3,3h24c1.657,0,3-1.343,3-3V8C31,6.343,29.657,5,28,5z M30,24c0,1.103-0.897,2-2,2H4c-1.103,0-2-0.897-2-2V8c0-1.103,0.897-2,2-2h24c1.103,0,2,0.897,2,2V24z M25,16.5c0,0.276-0.224,0.5-0.5,0.5h-6c-0.276,0-0.5-0.224-0.5-0.5s0.224-0.5,0.5-0.5h6C24.776,16,25,16.224,25,16.5z M25,18.5c0,0.276-0.224,0.5-0.5,0.5h-6c-0.276,0-0.5-0.224-0.5-0.5s0.224-0.5,0.5-0.5h6C24.776,18,25,16.224,25,18.5z M25,14.5c0,0.276-0.224,0.5-0.5,0.5h-6c-0.276,0-0.5-0.224-0.5-0.5s0.224-0.5,0.5-0.5h6C24.776,14,25,14.224,25,14.5z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="text-sm font-semibold text-white">DNI — Parte Frontal</span>
                    </div>
                    <p class="text-[11px] text-gray-500 leading-relaxed">Enfoca el lado frontal del documento.</p>
                </div>
                <button type="button" onclick="openDniCamera('front')" id="btn-dni-front"
                    class="flex-shrink-0 flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3" stroke-width="2"/></svg>
                    Capturar
                </button>
                <input type="hidden" name="dni_front" id="dniFrontInput">
            </div>

            <!-- ══ DNI Trasera Card ══ -->
            <div id="card-dni-back" class="doc-card p-4 flex items-center gap-4">
                <div class="flex-shrink-0 w-12 h-12 bg-gray-800/80 border border-gray-700/50 flex items-center justify-center">
                    <svg id="icon-dni-back" class="w-6 h-6 text-gray-500" viewBox="0 0 32 32"><path fill="currentColor" d="M13.219,15.087l-0.049-0.024C13.681,14.524,14,13.801,14,13c0-1.657-1.343-3-3-3s-3,1.343-3,3c0,0.801,0.319,1.524,0.83,2.062l-0.049,0.024C7.668,15.829,7,17.078,7,18.415V21c0,0.552,0.448,1,1,1h6c0.552,0,1-0.448,1-1v-2.585C15,17.078,14.332,15.829,13.219,15.087z M11,11c1.103,0,2,0.897,2,2c0,1.103-0.897,2-2,2s-2-0.897-2-2C9,11.897,9.897,11,11,11z M14,21H8v-2.585c0-0.986,0.48-1.903,1.288-2.464l0.463-0.231C10.132,15.896,10.553,16,11,16s0.868-0.104,1.249-0.28l0.463,0.231C13.52,16.512,14,17.429,14,18.415V21z M28,5H4C2.343,5,1,6.343,1,8v16c0,1.657,1.343,3,3,3h24c1.657,0,3-1.343,3-3V8C31,6.343,29.657,5,28,5z M30,24c0,1.103-0.897,2-2,2H4c-1.103,0-2-0.897-2-2V8c0-1.103,0.897-2,2-2h24c1.103,0,2,0.897,2,2V24z M25,16.5c0,0.276-0.224,0.5-0.5,0.5h-6c-0.276,0-0.5-0.224-0.5-0.5s0.224-0.5,0.5-0.5h6C24.776,16,25,16.224,25,16.5z M25,18.5c0,0.276-0.224,0.5-0.5,0.5h-6c-0.276,0-0.5-0.224-0.5-0.5s0.224-0.5,0.5-0.5h6C24.776,18,25,16.224,25,18.5z M25,14.5c0,0.276-0.224,0.5-0.5,0.5h-6c-0.276,0-0.5-0.224-0.5-0.5s0.224-0.5,0.5-0.5h6C24.776,14,25,14.224,25,14.5z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="text-sm font-semibold text-white">DNI — Parte Trasera</span>
                   </div>
                    <p class="text-[11px] text-gray-500 leading-relaxed">Encuadra el reverso donde figura tu chip/firma.</p>
                </div>
                <button type="button" onclick="openDniCamera('back')" id="btn-dni-back"
                    class="flex-shrink-0 flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3" stroke-width="2"/></svg>
                    Capturar
                </button>
                <input type="hidden" name="dni_back" id="dniBackInput">
            </div>

            <!-- ══ Recibo Card ══ -->
            <div id="card-utility" class="doc-card p-4 flex items-center gap-4">
                <div class="flex-shrink-0 w-12 h-12 bg-gray-800/80 border border-gray-700/50 flex items-center justify-center">
                    <svg id="icon-utility" class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="text-sm font-semibold text-white">Recibo de Luz o Agua</span>
                    </div>
                    <p class="text-[11px] text-gray-500 leading-relaxed">Recibo reciente de los últimos 3 meses.</p>
                </div>
                <button type="button" onclick="openUtilityCamera()" id="btn-utility"
                    class="flex-shrink-0 flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3" stroke-width="2"/></svg>
                    Capturar
                </button>
                <input type="hidden" name="utility_bill" id="utilityInput">
            </div>

            <!-- Botones navegación -->
            <div class="pt-6 pb-2 flex items-center justify-between">
                <a href="{{ route('onboarding.step1') }}"
                    class="flex items-center gap-2 text-gray-400 hover:text-white font-medium text-sm px-4 py-2.5 hover:bg-gray-900/50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Atrás
                </a>
                <button type="submit" id="submitBtn"
                    class="flex items-center gap-2 text-white font-medium text-sm px-4 py-2 border-b border-green-500 hover:text-green-400 hover:border-green-400 transition-colors bg-transparent">
                    Finalizar
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <!-- Modal cámara unificado -->
    <div id="cameraModal" class="fixed inset-0 bg-black hidden flex-col z-50">
        <div id="cameraInner" class="max-w-md w-full flex flex-col h-full md:h-auto md:max-h-[92vh] md:my-auto md:mx-auto md:rounded-2xl overflow-hidden bg-black md:bg-gray-950">
            <!-- Header -->
            <div class="flex items-center justify-between px-4 py-3 md:px-5 md:pt-5 md:pb-3 md:border-b md:border-gray-800/50">
                <div>
                    <h3 class="text-sm md:text-base font-bold text-white" id="cameraTitle">Selfie</h3>
                    <p class="text-[10px] md:text-[11px] text-gray-500" id="cameraHint">Centra tu rostro en el círculo</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" id="switchCameraBtn" onclick="switchCamera()" class="w-8 h-8 flex items-center justify-center bg-white/10 hover:bg-white/20 text-gray-300 hover:text-white transition-colors" title="Cambiar cámara">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                    <button type="button" onclick="closeCamera()"
                        class="w-8 h-8 flex items-center justify-center bg-white/10 hover:bg-white/20 text-gray-300 hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Preview cámara -->
            <div class="relative bg-black overflow-hidden flex-1" id="cameraPreviewContainer">
                <video id="videoPreview" autoplay playsinline class="w-full h-full object-cover"></video>
                <canvas id="capturedImage" class="w-full h-full object-cover hidden absolute inset-0"></canvas>

                <!-- Guía selfie (oval) -->
                <div id="guideOval" class="absolute inset-0 pointer-events-none flex items-center justify-center">
                    <div class="w-56 h-72 sm:w-64 sm:h-80 rounded-[40%] border-2 border-white/30 border-dashed"></div>
                </div>
                <!-- Guía DNI (rectángulo) -->
                <div id="guideRect" class="absolute inset-0 pointer-events-none hidden flex items-center justify-center">
                    <div class="border-2 border-white/30 border-dashed" style="width:85%;height:65%;"></div>
                </div>
            </div>

            <!-- Botones -->
            <div class="px-4 py-4 md:p-5 md:border-t md:border-gray-800/50 bg-black md:bg-gray-950">
                <button type="button" id="captureBtn" onclick="capturePhoto()"
                    class="w-full bg-white hover:bg-gray-200 text-black font-bold py-4 md:py-3.5 transition-all flex items-center justify-center gap-2 text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="3.5" stroke-width="2.5"/>
                    </svg>
                    Capturar
                </button>
                <div id="actionButtons" class="hidden grid grid-cols-2 gap-3">
                    <button type="button" onclick="retakePhoto()"
                        class="bg-white/10 hover:bg-white/20 text-white font-medium py-4 md:py-3 transition-colors text-sm">Repetir</button>
                    <button type="button" onclick="confirmPhoto()"
                        class="bg-white hover:bg-gray-200 text-black font-bold py-4 md:py-3 transition-colors text-sm">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let stream = null;
        let capturedBlob = null;
        let currentDoc = null; // 'selfie' | 'dni-front' | 'dni-back' | 'utility'
        let facingMode = 'user'; // 'user' (frontal) o 'environment' (trasera)
        const docStates = { selfie: false, 'dni-front': false, 'dni-back': false, utility: false };

        function updateProgress() {
            const done = Object.values(docStates).filter(Boolean).length;
            const pct = Math.round((done / 4) * 100);
            document.getElementById('progressBar').style.width = pct + '%';
            document.getElementById('progressText').textContent = pct + '% (' + done + ' de 4)';
        }

        function markCardCaptured(docKey) {
            docStates[docKey] = true;
            updateProgress();

            const card = document.getElementById('card-' + docKey);
            if (!card) return;

            card.classList.add('captured');

            const icon = card.querySelector('[id^="icon-"]');
            if (icon) {
                // Reemplazar todo el SVG con uno fill-compatible
                icon.outerHTML = '<svg id="' + icon.id + '" class="w-6 h-6 text-green-400 check-pulse" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1.93 14.18l-3.54-3.54 1.42-1.41 2.12 2.12 5.3-5.3 1.42 1.41-6.72 6.72z"/></svg>';
                setTimeout(function() {
                    var newIcon = document.getElementById(icon.id);
                    if (newIcon) newIcon.classList.remove('check-pulse');
                }, 400);
            }

            const btn = card.querySelector('[id^="btn-"]');
            if (btn) {
                btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg> Cambiar';
                btn.classList.remove('bg-emerald-600', 'hover:bg-emerald-500', 'active:bg-emerald-700');
                btn.classList.add('bg-gray-700', 'hover:bg-gray-600', 'active:bg-gray-800');
            }
        }

        function openCamera() {
            currentDoc = 'selfie';
            facingMode = 'user'; // Selfie usa cámara frontal por defecto
            document.getElementById('cameraTitle').textContent = 'Foto Selfie';
            document.getElementById('cameraHint').textContent = 'Centra tu rostro en el círculo';
            document.getElementById('guideOval').classList.remove('hidden');
            document.getElementById('guideRect').classList.add('hidden');
            setCameraLayout('portrait');
            startCamera();
        }

        function openDniCamera(side) {
            currentDoc = 'dni-' + side;
            facingMode = 'environment'; // DNI usa cámara trasera por defecto
            document.getElementById('cameraTitle').textContent = side === 'front' ? 'DNI — Parte Frontal' : 'DNI — Parte Trasera';
            document.getElementById('cameraHint').textContent = 'Encuadra bien todo el documento';
            document.getElementById('guideOval').classList.add('hidden');
            document.getElementById('guideRect').classList.remove('hidden');
            setCameraLayout('landscape');
            startCamera();
        }

        function openUtilityCamera() {
            currentDoc = 'utility';
            facingMode = 'environment'; // Recibo usa cámara trasera por defecto
            document.getElementById('cameraTitle').textContent = 'Recibo de Luz o Agua';
            document.getElementById('cameraHint').textContent = 'Enfoca el recibo completo';
            document.getElementById('guideOval').classList.add('hidden');
            document.getElementById('guideRect').classList.add('hidden');
            setCameraLayout('portrait');
            startCamera();
        }

        function setCameraLayout(mode) {
            var modal = document.getElementById('cameraModal');
            var inner = document.getElementById('cameraInner');
            var container = document.getElementById('cameraPreviewContainer');
            if (mode === 'portrait') {
                modal.classList.remove('justify-center');
                inner.classList.remove('max-w-lg', 'md:my-auto', 'md:h-auto', 'md:max-h-[92vh]');
                inner.classList.add('max-w-md', 'h-full');
                container.style.aspectRatio = '3/4';
                container.classList.add('flex-1');
            } else {
                modal.classList.add('justify-center');
                inner.classList.remove('max-w-md', 'h-full');
                inner.classList.add('max-w-lg', 'md:my-auto', 'md:h-auto', 'md:max-h-[92vh]');
                container.style.aspectRatio = '4/3';
                container.classList.remove('flex-1');
            }
        }

        function startCamera() {
            // Primero mostrar modal con estado de carga
            var modal = document.getElementById('cameraModal');
            modal.classList.add('active');

            var video = document.getElementById('videoPreview');
            var canvas = document.getElementById('capturedImage');

            canvas.classList.add('hidden');
            video.classList.remove('hidden');
            document.getElementById('captureBtn').classList.remove('hidden');
            document.getElementById('actionButtons').classList.add('hidden');

            // Detener stream anterior si existe
            stopStream();

            // Intentar cámara según facingMode, luego cualquier cámara
            navigator.mediaDevices.getUserMedia({ video: { facingMode: facingMode, width: { ideal: 1280 }, height: { ideal: 720 } } })
                .then(function(s) {
                    stream = s;
                    video.srcObject = s;
                    video.play();
                    updateMirrorState();
                })
                .catch(function() {
                    // Fallback: cualquier cámara disponible
                    navigator.mediaDevices.getUserMedia({ video: { width: { ideal: 1280 }, height: { ideal: 720 } } })
                        .then(function(s) {
                            stream = s;
                            video.srcObject = s;
                            video.play();
                            updateMirrorState();
                        })
                        .catch(function(err) {
                            alert('No se pudo acceder a la cámara. Verifica los permisos del navegador.');
                            closeCamera();
                        });
                });
        }

        function updateMirrorState() {
            var video = document.getElementById('videoPreview');
            var canvas = document.getElementById('capturedImage');
            // Espejar solo si es cámara frontal (selfie)
            if (facingMode === 'user') {
                video.style.transform = 'scaleX(-1)';
                canvas.style.transform = 'scaleX(-1)';
            } else {
                video.style.transform = 'scaleX(1)';
                canvas.style.transform = 'scaleX(1)';
            }
        }

        function switchCamera() {
            facingMode = facingMode === 'user' ? 'environment' : 'user';
            stopStream();
            var video = document.getElementById('videoPreview');
            var canvas = document.getElementById('capturedImage');

            canvas.classList.add('hidden');
            video.classList.remove('hidden');
            document.getElementById('captureBtn').classList.remove('hidden');
            document.getElementById('actionButtons').classList.add('hidden');

            navigator.mediaDevices.getUserMedia({ video: { facingMode: facingMode, width: { ideal: 1280 }, height: { ideal: 720 } } })
                .then(function(s) {
                    stream = s;
                    video.srcObject = s;
                    video.play();
                    updateMirrorState();
                })
                .catch(function() {
                    navigator.mediaDevices.getUserMedia({ video: { width: { ideal: 1280 }, height: { ideal: 720 } } })
                        .then(function(s) {
                            stream = s;
                            video.srcObject = s;
                            video.play();
                            updateMirrorState();
                        })
                        .catch(function() {
                            alert('No se pudo cambiar de cámara.');
                        });
                });
        }

        function closeCamera() {
            stopStream();
            var modal = document.getElementById('cameraModal');
            modal.classList.remove('active');
        }

        function stopStream() {
            if (stream) {
                stream.getTracks().forEach(function(t) { t.stop(); });
                stream = null;
            }
        }

        function capturePhoto() {
            var video = document.getElementById('videoPreview');
            var canvas = document.getElementById('capturedImage');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);

            video.classList.add('hidden');
            canvas.classList.remove('hidden');
            document.getElementById('captureBtn').classList.add('hidden');
            document.getElementById('actionButtons').classList.remove('hidden');
        }

        function retakePhoto() {
            var video = document.getElementById('videoPreview');
            var canvas = document.getElementById('capturedImage');
            video.classList.remove('hidden');
            canvas.classList.add('hidden');
            document.getElementById('captureBtn').classList.remove('hidden');
            document.getElementById('actionButtons').classList.add('hidden');
        }

        function confirmPhoto() {
            var canvas = document.getElementById('capturedImage');
            canvas.toBlob(function(blob) {
                if (!blob) return;

                var reader = new FileReader();
                reader.onloadend = function() {
                    var base64 = reader.result;

                    switch(currentDoc) {
                        case 'selfie':
                            document.getElementById('selfieInput').value = base64;
                            markCardCaptured('selfie');
                            break;
                        case 'dni-front':
                            document.getElementById('dniFrontInput').value = base64;
                            markCardCaptured('dni-front');
                            break;
                        case 'dni-back':
                            document.getElementById('dniBackInput').value = base64;
                            markCardCaptured('dni-back');
                            break;
                        case 'utility':
                            document.getElementById('utilityInput').value = base64;
                            markCardCaptured('utility');
                            break;
                    }

                    closeCamera();
                };
                reader.readAsDataURL(blob);
            }, 'image/jpeg', 0.85);
        }

        function noBack() { window.history.forward(); }
        window.addEventListener('popstate', function() { history.pushState(null, '', location.href); });

        // Loading overlay
        document.addEventListener('submit', function(e) {
            var form = e.target;
            if (form.tagName === 'FORM' && !form.dataset.noLoading) {
                var overlay = document.getElementById('pk-loading');
                if (overlay) {
                    overlay.classList.remove('opacity-0');
                    overlay.classList.add('opacity-100');
                    overlay.classList.add('pointer-events-auto');
                }
            }
        });
    </script>

    <!-- Subtle loading overlay -->
    <div id="pk-loading" class="fixed inset-0 z-[9999] pointer-events-none flex items-center justify-center opacity-0 transition-opacity duration-300">
        <div class="bg-gray-950/60 backdrop-blur-sm absolute inset-0"></div>
        <div class="relative flex flex-col items-center gap-3">
            <div class="w-8 h-8 border-2 border-gray-600 border-t-green-500 rounded-full animate-spin"></div>
            <p class="text-xs font-medium text-gray-400">Subiendo documentos...</p>
        </div>
    </div>

    @include('partials.global-scripts')

</body>
</html>