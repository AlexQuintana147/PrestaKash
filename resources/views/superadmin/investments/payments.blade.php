@extends('superadmin.layout')

@section('title', 'Pagos de Inversores')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8 max-w-5xl mx-auto">

    <div class="mb-8">
        <a href="{{ route('superadmin.investments.index') }}" class="text-xs text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors flex items-center gap-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Volver a inversiones
        </a>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight mt-3">Pagos de Inversores</h1>
        <p class="text-xs text-gray-400 mt-1">Revisa y confirma los comprobantes de pago de los inversores aprobados</p>
    </div>

    @if(session('success'))
        <div class="mb-6 flex items-center gap-2 text-sm text-green-600 font-bold bg-green-50 dark:bg-green-500/10 p-4 rounded-2xl border border-green-100 dark:border-green-500/20">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-500/10 rounded-2xl border border-red-100 dark:border-red-500/20 text-xs text-red-600 dark:text-red-400">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    @forelse($payments as $opportunity)
        <div class="mb-6 bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl overflow-hidden">
            <div class="p-5 border-b border-gray-100 dark:border-gray-900">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-gray-900 dark:text-white">{{ $opportunity->title }}</h2>
                        <p class="text-[10px] text-gray-400 font-mono">{{ $opportunity->code }}</p>
                    </div>
                    <span class="text-[10px] font-bold text-amber-600 bg-amber-50 dark:bg-amber-500/10 px-2 py-1 rounded-lg">{{ $opportunity->investors->count() }} pago{{ $opportunity->investors->count() !== 1 ? 's' : '' }} pendiente{{ $opportunity->investors->count() !== 1 ? 's' : '' }}</span>
                </div>
            </div>

            @foreach($opportunity->investors as $investor)
                <div class="p-5 border-b border-gray-50 dark:border-gray-900/50 last:border-b-0">
                    <div class="flex flex-col md:flex-row md:items-start gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-500/10 flex items-center justify-center">
                                    <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">{{ strtoupper(substr($investor->name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $investor->name }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $investor->email }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-xs">
                                <div>
                                    <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Monto</p>
                                    <p class="font-bold text-gray-900 dark:text-white">{{ $opportunity->currency === 'USD' ? '$' : 'S/' }} {{ number_format($investor->pivot->amount, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Método</p>
                                    <p class="font-bold text-gray-900 dark:text-white">{{ $investor->pivot->payment_method === 'cash' ? 'Efectivo' : 'Transferencia' }}</p>
                                </div>
                                @if($investor->pivot->operation_number)
                                    <div>
                                        <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">N° Operación</p>
                                        <p class="font-mono font-bold text-gray-900 dark:text-white">{{ $investor->pivot->operation_number }}</p>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Enviado</p>
                                    <p class="text-gray-900 dark:text-white">{{ $investor->pivot->payment_submitted_at ? \Carbon\Carbon::parse($investor->pivot->payment_submitted_at)->format('d/m/Y H:i') : '—' }}</p>
                                </div>
                            </div>

                            @if($investor->pivot->voucher_path)
                                <div class="mt-3">
                                    <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($investor->pivot->voucher_path) }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:opacity-70 transition-opacity">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        Ver comprobante
                                    </a>
                                </div>
                            @endif
                        </div>

                        <div class="flex flex-col gap-2 md:min-w-[200px]">
                            <form action="{{ route('superadmin.investments.confirm-payment', [$opportunity, $investor->id]) }}" method="POST">
                                @csrf
                                <div class="mb-2">
                                    <label class="block text-[9px] text-gray-400 uppercase font-bold tracking-tighter mb-1">Enviar a caja</label>
                                    <select name="cashbox_id" required
                                            class="w-full text-xs bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:border-green-500">
                                        <option value="">Seleccionar caja</option>
                                        @foreach($cashboxes->where('currency', $opportunity->currency) as $cb)
                                            <option value="{{ $cb->id }}">{{ $cb->name }} ({{ $cb->currency }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="w-full text-xs font-bold text-white bg-green-600 hover:bg-green-700 rounded-lg px-4 py-2 transition-colors">
                                    Confirmar Pago
                                </button>
                            </form>

                            <form action="{{ route('superadmin.investments.reject-payment', [$opportunity, $investor->id]) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-xs font-bold text-red-600 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 rounded-lg px-4 py-2 transition-colors">
                                    Rechazar Pago
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @empty
        <div class="text-center py-16">
            <svg class="w-12 h-12 text-gray-200 dark:text-gray-800 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm text-gray-400">No hay pagos pendientes por revisar</p>
        </div>
    @endforelse
</div>
@endsection
