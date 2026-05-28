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
            <div class="flex items-center gap-3">
                <h1 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white leading-tight truncate">{{ $user->name }}</h1>
                @if(!$user->is_ghost && $user->documents && $user->documents->status === 'approved')
                <button type="button" onclick="openEmitContractModal({{ $user->id }})"
                        class="flex-shrink-0 px-3 py-1.5 text-xs font-semibold rounded-lg bg-orange-500 hover:bg-orange-600 text-white transition-colors shadow-sm">
                    Emitir contrato
                </button>
                @endif
            </div>
            <div class="flex items-center gap-1.5 mt-1">
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
                    @php $cp = $user->clientProfile; @endphp
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
                        'Tipo Persona' => [
                            'value' => $user->person_type === 'juridica' ? 'Jurídica' : 'Natural',
                        ],
                        'RUC'          => $user->person_type === 'juridica' ? [
                            'value' => $user->ruc,
                            'editable' => true,
                            'field' => 'ruc'
                        ] : null,
                        'Razón Social' => $user->person_type === 'juridica' ? [
                            'value' => $user->business_name,
                            'editable' => true,
                            'field' => 'business_name'
                        ] : null,
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
                        'Estado Civil'  => $cp && $cp->marital_status ? [
                            'value' => match($cp->marital_status) {
                                'soltero' => 'Soltero/a',
                                'casado_separado' => 'Casado/a — bienes separados',
                                'casado_compartido' => 'Casado/a — bienes compartidos',
                                'divorciado' => 'Divorciado/a',
                                'viudo' => 'Viudo/a',
                                'conviviente' => 'Conviviente',
                                default => $cp->marital_status
                            }
                        ] : null,
                        'Hijos'        => $cp ? [
                            'value' => $cp->has_children
                                ? (($cp->children_count ?? 1) . (($cp->children_count ?? 1) == 1 ? ' hijo' : ' hijos'))
                                : 'No tiene'
                        ] : null,
                        'Empleo'       => $cp ? [
                            'value' => $cp->is_employed
                                ? ($cp->employment_type === 'independent'
                                    ? 'Independiente' . ($cp->independent_type ? ' — ' . $cp->independent_type : '')
                                    : 'Dependiente')
                                : 'Sin empleo'
                        ] : null,
                        'Lugar trabajo'=> ($cp && $cp->is_employed && $cp->workplace) ? ['value' => $cp->workplace] : null,
                        'Cargo'        => ($cp && $cp->is_employed && $cp->job_title) ? ['value' => $cp->job_title] : null,
                        'Ingreso mensual' => ($cp && $cp->is_employed && $cp->monthly_income) ? [
                            'value' => 'S/ ' . number_format($cp->monthly_income, 2, '.', ',')
                        ] : null,
                        'Estado académico' => ($cp && $cp->academic_status) ? [
                            'value' => $cp->academic_status
                        ] : null,
                        'Tipo educación' => ($cp && $cp->academic_status && str_contains($cp->academic_status, 'Superior —')) ? [
                            'value' => preg_replace('/Superior — (.+) \(.+/', '$1', $cp->academic_status)
                        ] : null,
                        'Nivel académico' => ($cp && $cp->academic_status && str_contains($cp->academic_status, 'Superior —')) ? [
                            'value' => preg_replace('/Superior — .+ \((.+)\)/', '$1', $cp->academic_status)
                        ] : null,
                        'Institución'  => ($cp && $cp->institution) ? ['value' => $cp->institution] : null,
                        'Carrera'      => ($cp && $cp->career) ? ['value' => $cp->career] : null,
                        'Instagram'    => ($cp && $cp->instagram) ? [
                            'value' => $cp->instagram,
                            'icon' => 'insta',
                            'link' => 'https://instagram.com/' . ltrim($cp->instagram, '@'),
                            'is_instagram' => true
                        ] : null,
                        'Cónyuge Nombres' => ($cp && $cp->marital_status === 'casado_compartido' && $cp->spouse_first_name) ? ['value' => $cp->spouse_first_name] : null,
                        'Cónyuge Apellidos' => ($cp && $cp->marital_status === 'casado_compartido' && $cp->spouse_last_name) ? ['value' => $cp->spouse_last_name] : null,
                        'Cónyuge DNI' => ($cp && $cp->marital_status === 'casado_compartido' && $cp->spouse_dni) ? ['value' => $cp->spouse_dni] : null,
                        'Tasa de interés' => ($status === 'approved' && !$user->is_ghost) ? [
                            'value' => number_format($user->loan_interest_rate ?? 20, 2, '.', ''),
                            'editable' => true,
                            'field' => 'loan_interest_rate',
                            'suffix' => '%'
                        ] : null,
                        'Línea de crédito' => ($status === 'approved' && !$user->is_ghost) ? [
                            'value' => number_format($user->credit_line ?? 500, 2, '.', ','),
                            'editable' => true,
                            'field' => 'credit_line',
                            'prefix' => 'S/ '
                        ] : null,
                        'Registro'     => $user->created_at->format('d/m/Y H:i'),
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
                        @elseif(is_array($data) && isset($data['is_instagram']))
                            <a href="{{ $data['link'] }}" target="_blank"
                               class="flex justify-between items-center py-3 px-3 -mx-3 bg-gradient-to-r from-purple-600 via-pink-500 to-orange-400 mb-2 hover:opacity-90 transition-opacity border border-white shadow-sm">
                                <span class="text-xs text-white flex-shrink-0">{{ $label }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-white text-right">{{ $value ?? '—' }}</span>
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="white">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                                    </svg>
                                </div>
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
                                        {{ is_array($data) && isset($data['prefix']) ? $data['prefix'] : '' }}{{ $value ?? '—' }}{{ is_array($data) && isset($data['suffix']) ? $data['suffix'] : '' }}
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

        <!-- Contenido principal -->
        <div class="md:col-span-8">
            @if($user->is_ghost)
                @php
                    $cashboxes = \App\Models\Cashbox::where('is_active', true)->orderBy('name')->get();
                    $ghostMovements = $user->ghostMovements()->with('cashbox')->latest()->paginate(10);
                    $totalIncome = $user->ghostMovements()->where('type', 'income')->sum('amount');
                    $totalExpense = $user->ghostMovements()->where('type', 'expense')->sum('amount');
                    $balance = $totalIncome - $totalExpense;
                @endphp

                <div class="mb-6 p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">Usuario fantasma — Solo panel interno</p>
                            <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">Este usuario <strong>no figurará</strong> en las secciones de inversiones ni préstamos. Sus movimientos solo son visibles aquí y en la caja asignada.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3 mb-6">
                    <div class="p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                        <p class="text-[10px] uppercase tracking-widest text-green-600 dark:text-green-400 mb-1">Ingresos</p>
                        <p class="text-lg font-bold text-green-700 dark:text-green-300">{{ $user->ghost_currency }} {{ number_format($totalIncome, 2) }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                        <p class="text-[10px] uppercase tracking-widest text-red-600 dark:text-red-400 mb-1">Egresos</p>
                        <p class="text-lg font-bold text-red-700 dark:text-red-300">{{ $user->ghost_currency }} {{ number_format($totalExpense, 2) }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                        <p class="text-[10px] uppercase tracking-widest text-gray-600 dark:text-gray-400 mb-1">Balance</p>
                        <p class="text-lg font-bold {{ $balance >= 0 ? 'text-gray-900 dark:text-white' : 'text-red-600 dark:text-red-400' }}">{{ $user->ghost_currency }} {{ number_format($balance, 2) }}</p>
                    </div>
                </div>

                <div class="mb-8">
                    <p class="text-xs text-gray-600 dark:text-gray-600 uppercase tracking-widest mb-4">Registrar movimiento</p>
                    <form action="{{ route('superadmin.users.ghost-movement', $user) }}" method="POST" class="p-5 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/50">
                        @csrf
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label class="block text-[10px] uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1.5">Tipo de movimiento</label>
                                <select name="type" id="ghost-type" required class="w-full bg-white dark:bg-black border border-gray-200 dark:border-gray-800 rounded-lg text-sm px-3 py-2.5 text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 transition-colors">
                                    <option value="">Seleccionar</option>
                                    <option value="income">Ingreso</option>
                                    <option value="expense">Egreso</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1.5">Razón</label>
                                <select name="reason" id="ghost-reason" required class="w-full bg-white dark:bg-black border border-gray-200 dark:border-gray-800 rounded-lg text-sm px-3 py-2.5 text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 transition-colors">
                                    <option value="">Seleccionar</option>
                                    <option value="loan">Préstamo</option>
                                    <option value="investment">Inversión</option>
                                    <option value="other">Otro</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1.5">Moneda</label>
                                <select name="currency" id="ghost-currency" required class="w-full bg-white dark:bg-black border border-gray-200 dark:border-gray-800 rounded-lg text-sm px-3 py-2.5 text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 transition-colors">
                                    <option value="PEN">PEN (Soles)</option>
                                    <option value="USD">USD (Dólares)</option>
                                </select>
                            </div>
                        </div>
                        <div id="ghost-code-preview" class="hidden mb-4 p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                                <p class="text-xs text-blue-700 dark:text-blue-300">Se asignará el código: <strong id="ghost-code-value" class="font-mono">—</strong></p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-[10px] uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1.5">Monto</label>
                                <input type="hidden" name="amount" id="ghost-amount-hidden" value="">
                                <input type="text" id="ghost-amount-display" inputmode="numeric" required placeholder="0.00" autocomplete="off"
                                    class="w-full bg-white dark:bg-black border border-gray-200 dark:border-gray-800 rounded-lg text-sm px-3 py-2.5 text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 transition-colors text-right font-mono">
                            </div>
                            <div>
                                <label class="block text-[10px] uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1.5">Caja</label>
                                <select name="cashbox_id" id="ghost-cashbox" required class="w-full bg-white dark:bg-black border border-gray-200 dark:border-gray-800 rounded-lg text-sm px-3 py-2.5 text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 transition-colors">
                                    @foreach($cashboxes as $cb)
                                        <option value="{{ $cb->id }}" data-currency="{{ $cb->currency }}" {{ $cb->id === $user->ghost_cashbox_id ? 'selected' : '' }}>{{ $cb->name }} ({{ $cb->currency }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1.5">Descripción</label>
                                <input type="text" name="description" required maxlength="255" placeholder="Descripción del movimiento" class="w-full bg-white dark:bg-black border border-gray-200 dark:border-gray-800 rounded-lg text-sm px-3 py-2.5 text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 transition-colors">
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <button type="submit" class="px-5 py-2.5 text-sm font-semibold rounded-lg bg-purple-600 hover:bg-purple-700 text-white transition-colors">
                                Registrar movimiento
                            </button>
                        </div>
                    </form>
                    <script>
                        (function() {
                            const reasonEl = document.getElementById('ghost-reason');
                            const codePreview = document.getElementById('ghost-code-preview');
                            const codeValue = document.getElementById('ghost-code-value');
                            const existingCode = {{ $user->ghost_code ? 'true' : 'false' }};

                            function updateCodePreview() {
                                if (existingCode) {
                                    codePreview.classList.add('hidden');
                                    return;
                                }
                                const reason = reasonEl.value;
                                if (!reason) {
                                    codePreview.classList.add('hidden');
                                    return;
                                }
                                codeValue.textContent = '...';
                                codePreview.classList.remove('hidden');

                                fetch('{{ route('superadmin.users.ghost-next-code') }}?reason=' + encodeURIComponent(reason), {
                                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                                })
                                .then(r => r.json())
                                .then(data => {
                                    codeValue.textContent = data.code || '—';
                                })
                                .catch(() => {
                                    codeValue.textContent = '—';
                                });
                            }

                            reasonEl.addEventListener('change', updateCodePreview);

                            const amountDisplay = document.getElementById('ghost-amount-display');
                            const amountHidden = document.getElementById('ghost-amount-hidden');
                            let rawCents = '';

                            function formatAmount(cents) {
                                if (!cents || cents === '0') return '0.00';
                                const num = parseInt(cents, 10) / 100;
                                return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            }

                            amountDisplay.addEventListener('input', function(e) {
                                const digits = this.value.replace(/\D/g, '');
                                rawCents = digits;
                                const formatted = formatAmount(digits);
                                this.value = formatted;
                                const decimal = digits ? (parseInt(digits, 10) / 100).toFixed(2) : '';
                                amountHidden.value = decimal;
                            });

                            amountDisplay.addEventListener('keydown', function(e) {
                                if (e.key === 'Backspace') {
                                    e.preventDefault();
                                    rawCents = rawCents.slice(0, -1);
                                    this.value = formatAmount(rawCents);
                                    amountHidden.value = rawCents ? (parseInt(rawCents, 10) / 100).toFixed(2) : '';
                                }
                            });

                            amountDisplay.addEventListener('focus', function() {
                                if (!this.value) this.value = '0.00';
                            });

                            const form = amountDisplay.closest('form');
                            if (form) {
                                form.addEventListener('submit', function() {
                                    if (!amountHidden.value) {
                                        amountHidden.value = amountDisplay.value;
                                    }
                                }, true);
                            }

                            const currencyEl = document.getElementById('ghost-currency');
                            const cashboxEl = document.getElementById('ghost-cashbox');

                            function filterCashboxes() {
                                const cur = currencyEl.value;
                                const opts = cashboxEl.querySelectorAll('option');
                                let firstVisible = null;
                                opts.forEach(function(opt) {
                                    const match = opt.dataset.currency === cur;
                                    opt.hidden = !match;
                                    opt.disabled = !match;
                                    if (match && !firstVisible) firstVisible = opt;
                                });
                                const selected = cashboxEl.options[cashboxEl.selectedIndex];
                                if (!selected || selected.dataset.currency !== cur) {
                                    cashboxEl.value = firstVisible ? firstVisible.value : '';
                                }
                            }

                            currencyEl.addEventListener('change', filterCashboxes);
                            filterCashboxes();
                        })();
                    </script>
                </div>

                <div id="ghost-movements-section">
                    <p class="text-xs text-gray-600 dark:text-gray-600 uppercase tracking-widest mb-4">Historial de movimientos</p>
                    @if($ghostMovements->count() > 0)
                        <div class="space-y-2">
                            @foreach($ghostMovements as $mov)
                                @php $isHighlighted = request('highlight_movement') == $mov->id; @endphp
                                <div id="ghost-mov-{{ $mov->id }}" class="flex items-center justify-between p-3 rounded-lg border {{ $isHighlighted ? 'border-amber-400 dark:border-amber-500 bg-amber-50 dark:bg-amber-900/20 ring-2 ring-amber-400/30' : 'border-gray-200 dark:border-gray-800 bg-white dark:bg-black' }} transition-all duration-500">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $mov->type === 'income' ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30' }}">
                                            @if($mov->type === 'income')
                                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                            @else
                                                <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $mov->description }}</p>
                                            <p class="text-[10px] text-gray-500 dark:text-gray-500">{{ $mov->created_at->format('d/m/Y H:i') }} · {{ $mov->cashbox->name ?? '—' }}</p>
                                        </div>
                                    </div>
                                    <p class="text-sm font-bold {{ $mov->type === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $mov->type === 'income' ? '+' : '-' }}{{ $mov->cashbox->currency ?? 'PEN' }} {{ number_format($mov->amount, 2) }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            {{ $ghostMovements->withQueryString()->links() }}
                        </div>
                        @if(request('highlight_movement'))
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const el = document.getElementById('ghost-mov-{{ request('highlight_movement') }}');
                                    if (el) {
                                        setTimeout(function() {
                                            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                        }, 300);
                                        setTimeout(function() {
                                            el.classList.remove('ring-2', 'ring-amber-400/30');
                                            el.classList.add('ring-0');
                                        }, 5000);
                                    }
                                });
                            </script>
                        @endif
                    @else
                        <p class="text-sm text-gray-400 dark:text-gray-500">Sin movimientos registrados.</p>
                    @endif
                </div>
            @else
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
            @endif
        </div>

    </div>
</div>

@php
    $profileChangeRequests = $user->profileChangeRequests()->latest()->get();
    $pendingRequests = $profileChangeRequests->where('status', 'pending');
@endphp

@if($pendingRequests->count() > 0)
<div class="mt-6 px-5 md:px-10">
    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b border-amber-200 dark:border-amber-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-sm font-bold text-amber-800 dark:text-amber-300">Solicitudes de cambio de perfil</h3>
            </div>
            <span class="text-xs font-semibold bg-amber-500 text-white px-2 py-0.5 rounded-full">{{ $pendingRequests->count() }}</span>
        </div>

        <div class="px-5 py-3 border-b border-amber-100 dark:border-amber-900/40 bg-amber-100/50 dark:bg-amber-900/10">
            <div class="flex flex-wrap items-center gap-3 text-xs text-amber-700 dark:text-amber-400">
                <span class="font-medium">Contacto:</span>
                <a href="mailto:{{ $user->email }}" class="flex items-center gap-1 hover:underline">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    {{ $user->email }}
                </a>
                @if($user->phone)
                <a href="https://wa.me/51{{ preg_replace('/\D/', '', $user->phone) }}" target="_blank" class="flex items-center gap-1 hover:underline">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492a.5.5 0 00.611.611l4.458-1.495A11.943 11.943 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-2.317 0-4.474-.693-6.282-1.882l-.438-.293-2.645.887.887-2.645-.293-.438A9.955 9.955 0 012 12C2 6.486 6.486 2 12 2s10 4.486 10 10-4.486 10-10 10z"/></svg>
                    {{ $user->phone }}
                </a>
                @endif
            </div>
        </div>

        @foreach($pendingRequests as $req)
        <div class="px-5 py-4 {{ !$loop->last ? 'border-b border-amber-100 dark:border-amber-900/40' : '' }}">
            <p class="text-xs text-amber-600 dark:text-amber-400 mb-3">Solicitado el {{ $req->created_at->format('d/m/Y H:i') }}</p>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs text-amber-700 dark:text-amber-400 uppercase tracking-wider">
                            <th class="text-left py-2 pr-4 font-medium">Campo</th>
                            <th class="text-left py-2 pr-4 font-medium">Actual</th>
                            <th class="text-left py-2 font-medium">Nuevo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-amber-100 dark:divide-amber-900/30">
                        @php
                            $labels = \App\Models\ProfileChangeRequest::fieldLabels();
                            $changed = collect($req->new_data)->filter(fn($v, $k) => (string)($req->old_data[$k] ?? '') !== (string)$v);
                        @endphp
                        @foreach($changed as $field => $newValue)
                        <tr>
                            <td class="py-2 pr-4 text-gray-700 dark:text-gray-300 font-medium text-xs">{{ $labels[$field] ?? $field }}</td>
                            <td class="py-2 pr-4 text-red-600 dark:text-red-400 text-xs line-through">{{ \App\Models\ProfileChangeRequest::formatValue($field, $req->old_data[$field] ?? null) }}</td>
                            <td class="py-2 text-green-600 dark:text-green-400 text-xs font-medium">{{ \App\Models\ProfileChangeRequest::formatValue($field, $newValue) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="flex gap-2 mt-4">
                <form action="{{ route('superadmin.profile-changes.approve', $req) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-lg bg-green-600 hover:bg-green-700 text-white transition-colors">
                        Aprobar cambios
                    </button>
                </form>
                <button type="button" onclick="openRejectChangeModal({{ $req->id }})" class="px-4 py-2 text-xs font-semibold rounded-lg bg-red-500 hover:bg-red-600 text-white transition-colors">
                    Rechazar
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@if($profileChangeRequests->where('status', '!=', 'pending')->count() > 0)
<div class="mt-4 px-5 md:px-10">
    @if($profileChangeRequests->where('status', '!=', 'pending')->count() > 5)
    <div class="mb-2 flex items-center gap-2 px-3 py-2 rounded-xl bg-amber-50 dark:bg-amber-500/5 border border-amber-200 dark:border-amber-500/20">
        <svg class="w-3.5 h-3.5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <p class="text-[11px] font-medium text-amber-600 dark:text-amber-400">Este cliente modifica sus datos con frecuencia</p>
    </div>
    @endif
    <details class="bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 rounded-2xl">
        <summary class="px-5 py-3 cursor-pointer text-xs font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
            Historial de solicitudes ({{ $profileChangeRequests->where('status', '!=', 'pending')->count() }})
        </summary>
        <div class="px-5 pb-4 space-y-3">
            @foreach($profileChangeRequests->where('status', '!=', 'pending') as $req)
            @php
                $reqChanges = [];
                $fieldLabels = \App\Models\ProfileChangeRequest::fieldLabels();
                $oldData = $req->old_data ?? [];
                $newData = $req->new_data ?? [];
                foreach ($newData as $field => $newVal) {
                    $oldVal = $oldData[$field] ?? null;
                    $reqChanges[] = [
                        'label' => $fieldLabels[$field] ?? $field,
                        'old' => \App\Models\ProfileChangeRequest::formatValue($field, $oldVal),
                        'new' => \App\Models\ProfileChangeRequest::formatValue($field, $newVal),
                    ];
                }
            @endphp
            <div class="req-row flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-800' : '' }}">
                <div class="text-xs text-gray-600 dark:text-gray-400">
                    {{ $req->created_at->format('d/m/Y H:i') }}
                    @if($req->status === 'approved')
                        <span class="text-green-600 font-medium">— Aprobada</span>
                    @elseif($req->status === 'rejected')
                        <span class="text-red-500 font-medium">— Rechazada</span>
                    @else
                        <span class="text-gray-400 font-medium">— Cancelada</span>
                    @endif
                </div>
                <button type="button" onclick='openReqDetailModal({{ json_encode($reqChanges) }})' class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors">Ver detalles</button>
            </div>
            @endforeach
            <div id="req-pagination" class="flex items-center justify-center gap-3 pt-2" style="display:none">
                <button type="button" id="req-btn-prev" onclick="reqChangePage(-1)" class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">Anterior</button>
                <span id="req-page-info" class="text-xs font-medium text-gray-400 dark:text-gray-500"></span>
                <button type="button" id="req-btn-next" onclick="reqChangePage(1)" class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">Siguiente</button>
            </div>
        </div>
    </details>
</div>
<script>
(function() {
    const PER_PAGE = 5;
    let currentPage = 1;

    function getRows() {
        return Array.from(document.querySelectorAll('.req-row'));
    }

    function render() {
        const rows = getRows();
        const total = rows.length;
        const totalPages = Math.max(1, Math.ceil(total / PER_PAGE));
        if (currentPage > totalPages) currentPage = totalPages;

        rows.forEach(r => r.style.display = 'none');
        const start = (currentPage - 1) * PER_PAGE;
        rows.slice(start, start + PER_PAGE).forEach(r => r.style.display = '');

        const pagination = document.getElementById('req-pagination');
        pagination.style.display = totalPages <= 1 ? 'none' : 'flex';
        document.getElementById('req-page-info').textContent = totalPages > 1 ? currentPage + ' / ' + totalPages : '';
        document.getElementById('req-btn-prev').disabled = currentPage === 1;
        document.getElementById('req-btn-next').disabled = currentPage === totalPages;
    }

    window.reqChangePage = function(delta) {
        currentPage += delta;
        render();
    };

    const details = document.querySelector('details');
    if (details) {
        details.addEventListener('toggle', function() {
            if (details.open) {
                currentPage = 1;
                render();
            }
        });
    }
})();
</script>

<div id="reqDetailModal" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-950 rounded-2xl w-full max-w-sm shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Detalles del cambio</h3>
            <button type="button" onclick="closeReqDetailModal()" class="w-8 h-8 rounded-xl flex items-center justify-center text-gray-400 dark:text-gray-600 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="reqDetailContent" class="px-5 py-4 space-y-3 max-h-[60vh] overflow-y-auto"></div>
    </div>
</div>

<script>
function openReqDetailModal(changes) {
    const container = document.getElementById('reqDetailContent');
    container.innerHTML = changes.map(c => `
        <div class="py-2 border-b border-gray-100 dark:border-gray-800 last:border-0">
            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1.5">${c.label}</p>
            <div class="flex items-center gap-2 text-xs">
                <span class="text-red-400 line-through flex-1">${c.old}</span>
                <svg class="w-3 h-3 text-gray-300 dark:text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-green-600 dark:text-green-400 font-medium flex-1">${c.new}</span>
            </div>
        </div>
    `).join('');
    document.getElementById('reqDetailModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeReqDetailModal() {
    document.getElementById('reqDetailModal').classList.add('hidden');
    document.body.style.overflow = '';
}
document.getElementById('reqDetailModal').addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeReqDetailModal();
});
</script>
@endif

<div id="rejectChangeModal" class="hidden fixed inset-0 z-50 bg-black/70 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 w-full max-w-sm shadow-2xl">
        <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Rechazar solicitud</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Indica el motivo del rechazo (opcional).</p>
        <form id="rejectChangeForm" method="POST">
            @csrf
            <textarea name="rejection_reason" rows="3" class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="Motivo del rechazo..."></textarea>
            <div class="flex gap-3 mt-4">
                <button type="button" onclick="closeRejectChangeModal()" class="flex-1 py-2.5 text-sm font-medium rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">Cancelar</button>
                <button type="submit" class="flex-1 py-2.5 text-sm font-semibold rounded-lg bg-red-500 hover:bg-red-600 text-white transition-colors">Rechazar</button>
            </div>
        </form>
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
     class="hidden fixed inset-0 z-50 bg-black/90 flex flex-col items-center justify-center p-4"
     onwheel="if(!this.classList.contains('hidden')){event.preventDefault();zoom(event.deltaY<0?0.15:-0.15)}">

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

    <!-- Imagen con zoom y pan -->
    <div id="lightboxImgContainer"
         class="overflow-hidden w-full max-w-4xl flex items-center justify-center rounded-lg relative"
         style="max-height: 80vh; cursor: grab;">
        <img id="lightboxImg" src="" alt=""
             class="transition-transform duration-100 rounded-lg select-none"
             style="transform-origin: center; max-width: 100%; will-change: transform;"
             onwheel="event.preventDefault(); event.stopPropagation(); zoom(event.deltaY < 0 ? 0.15 : -0.15);">
    </div>
</div>

<script>
function formatCurrencyInput(cents) {
    let amount = cents / 100;
    let parts = amount.toFixed(2).split('.');
    let intPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    return intPart + '.' + parts[1];
}

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
        } else if (field === 'credit_line') {
            fieldValueEl.placeholder = 'Ej: 2000 = 20.00';
            let initialNum = parseFloat(value) || 0;
            let cents = Math.round(initialNum * 100);
            fieldValueEl.value = formatCurrencyInput(cents);
            fieldValueEl.oninput = function() {
                let rawDigits = this.value.replace(/\D/g, '');
                if (rawDigits.length === 0) rawDigits = '0';
                let cursorPos = this.value.length;
                this.value = formatCurrencyInput(parseInt(rawDigits, 10));
                let newPos = this.value.length;
                this.setSelectionRange(newPos, newPos);
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
        } else if (field === 'credit_line') {
            let cleanValue = value.replace(/,/g, '');
            const num = parseFloat(cleanValue);
            if (isNaN(num) || num < 0) {
                error = 'La línea de crédito debe ser un número positivo.';
            } else {
                document.getElementById('editFieldValue').value = cleanValue;
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

let currentScale = 1;
let panX = 0;
let panY = 0;
let isPanning = false;
let startX, startY;

function openLightbox(src, label) {
    currentScale = 1;
    panX = 0; panY = 0;
    const img = document.getElementById('lightboxImg');
    img.src = src;
    img.style.transform = `translate(0px, 0px) scale(1)`;
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

function applyTransform() {
    const img = document.getElementById('lightboxImg');
    img.style.transform = `translate(${panX}px, ${panY}px) scale(${currentScale})`;
}

function zoom(delta) {
    currentScale = Math.min(5, Math.max(0.25, currentScale + delta));
    if (currentScale <= 1) { panX = 0; panY = 0; }
    applyTransform();
    document.getElementById('zoomLevel').textContent = Math.round(currentScale * 100) + '%';
    document.getElementById('lightboxImgContainer').style.cursor = currentScale > 1 ? 'grab' : 'default';
}

function resetZoom() {
    currentScale = 1;
    panX = 0; panY = 0;
    applyTransform();
    document.getElementById('zoomLevel').textContent = '100%';
    document.getElementById('lightboxImgContainer').style.cursor = 'default';
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

// Variables para emitir contrato
let emitContractUserId = null;
let isModalClosing = false;

function openEmitContractModal(userId) {
    if (isModalClosing) return;
    
    emitContractUserId = userId;
    const modal = document.getElementById('emitContractModal');
    
    // Reset form
    document.getElementById('contractType').value = '';
    document.getElementById('contractId').value = '';
    document.getElementById('relatedCode').value = '';
    document.getElementById('contractTypeGroup').classList.remove('hidden');
    document.getElementById('contractSelectGroup').classList.add('hidden');
    document.getElementById('relatedCodeGroup').classList.add('hidden');
    document.getElementById('previewButtons').classList.add('hidden');
    document.getElementById('contractError').classList.add('hidden');
    
    // Reset stepper
    updateStepper(1);
    
    // Reset contract type cards
    document.querySelectorAll('.contract-type-card').forEach(c => c.classList.remove('selected'));

    // Reset contracts container
    document.getElementById('contractsContainer').innerHTML = '';
    document.getElementById('contractsLoading').classList.add('hidden');
    document.getElementById('contractsEmpty').classList.add('hidden');

    // Reset codes container
    document.getElementById('codesContainer').innerHTML = '';
    document.getElementById('codesLoading').classList.add('hidden');
    document.getElementById('codesEmpty').classList.add('hidden');
    
    // Mostrar modal con transición
    modal.classList.remove('hidden');
    // Forzar reflow para que la transición funcione
    modal.offsetHeight;
    modal.classList.add('modal-visible');
    document.body.style.overflow = 'hidden';
    
    // Focus en el primer campo
    setTimeout(() => {
        document.getElementById('contractType').focus();
    }, 150);
}

function closeEmitContractModal() {
    if (isModalClosing) return;
    
    isModalClosing = true;
    const modal = document.getElementById('emitContractModal');
    
    modal.classList.remove('modal-visible');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        isModalClosing = false;
    }, 200);
}

function selectContractType(type) {
    const hiddenInput = document.getElementById('contractType');
    const contractSelectGroup = document.getElementById('contractSelectGroup');
    const relatedCodeGroup = document.getElementById('relatedCodeGroup');
    const label = document.getElementById('relatedCodeLabel');
    const previewButtons = document.getElementById('previewButtons');

    // Marcar la card seleccionada
    document.querySelectorAll('.contract-type-card').forEach(card => {
        card.classList.toggle('selected', card.dataset.type === type);
    });

    hiddenInput.value = type;

    // Reset following steps
    document.getElementById('contractId').value = '';
    document.getElementById('contractsContainer').innerHTML = '';
    document.getElementById('relatedCode').value = '';
    document.getElementById('codesContainer').innerHTML = '';

    // Actualizar etiqueta según tipo
    label.textContent = type === 'investment' ? 'INVERSIÓN ASOCIADA' : 'PRÉSTAMO ASOCIADO';

    contractSelectGroup.classList.remove('hidden');
    relatedCodeGroup.classList.add('hidden');
    previewButtons.classList.add('hidden');
    updateStepper(2);
    loadContracts(type);
}

function loadContracts(type) {
    const url = `/gestion-interna/contratos/available?type=${type}`;
    const container = document.getElementById('contractsContainer');
    const loading = document.getElementById('contractsLoading');
    const empty = document.getElementById('contractsEmpty');
    const hiddenInput = document.getElementById('contractId');

    container.innerHTML = '';
    hiddenInput.value = '';
    loading.classList.remove('hidden');
    empty.classList.add('hidden');

    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error('Error al cargar contratos');
            return response.json();
        })
        .then(data => {
            loading.classList.add('hidden');

            if (data.length === 0) {
                empty.classList.remove('hidden');
                document.getElementById('contractErrorMessage').textContent = 'No hay contratos aprobados para este tipo';
                document.getElementById('contractError').classList.remove('hidden');
            } else {
                document.getElementById('contractError').classList.add('hidden');

                data.forEach(contract => {
                    const card = document.createElement('div');
                    card.className = 'contract-option';
                    card.dataset.contractId = contract.id;
                    card.innerHTML = `
                        <div class="contract-main">${contract.name}</div>
                        <div class="contract-details">
                            <span class="contract-detail-item">
                                <svg class="contract-detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                ${contract.clauses_count || 0} cláusulas
                            </span>
                        </div>
                    `;
                    card.onclick = () => selectContract(contract.id, card);
                    container.appendChild(card);
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            loading.classList.add('hidden');
            empty.classList.remove('hidden');
            document.getElementById('contractErrorMessage').textContent = 'Error al cargar. Intente nuevamente.';
            document.getElementById('contractError').classList.remove('hidden');
        });
}

function selectContract(contractId, cardElement) {
    const container = document.getElementById('contractsContainer');
    const hiddenInput = document.getElementById('contractId');

    // Remove selected class from all cards
    container.querySelectorAll('.contract-option').forEach(c => c.classList.remove('selected'));

    // Add selected class to clicked card
    cardElement.classList.add('selected');

    // Update hidden input
    hiddenInput.value = contractId;

    // Trigger step 3
    const type = document.getElementById('contractType').value;
    const relatedCodeGroup = document.getElementById('relatedCodeGroup');
    const previewButtons = document.getElementById('previewButtons');

    relatedCodeGroup.classList.remove('hidden');
    previewButtons.classList.add('hidden');
    loadRelatedCodes(type);
}

function loadRelatedCodes(type) {
    const url = `/gestion-interna/users/${emitContractUserId}/related-codes?type=${type}`;
    
    // Mostrar estados
    document.getElementById('codesLoading').classList.remove('hidden');
    document.getElementById('codesContainer').classList.add('hidden');
    document.getElementById('codesEmpty').classList.add('hidden');
    
    // Actualizar stepper - paso 3 activo
    updateStepper(3);
    
    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error('Error al cargar códigos');
            return response.json();
        })
        .then(data => {
            document.getElementById('codesLoading').classList.add('hidden');
            
            if (data.length === 0) {
                const label = type === 'investment' ? 'inversiones' : 'préstamos';
                document.getElementById('codesEmptyText').textContent = `Este usuario no tiene ${label} asociadas`;
                document.getElementById('codesEmpty').classList.remove('hidden');
                document.getElementById('contractError').classList.add('hidden');
                return;
            }
            
            document.getElementById('codesContainer').classList.remove('hidden');
            const container = document.getElementById('codesContainer');
            container.innerHTML = '';
            
            data.forEach(item => {
                // Parsear la descripción para extraer datos
                const descParts = item.description.split(' - ');
                let monto = '', plazo = '';
                
                descParts.forEach(part => {
                    if (part.includes('Monto:')) monto = part.replace('Monto:', '').trim();
                    if (part.includes('meses')) plazo = part.trim();
                    if (part.includes('%')) plazo += ' · ' + part;
                });
                
                const card = document.createElement('div');
                card.className = 'code-option';
                card.dataset.code = item.code;
                card.onclick = function() { selectCodeCard(this); };
                
                card.innerHTML = `
                    <div class="code-main">${item.code}</div>
                    <div class="code-details">
                        ${monto ? `<span class="code-detail-item">
                            <svg class="code-detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2m0 0c0 1.105-.895 2-2 2s-2-.895-2-2m2-4h.01M17 21h-8a2 2 0 01-2-2V5a2 2 0 012-2h8a2 2 0 012 2v14a2 2 0 01-2 2z"/>
                            </svg>
                            ${monto}
                        </span>` : ''}
                        ${plazo ? `<span class="code-detail-item">
                            <svg class="code-detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            ${plazo}
                        </span>` : ''}
                    </div>
                `;
                
                container.appendChild(card);
            });
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('codesLoading').classList.add('hidden');
            document.getElementById('codesEmptyText').textContent = 'Error al cargar. Intente nuevamente.';
            document.getElementById('codesEmpty').classList.remove('hidden');
        });
}

function selectCodeCard(card) {
    // Deseleccionar todos
    document.querySelectorAll('.code-option').forEach(c => c.classList.remove('selected'));
    // Seleccionar este
    card.classList.add('selected');
    // Actualizar valor del select oculto
    document.getElementById('relatedCode').value = card.dataset.code;
    // Mostrar botones
    document.getElementById('previewButtons').classList.remove('hidden');
}

function updateStepper(currentStep) {
    for (let i = 1; i <= 3; i++) {
        const numEl = document.getElementById(`step${i}Num`);
        const labelEl = document.getElementById(`step${i}Label`);
        const connEl = document.getElementById(`connector${i}`);
        
        numEl.classList.remove('active', 'completed');
        labelEl.classList.remove('active', 'completed');
        
        if (i < currentStep) {
            numEl.classList.add('completed');
            labelEl.classList.add('completed');
            if (connEl) connEl.classList.add('active');
        } else if (i === currentStep) {
            numEl.classList.add('active');
            labelEl.classList.add('active');
        }
    }
}

function openContractPreview() {
    const type = document.getElementById('contractType').value;
    const contractId = document.getElementById('contractId').value;
    const relatedCodeInput = document.getElementById('relatedCode');
    const relatedCode = relatedCodeInput ? relatedCodeInput.value : null;
    
    if (!type || !contractId || !relatedCode) {
        alert('Por favor complete todos los pasos: tipo de contrato, plantilla y operación asociada.');
        return;
    }
    
    // Mostrar modal de preview
    const modal = document.getElementById('contractPreviewModal');
    const loading = document.getElementById('previewLoading');
    const content = document.getElementById('previewContent');
    
    // Reset modal
    loading.classList.remove('hidden');
    content.classList.add('hidden');
    content.innerHTML = '';
    
    modal.classList.add('modal-visible');
    document.body.style.overflow = 'hidden';
    
    // Cargar contenido del contrato
    const url = `/gestion-interna/contratos/${contractId}/preview-client?user_id=${emitContractUserId}&type=${type}&related_code=${relatedCode}`;
    
    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error('Error al cargar el contrato');
            return response.text();
        })
        .then(htmlContent => {
            loading.classList.add('hidden');
            content.innerHTML = htmlContent;
            content.classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            loading.classList.add('hidden');
            content.innerHTML = '<p class="text-red-500 text-center py-8">Error al cargar el contrato. Intente nuevamente.</p>';
            content.classList.remove('hidden');
        });
}

function closeContractPreviewModal() {
    const modal = document.getElementById('contractPreviewModal');
    modal.classList.remove('modal-visible');
    
    setTimeout(() => {
        document.body.style.overflow = '';
    }, 250);
}

async function downloadContractPDF() {
    const type = document.getElementById('contractType').value;
    const contractId = document.getElementById('contractId').value;
    const relatedCodeInput = document.getElementById('relatedCode');
    const relatedCode = relatedCodeInput ? relatedCodeInput.value : null;
    
    if (!type || !contractId || !relatedCode) {
        alert('Por favor complete todos los pasos: tipo de contrato, plantilla y operación asociada.');
        return;
    }

    const btn = event.currentTarget;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = `
        <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>Generando...</span>
    `;

    try {
        const url = `/gestion-interna/contratos/${contractId}/download-pdf?user_id=${emitContractUserId}&type=${type}&related_code=${relatedCode}`;
        
        const response = await fetch(url, {
            headers: { 'Accept': 'application/pdf' }
        });
        
        if (!response.ok) {
            const err = await response.json().catch(() => ({}));
            throw new Error(err.message || 'Error del servidor al generar PDF');
        }
        
        const blob = await response.blob();
        const blobUrl = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = blobUrl;
        a.download = `contrato_${emitContractUserId}_${relatedCode}.pdf`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(blobUrl);

    } catch (error) {
        console.error('Error al descargar PDF:', error);
        alert('Error: ' + error.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}

document.getElementById('emitContractModal').addEventListener('click', e => {
    if (e.target === document.getElementById('emitContractModal')) closeEmitContractModal();
});

// Zoom con rueda del mouse — en TODO el lightbox
const lb = document.getElementById('lightbox');

lb.addEventListener('wheel', function(e) {
    if (lb.classList.contains('hidden')) return;
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    zoom(e.deltaY < 0 ? 0.15 : -0.15);
}, { passive: false, capture: true });

// Drag/Pan con mouse
const imgContainer = document.getElementById('lightboxImgContainer');

imgContainer.addEventListener('mousedown', e => {
    if (currentScale <= 1) return;
    isPanning = true;
    startX = e.clientX - panX;
    startY = e.clientY - panY;
    imgContainer.style.cursor = 'grabbing';
});

document.addEventListener('mousemove', e => {
    if (!isPanning) return;
    panX = e.clientX - startX;
    panY = e.clientY - startY;
    applyTransform();
});

document.addEventListener('mouseup', () => {
    isPanning = false;
    imgContainer.style.cursor = currentScale > 1 ? 'grab' : 'default';
});

lbImg.addEventListener('mouseleave', () => {
    isPanning = false;
    imgContainer.style.cursor = currentScale > 1 ? 'grab' : 'default';
});

function openDeleteGhostModal() {
    document.getElementById('deleteGhostModal').classList.remove('hidden');
}

function closeDeleteGhostModal() {
    document.getElementById('deleteGhostModal').classList.add('hidden');
}

function openRejectChangeModal(requestId) {
    const form = document.getElementById('rejectChangeForm');
    form.action = `/gestion-interna/solicitudes-cambio/${requestId}/rechazar`;
    document.getElementById('rejectChangeModal').classList.remove('hidden');
}

function closeRejectChangeModal() {
    document.getElementById('rejectChangeModal').classList.add('hidden');
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

<style>
/* Estilos mejorados para modal de emitir contrato */
#emitContractModal, #contractPreviewModal {
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease;
    z-index: 50;
}

#emitContractModal.modal-visible, #contractPreviewModal.modal-visible {
    opacity: 1;
    visibility: visible;
}

#emitContractModal .modal-content, #contractPreviewModal .modal-content {
    transform: scale(0.95) translateY(20px);
    transition: transform 0.3s ease;
}

#emitContractModal.modal-visible .modal-content, #contractPreviewModal.modal-visible .modal-content {
    transform: scale(1) translateY(0);
}

