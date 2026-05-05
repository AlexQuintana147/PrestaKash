<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PrestaKash — Completa tu perfil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Roboto', sans-serif; } </style>
</head>
<body class="bg-gray-950 min-h-screen flex items-center justify-center px-4 py-10">
<div class="w-full max-w-lg">

    <!-- Logo -->
    <div class="text-center mb-6">
        <span class="text-3xl font-extrabold text-white">PrestaKash</span>
        <p class="text-gray-400 text-sm mt-1">Completa tu perfil para continuar</p>
    </div>

    <!-- Pasos -->
    <div class="flex items-center justify-center gap-3 mb-8">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white text-sm font-bold">1</div>
            <span class="text-white text-sm font-medium">Datos personales</span>
        </div>
        <div class="w-8 h-px bg-gray-700"></div>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center text-gray-400 text-sm font-bold">2</div>
            <span class="text-gray-500 text-sm">Documentos</span>
        </div>
    </div>

    <!-- Card -->
    <div class="bg-gray-900 rounded-2xl p-8 shadow-2xl">
        <h2 class="text-xl font-bold text-white mb-1">Datos personales</h2>
        <p class="text-gray-400 text-sm mb-6">Tu correo viene de Google. Completa los datos restantes.</p>

        @if(session('error'))
            <div class="bg-red-500/10 border border-red-500/30 rounded-lg p-3 mb-5">
                <p class="text-red-400 text-sm">{{ session('error') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/30 rounded-lg p-3 mb-5">
                @foreach($errors->all() as $error)
                    <p class="text-red-400 text-sm">• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('onboarding.step1.store') }}" method="POST" class="space-y-4">
        @csrf

        <!-- Correo (solo lectura) -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Correo electrónico</label>
            <input type="email" value="{{ Auth::user()->email }}" disabled
                class="w-full bg-gray-800 border border-gray-700 text-gray-400 rounded-lg px-4 py-2.5 text-sm cursor-not-allowed">
        </div>

        <!-- Nombres y Apellidos -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Nombres</label>
                <input type="text" name="first_name" value="{{ old('first_name') }}" required
                    placeholder="Ej: Juan Carlos"
                    oninput="this.value=this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g,'')"
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-green-500 transition-colors">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Apellidos</label>
                <input type="text" name="last_name" value="{{ old('last_name') }}" required
                    placeholder="Ej: Pérez García"
                    oninput="this.value=this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g,'')"
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-green-500 transition-colors">
            </div>
        </div>

        <!-- DNI y Teléfono -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Nro. Documento (DNI)</label>
                <input type="text" name="dni" value="{{ old('dni') }}" required
                    placeholder="12345678" maxlength="8"
                    oninput="this.value=this.value.replace(/\D/g,'')"
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-green-500 transition-colors">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Número de teléfono</label>
                <input type="text" name="phone" value="{{ old('phone') }}" required
                    placeholder="987654321" maxlength="9"
                    oninput="this.value=this.value.replace(/\D/g,'')"
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-green-500 transition-colors">
            </div>
        </div>

        <!-- Departamento -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Departamento</label>
            <select name="department" id="department" required
                class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-green-500 transition-colors">
                <option value="">Selecciona un departamento</option>
            </select>
        </div>

        <!-- Provincia -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Provincia</label>
            <select name="province" id="province" required disabled
                class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-green-500 transition-colors disabled:opacity-50">
                <option value="">Selecciona una provincia</option>
            </select>
        </div>

        <!-- Distrito -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Distrito</label>
            <select name="district" id="district" required disabled
                class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-green-500 transition-colors disabled:opacity-50">
                <option value="">Selecciona un distrito</option>
            </select>
        </div>

        <!-- Dirección -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Dirección</label>
            <input type="text" name="address" value="{{ old('address') }}" required
                placeholder="Ej: Av. Arequipa 1234, Dpto 5B"
                class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-green-500 transition-colors">
        </div>

        <button type="submit"
            class="w-full bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-400 hover:to-emerald-400 text-white font-bold py-3 rounded-xl transition-all text-sm mt-2">
            Siguiente →
        </button>

        </form>
    </div>
</div>

<script>
const deptSelect = document.getElementById('department');
const provSelect = document.getElementById('province');
const distSelect = document.getElementById('district');

// Cargar departamentos al iniciar
fetch('/ubigeo/departments')
    .then(r => r.json())
    .then(data => {
        data.forEach(d => {
            deptSelect.innerHTML += `<option value="${d.id}">${d.name}</option>`;
        });
        // Restaurar valor si hubo error de validación
        @if(old('department'))
            deptSelect.value = '{{ old('department') }}';
            deptSelect.dispatchEvent(new Event('change'));
        @endif
    });

// Al cambiar departamento, cargar provincias
deptSelect.addEventListener('change', function () {
    provSelect.innerHTML = '<option value="">Selecciona una provincia</option>';
    distSelect.innerHTML = '<option value="">Selecciona un distrito</option>';
    provSelect.disabled = true;
    distSelect.disabled = true;

    if (!this.value) return;

    fetch(`/ubigeo/provinces/${this.value}`)
        .then(r => r.json())
        .then(data => {
            data.forEach(p => {
                provSelect.innerHTML += `<option value="${p.id}">${p.name}</option>`;
            });
            provSelect.disabled = false;
            @if(old('province'))
                provSelect.value = '{{ old('province') }}';
                provSelect.dispatchEvent(new Event('change'));
            @endif
        });
});

// Al cambiar provincia, cargar distritos
provSelect.addEventListener('change', function () {
    distSelect.innerHTML = '<option value="">Selecciona un distrito</option>';
    distSelect.disabled = true;

    if (!this.value) return;

    fetch(`/ubigeo/districts/${this.value}`)
        .then(r => r.json())
        .then(data => {
            data.forEach(d => {
                distSelect.innerHTML += `<option value="${d.id}">${d.name}</option>`;
            });
            distSelect.disabled = false;
            @if(old('district'))
                distSelect.value = '{{ old('district') }}';
            @endif
        });
});
</script>
</body>
</html>
