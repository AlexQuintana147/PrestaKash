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

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Nombre completo</label>
                <input type="text" name="name" required value="{{ old('name') }}"
                    class="w-full px-0 py-2.5 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white placeholder:text-gray-300 transition-colors"
                    placeholder="Juan Pérez García">
                @error('name')
                <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">DNI</label>
                    <input type="text" name="dni" required maxlength="20" value="{{ old('dni') }}"
                        class="w-full px-0 py-2.5 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors"
                        placeholder="12345678">
                    @error('dni')
                    <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Celular</label>
                    <input type="text" name="phone" maxlength="20" value="{{ old('phone') }}"
                        class="w-full px-0 py-2.5 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors"
                        placeholder="+51 999 999 999">
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
                        placeholder="Mínimo 8 caracteres">
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
                <button type="button" onclick="toggleAllPermissions()" class="text-[10px] font-bold text-orange-500 hover:text-orange-600 transition-colors">Todo / Nada</button>
            </div>

            <div class="space-y-0 divide-y divide-gray-100 dark:divide-gray-800/60">
                @foreach($sections as $key => $label)
                <div class="py-3 flex items-center gap-4 group">
                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300 w-28 flex-shrink-0">{{ $label }}</span>
                    <div class="flex items-center gap-3 flex-wrap">
                        @php $base = "permissions[" . $key . "]" @endphp
                        <label class="inline-flex items-center gap-1 cursor-pointer group/perm relative">
                            <input type="checkbox" name="{{ $base }}[can_view]" value="1" checked
                                class="w-3.5 h-3.5 rounded border-gray-300 dark:border-gray-600 text-orange-500 focus:ring-orange-500 dark:bg-gray-800">
                            <span class="text-[10px] font-medium text-gray-500 dark:text-gray-400">Ver</span>
                            <span class="help-icon w-3.5 h-3.5 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-[8px] font-bold text-gray-400 cursor-help opacity-0 group-hover/perm:opacity-100 transition-opacity" title="Puede ver y listar registros de {{ strtolower($label) }}">?</span>
                        </label>
                        <label class="inline-flex items-center gap-1 cursor-pointer group/perm relative">
                            <input type="checkbox" name="{{ $base }}[can_create]" value="1"
                                class="w-3.5 h-3.5 rounded border-gray-300 dark:border-gray-600 text-emerald-500 focus:ring-emerald-500 dark:bg-gray-800">
                            <span class="text-[10px] font-medium text-gray-500 dark:text-gray-400">Crear</span>
                            <span class="help-icon w-3.5 h-3.5 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-[8px] font-bold text-gray-400 cursor-help opacity-0 group-hover/perm:opacity-100 transition-opacity" title="Puede crear nuevos registros de {{ strtolower($label) }}">?</span>
                        </label>
                        <label class="inline-flex items-center gap-1 cursor-pointer group/perm relative">
                            <input type="checkbox" name="{{ $base }}[can_edit]" value="1"
                                class="w-3.5 h-3.5 rounded border-gray-300 dark:border-gray-600 text-blue-500 focus:ring-blue-500 dark:bg-gray-800">
                            <span class="text-[10px] font-medium text-gray-500 dark:text-gray-400">Editar</span>
                            <span class="help-icon w-3.5 h-3.5 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-[8px] font-bold text-gray-400 cursor-help opacity-0 group-hover/perm:opacity-100 transition-opacity" title="Puede modificar registros existentes de {{ strtolower($label) }}">?</span>
                        </label>
                        <label class="inline-flex items-center gap-1 cursor-pointer group/perm relative">
                            <input type="checkbox" name="{{ $base }}[can_delete]" value="1"
                                class="w-3.5 h-3.5 rounded border-gray-300 dark:border-gray-600 text-red-500 focus:ring-red-500 dark:bg-gray-800">
                            <span class="text-[10px] font-medium text-gray-500 dark:text-gray-400">Eliminar</span>
                            <span class="help-icon w-3.5 h-3.5 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-[8px] font-bold text-gray-400 cursor-help opacity-0 group-hover/perm:opacity-100 transition-opacity" title="Puede eliminar registros de {{ strtolower($label) }}">?</span>
                        </label>
                    </div>
                    <input type="hidden" name="{{ $base }}[section]" value="{{ $key }}">
                </div>
                @endforeach
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
            <a href="{{ route('superadmin.workers.index') }}"
                class="px-5 py-2 text-xs font-bold text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                class="px-5 py-2 text-xs font-bold bg-gray-900 dark:bg-white text-white dark:text-gray-900 hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors rounded-lg">
                Crear trabajador
            </button>
        </div>
    </form>
</div>

<script>
function toggleAllPermissions() {
    const all = document.querySelectorAll('input[type="checkbox"][name*="can_"]');
    const firstChecked = all[0]?.checked;
    all.forEach(cb => cb.checked = !firstChecked);
}
</script>
@endsection
