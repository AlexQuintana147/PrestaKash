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
            <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Retorno Mensual</p>
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

        @if($investment->bank_accounts)
            <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-3 mt-6">Cuentas Bancarias</p>
            <div class="space-y-2">
                @foreach($investment->bank_accounts as $acc)
                    <div class="p-3 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                        <p class="text-xs font-bold text-gray-900 dark:text-white">{{ $acc['bank'] }}</p>
                        <div class="flex gap-6 mt-1">
                            <span class="text-[10px] text-gray-400">Cta: <span class="font-mono text-gray-900 dark:text-white">{{ $acc['account_number'] }}</span></span>
                            <span class="text-[10px] text-gray-400">CCI: <span class="font-mono text-gray-900 dark:text-white">{{ $acc['cci'] }}</span></span>
                        </div>
                    </div>
                @endforeach
            </div>
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
        <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-widest">Inversores ({{ $investors->total() }})</h2>
        @if($investment->status === 'open')
            <button type="button" onclick="openCloseOpportunityModal()" class="text-xs font-bold border-b-2 pb-0.5 hover:opacity-70 transition-opacity {{ $investment->isFunded() ? 'text-blue-600 border-blue-600' : 'text-yellow-600 border-yellow-600' }}">
                {{ $investment->isFunded() ? 'Marcar como Financiada' : 'Cerrar Oportunidad' }}
            </button>
        @endif
    </div>

    @if($investors->count())
        <div class="divide-y divide-gray-100 dark:divide-gray-900">
            @foreach($investors as $investor)
                @php
                    $selfie = $investor->documents?->selfie;
                    $photo = $selfie ? Storage::url($selfie) : ($investor->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($investor->name) . '&background=e5e7eb&color=6b7280');
                    $investorData = json_encode([
                        'id' => $investor->id,
                        'name' => $investor->name,
                        'email' => $investor->email,
                        'phone' => $investor->phone ?? '—',
                        'dni' => $investor->dni ?? '—',
                        'ruc' => $investor->ruc ?? '—',
                        'person_type' => $investor->person_type ?? '—',
                        'business_name' => $investor->business_name ?? '—',
                        'department' => $investor->department ? \App\Models\UbigeoDepartment::where('id', str_pad($investor->department, 2, '0', STR_PAD_LEFT))->value('name') ?? '—' : '—',
                        'province' => $investor->province ? \App\Models\UbigeoProvince::where('id', str_pad($investor->province, 4, '0', STR_PAD_LEFT))->value('name') ?? '—' : '—',
                        'district' => $investor->district ? \App\Models\UbigeoDistrict::where('id', str_pad($investor->district, 6, '0', STR_PAD_LEFT))->value('name') ?? '—' : '—',
                        'address' => $investor->address ?? '—',
                        'occupation' => $investor->occupation ?? '—',
                        'selfie' => $selfie ? Storage::url($selfie) : null,
                        'avatar' => $investor->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($investor->name),
                        'status' => $investor->pivot->status,
                        'amount' => $investment->currency === 'USD' ? '$' : 'S/' . ' ' . number_format($investor->pivot->amount, 2),
                        'currency' => $investment->currency,
                        'date' => $investor->pivot->created_at->format('d/m/Y'),
                        'referral_code' => $investor->referral_code ?? '—',
                        'referred_by' => $investor->referred_by ?? null,
                    ]);
                @endphp
                <div id="investor-{{ $investor->id }}" class="py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4 transition-all duration-500 rounded-2xl px-2">
                    <div class="flex items-center gap-4 min-w-0">
                        <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-900 flex items-center justify-center overflow-hidden flex-shrink-0 border border-gray-200 dark:border-gray-800">
                            <img src="{{ $photo }}" class="w-full h-full object-cover">
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $investor->name }}</p>
                                <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest {{ $investor->person_type === 'juridica' ? 'bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' }}">
                                    {{ $investor->person_type === 'juridica' ? 'JURÍDICA' : 'NATURAL' }}
                                </span>
                                <button onclick='openInvestorModal({{ $investorData }})' class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 text-[10px]">
                                <span class="text-gray-500 dark:text-gray-500">
                                    @if($investor->person_type === 'juridica')
                                        RUC: <span class="font-bold text-gray-900 dark:text-white">{{ $investor->ruc ?? '—' }}</span>
                                    @else
                                        DNI: <span class="font-bold text-gray-900 dark:text-white">{{ $investor->dni ?? '—' }}</span>
                                    @endif
                                </span>
                                <a href="mailto:{{ $investor->email }}" class="flex items-center gap-1 bg-blue-500 hover:bg-blue-600 transition-colors px-2 py-1 text-white font-bold">
                                    {{ $investor->email }}
                                    <svg class="w-3 h-3 flex-shrink-0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><rect x="7.904" y="119.947" style="fill:#5EBAE7;" width="496.195" height="272.109"/><polygon style="fill:#36A9E1;" points="256,280.01 23.91,127.951 7.904,135.954 256,312.022 352.037,223.987 "/><path style="fill:#1D1D1B;" d="M0,112.043v287.913h512V112.043H0z M477.266,127.851L256,270.603L34.733,127.851H477.266z M15.807,384.149V134.452L256,289.414l240.193-154.962v249.696H15.807V384.149z"/></svg>
                                </a>
                                @if($investor->phone)
                                    <a href="https://wa.me/51{{ preg_replace('/\D/', '', $investor->phone) }}" target="_blank" class="flex items-center gap-1 bg-green-500 hover:bg-green-600 transition-colors px-2 py-1 text-white font-bold">
                                        {{ $investor->phone }}
                                        <svg class="w-3 h-3 flex-shrink-0" viewBox="0 0 32 32" fill="none"><path d="M28 16C28 22.6274 22.6274 28 16 28C13.4722 28 11.1269 27.2184 9.19266 25.8837L5.09091 26.9091L6.16576 22.8784C4.80092 20.9307 4 18.5589 4 16C4 9.37258 9.37258 4 16 4C22.6274 4 28 9.37258 28 16Z" fill="url(#wssp_inv_list)"/><path fill-rule="evenodd" clip-rule="evenodd" d="M16 30C23.732 30 30 23.732 30 16C30 8.26801 23.732 2 16 2C8.26801 2 2 8.26801 2 16C2 18.5109 2.661 20.8674 3.81847 22.905L2 30L9.31486 28.3038C11.1894 29.3854 13.5789 30 16 30ZM16 27.8462C22.5425 27.8462 27.8462 22.5425 27.8462 16C27.8462 9.45755 22.5425 4.15385 16 4.15385C9.45755 4.15385 4.15385 9.45755 4.15385 16C4.15385 18.5261 4.9445 20.8675 6.29184 22.7902L5.23077 26.7692L9.27993 25.7569C11.1894 27.0746 13.5046 27.8462 16 27.8462Z" fill="white"/><path d="M12.5 9.49989C12.1672 8.83131 11.6565 8.8905 11.1407 8.8905C10.2188 8.8905 8.78125 9.99478 8.78125 12.05C8.78125 13.7343 9.52345 15.578 12.0244 18.3361C14.438 20.9979 17.6094 22.3748 20.2422 22.3279C22.875 22.2811 23.4167 20.0154 23.4167 19.2503C23.4167 18.9112 23.2062 18.742 23.0613 18.696C22.1641 18.2654 20.5093 17.4631 20.1328 17.3124C19.7563 17.1617 19.5597 17.3656 19.4375 17.4765C19.0961 17.8018 18.4193 18.7608 18.1875 18.9765C17.9558 19.1922 17.6103 19.083 17.4665 19.0015C16.9374 18.7892 15.5029 18.1511 14.3595 17.0426C12.9453 15.6718 12.8623 15.2001 12.5959 14.7803C12.3828 14.4444 12.5392 14.2384 12.6172 14.1483C12.9219 13.7968 13.3426 13.254 13.5313 12.9843C13.7199 12.7145 13.5702 12.305 13.4803 12.05C13.0938 10.953 12.7663 10.0347 12.5 9.49989Z" fill="white"/><defs><linearGradient id="wssp_inv_list" x1="26.5" y1="7" x2="4" y2="28" gradientUnits="userSpaceOnUse"><stop stop-color="#5BD066"/><stop offset="1" stop-color="#27B43E"/></linearGradient></defs></svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 md:gap-6 flex-shrink-0 pl-16 md:pl-0">
                        <div class="text-right">
                            <div class="flex items-center gap-2 justify-end mb-1">
                                <p class="text-lg font-black text-gray-900 dark:text-white tracking-tighter">{{ $investment->currency === 'USD' ? '$' : 'S/' }} {{ number_format($investor->pivot->amount, 2) }}</p>
                                <span class="text-[8px] font-black uppercase tracking-widest px-2 py-0.5 rounded
                                    {{ $investor->pivot->status === 'approved' ? 'bg-green-100 text-green-600 dark:bg-green-500/10 dark:text-green-400' : '' }}
                                    {{ $investor->pivot->status === 'pending' ? 'bg-yellow-100 text-yellow-600 dark:bg-yellow-500/10 dark:text-yellow-400' : '' }}
                                    {{ $investor->pivot->status === 'rejected' ? 'bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-400' : '' }}">
                                    {{ $investor->pivot->status === 'approved' ? 'Aprobado' : ($investor->pivot->status === 'pending' ? 'Pendiente' : 'Rechazado') }}
                                </span>
                                @if($investor->pivot->status === 'approved' && $investor->pivot->payment_status)
                                    <span class="text-[8px] font-black uppercase tracking-widest px-2 py-0.5 rounded
                                        {{ $investor->pivot->payment_status === 'pending' ? 'bg-amber-100 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400' : '' }}
                                        {{ $investor->pivot->payment_status === 'confirmed' ? 'bg-green-100 text-green-600 dark:bg-green-500/10 dark:text-green-400' : '' }}
                                        {{ $investor->pivot->payment_status === 'rejected' ? 'bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-400' : '' }}
                                        {{ $investor->pivot->payment_status === 'expired' ? 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' : '' }}">
                                        {{ $investor->pivot->payment_status === 'pending' ? 'Pago Pend.' : ($investor->pivot->payment_status === 'confirmed' ? 'Pago OK' : ($investor->pivot->payment_status === 'rejected' ? 'Pago Rech.' : 'Pago Exp.')) }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-[10px] text-gray-400 dark:text-gray-600 font-medium">{{ $investor->pivot->created_at->format('d/m/Y') }}</p>
                        </div>
                        @if($investor->pivot->status === 'pending')
                            <div class="flex gap-3">
                                <button type="button" onclick="openApproveInvestorModal({{ $investment->id }}, {{ $investor->id }}, '{{ $investor->name }}', '{{ $investment->currency === 'USD' ? '$' : 'S/' }} {{ number_format($investor->pivot->amount, 2) }}')" class="text-xs font-bold text-green-600 border-b-2 border-green-600 pb-0.5 hover:opacity-70 transition-opacity">Aprobar</button>
                                <button type="button" onclick="openRejectInvestorModal({{ $investment->id }}, {{ $investor->id }}, '{{ $investor->name }}', '{{ $investment->currency === 'USD' ? '$' : 'S/' }} {{ number_format($investor->pivot->amount, 2) }}')" class="text-xs font-bold text-red-600 border-b-2 border-red-600 pb-0.5 hover:opacity-70 transition-opacity">Rechazar</button>
                            </div>
                        @elseif($investor->pivot->status === 'approved')
                            <button type="button" onclick="openDisapproveInvestorModal({{ $investment->id }}, {{ $investor->id }}, '{{ $investor->name }}', '{{ $investment->currency === 'USD' ? '$' : 'S/' }} {{ number_format($investor->pivot->amount, 2) }}')" class="text-xs font-bold text-amber-600 border-b-2 border-amber-600 pb-0.5 hover:opacity-70 transition-opacity">Desaprobar</button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @if($investors->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $investors->withQueryString()->links() }}
            </div>
        @endif
    @else
        <p class="text-sm text-gray-400 dark:text-gray-600 text-center py-8">Aún no hay inversores participando</p>
    @endif

    <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800">
        @if($investment->status !== 'cancelled' && $investment->approvedInvestors()->count() === 0)
            <button type="button" onclick="openDeleteOpportunityModal()" class="text-xs font-bold text-red-600 border-b-2 border-red-600 pb-0.5 hover:opacity-70 transition-opacity">Eliminar Oportunidad</button>
        @endif
    </div>
</div>

<div id="approveInvestorModal" class="hidden fixed inset-0 bg-white/40 dark:bg-black/60 backdrop-blur-md z-[110] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-950 w-full max-w-sm rounded-3xl shadow-2xl relative p-8 text-center">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Aprobar Inversor</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
            ¿Confirmas la aprobación de <span id="approve_inv_name" class="font-bold text-gray-900 dark:text-white"></span> por <span id="approve_inv_amount" class="font-bold text-gray-900 dark:text-white"></span>?
        </p>
        <p class="text-xs text-amber-600 dark:text-amber-400 mb-6">El inversor deberá subir su comprobante de pago para completar el proceso.</p>
        <form id="approveInvestorForm" method="POST">
            @csrf
            <div class="flex justify-center gap-8">
                <button type="button" onclick="closeApproveInvestorModal()" class="text-xs font-bold text-gray-500 dark:text-gray-400 border-b-2 border-gray-300 dark:border-gray-700 pb-0.5 hover:opacity-70 transition-opacity">Cancelar</button>
                <button type="submit" id="approve_inv_submit_btn" class="text-xs font-bold text-green-600 border-b-2 border-green-600 pb-0.5 hover:opacity-70 transition-opacity">Aprobar</button>
            </div>
        </form>
    </div>
</div>

<div id="rejectInvestorModal" class="hidden fixed inset-0 bg-white/40 dark:bg-black/60 backdrop-blur-md z-[110] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-950 w-full max-w-sm rounded-3xl shadow-2xl relative p-8 text-center">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Rechazar Inversor</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
            ¿Estás seguro de rechazar la solicitud de <span id="reject_inv_name" class="font-bold text-gray-900 dark:text-white"></span> por <span id="reject_inv_amount" class="font-bold text-gray-900 dark:text-white"></span>?
        </p>
        <div class="mb-8 text-left">
            <label class="text-[10px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-[0.2em] block mb-2">Motivo del rechazo (opcional)</label>
            <textarea id="reject_inv_reason" rows="2" class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg py-2 px-3 text-sm text-gray-900 dark:text-white focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors resize-none"></textarea>
        </div>
        <div class="flex justify-center gap-8">
            <button onclick="closeRejectInvestorModal()" class="text-xs font-bold text-gray-500 dark:text-gray-400 border-b-2 border-gray-300 dark:border-gray-700 pb-0.5 hover:opacity-70 transition-opacity">Cancelar</button>
            <form id="rejectInvestorForm" method="POST">
                @csrf
                <input type="hidden" name="reason" id="reject_inv_reason_input" value="">
                <button type="submit" class="text-xs font-bold text-red-600 border-b-2 border-red-600 pb-0.5 hover:opacity-70 transition-opacity">Rechazar</button>
            </form>
        </div>
    </div>
</div>

<div id="disapproveInvestorModal" class="hidden fixed inset-0 bg-white/40 dark:bg-black/60 backdrop-blur-md z-[110] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-950 w-full max-w-sm rounded-3xl shadow-2xl relative p-8 text-center">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Desaprobar Inversor</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">
            ¿Desaprobar a <span id="disapprove_inv_name" class="font-bold text-gray-900 dark:text-white"></span>? Volverá a estado pendiente.
        </p>
        <div class="flex justify-center gap-8">
            <button onclick="closeDisapproveInvestorModal()" class="text-xs font-bold text-gray-500 dark:text-gray-400 border-b-2 border-gray-300 dark:border-gray-700 pb-0.5 hover:opacity-70 transition-opacity">Cancelar</button>
            <form id="disapproveInvestorForm" method="POST">
                @csrf
                <button type="submit" class="text-xs font-bold text-amber-600 border-b-2 border-amber-600 pb-0.5 hover:opacity-70 transition-opacity">Desaprobar</button>
            </form>
        </div>
    </div>
</div>

<div id="investorModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white dark:bg-gray-950 w-full max-w-2xl rounded-[2.5rem] shadow-2xl overflow-hidden relative my-auto">
        <button onclick="closeInvestorModal()" class="absolute top-6 right-6 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-900 transition-colors z-20">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div class="flex flex-col md:flex-row">
            <div class="w-full md:w-2/5 bg-gray-50 dark:bg-gray-900 p-8 flex flex-col items-center justify-center border-b md:border-b-0 md:border-r border-gray-100 dark:border-gray-800">
                <img id="modalSelfie" src="" class="w-40 h-40 rounded-[2rem] object-cover shadow-xl border-4 border-white dark:border-gray-800 mb-6">
                <h3 id="modalName" class="text-xl font-bold text-gray-900 dark:text-white text-center leading-tight"></h3>
                <div class="flex flex-wrap gap-1.5 mt-3 justify-center">
                    <a id="modalEmailBtn" href="" class="flex items-center gap-1 bg-blue-500 hover:bg-blue-600 transition-colors px-2 py-1 text-white font-bold text-[9px]">
                        <span id="modalEmailText"></span>
                        <svg class="w-3 h-3 flex-shrink-0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><rect x="7.904" y="119.947" style="fill:#5EBAE7;" width="496.195" height="272.109"/><polygon style="fill:#36A9E1;" points="256,280.01 23.91,127.951 7.904,135.954 256,312.022 352.037,223.987 "/><path style="fill:#1D1D1B;" d="M0,112.043v287.913h512V112.043H0z M477.266,127.851L256,270.603L34.733,127.851H477.266z M15.807,384.149V134.452L256,289.414l240.193-154.962v249.696H15.807V384.149z"/></svg>
                    </a>
                    <a id="modalWhatsappBtn" href="" target="_blank" class="flex items-center gap-1 bg-green-500 hover:bg-green-600 transition-colors px-2 py-1 text-white font-bold text-[9px]">
                        <span id="modalPhoneText"></span>
                        <svg class="w-3 h-3 flex-shrink-0" viewBox="0 0 32 32" fill="none"><path d="M28 16C28 22.6274 22.6274 28 16 28C13.4722 28 11.1269 27.2184 9.19266 25.8837L5.09091 26.9091L6.16576 22.8784C4.80092 20.9307 4 18.5589 4 16C4 9.37258 9.37258 4 16 4C22.6274 4 28 9.37258 28 16Z" fill="url(#wssp_inv)"/><path fill-rule="evenodd" clip-rule="evenodd" d="M16 30C23.732 30 30 23.732 30 16C30 8.26801 23.732 2 16 2C8.26801 2 2 8.26801 2 16C2 18.5109 2.661 20.8674 3.81847 22.905L2 30L9.31486 28.3038C11.1894 29.3854 13.5789 30 16 30ZM16 27.8462C22.5425 27.8462 27.8462 22.5425 27.8462 16C27.8462 9.45755 22.5425 4.15385 16 4.15385C9.45755 4.15385 4.15385 9.45755 4.15385 16C4.15385 18.5261 4.9445 20.8675 6.29184 22.7902L5.23077 26.7692L9.27993 25.7569C11.1894 27.0746 13.5046 27.8462 16 27.8462Z" fill="white"/><path d="M12.5 9.49989C12.1672 8.83131 11.6565 8.8905 11.1407 8.8905C10.2188 8.8905 8.78125 9.99478 8.78125 12.05C8.78125 13.7343 9.52345 15.578 12.0244 18.3361C14.438 20.9979 17.6094 22.3748 20.2422 22.3279C22.875 22.2811 23.4167 20.0154 23.4167 19.2503C23.4167 18.9112 23.2062 18.742 23.0613 18.696C22.1641 18.2654 20.5093 17.4631 20.1328 17.3124C19.7563 17.1617 19.5597 17.3656 19.4375 17.4765C19.0961 17.8018 18.4193 18.7608 18.1875 18.9765C17.9558 19.1922 17.6103 19.083 17.4665 19.0015C16.9374 18.7892 15.5029 18.1511 14.3595 17.0426C12.9453 15.6718 12.8623 15.2001 12.5959 14.7803C12.3828 14.4444 12.5392 14.2384 12.6172 14.1483C12.9219 13.7968 13.3426 13.254 13.5313 12.9843C13.7199 12.7145 13.5702 12.305 13.4803 12.05C13.0938 10.953 12.7663 10.0347 12.5 9.49989Z" fill="white"/><defs><linearGradient id="wssp_inv" x1="26.5" y1="7" x2="4" y2="28" gradientUnits="userSpaceOnUse"><stop stop-color="#5BD066"/><stop offset="1" stop-color="#27B43E"/></linearGradient></defs></svg>
                    </a>
                </div>
                <div id="modalBadge" class="mt-6 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-[0.2em]"></div>
                <p id="modalDate" class="text-[10px] text-gray-400 mt-3"></p>
            </div>
            <div class="w-full md:w-3/5 p-8 md:p-10 space-y-6 max-h-[70vh] overflow-y-auto">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">Información Personal</p>
                    <div class="grid grid-cols-2 gap-x-6 gap-y-3">
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">DNI / Documento</p>
                            <p id="modalDni" class="text-sm font-bold text-gray-900 dark:text-white mt-0.5"></p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">RUC</p>
                            <p id="modalRuc" class="text-sm font-bold text-gray-900 dark:text-white mt-0.5"></p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Tipo de Persona</p>
                            <p id="modalPersonType" class="text-sm font-bold text-gray-900 dark:text-white mt-0.5"></p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Razón Social</p>
                            <p id="modalBusinessName" class="text-sm font-bold text-gray-900 dark:text-white mt-0.5"></p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Dirección</p>
                            <p id="modalAddress" class="text-sm font-bold text-gray-900 dark:text-white mt-0.5"></p>
                        </div>
                    </div>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">Detalles de Inversión</p>
                    <div class="grid grid-cols-2 gap-x-6 gap-y-3">
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Ocupación</p>
                            <p id="modalOccupation" class="text-sm font-bold text-gray-900 dark:text-white mt-0.5"></p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Moneda</p>
                            <p id="modalCurrency" class="text-sm font-bold text-gray-900 dark:text-white mt-0.5"></p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Monto Invertido</p>
                            <p id="modalAmount" class="text-sm font-bold text-green-600 dark:text-green-400 mt-0.5"></p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Fecha Solicitud</p>
                            <p id="modalTerm" class="text-sm font-bold text-gray-900 dark:text-white mt-0.5"></p>
                        </div>
                    </div>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Referido</p>
                    <p id="modalReferral" class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="closeOpportunityModal" class="hidden fixed inset-0 bg-white/40 dark:bg-black/60 backdrop-blur-md z-[110] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-950 w-full max-w-sm rounded-3xl shadow-2xl relative p-8 text-center">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $investment->isFunded() ? 'Marcar como Financiada' : 'Cerrar Oportunidad' }}</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">
            ¿Confirmas que deseas {{ $investment->isFunded() ? 'marcar como financiada' : 'cerrar' }} la oportunidad <span class="font-bold text-gray-900 dark:text-white">{{ $investment->title }}</span>? {{ $investment->isFunded() ? 'Ya no se aceptarán más inversores.' : 'Las solicitudes pendientes serán rechazadas automáticamente.' }}
        </p>
        <div class="flex justify-center gap-8">
            <button onclick="closeCloseOpportunityModal()" class="text-xs font-bold text-gray-500 dark:text-gray-400 border-b-2 border-gray-300 dark:border-gray-700 pb-0.5 hover:opacity-70 transition-opacity">Cancelar</button>
            <form action="{{ route('superadmin.investments.update', $investment) }}" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="status" value="{{ $investment->isFunded() ? 'funded' : 'closed' }}">
                <button type="submit" class="text-xs font-bold {{ $investment->isFunded() ? 'text-blue-600 border-b-2 border-blue-600' : 'text-yellow-600 border-b-2 border-yellow-600' }} pb-0.5 hover:opacity-70 transition-opacity">Confirmar</button>
            </form>
        </div>
    </div>
