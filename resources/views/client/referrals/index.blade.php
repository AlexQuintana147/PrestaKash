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
            <p class="text-2xl font-bold text-gray-900 dark:text-white">S/ 0</p>
            <p class="text-[10px] text-gray-400 dark:text-gray-600 mt-0.5">Retiro mínimo S/ 50</p>
        </div>
    </section>

    <!-- Lista -->
    <section>
        <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">Referidos</p>

        @if($user->referrals->isEmpty())
            <div class="py-12 text-center">
                <p class="text-sm text-gray-400 dark:text-gray-600">Aún no tienes referidos</p>
            </div>
        @else
            <div class="space-y-0">
                @foreach($user->referrals as $referral)
                    <div class="flex items-center justify-between py-4 border-b border-gray-100 dark:border-gray-800 last:border-0">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ strtoupper(substr($referral->name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $referral->name }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">{{ $referral->email }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-400 dark:text-gray-600">{{ $referral->created_at->diffForHumans() }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

</div>

<script>
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
