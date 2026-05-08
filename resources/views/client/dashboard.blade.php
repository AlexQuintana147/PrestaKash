@extends('client.layout')

@section('title', 'Inicio')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-10">

    <!-- Saludo y Perfil Minimalista -->
    <div class="flex flex-col md:flex-row md:items-end gap-6 md:gap-10 mb-16">
        @php
            $selfie = $user->documents?->selfie;
            $profilePhoto = $selfie ? Storage::url($selfie) : ($user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=e5e7eb&color=6b7280');
        @endphp
        
        <div class="relative inline-block">
            <div class="w-24 h-24 md:w-32 md:h-32 rounded-2xl overflow-hidden grayscale hover:grayscale-0 transition-all duration-700">
                <img src="{{ $profilePhoto }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
            </div>
            <div class="absolute -bottom-1 -right-1 bg-green-500 w-3 h-3 rounded-full border-2 border-white dark:border-gray-950"></div>
        </div>

        <div class="flex-1">
            <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-[0.3em] mb-2">Identidad Verificada</p>
            <h1 class="text-4xl md:text-5xl font-light text-gray-900 dark:text-white tracking-tighter mb-6">
                {{ $user->first_name ?? $user->name }}
            </h1>
            
            <div class="flex flex-wrap gap-x-8 gap-y-4">
                <a href="#" class="group inline-flex items-center gap-2 text-[10px] font-bold text-gray-900 dark:text-white uppercase tracking-widest transition-opacity hover:opacity-70">
                    <span class="border-b-2 border-gray-900 dark:border-white pb-1">Gestionar cuentas de banco</span>
                </a>
                <a href="{{ route('client.loans.profile.edit') }}" class="group inline-flex items-center gap-2 text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest transition-opacity hover:opacity-70">
                    <span class="border-b-2 border-transparent group-hover:border-gray-400 dark:group-hover:border-gray-500 pb-1 transition-colors">Editar Perfil</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Estado rápido -->
    <div class="flex items-center gap-6 mb-12 pb-8 border-b border-gray-100 dark:border-gray-900">
        <div>
            <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Estado</p>
            <div class="flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                <span class="text-sm font-medium text-green-600 dark:text-green-400">Verificado</span>
            </div>
        </div>
        <div class="w-px h-8 bg-gray-100 dark:bg-gray-900"></div>
        <div>
            <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Préstamos activos</p>
            <p class="text-sm font-medium text-gray-900 dark:text-white">0</p>
        </div>
        <div class="w-px h-8 bg-gray-100 dark:bg-gray-900"></div>
        <div>
            <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Miembro desde</p>
            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->created_at->format('d M Y') }}</p>
        </div>
    </div>

    <!-- Datos personales -->
    <div class="mb-10">
        <p class="text-xs text-gray-600 dark:text-gray-600 uppercase tracking-widest mb-5">Datos personales</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-16 gap-y-0">
            @foreach([
                'Nombres'   => $user->first_name,
                'Apellidos' => $user->last_name,
                'DNI'       => $user->dni,
                'Teléfono'  => $user->phone,
                'Correo'    => $user->email,
            ] as $label => $value)
            <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-900">
                <span class="text-xs text-gray-600 dark:text-gray-400">{{ $label }}</span>
                <span class="text-xs font-medium text-gray-900 dark:text-white">{{ $value ?? '—' }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Dirección -->
    <div class="mb-10">
        <p class="text-xs text-gray-600 dark:text-gray-600 uppercase tracking-widest mb-5">Dirección</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-16 gap-y-0">
            @foreach([
                'Departamento' => $department,
                'Provincia'    => $province,
                'Distrito'     => $district,
                'Dirección'    => $user->address,
            ] as $label => $value)
            <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-900">
                <span class="text-xs text-gray-600 dark:text-gray-400">{{ $label }}</span>
                <span class="text-xs font-medium text-gray-900 dark:text-white text-right max-w-[220px]">{{ $value ?? '—' }}</span>
            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
