@extends('superadmin.layout')

@section('title', 'Gestión de Inversores')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8">

    <!-- Header -->
    <div class="flex items-end justify-between mb-8">
        <div>
            <p class="text-xs text-gray-600 dark:text-gray-600 uppercase tracking-widest mb-1">Panel</p>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Inversores</h1>
        </div>
        <p class="hidden md:block text-sm text-gray-600 dark:text-gray-400">{{ now()->format('d M Y') }}</p>
    </div>

    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 text-sm text-green-500">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Filtros -->
    <div class="mb-6">
        <!-- Mobile: grid 2 columnas -->
        <div class="md:hidden space-y-2">
            <div class="grid grid-cols-3 gap-2">
                @foreach(['all' => 'Todos', 'pending' => 'Pendientes', 'approved' => 'Aprobados'] as $key => $label)
                    <a href="{{ route('superadmin.investors.index', ['filter' => $key]) }}"
                       class="px-3 py-2 rounded-lg text-xs font-medium text-center transition-colors
                              {{ $filter === $key
                                  ? 'bg-gray-200 dark:bg-gray-800 text-gray-900 dark:text-white'
                                  : 'bg-gray-100 dark:bg-gray-900 text-gray-500 dark:text-gray-500' }}">
                        {{ $label }}
                        @if($counts[$key] > 0)
                            <span class="ml-1 opacity-60">{{ $counts[$key] }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
            <div class="grid grid-cols-1 gap-2 w-1/2 mx-auto">
                @foreach(['rejected' => 'Rechazados'] as $key => $label)
                    <a href="{{ route('superadmin.investors.index', ['filter' => $key]) }}"
                       class="px-3 py-2 rounded-lg text-xs font-medium text-center transition-colors
                              {{ $filter === $key
                                  ? 'bg-gray-200 dark:bg-gray-800 text-gray-900 dark:text-white'
                                  : 'bg-gray-100 dark:bg-gray-900 text-gray-500 dark:text-gray-500' }}">
                        {{ $label }}
                        @if($counts[$key] > 0)
                            <span class="ml-1 opacity-60">{{ $counts[$key] }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Desktop: tabs horizontales -->
        <div class="hidden md:flex items-center gap-0 border-b border-gray-200 dark:border-gray-800">
            @foreach(['all' => 'Todos', 'pending' => 'Pendientes', 'approved' => 'Aprobados', 'rejected' => 'Rechazados'] as $key => $label)
                <a href="{{ route('superadmin.investors.index', ['filter' => $key]) }}"
                   class="relative flex-shrink-0 px-4 py-3 text-sm font-medium transition-colors
                          {{ $filter === $key
                              ? 'text-gray-900 dark:text-white'
                              : 'text-gray-600 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-400' }}">
                    {{ $label }}
                    @if($counts[$key] > 0)
                        <span class="ml-1.5 text-xs {{ $filter === $key ? 'text-gray-500 dark:text-gray-400' : 'text-gray-300 dark:text-gray-500' }}">{{ $counts[$key] }}</span>
                    @endif
                    @if($filter === $key)
                        <span class="absolute bottom-0 left-0 right-0 h-px bg-gray-900 dark:bg-white"></span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    <!-- Cabecera de columnas (solo desktop) -->
    <div class="hidden md:grid py-2 mb-1 px-2 text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider items-center" style="grid-template-columns: 1fr 150px 130px 120px 100px 80px;">
        <span>Inversor</span>
        <span class="text-center">Ocupación</span>
        <span class="text-center">Monto</span>
        <span class="text-center">Plazo</span>
        <span class="text-center">Estado</span>
        <span class="text-right">Acciones</span>
    </div>

    <!-- Lista -->
    @forelse($applications as $app)
    <div class="flex flex-col md:grid items-center py-4 border-b border-gray-200 dark:border-gray-900 hover:bg-gray-50 dark:hover:bg-gray-900/30 -mx-2 px-2 rounded-lg transition-colors group"
       style="grid-template-columns: 1fr 150px 130px 120px 100px 80px;">

        <!-- Inversor Info -->
        <div class="flex items-center gap-3 min-w-0">
            @php
                $svgColor = match($app->status) {
                    'approved' => '#22c55e',
                    'rejected' => '#ef4444',
                    'pending'  => '#eab308',
                    default    => '#9ca3af',
                };
            @endphp
            <div class="w-9 h-9 flex-shrink-0 flex items-center justify-center">
                <svg width="36" height="36" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.877014 7.49988C0.877014 3.84219 3.84216 0.877045 7.49985 0.877045C11.1575 0.877045 14.1227 3.84219 14.1227 7.49988C14.1227 11.1575 11.1575 14.1227 7.49985 14.1227C3.84216 14.1227 0.877014 11.1575 0.877014 7.49988ZM7.49985 1.82704C4.36683 1.82704 1.82701 4.36686 1.82701 7.49988C1.82701 8.97196 2.38774 10.3131 3.30727 11.3213C4.19074 9.94119 5.73818 9.02499 7.50023 9.02499C9.26206 9.02499 10.8093 9.94097 11.6929 11.3208C12.6121 10.3127 13.1727 8.97172 13.1727 7.49988C13.1727 4.36686 10.6328 1.82704 7.49985 1.82704ZM10.9818 11.9787C10.2839 10.7795 8.9857 9.97499 7.50023 9.97499C6.01458 9.97499 4.71624 10.7797 4.01845 11.9791C4.97952 12.7272 6.18765 13.1727 7.49985 13.1727C8.81227 13.1727 10.0206 12.727 10.9818 11.9787ZM5.14999 6.50487C5.14999 5.207 6.20212 4.15487 7.49999 4.15487C8.79786 4.15487 9.84999 5.207 9.84999 6.50487C9.84999 7.80274 8.79786 8.85487 7.49999 8.85487C6.20212 8.85487 5.14999 7.80274 5.14999 6.50487ZM7.49999 5.10487C6.72679 5.10487 6.09999 5.73167 6.09999 6.50487C6.09999 7.27807 6.72679 7.90487 7.49999 7.90487C8.27319 7.90487 8.89999 7.27807 8.89999 6.50487C8.89999 5.73167 8.27319 5.10487 7.49999 5.10487Z" fill="{{ $svgColor }}"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $app->user->name }}</p>
                <p class="text-xs text-gray-600 dark:text-gray-400 truncate">{{ $app->phone }}</p>
            </div>
        </div>

        <span class="hidden md:block text-xs text-gray-900 dark:text-white text-center">{{ $app->occupation }}</span>
        <span class="hidden md:block text-xs text-green-600 dark:text-green-400 font-bold text-center">S/ {{ number_format($app->intended_amount, 2) }}</span>
        <span class="hidden md:block text-xs text-gray-900 dark:text-white text-center">{{ $app->preferred_term }}</span>

        <!-- Estado -->
        <div class="flex items-center justify-center gap-1.5 ml-auto md:ml-0 my-2 md:my-0">
            @if($app->status === 'pending')
                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 flex-shrink-0"></span>
                <span class="text-xs text-yellow-500">Pendiente</span>
            @elseif($app->status === 'approved')
                <span class="w-1.5 h-1.5 rounded-full bg-green-500 flex-shrink-0"></span>
                <span class="text-xs text-green-500">Aprobado</span>
            @elseif($app->status === 'rejected')
                <span class="w-1.5 h-1.5 rounded-full bg-red-500 flex-shrink-0"></span>
                <span class="text-xs text-red-500">Rechazado</span>
            @endif
        </div>

        <!-- Acciones -->
        <div class="flex items-center justify-end gap-2 w-full md:w-auto">
            @if($app->status === 'pending')
                <form action="{{ route('superadmin.investors.approve', $app) }}" method="POST" onsubmit="return confirm('¿Aprobar esta solicitud?')">
                    @csrf
                    <button type="submit" class="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-xl transition-colors" title="Aprobar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </button>
                </form>
                <button onclick="toggleRejectModal({{ $app->id }})" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-colors" title="Rechazar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            @else
                <span class="text-[10px] text-gray-400 dark:text-gray-600 font-bold uppercase md:pr-2">Procesado</span>
            @endif
        </div>

    </div>
    @empty
    <div class="py-20 text-center">
        <p class="text-gray-400 dark:text-gray-500 text-sm">No hay solicitudes en esta categoría.</p>
    </div>
    @endforelse

    <!-- Paginación -->
    @if($applications->hasPages())
    <div class="mt-8 flex items-center justify-between text-xs text-gray-600 dark:text-gray-400">
        <span>{{ $applications->firstItem() }}–{{ $applications->lastItem() }} de {{ $applications->total() }}</span>
        <div class="flex items-center gap-5">
            @if($applications->onFirstPage())
                <span class="opacity-30">← Anterior</span>
            @else
                <a href="{{ $applications->previousPageUrl() }}" class="hover:text-gray-900 dark:hover:text-white transition-colors">← Anterior</a>
            @endif
            @if($applications->hasMorePages())
                <a href="{{ $applications->nextPageUrl() }}" class="hover:text-gray-900 dark:hover:text-white transition-colors">Siguiente →</a>
            @else
                <span class="opacity-30">Siguiente →</span>
            @endif
        </div>
    </div>
    @endif

</div>

<!-- Modal Rechazo -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-950 w-full max-w-md rounded-3xl p-8 shadow-2xl">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Rechazar Solicitud</h2>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Motivo del rechazo</label>
                <textarea name="reason" rows="3" required class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500/20 dark:text-white transition-all resize-none"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="toggleRejectModal()" class="flex-1 px-4 py-3 rounded-2xl text-sm font-bold text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-900 transition-all">Cancelar</button>
                <button type="submit" class="flex-1 px-4 py-3 rounded-2xl text-sm font-bold bg-red-600 text-white hover:bg-red-500 transition-all">Confirmar</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleRejectModal(appId = null) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    if (appId) {
        form.action = `/gestion-interna/inversores/${appId}/rechazar`;
        modal.classList.remove('hidden');
    } else {
        modal.classList.add('hidden');
    }
}
</script>
@endsection
