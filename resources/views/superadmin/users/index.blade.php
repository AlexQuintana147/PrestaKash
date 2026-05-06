@extends('superadmin.layout')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="min-h-screen px-10 py-10">

    <!-- Header -->
    <div class="flex items-end justify-between mb-10">
        <div>
            <p class="text-xs text-gray-600 uppercase tracking-widest mb-1">Panel</p>
            <h1 class="text-3xl font-bold text-white tracking-tight">Gestión de Usuarios</h1>
        </div>
        <p class="text-sm text-gray-600">{{ now()->format('d M Y') }}</p>
    </div>

    @if(session('success'))
        <div class="mb-8 flex items-center gap-3 text-sm text-green-400">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Filtros tipo tab minimalista -->
    <div class="flex items-center gap-0 border-b border-gray-800 mb-8">
        @foreach(['all' => 'Todos', 'pending' => 'Pendientes', 'approved' => 'Aprobados', 'rejected' => 'Rechazados'] as $key => $label)
            <a href="{{ route('superadmin.users.index', ['filter' => $key]) }}"
               class="relative px-5 py-3 text-sm font-medium transition-colors
                      {{ $filter === $key ? 'text-white' : 'text-gray-600 hover:text-gray-400' }}">
                {{ $label }}
                @if($counts[$key] > 0)
                    <span class="ml-2 text-xs {{ $filter === $key ? 'text-gray-400' : 'text-gray-700' }}">{{ $counts[$key] }}</span>
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
    <div class="flex items-center justify-between py-4 border-b border-gray-900 group">

        <!-- Avatar + info -->
        <div class="flex items-center gap-4 min-w-0">
            <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=111827&color=6b7280&size=64' }}"
                 class="w-9 h-9 rounded-full object-cover flex-shrink-0 bg-gray-900">
            <div class="min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ $user->name }}</p>
                <p class="text-xs text-gray-600 truncate">{{ $user->email }}</p>
            </div>
        </div>

        <!-- Meta -->
        <div class="hidden md:flex items-center gap-10 text-xs text-gray-600 mx-8">
            <span>{{ $user->dni ?? '—' }}</span>
            <span>{{ $user->phone ?? '—' }}</span>
            <span>{{ $user->created_at->format('d/m/Y') }}</span>
        </div>

        <!-- Estado + acción -->
        <div class="flex items-center gap-6 flex-shrink-0">
            @if($status === 'pending')
                <span class="flex items-center gap-1.5 text-xs text-yellow-500">
                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> Pendiente
                </span>
            @elseif($status === 'approved')
                <span class="flex items-center gap-1.5 text-xs text-green-500">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aprobado
                </span>
            @elseif($status === 'rejected')
                <span class="flex items-center gap-1.5 text-xs text-red-500">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Rechazado
                </span>
            @else
                <span class="flex items-center gap-1.5 text-xs text-gray-600">
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-700"></span> Sin docs
                </span>
            @endif

            <a href="{{ route('superadmin.users.show', $user) }}"
               class="text-xs text-gray-600 hover:text-white transition-colors opacity-0 group-hover:opacity-100">
                Ver →
            </a>
        </div>

    </div>
    @empty
    <div class="py-20 text-center">
        <p class="text-gray-700 text-sm">No hay usuarios en esta categoría.</p>
    </div>
    @endforelse

    <!-- Paginación -->
    @if($users->hasPages())
    <div class="mt-8 flex items-center justify-between text-xs text-gray-600">
        <span>{{ $users->firstItem() }}–{{ $users->lastItem() }} de {{ $users->total() }}</span>
        <div class="flex items-center gap-4">
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
