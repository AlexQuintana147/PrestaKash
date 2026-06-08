@extends('client.layout')

@section('title', 'Invertir')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8 max-w-5xl">

    <!-- Header -->
    <div class="mb-12">
        <p class="text-xs text-gray-600 dark:text-gray-100 uppercase tracking-widest mb-0.5">Oportunidad</p>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Invertir en PrestaKash</h1>
    </div>

    <!-- Hero Card -->
    <div class="mb-16">
        <div class="max-w-2xl">
            <h2 class="text-3xl md:text-5xl font-bold text-gray-900 dark:text-white leading-tight mb-6">
                Haz crecer tu patrimonio <span class="text-green-600 dark:text-green-400 text-nowrap">con seguridad.</span>
            </h2>
            <p class="text-gray-500 dark:text-gray-100 text-base md:text-lg leading-relaxed mb-8">
                Únete como inversor y financia préstamos evaluados bajo rigurosos estándares. Obtén rentabilidades superiores y genera ingresos pasivos mes a mes.
            </p>
            
            @if(session('success'))
                <div class="mb-8 flex items-center gap-2 text-sm text-green-500 font-bold bg-green-50 dark:bg-green-500/10 p-4 rounded-2xl border border-green-100 dark:border-green-500/20">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('info'))
                <div class="mb-8 flex items-center gap-2 text-sm text-blue-500 font-bold bg-blue-50 dark:bg-blue-500/10 p-4 rounded-2xl border border-blue-100 dark:border-blue-500/20">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('info') }}
                </div>
            @endif

            @if($rejectionReason)
                <div class="mb-8 bg-red-50 dark:bg-red-500/10 p-5 rounded-2xl border border-red-100 dark:border-red-500/20">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-red-600 dark:text-red-400 mb-1">Tu solicitud anterior fue rechazada</p>
                            <p class="text-sm text-red-500 dark:text-red-300 mb-3">Motivo: {{ $rejectionReason }}</p>
                            <a href="{{ route('client.invest.apply') }}" class="inline-flex items-center gap-2 text-sm font-bold text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition-colors">
                                Volver a postularme
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            @if(!$rejectionReason)
            <a href="{{ route('client.invest.apply') }}" class="group inline-flex items-center gap-2 text-sm font-bold text-gray-900 dark:text-white hover:text-green-600 dark:hover:text-green-400 transition-colors">
                Postularme como Inversor
                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
            @endif
        </div>
    </div>
</div>
@endsection
