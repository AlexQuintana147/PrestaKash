@extends('superadmin.layout')

@section('title', $user->name)

@section('content')
<div class="min-h-screen px-10 py-10">

    <!-- Header -->
    <div class="flex items-center gap-4 mb-10">
        <a href="{{ route('superadmin.users.index') }}"
           class="text-gray-600 hover:text-white transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex items-center gap-3">
            <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=111827&color=6b7280&size=64' }}"
                 class="w-10 h-10 rounded-full object-cover bg-gray-900">
            <div>
                <h1 class="text-xl font-bold text-white leading-tight">{{ $user->name }}</h1>
                <p class="text-xs text-gray-600">{{ $user->email }}</p>
            </div>
        </div>

        <!-- Badge estado -->
        @if($user->documents)
        @php $status = $user->documents->status @endphp
        <div class="ml-auto">
            @if($status === 'pending')
                <span class="flex items-center gap-1.5 text-xs text-yellow-500">
                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> Pendiente de revisión
                </span>
            @elseif($status === 'approved')
                <span class="flex items-center gap-1.5 text-xs text-green-500">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aprobado
                </span>
            @elseif($status === 'rejected')
                <span class="flex items-center gap-1.5 text-xs text-red-500">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Rechazado
                </span>
            @endif
        </div>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-8 flex items-center gap-3 text-sm text-green-400">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-12 gap-10">

        <!-- Columna izquierda: datos + acciones -->
        <div class="col-span-4 space-y-8">

            <!-- Datos personales -->
            <div>
                <p class="text-xs text-gray-600 uppercase tracking-widest mb-4">Datos personales</p>
                <div class="space-y-3">
                    @foreach([
                        'DNI'          => $user->dni,
                        'Teléfono'     => $user->phone,
                        'Departamento' => $user->department,
                        'Provincia'    => $user->province,
                        'Distrito'     => $user->district,
                        'Dirección'    => $user->address,
                        'Registro'     => $user->created_at->format('d/m/Y'),
                    ] as $label => $value)
                    <div class="flex justify-between items-start border-b border-gray-900 pb-3">
                        <span class="text-xs text-gray-600">{{ $label }}</span>
                        <span class="text-xs text-white text-right max-w-[200px]">{{ $value ?? '—' }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Acciones -->
            @if($user->documents)
            <div>
                <p class="text-xs text-gray-600 uppercase tracking-widest mb-4">Acciones</p>
                <div class="space-y-2">
                    @if($user->documents->status !== 'approved')
                    <form action="{{ route('superadmin.users.approve', $user) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full py-2.5 text-sm font-semibold rounded-lg bg-white text-black hover:bg-gray-100 transition-colors">
                            Aprobar
                        </button>
                    </form>
                    @endif

                    @if($user->documents->status !== 'rejected')
                    <form action="{{ route('superadmin.users.reject', $user) }}" method="POST">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('¿Rechazar a este usuario?')"
                                class="w-full py-2.5 text-sm font-medium rounded-lg border border-gray-800 text-gray-500 hover:border-red-900 hover:text-red-500 transition-colors">
                            Rechazar
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endif

        </div>

        <!-- Columna derecha: documentos -->
        <div class="col-span-8">
            <p class="text-xs text-gray-600 uppercase tracking-widest mb-6">Documentos</p>

            @if($user->documents)
            <div class="grid grid-cols-2 gap-5">

                @foreach([
                    'Selfie'             => $user->documents->selfie,
                    'DNI — Frontal'      => $user->documents->dni_front,
                    'DNI — Posterior'    => $user->documents->dni_back,
                    'Recibo de servicios'=> $user->documents->utility_bill,
                ] as $label => $path)
                <div>
                    <p class="text-xs text-gray-600 mb-2">{{ $label }}</p>
                    @if($path)
                        <a href="{{ Storage::url($path) }}" target="_blank" class="block group">
                            <div class="relative overflow-hidden rounded-lg bg-gray-900" style="aspect-ratio: 4/3;">
                                <img src="{{ Storage::url($path) }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    @else
                        <div class="rounded-lg bg-gray-900 border border-gray-800 flex items-center justify-center text-gray-700 text-xs" style="aspect-ratio: 4/3;">
                            Sin imagen
                        </div>
                    @endif
                </div>
                @endforeach

            </div>
            @else
                <p class="text-sm text-gray-700">Este usuario no ha subido documentos.</p>
            @endif
        </div>

    </div>
</div>
@endsection
