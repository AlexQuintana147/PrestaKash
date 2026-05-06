@extends('superadmin.layout')

@section('title', 'Detalle de Usuario')

@section('content')
<div class="p-8">

    <!-- Header -->
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('superadmin.users.index') }}" class="text-gray-500 hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">{{ $user->name }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $user->email }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-green-500/10 border border-green-500/20 text-sm text-green-400">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-3 gap-6">

        <!-- Datos personales -->
        <div class="col-span-1 space-y-4">
            <div class="bg-gray-900 rounded-xl border border-gray-800 p-5">
                <div class="flex items-center gap-4 mb-5">
                    <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=1f2937&color=fff' }}"
                         class="w-14 h-14 rounded-full object-cover bg-gray-800">
                    <div>
                        <p class="font-semibold text-white">{{ $user->name }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">DNI</span>
                        <span class="text-white font-medium">{{ $user->dni ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Teléfono</span>
                        <span class="text-white font-medium">{{ $user->phone ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Departamento</span>
                        <span class="text-white font-medium">{{ $user->department ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Provincia</span>
                        <span class="text-white font-medium">{{ $user->province ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Distrito</span>
                        <span class="text-white font-medium">{{ $user->district ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Dirección</span>
                        <span class="text-white font-medium text-right max-w-[160px]">{{ $user->address ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Registro</span>
                        <span class="text-white font-medium">{{ $user->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Estado actual + acciones -->
            @if($user->documents)
            <div class="bg-gray-900 rounded-xl border border-gray-800 p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3">Estado</p>

                @php $status = $user->documents->status @endphp

                <div class="mb-4">
                    @if($status === 'pending')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-yellow-400/10 text-yellow-400">
                            <span class="w-2 h-2 rounded-full bg-yellow-400"></span> Pendiente de revisión
                        </span>
                    @elseif($status === 'approved')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-green-400/10 text-green-400">
                            <span class="w-2 h-2 rounded-full bg-green-400"></span> Aprobado
                        </span>
                    @elseif($status === 'rejected')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-red-400/10 text-red-400">
                            <span class="w-2 h-2 rounded-full bg-red-400"></span> Rechazado
                        </span>
                    @endif
                </div>

                <div class="flex flex-col gap-2">
                    @if($status !== 'approved')
                    <form action="{{ route('superadmin.users.approve', $user) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full py-2.5 rounded-lg text-sm font-semibold bg-green-500 hover:bg-green-600 text-white transition-colors">
                            Aprobar usuario
                        </button>
                    </form>
                    @endif

                    @if($status !== 'rejected')
                    <form action="{{ route('superadmin.users.reject', $user) }}" method="POST">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('¿Seguro que deseas rechazar a este usuario?')"
                                class="w-full py-2.5 rounded-lg text-sm font-semibold bg-gray-800 hover:bg-red-500/20 text-gray-300 hover:text-red-400 border border-gray-700 hover:border-red-500/30 transition-colors">
                            Rechazar usuario
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Documentos -->
        <div class="col-span-2">
            <div class="bg-gray-900 rounded-xl border border-gray-800 p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-5">Documentos subidos</p>

                @if($user->documents)
                <div class="grid grid-cols-2 gap-4">

                    <!-- Selfie -->
                    <div>
                        <p class="text-xs text-gray-500 mb-2">Selfie</p>
                        @if($user->documents->selfie)
                            <a href="{{ Storage::url($user->documents->selfie) }}" target="_blank">
                                <img src="{{ Storage::url($user->documents->selfie) }}"
                                     class="w-full rounded-lg object-cover bg-gray-800 hover:opacity-80 transition-opacity cursor-zoom-in"
                                     style="max-height: 200px; object-fit: cover;">
                            </a>
                        @else
                            <div class="w-full h-32 rounded-lg bg-gray-800 flex items-center justify-center text-gray-600 text-xs">Sin imagen</div>
                        @endif
                    </div>

                    <!-- DNI Frontal -->
                    <div>
                        <p class="text-xs text-gray-500 mb-2">DNI — Frontal</p>
                        @if($user->documents->dni_front)
                            <a href="{{ Storage::url($user->documents->dni_front) }}" target="_blank">
                                <img src="{{ Storage::url($user->documents->dni_front) }}"
                                     class="w-full rounded-lg object-cover bg-gray-800 hover:opacity-80 transition-opacity cursor-zoom-in"
                                     style="max-height: 200px; object-fit: cover;">
                            </a>
                        @else
                            <div class="w-full h-32 rounded-lg bg-gray-800 flex items-center justify-center text-gray-600 text-xs">Sin imagen</div>
                        @endif
                    </div>

                    <!-- DNI Posterior -->
                    <div>
                        <p class="text-xs text-gray-500 mb-2">DNI — Posterior</p>
                        @if($user->documents->dni_back)
                            <a href="{{ Storage::url($user->documents->dni_back) }}" target="_blank">
                                <img src="{{ Storage::url($user->documents->dni_back) }}"
                                     class="w-full rounded-lg object-cover bg-gray-800 hover:opacity-80 transition-opacity cursor-zoom-in"
                                     style="max-height: 200px; object-fit: cover;">
                            </a>
                        @else
                            <div class="w-full h-32 rounded-lg bg-gray-800 flex items-center justify-center text-gray-600 text-xs">Sin imagen</div>
                        @endif
                    </div>

                    <!-- Recibo de servicios -->
                    <div>
                        <p class="text-xs text-gray-500 mb-2">Recibo de servicios</p>
                        @if($user->documents->utility_bill)
                            <a href="{{ Storage::url($user->documents->utility_bill) }}" target="_blank">
                                <img src="{{ Storage::url($user->documents->utility_bill) }}"
                                     class="w-full rounded-lg object-cover bg-gray-800 hover:opacity-80 transition-opacity cursor-zoom-in"
                                     style="max-height: 200px; object-fit: cover;">
                            </a>
                        @else
                            <div class="w-full h-32 rounded-lg bg-gray-800 flex items-center justify-center text-gray-600 text-xs">Sin imagen</div>
                        @endif
                    </div>

                </div>
                @else
                    <p class="text-gray-600 text-sm">Este usuario no ha subido documentos aún.</p>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
