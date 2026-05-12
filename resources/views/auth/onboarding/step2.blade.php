<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PrestaKash — Sube tus documentos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }

        .upload-zone {
            border-bottom: 1px solid #374151;
            padding: 20px 0;
            cursor: pointer;
            transition: border-color 0.2s;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .upload-zone:hover {
            border-bottom-color: #10b981;
        }

        .upload-zone.has-file {
            border-bottom-color: #10b981;
        }

        .upload-zone input[type=file] {
            display: none;
        }

        #cameraModal {
            display: none;
        }

        #cameraModal.active {
            display: flex;
        }

        #videoPreview {
            transform: scaleX(-1);
        }

        #capturedImage {
            transform: scaleX(-1);
        }
    </style>
</head>

<body class="bg-gray-950 min-h-screen flex items-center justify-center px-4 py-12" onload="noBack();"
    onpageshow="if(event.persisted) noBack();">
    <div class="w-full max-w-xl">

        <!-- Header -->
        <div class="mb-10">
            <p class="text-xs font-semibold text-green-400 uppercase tracking-widest mb-1">PrestaKash</p>
            <h1 class="text-2xl font-bold text-white">Verifica tu identidad</h1>
            <p class="text-gray-500 text-sm mt-1">Paso 2 de 2 — Documentos</p>

            <!-- Barra de progreso -->
            <div class="mt-4 h-0.5 bg-gray-800 rounded-full">
                <div class="h-0.5 bg-green-500 rounded-full w-full"></div>
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
            class="space-y-2" id="mainForm">
            @csrf

            <!-- Selfie -->
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Foto Selfie</label>

                <!-- Preview de selfie -->
                <div id="preview-selfie" class="hidden mb-2">
                    <div class="relative rounded-lg overflow-hidden bg-gray-900" style="max-width: 200px;">
                        <img id="img-selfie" class="w-full h-auto" style="transform: scaleX(-1);">
                        <button type="button" onclick="changeSelfie()"
                            class="absolute bottom-2 right-2 bg-gray-800/90 hover:bg-gray-700 text-white text-xs px-3 py-1.5 rounded-md transition-colors">
                            Cambiar
                        </button>
                    </div>
                </div>

                <button type="button" onclick="openCamera()" class="upload-zone w-full text-left" id="zone-selfie">
                    <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-500" id="icon-selfie" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-white" id="text-selfie">Tomar foto con cámara</p>
                        <p class="text-xs text-gray-500 mt-0.5">Captura tu rostro de frente</p>
                    </div>
                </button>
                <input type="hidden" name="selfie" id="selfieInput">
            </div>

            <!-- DNI Frontal -->
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">DNI — Parte
                    frontal</label>
                <input type="file" name="dni_front" id="input-dni-front" accept="image/*" class="hidden"
                    onchange="handleFile(this,'front')">

                <div id="preview-dni-front" class="hidden mb-2">
                    <div class="relative rounded-lg overflow-hidden bg-gray-900" style="max-width: 200px;">
                        <img id="img-dni-front" class="w-full h-auto">
                        <button type="button" onclick="resetDni('front')"
                            class="absolute bottom-2 right-2 bg-gray-800/90 hover:bg-gray-700 text-white text-xs px-3 py-1.5 rounded-md transition-colors">
                            Cambiar
                        </button>
                    </div>
                </div>

                <div id="method-dni-front" class="flex gap-3">
                    <button type="button" onclick="document.getElementById('input-dni-front').click()"
                        class="upload-zone flex-1 text-left">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-white">Subir imagen</p>
                            <p class="text-xs text-gray-500 mt-0.5">JPG, PNG o WEBP</p>
                        </div>
                    </button>
                    <button type="button" onclick="openDniCamera('front')" class="upload-zone flex-1 text-left">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-white">Usar cámara</p>
                            <p class="text-xs text-gray-500 mt-0.5">Tomar foto ahora</p>
                        </div>
                    </button>
                </div>
            </div>

            <!-- DNI Trasera -->
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">DNI — Parte
                    trasera</label>
                <input type="file" name="dni_back" id="input-dni-back" accept="image/*" class="hidden"
                    onchange="handleFile(this,'back')">

                <div id="preview-dni-back" class="hidden mb-2">
                    <div class="relative rounded-lg overflow-hidden bg-gray-900" style="max-width: 200px;">
                        <img id="img-dni-back" class="w-full h-auto">
                        <button type="button" onclick="resetDni('back')"
                            class="absolute bottom-2 right-2 bg-gray-800/90 hover:bg-gray-700 text-white text-xs px-3 py-1.5 rounded-md transition-colors">
                            Cambiar
                        </button>
                    </div>
                </div>

                <div id="method-dni-back" class="flex gap-3">
                    <button type="button" onclick="document.getElementById('input-dni-back').click()"
                        class="upload-zone flex-1 text-left">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-white">Subir imagen</p>
                            <p class="text-xs text-gray-500 mt-0.5">JPG, PNG o WEBP</p>
                        </div>
                    </button>
                    <button type="button" onclick="openDniCamera('back')" class="upload-zone flex-1 text-left">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-white">Usar cámara</p>
                            <p class="text-xs text-gray-500 mt-0.5">Tomar foto ahora</p>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Recibo -->
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Recibo de luz o
                    agua</label>
                <input type="file" name="utility_bill" id="input-utility" accept="image/*" class="hidden"
                    onchange="handleUtility(this)">

                <div id="preview-utility" class="hidden mb-2">
                    <div class="relative rounded-lg overflow-hidden bg-gray-900" style="max-width: 200px;">
                        <img id="img-utility" class="w-full h-auto">
                        <button type="button" onclick="resetUtility()"
                            class="absolute bottom-2 right-2 bg-gray-800/90 hover:bg-gray-700 text-white text-xs px-3 py-1.5 rounded-md transition-colors">
                            Cambiar
                        </button>
                    </div>
                </div>

                <button type="button" onclick="document.getElementById('input-utility').click()"
                    class="upload-zone w-full text-left" id="zone-utility">
                    <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-white">Seleccionar imagen</p>
                        <p class="text-xs text-gray-500 mt-0.5">JPG, PNG o WEBP — máx. 5MB</p>
                    </div>
                </button>
            </div>

            <!-- Botones -->
            <div class="pt-8 flex items-center justify-between">
                <a href="{{ route('onboarding.step1') }}"
                    class="flex items-center gap-2 text-red-500 hover:text-red-400 font-medium text-sm px-4 py-2 border-b border-red-500 hover:border-red-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Atrás
                </a>
                <button type="submit"
                    class="flex items-center gap-2 text-white font-medium text-sm px-4 py-2 border-b border-green-500 hover:text-green-400 hover:border-green-400 transition-colors bg-transparent">
                    Finalizar
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

        </form>
    </div>

    <!-- Modal cámara DNI -->
    <div id="dniCameraModal"
        class="fixed inset-0 bg-gray-950/95 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="max-w-sm w-full">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-lg font-bold text-white" id="dniCameraTitle">DNI — Frontal</h3>
                    <p class="text-xs text-gray-500">Encuadra bien el documento</p>
                </div>
                <button type="button" onclick="closeDniCamera()"
                    class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-800/50 hover:bg-gray-700 text-gray-400 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="relative bg-black rounded-xl overflow-hidden mb-4" style="aspect-ratio: 4/3;">
                <video id="dniVideoPreview" autoplay playsinline class="w-full h-full object-cover"></video>
                <canvas id="dniCapturedImage" class="w-full h-full object-cover hidden"></canvas>
                <!-- Guía rectangular para DNI -->
                <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
                    <div class="border-2 border-green-500/60 rounded-lg" style="width:85%;height:75%;"></div>
                </div>
            </div>

            <div class="space-y-2">
                <button type="button" id="dniCaptureBtn" onclick="captureDniPhoto()"
                    class="w-full bg-green-500 hover:bg-green-600 text-white font-medium py-3 rounded-lg transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="3" stroke-width="2" />
                    </svg>
                    Capturar
                </button>
                <div id="dniActionButtons" class="hidden grid grid-cols-2 gap-2">
                    <button type="button" onclick="retakeDniPhoto()"
                        class="bg-gray-800 hover:bg-gray-700 text-white font-medium py-3 rounded-lg transition-colors text-sm">Repetir</button>
                    <button type="button" onclick="confirmDniPhoto()"
                        class="bg-green-500 hover:bg-green-600 text-white font-medium py-3 rounded-lg transition-colors text-sm">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de cámara -->
    <div id="cameraModal" class="fixed inset-0 bg-gray-950/95 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div class="max-w-sm w-full">
            <!-- Header minimalista -->
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-lg font-bold text-white">Selfie</h3>
                    <p class="text-xs text-gray-500">Centra tu rostro</p>
                </div>
                <button type="button" onclick="closeCamera()"
                    class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-800/50 hover:bg-gray-700 text-gray-400 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Preview de cámara compacto -->
            <div class="relative bg-black rounded-xl overflow-hidden mb-4" style="aspect-ratio: 3/4;">
                <video id="videoPreview" autoplay playsinline class="w-full h-full object-cover"></video>
                <canvas id="capturedImage" class="w-full h-full object-cover hidden"></canvas>

                <!-- Overlay guía circular minimalista -->
                <div class="absolute inset-0 pointer-events-none" id="faceGuide">
                    <div
                        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-48 h-60 rounded-full border-2 border-green-500/60">
                    </div>
                </div>
            </div>

            <!-- Botones compactos -->
            <div class="space-y-2">
                <!-- Botón capturar -->
                <button type="button" id="captureBtn" onclick="capturePhoto()"
                    class="w-full bg-green-500 hover:bg-green-600 text-white font-medium py-3 rounded-lg transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="3" stroke-width="2" />
                    </svg>
                    Capturar
                </button>

                <!-- Botones repetir y confirmar -->
                <div id="actionButtons" class="hidden grid grid-cols-2 gap-2">
                    <button type="button" onclick="retakePhoto()"
                        class="bg-gray-800 hover:bg-gray-700 text-white font-medium py-3 rounded-lg transition-colors text-sm">
                        Repetir
                    </button>
                    <button type="button" onclick="confirmPhoto()"
                        class="bg-green-500 hover:bg-green-600 text-white font-medium py-3 rounded-lg transition-colors text-sm">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let stream = null;
        let capturedBlob = null;
        let dniStream = null;
        let currentDniSide = null; // 'front' | 'back'

        function handleFile(input, side) {
            if (!input.files || !input.files[0]) return;
            const file = input.files[0];
            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById('img-dni-' + side).src = e.target.result;
                document.getElementById('preview-dni-' + side).classList.remove('hidden');
                document.getElementById('method-dni-' + side).classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }

        function resetDni(side) {
            document.getElementById('preview-dni-' + side).classList.add('hidden');
            document.getElementById('method-dni-' + side).classList.remove('hidden');
            document.getElementById('input-dni-' + side).value = '';
        }

        function handleUtility(input) {
            if (!input.files || !input.files[0]) return;
            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById('img-utility').src = e.target.result;
                document.getElementById('preview-utility').classList.remove('hidden');
                document.getElementById('zone-utility').classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }

        function resetUtility() {
            document.getElementById('preview-utility').classList.add('hidden');
            document.getElementById('zone-utility').classList.remove('hidden');
            document.getElementById('input-utility').value = '';
        }

        // --- Cámara DNI ---
        async function openDniCamera(side) {
            currentDniSide = side;
            document.getElementById('dniCameraTitle').textContent = side === 'front' ? 'DNI — Frontal' : 'DNI — Trasera';
            const modal = document.getElementById('dniCameraModal');
            const video = document.getElementById('dniVideoPreview');
            try {
                dniStream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: { ideal: 'environment' }, width: 1280, height: 960 },
                    audio: false
                });
                video.srcObject = dniStream;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            } catch (err) {
                alert('No se pudo acceder a la cámara.');
            }
        }

        function closeDniCamera() {
            if (dniStream) { dniStream.getTracks().forEach(t => t.stop()); dniStream = null; }
            document.getElementById('dniCameraModal').classList.add('hidden');
            document.getElementById('dniCameraModal').classList.remove('flex');
            document.getElementById('dniVideoPreview').classList.remove('hidden');
            document.getElementById('dniCapturedImage').classList.add('hidden');
            document.getElementById('dniCaptureBtn').classList.remove('hidden');
            document.getElementById('dniActionButtons').classList.add('hidden');
        }

        function captureDniPhoto() {
            const video = document.getElementById('dniVideoPreview');
            const canvas = document.getElementById('dniCapturedImage');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            video.classList.add('hidden');
            canvas.classList.remove('hidden');
            document.getElementById('dniCaptureBtn').classList.add('hidden');
            document.getElementById('dniActionButtons').classList.remove('hidden');
        }

        function retakeDniPhoto() {
            document.getElementById('dniVideoPreview').classList.remove('hidden');
            document.getElementById('dniCapturedImage').classList.add('hidden');
            document.getElementById('dniCaptureBtn').classList.remove('hidden');
            document.getElementById('dniActionButtons').classList.add('hidden');
        }

        function confirmDniPhoto() {
            const canvas = document.getElementById('dniCapturedImage');
            canvas.toBlob((blob) => {
                const file = new File([blob], 'dni-' + currentDniSide + '.jpg', { type: 'image/jpeg' });
                const dt = new DataTransfer();
                dt.items.add(file);
                const input = document.getElementById('input-dni-' + currentDniSide);
                input.files = dt.files;

                const reader = new FileReader();
                reader.onload = (e) => {
                    document.getElementById('img-dni-' + currentDniSide).src = e.target.result;
                    document.getElementById('preview-dni-' + currentDniSide).classList.remove('hidden');
                    document.getElementById('method-dni-' + currentDniSide).classList.add('hidden');
                };
                reader.readAsDataURL(blob);
                closeDniCamera();
            }, 'image/jpeg', 0.9);
        }

        function changeSelfie() {
            document.getElementById('preview-selfie').classList.add('hidden');
            document.getElementById('zone-selfie').classList.remove('hidden');
            document.getElementById('selfieInput').value = '';

            // Resetear UI
            document.getElementById('text-selfie').textContent = 'Tomar foto con cámara';
            document.getElementById('text-selfie').classList.remove('text-green-400');
            document.getElementById('zone-selfie').classList.remove('has-file');
            const iconSelfie = document.getElementById('icon-selfie');
            iconSelfie.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>';
            iconSelfie.classList.remove('text-green-400');
            iconSelfie.classList.add('text-gray-500');
        }

        async function openCamera() {
            const modal = document.getElementById('cameraModal');
            const video = document.getElementById('videoPreview');

            try {
                stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'user', width: 1280, height: 960 },
                    audio: false
                });
                video.srcObject = stream;
                modal.classList.add('active');
            } catch (err) {
                alert('No se pudo acceder a la cámara. Por favor, verifica los permisos.');
                console.error('Error al acceder a la cámara:', err);
            }
        }

        function closeCamera() {
            const modal = document.getElementById('cameraModal');
            const video = document.getElementById('videoPreview');

            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }

            video.srcObject = null;
            modal.classList.remove('active');
            resetCameraUI();
        }

        function capturePhoto() {
            const video = document.getElementById('videoPreview');
            const canvas = document.getElementById('capturedImage');
            const ctx = canvas.getContext('2d');
            const faceGuide = document.getElementById('faceGuide');

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            ctx.drawImage(video, 0, 0);

            video.classList.add('hidden');
            canvas.classList.remove('hidden');
            faceGuide.classList.add('hidden');

            document.getElementById('captureBtn').classList.add('hidden');
            document.getElementById('actionButtons').classList.remove('hidden');
        }

        function retakePhoto() {
            const video = document.getElementById('videoPreview');
            const canvas = document.getElementById('capturedImage');
            const faceGuide = document.getElementById('faceGuide');

            video.classList.remove('hidden');
            canvas.classList.add('hidden');
            faceGuide.classList.remove('hidden');

            document.getElementById('captureBtn').classList.remove('hidden');
            document.getElementById('actionButtons').classList.add('hidden');
        }

        function confirmPhoto() {
            const canvas = document.getElementById('capturedImage');

            canvas.toBlob((blob) => {
                capturedBlob = blob;

                // Convertir a base64 para enviar en el formulario
                const reader = new FileReader();
                reader.onloadend = () => {
                    document.getElementById('selfieInput').value = reader.result;

                    // Mostrar preview
                    document.getElementById('img-selfie').src = reader.result;
                    document.getElementById('preview-selfie').classList.remove('hidden');
                    document.getElementById('zone-selfie').classList.add('hidden');

                    // Actualizar UI
                    document.getElementById('text-selfie').textContent = 'Selfie capturada';
                    document.getElementById('text-selfie').classList.add('text-green-400');
                    document.getElementById('zone-selfie').classList.add('has-file');
                    const iconSelfie = document.getElementById('icon-selfie');
                    iconSelfie.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>';
                    iconSelfie.classList.remove('text-gray-500');
                    iconSelfie.classList.add('text-green-400');

                    closeCamera();
                };
                reader.readAsDataURL(blob);
            }, 'image/jpeg', 0.9);
        }

        function resetCameraUI() {
            const video = document.getElementById('videoPreview');
            const canvas = document.getElementById('capturedImage');
            const faceGuide = document.getElementById('faceGuide');

            video.classList.remove('hidden');
            canvas.classList.add('hidden');
            faceGuide.classList.remove('hidden');

            document.getElementById('captureBtn').classList.remove('hidden');
            document.getElementById('actionButtons').classList.add('hidden');
        }

        // Bloquear botón atrás del navegador
        function noBack() { window.history.forward(); }
        for (let i = 0; i < 20; i++) {
            history.pushState(null, '', location.href);
        }
        window.addEventListener('popstate', () => {
            history.pushState(null, '', location.href);
        });
    </script>

    @include('partials.global-scripts')

</body>

</html>