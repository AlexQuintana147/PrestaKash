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
                                   pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ0-9\s]+"
                                   oninput="this.value=this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ0-9\s]/g,'')"
                                   class="w-full pl-0 pr-0 py-3 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-xl font-semibold text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-gray-700 focus:outline-none focus:border-green-500 transition-colors">
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Descripción</label>
                            <textarea name="description" rows="3" required placeholder="Describe el proyecto de inversión..."
                                      oninput="this.value=this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ0-9\s]/g,'')"
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
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Tasa de retorno mensual</label>
                            <div class="relative">
                                <input type="number" name="annual_return_rate" id="returnRateInput" step="0.01" min="0" max="100"
                                       value="{{ old('annual_return_rate', '2.00') }}"
                                       onkeypress="return (event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46)"
                                       oninput="let start = this.selectionStart;
                                                let oldVal = this.value;
                                                let val = oldVal.replace(/[^0-9.]/g, '');
                                                let parts = val.split('.');
                                                if(parts[0].length > 2) {
                                                    val = parts[0].slice(0, 2) + '.' + parts[0].slice(2) + (parts[1] || '');
                                                }
                                                parts = val.split('.');
                                                if(parts[1] && parts[1].length > 2) {
                                                    val = parts[0] + '.' + parts[1].slice(0, 2);
                                                }
                                                if(val.length > 1 && val.startsWith('0') && val[1] !== '.') val = val.replace(/^0+/, '');
                                                if (oldVal !== val) {
                                                    this.value = val;
                                                    this.setSelectionRange(start, start);
                                                }
                                                updatePreview();"
                                       class="w-full pl-0 pr-8 py-3 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-xl font-semibold text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-gray-700 focus:outline-none focus:border-green-500 transition-colors text-right">
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
                                      oninput="this.value=this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ0-9\s]/g,'')"
                                      class="w-full pl-0 pr-0 py-3 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-gray-700 focus:outline-none focus:border-green-500 transition-colors resize-none">{{ old('risks') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Fecha límite <span class="text-gray-300 dark:text-gray-700">(opcional)</span></label>
                            <div class="relative cursor-pointer" onclick="document.querySelector('input[name=\'deadline\']').showPicker()">
                                <input type="date" name="deadline" value="{{ old('deadline') }}"
                                       class="w-full pl-0 pr-0 py-3 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:border-green-500 transition-colors cursor-pointer appearance-none">
                            </div>
                        </div>
                    </div>
                </section>

                <section>
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest">Cuentas Bancarias para Depósitos</p>
                        <button type="button" onclick="addBankAccount()" class="text-xs font-bold text-green-600 border-b border-green-600 pb-0.5 hover:opacity-70 transition-opacity">+ Agregar cuenta</button>
                    </div>
                    
                    <div id="bankAccountsContainer" class="space-y-6">
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
                                <p class="text-[9px] text-gray-400 mt-1">Monto objetivo × tasa mensual × plazo en meses</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-white/5 rounded-2xl p-5">
                        <div class="flex items-start gap-3">
                            <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500 leading-relaxed">La tasa de retorno es mensual y fija. El retorno estimado se calcula sobre el monto objetivo completo durante el plazo definido.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
select option {
    background-color: white !important;
    color: #111827 !important;
}

html.dark select option {
    background-color: #1f2937 !important;
    color: #f9fafb !important;
}
</style>

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
    $('previewReturn').textContent = rateVal + '% mensual';
    $('previewTerm').textContent = monthsVal + ' meses';
    $('previewMin').textContent = sym + ' ' + fmtNum(minVal);

    const est = amountVal * rateVal / 100 * monthsVal;
    $('previewEstimated').textContent = sym + ' ' + fmtNum(est);

    $('currencySymbol').textContent = sym;
    document.querySelector('.min-symbol').textContent = sym;
}

document.querySelector('[name="title"]').addEventListener('input', updatePreview);
document.querySelectorAll('[name="currency"]').forEach(r => r.addEventListener('change', updatePreview));

const slider = $('termMonths');
const display = $('monthsDisplay');
slider.addEventListener('input', () => {
    display.textContent = slider.value + ' meses';
    updatePreview();
});

updatePreview();

let bankAccountIndex = 0;

function addBankAccount() {
    const idx = bankAccountIndex++;
    const container = document.getElementById('bankAccountsContainer');
    const div = document.createElement('div');
    div.className = 'bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 space-y-4 relative';
    div.id = 'bankAccount-' + idx;
    
    div.innerHTML = `
        <button type="button" onclick="removeBankAccount(${idx})" class="absolute top-3 right-3 text-gray-400 hover:text-red-500 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Nombre del Banco *</label>
            <select name="bank_name[${idx}]" required
                    class="w-full pl-0 pr-0 py-2 bg-white dark:bg-gray-900 border-0 border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:border-green-500 transition-colors cursor-pointer">
                <option value="">Seleccionar banco...</option>
                <option value="BCP">Banco de Crédito del Perú (BCP)</option>
                <option value="Interbank">Interbank</option>
                <option value="BBVA">BBVA Perú</option>
                <option value="Scotiabank">Scotiabank Perú</option>
                <option value="Banco de la Nación">Banco de la Nación</option>
                <option value="BanBif">BanBif</option>
                <option value="Banco Falabella">Banco Falabella</option>
                <option value="Ripley Bank">Ripley Bank</option>
                <option value="Alfin Banco">Alfin Banco</option>
                <option value="Banco GNB">Banco GNB</option>
                <option value="Citibank">Citibank Perú</option>
                <option value="Banco Pichincha">Banco Pichincha</option>
                <option value="Banco Santander">Banco Santander</option>
                <option value="Banco Ripley">Banco Ripley</option>
                <option value="Caja Arequipa">Caja Arequipa</option>
                <option value="Caja Cusco">Caja Cusco</option>
                <option value="Caja Huancayo">Caja Huancayo</option>
                <option value="Caja Ica">Caja Ica</option>
                <option value="Caja Piura">Caja Piura</option>
                <option value="Caja Sullana">Caja Sullana</option>
                <option value="Caja Tacna">Caja Tacna</option>
                <option value="Caja Trujillo">Caja Trujillo</option>
                <option value="CMAC Arequipa">CMAC Arequipa</option>
                <option value="CMAC Cusco">CMAC Cusco</option>
                <option value="CMAC Huancayo">CMAC Huancayo</option>
                <option value="CMAC Ica">CMAC Ica</option>
                <option value="CMAC Piura">CMAC Piura</option>
                <option value="CMAC Sullana">CMAC Sullana</option>
                <option value="CMAC Tacna">CMAC Tacna</option>
                <option value="CMAC Trujillo">CMAC Trujillo</option>
                <option value="Financiera Confianza">Financiera Confianza</option>
                <option value="Financiera Efectiva">Financiera Efectiva</option>
                <option value="Financiera Qali Qali">Financiera Qali Qali</option>
            </select>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Número de Cuenta *</label>
                <input type="text" name="account_number[${idx}]" required placeholder="000-000000-0" maxlength="30"
                       oninput="this.value=this.value.replace(/\D/g,'')"
                       class="w-full pl-0 pr-0 py-2 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-gray-700 focus:outline-none focus:border-green-500 transition-colors">
            </div>
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">CCI *</label>
                <input type="text" name="cci[${idx}]" required placeholder="000-000-000000-00" maxlength="30"
                       oninput="this.value=this.value.replace(/\D/g,'')"
                       class="w-full pl-0 pr-0 py-2 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-sm text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-gray-700 focus:outline-none focus:border-green-500 transition-colors">
            </div>
        </div>
    `;
    
    container.appendChild(div);
}

function removeBankAccount(idx) {
    const el = document.getElementById('bankAccount-' + idx);
    if (el) el.remove();
}

addBankAccount();

document.querySelector('form').addEventListener('submit', function() {
    ['target_amount', 'min_investment'].forEach(function(name) {
        const input = document.querySelector('[name="' + name + '"]');
        input.value = input.value.replace(/,/g, '');
    });
});

</script>
@endsection
