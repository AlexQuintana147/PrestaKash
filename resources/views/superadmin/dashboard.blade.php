@extends('superadmin.layout')

@section('title', 'Dashboard')

@push('styles')
<style>
    .dash-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .dash-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px -5px rgba(0,0,0,0.1);
    }
    .dark .dash-card:hover {
        box-shadow: 0 8px 25px -5px rgba(0,0,0,0.4);
    }
    .fade-up {
        animation: fadeUp 0.4s ease-out forwards;
        opacity: 0;
        transform: translateY(8px);
    }
    @keyframes fadeUp {
        to { opacity: 1; transform: translateY(0); }
    }
    .fade-up:nth-child(1) { animation-delay: 0.05s; }
    .fade-up:nth-child(2) { animation-delay: 0.1s; }
    .fade-up:nth-child(3) { animation-delay: 0.15s; }
    .fade-up:nth-child(4) { animation-delay: 0.2s; }
</style>
@endpush

@section('content')
<div class="p-4 md:p-6 lg:p-8 space-y-6 max-w-[1600px] mx-auto">

    <div>
        <h1 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Resumen general del sistema</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        <div class="dash-card fade-up bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 md:p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4.5 h-4.5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total clientes</p>
                    <p class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2 text-xs">
                <span class="text-green-600 dark:text-green-400 font-medium">+{{ $stats['today'] }}</span>
                <span class="text-gray-400">hoy</span>
            </div>
        </div>

        <div class="dash-card fade-up bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 md:p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4.5 h-4.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Pendientes revisión</p>
                    <p class="text-lg md:text-xl font-bold text-amber-600 dark:text-amber-400">{{ $stats['pending'] }}</p>
                </div>
            </div>
            @if($stats['pending'] > 0)
                <a href="{{ route('superadmin.users.index') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Revisar →</a>
            @else
                <span class="text-xs text-gray-400">Todo al día</span>
            @endif
        </div>

        <div class="dash-card fade-up bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 md:p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-lg bg-green-50 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4.5 h-4.5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Perfiles completos</p>
                    <p class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">{{ $stats['completed'] }}</p>
                </div>
            </div>
            @php $completionRate = $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100, 1) : 0; @endphp
            <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-1.5 overflow-hidden">
                <div class="h-full bg-green-500 rounded-full transition-all duration-700" style="width: {{ $completionRate }}%"></div>
            </div>
            <p class="text-[10px] text-gray-400 mt-1">{{ $completionRate }}% de completitud</p>
        </div>

        <div class="dash-card fade-up bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 md:p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-lg bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4.5 h-4.5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Nuevos hoy</p>
                    <p class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">{{ $stats['today'] }}</p>
                </div>
            </div>
            <a href="{{ route('superadmin.analytics') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Ver análisis →</a>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <a href="{{ route('superadmin.loans.index', ['filter' => 'pending']) }}" class="dash-card fade-up flex items-center gap-3 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-3.5 hover:border-blue-300 dark:hover:border-blue-700 transition-colors">
            <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Préstamos</span>
        </a>
        <a href="{{ route('superadmin.installments.index') }}" class="dash-card fade-up flex items-center gap-3 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-3.5 hover:border-green-300 dark:hover:border-green-700 transition-colors">
            <div class="w-8 h-8 rounded-lg bg-green-50 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
            </div>
            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Cobranzas</span>
        </a>
        <a href="{{ route('superadmin.investments.index') }}" class="dash-card fade-up flex items-center gap-3 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-3.5 hover:border-purple-300 dark:hover:border-purple-700 transition-colors">
            <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
            </div>
            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Inversiones</span>
        </a>
        <a href="{{ route('superadmin.analytics') }}" class="dash-card fade-up flex items-center gap-3 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-3.5 hover:border-amber-300 dark:hover:border-amber-700 transition-colors">
            <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" /></svg>
            </div>
            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Análisis</span>
        </a>
    </div>

    {{-- Recent Users --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl">
        <div class="flex items-center justify-between p-4 md:p-5 border-b border-gray-100 dark:border-gray-800">
            <div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Usuarios recientes</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Últimos 10 registros</p>
            </div>
            <a href="{{ route('superadmin.users.index') }}" class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:underline">Ver todos</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="text-left py-2.5 px-4 font-medium text-gray-500 dark:text-gray-400">Usuario</th>
                        <th class="text-left py-2.5 px-4 font-medium text-gray-500 dark:text-gray-400 hidden sm:table-cell">Rol</th>
                        <th class="text-left py-2.5 px-4 font-medium text-gray-500 dark:text-gray-400">Estado</th>
                        <th class="text-left py-2.5 px-4 font-medium text-gray-500 dark:text-gray-400 hidden md:table-cell">Registro</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentUsers as $user)
                        <tr class="border-b border-gray-50 dark:border-gray-800/50 hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                            <td class="py-2.5 px-4">
                                <a href="{{ route('superadmin.users.show', $user) }}" class="flex items-center gap-2.5 hover:underline">
                                    <div class="w-7 h-7 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center flex-shrink-0">
                                        <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-900 dark:text-white truncate max-w-[140px]">{{ $user->name }}</p>
                                        <p class="text-gray-400 truncate max-w-[140px]">{{ $user->email }}</p>
                                    </div>
                                </a>
                            </td>
                            <td class="py-2.5 px-4 hidden sm:table-cell">
                                <span class="text-gray-600 dark:text-gray-400">{{ $user->role->name ?? '—' }}</span>
                            </td>
                            <td class="py-2.5 px-4">
                                @if($user->profile_completed)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400">Completo</span>
                                @elseif($user->documents && $user->documents->where('status', 'pending')->count() > 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400">Pendiente</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-400">Incompleto</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-gray-400 hidden md:table-cell">{{ $user->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
