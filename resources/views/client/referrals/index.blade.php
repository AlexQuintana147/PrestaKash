@extends('client.layout')

@section('title', 'Mis Referidos')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-8 max-w-4xl">

    <!-- Header -->
    <div class="mb-10">
        <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-0.5">Gana dinero</p>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Mis Referidos</h1>
    </div>

    <!-- Link de referido -->
    <div class="bg-gradient-to-r from-green-500/10 to-emerald-500/10 dark:from-green-500/5 dark:to-emerald-500/5 border border-green-500/20 dark:border-green-500/30 rounded-2xl p-6 md:p-8 mb-8">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
            <span class="text-sm font-semibold text-green-600 dark:text-green-400">Invita y gana</span>
        </div>

        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 leading-relaxed">
            Comparte tu link de referido. Cada persona que se registre usando tu link será tu referido. ¡Acumula recompensas por cada amigo que se una!
        </p>

        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg px-4 py-3 flex items-center gap-2">
                <span id="referralLink" class="text-sm text-gray-700 dark:text-gray-300 truncate flex-1">{{ $referralLink }}</span>
            </div>
            <button onclick="copyReferralLink()" id="copyBtn"
                    class="flex items-center justify-center gap-2 px-5 py-3 bg-green-600 hover:bg-green-500 text-white text-sm font-semibold rounded-lg transition-colors">
                <svg id="copyIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <span id="copyText">Copiar</span>
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-xl p-5">
            <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">Total referidos</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums">{{ $totalReferrals }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-xl p-5">
            <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">Tu código</p>
            <p class="text-lg font-bold text-green-600 dark:text-green-400 tabular-nums tracking-wider">{{ $user->referral_code ?? 'Sin código' }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-xl p-5">
            <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">Ganancias</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums">S/ 0.00</p>
        </div>
    </div>

    <!-- Lista de referidos -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-widest">Referidos registrados</p>
        </div>

        @if($user->referrals->isEmpty())
            <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-xl p-10 text-center">
                <svg class="w-12 h-12 text-gray-300 dark:text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Aún no tienes referidos</p>
                <p class="text-xs text-gray-400 dark:text-gray-600">Comparte tu link para empezar a ganar</p>
            </div>
        @else
            <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-xl overflow-hidden">
                @foreach($user->referrals as $referral)
                    <div class="flex items-center justify-between px-5 py-4 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-800' : '' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                                    {{ strtoupper(substr($referral->name, 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $referral->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $referral->email }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $referral->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

<script>
function copyReferralLink() {
    const link = document.getElementById('referralLink').textContent.trim();
    navigator.clipboard.writeText(link).then(() => {
        const copyText = document.getElementById('copyText');
        const copyIcon = document.getElementById('copyIcon');
        copyText.textContent = '¡Copiado!';
        copyIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>';
        setTimeout(() => {
            copyText.textContent = 'Copiar';
            copyIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>';
        }, 2000);
    });
}
</script>
@endsection
