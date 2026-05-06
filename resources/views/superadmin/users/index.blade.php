@extends('superadmin.layout')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8">

    <!-- Header -->
    <div class="flex items-end justify-between mb-8">
        <div>
            <p class="text-xs text-gray-600 uppercase tracking-widest mb-1">Panel</p>
            <h1 class="text-2xl md:text-3xl font-bold text-white tracking-tight">Gestión de Usuarios</h1>
        </div>
        <p class="hidden md:block text-sm text-gray-600">{{ now()->format('d M Y') }}</p>
    </div>

    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 text-sm text-green-400">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Filtros -->
    <div class="flex items-center gap-0 border-b border-gray-800 mb-6 overflow-x-auto">
        @foreach(['all' => 'Todos', 'pending' => 'Pendientes', 'approved' => 'Aprobados', 'rejected' => 'Rechazados'] as $key => $label)
            <a href="{{ route('superadmin.users.index', ['filter' => $key]) }}"
               class="relative flex-shrink-0 px-4 py-3 text-sm font-medium transition-colors
                      {{ $filter === $key ? 'text-white' : 'text-gray-600 hover:text-gray-400' }}">
                {{ $label }}
                @if($counts[$key] > 0)
                    <span class="ml-1.5 text-xs {{ $filter === $key ? 'text-gray-400' : 'text-gray-700' }}">{{ $counts[$key] }}</span>
                @endif
                @if($filter === $key)
                    <span class="absolute bottom-0 left-0 right-0 h-px bg-white"></span>
                @endif
            </a>
        @endforeach
    </div>

    <!-- Lista -->
    @forelse($users as $user)
    @php $status = $user->documents?->status @endphp
    <a href="{{ route('superadmin.users.show', $user) }}"
       class="flex items-center justify-between py-4 border-b border-gray-900 hover:bg-gray-900/30 -mx-2 px-2 rounded-lg transition-colors group">

        <div class="flex items-center gap-3 min-w-0">
            <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=111827&color=6b7280&size=64' }}"
                 class="w-9 h-9 rounded-full object-cover flex-shrink-0 bg-gray-900">
            <div class="min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ $user->name }}</p>
                <p class="text-xs text-gray-600 truncate">
                    <span class="md:hidden">{{ $user->dni ?? $user->email }}</span>
                    <span class="hidden md:inline">{{ $user->email }}</span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-4 flex-shrink-0 ml-3">
            <!-- Info extra solo desktop -->
            <div class="hidden md:flex items-center gap-8 text-xs text-gray-600">
                <span>{{ $user->dni ?? '—' }}</span>
                <span>{{ $user->phone ?? '—' }}</span>
                <span>{{ $user->created_at->format('d/m/Y') }}</span>
            </div>

            <!-- Estado -->
            @if($status === 'pending')
                <span class="flex items-center gap-1.5 text-xs text-yellow-500 flex-shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                    <span class="hidden sm:inline">Pendiente</span>
                </span>
            @elseif($status === 'approved')
                <span class="flex items-center gap-1.5 text-xs text-green-500 flex-shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                    <span class="hidden sm:inline">Aprobado</span>
                </span>
            @elseif($status === 'rejected')
                <span class="flex items-center gap-1.5 text-xs text-red-500 flex-shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                    <span class="hidden sm:inline">Rechazado</span>
                </span>
            @else
                <span class="flex items-center gap-1.5 text-xs text-gray-600 flex-shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-700"></span>
                    <span class="hidden sm:inline">Sin docs</span>
                </span>
            @endif

            <svg class="w-3.5 h-3.5 text-gray-700 group-hover:text-gray-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </div>

    </a>
    @empty
    <div class="py-20 text-center">
        <p class="text-gray-700 text-sm">No hay usuarios en esta categoría.</p>
    </div>
    @endforelse

    <!-- Paginación -->
    @if($users->hasPages())
    <div class="mt-8 flex items-center justify-between text-xs text-gray-600">
        <span>{{ $users->firstItem() }}–{{ $users->lastItem() }} de {{ $users->total() }}</span>
        <div class="flex items-center gap-5">
            @if($users->onFirstPage())
                <span class="opacity-30">← Anterior</span>
            @else
                <a href="{{ $users->previousPageUrl() }}" class="hover:text-white transition-colors">← Anterior</a>
            @endif
            @if($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}" class="hover:text-white transition-colors">Siguiente →</a>
            @else
                <span class="opacity-30">Siguiente →</span>
            @endif
        </div>
    </div>
    @endif

</div>
@endsection
