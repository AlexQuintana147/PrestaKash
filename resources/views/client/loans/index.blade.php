@extends('client.layout')

@section('title', 'Gestionar Préstamos')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8">

    <!-- Header -->
    <div class="mb-12">
        <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-0.5">Panel</p>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Gestionar Préstamos</h1>
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
    <div class="grid grid-cols-1 md:grid-cols-2 gap-px bg-gray-200 dark:bg-gray-800 rounded-2xl overflow-hidden mb-12">

        <!-- Pedir préstamo -->
        <div class="bg-gray-50 dark:bg-gray-950 p-8 flex flex-col gap-6">
            <div class="flex items-center justify-between">
                <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span class="text-xs text-gray-400 dark:text-gray-600">Pronto</span>
            </div>
            <div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-1.5">Pedir un préstamo</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Solicita financiamiento rápido y seguro. Elige el monto y plazo que mejor se adapte a ti.</p>
            </div>
            <button disabled class="self-start text-xs text-gray-400 dark:text-gray-600 cursor-not-allowed">Disponible pronto</button>
        </div>

        <!-- Historial crediticio -->
        <div class="bg-gray-50 dark:bg-gray-950 p-8 flex flex-col gap-6">
            <div class="flex items-center justify-between">
                <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span class="text-xs text-gray-400 dark:text-gray-600">Pronto</span>
            </div>
            <div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-1.5">Historial crediticio</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Consulta tus préstamos anteriores, pagos realizados y tu estado crediticio actual.</p>
            </div>
            <button disabled class="self-start text-xs text-gray-400 dark:text-gray-600 cursor-not-allowed">Disponible pronto</button>
        </div>

    </div>

    <!-- Perfil -->
    <div>
        <div class="flex items-center justify-between mb-6">
            <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-widest">Tu perfil de préstamos</p>
            <a href="{{ route('client.loans.profile.edit') }}"
               class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                Editar →
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-y-6">
            @foreach([
                'Institución'  => $profile->institution,
                'Carrera'      => $profile->career,
                'Situación'    => $profile->academic_status,
                'Estado civil' => match($profile->marital_status) {
                    'soltero'           => 'Soltero/a',
                    'casado_separado'   => 'Casado — bienes sep.',
                    'casado_compartido' => 'Casado — bienes comp.',
                    default             => $profile->marital_status,
                },
                'Hijos'        => $profile->has_children ? 'Sí' : 'No',
                'Trabaja'      => $profile->is_employed ? 'Sí' : 'No',
                'Instagram'    => $profile->instagram ?? '—',
            ] as $label => $value)
            <div class="border-b border-gray-100 dark:border-gray-900 pb-4">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ $label }}</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $value }}</p>
            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
