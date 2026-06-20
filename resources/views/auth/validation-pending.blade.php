<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validación Pendiente - PrestaKash</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { colors: { white: '#F7F7F4', gray: { 50: '#F2F1ED', 100: '#F2F1ED', 500: '#7A7974', 600: '#7A7974', 800: '#232018', 900: '#1B1913', 950: '#14120B' } } } } }</script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in { animation: fadeIn 0.6s ease-out; }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .pulse { animation: pulse 2s ease-in-out infinite; }
    </style>
</head>
<body class="bg-gray-950 min-h-screen flex items-center justify-center px-4 py-12">
<div class="w-full max-w-xl fade-in">

    <div class="mb-10 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-yellow-500 rounded-full mb-6">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-white mb-2">Validación en Proceso</h1>
        <p class="text-gray-500 text-sm">Tu perfil está siendo revisado por nuestro equipo</p>
    </div>

    <div class="p-4 mb-8 text-center">
        <p class="text-yellow-400 text-sm font-medium">Estado: Pendiente de Aprobación</p>
    </div>

    <div class="p-6 mb-8 space-y-4">
        <div class="flex gap-3">
            <div class="flex-shrink-0 w-5 h-5 rounded-full bg-blue-500/20 flex items-center justify-center mt-0.5">
                <svg class="w-3 h-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-300 font-medium">Tu información está en revisión</p>
                <p class="text-xs text-gray-500 mt-1">Nuestro equipo está validando los datos y documentos que proporcionaste.</p>
            </div>
        </div>

        <div class="flex gap-3">
            <div class="flex-shrink-0 w-5 h-5 rounded-full bg-yellow-500/20 flex items-center justify-center mt-0.5">
                <svg class="w-3 h-3 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-300 font-medium">Tiempo estimado</p>
                <p class="text-xs text-gray-500 mt-1">El proceso de validación toma menos de 24 horas hábiles.</p>
            </div>
        </div>

        <div class="flex gap-3">
            <div class="flex-shrink-0 w-5 h-5 rounded-full bg-purple-500/20 flex items-center justify-center mt-0.5">
                <svg class="w-3 h-3 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-300 font-medium">Te notificaremos</p>
                <p class="text-xs text-gray-500 mt-1">Recibirás un correo en <span class="text-purple-400">{{ auth()->user()->email }}</span> cuando tu perfil sea aprobado.</p>
            </div>
        </div>

        <div class="flex gap-3">
            <div class="flex-shrink-0 w-5 h-5 rounded-full bg-red-500/20 flex items-center justify-center mt-0.5">
                <svg class="w-3 h-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-300 font-medium">Acceso restringido</p>
                <p class="text-xs text-gray-500 mt-1">Mientras tanto, no podrás acceder a las funcionalidades de la plataforma.</p>
            </div>
        </div>
    </div>

    <form action="{{ route('logout') }}" method="POST" class="text-center">
        @csrf
        <button type="submit" class="inline-flex items-center gap-2 text-white font-medium text-sm px-6 py-3 border border-gray-700 hover:border-red-500 hover:text-red-400 transition-all rounded-lg bg-transparent">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Cerrar Sesión
        </button>
    </form>

</div>
</body>
</html>
