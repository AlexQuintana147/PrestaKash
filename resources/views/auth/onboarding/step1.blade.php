<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PrestaKash — Completa tu perfil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Roboto', sans-serif; } </style>
</head>
<body class="bg-gray-950 min-h-screen flex items-center justify-center px-4">

<div class="w-full max-w-md">

    <!-- Logo -->
    <div class="text-center mb-8">
        <span class="text-3xl font-extrabold text-white">PrestaKash</span>
        <p class="text-gray-400 text-sm mt-1">Completa tu perfil para continuar</p>
    </div>

    <!-- Pasos -->
    <div class="flex items-center justify-center gap-3 mb-8">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white text-sm font-bold">1</div>
            <span class="text-white text-sm font-medium">Datos personales</span>
        </div>
        <div class="w-8 h-px bg-gray-700"></div>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center text-gray-400 text-sm font-bold">2</div>
            <span class="text-gray-500 text-sm">Documentos</span>
        </div>
    </div>

    <!-- Card -->
    <div>
        <h2 class="text-xl font-bold text-white mb-1">Datos personales</h2>
        <p class="text-gray-400 text-sm mb-6">Tu correo viene de Google. Solo necesitamos tu nombre completo y teléfono.</p>

        @if(session('error'))
            <div class="bg-red-500/10 border border-red-500/30 rounded-lg p-3 mb-5">
                <p class="text-red-400 text-sm">{{ session('error') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/30 rounded-lg p-3 mb-5">
                @foreach($errors->all() as $error)
                    <p class="text-red-400 text-sm">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('onboarding.step1.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Correo (solo lectura) -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Correo electrónico</label>
                <input type="email" value="{{ Auth::user()->email }}" disabled
                    class="w-full bg-gray-800 border border-gray-700 text-gray-400 rounded-lg px-4 py-2.5 text-sm cursor-not-allowed">
            </div>

            <!-- Nombre completo -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Nombre completo</label>
                <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required
                    placeholder="Ej: Juan Pérez García"
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-green-500 transition-colors">
            </div>

            <!-- Teléfono -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Número de celular</label>
                <input type="tel" name="phone" value="{{ old('phone') }}" required
                    placeholder="Ej: 987654321"
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-green-500 transition-colors">
            </div>

            <button type="submit"
                class="w-full bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-400 hover:to-emerald-400 text-white font-bold py-3 rounded-xl transition-all text-sm mt-2">
                Siguiente →
            </button>
        </form>
    </div>
</div>

</body>
</html>
