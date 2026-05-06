<!DOCTYPE html>
<html lang="es" class="h-full dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PrestaKash — @yield('title', 'Panel')</title>
    <script>
        (function() {
            const theme = localStorage.getItem('client-theme') || 'light';
            document.documentElement.classList.toggle('dark', theme === 'dark');
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Roboto', sans-serif; }</style>
</head>
<body class="h-full bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-white transition-colors duration-200">

<!-- Mobile topbar -->
<header class="md:hidden fixed top-0 left-0 right-0 z-40 bg-white dark:bg-black border-b border-gray-200 dark:border-gray-900 flex items-center justify-between px-5 h-14 transition-colors duration-200">
    <img src="/images/logo.webp" alt="PrestaKash" class="h-7 w-auto invert dark:invert-0">
    <button onclick="toggleSidebar()" class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors p-1">
        <svg id="menuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
        <svg id="menuClose" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</header>

<div id="sidebarOverlay" onclick="toggleSidebar()"
     class="hidden fixed inset-0 bg-black/40 z-30 md:hidden"></div>

<div class="flex h-full">

    <!-- Sidebar -->
    <aside id="sidebar"
           class="fixed md:static inset-y-0 left-0 z-40
                  w-64 bg-white dark:bg-black
                  border-r border-gray-100 dark:border-gray-900
                  flex flex-col
                  transform -translate-x-full md:translate-x-0
                  transition-transform duration-300 ease-in-out">

        <!-- Logo (desktop) -->
        <div class="hidden md:block px-6 py-5 border-b border-gray-100 dark:border-gray-900">
            <img src="/images/logo.webp" alt="PrestaKash" class="h-8 w-auto invert dark:invert-0">
            <p class="text-xs text-gray-400 dark:text-gray-600 mt-1 uppercase tracking-widest">Mi Panel</p>
        </div>

        <!-- Logo (mobile) -->
        <div class="md:hidden px-6 py-5 border-b border-gray-100 dark:border-gray-900 flex-shrink-0">
            <img src="/images/logo.webp" alt="PrestaKash" class="h-8 w-auto invert dark:invert-0">
            <p class="text-xs text-gray-400 dark:text-gray-600 mt-1 uppercase tracking-widest">Mi Panel</p>
        </div>

        <!-- Nav -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <a href="{{ route('client.dashboard') }}" onclick="closeSidebar()"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('client.dashboard')
                          ? 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white'
                          : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-900' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Inicio
            </a>

            <a href="#"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 dark:text-gray-600 cursor-not-allowed opacity-60">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Mis préstamos
                <span class="ml-auto text-xs bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-600 px-1.5 py-0.5 rounded">Pronto</span>
            </a>
        </nav>

        <!-- Theme + User + Logout -->
        <div class="px-3 py-4 border-t border-gray-100 dark:border-gray-900 space-y-1">

            <!-- Theme toggle -->
            <button onclick="toggleTheme()" id="themeBtn"
                    class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                <svg id="iconSun" class="w-4 h-4 flex-shrink-0 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"/>
                </svg>
                <svg id="iconMoon" class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                </svg>
                <span id="themeLabel">Modo oscuro</span>
            </button>

            <!-- User -->
            <div class="flex items-center gap-3 px-3 py-2">
                <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=e5e7eb&color=6b7280' }}"
                     class="w-8 h-8 rounded-full object-cover flex-shrink-0 bg-gray-200 dark:bg-gray-800">
                <div class="min-w-0">
                    <p class="text-xs font-medium text-gray-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-600 truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>

            <!-- Logout -->
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    <!-- Main -->
    <main class="flex-1 overflow-y-auto pt-14 md:pt-0 min-w-0">
        @yield('content')
    </main>

</div>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const open    = document.getElementById('menuOpen');
    const close   = document.getElementById('menuClose');
    const isOpen  = !sidebar.classList.contains('-translate-x-full');
    if (isOpen) {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        open.classList.remove('hidden');
        close.classList.add('hidden');
        document.body.style.overflow = '';
    } else {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
        open.classList.add('hidden');
        close.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    if (!sidebar.classList.contains('-translate-x-full')) toggleSidebar();
}

function toggleTheme() {
    const isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('client-theme', isDark ? 'dark' : 'light');
    updateThemeUI(isDark);
}

function updateThemeUI(isDark) {
    document.getElementById('iconSun').classList.toggle('hidden', isDark);
    document.getElementById('iconMoon').classList.toggle('hidden', !isDark);
    document.getElementById('themeLabel').textContent = isDark ? 'Modo claro' : 'Modo oscuro';
}

updateThemeUI(document.documentElement.classList.contains('dark'));
</script>

</body>
</html>
