@extends('superadmin.layout')

@section('title', 'Dashboard')

@section('content')
<div class="p-8">

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-white">Dashboard</h1>
        <p class="text-sm text-gray-500 mt-1">Resumen general de registros</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-4 gap-4 mb-8">
        <div class="bg-gray-900 rounded-xl p-5 border border-gray-800">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Total usuarios</p>
            <p class="text-3xl font-bold text-white">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-gray-900 rounded-xl p-5 border border-gray-800">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Pendientes</p>
            <p class="text-3xl font-bold text-yellow-400">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-gray-900 rounded-xl p-5 border border-gray-800">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Completados</p>
            <p class="text-3xl font-bold text-green-400">{{ $stats['completed'] }}</p>
        </div>
        <div class="bg-gray-900 rounded-xl p-5 border border-gray-800">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Hoy</p>
            <p class="text-3xl font-bold text-blue-400">{{ $stats['today'] }}</p>
        </div>
    </div>

    <!-- Recent users table -->
    <div class="bg-gray-900 rounded-xl border border-gray-800">
        <div class="px-6 py-4 border-b border-gray-800">
            <h2 class="text-sm font-semibold text-white">Registros recientes</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-800">
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">DNI</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Registro</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse($recentUsers as $user)
                    <tr class="hover:bg-gray-800/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $user->avatar }}" alt="" class="w-8 h-8 rounded-full object-cover bg-gray-800">
                                <span class="font-medium text-white">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-400">{{ $user->email }}</td>
                        <td class="px-6 py-4 text-gray-400">{{ $user->dni ?? '—' }}</td>
                        <td class="px-6 py-4">
                            @if($user->documents?->status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-400/10 text-yellow-400">
                                    Pendiente
                                </span>
                            @elseif($user->documents?->status === 'approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-400/10 text-green-400">
                                    Aprobado
                                </span>
                            @elseif($user->profile_completed)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-400/10 text-blue-400">
                                    Completado
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-700 text-gray-400">
                                    Sin completar
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-xs">{{ $user->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-600">No hay usuarios registrados aún.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

