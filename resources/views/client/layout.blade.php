<!DOCTYPE html>
<html lang="es" class="h-full dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PrestaKash — @yield('title', 'Panel')</title>
    <script>
        (function () {
            const theme = localStorage.getItem('client-theme') || 'light';
            document.documentElement.classList.toggle('dark', theme === 'dark');
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class', theme: { extend: { fontFamily: { sans: ['Montserrat', 'sans-serif'] } } } }</script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }

        nav::-webkit-scrollbar,
        main::-webkit-scrollbar,
        body::-webkit-scrollbar {
            width: 8px;
        }

        nav::-webkit-scrollbar-track,
        main::-webkit-scrollbar-track,
        body::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .dark nav::-webkit-scrollbar-track,
        .dark main::-webkit-scrollbar-track,
        .dark body::-webkit-scrollbar-track {
            background: #0f172a;
        }

        nav::-webkit-scrollbar-thumb,
        main::-webkit-scrollbar-thumb,
        body::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 99px;
            border: 2px solid #f1f1f1;
        }

        .dark nav::-webkit-scrollbar-thumb,
        .dark main::-webkit-scrollbar-thumb,
        .dark body::-webkit-scrollbar-thumb {
            background: #334155;
            border: 2px solid #0f172a;
        }

        nav::-webkit-scrollbar-thumb:hover,
        main::-webkit-scrollbar-thumb:hover,
        body::-webkit-scrollbar-thumb:hover {
            background: #aaa;
        }

        .dark nav::-webkit-scrollbar-thumb:hover,
        .dark main::-webkit-scrollbar-thumb:hover,
        .dark body::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }

        @keyframes swing {
            0% {
                transform: rotate(0deg);
            }

            20% {
                transform: rotate(15deg);
            }

            40% {
                transform: rotate(-10deg);
            }

            60% {
                transform: rotate(5deg);
            }

            80% {
                transform: rotate(-5deg);
            }

            100% {
                transform: rotate(0deg);
            }
        }

        .animate-swing {
            animation: swing 2s infinite;
        }
    </style>
</head>

