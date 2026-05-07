@extends('client.layout')

@section('title', 'Gestionar Préstamos')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8">

    <!-- Header -->
    <div class="flex items-end justify-between mb-10">
        <div>
            <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-0.5">Panel</p>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Gestionar Préstamos</h1>
        </div>
        <a href="{{ route('client.loans.profile.edit') }}"
           class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white border-b border-gray-300 dark:border-gray-600 hover:border-gray-900 dark:hover:border-white transition-colors pb-0.5">
            Editar perfil
        </a>
    </div>

    @if(session('success'))
        <div class="mb-8 flex items-center gap-2 text-sm text-green-500">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Acciones principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-12">

        <!-- Pedir préstamo -->
        <div class="group relative rounded-2xl border border-gray-200 dark:border-gray-800 p-6 hover:border-gray-400 dark:hover:border-gray-600 transition-colors cursor-not-allowed">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <span class="text-xs bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-500 px-2 py-0.5 rounded-full">Pronto</span>
            </div>
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Pedir un préstamo</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Solicita financiamiento rápido y seguro. Elige el monto y plazo que mejor se adapte a ti.</p>
        </div>

        <!-- Historial crediticio -->
        <div class="group relative rounded-2xl border border-gray-200 dark:border-gray-800 p-6 hover:border-gray-400 dark:hover:border-gray-600 transition-colors cursor-not-allowed">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <span class="text-xs bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-500 px-2 py-0.5 rounded-full">Pronto</span>
            </div>
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Historial crediticio</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Consulta tus préstamos anteriores, pagos realizados y tu estado crediticio actual.</p>
        </div>

    </div>

    <!-- Resumen perfil -->
    <div>
        <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-5">Tu perfil de préstamos</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-0">
            @foreach([
                'Institución'   => $profile->institution,
                'Carrera'       => $profile->career,
                'Situación'     => $profile->academic_status,
                'Estado civil'  => match($profile->marital_status) {
                    'soltero'            => 'Soltero/a',
                    'casado_separado'    => 'Casado — sep.',
                    'casado_compartido'  => 'Casado — comp.',
                    default              => $profile->marital_status,
                },
                'Hijos'         => $profile->has_children ? 'Sí' : 'No',
                'Trabaja'       => $profile->is_employed ? 'Sí' : 'No',
                'Instagram'     => $profile->instagram ?? '—',
            ] as $label => $value)
            <div class="py-3 border-b border-gray-100 dark:border-gray-900 pr-4">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">{{ $label }}</p>
                <p class="text-xs font-medium text-gray-900 dark:text-white truncate">{{ $value }}</p>
            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
