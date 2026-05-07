@extends('client.layout')

@section('title', 'Postular como Inversor')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8 max-w-3xl mx-auto">

    <!-- Header -->
    <div class="mb-12">
        <a href="{{ route('client.invertir') }}" class="inline-flex items-center gap-2 text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest hover:text-gray-900 dark:hover:text-white transition-colors mb-8">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver
        </a>
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white tracking-tight">Postulación</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-3 leading-relaxed">Completa tu perfil para unirte a nuestra red de inversión.</p>
    </div>

    <form action="{{ route('client.invest.store') }}" method="POST" class="space-y-8">
        @csrf

        <!-- Sección: Información -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-8">
            
            <div class="space-y-1">
                <label for="phone" class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Contacto</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', auth()->user()->phone) }}" required
                       placeholder="Tu número de teléfono"
                       maxlength="9"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 9)"
                       class="w-full bg-transparent border-b border-gray-100 dark:border-gray-900 rounded-none px-0 py-2 text-sm focus:outline-none focus:border-green-500 dark:text-white transition-all placeholder:text-gray-200 dark:placeholder:text-gray-800">
                @error('phone') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-1">
                <label for="occupation" class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Ocupación</label>
                <input type="text" name="occupation" id="occupation" value="{{ old('occupation') }}" required
                       placeholder="Ej: Empresario, Ingeniero..."
                       oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ ]/g, '')"
                       class="w-full bg-transparent border-b border-gray-100 dark:border-gray-900 rounded-none px-0 py-2 text-sm focus:outline-none focus:border-green-500 dark:text-white transition-all placeholder:text-gray-200 dark:placeholder:text-gray-800">
                @error('occupation') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-1">
                <label for="intended_amount" class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Monto Estimado (S/)</label>
                <input type="number" name="intended_amount" id="intended_amount" value="{{ old('intended_amount') }}" required
                       placeholder="Mínimo S/ 100"
                       class="w-full bg-transparent border-b border-gray-100 dark:border-gray-900 rounded-none px-0 py-2 text-sm focus:outline-none focus:border-green-500 dark:text-white transition-all placeholder:text-gray-200 dark:placeholder:text-gray-800">
                @error('intended_amount') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-1">
                <label for="source_of_funds" class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Origen de Fondos</label>
                <div class="relative group">
                    <select name="source_of_funds" id="source_of_funds" required
                            class="w-full bg-transparent border-b border-gray-100 dark:border-gray-900 rounded-none px-0 py-2.5 text-sm focus:outline-none focus:border-green-500 dark:text-white transition-all appearance-none cursor-pointer relative z-10">
                        <option value="" disabled selected class="dark:bg-gray-950">Selecciona una opción</option>
                        <option value="Ahorros personales" class="bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 py-2">Ahorros personales</option>
                        <option value="Ganancias de negocio" class="bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 py-2">Ganancias de negocio</option>
                        <option value="Venta de activos" class="bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 py-2">Venta de activos</option>
                        <option value="Herencia" class="bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 py-2">Herencia</option>
                        <option value="Otros" class="bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 py-2">Otros</option>
                    </select>
                    <div class="absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none text-gray-300 group-hover:text-green-500 transition-colors z-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
                @error('source_of_funds') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="space-y-3">
            <label class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Plazo de Inversión</label>
            <div class="flex flex-wrap gap-6">
                @foreach(['3 meses', '6 meses', '12 meses', 'Indefinido'] as $term)
                    <label class="relative group cursor-pointer">
                        <input type="radio" name="preferred_term" value="{{ $term }}" class="sr-only peer" required {{ old('preferred_term') == $term ? 'checked' : '' }}>
                        <div class="text-[11px] font-bold text-gray-300 dark:text-gray-700 peer-checked:text-green-600 dark:peer-checked:text-green-400 transition-all duration-300">
                            {{ $term }}
                        </div>
                        <div class="absolute -bottom-1 left-0 w-0 h-0.5 bg-green-600 dark:bg-green-400 peer-checked:w-full transition-all duration-300"></div>
                    </label>
                @endforeach
            </div>
            @error('preferred_term') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-1">
            <label for="experience" class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Experiencia (Opcional)</label>
            <textarea name="experience" id="experience" rows="2" placeholder="Cuéntanos sobre tus inversiones previas..."
                      class="w-full bg-transparent border-b border-gray-100 dark:border-gray-900 rounded-none px-0 py-2 text-sm focus:outline-none focus:border-green-500 dark:text-white transition-all resize-none placeholder:text-gray-200 dark:placeholder:text-gray-800">{{ old('experience') }}</textarea>
            @error('experience') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
        </div>

        <div class="pt-4 flex flex-col items-start">
            <button type="submit" class="group relative py-2 text-sm font-bold text-gray-900 dark:text-white transition-all hover:text-green-600 dark:hover:text-green-400">
                Enviar Solicitud
                <div class="absolute bottom-0 left-0 w-full h-0.5 bg-gray-900 dark:bg-white group-hover:bg-green-600 dark:group-hover:bg-green-400 transition-colors"></div>
            </button>
            <p class="text-[9px] text-gray-400 dark:text-gray-600 mt-4 uppercase tracking-[0.2em] font-medium">
                La información será tratada con confidencialidad.
            </p>
        </div>
    </form>

</div>
@endsection
