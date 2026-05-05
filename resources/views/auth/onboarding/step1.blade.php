<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PrestaKash — Completa tu perfil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; }
        .input-field {
            width: 100%;
            background: transparent;
            border: none;
            border-bottom: 1px solid #374151;
            color: white;
            padding: 10px 0;
            font-size: 0.875rem;
            outline: none;
            transition: border-color 0.2s;
        }
        .input-field:focus { border-bottom-color: #10b981; }
        .input-field::placeholder { color: #4b5563; }
        .input-field:disabled { color: #4b5563; cursor: not-allowed; }
        select.input-field option { background: #111827; color: white; }
        select.input-field:disabled { opacity: 0.4; }
    </style>
</head>
<body class="bg-gray-950 min-h-screen flex items-center justify-center px-4 py-12">
<div class="w-full max-w-xl">

    <!-- Logo + stepper -->
    <div class="mb-10">
        <p class="text-xs font-semibold text-green-400 uppercase tracking-widest mb-1">PrestaKash</p>
        <h1 class="text-2xl font-bold text-white">Cuéntanos sobre ti</h1>
        <p class="text-gray-500 text-sm mt-1">Paso 1 de 2 — Datos personales</p>

        <!-- Barra de progreso -->
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

        <!-- Correo -->
        <div>
            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Correo electrónico</label>
            <input type="email" value="{{ Auth::user()->email }}" disabled class="input-field">
        </div>

        <!-- Nombres y Apellidos -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Nombres</label>
                <input type="text" name="first_name" value="{{ old('first_name') }}" required
                    placeholder="Juan Carlos"
                    oninput="this.value=this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g,'')"
                    class="input-field">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Apellidos</label>
                <input type="text" name="last_name" value="{{ old('last_name') }}" required
                    placeholder="Pérez García"
                    oninput="this.value=this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g,'')"
                    class="input-field">
            </div>
        </div>

        <!-- DNI y Teléfono -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Nro. DNI</label>
                <input type="text" name="dni" value="{{ old('dni') }}" required
                    placeholder="12345678" maxlength="8"
                    oninput="this.value=this.value.replace(/\D/g,'')"
                    class="input-field">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Teléfono celular</label>
                <input type="text" name="phone" value="{{ old('phone') }}" required
                    placeholder="987654321" maxlength="9"
                    oninput="this.value=this.value.replace(/\D/g,'')"
                    class="input-field">
            </div>
        </div>

        <!-- Ubigeo -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Departamento</label>
                <select name="department" id="department" required class="input-field">
                    <option value="">Seleccionar</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Provincia</label>
                <select name="province" id="province" required disabled class="input-field">
                    <option value="">Seleccionar</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Distrito</label>
                <select name="district" id="district" required disabled class="input-field">
                    <option value="">Seleccionar</option>
                </select>
            </div>
        </div>

        <!-- Dirección -->
        <div>
            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Dirección</label>
            <input type="text" name="address" value="{{ old('address') }}" required
                placeholder="Av. Arequipa 1234, Dpto 5B"
                class="input-field">
        </div>

        <!-- Submit -->
        <div class="pt-4 flex items-center justify-between">
            <span class="text-xs text-gray-600">Todos los campos son obligatorios</span>
            <button type="submit"
                class="flex items-center gap-2 bg-green-500 hover:bg-green-400 text-white font-semibold px-8 py-3 rounded-xl transition-all text-sm">
                Siguiente
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>

    </form>
</div>

<script>
const deptSelect = document.getElementById('department');
const provSelect = document.getElementById('province');
const distSelect = document.getElementById('district');

fetch('/ubigeo/departments')
    .then(r => r.json())
    .then(data => {
        data.forEach(d => deptSelect.innerHTML += `<option value="${d.id}">${d.name}</option>`);
        @if(old('department'))
            deptSelect.value = '{{ old('department') }}';
            deptSelect.dispatchEvent(new Event('change'));
        @endif
    });

deptSelect.addEventListener('change', function () {
    provSelect.innerHTML = '<option value="">Seleccionar</option>';
    distSelect.innerHTML = '<option value="">Seleccionar</option>';
    provSelect.disabled = true;
    distSelect.disabled = true;
    if (!this.value) return;
    fetch(`/ubigeo/provinces/${this.value}`)
        .then(r => r.json())
        .then(data => {
            data.forEach(p => provSelect.innerHTML += `<option value="${p.id}">${p.name}</option>`);
            provSelect.disabled = false;
            @if(old('province'))
                provSelect.value = '{{ old('province') }}';
                provSelect.dispatchEvent(new Event('change'));
            @endif
        });
});

provSelect.addEventListener('change', function () {
    distSelect.innerHTML = '<option value="">Seleccionar</option>';
    distSelect.disabled = true;
    if (!this.value) return;
    fetch(`/ubigeo/districts/${this.value}`)
        .then(r => r.json())
        .then(data => {
            data.forEach(d => distSelect.innerHTML += `<option value="${d.id}">${d.name}</option>`);
            distSelect.disabled = false;
            @if(old('district'))
                distSelect.value = '{{ old('district') }}';
            @endif
        });
});
</script>
</body>
</html>
