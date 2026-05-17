@extends('superadmin.layout')

@section('title', 'Nueva Oportunidad de Inversión')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8 max-w-3xl mx-auto">

    <div class="mb-8">
        <a href="{{ route('superadmin.investments.index') }}" class="text-xs text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors flex items-center gap-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Volver a inversiones
        </a>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight mt-3">Nueva Oportunidad</h1>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-500/10 rounded-2xl text-sm text-red-600 dark:text-red-400">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('superadmin.investments.store') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Título del Proyecto</label>
            <input type="text" name="title" value="{{ old('title') }}" required placeholder="Ej: Cartera de préstamos Q2 2026"
                   class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl px-4 py-3 text-sm font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-900 dark:focus:ring-white transition-all placeholder:text-gray-300 dark:placeholder:text-gray-700">
        </div>

        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Descripción</label>
            <textarea name="description" rows="4" required placeholder="Describe el proyecto de inversión..."
                      class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-900 dark:focus:ring-white transition-all resize-none placeholder:text-gray-300 dark:placeholder:text-gray-700">{{ old('description') }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Monto Objetivo</label>
                <input type="number" name="target_amount" value="{{ old('target_amount') }}" required min="1" step="0.01"
                       class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl px-4 py-3 text-sm font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-900 dark:focus:ring-white transition-all placeholder:text-gray-300 dark:placeholder:text-gray-700">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Inversión Mínima</label>
                <input type="number" name="min_investment" value="{{ old('min_investment', 500) }}" required min="1" step="0.01"
                       class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl px-4 py-3 text-sm font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-900 dark:focus:ring-white transition-all placeholder:text-gray-300 dark:placeholder:text-gray-700">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Moneda</label>
                <select name="currency" class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl px-4 py-3 text-sm font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-900 dark:focus:ring-white transition-all">
                    <option value="PEN" {{ old('currency') === 'PEN' ? 'selected' : '' }}>Soles (PEN)</option>
                    <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>Dólares (USD)</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Retorno Anual (%)</label>
                <input type="number" name="annual_return_rate" value="{{ old('annual_return_rate') }}" required min="0" max="100" step="0.01"
                       class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl px-4 py-3 text-sm font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-900 dark:focus:ring-white transition-all placeholder:text-gray-300 dark:placeholder:text-gray-700">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Plazo (meses)</label>
                <input type="number" name="term_months" value="{{ old('term_months') }}" required min="1"
                       class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl px-4 py-3 text-sm font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-900 dark:focus:ring-white transition-all placeholder:text-gray-300 dark:placeholder:text-gray-700">
            </div>
        </div>

        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Riesgos Asociados</label>
            <textarea name="risks" rows="3" placeholder="Describe los riesgos potenciales..."
                      class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-900 dark:focus:ring-white transition-all resize-none placeholder:text-gray-300 dark:placeholder:text-gray-700">{{ old('risks') }}</textarea>
        </div>

        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Fecha Límite (opcional)</label>
            <input type="date" name="deadline" value="{{ old('deadline') }}"
                   class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl px-4 py-3 text-sm font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-900 dark:focus:ring-white transition-all">
        </div>

        <div class="flex gap-4 pt-4">
            <a href="{{ route('superadmin.investments.index') }}" class="text-sm font-bold text-gray-400 border-b-2 border-gray-300 pb-0.5 hover:opacity-70 transition-opacity">Cancelar</a>
            <button type="submit" class="text-sm font-bold text-green-600 border-b-2 border-green-600 pb-0.5 hover:opacity-70 transition-opacity">Crear Oportunidad</button>
        </div>
    </form>
</div>
@endsection
