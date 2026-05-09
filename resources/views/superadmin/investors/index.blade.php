@extends('superadmin.layout')

@section('title', 'Gestión de Inversores')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8">

    <!-- Header -->
    <div class="flex items-end justify-between mb-8">
        <div>
            <p class="text-xs text-gray-600 dark:text-gray-600 uppercase tracking-widest mb-1">Panel</p>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Inversores</h1>
        </div>
        <p class="hidden md:block text-sm text-gray-600 dark:text-gray-400">{{ now()->format('d M Y') }}</p>
    </div>

    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 text-sm text-green-500">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Filtros -->
    <div class="mb-6">
        <!-- Mobile: grid 2 columnas -->
        <div class="md:hidden space-y-2">
            <div class="grid grid-cols-3 gap-2">
                @foreach(['all' => 'Todos', 'pending' => 'Pendientes', 'approved' => 'Aprobados'] as $key => $label)
                    <a href="{{ route('superadmin.investors.index', ['filter' => $key]) }}"
                       class="px-3 py-2 rounded-lg text-xs font-medium text-center transition-colors
                              {{ $filter === $key
                                  ? 'bg-gray-200 dark:bg-gray-800 text-gray-900 dark:text-white'
                                  : 'bg-gray-100 dark:bg-gray-900 text-gray-500 dark:text-gray-500' }}">
                        {{ $label }}
                        @if($counts[$key] > 0)
                            <span class="ml-1 opacity-60">{{ $counts[$key] }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
            <div class="grid grid-cols-1 gap-2 w-1/2 mx-auto">
                @foreach(['rejected' => 'Rechazados'] as $key => $label)
                    <a href="{{ route('superadmin.investors.index', ['filter' => $key]) }}"
                       class="px-3 py-2 rounded-lg text-xs font-medium text-center transition-colors
                              {{ $filter === $key
                                  ? 'bg-gray-200 dark:bg-gray-800 text-gray-900 dark:text-white'
                                  : 'bg-gray-100 dark:bg-gray-900 text-gray-500 dark:text-gray-500' }}">
                        {{ $label }}
                        @if($counts[$key] > 0)
                            <span class="ml-1 opacity-60">{{ $counts[$key] }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Desktop: tabs horizontales -->
        <div class="hidden md:flex items-center gap-0 border-b border-gray-200 dark:border-gray-800">
            @foreach(['all' => 'Todos', 'pending' => 'Pendientes', 'approved' => 'Aprobados', 'rejected' => 'Rechazados'] as $key => $label)
                <a href="{{ route('superadmin.investors.index', ['filter' => $key]) }}"
                   class="relative flex-shrink-0 px-4 py-3 text-sm font-medium transition-colors
                          {{ $filter === $key
                              ? 'text-gray-900 dark:text-white'
                              : 'text-gray-600 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-400' }}">
                    {{ $label }}
                    @if($counts[$key] > 0)
                        <span class="ml-1.5 text-xs {{ $filter === $key ? 'text-gray-500 dark:text-gray-400' : 'text-gray-300 dark:text-gray-500' }}">{{ $counts[$key] }}</span>
                    @endif
                    @if($filter === $key)
                        <span class="absolute bottom-0 left-0 right-0 h-px bg-gray-900 dark:bg-white"></span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    <!-- Cabecera de columnas (solo desktop) -->
    <div class="hidden md:grid py-2 mb-1 px-2 text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider items-center" style="grid-template-columns: 1fr 150px 130px 120px 100px 80px;">
        <span>Inversor</span>
        <span class="text-center">Ocupación</span>
        <span class="text-center">Monto</span>
        <span class="text-center">Plazo</span>
        <span class="text-center">Estado</span>
        <span class="text-right">Acciones</span>
    </div>

    <!-- Lista -->
    @forelse($applications as $app)
    <div onclick="openInvestorModal({{ json_encode([
            'id' => $app->id,
            'name' => $app->user->name,
            'email' => $app->user->email,
            'phone' => $app->phone,
            'dni' => $app->user->dni ?? '—',
            'occupation' => $app->occupation,
            'amount' => number_format($app->intended_amount, 2),
            'term' => $app->preferred_term,
            'source' => $app->source_of_funds,
            'experience' => $app->experience ?? 'Sin experiencia previa registrada.',
            'status' => $app->status,
            'date' => $app->created_at->format('d/m/Y H:i'),
            'avatar' => $app->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($app->user->name),
            'selfie' => $app->user->documents?->selfie ? Storage::url($app->user->documents->selfie) : null
        ]) }})"
       class="flex flex-col md:grid items-center py-4 border-b border-gray-200 dark:border-gray-900 hover:bg-gray-50 dark:hover:bg-gray-900/30 -mx-2 px-2 rounded-lg transition-colors group cursor-pointer"
       style="grid-template-columns: 1fr 150px 130px 120px 100px 80px;">

        <!-- Inversor Info -->
        <div class="flex items-center gap-3 min-w-0">
            @php
                $svgColor = match($app->status) {
                    'approved' => '#22c55e',
                    'rejected' => '#ef4444',
                    'pending'  => '#eab308',
                    default    => '#9ca3af',
                };
                $selfieUrl = $app->user->documents?->selfie ? Storage::url($app->user->documents->selfie) : null;
            @endphp
            <div class="w-10 h-10 flex-shrink-0 relative">
                @if($selfieUrl)
                    <img src="{{ $selfieUrl }}" class="w-10 h-10 rounded-full object-cover border border-gray-100 dark:border-gray-800">
                @else
                    <div class="w-10 h-10 flex items-center justify-center">
                        <svg width="36" height="36" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M0.877014 7.49988C0.877014 3.84219 3.84216 0.877045 7.49985 0.877045C11.1575 0.877045 14.1227 3.84219 14.1227 7.49988C14.1227 11.1575 11.1575 14.1227 7.49985 14.1227C3.84216 14.1227 0.877014 11.1575 0.877014 7.49988ZM7.49985 1.82704C4.36683 1.82704 1.82701 4.36686 1.82701 7.49988C1.82701 8.97196 2.38774 10.3131 3.30727 11.3213C4.19074 9.94119 5.73818 9.02499 7.50023 9.02499C9.26206 9.02499 10.8093 9.94097 11.6929 11.3208C12.6121 10.3127 13.1727 8.97172 13.1727 7.49988C13.1727 4.36686 10.6328 1.82704 7.49985 1.82704ZM10.9818 11.9787C10.2839 10.7795 8.9857 9.97499 7.50023 9.97499C6.01458 9.97499 4.71624 10.7797 4.01845 11.9791C4.97952 12.7272 6.18765 13.1727 7.49985 13.1727C8.81227 13.1727 10.0206 12.727 10.9818 11.9787ZM5.14999 6.50487C5.14999 5.207 6.20212 4.15487 7.49999 4.15487C8.79786 4.15487 9.84999 5.207 9.84999 6.50487C9.84999 7.80274 8.79786 8.85487 7.49999 8.85487C6.20212 8.85487 5.14999 7.80274 5.14999 6.50487ZM7.49999 5.10487C6.72679 5.10487 6.09999 5.73167 6.09999 6.50487C6.09999 7.27807 6.72679 7.90487 7.49999 7.90487C8.27319 7.90487 8.89999 7.27807 8.89999 6.50487C8.89999 5.73167 8.27319 5.10487 7.49999 5.10487Z" fill="{{ $svgColor }}"/>
                        </svg>
                    </div>
                @endif
                <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-white dark:border-gray-950
                    {{ $app->status === 'approved' ? 'bg-green-500' : '' }}
                    {{ $app->status === 'pending' ? 'bg-yellow-500' : '' }}
                    {{ $app->status === 'rejected' ? 'bg-red-500' : '' }}">
                </div>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $app->user->name }}</p>
                <p class="text-xs text-gray-600 dark:text-gray-400 truncate">{{ $app->phone }}</p>
            </div>
        </div>

        <span class="hidden md:block text-xs text-gray-900 dark:text-white text-center">{{ $app->occupation }}</span>
        <span class="hidden md:block text-xs text-green-600 dark:text-green-400 font-bold text-center">S/ {{ number_format($app->intended_amount, 2) }}</span>
        <span class="hidden md:block text-xs text-gray-900 dark:text-white text-center">{{ $app->preferred_term }}</span>

        <!-- Estado -->
        <div class="flex items-center justify-center gap-1.5 ml-auto md:ml-0 my-2 md:my-0">
            @if($app->status === 'pending')
                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 flex-shrink-0"></span>
                <span class="text-xs text-yellow-500">Pendiente</span>
            @elseif($app->status === 'approved')
                <span class="w-1.5 h-1.5 rounded-full bg-green-500 flex-shrink-0"></span>
                <span class="text-xs text-green-500">Aprobado</span>
            @elseif($app->status === 'rejected')
                <span class="w-1.5 h-1.5 rounded-full bg-red-500 flex-shrink-0"></span>
                <span class="text-xs text-red-500">Rechazado</span>
            @endif
        </div>

        <!-- Acciones -->
        <div class="flex items-center justify-end gap-2 w-full md:w-auto" onclick="event.stopPropagation()">
            @if($app->status === 'pending')
                <button onclick="openConfirmModal('approve', {{ $app->id }})" class="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-xl transition-colors" title="Aprobar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </button>
                <button onclick="openConfirmModal('reject', {{ $app->id }})" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-colors" title="Rechazar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            @elseif($app->status === 'approved')
                <button onclick="openConfirmModal('unapprove', {{ $app->id }})" class="p-2 text-yellow-600 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 rounded-xl transition-colors" title="Desaprobar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </button>
            @else
                <button onclick="openConfirmModal('approve', {{ $app->id }})" class="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-xl transition-colors" title="Aprobar (Revertir)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </button>
            @endif
        </div>

    </div>
    @empty
    <div class="py-20 text-center">
        <p class="text-gray-400 dark:text-gray-500 text-sm">No hay solicitudes en esta categoría.</p>
    </div>
    @endforelse

    <!-- Paginación -->
    @if($applications->hasPages())
    <div class="mt-8 flex items-center justify-between text-xs text-gray-600 dark:text-gray-400">
        <span>{{ $applications->firstItem() }}–{{ $applications->lastItem() }} de {{ $applications->total() }}</span>
        <div class="flex items-center gap-5">
            @if($applications->onFirstPage())
                <span class="opacity-30">← Anterior</span>
            @else
                <a href="{{ $applications->previousPageUrl() }}" class="hover:text-gray-900 dark:hover:text-white transition-colors">← Anterior</a>
            @endif
            @if($applications->hasMorePages())
                <a href="{{ $applications->nextPageUrl() }}" class="hover:text-gray-900 dark:hover:text-white transition-colors">Siguiente →</a>
            @else
                <span class="opacity-30">Siguiente →</span>
            @endif
        </div>
    </div>
    @endif