/* Stepper/Progress indicator */
.contract-stepper {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 35px;
    padding: 0 5px;
}

.step-item {
    display: flex;
    align-items: center;
    gap: 10px;
    position: relative;
}

.step-number {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 800;
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    background: #f1f5f9;
    color: #94a3b8;
    border: none;
}

.step-number.active {
    background: #f97316;
    color: white;
    box-shadow: 0 4px 15px rgba(249, 115, 22, 0.4);
    transform: scale(1.15);
}

.step-number.completed {
    background: #22c55e;
    color: white;
    box-shadow: 0 4px 12px rgba(34, 197, 94, 0.2);
}

.step-label {
    font-size: 13px;
    font-weight: 700;
    color: #94a3b8;
    transition: all 0.3s ease;
}

.step-label.active {
    color: #f97316;
}

.step-label.completed {
    color: #475569;
}

.step-connector {
    width: 45px;
    height: 3px;
    background: #e2e8f0;
    margin: 0 12px;
    transition: all 0.6s ease;
    border-radius: 10px;
}

.step-connector.active {
    background: #22c55e;
}

/* Form fields styling */
.contract-field-group {
    position: relative;
    animation: fadeInSlide 0.4s ease-out;
}

@keyframes fadeInSlide {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.contract-field-label {
    display: block;
    font-size: 12px;
    font-weight: 800;
    color: #374151;
    margin-bottom: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.contract-select {
    width: 100%;
    padding: 16px 20px;
    font-size: 15px;
    font-weight: 500;
    border: 2px solid #f3f4f6;
    border-radius: 16px;
    background: #f9fafb;
    color: #1f2937;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
    background-position: right 20px center;
    background-repeat: no-repeat;
    background-size: 18px;
}

.contract-select:hover {
    border-color: #f97316;
    background: white;
}

.contract-select:focus {
    outline: none;
    border-color: #f97316;
    background: white;
    box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1);
}

/* Code card styling */
.code-option {
    padding: 16px 20px;
    border: 2px solid #f3f4f6;
    border-radius: 16px;
    background: white;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    margin-bottom: 12px;
    position: relative;
    overflow: hidden;
}

.code-option:hover {
    border-color: #f97316;
    background: #fffaf5;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.code-option.selected {
    border-color: #f97316;
    background: #fff7ed;
    box-shadow: 0 4px 15px rgba(249, 115, 22, 0.15);
}

.code-option.selected::after {
    content: '';
    position: absolute;
    top: 12px;
    right: 12px;
    width: 20px;
    height: 20px;
    background-color: #f97316;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='white' stroke-width='3'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M5 13l4 4L19 7'/%3E%3C/svg%3E");
    background-size: 12px;
    background-position: center;
    background-repeat: no-repeat;
    border-radius: 50%;
}

.code-main {
    font-size: 15px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 4px;
}

/* Contract type card styling */
.contract-type-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 16px 12px;
    border: 2px solid #f3f4f6;
    border-radius: 16px;
    background: white;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-align: center;
    gap: 2px;
}

