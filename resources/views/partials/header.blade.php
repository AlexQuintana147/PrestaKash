<nav class="fixed top-0 w-full z-50 bg-black border-b border-gray-900">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <div class="flex items-center gap-2.5">
                <img src="/images/logo.webp" alt="PrestaKash" class="h-10 w-auto">
            </div>

            <!-- Navigation Links + Auth - Desktop (centrado) -->
            <div class="hidden md:flex items-center gap-8">
                <a href="#calculadora" class="text-sm font-medium text-gray-300 hover:text-white transition-colors">
                    Calculadora
                </a>
                <a href="#beneficios" class="text-sm font-medium text-gray-300 hover:text-white transition-colors">
                    Beneficios
                </a>
                <a href="#requisitos" class="text-sm font-medium text-gray-300 hover:text-white transition-colors">
                    Requisitos
                </a>
                <span class="text-gray-700">|</span>
                @guest
                    <button onclick="openRegisterModal()" class="text-sm font-medium text-gray-300 hover:text-white transition-colors">
                        Registrarse
                    </button>
                @endguest
                @auth
                    <div class="flex items-center gap-3 px-4 py-2 bg-gray-900 rounded-full">
                        <img src="{{ Auth::user()->avatar }}" alt="avatar" class="w-7 h-7 rounded-full object-cover">
                        <span class="text-sm font-medium text-white">{{ Auth::user()->name }}</span>
                    </div>
                @else
                    <button onclick="openLoginModal()" class="px-6 py-2 text-sm font-semibold text-black bg-white hover:bg-gray-100 rounded-full transition-all">
                        Iniciar sesión
                    </button>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <button onclick="toggleMobileMenu()" class="md:hidden text-white p-2">
                <svg id="menuIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg id="closeIcon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="fixed inset-y-0 right-0 w-full bg-black z-40 md:hidden overflow-y-auto transform translate-x-full transition-transform duration-300 ease-in-out">
        <div class="min-h-screen p-6 flex flex-col">
            <!-- Close Button -->
            <div class="flex justify-end mb-8">
                <button onclick="toggleMobileMenu()" class="text-white p-2">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Navigation Section -->
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-white mb-6">Navegación</h2>
                <div class="space-y-3">
                    <a href="#calculadora" onclick="toggleMobileMenu()" class="block text-xl font-medium text-gray-300 hover:text-green-400 transition-colors py-3">
                        Calculadora
                    </a>
                    <a href="#beneficios" onclick="toggleMobileMenu()" class="block text-xl font-medium text-gray-300 hover:text-green-400 transition-colors py-3">
                        Beneficios
                    </a>
                    <a href="#requisitos" onclick="toggleMobileMenu()" class="block text-xl font-medium text-gray-300 hover:text-green-400 transition-colors py-3">
                        Requisitos
                    </a>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-800 my-6"></div>

            <!-- Auth Section -->
            @guest
                <div class="space-y-4">
                    <button onclick="openRegisterModal(); toggleMobileMenu();" class="block text-left text-xl font-medium text-white hover:text-green-400 transition-colors py-3">
                        Registrarse
                    </button>
                    <button onclick="openLoginModal(); toggleMobileMenu();" class="block text-left text-xl font-medium text-white hover:text-green-400 transition-colors py-3">
                        Iniciar sesión
                    </button>
                </div>
            @else
                <div class="flex items-center gap-3 px-4 py-3 bg-gray-900 rounded-xl">
                    <img src="{{ Auth::user()->avatar }}" alt="avatar" class="w-10 h-10 rounded-full object-cover">
                    <span class="text-base font-medium text-white">{{ Auth::user()->name }}</span>
                </div>
            @endguest

            <!-- Logo at bottom -->
            <div class="mt-auto pt-8">
                <img src="/images/logo.webp" alt="PrestaKash" class="h-12 w-auto">
            </div>
        </div>
    </div>

    <!-- Overlay -->
    <div id="mobileMenuOverlay" class="hidden fixed inset-0 bg-black/50 z-30 md:hidden transition-opacity duration-300" onclick="toggleMobileMenu()"></div>
</nav>

<script>
function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    const overlay = document.getElementById('mobileMenuOverlay');
    const menuIcon = document.getElementById('menuIcon');
    const closeIcon = document.getElementById('closeIcon');

    if (menu.classList.contains('translate-x-full')) {
        // Abrir menú (de derecha a izquierda)
        menu.classList.remove('translate-x-full');
        menu.classList.add('translate-x-0');
        overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } else {
        // Cerrar menú (de izquierda a derecha)
        menu.classList.add('translate-x-full');
        menu.classList.remove('translate-x-0');
        overlay.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    menuIcon.classList.toggle('hidden');
    closeIcon.classList.toggle('hidden');
}
</script>

