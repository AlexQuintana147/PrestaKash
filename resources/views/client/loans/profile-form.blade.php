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
.option-card input[type=radio] { display: none; }

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
.option-card input[type=radio]:checked ~ .check-box,
.option-card.selected .check-box {
    background: #111827;
    border-color: #111827;
}
.dark .option-card input[type=radio]:checked ~ .check-box,
.dark .option-card.selected .check-box {
    background: #ffffff;
    border-color: #ffffff;
}
.dark .option-card input[type=radio]:checked ~ .check-box svg path,
.dark .option-card.selected .check-box svg path {
    stroke: #111827;
}
.option-card input[type=radio]:checked ~ .check-box svg,
.option-card.selected .check-box svg {
    display: block;
}
.check-box svg { display: none; }

.option-card input[type=radio]:checked ~ span {
    color: #111827;
    font-weight: 500;
}
.dark .option-card input[type=radio]:checked ~ span {
    color: #ffffff;
}
.option-card.selected {
    border-color: #111827;
}
.dark .option-card.selected {
    border-color: #ffffff;
}
</style>

<div class="min-h-screen px-5 md:px-8 py-6">

    <div class="mb-6">
        <p class="text-xs text-gray-400 uppercase tracking-widest mb-0.5">Préstamos</p>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ isset($editing) ? 'Editar perfil' : 'Completa tu perfil' }}</h1>
        <p class="text-xs text-gray-500 mt-0.5">Necesitamos algunos datos antes de continuar.</p>
    </div>

    @if($errors->any())
    <div class="mb-4 p-3 border-l-2 border-red-400 bg-red-50 dark:bg-red-500/5 rounded-r-lg">
        @foreach($errors->all() as $error)
            <p class="text-red-500 text-xs">{{ $error }}</p>
        @endforeach
    </div>
    @endif

    <form action="{{ isset($editing) ? route('client.loans.profile.update') : route('client.loans.profile.store') }}" method="POST">
        @csrf
        @if(isset($editing)) @method('PUT') @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-0">

            <!-- Columna izquierda -->
            <div>
                <p class="text-xs text-gray-600 uppercase tracking-widest mb-4 pb-2 border-b border-gray-300 dark:border-gray-800">Información académica</p>
                <div class="space-y-4">

                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Universidad o instituto</label>
                        <input type="text" name="institution" value="{{ old('institution', $profile->institution ?? '') }}" required placeholder="Ej: UNMSM"
                               class="w-full bg-transparent border-b border-gray-400 dark:border-gray-700 text-gray-900 dark:text-white text-sm py-1.5 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors placeholder-gray-400 dark:placeholder-gray-600">
                    </div>

                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Carrera profesional</label>
                        <input type="text" name="career" value="{{ old('career', $profile->career ?? '') }}" required placeholder="Ej: Ingeniería de Sistemas"
                               class="w-full bg-transparent border-b border-gray-400 dark:border-gray-700 text-gray-900 dark:text-white text-sm py-1.5 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors placeholder-gray-400 dark:placeholder-gray-600">
                    </div>

                    <div>
                        <label class="block text-xs text-gray-600 mb-2">Situación académica</label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach(['Estudiante activo', 'Egresado', 'Titulado', 'Bachiller', 'Secundaria completa', 'Ninguno de los anteriores'] as $opt)
                            <label class="option-card {{ old('academic_status', $profile->academic_status ?? '') === $opt ? 'selected' : '' }}">
                                <input type="radio" name="academic_status" value="{{ $opt }}" {{ old('academic_status', $profile->academic_status ?? '') === $opt ? 'checked' : '' }} required onchange="syncCards(this)">
                                <div class="check-box">
                                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none">
                                        <path d="M1.5 5L4 7.5L8.5 2.5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <span class="leading-tight">{{ $opt }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>

            <!-- Columna derecha -->
            <div class="mt-8 md:mt-0">
                <p class="text-xs text-gray-600 uppercase tracking-widest mb-4 pb-2 border-b border-gray-300 dark:border-gray-800">Situación social</p>
                <div class="space-y-4">

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-600 mb-2">¿Tiene hijos?</label>
                            <div class="flex gap-2">
                                <label class="option-card {{ old('has_children') == '1' ? 'selected' : '' }}">
                                    <input type="radio" name="has_children" value="1" {{ old('has_children') == '1' ? 'checked' : '' }} required onchange="syncCards(this)">
                                    <div class="check-box">
                                        <svg width="10" height="10" viewBox="0 0 10 10" fill="none"><path d="M1.5 5L4 7.5L8.5 2.5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </div>
                                    <span>Sí</span>
                                </label>
                                <label class="option-card {{ old('has_children') == '0' ? 'selected' : '' }}">
                                    <input type="radio" name="has_children" value="0" {{ old('has_children') == '0' ? 'checked' : '' }} onchange="syncCards(this)">
                                    <div class="check-box">
                                        <svg width="10" height="10" viewBox="0 0 10 10" fill="none"><path d="M1.5 5L4 7.5L8.5 2.5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </div>
                                    <span>No</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-2">¿Trabaja actualmente?</label>
                            <div class="flex gap-2">
                                <label class="option-card {{ old('is_employed', $profile->is_employed ?? '') == '1' ? 'selected' : '' }}">
                                    <input type="radio" name="is_employed" value="1" {{ old('is_employed', $profile->is_employed ?? '') == '1' ? 'checked' : '' }} required onchange="syncCards(this); toggleEmployment()">
                                    <div class="check-box">
                                        <svg width="10" height="10" viewBox="0 0 10 10" fill="none"><path d="M1.5 5L4 7.5L8.5 2.5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </div>
                                    <span>Sí</span>
                                </label>
                                <label class="option-card {{ old('is_employed', $profile->is_employed ?? '') == '0' ? 'selected' : '' }}">
                                    <input type="radio" name="is_employed" value="0" {{ old('is_employed', $profile->is_employed ?? '') == '0' ? 'checked' : '' }} onchange="syncCards(this); toggleEmployment()">
                                    <div class="check-box">
                                        <svg width="10" height="10" viewBox="0 0 10 10" fill="none"><path d="M1.5 5L4 7.5L8.5 2.5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </div>
                                    <span>No</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Tipo de empleo -->
                    <div id="employmentTypeField" class="{{ old('is_employed', $profile->is_employed ?? '') == '1' ? '' : 'hidden' }} pl-3 border-l-2 border-gray-300 dark:border-gray-700">
                        <label class="block text-xs text-gray-600 mb-2">Tipo de trabajo</label>
                        <div class="flex gap-2">
                            @foreach(['dependiente' => 'Dependiente', 'independiente' => 'Independiente'] as $val => $label)
                            <label class="option-card {{ old('employment_type', $profile->employment_type ?? '') === $val ? 'selected' : '' }}">
                                <input type="radio" name="employment_type" value="{{ $val }}" {{ old('employment_type', $profile->employment_type ?? '') === $val ? 'checked' : '' }} onchange="syncCards(this)">
                                <div class="check-box">
                                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none"><path d="M1.5 5L4 7.5L8.5 2.5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                                <span>{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Instagram <span class="text-gray-300 dark:text-gray-600">(opcional)</span></label>
                        <input type="text" name="instagram" value="{{ old('instagram', $profile->instagram ?? '') }}" placeholder="@usuario"
                               class="w-full bg-transparent border-b border-gray-400 dark:border-gray-700 text-gray-900 dark:text-white text-sm py-1.5 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors placeholder-gray-400 dark:placeholder-gray-600">
                    </div>

                    <div>
                        <label class="block text-xs text-gray-600 mb-2">Estado civil</label>
                        <div class="space-y-1.5">
                            @foreach(['soltero' => 'Soltero/a', 'casado_separado' => 'Casado/a — bienes separados', 'casado_compartido' => 'Casado/a — bienes compartidos'] as $val => $label)
                            <label class="option-card w-full {{ old('marital_status') === $val ? 'selected' : '' }}">
                                <input type="radio" name="marital_status" value="{{ $val }}" {{ old('marital_status') === $val ? 'checked' : '' }} required onchange="syncCards(this); toggleSpouse()">
                                <div class="check-box">
                                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none"><path d="M1.5 5L4 7.5L8.5 2.5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                                <span>{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Campos cónyuge -->
                    <div id="spouseFields" class="{{ old('marital_status', $profile->marital_status ?? '') === 'casado_compartido' ? '' : 'hidden' }} space-y-4 pl-3 border-l-2 border-gray-300 dark:border-gray-700">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Nombres del cónyuge</label>
                                <input type="text" name="spouse_first_name" value="{{ old('spouse_first_name', $profile->spouse_first_name ?? '') }}" placeholder="Nombres"
                                       class="w-full bg-transparent border-b border-gray-400 dark:border-gray-700 text-gray-900 dark:text-white text-xs py-1.5 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors placeholder-gray-400 dark:placeholder-gray-600">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Apellidos del cónyuge</label>
                                <input type="text" name="spouse_last_name" value="{{ old('spouse_last_name', $profile->spouse_last_name ?? '') }}" placeholder="Apellidos"
                                       class="w-full bg-transparent border-b border-gray-400 dark:border-gray-700 text-gray-900 dark:text-white text-xs py-1.5 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors placeholder-gray-400 dark:placeholder-gray-600">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">DNI del cónyuge</label>
                            <input type="text" name="spouse_dni" value="{{ old('spouse_dni', $profile->spouse_dni ?? '') }}" placeholder="12345678" maxlength="8"
                                   oninput="this.value=this.value.replace(/\D/g,'')"
                                   class="w-full bg-transparent border-b border-gray-400 dark:border-gray-700 text-gray-900 dark:text-white text-xs py-1.5 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors placeholder-gray-400 dark:placeholder-gray-600">
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit"
                    class="text-sm font-medium text-gray-900 dark:text-white border-b border-gray-900 dark:border-white hover:text-gray-500 dark:hover:text-gray-400 hover:border-gray-500 dark:hover:border-gray-400 transition-colors pb-0.5 bg-transparent">
                Guardar y continuar →
            </button>
        </div>

    </form>
</div>

<script>
function syncCards(input) {
    const name = input.name;
    document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
        r.closest('.option-card').classList.toggle('selected', r.checked);
    });
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

function toggleEmployment() {
    const val = document.querySelector('input[name="is_employed"]:checked')?.value;
    const field = document.getElementById('employmentTypeField');
    if (val == '1') {
        field.classList.remove('hidden');
    } else {
        field.classList.add('hidden');
        document.querySelectorAll('input[name="employment_type"]').forEach(r => {
            r.checked = false;
            r.closest('.option-card').classList.remove('selected');
        });
    }
}
</script>
@endsection
