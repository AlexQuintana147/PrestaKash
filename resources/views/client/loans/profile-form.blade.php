@extends('client.layout')

@section('title', 'Completar perfil')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-10 max-w-xl">

    <div class="mb-10">
        <p class="text-xs text-gray-400 dark:text-gray-400 uppercase tracking-widest mb-1">Préstamos</p>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Completa tu perfil</h1>
        <p class="text-sm text-gray-400 dark:text-gray-400 mt-1">Necesitamos algunos datos adicionalestinuar.</p>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 border-l-2 border-red-400 bg-red-50 dark:bg-red-500/5 rounded-r-lg">
        @foreach($errors->all() as $error)
            <p class="text-red-500 text-sm">{{ $error }}</p>
        @endforeach
    </div>
    @endif

    <form action="{{ route('client.loans.profile.store') }}" method="POST" class="space-y-10">
        @csrf

        <!-- Información académica -->
        <div>
            <p class="text-xs text-gray-400 dark:text-gray-400 uppercase tracking-widest mb-5">Información académica</p>
            <div class="space-y-5">

                <div>
                    <label class="block text-xs text-gray-400 dark:text-gray-400 mb-1.5">Universidad o instituto</label>
                    <input type="text" name="institution" value="{{ old('institution') }}" required placeholder="Ej: Universidad Nacional Mayor de San Marcos"
                           class="w-full bg-transparent border-b border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm py-2 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors placeholder-gray-300 dark:placeholder-gray-600">
                </div>

                <div>
                    <label class="block text-xs text-gray-400 dark:text-gray-400 mb-1.5">Carrera profesional</label>
                    <input type="text" name="career" value="{{ old('career') }}" required placeholder="Ej: Ingeniería de Sistemas"
                           class="w-full bg-transparent border-b border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm py-2 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors placeholder-gray-300 dark:placeholder-gray-600">
                </div>

                <div>
                    <label class="block text-xs text-gray-400 dark:text-gray-400 mb-3">Situación académica</label>
                    <div class="space-y-2">
                        @foreach(['Estudiante activo', 'Egresado', 'Titulado', 'Bachiller'] as $opt)
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio" name="academic_status" value="{{ $opt }}" {{ old('academic_status') === $opt ? 'checked' : '' }} required class="accent-gray-900 dark:accent-white">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $opt }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

        <div class="border-t border-gray-100 dark:border-gray-800"></div>

        <!-- Situación social -->
        <div>
            <p class="text-xs text-gray-400 dark:text-gray-400 uppercase tracking-widest mb-5">Situación social</p>
            <div class="space-y-6">

                <div>
                    <label class="block text-xs text-gray-400 dark:text-gray-400 mb-3">¿Tiene hijos?</label>
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="has_children" value="1" {{ old('has_children') == '1' ? 'checked' : '' }} required class="accent-gray-900 dark:accent-white">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Sí</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="has_children" value="0" {{ old('has_children') == '0' ? 'checked' : '' }} class="accent-gray-900 dark:accent-white">
                            <span class="text-sm text-gray-700 dark:text-gray-300">No</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs text-gray-400 dark:text-gray-400 mb-1.5">Instagram <span class="text-gray-300 dark:text-gray-600">(opcional)</span></label>
                    <input type="text" name="instagram" value="{{ old('instagram') }}" placeholder="@usuario"
                           class="w-full bg-transparent border-b border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm py-2 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors placeholder-gray-300 dark:placeholder-gray-600">
                </div>

                <div>
                    <label class="block text-xs text-gray-400 dark:text-gray-400 mb-3">¿Trabaja actualmente?</label>
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_employed" value="1" {{ old('is_employed') == '1' ? 'checked' : '' }} required class="accent-gray-900 dark:accent-white">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Sí</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_employed" value="0" {{ old('is_employed') == '0' ? 'checked' : '' }} class="accent-gray-900 dark:accent-white">
                            <span class="text-sm text-gray-700 dark:text-gray-300">No</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs text-gray-400 dark:text-gray-400 mb-3">Estado civil</label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio" name="marital_status" value="soltero" {{ old('marital_status') === 'soltero' ? 'checked' : '' }} required class="accent-gray-900 dark:accent-white" onchange="toggleSpouse()">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Soltero/a</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio" name="marital_status" value="casado_separado" {{ old('marital_status') === 'casado_separado' ? 'checked' : '' }} class="accent-gray-900 dark:accent-white" onchange="toggleSpouse()">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Casado/a — bienes separados</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio" name="marital_status" value="casado_compartido" {{ old('marital_status') === 'casado_compartido' ? 'checked' : '' }} class="accent-gray-900 dark:accent-white" onchange="toggleSpouse()">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Casado/a — bienes compartidos</span>
                        </label>
                    </div>
                </div>

                <!-- Campos cónyuge (solo bienes compartidos) -->
                <div id="spouseFields" class="{{ old('marital_status') === 'casado_compartido' ? '' : 'hidden' }} space-y-5 pl-4 border-l-2 border-gray-200 dark:border-gray-700">
                    <div>
                        <label class="block text-xs text-gray-400 dark:text-gray-400 mb-1.5">Nombre completo del cónyuge</label>
                        <input type="text" name="spouse_name" value="{{ old('spouse_name') }}" placeholder="Nombres y apellidos"
                               class="w-full bg-transparent border-b border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm py-2 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors placeholder-gray-300 dark:placeholder-gray-600">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 dark:text-gray-400 mb-1.5">DNI del cónyuge</label>
                        <input type="text" name="spouse_dni" value="{{ old('spouse_dni') }}" placeholder="12345678" maxlength="8"
                               oninput="this.value=this.value.replace(/\D/g,'')"
                               class="w-full bg-transparent border-b border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm py-2 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors placeholder-gray-300 dark:placeholder-gray-600">
                    </div>
                </div>

            </div>
        </div>

        <button type="submit"
                class="w-full py-3 text-sm font-semibold rounded-xl bg-gray-900 dark:bg-white text-white dark:text-black hover:bg-gray-700 dark:hover:bg-gray-100 transition-colors">
            Guardar y continuar
        </button>

    </form>
</div>

<script>
function toggleSpouse() {
    const val = document.querySelector('input[name="marital_status"]:checked')?.value;
    const fields = document.getElementById('spouseFields');
    if (val === 'casado_compartido') {
        fields.classList.remove('hidden');
    } else {
        fields.classList.add('hidden');
        document.querySelector('input[name="spouse_name"]').value = '';
        document.querySelector('input[name="spouse_dni"]').value = '';
    }
}
</script>
@endsection

antes
