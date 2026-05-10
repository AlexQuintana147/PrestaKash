@extends('superadmin.layout')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8">

    <!-- Header -->
    <div class="flex items-end justify-between mb-8">
        <div>
            <p class="text-xs text-gray-600 dark:text-gray-600 uppercase tracking-widest mb-1">Panel</p>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Clientes</h1>
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
                    <a href="{{ route('superadmin.users.index', ['filter' => $key]) }}"
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
            <div class="grid grid-cols-2 gap-2 w-2/3 mx-auto">
                @foreach(['rejected' => 'Rechazados', 'blocked' => 'Bloqueados'] as $key => $label)
                    <a href="{{ route('superadmin.users.index', ['filter' => $key]) }}"
                       class="px-3 py-2 rounded-lg text-xs font-medium text-center transition-colors
                              {{ $filter === $key
                                  ? ($key === 'blocked' ? 'bg-red-50 dark:bg-red-500/10 text-red-500' : 'bg-gray-200 dark:bg-gray-800 text-gray-900 dark:text-white')
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
            @foreach(['all' => 'Todos', 'pending' => 'Pendientes', 'approved' => 'Aprobados', 'rejected' => 'Rechazados', 'blocked' => 'Bloqueados'] as $key => $label)
                <a href="{{ route('superadmin.users.index', ['filter' => $key]) }}"
                   class="relative flex-shrink-0 px-4 py-3 text-sm font-medium transition-colors
                          {{ $filter === $key
                              ? ($key === 'blocked' ? 'text-red-500' : 'text-gray-900 dark:text-white')
                              : 'text-gray-600 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-400' }}">
                    {{ $label }}
                    @if($counts[$key] > 0)
                        <span class="ml-1.5 text-xs {{ $filter === $key ? ($key === 'blocked' ? 'text-red-400' : 'text-gray-500 dark:text-gray-400') : 'text-gray-300 dark:text-gray-500' }}">{{ $counts[$key] }}</span>
                    @endif
                    @if($filter === $key)
                        <span class="absolute bottom-0 left-0 right-0 h-px {{ $key === 'blocked' ? 'bg-red-500' : 'bg-gray-900 dark:bg-white' }}"></span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    <!-- Cabecera de columnas (solo desktop) -->
    @if($filter !== 'blocked')
    <div class="hidden md:grid py-2 mb-1 px-2 text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider items-center" style="grid-template-columns: 1fr 100px 110px 80px 100px 90px 90px 24px;">
        <span>Usuario</span>
        <span class="text-center">DNI</span>
        <span class="text-center">Teléfono</span>
        <span class="text-center">Tasa %</span>
        <span class="text-center">Registro</span>
        <span class="text-center">Estado</span>
        <span class="text-center">Referidor</span>
        <span></span>
    </div>
    @endif

    @if($filter === 'blocked')
    <!-- Lista de bloqueados -->
    <div class="hidden md:grid py-2 mb-1 px-2 text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider items-center" style="grid-template-columns: 1fr 120px 130px 120px 24px;">
        <span>Usuario</span>
        <span class="text-center">DNI</span>
        <span class="text-center">Teléfono</span>
        <span class="text-center">Bloqueado</span>
        <span></span>
    </div>

    @forelse($blockedUsers as $blocked)
    <div class="flex md:grid items-center py-4 border-b border-gray-200 dark:border-gray-900 -mx-2 px-2"
         style="grid-template-columns: 1fr 120px 130px 120px 24px;">

        <div class="flex items-center gap-3 min-w-0">
            <div class="w-9 h-9 rounded-full bg-gray-200 dark:bg-gray-800 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $blocked->name }}</p>
                <p class="text-xs text-gray-600 dark:text-gray-400 truncate">{{ $blocked->email }}</p>
                @if($blocked->reason)
                    <p class="text-xs text-red-400 truncate mt-0.5">{{ $blocked->reason }}</p>
                @endif
            </div>
        </div>

        <span class="hidden md:block text-xs text-gray-900 dark:text-white text-center">{{ $blocked->dni ?? '—' }}</span>
        <span class="hidden md:block text-xs text-gray-900 dark:text-white text-center">{{ $blocked->phone ?? '—' }}</span>
        <span class="hidden md:block text-xs text-gray-900 dark:text-white text-center">{{ $blocked->created_at->format('d/m/Y') }}</span>

        <div class="ml-auto md:ml-0 flex justify-center">
            <span class="w-1.5 h-1.5 rounded-full bg-gray-400 dark:bg-gray-600"></span>
        </div>
    </div>
    @empty
    <div class="py-20 text-center">
        <p class="text-gray-400 dark:text-gray-500 text-sm">No hay usuarios bloqueados.</p>
    </div>
    @endforelse

    @else
    <!-- Lista normal -->
    @forelse($users as $user)
    @php $status = $user->documents?->status @endphp
    <div class="flex md:grid items-center py-4 border-b border-gray-200 dark:border-gray-900 hover:bg-gray-50 dark:hover:bg-gray-900/30 -mx-2 px-2 rounded-lg transition-colors group"
       style="grid-template-columns: 1fr 100px 110px 80px 100px 90px 90px 24px;">

        <a href="{{ route('superadmin.users.show', $user) }}" class="flex items-center gap-3 min-w-0">
            @php
                $svgColor = match($status) {
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
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $user->name }}</p>
                <p class="text-xs text-gray-600 dark:text-gray-400 truncate">{{ $user->email }}</p>
            </div>
        </a>

        <span class="hidden md:block text-xs text-gray-900 dark:text-white text-center">{{ $user->dni ?? '—' }}</span>
        <span class="hidden md:block text-xs text-gray-900 dark:text-white text-center">{{ $user->phone ?? '—' }}</span>
        
        <!-- Tasa % -->
        <div class="hidden md:flex items-center justify-center">
            <form action="{{ route('superadmin.users.update-field', $user) }}" method="POST" class="flex items-center gap-1">
                @csrf
                @method('PATCH')
                <input type="hidden" name="field" value="loan_interest_rate">
                <input type="number" name="value" value="{{ $user->loan_interest_rate ?? 20 }}" 
                       class="w-10 text-center text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:outline-none focus:border-blue-500 p-0"
                       step="0.1" min="0" max="100">
                <button type="submit" class="text-blue-500 hover:text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>
            </form>
        </div>

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
                <span class="text-xs text-gray-600 dark:text-gray-400 hidden sm:inline">Sin docs</span>
            @endif
        </div>

        <!-- Referido por -->
        <div class="hidden md:flex items-center justify-center h-full">
            @if($user->referrer)
                <span class="text-xs text-gray-600 dark:text-gray-400 font-medium" title="{{ $user->referrer->name }}">
                    {{ $user->referrer->first_name }} {{ Str::substr($user->referrer->last_name, 0, 1) }}.
                </span>
            @else
                <span class="text-xs text-gray-400 dark:text-gray-500">—</span>
            @endif
        </div>

        <a href="{{ route('superadmin.users.show', $user) }}" class="flex justify-end">
            <svg class="w-3.5 h-3.5 text-gray-300 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-400 transition-colors hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>

    </div>
    @empty
    <div class="py-20 text-center">
        <p class="text-gray-400 dark:text-gray-500 text-sm">No hay usuarios en esta categoría.</p>
    </div>
    @endforelse

    @endif

    <!-- Paginación -->
    @if($filter !== 'blocked' && $users->hasPages())
    <div class="mt-8 flex items-center justify-between text-xs text-gray-600 dark:text-gray-400">
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