.contract-type-card:hover {
    border-color: #f97316;
    background: #fffaf5;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.contract-type-card.selected {
    border-color: #f97316;
    background: #fff7ed;
    box-shadow: 0 4px 15px rgba(249, 115, 22, 0.15);
    position: relative;
}

.contract-type-card.selected::after {
    content: '';
    position: absolute;
    top: 8px;
    right: 8px;
    width: 18px;
    height: 18px;
    background-color: #f97316;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='white' stroke-width='3'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M5 13l4 4L19 7'/%3E%3C/svg%3E");
    background-size: 10px;
    background-position: center;
    background-repeat: no-repeat;
    border-radius: 50%;
}

/* Contract option (template) card styling */
.contract-option {
    padding: 14px 18px;
    border: 2px solid #f3f4f6;
    border-radius: 14px;
    background: white;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.contract-option:hover {
    border-color: #f97316;
    background: #fffaf5;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.contract-option.selected {
    border-color: #f97316;
    background: #fff7ed;
    box-shadow: 0 4px 15px rgba(249, 115, 22, 0.15);
}

.contract-option.selected::after {
    content: '';
    position: absolute;
    top: 12px;
    right: 12px;
    width: 20px;
    height: 20px;
    background-color: #f97316;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='white' stroke-width='3'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M5 13l4 4L19 7'/%3E%3C/svg%3E");
    background-size: 12px;
    background-position: center;
    background-repeat: no-repeat;
    border-radius: 50%;
}

.contract-main {
    font-size: 14px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 4px;
    padding-right: 28px;
}

.contract-details {
    font-size: 12px;
    color: #6b7280;
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.contract-detail-item {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.contract-detail-icon {
    width: 14px;
    height: 14px;
    color: #f97316;
}

.code-details {
    font-size: 13px;
    color: #6b7280;
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.code-detail-item {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.code-detail-icon {
    width: 14px;
    height: 14px;
    color: #f97316;
}

/* Action buttons */
.contract-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid #f3f4f6;
}

.btn-contract-primary {
    padding: 16px 24px;
    font-size: 15px;
    font-weight: 700;
    border-radius: 16px;
    border: none;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.btn-preview {
    background: #3b82f6;
    color: white;
    box-shadow: 0 4px 14px 0 rgba(59, 130, 246, 0.3);
}

.btn-preview:hover {
    background: #2563eb;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
}

.btn-download {
    background: #10b981;
    color: white;
    box-shadow: 0 4px 14px 0 rgba(16, 185, 129, 0.3);
}

.btn-download:hover {
    background: #059669;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
}

/* Error message */
#contractError {
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Loading state */
.loading-codes {
    text-align: center;
    padding: 30px;
    color: #9ca3af;
}

.loading-spinner-small {
    width: 32px;
    height: 32px;
    border: 3px solid #e5e7eb;
    border-top-color: #f97316;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin: 0 auto 12px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Empty state */
.empty-state {
    text-align: center;
    padding: 30px 20px;
}

.empty-state-icon {
    width: 48px;
    height: 48px;
    background: #fef3c7;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
}

.empty-state-text {
    color: #6b7280;
    font-size: 14px;
}

/* === DOCUMENT STYLES (Vista previa contrato - match PDF exact) === */
#contractPreviewContent {
    background: #f8f9fa;
    padding: 24px;
    border-radius: 12px;
}

.doc-page {
    position: relative;
    max-width: 650px;
    margin: 0 auto;
    background: white;
    padding: 48px 44px 40px;
    border: 1px solid #eee;
}

.doc-page::before {
    content: '';
    position: absolute;
    top: 16px;
    right: 20px;
    width: 36px;
    height: 48px;
    border: 1.5px solid #ddd;
    border-radius: 2px;
    opacity: 0.5;
}
.doc-page.has-logo::before { display: none; }
.doc-logo {
    position: absolute;
    top: 12px;
    right: 20px;
    max-width: 90px;
    max-height: 56px;
    object-fit: contain;
}

.doc-title {
    text-align: center;
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 22px;
    font-weight: 700;
    color: #1a1a1a;
    letter-spacing: 0.02em;
    margin-bottom: 6px;
    line-height: 1.35;
}

.doc-subtitle {
    text-align: center;
    font-size: 10.5px;
    text-transform: uppercase;
    letter-spacing: 0.18em;
    color: #888;
    margin-bottom: 32px;
    font-weight: 500;
}

.doc-divider {
    height: 1px;
    background: #ccc;
    margin: 28px 0;
    border: none;
}

.doc-clause-title {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 14px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 10px;
    letter-spacing: 0.01em;
}

.doc-clause-content {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 13px;
    line-height: 1.85;
    color: #333;
    text-align: justify;
    word-wrap: break-word;
}

.doc-clause-content p {
    margin: 0 0 10px 0;
}

.doc-clause {
    margin-bottom: 22px;
}

.doc-footer {
    margin-top: 36px;
    padding-top: 20px;
    border-top: 1px solid #e8e8e8;
    width: 100%;
}

.doc-footer-table {
    width: 100%;
    border-collapse: collapse;
}

.doc-footer-table td {
    width: 50%;
    vertical-align: bottom;
    text-align: center;
    padding: 0 10px;
}

.doc-signature-line {
    width: 70%;
    height: 1px;
    background: #aaa;
    margin: 0 auto;
}

.doc-signature-img {
    max-height: 50px;
    margin-bottom: 4px;
    display: block;
    margin-left: auto;
    margin-right: auto;
}

.sig-name {
    font-size: 9px;
    color: #666;
    margin-top: 5px;
    text-align: center;
}

.sig-label {
    font-size: 8px;
    color: #888;
    text-align: center;
}

.doc-missing-placeholder {
    color: #dc2626;
    font-weight: 600;
}

.emit-contract-shell {
    width: min(720px, 100%);
    max-height: min(92vh, 880px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.emit-contract-main {
    display: flex;
    flex-direction: column;
    min-height: 0;
    flex: 1;
}

.emit-contract-stepper-wrap {
    flex-shrink: 0;
    padding: 28px 28px 0;
}

.emit-contract-scroll {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overscroll-behavior: contain;
    padding: 20px 24px 24px;
}

#contractsContainer,
#codesContainer {
    max-height: min(26vh, 220px);
    overflow-y: auto;
    padding-right: 4px;
}

#previewButtons.emit-contract-footer {
    position: sticky;
    bottom: 0;
    margin-top: 20px;
    padding-top: 16px;
    background: linear-gradient(180deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.92) 18%, #ffffff 42%);
}

/* ===== RESPONSIVE: Modal emitir contrato ===== */
@media (max-width: 640px) {
    #emitContractModal > div.modal-content {
        width: 100% !important;
        max-height: calc(100vh - 10px) !important;
        border-radius: 20px 20px 0 0 !important;
    }
    #emitContractModal {
        align-items: flex-end !important;
        padding: 8px 8px 0 !important;
    }
    .emit-contract-shell {
        max-height: calc(100vh - 8px) !important;
    }
    #emitContractModal .relative.px-6.py-5 {
        padding: 16px 18px !important;
    }
    #emitContractModal .relative.px-6.py-5 h3 {
        font-size: 15px !important;
    }
    #emitContractModal .relative.px-6.py-5 p {
        font-size: 11px !important;
    }
    .emit-contract-stepper-wrap {
        padding: 16px 12px 0 !important;
    }
    .contract-stepper {
        margin-bottom: 20px !important;
        justify-content: flex-start !important;
        overflow-x: auto;
        padding-bottom: 2px !important;
        scrollbar-width: none;
    }
    .contract-stepper::-webkit-scrollbar {
        display: none;
    }
    .step-item {
        gap: 6px !important;
        flex: 0 0 auto;
    }
    .step-number {
        width: 28px !important;
        height: 28px !important;
        font-size: 12px !important;
    }
    .step-label {
        font-size: 10px !important;
    }
    .step-connector {
        width: 20px !important;
        margin: 0 6px !important;
        flex: 0 0 20px;
    }
    .emit-contract-scroll {
        padding: 14px 14px 16px !important;
    }
    .contract-field-label {
        font-size: 10px !important;
        margin-bottom: 8px !important;
    }
    #contractsContainer,
    #codesContainer {
        max-height: min(30vh, 250px) !important;
    }
    .contract-select {
        padding: 12px 14px !important;
        font-size: 14px !important;
        border-radius: 12px !important;
    }
    .code-option {
        padding: 12px 14px !important;
        border-radius: 12px !important;
    }
    .code-main {
        font-size: 13px !important;
    }
    .code-details {
        font-size: 11px !important;
        gap: 8px !important;
    }
    .contract-actions {
        grid-template-columns: 1fr !important;
        gap: 10px !important;
    }
    #previewButtons.emit-contract-footer {
        margin-top: 16px !important;
        padding-top: 14px !important;
        padding-bottom: 2px !important;
    }
    .btn-contract-primary {
        padding: 12px 16px !important;
        font-size: 14px !important;
        border-radius: 12px !important;
    }
    .contract-type-card {
        padding: 12px 8px !important;
        border-radius: 12px !important;
    }
    .contract-type-card .w-10 {
        width: 32px !important;
        height: 32px !important;
    }
    .contract-type-card .w-10 svg {
        width: 18px !important;
        height: 18px !important;
    }
    .contract-type-card span.text-sm {
        font-size: 12px !important;
    }
    .contract-type-card span.text-xs {
        font-size: 10px !important;
    }
    .contract-option {
        padding: 12px 14px !important;
        border-radius: 12px !important;
    }
    .contract-main {
        font-size: 13px !important;
    }
    .contract-details {
        font-size: 11px !important;
        gap: 8px !important;
    }
}

