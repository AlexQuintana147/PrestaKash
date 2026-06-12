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
            background: rgba(255,255,255,0.04);
            border: 1px solid #2a2a2a;
            border-radius: 10px;
            color: white;
            padding: 12px 16px;
            font-size: 0.9rem;
            outline: none;
            transition: all 0.2s;
        }

        .input-field:focus {
            border-color: #10b981;
            background: rgba(16,185,129,0.05);
            box-shadow: 0 0 0 3px rgba(16,185,129,0.08);
        }

        .input-field::placeholder {
            color: #4b5563;
        }

        .input-field:disabled {
            color: #9ca3af;
            cursor: not-allowed;
            opacity: 0.6;
            background: rgba(255,255,255,0.02);
        }

        select.input-field option {
            background: #111111;
            color: white;
        }

        select.input-field:disabled {
            opacity: 0.4;
        }

        /* Custom select */
        .custom-select { position: relative; }

        .custom-select-trigger {
            width: 100%;
            background: rgba(255,255,255,0.04);
            border: 1px solid #2a2a2a;
            border-radius: 10px;
            color: white;
            padding: 12px 40px 12px 16px;
            font-size: 0.9rem;
            cursor: pointer;
            display: flex; justify-content: space-between; align-items: center;
            transition: all 0.2s;
            font-family: 'Roboto', sans-serif;
        }

        .custom-select-trigger:focus,
        .custom-select-trigger.open {
            outline: none;
            border-color: #10b981;
            background: rgba(16,185,129,0.05);
            box-shadow: 0 0 0 3px rgba(16,185,129,0.08);
        }

        .custom-select-trigger.disabled {
            opacity: 0.35;
            cursor: not-allowed;
            pointer-events: none;
        }

        .custom-select-trigger .placeholder { color: #4b5563; }

        .custom-select-dropdown {
            position: absolute;
            top: calc(100% + 6px);
            left: 0; right: 0;
            background: #0f0f0f;
            border: 1px solid #222;
            border-radius: 12px;
            max-height: 220px;
            overflow-y: auto;
            z-index: 50;
            display: none;
            box-shadow: 0 20px 50px rgba(0,0,0,0.6), 0 0 0 1px rgba(255,255,255,0.03);
        }
        .custom-select-dropdown.open { display: block; }

        .custom-select-search {
            padding: 12px 14px;
            border-bottom: 1px solid #1a1a1a;
            position: sticky; top: 0; background: #0f0f0f;
            border-radius: 12px 12px 0 0;
        }
        .custom-select-search input {
            width: 100%; background: rgba(255,255,255,0.04); border: 1px solid #222; border-radius: 8px;
            color: white; font-size: 0.82rem; outline: none; padding: 8px 10px;
        }
        .custom-select-search input:focus { border-color: #10b981; }
        .custom-select-search input::placeholder { color: #4b5563; }

        .custom-select-option {
            padding: 11px 16px; font-size: 0.85rem; color: #d1d5db; cursor: pointer;
            transition: all 0.15s;
        }
        .custom-select-option:hover { background: rgba(255,255,255,0.06); color: white; }
        .custom-select-option.selected { color: #10b981; font-weight: 500; }

        .custom-select-dropdown::-webkit-scrollbar { width: 4px; }
        .custom-select-dropdown::-webkit-scrollbar-track { background: transparent; }
        .custom-select-dropdown::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }

        /* Stepper */
        .step-section {
            display: none; opacity: 0; transform: translateY(10px);
            transition: opacity 0.35s ease, transform 0.35s ease;
        }
        .step-section.active { display: block; opacity: 1; transform: translateY(0); }

        .stepper-dot {
            width: 32px; height: 32px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700;
            border: 2px solid #374151; color: #6b7280;
            background: transparent; transition: all 0.35s ease; flex-shrink: 0;
        }
        .stepper-dot.completed { background: #10b981; border-color: #10b981; color: #000; }
        .stepper-dot.active {
            border-color: #10b981; color: #10b981;
            box-shadow: 0 0 16px rgba(16,185,129,0.3), inset 0 0 8px rgba(16,185,129,0.1);
        }
        .stepper-line { height: 2px; flex: 1; background: #252525; transition: background 0.35s ease; min-width: 24px; }
        .stepper-line.completed { background: linear-gradient(90deg, #10b981, #34d399); }

        .stepper-label {
            font-size: 9px; color: #4b5563; font-weight: 600; text-transform: uppercase;
            letter-spacing: 0.08em; transition: color 0.35s ease; margin-top: 6px;
        }
        .stepper-label.active { color: #10b981; }
        .stepper-label.completed { color: #6b7280; }

        /* Field group card */
        .field-group {
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 14px;
            padding: 22px 20px;
            margin-bottom: 18px;
        }
        .field-group-label {
            font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em;
            color: #10b981; margin-bottom: 16px; display: flex; align-items: center; gap: 6px;
        }
        .field-group-label::before {
            content: ''; width: 3px; height: 12px; background: #10b981; border-radius: 2px;
        }

        /* Radio cards */
        .radio-card {
            flex: 1; padding: 14px 18px; border: 1px solid #252525; border-radius: 12px;
            cursor: pointer; transition: all 0.2s; text-align: center;
            background: rgba(255,255,255,0.01);
        }
        .radio-card:hover { border-color: #333; background: rgba(255,255,255,0.03); }
        .radio-card.selected { border-color: #10b981; background: rgba(16,185,129,0.06); }
        .radio-card-text { font-size: 13px; font-weight: 500; color: #9ca3af; transition: color 0.2s; }
        .radio-card.selected .radio-card-text { color: #10b981; }

        /* Nav buttons */
        .nav-btn {
            display: inline-flex; align-items: center; gap: 8px;
            font-size: 13px; font-weight: 600; padding: 10px 22px;
            border-radius: 10px; transition: all 0.2s; cursor: pointer;
            border: none; outline: none;
        }
        .nav-btn-primary {
            background: #10b981; color: #000;
        }
        .nav-btn-primary:hover { background: #059669; box-shadow: 0 4px 16px rgba(16,185,129,0.25); }
        .nav-btn-secondary {
            background: rgba(255,255,255,0.05); color: #9ca3af; border: 1px solid #252525;
        }
        .nav-btn-secondary:hover { background: rgba(255,255,255,0.08); color: #d1d5db; border-color: #333; }
        .nav-btn-submit {
            background: linear-gradient(135deg, #10b981, #059669); color: #000;
            font-weight: 700; letter-spacing: 0.02em;
        }
        .nav-btn-submit:hover { box-shadow: 0 6px 24px rgba(16,185,129,0.3); transform: translateY(-1px); }

        /* Helper text */
        .helper-text { font-size: 11px; color: #4b5563; margin-top: 6px; line-height: 1.4; }
        .error-text { font-size: 11px; color: #ef4444; margin-top: 6px; }

        /* Form label */
        .form-label {
            display: block; font-size: 11px; font-weight: 600; text-transform: uppercase;
            letter-spacing: 0.06em; color: #9ca3af; margin-bottom: 8px;
        }

        /* Field error state */
        .input-field.field-error {
            border-color: #ef4444 !important;
            background: rgba(239,68,68,0.06) !important;
            box-shadow: 0 0 0 3px rgba(239,68,68,0.1) !important;
        }
        .field-error-msg {
            font-size: 11px; color: #ef4444; margin-top: 6px; display: flex; align-items: center; gap: 4px;
        }
    </style>
</head>

<body class="bg-black min-h-screen flex items-center justify-center px-4 py-10" onload="noBack();"
    onpageshow="if(event.persisted) noBack();">
    <div class="w-full max-w-lg">

        <!-- Header -->
        <div class="mb-8 text-center">
            <p class="text-[11px] font-bold text-green-400 uppercase tracking-[0.2em] mb-2">PrestaKash</p>
            <h1 class="text-[22px] font-bold text-white mb-1">Cuéntanos sobre ti</h1>
            <p class="text-gray-500 text-xs">Paso 1 de 2 — Completa tus datos</p>

            <!-- Stepper -->
            <div class="mt-6 flex items-center justify-center gap-0">
                <div class="flex flex-col items-center" style="min-width:60px">
                    <div class="stepper-dot active" id="stepper-dot-1">1</div>
                    <span class="stepper-label active" id="stepper-label-1">Identidad</span>
                </div>
                <div class="stepper-line" id="stepper-line-1"></div>
                <div class="flex flex-col items-center" style="min-width:60px">
                    <div class="stepper-dot" id="stepper-dot-2">2</div>
                    <span class="stepper-label" id="stepper-label-2">Contacto</span>
                </div>
                <div class="stepper-line" id="stepper-line-2"></div>
                <div class="flex flex-col items-center" style="min-width:60px">
                    <div class="stepper-dot" id="stepper-dot-3">3</div>
                    <span class="stepper-label" id="stepper-label-3">Ubicación</span>
                </div>
                <div class="stepper-line" id="stepper-line-3"></div>
                <div class="flex flex-col items-center" style="min-width:60px">
                    <div class="stepper-dot" id="stepper-dot-4">4</div>
                    <span class="stepper-label" id="stepper-label-4">Dirección</span>
                </div>
            </div>
        </div>

        @if(session('error') || $errors->any())
            <div class="mb-5 p-4 bg-red-500/8 border border-red-500/20 rounded-xl">
                @if(session('error'))
                    <p class="text-red-400 text-xs flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        {{ session('error') }}
                    </p>
                @endif
                @foreach($errors->all() as $error)
                    <p class="text-red-400 text-xs flex items-center gap-2 mt-1">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        {{ $error }}
                    </p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('onboarding.step1.store') }}" method="POST" id="onboarding-form" novalidate>
            @csrf

            <script>
            // Prevenir que ENTER envíe el formulario desde campos de texto
            document.getElementById('onboarding-form').addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && ['INPUT', 'TEXTAREA'].includes(e.target.tagName)) {
                    e.preventDefault();
                    // Simular clic en el botón "Continuar" del paso actual
                    var currentSection = document.getElementById('step-section-' + currentStep);
                    if (currentSection) {
                        var continueBtn = currentSection.querySelector('.nav-btn-primary');
                        if (continueBtn) continueBtn.click();
                    }
                }
            });
            </script>

            <!-- ========== SECCIÓN 1: Identidad ========== -->
            <div class="step-section active" id="step-section-1">

                <!-- Tipo de persona - Radio cards -->
                <div class="field-group">
                    <div class="field-group-label">Tipo de persona</div>
                    <div class="flex gap-3">
                        <label class="radio-card {{ (old('person_type', Auth::user()->person_type ?? 'natural') == 'natural') ? 'selected' : '' }}">
                            <input type="radio" name="person_type" value="natural"
                                {{ (old('person_type', Auth::user()->person_type ?? 'natural') == 'natural') ? 'checked' : '' }}
                                class="hidden peer" onchange="togglePersonType(this.value); updateRadioCards(this)">
                            <span class="radio-card-text">Persona Natural</span>
                        </label>
                        <label class="radio-card {{ (old('person_type', Auth::user()->person_type ?? '') == 'juridica') ? 'selected' : '' }}">
                            <input type="radio" name="person_type" value="juridica"
                                {{ (old('person_type', Auth::user()->person_type ?? '') == 'juridica') ? 'checked' : '' }}
                                class="hidden peer" onchange="togglePersonType(this.value); updateRadioCards(this)">
                            <span class="radio-card-text">Persona Jurídica</span>
                        </label>
                    </div>
                </div>

                <!-- Campos jurídicos (condicional) -->
                <div id="juridica-fields" class="{{ (old('person_type', Auth::user()->person_type ?? '') == 'juridica') ? '' : 'hidden' }} field-group">
                    <div class="field-group-label">Datos de la empresa</div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">RUC</label>
                            <input type="text" id="rucInput" name="ruc" value="{{ old('ruc', Auth::user()->ruc ?? '') }}" maxlength="11"
                                oninput="this.value=this.value.replace(/\D/g,''); clearFieldError(this)" class="input-field"
                                placeholder="20123456789" data-required-if-juridica="true" data-label="RUC" data-minlength="11">
                            <p id="rucError" class="error-text hidden">Este RUC ya está registrado</p>
                        </div>
                        <div>
                            <label class="form-label">Razón Social</label>
                            <input type="text" name="business_name" value="{{ old('business_name', Auth::user()->business_name ?? '') }}"
                                oninput="this.value=this.value.replace(/[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s.&,\-\(\)\'\"\/]/g,''); clearFieldError(this)"
                                class="input-field" placeholder="Empresa S.A.C." data-required-if-juridica="true" data-label="Razón Social">
                        </div>
                    </div>
                </div>

                <!-- Nombres y Apellidos -->
                <div class="field-group">
                    <div class="field-group-label">Nombre completo</div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Nombres</label>
                            <input type="text" name="first_name" value="{{ old('first_name', Auth::user()->first_name ?? '') }}"
                                placeholder="Ej: Mateo Pedro"
                                oninput="this.value=this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g,'').toLowerCase().replace(/(^|\s)\S/g,l=>l.toUpperCase()); clearFieldError(this)"
                                class="input-field" data-required="true" data-label="Nombres">
                        </div>
                        <div>
                            <label class="form-label">Apellidos</label>
                            <input type="text" name="last_name" value="{{ old('last_name', Auth::user()->last_name ?? '') }}"
                                placeholder="Ej: Castillo Villa"
                                oninput="this.value=this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g,'').toLowerCase().replace(/(^|\s)\S/g,l=>l.toUpperCase()); clearFieldError(this)"
                                class="input-field" data-required="true" data-label="Apellidos">
                            <p class="helper-text">Ingresa todos tus apellidos separados por espacio.</p>
                        </div>
                    </div>
                </div>

                <!-- Nav sección 1 -->
                <div class="flex justify-end mt-6">
                    <button type="button" onclick="goToStep(2)" class="nav-btn nav-btn-primary">
                        Continuar
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

            <!-- ========== SECCIÓN 2: Contacto ========== -->
            <div class="step-section" id="step-section-2">

                <div class="field-group">
                    <div class="field-group-label">Información de contacto</div>

                    <div class="mb-5">
                        <label class="form-label">Correo electrónico</label>
                        <input type="email" value="{{ Auth::user()->email }}" disabled class="input-field">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Número de DNI</label>
                            <input type="text" id="dniInput" name="dni" value="{{ old('dni', Auth::user()->dni ?? '') }}"
                                placeholder="12345678" maxlength="8" inputmode="numeric"
                                oninput="this.value=this.value.replace(/\D/g,''); clearFieldError(this)"
                                class="input-field" data-required="true" data-label="DNI" data-minlength="8">
                            <p id="dniError" class="error-text hidden">Este DNI ya está registrado</p>
                        </div>
                        <div>
                            <label class="form-label">Teléfono celular</label>
                            <input type="tel" id="phoneInput" name="phone" value="{{ old('phone', Auth::user()->phone ?? '') }}"
                                placeholder="987654321" maxlength="9" inputmode="numeric"
                                oninput="this.value=this.value.replace(/\D/g,''); clearFieldError(this)"
                                class="input-field" data-required="true" data-label="Celular" data-minlength="9">
                            <p id="phoneError" class="error-text hidden">Este celular ya está registrado</p>
                        </div>
                    </div>
                </div>

                <!-- Nav sección 2 -->
                <div class="flex items-center justify-between mt-6">
                    <button type="button" onclick="goToStep(1)" class="nav-btn nav-btn-secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Anterior
                    </button>
                    <button type="button" id="step2ContinueBtn" onclick="goToStep(3)" class="nav-btn nav-btn-primary">
                        Continuar
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

            <!-- ========== SECCIÓN 3: Ubicación ========== -->
            <div class="step-section" id="step-section-3">

                <div class="field-group">
                    <div class="field-group-label">Ubicación (Ubigeo)</div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="form-label">Departamento</label>
                            <div class="custom-select" id="cs-department">
                                <button type="button" class="custom-select-trigger disabled" id="cs-department-trigger">
                                    <span class="placeholder">Seleccionar</span>
                                    <svg class="w-3.5 h-3.5 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div class="custom-select-dropdown" id="cs-department-dropdown">
                                    <div class="custom-select-search"><input type="text" placeholder="Buscar..." id="cs-department-search"></div>
                                    <div id="cs-department-options"></div>
                                </div>
                                <input type="hidden" name="department" id="department" value="{{ old('department', Auth::user()->department ?? '') }}">
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Provincia</label>
                            <div class="custom-select" id="cs-province">
                                <button type="button" class="custom-select-trigger disabled" id="cs-province-trigger">
                                    <span class="placeholder">Seleccionar</span>
                                    <svg class="w-3.5 h-3.5 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div class="custom-select-dropdown" id="cs-province-dropdown">
                                    <div class="custom-select-search"><input type="text" placeholder="Buscar..." id="cs-province-search"></div>
                                    <div id="cs-province-options"></div>
                                </div>
                                <input type="hidden" name="province" id="province" value="{{ old('province', Auth::user()->province ?? '') }}">
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Distrito</label>
                            <div class="custom-select" id="cs-district">
                                <button type="button" class="custom-select-trigger disabled" id="cs-district-trigger">
                                    <span class="placeholder">Seleccionar</span>
                                    <svg class="w-3.5 h-3.5 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div class="custom-select-dropdown" id="cs-district-dropdown">
                                    <div class="custom-select-search"><input type="text" placeholder="Buscar..." id="cs-district-search"></div>
                                    <div id="cs-district-options"></div>
                                </div>
                                <input type="hidden" name="district" id="district" value="{{ old('district', Auth::user()->district ?? '') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Nav sección 3 -->
                <div class="flex items-center justify-between mt-6">
                    <button type="button" onclick="goToStep(2)" class="nav-btn nav-btn-secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Anterior
                    </button>
                    <button type="button" onclick="goToStep(4)" class="nav-btn nav-btn-primary">
                        Continuar
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

            <!-- ========== SECCIÓN 4: Dirección + Submit ========== -->
            <div class="step-section" id="step-section-4">

                <div class="field-group">
                    <div class="field-group-label">Dirección completa</div>
                    <div>
                        <label class="form-label">Dirección</label>
                        <input type="text" name="address" value="{{ old('address', Auth::user()->address ?? '') }}"
                            placeholder="Av. Arequipa 1234, Dpto 5B"
                            oninput="clearFieldError(this)"
                            class="input-field" data-required="true" data-label="Dirección">
                        <p class="helper-text">Ingresa tu dirección exacta incluyendo número y referencia si es necesario.</p>
                    </div>
                </div>

                <!-- Nav final -->
                <div class="flex items-center justify-between mt-6">
                    <button type="button" onclick="goToStep(3)" class="nav-btn nav-btn-secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Anterior
                    </button>
                    <button type="submit" id="submitBtn" class="nav-btn nav-btn-submit">
                        Continuar al Paso 2
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

        </form>

        <!-- Form logout independiente -->
        <form id="form-logout" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>

    <script>
        // ===== STEPPER / BARRA DE PROGRESO STEP 1 =====
        let currentStep = 1;
        const totalSteps = 4;

        function goToStep(step, keepErrors) {
            if (step < 1 || step > totalSteps) return;

            // Si vamos hacia adelante, validar el paso actual primero
            if (step > currentStep) {
                const result = validateStep(currentStep);
                if (!result.valid) {
                    result.firstInvalid?.focus();
                    result.firstInvalid?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return; // No avanzar si hay errores
                }
            }

            // Limpiar errores al cambiar de paso (a menos que se indique lo contrario)
            if (!keepErrors) clearAllErrors();

            // Ocultar sección actual
            const currentSection = document.getElementById('step-section-' + currentStep);
            if (currentSection) {
                currentSection.classList.remove('active');
                setTimeout(() => { if (!currentSection.classList.contains('active')) currentSection.style.display = 'none'; }, 300);
            }

            // Mostrar nueva sección
            const newSection = document.getElementById('step-section-' + step);
            if (newSection) {
                newSection.style.display = 'block';
                // Forzar reflow para que la animación funcione
                void newSection.offsetWidth;
                newSection.classList.add('active');
            }

            // Actualizar dots y líneas del stepper
            for (let i = 1; i <= totalSteps; i++) {
                const dot = document.getElementById('stepper-dot-' + i);
                const label = document.getElementById('stepper-label-' + i);
                const line = document.getElementById('stepper-line-' + i);

                if (i < step) {
                    // Completado
                    dot.className = 'stepper-dot completed';
                    dot.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>';
                    label.className = 'stepper-label completed';
                    if (line) line.className = 'stepper-line completed';
                } else if (i === step) {
                    // Activo
                    dot.className = 'stepper-dot active';
                    dot.textContent = i;
                    label.className = 'stepper-label active';
                    if (line) line.className = 'stepper-line';
                } else {
                    // Pendiente
                    dot.className = 'stepper-dot';
                    dot.textContent = i;
                    label.className = 'stepper-label';
                    if (line) line.className = 'stepper-line';
                }
            }

            currentStep = step;

            // Scroll suave al inicio del formulario
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function updateRadioCards(radioInput) {
            document.querySelectorAll('.radio-card').forEach(card => {
                card.classList.remove('selected');
            });
            if (radioInput.checked) {
                radioInput.closest('.radio-card').classList.add('selected');
            }
        }

        // Inicializar estado de radio cards
        document.querySelectorAll('input[name="person_type"]').forEach(r => {
            if (r.checked) r.closest('.radio-card').classList.add('selected');
        });

        // ===== VALIDACIÓN PERSONALIZADA DE CAMPOS =====
        function showFieldError(input, message) {
            input.classList.add('field-error');
            // Buscar o crear mensaje de error
            let msgEl = input.parentElement.querySelector('.field-error-msg');
            if (!msgEl) {
                msgEl = document.createElement('p');
                msgEl.className = 'field-error-msg';
                input.parentElement.appendChild(msgEl);
            }
            msgEl.innerHTML = '<svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> ' + message;
            msgEl.style.display = 'flex';
        }

        function clearFieldError(input) {
            input.classList.remove('field-error');
            const msgEl = input.parentElement.querySelector('.field-error-msg');
            if (msgEl) { msgEl.remove(); }
        }

        function clearAllErrors() {
            document.querySelectorAll('.field-error').forEach(el => clearFieldError(el));
        }

        function validateStep(stepNum) {
            const section = document.getElementById('step-section-' + stepNum);
            if (!section) return true;

            const fields = section.querySelectorAll('[data-required], [data-required-if-juridica]');
            let isValid = true;
            let firstInvalid = null;

            const isJuridica = document.querySelector('input[name="person_type"]:checked')?.value === 'juridica';

            fields.forEach(field => {
                // Si es campo condicional de jurídica y no es jurídica, saltar
                if (field.hasAttribute('data-required-if-juridica') && !isJuridica) return;

                const label = field.getAttribute('data-label') || 'Este campo';
                const value = field.value.trim();

                // Requerido
                if (value === '') {
                    showFieldError(field, label + ' es obligatorio');
                    if (!firstInvalid) firstInvalid = field;
                    isValid = false;
                    return;
                }

                // Longitud mínima (para DNI=8, Celular=9, RUC=11)
                const minLen = field.getAttribute('data-minlength');
                if (minLen && value.length < parseInt(minLen)) {
                    showFieldError(field, label + ' debe tener ' + minLen + ' dígitos');
                    if (!firstInvalid) firstInvalid = field;
                    isValid = false;
                    return;
                }

                // Validaciones específicas por campo
                const name = field.getAttribute('name');
                if (name === 'phone' && !/^9\d{8}$/.test(value)) {
                    showFieldError(field, label + ' debe comenzar con 9 y tener 9 dígitos');
                    if (!firstInvalid) firstInvalid = field;
                    isValid = false;
                    return;
                }
                if (name === 'dni' && !/^\d{8}$/.test(value)) {
                    showFieldError(field, label + ' debe tener exactamente 8 dígitos');
                    if (!firstInvalid) firstInvalid = field;
                    isValid = false;
                    return;
                }
                if (name === 'last_name') {
                    const words = value.split(/\s+/).filter(w => w.length > 0);
                    if (words.length < 2) {
                        showFieldError(field, 'Debes ingresar al menos dos apellidos');
                        if (!firstInvalid) firstInvalid = field;
                        isValid = false;
                        return;
                    }
                }
                if (name === 'address') {
                    const words = value.split(/\s+/).filter(w => w.length > 0);
                    if (words.length < 2) {
                        showFieldError(field, 'La dirección debe tener al menos dos palabras');
                        if (!firstInvalid) firstInvalid = field;
                        isValid = false;
                        return;
                    }
                }
                if (name === 'ruc' && value !== '' && !/^(10|20)\d{9}$/.test(value)) {
                    showFieldError(field, label + ' debe tener 11 dígitos y comenzar con 10 o 20');
                    if (!firstInvalid) firstInvalid = field;
                    isValid = false;
                    return;
                }
            });

            // Validar que teléfono/DNI no estén duplicados (solo en paso 2)
            if (stepNum === 2) {
                const phoneField = section.querySelector('[name="phone"]');
                if (phoneField && !phoneValid && phoneField.value.trim().length === 9) {
                    showFieldError(phoneField, 'Este celular ya está registrado');
                    if (!firstInvalid) firstInvalid = phoneField;
                    isValid = false;
                }
                const dniField = section.querySelector('[name="dni"]');
                if (dniField && !dniValid && dniField.value.trim().length === 8) {
                    showFieldError(dniField, 'Este DNI ya está registrado');
                    if (!firstInvalid) firstInvalid = dniField;
                    isValid = false;
                }
            }

            return { valid: isValid, firstInvalid };
        }

        // ===== VALIDAR TODO AL SUBMIT =====
        document.getElementById('onboarding-form').addEventListener('submit', function(e) {
            clearAllErrors();
            for (let s = 1; s <= totalSteps; s++) {
                const result = validateStep(s);
                if (!result.valid) {
                    e.preventDefault();
                    goToStep(s, true); // true = no borrar los errores recién mostrados
                    setTimeout(() => {
                        result.firstInvalid?.focus();
                        result.firstInvalid?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 350);
                    return;
                }
            }
            // Validación OK — mostrar overlay de "Procesando..."
            var overlay = document.getElementById('pk-loading');
            if (overlay) {
                overlay.classList.remove('opacity-0');
                overlay.classList.add('opacity-100');
                overlay.classList.add('pointer-events-auto');
            }
        });

        // ===== AUTO-NAVEGAR AL PASO CON ERRORES =====
        (function() {
            // Mapeo: campo -> número de paso donde está
            const fieldToStep = {
                'person_type': 1, 'ruc': 1, 'business_name': 1,
                'first_name': 1, 'last_name': 1,
                'email': 2, 'dni': 2, 'phone': 2,
                'department': 3, 'province': 3, 'district': 3,
                'address': 4
            };

            // Detectar errores de Laravel (se muestran en el bloque de errores general)
            // y también errores inline de validación HTML5
            let targetStep = 1;

            // 1) Buscar si hay errores generales en la página (bloque de alerta roja)
            const errorBlock = document.querySelector('.bg-red-500\\/8');
            if (errorBlock) {
                // Hay errores - buscar qué campos tienen error basado en los mensajes
                // Los errores de Laravel suelen mencionar el nombre del campo
                const errorText = errorBlock.textContent.toLowerCase();
                for (const [field, step] of Object.entries(fieldToStep)) {
                    if (errorText.includes(field) || errorText.includes(field.replace('_', ' '))) {
                        if (step > targetStep) targetStep = step;
                    }
                }
                // Palabras clave adicionales por sección
                if (errorText.includes('ruc') || errorText.includes('razón') || errorText.includes('razon') || errorText.includes('empresa')) targetStep = Math.max(targetStep, 1);
                if (errorText.includes('nombre') || errorText.includes('apellido')) targetStep = Math.max(targetStep, 1);
                if (errorText.includes('dni') || errorText.includes('celular') || errorText.includes('teléfono') || errorText.includes('correo') || errorText.includes('email')) targetStep = Math.max(targetStep, 2);
                if (errorText.includes('departamento') || errorText.includes('provincia') || errorText.includes('distrito') || errorText.includes('ubigeo')) targetStep = Math.max(targetStep, 3);
                if (errorText.includes('dirección') || errorText.includes('direccion') || errorText.includes('address')) targetStep = Math.max(targetStep, 4);

                // Si se encontró un paso con error (>1), navegar a él
                if (targetStep > 1) {
                    // Ocultar paso 1 inmediatamente (sin animación)
                    const s1 = document.getElementById('step-section-1');
                    if (s1) { s1.classList.remove('active'); s1.style.display = 'none'; }
                    goToStep(targetStep);
                }
            }
        })();

        function togglePersonType(val) {
            const extra = document.getElementById('juridica-fields');
            const rucInput = document.getElementById('rucInput');
            const bizInput = document.querySelector('input[name="business_name"]');
            if (val === 'juridica') {
                extra.classList.remove('hidden');
            } else {
                extra.classList.add('hidden');
                // Limpiar campos jurídicos para evitar validaciones cruzadas
                if (rucInput) { rucInput.value = ''; }
                if (bizInput) { bizInput.value = ''; }
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
            const blocked = !dniValid || !phoneValid || !rucValid;
            if (blocked) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
            // También bloquear el botón "Continuar" del paso 2 si el teléfono o DNI están duplicados
            const step2Btn = document.getElementById('step2ContinueBtn');
            if (step2Btn) {
                const step2Blocked = !dniValid || !phoneValid;
                if (step2Blocked) {
                    step2Btn.disabled = true;
                    step2Btn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    step2Btn.disabled = false;
                    step2Btn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
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
                        redirect: 'error',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ ruc: ruc })
                    })
                        .then(response => {
                            if (!response.ok) throw new Error('Server error');
                            return response.json();
                        })
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
                    dniInput.classList.remove('field-error');
                    dniValid = true;
                    updateSubmitButton();
                    return;
                }

                dniCheckTimeout = setTimeout(() => {
                    fetch('/check-dni', {
                        method: 'POST',
                        redirect: 'error',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ dni: dni })
                    })
                        .then(response => {
                            if (!response.ok) throw new Error('Server error');
                            return response.json();
                        })
                        .then(data => {
                            if (data.exists) {
                                dniError.classList.remove('hidden');
                                dniInput.classList.add('field-error');
                                dniValid = false;
                            } else {
                                dniError.classList.add('hidden');
                                dniInput.classList.remove('field-error');
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
                    phoneInput.classList.remove('field-error');
                    phoneValid = true;
                    updateSubmitButton();
                    return;
                }

                phoneCheckTimeout = setTimeout(() => {
                    fetch('/check-phone', {
                        method: 'POST',
                        redirect: 'error',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ phone: phone })
                    })
                        .then(response => {
                            if (!response.ok) throw new Error('Server error');
                            return response.json();
                        })
                        .then(data => {
                            if (data.exists) {
                                phoneError.classList.remove('hidden');
                                phoneInput.classList.add('field-error');
                                phoneValid = false;
                            } else {
                                phoneError.classList.add('hidden');
                                phoneInput.classList.remove('field-error');
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

        // Verificar campos precargados al cargar la página
        function checkPrefilledFields() {
            // Verificar DNI
            if (dniInput && dniInput.value.length === 8) {
                fetch('/check-dni', {
                    method: 'POST',
                    redirect: 'error',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ dni: dniInput.value })
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Server error');
                        return response.json();
                    })
                    .then(data => {
                        if (data.exists) {
                            document.getElementById('dniError').classList.remove('hidden');
                            dniInput.classList.add('field-error');
                            dniValid = false;
                        }
                        updateSubmitButton();
                    })
                    .catch(() => {});
            }
            // Verificar teléfono
            if (phoneInput && phoneInput.value.length === 9) {
                fetch('/check-phone', {
                    method: 'POST',
                    redirect: 'error',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ phone: phoneInput.value })
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Server error');
                        return response.json();
                    })
                    .then(data => {
                        if (data.exists) {
                            phoneError.classList.remove('hidden');
                            phoneInput.classList.add('field-error');
                            phoneValid = false;
                        }
                        updateSubmitButton();
                    })
                    .catch(() => {});
            }
            // Verificar RUC
            if (rucInput && rucInput.value.length === 11) {
                fetch('/check-ruc', {
                    method: 'POST',
                    redirect: 'error',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ ruc: rucInput.value })
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Server error');
                        return response.json();
                    })
                    .then(data => {
                        if (data.exists) {
                            document.getElementById('rucError').classList.remove('hidden');
                            rucValid = false;
                        }
                        updateSubmitButton();
                    })
                    .catch(() => {});
            }
        }
        checkPrefilledFields();
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

</body>

</html>