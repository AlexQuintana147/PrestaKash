@extends('superadmin.layout')

@section('title', 'Nueva Oportunidad')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8 max-w-4xl mx-auto">

    <div class="mb-10">
        <a href="{{ route('superadmin.investments.index') }}" class="inline-flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors mb-4">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Volver
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Nueva Oportunidad</h1>
    </div>

    @if($errors->any())
        <div class="mb-8 text-xs text-red-500 space-y-0.5">
            @foreach($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('superadmin.investments.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-12 lg:gap-16">

            <div class="lg:col-span-3 space-y-10">

                <section>
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">Proyecto</p>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Título</label>
                            <input type="text" name="title" value="{{ old('title') }}" required placeholder="Ej: Cartera de préstamos Q2 2026"
                                   class="w-full pl-0 pr-0 py-3 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-xl font-semibold text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-gray-700 focus:outline-none focus:border-green-500 transition-colors">
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Descripción</label>
                            <textarea name="description" rows="3" required placeholder="Describe el proyecto de inversión..."
                                      class="w-full pl-0 pr-0 py-3 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-gray-700 focus:outline-none focus:border-green-500 transition-colors resize-none">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </section>

                <section>
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">Importe y moneda</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Monto objetivo</label>
                            <div class="relative">
                                <span id="currencySymbol" class="absolute left-0 top-1/2 -translate-y-1/2 text-sm font-semibold text-gray-400 dark:text-gray-500 pointer-events-none">S/</span>
                                <input type="text" name="target_amount" value="{{ old('target_amount') }}" required placeholder="0.00" inputmode="decimal"
                                       class="w-full pl-8 pr-0 py-3 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-xl font-semibold text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-gray-700 focus:outline-none focus:border-green-500 transition-colors">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Moneda</label>
                            <div class="flex gap-2 mt-1">
                                <label class="cursor-pointer flex-1">
                                    <input type="radio" name="currency" value="PEN" class="hidden peer" {{ old('currency', 'PEN') === 'PEN' ? 'checked' : '' }}>
                                    <div class="flex items-center justify-center gap-2 py-2.5 text-sm text-gray-500 dark:text-gray-400 border-b-2 border-transparent peer-checked:border-green-500 peer-checked:text-green-600 dark:peer-checked:text-green-400 transition-all">
                                        <span>🇵🇪</span>
                                        <span class="font-medium">Soles</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer flex-1">
                                    <input type="radio" name="currency" value="USD" class="hidden peer" {{ old('currency') === 'USD' ? 'checked' : '' }}>
                                    <div class="flex items-center justify-center gap-2 py-2.5 text-sm text-gray-500 dark:text-gray-400 border-b-2 border-transparent peer-checked:border-green-500 peer-checked:text-green-600 dark:peer-checked:text-green-400 transition-all">
                                        <span>🇺🇸</span>
                                        <span class="font-medium">Dólares</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Inversión mínima</label>
                        <div class="relative">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 text-sm font-semibold text-gray-400 dark:text-gray-500 pointer-events-none min-symbol">S/</span>
                            <input type="text" name="min_investment" value="{{ old('min_investment', '500') }}" required placeholder="500.00" inputmode="decimal"
                                   class="w-full pl-8 pr-0 py-3 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-sm font-semibold text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-gray-700 focus:outline-none focus:border-green-500 transition-colors">
                        </div>
                    </div>
                </section>

                <section>
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">Retorno y plazo</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Tasa de retorno anual</label>
                            <div class="relative">
                                <input type="text" name="annual_return_rate" value="{{ old('annual_return_rate') }}" required placeholder="0.00" inputmode="decimal"
                                       class="w-full pl-0 pr-8 py-3 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-xl font-semibold text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-gray-700 focus:outline-none focus:border-green-500 transition-colors">
                                <span class="absolute right-0 top-1/2 -translate-y-1/2 text-sm font-semibold text-gray-400 dark:text-gray-500 pointer-events-none">%</span>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <label class="text-xs text-gray-500 dark:text-gray-400">Plazo</label>
                                <span id="monthsDisplay" class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">{{ old('term_months', 12) }} meses</span>
                            </div>
                            <input type="range" name="term_months" id="termMonths" min="1" max="36" value="{{ old('term_months', 12) }}"
                                   class="w-full h-1 bg-gray-200 dark:bg-gray-800 rounded-full appearance-none cursor-pointer">
                            <div class="relative h-4 text-[10px] text-gray-300 dark:text-gray-700 mt-1.5">
                                <span class="absolute left-0">1</span>
                                <span class="absolute left-[14.28%] -translate-x-1/2">6</span>
                                <span class="absolute left-[42.85%] -translate-x-1/2">12</span>
                                <span class="absolute left-[71.42%] -translate-x-1/2">24</span>
                                <span class="absolute right-0">36</span>
                            </div>
                        </div>
                    </div>
                </section>

                <section>
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">Riesgos y cierre</p>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Riesgos asociados</label>
                            <textarea name="risks" rows="3" placeholder="Describe los riesgos potenciales..."
                                      class="w-full pl-0 pr-0 py-3 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-gray-700 focus:outline-none focus:border-green-500 transition-colors resize-none">{{ old('risks') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Fecha límite <span class="text-gray-300 dark:text-gray-700">(opcional)</span></label>
                            <div class="relative">
                                <input type="date" name="deadline" value="{{ old('deadline') }}"
                                       class="w-full pl-0 pr-10 py-3 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:border-green-500 transition-colors cursor-pointer appearance-none">
                                <div class="absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="flex gap-6 pt-6">
                    <a href="{{ route('superadmin.investments.index') }}" class="text-sm font-bold text-gray-400 border-b-2 border-gray-300 pb-0.5 hover:opacity-70 transition-opacity">Cancelar</a>
                    <button type="submit" class="text-sm font-bold text-green-600 border-b-2 border-green-600 pb-0.5 hover:opacity-70 transition-opacity">Crear Oportunidad</button>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="lg:sticky lg:top-8 space-y-6">
                    <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl p-6">
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">Vista previa</p>

                        <div class="space-y-4">
                            <div>
                                <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Proyecto</p>
                                <p id="previewTitle" class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">—</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Monto</p>
                                    <p id="previewAmount" class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">S/ 0.00</p>
                                </div>
                                <div>
                                    <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Retorno</p>
                                    <p id="previewReturn" class="text-sm font-bold text-green-600 dark:text-green-400 mt-0.5">0% anual</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Plazo</p>
                                    <p id="previewTerm" class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">12 meses</p>
                                </div>
                                <div>
                                    <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Mín. inversión</p>
                                    <p id="previewMin" class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">S/ 500.00</p>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
                                <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Retorno estimado total</p>
                                <p id="previewEstimated" class="text-lg font-bold text-green-600 dark:text-green-400 mt-0.5">S/ 0.00</p>
                                <p class="text-[9px] text-gray-400 mt-1">Basado en el monto objetivo × tasa × plazo</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-white/5 rounded-2xl p-5">
                        <div class="flex items-start gap-3">
                            <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500 leading-relaxed">La tasa de retorno es anual y fija. El retorno estimado se calcula sobre el monto objetivo completo durante el plazo definido.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
const $ = id => document.getElementById(id);

function parseNum(val) {
    return parseFloat(val.replace(/,/g, '')) || 0;
}

function fmtNum(val) {
    return val.toLocaleString('en', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

function initAmountField(input) {
    input.addEventListener('input', function () {
        let oldVal = this.value;
        let val = oldVal.replace(/\D/g, '');

        if (val.length === 0) {
            val = '0.00';
        } else {
            while (val.length < 3) {
                val = '0' + val;
            }
            let integerPart = val.slice(0, -2);
            let decimalPart = val.slice(-2);
            integerPart = parseInt(integerPart, 10).toString();
            integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            val = integerPart + '.' + decimalPart;
        }

        if (oldVal !== val) {
            this.value = val;
            this.setSelectionRange(val.length, val.length);
        }

        updatePreview();
    });
}

['target_amount', 'min_investment'].forEach(name => {
    const input = document.querySelector('[name="' + name + '"]');
    initAmountField(input);
});

document.querySelector('form').addEventListener('submit', () => {
    ['target_amount', 'min_investment'].forEach(name => {
        const input = document.querySelector('[name="' + name + '"]');
        input.value = input.value.replace(/,/g, '');
    });
});

function updatePreview() {
    const titleVal = document.querySelector('[name="title"]').value;
    const amountVal = parseNum(document.querySelector('[name="target_amount"]').value);
    const rateVal = parseFloat(document.querySelector('[name="annual_return_rate"]').value) || 0;
    const monthsVal = parseInt(document.querySelector('[name="term_months"]').value) || 12;
    const minVal = parseNum(document.querySelector('[name="min_investment"]').value);
    const currency = document.querySelector('[name="currency"]:checked').value;
    const sym = currency === 'USD' ? '$' : 'S/';

    $('previewTitle').textContent = titleVal || '—';
    $('previewAmount').textContent = sym + ' ' + fmtNum(amountVal);
    $('previewReturn').textContent = rateVal + '% anual';
    $('previewTerm').textContent = monthsVal + ' meses';
    $('previewMin').textContent = sym + ' ' + fmtNum(minVal);

    const est = amountVal * rateVal / 100 * monthsVal / 12;
    $('previewEstimated').textContent = sym + ' ' + fmtNum(est);

    $('currencySymbol').textContent = sym;
    document.querySelector('.min-symbol').textContent = sym;
}

document.querySelector('[name="title"]').addEventListener('input', updatePreview);
document.querySelector('[name="annual_return_rate"]').addEventListener('input', updatePreview);
document.querySelectorAll('[name="currency"]').forEach(r => r.addEventListener('change', updatePreview));

const slider = $('termMonths');
const display = $('monthsDisplay');
slider.addEventListener('input', () => {
    display.textContent = slider.value + ' meses';
    updatePreview();
});

updatePreview();
</script>
@endsection
