@extends('client.layout')

@section('title', 'Invertir')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8 max-w-5xl">

    <!-- Header -->
    <div class="mb-12">
        <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-0.5">Oportunidad</p>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Invertir en PrestaKash</h1>
    </div>

    <!-- Hero Card -->
    <div class="mb-16">
        <div class="max-w-2xl">
            <h2 class="text-3xl md:text-5xl font-bold text-gray-900 dark:text-white leading-tight mb-6">
                Haz crecer tu patrimonio <span class="text-green-600 dark:text-green-400 text-nowrap">con seguridad.</span>
            </h2>
            <p class="text-gray-500 dark:text-gray-400 text-base md:text-lg leading-relaxed mb-8">
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

            @if(session('error'))
                <div class="mb-8 flex items-center gap-2 text-sm text-red-500 font-bold bg-red-50 dark:bg-red-500/10 p-4 rounded-2xl border border-red-100 dark:border-red-500/20">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <a href="{{ route('client.invest.apply') }}" class="group inline-flex items-center gap-2 text-sm font-bold text-gray-900 dark:text-white hover:text-green-600 dark:hover:text-green-400 transition-colors">
                Postularme como Inversor
                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </div>

    <!-- Benefits Section -->
    <div class="mb-12">
        <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-8">Beneficios y Rentabilidad</p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-px bg-gray-100 dark:bg-gray-900 rounded-3xl overflow-hidden border border-gray-100 dark:border-gray-900 shadow-sm">
            
            <!-- Benefit 1: Earning Model -->
            <div class="bg-white dark:bg-gray-950 p-8 flex flex-col h-full">
                <h3 class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-6">Rentabilidad</h3>
                <div class="space-y-5">
                    <div class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-gray-900 dark:text-white leading-tight mb-1.5">Gana el 2% Mensual</p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed">
                                Recibe el 2% de tu inversión cada 30 días. Por ejemplo: invierte S/ 20,000 y recibe S/ 400 mensuales.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Benefit 2: Security -->
            <div class="bg-white dark:bg-gray-950 p-8 flex flex-col h-full">
                <h3 class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-6">Seguridad</h3>
                <div class="space-y-5">
                    <div class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-gray-900 dark:text-white leading-tight mb-1.5">Retorno de Capital</p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed">
                                Tu inversión principal está protegida. Al finalizar el periodo, recuperas el 100% de tu capital íntegro.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Benefit 3: Transparency -->
            <div class="bg-white dark:bg-gray-950 p-8 flex flex-col h-full">
                <h3 class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-6">Confianza</h3>
                <div class="space-y-5">
                    <div class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-gray-900 dark:text-white leading-tight mb-1.5">Pagos Puntuales</p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed">
                                Los intereses generados se depositan automáticamente cada mes directamente a tu cuenta bancaria.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Footer Info -->
    <div class="flex items-center gap-2 text-[10px] text-gray-400 dark:text-gray-600 uppercase tracking-widest font-medium">
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Sujeto a términos y condiciones de inversión.
    </div>

</div>
@endsection