<!-- Modal de Inicio de Sesión -->
<div id="loginModal" class="hidden fixed inset-0 bg-black/95 backdrop-blur-lg z-50 flex items-center justify-center p-4">
    <div class="bg-black max-w-sm w-full relative overflow-hidden">
        <!-- Header -->
        <div class="relative px-6 pt-6 pb-4">
            <button onclick="closeLoginModal()" class="absolute top-4 right-4 w-6 h-6 flex items-center justify-center text-gray-500 hover:text-white transition-all z-10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <div class="relative">
                <p class="text-xs font-semibold text-green-400 uppercase tracking-widest mb-1">PrestaKash</p>
                <h2 class="text-xl font-bold text-white mb-1">Bienvenido de nuevo</h2>
                <p class="text-xs text-gray-500">Inicia sesión para continuar</p>
            </div>
        </div>

        <div class="px-6 pb-6">
            <!-- Botón Google -->
            <a href="{{ route('auth.google') }}" class="flex items-center justify-center gap-2 w-full bg-white hover:bg-gray-50 text-gray-800 font-medium py-2.5 rounded-lg transition-all mb-4 text-sm">
                <svg class="w-4 h-4" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                Continuar con Google
            </a>

            <div class="relative my-4">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-800"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="px-2 bg-black text-gray-600 uppercase tracking-wider">O</span>
                </div>
            </div>

            <!-- Formulario manual -->
            <form action="{{ route('login.post') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Correo electrónico</label>
                    <input type="email" name="email" required class="w-full bg-transparent border-b border-gray-800 text-white placeholder-gray-600 px-0 py-2 focus:outline-none focus:border-green-500 transition-colors text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Contraseña</label>
                    <input type="password" name="password" required class="w-full bg-transparent border-b border-gray-800 text-white placeholder-gray-600 px-0 py-2 focus:outline-none focus:border-green-500 transition-colors text-sm">
                </div>
                <div class="pt-3">
                    <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2.5 rounded-lg transition-all text-sm">
                        Iniciar sesión
                    </button>
                </div>
            </form>

            <p class="text-center text-xs text-gray-500 mt-4">
                ¿No tienes cuenta? <button type="button" onclick="closeLoginModal(); openRegisterModal();" class="text-green-400 hover:text-green-300 font-medium transition-colors">Crear cuenta</button>
            </p>
        </div>
    </div>
</div>

<!-- Modal de Registro -->
<div id="registerModal" class="hidden fixed inset-0 bg-black/95 backdrop-blur-lg z-50 flex items-center justify-center p-4">
    <div class="bg-black max-w-sm w-full relative overflow-hidden">
        <!-- Header -->
        <div class="relative px-6 pt-6 pb-4">
            <button onclick="closeRegisterModal()" class="absolute top-4 right-4 w-6 h-6 flex items-center justify-center text-gray-500 hover:text-white transition-all z-10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <div class="relative">
                <p class="text-xs font-semibold text-green-400 uppercase tracking-widest mb-1">PrestaKash</p>
                <h2 class="text-xl font-bold text-white mb-1">Crear cuenta</h2>
                <p class="text-xs text-gray-500">Comienza tu solicitud en minutos</p>
            </div>
        </div>

        <div class="px-6 pb-6">
            <!-- Botón Google -->
            <a href="{{ route('auth.google') }}" class="flex items-center justify-center gap-2 w-full bg-white hover:bg-gray-50 text-gray-800 font-medium py-2.5 rounded-lg transition-all mb-4 text-sm">
                <svg class="w-4 h-4" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                Continuar con Google
            </a>

            <div class="relative my-4">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-800"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="px-2 bg-black text-gray-600 uppercase tracking-wider">O</span>
                </div>
            </div>

            <!-- Formulario manual -->
            <form action="{{ route('register') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Nombre completo</label>
                    <input type="text" name="name" required class="w-full bg-transparent border-b border-gray-800 text-white placeholder-gray-600 px-0 py-2 focus:outline-none focus:border-green-500 transition-colors text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Correo electrónico</label>
                    <input type="email" name="email" required class="w-full bg-transparent border-b border-gray-800 text-white placeholder-gray-600 px-0 py-2 focus:outline-none focus:border-green-500 transition-colors text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Contraseña</label>
                    <input type="password" name="password" required class="w-full bg-transparent border-b border-gray-800 text-white placeholder-gray-600 px-0 py-2 focus:outline-none focus:border-green-500 transition-colors text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" required class="w-full bg-transparent border-b border-gray-800 text-white placeholder-gray-600 px-0 py-2 focus:outline-none focus:border-green-500 transition-colors text-sm">
                </div>
                <div class="pt-3">
                    <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2.5 rounded-lg transition-all text-sm">
                        Crear cuenta
                    </button>
                </div>
            </form>

            <p class="text-center text-xs text-gray-500 mt-4">
                ¿Ya tienes cuenta? <button type="button" onclick="closeRegisterModal(); openLoginModal();" class="text-green-400 hover:text-green-300 font-medium transition-colors">Iniciar sesión</button>
            </p>
        </div>
    </div>
</div>

<script>
function openLoginModal() {
    document.getElementById('loginModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeLoginModal() {
    document.getElementById('loginModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function openRegisterModal() {
    document.getElementById('registerModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeRegisterModal() {
    document.getElementById('registerModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Cerrar modales al hacer clic fuera
document.getElementById('loginModal')?.addEventListener('click', (e) => {
    if (e.target.id === 'loginModal') closeLoginModal();
});

document.getElementById('registerModal')?.addEventListener('click', (e) => {
    if (e.target.id === 'registerModal') closeRegisterModal();
});
</script>