/* ===== RESPONSIVE: Vista previa del contrato ===== */
@media (max-width: 640px) {
    #contractPreviewModal > div.modal-content {
        border-radius: 16px !important;
        max-height: 95vh;
    }
    #contractPreviewModal .px-6.py-4 {
        padding: 12px 14px !important;
    }
    #contractPreviewModal .px-6.py-4 h3 {
        font-size: 13.5px !important;
    }
    #contractPreviewModal .p-6 {
        padding: 10px !important;
    }
    #contractPreviewContent {
        padding: 12px !important;
        border-radius: 8px !important;
    }
    .doc-page {
        padding: 20px 14px 16px !important;
        max-width: 100% !important;
    }
    .doc-title {
        font-size: 16px !important;
    }
    .doc-subtitle {
        font-size: 8.5px !important;
        margin-bottom: 14px !important;
    }
    .doc-divider {
        margin: 14px 0 !important;
    }
    .doc-clause-title {
        font-size: 11.5px !important;
        margin-bottom: 6px !important;
    }
    .doc-clause-content {
        font-size: 11px !important;
        line-height: 1.65 !important;
    }
    .doc-clause {
        margin-bottom: 12px !important;
    }
    .doc-footer {
        margin-top: 20px !important;
        padding-top: 14px !important;
    }
    .doc-footer-table td {
        padding: 0 6px !important;
    }
    .doc-signature-line {
        width: 60% !important;
    }
    .doc-logo {
        max-width: 60px !important;
        max-height: 40px !important;
        top: 8px !important;
        right: 10px !important;
    }
}
</style>

