@extends('superadmin.layout')

@section('title', 'Referidos')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8">

    <!-- Header -->
    <div class="flex items-end justify-between mb-8">
        <div>
            <p class="text-xs text-gray-600 dark:text-gray-600 uppercase tracking-widest mb-1">Panel Administrativo</p>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Referidos</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Usuarios que pagaron su préstamo completo y sus referidores.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 text-sm text-green-500">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 flex items-center gap-3 text-sm text-red-500">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Filtros -->
    <div class="hidden md:flex items-center gap-0 border-b border-gray-200 dark:border-gray-800 mb-8 overflow-x-auto no-scrollbar">
        @foreach(['pending' => 'Pendientes', 'paid' => 'Pagados'] as $key => $label)
            <a href="{{ route('superadmin.referrals.payouts', ['filter' => $key]) }}"
                class="relative flex-shrink-0 px-6 py-4 text-sm font-medium transition-colors flex items-center gap-2
                          {{ $filter === $key ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-400' }}">
                {{ $label }}
                @if($filter === $key)
                    <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-gray-900 dark:bg-white rounded-full"></span>
                @endif
                @if($counts[$key] > 0)
                    <span class="text-[10px] font-bold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-full px-2 py-0.5">{{ $counts[$key] }}</span>
                @endif
            </a>
        @endforeach
    </div>

    <!-- Filtros Mobile -->
    <div class="md:hidden mb-8">
        <div class="grid grid-cols-2 gap-2">
            @foreach(['pending' => 'Pendientes', 'paid' => 'Pagados'] as $key => $label)
                <a href="{{ route('superadmin.referrals.payouts', ['filter' => $key]) }}"
                   class="flex items-center justify-center py-2.5 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all border
                          {{ $filter === $key ? 'bg-gray-900 text-white dark:bg-white dark:text-black border-transparent' : 'bg-transparent text-gray-400 border-gray-200 dark:border-gray-800' }}">
                    {{ $label }}
                    @if($counts[$key] > 0)
                        <span class="ml-1.5 text-[9px] font-bold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-full px-1.5 py-0.5">{{ $counts[$key] }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    <!-- Lista -->
    @forelse($referrals as $referral)
        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-900 rounded-2xl p-5 md:p-6 mb-4">
            <div class="flex flex-col md:flex-row md:items-center gap-5">

                <!-- Referido -->
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] uppercase tracking-widest font-semibold text-gray-400 mb-2">Referido</p>
                    <div class="flex items-center gap-3">
                        @php $refSelfie = $referral->referred->documents?->selfie ? \Illuminate\Support\Facades\Storage::url($referral->referred->documents->selfie) : null; @endphp
                        <button type="button" onclick="openUserDetailModal({{ $referral->referred->id }})"
                            class="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0 ring-1 ring-inset ring-green-100 dark:ring-green-500/20 hover:ring-2 hover:ring-green-300 transition-all cursor-pointer">
                            @if($refSelfie)
                                <img src="{{ $refSelfie }}" alt="{{ $referral->referred->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-green-50 dark:bg-green-500/10 flex items-center justify-center">
                                    <span class="text-xs font-bold text-green-600 dark:text-green-400">{{ strtoupper(substr($referral->referred->name, 0, 1)) }}</span>
                                </div>
                            @endif
                        </button>
                        <div class="min-w-0">
                            <button type="button" onclick="openUserDetailModal({{ $referral->referred->id }})" class="text-left hover:underline">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $referral->referred->name }}</p>
                            </button>
                            <p class="text-xs text-gray-400 truncate">{{ $referral->referred->email }}</p>
                        </div>
                    </div>
                    @if($referral->paid_loan)
                        <div class="mt-3 flex items-center gap-3 text-xs">
                            <span class="px-2 py-0.5 rounded-md bg-green-50 dark:bg-green-500/10 text-green-700 dark:text-green-400 font-medium ring-1 ring-inset ring-green-100 dark:ring-green-500/20">
                                Préstamo pagado
                            </span>
                            <span class="text-gray-500">
                                {{ $referral->paid_loan->currency === 'USD' ? '$' : 'S/' }} {{ number_format($referral->paid_loan->total_amount, 2) }}
                            </span>
                            <span class="text-gray-400">{{ $referral->paid_loan->code }}</span>
                        </div>
                    @endif
                </div>

                <!-- Referidor -->
                <div class="flex-1 min-w-0 md:border-l md:border-gray-100 md:dark:border-gray-800 md:pl-5">
                    <p class="text-[10px] uppercase tracking-widest font-semibold text-gray-400 mb-2">Referidor</p>
                    <div class="flex items-center gap-3">
                        @php $refSelfie = $referral->referrer->documents?->selfie ? \Illuminate\Support\Facades\Storage::url($referral->referrer->documents->selfie) : null; @endphp
                        <button type="button" onclick="openUserDetailModal({{ $referral->referrer->id }})"
                            class="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0 ring-1 ring-inset ring-indigo-100 dark:ring-indigo-500/20 hover:ring-2 hover:ring-indigo-300 transition-all cursor-pointer">
                            @if($refSelfie)
                                <img src="{{ $refSelfie }}" alt="{{ $referral->referrer->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center">
                                    <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">{{ strtoupper(substr($referral->referrer->name, 0, 1)) }}</span>
                                </div>
                            @endif
                        </button>
                        <div class="min-w-0">
                            <button type="button" onclick="openUserDetailModal({{ $referral->referrer->id }})" class="text-left hover:underline">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $referral->referrer->name }}</p>
                            </button>
                            <p class="text-xs text-gray-400 truncate">{{ $referral->referrer->email }}</p>
                        </div>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @if($referral->referrer_has_loans)
                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 font-medium ring-1 ring-inset ring-blue-100 dark:ring-blue-500/20">Tiene préstamos</span>
                        @endif
                        @if($referral->referrer_has_investments)
                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-purple-50 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400 font-medium ring-1 ring-inset ring-purple-100 dark:ring-purple-500/20">Ha invertido</span>
                        @endif
                    </div>
                </div>

                <!-- Monto a pagar / Acciones -->
                <div class="md:w-64 shrink-0 md:border-l md:border-gray-100 md:dark:border-gray-800 md:pl-5">
                    @if($filter === 'pending')
                        <form action="{{ route('superadmin.referrals.payouts.store', $referral) }}" method="POST" class="space-y-2">
                            @csrf
                            <div>
                                <label class="block text-[10px] uppercase tracking-widest font-semibold text-gray-400 mb-1">Monto para referidor</label>
                                <div class="relative">
                                    <span class="absolute left-0 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-medium">S/</span>
                                    <input type="number" name="amount" step="0.01" min="0" required
                                           value="{{ $referral->payout ? number_format($referral->payout->amount, 2, '.', '') : '' }}"
                                           class="w-full pl-5 pr-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg text-sm text-gray-900 dark:text-white focus:outline-none focus:border-gray-400 dark:focus:border-gray-600 transition-colors">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] uppercase tracking-widest font-semibold text-gray-400 mb-1">Notas (opcional)</label>
                                <input type="text" name="notes" maxlength="500"
                                       value="{{ $referral->payout?->notes ?? '' }}"
                                       class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg text-sm text-gray-900 dark:text-white focus:outline-none focus:border-gray-400 dark:focus:border-gray-600 transition-colors placeholder-gray-300 dark:placeholder-gray-700">
                            </div>
                            <button type="submit"
                                    class="w-full text-[11px] font-semibold text-white bg-gray-900 dark:bg-white dark:text-gray-900 hover:bg-gray-800 dark:hover:bg-gray-100 rounded-lg px-4 py-2.5 transition-colors">
                                {{ $referral->payout ? 'Actualizar' : 'Guardar' }}
                            </button>
                        </form>
                    @else
                        <div class="space-y-2">
                            <div>
                                <p class="text-[10px] uppercase tracking-widest font-semibold text-gray-400 mb-1">Monto pagado</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">
                                    S/ {{ number_format($referral->payout?->amount ?? 0, 2) }}
                                </p>
                            </div>
                            @if($referral->payout?->paid_at)
                                <p class="text-xs text-gray-400">Pagado el {{ $referral->payout->paid_at->format('d/m/Y') }}</p>
                            @endif
                            @if($referral->payout?->notes)
                                <p class="text-xs text-gray-500 dark:text-gray-400 italic">{{ $referral->payout->notes }}</p>
                            @endif
                        </div>
                    @endif
                </div>

            </div>
        </div>
    @empty
        <div class="text-center py-20">
            <div class="w-16 h-16 mx-auto mb-5 rounded-2xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center">
                <svg class="w-7 h-7 text-gray-300 dark:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-400">No hay referidos en este estado</p>
            <p class="text-[11px] text-gray-300 dark:text-gray-600 mt-1">Los usuarios con préstamos pagados aparecerán aquí.</p>
        </div>
    @endforelse

    <!-- Pagination -->
    @if($referrals->hasPages())
        <div class="mt-6">
            {{ $referrals->links() }}
        </div>
    @endif
