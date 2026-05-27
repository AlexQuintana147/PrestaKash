@extends('client.layout')

@section('title', 'Mis Referidos')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8 max-w-4xl">

    <!-- Header -->
    <div class="mb-10">
        <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-0.5">Gana dinero</p>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Mis Referidos</h1>
    </div>

    <!-- Link -->
    <section class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-end gap-4">
            <div class="flex-1">
                <span id="referralLink" class="text-sm font-medium text-gray-700 dark:text-gray-300 block border-b border-gray-200 dark:border-gray-800 pb-2">{{ $referralLink }}</span>
            </div>
            <button onclick="copyReferralLink()"
                    class="text-green-600 dark:text-green-400 hover:text-green-500 dark:hover:text-green-300 text-xs font-medium transition-colors pb-0.5 border-b border-green-600 dark:border-green-400">
                <span id="copyText">Copiar link</span>
            </button>
        </div>
    </section>

    <!-- Stats -->
    <section class="grid grid-cols-3 gap-6 mb-10">
        <div>
            <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">Referidos</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalReferrals }}</p>
        </div>
        <div>
            <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">Tu código</p>
            <p class="text-xl font-bold text-green-600 dark:text-green-400 tracking-wider">{{ $user->referral_code }}</p>
        </div>
        <div>
            <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">Ganancias</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">S/ {{ number_format($totalEarnings, 2) }}</p>
            @if($totalEarnings >= 50)
                <button type="button" onclick="openWithdrawModal()" class="mt-2 w-full text-[10px] font-semibold text-white bg-green-600 hover:bg-green-700 rounded-lg px-3 py-1.5 transition-colors">
                    Solicitar retiro
                </button>
            @else
                @php $remaining = max(0, 50 - $totalEarnings); @endphp
                <p class="text-[10px] text-gray-400 dark:text-gray-600 mt-1">Te falta S/ {{ number_format($remaining, 2) }} para retirar</p>
                <div class="mt-1.5 h-1 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                    <div class="h-full bg-green-500 rounded-full transition-all" style="width: {{ min(($totalEarnings / 50) * 100, 100) }}%"></div>
                </div>
            @endif
        </div>
    </section>

    <!-- Filtros -->
    <section class="mb-6">
        <div class="flex items-center gap-0 border-b border-gray-200 dark:border-gray-800">
            @foreach(['all' => 'Todos', 'approved' => 'Aprobados', 'pending' => 'Pendientes', 'rejected' => 'Rechazados'] as $key => $label)
                <a href="{{ route('client.referidos', ['filter' => $key]) }}"
                   class="relative flex-shrink-0 px-4 py-3 text-sm font-medium transition-colors
                          {{ $filter === $key
                              ? 'text-gray-900 dark:text-white'
                              : 'text-gray-600 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-400' }}">
                    {{ $label }}
                    @if(isset($counts[$key]) && $counts[$key] > 0)
                        <span class="ml-1.5 text-xs {{ $filter === $key ? 'text-gray-500 dark:text-gray-400' : 'text-gray-300 dark:text-gray-500' }}">{{ $counts[$key] }}</span>
                    @endif
                    @if($filter === $key)
                        <span class="absolute bottom-0 left-0 right-0 h-px bg-gray-900 dark:bg-white"></span>
                    @endif
                </a>
            @endforeach
        </div>
    </section>

    <!-- Lista -->
    <section>
        @if($referrals->isEmpty())
            <div class="py-12 text-center">
                <p class="text-sm text-gray-400 dark:text-gray-600">No hay referidos con este filtro</p>
            </div>
        @else
            <div class="space-y-0">
                @foreach($referrals as $record)
                    @php $referred = $record->referred; @endphp
                    @php $status = $referred->documents?->status; @endphp
                    @php $payout = $record->payouts->first(); @endphp
                    <button type="button" onclick="openReferralDetailModal({{ json_encode([
                        'name' => $referred->name,
                        'email' => $referred->email,
                        'selfie' => $referred->documents?->selfie ? \Illuminate\Support\Facades\Storage::url($referred->documents->selfie) : null,
                        'status' => $referred->referralStatus,
                        'amount' => $payout ? number_format($payout->amount, 2) : null,
                        'status_payout' => $payout?->status,
                        'notes' => $payout?->notes,
                        'loan_code' => $payout?->loan?->code,
                        'paid_at' => $payout?->paid_at?->format('d/m/Y') ?? null,
                    ]) }})"
                        class="w-full flex items-center justify-between py-4 border-b border-gray-100 dark:border-gray-800 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-900/50 transition-colors text-left px-2 -mx-2 rounded-xl">
                        <div class="flex items-center gap-3">
                            @php
                                $selfieUrl = $referred->documents?->selfie ? \Illuminate\Support\Facades\Storage::url($referred->documents->selfie) : null;
                            @endphp
                            @if($selfieUrl)
                                <img src="{{ $selfieUrl }}" alt="{{ $referred->name }}" class="w-9 h-9 rounded-full object-cover flex-shrink-0 border border-gray-200 dark:border-gray-700">
                            @else
                                <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0" style="background-color: {{ match($status) { 'approved' => '#dcfce7', 'rejected' => '#fee2e2', 'pending' => '#fef9c3', default => '#f3f4f6' } }};">
                                    <svg class="w-4 h-4" style="color: {{ match($status) { 'approved' => '#22c55e', 'rejected' => '#ef4444', 'pending' => '#eab308', default => '#9ca3af' } }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $referred->name }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">{{ $referred->email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($referred->referralStatus['color'] === 'green')
                                <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-medium text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                    {{ $referred->referralStatus['label'] }}
                                </span>
                            @elseif($referred->referralStatus['color'] === 'yellow')
                                <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-medium text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                    {{ $referred->referralStatus['label'] }}
                                </span>
                            @elseif($referred->referralStatus['color'] === 'red')
                                <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-medium text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                    {{ $referred->referralStatus['label'] }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                    {{ $referred->referralStatus['label'] }}
                                </span>
                            @endif
                            <span class="text-[10px] text-gray-400 dark:text-gray-600">{{ $record->created_at->diffForHumans() }}</span>
                            <svg class="w-4 h-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </button>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $referrals->links() }}
            </div>
        @endif
    </section>

</div>

<!-- Referral Detail Modal -->
<div id="referralDetailModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div onclick="closeReferralDetailModal()" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
    <div class="absolute inset-x-0 bottom-0 max-h-[85vh] sm:inset-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 sm:max-w-md sm:w-full bg-white dark:bg-gray-950 rounded-t-3xl sm:rounded-3xl shadow-2xl flex flex-col overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div id="refModalAvatar" class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0 bg-gray-100 dark:bg-gray-900"></div>
                <div>
                    <h3 id="refModalName" class="text-sm font-bold text-gray-900 dark:text-white"></h3>
                    <p id="refModalEmail" class="text-[10px] text-gray-400"></p>
                </div>
            </div>
            <button type="button" onclick="closeReferralDetailModal()" class="w-8 h-8 rounded-xl flex items-center justify-center text-gray-400 dark:text-gray-600 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="refModalContent" class="flex-1 overflow-y-auto p-5">
        </div>
    </div>
</div>

<script>
function openReferralDetailModal(data) {
    const modal = document.getElementById('referralDetailModal');
    document.getElementById('refModalName').textContent = data.name;
    document.getElementById('refModalEmail').textContent = data.email;
    document.getElementById('refModalAvatar').innerHTML = data.selfie
        ? `<img src="${data.selfie}" alt="${data.name}" class="w-full h-full object-cover">`
        : `<div class="w-full h-full flex items-center justify-center text-xs font-bold text-gray-500 dark:text-gray-400">${data.name.charAt(0).toUpperCase()}</div>`;

    let html = '';

    if (data.amount) {
        const isPaid = data.status_payout === 'paid';
        const statusBg = isPaid ? 'bg-green-50 dark:bg-green-950/20 border-green-100 dark:border-green-900/30' : 'bg-amber-50 dark:bg-amber-950/20 border-amber-100 dark:border-amber-900/30';
        const statusColor = isPaid ? 'text-green-600 dark:text-green-400' : 'text-amber-600 dark:text-amber-400';

        html += `<div class="rounded-2xl p-5 border ${statusBg} mb-4">
            <p class="text-[10px] uppercase tracking-widest font-semibold text-gray-400 mb-3">Tu ganancia</p>
            <p class="text-3xl font-bold ${statusColor}">S/ ${data.amount}</p>
            <div class="flex items-center gap-2 mt-2">
                ${data.loan_code ? `<span class="text-[10px] text-gray-400">${data.loan_code}</span>` : ''}
            </div>
            ${data.paid_at ? `<p class="text-[10px] text-gray-400 mt-1">Pagado el ${data.paid_at}</p>` : ''}
        </div>`;

        if (data.notes) {
            html += `<div class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-4">
                <p class="text-[10px] uppercase tracking-widest font-semibold text-gray-400 mb-2">Mensaje del admin</p>
                <p class="text-sm text-gray-700 dark:text-gray-300 italic">${data.notes}</p>
            </div>`;
        }
    } else {
        html += `<div class="text-center py-12">
            <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-gray-100 dark:bg-gray-900 flex items-center justify-center">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-500">Aún no hay ganancias registradas</p>
            <p class="text-[11px] text-gray-400 mt-0.5">Cuando el admin asigne un monto, aparecerá aquí.</p>
        </div>`;
    }

    document.getElementById('refModalContent').innerHTML = html;
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeReferralDetailModal() {
    document.getElementById('referralDetailModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function copyReferralLink() {
    const link = document.getElementById('referralLink').textContent.trim();
    navigator.clipboard.writeText(link).then(() => {
        const copyText = document.getElementById('copyText');
        copyText.textContent = '¡Copiado!';
        setTimeout(() => {
            copyText.textContent = 'Copiar link';
        }, 2000);
    });
}
</script>
@endsection
