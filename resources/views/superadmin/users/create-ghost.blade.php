@extends('superadmin.layout')

@section('title', 'Crear Usuario Personalizado')

@section('content')
    <div class="min-h-[calc(100-8rem)] flex items-center justify-center px-4 md:px-10 py-8">
        <div class="w-full max-w-3xl">
            <!-- Header -->
            <div class="flex items-center gap-4 mb-12">
                <a href="{{ route('superadmin.users.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <p class="text-[10px] text-gray-500 uppercase tracking-[0.2em] mb-1">Panel / Clientes</p>
                    <h1 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Crear usuario
                        personalizado</h1>
                </div>
            </div>

            @if($errors->any())
                <div class="mb-6 p-4 border-l-2 border-red-500 bg-red-50 dark:bg-red-500/10 rounded-r-lg">
                    @foreach($errors->all() as $error)
                        <p class="text-red-600 dark:text-red-400 text-sm">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('superadmin.users.store-ghost') }}" method="POST" class="space-y-10">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-10">
                    <!-- Nombres -->
                    <div>
                        <label
                            class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-1">Nombres</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required
                            placeholder="Ej: Juan Pedro"
                            class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white px-0 py-2 focus:outline-none focus:border-blue-500 transition-colors placeholder-gray-300 dark:placeholder-gray-700">
                    </div>

                    <!-- Apellidos -->
                    <div>
                        <label
                            class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-1">Apellidos</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required
                            placeholder="Ej: Perez Garcia"
                            class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white px-0 py-2 focus:outline-none focus:border-blue-500 transition-colors placeholder-gray-300 dark:placeholder-gray-700">
                    </div>

                    <!-- DNI -->
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-1">DNI</label>
                        <input type="text" name="dni" value="{{ old('dni') }}" required maxlength="8" placeholder="12345678"
                            oninput="this.value=this.value.replace(/\D/g,'')"
                            class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white px-0 py-2 focus:outline-none focus:border-blue-500 transition-colors placeholder-gray-300 dark:placeholder-gray-700">
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label
                            class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-1">Teléfono</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required maxlength="9"
                            placeholder="987654321" oninput="this.value=this.value.replace(/\D/g,'')"
                            class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white px-0 py-2 focus:outline-none focus:border-blue-500 transition-colors placeholder-gray-300 dark:placeholder-gray-700">
                    </div>

                    <!-- Email -->
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-1">Correo
                            electrónico</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            placeholder="usuario@ejemplo.com"
                            class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white px-0 py-2 focus:outline-none focus:border-blue-500 transition-colors placeholder-gray-300 dark:placeholder-gray-700">
                    </div>

                    <!-- Ubigeo -->
                    <div>
                        <label
                            class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-1">Departamento</label>
                        <select name="department" id="department" required
                            class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white px-0 py-2 focus:outline-none focus:border-blue-500 transition-colors appearance-none cursor-pointer">
                            <option value="" class="dark:bg-black">Seleccionar</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department') == $dept->id ? 'selected' : '' }}
                                    class="dark:bg-black">
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-1">Provincia</label>
                        <select name="province" id="province" required disabled
                            class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white px-0 py-2 focus:outline-none focus:border-blue-500 transition-colors appearance-none cursor-pointer disabled:opacity-30">
                            <option value="" class="dark:bg-black">Seleccionar</option>
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-1">Distrito</label>
                        <select name="district" id="district" required disabled
                            class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white px-0 py-2 focus:outline-none focus:border-blue-500 transition-colors appearance-none cursor-pointer disabled:opacity-30">
                            <option value="" class="dark:bg-black">Seleccionar</option>
                        </select>
                    </div>

                    <!-- Dirección -->
                    <div class="md:col-span-2">
                        <label
                            class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-1">Dirección</label>
                        <input type="text" name="address" value="{{ old('address') }}" required
                            placeholder="Av. Principal 123"
                            class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white px-0 py-2 focus:outline-none focus:border-blue-500 transition-colors placeholder-gray-300 dark:placeholder-gray-700">
                    </div>
                </div>

                <div class="flex justify-center md:justify-end pt-8">
                    <button type="submit"
                        class="text-blue-600 hover:text-blue-700 border-b border-blue-600 hover:border-blue-700 pb-0.5 text-sm font-bold transition-all active:scale-[0.98]">
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