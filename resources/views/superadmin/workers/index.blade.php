@extends('superadmin.layout')

@section('title', 'Trabajadores')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8">
    <div class="flex items-end justify-between mb-8">
        <div>
            <p class="text-xs text-gray-600 dark:text-gray-100 uppercase tracking-widest mb-1">Gestión</p>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Trabajadores</h1>
        </div>
        <a href="{{ route('superadmin.workers.create') }}"
            class="inline-flex items-center gap-2 px-0 py-2 bg-transparent border-0 border-b border-orange-300 dark:border-orange-800 text-orange-500 text-xs font-bold hover:border-orange-500 dark:hover:border-orange-500 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuevo trabajador
        </a>
    </div>

    @if(session('success'))
    <div class="mb-6 flex items-center gap-3 text-sm text-green-500">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif

    @if($workers->count() === 0)
    <div class="text-center py-20">
        <p class="text-sm font-medium text-gray-400">Sin trabajadores</p>
        <p class="text-xs text-gray-300 mt-1">Crea tu primer trabajador para que acceda al panel</p>
    </div>
    @else
    <div class="space-y-2">
        @foreach($workers as $worker)
        <div class="flex items-center gap-4 p-4 bg-white dark:bg-gray-900/60 rounded-xl border border-gray-100 dark:border-gray-800/50 hover:border-gray-200 dark:hover:border-gray-700 transition-colors group">
            <div class="w-10 h-10 rounded-full bg-orange-100 dark:bg-orange-950/30 flex items-center justify-center flex-shrink-0">
                <span class="text-sm font-bold text-orange-600 dark:text-orange-400">{{ strtoupper(substr($worker->name, 0, 1)) }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $worker->name }}</p>
                <div class="flex items-center gap-3 mt-0.5 text-[11px] text-gray-400">
                    <span>{{ $worker->email }}</span>
                    <span class="w-px h-3 bg-gray-200 dark:bg-gray-700"></span>
                    <span>DNI: {{ $worker->dni }}</span>
                    @if($worker->phone)
                    <span class="w-px h-3 bg-gray-200 dark:bg-gray-700"></span>
                    <span>{{ $worker->phone }}</span>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                @php
                    $permCount = $worker->workerPermissions()->where('can_view', true)->count();
                    $totalSections = count(\App\Models\WorkerPermission::availableSections());
                @endphp
                <span class="px-2 py-1 rounded-md text-[10px] font-bold {{ $permCount > 0 ? 'bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-400' }}">
                    {{ $permCount }}/{{ $totalSections }} módulos
                </span>
                <a href="{{ route('superadmin.workers.edit', $worker) }}"
                    class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors opacity-0 group-hover:opacity-100"
                    title="Editar">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </a>
                <form action="{{ route('superadmin.workers.destroy', $worker) }}" method="POST" onsubmit="return confirm('¿Eliminar este trabajador?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-950/20 transition-colors opacity-0 group-hover:opacity-100" title="Eliminar">
                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