<body class="h-full bg-white dark:bg-gray-950 text-gray-900 dark:text-white transition-colors duration-200">

    <div id="app-loader" style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;transition:opacity .2s">
        <div style="width:36px;height:36px;border:3px solid #e5e7eb;border-top-color:#E94560;border-radius:50%;animation:pk-spin .6s linear infinite"></div>
        <style>#app-loader{background:#fff}.dark #app-loader{background:#030712}.dark #app-loader>div{border-color:#374151;border-top-color:#E94560}@keyframes pk-spin{to{transform:rotate(360deg)}}</style>
    </div>
    <script>document.addEventListener('DOMContentLoaded',function(){var l=document.getElementById('app-loader');l.style.opacity='0';setTimeout(function(){l.remove()},200)})</script>

    <!-- Mobile topbar -->
    <header
        class="md:hidden fixed top-0 left-0 right-0 z-40 bg-white dark:bg-black border-b border-gray-200 dark:border-gray-900 flex items-center justify-between px-5 h-14 transition-colors duration-200">
        <a href="{{ route('client.dashboard') }}" class="flex items-center">
            <img src="/images/logo.webp" alt="PrestaKash" class="h-7 w-auto invert dark:invert-0">
        </a>
        <button onclick="toggleSidebar()"
            class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors p-1">
            <svg id="menuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg id="menuClose" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </header>

    <div id="sidebarOverlay" onclick="toggleSidebar()" class="hidden fixed inset-0 bg-black/40 z-30 md:hidden"></div>

    <div class="flex h-full">

        <!-- Sidebar -->
        <aside id="sidebar" class="fixed md:static inset-y-0 left-0 z-40
                  w-64 bg-white dark:bg-black
                  border-r border-gray-100 dark:border-gray-900
                  flex flex-col
                  transform -translate-x-full md:translate-x-0
                  transition-transform duration-300 ease-in-out">

            <!-- Logo (desktop) -->
            <div class="hidden md:block px-6 py-5 border-b border-gray-100 dark:border-gray-900">
                <a href="{{ route('client.dashboard') }}" class="block">
                    <img src="/images/logo.webp" alt="PrestaKash" class="h-8 w-auto invert dark:invert-0">
                </a>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 uppercase tracking-widest">Mi Panel</p>
            </div>

            <!-- Logo (mobile) -->
            <div class="md:hidden px-6 py-5 border-b border-gray-100 dark:border-gray-900 flex-shrink-0">
                <a href="{{ route('client.dashboard') }}" class="block">
                    <img src="/images/logo.webp" alt="PrestaKash" class="h-8 w-auto invert dark:invert-0">
                </a>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 uppercase tracking-widest">Mi Panel</p>
            </div>

            <!-- Nav -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <a href="{{ route('client.dashboard') }}" onclick="closeSidebar()"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('client.dashboard')
    ? 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white'
    : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-900' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Inicio
                </a>

                @php
                    $hasCounterOffer = Auth::user()->loans()->where('status', 'counter_offered')->exists();
                @endphp

                <a href="{{ route('client.loans') }}" onclick="closeSidebar()"
                    class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('client.loans*') ? 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-900' }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="truncate">Gestionar Préstamos</span>
                    </div>
                    @if($hasCounterOffer)
                        <div class="relative flex-shrink-0 ml-2">
                            <svg class="w-4 h-4 text-purple-600 animate-swing origin-top" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                    @endif
                </a>

                <a href="{{ route('client.referidos') }}" onclick="closeSidebar()"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('client.referidos') ? 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-900' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Ver Referidos
                </a>

                @php
                    $hasInvestmentNotification = Auth::user() && Auth::user()->is_investor && Auth::user()->investment_rejection_reason;
                    $isInvestActive = request()->routeIs('client.invertir*') || request()->routeIs('client.investments.*');
                    $hasPendingPayouts = Auth::user() && Auth::user()->is_investor &&
                        \App\Models\InvestorPayout::where('investor_user_id', Auth::id())
                            ->where('status', 'pending')
                            ->exists();
                @endphp

                @if(auth()->user() && auth()->user()->is_investor)
                <div>
                    <button onclick="toggleInvestMenu()" id="invest-toggle-btn"
                        class="flex items-center justify-between w-full px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                        {{ $isInvestActive ? 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-900' }}">
                        <div class="flex items-center gap-3 min-w-0">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            <span class="truncate">Invertir</span>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($hasInvestmentNotification)
                                <svg class="w-4 h-4 text-red-500 animate-swing origin-top" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            @endif
                            <svg id="invest-chevron" class="w-3.5 h-3.5 transition-transform duration-200 {{ $isInvestActive ? 'rotate-90' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </button>
                    <div id="invest-submenu" class="mt-1 ml-4 space-y-0.5 {{ $isInvestActive ? '' : 'hidden' }}">
                        <a href="{{ route('client.investments.index') }}" onclick="closeSidebar()"
                            class="flex items-center justify-between px-3 py-2 rounded-lg text-[13px] font-medium transition-colors
                            {{ request()->routeIs('client.investments.index') ? 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-900' }}">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                Oportunidades
                            </div>
                            @if($hasInvestmentNotification)
                                <svg class="w-4 h-4 text-red-500 animate-swing origin-top" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            @endif
                        </a>
                        <a href="{{ route('client.investments.my-investments') }}" onclick="closeSidebar()"
                            class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-[13px] font-medium transition-colors
                            {{ request()->routeIs('client.investments.my-investments') ? 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-900' }}">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            Mis Inversiones
                        </a>
                        <a href="{{ route('client.investments.payments') }}" onclick="closeSidebar()"
                            class="flex items-center justify-between px-3 py-2 rounded-lg text-[13px] font-medium transition-colors
                            {{ request()->routeIs('client.investments.payments') ? 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-900' }}">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                Mis Pagos
                            </div>
                            @if($hasPendingPayouts)
                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            @endif
                        </a>
                    </div>
                </div>
                @else
                <a href="{{ route('client.invertir') }}" onclick="closeSidebar()"
                    class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('client.invertir*') ? 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-900' }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        <span class="truncate">Invertir</span>
                    </div>
                </a>
                @endif
            </nav>

            <!-- Theme + User + Logout -->
            <div class="px-3 py-4 border-t border-gray-100 dark:border-gray-900 space-y-1">

                <!-- Theme toggle -->
                <button onclick="toggleTheme()" id="themeBtn"
                    class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                    <svg id="iconSun" class="w-4 h-4 flex-shrink-0 hidden" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                    </svg>
                    <svg id="iconMoon" class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                    </svg>
                    <span id="themeLabel">Modo oscuro</span>
                </button>

                <!-- User -->
                <div class="flex items-center gap-3 px-3 py-2">
                    @php
                        $selfie = Auth::user()->documents?->selfie;
                        $profilePhoto = $selfie ? Storage::url($selfie) : (Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=e5e7eb&color=6b7280');
                    @endphp
                    <img src="{{ $profilePhoto }}"
                        class="w-8 h-8 rounded-full object-cover flex-shrink-0 bg-gray-200 dark:bg-gray-800">
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-900 dark:text-white truncate">{{ Auth::user()->name }}
                        </p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>

                <!-- Logout -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
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

    <!-- Subtle loading overlay -->
    <div id="pk-loading" class="fixed inset-0 z-[9999] pointer-events-none flex items-center justify-center opacity-0 transition-opacity duration-300">
        <div class="bg-white/60 dark:bg-gray-950/60 backdrop-blur-sm absolute inset-0"></div>
        <div class="relative flex flex-col items-center gap-3">
            <div class="w-8 h-8 border-2 border-gray-300 dark:border-gray-600 border-t-green-500 rounded-full animate-spin"></div>
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Procesando...</p>
        </div>
    </div>

    <script>
        // Show loading overlay on any form submit
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

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const open = document.getElementById('menuOpen');
            const close = document.getElementById('menuClose');
            const isOpen = !sidebar.classList.contains('-translate-x-full');
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

        function toggleInvestMenu() {
            const submenu = document.getElementById('invest-submenu');
            const chevron = document.getElementById('invest-chevron');
            submenu.classList.toggle('hidden');
            chevron.classList.toggle('rotate-90');
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

    @include('partials.global-scripts')

</body>

</html>