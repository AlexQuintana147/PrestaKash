@extends('client.layout')

@section('title', $opportunity->title)

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8 max-w-3xl mx-auto">

    <div class="mb-8">
        <a href="{{ route('client.investments.index') }}" class="text-xs text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors flex items-center gap-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Volver a oportunidades
        </a>
        <div class="flex items-center gap-3 mt-3">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">{{ $opportunity->title }}</h1>
        </div>
        <p class="text-[9px] font-mono text-gray-400 mt-1">{{ $opportunity->code }}</p>
    </div>

    @if(session('error'))
        <div class="mb-6 flex items-center gap-2 text-sm text-red-500 font-bold bg-red-50 dark:bg-red-500/10 p-4 rounded-2xl border border-red-100 dark:border-red-500/20">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl p-4">
            <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Monto Objetivo</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($opportunity->target_amount, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl p-4">
            <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Retorno Anual</p>
            <p class="text-lg font-bold text-green-600 dark:text-green-400 mt-1">{{ $opportunity->annual_return_rate }}%</p>
        </div>
        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl p-4">
            <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Plazo</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ $opportunity->term_months }} meses</p>
        </div>
        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl p-4">
            <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Inversión Mínima</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($opportunity->min_investment, 2) }}</p>
        </div>
    </div>

    <div class="w-full h-2 bg-gray-100 dark:bg-gray-800 rounded-full mb-2 overflow-hidden">
        <div class="h-full rounded-full bg-indigo-500" style="width: {{ min(100, $opportunity->progress_percent) }}%"></div>
    </div>
    <p class="text-xs text-gray-400 mb-8">{{ $opportunity->progress_percent }}% financiado · {{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($opportunity->remaining_amount, 2) }} disponible</p>

    <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl p-6 mb-8">
        <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-3">Descripción del Proyecto</p>
        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $opportunity->description }}</p>

        @if($opportunity->risks)
            <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-800">
                <p class="text-[10px] font-bold text-red-400 uppercase tracking-widest mb-3">Riesgos Asociados</p>
                <p class="text-sm text-red-600 dark:text-red-400 leading-relaxed whitespace-pre-line">{{ $opportunity->risks }}</p>
            </div>
        @endif

        <div class="grid grid-cols-2 gap-4 mt-6 pt-4 border-t border-gray-100 dark:border-gray-800">
            <div>
                <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Fecha Límite</p>
                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $opportunity->deadline ? $opportunity->deadline->format('d/m/Y') : 'Sin límite' }}</p>
            </div>
            <div>
                <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Retorno Estimado</p>
                <p class="text-sm font-bold text-green-600 dark:text-green-400">{{ $opportunity->estimated_return }}% en {{ $opportunity->term_months }} meses</p>
            </div>
        </div>
    </div>

    @if($existingRequest)
        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl p-6">
            <div class="flex items-center gap-3">
                @if($existingRequest->pivot->status === 'pending')
                    <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <p class="text-sm font-bold text-yellow-600 dark:text-yellow-400">Solicitud Pendiente</p>
                        <p class="text-xs text-gray-400">Tu solicitud de S/ {{ number_format($existingRequest->pivot->amount, 2) }} está siendo revisada por el administrador.</p>
                    </div>
                @elseif($existingRequest->pivot->status === 'approved')
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <div>
                        <p class="text-sm font-bold text-green-600 dark:text-green-400">Solicitud Aprobada</p>
                        <p class="text-xs text-gray-400">Tu inversión de S/ {{ number_format($existingRequest->pivot->amount, 2) }} ha sido aprobada.</p>
                    </div>
                @else
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <div>
                        <p class="text-sm font-bold text-red-600 dark:text-red-400">Solicitud Rechazada</p>
                        @if($existingRequest->pivot->admin_notes)
                            <p class="text-xs text-red-500 dark:text-red-400 mt-1">{{ $existingRequest->pivot->admin_notes }}</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        @if($existingRequest->pivot->status === 'rejected')
        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl p-6">
            <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">Intentar de Nuevo</p>

            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 dark:bg-red-500/10 rounded-xl text-xs text-red-600 dark:text-red-400">
                    @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif

            <form action="{{ route('client.investments.apply', $opportunity) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Monto a Invertir</label>
                    <div class="relative">
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 text-sm font-bold text-gray-400 pointer-events-none">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}</span>
                        <input type="text" name="amount" id="investAmount" required inputmode="decimal"
                               placeholder="{{ number_format($opportunity->min_investment, 2) }}"
                               class="w-full pl-8 pr-0 py-3 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-xl font-semibold text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-gray-700 focus:outline-none focus:border-green-500 transition-colors">
                    </div>
                    <input type="hidden" id="minInvestment" value="{{ $opportunity->min_investment }}">
                    <input type="hidden" id="maxInvestment" value="{{ $opportunity->remaining_amount }}">
                    <p class="text-[10px] text-gray-400 mt-1">Mínimo: {{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($opportunity->min_investment, 2) }} · Máximo: {{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($opportunity->remaining_amount, 2) }}</p>
                </div>
                <button type="submit" class="text-sm font-bold text-green-600 border-b-2 border-green-600 pb-0.5 hover:opacity-70 transition-opacity">
                    Enviar Nueva Solicitud
                </button>
            </form>
        </div>
        @endif
    @else
        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl p-6">
            <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">Participar en esta Oportunidad</p>

            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 dark:bg-red-500/10 rounded-xl text-xs text-red-600 dark:text-red-400">
                    @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif

            <form action="{{ route('client.investments.apply', $opportunity) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Monto a Invertir</label>
                    <div class="relative">
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 text-sm font-bold text-gray-400 pointer-events-none">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }}</span>
                        <input type="text" name="amount" id="investAmount" required inputmode="decimal"
                               placeholder="{{ number_format($opportunity->min_investment, 2) }}"
                               class="w-full pl-8 pr-0 py-3 bg-transparent border-0 border-b border-gray-200 dark:border-gray-800 text-xl font-semibold text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-gray-700 focus:outline-none focus:border-green-500 transition-colors">
                    </div>
                    <input type="hidden" id="minInvestment" value="{{ $opportunity->min_investment }}">
                    <input type="hidden" id="maxInvestment" value="{{ $opportunity->remaining_amount }}">
                    <p class="text-[10px] text-gray-400 mt-1">Mínimo: {{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($opportunity->min_investment, 2) }} · Máximo: {{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($opportunity->remaining_amount, 2) }}</p>
                </div>
                <button type="submit" class="text-sm font-bold text-green-600 border-b-2 border-green-600 pb-0.5 hover:opacity-70 transition-opacity">
                    Enviar Solicitud
                </button>
            </form>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const investAmount = document.getElementById('investAmount');
    if (!investAmount) return;

    const minVal = parseFloat(document.getElementById('minInvestment').value);
    const maxVal = parseFloat(document.getElementById('maxInvestment').value);

    function formatAmount(num) {
        let integerPart = Math.floor(num).toString();
        let decimalPart = Math.round((num - Math.floor(num)) * 100).toString().padStart(2, '0');
        integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return integerPart + '.' + decimalPart;
    }

    investAmount.addEventListener('input', function () {
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
    });

    investAmount.addEventListener('blur', function () {
        let rawNum = parseFloat(this.value.replace(/,/g, ''));
        if (isNaN(rawNum) || rawNum < minVal) {
            this.value = formatAmount(minVal);
        } else if (rawNum > maxVal) {
            this.value = formatAmount(maxVal);
        }
    });

    investAmount.closest('form').addEventListener('submit', function () {
        investAmount.value = investAmount.value.replace(/,/g, '');
    });
});
</script>
@endsection