</div>

<!-- User Detail Modal -->
<div id="userDetailModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div onclick="closeUserDetailModal()" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
    <div class="absolute inset-x-0 bottom-0 max-h-[85vh] sm:inset-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 sm:max-w-lg sm:w-full bg-white dark:bg-gray-950 rounded-t-3xl sm:rounded-3xl shadow-2xl flex flex-col overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div id="modalAvatar" class="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0 bg-gray-100 dark:bg-gray-900"></div>
                <div>
                    <h3 id="modalName" class="text-sm font-bold text-gray-900 dark:text-white"></h3>
                    <p id="modalEmail" class="text-[10px] text-gray-400"></p>
                </div>
            </div>
            <button type="button" onclick="closeUserDetailModal()" class="w-8 h-8 rounded-xl flex items-center justify-center text-gray-400 dark:text-gray-600 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="modalContent" class="flex-1 overflow-y-auto p-5 space-y-4">
            <div class="animate-pulse space-y-3">
                <div class="h-4 bg-gray-100 dark:bg-gray-900 rounded w-3/4"></div>
                <div class="h-4 bg-gray-100 dark:bg-gray-900 rounded w-1/2"></div>
                <div class="h-4 bg-gray-100 dark:bg-gray-900 rounded w-2/3"></div>
            </div>
        </div>
    </div>
