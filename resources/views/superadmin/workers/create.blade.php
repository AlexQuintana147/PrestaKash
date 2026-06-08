@extends('superadmin.layout')

@section('title', 'Nuevo Trabajador')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8 max-w-2xl">
    <div class="mb-8">
        <a href="{{ route('superadmin.workers.index') }}" class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors mb-3">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Volver
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Nuevo trabajador</h1>
        <p class="text-sm text-gray-400 mt-1">Completa los datos y asigna permisos de acceso</p>
    </div>

    @if($errors->has('general'))
        <div class="mb-6 rounded-xl bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-900/30 p-4">
            <p class="text-sm font-medium text-red-600 dark:text-red-400">{{ $errors->first('general') }}</p>
        </div>
    @endif

    <form action="{{ route('superadmin.workers.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="space-y-4">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest pb-2 border-b border-gray-200 dark:border-gray-800">Datos personales</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-100 mb-1">Nombres</label>
                    <input type="text" name="first_name" required value="{{ old('first_name') }}"
                        class="w-full px-0 py-2.5 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white placeholder:text-gray-300 transition-colors"
                        placeholder="Juan Carlos">
                    @error('first_name')
                    <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-100 mb-1">Apellidos completos</label>
                    <input type="text" name="last_name" required value="{{ old('last_name') }}"
                        class="w-full px-0 py-2.5 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white placeholder:text-gray-300 transition-colors"
                        placeholder="Pérez García">
                    @error('last_name')
                    <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-100 mb-1">DNI</label>
                    <input type="text" name="dni" required maxlength="8" value="{{ old('dni') }}"
                        class="w-full px-0 py-2.5 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors"
                        placeholder="8 dígitos numéricos">
                    @error('dni')
                    <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-100 mb-1">Celular</label>
                    <input type="tel" name="phone" maxlength="9" inputmode="numeric" value="{{ old('phone') }}"
                        class="w-full px-0 py-2.5 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors"
                        placeholder="912345678"
                        oninput="this.value = this.value.replace(/\D/g,'')">
                    @error('phone')
                    <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-100 mb-1">Correo electrónico</label>
                <input type="email" name="email" required value="{{ old('email') }}"
                    class="w-full px-0 py-2.5 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white placeholder:text-gray-300 transition-colors"
                    placeholder="trabajador@ejemplo.com">
                @error('email')
                <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-100 mb-1">Contraseña</label>
                    <input type="password" name="password" required minlength="8"
                        class="w-full px-0 py-2.5 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors"
                        placeholder="Mayúscula, minúscula, número (8+)">
                    @error('password')
                    <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-100 mb-1">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" required minlength="8"
                        class="w-full px-0 py-2.5 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors"
                        placeholder="Repetir contraseña">
                </div>
            </div>
        </div>

        <div class="space-y-4 pt-4">
            <div class="flex items-center justify-between pb-2 border-b border-gray-200 dark:border-gray-800">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Permisos de acceso</p>
                <button type="button" onclick="toggleAllPermissions()" class="text-[10px] font-bold text-orange-500 hover:text-orange-600 transition-colors hidden sm:inline-flex">Todo / Nada</button>
            </div>

            <div class="space-y-0 divide-y divide-gray-100 dark:divide-gray-800/60">
                @foreach($sections as $key => $label)
                <div class="py-3 flex items-center gap-4 group">
                    <label class="inline-flex items-center gap-3 cursor-pointer">
                        @php $base = "permissions[" . $loop->index . "]" @endphp
                        <input type="hidden" name="{{ $base }}[section]" value="{{ $key }}">
                        <input type="checkbox" name="{{ $base }}[enabled]" value="1" checked
                            class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-orange-500 focus:ring-orange-500 dark:bg-gray-800">
                        <span class="text-xs font-bold text-gray-700 dark:text-gray-100">{{ $label }}</span>
                    </label>
                </div>
                @endforeach
            </div>
            @error('permissions')
            <p class="text-[10px] text-red-500 mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-6 flex flex-col sm:flex-row gap-6 justify-end">
            <button type="button" onclick="window.location='{{ route('superadmin.workers.index') }}'"
                class="px-0 py-2 text-sm font-medium text-gray-500 dark:text-gray-100 bg-transparent border-0 border-b border-gray-300 dark:border-gray-700 hover:border-gray-500 dark:hover:border-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors text-left sm:text-right">
                Cancelar
            </button>
            <button type="submit"
                class="px-0 py-2 text-sm font-bold text-orange-500 bg-transparent border-0 border-b border-orange-300 dark:border-orange-800 hover:border-orange-500 dark:hover:border-orange-500 transition-colors text-left sm:text-right">
                Guardar trabajador
            </button>
        </div>
    </form>

    <script>
    function toggleAllPermissions() {
        const all = document.querySelectorAll('form input[type="checkbox"][name*="[enabled]"]');
        if (!all.length) return;
        const firstState = all[0].checked;
        all.forEach(cb => {
            cb.checked = !firstState;
        });
    }
    </script>
</div>
@endsection