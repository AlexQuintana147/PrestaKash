<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Completado - PrestaKash</title>
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
        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-500 rounded-full mb-6">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-white mb-2">¡Registro Completado!</h1>
        <p class="text-gray-500 text-sm">Tu información ha sido recibida correctamente</p>
    </div>

    <div class="p-6 mb-8 space-y-4">
        <div class="flex gap-3">
            <div class="flex-shrink-0 w-5 h-5 rounded-full bg-blue-500/20 flex items-center justify-center mt-0.5">
                <svg class="w-3 h-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-300 font-medium">Notificación por correo</p>
                <p class="text-xs text-gray-500 mt-1">Te enviaremos un email a <span class="text-blue-400">{{ auth()->user()->email }}</span> cuando tu perfil haya sido validado.</p>
            </div>
        </div>

        <div class="flex gap-3">
            <div class="flex-shrink-0 w-5 h-5 rounded-full bg-yellow-500/20 flex items-center justify-center mt-0.5">
                <svg class="w-3 h-3 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-300 font-medium">Tiempo de validación</p>
                <p class="text-xs text-gray-500 mt-1">Nuestro equipo revisará tu información en menos de 24 horas.</p>
            </div>
        </div>

        <div class="flex gap-3">
            <div class="flex-shrink-0 w-5 h-5 rounded-full bg-green-500/20 flex items-center justify-center mt-0.5">
                <svg class="w-3 h-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-300 font-medium">Próximos pasos</p>
                <p class="text-xs text-gray-500 mt-1">Una vez aprobado, podrás acceder a todas las funcionalidades de PrestaKash.</p>
            </div>
        </div>
    </div>

    <div class="text-center mb-8">
        <div class="text-5xl font-bold text-green-500 mb-2" id="countdown">10</div>
        <p class="text-xs text-gray-500 uppercase tracking-wider">Redirigiendo al inicio automáticamente</p>
    </div>

    <form action="{{ route('logout') }}" method="POST" id="logoutForm" class="text-center">
        @csrf
        <button type="submit" class="inline-flex items-center gap-2 text-white font-medium text-sm px-6 py-3 border border-gray-700 hover:border-green-500 hover:text-green-400 transition-all rounded-lg bg-transparent">
            Volver al Inicio
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </form>

</div>

<script>
let seconds = 10;
const countdownEl = document.getElementById('countdown');
const logoutForm = document.getElementById('logoutForm');

const interval = setInterval(() => {
    seconds--;
    countdownEl.textContent = seconds;

    if (seconds <= 0) {
        clearInterval(interval);
        logoutForm.submit();
    }
}, 1000);
</script>
</body>
</html>
