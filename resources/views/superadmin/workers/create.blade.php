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

    <form action="{{ route('superadmin.workers.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="space-y-4">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest pb-2 border-b border-gray-200 dark:border-gray-800">Datos personales</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Nombres</label>
                    <input type="text" name="first_name" required value="{{ old('first_name') }}"
                        class="w-full px-0 py-2.5 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white placeholder:text-gray-300 transition-colors"
                        placeholder="Juan Carlos">
                    @error('first_name')
                    <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Apellidos completos</label>
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
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">DNI</label>
                    <input type="text" name="dni" required maxlength="8" value="{{ old('dni') }}"
                        class="w-full px-0 py-2.5 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors"
                        placeholder="8 dígitos numéricos">
                    @error('dni')
                    <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Celular</label>
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
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Correo electrónico</label>
                <input type="email" name="email" required value="{{ old('email') }}"
                    class="w-full px-0 py-2.5 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white placeholder:text-gray-300 transition-colors"
                    placeholder="trabajador@ejemplo.com">
                @error('email')
                <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Contraseña</label>
                    <input type="password" name="password" required minlength="8"
                        class="w-full px-0 py-2.5 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors"
                        placeholder="Mayúscula, minúscula, número (8+)">
                    @error('password')
                    <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Confirmar contraseña</label>
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
                <div class="py-3 flex items-start gap-4 group">
                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300 w-28 flex-shrink-0 pt-0.5">{{ $label }}</span>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 flex-wrap">
                        @php $base = "permissions[" . $key . "]" @endphp
                        <label class="inline-flex items-center gap-1 cursor-pointer group/perm relative">
                            <input type="checkbox" name="{{ $base }}[can_view]" value="1" checked
                                class="w-3.5 h-3.5 rounded border-gray-300 dark:border-gray-600 text-orange-500 focus:ring-orange-500 dark:bg-gray-800">
                            <span class="text-[10px] font-medium text-gray-500 dark:text-gray-400">Ver</span>
                            <span class="help-icon hidden sm:flex relative w-3.5 h-3.5 rounded-full bg-gray-200 dark:bg-gray-700 items-center justify-center text-[8px] font-bold text-gray-400 cursor-help opacity-0 group-hover/perm:opacity-100 transition-opacity">
                                ?
                                <span class="tooltip invisible absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 px-2 py-1 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-[9px] font-medium rounded-md whitespace-nowrap shadow-lg z-50 opacity-0 transition-all duration-150 group-hover/perm:visible group-hover/perm:opacity-100">Ver y listar {{ strtolower($label) }}</span>
                            </span>
                        </label>
                        <label class="inline-flex items-center gap-1 cursor-pointer group/perm relative">
                            <input type="checkbox" name="{{ $base }}[can_create]" value="1"
                                class="w-3.5 h-3.5 rounded border-gray-300 dark:border-gray-600 text-emerald-500 focus:ring-emerald-500 dark:bg-gray-800">
                            <span class="text-[10px] font-medium text-gray-500 dark:text-gray-400">Crear</span>
                            <span class="help-icon hidden sm:flex relative w-3.5 h-3.5 rounded-full bg-gray-200 dark:bg-gray-700 items-center justify-center text-[8px] font-bold text-gray-400 cursor-help opacity-0 group-hover/perm:opacity-100 transition-opacity">
                                ?
                                <span class="tooltip invisible absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 px-2 py-1 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-[9px] font-medium rounded-md whitespace-nowrap shadow-lg z-50 opacity-0 transition-all duration-150 group-hover/perm:visible group-hover/perm:opacity-100">Crear nuevos {{ strtolower($label) }}</span>
                            </span>
                        </label>
                        <label class="inline-flex items-center gap-1 cursor-pointer group/perm relative">
                            <input type="checkbox" name="{{ $base }}[can_edit]" value="1"
                                class="w-3.5 h-3.5 rounded border-gray-300 dark:border-gray-600 text-blue-500 focus:ring-blue-500 dark:bg-gray-800">
                            <span class="text-[10px] font-medium text-gray-500 dark:text-gray-400">Editar</span>
                            <span class="help-icon hidden sm:flex relative w-3.5 h-3.5 rounded-full bg-gray-200 dark:bg-gray-700 items-center justify-center text-[8px] font-bold text-gray-400 cursor-help opacity-0 group-hover/perm:opacity-100 transition-opacity">
                                ?
                                <span class="tooltip invisible absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 px-2 py-1 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-[9px] font-medium rounded-md whitespace-nowrap shadow-lg z-50 opacity-0 transition-all duration-150 group-hover/perm:visible group-hover/perm:opacity-100">Modificar {{ strtolower($label) }}</span>
                            </span>
                        </label>
                        <label class="inline-flex items-center gap-1 cursor-pointer group/perm relative">
                            <input type="checkbox" name="{{ $base }}[can_delete]" value="1"
                                class="w-3.5 h-3.5 rounded border-gray-300 dark:border-gray-600 text-red-500 focus:ring-red-500 dark:bg-gray-800">
                            <span class="text-[10px] font-medium text-gray-500 dark:text-gray-400">Eliminar</span>
                            <span class="help-icon hidden sm:flex relative w-3.5 h-3.5 rounded-full bg-gray-200 dark:bg-gray-700 items-center justify-center text-[8px] font-bold text-gray-400 cursor-help opacity-0 group-hover/perm:opacity-100 transition-opacity">
                                ?
                                <span class="tooltip invisible absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 px-2 py-1 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-[9px] font-medium rounded-md whitespace-nowrap shadow-lg z-50 opacity-0 transition-all duration-150 group-hover/perm:visible group-hover/perm:opacity-100">Eliminar {{ strtolower($label) }}</span>
                            </span>
                        </label>
                    </div>
                </div>
                @endforeach
            </div>
            @error('permissions')
            <p class="text-[10px] text-red-500 mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-6 flex flex-col sm:flex-row gap-6 justify-end">
            <button type="button" onclick="window.location='{{ route('superadmin.workers.index') }}'"
                class="px-0 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 bg-transparent border-0 border-b border-gray-300 dark:border-gray-700 hover:border-gray-500 dark:hover:border-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors text-left sm:text-right">
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
        const all = document.querySelectorAll('form input[type="checkbox"][name*="[can_view]"]');
        if (!all.length) return;
        const firstState = all[0].checked;
        document.querySelectorAll('form input[type="checkbox"]').forEach(cb => {
            cb.checked = !firstState;
        });
    }
    </script>
</div>
@endsection