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
        .upload-zone {
            border-bottom: 1px solid #374151;
            padding: 20px 0;
            cursor: pointer;
            transition: border-color 0.2s;
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .upload-zone:hover { border-bottom-color: #10b981; }
        .upload-zone.has-file { border-bottom-color: #10b981; }
        .upload-zone input[type=file] { display: none; }

        #cameraModal { display: none; }
        #cameraModal.active { display: flex; }
        #videoPreview { transform: scaleX(-1); }
        #capturedImage { transform: scaleX(-1); }
    </style>
</head>
<body class="bg-gray-950 min-h-screen flex items-center justify-center px-4 py-12" onload="noBack();" onpageshow="if(event.persisted) noBack();">
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

    <form action="{{ route('onboarding.step2.store') }}" method="POST" enctype="multipart/form-data" class="space-y-2" id="mainForm">
    @csrf

        <!-- Selfie -->
        <div>
            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Foto Selfie</label>
            <button type="button" onclick="openCamera()" class="upload-zone w-full text-left" id="zone-selfie">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-500" id="icon-selfie" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
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
            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">DNI — Parte frontal</label>
            <label class="upload-zone" id="zone-dni-front">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-500" id="icon-dni-front" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="text-sm text-white" id="text-dni-front">Seleccionar imagen</p>
                    <p class="text-xs text-gray-500 mt-0.5">JPG, PNG o WEBP — máx. 5MB</p>
                </div>
                <input type="file" name="dni_front" accept="image/*" onchange="handleFile(this,'zone-dni-front','text-dni-front','icon-dni-front')">
            </label>
        </div>

        <!-- DNI Trasera -->
        <div>
            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">DNI — Parte trasera</label>
            <label class="upload-zone" id="zone-dni-back">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-500" id="icon-dni-back" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="text-sm text-white" id="text-dni-back">Seleccionar imagen</p>
                    <p class="text-xs text-gray-500 mt-0.5">JPG, PNG o WEBP — máx. 5MB</p>
                </div>
                <input type="file" name="dni_back" accept="image/*" onchange="handleFile(this,'zone-dni-back','text-dni-back','icon-dni-back')">
            </label>
        </div>

        <!-- Recibo -->
        <div>
            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Recibo de luz o agua</label>
            <label class="upload-zone" id="zone-utility">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-500" id="icon-utility" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <p class="text-sm text-white" id="text-utility">Seleccionar imagen</p>
                    <p class="text-xs text-gray-500 mt-0.5">JPG, PNG o WEBP — máx. 5MB</p>
                </div>
                <input type="file" name="utility_bill" accept="image/*" onchange="handleFile(this,'zone-utility','text-utility','icon-utility')">
            </label>
        </div>

        <!-- Botones -->
        <div class="pt-8 flex items-center justify-between">
            <a href="{{ route('onboarding.step1') }}"
                class="flex items-center gap-2 text-red-500 hover:text-red-400 font-medium text-sm px-4 py-2 border-b border-red-500 hover:border-red-400 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Atrás
            </a>
            <button type="submit"
                class="flex items-center gap-2 text-white font-medium text-sm px-4 py-2 border-b border-green-500 hover:text-green-400 hover:border-green-400 transition-colors bg-transparent">
                Finalizar
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>

    </form>
</div>

<!-- Modal de cámara -->
<div id="cameraModal" class="fixed inset-0 bg-black bg-opacity-90 items-center justify-center z-50 p-4">
    <div class="bg-gray-900 rounded-2xl max-w-2xl w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-white">Tomar Selfie</h3>
            <button type="button" onclick="closeCamera()" class="text-gray-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="relative bg-black rounded-lg overflow-hidden mb-4" style="aspect-ratio: 4/3;">
            <video id="videoPreview" autoplay playsinline class="w-full h-full object-cover"></video>
            <canvas id="capturedImage" class="w-full h-full object-cover hidden"></canvas>
        </div>

        <div class="flex gap-3">
            <button type="button" id="captureBtn" onclick="capturePhoto()" class="flex-1 bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded-lg transition-colors">
                <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </button>
            <button type="button" id="retakeBtn" onclick="retakePhoto()" class="flex-1 bg-gray-700 hover:bg-gray-600 text-white font-bold py-3 rounded-lg transition-colors hidden">
                Repetir
            </button>
            <button type="button" id="confirmBtn" onclick="confirmPhoto()" class="flex-1 bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded-lg transition-colors hidden">
                Confirmar
            </button>
        </div>
    </div>
</div>

<script>
let stream = null;
let capturedBlob = null;

function handleFile(input, zoneId, textId, iconId) {
    if (!input.files || !input.files[0]) return;
    const name = input.files[0].name;
    document.getElementById(textId).textContent = name;
    document.getElementById(textId).classList.add('text-green-400');
    document.getElementById(zoneId).classList.add('has-file');
    document.getElementById(iconId).innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>';
    document.getElementById(iconId).classList.remove('text-gray-500');
    document.getElementById(iconId).classList.add('text-green-400');
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

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    ctx.drawImage(video, 0, 0);

    video.classList.add('hidden');
    canvas.classList.remove('hidden');

    document.getElementById('captureBtn').classList.add('hidden');
    document.getElementById('retakeBtn').classList.remove('hidden');
    document.getElementById('confirmBtn').classList.remove('hidden');
}

function retakePhoto() {
    const video = document.getElementById('videoPreview');
    const canvas = document.getElementById('capturedImage');

    video.classList.remove('hidden');
    canvas.classList.add('hidden');

    document.getElementById('captureBtn').classList.remove('hidden');
    document.getElementById('retakeBtn').classList.add('hidden');
    document.getElementById('confirmBtn').classList.add('hidden');
}

function confirmPhoto() {
    const canvas = document.getElementById('capturedImage');

    canvas.toBlob((blob) => {
        capturedBlob = blob;

        // Convertir a base64 para enviar en el formulario
        const reader = new FileReader();
        reader.onloadend = () => {
            document.getElementById('selfieInput').value = reader.result;

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

    video.classList.remove('hidden');
    canvas.classList.add('hidden');

    document.getElementById('captureBtn').classList.remove('hidden');
    document.getElementById('retakeBtn').classList.add('hidden');
    document.getElementById('confirmBtn').classList.add('hidden');
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
</body>
</html>
