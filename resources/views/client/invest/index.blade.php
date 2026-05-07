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
    <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-3xl p-8 md:p-12 mb-12 shadow-sm">
        <div class="max-w-2xl">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white leading-tight mb-6">
                Haz crecer tu patrimonio <span class="text-green-600 dark:text-green-400 text-nowrap">con seguridad.</span>
            </h2>
            <p class="text-gray-500 dark:text-gray-400 text-base leading-relaxed mb-8">
                Únete como inversor y financia préstamos evaluados bajo rigurosos estándares. Obtén rentabilidades superiores y genera ingresos pasivos mes a mes.
            </p>
            <button class="bg-gray-900 dark:bg-white text-white dark:text-black px-8 py-3.5 rounded-2xl font-bold text-sm transition-all hover:scale-[1.02] active:scale-[0.98]">
                Postularme como Inversor
            </button>
        </div>
    </div>

    <!-- Benefits Section -->
    <div class="mb-12">
        <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-8">Beneficios exclusivos</p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-px bg-gray-100 dark:bg-gray-900 rounded-3xl overflow-hidden border border-gray-100 dark:border-gray-900">
            
            <!-- Benefit 1 -->
            <div class="bg-white dark:bg-gray-950 p-8 flex flex-col gap-4">
                <div class="w-10 h-10 rounded-xl bg-green-50 dark:bg-green-500/10 flex items-center justify-center text-green-600 dark:text-green-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-2">Rentabilidad Competitiva</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Tasas de retorno diseñadas para maximizar el crecimiento de tu capital de forma constante.</p>
                </div>
            </div>

            <!-- Benefit 2 -->
            <div class="bg-white dark:bg-gray-950 p-8 flex flex-col gap-4">
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-2">Riesgo Gestionado</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Filtramos cada solicitud con un sistema de evaluación propio para proteger tu inversión.</p>
                </div>
            </div>

            <!-- Benefit 3 -->
            <div class="bg-white dark:bg-gray-950 p-8 flex flex-col gap-4">
                <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-500/10 flex items-center justify-center text-purple-600 dark:text-purple-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-2">Transparencia Total</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Monitorea tus rendimientos y el estado de tus fondos en tiempo real desde tu panel.</p>
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