</div>

<div id="deleteOpportunityModal" class="hidden fixed inset-0 bg-white/40 dark:bg-black/60 backdrop-blur-md z-[110] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-950 w-full max-w-sm rounded-3xl shadow-2xl relative p-8 text-center">
        <div class="mx-auto w-12 h-12 rounded-full bg-red-100 dark:bg-red-500/10 flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Eliminar Oportunidad</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
            ¿Estás seguro de eliminar <span class="font-bold text-gray-900 dark:text-white">{{ $investment->title }}</span>?
        </p>
        <div class="bg-red-50 dark:bg-red-500/10 rounded-xl p-3 mb-6 text-left">
            <p class="text-xs font-bold text-red-600 dark:text-red-400">⚠ Esta acción es irreversible y eliminará:</p>
            <ul class="text-xs text-red-500 dark:text-red-400 mt-1 space-y-0.5">
                <li>• La oportunidad de inversión</li>
                <li>• Todas las solicitudes de inversores (pendientes y rechazadas)</li>
                <li>• El historial de participaciones</li>
            </ul>
        </div>
        <div class="flex justify-center gap-8">
            <button onclick="closeDeleteOpportunityModal()" class="text-xs font-bold text-gray-500 dark:text-gray-400 border-b-2 border-gray-300 dark:border-gray-700 pb-0.5 hover:opacity-70 transition-opacity">Cancelar</button>
            <form action="{{ route('superadmin.investments.destroy', $investment) }}" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="text-xs font-bold text-red-600 border-b-2 border-red-600 pb-0.5 hover:opacity-70 transition-opacity">Eliminar Definitivamente</button>
            </form>
        </div>
    </div>
