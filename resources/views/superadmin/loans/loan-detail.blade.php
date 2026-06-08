@extends('superadmin.layout')

@section('title', 'Préstamo ' . $loan->code)

@php
    $cur = $loan->currency === 'USD' ? '$' : 'S/';
@endphp

@section('content')
<div class="px-4 md:px-10 py-6 md:py-8 max-w-5xl mx-auto">
    <div class="flex items-center gap-3 mb-6 md:mb-8">
        <a href="{{ route('superadmin.installments.index') }}"
           class="text-gray-600 dark:text-gray-100 hover:text-gray-900 dark:hover:text-white transition-colors flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="min-w-0 flex-1">
            <h1 class="text-base md:text-xl font-bold text-gray-900 dark:text-white truncate">Préstamo {{ $loan->code }}</h1>
            <p class="text-[10px] text-gray-400 mt-0.5 uppercase tracking-widest font-semibold">Detalle completo de cuotas</p>
        </div>
    </div>

    @include('superadmin.loans.partials.installments-group-rows', [
        'loan' => $loan,
        'userInstallments' => $userInstallments,
    ])
</div>
@endsection
