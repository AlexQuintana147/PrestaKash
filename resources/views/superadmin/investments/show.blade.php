@extends('superadmin.layout')

@section('title', $investment->title)

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8 max-w-4xl mx-auto">

    <div class="mb-8">
        <a href="{{ route('superadmin.investments.index') }}" class="text-xs text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors flex items-center gap-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Volver a inversiones
        </a>
        <div class="flex items-center gap-3 mt-3">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">{{ $investment->title }}</h1>
            <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest
                {{ $investment->status === 'open' ? 'bg-green-100 text-green-600 dark:bg-green-500/10 dark:text-green-400' : '' }}
                {{ $investment->status === 'funded' ? 'bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : '' }}
                {{ $investment->status === 'closed' ? 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' : '' }}
                {{ $investment->status === 'cancelled' ? 'bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-400' : '' }}">
                {{ $investment->status === 'open' ? 'Abierta' : ($investment->status === 'funded' ? 'Financiada' : ($investment->status === 'closed' ? 'Cerrada' : 'Cancelada')) }}
            </span>
        </div>
        <p class="text-[9px] font-mono text-gray-400 mt-1">{{ $investment->code }}</p>
    </div>

    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 text-sm text-green-500">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl p-4">
            <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Monto Objetivo</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ $investment->currency === 'USD' ? '$' : 'S/' }} {{ number_format($investment->target_amount, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl p-4">
            <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Recaudado</p>
            <p class="text-lg font-bold text-green-600 dark:text-green-400 mt-1">{{ $investment->currency === 'USD' ? '$' : 'S/' }} {{ number_format($investment->raised_amount, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl p-4">
            <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Retorno Anual</p>
            <p class="text-lg font-bold text-indigo-600 dark:text-indigo-400 mt-1">{{ $investment->annual_return_rate }}%</p>
        </div>
        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl p-4">
            <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Plazo</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ $investment->term_months }} meses</p>
        </div>
    </div>

    <div class="w-full h-2 bg-gray-100 dark:bg-gray-800 rounded-full mb-8 overflow-hidden">
        <div class="h-full rounded-full transition-all {{ $investment->progress_percent >= 100 ? 'bg-green-500' : 'bg-indigo-500' }}" style="width: {{ min(100, $investment->progress_percent) }}%"></div>
    </div>

    <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl p-6 mb-8">
        <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-3">Descripción</p>
        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $investment->description }}</p>

        @if($investment->risks)
            <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-3 mt-6">Riesgos</p>
            <p class="text-sm text-red-600 dark:text-red-400 leading-relaxed whitespace-pre-line">{{ $investment->risks }}</p>
        @endif

        <div class="grid grid-cols-3 gap-4 mt-6 pt-4 border-t border-gray-100 dark:border-gray-800">
            <div>
                <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Inversión Mínima</p>
                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $investment->currency === 'USD' ? '$' : 'S/' }} {{ number_format($investment->min_investment, 2) }}</p>
            </div>
            <div>
                <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Fecha Límite</p>
                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $investment->deadline ? $investment->deadline->format('d/m/Y') : 'Sin límite' }}</p>
            </div>
            <div>
                <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Creado por</p>
                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $investment->creator->name }}</p>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-widest">Inversores ({{ $investment->investors->count() }})</h2>
        @if($investment->status === 'open')
            <form action="{{ route('superadmin.investments.update', $investment) }}" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="status" value="{{ $investment->isFunded() ? 'funded' : 'closed' }}">
                <button type="submit" class="text-xs font-bold border-b-2 pb-0.5 hover:opacity-70 transition-opacity {{ $investment->isFunded() ? 'text-blue-600 border-blue-600' : 'text-yellow-600 border-yellow-600' }}">
                    {{ $investment->isFunded() ? 'Marcar como Financiada' : 'Cerrar Oportunidad' }}
                </button>
            </form>
        @endif
    </div>

    @if($investment->investors->count())
        <div class="space-y-2">
            @foreach($investment->investors as $investor)
                <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-xl p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                            <span class="text-xs font-bold text-gray-500">{{ strtoupper(substr($investor->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $investor->name }}</p>
                            <p class="text-xs text-gray-400">{{ $investor->email }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $investment->currency === 'USD' ? '$' : 'S/' }} {{ number_format($investor->pivot->amount, 2) }}</p>
                            <span class="text-[8px] font-black uppercase tracking-widest
                                {{ $investor->pivot->status === 'approved' ? 'text-green-600' : '' }}
                                {{ $investor->pivot->status === 'pending' ? 'text-yellow-600' : '' }}
                                {{ $investor->pivot->status === 'rejected' ? 'text-red-600' : '' }}">
                                {{ $investor->pivot->status === 'approved' ? 'Aprobado' : ($investor->pivot->status === 'pending' ? 'Pendiente' : 'Rechazado') }}
                            </span>
                        </div>
                        @if($investor->pivot->status === 'pending')
                            <div class="flex gap-3">
                                <form action="{{ route('superadmin.investments.approve-investor', [$investment, $investor->id]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs font-bold text-green-600 border-b-2 border-green-600 pb-0.5 hover:opacity-70 transition-opacity">Aprobar</button>
                                </form>
                                <form action="{{ route('superadmin.investments.reject-investor', [$investment, $investor->id]) }}" method="POST" onsubmit="return confirmReject(this)">
                                    @csrf
                                    <input type="hidden" name="reason" value="">
                                    <button type="submit" class="text-xs font-bold text-red-600 border-b-2 border-red-600 pb-0.5 hover:opacity-70 transition-opacity">Rechazar</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-sm text-gray-400 dark:text-gray-600 text-center py-8">Aún no hay inversores participando</p>
    @endif

    <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800">
        @if($investment->status !== 'cancelled' && $investment->approvedInvestors()->count() === 0)
            <form action="{{ route('superadmin.investments.destroy', $investment) }}" method="POST" onsubmit="return confirm('¿Eliminar esta oportunidad?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-xs font-bold text-red-600 border-b-2 border-red-600 pb-0.5 hover:opacity-70 transition-opacity">Eliminar Oportunidad</button>
            </form>
        @endif
    </div>
</div>

<script>
function confirmReject(form) {
    const reason = prompt('Motivo del rechazo (opcional):');
    if (reason === null) return false;
    form.querySelector('[name="reason"]').value = reason;
    return true;
}
</script>
@endsection
