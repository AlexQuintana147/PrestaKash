@extends('client.layout')

@section('title', 'Gestionar Préstamos')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-10">

    <div class="mb-10">
        <p class="text-xs text-gray-400 dark:text-gray-400 uppercase tracking-widest mb-1">Panel</p>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Gestionar Préstamos</h1>
    </div>

    @if(session('success'))
        <div class="mb-6 flex items-center gap-2 text-sm text-green-500">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Banner próximamente -->
    <div class="rounded-2xl bg-gray-900 dark:bg-gray-800 px-6 py-8 text-center max-w-md mx-auto mt-20">
        <p class="text-xs text-gray-500 uppercase tracking-widest mb-2">Próximamente</p>
        <h2 class="text-xl font-bold text-white mb-2">Solicitud de préstamos</h2>
        <p class="text-sm text-gray-400">Pronto podrás solicitar y gestionar tus préstamos desde aquí.</p>
    </div>

</div>
@endsection