</div>

<script>
async function openUserDetailModal(userId) {
    const modal = document.getElementById('userDetailModal');
    const content = document.getElementById('modalContent');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    content.innerHTML = `
        <div class="animate-pulse space-y-3">
            <div class="h-4 bg-gray-100 dark:bg-gray-900 rounded w-3/4"></div>
            <div class="h-4 bg-gray-100 dark:bg-gray-900 rounded w-1/2"></div>
            <div class="h-4 bg-gray-100 dark:bg-gray-900 rounded w-2/3"></div>
        </div>
    `;
    try {
        const res = await fetch('{{ route("superadmin.referrals.user-detail") }}?user_id=' + userId);
        const data = await res.json();
        document.getElementById('modalName').textContent = data.name;
        document.getElementById('modalEmail').textContent = data.email;
        document.getElementById('modalAvatar').innerHTML = data.selfie
            ? `<img src="${data.selfie}" alt="${data.name}" class="w-full h-full object-cover">`
            : `<div class="w-full h-full flex items-center justify-center text-xs font-bold text-gray-500 dark:text-gray-400">${data.initials}</div>`;

        let html = '';

        // Estado general
        html += `<div class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-4">
            <p class="text-[10px] uppercase tracking-widest font-semibold text-gray-400 mb-3">Estado financiero</p>
            <div class="grid grid-cols-3 gap-3">
                <div class="text-center">
                    <p class="text-lg font-bold ${data.total_debt > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'}">${data.total_debt > 0 ? 'S/ ' + data.total_debt.toFixed(2) : 'S/ 0.00'}</p>
                    <p class="text-[10px] text-gray-400">Deuda total</p>
                </div>
                <div class="text-center">
                    <p class="text-lg font-bold text-gray-900 dark:text-white">${data.active_loans}</p>
                    <p class="text-[10px] text-gray-400">Préstamos activos</p>
                </div>
                <div class="text-center">
                    <p class="text-lg font-bold text-gray-900 dark:text-white">${data.paid_loans}</p>
                    <p class="text-[10px] text-gray-400">Préstamos pagados</p>
                </div>
            </div>
        </div>`;

        // Cuotas vencidas
        if (data.overdue_installments.length > 0) {
            html += `<div class="bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-900/30 rounded-2xl p-4">
                <p class="text-[10px] uppercase tracking-widest font-semibold text-red-400 mb-3">Cuotas vencidas</p>
                <div class="space-y-2">`;
            data.overdue_installments.forEach(inst => {
                html += `<div class="flex items-center justify-between text-sm">
                    <span class="text-gray-700 dark:text-gray-300">${inst.loan_code} — Cuota #${inst.number}</span>
                    <span class="font-bold text-red-600 dark:text-red-400">S/ ${inst.amount.toFixed(2)}</span>
                </div>`;
            });
            html += `</div></div>`;
        }

        // Próximos pagos
        if (data.upcoming_installments.length > 0) {
            html += `<div class="bg-amber-50 dark:bg-amber-950/20 border border-amber-100 dark:border-amber-900/30 rounded-2xl p-4">
                <p class="text-[10px] uppercase tracking-widest font-semibold text-amber-400 mb-3">Próximos pagos</p>
                <div class="space-y-2">`;
            data.upcoming_installments.forEach(inst => {
                html += `<div class="flex items-center justify-between text-sm">
                    <span class="text-gray-700 dark:text-gray-300">${inst.loan_code} — Cuota #${inst.number}</span>
                    <span class="font-bold text-amber-600 dark:text-amber-400">S/ ${inst.amount.toFixed(2)}</span>
                </div><p class="text-[10px] text-gray-400">Vence ${inst.due_date}</p>`;
            });
            html += `</div></div>`;
        }

        if (data.overdue_installments.length === 0 && data.upcoming_installments.length === 0) {
            html += `<div class="text-center py-8">
                <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-green-50 dark:bg-green-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-sm font-medium text-gray-500">Sin cuotas pendientes</p>
                <p class="text-[11px] text-gray-400 mt-0.5">El usuario está al día con sus pagos.</p>
            </div>`;
        }

        content.innerHTML = html;
    } catch (e) {
        content.innerHTML = `<p class="text-sm text-red-500 text-center py-8">Error al cargar los datos.</p>`;
    }
}

function closeUserDetailModal() {
    document.getElementById('userDetailModal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.getElementById('userDetailModal').addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeUserDetailModal();
});
</script>
@endsection
