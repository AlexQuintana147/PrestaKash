@extends('superadmin.layout')

@section('title', 'Crear Usuario Personalizado')

@section('content')
    <div class="min-h-screen px-5 md:px-10 py-8">
        <!-- Header -->
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('superadmin.users.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <p class="text-xs text-gray-600 uppercase tracking-widest mb-1">Panel / Clientes</p>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Crear usuario
                    personalizado</h1>
            </div>
        </div>

        <div class="max-w-4xl">
            @if($errors->any())
                <div class="mb-6 p-4 border-l-2 border-red-500 bg-red-50 dark:bg-red-500/10 rounded-r-lg">
                    @foreach($errors->all() as $error)
                        <p class="text-red-600 dark:text-red-400 text-sm">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('superadmin.users.store-ghost') }}" method="POST"
                class="space-y-8 bg-white dark:bg-gray-900 p-6 md:p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombres -->
                    <div>
                        <label
                            class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Nombres</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required
                            placeholder="Ej: Juan Pedro"
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                    </div>

                    <!-- Apellidos -->
                    <div>
                        <label
                            class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Apellidos</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required
                            placeholder="Ej: Perez Garcia"
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                    </div>

                    <!-- DNI -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">DNI</label>
                        <input type="text" name="dni" value="{{ old('dni') }}" required maxlength="8" placeholder="12345678"
                            oninput="this.value=this.value.replace(/\D/g,'')"
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label
                            class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Teléfono</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required maxlength="9"
                            placeholder="987654321" oninput="this.value=this.value.replace(/\D/g,'')"
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                    </div>

                    <!-- Email -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Correo
                            electrónico</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            placeholder="usuario@ejemplo.com"
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                    </div>

                    <!-- Ubigeo -->
                    <div>
                        <label
                            class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Departamento</label>
                        <select name="department" id="department" required
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                            <option value="">Seleccionar</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Provincia</label>
                        <select name="province" id="province" required disabled
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all disabled:opacity-50">
                            <option value="">Seleccionar</option>
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Distrito</label>
                        <select name="district" id="district" required disabled
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all disabled:opacity-50">
                            <option value="">Seleccionar</option>
                        </select>
                    </div>

                    <!-- Dirección -->
                    <div class="md:col-span-2">
                        <label
                            class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Dirección</label>
                        <input type="text" name="address" value="{{ old('address') }}" required
                            placeholder="Av. Principal 123"
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-bold transition-all shadow-lg shadow-blue-500/20">
                        Crear usuario fantasma
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deptSelect = document.getElementById('department');
            const provSelect = document.getElementById('province');
            const distSelect = document.getElementById('district');

            deptSelect.addEventListener('change', function () {
                const deptId = this.value;
                provSelect.innerHTML = '<option value="">Seleccionar</option>';
                distSelect.innerHTML = '<option value="">Seleccionar</option>';
                provSelect.disabled = true;
                distSelect.disabled = true;

                if (deptId) {
                    fetch(`/ubigeo/provinces/${deptId}`)
                        .then(r => r.json())
                        .then(data => {
                            data.forEach(p => {
                                const opt = document.createElement('option');
                                opt.value = p.id;
                                opt.textContent = p.name;
                                provSelect.appendChild(opt);
                            });
                            provSelect.disabled = false;
                        });
                }
            });

            provSelect.addEventListener('change', function () {
                const provId = this.value;
                distSelect.innerHTML = '<option value="">Seleccionar</option>';
                distSelect.disabled = true;

                if (provId) {
                    fetch(`/ubigeo/districts/${provId}`)
                        .then(r => r.json())
                        .then(data => {
                            data.forEach(d => {
                                const opt = document.createElement('option');
                                opt.value = d.id;
                                opt.textContent = d.name;
                                distSelect.appendChild(opt);
                            });
                            distSelect.disabled = false;
                        });
                }
            });
        });
    </script>
    @include('partials.global-scripts')
@endsection