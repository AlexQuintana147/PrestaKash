@extends('superadmin.layout')

@section('title', 'Editar Trabajador')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8 max-w-2xl">
    <div class="mb-8">
        <a href="{{ route('superadmin.workers.index') }}" class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors mb-3">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Volver
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Editar trabajador</h1>
        <p class="text-sm text-gray-400 mt-1">{{ $worker->name }}</p>
    </div>

    <form action="{{ route('superadmin.workers.update', $worker) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white dark:bg-gray-900/60 rounded-xl border border-gray-100 dark:border-gray-800/50 p-5 space-y-4">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Datos personales</p>

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Nombre completo</label>
                <input type="text" name="name" required value="{{ old('name', $worker->name) }}"
                    class="w-full px-3 py-2.5 rounded-lg bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-300 dark:focus:ring-gray-700">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">DNI</label>
                    <input type="text" name="dni" required maxlength="20" value="{{ old('dni', $worker->dni) }}"
                        class="w-full px-3 py-2.5 rounded-lg bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-300 dark:focus:ring-gray-700">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Celular</label>
                    <input type="text" name="phone" maxlength="20" value="{{ old('phone', $worker->phone) }}"
                        class="w-full px-3 py-2.5 rounded-lg bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-300 dark:focus:ring-gray-700">
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Correo electrónico</label>
                <input type="email" name="email" required value="{{ old('email', $worker->email) }}"
                    class="w-full px-3 py-2.5 rounded-lg bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-300 dark:focus:ring-gray-700">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Nueva contraseña</label>
                    <input type="password" name="password" minlength="8"
                        class="w-full px-3 py-2.5 rounded-lg bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-300 dark:focus:ring-gray-700"
                        placeholder="Dejar vacío para no cambiar">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" minlength="8"
                        class="w-full px-3 py-2.5 rounded-lg bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-300 dark:focus:ring-gray-700"
                        placeholder="Repetir nueva contraseña">
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900/60 rounded-xl border border-gray-100 dark:border-gray-800/50 p-5 space-y-4">
            <div class="flex items-center justify-between">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Permisos de acceso</p>
                <button type="button" onclick="toggleAllPermissions()" class="text-[10px] font-bold text-orange-500 hover:text-orange-600 transition-colors">Todo / Nada</button>
            </div>

            <div class="space-y-2">
                @foreach($sections as $key => $label)
                @php
                    $base = "permissions[" . $key . "]";
                    $perm = $existingPermissions[$key] ?? null;
                @endphp
                <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-950/50">
                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300 w-28 flex-shrink-0">{{ $label }}</span>
                    <div class="flex items-center gap-2 flex-wrap">
                        <label class="inline-flex items-center gap-1 cursor-pointer">
                            <input type="checkbox" name="{{ $base }}[can_view]" value="1" {{ ($perm && $perm['can_view']) || (!$perm && old($base . '.can_view')) ? 'checked' : '' }}
                                class="w-3.5 h-3.5 rounded border-gray-300 dark:border-gray-600 text-orange-500 focus:ring-orange-500 dark:bg-gray-800 perm-view">
                            <span class="text-[10px] font-medium text-gray-500 dark:text-gray-400">Ver</span>
                        </label>
                        <label class="inline-flex items-center gap-1 cursor-pointer">
                            <input type="checkbox" name="{{ $base }}[can_create]" value="1" {{ ($perm && $perm['can_create']) || old($base . '.can_create') ? 'checked' : '' }}
                                class="w-3.5 h-3.5 rounded border-gray-300 dark:border-gray-600 text-emerald-500 focus:ring-emerald-500 dark:bg-gray-800">
                            <span class="text-[10px] font-medium text-gray-500 dark:text-gray-400">Crear</span>
                        </label>
                        <label class="inline-flex items-center gap-1 cursor-pointer">
                            <input type="checkbox" name="{{ $base }}[can_edit]" value="1" {{ ($perm && $perm['can_edit']) || old($base . '.can_edit') ? 'checked' : '' }}
                                class="w-3.5 h-3.5 rounded border-gray-300 dark:border-gray-600 text-blue-500 focus:ring-blue-500 dark:bg-gray-800">
                            <span class="text-[10px] font-medium text-gray-500 dark:text-gray-400">Editar</span>
                        </label>
                        <label class="inline-flex items-center gap-1 cursor-pointer">
                            <input type="checkbox" name="{{ $base }}[can_delete]" value="1" {{ ($perm && $perm['can_delete']) || old($base . '.can_delete') ? 'checked' : '' }}
                                class="w-3.5 h-3.5 rounded border-gray-300 dark:border-gray-600 text-red-500 focus:ring-red-500 dark:bg-gray-800">
                            <span class="text-[10px] font-medium text-gray-500 dark:text-gray-400">Eliminar</span>
                        </label>
                    </div>
                    <input type="hidden" name="{{ $base }}[section]" value="{{ $key }}">
                </div>
                @endforeach
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <a href="{{ route('superadmin.workers.index') }}"
                class="px-5 py-2.5 rounded-lg text-xs font-bold text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                class="px-5 py-2.5 rounded-lg text-xs font-bold bg-gray-900 dark:bg-white text-white dark:text-gray-900 hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors">
                Guardar cambios
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
