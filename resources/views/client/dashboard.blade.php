@extends('client.layout')

@section('title', 'Inicio')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-10">

    <!-- Saludo -->
    <div class="mb-10">
        <p class="text-xs text-gray-400 dark:text-gray-600 uppercase tracking-widest mb-1">Panel</p>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
            Hola, {{ $user->first_name ?? $user->name }} 👋
        </h1>
    </div>

    <!-- Estado rápido -->
    <div class="flex items-center gap-6 mb-12 pb-8 border-b border-gray-100 dark:border-gray-900">
        <div>
            <p class="text-xs text-gray-400 dark:text-gray-600 mb-1">Estado</p>
            <div class="flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                <span class="text-sm font-medium text-green-600 dark:text-green-400">Verificado</span>
            </div>
        </div>
        <div class="w-px h-8 bg-gray-100 dark:bg-gray-900"></div>
        <div>
            <p class="text-xs text-gray-400 dark:text-gray-600 mb-1">Préstamos activos</p>
            <p class="text-sm font-medium text-gray-900 dark:text-white">0</p>
        </div>
        <div class="w-px h-8 bg-gray-100 dark:bg-gray-900"></div>
        <div>
            <p class="text-xs text-gray-400 dark:text-gray-600 mb-1">Miembro desde</p>
            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->created_at->format('d M Y') }}</p>
        </div>
    </div>

    <!-- Datos personales -->
    <div class="mb-10">
        <p class="text-xs text-gray-400 dark:text-gray-600 uppercase tracking-widest mb-5">Datos personales</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-16 gap-y-0">
            @foreach([
                'Nombres'   => $user->first_name,
                'Apellidos' => $user->last_name,
                'DNI'       => $user->dni,
                'Teléfono'  => $user->phone,
                'Correo'    => $user->email,
            ] as $label => $value)
            <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-900">
                <span class="text-xs text-gray-400 dark:text-gray-600">{{ $label }}</span>
                <span class="text-xs font-medium text-gray-900 dark:text-white">{{ $value ?? '—' }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Dirección -->
    <div class="mb-10">
        <p class="text-xs text-gray-400 dark:text-gray-600 uppercase tracking-widest mb-5">Dirección</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-16 gap-y-0">
            @foreach([
                'Departamento' => $department,
                'Provincia'    => $province,
                'Distrito'     => $district,
                'Dirección'    => $user->address,
            ] as $label => $value)
            <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-900">
                <span class="text-xs text-gray-400 dark:text-gray-600">{{ $label }}</span>
                <span class="text-xs font-medium text-gray-900 dark:text-white text-right max-w-[220px]">{{ $value ?? '—' }}</span>
            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
