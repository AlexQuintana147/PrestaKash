@extends('superadmin.layout')

@section('title', $user->name)

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8">

    <!-- Header -->
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('superadmin.users.index') }}"
           class="text-gray-400 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=e5e7eb&color=6b7280&size=64' }}"
             class="w-9 h-9 rounded-full object-cover bg-gray-200 dark:bg-gray-900 flex-shrink-0">
        <div class="min-w-0 flex-1">
            <h1 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white leading-tight truncate">{{ $user->name }}</h1>
            <p class="text-xs text-gray-400 dark:text-gray-400 truncate">{{ $user->email }}</p>
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
                <p class="text-xs text-gray-400 dark:text-gray-400 uppercase tracking-widest mb-4">Datos personales</p>
                <div class="space-y-3">
                    @foreach([
                        'Nombres'      => $user->first_name,
                        'Apellidos'    => $user->last_name,
                        'DNI'          => $user->dni,
                        'Teléfono'     => $user->phone,
                        'Departamento' => $department,
                        'Provincia'    => $province,
                        'Distrito'     => $district,
                        'Dirección'    => $user->address,
                        'Registro'     => $user->created_at->format('d/m/Y'),
                    ] as $label => $value)
                    <div class="flex justify-between items-start border-b border-gray-100 dark:border-gray-900 pb-3">
                        <span class="text-xs text-gray-400 dark:text-gray-400 flex-shrink-0">{{ $label }}</span>
                        <span class="text-xs text-gray-900 dark:text-white text-right ml-4">{{ $value ?? '—' }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            @if($user->documents)
            <div>
                <p class="text-xs text-gray-400 dark:text-gray-400 uppercase tracking-widest mb-4">Acciones</p>

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
            <p class="text-xs text-gray-400 dark:text-gray-400 uppercase tracking-widest mb-5">Documentos</p>

            @if($user->documents)
            <div class="grid grid-cols-2 gap-3 md:gap-5">
                @foreach([
                    'Selfie'              => $user->documents->selfie,
                    'Recibo de servicios' => $user->documents->utility_bill,
                    'DNI — Frontal'       => $user->documents->dni_front,
                    'DNI — Posterior'     => $user->documents->dni_back,
                ] as $label => $path)
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-400 mb-2">{{ $label }}</p>
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
</script>
@endsection
