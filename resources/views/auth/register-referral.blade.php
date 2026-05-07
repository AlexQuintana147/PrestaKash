<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse — PrestaKash</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Roboto', sans-serif; }</style>
</head>
<body class="min-h-screen bg-black flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <img src="/images/logo.webp" alt="PrestaKash" class="h-12 w-auto mx-auto mb-4">
            <p class="text-xs font-semibold text-green-400 uppercase tracking-widest mb-1">PrestaKash</p>
            <h1 class="text-2xl font-bold text-white mb-1">Crea tu cuenta</h1>
            <p class="text-sm text-gray-500">Te ha invitado un usuario</p>
        </div>

        @if(session('error'))
            <div class="mb-4 p-3 bg-red-500/10 border border-red-500/20 rounded-lg text-red-400 text-xs text-center">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="referred_by_code" value="{{ $referralCode }}">

            <div>
                <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-1.5">Nombre completo</label>
                <input type="text" name="name" required value="{{ old('name') }}"
                       class="w-full bg-transparent border-b border-gray-800 text-white placeholder-gray-600 px-0 py-2 focus:outline-none focus:border-green-500 transition-colors text-sm @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-1.5">Correo electrónico</label>
                <input type="email" name="email" required value="{{ old('email') }}"
                       class="w-full bg-transparent border-b border-gray-800 text-white placeholder-gray-600 px-0 py-2 focus:outline-none focus:border-green-500 transition-colors text-sm @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-1.5">Contraseña</label>
                <input type="password" name="password" required
                       class="w-full bg-transparent border-b border-gray-800 text-white placeholder-gray-600 px-0 py-2 focus:outline-none focus:border-green-500 transition-colors text-sm @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-1.5">Confirmar contraseña</label>
                <input type="password" name="password_confirmation" required
                       class="w-full bg-transparent border-b border-gray-800 text-white placeholder-gray-600 px-0 py-2 focus:outline-none focus:border-green-500 transition-colors text-sm">
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 rounded-lg transition-all text-sm">
                    Crear cuenta
                </button>
            </div>
        </form>

        <p class="text-center text-xs text-gray-500 mt-6">
            ¿Ya tienes cuenta? <a href="{{ route('login') }}" class="text-green-400 hover:text-green-300">Inicia sesión</a>
        </p>
    </div>
</body>
</html>
