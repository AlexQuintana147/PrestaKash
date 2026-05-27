<!DOCTYPE html>
<html lang="es" class="h-full dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperAdmin — @yield('title', 'Panel')</title>
    <script>
        // Aplicar tema antes de renderizar para evitar flash
        (function () {
            const theme = localStorage.getItem('sa-theme') || 'dark';
            document.documentElement.classList.toggle('dark', theme === 'dark');
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }

        nav::-webkit-scrollbar,
        main::-webkit-scrollbar,
        body::-webkit-scrollbar {
            width: 4px;
        }

        nav::-webkit-scrollbar-track,
        main::-webkit-scrollbar-track,
        body::-webkit-scrollbar-track {
            background: transparent;
        }

        nav::-webkit-scrollbar-thumb,
        main::-webkit-scrollbar-thumb,
        body::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 99px;
        }

        .dark nav::-webkit-scrollbar-thumb,
        .dark main::-webkit-scrollbar-thumb,
        .dark body::-webkit-scrollbar-thumb {
            background: #374151;
        }

        nav::-webkit-scrollbar-thumb:hover,
        main::-webkit-scrollbar-thumb:hover,
        body::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        .dark nav::-webkit-scrollbar-thumb:hover,
        .dark main::-webkit-scrollbar-thumb:hover,
        .dark body::-webkit-scrollbar-thumb:hover {
            background: #4b5563;
        }

        /* Estilos para los spinners de inputs numéricos en modo oscuro */
        .dark input[type="number"]::-webkit-inner-spin-button,
        .dark input[type="number"]::-webkit-outer-spin-button {
            filter: invert(1) brightness(2);
            cursor: pointer;
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
    @stack('styles')
</head>

<body class="h-full bg-white dark:bg-gray-950 text-gray-900 dark:text-white transition-colors duration-200"
    onload="noBack();" onpageshow="if(event.persisted) noBack();">

    <script>
        function noBack() { window.history.forward(); }
        for (let i = 0; i < 20; i++) { history.pushState(null, '', location.href); }
        window.addEventListener('popstate', () => { history.pushState(null, '', location.href); });
    </script>

    <!-- Mobile topbar -->
    <header
        class="md:hidden fixed top-0 left-0 right-0 z-40 bg-white dark:bg-black border-b border-gray-200 dark:border-gray-900 flex items-center justify-between px-5 h-14 transition-colors duration-200">
        <img src="/images/logo.webp" alt="PrestaKash" class="h-7 w-auto invert dark:invert-0">
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

    <!-- Overlay -->
    <div id="sidebarOverlay" onclick="toggleSidebar()" class="hidden fixed inset-0 bg-black/60 z-30 md:hidden"></div>

    <div class="flex h-full">

        <!-- Sidebar -->
            <aside id="sidebar" class="fixed md:static inset-y-0 left-0 z-40
                  w-60 bg-white dark:bg-black
                  border-r border-gray-200 dark:border-gray-900
                  flex flex-col
                  transform -translate-x-full md:translate-x-0
                  transition-transform duration-300 ease-in-out">

                <div id="sidebar-content">

            <!-- Logo (desktop) -->
            <div class="hidden md:flex px-6 py-5 border-b border-gray-200 dark:border-gray-900 items-center gap-3">
                <img src="/images/logo.webp" alt="PrestaKash" class="h-8 w-auto invert dark:invert-0">
                <button onclick="refreshSidebar(this)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors" title="Refrescar">
                    <svg class="w-4 h-4 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
            </div>

            <!-- Logo (mobile) -->
            <div class="md:hidden px-6 py-5 border-b border-gray-200 dark:border-gray-900 flex-shrink-0 flex items-center gap-3">
                <img src="/images/logo.webp" alt="PrestaKash" class="h-8 w-auto invert dark:invert-0">
                <button onclick="refreshSidebar(this)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors" title="Refrescar">
                    <svg class="w-4 h-4 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
            </div>

            <!-- Nav -->
            <nav class="flex-1 px-3 py-4 overflow-y-auto space-y-5">

                <!-- Principal -->
                <div>
                    <p
                        class="px-3 mb-1 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-widest">
                        Principal</p>
                    <div class="space-y-1">
                        <a href="{{ route('superadmin.dashboard') }}" onclick="closeSidebar()"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('superadmin.dashboard') ? 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-900' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Dashboard
                        </a>
                        <a href="{{ route('superadmin.users.index') }}" onclick="closeSidebar()"
                            class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('superadmin.users.*') ? 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-900' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Clientes
                            </div>
                            @php $pendingUsers = \App\Models\User::whereHas('documents', fn($q) => $q->where('status', 'pending'))->count(); @endphp
                            @php $pendingProfileChanges = \App\Models\ProfileChangeRequest::where('status', 'pending')->count(); @endphp
                            <div id="sidebar-badges" class="flex items-center gap-1" @if($pendingUsers == 0 && $pendingProfileChanges == 0) style="display:none" @endif>
                                @if($pendingProfileChanges > 0)
                                    <span class="text-[10px] font-bold bg-amber-500 text-white rounded-full w-4 h-4 flex items-center justify-center flex-shrink-0">{{ $pendingProfileChanges }}</span>
                                @endif
                                @if($pendingUsers > 0)
                                    <svg class="w-4 h-4 text-purple-600 animate-swing origin-top flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                @endif
                            </div>
                        </a>
                        <a href="{{ route('superadmin.workers.index') }}" onclick="closeSidebar()"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('superadmin.workers.*') ? 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-900' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Trabajadores
                        </a>
                        <a href="{{ route('superadmin.investors.index') }}" onclick="closeSidebar()"
                            class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('superadmin.investors.*') ? 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-900' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                                Inversores
                            </div>
                            @php $pendingInvestors = \App\Models\InvestorApplication::where('status', 'pending')->count(); @endphp
                            <div id="badge-investors" class="flex items-center gap-1" @if($pendingInvestors == 0) style="display:none" @endif>
                            @if($pendingInvestors > 0)
                                <svg class="w-4 h-4 text-purple-600 animate-swing origin-top flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            @endif
                            </div>
                        </a>
                        <a href="{{ route('superadmin.settings') }}" onclick="closeSidebar()"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('superadmin.settings*') ? 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-900' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Configuración
                        </a>
                        <a href="{{ route('superadmin.contracts.index') }}" onclick="closeSidebar()"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('superadmin.contracts*') ? 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-900' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Contratos
                        </a>
                    </div>
                </div>



                <!-- Finanzas -->
                <div>
                    <p
                        class="px-3 mb-1 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-widest">
                        Finanzas</p>
                    <div class="space-y-1">
                        <a href="{{ route('superadmin.loans.index', ['filter' => 'pending']) }}" onclick="closeSidebar()"
                            class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('superadmin.loans.index') ? 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-900' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Control de préstamos
                            </div>
                            @php $pendingLoans = \App\Models\Loan::where('status', 'pending')->count(); @endphp
                            <div id="badge-loans" class="flex items-center gap-1" @if($pendingLoans == 0) style="display:none" @endif>
                            @if($pendingLoans > 0)
                                <svg class="w-4 h-4 text-purple-600 animate-swing origin-top flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            @endif
                            </div>
                        </a>
                        
                        <a href="{{ route('superadmin.investments.index') }}" onclick="closeSidebar()"
                            class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              {{ (request()->routeIs('superadmin.investments.*') && !request()->routeIs('superadmin.investments.payouts')) ? 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-900' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                Control de Inversiones
                            </div>
                            @php
                                $pendingInvestments = \DB::table('investor_opportunity')->where('status', 'pending')->count();
                                $pendingVouchers = \DB::table('investor_opportunity')->where('payment_status', 'pending')->whereNotNull('voucher_path')->count();
                            @endphp
                            <div id="badge-investments" class="flex items-center gap-1" @if($pendingInvestments == 0 && $pendingVouchers == 0) style="display:none" @endif>
                            @if($pendingInvestments > 0)
                                <svg class="w-4 h-4 text-purple-600 animate-swing origin-top flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            @endif
                            @if($pendingVouchers > 0)
                                <svg class="w-4 h-4 text-green-600 animate-swing origin-top flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            @endif
                            </div>
                        </a>
                        <a href="{{ route('superadmin.installments.index') }}" onclick="closeSidebar()"
                            class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('superadmin.installments.index') ? 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-900' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                Cobranzas
                            </div>
                            @php $pendingPayments = \App\Models\LoanInstallment::where('status', 'processing')->count(); @endphp
                            <div id="badge-payments" class="flex items-center gap-1" @if($pendingPayments == 0) style="display:none" @endif>
                            @if($pendingPayments > 0)
                                <svg class="w-4 h-4 text-purple-600 animate-swing origin-top flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            @endif
                            </div>
                        </a>
                        <a href="{{ route('superadmin.investments.payouts') }}" onclick="closeSidebar()"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('superadmin.investments.payouts') ? 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-900' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Pagos
                        </a>
                        <a href="{{ route('superadmin.referrals.payouts') }}" onclick="closeSidebar()"
                            class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('superadmin.referrals.payouts') ? 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-900' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Referidos
                            </div>
                        </a>
                        <a href="{{ route('superadmin.cashboxes.index') }}" onclick="closeSidebar()"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('superadmin.cashboxes.*') ? 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-900' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Caja
                        </a>
                    </div>
                </div>

            </nav>

            <!-- Theme toggle + User info + logout -->
            <div class="px-3 py-4 border-t border-gray-200 dark:border-gray-900 space-y-1">

                <!-- Theme toggle -->
                <button onclick="toggleTheme()" id="themeBtn"
                    class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-900 transition-colors">
                    <!-- Sun (light mode icon) -->
                    <svg id="iconSun" class="w-4 h-4 flex-shrink-0 hidden" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                    </svg>
                    <!-- Moon (dark mode icon) -->
                    <svg id="iconMoon" class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                    </svg>
                    <span id="themeLabel">Modo claro</span>
                </button>

                <!-- User -->
                <div class="flex items-center gap-3 px-3 py-2">
                    <div class="w-10 h-10 flex-shrink-0">
                        <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" xml:space="preserve">
                            <path
                                d="M131.68,315.735c0,0-19.466,8.9-18.922,26.089c0.166,5.262,2.463,10.215,5.93,14.176c4.223,4.824,11.837,13.579,19.059,22.166c8.895,10.575,15.999,22.572,20.691,35.57c10.645,29.482,44.367,92.189,138.654,92.204c7.163,0.001,14.254-1.507,20.803-4.409c26.957-11.952,92.664-52.674,80.02-162.261c0,0-26.652-1.646-51.02,58.319c-4.585,11.282-14.89,19.344-27.023,20.391c-25.932,2.237-71.488-5.51-113.209-71.324c-9.015-14.219-22.092-25.586-37.93-31.299C156.746,311.035,143.01,309.131,131.68,315.735z"
                                fill="#59E1FF" />
                            <path
                                d="M397.914,339.271c0,0-26.654-1.646-51.021,58.323c-4.584,11.28-14.886,19.338-27.016,20.386c-20.094,1.735-51.976-2.546-84.724-35.177l-7.675,3.958c0,0,34.052,52.335,78.828,70.462c2.733,1.106,5.752,1.281,8.627,0.62c6.629-1.524,19.313-5.481,31.652-15.09c4.805-3.742,11.719,0.725,10.41,6.673c-2.21,10.046-5.816,22.762-11.621,35.721C374.226,463.07,407.201,419.76,397.914,339.271z"
                                fill="#30A7EA" />
                            <path
                                d="M232.896,261.559c0,0-35.019-20.627-60.442,20.627c0,0-47.333-9.553-63.085,31.662c-1.438,3.761,0.312,8.006,3.878,9.874c2.419,1.267,5.297,1.235,7.682-0.096c7.553-4.214,22.743-15.056,38.573-2.584c0,0,26.388,17.874,47.433,45.228c8.271,10.752,17.05,21.098,26.913,30.41c17.892,16.893,45.473,38.896,73.429,45.559c2.135,0.508,4.353,0.332,6.415-0.416c10.115-3.673,40.057-18.558,56.831-69.81c2.446-7.473,6.566-14.39,12.324-19.745c4.053-3.77,9.171-7.115,15.129-8.086c3.545-0.577,5.905-3.988,4.941-7.449c-1.875-6.729-6.591-16.088-18.435-19.049c0,0-1.344-24.465-24.178-33.578L232.896,261.559z"
                                fill="#CC5697" />
                            <path
                                d="M206.729,118.542c0,0-24.221,8.996-24.221,35.929v49.066c0,0-0.836,12.946-10.44,13.781c0,0-4.543,0.649-8.369-1.896c-3.883-2.582-9.031-0.081-9.476,4.561c-0.44,4.598,0.02,10.122,2.523,16.172c2.229,5.388,5.883,10.093,10.355,13.835c10.708,8.961,35.126,22.408,73.815,5.33V118.542H206.729z"
                                fill="#E6E6F8" />
                            <rect fill="#E8897B" height="153.753" width="123.62" x="236.685" y="118.542" />
                            <circle cx="295.865" cy="160.072" fill="#333345" r="5.601" />
                            <circle cx="344.193" cy="160.072" fill="#333345" r="5.601" />
                            <path
                                d="M265.179,118.542c0,0,6.138,34.32-19.727,47.131v61.437c0,8.727,3.691,17.232,10.58,22.589c4.056,3.154,9.434,5.889,16.394,6.868c3.018,0.425,5.816-1.618,6.589-4.565c2.509-9.578,11.169-30.12,39.763-34.839c9.562-1.578,19.463-0.939,28.467,2.643c11.334,4.509,24.678,14.601,25.235,37.145c0,36.801-29.832,66.633-66.632,66.633h-11.736c-36.8,0-66.632-29.832-66.632-66.633V118.542H265.179z"
                                fill="#E6E6F8" />
                            <path
                                d="M357.27,34.356c-2.023,2.83-2.057,6.575-0.074,9.434c1.52,2.193,3.265,5.156,4.719,8.81c5.706,14.337-0.33,31.994-14.295,38.56c-5.067,2.383-11.594,3.959-19.995,3.959c-8.597,0-15.231-1.651-20.347-4.127c-14.202-6.875-19.758-24.223-13.77-38.822c1.414-3.449,3.078-6.261,4.536-8.367c1.98-2.861,1.972-6.599-0.052-9.43c-6.479-9.063-12.326-19.592-15.823-26.26c-0.718-1.37-2.05-2.055-3.383-2.055v105.007c0,0-0.498,14.452,16.446,14.452h32.392h39.12c5.367,0,9.717-4.351,9.717-9.718V16.97c0-3.935-5.233-5.325-7.153-1.891C365.99,21.013,361.783,28.04,357.27,34.356z"
                                fill="#FFCA95" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-900 dark:text-white truncate">{{ Auth::user()->name }}
                        </p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Super Admin</p>
                    </div>
                </div>

                <!-- Logout -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-900 transition-colors">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Cerrar sesión
                    </button>
                </form>
            </div>
                </div>
        </aside>

        <!-- Main content -->
        <main id="main-content" class="flex-1 overflow-y-auto pt-14 md:pt-0 min-w-0">
            @yield('content')
        </main>

    </div>

    <script>
        async function refreshSidebar(btn) {
            const svg = btn ? btn.querySelector('svg') : null;
            if (svg) { svg.classList.add('animate-spin'); btn.disabled = true; }
            try {
                const res = await fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const html = await res.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newSidebar = doc.getElementById('sidebar-content');
                if (newSidebar) {
                    document.getElementById('sidebar-content').innerHTML = newSidebar.innerHTML;
                }
                if (btn) {
                    const newMain = doc.getElementById('main-content');
                    if (newMain) {
                        document.getElementById('main-content').innerHTML = newMain.innerHTML;
                    }
                }
            } catch (e) {} finally {
                if (svg) { svg.classList.remove('animate-spin'); btn.disabled = false; }
            }
        }

        const bellSvg = '<svg class="w-4 h-4 text-purple-600 animate-swing origin-top flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>';
        const bellGreenSvg = '<svg class="w-4 h-4 text-green-600 animate-swing origin-top flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>';

        function updateBadge(id, html, show) {
            const el = document.getElementById(id);
            if (!el) return;
            el.innerHTML = html;
            el.style.display = show ? 'flex' : 'none';
        }

        async function updateSidebarBadges() {
            try {
                const res = await fetch('{{ route("superadmin.sidebar-stats") }}');
                const d = await res.json();

                let h = '';
                if (d.pending_profile_changes > 0) h += '<span class="text-[10px] font-bold bg-amber-500 text-white rounded-full w-4 h-4 flex items-center justify-center flex-shrink-0">' + d.pending_profile_changes + '</span>';
                if (d.pending_users > 0) h += bellSvg;
                updateBadge('sidebar-badges', h, d.pending_users > 0 || d.pending_profile_changes > 0);

                updateBadge('badge-investors', d.pending_investors > 0 ? bellSvg : '', d.pending_investors > 0);
                updateBadge('badge-loans', d.pending_loans > 0 ? bellSvg : '', d.pending_loans > 0);

                let invH = '';
                if (d.pending_investments > 0) invH += bellSvg;
                if (d.pending_vouchers > 0) invH += bellGreenSvg;
                updateBadge('badge-investments', invH, d.pending_investments > 0 || d.pending_vouchers > 0);

                updateBadge('badge-payments', d.pending_payments > 0 ? bellSvg : '', d.pending_payments > 0);
            } catch (e) {}
        }

        let _sidebarInterval;
        function startSidebarAutoRefresh() {
            if (_sidebarInterval) return;
            _sidebarInterval = setInterval(() => {
                if (document.visibilityState === 'visible') updateSidebarBadges();
            }, 10000);
        }
        function stopSidebarAutoRefresh() {
            if (_sidebarInterval) { clearInterval(_sidebarInterval); _sidebarInterval = null; }
        }
        document.addEventListener('DOMContentLoaded', startSidebarAutoRefresh);
        document.addEventListener('visibilitychange', () => {
            document.visibilityState === 'visible' ? startSidebarAutoRefresh() : stopSidebarAutoRefresh();
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
            if (!sidebar.classList.contains('-translate-x-full')) {
                toggleSidebar();
            }
        }

        function toggleTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('sa-theme', isDark ? 'dark' : 'light');
            updateThemeUI(isDark);
        }

        function updateThemeUI(isDark) {
            document.getElementById('iconSun').classList.toggle('hidden', isDark);
            document.getElementById('iconMoon').classList.toggle('hidden', !isDark);
            document.getElementById('themeLabel').textContent = isDark ? 'Modo claro' : 'Modo oscuro';
        }

        // Inicializar iconos según tema actual
        updateThemeUI(document.documentElement.classList.contains('dark'));
    </script>

    @include('partials.global-scripts')

    @stack('scripts')

</body>

</html>