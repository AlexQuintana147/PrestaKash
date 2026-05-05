<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PrestaKash — Sube tus documentos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Roboto', sans-serif; } </style>
</head>
<body class="bg-gray-950 min-h-screen flex items-center justify-center px-4 py-10">

<div class="w-full max-w-md">

    <!-- Logo -->
    <div class="text-center mb-8">
        <span class="text-3xl font-extrabold text-white">PrestaKash</span>
        <p class="text-gray-400 text-sm mt-1">Paso 2 de 2</p>
    </div>

    <!-- Pasos -->
    <div class="flex items-center justify-center gap-3 mb-8">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-green-500/30 border border-green-500 flex items-center justify-center text-green-400 text-sm font-bold">✓</div>
            <span class="text-gray-400 text-sm">Datos personales</span>
        </div>
        <div class="w-8 h-px bg-gray-700"></div>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white text-sm font-bold">2</div>
            <span class="text-white text-sm font-medium">Documentos</span>
        </div>
    </div>

    <!-- Card -->
    <div class="bg-gray-900 rounded-2xl p-8 shadow-2xl">
        <h2 class="text-xl font-bold text-white mb-1">Sube tus documentos</h2>
        <p class="text-gray-400 text-sm mb-6">Necesitamos verificar tu identidad. Las imágenes se guardan de forma segura.</p>

        @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/30 rounded-lg p-3 mb-5">
                @foreach($errors->all() as $error)
                    <p class="text-red-400 text-sm">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('onboarding.step2.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- DNI Frontal -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">DNI — Parte frontal</label>
                <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-700 rounded-xl cursor-pointer hover:border-green-500 transition-colors bg-gray-800/50" id="label-dni-front">
                    <svg class="w-7 h-7 text-gray-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="text-gray-400 text-xs" id="text-dni-front">Haz clic o arrastra la imagen</span>
                    <input type="file" name="dni_front" accept="image/*" class="hidden" onchange="previewFile(this, 'text-dni-front')">
                </label>
            </div>

            <!-- DNI Trasera -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">DNI — Parte trasera</label>
                <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-700 rounded-xl cursor-pointer hover:border-green-500 transition-colors bg-gray-800/50">
                    <svg class="w-7 h-7 text-gray-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="text-gray-400 text-xs" id="text-dni-back">Haz clic o arrastra la imagen</span>
                    <input type="file" name="dni_back" accept="image/*" class="hidden" onchange="previewFile(this, 'text-dni-back')">
                </label>
            </div>

            <!-- Recibo de luz o agua -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Recibo de luz o agua</label>
                <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-700 rounded-xl cursor-pointer hover:border-green-500 transition-colors bg-gray-800/50">
                    <svg class="w-7 h-7 text-gray-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span class="text-gray-400 text-xs" id="text-utility">Haz clic o arrastra la imagen</span>
                    <input type="file" name="utility_bill" accept="image/*" class="hidden" onchange="previewFile(this, 'text-utility')">
                </label>
            </div>

            <button type="submit"
                class="w-full bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-400 hover:to-emerald-400 text-white font-bold py-3 rounded-xl transition-all text-sm mt-2">
                Finalizar registro
            </button>
        </form>
    </div>
</div>

<script>
    function previewFile(input, textId) {
        const el = document.getElementById(textId);
        if (input.files && input.files[0]) {
            el.textContent = input.files[0].name;
            el.classList.add('text-green-400');
        }
    }
</script>

</body>
</html>
