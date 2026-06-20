<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Acceso restringido</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { colors: { white: '#F7F7F4', gray: { 50: '#F2F1ED', 100: '#F2F1ED', 500: '#7A7974', 600: '#7A7974', 800: '#232018', 900: '#1B1913', 950: '#14120B' } } } } }</script>
</head>
<body class="min-h-full bg-gray-950 flex items-center justify-center p-4">

    <div class="w-full max-w-sm bg-gray-900 border border-gray-800 rounded-2xl p-8">

        @if($unavailable)
            {{-- Enlace ya usado --}}
            <div class="text-center">
                <div class="w-14 h-14 rounded-full bg-gray-900 border border-gray-800 flex items-center justify-center mx-auto mb-5">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m0 0v2m0-2h2m-2 0H10m2-5V7m0 0V5m0 2h2M12 7H10"/>
                    </svg>
                </div>
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-widest mb-2">Acceso restringido</p>
                <h1 class="text-xl font-bold text-white mb-2">Este enlace ya no está disponible</h1>
                <p class="text-sm text-gray-500">El acceso de administrador ya fue configurado.</p>
            </div>

        @else
            {{-- Formulario de registro --}}
            <div class="mb-8">
                <img src="/images/logo.webp" alt="PrestaKash" class="h-9 w-auto mb-6">
                <p class="text-xs font-semibold text-green-400 uppercase tracking-widest mb-1">Acceso único</p>
                <h1 class="text-2xl font-bold text-white mb-1">Crear cuenta Super Admin</h1>
                <p class="text-sm text-gray-500">Este enlace solo puede usarse una vez.</p>
            </div>

            @if(session('error'))
                <div class="mb-4 px-4 py-3 rounded-lg bg-red-500/10 border border-red-500/20 text-sm text-red-400">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Google --}}
            <a href="{{ route('superadmin.register.google', $token) }}"
               class="flex items-center justify-center gap-2 w-full bg-white hover:bg-gray-100 text-gray-800 font-medium py-2.5 rounded-lg transition-all mb-5 text-sm">
                <svg class="w-4 h-4" viewBox="0 0 24 24">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                Continuar con Google
            </a>

            <div class="relative my-5">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-800"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="px-2 bg-gray-950 text-gray-600 uppercase tracking-wider">O</span>
                </div>
            </div>

            {{-- Form manual --}}
            <form action="{{ route('superadmin.register.store', $token) }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Nombre completo</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full bg-transparent border-b border-gray-800 text-white placeholder-gray-700 px-0 py-2 focus:outline-none focus:border-green-500 transition-colors text-sm @error('name') border-red-500 @enderror">
                    @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Correo electrónico</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full bg-transparent border-b border-gray-800 text-white placeholder-gray-700 px-0 py-2 focus:outline-none focus:border-green-500 transition-colors text-sm @error('email') border-red-500 @enderror">
                    @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Contraseña</label>
                    <input type="password" name="password" required minlength="8"
                           class="w-full bg-transparent border-b border-gray-800 text-white placeholder-gray-700 px-0 py-2 focus:outline-none focus:border-green-500 transition-colors text-sm @error('password') border-red-500 @enderror">
                    @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" required minlength="8"
                           class="w-full bg-transparent border-b border-gray-800 text-white placeholder-gray-700 px-0 py-2 focus:outline-none focus:border-green-500 transition-colors text-sm">
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2.5 rounded-lg transition-all text-sm">
                        Crear cuenta
                    </button>
                </div>
            </form>
@        @endif

    </div>

    @include('partials.global-scripts')

    <!-- Subtle loading overlay -->
    <div id="pk-loading" class="fixed inset-0 z-[9999] pointer-events-none flex items-center justify-center opacity-0 transition-opacity duration-300">
        <div class="bg-gray-950/60 backdrop-blur-sm absolute inset-0"></div>
        <div class="relative flex flex-col items-center gap-3">
            <div class="w-8 h-8 border-2 border-gray-600 border-t-green-500 rounded-full animate-spin"></div>
            <p class="text-xs font-medium text-gray-400">Procesando...</p>
        </div>
    </div>
    <script>
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

</body>
</html>
