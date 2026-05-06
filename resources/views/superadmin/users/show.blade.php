@extends('superadmin.layout')

@section('title', $user->name)

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8">

    <!-- Header -->
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('superadmin.users.index') }}"
           class="text-gray-400 dark:text-gray-600 hover:text-gray-900 dark:hover:text-white transition-colors flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=e5e7eb&color=6b7280&size=64' }}"
             class="w-9 h-9 rounded-full object-cover bg-gray-200 dark:bg-gray-900 flex-shrink-0">
        <div class="min-w-0 flex-1">
            <h1 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white leading-tight truncate">{{ $user->name }}</h1>
            <p class="text-xs text-gray-400 dark:text-gray-600 truncate">{{ $user->email }}</p>
        </div>

        @if($user->documents)
        @php $status = $user->documents->status @endphp
        <div class="flex-shrink-0">
            @if($status === 'pending')
                <span class="flex items-center gap-1.5 text-xs text-yellow-500">
                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                    <span class="hidden sm:inline">Pendiente</span>
                </span>
            @elseif($status === 'approved')
                <span class="flex items-center gap-1.5 text-xs text-green-500">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                    <span class="hidden sm:inline">Aprobado</span>
                </span>
            @elseif($status === 'rejected')
                <span class="flex items-center gap-1.5 text-xs text-red-500">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                    <span class="hidden sm:inline">Rechazado</span>
                </span>
            @endif
        </div>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 text-sm text-green-500">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col md:grid md:grid-cols-12 gap-8 md:gap-10">

        <!-- Datos + acciones -->
        <div class="md:col-span-4 space-y-8">

            <div>
                <p class="text-xs text-gray-400 dark:text-gray-600 uppercase tracking-widest mb-4">Datos personales</p>
                <div class="space-y-3">
                    @foreach([
                        'Nombres'      => $user->first_name,
                        'Apellidos'    => $user->last_name,
                        'DNI'          => $user->dni,
                        'Teléfono'     => $user->phone,
                        'Departamento' => $department,
                        'Provincia'    => $province,
                        'Distrito'     => $district,
                        'Dirección'    => $user->address,
                        'Registro'     => $user->created_at->format('d/m/Y'),
                    ] as $label => $value)
                    <div class="flex justify-between items-start border-b border-gray-100 dark:border-gray-900 pb-3">
                        <span class="text-xs text-gray-400 dark:text-gray-600 flex-shrink-0">{{ $label }}</span>
                        <span class="text-xs text-gray-900 dark:text-white text-right ml-4">{{ $value ?? '—' }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            @if($user->documents)
            <div>
                <p class="text-xs text-gray-400 dark:text-gray-600 uppercase tracking-widest mb-4">Acciones</p>
                <div class="flex flex-col gap-2">
                    @if($user->documents->status !== 'approved')
                    <form action="{{ route('superadmin.users.approve', $user) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full py-2.5 text-sm font-semibold rounded-lg bg-gray-900 dark:bg-white text-white dark:text-black hover:bg-gray-700 dark:hover:bg-gray-100 transition-colors">
                            Aprobar
                        </button>
                    </form>
                    @endif

                    @if($user->documents->status !== 'rejected')
                    <form action="{{ route('superadmin.users.reject', $user) }}" method="POST">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('¿Rechazar a este usuario?')"
                                class="w-full py-2.5 text-sm font-medium rounded-lg border border-gray-200 dark:border-gray-800 text-gray-500 dark:text-gray-500 hover:border-red-300 dark:hover:border-red-900 hover:text-red-500 transition-colors">
                            Rechazar
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endif

        </div>

        <!-- Documentos -->
        <div class="md:col-span-8">
            <p class="text-xs text-gray-400 dark:text-gray-600 uppercase tracking-widest mb-5">Documentos</p>

            @if($user->documents)
            <div class="grid grid-cols-2 gap-3 md:gap-5">
                @foreach([
                    'Selfie'              => $user->documents->selfie,
                    'DNI — Frontal'       => $user->documents->dni_front,
                    'DNI — Posterior'     => $user->documents->dni_back,
                    'Recibo de servicios' => $user->documents->utility_bill,
                ] as $label => $path)
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-600 mb-2">{{ $label }}</p>
                    @if($path)
                        <a href="{{ Storage::url($path) }}" target="_blank" class="block group">
                            <div class="relative overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-900" style="aspect-ratio: 4/3;">
                                <img src="{{ Storage::url($path) }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    @else
                        <div class="rounded-lg bg-gray-100 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 flex items-center justify-center text-gray-400 dark:text-gray-700 text-xs" style="aspect-ratio: 4/3;">
                            Sin imagen
                        </div>
                    @endif
                </div>
                @endforeach
            </div>
            @else
                <p class="text-sm text-gray-400 dark:text-gray-700">Este usuario no ha subido documentos.</p>
            @endif
        </div>

    </div>
</div>
@endsection