<!-- Modal emitir contrato -->
<div id="emitContractModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 min-h-screen">
    <div class="modal-content emit-contract-shell bg-white rounded-3xl shadow-2xl overflow-hidden">
        <!-- Header -->
        <div class="relative px-6 py-5 bg-gradient-to-r from-orange-500 via-orange-600 to-amber-500">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg%20width%3D%2260%22%20height%3D%2260%22%20viewBox%3D%220%200%2060%2060%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cg%20fill%3D%22none%22%20fill-rule%3D%22evenodd%22%3E%3Cg%20fill%3D%22%23ffffff%22%20fill-opacity%3D%220.08%22%3E%3Cpath%20d%3D%22M36%2034v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6%2034v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6%204V0H4v4H0v2h4v4h2V6h4V4H6z%22%2F%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E')] opacity-30"></div>
            <div class="relative flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div>
                        <h3 class="text-lg font-bold text-white">Emitir Contrato</h3>
                        <p class="text-xs text-white/80">Complete los datos para generar</p>
                    </div>
                </div>
                <button type="button" onclick="closeEmitContractModal()" 
                        class="w-9 h-9 rounded-xl flex items-center justify-center text-white/80 hover:text-white hover:bg-white/20 transition-all"
                        aria-label="Cerrar modal">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        <div class="emit-contract-main">
            <!-- Stepper -->
            <div class="emit-contract-stepper-wrap">
                <div class="contract-stepper">
                    <div class="step-item">
                        <span id="step1Num" class="step-number active">1</span>
                        <span id="step1Label" class="step-label active">Tipo</span>
                    </div>
                    <div id="connector1" class="step-connector"></div>
                    <div class="step-item">
                        <span id="step2Num" class="step-number">2</span>
                        <span id="step2Label" class="step-label">Contrato</span>
                    </div>
                    <div id="connector2" class="step-connector"></div>
                    <div class="step-item">
                        <span id="step3Num" class="step-number">3</span>
                        <span id="step3Label" class="step-label">Código</span>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="emit-contract-scroll space-y-5">
                <!-- Mensaje de error -->
                <div id="contractError" class="hidden p-4 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm flex items-start gap-3">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span id="contractErrorMessage"></span>
                </div>

                <!-- Paso 1: Tipo de contrato -->
                <div id="contractTypeGroup" class="contract-field-group">
                    <label class="contract-field-label">TIPO DE CONTRATO</label>
                    <input type="hidden" id="contractType" value="">
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" onclick="selectContractType('loan')"
                                class="contract-type-card" data-type="loan">
                            <div class="w-10 h-10 flex items-center justify-center mb-2">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                                </svg>
                            </div>
                            <span class="text-sm font-bold text-gray-900 dark:text-gray-900">Préstamo</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Crédito personal</span>
                        </button>
                        <button type="button" onclick="selectContractType('investment')"
                                class="contract-type-card" data-type="investment">
                            <div class="w-10 h-10 flex items-center justify-center mb-2">
                                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                            <span class="text-sm font-bold text-gray-900 dark:text-gray-900">Inversión</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Depósito a plazo</span>
                        </button>
                    </div>
                </div>

                <!-- Paso 2: Selección de contrato -->
                <div id="contractSelectGroup" class="hidden contract-field-group">
                    <label class="contract-field-label">PLANTILLA DE CONTRATO</label>

                    <div id="contractsLoading" class="loading-codes hidden">
                        <div class="loading-spinner-small"></div>
                        <p class="text-sm">Cargando plantillas...</p>
                    </div>

                    <div id="contractsContainer" class="space-y-2">
                    </div>

                    <input type="hidden" id="contractId" name="contractId" value="">

                    <div id="contractsEmpty" class="empty-state hidden">
                        <div class="empty-state-icon">
                            <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="empty-state-text">No se encontraron plantillas</p>
                    </div>
                </div>

                <!-- Paso 3: Selección de código relacionado -->
                <div id="relatedCodeGroup" class="hidden contract-field-group">
                    <label id="relatedCodeLabel" class="contract-field-label">OPERACIÓN ASOCIADA</label>

                    <div id="codesLoading" class="loading-codes hidden">
                        <div class="loading-spinner-small"></div>
                        <p class="text-sm">Buscando operaciones...</p>
                    </div>

                    <div id="codesContainer" class="space-y-2">
                    </div>

                    <input type="hidden" id="relatedCode" name="relatedCode" value="">

                    <div id="codesEmpty" class="empty-state hidden">
                        <div class="empty-state-icon">
                            <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="empty-state-text" id="codesEmptyText">No se encontraron operaciones</p>
                    </div>
                </div>

                <!-- Botones de preview y descarga -->
                <div id="previewButtons" class="hidden contract-actions emit-contract-footer">
                    <button type="button" onclick="openContractPreview()"
                            class="btn-contract-primary btn-preview">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Vista Previa
                    </button>
                    <button type="button" onclick="downloadContractPDF()"
                            class="btn-contract-primary btn-download">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Descargar PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal vista previa del contrato -->
