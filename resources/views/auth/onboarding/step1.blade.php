<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PrestaKash — Completa tu perfil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }

        .input-field {
            width: 100%;
            background: transparent;
            border: none;
            border-bottom: 1px solid #2a2a2a;
            color: white;
            padding: 10px 0;
            font-size: 0.875rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .input-field:focus {
            border-bottom-color: #10b981;
        }

        .input-field::placeholder {
            color: #6b7280;
        }

        .input-field:disabled {
            color: #ffffff;
            cursor: not-allowed;
            opacity: 0.7;
        }

        select.input-field option {
            background: #111111;
            color: white;
        }

        select.input-field:disabled {
            opacity: 0.4;
        }

        /* Custom select */
        .custom-select {
            position: relative;
        }

        .custom-select-trigger {
            width: 100%;
            background: transparent;
            border: none;
            border-bottom: 1px solid #2a2a2a;
            color: white;
            padding: 10px 24px 10px 0;
            font-size: 0.875rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: border-color 0.2s;
            font-family: 'Roboto', sans-serif;
        }

        .custom-select-trigger:focus {
            outline: none;
            border-bottom-color: #10b981;
        }

        .custom-select-trigger.open {
            border-bottom-color: #10b981;
        }

        .custom-select-trigger.disabled {
            opacity: 0.35;
            cursor: not-allowed;
            pointer-events: none;
        }

        .custom-select-trigger .placeholder {
            color: #6b7280;
        }

        .custom-select-dropdown {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            background: #0a0a0a;
            border: 1px solid #1a1a1a;
            border-radius: 10px;
            max-height: 220px;
            overflow-y: auto;
            z-index: 50;
            display: none;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
        }

        .custom-select-dropdown.open {
            display: block;
        }

        .custom-select-search {
            padding: 10px 14px;
            border-bottom: 1px solid #1a1a1a;
            position: sticky;
            top: 0;
            background: #0a0a0a;
        }

        .custom-select-search input {
            width: 100%;
            background: transparent;
            border: none;
            color: white;
            font-size: 0.8rem;
            outline: none;
        }

        .custom-select-search input::placeholder {
            color: #6b7280;
        }

        .custom-select-option {
            padding: 10px 14px;
            font-size: 0.8rem;
            color: #e5e7eb;
            cursor: pointer;
            transition: background 0.1s, color 0.1s;
        }

        .custom-select-option:hover {
            background: #1a1a1a;
            color: white;
        }

        .custom-select-option.selected {
            color: #10b981;
        }

        .custom-select-dropdown::-webkit-scrollbar {
            width: 4px;
        }

        .custom-select-dropdown::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-select-dropdown::-webkit-scrollbar-thumb {
            background: #2a2a2a;
            border-radius: 4px;
        }
    </style>
</head>

<body class="bg-black min-h-screen flex items-center justify-center px-4 py-12" onload="noBack();"
    onpageshow="if(event.persisted) noBack();">
    <div class="w-full max-w-xl">

        <div class="mb-10">
            <p class="text-xs font-semibold text-green-400 uppercase tracking-widest mb-1">PrestaKash</p>
            <h1 class="text-2xl font-bold text-white">Cuéntanos sobre ti</h1>
            <p class="text-gray-400 text-sm mt-1">Paso 1 de 2 — Datos personales</p>

            <div class="mt-4 h-0.5 bg-gray-800 rounded-full">
                <div class="h-0.5 bg-green-500 rounded-full w-1/2"></div>
            </div>
        </div>

        @if(session('error') || $errors->any())
            <div class="mb-6 p-4 border-l-2 border-red-500 bg-red-500/5 rounded-r-lg">
                @if(session('error'))
                    <p class="text-red-400 text-sm">{{ session('error') }}</p>
                @endif
                @foreach($errors->all() as $error)
                    <p class="text-red-400 text-sm">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('onboarding.step1.store') }}" method="POST" class="space-y-8">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                <div>
                    <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Tipo de
                        Persona</label>
                    <div class="flex gap-4 mt-2">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="person_type" value="natural"
                                {{ (old('person_type', Auth::user()->person_type ?? 'natural') == 'natural') ? 'checked' : '' }} class="hidden peer"
                                onchange="togglePersonType(this.value)">
                            <div
                                class="w-4 h-4 rounded-full border border-gray-700 peer-checked:border-green-500 peer-checked:bg-green-500 flex items-center justify-center transition-all">
                                <div class="w-1.5 h-1.5 rounded-full bg-white opacity-0 peer-checked:opacity-100"></div>
                            </div>
                            <span class="text-sm text-gray-300 peer-checked:text-white transition-colors">Natural</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="person_type" value="juridica"
                                {{ (old('person_type', Auth::user()->person_type ?? '') == 'juridica') ? 'checked' : '' }} class="hidden peer"
                                onchange="togglePersonType(this.value)">
                            <div
                                class="w-4 h-4 rounded-full border border-gray-700 peer-checked:border-green-500 peer-checked:bg-green-500 flex items-center justify-center transition-all">
                                <div class="w-1.5 h-1.5 rounded-full bg-white opacity-0 peer-checked:opacity-100"></div>
                            </div>
                            <span class="text-sm text-gray-300 peer-checked:text-white transition-colors">Jurídica</span>
                        </label>
                    </div>
                </div>
            </div>

            <div id="juridica-fields" class="{{ (old('person_type', Auth::user()->person_type ?? '') == 'juridica') ? '' : 'hidden' }} space-y-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">RUC</label>
                        <input type="text" id="rucInput" name="ruc" value="{{ old('ruc', Auth::user()->ruc ?? '') }}" maxlength="11"
                            oninput="this.value=this.value.replace(/\D/g,'')" class="input-field"
                            placeholder="20123456789">
                        <p id="rucError" class="text-red-500 text-[10px] mt-1 hidden">Este RUC ya está registrado</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Razón
                            Social</label>
                        <input type="text" name="business_name" value="{{ old('business_name', Auth::user()->business_name ?? '') }}" class="input-field"
                            oninput="this.value=this.value.replace(/[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s.&,\-\(\)\'\"\/]/g,'')"
                            placeholder="Empresa S.A.C.">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Correo
                    electrónico</label>
                <input type="email" value="{{ Auth::user()->email }}" disabled class="input-field">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                <div>
                    <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Nombres</label>
                    <input type="text" name="first_name" value="{{ old('first_name', Auth::user()->first_name ?? '') }}" required
                        placeholder="Mateo Pedro" oninput="this.value=this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g,'').toLowerCase().replace(/(^|\s)\S/g,l=>l.toUpperCase())"
                        class="input-field">
                </div>
                <div>
                    <label
                        class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Apellidos</label>
                    <input type="text" name="last_name" value="{{ old('last_name', Auth::user()->last_name ?? '') }}" required
                        placeholder="Ej: Castillo Villa"
                        oninput="this.value=this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g,'').toLowerCase().replace(/(^|\s)\S/g,l=>l.toUpperCase())" class="input-field">
                    <p class="text-[10px] text-gray-400 mt-1">Ingresa tus dos apellidos separados por un espacio.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                <div>
                    <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Nro.
                        DNI</label>
                    <input type="text" id="dniInput" name="dni" value="{{ old('dni', Auth::user()->dni ?? '') }}" required placeholder="12345678"
                        maxlength="8" inputmode="numeric" pattern="[0-9]{8}" class="input-field">
                    <p id="dniError" class="text-red-500 text-xs mt-1 hidden">Este DNI ya está registrado</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Teléfono
                        celular</label>
                    <input type="tel" id="phoneInput" name="phone" value="{{ old('phone', Auth::user()->phone ?? '') }}" required
                        placeholder="987654321" maxlength="9" inputmode="numeric" pattern="[0-9]{9}" oninput="this.value=this.value.replace(/\D/g,'')"
                        class="input-field">
                    <p id="phoneError" class="text-red-500 text-xs mt-1 hidden">Este celular ya está registrado</p>
                </div>
            </div>

            <!-- Ubigeo -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
                <div>
                    <label
                        class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Departamento</label>
                    <div class="custom-select" id="cs-department">
                        <button type="button" class="custom-select-trigger disabled" id="cs-department-trigger">
                            <span class="placeholder">Seleccionar</span>
                            <svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div class="custom-select-dropdown" id="cs-department-dropdown">
                            <div class="custom-select-search"><input type="text" placeholder="Buscar..."
                                    id="cs-department-search"></div>
                            <div id="cs-department-options"></div>
                        </div>
                        <input type="hidden" name="department" id="department" value="{{ old('department', Auth::user()->department ?? '') }}">
                    </div>
                </div>
                <div>
                    <label
                        class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Provincia</label>
                    <div class="custom-select" id="cs-province">
                        <button type="button" class="custom-select-trigger disabled" id="cs-province-trigger">
                            <span class="placeholder">Seleccionar</span>
                            <svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div class="custom-select-dropdown" id="cs-province-dropdown">
                            <div class="custom-select-search"><input type="text" placeholder="Buscar..."
                                    id="cs-province-search"></div>
                            <div id="cs-province-options"></div>
                        </div>
                        <input type="hidden" name="province" id="province" value="{{ old('province', Auth::user()->province ?? '') }}">
                    </div>
                </div>
                <div>
                    <label
                        class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Distrito</label>
                    <div class="custom-select" id="cs-district">
                        <button type="button" class="custom-select-trigger disabled" id="cs-district-trigger">
                            <span class="placeholder">Seleccionar</span>
                            <svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div class="custom-select-dropdown" id="cs-district-dropdown">
                            <div class="custom-select-search"><input type="text" placeholder="Buscar..."
                                    id="cs-district-search"></div>
                            <div id="cs-district-options"></div>
                        </div>
                        <input type="hidden" name="district" id="district" value="{{ old('district', Auth::user()->district ?? '') }}">
                    </div>
                </div>
            </div>

            <!-- Dirección -->
            <div>
                <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Dirección</label>
                <input type="text" name="address" value="{{ old('address', Auth::user()->address ?? '') }}" required
                    placeholder="Av. Arequipa 1234, Dpto 5B" class="input-field">
            </div>

            <!-- Submit -->
            <div class="pt-4 flex items-center justify-between">
                <button type="submit" form="form-logout"
                    class="flex items-center gap-2 text-red-500 hover:text-red-400 font-medium text-sm px-4 py-2 border-b border-red-500 hover:border-red-400 transition-colors bg-transparent">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Volver
                </button>
                <button type="submit" id="submitBtn"
                    class="flex items-center gap-2 text-white font-medium text-sm px-4 py-2 border-b border-green-500 hover:text-green-400 hover:border-green-400 transition-colors bg-transparent">
                    Siguiente
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

        </form>

        <!-- Form logout independiente -->
        <form id="form-logout" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>

    <script>
        function togglePersonType(val) {
            const extra = document.getElementById('juridica-fields');
            if (val === 'juridica') {
                extra.classList.remove('hidden');
            } else {
                extra.classList.add('hidden');
            }
        }

        function buildCustomSelect({ triggerId, dropdownId, optionsId, searchId, hiddenId, onSelect }) {
            const trigger = document.getElementById(triggerId);
            const dropdown = document.getElementById(dropdownId);
            const optsCont = document.getElementById(optionsId);
            const search = document.getElementById(searchId);
            const hidden = document.getElementById(hiddenId);

            function renderOptions(items) {
                optsCont.innerHTML = '';
                items.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'custom-select-option' + (hidden.value == item.id ? ' selected' : '');
                    div.textContent = item.name;
                    div.dataset.id = item.id;
                    div.addEventListener('click', () => {
                        hidden.value = item.id;
                        trigger.innerHTML = `<span>${item.name}</span><svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>`;
                        dropdown.classList.remove('open');
                        trigger.classList.remove('open');
                        if (onSelect) onSelect(item.id);
                    });
                    optsCont.appendChild(div);
                });
            }

            trigger.addEventListener('click', () => {
                if (trigger.classList.contains('disabled')) return;
                const isOpen = dropdown.classList.toggle('open');
                trigger.classList.toggle('open', isOpen);
                if (isOpen) { search.value = ''; search.focus(); renderOptions(trigger._items || []); }
            });

            search.addEventListener('input', () => {
                const q = search.value.toLowerCase();
                const filtered = (trigger._items || []).filter(i => i.name.toLowerCase().includes(q));
                renderOptions(filtered);
            });

            document.addEventListener('click', e => {
                if (!trigger.closest('.custom-select').contains(e.target)) {
                    dropdown.classList.remove('open');
                    trigger.classList.remove('open');
                }
            });

            return { trigger, renderOptions, hidden };
        }

        // ── Instanciar los 3 selects ──────────────────────────────────────
        const deptCS = buildCustomSelect({
            triggerId: 'cs-department-trigger', dropdownId: 'cs-department-dropdown',
            optionsId: 'cs-department-options', searchId: 'cs-department-search',
            hiddenId: 'department',
            onSelect: (id) => {
                // Reset provincia y distrito
                provCS.trigger._items = [];
                provCS.trigger.innerHTML = `<span class="placeholder">Seleccionar</span><svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>`;
                provCS.hidden.value = '';
                provCS.trigger.classList.add('disabled');
                distCS.trigger._items = [];
                distCS.trigger.innerHTML = `<span class="placeholder">Seleccionar</span><svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>`;
                distCS.hidden.value = '';
                distCS.trigger.classList.add('disabled');

                fetch(`/ubigeo/provinces/${id}`).then(r => r.json()).then(data => {
                    provCS.trigger._items = data;
                    provCS.trigger.classList.remove('disabled');
                });
            }
        });

        const provCS = buildCustomSelect({
            triggerId: 'cs-province-trigger', dropdownId: 'cs-province-dropdown',
            optionsId: 'cs-province-options', searchId: 'cs-province-search',
            hiddenId: 'province',
            onSelect: (id) => {
                distCS.trigger._items = [];
                distCS.trigger.innerHTML = `<span class="placeholder">Seleccionar</span><svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>`;
                distCS.hidden.value = '';
                distCS.trigger.classList.add('disabled');

                fetch(`/ubigeo/districts/${id}`).then(r => r.json()).then(data => {
                    distCS.trigger._items = data;
                    distCS.trigger.classList.remove('disabled');
                });
            }
        });

        const distCS = buildCustomSelect({
            triggerId: 'cs-district-trigger', dropdownId: 'cs-district-dropdown',
            optionsId: 'cs-district-options', searchId: 'cs-district-search',
            hiddenId: 'district'
        });

        fetch('/ubigeo/departments').then(r => r.json()).then(data => {
            deptCS.trigger._items = data;
            deptCS.trigger.classList.remove('disabled');
            @php
                $savedDept = old('department', Auth::user()->department ?? '');
                $savedProv = old('province', Auth::user()->province ?? '');
                $savedDist = old('district', Auth::user()->district ?? '');
            @endphp
            @if($savedDept)
                const savedDept = '{{ $savedDept }}';
                const found = data.find(d => d.id == savedDept);
                if (found) {
                    deptCS.hidden.value = found.id;
                    deptCS.trigger.innerHTML = `<span>${found.name}</span><svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>`;
                    fetch(`/ubigeo/provinces/${found.id}`).then(r => r.json()).then(provs => {
                        provCS.trigger._items = provs;
                        provCS.trigger.classList.remove('disabled');
                        @if($savedProv)
                            const savedProv = '{{ $savedProv }}';
                            const provFound = provs.find(p => p.id == savedProv);
                            if (provFound) {
                                provCS.hidden.value = provFound.id;
                                provCS.trigger.innerHTML = `<span>${provFound.name}</span><svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>`;
                                fetch(`/ubigeo/districts/${provFound.id}`).then(r => r.json()).then(dists => {
                                    distCS.trigger._items = dists;
                                    distCS.trigger.classList.remove('disabled');
                                    @if($savedDist)
                                        const savedDist = '{{ $savedDist }}';
                                        const distFound = dists.find(d => d.id == savedDist);
                                        if (distFound) {
                                            distCS.hidden.value = distFound.id;
                                            distCS.trigger.innerHTML = `<span>${distFound.name}</span><svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>`;
                                        }
                                    @endif
                                });
                            }
                        @endif
                    });
                }
            @endif
});

        // Bloquear botón atrás del navegador
        function noBack() { window.history.forward(); }
        // Llenar el historial con entradas de esta misma página para que no haya "atrás" disponible
        for (let i = 0; i < 20; i++) {
            history.pushState(null, '', location.href);
        }
        window.addEventListener('popstate', () => {
            history.pushState(null, '', location.href);
        });

        // Validación en tiempo real de DNI, Celular y RUC
        let dniCheckTimeout, phoneCheckTimeout, rucCheckTimeout;
        const dniInput = document.getElementById('dniInput');
        const phoneInput = document.getElementById('phoneInput');
        const rucInput = document.getElementById('rucInput');
        const dniError = document.getElementById('dniError');
        const phoneError = document.getElementById('phoneError');
        const rucError = document.getElementById('rucError');
        const submitBtn = document.getElementById('submitBtn');

        let dniValid = true;
        let phoneValid = true;
        let rucValid = true;

        function updateSubmitButton() {
            if (!dniValid || !phoneValid || !rucValid) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        if (rucInput) {
            rucInput.addEventListener('input', function () {
                clearTimeout(rucCheckTimeout);
                const ruc = this.value;

                if (ruc.length !== 11) {
                    rucError.classList.add('hidden');
                    rucValid = true;
                    updateSubmitButton();
                    return;
                }

                rucCheckTimeout = setTimeout(() => {
                    fetch('/check-ruc', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ ruc: ruc })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.exists) {
                                rucError.classList.remove('hidden');
                                rucValid = false;
                            } else {
                                rucError.classList.add('hidden');
                                rucValid = true;
                            }
                            updateSubmitButton();
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                }, 500);
            });
        }

        if (dniInput) {
            dniInput.addEventListener('input', function () {
                this.value = this.value.replace(/\D/g, '').slice(0, 8);
                clearTimeout(dniCheckTimeout);
                const dni = this.value;

                if (dni.length !== 8) {
                    dniError.classList.add('hidden');
                    dniValid = true;
                    updateSubmitButton();
                    return;
                }

                dniCheckTimeout = setTimeout(() => {
                    fetch('/check-dni', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ dni: dni })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.exists) {
                                dniError.classList.remove('hidden');
                                dniValid = false;
                            } else {
                                dniError.classList.add('hidden');
                                dniValid = true;
                            }
                            updateSubmitButton();
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                }, 500);
            });
        }

        if (phoneInput) {
            phoneInput.addEventListener('input', function () {
                clearTimeout(phoneCheckTimeout);
                const phone = this.value;

                if (phone.length !== 9) {
                    phoneError.classList.add('hidden');
                    phoneValid = true;
                    updateSubmitButton();
                    return;
                }

                phoneCheckTimeout = setTimeout(() => {
                    fetch('/check-phone', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ phone: phone })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.exists) {
                                phoneError.classList.remove('hidden');
                                phoneValid = false;
                            } else {
                                phoneError.classList.add('hidden');
                                phoneValid = true;
                            }
                            updateSubmitButton();
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                }, 500);
            });
        }
    </script>

    @include('partials.global-scripts')

    <!-- Subtle loading overlay -->
    <div id="pk-loading" class="fixed inset-0 z-[9999] pointer-events-none flex items-center justify-center opacity-0 transition-opacity duration-300">
        <div class="bg-black/60 backdrop-blur-sm absolute inset-0"></div>
        <div class="relative flex flex-col items-center gap-3">
            <div class="w-8 h-8 border-2 border-gray-600 border-t-green-500 rounded-full animate-spin"></div>
            <p class="text-xs font-medium text-gray-400">Procesando...</p>
        </div>
    </div>
    <script>
        document.addEventListener('submit', function(e) {
            var form = e.target;
            if (form.tagName === 'FORM' && !form.dataset.noLoading) {
                var overlay = document.getElementById('pk-loading');
                if (overlay) {
                    overlay.classList.remove('opacity-0');
                    overlay.classList.add('opacity-100');
                    overlay.classList.add('pointer-events-auto');
                }
            }
        });
    </script>

</body>

</html>