@extends('client.layout')

@section('title', 'Postular como Inversor')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8 max-w-3xl mx-auto">

    <!-- Header -->
    <div class="mb-10">
        <a href="{{ route('client.invertir') }}" class="inline-flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors mb-6">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver
        </a>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Formulario de Postulación</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Completa los siguientes datos para que nuestro equipo pueda evaluar tu perfil de inversor.</p>
    </div>

    <form action="{{ route('client.invest.store') }}" method="POST" class="space-y-8">
        @csrf

        <!-- Sección: Información Personal y Profesional -->
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="phone" class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Teléfono de contacto</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', auth()->user()->phone) }}" required
                           class="w-full bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:text-white transition-all">
                    @error('phone') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="occupation" class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Ocupación / Profesión</label>
                    <input type="text" name="occupation" id="occupation" value="{{ old('occupation') }}" placeholder="Ej: Empresario, Ingeniero..." required
                           class="w-full bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:text-white transition-all">
                    @error('occupation') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="intended_amount" class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Monto aproximado a invertir (S/)</label>
                    <input type="number" name="intended_amount" id="intended_amount" value="{{ old('intended_amount') }}" placeholder="Mínimo S/ 100" required
                           class="w-full bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:text-white transition-all">
                    @error('intended_amount') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="source_of_funds" class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Origen de los fondos</label>
                    <select name="source_of_funds" id="source_of_funds" required
                            class="w-full bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:text-white transition-all appearance-none">
                        <option value="">Selecciona una opción</option>
                        <option value="Ahorros personales">Ahorros personales</option>
                        <option value="Ganancias de negocio">Ganancias de negocio</option>
                        <option value="Venta de activos">Venta de activos</option>
                        <option value="Herencia">Herencia</option>
                        <option value="Otros">Otros</option>
                    </select>
                    @error('source_of_funds') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="preferred_term" class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Plazo preferido de inversión</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach(['3 meses', '6 meses', '12 meses', 'Indefinido'] as $term)
                        <label class="relative flex items-center justify-center p-4 border border-gray-100 dark:border-gray-900 rounded-2xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-900 transition-all group">
                            <input type="radio" name="preferred_term" value="{{ $term }}" class="sr-only peer" required {{ old('preferred_term') == $term ? 'checked' : '' }}>
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 peer-checked:text-green-600 dark:peer-checked:text-green-400 transition-colors">{{ $term }}</span>
                            <div class="absolute inset-0 border-2 border-transparent peer-checked:border-green-500/50 rounded-2xl pointer-events-none transition-all"></div>
                        </label>
                    @endforeach
                </div>
                @error('preferred_term') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="experience" class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Experiencia previa en inversiones (Opcional)</label>
                <textarea name="experience" id="experience" rows="4" placeholder="Cuéntanos brevemente si has invertido antes..."
                          class="w-full bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:text-white transition-all resize-none">{{ old('experience') }}</textarea>
                @error('experience') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="pt-6 border-t border-gray-100 dark:border-gray-900">
            <button type="submit" class="w-full bg-gray-900 dark:bg-white text-white dark:text-black font-bold py-4 rounded-2xl transition-all hover:scale-[1.01] active:scale-[0.99] shadow-lg shadow-gray-900/10 dark:shadow-none">
                Enviar Solicitud de Inversor
            </button>
            <p class="text-[10px] text-center text-gray-400 dark:text-gray-600 mt-4 uppercase tracking-widest">
                Al enviar, confirmas que la información proporcionada es verídica.
            </p>
        </div>
    </form>

</div>
@endsection
