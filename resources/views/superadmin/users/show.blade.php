@extends('superadmin.layout')

@section('title', $user->name)

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8">

    <!-- Header -->
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('superadmin.users.index') }}"
           class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        @if($user->is_ghost)
            <svg fill="#9333ea" width="36px" height="36px" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0"><path d="M511.984-.128c-229.216 0-415.681 199.903-415.681 445.6v546.672c0 13.216 8.16 25.088 20.496 29.84 3.712 1.471 7.632 2.16 11.504 2.16 8.848 0 17.536-3.68 23.712-10.527l120.592-133.12 94.431 130.432a31.918 31.918 0 0 0 25.68 13.215h.224a31.98 31.98 0 0 0 25.664-12.912l94.816-127.344 93.184 127.152a31.993 31.993 0 0 0 25.809 13.088 32.028 32.028 0 0 0 25.808-13.055l95.569-130.288 118 132.624c8.816 9.904 22.944 13.376 35.28 8.624 12.4-4.72 20.624-16.624 20.624-29.905V445.456C927.696 199.776 741.2-.128 511.984-.128zm351.711 908.16l-88.402-99.376c-6.432-7.216-15.808-11.311-25.407-10.687a32.105 32.105 0 0 0-24.32 13.024l-93.12 127.008-93.008-126.912A31.975 31.975 0 0 0 513.758 798h-.127a31.935 31.935 0 0 0-25.664 12.912l-94.689 127.152-92-127.088c-5.664-7.807-14.528-12.655-24.16-13.151-.592-.032-1.151-.065-1.743-.065a31.984 31.984 0 0 0-23.712 10.528l-91.376 100.848v-463.68c0-210.4 157.776-381.601 351.68-381.601 193.937 0 351.713 171.184 351.713 381.6V908.03h.015zM671.997 352.16c-35.28 0-63.84 28.592-63.84 63.808 0 35.248 28.56 63.84 63.84 63.84s63.84-28.592 63.84-63.84c0-35.216-28.56-63.808-63.84-63.808zm-320 0c-35.28 0-63.84 28.592-63.84 63.808 0 35.248 28.576 63.84 63.84 63.84s63.84-28.592 63.84-63.84c0-35.216-28.56-63.808-63.84-63.808z"/></svg>
        @else
            <img src="{{ $user->documents?->selfie ? Storage::url($user->documents->selfie) : ($user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=e5e7eb&color=6b7280&size=64') }}"
                 class="w-9 h-9 rounded-full object-cover bg-gray-200 dark:bg-gray-900 flex-shrink-0">
        @endif
        <div class="min-w-0 flex-1">
            <h1 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white leading-tight truncate">{{ $user->name }}</h1>
            <div class="flex items-center gap-1.5">
                <p class="text-xs text-gray-600 dark:text-gray-400 truncate">{{ $user->email }}</p>
                <a href="mailto:{{ $user->email }}" class="hover:opacity-80 transition-opacity">
                    <svg class="w-4 h-4" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
                        <rect x="7.904" y="119.947" style="fill:#5EBAE7;" width="496.195" height="272.109"/>
                        <polygon style="fill:#36A9E1;" points="256,280.01 23.91,127.951 7.904,135.954 256,312.022 352.037,223.987 "/>
                        <path style="fill:#1D1D1B;" d="M0,112.043v287.913h512V112.043H0z M477.266,127.851L256,270.603L34.733,127.851H477.266z M15.807,384.149V134.452L256,289.414l240.193-154.962v249.696H15.807V384.149z"/>
                        <g>
                            <rect x="215.982" y="344.138" style="fill:#FFFFFF;" width="16.007" height="15.807"/>
                            <rect x="247.997" y="344.138" style="fill:#FFFFFF;" width="16.006" height="15.807"/>
                            <rect x="280.013" y="344.138" style="fill:#FFFFFF;" width="16.007" height="15.807"/>
                        </g>
                    </svg>
                </a>
            </div>
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
                <div class="flex items-center justify-between mb-4">
                    <p class="text-xs text-gray-600 dark:text-gray-600 uppercase tracking-widest">Datos personales</p>
                    @if(!$user->is_ghost)
                        <button type="button" onclick="openEditWarningModal()"
                                class="text-[10px] font-bold text-blue-500 hover:text-blue-600 uppercase tracking-wider transition-colors">
                            Editar datos
                        </button>
                    @else
                        <div class="flex gap-3">
                            <a href="{{ route('superadmin.users.edit-ghost', $user) }}"
                               class="text-[10px] font-bold text-blue-500 hover:text-blue-600 uppercase tracking-wider transition-colors">
                                Editar
                            </a>
                            <button type="button" onclick="openDeleteGhostModal()"
                                    class="text-[10px] font-bold text-red-500 hover:text-red-600 uppercase tracking-wider transition-colors">
                                Eliminar
                            </button>
                        </div>
                    @endif
                </div>
                <div class="space-y-3">
                    @foreach(array_filter([
                        'Nombres'      => [
                            'value' => $user->first_name,
                            'editable' => true,
                            'field' => 'first_name'
                        ],
                        'Apellidos'    => [
                            'value' => $user->last_name,
                            'editable' => true,
                            'field' => 'last_name'
                        ],
                        'DNI'          => [
                            'value' => $user->dni,
                            'editable' => true,
                            'field' => 'dni'
                        ],
                        'Teléfono'     => [
                            'value' => $user->phone,
                            'icon' => 'wssp',
                            'link' => 'https://wa.me/51' . preg_replace('/\D/', '', $user->phone),
                            'is_whatsapp' => true,
                            'editable' => true,
                            'field' => 'phone'
                        ],
                        'Correo'       => [
                            'value' => $user->email,
                            'icon' => 'email',
                            'link' => 'mailto:' . $user->email,
                            'is_email' => true
                        ],
                        'Departamento' => $department,
                        'Provincia'    => $province,
                        'Distrito'     => $district,
                        'Dirección'    => [
                            'value' => $user->address,
                            'editable' => true,
                            'field' => 'address'
                        ],
                        'Tasa de interés' => ($status === 'approved' && !$user->is_ghost) ? [
                            'value' => number_format($user->loan_interest_rate ?? 20, 2, '.', ''),
                            'editable' => true,
                            'field' => 'loan_interest_rate',
                            'suffix' => '%'
                        ] : null,
                        'Registro'     => $user->created_at->format('d/m/Y'),
                        'Referido por' => (!$user->is_ghost && $user->referrer) ? [
                            'value' => $user->referrer->first_name . ' ' . $user->referrer->last_name,
                            'link' => route('superadmin.users.show', $user->referrer),
                            'is_referrer' => true
                        ] : null,
                    ], fn($v) => !is_null($v)) as $label => $data)
                        @php 
                            $value = is_array($data) ? $data['value'] : $data;
                            $isEditable = is_array($data) && isset($data['editable']) && $data['editable'];
                        @endphp
                        @if(is_array($data) && isset($data['is_referrer']))
                            <a href="{{ $data['link'] }}"
                               class="flex justify-between items-start py-3 px-3 -mx-3 bg-gray-900 dark:bg-white mb-2 hover:opacity-90 transition-opacity cursor-pointer">
                                <span class="text-xs text-white dark:text-gray-900 flex-shrink-0">Referido por</span>
                                <span class="text-xs font-semibold text-white dark:text-gray-900 text-right ml-4">
                                    {{ $value }}
                                </span>
                            </a>
                        @elseif(is_array($data) && isset($data['is_whatsapp']))
                            <div class="flex justify-between items-center py-3 px-3 -mx-3 bg-green-500 mb-2 hover:bg-green-600 transition-colors border border-white shadow-sm relative group">
                                <a href="{{ $data['link'] }}" target="_blank" class="absolute inset-0 z-0 wssp-link"></a>
                                <span class="text-xs text-white flex-shrink-0 relative z-10">{{ $label }}</span>
                                <div class="flex items-center gap-2 relative z-10">
                                    <span class="text-xs font-bold text-white text-right">{{ $value ?? '—' }}</span>
                                    @if($isEditable)
                                        <button type="button" onclick="openEditFieldModal('{{ $label }}', '{{ $data['field'] }}', '{{ $value }}')"
                                                class="edit-action-btn hidden p-1 hover:bg-white/20 rounded transition-colors text-white relative z-20">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </button>
                                    @endif
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
                                </div>
                            </div>
                        @elseif(is_array($data) && isset($data['is_email']))
                            <a href="{{ $data['link'] }}"
                               class="flex justify-between items-center py-3 px-3 -mx-3 bg-blue-500 mb-2 hover:bg-blue-600 transition-colors border border-white shadow-sm">
                                <span class="text-xs text-white flex-shrink-0">{{ $label }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-white text-right">{{ $value ?? '—' }}</span>
                                    <svg class="w-5 h-5" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
                                        <rect x="7.904" y="119.947" style="fill:#5EBAE7;" width="496.195" height="272.109"/>
                                        <polygon style="fill:#36A9E1;" points="256,280.01 23.91,127.951 7.904,135.954 256,312.022 352.037,223.987 "/>
                                        <path style="fill:#1D1D1B;" d="M0,112.043v287.913h512V112.043H0z M477.266,127.851L256,270.603L34.733,127.851H477.266z M15.807,384.149V134.452L256,289.414l240.193-154.962v249.696H15.807V384.149z"/>
                                        <g>
                                            <rect x="215.982" y="344.138" style="fill:#FFFFFF;" width="16.007" height="15.807"/>
                                            <rect x="247.997" y="344.138" style="fill:#FFFFFF;" width="16.006" height="15.807"/>
                                            <rect x="280.013" y="344.138" style="fill:#FFFFFF;" width="16.007" height="15.807"/>
                                        </g>
                                    </svg>
                                </div>
                            </a>
                        @else
                            <div class="flex justify-between items-start border-b border-gray-100 dark:border-gray-900 pb-3">
                                <span class="text-xs text-gray-600 dark:text-gray-400 flex-shrink-0">{{ $label }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-900 dark:text-white text-right">
                                        {{ $value ?? '—' }}{{ is_array($data) && isset($data['suffix']) ? $data['suffix'] : '' }}
                                    </span>
                                    @if($isEditable)
                                        <button type="button" onclick="openEditFieldModal('{{ $label }}', '{{ $data['field'] }}', '{{ $value }}')"
                                                class="edit-action-btn hidden p-1 hover:bg-gray-100 dark:hover:bg-gray-800 rounded transition-colors text-gray-400">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            @if($user->documents && !$user->is_ghost)
            <div>
                <p class="text-xs text-gray-600 dark:text-gray-600 uppercase tracking-widest mb-4">Acciones</p>

                @if($user->documents->status === 'rejected')
                <div class="p-3 rounded-lg bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 mb-3">
                    <p class="text-xs font-medium text-red-500 mb-1">Motivo de rechazo</p>
                    <p class="text-xs text-red-400">{{ $user->documents->rejection_reason ?? '—' }}</p>

                    @if($user->documents->rejected_at)
                    @php
                        $deleteAt = $user->documents->rejected_at->addSeconds(60);
                        $secondsLeft = max(0, (int) now()->diffInSeconds($deleteAt, false));
                    @endphp
                    @if($secondsLeft > 0)
                    <div class="mt-3 pt-3 border-t border-red-200 dark:border-red-500/20">
                        <p class="text-xs text-red-400 mb-1">El usuario será eliminado en:</p>
                        <p id="countdown" class="text-2xl font-bold text-red-500 tabular-nums">{{ $secondsLeft }}s</p>
                    </div>
                    <script>
                        (function() {
                            let seconds = {{ $secondsLeft }};
                            const el = document.getElementById('countdown');
                            const timer = setInterval(() => {
                                seconds--;
                                if (seconds <= 0) {
                                    clearInterval(timer);
                                    el.textContent = 'Eliminado';
                                    setTimeout(() => window.location.href = '{{ route('superadmin.users.index') }}', 1500);
                                } else {
                                    el.textContent = seconds + 's';
                                }
                            }, 1000);
                        })();
                    </script>
                    @else
                    <p class="text-xs text-red-400 mt-2 opacity-60">Usuario pendiente de eliminación.</p>
                    @endif
                    @endif
                </div>
                @endif
                <div class="flex gap-2">
                    @if($user->documents->status === 'approved')
                        {{-- Ya aprobado: solo mostrar desaprobar --}}
                        <form action="{{ route('superadmin.users.unapprove', $user) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit"
                                    class="w-full py-2.5 text-sm font-semibold rounded-lg bg-blue-500 hover:bg-blue-600 text-white transition-colors">
                                Desaprobar
                            </button>
                        </form>
                    @else
                        @if($user->documents->status === 'pending')
                        <button type="button" onclick="openApproveModal()"
                                class="flex-1 py-2.5 text-sm font-semibold rounded-lg bg-green-500 hover:bg-green-600 text-white transition-colors">
                            Aprobar
                        </button>
                        <button type="button" onclick="openRejectModal()"
                                class="flex-1 py-2.5 text-sm font-semibold rounded-lg bg-red-500 hover:bg-red-600 text-white transition-colors">
                            Rechazar
                        </button>
                        @endif
                    @endif
                </div>

                {{-- Bloquear: disponible para pending y approved --}}
                @if(in_array($user->documents->status, ['pending', 'approved']))
                <button type="button" onclick="openBlockModal()"
                        class="mt-2 w-full py-2.5 text-sm font-medium rounded-lg border border-gray-200 dark:border-gray-800 text-gray-500 dark:text-gray-500 hover:border-gray-900 dark:hover:border-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                    Bloquear usuario
                </button>
                @endif
            </div>
            @endif

        </div>

        <!-- Documentos -->
        <div class="md:col-span-8">
            <p class="text-xs text-gray-600 dark:text-gray-600 uppercase tracking-widest mb-5">Documentos</p>

            @if($user->documents)
            <div class="grid grid-cols-2 gap-3 md:gap-5">
                @foreach([
                    'Selfie'              => $user->documents->selfie,
                    'Recibo de servicios' => $user->documents->utility_bill,
                    'DNI — Frontal'       => $user->documents->dni_front,
                    'DNI — Posterior'     => $user->documents->dni_back,
                ] as $label => $path)
                <div>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">{{ $label }}</p>
                    @if($path)
                        <button onclick="openLightbox('{{ Storage::url($path) }}', '{{ $label }}')" class="block w-full group text-left">
                            <div class="relative overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-900" style="aspect-ratio: 4/3;">
                                <img src="{{ Storage::url($path) }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </button>
                    @else
                        <div class="rounded-lg bg-gray-100 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 flex items-center justify-center text-gray-400 dark:text-gray-500 text-xs" style="aspect-ratio: 4/3;">
                            Sin imagen
                        </div>
                    @endif
                </div>
                @endforeach
            </div>
            @else
                <p class="text-sm text-gray-400 dark:text-gray-500">Este usuario no ha subido documentos.</p>
            @endif
        </div>

    </div>
</div>

<!-- Modal advertencia edición -->
<div id="editWarningModal" class="hidden fixed inset-0 z-50 bg-black/70 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 w-full max-w-sm shadow-2xl text-center">
        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4">
            <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Datos sensibles</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
            Si vas a modificar estos datos, son muy sensibles, así que procede con cuidado.
        </p>
        <div class="flex gap-3">
            <button onclick="closeEditWarningModal()"
                    class="flex-1 py-2.5 text-sm font-medium rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                Cancelar
            </button>
            <button onclick="enableEditMode()"
                    class="flex-1 py-2.5 text-sm font-semibold rounded-lg bg-yellow-500 hover:bg-yellow-600 text-white transition-colors">
                Editar
            </button>
        </div>
    </div>
</div>

<!-- Modal campo edición -->
<div id="editFieldModal" class="hidden fixed inset-0 z-50 bg-black/70 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 w-full max-w-sm shadow-2xl">
        <h3 id="editFieldTitle" class="text-base font-bold text-gray-900 dark:text-white mb-4"></h3>
        <form action="{{ route('superadmin.users.update-field', $user) }}" method="POST" onsubmit="return validateField()">
            @csrf
            @method('PATCH')
            <input type="hidden" name="field" id="editFieldName">
            <div class="mb-5">
                <input type="text" name="value" id="editFieldValue"
                       class="w-full bg-transparent border-b border-gray-200 dark:border-gray-700 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 py-2 focus:outline-none focus:border-blue-500 transition-colors">
                <p id="editFieldError" class="text-[10px] text-red-500 mt-1 hidden"></p>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeEditFieldModal()"
                        class="flex-1 py-2.5 text-sm font-medium rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 text-sm font-semibold rounded-lg bg-blue-500 hover:bg-blue-600 text-white transition-colors">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal bloquear usuario -->
<div id="blockModal" class="hidden fixed inset-0 z-50 bg-black/70 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 w-full max-w-sm shadow-2xl">
        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-800 mx-auto mb-4">
            <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
            </svg>
        </div>
        <h3 class="text-base font-bold text-gray-900 dark:text-white text-center mb-1">Bloquear usuario</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-5">
            Se eliminarán sus documentos y no podrá volver a registrarse con sus datos.
        </p>

        <form action="{{ route('superadmin.users.block', $user) }}" method="POST">
            @csrf
            <div class="mb-5">
                <label class="block text-xs text-gray-500 dark:text-gray-500 mb-2">Motivo <span class="text-gray-400">(opcional)</span></label>
                <input type="text" name="reason" placeholder="Ej: Actividad sospechosa..."
                       class="w-full bg-transparent border-b border-gray-200 dark:border-gray-700 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 py-2 focus:outline-none focus:border-gray-900 dark:focus:border-gray-400 transition-colors">
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeBlockModal()"
                        class="flex-1 py-2.5 text-sm font-medium rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 text-sm font-semibold rounded-lg bg-gray-900 dark:bg-white text-white dark:text-black hover:bg-gray-700 dark:hover:bg-gray-100 transition-colors">
                    Bloquear
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal confirmar rechazo -->
<div id="rejectModal" class="hidden fixed inset-0 z-50 bg-black/70 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 w-full max-w-sm shadow-2xl">
        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 dark:bg-red-500/10 mx-auto mb-4">
            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>
        <h3 class="text-base font-bold text-gray-900 dark:text-white text-center mb-1">Rechazar usuario</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-5">
            Esta acción es <span class="font-semibold text-red-500">irreversible</span>. Indica el motivo del rechazo.
        </p>

        <form action="{{ route('superadmin.users.reject', $user) }}" method="POST">
            @csrf

            <!-- Motivos predefinidos -->
            <div class="space-y-2 mb-4">
                @foreach([
                    'El DNI no es legible o está incorrecto',
                    'La selfie no coincide con el DNI',
                    'El recibo de servicios no es válido',
                    'Los datos personales no coinciden',
                    'Documentos vencidos o alterados',
                ] as $motivo)
                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-800 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors has-[:checked]:border-red-400 has-[:checked]:bg-red-50 dark:has-[:checked]:bg-red-500/10">
                    <input type="radio" name="reason" value="{{ $motivo }}"
                           class="accent-red-500 flex-shrink-0"
                           onchange="document.getElementById('customReason').value = ''">
                    <span class="text-xs text-gray-700 dark:text-gray-300">{{ $motivo }}</span>
                </label>
                @endforeach

                <!-- Motivo personalizado -->
                <div class="pt-1">
                    <input type="text" id="customReason" placeholder="Otro motivo..."
                           class="w-full bg-transparent border-b border-gray-200 dark:border-gray-700 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 py-2 focus:outline-none focus:border-red-400 transition-colors"
                           oninput="uncheckRadios()">
                </div>
            </div>

            <div class="flex gap-3 mt-5">
                <button type="button" onclick="closeRejectModal()"
                        class="flex-1 py-2.5 text-sm font-medium rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    Cancelar
                </button>
                <button type="submit" onclick="return setCustomReason()"
                        class="flex-1 py-2.5 text-sm font-semibold rounded-lg bg-red-500 hover:bg-red-600 text-white transition-colors">
                    Rechazar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal confirmar aprobación -->
<div id="approveModal" class="hidden fixed inset-0 z-50 bg-black/70 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 w-full max-w-sm shadow-2xl">
        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-green-100 dark:bg-green-500/10 mx-auto mb-4">
            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h3 class="text-base font-bold text-gray-900 dark:text-white text-center mb-1">Aprobar usuario</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6">
            ¿Confirmas que quieres aprobar a <span class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</span>?
        </p>
        <div class="flex gap-3">
            <button onclick="closeApproveModal()"
                    class="flex-1 py-2.5 text-sm font-medium rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                Cancelar
            </button>
            <form action="{{ route('superadmin.users.approve', $user) }}" method="POST" class="flex-1">
                @csrf
                <button type="submit"
                        class="w-full py-2.5 text-sm font-semibold rounded-lg bg-green-500 hover:bg-green-600 text-white transition-colors">
                    Sí, aprobar
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Lightbox modal -->
<div id="lightbox" onclick="closeLightbox(event)"
     class="hidden fixed inset-0 z-50 bg-black/90 flex flex-col items-center justify-center p-4">

    <!-- Header -->
    <div class="flex items-center justify-between w-full max-w-4xl mb-3 flex-shrink-0">
        <p id="lightboxLabel" class="text-sm text-gray-400"></p>
        <div class="flex items-center gap-3">
            <!-- Zoom out -->
            <button onclick="zoom(-0.25)" class="p-2 text-gray-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"/>
                </svg>
            </button>
            <!-- Zoom level -->
            <span id="zoomLevel" class="text-xs text-gray-500 w-10 text-center">100%</span>
            <!-- Zoom in -->
            <button onclick="zoom(0.25)" class="p-2 text-gray-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                </svg>
            </button>
            <!-- Reset -->
            <button onclick="resetZoom()" class="p-2 text-gray-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </button>
            <!-- Cerrar -->
            <button onclick="closeLightbox()" class="p-2 text-gray-400 hover:text-white transition-colors ml-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Imagen con scroll si hace zoom -->
    <div class="overflow-hidden w-full max-w-4xl flex items-center justify-center" style="max-height: 80vh;">
        <img id="lightboxImg" src="" alt=""
             class="transition-transform duration-200 rounded-lg select-none"
             style="transform-origin: center; max-width: 100%;">
    </div>
</div>

<script>
let currentScale = 1;

function openBlockModal() {
    document.getElementById('blockModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeBlockModal() {
    document.getElementById('blockModal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.getElementById('blockModal').addEventListener('click', e => {
    if (e.target === document.getElementById('blockModal')) closeBlockModal();
});

function openRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function uncheckRadios() {
    document.querySelectorAll('#rejectModal input[type=radio]').forEach(r => r.checked = false);
}

function setCustomReason() {
    const custom = document.getElementById('customReason').value.trim();
    const checked = document.querySelector('#rejectModal input[type=radio]:checked');
    if (!custom && !checked) {
        alert('Debes seleccionar o escribir un motivo de rechazo.');
        return false;
    }
    if (custom) {
        // Inyectar el valor custom en un hidden input
        const form = document.querySelector('#rejectModal form');
        let hidden = form.querySelector('input[name=reason][type=hidden]');
        if (!hidden) {
            hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'reason';
            form.appendChild(hidden);
        }
        // Desmarcar radios para que no compitan
        document.querySelectorAll('#rejectModal input[type=radio]').forEach(r => r.disabled = true);
        hidden.value = custom;
    }
    return true;
}

document.getElementById('rejectModal').addEventListener('click', e => {
    if (e.target === document.getElementById('rejectModal')) closeRejectModal();
});

    function openEditWarningModal() {
        document.getElementById('editWarningModal').classList.remove('hidden');
    }

    function closeEditWarningModal() {
        document.getElementById('editWarningModal').classList.add('hidden');
    }

    function enableEditMode() {
        closeEditWarningModal();
        document.querySelectorAll('.edit-action-btn').forEach(btn => {
            btn.classList.remove('hidden');
        });
        // Desactivar links de whatsapp
        document.querySelectorAll('.wssp-link').forEach(link => {
            link.classList.add('pointer-events-none');
        });
    }

    function openEditFieldModal(label, field, value) {
        const titleEl = document.getElementById('editFieldTitle');
        const fieldNameEl = document.getElementById('editFieldName');
        const fieldValueEl = document.getElementById('editFieldValue');
        const errorEl = document.getElementById('editFieldError');

        titleEl.textContent = 'Editar ' + label;
        fieldNameEl.value = field;
        fieldValueEl.value = value;
        errorEl.textContent = '';
        errorEl.classList.add('hidden');

        // Reset inputs
        fieldValueEl.oninput = null;
        fieldValueEl.maxLength = 255;

        // Configurar validaciones según el campo
        if (field === 'first_name') {
            fieldValueEl.placeholder = 'Solo letras';
        } else if (field === 'last_name') {
            fieldValueEl.placeholder = 'Mínimo dos apellidos';
        } else if (field === 'dni') {
            fieldValueEl.maxLength = 8;
            fieldValueEl.placeholder = '8 dígitos numéricos';
            fieldValueEl.oninput = function() { this.value = this.value.replace(/\D/g, '').slice(0, 8); };
        } else if (field === 'phone') {
            fieldValueEl.maxLength = 9;
            fieldValueEl.placeholder = '9 dígitos numéricos';
            fieldValueEl.oninput = function() { this.value = this.value.replace(/\D/g, '').slice(0, 9); };
        } else if (field === 'address') {
            fieldValueEl.maxLength = 50;
            fieldValueEl.placeholder = 'Máximo 50 caracteres';
        } else if (field === 'loan_interest_rate') {
            fieldValueEl.placeholder = 'Ej: 20.00';
            fieldValueEl.oninput = function() {
                let start = this.selectionStart;
                let oldVal = this.value;
                let val = oldVal.replace(/[^0-9.]/g, '');
                let parts = val.split('.');
                if(parts[0].length > 2) {
                    val = parts[0].slice(0, 2) + '.' + parts[0].slice(2) + (parts[1] || '');
                }
                parts = val.split('.');
                if(parts[1] && parts[1].length > 2) {
                    val = parts[0] + '.' + parts[1].slice(0, 2);
                }
                if(val.length > 1 && val.startsWith('0') && val[1] !== '.') {
                    val = val.replace(/^0+/, '');
                }
                if (oldVal !== val) {
                    this.value = val;
                    this.setSelectionRange(start, start);
                }
            };
        }

        document.getElementById('editFieldModal').classList.remove('hidden');
    }

    function validateField() {
        const field = document.getElementById('editFieldName').value;
        const value = document.getElementById('editFieldValue').value.trim();
        const errorEl = document.getElementById('editFieldError');
        let error = '';

        if (field === 'first_name') {
            if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(value)) error = 'El nombre solo puede contener letras.';
        } else if (field === 'last_name') {
            if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(value)) {
                error = 'Los apellidos solo pueden contener letras.';
            } else if (value.split(/\s+/).filter(word => word.length > 0).length < 2) {
                error = 'Debes ingresar al menos dos apellidos.';
            }
        } else if (field === 'dni') {
            if (!/^\d{8}$/.test(value)) error = 'El DNI debe tener exactamente 8 dígitos numéricos.';
        } else if (field === 'phone') {
            if (!/^\d{9}$/.test(value)) error = 'El teléfono debe tener exactamente 9 dígitos numéricos.';
        } else if (field === 'address') {
            if (value.length > 50) error = 'La dirección no puede exceder los 50 caracteres.';
        } else if (field === 'loan_interest_rate') {
            const num = parseFloat(value);
            if (isNaN(num) || num < 0 || num > 100) {
                error = 'La tasa debe ser un número entre 0 y 100.';
            }
        }

        if (error) {
            errorEl.textContent = error;
            errorEl.classList.remove('hidden');
            return false;
        }
        return true;
    }

    function closeEditFieldModal() {
        document.getElementById('editFieldModal').classList.add('hidden');
    }

    function openApproveModal() {
    document.getElementById('approveModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.getElementById('approveModal').addEventListener('click', e => {
    if (e.target === document.getElementById('approveModal')) closeApproveModal();
});

function openLightbox(src, label) {
    currentScale = 1;
    document.getElementById('lightboxImg').src = src;
    document.getElementById('lightboxImg').style.transform = 'scale(1)';
    document.getElementById('lightboxLabel').textContent = label;
    document.getElementById('zoomLevel').textContent = '100%';
    document.getElementById('lightbox').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeLightbox(e) {
    if (e && e.target !== document.getElementById('lightbox')) return;
    document.getElementById('lightbox').classList.add('hidden');
    document.body.style.overflow = '';
}

function zoom(delta) {
    currentScale = Math.min(4, Math.max(0.25, currentScale + delta));
    document.getElementById('lightboxImg').style.transform = `scale(${currentScale})`;
    document.getElementById('zoomLevel').textContent = Math.round(currentScale * 100) + '%';
}

function resetZoom() {
    currentScale = 1;
    document.getElementById('lightboxImg').style.transform = 'scale(1)';
    document.getElementById('zoomLevel').textContent = '100%';
}

// Cerrar con Escape
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.getElementById('lightbox').classList.add('hidden');
        document.body.style.overflow = '';
    }
    if (e.key === '+' || e.key === '=') zoom(0.25);
    if (e.key === '-') zoom(-0.25);
});