</div>

<!-- Modal Acción Genérico -->
<div id="actionModal" class="hidden fixed inset-0 bg-black/60 z-[60] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-950 w-full max-w-sm rounded-[2rem] p-8 shadow-2xl relative">
        <div id="actionIcon" class="w-12 h-12 rounded-2xl flex items-center justify-center mb-6"></div>
        <h2 id="actionTitle" class="text-xl font-bold text-gray-900 dark:text-white mb-2"></h2>
        <p id="actionDescription" class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed mb-8"></p>
        
        <form id="actionForm" method="POST" class="space-y-4">
            @csrf
            <div id="actionExtraFields"></div>
            <div class="flex gap-3">
                <button type="button" onclick="closeActionModal()" class="flex-1 px-4 py-3 rounded-2xl text-xs font-bold text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-900 transition-all">Cancelar</button>
                <button type="submit" id="actionConfirmBtn" class="flex-1 px-4 py-3 rounded-2xl text-[10px] sm:text-xs font-bold text-white transition-all shadow-lg"></button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Inversor Detalles -->
<div id="investorModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white dark:bg-gray-950 w-full max-w-2xl rounded-[2.5rem] shadow-2xl overflow-hidden relative my-auto">
        <!-- Close Button -->
        <button onclick="closeInvestorModal()" class="absolute top-6 right-6 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-900 transition-colors z-20">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <div class="flex flex-col md:flex-row">
            <!-- Left: Image -->
            <div class="w-full md:w-2/5 bg-gray-50 dark:bg-gray-900 p-8 flex flex-col items-center justify-center border-b md:border-b-0 md:border-r border-gray-100 dark:border-gray-800">
                <img id="modalSelfie" src="" class="w-40 h-40 rounded-[2rem] object-cover shadow-xl border-4 border-white dark:border-gray-800 mb-6">
                <h3 id="modalName" class="text-xl font-bold text-gray-900 dark:text-white text-center leading-tight"></h3>
                <p id="modalEmail" class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center"></p>
                <div id="modalBadge" class="mt-6 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-[0.2em]"></div>
            </div>

            <!-- Right: Details -->
            <div class="w-full md:w-3/5 p-8 md:p-10 space-y-8">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">Información de Perfil</p>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">DNI / Documento</p>
                            <p id="modalDni" class="text-sm font-bold text-gray-900 dark:text-white mt-1"></p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Teléfono</p>
                            <div class="flex items-center gap-2 mt-1">
                                <p id="modalPhone" class="text-sm font-bold text-gray-900 dark:text-white"></p>
                                <a id="modalWhatsapp" href="" target="_blank" class="hover:opacity-80 transition-opacity">
                                    <svg class="w-5 h-5" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M16 31C23.732 31 30 24.732 30 17C30 9.26801 23.732 3 16 3C8.26801 3 2 9.26801 2 17C2 19.5109 2.661 21.8674 3.81847 23.905L2 31L9.31486 29.3038C11.3014 30.3854 13.5789 31 16 31ZM16 28.8462C22.5425 28.8462 27.8462 23.5425 27.8462 17C27.8462 10.4576 22.5425 5.15385 16 5.15385C9.45755 5.15385 4.15385 10.4576 4.15385 17C4.15385 19.5261 4.9445 21.8675 6.29184 23.7902L5.23077 27.7692L9.27993 26.7569C11.1894 28.0746 13.5046 28.8462 16 28.8462Z" fill="#BFC8D0"/>
                                        <path d="M28 16C28 22.6274 22.6274 28 16 28C13.4722 28 11.1269 27.2184 9.19266 25.8837L5.09091 26.9091L6.16576 22.8784C4.80092 20.9307 4 18.5589 4 16C4 9.37258 9.37258 4 16 4C22.6274 4 28 9.37258 28 16Z" fill="url(#paint0_linear_87_7264)"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M16 30C23.732 30 30 23.732 30 16C30 8.26801 23.732 2 16 2C8.26801 2 2 8.26801 2 16C2 18.5109 2.661 20.8674 3.81847 22.905L2 30L9.31486 28.3038C11.1894 29.3854 13.5789 30 16 30ZM16 27.8462C22.5425 27.8462 27.8462 22.5425 27.8462 16C27.8462 9.45755 22.5425 4.15385 16 4.15385C9.45755 4.15385 4.15385 9.45755 4.15385 16C4.15385 18.5261 4.9445 20.8675 6.29184 22.7902L5.23077 26.7692L9.27993 25.7569C11.1894 27.0746 13.5046 27.8462 16 27.8462Z" fill="white"/>
                                        <path d="M12.5 9.49989C12.1672 8.83131 11.6565 8.8905 11.1407 8.8905C10.2188 8.8905 8.78125 9.99478 8.78125 12.05C8.78125 13.7343 9.52345 15.578 12.0244 18.3361C14.438 20.9979 17.6094 22.3748 20.2422 22.3279C22.875 22.2811 23.4167 20.0154 23.4167 19.2503C23.4167 18.9112 23.2062 18.742 23.0613 18.696C22.1641 18.2654 20.5093 17.4631 20.1328 17.3124C19.7563 17.1617 19.5597 17.3656 19.4375 17.4765C19.0961 17.8018 18.4193 18.7608 18.1875 18.9765C17.9558 19.1922 17.6103 19.083 17.4665 19.0015C16.9374 18.7892 15.5029 18.1511 14.3595 17.0426C12.9453 15.6718 12.8623 15.2001 12.5959 14.7803C12.3828 14.4444 12.5392 14.2384 12.6172 14.1483C12.9219 13.7968 13.3426 13.254 13.5313 12.9843C13.7199 12.7145 13.5702 12.305 13.4803 12.05C13.0938 10.953 12.7663 10.0347 12.5 9.49989Z" fill="white"/>
                                        <defs>
                                            <linearGradient id="paint0_linear_87_7264" x1="26.5" y1="7" x2="4" y2="28" gradientUnits="userSpaceOnUse">
                                                <stop stop-color="#5BD066"/>
                                                <stop offset="1" stop-color="#27B43E"/>
                                            </linearGradient>
                                        </defs>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">Detalles de Postulación</p>
                    <div class="space-y-4">
                        <div class="flex justify-between border-b border-gray-50 dark:border-gray-900 pb-2">
                            <span class="text-xs text-gray-500">Ocupación</span>
                            <span id="modalOccupation" class="text-xs font-bold text-gray-900 dark:text-white"></span>
                        </div>
                        <div class="flex justify-between border-b border-gray-50 dark:border-gray-900 pb-2">
                            <span class="text-xs text-gray-500">Monto Estimado</span>
                            <span id="modalAmount" class="text-xs font-bold text-green-600 dark:text-green-400"></span>
                        </div>
                        <div class="flex justify-between border-b border-gray-50 dark:border-gray-900 pb-2">
                            <span class="text-xs text-gray-500">Plazo Preferido</span>
                            <span id="modalTerm" class="text-xs font-bold text-gray-900 dark:text-white"></span>
                        </div>
                        <div class="flex justify-between border-b border-gray-50 dark:border-gray-900 pb-2">
                            <span class="text-xs text-gray-500">Origen de Fondos</span>
                            <span id="modalSource" class="text-xs font-bold text-gray-900 dark:text-white"></span>
                        </div>
                    </div>
                </div>

                <div>
                    <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Experiencia</p>
                    <p id="modalExperience" class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed italic"></p>
                </div>

                <div id="modalActions" class="pt-4 flex gap-3">
                    <!-- Dinámico -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openConfirmModal(type, appId) {
    const modal = document.getElementById('actionModal');
    const form = document.getElementById('actionForm');
    const title = document.getElementById('actionTitle');
    const desc = document.getElementById('actionDescription');
    const icon = document.getElementById('actionIcon');
    const btn = document.getElementById('actionConfirmBtn');
    const extra = document.getElementById('actionExtraFields');

    extra.innerHTML = '';
    
    if (type === 'approve') {
        form.action = `/gestion-interna/inversores/${appId}/aprobar`;
        title.textContent = '¿Aprobar Inversor?';
        desc.textContent = 'El usuario será notificado y podrá comenzar a operar como inversor en la plataforma.';
        icon.className = 'w-12 h-12 rounded-2xl flex items-center justify-center mb-6 bg-green-50 dark:bg-green-500/10 text-green-600';
        icon.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>';
        btn.textContent = 'Confirmar Aprobación';
        btn.className = 'flex-1 px-4 py-3 rounded-2xl text-[10px] sm:text-xs font-bold text-white bg-green-600 hover:bg-green-500 shadow-green-500/20 transition-all';
    } else if (type === 'unapprove') {
        form.action = `/gestion-interna/inversores/${appId}/desaprobar`;
        title.textContent = '¿Desaprobar Inversor?';
        desc.textContent = 'La solicitud volverá al estado pendiente y el usuario perderá sus privilegios de inversor.';
        icon.className = 'w-12 h-12 rounded-2xl flex items-center justify-center mb-6 bg-yellow-50 dark:bg-yellow-500/10 text-yellow-600';
        icon.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>';
        btn.textContent = 'Confirmar Reversión';
        btn.className = 'flex-1 px-4 py-3 rounded-2xl text-[10px] sm:text-xs font-bold text-white bg-yellow-600 hover:bg-yellow-500 shadow-yellow-500/20 transition-all';
    } else if (type === 'reject') {
        form.action = `/gestion-interna/inversores/${appId}/rechazar`;
        title.textContent = 'Rechazar Solicitud';
        desc.textContent = 'Por favor, indica el motivo del rechazo para informar al postulante.';
        icon.className = 'w-12 h-12 rounded-2xl flex items-center justify-center mb-6 bg-red-50 dark:bg-red-500/10 text-red-600';
        icon.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>';
        extra.innerHTML = `
            <div class="mb-4">
                <textarea name="reason" rows="3" required placeholder="Escribe el motivo..." class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500/20 dark:text-white transition-all resize-none"></textarea>
            </div>
        `;
        btn.textContent = 'Confirmar Rechazo';
        btn.className = 'flex-1 px-4 py-3 rounded-2xl text-[10px] sm:text-xs font-bold text-white bg-red-600 hover:bg-red-500 shadow-red-500/20 transition-all';
    }

    modal.classList.remove('hidden');
}