<div id="contractPreviewModal" class="fixed inset-0 bg-black/60 flex items-center justify-center p-4 min-h-screen z-[60]">
    <div class="modal-content bg-white dark:bg-gray-900 rounded-2xl w-full max-w-4xl max-h-[90vh] shadow-2xl overflow-hidden">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-blue-500 to-blue-600">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-white">Vista previa del contrato</h3>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="downloadContractPDF()"
                        class="px-4 py-2 text-sm font-medium rounded-lg bg-white/20 hover:bg-white/30 text-white transition-all"
                        aria-label="Descargar PDF">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Descargar PDF
                    </span>
                </button>
                <button type="button" onclick="closeContractPreviewModal()" 
                        class="w-8 h-8 rounded-xl flex items-center justify-center text-white/80 hover:text-white hover:bg-white/20 transition-all"
                        aria-label="Cerrar modal">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Body -->
        <div id="contractPreviewContent" class="p-6 overflow-y-auto max-h-[calc(90vh-80px)] bg-white">
            <!-- Loading state -->
            <div id="previewLoading" class="flex flex-col items-center justify-center py-20">
                <div class="w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mb-4"></div>
                <p class="text-gray-500 text-sm">Cargando contrato...</p>
            </div>
            
            <!-- Contract content will be loaded here -->
            <div id="previewContent" class="hidden contract-preview-content"></div>
        </div>
    </div>
</div>

@include('partials.global-scripts')
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.2/dist/jspdf.umd.min.js"></script>
@endsection
