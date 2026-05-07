@extends('superadmin.layout')

@section('title', 'Configuración')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8 max-w-xl">

    <div class="mb-10">
        <p class="text-xs text-gray-600 dark:text-gray-600 uppercase tracking-widest mb-1">Panel</p>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Configuración</h1>
    </div>

    <!-- Nombre -->
    <div class="mb-10">
        <p class="text-xs text-gray-600 dark:text-gray-600 uppercase tracking-widest mb-5">Nombre</p>

        @if(session('success_name'))
            <div class="mb-4 flex items-center gap-2 text-sm text-green-500">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success_name') }}
            </div>
        @endif

        <form action="{{ route('superadmin.settings.name') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1.5">Nombre completo</label>
                <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required
                       class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-gray-900 dark:text-white text-sm py-2 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors @error('name') border-red-400 @enderror">
                @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit"
                    class="px-5 py-2 text-sm font-semibold rounded-lg bg-gray-900 dark:bg-white text-white dark:text-black hover:bg-gray-700 dark:hover:bg-gray-100 transition-colors">
                Guardar nombre
            </button>
        </form>
    </div>

    <div class="border-t border-gray-100 dark:border-gray-900 mb-10"></div>

    <!-- Contraseña -->
    <div>
        <p class="text-xs text-gray-600 dark:text-gray-600 uppercase tracking-widest mb-5">Contraseña</p>

        @if(session('success_password'))
            <div class="mb-4 flex items-center gap-2 text-sm text-green-500">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success_password') }}
            </div>
        @endif

        <form action="{{ route('superadmin.settings.password') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1.5">Contraseña actual</label>
                <input type="password" name="current_password" required
                       class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-gray-900 dark:text-white text-sm py-2 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors @error('current_password') border-red-400 @enderror">
                @error('current_password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1.5">Nueva contraseña</label>
                <input type="password" name="password" required minlength="8"
                       class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-gray-900 dark:text-white text-sm py-2 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors @error('password') border-red-400 @enderror">
                @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1.5">Confirmar nueva contraseña</label>
                <input type="password" name="password_confirmation" required minlength="8"
                       class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-gray-900 dark:text-white text-sm py-2 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors">
            </div>
            <button type="submit"
                    class="px-5 py-2 text-sm font-semibold rounded-lg bg-gray-900 dark:bg-white text-white dark:text-black hover:bg-gray-700 dark:hover:bg-gray-100 transition-colors">
                Actualizar contraseña
            </button>
        </form>
    </div>

    <div class="border-t border-gray-100 dark:border-gray-900 mb-10"></div>

    <!-- Configuración de Empresa -->
    <div>
        <p class="text-xs text-gray-600 dark:text-gray-600 uppercase tracking-widest mb-5">Configuración de Empresa</p>
        <p class="text-[10px] text-gray-400 dark:text-gray-500 mb-4">Formatos recomendados: PNG, JPG o WebP</p>

        @if(session('success_company'))
            <div class="mb-4 flex items-center gap-2 text-sm text-green-500">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success_company') }}
            </div>
        @endif

        <form action="{{ route('superadmin.settings.company') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Logo -->
            <div>
                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1.5">Logo de la empresa</label>
                <div class="flex items-center gap-4">
                    @if($company_logo)
                        <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-900 flex items-center justify-center">
                            <img src="{{ Storage::url($company_logo) }}" alt="Logo" class="w-full h-full object-contain">
                        </div>
                    @endif
                    <input type="file" name="company_logo" accept="image/png,image/jpeg,image-webp"
                           class="text-sm text-gray-500 dark:text-gray-400
                                  file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0
                                  file:text-xs file:font-semibold file:bg-gray-900 file:text-white
                                  dark:file:bg-white dark:file:text-black file:transition-colors
                                  hover:file:bg-gray-700 hover:dark:file:bg-gray-100">
                </div>
                @error('company_logo') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Firma -->
            <div>
                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1.5">Firma de la empresa</label>
                <div class="flex items-center gap-4">
                    @if($company_signature)
                        <div class="w-24 h-12 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-900 flex items-center justify-center">
                            <img src="{{ Storage::url($company_signature) }}" alt="Firma" class="w-full h-full object-contain">
                        </div>
                    @endif
                    <input type="file" name="company_signature" accept="image/png,image/jpeg,image-webp"
                           class="text-sm text-gray-500 dark:text-gray-400
                                  file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0
                                  file:text-xs file:font-semibold file:bg-gray-900 file:text-white
                                  dark:file:bg-white dark:file:text-black file:transition-colors
                                  hover:file:bg-gray-700 hover:dark:file:bg-gray-100">
                </div>
                @error('company_signature') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- RUC -->
            <div>
                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1.5">RUC de la empresa</label>
                <input type="text" name="company_ruc" value="{{ old('company_ruc', $company_ruc) }}" maxlength="20"
                       class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-gray-900 dark:text-white text-sm py-2 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-colors @error('company_ruc') border-red-400 @enderror">
                @error('company_ruc') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit"
                    class="px-5 py-2 text-sm font-semibold rounded-lg bg-gray-900 dark:bg-white text-white dark:text-black hover:bg-gray-700 dark:hover:bg-gray-100 transition-colors">
                Guardar configuración
            </button>
        </form>
    </div>

</div>
@endsection
