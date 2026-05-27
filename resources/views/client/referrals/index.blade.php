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
                @foreach($referrals as $referral)
                    @php $status = $referral->documents?->status; @endphp
                    <div class="flex items-center justify-between py-4 border-b border-gray-100 dark:border-gray-800 last:border-0">
                        <div class="flex items-center gap-3">
                            @php
                                $selfieUrl = $referral->documents?->selfie ? \Illuminate\Support\Facades\Storage::url($referral->documents->selfie) : null;
                            @endphp
                            @if($selfieUrl)
                                <img src="{{ $selfieUrl }}" alt="{{ $referral->name }}" class="w-9 h-9 rounded-full object-cover flex-shrink-0 border border-gray-200 dark:border-gray-700">
                            @else
                                <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0" style="background-color: {{ match($status) { 'approved' => '#dcfce7', 'rejected' => '#fee2e2', 'pending' => '#fef9c3', default => '#f3f4f6' } }};">
                                    <svg class="w-4 h-4" style="color: {{ match($status) { 'approved' => '#22c55e', 'rejected' => '#ef4444', 'pending' => '#eab308', default => '#9ca3af' } }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $referral->name }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">{{ $referral->email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($referral->referralStatus['color'] === 'green')
                                <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-medium text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                    {{ $referral->referralStatus['label'] }}
                                </span>
                            @elseif($referral->referralStatus['color'] === 'yellow')
                                <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-medium text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                    {{ $referral->referralStatus['label'] }}
                                </span>
                            @elseif($referral->referralStatus['color'] === 'red')
                                <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-medium text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                    {{ $referral->referralStatus['label'] }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                    {{ $referral->referralStatus['label'] }}
                                </span>
                            @endif
                            <span class="text-[10px] text-gray-400 dark:text-gray-600">{{ $referral->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $referrals->links() }}
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
