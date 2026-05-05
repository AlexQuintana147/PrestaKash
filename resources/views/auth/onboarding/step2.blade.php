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
    </style>
</head>
<body class="bg-gray-950 min-h-screen flex items-center justify-center px-4 py-12">
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

    <form action="{{ route('onboarding.step2.store') }}" method="POST" enctype="multipart/form-data" class="space-y-2">
    @csrf

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

<script>
function handleFile(input, zoneId, textId, iconId) {
    if (!input.files || !input.files[0]) return;
    const name = input.files[0].name;
    document.getElementById(textId).textContent = name;
    document.getElementById(textId).classList.add('text-green-400');
    document.getElementById(zoneId).classList.add('has-file');
    // Cambiar icono a check
    document.getElementById(iconId).innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>';
    document.getElementById(iconId).classList.remove('text-gray-500');
    document.getElementById(iconId).classList.add('text-green-400');
}

// Bloquear botón atrás del navegador
history.pushState(null, '', location.href);
window.addEventListener('popstate', () => {
    window.location.replace('{{ route('onboarding.step2') }}');
});
</script>
</body>
</html>
