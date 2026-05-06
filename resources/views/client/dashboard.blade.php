@extends('client.layout')

@section('title', 'Inicio')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8">

    <!-- Saludo -->
    <div class="mb-8">
        <p class="text-xs text-gray-400 uppercase tracking-widest mb-1">Bienvenido</p>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 tracking-tight">
            Hola, {{ $user->first_name ?? $user->name }} 👋
        </h1>
        <p class="text-sm text-gray-400 mt-1">Tu cuenta está activa y verificada.</p>
    </div>

    <!-- Estado de cuenta -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-3">Estado</p>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                <span class="text-sm font-semibold text-green-600">Verificado</span>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-3">Préstamos activos</p>
            <p class="text-2xl font-bold text-gray-900">0</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-3">Miembro desde</p>
            <p class="text-sm font-semibold text-gray-900">{{ $user->created_at->format('d M Y') }}</p>
        </div>
    </div>

    <!-- Perfil -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Datos personales -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <p class="text-xs text-gray-400 uppercase tracking-widest mb-5">Datos personales</p>
            <div class="space-y-3">
                @foreach([
                    'Nombres'      => $user->first_name,
                    'Apellidos'    => $user->last_name,
                    'DNI'          => $user->dni,
                    'Teléfono'     => $user->phone,
                    'Correo'       => $user->email,
                ] as $label => $value)
                <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                    <span class="text-xs text-gray-400">{{ $label }}</span>
                    <span class="text-xs font-medium text-gray-900">{{ $value ?? '—' }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Dirección -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <p class="text-xs text-gray-400 uppercase tracking-widest mb-5">Dirección</p>
            <div class="space-y-3">
                @foreach([
                    'Departamento' => $user->department,
                    'Provincia'    => $user->province,
                    'Distrito'     => $user->district,
                    'Dirección'    => $user->address,
                ] as $label => $value)
                <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                    <span class="text-xs text-gray-400">{{ $label }}</span>
                    <span class="text-xs font-medium text-gray-900 text-right max-w-[200px]">{{ $value ?? '—' }}</span>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- Banner préstamos -->
    <div class="mt-6 bg-gray-900 rounded-2xl p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Próximamente</p>
            <h2 class="text-lg font-bold text-white">Solicita tu préstamo</h2>
            <p class="text-sm text-gray-400 mt-1">Pronto podrás gestionar tus préstamos desde aquí.</p>
        </div>
        <button disabled class="flex-shrink-0 px-5 py-2.5 rounded-xl bg-white/10 text-white text-sm font-medium cursor-not-allowed opacity-50">
            Disponible pronto
        </button>
    </div>

</div>
@endsection
