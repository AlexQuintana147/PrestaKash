@extends('superadmin.layout')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="p-8">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">Gestión de Usuarios</h1>
        <p class="text-sm text-gray-500 mt-1">Usuarios que han completado el registro</p>
    </div>

    @if(session('success'))
        <div class="mb-5 px-4 py-3 rounded-lg bg-green-500/10 border border-green-500/20 text-sm text-green-400">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filtros -->
    <div class="flex items-center gap-2 mb-6">
        @foreach(['all' => 'Todos', 'pending' => 'Pendientes', 'approved' => 'Aprobados', 'rejected' => 'Rechazados'] as $key => $label)
            <a href="{{ route('superadmin.users.index', ['filter' => $key]) }}"
               class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors
                      {{ $filter === $key ? 'bg-gray-800 text-white' : 'text-gray-500 hover:text-white hover:bg-gray-900' }}">
                {{ $label }}
                <span class="text-xs px-1.5 py-0.5 rounded-full
                    {{ $filter === $key ? 'bg-gray-700 text-gray-300' : 'bg-gray-900 text-gray-600' }}">
                    {{ $counts[$key] }}
                </span>
            </a>
        @endforeach
    </div>

    <!-- Tabla -->
    <div class="bg-gray-900 rounded-xl border border-gray-800">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-800">
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">DNI</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Registro</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-800/40 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=1f2937&color=fff' }}"
                                     class="w-8 h-8 rounded-full object-cover bg-gray-800">
                                <div>
                                    <p class="font-medium text-white">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-400">{{ $user->dni ?? '—' }}</td>
                        <td class="px-6 py-4 text-gray-400">{{ $user->phone ?? '—' }}</td>
                        <td class="px-6 py-4">
                            @php $status = $user->documents?->status @endphp
                            @if($status === 'pending')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-400/10 text-yellow-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-400"></span> Pendiente
                                </span>
                            @elseif($status === 'approved')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-400/10 text-green-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span> Aprobado
                                </span>
                            @elseif($status === 'rejected')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-400/10 text-red-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Rechazado
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-700 text-gray-400">
                                    Sin documentos
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-xs">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('superadmin.users.show', $user) }}"
                               class="text-xs font-medium text-gray-400 hover:text-white transition-colors">
                                Ver detalles →
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-600">No hay usuarios en esta categoría.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-800">
            {{ $users->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