</div>

<script>
function openApproveInvestorModal(investmentId, userId, name, amount) {
    document.getElementById('approve_inv_name').textContent = name;
    document.getElementById('approve_inv_amount').textContent = amount;
    document.getElementById('approveInvestorForm').action = `/gestion-interna/inversiones/${investmentId}/inversor/${userId}/aprobar`;
    document.getElementById('approveInvestorModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeApproveInvestorModal() {
    document.getElementById('approveInvestorModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function openRejectInvestorModal(investmentId, userId, name, amount) {
    document.getElementById('reject_inv_name').textContent = name;
    document.getElementById('reject_inv_amount').textContent = amount;
    document.getElementById('rejectInvestorForm').action = `/gestion-interna/inversiones/${investmentId}/inversor/${userId}/rechazar`;
    document.getElementById('reject_inv_reason').value = '';
    document.getElementById('rejectInvestorModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeRejectInvestorModal() {
    document.getElementById('rejectInvestorModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function openDisapproveInvestorModal(investmentId, userId, name, amount) {
    document.getElementById('disapprove_inv_name').textContent = name;
    document.getElementById('disapproveInvestorForm').action = `/gestion-interna/inversiones/${investmentId}/inversor/${userId}/desaprobar`;
    document.getElementById('disapproveInvestorModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDisapproveInvestorModal() {
    document.getElementById('disapproveInvestorModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function openCloseOpportunityModal() {
    document.getElementById('closeOpportunityModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeCloseOpportunityModal() {
    document.getElementById('closeOpportunityModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function openDeleteOpportunityModal() {
    document.getElementById('deleteOpportunityModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDeleteOpportunityModal() {
    document.getElementById('deleteOpportunityModal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.getElementById('rejectInvestorForm').addEventListener('submit', function() {
    document.getElementById('reject_inv_reason_input').value = document.getElementById('reject_inv_reason').value;
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeApproveInvestorModal();
        closeRejectInvestorModal();
        closeDisapproveInvestorModal();
        closeInvestorModal();
        closeCloseOpportunityModal();
        closeDeleteOpportunityModal();
    }
});

function openInvestorModal(data) {
    const modal = document.getElementById('investorModal');

    document.getElementById('modalSelfie').src = data.selfie || data.avatar;
    document.getElementById('modalName').textContent = data.name;

    const emailBtn = document.getElementById('modalEmailBtn');
    const emailText = document.getElementById('modalEmailText');
    emailText.textContent = data.email;
    emailBtn.href = `mailto:${data.email}`;

    const phoneText = document.getElementById('modalPhoneText');
    phoneText.textContent = data.phone;
    const whatsappBtn = document.getElementById('modalWhatsappBtn');
    const cleanPhone = data.phone.replace(/\D/g, '');
    whatsappBtn.href = `https://wa.me/51${cleanPhone}`;

    document.getElementById('modalDni').textContent = data.dni;
    document.getElementById('modalRuc').textContent = data.ruc;
    document.getElementById('modalPersonType').textContent = data.person_type === 'natural' ? 'Persona Natural' : (data.person_type === 'juridica' ? 'Persona Jurídica' : data.person_type);
    document.getElementById('modalBusinessName').textContent = data.business_name;
    document.getElementById('modalAddress').textContent = [data.department, data.province, data.district].filter(v => v && v !== '—').join(', ') + (data.address && data.address !== '—' ? ' — ' + data.address : '');
    document.getElementById('modalOccupation').textContent = data.occupation;
    document.getElementById('modalCurrency').textContent = data.currency === 'USD' ? 'Dólares (USD)' : 'Soles (PEN)';
    document.getElementById('modalAmount').textContent = data.amount;
    document.getElementById('modalTerm').textContent = data.date;

    const referralEl = document.getElementById('modalReferral');
    if (data.referred_by) {
        referralEl.textContent = 'Código de referido: ' + data.referred_by;
    } else if (data.referral_code && data.referral_code !== '—') {
        referralEl.textContent = 'Su código: ' + data.referral_code + ' (sin referidor)';
    } else {
        referralEl.textContent = 'Sin referidor';
    }

    const badge = document.getElementById('modalBadge');
    badge.textContent = data.status === 'approved' ? 'Aprobado' : (data.status === 'pending' ? 'Pendiente' : 'Rechazado');
    badge.className = 'mt-6 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-[0.2em] ' +
        (data.status === 'approved' ? 'bg-green-100 text-green-700' : (data.status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700'));

    document.getElementById('modalDate').textContent = 'Solicitado: ' + data.date;

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeInvestorModal() {
    document.getElementById('investorModal').classList.add('hidden');
    document.body.style.overflow = '';
}

window.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const highlightInvestor = urlParams.get('highlight_investor');
    if (highlightInvestor) {
        const el = document.getElementById('investor-' + highlightInvestor);
        if (el) {
            el.classList.add('bg-yellow-50', 'dark:bg-yellow-900/10', 'ring-2', 'ring-yellow-400');
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => {
                el.classList.remove('bg-yellow-50', 'dark:bg-yellow-900/10', 'ring-2', 'ring-yellow-400');
            }, 3000);
        }
    }
});
</script>
@endsection
