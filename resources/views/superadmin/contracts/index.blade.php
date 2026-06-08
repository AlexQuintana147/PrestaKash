@extends('superadmin.layout')

@section('title', 'Contratos')

@section('content')
<div class="px-5 md:px-10 py-8">
    <div class="flex items-end justify-between mb-8">
        <div>
            <p class="text-xs text-gray-600 dark:text-gray-100 uppercase tracking-widest mb-1">Panel Administrativo</p>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Contratos</h1>
        </div>
        <a href="{{ route('superadmin.contracts.create') }}" class="flex items-center gap-2 px-5 py-2.5 bg-black text-white dark:bg-white dark:text-black rounded-xl text-xs font-bold uppercase tracking-widest hover:opacity-80 transition-opacity">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuevo Contrato
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 text-sm text-green-500">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-4">
        @forelse($contracts as $contract)
            <a href="{{ route('superadmin.contracts.edit', $contract) }}"
               class="group flex items-center justify-between p-5 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl hover:border-gray-300 dark:hover:border-gray-700 transition-all">
                <div class="flex items-center gap-4">
                    <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-900 group-hover:bg-black dark:group-hover:bg-white transition-colors">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-100 group-hover:text-white dark:group-hover:text-black transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">{{ $contract->name }}</h3>
                        @if($contract->description)
                            <p class="text-xs text-gray-500 dark:text-gray-100 mt-0.5">{{ Str::limit($contract->description, 80) }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-xs font-mono px-2 py-1 rounded-md {{ $contract->is_active ? 'bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-100' }}">
                        {{ $contract->is_active ? 'Activo' : 'Inactivo' }}
                    </span>
                    <span class="text-[10px] text-gray-500 dark:text-gray-100 font-medium">{{ $contract->clauses_count }} cláusula{{ $contract->clauses_count != 1 ? 's' : '' }}</span>
                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-100 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>
        @empty
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <svg class="w-12 h-12 text-gray-400 dark:text-gray-100 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="text-sm text-gray-500 dark:text-gray-100 font-medium">No hay contratos aún</p>
                <a href="{{ route('superadmin.contracts.create') }}" class="mt-3 text-xs text-blue-500 hover:text-blue-600 font-medium">Crear primer contrato</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
