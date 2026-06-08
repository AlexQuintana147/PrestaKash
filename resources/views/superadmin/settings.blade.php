@extends('superadmin.layout')

@section('title', 'Configuración')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8">

    <div class="mb-12">
        <h1 class="text-3xl font-light text-gray-900 dark:text-white tracking-tight">Configuración</h1>
        <p class="text-[10px] text-gray-400 uppercase tracking-[0.2em] mt-2">Gestión de cuenta e identidad</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

        <div class="space-y-12">
            <!-- Perfil -->
            <section>
                <div class="flex items-center gap-3 mb-6">
                    <div class="text-gray-900 dark:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Perfil</h2>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wider">Información personal</p>
                    </div>
                </div>

                <div class="space-y-6">
                    @if(session('success_name'))
                        <div class="flex items-center gap-2 text-xs text-green-500">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ session('success_name') }}
                        </div>
                    @endif

                    <form action="{{ route('superadmin.settings.name') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="group">
                            <label class="block text-[10px] font-medium text-gray-500 dark:text-gray-100 mb-1 transition-colors group-focus-within:text-gray-900 dark:group-focus-within:text-white">Nombre completo</label>
                            <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required
                                   class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-gray-900 dark:text-white text-sm py-2 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all @error('name') border-red-400 @enderror">
                            @error('name') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <button type="submit"
                                class="inline-flex items-center text-xs font-bold text-gray-900 dark:text-white hover:opacity-70 transition-opacity border-b-2 border-gray-900 dark:border-white pb-1">
                            GUARDAR CAMBIOS
                        </button>
                    </form>
                </div>
            </section>

            <!-- Representante Legal -->
            <section>
                <div class="flex items-center gap-3 mb-6">
                    <div class="text-gray-900 dark:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Representante Legal</h2>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wider">Datos del firmante</p>
                    </div>
                </div>

                <div class="space-y-6">
                    @if(session('success_representative'))
                        <div class="flex items-center gap-2 text-xs text-green-500">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ session('success_representative') }}
                        </div>
                    @endif

                    <form action="{{ route('superadmin.settings.company') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="group">
                            <label class="block text-[10px] font-medium text-gray-500 dark:text-gray-100 mb-1">Nombres Completos</label>
                            <input type="text" name="representative_name" value="{{ old('representative_name', $representative_name) }}"
                                   placeholder="Carlos Antonio" required
                                   class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-gray-900 dark:text-white text-sm py-2 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all @error('representative_name') border-red-400 @enderror">
                            @error('representative_name') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="group">
                            <label class="block text-[10px] font-medium text-gray-500 dark:text-gray-100 mb-1">Apellidos Completos (Mínimo 2 apellidos)</label>
                            <input type="text" name="representative_last_name" value="{{ old('representative_last_name', $representative_last_name) }}"
                                   placeholder="Vega García" required
                                   class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-gray-900 dark:text-white text-sm py-2 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all @error('representative_last_name') border-red-400 @enderror">
                            @error('representative_last_name') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="group">
                            <label class="block text-[10px] font-medium text-gray-500 dark:text-gray-100 mb-1">DNI</label>
                            <input type="text" name="representative_dni" value="{{ old('representative_dni', $representative_dni) }}" maxlength="8"
                                   placeholder="12345678"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                   class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-gray-900 dark:text-white text-sm py-2 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all @error('representative_dni') border-red-400 @enderror">
                            @error('representative_dni') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="group">
                            <label class="block text-[10px] font-medium text-gray-500 dark:text-gray-100 mb-1">Correo Electrónico</label>
                            <input type="email" name="representative_email" value="{{ old('representative_email', $representative_email) }}"
                                   placeholder="representante@empresa.com"
                                   class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-gray-900 dark:text-white text-sm py-2 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all @error('representative_email') border-red-400 @enderror">
                            @error('representative_email') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="group">
                            <label class="block text-[10px] font-medium text-gray-500 dark:text-gray-100 mb-1">Teléfono</label>
                            <input type="text" name="representative_phone" value="{{ old('representative_phone', $representative_phone) }}"
                                   maxlength="9"
                                   placeholder="Ej: 987654321"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 9)"
                                   class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-gray-900 dark:text-white text-sm py-2 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all @error('representative_phone') border-red-400 @enderror">
                            @error('representative_phone') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                    class="inline-flex items-center text-xs font-bold text-gray-900 dark:text-white hover:opacity-70 transition-opacity border-b-2 border-gray-900 dark:border-white pb-1">
                                GUARDAR DATOS
                            </button>
                        </div>
                    </form>
                </div>
            </section>
        </div>

        <!-- Seguridad -->
        <section>
            <div class="flex items-center gap-3 mb-6">
                <div class="text-gray-900 dark:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Seguridad</h2>
                    <p class="text-[10px] text-gray-500 uppercase tracking-wider">Acceso a la cuenta</p>
                </div>
            </div>

            <div class="space-y-6">
                @if(session('success_password'))
                    <div class="flex items-center gap-2 text-xs text-green-500">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ session('success_password') }}
                    </div>
                @endif

                <form action="{{ route('superadmin.settings.password') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="group">
                        <label class="block text-[10px] font-medium text-gray-500 dark:text-gray-100 mb-1 transition-colors group-focus-within:text-gray-900 dark:group-focus-within:text-white">Contraseña actual</label>
                        <input type="password" name="current_password" required
                               class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-gray-900 dark:text-white text-sm py-2 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all placeholder-gray-400 dark:placeholder-gray-100 @error('current_password') border-red-400 @enderror"
                               placeholder="••••••••">
                        @error('current_password') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="group">
                        <label class="block text-[10px] font-medium text-gray-500 dark:text-gray-100 mb-1 transition-colors group-focus-within:text-gray-900 dark:group-focus-within:text-white">Nueva contraseña</label>
                        <input type="password" name="password" required minlength="8"
                               class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-gray-900 dark:text-white text-sm py-2 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all placeholder-gray-400 dark:placeholder-gray-100 @error('password') border-red-400 @enderror"
                               placeholder="Mínimo 8 caracteres">
                        @error('password') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="group">
                        <label class="block text-[10px] font-medium text-gray-500 dark:text-gray-100 mb-1 transition-colors group-focus-within:text-gray-900 dark:group-focus-within:text-white">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-gray-900 dark:text-white text-sm py-2 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all placeholder-gray-400 dark:placeholder-gray-100"
                               placeholder="••••••••">
                    </div>
                    <button type="submit"
                            class="inline-flex items-center text-xs font-bold text-gray-900 dark:text-white hover:opacity-70 transition-opacity border-b-2 border-gray-900 dark:border-white pb-1">
                        ACTUALIZAR CONTRASEÑA
                    </button>
                </form>
            </div>
        </section>

        <!-- Empresa -->
        <section>
            <div class="flex items-center gap-3 mb-6">
                <div class="text-gray-900 dark:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Empresa</h2>
                    <p class="text-[10px] text-gray-500 uppercase tracking-wider">Identidad corporativa</p>
                </div>
            </div>

            <div class="space-y-6">
                @if(session('success_company'))
                    <div class="flex items-center gap-2 text-xs text-green-500">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ session('success_company') }}
                    </div>
                @endif

                <form action="{{ route('superadmin.settings.company') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block text-[10px] font-medium text-gray-500 dark:text-gray-100 mb-3 uppercase tracking-wider">Logo de la empresa</label>
                        <div class="flex flex-col gap-4">
                            <label for="company_logo" class="relative group cursor-pointer w-32 h-32 border-2 border-dashed border-gray-100 dark:border-gray-800 flex items-center justify-center overflow-hidden hover:border-gray-300 dark:hover:border-gray-600 transition-all">
                                @if($company_logo)
                                    <img src="{{ Storage::url($company_logo) }}" id="logo-preview" alt="Logo" class="w-full h-full object-contain opacity-80 group-hover:opacity-100 transition-opacity">
                                @else
                                    <img id="logo-preview" class="hidden w-full h-full object-contain">
                                    <svg id="logo-placeholder" class="w-8 h-8 text-gray-400 dark:text-gray-100 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                @endif
                                <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <span class="text-[8px] font-bold text-gray-900 dark:text-white uppercase tracking-tighter bg-white/80 dark:bg-black/80 px-2 py-1">Seleccionar</span>
                                </div>
                            </label>
                            <div class="flex items-center gap-3">
                                <label for="company_logo" class="text-[10px] font-bold text-gray-900 dark:text-white cursor-pointer hover:opacity-70 border-b border-gray-900 dark:border-white pb-0.5 transition-all uppercase tracking-tighter">
                                    Cambiar imagen
                                </label>
                                <input type="file" name="company_logo" id="company_logo" accept="image/png,image/jpeg,image-webp"
                                       onchange="previewImage(this, 'logo-preview', 'logo-placeholder')"
                                       class="hidden">
                            </div>
                        </div>
                        @error('company_logo') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-medium text-gray-500 dark:text-gray-100 mb-3 uppercase tracking-wider">Firma digital</label>
                        <div class="flex flex-col gap-4">
                            <label for="company_signature" class="relative group cursor-pointer w-48 h-20 border-2 border-dashed border-gray-100 dark:border-gray-800 flex items-center justify-center overflow-hidden hover:border-gray-300 dark:hover:border-gray-600 transition-all">
                                @if($company_signature)
                                    <img src="{{ Storage::url($company_signature) }}" id="signature-preview" alt="Firma" class="w-full h-full object-contain opacity-80 group-hover:opacity-100 transition-opacity">
                                @else
                                    <img id="signature-preview" class="hidden w-full h-full object-contain">
                                    <svg id="signature-placeholder" class="w-8 h-8 text-gray-400 dark:text-gray-100 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                @endif
                                <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <span class="text-[8px] font-bold text-gray-900 dark:text-white uppercase tracking-tighter bg-white/80 dark:bg-black/80 px-2 py-1">Seleccionar</span>
                                </div>
                            </label>
                            <div class="flex items-center gap-3">
                                <label for="company_signature" class="text-[10px] font-bold text-gray-900 dark:text-white cursor-pointer hover:opacity-70 border-b border-gray-900 dark:border-white pb-0.5 transition-all uppercase tracking-tighter">
                                    Cambiar firma
                                </label>
                                <input type="file" name="company_signature" id="company_signature" accept="image/png,image/jpeg,image-webp"
                                       onchange="previewImage(this, 'signature-preview', 'signature-placeholder')"
                                       class="hidden">
                            </div>
                        </div>
                        @error('company_signature') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="group">
                        <label class="block text-[10px] font-medium text-gray-500 dark:text-gray-100 mb-1 transition-colors group-focus-within:text-gray-900 dark:group-focus-within:text-white">Razón Social</label>
                        <input type="text" name="company_razon_social" value="{{ old('company_razon_social', $company_razon_social) }}"
                               oninput="this.value = this.value.replace(/[^a-zA-Z\sñÑáéíóúÁÉÍÓÚ]/g, '')"
                               class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-gray-900 dark:text-white text-sm py-2 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all placeholder-gray-400 dark:placeholder-gray-100 @error('company_razon_social') border-red-400 @enderror"
                               placeholder="Nombre legal de la empresa">
                        @error('company_razon_social') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="group">
                        <label class="block text-[10px] font-medium text-gray-500 dark:text-gray-100 mb-1 transition-colors group-focus-within:text-gray-900 dark:group-focus-within:text-white">RUC</label>
                        <input type="text" name="company_ruc" value="{{ old('company_ruc', $company_ruc) }}" maxlength="20"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                               class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-gray-900 dark:text-white text-sm py-2 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all placeholder-gray-400 dark:placeholder-gray-100 @error('company_ruc') border-red-400 @enderror"
                               placeholder="Solo números">
                        @error('company_ruc') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="group">
                        <label class="block text-[10px] font-medium text-gray-500 dark:text-gray-100 mb-1 transition-colors group-focus-within:text-gray-900 dark:group-focus-within:text-white">Dirección de Empresa</label>
                        <input type="text" name="company_address" value="{{ old('company_address', $company_address) }}"
                               class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-gray-900 dark:text-white text-sm py-2 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all placeholder-gray-400 dark:placeholder-gray-100 @error('company_address') border-red-400 @enderror"
                               placeholder="Dirección legal">
                        @error('company_address') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="group">
                        <label class="block text-[10px] font-medium text-gray-500 dark:text-gray-100 mb-1 transition-colors group-focus-within:text-gray-900 dark:group-focus-within:text-white">Correo de Empresa</label>
                        <input type="email" name="company_email" value="{{ old('company_email', $company_email) }}"
                               placeholder="contacto@empresa.com"
                               class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-gray-900 dark:text-white text-sm py-2 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all placeholder-gray-400 dark:placeholder-gray-100 @error('company_email') border-red-400 @enderror">
                        @error('company_email') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="group">
                        <label class="block text-[10px] font-medium text-gray-500 dark:text-gray-100 mb-1 transition-colors group-focus-within:text-gray-900 dark:group-focus-within:text-white">Teléfono de Empresa</label>
                        <input type="text" name="company_phone" value="{{ old('company_phone', $company_phone) }}"
                               maxlength="9"
                               placeholder="987654321"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 9)"
                               class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-gray-900 dark:text-white text-sm py-2 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all placeholder-gray-400 dark:placeholder-gray-100 @error('company_phone') border-red-400 @enderror">
                        @error('company_phone') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                                class="inline-flex items-center text-xs font-bold text-gray-900 dark:text-white hover:opacity-70 transition-opacity border-b-2 border-gray-900 dark:border-white pb-1">
                            GUARDAR CONFIGURACIÓN
                        </button>
                    </div>
                </form>
            </div>
        </section>

    </div>
</div>

<script>
function previewImage(input, previewId, placeholderId) {
    const preview = document.getElementById(previewId);
    const placeholder = document.getElementById(placeholderId);
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (placeholder) {
                placeholder.classList.add('hidden');
            }
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
