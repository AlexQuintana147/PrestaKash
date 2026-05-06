@extends('superadmin.layout')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8">

    <!-- Header -->
    <div class="flex items-end justify-between mb-8">
        <div>
            <p class="text-xs text-gray-400 dark:text-gray-600 uppercase tracking-widest mb-1">Panel</p>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Gestión de Usuarios</h1>
        </div>
        <p class="hidden md:block text-sm text-gray-400 dark:text-gray-600">{{ now()->format('d M Y') }}</p>
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
    <div class="flex items-center gap-0 border-b border-gray-200 dark:border-gray-800 mb-6 overflow-x-auto">
        @foreach(['all' => 'Todos', 'pending' => 'Pendientes', 'approved' => 'Aprobados', 'rejected' => 'Rechazados'] as $key => $label)
            <a href="{{ route('superadmin.users.index', ['filter' => $key]) }}"
               class="relative flex-shrink-0 px-4 py-3 text-sm font-medium transition-colors
                      {{ $filter === $key
                          ? 'text-gray-900 dark:text-white'
                          : 'text-gray-400 dark:text-gray-600 hover:text-gray-700 dark:hover:text-gray-400' }}">
                {{ $label }}
                @if($counts[$key] > 0)
                    <span class="ml-1.5 text-xs {{ $filter === $key ? 'text-gray-500 dark:text-gray-400' : 'text-gray-300 dark:text-gray-700' }}">{{ $counts[$key] }}</span>
                @endif
                @if($filter === $key)
                    <span class="absolute bottom-0 left-0 right-0 h-px bg-gray-900 dark:bg-white"></span>
                @endif
            </a>
        @endforeach
    </div>

    <!-- Cabecera de columnas (solo desktop) -->
    <div class="hidden md:grid py-2 mb-1 px-2 text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider items-center" style="grid-template-columns: 1fr 120px 130px 100px 110px 24px;">
        <span>Usuario</span>
        <span class="text-center">DNI</span>
        <span class="text-center">Teléfono</span>
        <span class="text-center">Registro</span>
        <span class="text-center">Estado</span>
        <span></span>
    </div>

    <!-- Lista -->
    @forelse($users as $user)
    @php $status = $user->documents?->status @endphp
    <a href="{{ route('superadmin.users.show', $user) }}"
       class="flex md:grid items-center py-4 border-b border-gray-200 dark:border-gray-900 hover:bg-gray-50 dark:hover:bg-gray-900/30 -mx-2 px-2 rounded-lg transition-colors group"
       style="grid-template-columns: 1fr 120px 130px 100px 110px 24px;">

        <div class="flex items-center gap-3 min-w-0">
            <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=e5e7eb&color=6b7280&size=64' }}"
                 class="w-9 h-9 rounded-full object-cover flex-shrink-0 bg-gray-200 dark:bg-gray-900">
            <div class="min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $user->name }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-600 truncate">{{ $user->email }}</p>
            </div>
        </div>

        <span class="hidden md:block text-xs text-gray-900 dark:text-white text-center">{{ $user->dni ?? '—' }}</span>
        <span class="hidden md:block text-xs text-gray-900 dark:text-white text-center">{{ $user->phone ?? '—' }}</span>
        <span class="hidden md:block text-xs text-gray-900 dark:text-white text-center">{{ $user->created_at->format('d/m/Y') }}</span>

        <!-- Estado -->
        <div class="flex items-center justify-center gap-1.5 ml-auto md:ml-0">
            @if($status === 'pending')
                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 flex-shrink-0"></span>
                <span class="text-xs text-yellow-500 hidden sm:inline">Pendiente</span>
            @elseif($status === 'approved')
                <span class="w-1.5 h-1.5 rounded-full bg-green-500 flex-shrink-0"></span>
                <span class="text-xs text-green-500 hidden sm:inline">Aprobado</span>
            @elseif($status === 'rejected')
                <span class="w-1.5 h-1.5 rounded-full bg-red-500 flex-shrink-0"></span>
                <span class="text-xs text-red-500 hidden sm:inline">Rechazado</span>
            @else
                <span class="w-1.5 h-1.5 rounded-full bg-gray-300 dark:bg-gray-700 flex-shrink-0"></span>
                <span class="text-xs text-gray-400 dark:text-gray-600 hidden sm:inline">Sin docs</span>
            @endif
        </div>

        <svg class="w-3.5 h-3.5 text-gray-300 dark:text-gray-700 group-hover:text-gray-500 dark:group-hover:text-gray-400 transition-colors hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>

    </a>
    @empty
    <div class="py-20 text-center">
        <p class="text-gray-400 dark:text-gray-700 text-sm">No hay usuarios en esta categoría.</p>
    </div>
    @endforelse

    <!-- Paginación -->
    @if($users->hasPages())
    <div class="mt-8 flex items-center justify-between text-xs text-gray-400 dark:text-gray-600">
        <span>{{ $users->firstItem() }}–{{ $users->lastItem() }} de {{ $users->total() }}</span>
        <div class="flex items-center gap-5">
            @if($users->onFirstPage())
                <span class="opacity-30">← Anterior</span>
            @else
                <a href="{{ $users->previousPageUrl() }}" class="hover:text-gray-900 dark:hover:text-white transition-colors">← Anterior</a>
            @endif
            @if($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}" class="hover:text-gray-900 dark:hover:text-white transition-colors">Siguiente →</a>
            @else
                <span class="opacity-30">Siguiente →</span>
            @endif
        </div>
    </div>
    @endif

</div>
@endsection
