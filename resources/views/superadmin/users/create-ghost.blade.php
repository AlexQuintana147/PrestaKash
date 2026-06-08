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
                            oninput="this.value=this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ ]/g,'')"
                            class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white px-0 py-2 focus:outline-none focus:border-blue-500 transition-colors placeholder-gray-400 dark:placeholder-gray-100">
                    </div>

                    <!-- Apellidos -->
                    <div>
                        <label
                            class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-1">Apellidos</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required
                            placeholder="Ej: Perez Garcia"
                            oninput="this.value=this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ ]/g,'')"
                            class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white px-0 py-2 focus:outline-none focus:border-blue-500 transition-colors placeholder-gray-400 dark:placeholder-gray-100">
                        <p class="text-[10px] text-gray-500 mt-1">Ingresa tus dos apellidos separados por un espacio.</p>
                    </div>

                    <!-- DNI -->
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-1">DNI</label>
                        <input type="text" name="dni" value="{{ old('dni') }}" required maxlength="8" placeholder="12345678"
                            oninput="this.value=this.value.replace(/\D/g,'')"
                            class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white px-0 py-2 focus:outline-none focus:border-blue-500 transition-colors placeholder-gray-400 dark:placeholder-gray-100">
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label
                            class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-1">Teléfono</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required maxlength="9"
                            placeholder="987654321" oninput="this.value=this.value.replace(/\D/g,'')"
                            class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white px-0 py-2 focus:outline-none focus:border-blue-500 transition-colors placeholder-gray-400 dark:placeholder-gray-100">
                    </div>

                    <!-- Email -->
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-1">Correo
                            electrónico</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            placeholder="usuario@ejemplo.com"
                            class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white px-0 py-2 focus:outline-none focus:border-blue-500 transition-colors placeholder-gray-400 dark:placeholder-gray-100">
                    </div>

                    <!-- Ubigeo -->
                    <div class="relative">
                        <label
                            class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-1">Departamento</label>
                        <div class="relative">
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
                            <div class="absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none">
                                <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <label
                            class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-1">Provincia</label>
                        <div class="relative">
                            <select name="province" id="province" required disabled
                                class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white px-0 py-2 focus:outline-none focus:border-blue-500 transition-colors appearance-none cursor-pointer disabled:opacity-30">
                                <option value="" class="dark:bg-black">Seleccionar</option>
                            </select>
                            <div class="absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none">
                                <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <label
                            class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-1">Distrito</label>
                        <div class="relative">
                            <select name="district" id="district" required disabled
                                class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white px-0 py-2 focus:outline-none focus:border-blue-500 transition-colors appearance-none cursor-pointer disabled:opacity-30">
                                <option value="" class="dark:bg-black">Seleccionar</option>
                            </select>
                            <div class="absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none">
                                <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Dirección -->
                    <div class="md:col-span-2">
                        <label
                            class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-1">Dirección</label>
                        <input type="text" name="address" value="{{ old('address') }}" required
                            placeholder="Av. Principal 123"
                            class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white px-0 py-2 focus:outline-none focus:border-blue-500 transition-colors placeholder-gray-400 dark:placeholder-gray-100">
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

            function loadProvinces(deptId, selectedProvId = null) {
                provSelect.innerHTML = '<option value="" class="dark:bg-black">Seleccionar</option>';
                distSelect.innerHTML = '<option value="" class="dark:bg-black">Seleccionar</option>';
                provSelect.disabled = true;
                distSelect.disabled = true;

                if (!deptId) return;

                const url = `/ubigeo/provinces/${deptId}`;
                console.log('Fetching provinces from:', url);

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(async r => {
                        console.log('Response status:', r.status);
                        const contentType = r.headers.get("content-type");
                        if (!r.ok || !contentType || !contentType.includes("application/json")) {
                            const text = await r.text();
                            console.error('Fetch error body (likely HTML):', text);
                            throw new Error(`Server error: ${r.status}. Expected JSON but got ${contentType}`);
                        }
                        return r.json();
                    })
                    .then(data => {
                        data.forEach(p => {
                            const opt = document.createElement('option');
                            opt.value = p.id;
                            opt.textContent = p.name;
                            opt.className = 'dark:bg-black';
                            if (selectedProvId && p.id == selectedProvId) opt.selected = true;
                            provSelect.appendChild(opt);
                        });
                        provSelect.disabled = false;
                        if (selectedProvId) {
                            loadDistricts(selectedProvId, '{{ old('district') }}');
                        }
                    })
                    .catch(err => console.error('Error fetching provinces:', err));
            }

            function loadDistricts(provId, selectedDistId = null) {
                distSelect.innerHTML = '<option value="" class="dark:bg-black">Seleccionar</option>';
                distSelect.disabled = true;

                if (!provId) return;

                const url = `/ubigeo/districts/${provId}`;
                console.log('Fetching districts from:', url);

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(async r => {
                        console.log('Response status:', r.status);
                        const contentType = r.headers.get("content-type");
                        if (!r.ok || !contentType || !contentType.includes("application/json")) {
                            const text = await r.text();
                            console.error('Fetch error body (likely HTML):', text);
                            throw new Error(`Server error: ${r.status}. Expected JSON but got ${contentType}`);
                        }
                        return r.json();
                    })
                    .then(data => {
                        data.forEach(d => {
                            const opt = document.createElement('option');
                            opt.value = d.id;
                            opt.textContent = d.name;
                            opt.className = 'dark:bg-black';
                            if (selectedDistId && d.id == selectedDistId) opt.selected = true;
                            distSelect.appendChild(opt);
                        });
                        distSelect.disabled = false;
                    })
                    .catch(err => console.error('Error fetching districts:', err));
            }

            deptSelect.addEventListener('change', function () {
                loadProvinces(this.value);
            });

            provSelect.addEventListener('change', function () {
                loadDistricts(this.value);
            });

            // Carga inicial si hay valores (ej: por validación fallida)
            if (deptSelect.value) {
                loadProvinces(deptSelect.value, '{{ old('province') }}');
            }
        });
    </script>
    @include('partials.global-scripts')
@endsection