// Zoom con rueda del mouse (en todo el modal)
document.getElementById('lightbox').addEventListener('wheel', e => {
    if (document.getElementById('lightbox').classList.contains('hidden')) return;
    e.preventDefault();
    zoom(e.deltaY < 0 ? 0.15 : -0.15);
}, { passive: false });

function openDeleteGhostModal() {
    document.getElementById('deleteGhostModal').classList.remove('hidden');
}

function closeDeleteGhostModal() {
    document.getElementById('deleteGhostModal').classList.add('hidden');
}
</script>

@if($user->is_ghost)
    <!-- Modal eliminar usuario fantasma -->
    <div id="deleteGhostModal" class="hidden fixed inset-0 z-50 bg-black/70 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 w-full max-w-sm shadow-2xl text-center">
            <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 dark:bg-red-500/10 mx-auto mb-4">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Eliminar usuario fantasma</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                ¿Estás seguro de que deseas eliminar permanentemente a este usuario fantasma? Esta acción no se puede
                deshacer.
            </p>

            <form action="{{ route('superadmin.users.destroy-ghost', $user) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex gap-3">
                    <button type="button" onclick="closeDeleteGhostModal()"
                        class="flex-1 py-2.5 text-sm font-medium rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 py-2.5 text-sm font-semibold rounded-lg bg-red-500 hover:bg-red-600 text-white transition-colors">
                        Eliminar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

@include('partials.global-scripts')
@endsection