function closeActionModal() {
    document.getElementById('actionModal').classList.add('hidden');
}

function openInvestorModal(data) {
    const modal = document.getElementById('investorModal');
    
    document.getElementById('modalSelfie').src = data.selfie || data.avatar;
    document.getElementById('modalName').textContent = data.name;
    document.getElementById('modalEmail').textContent = data.email;
    document.getElementById('modalDni').textContent = data.dni;
    document.getElementById('modalPhone').textContent = data.phone;

    const whatsappBtn = document.getElementById('modalWhatsapp');
    if (whatsappBtn) {
        const cleanPhone = data.phone.replace(/\D/g, '');
        whatsappBtn.href = `https://wa.me/51${cleanPhone}`;
    }

    document.getElementById('modalOccupation').textContent = data.occupation;
    document.getElementById('modalAmount').textContent = 'S/ ' + data.amount;
    document.getElementById('modalTerm').textContent = data.term;
    document.getElementById('modalSource').textContent = data.source;
    document.getElementById('modalExperience').textContent = data.experience;

    const badge = document.getElementById('modalBadge');
    badge.textContent = data.status === 'pending' ? 'Pendiente' : (data.status === 'approved' ? 'Aprobado' : 'Rechazado');
    badge.className = 'mt-6 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-[0.2em] ' + 
        (data.status === 'approved' ? 'bg-green-100 text-green-700' : (data.status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700'));

    const actions = document.getElementById('modalActions');
    actions.innerHTML = '';
    if (data.status === 'pending') {
        actions.innerHTML = `
            <button onclick="closeInvestorModal(); openConfirmModal('approve', ${data.id})" class="flex-1 bg-green-600 hover:bg-green-500 text-white font-bold py-3 rounded-2xl text-xs transition-all shadow-lg shadow-green-500/20">Aprobar Inversor</button>
            <button onclick="closeInvestorModal(); openConfirmModal('reject', ${data.id})" class="flex-1 bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400 font-bold py-3 rounded-2xl text-xs hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 transition-all">Rechazar</button>
        `;
    } else if (data.status === 'approved') {
        actions.innerHTML = `
            <button onclick="closeInvestorModal(); openConfirmModal('unapprove', ${data.id})" class="w-full bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400 font-bold py-3 rounded-2xl text-xs hover:bg-yellow-50 dark:hover:bg-yellow-900/20 hover:text-yellow-600 transition-all border border-transparent hover:border-yellow-200 dark:hover:border-yellow-900/50">Desaprobar Inversor</button>
        `;
    } else if (data.status === 'rejected') {
        actions.innerHTML = `
            <button onclick="closeInvestorModal(); openConfirmModal('approve', ${data.id})" class="w-full bg-green-600 hover:bg-green-500 text-white font-bold py-3 rounded-2xl text-xs transition-all shadow-lg shadow-green-500/20">Aprobar (Revertir Rechazo)</button>
        `;
    }

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeInvestorModal() {
    document.getElementById('investorModal').classList.add('hidden');
    document.body.style.overflow = '';
}
</script>
@endsection
