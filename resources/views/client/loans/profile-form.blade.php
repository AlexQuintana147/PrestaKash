@extends('client.layout')

@section('title', 'Completar perfil')

@section('content')
    <style>
        .option-card {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 12px;
            border: 1.5px solid #d1d5db;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.15s;
            font-size: 0.75rem;
            color: #111827;
            user-select: none;
        }

        .dark .option-card {
            border-color: #374151;
            color: #d1d5db;
        }

        .option-card:hover {
            border-color: #6b7280;
        }

        .dark .option-card:hover {
            border-color: #6b7280;
        }

        .option-card input[type=radio] {
            display: none;
        }

        .option-card .check-box {
            width: 16px;
            height: 16px;
            border: 1.5px solid #9ca3af;
            border-radius: 4px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
        }

        .dark .option-card .check-box {
            border-color: #4b5563;
        }

        .option-card input[type=radio]:checked~.check-box,
        .option-card.selected .check-box {
            background: #111827;
            border-color: #111827;
        }

        .dark .option-card input[type=radio]:checked~.check-box,
        .dark .option-card.selected .check-box {
            background: #ffffff;
            border-color: #ffffff;
        }

        .dark .option-card input[type=radio]:checked~.check-box svg path,
        .dark .option-card.selected .check-box svg path {
            stroke: #111827;
        }

        .option-card input[type=radio]:checked~.check-box svg,
        .option-card.selected .check-box svg {
            display: block;
        }

        .check-box svg {
            display: none;
        }

        .option-card input[type=radio]:checked~span {
            color: #111827;
            font-weight: 500;
        }

        .dark .option-card input[type=radio]:checked~span {
            color: #ffffff;
        }

        .option-card:has(input[type=radio]:checked),
        .option-card.selected {
            border-color: #111827;
        }

        .dark .option-card:has(input[type=radio]:checked),
        .dark .option-card.selected {
            border-color: #ffffff;
        }

        /* Custom scrollbar for dark mode compatibility */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 5px;
            border: 2px solid #f1f1f1;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #aaa;
        }

        .dark ::-webkit-scrollbar-track {
            background: #0f172a;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #334155;
            border: 2px solid #0f172a;
        }

        .dark ::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }
    </style>

    <div class="min-h-screen px-5 md:px-8 py-6">

        <div class="mb-6 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-widest mb-0.5">Préstamos</p>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                    {{ isset($editing) ? 'Editar perfil' : 'Completa tu perfil' }}
                </h1>
                <p class="text-xs text-gray-500 mt-0.5">Necesitamos algunos datos antes de continuar.</p>
            </div>
            @if(isset($editing))
            <a href="{{ route('client.dashboard') }}"
                class="inline-flex items-center gap-2 text-xs font-semibold text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver
            </a>
            @endif
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 border-l-2 border-red-400 bg-red-50 dark:bg-red-500/5 rounded-r-lg">
                @foreach($errors->all() as $error)
                    <p class="text-red-500 text-xs">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if(isset($editing))
            @php $pendingChange = \App\Models\ProfileChangeRequest::where('user_id', Auth::id())->where('status', 'pending')->first(); @endphp
            @if($pendingChange)
                <div class="mb-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl px-4 py-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-xs text-amber-700 dark:text-amber-400">Ya tienes cambios pendientes de aprobación (solicitados el {{ $pendingChange->created_at->format('d/m/Y') }}). Los nuevos cambios reemplazarán la solicitud anterior.</p>
                </div>
            @endif
        @endif

        <form action="{{ isset($editing) ? route('client.loans.profile.update') : route('client.loans.profile.store') }}"
            method="POST">
            @csrf
            @if(isset($editing)) @method('PUT') @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-0">

                <!-- Columna izquierda -->
                <div>
                    <p
                        class="text-xs text-gray-600 uppercase tracking-widest mb-4 pb-2 border-b border-gray-300 dark:border-gray-800">
                        Información académica</p>
                    <div class="space-y-4">

                        <div>
                            <label class="block text-xs text-gray-600 mb-2">Situación académica</label>
                            <div class="grid grid-cols-2 gap-2">
                                @php
                                    $mainOptions = ['Secundaria completa', 'Educación superior', 'Ninguno de los anteriores'];
                                    $currentStatus = old('academic_status', $profile->academic_status ?? '');

                                    // Determinar el valor principal si es educación superior
                                    $mainValue = $currentStatus;
                                    if (str_contains($currentStatus, 'Superior —')) {
                                        $mainValue = 'Educación superior';
                                    }
                                @endphp
                                @foreach($mainOptions as $opt)
                                    <label class="option-card">
                                        <input type="radio" name="main_academic_status" value="{{ $opt }}" {{ $mainValue === $opt ? 'checked' : '' }} required
                                            onchange="syncCards(this); handleMainStatusChange(this.value)">
                                        <div class="check-box">
                                            <svg width="10" height="10" viewBox="0 0 10 10" fill="none">
                                                <path d="M1.5 5L4 7.5L8.5 2.5" stroke="white" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </div>
                                        <span class="leading-tight">{{ $opt }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <input type="hidden" name="academic_status" id="academic_status_hidden" value="{{ $currentStatus }}">
                        </div>

                        <!-- Sub-opciones Educación Superior -->
                        <div id="education_superior_options" class="{{ $mainValue === 'Educación superior' ? '' : 'hidden' }} space-y-4 pl-3 border-l-2 border-gray-300 dark:border-gray-700">
                            <div>
                                <label class="block text-xs text-gray-600 mb-2">Tipo de educación</label>
                                <div class="flex gap-2">
                                    @php
                                        $isTecnico = str_contains($currentStatus, 'Técnico');
                                        $isUniversitario = str_contains($currentStatus, 'Universitario');
                                    @endphp
                                    <label class="option-card">
                                        <input type="radio" name="education_type" value="Técnico" {{ $isTecnico ? 'checked' : '' }}
                                            onchange="syncCards(this); handleEducationTypeChange(this.value)">
                                        <div class="check-box">
                                            <svg width="10" height="10" viewBox="0 0 10 10" fill="none">
                                                <path d="M1.5 5L4 7.5L8.5 2.5" stroke="white" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </div>
                                        <span>Técnico</span>
                                    </label>
                                    <label class="option-card">
                                        <input type="radio" name="education_type" value="Universitario" {{ $isUniversitario ? 'checked' : '' }}
                                            onchange="syncCards(this); handleEducationTypeChange(this.value)">
                                        <div class="check-box">
                                            <svg width="10" height="10" viewBox="0 0 10 10" fill="none">
                                                <path d="M1.5 5L4 7.5L8.5 2.5" stroke="white" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </div>
                                        <span>Universitario</span>
                                    </label>
                                </div>
                            </div>

                            <div id="education_level_container" class="{{ $isTecnico || $isUniversitario ? '' : 'hidden' }}">
                                <label class="block text-xs text-gray-600 mb-2">Estado</label>
                                <div id="level_options" class="grid grid-cols-2 gap-2">
                                    <!-- Se llena por JS -->
                                </div>
                            </div>
                        </div>

                        <div>
                            <label id="label_institution" class="block text-xs text-gray-600 mb-1">Universidad o instituto</label>
                            <input type="text" name="institution" id="input_institution"
                                value="{{ old('institution', $profile->institution ?? '') }}" required
                                placeholder="Ej: UNMSM"
                                oninput="this.value = this.value.replace(/[^a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s\.]/g, '')"
                                class="w-full bg-transparent border-b border-gray-400 dark:border-gray-700 text-gray-900 dark:text-white text-sm py-1.5 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors placeholder-gray-500 dark:placeholder-gray-100 @error('institution') border-red-500 @enderror disabled:opacity-30 disabled:cursor-not-allowed">
                            @error('institution') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Carrera profesional</label>
                            <input type="text" name="career" id="input_career"
                                value="{{ old('career', $profile->career ?? '') }}" required
                                placeholder="Ej: Ingeniería de Sistemas"
                                oninput="this.value = this.value.replace(/[^a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s\.]/g, '')"
                                class="w-full bg-transparent border-b border-gray-400 dark:border-gray-700 text-gray-900 dark:text-white text-sm py-1.5 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors placeholder-gray-500 dark:placeholder-gray-100 @error('career') border-red-500 @enderror disabled:opacity-30 disabled:cursor-not-allowed">
                            @error('career') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>

                    </div>
                </div>

                <!-- Columna derecha -->
                <div class="mt-8 md:mt-0">
                    <p
                        class="text-xs text-gray-600 uppercase tracking-widest mb-4 pb-2 border-b border-gray-300 dark:border-gray-800">
                        Situación social</p>
                    <div class="space-y-4">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-600 mb-2">¿Tiene hijos?</label>
                                <div class="flex gap-2">
                                    <label class="option-card">
                                        <input type="radio" name="has_children" value="1" {{ old('has_children', isset($profile) ? ($profile->has_children ? '1' : '0') : '') == '1' ? 'checked' : '' }} required onchange="syncCards(this); toggleChildrenCount(this.value)">
                                        <div class="check-box">
                                            <svg width="10" height="10" viewBox="0 0 10 10" fill="none">
                                                <path d="M1.5 5L4 7.5L8.5 2.5" stroke="white" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </div>
                                        <span>Sí</span>
                                    </label>
                                    <label class="option-card">
                                        <input type="radio" name="has_children" value="0" {{ old('has_children', isset($profile) ? ($profile->has_children ? '1' : '0') : '') == '0' ? 'checked' : '' }} onchange="syncCards(this); toggleChildrenCount(this.value)">
                                        <div class="check-box">
                                            <svg width="10" height="10" viewBox="0 0 10 10" fill="none">
                                                <path d="M1.5 5L4 7.5L8.5 2.5" stroke="white" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </div>
                                        <span>No</span>
                                    </label>
                                </div>
                            </div>
                            <div id="childrenCountField" class="{{ old('has_children', $profile->has_children ?? false) ? '' : 'hidden' }}">
                                <label class="block text-xs text-gray-600 mb-1">¿Cuántos hijos tiene?</label>
                                <input type="number" name="children_count" value="{{ old('children_count', $profile->children_count ?? '') }}"
                                    placeholder="0" min="1" max="20"
                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                    oninput="if(this.value && this.value < 1) this.value = 1; if(this.value > 20) this.value = 20;"
                                    class="w-full bg-transparent border-b border-gray-400 dark:border-gray-700 text-gray-900 dark:text-white text-sm py-1.5 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors placeholder-gray-500 dark:placeholder-gray-100">
                                @error('children_count') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs text-gray-600 mb-2">¿Trabaja actualmente?</label>
                            <div class="flex gap-2">
                                <label class="option-card">
                                    <input type="radio" name="is_employed" value="1" {{ old('is_employed', isset($profile) ? ($profile->is_employed ? '1' : '0') : '') == '1' ? 'checked' : '' }} required onchange="syncCards(this); toggleEmployment()">
                                    <div class="check-box">
                                        <svg width="10" height="10" viewBox="0 0 10 10" fill="none">
                                            <path d="M1.5 5L4 7.5L8.5 2.5" stroke="white" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                    <span>Sí</span>
                                </label>
                                <label class="option-card">
                                    <input type="radio" name="is_employed" value="0" {{ old('is_employed', isset($profile) ? ($profile->is_employed ? '1' : '0') : '') == '0' ? 'checked' : '' }} onchange="syncCards(this); toggleEmployment()">
                                    <div class="check-box">
                                        <svg width="10" height="10" viewBox="0 0 10 10" fill="none">
                                            <path d="M1.5 5L4 7.5L8.5 2.5" stroke="white" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                    <span>No</span>
                                </label>
                            </div>
                        </div>

                        <!-- Detalle Empleo -->
                        <div id="employmentDetailField" class="space-y-4 pl-3 border-l-2 border-gray-300 dark:border-gray-700">

                            <!-- Tipo de empleo (solo si trabaja) -->
                            <div id="employmentTypeField" class="{{ old('is_employed', $profile->is_employed ?? false) ? '' : 'hidden' }}">
                                <label class="block text-xs text-gray-600 mb-2">Tipo de trabajo</label>
                                <div class="flex gap-2">
                                    @foreach(['dependiente' => 'Dependiente', 'independiente' => 'Independiente'] as $val => $label)
                                        <label class="option-card">
                                            <input type="radio" name="employment_type" value="{{ $val }}" {{ old('employment_type', $profile->employment_type ?? '') === $val ? 'checked' : '' }}
                                                onchange="syncCards(this); toggleEmploymentSubFields(this.value)">
                                            <div class="check-box">
                                                <svg width="10" height="10" viewBox="0 0 10 10" fill="none">
                                                    <path d="M1.5 5L4 7.5L8.5 2.5" stroke="white" stroke-width="1.5"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </div>
                                            <span>{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('employment_type') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Campos Dependiente -->
                            <div id="dependiente_fields" class="{{ old('employment_type', $profile->employment_type ?? '') === 'dependiente' ? '' : 'hidden' }} space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Empresa / Lugar</label>
                                        <input type="text" name="workplace" value="{{ old('workplace', $profile->workplace ?? '') }}"
                                            placeholder="Nombre de empresa"
                                            oninput="this.value = this.value.replace(/[^a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s]/g, '')"
                                            class="w-full bg-transparent border-b border-gray-400 dark:border-gray-700 text-gray-900 dark:text-white text-sm py-1.5 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors placeholder-gray-500 dark:placeholder-gray-100">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Cargo</label>
                                        <input type="text" name="job_title" value="{{ old('job_title', $profile->job_title ?? '') }}"
                                            placeholder="Puesto que ocupa"
                                            oninput="this.value = this.value.replace(/[^a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s]/g, '')"
                                            class="w-full bg-transparent border-b border-gray-400 dark:border-gray-700 text-gray-900 dark:text-white text-sm py-1.5 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors placeholder-gray-500 dark:placeholder-gray-100">
                                    </div>
                                </div>
                            </div>

                            <!-- Campos Independiente -->
                            <div id="independiente_fields" class="{{ old('employment_type', $profile->employment_type ?? '') === 'independiente' ? '' : 'hidden' }}">
                                <label class="block text-xs text-gray-600 mb-2">Usted es:</label>
                                <div class="grid grid-cols-3 gap-2">
                                    @foreach(['comerciante' => 'Comerciante', 'empresario' => 'Empresario', 'emprendedor' => 'Emprendedor'] as $val => $label)
                                        <label class="option-card">
                                            <input type="radio" name="independent_type" value="{{ $val }}" {{ old('independent_type', $profile->independent_type ?? '') === $val ? 'checked' : '' }}
                                                onchange="syncCards(this)">
                                            <div class="check-box">
                                                <svg width="10" height="10" viewBox="0 0 10 10" fill="none">
                                                    <path d="M1.5 5L4 7.5L8.5 2.5" stroke="white" stroke-width="1.5"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </div>
                                            <span class="leading-tight text-[10px]">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Ingreso Mensual (Siempre visible si se llega aquí, sea trabajando o no) -->
                            <div id="incomeField">
                                <label class="block text-xs text-gray-600 mb-1">Ingreso mensual aproximado (S/)</label>
                                <input type="hidden" name="monthly_income" value="{{ old('monthly_income', isset($profile) ? $profile->monthly_income : '') }}">
                                <input type="text" id="monthly_income_display" value="{{ old('monthly_income', isset($profile) ? number_format($profile->monthly_income, 2, '.', ',') : '') }}"
                                    placeholder="100.00" step="0.01" min="100"
                                    oninput="formatCurrency(this)"
                                    class="w-full bg-transparent border-b border-gray-400 dark:border-gray-700 text-gray-900 dark:text-white text-sm py-1.5 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors placeholder-gray-500 dark:placeholder-gray-100">
                                @error('monthly_income') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Instagram <span
                                    class="text-gray-500 dark:text-gray-100">(opcional)</span></label>
                            <input type="text" name="instagram" value="{{ old('instagram', $profile->instagram ?? '') }}"
                                placeholder="@usuario"
                                class="w-full bg-transparent border-b border-gray-400 dark:border-gray-700 text-gray-900 dark:text-white text-sm py-1.5 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors placeholder-gray-500 dark:placeholder-gray-100 @error('instagram') border-red-500 @enderror">
                            @error('instagram') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs text-gray-600 mb-2">Estado civil</label>
                            <div class="space-y-1.5">
                                @foreach(['soltero' => 'Soltero/a', 'casado_separado' => 'Casado/a — bienes separados', 'casado_compartido' => 'Casado/a — bienes compartidos'] as $val => $label)
                                    <label class="option-card w-full">
                                        <input type="radio" name="marital_status" value="{{ $val }}" {{ old('marital_status', $profile->marital_status ?? '') === $val ? 'checked' : '' }} required
                                            onchange="syncCards(this); toggleSpouse()">
                                        <div class="check-box">
                                            <svg width="10" height="10" viewBox="0 0 10 10" fill="none">
                                                <path d="M1.5 5L4 7.5L8.5 2.5" stroke="white" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </div>
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Campos cónyuge -->
                        <div id="spouseFields"
                            class="{{ old('marital_status', $profile->marital_status ?? '') === 'casado_compartido' ? '' : 'hidden' }} space-y-4 pl-3 border-l-2 border-gray-300 dark:border-gray-700">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Nombres del cónyuge</label>
                                    <input type="text" name="spouse_first_name"
                                        value="{{ old('spouse_first_name', $profile->spouse_first_name ?? '') }}"
                                        placeholder="Nombres"
                                        oninput="this.value = this.value.replace(/[^a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s]/g, '')"
                                        class="w-full bg-transparent border-b border-gray-400 dark:border-gray-700 text-gray-900 dark:text-white text-xs py-1.5 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors placeholder-gray-500 dark:placeholder-gray-100 @error('spouse_first_name') border-red-500 @enderror">
                                    @error('spouse_first_name') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Apellidos del cónyuge</label>
                                    <input type="text" name="spouse_last_name"
                                        value="{{ old('spouse_last_name', $profile->spouse_last_name ?? '') }}"
                                        placeholder="Apellidos"
                                        oninput="this.value = this.value.replace(/[^a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s]/g, '')"
                                        class="w-full bg-transparent border-b border-gray-400 dark:border-gray-700 text-gray-900 dark:text-white text-xs py-1.5 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors placeholder-gray-500 dark:placeholder-gray-100 @error('spouse_last_name') border-red-500 @enderror">
                                    @error('spouse_last_name') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">DNI del cónyuge</label>
                                <input type="text" name="spouse_dni"
                                    value="{{ old('spouse_dni', $profile->spouse_dni ?? '') }}" placeholder="12345678"
                                    maxlength="8" oninput="this.value=this.value.replace(/\D/g,'')"
                                    class="w-full bg-transparent border-b border-gray-400 dark:border-gray-700 text-gray-900 dark:text-white text-xs py-1.5 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors placeholder-gray-500 dark:placeholder-gray-100 @error('spouse_dni') border-red-500 @enderror">
                                @error('spouse_dni') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" id="profileSubmitBtn"
                    class="text-sm font-medium border-b pb-0.5 bg-transparent transition-colors disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:text-gray-900 dark:disabled:hover:text-white disabled:hover:border-gray-900 dark:disabled:hover:border-white text-gray-900 dark:text-white border-gray-900 dark:border-white hover:text-gray-500 dark:hover:text-gray-400 hover:border-gray-500 dark:hover:border-gray-400">
                    {{ isset($editing) ? 'Enviar solicitud de cambio →' : 'Guardar perfil →' }}
                </button>
            </div>

        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inicializar estado de las tarjetas
            document.querySelectorAll('input[type="radio"]:checked').forEach(input => {
                syncCards(input);
            });

            // Inicializar lógica de campos según el estado actual
            const mainStatusInput = document.querySelector('input[name="main_academic_status"]:checked');
            if (mainStatusInput) {
                handleMainStatusChange(mainStatusInput.value, true);

                if (mainStatusInput.value === 'Educación superior') {
                    const typeInput = document.querySelector('input[name="education_type"]:checked');
                    if (typeInput) {
                        handleEducationTypeChange(typeInput.value, true);
                    }
                }
            }
        });

        function syncCards(input) {
            if (!input.name) return;
            const name = input.name;
            document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
                const card = r.closest('.option-card');
                if (card) card.classList.toggle('selected', r.checked);
            });
        }

        function handleMainStatusChange(val, isInit = false) {
            const supOptions = document.getElementById('education_superior_options');
            const instLabel = document.getElementById('label_institution');
            const instInput = document.getElementById('input_institution');
            const careerInput = document.getElementById('input_career');
            const hiddenStatus = document.getElementById('academic_status_hidden');

            if (val === 'Educación superior') {
                supOptions.classList.remove('hidden');
                instLabel.textContent = 'Universidad o instituto';
                careerInput.disabled = false;
                careerInput.required = true;
                instInput.disabled = false;
                instInput.required = true;
            } else if (val === 'Secundaria completa') {
                supOptions.classList.add('hidden');
                instLabel.textContent = 'Centro Educativo';
                careerInput.disabled = true;
                careerInput.required = false;
                if (!isInit) careerInput.value = '';
                instInput.disabled = false;
                instInput.required = true;
                hiddenStatus.value = val;
            } else {
                // Ninguno de los anteriores
                supOptions.classList.add('hidden');
                instLabel.textContent = 'Universidad o instituto';
                careerInput.disabled = true;
                careerInput.required = false;
                if (!isInit) careerInput.value = '';
                instInput.disabled = true;
                instInput.required = false;
                if (!isInit) instInput.value = '';
                hiddenStatus.value = val;
            }

            // Limpiar sub-opciones si no es educación superior y no es inicialización
            if (val !== 'Educación superior' && !isInit) {
                document.querySelectorAll('input[name="education_type"]').forEach(r => r.checked = false);
                document.getElementById('education_level_container').classList.add('hidden');
                document.getElementById('level_options').innerHTML = '';
                syncCards({name: 'education_type'});
            }
        }

        function handleEducationTypeChange(type, isInit = false) {
            const container = document.getElementById('education_level_container');
            const optionsDiv = document.getElementById('level_options');
            container.classList.remove('hidden');
            optionsDiv.innerHTML = '';

            let levels = [];
            if (type === 'Universitario') {
                levels = ['Estudiante activo', 'Egresado', 'Bachiller'];
            } else if (type === 'Técnico') {
                levels = ['Egresado', 'Titulado'];
            }

            const currentStatus = document.getElementById('academic_status_hidden').value;

            levels.forEach(lvl => {
                const label = document.createElement('label');
                label.className = 'option-card';

                const fullVal = `Superior — ${type} (${lvl})`;
                const isChecked = currentStatus === fullVal;

                label.innerHTML = `
                    <input type="radio" name="education_level" value="${lvl}" ${isChecked ? 'checked' : ''} required
                        onchange="syncCards(this); updateAcademicStatusHidden('${type}', this.value)">
                    <div class="check-box">
                        <svg width="10" height="10" viewBox="0 0 10 10" fill="none">
                            <path d="M1.5 5L4 7.5L8.5 2.5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <span class="leading-tight">${lvl}</span>
                `;
                optionsDiv.appendChild(label);
                if (isChecked) syncCards(label.querySelector('input'));
            });
        }

        function updateAcademicStatusHidden(type, level) {
            document.getElementById('academic_status_hidden').value = `Superior — ${type} (${level})`;
        }

        function toggleSpouse() {
            const val = document.querySelector('input[name="marital_status"]:checked')?.value;
            const fields = document.getElementById('spouseFields');
            if (val === 'casado_compartido') {
                fields.classList.remove('hidden');
            } else {
                fields.classList.add('hidden');
                document.querySelector('input[name="spouse_first_name"]').value = '';
                document.querySelector('input[name="spouse_last_name"]').value = '';
                document.querySelector('input[name="spouse_dni"]').value = '';
            }
        }

        function toggleChildrenCount(val) {
            const field = document.getElementById('childrenCountField');
            if (val == '1') {
                field.classList.remove('hidden');
            } else {
                field.classList.add('hidden');
                field.querySelector('input').value = '';
            }
        }

        function toggleEmployment() {
            const val = document.querySelector('input[name="is_employed"]:checked')?.value;
            const typeField = document.getElementById('employmentTypeField');
            const detailField = document.getElementById('employmentDetailField');

            if (val == '1') {
                typeField.classList.remove('hidden');
            } else {
                typeField.classList.add('hidden');
                // Limpiar sub-campos
                document.querySelectorAll('input[name="employment_type"]').forEach(r => {
                    r.checked = false;
                    r.closest('.option-card').classList.remove('selected');
                });
                toggleEmploymentSubFields(null);
            }
        }

        function toggleEmploymentSubFields(val) {
            const dep = document.getElementById('dependiente_fields');
            const indep = document.getElementById('independiente_fields');

            if (val === 'dependiente') {
                dep.classList.remove('hidden');
                indep.classList.add('hidden');
                // Limpiar indep
                document.querySelectorAll('input[name="independent_type"]').forEach(r => {
                    r.checked = false;
                    r.closest('.option-card').classList.remove('selected');
                });
            } else if (val === 'independiente') {
                dep.classList.add('hidden');
                indep.classList.remove('hidden');
                // Limpiar dep
                dep.querySelectorAll('input').forEach(i => i.value = '');
            } else {
                dep.classList.add('hidden');
                indep.classList.add('hidden');
            }
        }

        function formatCurrency(input) {
            // Remover todo excepto dígitos
            let value = input.value.replace(/[^\d]/g, '');

            if (value === '') {
                input.value = '';
                // Actualizar campo oculto
                document.querySelector('input[name="monthly_income"]').value = '';
                return;
            }

            // Convertir a número y formatear
            if (value.length <= 2) {
                input.value = '0.' + value.padStart(2, '0');
            } else {
                let integerPart = value.slice(0, -2);
                let decimalPart = value.slice(-2);

                // Remover ceros a la izquierda del entero
                integerPart = integerPart.replace(/^0+/, '') || '0';

                // Agregar separadores de miles
                integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                input.value = integerPart + '.' + decimalPart;
            }

            // Actualizar campo oculto con valor sin formato
            let numericValue = value.slice(0, -2) + '.' + value.slice(-2);
            document.querySelector('input[name="monthly_income"]').value = parseFloat(numericValue);
        }

        // Formatear valor inicial al cargar la página
        document.addEventListener('DOMContentLoaded', function () {
            const incomeInput = document.querySelector('input[name="monthly_income"]');
            if (incomeInput && incomeInput.value) {
                formatCurrency(incomeInput);
            }
        });

        @if(isset($editing))
        (function () {
            var trackedFields = [
                'academic_status', 'institution', 'career',
                'has_children', 'children_count',
                'is_employed', 'employment_type', 'workplace', 'job_title',
                'independent_type', 'monthly_income',
                'instagram', 'marital_status',
                'spouse_first_name', 'spouse_last_name', 'spouse_dni'
            ];

            function getSnapshot() {
                var data = {};
                trackedFields.forEach(function (name) {
                    var el = document.querySelector('[name="' + name + '"]');
                    if (!el) return;
                    if (el.type === 'radio') {
                        var checked = document.querySelector('[name="' + name + '"]:checked');
                        data[name] = checked ? checked.value : '';
                    } else if (el.type === 'checkbox') {
                        data[name] = el.checked ? '1' : '0';
                    } else {
                        data[name] = el.value;
                    }
                });
                return JSON.stringify(data);
            }

            function init() {
                var btn = document.getElementById('profileSubmitBtn');
                if (!btn) return;
                var initial = getSnapshot();

                function check() {
                    btn.disabled = (getSnapshot() === initial);
                }

                check();

                document.addEventListener('input', check);
                document.addEventListener('change', check);
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function () { setTimeout(init, 100); });
            } else {
                setTimeout(init, 100);
            }
        })();
        @endif
    </script>
@endsection
